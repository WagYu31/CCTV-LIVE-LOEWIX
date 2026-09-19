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
    echo "<h1>Akses Ditolak</h1><p>Gunakan parameter ?secret=loewix2026 atau jalankan lewat terminal CLI: <code>php sync_import.php</code></p>";
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

$tarFile = __DIR__ . '/data_sync_package.tar.gz';
if (!file_exists($tarFile)) {
    echo "ERROR: File data_sync_package.tar.gz tidak ditemukan di root server.\n";
    exit(1);
}

echo "===================================================\n";
echo "  LOEWIX DATA SYNCHRONIZATION IMPORT TOOL\n";
echo "===================================================\n";
echo "[1/4] Memeriksa paket sinkronisasi... OK (" . round(filesize($tarFile) / 1024, 1) . " KB)\n";

// Backup existing loewix_db.json if it exists
if (file_exists(__DIR__ . '/data/loewix_db.json')) {
    $bakFile = __DIR__ . '/data/loewix_db.json.bak_' . date('Ymd_His');
    @copy(__DIR__ . '/data/loewix_db.json', $bakFile);
    echo "[2/4] Backup database lama: " . basename($bakFile) . "\n";
} else {
    echo "[2/4] Database lama belum ada. Melewati backup.\n";
}

// Extract tar.gz
echo "[3/4] Mengekstrak paket data (kamera, AI faces, database)...\n";
$output = [];
$returnVar = 0;
exec("tar -xzf " . escapeshellarg($tarFile) . " -C " . escapeshellarg(__DIR__), $output, $returnVar);

if ($returnVar !== 0) {
    echo "GAGAL mengekstrak arsip dengan tar. Mencoba dengan PharData...\n";
    try {
        $phar = new PharData($tarFile);
        $phar->extractTo(__DIR__, null, true);
    } catch (Exception $e) {
        echo "ERROR: Gagal mengekstrak arsip: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Set permissions
@chmod(__DIR__ . '/data/loewix_db.json', 0666);
if (file_exists(__DIR__ . '/data/loewix_ai.db')) @chmod(__DIR__ . '/data/loewix_ai.db', 0666);
if (file_exists(__DIR__ . '/data/encoding.json')) @chmod(__DIR__ . '/data/encoding.json', 0666);
if (file_exists(__DIR__ . '/data/cameras_encode.json')) @chmod(__DIR__ . '/data/cameras_encode.json', 0666);

// Verify imported data
echo "[4/4] Verifikasi hasil import...\n";
if (file_exists(__DIR__ . '/data/loewix_db.json')) {
    $dbContent = json_decode(file_get_contents(__DIR__ . '/data/loewix_db.json'), true);
    $camCount = count($dbContent['cameras'] ?? []);
    $userCount = count($dbContent['users'] ?? []);
    $faceCount = count($dbContent['ai_faces'] ?? []);
    
    echo "\n>>> BERHASIL DISINKRONKAN! <<<\n";
    echo " - Total Kamera: {$camCount} kamera (Yamaha DDS, RTSP STG, L12, Parkiran, L8, dll.)\n";
    echo " - Total User: {$userCount} pengguna\n";
    echo " - Total Profil Wajah: {$faceCount} wajah terdaftar\n";
    echo "\nSilakan refresh web: https://loewixcctv.com/local/\n";
    echo "===================================================\n";
} else {
    echo "PERINGATAN: File data/loewix_db.json tidak ditemukan setelah ekstraksi.\n";
}
