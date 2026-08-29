<?php
/**
 * Database Handler & Storage Layer
 * PT. LOEWIX INDONESIA - CCTV SURVEILLANCE PLATFORM
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_FILE', __DIR__ . '/../data/loewix_db.json');

// Ensure data directory exists
if (!file_exists(__DIR__ . '/../data')) {
    @mkdir(__DIR__ . '/../data', 0777, true);
}

// Initial Database Data Structure
function get_db_data() {
    if (!file_exists(DB_FILE)) {
        $defaultData = [
            'users' => [
                [
                    'id' => 1,
                    'name' => 'Super Admin Loewix',
                    'email' => 'admin@loewixcctv.com',
                    'password' => password_hash('admin123', PASSWORD_BCRYPT),
                    'role' => 'super_admin',
                    'cctv_quota' => 9999,
                    'phone' => '+62 (021) 800-LOEWIX',
                    'city' => 'all',
                    'status' => 'active',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 2,
                    'name' => 'PT. Jaya Sentosa Enterprise',
                    'email' => 'customer@jayasentosa.com',
                    'password' => password_hash('customer123', PASSWORD_BCRYPT),
                    'role' => 'customer',
                    'cctv_quota' => 20,
                    'phone' => '+62 812-3456-7890',
                    'city' => 'siantar',
                    'status' => 'active',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 3,
                    'name' => 'PT. Berlian Djaya Nusantara',
                    'email' => 'berlian@gmail.com',
                    'password' => password_hash('berlian123', PASSWORD_BCRYPT),
                    'role' => 'customer',
                    'cctv_quota' => 20,
                    'phone' => '+6285771593522',
                    'city' => 'jakarta',
                    'status' => 'active',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 4,
                    'name' => 'Onefifteenh Caffe',
                    'email' => 'Caffe@gmail.com',
                    'password' => password_hash('caffe123', PASSWORD_BCRYPT),
                    'role' => 'customer',
                    'cctv_quota' => 5,
                    'phone' => '085771593522',
                    'city' => 'bali',
                    'status' => 'active',
                    'created_at' => '2026-08-15 00:00:00'
                ]
            ],
            'cameras' => [
                [
                    'id' => 5001,
                    'user_id' => 2,
                    'title' => 'TESS',
                    'city' => 'siantar',
                    'streamPath' => 'cctv_loewix_1',
                    'hls_url' => 'http://stream.loewixcctv.com/cctv_loewix_1/index.m3u8',
                    'thumbnail' => 'assets/image/thumbnail/default-thumbnail.png',
                    'lat' => '2.9750',
                    'lng' => '99.0789',
                    'platform' => 'mediamtx',
                    'status' => 'online',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 5002,
                    'user_id' => 3,
                    'title' => 'CAM LOEWIX JAKARTA 1',
                    'city' => 'jakarta',
                    'streamPath' => 'cctv_loewix_2',
                    'hls_url' => 'http://stream.loewixcctv.com/cctv_loewix_2/index.m3u8',
                    'thumbnail' => 'assets/image/thumbnail/default-thumbnail.png',
                    'lat' => '-6.2088',
                    'lng' => '106.8456',
                    'platform' => 'mediamtx',
                    'status' => 'online',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 5003,
                    'user_id' => 4,
                    'title' => 'ONEFIFTEENH BALI CAM 1',
                    'city' => 'bali',
                    'streamPath' => 'cctv_loewix_3',
                    'hls_url' => 'http://stream.loewixcctv.com/cctv_loewix_3/index.m3u8',
                    'thumbnail' => 'assets/image/thumbnail/default-thumbnail.png',
                    'lat' => '-8.6705',
                    'lng' => '115.2126',
                    'platform' => 'mediamtx',
                    'status' => 'online',
                    'created_at' => '2026-08-15 00:00:00'
                ]
            ],
            'cities' => [
                ['id' => 'siantar', 'name' => 'Kota Pematangsiantar', 'lat' => 2.9568, 'lng' => 99.0619, 'zoom' => 14],
                ['id' => 'jakarta', 'name' => 'DKI Jakarta', 'lat' => -6.2088, 'lng' => 106.8456, 'zoom' => 12],
                ['id' => 'medan', 'name' => 'Kota Medan', 'lat' => 3.5952, 'lng' => 98.6722, 'zoom' => 13],
                ['id' => 'bandung', 'name' => 'Kota Bandung', 'lat' => -6.9175, 'lng' => 107.6191, 'zoom' => 12],
                ['id' => 'bali', 'name' => 'Bali / Denpasar', 'lat' => -8.6705, 'lng' => 115.2126, 'zoom' => 12]
            ]
        ];
        file_put_contents(DB_FILE, json_encode($defaultData, JSON_PRETTY_PRINT));
        return $defaultData;
    }
    $content = file_get_contents(DB_FILE);
    $data = json_decode($content, true);
    if (!$data || empty($data['users']) || empty($data['cameras'])) {
        $data = [
            'users' => [
                [
                    'id' => 1,
                    'name' => 'Super Admin Loewix',
                    'email' => 'admin@loewixcctv.com',
                    'password' => password_hash('admin123', PASSWORD_BCRYPT),
                    'role' => 'super_admin',
                    'cctv_quota' => 9999,
                    'phone' => '+62 (021) 800-LOEWIX',
                    'city' => 'all',
                    'status' => 'active',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 2,
                    'name' => 'PT. Jaya Sentosa Enterprise',
                    'email' => 'customer@jayasentosa.com',
                    'password' => password_hash('customer123', PASSWORD_BCRYPT),
                    'role' => 'customer',
                    'cctv_quota' => 20,
                    'phone' => '+62 812-3456-7890',
                    'city' => 'siantar',
                    'status' => 'active',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 3,
                    'name' => 'PT. Berlian Djaya Nusantara',
                    'email' => 'berlian@gmail.com',
                    'password' => password_hash('berlian123', PASSWORD_BCRYPT),
                    'role' => 'customer',
                    'cctv_quota' => 20,
                    'phone' => '+6285771593522',
                    'city' => 'jakarta',
                    'status' => 'active',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 4,
                    'name' => 'Onefifteenh Caffe',
                    'email' => 'Caffe@gmail.com',
                    'password' => password_hash('caffe123', PASSWORD_BCRYPT),
                    'role' => 'customer',
                    'cctv_quota' => 5,
                    'phone' => '085771593522',
                    'city' => 'bali',
                    'status' => 'active',
                    'created_at' => '2026-08-15 00:00:00'
                ]
            ],
            'cameras' => [
                [
                    'id' => 5001,
                    'user_id' => 2,
                    'title' => 'TESS',
                    'city' => 'siantar',
                    'streamPath' => 'cctv_loewix_1',
                    'hls_url' => 'http://stream.loewixcctv.com/cctv_loewix_1/index.m3u8',
                    'thumbnail' => 'assets/image/thumbnail/default-thumbnail.png',
                    'lat' => '2.9750',
                    'lng' => '99.0789',
                    'platform' => 'mediamtx',
                    'status' => 'online',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 5002,
                    'user_id' => 3,
                    'title' => 'CAM LOEWIX JAKARTA 1',
                    'city' => 'jakarta',
                    'streamPath' => 'cctv_loewix_2',
                    'hls_url' => 'http://stream.loewixcctv.com/cctv_loewix_2/index.m3u8',
                    'thumbnail' => 'assets/image/thumbnail/default-thumbnail.png',
                    'lat' => '-6.2088',
                    'lng' => '106.8456',
                    'platform' => 'mediamtx',
                    'status' => 'online',
                    'created_at' => '2026-08-14 00:00:00'
                ],
                [
                    'id' => 5003,
                    'user_id' => 4,
                    'title' => 'ONEFIFTEENH BALI CAM 1',
                    'city' => 'bali',
                    'streamPath' => 'cctv_loewix_3',
                    'hls_url' => 'http://stream.loewixcctv.com/cctv_loewix_3/index.m3u8',
                    'thumbnail' => 'assets/image/thumbnail/default-thumbnail.png',
                    'lat' => '-8.6705',
                    'lng' => '115.2126',
                    'platform' => 'mediamtx',
                    'status' => 'online',
                    'created_at' => '2026-08-15 00:00:00'
                ]
            ],
            'cities' => [
                ['id' => 'siantar', 'name' => 'Kota Pematangsiantar', 'lat' => 2.9568, 'lng' => 99.0619, 'zoom' => 14],
                ['id' => 'jakarta', 'name' => 'DKI Jakarta', 'lat' => -6.2088, 'lng' => 106.8456, 'zoom' => 12],
                ['id' => 'medan', 'name' => 'Kota Medan', 'lat' => 3.5952, 'lng' => 98.6722, 'zoom' => 13],
                ['id' => 'bandung', 'name' => 'Kota Bandung', 'lat' => -6.9175, 'lng' => 107.6191, 'zoom' => 12],
                ['id' => 'bali', 'name' => 'Bali / Denpasar', 'lat' => -8.6705, 'lng' => 115.2126, 'zoom' => 12]
            ]
        ];
        file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    // Auto-migration: ensure 'plans' key exists
    if (!isset($data['plans']) || !is_array($data['plans']) || empty($data['plans'])) {
        $data['plans'] = [
            [
                'id' => 'starter_4',
                'name' => 'Starter Cloud',
                'cctv_quota' => 4,
                'price_monthly' => 149000,
                'price_annual' => 1490000,
                'features' => [
                    '4 Titik Kamera Live',
                    'Full HD 1080p Stream H.265',
                    'WebRTC & HLS Low Latency',
                    'Cloud Recording 7 Hari',
                    'Dukungan Teknis Standar'
                ],
                'badge' => ''
            ],
            [
                'id' => 'business_10',
                'name' => 'Business Pro',
                'cctv_quota' => 10,
                'price_monthly' => 299000,
                'price_annual' => 2990000,
                'features' => [
                    '10 Titik Kamera Live',
                    '2K / 4K Ultra HD Streaming',
                    'AI Motion & Intrusion Detection',
                    'Multi-User Access Control',
                    'Cloud Recording 14 Hari',
                    'Prioritas Bandwidth Relay'
                ],
                'badge' => 'POPULER'
            ],
            [
                'id' => 'enterprise_20',
                'name' => 'Enterprise Fleet',
                'cctv_quota' => 20,
                'price_monthly' => 549000,
                'price_annual' => 5490000,
                'features' => [
                    '20 Titik Kamera Live',
                    '4K Ultra HD & AI Telemetry',
                    'AI People & Vehicle Counting',
                    'Cloud Recording 30 Hari',
                    'Dedicated P2P Relay Server',
                    'SLA 99.9% 24/7 Priority Support'
                ],
                'badge' => 'ENTERPRISE'
            ],
            [
                'id' => 'corporate_50',
                'name' => 'Corporate Custom',
                'cctv_quota' => 50,
                'price_monthly' => 1199000,
                'price_annual' => 11990000,
                'features' => [
                    '50 Titik Kamera Live',
                    'Dedicated Streaming Node',
                    'Custom Domain & Brand White-label',
                    'Unlimited Cloud Archive',
                    'Dedicated Account Manager'
                ],
                'badge' => 'CUSTOM'
            ]
        ];
        file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT));
    }

    if (!isset($data['subscriptions']) || !is_array($data['subscriptions'])) {
        $data['subscriptions'] = [
            [
                'id' => 1,
                'user_id' => 2,
                'plan_id' => 'enterprise_20',
                'plan_name' => 'Enterprise Fleet',
                'cctv_quota' => 20,
                'billing_cycle' => 'annual',
                'amount' => 5490000,
                'status' => 'active',
                'start_date' => '2026-08-14 00:00:00',
                'expires_at' => '2027-08-14 23:59:59',
                'auto_renew' => true
            ],
            [
                'id' => 2,
                'user_id' => 3,
                'plan_id' => 'enterprise_20',
                'plan_name' => 'Enterprise Fleet',
                'cctv_quota' => 20,
                'billing_cycle' => 'annual',
                'amount' => 5490000,
                'status' => 'active',
                'start_date' => '2026-08-14 00:00:00',
                'expires_at' => '2027-08-14 23:59:59',
                'auto_renew' => true
            ],
            [
                'id' => 3,
                'user_id' => 4,
                'plan_id' => 'starter_4',
                'plan_name' => 'Starter Cloud (Plus 1 Cam)',
                'cctv_quota' => 5,
                'billing_cycle' => 'monthly',
                'amount' => 149000,
                'status' => 'active',
                'start_date' => '2026-08-15 00:00:00',
                'expires_at' => '2026-09-15 23:59:59',
                'auto_renew' => true
            ]
        ];
        file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT));
    }

    if (!isset($data['invoices']) || !is_array($data['invoices'])) {
        $data['invoices'] = [
            [
                'id' => 1,
                'order_id' => 'INV-LOEWIX-20260814-001',
                'user_id' => 2,
                'user_name' => 'PT. Jaya Sentosa Enterprise',
                'user_email' => 'customer@jayasentosa.com',
                'plan_id' => 'enterprise_20',
                'plan_name' => 'Enterprise Fleet (20 CCTV)',
                'billing_cycle' => 'annual',
                'amount' => 5490000,
                'tax_amount' => 603900,
                'total_amount' => 6093900,
                'status' => 'settlement',
                'payment_type' => 'bank_transfer_bca',
                'snap_token' => 'SNAP_LOEWIX_INIT_001',
                'transaction_time' => '2026-08-14 10:15:20',
                'settlement_time' => '2026-08-14 10:18:45'
            ],
            [
                'id' => 2,
                'order_id' => 'INV-LOEWIX-20260814-002',
                'user_id' => 3,
                'user_name' => 'PT. Berlian Djaya Nusantara',
                'user_email' => 'berlian@gmail.com',
                'plan_id' => 'enterprise_20',
                'plan_name' => 'Enterprise Fleet (20 CCTV)',
                'billing_cycle' => 'annual',
                'amount' => 5490000,
                'tax_amount' => 603900,
                'total_amount' => 6093900,
                'status' => 'settlement',
                'payment_type' => 'qris',
                'snap_token' => 'SNAP_LOEWIX_INIT_002',
                'transaction_time' => '2026-08-14 14:22:10',
                'settlement_time' => '2026-08-14 14:23:05'
            ],
            [
                'id' => 3,
                'order_id' => 'INV-LOEWIX-20260815-003',
                'user_id' => 4,
                'user_name' => 'Onefifteenh Caffe',
                'user_email' => 'Caffe@gmail.com',
                'plan_id' => 'starter_4',
                'plan_name' => 'Starter Cloud (4 CCTV)',
                'billing_cycle' => 'monthly',
                'amount' => 149000,
                'tax_amount' => 16390,
                'total_amount' => 165390,
                'status' => 'settlement',
                'payment_type' => 'gopay',
                'snap_token' => 'SNAP_LOEWIX_INIT_003',
                'transaction_time' => '2026-08-15 09:30:00',
                'settlement_time' => '2026-08-15 09:30:45'
            ]
        ];
        file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT));
    }

    if (!isset($data['billing_profiles']) || !is_array($data['billing_profiles'])) {
        $data['billing_profiles'] = [
            [
                'user_id' => 2,
                'company_name' => 'PT. Jaya Sentosa Enterprise',
                'tax_id' => '01.234.567.8-012.000',
                'billing_email' => 'finance@jayasentosa.com',
                'billing_phone' => '+62 812-3456-7890',
                'billing_address' => 'Jl. Sutomo No. 88, Pematangsiantar, Sumatera Utara'
            ],
            [
                'user_id' => 3,
                'company_name' => 'PT. Berlian Djaya Nusantara',
                'tax_id' => '02.345.678.9-034.000',
                'billing_email' => 'berlian@gmail.com',
                'billing_phone' => '+62 857-7159-3522',
                'billing_address' => 'Gedung Cyber 2 Tower Lt. 15, Jl. HR. Rasuna Said, Jakarta Selatan'
            ],
            [
                'user_id' => 4,
                'company_name' => 'Onefifteenh Caffe',
                'tax_id' => '-',
                'billing_email' => 'Caffe@gmail.com',
                'billing_phone' => '085771593522',
                'billing_address' => 'Jl. Danau Tamblingan No. 115, Sanur, Denpasar Selatan, Bali'
            ]
        ];
        file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    return $data;
}

function save_db_data($data) {
    file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

// Authentication Helpers
function get_logged_in_user() {
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_email'])) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'cctv_quota' => $_SESSION['cctv_quota'] ?? 10,
            'city' => $_SESSION['user_city'] ?? 'all'
        ];
    }
    return null;
}

function is_super_admin() {
    $user = get_logged_in_user();
    return $user && $user['role'] === 'super_admin';
}

/**
 * Get user active subscription
 */
function get_user_subscription($userId) {
    $db = get_db_data();
    foreach ($db['subscriptions'] as $sub) {
        if ((int)$sub['user_id'] === (int)$userId && $sub['status'] === 'active') {
            return $sub;
        }
    }
    return null;
}

/**
 * Get user invoices list
 */
function get_user_invoices($userId) {
    $db = get_db_data();
    $list = [];
    foreach ($db['invoices'] as $inv) {
        if ((int)$inv['user_id'] === (int)$userId) {
            $list[] = $inv;
        }
    }
    // Sort newest first
    usort($list, function($a, $b) {
        return strcmp($b['transaction_time'] ?? '', $a['transaction_time'] ?? '');
    });
    return $list;
}

/**
 * Get user billing profile
 */
function get_user_billing_profile($userId) {
    $db = get_db_data();
    foreach ($db['billing_profiles'] as $prof) {
        if ((int)$prof['user_id'] === (int)$userId) {
            return $prof;
        }
    }
    // Fallback if not set
    foreach ($db['users'] as $u) {
        if ((int)$u['id'] === (int)$userId) {
            return [
                'user_id' => $u['id'],
                'company_name' => $u['name'],
                'tax_id' => '-',
                'billing_email' => $u['email'],
                'billing_phone' => $u['phone'] ?? '-',
                'billing_address' => 'Kota ' . ucfirst($u['city'] ?? 'Jakarta') . ', Indonesia'
            ];
        }
    }
    return null;
}

/**
 * Activate or Renew Subscription upon successful payment
 */
function activate_user_subscription($userId, $planId, $billingCycle, $amount, $orderId, $paymentType = 'midtrans') {
    $db = get_db_data();
    
    // Find plan details
    $targetPlan = null;
    foreach ($db['plans'] as $p) {
        if ($p['id'] === $planId) {
            $targetPlan = $p;
            break;
        }
    }
    if (!$targetPlan) {
        $targetPlan = $db['plans'][1]; // fallback to business_10
    }

    $cctvQuota = (int)$targetPlan['cctv_quota'];
    $durationDays = ($billingCycle === 'annual') ? 365 : 30;
    
    $startDate = date('Y-m-d H:i:s');
    $expiresAt = date('Y-m-d 23:59:59', strtotime("+{$durationDays} days"));

    // Update or create subscription
    $foundSub = false;
    foreach ($db['subscriptions'] as &$sub) {
        if ((int)$sub['user_id'] === (int)$userId) {
            $sub['plan_id'] = $targetPlan['id'];
            $sub['plan_name'] = $targetPlan['name'];
            $sub['cctv_quota'] = $cctvQuota;
            $sub['billing_cycle'] = $billingCycle;
            $sub['amount'] = $amount;
            $sub['status'] = 'active';
            $sub['start_date'] = $startDate;
            $sub['expires_at'] = $expiresAt;
            $foundSub = true;
            break;
        }
    }
    if (!$foundSub) {
        $existingSubIds = array_column($db['subscriptions'], 'id');
        $newSubId = count($existingSubIds) > 0 ? max($existingSubIds) + 1 : 1;
        $db['subscriptions'][] = [
            'id' => $newSubId,
            'user_id' => (int)$userId,
            'plan_id' => $targetPlan['id'],
            'plan_name' => $targetPlan['name'],
            'cctv_quota' => $cctvQuota,
            'billing_cycle' => $billingCycle,
            'amount' => $amount,
            'status' => 'active',
            'start_date' => $startDate,
            'expires_at' => $expiresAt,
            'auto_renew' => true
        ];
    }

    // Update user cctv quota
    foreach ($db['users'] as &$u) {
        if ((int)$u['id'] === (int)$userId) {
            $u['cctv_quota'] = $cctvQuota;
            break;
        }
    }

    // Update invoice status if orderId provided
    if ($orderId) {
        foreach ($db['invoices'] as &$inv) {
            if ($inv['order_id'] === $orderId) {
                $inv['status'] = 'settlement';
                $inv['payment_type'] = $paymentType;
                $inv['settlement_time'] = date('Y-m-d H:i:s');
                break;
            }
        }
    }

    save_db_data($db);

    // Update active session quota if current user is logged in
    if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$userId) {
        $_SESSION['cctv_quota'] = $cctvQuota;
    }

    return true;
}

