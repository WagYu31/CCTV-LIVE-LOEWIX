<?php
/**
 * Loewix Enterprise VMS - Camera Encode Configuration Gateway
 * Handles Main Stream & Extra Stream encoding parameters (H.265/H.264/H.265AI, Bitrate, FPS, Resolution, Audio/Video)
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dataFile = __DIR__ . '/../data/cameras_encode.json';

// Default encode template for any channel
function getDefaultEncodeProfile($channel = 1) {
    return [
        'channel' => (int)$channel,
        'main_stream' => [
            'compression' => 'H.265',
            'resolution' => '6M', // 6M, 4K, 5M, 4M, 3M, 1080P, 720P
            'fps' => 20,
            'bitrate_type' => 'VBR', // VBR, CBR
            'quality' => 'high', // lowest, low, middle, good, better, best, high
            'bitrate_kbps' => 3316,
            'iframe_interval' => 2,
            'enable_video' => true,
            'enable_audio' => true,
            'smart_encode' => 'H.265AI' // Off, H.264+, H.265+, H.265AI, H.265X
        ],
        'extra_stream' => [
            'compression' => 'H.265',
            'resolution' => 'HD1', // D1, HD1, CIF, QVGA
            'fps' => 20,
            'bitrate_type' => 'VBR',
            'quality' => 'high',
            'bitrate_kbps' => 552,
            'iframe_interval' => 2,
            'enable_video' => true,
            'enable_audio' => true,
            'smart_encode' => 'H.265AI'
        ],
        'advanced' => [
            'audio_codec' => 'AAC', // AAC, G.711A, G.711U
            'audio_samplerate' => 8000,
            'gop_size' => 40,
            'roi_enabled' => true,
            'watermark_osd' => true
        ],
        'updated_at' => date('Y-m-d H:i:s')
    ];
}

// Load all encode profiles
function loadEncodeProfiles($filePath) {
    if (!file_exists($filePath)) {
        $initial = [];
        for ($ch = 1; $ch <= 16; $ch++) {
            $initial[$ch] = getDefaultEncodeProfile($ch);
        }
        @file_put_contents($filePath, json_encode($initial, JSON_PRETTY_PRINT));
        return $initial;
    }
    $content = @file_get_contents($filePath);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

// Save all profiles
function saveEncodeProfiles($filePath, $data) {
    return @file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'get';
$channel = (int)($_GET['channel'] ?? $_POST['channel'] ?? 1);
if ($channel < 1) $channel = 1;

$profiles = loadEncodeProfiles($dataFile);

if ($action === 'get') {
    $profile = $profiles[$channel] ?? getDefaultEncodeProfile($channel);
    
    // Calculate telemetry metrics
    $mainBitrate = (int)($profile['main_stream']['bitrate_kbps'] ?? 3316);
    $extraBitrate = (int)($profile['extra_stream']['bitrate_kbps'] ?? 552);
    $totalKbps = $mainBitrate + $extraBitrate;
    $gbPerDay = round(($totalKbps * 3600 * 24) / (8 * 1024 * 1024), 2);

    echo json_encode([
        'success' => true,
        'channel' => $channel,
        'profile' => $profile,
        'metrics' => [
            'total_bitrate_mbps' => round($totalKbps / 1024, 2),
            'storage_gb_per_day' => $gbPerDay,
            'recommended_bandwidth' => round(($mainBitrate * 1.2) / 1024, 2) . ' Mbps'
        ]
    ]);
    exit;
}

if ($action === 'get_all') {
    echo json_encode([
        'success' => true,
        'channels' => $profiles
    ]);
    exit;
}

if ($action === 'save') {
    $inputRaw = file_get_contents('php://input');
    $postData = json_decode($inputRaw, true);
    if (!is_array($postData)) {
        $postData = $_POST;
    }

    $targetChannel = (int)($postData['channel'] ?? $channel);
    $copyToAll = !empty($postData['copy_to_all']);

    $newProfile = [
        'channel' => $targetChannel,
        'main_stream' => [
            'compression' => $postData['main_stream']['compression'] ?? 'H.265',
            'resolution' => $postData['main_stream']['resolution'] ?? '6M',
            'fps' => (int)($postData['main_stream']['fps'] ?? 20),
            'bitrate_type' => $postData['main_stream']['bitrate_type'] ?? 'VBR',
            'quality' => $postData['main_stream']['quality'] ?? 'high',
            'bitrate_kbps' => (int)($postData['main_stream']['bitrate_kbps'] ?? 3316),
            'iframe_interval' => (int)($postData['main_stream']['iframe_interval'] ?? 2),
            'enable_video' => !empty($postData['main_stream']['enable_video']),
            'enable_audio' => !empty($postData['main_stream']['enable_audio']),
            'smart_encode' => $postData['main_stream']['smart_encode'] ?? 'H.265AI'
        ],
        'extra_stream' => [
            'compression' => $postData['extra_stream']['compression'] ?? 'H.265',
            'resolution' => $postData['extra_stream']['resolution'] ?? 'HD1',
            'fps' => (int)($postData['extra_stream']['fps'] ?? 20),
            'bitrate_type' => $postData['extra_stream']['bitrate_type'] ?? 'VBR',
            'quality' => $postData['extra_stream']['quality'] ?? 'high',
            'bitrate_kbps' => (int)($postData['extra_stream']['bitrate_kbps'] ?? 552),
            'iframe_interval' => (int)($postData['extra_stream']['iframe_interval'] ?? 2),
            'enable_video' => !empty($postData['extra_stream']['enable_video']),
            'enable_audio' => !empty($postData['extra_stream']['enable_audio']),
            'smart_encode' => $postData['extra_stream']['smart_encode'] ?? 'H.265AI'
        ],
        'advanced' => [
            'audio_codec' => $postData['advanced']['audio_codec'] ?? 'AAC',
            'audio_samplerate' => (int)($postData['advanced']['audio_samplerate'] ?? 8000),
            'gop_size' => (int)($postData['advanced']['gop_size'] ?? 40),
            'roi_enabled' => !empty($postData['advanced']['roi_enabled']),
            'watermark_osd' => !empty($postData['advanced']['watermark_osd'])
        ],
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($copyToAll) {
        foreach ($profiles as $ch => $p) {
            $cloned = $newProfile;
            $cloned['channel'] = (int)$ch;
            $profiles[$ch] = $cloned;
        }
        $msg = "Konfigurasi Encode berhasil diterapkan ke SEMUA Channel CCTV!";
    } else {
        $profiles[$targetChannel] = $newProfile;
        $msg = "Konfigurasi Encode Channel {$targetChannel} berhasil disimpan!";
    }

    saveEncodeProfiles($dataFile, $profiles);

    echo json_encode([
        'success' => true,
        'message' => $msg,
        'profile' => $newProfile
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
