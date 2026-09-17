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
$db = get_db_data();
$user = get_logged_in_user();
if (!$user && !empty($_REQUEST['user_id'])) {
    $reqUserId = (int)$_REQUEST['user_id'];
    if (isset($db['users']) && is_array($db['users'])) {
        foreach ($db['users'] as $u) {
            if ((int)$u['id'] === $reqUserId && ($u['status'] ?? 'active') === 'active') {
                $user = $u;
                break;
            }
        }
    }
}

// Initialize AI collections in db if not present
if (!isset($db['ai_faces']) || !is_array($db['ai_faces'])) {
    $db['ai_faces'] = [
        [
            'id' => 1,
            'user_id' => 1,
            'name' => 'Wahyu Utomo',
            'category' => 'vip', // vip, employee, resident, blacklist, guest
            'role_title' => 'Super Admin & Owner',
            'photo' => 'assets/uploads/faces/face_wahyu_utomo.jpg',
            'notes' => 'Akses penuh VIP Master & Owner Loewix 24/7',
            'created_at' => '2026-08-20 10:00:00'
        ],
        [
            'id' => 2,
            'user_id' => 3,
            'name' => 'Bambang Supriyanto',
            'category' => 'vip',
            'role_title' => 'Direktur Operasional',
            'photo' => 'assets/image/avatar-default.png',
            'notes' => 'Akses penuh VIP 24/7',
            'created_at' => '2026-08-20 10:00:00'
        ],
        [
            'id' => 3,
            'user_id' => 3,
            'name' => 'Siti Rahmawati',
            'category' => 'employee',
            'role_title' => 'Staff Administrasi',
            'photo' => 'assets/image/avatar-default.png',
            'notes' => 'Jam kerja 08:00 - 17:00 WIB',
            'created_at' => '2026-08-21 11:30:00'
        ],
        [
            'id' => 4,
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
        $extraPhotos = [];
        $safeName = preg_replace('/[^a-zA-Z0-9 _\-]/', '', trim($f['name'] ?? ''));
        if (!empty($safeName)) {
            $dir = realpath(__DIR__ . '/..') . '/assets/uploads/faces/' . $safeName;
            if (is_dir($dir)) {
                $files = glob($dir . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG}', GLOB_BRACE) ?: [];
                foreach ($files as $file) {
                    $rel = 'assets/uploads/faces/' . $safeName . '/' . basename($file);
                    if ($rel !== ($f['photo'] ?? '')) {
                        $extraPhotos[] = $rel;
                    }
                }
            }
        }
        if (stripos($f['name'] ?? '', 'wahyu') !== false) {
            $f['category'] = 'vip';
            $f['role_title'] = 'Super Admin & Owner';
            $candidateDirect = 'assets/uploads/faces/face_wahyu_utomo.jpg';
            if (file_exists(realpath(__DIR__ . '/..') . '/' . $candidateDirect) && !in_array($candidateDirect, $extraPhotos) && $candidateDirect !== ($f['photo'] ?? '')) {
                $extraPhotos[] = $candidateDirect;
            }
            foreach (['Wahyu', 'Wahyu Utomo'] as $wDirName) {
                $wDir = realpath(__DIR__ . '/..') . '/assets/uploads/faces/' . $wDirName;
                if (is_dir($wDir)) {
                    $wFiles = glob($wDir . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG}', GLOB_BRACE) ?: [];
                    foreach ($wFiles as $wFile) {
                        $rel = 'assets/uploads/faces/' . $wDirName . '/' . basename($wFile);
                        if (!in_array($rel, $extraPhotos) && $rel !== ($f['photo'] ?? '')) {
                            $extraPhotos[] = $rel;
                        }
                    }
                }
            }
        }
        $f['extra_photos'] = array_values(array_unique($extraPhotos));

        // Provide base64 data URIs for zero-latency, zero-CORS face descriptor building
        $projectRoot = realpath(__DIR__ . '/..');
        $photoRel = $f['photo'] ?? '';
        if (str_starts_with($photoRel, 'data:image')) {
            $f['photo_b64'] = $photoRel;
        } else {
            $photoAbs = $projectRoot . '/' . ltrim($photoRel, '/');
            if (!file_exists($photoAbs)) {
                $photoAbs = __DIR__ . '/../' . ltrim($photoRel, '/');
            }
            if (file_exists($photoAbs) && !is_dir($photoAbs) && filesize($photoAbs) < 6000000) {
                $ext = strtolower(pathinfo($photoAbs, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg');
                $f['photo_b64'] = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoAbs));
            } else {
                $f['photo_b64'] = '';
            }
        }

        $extraB64 = [];
        foreach (array_slice($f['extra_photos'], 0, 5) as $ep) {
            $epAbs = $projectRoot . '/' . ltrim($ep, '/');
            if (file_exists($epAbs) && !is_dir($epAbs) && filesize($epAbs) < 600000) {
                $ext = strtolower(pathinfo($epAbs, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg');
                $extraB64[] = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($epAbs));
            }
        }
        $f['extra_photos_b64'] = $extraB64;

        $faces[] = $f;
    }

    foreach ($db['ai_plates'] as $p) {
        $plates[] = $p;
    }

    foreach ($db['ai_logs'] as $l) {
        if ($isSuperAdmin || (int)($l['user_id'] ?? 0) === $userId || (int)($l['user_id'] ?? 0) === 0) {
            $logs[] = $l;
        }
    }

    $cameras = [];
    if (isset($db['cameras']) && is_array($db['cameras'])) {
        foreach ($db['cameras'] as $cam) {
            if (!empty($cam['hls_url']) && strpos($cam['hls_url'], 'http://stream.loewixcctv.com') === 0) {
                $cam['hls_url'] = str_replace('http://', 'https://', $cam['hls_url']);
            }
            $cameras[] = $cam;
        }
    }

    $encFile = __DIR__ . '/../data/encoding.json';
    $encodings = [];
    if (file_exists($encFile)) {
        $encData = json_decode(file_get_contents($encFile), true);
        if (isset($encData['faces']) && is_array($encData['faces'])) {
            foreach ($encData['faces'] as $ef) {
                if (isset($ef['encoding']) && is_authentic_face_descriptor($ef['encoding'])) {
                    $encodings[] = $ef;
                }
            }
        }
    }

    echo json_encode([
        'success' => true,
        'faces' => $faces,
        'encodings' => $encodings,
        'plates' => $plates,
        'logs' => $logs,
        'cameras' => $cameras,
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

// DEEPFACE INTEGRATION: Sync face photos to DeepFace ArcFace FAISS DB
function syncFaceToDeepFaceDB($name, &$photoPath, $category = 'employee', $notes = '') {
    $projectRoot = realpath(__DIR__ . '/..');
    $deepfaceDBDir = $projectRoot . '/assets/uploads/faces';

    // Sanitize name for directory
    $safeName = preg_replace('/[^a-zA-Z0-9 _\-]/', '', trim($name));
    if (empty($safeName)) return false;

    $personDir = $deepfaceDBDir . '/' . $safeName;
    if (!is_dir($personDir)) {
        @mkdir($personDir, 0777, true);
    }

    $b64Payload = null;

    if (str_starts_with($photoPath, 'data:image') || strpos($photoPath, 'data:image') === 0) {
        $b64Payload = $photoPath;
        $parts = explode(',', $photoPath);
        if (count($parts) === 2) {
            $binary = base64_decode($parts[1]);
            if ($binary !== false) {
                $destFilename = 'face_' . date('Ymd_His') . '_' . rand(1000, 9999) . '.jpg';
                $destPath = $personDir . '/' . $destFilename;
                if (@file_put_contents($destPath, $binary)) {
                    $photoPath = 'assets/uploads/faces/' . $safeName . '/' . $destFilename;
                }
            }
        }
    } else {
        $absPhotoPath = $photoPath;
        if (!file_exists($absPhotoPath)) {
            $absPhotoPath = $projectRoot . '/' . ltrim($photoPath, '/');
        }
        if (file_exists($absPhotoPath)) {
            $ext = pathinfo($absPhotoPath, PATHINFO_EXTENSION) ?: 'jpg';
            $destFilename = 'face_' . date('Ymd_His') . '_' . rand(1000, 9999) . '.' . $ext;
            $destPath = $personDir . '/' . $destFilename;
            @copy($absPhotoPath, $destPath);
            $content = @file_get_contents($absPhotoPath);
            if ($content) {
                $b64Payload = 'data:image/jpeg;base64,' . base64_encode($content);
            }
        }
    }

    if ($b64Payload) {
        $ch = curl_init('http://127.0.0.1:5050/api/v1/faces/register');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode([
                'name' => $name,
                'category' => $category,
                'notes' => $notes,
                'img_b64' => $b64Payload
            ])
        ]);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpCode === 200);
    }

    return false;
}

function is_authentic_face_descriptor($descriptor) {
    if (!is_array($descriptor) || count($descriptor) !== 128) return false;
    $sumSq = 0.0;
    $maxVal = 0.0;
    $mean = 0.0;
    foreach ($descriptor as $v) {
        $fv = (float)$v;
        $sumSq += ($fv * $fv);
        $mean += $fv;
        $absV = abs($fv);
        if ($absV > $maxVal) $maxVal = $absV;
    }
    $mean /= 128;
    // Compute variance to reject flat/dummy embeddings
    $variance = 0.0;
    $uniqueVals = [];
    foreach ($descriptor as $v) {
        $fv = (float)$v;
        $variance += ($fv - $mean) * ($fv - $mean);
        $uniqueVals[round($fv, 6)] = true;
    }
    $variance /= 128;
    $uniqueCount = count($uniqueVals);
    // Real ResNet embeddings: sumSq 1.0-5.0, maxVal <= 0.85, variance > 0.005, mostly unique values
    return ($sumSq >= 0.50 && $sumSq <= 5.0 && $maxVal <= 0.85 && $variance >= 0.005 && $uniqueCount >= 50);
}

function syncEncodingJSON($name, $descriptor, $category = 'employee', $role = 'Staff', $photo = '', $oldName = '') {
    if (!is_authentic_face_descriptor($descriptor)) return;
    $encFile = __DIR__ . '/../data/encoding.json';
    $data = file_exists($encFile) ? json_decode(file_get_contents($encFile), true) : null;
    if (!is_array($data)) {
        $data = ['description' => 'Loewix CCTV AI Vision Face Encodings Database', 'faces' => []];
    }
    if (!isset($data['faces']) || !is_array($data['faces'])) {
        $data['faces'] = [];
    }
    $updated = false;
    foreach ($data['faces'] as &$ef) {
        $curName = strtolower($ef['name'] ?? '');
        if ($curName === strtolower($name) || (!empty($oldName) && $curName === strtolower($oldName))) {
            $ef['name'] = $name;
            $ef['encoding'] = array_map('floatval', $descriptor);
            $ef['category'] = $category;
            $ef['role'] = $role;
            if ($photo) $ef['photo'] = $photo;
            $ef['updated_at'] = date('Y-m-d H:i:s');
            $updated = true;
            break;
        }
    }
    if (!$updated) {
        $data['faces'][] = [
            'name' => $name,
            'category' => $category,
            'role' => $role,
            'photo' => $photo,
            'encoding' => array_map('floatval', $descriptor),
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
    $data['updated_at'] = date('Y-m-d H:i:s');
    @file_put_contents($encFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// BATCH DESCRIPTOR SYNC: Upgrade database with authentic ResNet embeddings computed in client WebGL
if ($action === 'sync_descriptors_batch') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload) || !isset($payload['descriptors']) || !is_array($payload['descriptors'])) {
        echo json_encode(['success' => false, 'message' => 'Payload tidak valid']);
        exit;
    }
    $synced = 0;
    foreach ($payload['descriptors'] as $item) {
        $name = trim($item['name'] ?? '');
        $desc = $item['descriptor'] ?? null;
        $cat = $item['category'] ?? 'employee';
        $role = $item['role_title'] ?? 'Staff';
        $photo = $item['photo'] ?? '';
        if ($name && is_authentic_face_descriptor($desc)) {
            syncEncodingJSON($name, $desc, $cat, $role, $photo);
            foreach ($db['ai_faces'] as &$f) {
                if (strtolower($f['name'] ?? '') === strtolower($name)) {
                    $f['descriptor'] = array_map('floatval', $desc);
                    break;
                }
            }
            $synced++;
        }
    }
    if ($synced > 0) {
        save_db_data($db);
    }
    echo json_encode(['success' => true, 'synced_count' => $synced]);
    exit;
}

// DEEPFACE: Health check proxy
if ($action === 'deepface_status') {
    $ch = curl_init('http://localhost:5050/api/deepface/health');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_HTTPHEADER => ['Accept: application/json']
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        echo json_encode(['success' => true, 'deepface' => $data]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'DeepFace server tidak tersedia.',
            'error' => $curlError ?: "HTTP $httpCode",
            'hint' => 'Jalankan: python3 deepface_server.py'
        ]);
    }
    exit;
}

// DEEPFACE: Sync all registered face photos to DeepFace FAISS DB
if ($action === 'sync_face_db') {
    // 1. Tell Python server to import all faces from data/loewix_db.json directly
    $pyCh = curl_init('http://127.0.0.1:5050/api/v1/system/sync-web-faces');
    curl_setopt_array($pyCh, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CONNECTTIMEOUT => 3
    ]);
    @curl_exec($pyCh);
    @curl_close($pyCh);

    $synced = 0;
    $errors = 0;
    $details = [];
    foreach ($db['ai_faces'] as &$face) {
        $fName = $face['name'] ?? '';
        $fPhoto = $face['photo'] ?? '';
        $fCat = $face['category'] ?? 'employee';
        $fNotes = $face['notes'] ?? '';
        if (!empty($fPhoto) && $fPhoto !== 'assets/image/avatar-default.png') {
            if (syncFaceToDeepFaceDB($fName, $face['photo'], $fCat, $fNotes)) {
                $synced++;
                $details[] = "$fName (OK)";
            } else {
                $errors++;
                $details[] = "$fName (Gagal)";
            }
        }
    }
    save_db_data($db);

    echo json_encode([
        'success' => true,
        'message' => "Sinkronisasi selesai: $synced wajah berhasil didaftarkan ke AI FAISS ArcFace.",
        'synced' => $synced,
        'errors' => $errors,
        'details' => $details
    ]);
    exit;
}

// PROXY FACE IMAGE WITH FULL CORS HEADERS
if ($action === 'get_face_image') {
    $faceId = (int)($_GET['id'] ?? 0);
    $path = trim($_GET['path'] ?? '');
    $targetPath = '';
    $projectRoot = realpath(__DIR__ . '/..');

    if ($faceId > 0 && !empty($db['ai_faces'])) {
        foreach ($db['ai_faces'] as $f) {
            if ((int)$f['id'] === $faceId) {
                $targetPath = $f['photo'] ?? '';
                break;
            }
        }
    } elseif (!empty($path)) {
        $targetPath = $path;
    }

    if (!empty($targetPath)) {
        if (str_starts_with($targetPath, 'data:image')) {
            $parts = explode(',', $targetPath);
            if (count($parts) === 2) {
                header('Access-Control-Allow-Origin: *');
                header('Content-Type: image/jpeg');
                echo base64_decode($parts[1]);
                exit;
            }
        }
        $abs = $projectRoot . '/' . ltrim($targetPath, '/');
        if (!file_exists($abs)) {
            $abs = __DIR__ . '/../' . ltrim($targetPath, '/');
        }
        if (file_exists($abs) && !is_dir($abs)) {
            $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
            $mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg');
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: ' . $mime);
            readfile($abs);
            exit;
        }
    }
    header('HTTP/1.1 404 Not Found');
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

    if (stripos($name, 'wahyu') !== false) {
        $category = 'vip';
        $roleTitle = 'Super Admin & Owner';
    }

    $descriptor = null;
    if (!empty($_POST['descriptor'])) {
        $rawDesc = is_array($_POST['descriptor']) ? $_POST['descriptor'] : json_decode($_POST['descriptor'], true);
        if (is_array($rawDesc) && count($rawDesc) === 128) {
            $descriptor = array_map('floatval', $rawDesc);
        }
    }

    if ($editId > 0) {
        $found = false;
        foreach ($db['ai_faces'] as &$f) {
            if ((int)$f['id'] === $editId) {
                $oldName = $f['name'] ?? '';
                $f['name'] = $name;
                $f['category'] = $category;
                $f['role_title'] = $roleTitle;
                if (!empty($photo)) {
                    // Auto-sync photo to DeepFace ArcFace FAISS DB & convert base64 to clean file path
                    syncFaceToDeepFaceDB($name, $photo, $category, $notes);
                    $f['photo'] = $photo;
                }
                if ($descriptor !== null) {
                    $f['descriptor'] = $descriptor;
                }
                $effectiveDesc = $descriptor ?: ($f['descriptor'] ?? null);
                if ($effectiveDesc) {
                    syncEncodingJSON($name, $effectiveDesc, $category, $f['role_title'] ?? 'Staff', $f['photo'] ?? '', $oldName);
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
            echo json_encode(['success' => true, 'message' => 'Data wajah berhasil diperbarui & disinkronkan ke AI ArcFace!', 'face' => $savedFace]);
            exit;
        }
    }

    $existingIds = array_column($db['ai_faces'], 'id');
    $newId = count($existingIds) > 0 ? max($existingIds) + 1 : 1;

    // Auto-sync new face photo to DeepFace ArcFace FAISS DB before saving
    if (!empty($photo) && $photo !== 'assets/image/avatar-default.png') {
        syncFaceToDeepFaceDB($name, $photo, $category, $notes);
    }

    if ($descriptor !== null) {
        syncEncodingJSON($name, $descriptor, $category, $roleTitle, $photo);
    }

    $newFace = [
        'id' => $newId,
        'user_id' => $userId,
        'name' => $name,
        'category' => $category,
        'role_title' => $roleTitle,
        'photo' => !empty($photo) ? $photo : 'assets/image/avatar-default.png',
        'descriptor' => $descriptor,
        'notes' => $notes,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db['ai_faces'][] = $newFace;
    save_db_data($db);

    echo json_encode(['success' => true, 'message' => 'Data wajah berhasil didaftarkan & disinkronkan ke AI ArcFace!', 'face' => $newFace]);
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
    $registeredPhoto = trim($_POST['registered_photo'] ?? '');
    $gender = trim($_POST['gender'] ?? 'Male');
    $mask = trim($_POST['mask'] ?? 'Not worn');
    $details = trim($_POST['details'] ?? 'Terdeteksi oleh AI Scanner');
    // DeepFace attributes
    $age = !empty($_POST['age']) ? (int)$_POST['age'] : null;
    $emotion = trim($_POST['emotion'] ?? '');
    $engine = trim($_POST['engine'] ?? '');

    // If base64 snapshot provided, save to disk to optimize database performance
    if (!empty($snapshot) && strpos($snapshot, 'data:image') === 0) {
        $uploadDir = __DIR__ . '/../assets/snapshots/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        $parts = explode(',', $snapshot);
        if (count($parts) === 2) {
            $binaryData = base64_decode($parts[1]);
            if ($binaryData !== false) {
                $filename = 'snap_' . time() . '_' . rand(1000, 9999) . '.jpg';
                if (@file_put_contents($uploadDir . $filename, $binaryData)) {
                    $snapshot = 'assets/snapshots/' . $filename;
                }
            }
        }
    }

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
        'registered_photo' => $registeredPhoto,
        'gender' => $gender,
        'mask' => $mask,
        'age' => $age,
        'emotion' => $emotion,
        'engine' => $engine,
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
    $deletedName = '';
    foreach ($db['ai_faces'] as $f) {
        if ((int)$f['id'] === $faceId) {
            $deletedName = $f['name'] ?? '';
            break;
        }
    }
    $db['ai_faces'] = array_values(array_filter($db['ai_faces'], fn($f) => (int)$f['id'] !== $faceId));
    save_db_data($db);

    if (!empty($deletedName)) {
        $encFile = __DIR__ . '/../data/encoding.json';
        if (file_exists($encFile)) {
            $encData = json_decode(file_get_contents($encFile), true);
            if (isset($encData['faces']) && is_array($encData['faces'])) {
                $encData['faces'] = array_values(array_filter($encData['faces'], fn($ef) => strtolower($ef['name'] ?? '') !== strtolower($deletedName)));
                @file_put_contents($encFile, json_encode($encData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        }
    }
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
