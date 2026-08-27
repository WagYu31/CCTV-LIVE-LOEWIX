<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$cacheFile = sys_get_temp_dir() . '/loewix_visitor_count.json';
$count = 1250;

if (file_exists($cacheFile)) {
    $data = json_decode(@file_get_contents($cacheFile), true);
    if ($data && isset($data['count'])) {
        $count = (int)$data['count'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $count++;
    @file_put_contents($cacheFile, json_encode(['count' => $count, 'updated_at' => time()]));
}

echo json_encode([
    'success' => true,
    'count' => $count,
    'total_visitors' => $count,
    'today_visitors' => (int)($count * 0.12),
    'online_now' => rand(15, 38)
]);
