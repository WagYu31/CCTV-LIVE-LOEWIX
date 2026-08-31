<?php
/**
 * Authentication REST API Endpoint
 * PT. LOEWIX INDONESIA
 */

error_reporting(0);
ini_set('display_errors', '0');
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mail.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Helper to get client IP
function get_client_ip_address() {
    return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email dan Password wajib diisi!']);
        exit;
    }

    $db = get_db_data();
    $foundUser = null;

    foreach ($db['users'] as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            $foundUser = $user;
            break;
        }
    }

    if (!$foundUser) {
        echo json_encode(['success' => false, 'message' => 'Email atau Password salah!']);
        exit;
    }

    if ($foundUser['status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Akun Anda sedang dinonaktifkan oleh Super Admin Loewix.']);
        exit;
    }

    // Verify Password (supports password_verify and plaintext fallback for initial test)
    $passwordValid = password_verify($password, $foundUser['password']) || ($password === $foundUser['password']);

    if (!$passwordValid) {
        echo json_encode(['success' => false, 'message' => 'Email atau Password salah!']);
        exit;
    }

    // Count active cameras for this user
    $usedCount = 0;
    foreach ($db['cameras'] as $cam) {
        if ($foundUser['role'] === 'super_admin' || (int)($cam['user_id'] ?? 0) === (int)$foundUser['id']) {
            $usedCount++;
        }
    }

    // Set Session
    $_SESSION['user_id'] = $foundUser['id'];
    $_SESSION['user_name'] = $foundUser['name'];
    $_SESSION['user_email'] = $foundUser['email'];
    $_SESSION['user_role'] = $foundUser['role'];
    $_SESSION['cctv_quota'] = $foundUser['cctv_quota'];
    $_SESSION['cctv_used'] = $usedCount;
    $_SESSION['user_city'] = $foundUser['city'];

    // Send Security Login Email Alert
    $loginIp = get_client_ip_address();
    $loginTime = date('d M Y, H:i:s');
    $device = $_SERVER['HTTP_USER_AGENT'] ?? 'Web Browser';
    $loginEmailHtml = get_email_login_alert($foundUser['name'], $foundUser['email'], $loginIp, $loginTime, $device);
    send_loewix_email($foundUser['email'], $foundUser['name'], "[Security Alert] Deteksi Login Akun Loewix CCTV", $loginEmailHtml);

    echo json_encode([
        'success' => true,
        'message' => 'Login Berhasil!',
        'user' => [
            'id' => $foundUser['id'],
            'name' => $foundUser['name'],
            'email' => $foundUser['email'],
            'role' => $foundUser['role'],
            'cctv_quota' => $foundUser['cctv_quota'],
            'cctv_used' => $usedCount,
            'city' => $foundUser['city']
        ]
    ]);
    exit;
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logout Berhasil!']);
    exit;
}

if ($action === 'check_session') {
    $user = get_logged_in_user();
    if ($user) {
        $db = get_db_data();
        $usedCount = 0;
        foreach ($db['cameras'] as $cam) {
            if ($user['role'] === 'super_admin' || $cam['user_id'] == $user['id']) {
                $usedCount++;
            }
        }
        $user['cctv_used'] = $usedCount;

        echo json_encode(['logged_in' => true, 'user' => $user]);
    } else {
        echo json_encode(['logged_in' => false]);
    }
    exit;
}

if ($action === 'register') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? 'siantar');
    $planId = trim($_POST['plan_id'] ?? 'business_10');

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Nama, Email, dan Password wajib diisi!']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Format email tidak valid!']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Kata sandi minimal 6 karakter!']);
        exit;
    }

    $db = get_db_data();

    // Check if email already registered
    $existingUser = null;
    foreach ($db['users'] as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            $existingUser = $user;
            break;
        }
    }

    if ($existingUser) {
        // Check if password matches to allow seamless continuation of checkout
        $passwordValid = password_verify($password, $existingUser['password']) || ($password === $existingUser['password']);
        if ($passwordValid) {
            // Log in the user and return their session so they can proceed directly to payment
            $_SESSION['user_id'] = $existingUser['id'];
            $_SESSION['user_name'] = $existingUser['name'];
            $_SESSION['user_email'] = $existingUser['email'];
            $_SESSION['user_role'] = $existingUser['role'];
            $_SESSION['cctv_quota'] = $existingUser['cctv_quota'];
            $_SESSION['user_city'] = $existingUser['city'];

            echo json_encode([
                'success' => true,
                'message' => 'Akun terverifikasi! Melanjutkan ke sesi pembayaran...',
                'user' => [
                    'id' => $existingUser['id'],
                    'name' => $existingUser['name'],
                    'email' => $existingUser['email'],
                    'role' => $existingUser['role'],
                    'cctv_quota' => $existingUser['cctv_quota'],
                    'city' => $existingUser['city']
                ]
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Email ini sudah terdaftar. Silakan masukkan kata sandi Anda yang benar atau klik "Masuk ke Akun Anda" di bawah.'
            ]);
            exit;
        }
    }

    // Determine initial quota based on plan
    $quota = 10;
    $planName = 'Business Pro';
    if (isset($db['plans'])) {
        foreach ($db['plans'] as $p) {
            if ($p['id'] === $planId) {
                $quota = (int)$p['cctv_quota'];
                $planName = $p['name'];
                break;
            }
        }
    }

    // Generate safe user ID
    $existingIds = array_column($db['users'], 'id');
    $newUserId = count($existingIds) > 0 ? max($existingIds) + 1 : 1;

    $newUser = [
        'id' => $newUserId,
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'role' => 'customer',
        'cctv_quota' => $quota,
        'phone' => !empty($phone) ? $phone : '-',
        'city' => $city,
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db['users'][] = $newUser;

    // Auto-create default billing profile for the user
    if (!isset($db['billing_profiles']) || !is_array($db['billing_profiles'])) {
        $db['billing_profiles'] = [];
    }
    $db['billing_profiles'][] = [
        'user_id' => $newUser['id'],
        'company_name' => $name,
        'tax_id' => '-',
        'billing_email' => $email,
        'billing_phone' => !empty($phone) ? $phone : '+62 812-3456-7890',
        'billing_address' => 'Kota ' . ucfirst($city) . ', Indonesia'
    ];

    // Auto-create active subscription
    if (!isset($db['subscriptions']) || !is_array($db['subscriptions'])) {
        $db['subscriptions'] = [];
    }
    $basePrice = 2990000;
    if ($quota >= 20) {
        $basePrice = 5490000;
    } elseif ($quota <= 4) {
        $basePrice = 1490000;
    }
    $taxAmount = (int)round($basePrice * 0.11);
    $grossAmount = $basePrice + $taxAmount;

    $existingSubIds = array_column($db['subscriptions'], 'id');
    $newSubId = count($existingSubIds) > 0 ? max($existingSubIds) + 1 : 1;
    $db['subscriptions'][] = [
        'id' => $newSubId,
        'user_id' => $newUser['id'],
        'plan_id' => $planId,
        'plan_name' => $planName . ' (' . $quota . ' CCTV)',
        'cctv_quota' => $quota,
        'billing_cycle' => 'annual',
        'amount' => $grossAmount,
        'status' => 'active',
        'start_date' => date('Y-m-d H:i:s'),
        'expires_at' => date('Y-m-d 23:59:59', strtotime('+1 year')),
        'auto_renew' => true
    ];

    // Auto-create official registration invoice
    if (!isset($db['invoices']) || !is_array($db['invoices'])) {
        $db['invoices'] = [];
    }
    $existingInvoiceIds = array_column($db['invoices'], 'id');
    $newInvoiceId = count($existingInvoiceIds) > 0 ? max($existingInvoiceIds) + 1 : 1;
    $orderId = 'INV-LWX-' . date('Ymd') . '-' . strtoupper(substr(md5($newUser['id'] . $email . time()), 0, 6));

    $db['invoices'][] = [
        'id' => $newInvoiceId,
        'order_id' => $orderId,
        'user_id' => (int)$newUser['id'],
        'user_name' => $name,
        'user_email' => strtolower($email),
        'plan_id' => $planId,
        'plan_name' => $planName . ' (' . $quota . ' CCTV)',
        'billing_cycle' => 'annual',
        'amount' => $basePrice,
        'tax_amount' => $taxAmount,
        'total_amount' => $grossAmount,
        'status' => 'settlement',
        'payment_type' => 'bank_transfer_bca',
        'snap_token' => 'SNAP_LOEWIX_AUTO_' . $newUser['id'],
        'transaction_time' => date('Y-m-d H:i:s'),
        'settlement_time' => date('Y-m-d H:i:s')
    ];

    save_db_data($db);

    // Auto-login newly registered user
    $_SESSION['user_id'] = $newUser['id'];
    $_SESSION['user_name'] = $newUser['name'];
    $_SESSION['user_email'] = $newUser['email'];
    $_SESSION['user_role'] = $newUser['role'];
    $_SESSION['cctv_quota'] = $newUser['cctv_quota'];
    $_SESSION['cctv_used'] = 0;
    $_SESSION['user_city'] = $newUser['city'];

    // Send Welcome & Account Activation Email
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    $portalUrl = $protocol . $host . '/customer/index.php';

    $welcomeHtml = get_email_welcome_registration($name, $email, $planName, $quota, $portalUrl);
    send_loewix_email($email, $name, "Selamat Datang di Loewix CCTV - Akun Berhasil Diaktivasi", $welcomeHtml);

    echo json_encode([
        'success' => true,
        'message' => 'Pendaftaran akun berhasil!',
        'user' => [
            'id' => $newUser['id'],
            'name' => $newUser['name'],
            'email' => $newUser['email'],
            'role' => $newUser['role'],
            'cctv_quota' => $newUser['cctv_quota'],
            'cctv_used' => 0,
            'city' => $newUser['city']
        ]
    ]);
    exit;
}

// FORGOT PASSWORD / REQUEST RESET TOKEN & OTP
if ($action === 'forgot_password') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Masukkan alamat email yang valid!']);
        exit;
    }

    $db = get_db_data();
    $targetUser = null;
    foreach ($db['users'] as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            $targetUser = $user;
            break;
        }
    }

    if (!$targetUser) {
        // Return friendly message for security
        echo json_encode([
            'success' => true, 
            'message' => 'Jika email terdaftar, instruksi reset kata sandi dan kode OTP telah dikirim ke email Anda.'
        ]);
        exit;
    }

    // Generate 6-Digit OTP and Reset Token
    $otpCode = strval(random_int(100000, 999999));
    $resetToken = bin2hex(random_bytes(24));
    $expiresAt = date('Y-m-d H:i:s', time() + (15 * 60)); // 15 mins

    if (!isset($db['password_resets'])) {
        $db['password_resets'] = [];
    }

    $db['password_resets'][] = [
        'user_id' => $targetUser['id'],
        'email' => $targetUser['email'],
        'otp' => $otpCode,
        'token' => $resetToken,
        'expires_at' => $expiresAt,
        'created_at' => date('Y-m-d H:i:s')
    ];
    save_db_data($db);

    // Send Email
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    $resetUrl = $protocol . $host . '/index.php?reset_token=' . $resetToken;

    $forgotHtml = get_email_forgot_password($targetUser['name'], $targetUser['email'], $resetUrl, $otpCode);
    send_loewix_email($targetUser['email'], $targetUser['name'], "Kode OTP & Reset Kata Sandi Akun Loewix CCTV", $forgotHtml);

    echo json_encode([
        'success' => true,
        'message' => 'Kode OTP verifikasi dan tautan reset telah dikirim ke email ' . $email . '!',
        'otp_simulation' => $otpCode // Provided for convenient demo test
    ]);
    exit;
}

// RESET PASSWORD (SUBMIT NEW PASSWORD VIA OTP / TOKEN)
if ($action === 'reset_password') {
    $email = trim($_POST['email'] ?? '');
    $otp = trim($_POST['otp'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');

    if (empty($email) || empty($otp) || empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Email, Kode OTP, dan Password Baru wajib diisi!']);
        exit;
    }

    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password baru minimal 6 karakter!']);
        exit;
    }

    $db = get_db_data();
    $validResetIndex = null;

    if (isset($db['password_resets'])) {
        foreach ($db['password_resets'] as $idx => $reset) {
            if (strtolower($reset['email']) === strtolower($email) && ($reset['otp'] === $otp || $reset['token'] === $otp)) {
                if (strtotime($reset['expires_at']) >= time()) {
                    $validResetIndex = $idx;
                    break;
                }
            }
        }
    }

    if ($validResetIndex === null) {
        echo json_encode(['success' => false, 'message' => 'Kode OTP salah atau telah kadaluarsa!']);
        exit;
    }

    // Update user password
    $updated = false;
    foreach ($db['users'] as &$u) {
        if (strtolower($u['email']) === strtolower($email)) {
            $u['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
            $updated = true;
            break;
        }
    }
    unset($u);

    if ($updated) {
        // Remove used reset entry
        unset($db['password_resets'][$validResetIndex]);
        $db['password_resets'] = array_values($db['password_resets']);
        save_db_data($db);

        echo json_encode([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui! Silakan login dengan kata sandi baru Anda.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui kata sandi. Pengguna tidak ditemukan.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid Action']);

