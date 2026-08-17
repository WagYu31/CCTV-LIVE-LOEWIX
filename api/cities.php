<?php
/**
 * Master Data Wilayah (Cities / Regions) Management API
 * PT. LOEWIX INDONESIA
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$db = get_db_data();

if (!isset($db['cities']) || !is_array($db['cities'])) {
    $db['cities'] = [];
}

// 1. GET: Return list of all cities
if ($method === 'GET') {
    echo json_encode([
        'success' => true,
        'cities' => array_values($db['cities']),
        'total' => count($db['cities'])
    ]);
    exit;
}

// 2. POST: Super Admin Add / Edit / Delete City
if ($method === 'POST') {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $id = strtolower(trim($_POST['id'] ?? ''));
        
        // Auto-generate ID/slug if not given
        if (empty($id)) {
            $id = preg_replace('/[^a-z0-9]/', '', strtolower($name));
        }

        $lat = floatval($_POST['lat'] ?? 0);
        $lng = floatval($_POST['lng'] ?? 0);
        $zoom = intval($_POST['zoom'] ?? 12);
        if ($zoom < 1 || $zoom > 19) $zoom = 12;

        if (empty($name) || empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Nama wilayah dan ID wajib diisi!']);
            exit;
        }

        // Check for duplicate ID
        foreach ($db['cities'] as $c) {
            if ($c['id'] === $id) {
                echo json_encode(['success' => false, 'message' => "Wilayah dengan ID '{$id}' sudah terdaftar!"]);
                exit;
            }
        }

        $newCity = [
            'id' => $id,
            'name' => $name,
            'lat' => $lat,
            'lng' => $lng,
            'zoom' => $zoom
        ];

        $db['cities'][] = $newCity;
        save_db_data($db);

        echo json_encode([
            'success' => true,
            'message' => "Wilayah '{$name}' berhasil ditambahkan ke sistem!",
            'city' => $newCity
        ]);
        exit;
    }

    if ($action === 'edit') {
        $id = strtolower(trim($_POST['id'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $lat = floatval($_POST['lat'] ?? 0);
        $lng = floatval($_POST['lng'] ?? 0);
        $zoom = intval($_POST['zoom'] ?? 12);

        if (empty($id) || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'ID dan Nama wilayah wajib diisi!']);
            exit;
        }

        $found = false;
        foreach ($db['cities'] as &$c) {
            if ($c['id'] === $id) {
                $c['name'] = $name;
                $c['lat'] = $lat;
                $c['lng'] = $lng;
                $c['zoom'] = $zoom;
                $found = true;
                break;
            }
        }
        unset($c);

        if (!$found) {
            echo json_encode(['success' => false, 'message' => "Wilayah dengan ID '{$id}' tidak ditemukan!"]);
            exit;
        }

        save_db_data($db);

        echo json_encode([
            'success' => true,
            'message' => "Wilayah '{$name}' berhasil diperbarui!"
        ]);
        exit;
    }

    if ($action === 'delete') {
        $id = strtolower(trim($_POST['id'] ?? ''));

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID wilayah wajib disertakan!']);
            exit;
        }

        // Prevent deleting last city or active city with cameras
        $activeCamerasInCity = 0;
        foreach ($db['cameras'] as $cam) {
            if (strtolower($cam['city'] ?? '') === $id) {
                $activeCamerasInCity++;
            }
        }

        if ($activeCamerasInCity > 0) {
            echo json_encode([
                'success' => false, 
                'message' => "Wilayah ini tidak dapat dihapus karena masih digunakan oleh {$activeCamerasInCity} kamera aktif!"
            ]);
            exit;
        }

        $newCities = [];
        $deletedName = '';
        foreach ($db['cities'] as $c) {
            if ($c['id'] === $id) {
                $deletedName = $c['name'];
            } else {
                $newCities[] = $c;
            }
        }

        $db['cities'] = $newCities;
        save_db_data($db);

        echo json_encode([
            'success' => true,
            'message' => "Wilayah '{$deletedName}' berhasil dihapus dari sistem!"
        ]);
        exit;
    }
}
