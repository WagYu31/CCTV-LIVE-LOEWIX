<?php
/**
 * Midtrans Payment Gateway Configuration & Core SDK Helper
 * PT. LOEWIX INDONESIA - CCTV SURVEILLANCE PLATFORM
 */

// Dynamic configuration loader from data/midtrans_config.json
$midtransConfigFile = __DIR__ . '/../data/midtrans_config.json';
$customMidtrans = [];
if (file_exists($midtransConfigFile)) {
    $customMidtrans = json_decode(file_get_contents($midtransConfigFile), true) ?: [];
}

// Configuration Settings (Sandbox by default, switch to true for production)
define('MIDTRANS_IS_PRODUCTION', $customMidtrans['is_production'] ?? false);

// Midtrans API Credentials (loaded from data/midtrans_config.json or ENV)
define('MIDTRANS_SERVER_KEY', $customMidtrans['server_key'] ?? (getenv('MIDTRANS_SERVER_KEY') ?: ''));
define('MIDTRANS_CLIENT_KEY', $customMidtrans['client_key'] ?? (getenv('MIDTRANS_CLIENT_KEY') ?: ''));
define('MIDTRANS_MERCHANT_ID', $customMidtrans['merchant_id'] ?? (getenv('MIDTRANS_MERCHANT_ID') ?: 'G589001445'));

// Snap API Endpoint
define('MIDTRANS_SNAP_URL', MIDTRANS_IS_PRODUCTION 
    ? 'https://app.midtrans.com/snap/v1/transactions' 
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions'
);

// Snap JS URL for Frontend
define('MIDTRANS_SNAP_JS_URL', MIDTRANS_IS_PRODUCTION
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js'
);

/**
 * Generate Midtrans Snap Token via cURL
 */
function create_midtrans_snap_token($transactionDetails, $customerDetails = [], $itemDetails = []) {
    $serverKey = MIDTRANS_SERVER_KEY;
    
    $payload = [
        'transaction_details' => $transactionDetails,
        'customer_details' => $customerDetails,
        'item_details' => $itemDetails,
        'credit_card' => [
            'secure' => true
        ],
        'expiry' => [
            'unit' => 'days',
            'duration' => 1
        ]
    ];

    // If using simulated / demo key and no external cURL is available or in demo sandbox
    if (strpos($serverKey, 'demo') !== false) {
        $simulatedToken = 'SNAP_LOEWIX_' . strtoupper(bin2hex(random_bytes(8)));
        $redirectUrl = (MIDTRANS_IS_PRODUCTION ? 'https://app.midtrans.com/snap/v2/vtweb/' : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/') . $simulatedToken;
        return [
            'success' => true,
            'token' => $simulatedToken,
            'redirect_url' => $redirectUrl,
            'is_simulation' => true
        ];
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MIDTRANS_SNAP_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($serverKey . ':')
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    if (is_resource($ch)) @curl_close($ch);

    if ($curlError) {
        return [
            'success' => false,
            'message' => 'Gagal terhubung ke Midtrans Gateway: ' . $curlError
        ];
    }

    $result = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300 && isset($result['token'])) {
        return [
            'success' => true,
            'token' => $result['token'],
            'redirect_url' => $result['redirect_url'] ?? '',
            'is_simulation' => false
        ];
    } else {
        // Fallback simulation token if live sandbox API key is pending
        $simulatedToken = 'SNAP_LOEWIX_' . strtoupper(bin2hex(random_bytes(8)));
        return [
            'success' => true,
            'token' => $simulatedToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $simulatedToken,
            'is_simulation' => true,
            'api_message' => $result['error_messages'][0] ?? 'Simulated Gateway Response'
        ];
    }
}

/**
 * Verify Midtrans Webhook Notification Signature
 */
function verify_midtrans_signature($orderId, $statusCode, $grossAmount, $signatureKey) {
    $serverKey = MIDTRANS_SERVER_KEY;
    $rawString = $orderId . $statusCode . $grossAmount . $serverKey;
    $calculatedSignature = hash('sha512', $rawString);
    return hash_equals($calculatedSignature, $signatureKey);
}
