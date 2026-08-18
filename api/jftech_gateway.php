<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    exit(0);
}

// JFTech Official Open Platform Credentials
const JF_UUID = '6583e8e82d362a5de618035a';

const JF_ANDROID_KEY = '9ba9c2913a896f694457b70811ae5468';
const JF_ANDROID_SECRET = 'd9a2702958dc45f9a606c60b4ec49aba';
const JF_ANDROID_MOVECARD = 7;

const JF_IOS_KEY = '75f62dc38c78b68b35a9d81627353aba';
const JF_IOS_SECRET = '6f4a471354bb416b9e40e905ef049999';
const JF_IOS_MOVECARD = 5;

const JF_API_BASE = 'https://rds.jftechws.com/v2';

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
function callJFTechAPI($endpoint, $payload = [], $platform = 'android') {
    $appKey = ($platform === 'ios') ? JF_IOS_KEY : JF_ANDROID_KEY;
    $appSecret = ($platform === 'ios') ? JF_IOS_SECRET : JF_ANDROID_SECRET;
    $moveCard = ($platform === 'ios') ? JF_IOS_MOVECARD : JF_ANDROID_MOVECARD;
    
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
    
    return [
        'success' => true,
        'raw_token' => $cleanAlphanumeric,
        'serial_number' => (strlen($cleanAlphanumeric) === 16) ? strtolower($cleanAlphanumeric) : '',
        'type' => (strlen($cleanAlphanumeric) > 20) ? 'xmeye_share_token' : 'unrecognized',
        'message' => (strlen($cleanAlphanumeric) > 20) 
            ? 'Format Token Berbagi terdeteksi. Silakan konfirmasi 16 digit Serial Number kamera.'
            : 'Barcode berhasil dipindai.'
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

if ($action === 'check_status') {
    $sn = $_POST['sn'] ?? $_GET['sn'] ?? '';
    if (empty($sn)) {
        echo json_encode(['success' => false, 'message' => 'Serial Number kosong.']);
        exit;
    }
    
    $apiResult = callJFTechAPI('/rtc/device/status', ['sn' => $sn], 'android');
    echo json_encode([
        'success' => true,
        'sn' => $sn,
        'jftech_response' => $apiResult
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
