<?php
/**
 * CCTV Camera Management REST API
 * Enforces Customer Quotas & User Data Isolation
 * PT. LOEWIX INDONESIA
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/db.php';

$user = get_logged_in_user();
$db = get_db_data();
$action = $_GET['action'] ?? $_POST['action'] ?? 'public_list';

if ($action === 'public_list' || $action === 'list') {
    $cityFilter = $_GET['city'] ?? 'all';
    $userIdFilter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
    $userCameras = [];
    $snSnapshotCache = [];

    foreach ($db['cameras'] as $cam) {
        if ($userIdFilter !== null && (int)($cam['user_id'] ?? 0) !== $userIdFilter) {
            continue;
        }
        if ($cityFilter === 'all' || strtolower($cam['city'] ?? '') === strtolower($cityFilter)) {
            // Force HTTPS for stream.loewixcctv.com to prevent mixed-content blocking
            if (!empty($cam['hls_url']) && strpos($cam['hls_url'], 'http://stream.loewixcctv.com') === 0) {
                $cam['hls_url'] = str_replace('http://', 'https://', $cam['hls_url']);
            }

            // Automatically attach real-time snapshot and live HLS stream URL for XMeye cameras
            if (($cam['connection_type'] ?? '') === 'xmeye_p2p' && !empty($cam['serial_number'])) {
                $sn = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cam['serial_number']));
                $ch = (int)($cam['channel'] ?? 1);
                
                require_once __DIR__ . '/jftech_gateway.php';
                if (function_exists('getJFTechLiveStreamUrl')) {
                    $liveHls = getJFTechLiveStreamUrl($sn, $ch, 'hls-ts', $cam['stream_quality'] ?? 'sub', $cam['device_user'] ?? 'admin', $cam['device_pass'] ?? '');
                    if ($liveHls) {
                        $cam['hls_url'] = $liveHls;
                        $cam['streamPath'] = $liveHls;
                    }
                }

                if (!array_key_exists($sn, $snSnapshotCache)) {
                    $cacheFile = sys_get_temp_dir() . '/jftech_snapshots_' . $sn . '.json';
                    if (file_exists($cacheFile)) {
                        $snSnapshotCache[$sn] = json_decode(file_get_contents($cacheFile), true);
                    } else {
                        $snSnapshotCache[$sn] = null;
                    }
                }

                if (isset($snSnapshotCache[$sn]['snapshots'][$ch]) && !empty($snSnapshotCache[$sn]['snapshots'][$ch])) {
                    $cam['thumbnail'] = $snSnapshotCache[$sn]['snapshots'][$ch];
                }
            }

            $userCameras[] = $cam;
        }
    }

    echo json_encode([
        'success' => true,
        'cameras' => $userCameras,
        'total' => count($userCameras)
    ]);
    exit;
}

if ($action === 'admin_add' || $action === 'add') {
    $userId = (int) ($_POST['user_id'] ?? ($user['id'] ?? 1));
    $title = trim($_POST['title'] ?? '');
    $city = trim($_POST['city'] ?? 'siantar');
    $connectionType = trim($_POST['connection_type'] ?? 'rtsp');
    $streamPath = trim($_POST['streamPath'] ?? '');
    $lat = trim($_POST['lat'] ?? '');
    $lng = trim($_POST['lng'] ?? '');

    // XMeye P2P specific parameters
    $serialNumber = trim($_POST['serial_number'] ?? '');
    $deviceUser = trim($_POST['device_user'] ?? 'admin');
    $devicePass = trim($_POST['device_pass'] ?? '');
    $channel = (int) ($_POST['channel'] ?? 1);
    $streamQuality = trim($_POST['stream_quality'] ?? 'sub');

    if ($connectionType === 'xmeye_p2p') {
        if (empty($title) || empty($serialNumber)) {
            echo json_encode(['success' => false, 'message' => 'Nama Kamera dan Serial Number XMeye (Cloud ID) wajib diisi!']);
            exit;
        }
        if (empty($streamPath)) {
            $streamPath = "xmeye_" . preg_replace('/[^a-zA-Z0-9]/', '', $serialNumber) . "_ch" . $channel;
        }
    } else {
        if (empty($title) || empty($streamPath)) {
            echo json_encode(['success' => false, 'message' => 'Nama Kamera dan Stream Path / RTSP URL wajib diisi!']);
            exit;
        }
    }

    // Generate safe ID starting from 5001
    $existingIds = array_column($db['cameras'], 'id');
    $newCamId = count($existingIds) > 0 ? max(max($existingIds), 5000) + 1 : 5001;

    $newCam = [
        'id' => $newCamId,
        'user_id' => $userId,
        'title' => $title,
        'city' => $city,
        'connection_type' => $connectionType,
        'serial_number' => $serialNumber,
        'device_user' => $deviceUser,
        'device_pass' => $devicePass,
        'channel' => $channel,
        'stream_quality' => $streamQuality,
        'streamPath' => $streamPath,
        'hls_url' => (strpos($streamPath, '.m3u8') !== false || strpos($streamPath, 'http') !== false) ? $streamPath : "https://stream.loewixcctv.com/{$streamPath}/index.m3u8",
        'thumbnail' => "assets/image/thumbnail/default-thumbnail.png",
        'lat' => $lat,
        'lng' => $lng,
        'platform' => $connectionType === 'xmeye_p2p' ? 'xmeye_p2p' : 'mediamtx',
        'status' => 'online',
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db['cameras'][] = $newCam;
    save_db_data($db);

    echo json_encode([
        'success' => true,
        'message' => "Kamera {$title} Berhasil Ditambahkan ke Database Server!",
        'camera' => $newCam
    ]);
    exit;
}

if ($action === 'batch_add_dvr') {
    $userId = (int) ($_POST['user_id'] ?? ($user['id'] ?? 1));
    $dvrTitle = trim($_POST['dvr_title'] ?? 'Kamera DVR');
    $city = trim($_POST['city'] ?? 'jakarta');
    $serialNumber = trim($_POST['serial_number'] ?? '');
    $deviceUser = trim($_POST['device_user'] ?? 'admin');
    $devicePass = trim($_POST['device_pass'] ?? '');
    $channelCount = (int) ($_POST['channel_count'] ?? 16); // 4, 8, 16, 32
    $streamQuality = trim($_POST['stream_quality'] ?? 'sub');
    $lat = trim($_POST['lat'] ?? '');
    $lng = trim($_POST['lng'] ?? '');

    if (empty($serialNumber)) {
        echo json_encode(['success' => false, 'message' => 'Serial Number XMeye (Cloud ID) wajib diisi!']);
        exit;
    }

    // Check customer quota
    $userQuota = 10;
    foreach ($db['users'] as $u) {
        if ((int)$u['id'] === $userId) {
            $userQuota = (int)($u['cctv_quota'] ?? 10);
            break;
        }
    }

    $currentUsed = 0;
    foreach ($db['cameras'] as $c) {
        if ((int)($c['user_id'] ?? 0) === $userId) {
            $currentUsed++;
        }
    }

    $availableSlots = max(0, $userQuota - $currentUsed);
    $toAddCount = min($channelCount, $availableSlots);

    if ($toAddCount <= 0) {
        echo json_encode([
            'success' => false, 
            'message' => "Batas Kuota Customer Tercapai ({$currentUsed}/{$userQuota} CCTV). Silakan tingkatkan kuota terlebih dahulu."
        ]);
        exit;
    }

    $existingIds = array_column($db['cameras'], 'id');
    $nextId = count($existingIds) > 0 ? max(max($existingIds), 5000) + 1 : 5001;
    $addedCameras = [];
    $cleanSN = preg_replace('/[^a-zA-Z0-9]/', '', $serialNumber);

    for ($ch = 1; $ch <= $toAddCount; $ch++) {
        $camTitle = "{$dvrTitle} (CH {$ch})";
        $streamPath = "xmeye_{$cleanSN}_ch{$ch}";

        $newCam = [
            'id' => $nextId++,
            'user_id' => $userId,
            'title' => $camTitle,
            'city' => $city,
            'connection_type' => 'xmeye_p2p',
            'serial_number' => $serialNumber,
            'device_user' => $deviceUser,
            'device_pass' => $devicePass,
            'channel' => $ch,
            'stream_quality' => $streamQuality,
            'streamPath' => $streamPath,
            'hls_url' => "https://stream.loewixcctv.com/{$streamPath}/index.m3u8",
            'thumbnail' => "assets/image/thumbnail/default-thumbnail.png",
            'lat' => $lat,
            'lng' => $lng,
            'platform' => 'xmeye_p2p',
            'status' => 'online',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $db['cameras'][] = $newCam;
        $addedCameras[] = $newCam;
    }

    save_db_data($db);

    echo json_encode([
        'success' => true,
        'message' => "Berhasil menambahkan {$toAddCount} Channel Kamera sekaligus dari DVR ({$serialNumber})!",
        'added_count' => $toAddCount,
        'cameras' => $addedCameras
    ]);
    exit;
}

if ($action === 'admin_edit' || $action === 'edit') {
    $camId = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $city = trim($_POST['city'] ?? 'siantar');
    $connectionType = trim($_POST['connection_type'] ?? 'rtsp');
    $streamPath = trim($_POST['streamPath'] ?? '');
    $lat = trim($_POST['lat'] ?? '');
    $lng = trim($_POST['lng'] ?? '');

    $serialNumber = trim($_POST['serial_number'] ?? '');
    $deviceUser = trim($_POST['device_user'] ?? 'admin');
    $devicePass = trim($_POST['device_pass'] ?? '');
    $channel = (int) ($_POST['channel'] ?? 1);
    $streamQuality = trim($_POST['stream_quality'] ?? 'sub');

    if ($connectionType === 'xmeye_p2p' && empty($streamPath) && !empty($serialNumber)) {
        $streamPath = "xmeye_" . preg_replace('/[^a-zA-Z0-9]/', '', $serialNumber) . "_ch" . $channel;
    }

    foreach ($db['cameras'] as &$c) {
        if ($c['id'] === $camId) {
            $c['title'] = $title;
            $c['city'] = $city;
            $c['connection_type'] = $connectionType;
            $c['serial_number'] = $serialNumber;
            $c['device_user'] = $deviceUser;
            if (!empty($devicePass)) {
                $c['device_pass'] = $devicePass;
            }
            $c['channel'] = $channel;
            $c['stream_quality'] = $streamQuality;
            $c['streamPath'] = $streamPath;
            $c['hls_url'] = (strpos($streamPath, '.m3u8') !== false || strpos($streamPath, 'http') !== false) ? $streamPath : "http://stream.loewixcctv.com/{$streamPath}/index.m3u8";
            $c['lat'] = $lat;
            $c['lng'] = $lng;
            $c['platform'] = $connectionType === 'xmeye_p2p' ? 'xmeye_p2p' : 'mediamtx';
            save_db_data($db);
            echo json_encode(['success' => true, 'message' => 'Kamera berhasil diperbarui!']);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'Kamera tidak ditemukan!']);
    exit;
}

if ($action === 'delete') {
    $camId = (int) ($_POST['id'] ?? 0);
    $newCameras = [];
    $deleted = false;

    foreach ($db['cameras'] as $c) {
        if ($c['id'] === $camId) {
            $deleted = true;
        } else {
            $newCameras[] = $c;
        }
    }

    if ($deleted) {
        $db['cameras'] = $newCameras;
        save_db_data($db);
        echo json_encode(['success' => true, 'message' => 'Kamera berhasil dihapus!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kamera tidak ditemukan.']);
    }
    exit;
}

if ($action === 'delete_all' || $action === 'clean_all') {
    $db['cameras'] = [];
    save_db_data($db);
    echo json_encode(['success' => true, 'message' => 'Semua channel kamera berhasil dibersihkan dari database server!']);
    exit;
}

if ($action === 'batch_delete') {
    $rawIds = $_POST['ids'] ?? [];
    if (is_string($rawIds)) {
        $decoded = json_decode($rawIds, true);
        $rawIds = is_array($decoded) ? $decoded : explode(',', $rawIds);
    }
    $ids = array_map('intval', (array)$rawIds);
    $ids = array_filter($ids, function($v) { return $v > 0; });

    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada kamera yang dipilih untuk dihapus.']);
        exit;
    }

    $newCameras = [];
    $deletedCount = 0;

    foreach ($db['cameras'] as $c) {
        if (in_array((int)$c['id'], $ids, true)) {
            $deletedCount++;
        } else {
            $newCameras[] = $c;
        }
    }

    if ($deletedCount > 0) {
        $db['cameras'] = $newCameras;
        save_db_data($db);
        echo json_encode([
            'success' => true, 
            'message' => "Berhasil menghapus {$deletedCount} channel kamera secara massal!",
            'deleted_count' => $deletedCount
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tidak ada kamera yang cocok untuk dihapus.']);
    }
    exit;
}

if ($action === 'upload_snapshot') {
    $sn = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $_POST['sn'] ?? $_GET['sn'] ?? ''));
    $channel = (int)($_POST['channel'] ?? $_GET['channel'] ?? 1);
    $imageData = $_POST['image'] ?? '';

    if (empty($sn) || empty($imageData)) {
        echo json_encode(['success' => false, 'message' => 'SN dan Image wajib diisi.']);
        exit;
    }

    $cacheFile = sys_get_temp_dir() . '/jftech_snapshots_' . $sn . '.json';
    $cache = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : ['sn' => $sn, 'snapshots' => []];
    
    if (strpos($imageData, 'data:image') === false) {
        $imageData = 'data:image/jpeg;base64,' . $imageData;
    }
    
    $cache['snapshots'][$channel] = $imageData;
    $cache['updated_at'] = time();
    @file_put_contents($cacheFile, json_encode($cache));

    echo json_encode(['success' => true, 'message' => "Snapshot CH {$channel} berhasil diupload!"]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid Action']);
