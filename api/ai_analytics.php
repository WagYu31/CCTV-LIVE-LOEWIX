<?php
/**
 * Loewix AI Vision Analytics Suite API
 * Real-Time Face Recognition & ANPR (Automatic Number Plate Recognition)
 * PT. LOEWIX INDONESIA
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0');

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
    $db['ai_faces'] = [];
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
    // Auto-sync ai_faces from data/encoding.json so production server DB is always up to date
    $encFile = __DIR__ . '/../data/encoding.json';
    if (file_exists($encFile)) {
        $encData = json_decode(file_get_contents($encFile), true);
        if (isset($encData['faces']) && is_array($encData['faces']) && empty($encData['faces'])) {
            // When encoding.json has been intentionally cleared, wipe server database faces too
            if (!empty($db['ai_faces'])) {
                $db['ai_faces'] = [];
                save_db_data($db);
            }
        } else if (!empty($encData['faces']) && is_array($encData['faces'])) {
            $existingNames = [];
            $initialCount = count($db['ai_faces'] ?? []);
                foreach (($db['ai_faces'] ?? []) as $f) {
                    $existingNames[strtolower(trim($f['name'] ?? ''))] = true;
                }
                // Purge obsolete dummy entries (Bambang, Siti, Tersangka)
                $db['ai_faces'] = array_values(array_filter($db['ai_faces'] ?? [], function($f) {
                    $n = strtolower(trim($f['name'] ?? ''));
                    return !in_array($n, ['bambang supriyanto', 'siti rahmawati', 'tersangka residu dpo (peringatan)']);
                }));
                $dbUpdated = (count($db['ai_faces']) !== $initialCount);

                $maxId = 0;
                foreach ($db['ai_faces'] as &$f) {
                    if (!isset($f['id']) || empty($f['id'])) {
                        $f['id'] = ++$maxId;
                        $dbUpdated = true;
                    } else if ((int)$f['id'] > $maxId) {
                        $maxId = (int)$f['id'];
                    }
                }
                unset($f);

                foreach ($db['ai_faces'] as &$efCheck) {
                    $curName = strtolower(trim($efCheck['name'] ?? ''));
                    if ($curName === 'wagyu' || $curName === 'yu' || str_contains($curName, 'wahyu')) {
                        $efCheck['category'] = 'vip';
                        $efCheck['role_title'] = 'Super Admin & Owner';
                        $dbUpdated = true;
                    }
                }
                unset($efCheck);

                foreach ($encData['faces'] as $ef) {
                    $efName = trim($ef['name'] ?? '');
                    if (!empty($efName) && !isset($existingNames[strtolower($efName)])) {
                        $isOwnerSync = (
                            stripos($efName, 'wahyu') !== false || 
                            stripos($efName, 'wagyu') !== false || 
                            strtolower($efName) === 'yu'
                        );
                        $newFace = [
                            'id' => ++$maxId,
                            'name' => $efName,
                            'category' => $isOwnerSync ? 'vip' : ($ef['category'] ?? 'employee'),
                            'role_title' => $isOwnerSync ? 'Super Admin & Owner' : ($ef['role'] ?? 'Staff'),
                            'photo' => $ef['photo'] ?? '',
                            'descriptor' => $ef['encoding'] ?? null,
                            'notes' => $isOwnerSync ? 'Super Admin Master & Owner Loewix 24/7' : 'Tersinkronisasi dari Database Biometrik Face AI',
                            'created_at' => $ef['created_at'] ?? date('Y-m-d H:i:s')
                        ];
                        $db['ai_faces'][] = $newFace;
                        $existingNames[strtolower($efName)] = true;
                        $dbUpdated = true;
                    }
                }
                if ($dbUpdated) {
                    save_db_data($db);
                }
            }
    }

    // Bidirectional sync: if $db['ai_faces'] has faces with descriptors, also ensure encoding.json has them
    // But do not run if encoding.json was intentionally emptied!
    if (!empty($db['ai_faces']) && (!isset($encData['faces']) || !empty($encData['faces']))) {
        $encUpdated = false;
        if (!isset($encData) || !is_array($encData)) {
            $encData = ['description' => 'Loewix CCTV AI Vision Face Encodings Database', 'faces' => []];
        }
        $existingEnc = [];
        foreach (($encData['faces'] ?? []) as $ef) {
            $existingEnc[strtolower(trim($ef['name'] ?? ''))] = true;
        }
        foreach ($db['ai_faces'] as $f) {
            $fn = strtolower(trim($f['name'] ?? ''));
            if (!empty($fn) && !isset($existingEnc[$fn]) && !empty($f['descriptor']) && is_array($f['descriptor'])) {
                $encData['faces'][] = [
                    'name' => $f['name'],
                    'category' => $f['category'] ?? 'employee',
                    'role' => $f['role_title'] ?? 'Staff',
                    'photo' => $f['photo'] ?? '',
                    'encoding' => array_map('floatval', $f['descriptor']),
                    'created_at' => $f['created_at'] ?? date('Y-m-d H:i:s')
                ];
                $existingEnc[$fn] = true;
                $encUpdated = true;
            }
        }
        if ($encUpdated) {
            $encData['updated_at'] = date('Y-m-d H:i:s');
            @file_put_contents($encFile, json_encode($encData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

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
        $isWahyuUser = (
            stripos($f['name'] ?? '', 'wahyu') !== false || 
            stripos($f['name'] ?? '', 'wagyu') !== false || 
            strtolower(trim($f['name'] ?? '')) === 'yu'
        );
        if ($isWahyuUser) {
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
            if (empty($cam['streamPath'])) {
                $cam['streamPath'] = 'cam_live_' . $cam['id'];
            }
            if (empty($cam['hls_url'])) {
                $cam['hls_url'] = "https://stream.loewixcctv.com/{$cam['streamPath']}/index.m3u8";
            } else if (strpos($cam['hls_url'], 'http://stream.loewixcctv.com') === 0) {
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
        @curl_close($ch);
        return ($httpCode === 200);
    }

    return false;
}

function is_authentic_face_descriptor($descriptor) {
    if (!is_array($descriptor) || count($descriptor) !== 128) return false;
    $sumSq = 0.0;
    $uniqueVals = [];
    foreach ($descriptor as $v) {
        $fv = (float)$v;
        $sumSq += ($fv * $fv);
        $uniqueVals[(string)round($fv, 4)] = true;
    }
    // Real 128D ResNet embeddings: sum of squares > 0.1 and not dummy constant
    return ($sumSq >= 0.10 && count($uniqueVals) >= 15);
}

function syncEncodingJSON($name, $descriptor, $category = 'employee', $role = 'Staff', $photo = '', $oldName = '') {
    $encFile = __DIR__ . '/../data/encoding.json';
    $data = file_exists($encFile) ? json_decode(file_get_contents($encFile), true) : null;
    if (!is_array($data)) {
        $data = ['description' => 'Loewix CCTV AI Vision Face Encodings Database', 'faces' => []];
    }
    if (!isset($data['faces']) || !is_array($data['faces'])) {
        $data['faces'] = [];
    }
    $updated = false;
    $validDesc = is_authentic_face_descriptor($descriptor) ? array_map('floatval', $descriptor) : null;
    foreach ($data['faces'] as &$ef) {
        $curName = strtolower($ef['name'] ?? '');
        if ($curName === strtolower($name) || (!empty($oldName) && $curName === strtolower($oldName))) {
            $ef['name'] = $name;
            if ($validDesc !== null) $ef['encoding'] = $validDesc;
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
            'encoding' => $validDesc,
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
    @curl_close($ch);

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

    if (stripos($name, 'wahyu') !== false || stripos($name, 'wagyu') !== false || strtolower($name) === 'yu') {
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
                syncEncodingJSON($name, $effectiveDesc, $category, $f['role_title'] ?? 'Staff', $f['photo'] ?? '', $oldName);
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

    // Always sync registered face to encoding.json so face is never lost across server sync cycles
    syncEncodingJSON($name, $descriptor, $category, $roleTitle, $photo);

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
    $saveSuccess = save_db_data($db);

    echo json_encode([
        'success' => true, 
        'db_saved' => $saveSuccess,
        'message' => 'Data wajah berhasil didaftarkan & disinkronkan ke AI ArcFace!', 
        'face' => $newFace
    ]);
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

if ($action === 'reset_all_faces') {
    $db['ai_faces'] = [];
    save_db_data($db);
    $encFile = __DIR__ . '/../data/encoding.json';
    @file_put_contents($encFile, json_encode([
        'description' => 'Loewix CCTV AI Vision Face Encodings Database',
        'updated_at' => date('Y-m-d H:i:s'),
        'faces' => []
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo json_encode(['success' => true, 'message' => 'Semua data biometrik wajah berhasil direset total.']);
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

// =========================================================================
// VISITOR & PERSONNEL INTELLIGENCE, FREQUENCY ANALYTICS & STRANGER RE-ID
// =========================================================================

function calc_face_descriptor_distance($desc1, $desc2) {
    if (!is_array($desc1) || !is_array($desc2) || count($desc1) !== 128 || count($desc2) !== 128) {
        return 999.0;
    }
    $sum = 0.0;
    for ($i = 0; $i < 128; $i++) {
        $diff = ((float)$desc1[$i]) - ((float)$desc2[$i]);
        $sum += ($diff * $diff);
    }
    return sqrt($sum);
}

// 7. LOG VISITOR / STRANGER DETECTION EVENT
if ($action === 'log_visitor_event') {
    $now = date('Y-m-d H:i:s');
    $today = date('Y-m-d');
    $time = date('H:i:s');

    $label = trim($_POST['label'] ?? 'Stranger');
    $category = trim($_POST['category'] ?? 'stranger'); // employee | vip | stranger | guest | blacklist
    $cameraId = (int)($_POST['camera_id'] ?? 5001);
    $cameraTitle = trim($_POST['camera_title'] ?? 'CCTV Camera');
    $direction = trim($_POST['direction'] ?? 'melintas'); // masuk | keluar | melintas
    $confidence = (float)($_POST['confidence'] ?? 95.0);
    $personId = trim($_POST['person_id'] ?? '');
    $snapshotRaw = trim($_POST['snapshot'] ?? '');
    $rawDescriptor = $_POST['descriptor'] ?? null;
    $descriptor = null;
    if (is_string($rawDescriptor)) {
        $descriptor = json_decode($rawDescriptor, true);
    } else if (is_array($rawDescriptor)) {
        $descriptor = $rawDescriptor;
    }

    // Save base64 snapshot to disk for permanent evidence storage
    $snapshotPath = '';
    if (!empty($snapshotRaw) && str_starts_with($snapshotRaw, 'data:image')) {
        $uploadDir = __DIR__ . '/../assets/uploads/snapshots/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        $parts = explode(',', $snapshotRaw);
        if (count($parts) === 2) {
            $bin = base64_decode($parts[1]);
            if ($bin !== false) {
                $filename = 'snap_vis_' . date('Ymd_His') . '_' . rand(100, 999) . '.jpg';
                if (@file_put_contents($uploadDir . $filename, $bin)) {
                    $snapshotPath = 'assets/uploads/snapshots/' . $filename;
                }
            }
        }
    } else if (!empty($snapshotRaw)) {
        $snapshotPath = $snapshotRaw;
    }

    if (!isset($db['visitor_profiles']) || !is_array($db['visitor_profiles'])) {
        $db['visitor_profiles'] = [];
    }

    $matchedProfile = null;
    $matchedIndex = -1;

    if ($category !== 'stranger') {
        // Employee / VIP / Registered match
        foreach ($db['visitor_profiles'] as $idx => $vp) {
            if ((!empty($personId) && ($vp['id'] == $personId)) || (strtolower(trim($vp['name'])) === strtolower($label))) {
                $matchedProfile = &$db['visitor_profiles'][$idx];
                $matchedIndex = $idx;
                break;
            }
        }
    } else {
        // Stranger re-identification via 128D Face Descriptor
        if (is_array($descriptor) && count($descriptor) === 128) {
            $bestDistance = 999.0;
            $bestIdx = -1;
            foreach ($db['visitor_profiles'] as $idx => $vp) {
                if (($vp['category'] ?? '') === 'stranger' && isset($vp['descriptor']) && is_array($vp['descriptor'])) {
                    $dist = calc_face_descriptor_distance($descriptor, $vp['descriptor']);
                    if ($dist < $bestDistance) {
                        $bestDistance = $dist;
                        $bestIdx = $idx;
                    }
                }
            }
            // Threshold for Face-API.js euclidean distance (0.55 is strict match)
            if ($bestDistance < 0.55 && $bestIdx >= 0) {
                $matchedProfile = &$db['visitor_profiles'][$bestIdx];
                $matchedIndex = $bestIdx;
            }
        }
    }

    // Update existing profile or create new one
    if ($matchedProfile) {
        $matchedProfile['total_visits'] = ($matchedProfile['total_visits'] ?? 0) + 1;
        if (!isset($matchedProfile['daily_visits']) || !is_array($matchedProfile['daily_visits'])) {
            $matchedProfile['daily_visits'] = [];
        }
        $matchedProfile['daily_visits'][$today] = ($matchedProfile['daily_visits'][$today] ?? 0) + 1;
        $matchedProfile['last_seen'] = $now;
        $matchedProfile['last_camera_id'] = $cameraId;
        $matchedProfile['last_camera_title'] = $cameraTitle;
        $matchedProfile['last_direction'] = $direction;
        if (!empty($snapshotPath)) {
            $matchedProfile['last_snapshot'] = $snapshotPath;
            if (empty($matchedProfile['photo'])) {
                $matchedProfile['photo'] = $snapshotPath;
            }
        }
        if (is_array($descriptor) && count($descriptor) === 128) {
            $matchedProfile['descriptor'] = $descriptor;
        }
    } else {
        // Create new visitor profile
        $isStranger = ($category === 'stranger');
        $prefix = $isStranger ? 'STR' : 'EMP';
        $num = count($db['visitor_profiles']) + 1;
        $newId = $prefix . '-' . date('Ymd') . '-' . sprintf('%03d', $num);

        $newProfile = [
            'id' => $newId,
            'name' => $isStranger ? ('Stranger #' . sprintf('%02d', $num)) : $label,
            'category' => $category,
            'role_title' => $isStranger ? 'Pengunjung Tidak Dikenal' : 'Personil Terdaftar',
            'photo' => $snapshotPath,
            'last_snapshot' => $snapshotPath,
            'first_seen' => $now,
            'last_seen' => $now,
            'total_visits' => 1,
            'daily_visits' => [
                $today => 1
            ],
            'last_camera_id' => $cameraId,
            'last_camera_title' => $cameraTitle,
            'last_direction' => $direction,
            'notes' => $isStranger ? 'Terdeteksi otomatis oleh AI Camera' : 'Profil terdaftar resmi',
            'descriptor' => (is_array($descriptor) && count($descriptor) === 128) ? $descriptor : null
        ];
        $db['visitor_profiles'][] = $newProfile;
        $matchedProfile = $newProfile;
    }

    // Record persistent detection event in ai_logs
    if (!isset($db['ai_logs']) || !is_array($db['ai_logs'])) {
        $db['ai_logs'] = [];
    }
    $logId = count($db['ai_logs']) > 0 ? (max(array_column($db['ai_logs'], 'id')) + 1) : 1;

    $newLog = [
        'id' => $logId,
        'user_id' => $userId,
        'visitor_id' => $matchedProfile['id'],
        'label' => $matchedProfile['name'],
        'category' => $matchedProfile['category'],
        'confidence' => $confidence,
        'camera_id' => $cameraId,
        'camera_title' => $cameraTitle,
        'direction' => $direction,
        'snapshot' => $snapshotPath ?: ($matchedProfile['photo'] ?? ''),
        'timestamp' => $now,
        'date' => $today,
        'time' => $time,
        'details' => "[{$direction}] {$matchedProfile['name']} di {$cameraTitle} (Total kunjungan: {$matchedProfile['total_visits']}x)"
    ];

    array_unshift($db['ai_logs'], $newLog);
    // Keep last 1000 logs for rich historical investigation
    if (count($db['ai_logs']) > 1000) {
        $db['ai_logs'] = array_slice($db['ai_logs'], 0, 1000);
    }

    save_db_data($db);

    echo json_encode([
        'success' => true,
        'visitor' => $matchedProfile,
        'log' => $newLog
    ]);
    exit;
}

// 8. GET VISITOR INTELLIGENCE & FREQUENCY ANALYTICS
if ($action === 'get_visitor_analytics') {
    $today = date('Y-m-d');
    if (!isset($db['visitor_profiles'])) $db['visitor_profiles'] = [];
    if (!isset($db['ai_logs'])) $db['ai_logs'] = [];

    // Calculate Today's KPI metrics
    $totalVisitsToday = 0;
    $karyawanToday = 0;
    $strangerToday = 0;
    $blacklistToday = 0;
    $todayPeopleMap = [];

    // 24 Hour Traffic distribution for today
    $hourlyTraffic = array_fill(0, 24, 0);

    foreach ($db['ai_logs'] as $l) {
        $logDate = $l['date'] ?? substr($l['timestamp'] ?? '', 0, 10);
        if ($logDate === $today) {
            $totalVisitsToday++;
            $cat = strtolower($l['category'] ?? '');
            if ($cat === 'stranger') {
                $strangerToday++;
            } else if ($cat === 'blacklist') {
                $blacklistToday++;
            } else {
                $karyawanToday++;
            }

            $vId = $l['visitor_id'] ?? ($l['label'] ?? '');
            if ($vId) $todayPeopleMap[$vId] = true;

            $hour = (int)date('H', strtotime($l['timestamp']));
            if ($hour >= 0 && $hour < 24) {
                $hourlyTraffic[$hour]++;
            }
        }
    }

    // Sort visitor profiles by today's visits, then total visits
    $profiles = $db['visitor_profiles'];
    usort($profiles, function($a, $b) use ($today) {
        $aToday = $a['daily_visits'][$today] ?? 0;
        $bToday = $b['daily_visits'][$today] ?? 0;
        if ($aToday !== $bToday) {
            return $bToday <=> $aToday;
        }
        return ($b['total_visits'] ?? 0) <=> ($a['total_visits'] ?? 0);
    });

    // Strip bulky descriptors from analytics overview list for fast loading
    $lightProfiles = array_map(function($p) {
        unset($p['descriptor']);
        return $p;
    }, array_slice($profiles, 0, 50));

    $recentLogs = array_slice($db['ai_logs'], 0, 50);

    echo json_encode([
        'success' => true,
        'today' => $today,
        'summary' => [
            'total_visits_today' => $totalVisitsToday,
            'unique_people_today' => count($todayPeopleMap),
            'karyawan_today' => $karyawanToday,
            'stranger_today' => $strangerToday,
            'blacklist_today' => $blacklistToday,
            'total_registered_profiles' => count($db['visitor_profiles'])
        ],
        'hourly_traffic' => $hourlyTraffic,
        'top_visitors' => $lightProfiles,
        'recent_logs' => $recentLogs
    ]);
    exit;
}

// 9. SEARCH & INVESTIGATION ENGINE
if ($action === 'search_visitors') {
    $keyword = strtolower(trim($_POST['keyword'] ?? $_GET['keyword'] ?? ''));
    $catFilter = strtolower(trim($_POST['category'] ?? $_GET['category'] ?? 'all'));
    $dateFilter = trim($_POST['date_filter'] ?? $_GET['date_filter'] ?? 'all');
    $camFilter = (int)($_POST['camera_id'] ?? $_GET['camera_id'] ?? 0);

    if (!isset($db['visitor_profiles'])) $db['visitor_profiles'] = [];
    if (!isset($db['ai_logs'])) $db['ai_logs'] = [];

    $today = date('Y-m-d');
    $startDate = '';
    if ($dateFilter === 'today') {
        $startDate = $today;
    } else if ($dateFilter === 'yesterday') {
        $startDate = date('Y-m-d', strtotime('-1 day'));
    } else if ($dateFilter === 'last_7_days') {
        $startDate = date('Y-m-d', strtotime('-7 days'));
    } else if ($dateFilter === 'last_30_days') {
        $startDate = date('Y-m-d', strtotime('-30 days'));
    } else if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFilter)) {
        $startDate = $dateFilter;
    }

    // Filter matching logs
    $matchingLogs = [];
    $matchedVisitorIds = [];

    foreach ($db['ai_logs'] as $l) {
        $logDate = $l['date'] ?? substr($l['timestamp'] ?? '', 0, 10);
        $lCat = strtolower($l['category'] ?? '');
        $lCam = (int)($l['camera_id'] ?? 0);
        $lName = strtolower($l['label'] ?? '');
        $lVisId = strtolower($l['visitor_id'] ?? '');

        // Date check
        if (!empty($startDate)) {
            if ($dateFilter === 'yesterday' || preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFilter)) {
                if ($logDate !== $startDate) continue;
            } else if ($dateFilter === 'today') {
                if ($logDate !== $today) continue;
            } else {
                if ($logDate < $startDate) continue;
            }
        }

        // Category check
        if ($catFilter !== 'all' && $lCat !== $catFilter) {
            continue;
        }

        // Camera check
        if ($camFilter > 0 && $lCam !== $camFilter) {
            continue;
        }

        // Keyword check
        if (!empty($keyword)) {
            $matchKey = (str_contains($lName, $keyword) || str_contains($lVisId, $keyword) || str_contains(strtolower($l['camera_title'] ?? ''), $keyword));
            if (!$matchKey) continue;
        }

        $matchingLogs[] = $l;
        if (!empty($l['visitor_id'])) {
            $matchedVisitorIds[$l['visitor_id']] = true;
        }
    }

    // Filter matching visitor profiles
    $matchingProfiles = [];
    foreach ($db['visitor_profiles'] as $vp) {
        $vId = $vp['id'] ?? '';
        $vName = strtolower($vp['name'] ?? '');
        $vCat = strtolower($vp['category'] ?? '');

        $hasLogMatch = isset($matchedVisitorIds[$vId]);
        $directKeywordMatch = empty($keyword) || str_contains($vName, $keyword) || str_contains(strtolower($vId), $keyword);
        $catMatch = ($catFilter === 'all' || $vCat === $catFilter);

        if (($hasLogMatch || $directKeywordMatch) && $catMatch) {
            $p = $vp;
            unset($p['descriptor']);
            $matchingProfiles[] = $p;
        }
    }

    echo json_encode([
        'success' => true,
        'count_visitors' => count($matchingProfiles),
        'count_logs' => count($matchingLogs),
        'visitors' => array_slice($matchingProfiles, 0, 50),
        'logs' => array_slice($matchingLogs, 0, 100)
    ]);
    exit;
}

// 10. GET INDIVIDUAL VISITOR INVESTIGATION PROFILE & DOSSIER
if ($action === 'get_visitor_detail') {
    $visitorId = trim($_POST['visitor_id'] ?? $_GET['visitor_id'] ?? '');
    if (empty($visitorId)) {
        echo json_encode(['success' => false, 'message' => 'visitor_id wajib diisi.']);
        exit;
    }

    if (!isset($db['visitor_profiles'])) $db['visitor_profiles'] = [];
    if (!isset($db['ai_logs'])) $db['ai_logs'] = [];

    $profile = null;
    foreach ($db['visitor_profiles'] as $vp) {
        if (($vp['id'] ?? '') === $visitorId || strtolower(trim($vp['name'] ?? '')) === strtolower($visitorId)) {
            $profile = $vp;
            break;
        }
    }

    if (!$profile) {
        echo json_encode(['success' => false, 'message' => 'Profil pengunjung tidak ditemukan.']);
        exit;
    }

    // Gather all logs for this visitor
    $logs = [];
    $dailyBreakdown = [];

    foreach ($db['ai_logs'] as $l) {
        if (($l['visitor_id'] ?? '') === $profile['id'] || strtolower(trim($l['label'] ?? '')) === strtolower($profile['name'])) {
            $logs[] = $l;
            $d = $l['date'] ?? substr($l['timestamp'] ?? '', 0, 10);
            if (!isset($dailyBreakdown[$d])) {
                $dailyBreakdown[$d] = [
                    'date' => $d,
                    'count' => 0,
                    'first_time' => $l['timestamp'],
                    'last_time' => $l['timestamp'],
                    'cameras' => []
                ];
            }
            $dailyBreakdown[$d]['count']++;
            if ($l['timestamp'] < $dailyBreakdown[$d]['first_time']) {
                $dailyBreakdown[$d]['first_time'] = $l['timestamp'];
            }
            if ($l['timestamp'] > $dailyBreakdown[$d]['last_time']) {
                $dailyBreakdown[$d]['last_time'] = $l['timestamp'];
            }
            if (!empty($l['camera_title']) && !in_array($l['camera_title'], $dailyBreakdown[$d]['cameras'])) {
                $dailyBreakdown[$d]['cameras'][] = $l['camera_title'];
            }
        }
    }

    // Sort daily breakdown by date desc
    krsort($dailyBreakdown);
    unset($profile['descriptor']);

    echo json_encode([
        'success' => true,
        'profile' => $profile,
        'daily_breakdown' => array_values($dailyBreakdown),
        'timeline' => array_slice($logs, 0, 100)
    ]);
    exit;
}

// 11. UPDATE VISITOR PROFILE (RENAME / RECLASSIFY / ADD INVESTIGATION NOTES)
if ($action === 'update_visitor_profile') {
    $visitorId = trim($_POST['visitor_id'] ?? '');
    $newName = trim($_POST['name'] ?? '');
    $newCategory = trim($_POST['category'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($visitorId) || empty($newName)) {
        echo json_encode(['success' => false, 'message' => 'ID Pengunjung dan Nama wajib diisi.']);
        exit;
    }

    if (!isset($db['visitor_profiles'])) $db['visitor_profiles'] = [];

    $found = false;
    $updatedProfile = null;
    foreach ($db['visitor_profiles'] as &$vp) {
        if (($vp['id'] ?? '') === $visitorId) {
            $oldName = $vp['name'];
            $vp['name'] = $newName;
            if (!empty($newCategory)) $vp['category'] = $newCategory;
            if ($notes !== '') $vp['notes'] = $notes;
            $found = true;
            $updatedProfile = $vp;

            // Also update matching entries in ai_logs
            if (isset($db['ai_logs']) && is_array($db['ai_logs'])) {
                foreach ($db['ai_logs'] as &$l) {
                    if (($l['visitor_id'] ?? '') === $visitorId || ($l['label'] ?? '') === $oldName) {
                        $l['label'] = $newName;
                        if (!empty($newCategory)) $l['category'] = $newCategory;
                    }
                }
                unset($l);
            }
            break;
        }
    }
    unset($vp);

    if ($found) {
        save_db_data($db);
        echo json_encode([
            'success' => true,
            'message' => "Profil {$newName} berhasil diperbarui!",
            'profile' => $updatedProfile
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Profil pengunjung tidak ditemukan.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action tidak dikenali.']);
exit;
