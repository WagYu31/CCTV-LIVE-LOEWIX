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
                ]
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
                ]
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
