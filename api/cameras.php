<?php
/**
 * CCTV Camera Management REST API
 * Enforces Customer Quotas & User Data Isolation
 * PT. LOEWIX INDONESIA
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$user = get_logged_in_user();
$db = get_db_data();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'list') {
    $cityFilter = $_GET['city'] ?? 'all';
    $userCameras = [];

    foreach ($db['cameras'] as $cam) {
        // Super Admin sees all cameras; Customer sees cameras matching their user_id
        if ($user['role'] === 'super_admin' || $cam['user_id'] == $user['id']) {
            if ($cityFilter === 'all' || $cam['city'] === $cityFilter) {
                $userCameras[] = $cam;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'cameras' => $userCameras,
        'quota' => [
            'total' => $user['cctv_quota'],
            'used' => count($userCameras)
        ]
    ]);
    exit;
}

if ($action === 'add') {
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Silakan Login Terlebih Dahulu!']);
        exit;
    }

    // Check active cameras count for quota enforcement
    $currentActiveCount = 0;
    foreach ($db['cameras'] as $c) {
        if ($c['user_id'] == $user['id']) {
            $currentActiveCount++;
        }
    }

    if ($user['role'] !== 'super_admin' && $currentActiveCount >= $user['cctv_quota']) {
        echo json_encode([
            'success' => false,
            'quota_exceeded' => true,
            'message' => "Batas Kuota Kamera Anda Telah Tercapai ({$currentActiveCount} / {$user['cctv_quota']} CCTV). Silakan hubungi Tim Sales PT. LOEWIX INDONESIA untuk melakukan Upgrade Kuota."
        ]);
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $city = trim($_POST['city'] ?? 'siantar');
    $streamPath = trim($_POST['streamPath'] ?? '');
    $lat = trim($_POST['lat'] ?? '');
    $lng = trim($_POST['lng'] ?? '');

    if (empty($title) || empty($streamPath)) {
        echo json_encode(['success' => false, 'message' => 'Judul Kamera dan Stream Path / RTSP URL wajib diisi!']);
        exit;
    }

    $newCamId = count($db['cameras']) > 0 ? max(array_column($db['cameras'], 'id')) + 1 : 1;
    $newCam = [
        'id' => $newCamId,
        'user_id' => $user['id'],
        'title' => $title,
        'city' => $city,
        'streamPath' => $streamPath,
        'hls_url' => "http://stream.loewixcctv.com/{$streamPath}/index.m3u8",
        'thumbnail' => "assets/image/thumbnail/default-thumbnail.png",
        'lat' => $lat,
        'lng' => $lng,
        'platform' => 'mediamtx',
        'status' => 'online',
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db['cameras'][] = $newCam;
    save_db_data($db);

    $updatedCount = $currentActiveCount + 1;
    echo json_encode([
        'success' => true,
        'message' => "Kamera {$title} Berhasil Ditambahkan! Kuota: {$updatedCount} / {$user['cctv_quota']} Terpakai.",
        'camera' => $newCam
    ]);
    exit;
}

if ($action === 'delete') {
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Silakan Login Terlebih Dahulu!']);
        exit;
    }

    $camId = (int) ($_POST['id'] ?? 0);
    $newCameras = [];
    $deleted = false;

    foreach ($db['cameras'] as $c) {
        if ($c['id'] === $camId && ($user['role'] === 'super_admin' || $c['user_id'] == $user['id'])) {
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
        echo json_encode(['success' => false, 'message' => 'Kamera tidak ditemukan atau Anda tidak memiliki akses.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid Action']);
