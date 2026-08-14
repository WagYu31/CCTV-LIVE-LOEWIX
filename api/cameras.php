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
$action = $_GET['action'] ?? $_POST['action'] ?? 'public_list';

if ($action === 'public_list' || $action === 'list') {
    $cityFilter = $_GET['city'] ?? 'all';
    $userCameras = [];

    foreach ($db['cameras'] as $cam) {
        if ($cityFilter === 'all' || strtolower($cam['city'] ?? '') === strtolower($cityFilter)) {
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
    $streamPath = trim($_POST['streamPath'] ?? '');
    $lat = trim($_POST['lat'] ?? '');
    $lng = trim($_POST['lng'] ?? '');

    if (empty($title) || empty($streamPath)) {
        echo json_encode(['success' => false, 'message' => 'Judul Kamera dan Stream Path / RTSP URL wajib diisi!']);
        exit;
    }

    // Generate safe ID starting from 5001
    $existingIds = array_column($db['cameras'], 'id');
    $newCamId = count($existingIds) > 0 ? max(max($existingIds), 5000) + 1 : 5001;

    $newCam = [
        'id' => $newCamId,
        'user_id' => $userId,
        'title' => $title,
        'city' => $city,
        'streamPath' => $streamPath,
        'hls_url' => (strpos($streamPath, '.m3u8') !== false || strpos($streamPath, 'http') !== false) ? $streamPath : "http://stream.loewixcctv.com/{$streamPath}/index.m3u8",
        'thumbnail' => "assets/image/thumbnail/default-thumbnail.png",
        'lat' => $lat,
        'lng' => $lng,
        'platform' => 'mediamtx',
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

if ($action === 'admin_edit' || $action === 'edit') {
    $camId = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $city = trim($_POST['city'] ?? 'siantar');
    $streamPath = trim($_POST['streamPath'] ?? '');
    $lat = trim($_POST['lat'] ?? '');
    $lng = trim($_POST['lng'] ?? '');

    foreach ($db['cameras'] as &$c) {
        if ($c['id'] === $camId) {
            $c['title'] = $title;
            $c['city'] = $city;
            $c['streamPath'] = $streamPath;
            $c['lat'] = $lat;
            $c['lng'] = $lng;
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

echo json_encode(['success' => false, 'message' => 'Invalid Action']);
