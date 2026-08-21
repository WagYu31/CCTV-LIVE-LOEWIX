<?php
/**
 * JFTech OpenAPI V3 Official Cloud Gateway
 * Loewix Surveillance Platform - Live Video Surveillance
 * PT. LOEWIX INDONESIA
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    exit(0);
}

// JFTech Official Open Platform Credentials (Loewix CCTV)
const JF_UUID = '6583e8e82d362a5de618035a';
const JF_BOID = '212A';
const JF_APPKEY = '5ac06bd785bff5b4819a339f72a46c1a';
const JF_APPSECRET = 'cd3fe772141447dba9e7b319f09acd37';
const JF_MOVECARD = 2;
const JF_ENDPOINT = 'api-as.jftechws.com';
const JF_BASE_URL = 'https://api-as.jftechws.com/gwp/v3';

/**
 * Generate 20-digit timestamp (7-digit counter + 13-digit timeMillis)
 */
function get_jf_time_millis() {
    $counter = '0000001';
    $timeMillis = (string) round(microtime(true) * 1000);
    return $counter . $timeMillis;
}

/**
 * Generate official JFTech signature (Byte shift & reverse merge algorithm)
 */
function generateJFTechSignature($uuid, $appKey, $appSecret, $timeMillis, $moveCard) {
    $encryptStr = $uuid . $appKey . $appSecret . $timeMillis;
    $bytes = array_values(unpack('C*', $encryptStr));
    $len = count($bytes);
    
    $changeBytes = $bytes;
    for ($i = 0; $i < $len; $i++) {
        $left = $i % $moveCard;
        $right = ($len - $i) % $moveCard;
        $temp = ($left > $right) ? $changeBytes[$i] : $changeBytes[$len - ($i + 1)];
        $changeBytes[$i] = $changeBytes[$len - ($i + 1)];
        $changeBytes[$len - ($i + 1)] = $temp;
    }
    
    $merged = array_fill(0, $len * 2, 0);
    for ($i = 0; $i < $len; $i++) {
        $merged[$i] = $bytes[$i];
        $merged[($len * 2) - 1 - $i] = $changeBytes[$i];
    }
    
    return md5(pack('C*', ...$merged));
}

/**
 * Execute HTTP JSON POST to JFTech OpenAPI V3
 */
function callJFTechV3($url, $payload = []) {
    $timeMillis = get_jf_time_millis();
    $signature = generateJFTechSignature(JF_UUID, JF_APPKEY, JF_APPSECRET, $timeMillis, JF_MOVECARD);
    
    $headers = [
        'Content-Type: application/json; charset=UTF-8',
        'uuid: ' . JF_UUID,
        'appKey: ' . JF_APPKEY,
        'timeMillis: ' . $timeMillis,
        'signature: ' . $signature,
        'X-Request-Id: ' . bin2hex(random_bytes(8))
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if (!$response) {
        return ['code' => 5000, 'msg' => 'Gagal terhubung ke JFTech Cloud Gateway. (HTTP ' . $httpCode . ')'];
    }
    
    $data = json_decode($response, true);
    return $data ?: ['code' => 5000, 'msg' => 'Format respon JSON tidak valid', 'raw' => $response];
}

/**
 * Cache and Retrieve Device Access Token
 */
function getJFDeviceToken($sn) {
    $sn = trim($sn);
    $cacheFile = sys_get_temp_dir() . '/jf_token_' . md5($sn) . '.json';
    
    if (file_exists($cacheFile)) {
        $cache = json_decode(file_get_contents($cacheFile), true);
        if ($cache && isset($cache['token']) && ($cache['expires_at'] ?? 0) > time() + 300) {
            return $cache['token'];
        }
    }
    
    $url = JF_BASE_URL . '/rtc/device/token';
    $res = callJFTechV3($url, ['sns' => [$sn]]);
    
    if (isset($res['code']) && $res['code'] === 2000 && !empty($res['data'][0]['token'])) {
        $token = $res['data'][0]['token'];
        @file_put_contents($cacheFile, json_encode([
            'token' => $token,
            'expires_at' => time() + 3600 // cache 1 hour
        ]));
        return $token;
    }
    
    return null;
}

/**
 * Intelligent Token/SN Extractor and Decryptor
 */
function parseAndResolveQRToken($rawInput) {
    $rawInput = trim($rawInput);
    
    if (preg_match('/\b([0-9a-fA-F]{16})\b/', $rawInput, $matches)) {
        return [
            'success' => true,
            'serial_number' => strtolower($matches[1]),
            'type' => 'direct_sn',
            'message' => 'Serial Number valid (16 Digit Cloud ID).'
        ];
    }
    
    if (preg_match('/(?:sn|id|serial|code)=([a-zA-Z0-9]+)/i', $rawInput, $matches)) {
        $extracted = $matches[1];
        if (strlen($extracted) === 16) {
            return [
                'success' => true,
                'serial_number' => strtolower($extracted),
                'type' => 'url_param',
                'message' => 'Serial Number diekstrak dari URL parameter.'
            ];
        }
    }

    if (stripos($rawInput, 'xmeye') !== false || stripos($rawInput, 'ctim') !== false) {
        if (preg_match_all('/([a-zA-Z0-9+\/=]{12,})/', $rawInput, $b64Matches)) {
            foreach ($b64Matches[1] as $b64Str) {
                $decoded = base64_decode($b64Str, true);
                if ($decoded && preg_match('/([0-9a-fA-F]{16})/', $decoded, $snMatches)) {
                    return [
                        'success' => true,
                        'serial_number' => strtolower($snMatches[1]),
                        'type' => 'xmeye_wizard_extracted',
                        'message' => 'Serial Number berhasil diekstrak dari barcode DVR!'
                    ];
                }
            }
        }
    }
    
    $cleanAlphanumeric = preg_replace('/[^a-zA-Z0-9]/', '', $rawInput);
    $isWizardQR = (stripos($rawInput, 'xmeye') !== false || stripos($rawInput, 'ctim') !== false);
    
    return [
        'success' => true,
        'raw_token' => $cleanAlphanumeric,
        'serial_number' => (strlen($cleanAlphanumeric) === 16) ? strtolower($cleanAlphanumeric) : '',
        'type' => $isWizardQR ? 'dvr_wizard_qr' : ((strlen($cleanAlphanumeric) > 20) ? 'xmeye_share_token' : 'unrecognized'),
        'message' => (strlen($cleanAlphanumeric) === 16) ? 'Serial Number 16 Digit valid.' : 'Barcode berhasil dipindai.'
    ];
}

// Route Requests
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'parse_qr') {
    $qrData = $_POST['qr_data'] ?? $_GET['qr_data'] ?? '';
    if (empty($qrData)) {
        echo json_encode(['success' => false, 'message' => 'Data QR kosong.']);
        exit;
    }
    
    $result = parseAndResolveQRToken($qrData);
    echo json_encode($result);
    exit;
}

if ($action === 'get_live_stream') {
    $sn = trim($_POST['sn'] ?? $_GET['sn'] ?? '');
    $channel = (int)($_POST['channel'] ?? $_GET['channel'] ?? 1);
    $streamType = trim($_POST['stream'] ?? $_GET['stream'] ?? 'sub');
    $deviceUser = trim($_POST['device_user'] ?? $_GET['device_user'] ?? 'admin');
    $devicePass = trim($_POST['device_pass'] ?? $_GET['device_pass'] ?? '');

    if (empty($sn)) {
        echo json_encode(['success' => false, 'message' => 'Serial Number wajib diisi.']);
        exit;
    }

    $cleanSN = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $sn));
    $channelIdx = max(0, $channel - 1);
    $streamIdx = ($streamType === 'main' || $streamType === '0') ? '0' : '1';

    if (empty($devicePass)) {
        if (file_exists(__DIR__ . '/../config/db.php')) {
            require_once __DIR__ . '/../config/db.php';
            $dbData = get_db_data();
            foreach (($dbData['cameras'] ?? []) as $cam) {
                if (isset($cam['serial_number']) && strtolower($cam['serial_number']) === $cleanSN && isset($cam['device_pass'])) {
                    $devicePass = $cam['device_pass'];
                    break;
                }
            }
        }
    }

    // 1. Get Device Access Token
    $deviceToken = getJFDeviceToken($cleanSN);
    
    if (!$deviceToken) {
        // Return structured stream fallback if token request fails
        $streamPath = "xmeye_{$cleanSN}_ch{$channel}";
        echo json_encode([
            'success' => false,
            'source' => 'token_error',
            'streamPath' => $streamPath,
            'hls_url' => "https://stream.loewixcctv.com/{$streamPath}/index.m3u8",
            'sn' => $cleanSN,
            'channel' => $channel,
            'message' => 'Gagal mendapatkan token perangkat dari Cloud JFTech.'
        ]);
        exit;
    }

    // 2. Request Live Stream HLS URL from JFTech Cloud OpenAPI V3
    $requestedProto = trim($_POST['protocol'] ?? $_GET['protocol'] ?? 'hls-fmp4');
    $url = JF_BASE_URL . '/rtc/device/livestream/' . $deviceToken;
    $body = [
        'channel' => (string) $channelIdx,
        'stream' => (string) $streamIdx,
        'protocol' => $requestedProto,
        'username' => $deviceUser ?: 'admin',
        'password' => $devicePass
    ];

    $res = callJFTechV3($url, $body);

    // Auto-fallback for password mismatches
    if (isset($res['data']['Ret']) && $res['data']['Ret'] === 106) {
        // Try empty password
        if ($body['password'] !== '') {
            $body['password'] = '';
            $res = callJFTechV3($url, $body);
        } else {
            // Try LoewixL12
            $body['password'] = 'LoewixL12';
            $res = callJFTechV3($url, $body);
        }
    }

    if (isset($res['code']) && $res['code'] === 2000 && !empty($res['data']['url'])) {
        echo json_encode([
            'success' => true,
            'source' => 'jftech_cloud_hls',
            'hls_url' => $res['data']['url'],
            'expireTime' => $res['data']['expireTime'] ?? null,
            'sn' => $cleanSN,
            'channel' => $channel,
            'message' => 'Live stream HLS resmi berhasil diperoleh dari JFTech Cloud!'
        ]);
        exit;
    }

    // Return error or fallback
    $streamPath = "xmeye_{$cleanSN}_ch{$channel}";
    echo json_encode([
        'success' => false,
        'source' => 'xmeye_p2p_stream',
        'streamPath' => $streamPath,
        'hls_url' => "https://stream.loewixcctv.com/{$streamPath}/index.m3u8",
        'sn' => $cleanSN,
        'channel' => $channel,
        'cloud_api_status' => $res['msg'] ?? 'Gagal membuat URL livestream',
        'message' => $res['msg'] ?? 'Gagal membuat URL livestream'
    ]);
    exit;
}

if ($action === 'get_snapshot') {
    $sn = trim($_POST['sn'] ?? $_GET['sn'] ?? '');
    $channel = (int)($_POST['channel'] ?? $_GET['channel'] ?? 1);
    if (empty($sn)) {
        echo json_encode(['success' => false, 'message' => 'SN wajib diisi.']);
        exit;
    }
    $cleanSN = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $sn));
    $channelIdx = max(0, $channel - 1);

    $deviceToken = getJFDeviceToken($cleanSN);
    if (!$deviceToken) {
        echo json_encode(['success' => false, 'message' => 'Gagal mendapatkan token']);
        exit;
    }

    $url = JF_BASE_URL . '/rtc/device/capture/' . $deviceToken;
    $body = [
        'Name' => 'OPSNAP',
        'OPSNAP' => [
            'Channel' => $channelIdx,
            'PicType' => 0
        ]
    ];
    $res = callJFTechV3($url, $body);

    if (($res['code'] ?? 0) === 2000 && isset($res['data']['image'])) {
        echo json_encode([
            'success' => true,
            'image' => $res['data']['image'],
            'sn' => $cleanSN,
            'channel' => $channel
        ]);
        exit;
    }

    echo json_encode([
        'success' => false,
        'message' => $res['data']['RetMsg'] ?? $res['msg'] ?? 'Snapshot gagal',
        'ret' => $res['data']['Ret'] ?? null
    ]);
    exit;
}

if ($action === 'get_all_snapshots') {
    $sn = trim($_POST['sn'] ?? $_GET['sn'] ?? '');
    if (empty($sn)) {
        echo json_encode(['success' => false, 'message' => 'SN wajib diisi.']);
        exit;
    }
    $cleanSN = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $sn));
    
    // Check local snapshot cache (valid 60 seconds)
    $cacheFile = sys_get_temp_dir() . '/jf_snapshots_' . md5($cleanSN) . '.json';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 90)) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached && !empty($cached['snapshots'])) {
            echo json_encode($cached);
            exit;
        }
    }

    $deviceToken = getJFDeviceToken($cleanSN);
    if (!$deviceToken) {
        echo json_encode(['success' => false, 'message' => 'Gagal mendapatkan token']);
        exit;
    }

    $snapshots = [];
    for ($ch = 0; $ch < 16; $ch++) {
        $url = JF_BASE_URL . '/rtc/device/capture/' . $deviceToken;
        $body = [
            'Name' => 'OPSNAP',
            'OPSNAP' => [
                'Channel' => $ch,
                'PicType' => 0
            ]
        ];
        $res = callJFTechV3($url, $body);
        if (($res['code'] ?? 0) === 2000 && isset($res['data']['image'])) {
            $snapshots[$ch + 1] = $res['data']['image'];
        }
    }

    $result = [
        'success' => true,
        'sn' => $cleanSN,
        'snapshots' => $snapshots
    ];
    
    if (!empty($snapshots)) {
        @file_put_contents($cacheFile, json_encode($result));
    }

    echo json_encode($result);
    exit;
}

// Default response: Return Gateway Status
echo json_encode([
    'success' => true,
    'gateway' => 'JFTech Open Platform Cloud Gateway V3',
    'status' => 'ONLINE',
    'region' => 'Asia (api-as.jftechws.com)',
    'uuid' => JF_UUID,
    'app_key' => substr(JF_APPKEY, 0, 8) . '****',
    'timestamp' => time()
]);
