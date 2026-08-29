<?php
/**
 * Automated Billing Expiry & Renewal Reminder Cron
 * Loewix Surveillance Cloud
 * PT. LOEWIX INDONESIA
 * 
 * Recommended Cron schedule:
 * 0 8 * * * php /path/to/cron_billing_reminders.php >> /path/to/data/logs/cron_billing.log 2>&1
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/mail.php';

echo "[" . date('Y-m-d H:i:s') . "] STARTING LOEWIX BILLING EXPIRY REMINDER SCAN...\n";

$db = get_db_data();
if (empty($db['subscriptions'])) {
    echo "No active subscriptions found.\n";
    exit(0);
}

$now = time();
$remindersSent = 0;

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$payUrl = $protocol . $host . '/customer/index.php#tab-package';

foreach ($db['subscriptions'] as $sub) {
    if ($sub['status'] !== 'active') {
        continue;
    }

    $expiresTimestamp = strtotime($sub['expires_at']);
    $diffSeconds = $expiresTimestamp - $now;
    $daysLeft = ceil($diffSeconds / 86400);

    // Find corresponding user
    $user = null;
    foreach ($db['users'] as $u) {
        if ((int)$u['id'] === (int)$sub['user_id']) {
            $user = $u;
            break;
        }
    }

    if (!$user) {
        continue;
    }

    // Trigger reminders on 7 days, 3 days, and 1 day remaining
    if ($daysLeft == 7 || $daysLeft == 3 || $daysLeft == 1) {
        echo "Found expiring subscription: User {$user['name']} ({$user['email']}) - {$daysLeft} days remaining.\n";
        
        $reminderHtml = get_email_bill_due_reminder($sub, $user, $daysLeft, $payUrl);
        $subject = "[Peringatan Tagihan] Masa Aktif Langganan CCTV Berakhir {$daysLeft} Hari Lagi ({$sub['plan_name']})";
        
        $sent = send_loewix_email($user['email'], $user['name'], $subject, $reminderHtml);
        if ($sent) {
            $remindersSent++;
            echo "  -> Reminder email successfully dispatched to {$user['email']}.\n";
        }
    }
}

echo "[" . date('Y-m-d H:i:s') . "] FINISHED SCAN. Total Reminders Sent: {$remindersSent}\n";
