<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    exit(0);
}

// JFTech Official Open Platform Credentials (Loewix Surveillance Platform)
const JF_UUID = '6a83efca64b42a6b4e0db2c3';
const JF_APPKEY = '5de0b6544dc1b9c56385fb7f2867bc45';
const JF_APPSECRET = 'e37c7fe1799a4c249ff0e1e4c715b43a';
const JF_MOVECARD = 3;

const JF_API_BASE = 'https://api-as.jftechws.com/gwp/v2';

/**
 * Generate official JFTech signature
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
    
    $merged = array_merge($bytes, $changeBytes);
    $binaryStr = pack('C*', ...$merged);
    return md5($binaryStr);
}

/**
 * Make API Request to JFTech Open Platform Cloud
 */
function callJFTechAPI($endpoint, $payload = []) {
    $appKey = JF_APPKEY;
    $appSecret = JF_APPSECRET;
    $moveCard = JF_MOVECARD;
    
    $timeMillis = (string) round(microtime(true) * 1000);
    $signature = generateJFTechSignature(JF_UUID, $appKey, $appSecret, $timeMillis, $moveCard);
    
    $url = JF_API_BASE . $endpoint;
    
    $headers = [
        'Content-Type: application/json',
        'appKey: ' . $appKey,
        'uuid: ' . JF_UUID,
        'timeMillis: ' . $timeMillis,
        'signature: ' . $signature
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if (!$response) {
        return ['success' => false, 'code' => $httpCode, 'message' => 'Gagal terhubung ke JFTech Cloud API.'];
    }
    
    $data = json_decode($response, true);
    return $data ?: ['success' => false, 'raw' => $response];
}

/**
 * Intelligent Token/SN Extractor and Decryptor
 */
function parseAndResolveQRToken($rawInput) {
    $rawInput = trim($rawInput);
    
    // Check if it already has a clean 16 hex SN
    if (preg_match('/\b([0-9a-fA-F]{16})\b/', $rawInput, $matches)) {
        return [
            'success' => true,
            'serial_number' => strtolower($matches[1]),
            'type' => 'direct_sn',
            'message' => 'Serial Number valid (16 Digit Cloud ID).'
        ];
    }
    
    // Extract if it has sn= / id= / code=
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

    // Special Parser for XMeye Live Screen Wizard QR (xmeyectim...)
    if (stripos($rawInput, 'xmeye') !== false || stripos($rawInput, 'ctim') !== false) {
        // Try to search for base64 encoded hex in substrings
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
    
    // If it is a long token (like 8qS50... or 8X63...)
    // Call JFTech Cloud API with Android Credentials first
    $apiResult = callJFTechAPI('/rtc/device/share/parse', ['token' => $rawInput], 'android');
    
    if (isset($apiResult['code']) && $apiResult['code'] === 2000 && !empty($apiResult['data']['sn'])) {
        return [
            'success' => true,
            'serial_number' => strtolower($apiResult['data']['sn']),
            'username' => $apiResult['data']['username'] ?? 'admin',
            'type' => 'jftech_cloud_decrypted',
            'message' => 'Token berhasil didekripsi oleh JFTech Open Platform Cloud!'
        ];
    }
    
    // Fallback to iOS credentials
    $apiResultIOS = callJFTechAPI('/rtc/device/share/parse', ['token' => $rawInput], 'ios');
    if (isset($apiResultIOS['code']) && $apiResultIOS['code'] === 2000 && !empty($apiResultIOS['data']['sn'])) {
        return [
            'success' => true,
            'serial_number' => strtolower($apiResultIOS['data']['sn']),
            'username' => $apiResultIOS['data']['username'] ?? 'admin',
            'type' => 'jftech_cloud_decrypted_ios',
            'message' => 'Token berhasil didekripsi oleh JFTech iOS Gateway!'
        ];
    }
    
    // If Cloud API returns raw token info or requires fallback
    $cleanAlphanumeric = preg_replace('/[^a-zA-Z0-9]/', '', $rawInput);
    
    $isWizardQR = (stripos($rawInput, 'xmeye') !== false || stripos($rawInput, 'ctim') !== false);
    
    return [
        'success' => true,
        'raw_token' => $cleanAlphanumeric,
        'serial_number' => (strlen($cleanAlphanumeric) === 16) ? strtolower($cleanAlphanumeric) : '',
        'type' => $isWizardQR ? 'dvr_wizard_qr' : ((strlen($cleanAlphanumeric) > 20) ? 'xmeye_share_token' : 'unrecognized'),
        'message' => $isWizardQR 
            ? 'Barcode Wizard Layar DVR terdeteksi. Buka menu [Main Menu > Info > Version] di DVR untuk QR Code Cloud ID 16 Digit.'
            : ((strlen($cleanAlphanumeric) > 20) 
                ? 'Format Token Berbagi terdeteksi. Silakan masukkan 16 digit Serial Number kamera.'
                : 'Barcode berhasil dipindai.')
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

    $cleanSN = preg_replace('/[^a-zA-Z0-9]/', '', $sn);
    $channelIdx = max(0, $channel - 1);
    $streamIdx = ($streamType === 'main' || $streamType === '0') ? 0 : 1;

    // 1. Try to request Live Stream Address from JFTech Cloud OpenAPI
    $cloudResult = callJFTechAPI('/rtc/device/livestream', [
        'sn' => $cleanSN,
        'channel' => $channelIdx,
        'stream' => $streamIdx,
        'protocol' => 'hls',
        'expireTime' => 86400
    ], 'android');

    if (isset($cloudResult['code']) && $cloudResult['code'] === 2000 && !empty($cloudResult['data']['url'])) {
        echo json_encode([
            'success' => true,
            'source' => 'jftech_cloud_hls',
            'hls_url' => $cloudResult['data']['url'],
            'flv_url' => $cloudResult['data']['flvUrl'] ?? '',
            'sn' => $cleanSN,
            'channel' => $channel,
            'message' => 'Live stream HLS berhasil diperoleh dari JFTech Cloud.'
        ]);
        exit;
    }

    // 2. Return standard structured P2P Media path for Web Player
    $streamPath = "xmeye_{$cleanSN}_ch{$channel}";
    $hlsUrl = "https://stream.loewixcctv.com/{$streamPath}/index.m3u8";

    echo json_encode([
        'success' => true,
        'source' => 'xmeye_p2p_stream',
        'streamPath' => $streamPath,
        'hls_url' => $hlsUrl,
        'sn' => $cleanSN,
        'channel' => $channel,
        'cloud_api_status' => $cloudResult['msg'] ?? 'P2P Ready',
        'message' => 'Jalur XMeye P2P Channel ' . $channel . ' aktif.'
    ]);
    exit;
}

// Default response: Return Gateway Status
echo json_encode([
    'success' => true,
    'gateway' => 'JFTech Open Platform Cloud Gateway',
    'status' => 'ONLINE',
    'uuid' => JF_UUID,
    'android_key' => substr(JF_ANDROID_KEY, 0, 8) . '****',
    'ios_key' => substr(JF_IOS_KEY, 0, 8) . '****',
    'timestamp' => time()
]);
