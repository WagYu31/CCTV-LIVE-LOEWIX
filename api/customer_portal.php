<?php
/**
 * Customer Self-Service Portal REST API
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
$action = $_GET['action'] ?? $_POST['action'] ?? 'get_profile';

// Fallback for user_id parameter if session not populated in some environments
if (!$user && !empty($_REQUEST['user_id'])) {
    $reqUserId = (int)$_REQUEST['user_id'];
    foreach ($db['users'] as $u) {
        if ((int)$u['id'] === $reqUserId && ($u['status'] ?? 'active') === 'active') {
            $user = [
                'id' => $u['id'],
                'name' => $u['name'],
                'email' => $u['email'],
                'role' => $u['role'],
                'cctv_quota' => (int)($u['cctv_quota'] ?? 10),
                'phone' => $u['phone'] ?? '-',
                'city' => $u['city'] ?? 'siantar',
                'status' => $u['status'] ?? 'active',
                'created_at' => $u['created_at'] ?? ''
            ];
            break;
        }
    }
}

if (!$user) {
    echo json_encode([
        'success' => false,
        'logged_in' => false,
        'message' => 'Silakan login terlebih dahulu untuk mengakses Customer Portal.'
    ]);
    exit;
}

$customerId = (int)$user['id'];

// Count customer cameras
$customerCameras = [];
foreach ($db['cameras'] as $cam) {
    if ((int)($cam['user_id'] ?? 0) === $customerId || $user['role'] === 'super_admin') {
        $customerCameras[] = $cam;
    }
}
$usedQuota = count($customerCameras);
$totalQuota = (int)($user['cctv_quota'] ?? 20);

if ($action === 'get_profile') {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'cctv_quota' => $totalQuota,
            'cctv_used' => $usedQuota,
            'remaining_quota' => max(0, $totalQuota - $usedQuota),
            'phone' => $user['phone'] ?? '-',
            'city' => $user['city'] ?? 'siantar',
            'status' => $user['status'] ?? 'active'
        ]
    ]);
    exit;
}

function sync_rtsp_to_mediamtx($streamPath, $rtspUrl) {
    if (empty($streamPath) || empty($rtspUrl)) return false;
    
    $mediamtxFile = __DIR__ . '/../mediamtx.yml';
    if (!file_exists($mediamtxFile)) return false;

    $content = @file_get_contents($mediamtxFile);
    if ($content === false) return false;

    $cleanRtspUrl = str_replace('"', '', $rtspUrl);
    // Transcode local/IP/DVR cameras to standard H.264 so browser HLS plays smoothly without H.265/DeltaPocS0 errors
    $needsTranscode = (strpos($cleanRtspUrl, '192.168.') !== false || strpos($cleanRtspUrl, '10.') === 0 || strpos($cleanRtspUrl, '172.') === 0 || strpos($streamPath, 'cam_live_') === 0);

    $ffmpegCmd = "ffmpeg -nostdin -loglevel error -rtsp_transport tcp -timeout 8000000 -i \"{$cleanRtspUrl}\" -c:v libx264 -preset ultrafast -tune zerolatency -pix_fmt yuv420p -r 20 -fps_mode cfr -g 40 -keyint_min 40 -sc_threshold 0 -b:v 800k -maxrate 1000k -bufsize 2000k -an -f rtsp -pkt_size 1316 -rtsp_transport tcp rtsp://127.0.0.1:8554/{$streamPath}";

    // Check if path already registered in mediamtx.yml
    $alreadyExists = preg_match('/^\s*' . preg_quote($streamPath, '/') . ':\s*$/m', $content);

    if (!$alreadyExists) {
        // Append path to mediamtx.yml
        if ($needsTranscode) {
            $newEntry = "\n  {$streamPath}:\n    runOnInit: {$ffmpegCmd}\n    runOnInitRestart: yes\n";
        } else {
            $newEntry = "\n  {$streamPath}:\n    source: {$cleanRtspUrl}\n    rtspTransport: tcp\n    sourceOnDemand: yes\n";
        }
        $content .= $newEntry;
        @file_put_contents($mediamtxFile, $content);
    }

    // Update MediaMTX live configuration via API (9997)
    try {
        $apiPayload = $needsTranscode ? [
            'runOnInit' => $ffmpegCmd,
            'runOnInitRestart' => true
        ] : [
            'source' => $cleanRtspUrl,
            'rtspTransport' => 'tcp',
            'sourceOnDemand' => true
        ];

        $ch = curl_init("http://127.0.0.1:9997/v3/config/paths/add/" . urlencode($streamPath));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiPayload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $res = @curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        // If path already exists, update/replace it
        if ($httpCode === 400 || $alreadyExists) {
            $ch = curl_init("http://127.0.0.1:9997/v3/config/paths/replace/" . urlencode($streamPath));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiPayload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            @curl_exec($ch);
            @curl_close($ch);
        }
    } catch (Exception $e) {}

    return true;
}

if ($action === 'my_cameras') {
    $snSnapshotCache = [];

    foreach ($customerCameras as &$cam) {
        // Force HTTPS for stream.loewixcctv.com to prevent mixed-content blocking
        if (!empty($cam['hls_url']) && strpos($cam['hls_url'], 'http://stream.loewixcctv.com') === 0) {
            $cam['hls_url'] = str_replace('http://', 'https://', $cam['hls_url']);
        }

        // Auto-repair corrupted HLS URLs that contain rtsp:// inside them
        if (!empty($cam['hls_url']) && strpos($cam['hls_url'], 'rtsp://') !== false) {
            $cleanPath = (!empty($cam['streamPath']) && strpos($cam['streamPath'], 'rtsp://') === false) ? $cam['streamPath'] : 'cam_live_' . $cam['id'];
            $cam['streamPath'] = $cleanPath;
            $cam['hls_url'] = "https://stream.loewixcctv.com/{$cleanPath}/index.m3u8";
        }

        // Auto-resolve RTSP cameras missing HLS URL or streamPath
        if (($cam['connection_type'] ?? '') === 'rtsp' && !empty($cam['rtsp_url'])) {
            if (empty($cam['streamPath'])) {
                if (strpos($cam['rtsp_url'], '103.164.101.50:8203') !== false && strpos($cam['rtsp_url'], 'channel=1') !== false) {
                    $cam['streamPath'] = 'cctv_loewix_1';
                } elseif (strpos($cam['rtsp_url'], '103.164.101.50:8203') !== false && strpos($cam['rtsp_url'], 'channel=2') !== false) {
                    $cam['streamPath'] = 'cctv_loewix_2';
                } elseif (strpos($cam['rtsp_url'], '103.164.101.50:8203') !== false && strpos($cam['rtsp_url'], 'channel=3') !== false) {
                    $cam['streamPath'] = 'cctv_loewix_3';
                } else {
                    $cam['streamPath'] = 'cam_live_' . $cam['id'];
                }
            }
            if (empty($cam['hls_url'])) {
                $cam['hls_url'] = "https://stream.loewixcctv.com/{$cam['streamPath']}/index.m3u8";
            }
            sync_rtsp_to_mediamtx($cam['streamPath'], $cam['rtsp_url']);
        }

        // Check if a dedicated saved live snapshot exists for this camera
        $snapFile = __DIR__ . "/../assets/image/snapshots/cam_{$cam['id']}.jpg";
        if (file_exists($snapFile) && filesize($snapFile) > 1000) {
            $cam['thumbnail'] = "assets/image/snapshots/cam_{$cam['id']}.jpg?v=" . filemtime($snapFile);
        } else {
            // Check if existing thumbnail is valid (not old Siantar or default-thumbnail)
            if (!empty($cam['thumbnail']) && (strpos($cam['thumbnail'], 'default-thumbnail') !== false || strpos($cam['thumbnail'], 'icon-cctv') !== false || strpos($cam['thumbnail'], 'Jalan-') !== false || strpos($cam['thumbnail'], 'Simpang-') !== false)) {
                $cam['thumbnail'] = '';
            }
        }

        // Automatically attach real-time cached snapshot and stream URL for XMeye cameras
        if (($cam['connection_type'] ?? '') === 'xmeye_p2p' && !empty($cam['serial_number'])) {
            $sn = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cam['serial_number']));
            $ch = (int)($cam['channel'] ?? 1);
            $chIdx = max(0, $ch - 1);

            // Attach cached live stream HLS URL if available (zero-delay instant start)
            $cacheKey = md5("{$sn}_{$chIdx}_stream1_v3");
            $streamCacheFile = sys_get_temp_dir() . "/jf_stream_{$cacheKey}.json";
            if (!file_exists($streamCacheFile)) {
                $legacyKey = md5("{$sn}_{$chIdx}_v3");
                $streamCacheFile = sys_get_temp_dir() . "/jf_stream_{$legacyKey}.json";
            }
            if (file_exists($streamCacheFile)) {
                $cachedStream = json_decode(@file_get_contents($streamCacheFile), true);
                if ($cachedStream && !empty($cachedStream['url']) && ($cachedStream['expires_at'] ?? 0) > time()) {
                    $cam['hls_url'] = $cachedStream['url'];
                    $cam['streamPath'] = $cachedStream['url'];
                }
            }

            // If stream is not resolved yet, fetch and cache it now
            if (empty($cam['hls_url']) || strpos($cam['hls_url'], 'bcloud365.net') === false) {
                if (file_exists(__DIR__ . '/jftech_gateway.php')) {
                    require_once __DIR__ . '/jftech_gateway.php';
                    $resolvedUrl = getJFTechLiveStreamUrl($sn, $ch, 'hls-fmp4', $cam['stream_quality'] ?? 'sub', $cam['device_user'] ?? 'admin', $cam['device_pass'] ?? '');
                    if ($resolvedUrl) {
                        $cam['hls_url'] = $resolvedUrl;
                        $cam['streamPath'] = $resolvedUrl;
                    }
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

            if (empty($cam['thumbnail']) && isset($snSnapshotCache[$sn]['snapshots'][$ch]) && !empty($snSnapshotCache[$sn]['snapshots'][$ch])) {
                $cam['thumbnail'] = $snSnapshotCache[$sn]['snapshots'][$ch];
            }
        }
    }

    echo json_encode([
        'success' => true,
        'total' => count($customerCameras),
        'quota' => $totalQuota,
        'used' => $usedQuota,
        'remaining' => max(0, $totalQuota - $usedQuota),
        'cameras' => $customerCameras
    ]);
    exit;
}

if ($action === 'save_snapshot') {
    $camId = (int)($_POST['camera_id'] ?? 0);
    $imageData = trim($_POST['image_data'] ?? '');

    if ($camId <= 0 || empty($imageData)) {
        echo json_encode(['success' => false, 'message' => 'Data gambar / ID Kamera tidak valid.']);
        exit;
    }

    // Verify camera ownership
    $found = false;
    foreach ($customerCameras as &$c) {
        if ((int)$c['id'] === $camId) {
            $found = true;
            break;
        }
    }
    if (!$found && $user['role'] !== 'super_admin') {
        echo json_encode(['success' => false, 'message' => 'Kamera tidak ditemukan.']);
        exit;
    }

    if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $imageData = base64_decode($imageData);
        if ($imageData === false) {
            echo json_encode(['success' => false, 'message' => 'Gagal mendekode gambar base64.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Format URI gambar tidak valid.']);
        exit;
    }

    $targetDir = __DIR__ . '/../assets/image/snapshots';
    if (!file_exists($targetDir)) {
        @mkdir($targetDir, 0777, true);
    }

    $fileName = "cam_{$camId}.jpg";
    $filePath = "{$targetDir}/{$fileName}";
    @file_put_contents($filePath, $imageData);

    $relativeUrl = "assets/image/snapshots/{$fileName}";

    // Update in database
    foreach ($db['cameras'] as &$c) {
        if ((int)$c['id'] === $camId) {
            $c['thumbnail'] = $relativeUrl;
            $c['last_snapshot_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    save_db_data($db);

    echo json_encode([
        'success' => true,
        'message' => 'Snapshot live kamera berhasil disimpan!',
        'thumbnail' => $relativeUrl . '?v=' . time()
    ]);
    exit;
}

if ($action === 'update_profile') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Nama tidak boleh kosong!']);
        exit;
    }

    foreach ($db['users'] as $k => $u) {
        if ((int)$u['id'] === $customerId) {
            $db['users'][$k]['name'] = $name;
            if (!empty($phone)) $db['users'][$k]['phone'] = $phone;
            if (!empty($city)) $db['users'][$k]['city'] = $city;
            save_db_data($db);

            // Update session
            $_SESSION['user_name'] = $name;
            if (!empty($city)) $_SESSION['user_city'] = $city;

            echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui!', 'user' => $db['users'][$k]]);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'User tidak ditemukan.']);
    exit;
}

if ($action === 'change_password') {
    $oldPass = trim($_POST['old_password'] ?? '');
    $newPass = trim($_POST['new_password'] ?? '');

    if (empty($newPass) || strlen($newPass) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password baru minimal 6 karakter!']);
        exit;
    }

    foreach ($db['users'] as $k => $u) {
        if ((int)$u['id'] === $customerId) {
            // Verify old password if provided
            if (!empty($oldPass)) {
                $valid = password_verify($oldPass, $u['password']) || ($oldPass === $u['password']);
                if (!$valid) {
                    echo json_encode(['success' => false, 'message' => 'Password lama Anda tidak sesuai!']);
                    exit;
                }
            }
            $db['users'][$k]['password'] = password_hash($newPass, PASSWORD_BCRYPT);
            save_db_data($db);
            echo json_encode(['success' => true, 'message' => 'Password Anda berhasil diperbarui!']);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'User tidak ditemukan.']);
    exit;
}

if ($action === 'save_camera') {
    $camId = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $city = trim($_POST['city'] ?? 'siantar');
    $platform = trim($_POST['platform'] ?? 'mediamtx');
    $hls_url = trim($_POST['hls_url'] ?? '');
    $rtsp_url = trim($_POST['rtsp_url'] ?? '');
    $streamPath = trim($_POST['streamPath'] ?? '');
    $connection_type = trim($_POST['connection_type'] ?? 'rtsp');
    $serial_number = trim($_POST['serial_number'] ?? '');
    $channel = (int)($_POST['channel'] ?? 1);
    $status = trim($_POST['status'] ?? 'online');

    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Nama kamera wajib diisi!']);
        exit;
    }

    // If user pasted rtsp:// into hls_url field, or hls_url contains rtsp://, clean it up
    if (strpos($hls_url, 'rtsp://') !== false) {
        $extractedRtsp = preg_replace('#^https?://[^/]+/#i', '', $hls_url);
        if (empty($rtsp_url) && strpos($extractedRtsp, 'rtsp://') === 0) {
            $rtsp_url = $extractedRtsp;
        }
        $hls_url = '';
    }

    // Auto-resolve RTSP parameters and streamPath
    if ($connection_type === 'rtsp' && !empty($rtsp_url)) {
        if (empty($streamPath)) {
            if (strpos($rtsp_url, '103.164.101.50:8203') !== false && strpos($rtsp_url, 'channel=1') !== false) {
                $streamPath = 'cctv_loewix_1';
            } elseif (strpos($rtsp_url, '103.164.101.50:8203') !== false && strpos($rtsp_url, 'channel=2') !== false) {
                $streamPath = 'cctv_loewix_2';
            } elseif (strpos($rtsp_url, '103.164.101.50:8203') !== false && strpos($rtsp_url, 'channel=3') !== false) {
                $streamPath = 'cctv_loewix_3';
            } else {
                $tempId = $camId > 0 ? $camId : (count($db['cameras']) > 0 ? max(array_column($db['cameras'], 'id')) + 1 : 1);
                $streamPath = 'cam_live_' . $tempId;
            }
        }
        if (empty($hls_url)) {
            $hls_url = "https://stream.loewixcctv.com/{$streamPath}/index.m3u8";
        }
        sync_rtsp_to_mediamtx($streamPath, $rtsp_url);
    }

    if ($camId > 0) {
        // Edit existing camera
        $found = false;
        foreach ($db['cameras'] as &$c) {
            if ((int)$c['id'] === $camId && ((int)($c['user_id'] ?? 0) === $customerId || $user['role'] === 'super_admin')) {
                $c['title'] = $title;
                $c['city'] = $city;
                $c['platform'] = $platform;
                $c['hls_url'] = $hls_url;
                $c['rtsp_url'] = $rtsp_url;
                $c['streamPath'] = $streamPath;
                $c['connection_type'] = $connection_type;
                if (!empty($serial_number)) $c['serial_number'] = $serial_number;
                $c['channel'] = $channel;
                $c['status'] = $status;
                $found = true;
                break;
            }
        }
        if ($found) {
            save_db_data($db);
            echo json_encode(['success' => true, 'message' => 'Kamera CCTV berhasil diperbarui!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Kamera tidak ditemukan atau Anda tidak memiliki akses.']);
        }
        exit;
    } else {
        // Add new camera - Check Quota
        if ($usedQuota >= $totalQuota && $user['role'] !== 'super_admin') {
            echo json_encode([
                'success' => false,
                'quota_exceeded' => true,
                'message' => "Batas kuota ({$totalQuota} Kamera) telah tercapai! Silakan hubungi Loewix Support untuk upgrade kuota."
            ]);
            exit;
        }

        $newId = count($db['cameras']) > 0 ? max(array_column($db['cameras'], 'id')) + 1 : 1;
        $newCam = [
            'id' => $newId,
            'user_id' => $customerId,
            'title' => $title,
            'city' => $city,
            'platform' => $platform,
            'hls_url' => $hls_url,
            'rtsp_url' => $rtsp_url,
            'streamPath' => $streamPath ?: $hls_url,
            'connection_type' => $connection_type,
            'serial_number' => $serial_number,
            'channel' => $channel,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $db['cameras'][] = $newCam;
        save_db_data($db);

        echo json_encode(['success' => true, 'message' => 'Kamera CCTV baru berhasil ditambahkan ke channel Anda!', 'camera' => $newCam]);
        exit;
    }
}

if ($action === 'delete_camera') {
    $camId = (int)($_POST['id'] ?? 0);
    $newCams = [];
    $deleted = false;

    foreach ($db['cameras'] as $c) {
        if ((int)$c['id'] === $camId && ((int)($c['user_id'] ?? 0) === $customerId || $user['role'] === 'super_admin')) {
            $deleted = true;
        } else {
            $newCams[] = $c;
        }
    }

    if ($deleted) {
        $db['cameras'] = $newCams;
        save_db_data($db);
        echo json_encode(['success' => true, 'message' => 'Kamera CCTV berhasil dihapus.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kamera tidak ditemukan atau bukan milik Anda.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action tidak valid.']);
