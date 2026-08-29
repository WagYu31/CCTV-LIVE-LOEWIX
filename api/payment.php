<?php
/**
 * Loewix Payment & Billing Management REST API
 * Midtrans Integration & SaaS Subscription Controller
 * PT. LOEWIX INDONESIA
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/midtrans.php';
require_once __DIR__ . '/../config/mail.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// 1. GET ALL SUBSCRIPTION PLANS
if ($action === 'get_plans') {
    $db = get_db_data();
    echo json_encode([
        'success' => true,
        'plans' => $db['plans'] ?? []
    ]);
    exit;
}

// 1B. SAVE OR UPDATE SUBSCRIPTION PLAN (SUPER ADMIN)
if ($action === 'save_plan') {
    $planId = trim($_POST['id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $quota = (int)($_POST['cctv_quota'] ?? 4);
    $priceMonthly = (int)($_POST['price_monthly'] ?? 0);
    $priceAnnual = (int)($_POST['price_annual'] ?? 0);
    $badge = trim($_POST['badge'] ?? '');
    $featuresStr = trim($_POST['features'] ?? '');
    $features = !empty($featuresStr) ? array_map('trim', explode("\n", $featuresStr)) : [];

    if (empty($name) || $priceMonthly <= 0) {
        echo json_encode(['success' => false, 'message' => 'Nama paket dan harga bulanan wajib diisi!']);
        exit;
    }

    if (empty($planId)) {
        $planId = 'plan_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name)) . '_' . $quota;
    }

    $db = get_db_data();
    if (!isset($db['plans']) || !is_array($db['plans'])) {
        $db['plans'] = [];
    }

    $found = false;
    foreach ($db['plans'] as &$p) {
        if ($p['id'] === $planId) {
            $p['name'] = $name;
            $p['cctv_quota'] = $quota;
            $p['price_monthly'] = $priceMonthly;
            $p['price_annual'] = $priceAnnual > 0 ? $priceAnnual : ($priceMonthly * 10);
            $p['badge'] = $badge;
            if (!empty($features)) $p['features'] = $features;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $db['plans'][] = [
            'id' => $planId,
            'name' => $name,
            'cctv_quota' => $quota,
            'price_monthly' => $priceMonthly,
            'price_annual' => $priceAnnual > 0 ? $priceAnnual : ($priceMonthly * 10),
            'badge' => $badge,
            'features' => !empty($features) ? $features : [
                "$quota Titik Kamera Live",
                "Full HD 1080p Stream H.265",
                "WebRTC & HLS Low Latency",
                "Cloud Recording 7 Hari",
                "Prioritas Bandwidth Relay"
            ]
        ];
    }

    save_db_data($db);
    echo json_encode(['success' => true, 'message' => 'Paket langganan berhasil disimpan!', 'plans' => $db['plans']]);
    exit;
}

// 1C. DELETE SUBSCRIPTION PLAN (SUPER ADMIN)
if ($action === 'delete_plan') {
    $planId = trim($_POST['id'] ?? '');
    if (empty($planId)) {
        echo json_encode(['success' => false, 'message' => 'ID Paket tidak valid.']);
        exit;
    }

    $db = get_db_data();
    $newPlans = [];
    foreach ($db['plans'] as $p) {
        if ($p['id'] !== $planId) {
            $newPlans[] = $p;
        }
    }
    $db['plans'] = $newPlans;
    save_db_data($db);
    echo json_encode(['success' => true, 'message' => 'Paket berhasil dihapus.', 'plans' => $db['plans']]);
    exit;
}

// 2. CREATE SNAP TOKEN & TRANSACTION INVOICE
if ($action === 'create_snap_token') {
    $planId = trim($_POST['plan_id'] ?? 'business_10');
    $billingCycle = trim($_POST['billing_cycle'] ?? 'monthly');
    $customName = trim($_POST['name'] ?? '');
    $customEmail = trim($_POST['email'] ?? '');
    $customPhone = trim($_POST['phone'] ?? '');

    $user = get_logged_in_user();
    $userId = $user ? $user['id'] : null;
    $userName = $user ? $user['name'] : $customName;
    $userEmail = $user ? $user['email'] : $customEmail;
    $userPhone = $user ? ($user['phone'] ?? '+62 812-3456-7890') : $customPhone;

    if (empty($userName) || empty($userEmail)) {
        echo json_encode(['success' => false, 'message' => 'Nama dan Email pembeli wajib diisi!']);
        exit;
    }

    $db = get_db_data();
    $selectedPlan = null;
    foreach ($db['plans'] as $p) {
        if ($p['id'] === $planId) {
            $selectedPlan = $p;
            break;
        }
    }

    if (!$selectedPlan) {
        $selectedPlan = $db['plans'][1]; // fallback business_10
    }

    // Pricing calculation
    $basePrice = ($billingCycle === 'annual') 
        ? (int)$selectedPlan['price_annual'] 
        : (int)$selectedPlan['price_monthly'];

    $taxAmount = (int)round($basePrice * 0.11); // PPN 11%
    $grossAmount = $basePrice + $taxAmount;

    $orderId = 'INV-LWX-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

    $transactionDetails = [
        'order_id' => $orderId,
        'gross_amount' => $grossAmount
    ];

    $customerDetails = [
        'first_name' => $userName,
        'email' => $userEmail,
        'phone' => !empty($userPhone) ? $userPhone : '+62 812-3456-7890'
    ];

    $periodText = ($billingCycle === 'annual') ? '1 Tahun' : '1 Bulan';
    $itemDetails = [
        [
            'id' => $selectedPlan['id'],
            'price' => $basePrice,
            'quantity' => 1,
            'name' => substr("Paket " . $selectedPlan['name'] . " (" . $periodText . ")", 0, 50)
        ],
        [
            'id' => 'TAX-PPN11',
            'price' => $taxAmount,
            'quantity' => 1,
            'name' => 'PPN 11%'
        ]
    ];

    $snapResult = create_midtrans_snap_token($transactionDetails, $customerDetails, $itemDetails);

    if (!$snapResult['success']) {
        echo json_encode($snapResult);
        exit;
    }

    // Save pending invoice to database
    $existingInvoiceIds = array_column($db['invoices'], 'id');
    $newInvoiceId = count($existingInvoiceIds) > 0 ? max($existingInvoiceIds) + 1 : 1;

    $newInvoice = [
        'id' => $newInvoiceId,
        'order_id' => $orderId,
        'user_id' => $userId,
        'user_name' => $userName,
        'user_email' => $userEmail,
        'plan_id' => $selectedPlan['id'],
        'plan_name' => $selectedPlan['name'] . ' (' . $selectedPlan['cctv_quota'] . ' CCTV)',
        'billing_cycle' => $billingCycle,
        'amount' => $basePrice,
        'tax_amount' => $taxAmount,
        'total_amount' => $grossAmount,
        'status' => 'pending',
        'payment_type' => '-',
        'snap_token' => $snapResult['token'],
        'transaction_time' => date('Y-m-d H:i:s'),
        'settlement_time' => null
    ];

    $db['invoices'][] = $newInvoice;
    save_db_data($db);

    echo json_encode([
        'success' => true,
        'snap_token' => $snapResult['token'],
        'order_id' => $orderId,
        'gross_amount' => $grossAmount,
        'redirect_url' => $snapResult['redirect_url'] ?? '',
        'is_simulation' => $snapResult['is_simulation'] ?? false,
        'plan' => [
            'name' => $selectedPlan['name'],
            'quota' => $selectedPlan['cctv_quota'],
            'billing_cycle' => $billingCycle,
            'total_formatted' => 'Rp ' . number_format($grossAmount, 0, ',', '.')
        ]
    ]);
    exit;
}

// 3. COMPLETE / SIMULATE PAYMENT (CLIENT & SANDBOX SIMULATION)
if ($action === 'verify_payment' || $action === 'simulate_payment') {
    $orderId = trim($_POST['order_id'] ?? '');
    $paymentType = trim($_POST['payment_type'] ?? 'midtrans_gateway');

    if (empty($orderId)) {
        echo json_encode(['success' => false, 'message' => 'Order ID wajib diisi!']);
        exit;
    }

    $db = get_db_data();
    $targetInvoice = null;
    foreach ($db['invoices'] as $inv) {
        if ($inv['order_id'] === $orderId) {
            $targetInvoice = $inv;
            break;
        }
    }

    if (!$targetInvoice) {
        echo json_encode(['success' => false, 'message' => 'Invoice transaksi tidak ditemukan!']);
        exit;
    }

    // If userId not attached in invoice, try to find user by email
    $targetUserId = $targetInvoice['user_id'];
    if (!$targetUserId) {
        foreach ($db['users'] as $u) {
            if (strtolower($u['email']) === strtolower($targetInvoice['user_email'])) {
                $targetUserId = $u['id'];
                break;
            }
        }
    }

    if ($targetUserId) {
        activate_user_subscription(
            $targetUserId,
            $targetInvoice['plan_id'],
            $targetInvoice['billing_cycle'],
            $targetInvoice['amount'],
            $orderId,
            $paymentType
        );

        echo json_encode([
            'success' => true,
            'message' => 'Pembayaran Berhasil Dikonfirmasi! Paket Langganan & Kuota CCTV Aktif.',
            'order_id' => $orderId
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Pembayaran diterima! Akun akan diaktivasi setelah login.',
            'order_id' => $orderId
        ]);
    }
    exit;
}

// 4. MIDTRANS WEBHOOK / IPN NOTIFICATION LISTENER
if ($action === 'webhook') {
    $rawNotification = file_get_contents('php://input');
    $notif = json_decode($rawNotification, true);

    if (!$notif || empty($notif['order_id'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid webhook payload']);
        exit;
    }

    $orderId = $notif['order_id'];
    $statusCode = $notif['status_code'] ?? '';
    $grossAmount = $notif['gross_amount'] ?? '';
    $signatureKey = $notif['signature_key'] ?? '';
    $transactionStatus = $notif['transaction_status'] ?? '';
    $paymentType = $notif['payment_type'] ?? 'midtrans';
    $fraudStatus = $notif['fraud_status'] ?? '';

    // Verify signature
    $isSignatureValid = verify_midtrans_signature($orderId, $statusCode, $grossAmount, $signatureKey);

    if (!$isSignatureValid && !empty($signatureKey)) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Invalid Signature']);
        exit;
    }

    $db = get_db_data();
    $targetInvoice = null;
    foreach ($db['invoices'] as &$inv) {
        if ($inv['order_id'] === $orderId) {
            $targetInvoice = &$inv;
            break;
        }
    }

    if ($targetInvoice) {
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $targetInvoice['status'] = 'settlement';
                $targetInvoice['payment_type'] = $paymentType;
                $targetInvoice['settlement_time'] = date('Y-m-d H:i:s');
                if ($targetInvoice['user_id']) {
                    activate_user_subscription(
                        $targetInvoice['user_id'],
                        $targetInvoice['plan_id'],
                        $targetInvoice['billing_cycle'],
                        $targetInvoice['amount'],
                        $orderId,
                        $paymentType
                    );
                }
            }
        } else if ($transactionStatus == 'settlement') {
            $targetInvoice['status'] = 'settlement';
            $targetInvoice['payment_type'] = $paymentType;
            $targetInvoice['settlement_time'] = date('Y-m-d H:i:s');
            if ($targetInvoice['user_id']) {
                activate_user_subscription(
                    $targetInvoice['user_id'],
                    $targetInvoice['plan_id'],
                    $targetInvoice['billing_cycle'],
                    $targetInvoice['amount'],
                    $orderId,
                    $paymentType
                );
            }
        } else if ($transactionStatus == 'pending') {
            $targetInvoice['status'] = 'pending';
            $targetInvoice['payment_type'] = $paymentType;
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $targetInvoice['status'] = 'failed';
        }
        save_db_data($db);
    }

    http_response_code(200);
    echo json_encode(['status' => 'ok', 'order_id' => $orderId]);
    exit;
}

// 5. GET USER BILLING DASHBOARD (FOR CUSTOMER HUB)
if ($action === 'get_billing_dashboard') {
    $user = get_logged_in_user();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $db = get_db_data();
    $userId = (int)$user['id'];

    // Active subscription
    $sub = get_user_subscription($userId);
    if (!$sub) {
        $sub = [
            'plan_id' => 'business_10',
            'plan_name' => 'Business Pro Plan',
            'cctv_quota' => $user['cctv_quota'] ?? 20,
            'billing_cycle' => 'annual',
            'amount' => 2990000,
            'status' => 'active',
            'start_date' => date('Y-m-01 00:00:00'),
            'expires_at' => date('Y-12-31 23:59:59', strtotime('+1 year')),
            'auto_renew' => true
        ];
    }

    // Count camera usage
    $usedCount = 0;
    foreach ($db['cameras'] as $cam) {
        if ($user['role'] === 'super_admin' || (int)($cam['user_id'] ?? 0) === $userId) {
            $usedCount++;
        }
    }

    $invoices = get_user_invoices($userId);
    $profile = get_user_billing_profile($userId);

    echo json_encode([
        'success' => true,
        'subscription' => $sub,
        'cctv_used' => $usedCount,
        'invoices' => $invoices,
        'billing_profile' => $profile,
        'plans' => $db['plans'] ?? []
    ]);
    exit;
}

// 6. UPDATE BILLING PROFILE
if ($action === 'update_billing_profile') {
    $user = get_logged_in_user();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $userId = (int)$user['id'];
    $companyName = trim($_POST['company_name'] ?? '');
    $taxId = trim($_POST['tax_id'] ?? '');
    $billingEmail = trim($_POST['billing_email'] ?? '');
    $billingPhone = trim($_POST['billing_phone'] ?? '');
    $billingAddress = trim($_POST['billing_address'] ?? '');

    $db = get_db_data();
    $found = false;
    foreach ($db['billing_profiles'] as &$prof) {
        if ((int)$prof['user_id'] === $userId) {
            $prof['company_name'] = $companyName;
            $prof['tax_id'] = $taxId;
            $prof['billing_email'] = $billingEmail;
            $prof['billing_phone'] = $billingPhone;
            $prof['billing_address'] = $billingAddress;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $db['billing_profiles'][] = [
            'user_id' => $userId,
            'company_name' => $companyName,
            'tax_id' => $taxId,
            'billing_email' => $billingEmail,
            'billing_phone' => $billingPhone,
            'billing_address' => $billingAddress
        ];
    }

    save_db_data($db);

    echo json_encode([
        'success' => true,
        'message' => 'Profil Billing berhasil diperbarui!'
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid Action']);
