<?php
/**
 * Loewix CCTV - One-Click Data Sync Import Script
 * Syncs cameras, users, AI face biometric profiles, and settings from local package to server.
 * PT. LOEWIX INDONESIA
 */

$isCli = (php_sapi_name() === 'cli');
$secret = $_GET['secret'] ?? '';

if (!$isCli && $secret !== 'loewix2026') {
    http_response_code(403);
    echo "<h1>Akses Ditolak</h1><p>Buka dengan parameter: <code>https://loewixcctv.com/sync_import.php?secret=loewix2026</code> atau jalankan di terminal: <code>php sync_import.php</code></p>";
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

echo "===================================================\n";
echo "  LOEWIX DATA SYNCHRONIZATION IMPORT TOOL\n";
echo "===================================================\n";

$baseDir = __DIR__;
$dataDir = $baseDir . '/data';

if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0777, true);
}

// 1. Synchronize loewix_db.json from seed
echo "[1/4] Mengimpor Database Utama (loewix_db.json)...\n";
$seedFile = $dataDir . '/loewix_db_seed.json';
$targetDb = $dataDir . '/loewix_db.json';

if (file_exists($seedFile)) {
    $seedJson = file_get_contents($seedFile);
    // Backup old db if exists
    if (file_exists($targetDb)) {
        @copy($targetDb, $targetDb . '.bak_' . date('Ymd_His'));
    }
    $written = @file_put_contents($targetDb, $seedJson);
    if ($written !== false) {
        echo " -> Database loewix_db.json berhasil ditulis (" . round($written / 1024, 1) . " KB)\n";
        @chmod($targetDb, 0666);
    } else {
        echo " -> Gagal menulis loewix_db.json (masalah permission). Mencoba copy...\n";
        @copy($seedFile, $targetDb);
    }
} else {
    echo " -> File seed {$seedFile} tidak ditemukan.\n";
}

// 2. Synchronize encoding and camera encode seeds
echo "[2/4] Mengimpor Konfigurasi Encodings...\n";
if (file_exists($dataDir . '/cameras_encode_seed.json')) {
    @copy($dataDir . '/cameras_encode_seed.json', $dataDir . '/cameras_encode.json');
    @chmod($dataDir . '/cameras_encode.json', 0666);
    echo " -> cameras_encode.json berhasil diperbarui.\n";
}
if (file_exists($dataDir . '/encoding_seed.json')) {
    @copy($dataDir . '/encoding_seed.json', $dataDir . '/encoding.json');
    @chmod($dataDir . '/encoding.json', 0666);
    echo " -> encoding.json berhasil diperbarui.\n";
}

// 3. Extract Face Biometrics and Archive with safe tar flags
echo "[3/4] Mengekstrak Foto Wajah & Biometrik...\n";
$tarFile = $baseDir . '/data_sync_package.tar.gz';
if (file_exists($tarFile)) {
    // Use --no-same-owner and --no-same-permissions to avoid Linux permission/utime errors
    $cmd = "tar --no-same-owner --no-same-permissions -xzf " . escapeshellarg($tarFile) . " -C " . escapeshellarg($baseDir) . " 2>&1";
    exec($cmd, $out, $ret);
    if ($ret === 0) {
        echo " -> Arsip wajah & database SQLite berhasil diekstrak tanpa error.\n";
    } else {
        echo " -> Catatan tar: " . implode(" ", array_slice($out, -2)) . "\n";
    }
}

// 4. Verify & Summary
echo "[4/4] Verifikasi Hasil Impor...\n";
if (file_exists($targetDb)) {
    $db = json_decode(file_get_contents($targetDb), true);
    $cameras = $db['cameras'] ?? [];
    $users = $db['users'] ?? [];
    $faces = $db['ai_faces'] ?? [];

    echo "\n===================================================\n";
    echo "  HASIL SINKRONISASI DATA:\n";
    echo "===================================================\n";
    echo " TOTAL KAMERA  : " . count($cameras) . " kamera aktif\n";
    foreach ($cameras as $c) {
        echo "   * [ID {$c['id']}] {$c['title']}\n";
    }
    echo " TOTAL PENGGUNA: " . count($users) . " akun\n";
    echo " PROFIL WAJAH  : " . count($faces) . " wajah terdaftar\n";
    echo "===================================================\n";
    echo "SELESAI! Silakan buka web sekarang:\n";
    echo "👉 https://loewixcctv.com/local/\n";
    echo "===================================================\n";
} else {
    echo "ERROR: Database target tidak dapat dibaca.\n";
}
