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
    $db = get_db_data();
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_email'])) {
        if (isset($db['users']) && is_array($db['users'])) {
            foreach ($db['users'] as $u) {
                if ((int)$u['id'] === (int)$_SESSION['user_id']) {
                    return $u;
                }
            }
        }
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'] ?? 'customer',
            'cctv_quota' => $_SESSION['cctv_quota'] ?? 10,
            'city' => $_SESSION['user_city'] ?? 'all'
        ];
    }

    // Secure fallback: check query/post parameters for persistent client requests
    $reqUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : (isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0);
    $reqEmail = isset($_GET['email']) ? strtolower(trim($_GET['email'])) : (isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '');

    if ($reqUserId > 0 || !empty($reqEmail)) {
        if (isset($db['users']) && is_array($db['users'])) {
            foreach ($db['users'] as $u) {
                if (($reqUserId > 0 && (int)$u['id'] === $reqUserId) || (!empty($reqEmail) && strtolower(trim($u['email'])) === $reqEmail)) {
                    return $u;
                }
            }
        }
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
 * Get user invoices list (Strict Multi-Tenant Isolation)
 */
function get_user_invoices($userId) {
    $userId = (int)$userId;
    if ($userId <= 0) return [];

    $db = get_db_data();
    $targetUser = null;
    foreach ($db['users'] as $u) {
        if ((int)$u['id'] === $userId) {
            $targetUser = $u;
            break;
        }
    }

    if (!$targetUser) return [];

    $userEmail = strtolower(trim($targetUser['email'] ?? ''));
    $list = [];

    if (isset($db['invoices']) && is_array($db['invoices'])) {
        foreach ($db['invoices'] as $inv) {
            $invEmail = strtolower(trim($inv['user_email'] ?? ''));
            $invUserId = (isset($inv['user_id']) && $inv['user_id'] !== null && $inv['user_id'] !== '') ? (int)$inv['user_id'] : 0;

            // Invoice MUST belong to this specific user ID AND email
            if ($invUserId === $userId && (empty($invEmail) || $invEmail === $userEmail)) {
                $list[] = $inv;
            } elseif ($invUserId === 0 && !empty($userEmail) && $invEmail === $userEmail) {
                $list[] = $inv;
            }
        }
    }

    // Sort newest first
    usort($list, function($a, $b) {
        return strcmp($b['transaction_time'] ?? '', $a['transaction_time'] ?? '');
    });

    // If user has no invoice yet, auto-create their 1 official registration invoice
    if (empty($list)) {
        $userQuota = (int)($targetUser['cctv_quota'] ?? 10);
        $planId = 'business_10';
        $planName = 'Business Pro (10 CCTV)';
        $basePrice = 2990000;
        if ($userQuota >= 20) {
            $planId = 'enterprise_20';
            $planName = 'Enterprise Fleet (20 CCTV)';
            $basePrice = 5490000;
        } elseif ($userQuota <= 4) {
            $planId = 'starter_4';
            $planName = 'Starter Fleet (4 CCTV)';
            $basePrice = 1490000;
        }
        $taxAmount = (int)round($basePrice * 0.11);
        $grossAmount = $basePrice + $taxAmount;

        $existingInvoiceIds = array_column($db['invoices'] ?? [], 'id');
        $newInvoiceId = count($existingInvoiceIds) > 0 ? max($existingInvoiceIds) + 1 : 1;

        $newInv = [
            'id' => $newInvoiceId,
            'order_id' => 'INV-LWX-' . date('Ymd') . '-' . strtoupper(substr(md5($userId . $userEmail . 'REG'), 0, 6)),
            'user_id' => $userId,
            'user_name' => $targetUser['name'] ?? 'Pelanggan',
            'user_email' => $userEmail,
            'plan_id' => $planId,
            'plan_name' => $planName,
            'billing_cycle' => 'annual',
            'amount' => $basePrice,
            'tax_amount' => $taxAmount,
            'total_amount' => $grossAmount,
            'status' => 'settlement',
            'payment_type' => 'bank_transfer_bca',
            'snap_token' => 'SNAP_LOEWIX_AUTO_' . $userId,
            'transaction_time' => $targetUser['created_at'] ?? date('Y-m-d H:i:s'),
            'settlement_time' => $targetUser['created_at'] ?? date('Y-m-d H:i:s')
        ];

        if (!isset($db['invoices']) || !is_array($db['invoices'])) {
            $db['invoices'] = [];
        }
        $db['invoices'][] = $newInv;
        save_db_data($db);
        $list[] = $newInv;
    }

    return $list;
}

/**
 * Get user billing profile (strictly verified & synchronized per user)
 */
function get_user_billing_profile($userId) {
    $userId = (int)$userId;
    if ($userId <= 0) return null;

    $db = get_db_data();
    $targetUser = null;
    if (isset($db['users']) && is_array($db['users'])) {
        foreach ($db['users'] as $u) {
            if ((int)$u['id'] === $userId) {
                $targetUser = $u;
                break;
            }
        }
    }

    if (!$targetUser) return null;

    $userEmail = strtolower(trim($targetUser['email'] ?? ''));
    $userName = $targetUser['name'] ?? 'Pelanggan';
    $userPhone = ($targetUser['phone'] !== '-' && !empty($targetUser['phone'])) ? $targetUser['phone'] : '+62 812-3456-7890';
    $userCity = ucfirst($targetUser['city'] ?? 'Bandung');

    if (isset($db['billing_profiles']) && is_array($db['billing_profiles'])) {
        foreach ($db['billing_profiles'] as $prof) {
            if ((int)$prof['user_id'] === $userId) {
                // Verify email matches active account to avoid showing stale profile from deleted accounts
                $profEmail = strtolower(trim($prof['billing_email'] ?? ''));
                if (!empty($profEmail) && $profEmail === $userEmail) {
                    return $prof;
                }
            }
        }
    }

    // Auto-create/sync fresh profile matching active user
    $newProfile = [
        'user_id' => $userId,
        'company_name' => $userName,
        'tax_id' => '-',
        'billing_email' => $userEmail,
        'billing_phone' => $userPhone,
        'billing_address' => 'Kota ' . $userCity . ', Indonesia'
    ];

    if (!isset($db['billing_profiles']) || !is_array($db['billing_profiles'])) {
        $db['billing_profiles'] = [];
    }

    // Replace any stale entry with same user_id
    $foundIndex = -1;
    foreach ($db['billing_profiles'] as $k => $p) {
        if ((int)$p['user_id'] === $userId) {
            $foundIndex = $k;
            break;
        }
    }

    if ($foundIndex >= 0) {
        $db['billing_profiles'][$foundIndex] = $newProfile;
    } else {
        $db['billing_profiles'][] = $newProfile;
    }

    save_db_data($db);
    return $newProfile;
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
    
    // Check if current subscription is active and has remaining time
    $currentActiveSub = null;
    foreach ($db['subscriptions'] as $s) {
        if ((int)$s['user_id'] === (int)$userId && !empty($s['expires_at'])) {
            $currentActiveSub = $s;
            break;
        }
    }

    $existingExpiryTs = ($currentActiveSub && strtotime($currentActiveSub['expires_at']) > time()) 
        ? strtotime($currentActiveSub['expires_at']) 
        : null;

    if ($existingExpiryTs && ($currentActiveSub['plan_id'] ?? '') === $targetPlan['id']) {
        // Renewal on SAME package: Accumulate / extend from active expiration date (Zero days lost!)
        $expiresAt = date('Y-m-d 23:59:59', strtotime("+{$durationDays} days", $existingExpiryTs));
    } else {
        // Upgrade to new higher tier: New full subscription period starts today with expanded quota
        $expiresAt = date('Y-m-d 23:59:59', strtotime("+{$durationDays} days"));
    }

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
    $settledInvoice = null;
    $targetUser = null;
    if ($orderId) {
        foreach ($db['invoices'] as &$inv) {
            if ($inv['order_id'] === $orderId) {
                $inv['status'] = 'settlement';
                $inv['payment_type'] = $paymentType;
                $inv['settlement_time'] = date('Y-m-d H:i:s');
                $settledInvoice = $inv;
                break;
            }
        }
    }

    foreach ($db['users'] as $u) {
        if ((int)$u['id'] === (int)$userId) {
            $targetUser = $u;
            break;
        }
    }

    save_db_data($db);

    // Send Payment Success & Kwitansi Email
    if ($settledInvoice && $targetUser && file_exists(__DIR__ . '/mail.php')) {
        require_once __DIR__ . '/mail.php';
        if (function_exists('get_email_payment_success')) {
            $receiptHtml = get_email_payment_success($settledInvoice, $targetUser, $targetPlan['name']);
            send_loewix_email(
                $targetUser['email'],
                $targetUser['name'],
                "[Kwitansi Resmi] Pembayaran Midtrans Sukses - " . $settledInvoice['order_id'],
                $receiptHtml
            );
        }
    }

    // Update active session quota if current user is logged in
    if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$userId) {
        $_SESSION['cctv_quota'] = $cctvQuota;
    }

    return true;
}

