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
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'name' => 'PT. Jaya Sentosa Enterprise',
                    'email' => 'customer@jayasentosa.com',
                    'password' => password_hash('customer123', PASSWORD_BCRYPT),
                    'role' => 'customer',
                    'cctv_quota' => 10,
                    'phone' => '+62 812-3456-7890',
                    'city' => 'siantar',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ],
            'cameras' => []
        ];
        file_put_contents(DB_FILE, json_encode($defaultData, JSON_PRETTY_PRINT));
        return $defaultData;
    }
    $content = file_get_contents(DB_FILE);
    $data = json_decode($content, true);
    if (!$data) {
        $data = ['users' => [], 'cameras' => []];
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
