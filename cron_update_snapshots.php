<?php
/**
 * Loewix CCTV - Automatic Periodic Live Snapshot Capture Worker
 * Run via CLI Cron (e.g. weekly: 0 3 * * 0 php /path/to/cron_update_snapshots.php)
 * or accessed via browser by admin with key.
 */

require_once __DIR__ . '/config/db.php';

$isCli = (php_sapi_name() === 'cli');
if (!$isCli) {
    session_start();
    if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin') {
        $key = $_GET['key'] ?? '';
        if ($key !== 'loewix_secure_cron_2026') {
            http_response_code(403);
            die(json_encode(['error' => 'Unauthorized']));
        }
    }
}

$db = get_db_data();
$cameras = $db['cameras'] ?? [];
$targetDir = __DIR__ . '/assets/image/snapshots';

if (!file_exists($targetDir)) {
    @mkdir($targetDir, 0777, true);
}

$results = [];
$now = time();

echo $isCli ? "=== Loewix CCTV Snapshot Auto-Capture ===\n" : "<pre>=== Loewix CCTV Snapshot Auto-Capture ===\n";

foreach ($cameras as &$cam) {
    $camId = $cam['id'] ?? 0;
    $title = $cam['title'] ?? 'Camera';
    $streamUrl = $cam['hls_url'] ?? $cam['streamPath'] ?? '';
    $lastSnapshot = isset($cam['last_snapshot_at']) ? strtotime($cam['last_snapshot_at']) : 0;

    $targetFile = "{$targetDir}/cam_{$camId}.jpg";
    $success = false;

    echo "Processing [{$camId}] {$title}... ";

    // 1. If RTSP or HLS stream exists (e.g. Yamaha DDS)
    if (!empty($streamUrl)) {
        if (!str_starts_with($streamUrl, 'http://') && !str_starts_with($streamUrl, 'https://') && !str_starts_with($streamUrl, 'rtsp://')) {
            $streamUrl = "https://stream.loewixcctv.com/{$streamUrl}/index.m3u8";
        } elseif (str_starts_with($streamUrl, 'http://stream.loewixcctv.com')) {
            $streamUrl = str_replace('http://', 'https://', $streamUrl);
        }

        // Try ffmpeg if installed (3s network timeout)
        $escapedUrl = escapeshellarg($streamUrl);
        $escapedTarget = escapeshellarg($targetFile);
        $cmd = "ffmpeg -y -stimeout 3000000 -timeout 3000000 -t 3 -i {$escapedUrl} -ss 00:00:01 -vframes 1 -q:v 2 {$escapedTarget} 2>&1";
        @exec($cmd, $out, $returnCode);

        if ($returnCode === 0 && file_exists($targetFile) && filesize($targetFile) > 1000) {
            $success = true;
        }
    }

    // 2. If XMeye P2P camera
    if (!$success && ($cam['connection_type'] ?? '') === 'xmeye_p2p' && !empty($cam['serial_number'])) {
        if (file_exists(__DIR__ . '/api/jftech_gateway.php')) {
            require_once __DIR__ . '/api/jftech_gateway.php';
            $sn = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cam['serial_number']));
            $ch = (int)($cam['channel'] ?? 1);
            $liveUrl = getJFTechLiveStreamUrl($sn, $ch, 'hls-fmp4', 'sub', $cam['device_user'] ?? 'admin', $cam['device_pass'] ?? '');

            if ($liveUrl) {
                $escapedUrl = escapeshellarg($liveUrl);
                $escapedTarget = escapeshellarg($targetFile);
                $cmd = "ffmpeg -y -stimeout 3000000 -timeout 3000000 -t 3 -i {$escapedUrl} -ss 00:00:01 -vframes 1 -q:v 2 {$escapedTarget} 2>&1";
                @exec($cmd, $out2, $ret2);
                if ($ret2 === 0 && file_exists($targetFile) && filesize($targetFile) > 1000) {
                    $success = true;
                }
            }
        }
    }

    if ($success) {
        $cam['thumbnail'] = "assets/image/snapshots/cam_{$camId}.jpg";
        $cam['last_snapshot_at'] = date('Y-m-d H:i:s');
        echo "SUCCESS (Captured)\n";
        $results[$camId] = 'SUCCESS';
    } else {
        echo "SKIPPED / OFFLINE\n";
        $results[$camId] = 'OFFLINE';
    }
}

save_db_data($db);
echo "Completed capture cycle at " . date('Y-m-d H:i:s') . "\n";
if (!$isCli) echo "</pre>";
