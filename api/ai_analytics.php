<?php
/**
 * Loewix AI Vision Analytics Suite API
 * Real-Time Face Recognition & ANPR (Automatic Number Plate Recognition)
 * PT. LOEWIX INDONESIA
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? 'get_ai_data');
$user = get_logged_in_user();

$db = get_db_data();

// Initialize AI collections in db if not present
if (!isset($db['ai_faces']) || !is_array($db['ai_faces'])) {
    $db['ai_faces'] = [
        [
            'id' => 1,
            'user_id' => 3,
            'name' => 'Bambang Supriyanto',
            'category' => 'vip', // vip, employee, resident, blacklist, guest
            'role_title' => 'Direktur Operasional',
            'photo' => 'assets/image/avatar-default.png',
            'notes' => 'Akses penuh VIP 24/7',
            'created_at' => '2026-08-20 10:00:00'
        ],
        [
            'id' => 2,
            'user_id' => 3,
            'name' => 'Siti Rahmawati',
            'category' => 'employee',
            'role_title' => 'Staff Administrasi',
            'photo' => 'assets/image/avatar-default.png',
            'notes' => 'Jam kerja 08:00 - 17:00 WIB',
            'created_at' => '2026-08-21 11:30:00'
        ],
        [
            'id' => 3,
            'user_id' => 3,
            'name' => 'Tersangka Residu DPO (Peringatan)',
            'category' => 'blacklist',
            'role_title' => 'DPO Pencurian Spion',
            'photo' => 'assets/image/avatar-default.png',
            'notes' => 'Segera amankan atau hubungi security jika terdeteksi!',
            'created_at' => '2026-08-25 14:15:00'
        ]
    ];
}

if (!isset($db['ai_plates']) || !is_array($db['ai_plates'])) {
    $db['ai_plates'] = [
        [
            'id' => 1,
            'user_id' => 3,
            'plate_number' => 'B 1234 YMH',
            'owner_name' => 'Bambang Supriyanto',
            'vehicle_type' => 'car', // car, motorcycle, truck
            'vehicle_model' => 'Toyota Alphard Hitam',
            'category' => 'vip', // vip, employee, resident, guest, blacklist
            'notes' => 'Slot Parkir VIP A-01',
            'created_at' => '2026-08-20 10:05:00'
        ],
        [
            'id' => 2,
            'user_id' => 3,
            'plate_number' => 'B 5678 DDS',
            'owner_name' => 'Operasional Kantor Yamaha',
            'vehicle_type' => 'car',
            'vehicle_model' => 'Toyota Innova Zenix Putih',
            'category' => 'employee',
            'notes' => 'Kendaraan Dinas Pool',
            'created_at' => '2026-08-22 09:00:00'
        ],
        [
            'id' => 3,
            'user_id' => 3,
            'plate_number' => 'B 9999 DPO',
            'owner_name' => 'Plat Dicurigai',
            'vehicle_type' => 'motorcycle',
            'vehicle_model' => 'Honda Beat Hitam',
            'category' => 'blacklist',
            'notes' => 'Telah 2x melakukan pengintaian tanpa izin',
            'created_at' => '2026-08-26 15:40:00'
        ]
    ];
}

if (!isset($db['ai_logs']) || !is_array($db['ai_logs'])) {
    $db['ai_logs'] = [
        [
            'id' => 1,
            'user_id' => 3,
            'type' => 'face', // face | anpr
            'camera_id' => 5002,
            'camera_title' => 'CAM LOEWIX JAKARTA 1 - LOBBY UTAMA',
            'label' => 'Bambang Supriyanto',
            'category' => 'vip',
            'confidence' => 96.8,
            'snapshot' => '',
            'details' => 'Terdeteksi di Lobby Utama • Akses Gate Terbuka Otomatis',
            'timestamp' => date('Y-m-d H:i:s', strtotime('-15 minutes'))
        ],
        [
            'id' => 2,
            'user_id' => 3,
            'type' => 'anpr',
            'camera_id' => 5002,
            'camera_title' => 'CAM LOEWIX GATE MASUK',
            'label' => 'B 1234 YMH',
            'category' => 'vip',
            'confidence' => 98.4,
            'snapshot' => '',
            'details' => 'Toyota Alphard Hitam • Palang Pintu Masuk Otomatis Buka',
            'timestamp' => date('Y-m-d H:i:s', strtotime('-18 minutes'))
        ],
        [
            'id' => 3,
            'user_id' => 3,
            'type' => 'anpr',
            'camera_id' => 5002,
            'camera_title' => 'CAM LOEWIX GATE MASUK',
            'label' => 'B 5678 DDS',
            'category' => 'employee',
            'confidence' => 97.2,
            'snapshot' => '',
            'details' => 'Toyota Innova Zenix Putih • Masuk Area Parkir Karyawan',
            'timestamp' => date('Y-m-d H:i:s', strtotime('-45 minutes'))
        ]
    ];
}

if (!isset($db['ai_settings']) || !is_array($db['ai_settings'])) {
    $db['ai_settings'] = [
        'face_recognition_enabled' => true,
        'anpr_enabled' => true,
        'sound_alert_enabled' => true,
        'auto_gate_trigger' => true,
        'min_confidence_face' => 85,
        'min_confidence_anpr' => 80
    ];
}

$userId = $user ? (int)$user['id'] : (isset($_GET['user_id']) ? (int)$_GET['user_id'] : 3);
$isSuperAdmin = ($user && $user['role'] === 'super_admin');

// 1. GET ALL AI DATA
if ($action === 'get_ai_data') {
    $faces = [];
    $plates = [];
    $logs = [];

    foreach ($db['ai_faces'] as $f) {
        if ($isSuperAdmin || (int)($f['user_id'] ?? 0) === $userId || (int)($f['user_id'] ?? 0) === 0) {
            $faces[] = $f;
        }
    }

    foreach ($db['ai_plates'] as $p) {
        if ($isSuperAdmin || (int)($p['user_id'] ?? 0) === $userId || (int)($p['user_id'] ?? 0) === 0) {
            $plates[] = $p;
        }
    }

    foreach ($db['ai_logs'] as $l) {
        if ($isSuperAdmin || (int)($l['user_id'] ?? 0) === $userId || (int)($l['user_id'] ?? 0) === 0) {
            $logs[] = $l;
        }
    }

    // Sort logs newest first
    usort($logs, function($a, $b) {
        return strcmp($b['timestamp'] ?? '', $a['timestamp'] ?? '');
    });

    echo json_encode([
        'success' => true,
        'faces' => $faces,
        'plates' => $plates,
        'logs' => $logs,
        'settings' => $db['ai_settings'] ?? [],
        'stats' => [
            'total_faces' => count($faces),
            'total_plates' => count($plates),
            'total_detections_today' => count($logs),
            'blacklist_alerts' => count(array_filter($logs, fn($l) => ($l['category'] ?? '') === 'blacklist'))
        ]
    ]);
    exit;
}

// 2. REGISTER / UPDATE FACE
if ($action === 'register_face' || $action === 'update_face') {
    $editId = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'employee'); // vip, employee, resident, blacklist, guest
    $roleTitle = trim($_POST['role_title'] ?? 'Tamu Terdaftar');
    $photo = trim($_POST['photo'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Nama lengkap wajib diisi.']);
        exit;
    }

    if ($editId > 0) {
        $found = false;
        foreach ($db['ai_faces'] as &$f) {
            if ((int)$f['id'] === $editId) {
                $f['name'] = $name;
                $f['category'] = $category;
                $f['role_title'] = $roleTitle;
                if (!empty($photo)) {
                    $f['photo'] = $photo;
                }
                $f['notes'] = $notes;
                $f['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                $savedFace = $f;
                break;
            }
        }
        if ($found) {
            save_db_data($db);
            echo json_encode(['success' => true, 'message' => 'Data wajah berhasil diperbarui & di-rescan!', 'face' => $savedFace]);
            exit;
        }
    }

    $existingIds = array_column($db['ai_faces'], 'id');
    $newId = count($existingIds) > 0 ? max($existingIds) + 1 : 1;

    $newFace = [
        'id' => $newId,
        'user_id' => $userId,
        'name' => $name,
        'category' => $category,
        'role_title' => $roleTitle,
        'photo' => !empty($photo) ? $photo : 'assets/image/avatar-default.png',
        'notes' => $notes,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db['ai_faces'][] = $newFace;
    save_db_data($db);

    echo json_encode(['success' => true, 'message' => 'Data wajah berhasil didaftarkan!', 'face' => $newFace]);
    exit;
}

// 3. REGISTER NEW VEHICLE PLATE (ANPR)
if ($action === 'register_plate') {
    $plateNumber = strtoupper(trim($_POST['plate_number'] ?? ''));
    $ownerName = trim($_POST['owner_name'] ?? '');
    $vehicleType = trim($_POST['vehicle_type'] ?? 'car'); // car, motorcycle, truck
    $vehicleModel = trim($_POST['vehicle_model'] ?? 'Kendaraan');
    $category = trim($_POST['category'] ?? 'resident'); // vip, employee, resident, guest, blacklist
    $notes = trim($_POST['notes'] ?? '');

    if (empty($plateNumber)) {
        echo json_encode(['success' => false, 'message' => 'Nomor Plat Kendaraan wajib diisi.']);
        exit;
    }

    $existingIds = array_column($db['ai_plates'], 'id');
    $newId = count($existingIds) > 0 ? max($existingIds) + 1 : 1;

    $newPlate = [
        'id' => $newId,
        'user_id' => $userId,
        'plate_number' => $plateNumber,
        'owner_name' => $ownerName,
        'vehicle_type' => $vehicleType,
        'vehicle_model' => $vehicleModel,
        'category' => $category,
        'notes' => $notes,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db['ai_plates'][] = $newPlate;
    save_db_data($db);

    echo json_encode(['success' => true, 'message' => 'Nomor plat kendaraan berhasil didaftarkan!', 'plate' => $newPlate]);
    exit;
}

// 4. LOG REAL-TIME DETECTION EVENT (FROM CLIENT / WEBCAM / RTSP SAMPLER)
if ($action === 'log_detection') {
    $type = trim($_POST['type'] ?? 'face'); // face | anpr
    $cameraId = (int)($_POST['camera_id'] ?? 5002);
    $cameraTitle = trim($_POST['camera_title'] ?? 'CAM CCTV LOEWIX');
    $label = trim($_POST['label'] ?? 'Unknown');
    $category = trim($_POST['category'] ?? 'unknown');
    $confidence = (float)($_POST['confidence'] ?? 95.0);
    $snapshot = trim($_POST['snapshot'] ?? '');
    $details = trim($_POST['details'] ?? 'Terdeteksi oleh AI Scanner');

    $existingIds = array_column($db['ai_logs'], 'id');
    $newId = count($existingIds) > 0 ? max($existingIds) + 1 : 1;

    $newLog = [
        'id' => $newId,
        'user_id' => $userId,
        'type' => $type,
        'camera_id' => $cameraId,
        'camera_title' => $cameraTitle,
        'label' => $label,
        'category' => $category,
        'confidence' => $confidence,
        'snapshot' => $snapshot,
        'details' => $details,
        'timestamp' => !empty($_POST['timestamp']) ? trim($_POST['timestamp']) : date('Y-m-d H:i:s')
    ];

    array_unshift($db['ai_logs'], $newLog);

    // Keep max 200 logs
    if (count($db['ai_logs']) > 200) {
        $db['ai_logs'] = array_slice($db['ai_logs'], 0, 200);
    }

    save_db_data($db);

    echo json_encode(['success' => true, 'log' => $newLog]);
    exit;
}

// 5. DELETE ENTITY
if ($action === 'delete_face') {
    $faceId = (int)($_POST['id'] ?? 0);
    $db['ai_faces'] = array_values(array_filter($db['ai_faces'], fn($f) => (int)$f['id'] !== $faceId));
    save_db_data($db);
    echo json_encode(['success' => true, 'message' => 'Wajah terdaftar berhasil dihapus.']);
    exit;
}

if ($action === 'delete_plate') {
    $plateId = (int)($_POST['id'] ?? 0);
    $db['ai_plates'] = array_values(array_filter($db['ai_plates'], fn($p) => (int)$p['id'] !== $plateId));
    save_db_data($db);
    echo json_encode(['success' => true, 'message' => 'Nomor plat terdaftar berhasil dihapus.']);
    exit;
}

if ($action === 'clear_logs') {
    $db['ai_logs'] = [];
    save_db_data($db);
    echo json_encode(['success' => true, 'message' => 'Seluruh riwayat deteksi AI berhasil dibersihkan.']);
    exit;
}

// 6. UPDATE AI SETTINGS
if ($action === 'update_ai_settings') {
    $faceRec = isset($_POST['face_recognition_enabled']) ? (bool)$_POST['face_recognition_enabled'] : true;
    $anpr = isset($_POST['anpr_enabled']) ? (bool)$_POST['anpr_enabled'] : true;
    $sound = isset($_POST['sound_alert_enabled']) ? (bool)$_POST['sound_alert_enabled'] : true;
    $autoGate = isset($_POST['auto_gate_trigger']) ? (bool)$_POST['auto_gate_trigger'] : true;

    $db['ai_settings'] = [
        'face_recognition_enabled' => $faceRec,
        'anpr_enabled' => $anpr,
        'sound_alert_enabled' => $sound,
        'auto_gate_trigger' => $autoGate,
        'min_confidence_face' => (int)($_POST['min_confidence_face'] ?? 85),
        'min_confidence_anpr' => (int)($_POST['min_confidence_anpr'] ?? 80)
    ];

    save_db_data($db);
    echo json_encode(['success' => true, 'settings' => $db['ai_settings']]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action tidak dikenali.']);
exit;
