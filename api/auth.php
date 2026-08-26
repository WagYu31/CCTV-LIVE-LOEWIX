<?php
/**
 * Authentication REST API Endpoint
 * PT. LOEWIX INDONESIA
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

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
        // Count active cameras for this user
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

echo json_encode(['success' => false, 'message' => 'Invalid Action']);
