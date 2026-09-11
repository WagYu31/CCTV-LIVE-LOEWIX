<?php
/**
 * Loewix CCTV AI Vision — HTTPS Proxy Bridge
 * ===========================================
 * Safely bridges HTTPS browser requests from loewixcctv.com to local FastAPI daemon (127.0.0.1:5050)
 * Eliminates Mixed-Content SSL blocking, CORS headers issues, and external firewall port requirements.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$targetHost = 'http://127.0.0.1:5050';
$endpoint = $_GET['endpoint'] ?? ($_POST['endpoint'] ?? '/api/deepface/health');

// Ensure leading slash
if (!str_starts_with($endpoint, '/')) {
    $endpoint = '/' . $endpoint;
}

$url = $targetHost . $endpoint;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 12);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

if ($method === 'POST') {
    $body = file_get_contents('php://input');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
} else {
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
if (is_resource($ch)) {
    @curl_close($ch);
}

if ($curlError || !$response) {
    http_response_code(502);
    echo json_encode([
        'status' => 'offline',
        'success' => false,
        'message' => 'Loewix AI Vision Daemon (127.0.0.1:5050) is offline or unreachable.',
        'error' => $curlError ?: "HTTP $httpCode"
    ]);
    exit;
}

http_response_code($httpCode ?: 200);
echo $response;
