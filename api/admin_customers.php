<?php
/**
 * Super Admin Customer & Quota Management API
 * PT. LOEWIX INDONESIA
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = get_db_data();

if ($method === 'GET') {
    // List all customers with camera usage count
    $customers = [];
    foreach ($db['users'] as $u) {
        if ($u['role'] === 'customer') {
            // Calculate active camera count for customer
            $activeCams = 0;
            foreach ($db['cameras'] as $c) {
                if ($c['user_id'] == $u['id']) {
                    $activeCams++;
                }
            }
            $customers[] = [
                'id' => $u['id'],
                'name' => $u['name'],
                'email' => $u['email'],
                'phone' => $u['phone'] ?? '-',
                'city' => $u['city'] ?? 'siantar',
                'cctv_quota' => (int) $u['cctv_quota'],
                'cctv_used' => $activeCams,
                'status' => $u['status'],
                'created_at' => $u['created_at']
            ];
        }
    }
    echo json_encode(['success' => true, 'customers' => $customers]);
    exit;
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $quota = (int) ($_POST['cctv_quota'] ?? 10);
        $phone = trim($_POST['phone'] ?? '');
        $city = trim($_POST['city'] ?? 'siantar');

        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Nama, Email, dan Password wajib diisi!']);
            exit;
        }

        // Check duplicate email
        foreach ($db['users'] as $u) {
            if (strtolower($u['email']) === strtolower($email)) {
                echo json_encode(['success' => false, 'message' => 'Email ini sudah terdaftar!']);
                exit;
            }
        }

        $newId = count($db['users']) > 0 ? max(array_column($db['users'], 'id')) + 1 : 1;
        $newUser = [
            'id' => $newId,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'customer',  
            'cctv_quota' => max(1, $quota),
            'phone' => $phone,
            'city' => $city,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $db['users'][] = $newUser;
        save_db_data($db);

        echo json_encode(['success' => true, 'message' => 'Customer baru berhasil ditambahkan dengan Kuota ' . $newUser['cctv_quota'] . ' CCTV!', 'customer' => $newUser]);
        exit;
    }

    if ($action === 'update_quota') {
        $id = (int) ($_POST['id'] ?? 0);
        $newQuota = (int) ($_POST['cctv_quota'] ?? 10);

        foreach ($db['users'] as &$u) {
            if ($u['id'] === $id && $u['role'] === 'customer') {
                $u['cctv_quota'] = max(1, $newQuota);
                save_db_data($db);
                echo json_encode(['success' => true, 'message' => 'Kuota Customer ' . $u['name'] . ' berhasil diperbarui menjadi ' . $u['cctv_quota'] . ' CCTV!']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Customer tidak ditemukan!']);
        exit;
    }

    if ($action === 'toggle_status') {
        $id = (int) ($_POST['id'] ?? 0);

        foreach ($db['users'] as &$u) {
            if ($u['id'] === $id && $u['role'] === 'customer') {
                $u['status'] = ($u['status'] === 'active') ? 'suspended' : 'active';
                save_db_data($db);
                echo json_encode(['success' => true, 'message' => 'Status Customer ' . $u['name'] . ' diubah menjadi ' . strtoupper($u['status']) . '!']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Customer tidak ditemukan!']);
        exit;
    }

    if ($action === 'update_customer') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $city = trim($_POST['city'] ?? 'siantar');

        if (empty($name) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Nama dan Email wajib diisi!']);
            exit;
        }

        foreach ($db['users'] as &$u) {
            if ($u['id'] === $id && $u['role'] === 'customer') {
                $u['name'] = $name;
                $u['email'] = $email;
                $u['phone'] = $phone;
                $u['city'] = $city;
                save_db_data($db);
                echo json_encode(['success' => true, 'message' => 'Data Customer ' . $name . ' berhasil diperbarui!']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Customer tidak ditemukan!']);
        exit;
    }

    if ($action === 'reset_password') {
        $id = (int) ($_POST['id'] ?? 0);
        $newPassword = trim($_POST['password'] ?? '');

        if (empty($newPassword)) {
            echo json_encode(['success' => false, 'message' => 'Password baru tidak boleh kosong!']);
            exit;
        }

        foreach ($db['users'] as &$u) {
            if ($u['id'] === $id && $u['role'] === 'customer') {
                $u['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
                save_db_data($db);
                echo json_encode(['success' => true, 'message' => 'Password Customer ' . $u['name'] . ' berhasil direset!']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Customer tidak ditemukan!']);
        exit;
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $newUsers = [];
        $deletedName = '';

        foreach ($db['users'] as $u) {
            if ($u['id'] === $id && $u['role'] === 'customer') {
                $deletedName = $u['name'];
            } else {
                $newUsers[] = $u;
            }
        }

        if ($deletedName) {
            $db['users'] = $newUsers;

            // Also delete all cameras associated with this customer
            $newCameras = [];
            $deletedCamCount = 0;
            foreach ($db['cameras'] as $c) {
                if ((int)($c['user_id'] ?? 0) === $id) {
                    $deletedCamCount++;
                } else {
                    $newCameras[] = $c;
                }
            }
            $db['cameras'] = $newCameras;
            save_db_data($db);

            $camInfo = $deletedCamCount > 0 ? " beserta {$deletedCamCount} channel kameranya" : "";
            echo json_encode(['success' => true, 'message' => "Akun Customer '{$deletedName}'{$camInfo} berhasil dihapus permanen!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Customer tidak ditemukan!']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid Request']);
