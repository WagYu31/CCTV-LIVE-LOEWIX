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

foreach ($db['subscriptions'] as &$sub) {
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

    // When subscription is within 7 days of expiry, ensure renewal invoice exists
    if ($daysLeft <= 7 && $daysLeft >= -3) {
        $renewalOrderId = 'INV-LX-RENEW-' . date('Ym', $expiresTimestamp) . '-' . $user['id'];
        
        // Check if invoice already exists
        $invoiceExists = false;
        if (!isset($db['invoices']) || !is_array($db['invoices'])) {
            $db['invoices'] = [];
        }

        foreach ($db['invoices'] as $inv) {
            if ($inv['order_id'] === $renewalOrderId) {
                $invoiceExists = true;
                break;
            }
        }

        if (!$invoiceExists) {
            $baseAmount = (int)($sub['amount'] ?? 299000);
            $taxAmount = (int)round($baseAmount * 0.11);
            $totalAmount = $baseAmount + $taxAmount;

            $existingIds = array_column($db['invoices'], 'id');
            $newInvId = count($existingIds) > 0 ? max($existingIds) + 1 : 1;

            $newInvoice = [
                'id' => $newInvId,
                'order_id' => $renewalOrderId,
                'user_id' => (int)$user['id'],
                'user_name' => $user['name'],
                'user_email' => $user['email'],
                'plan_id' => $sub['plan_id'] ?? 'business_10',
                'plan_name' => ($sub['plan_name'] ?? 'Business Pro Plan') . ' (Perpanjangan)',
                'billing_cycle' => $sub['billing_cycle'] ?? 'monthly',
                'amount' => $baseAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_type' => 'midtrans_gateway',
                'transaction_time' => date('Y-m-d H:i:s'),
                'due_date' => $sub['expires_at']
            ];

            $db['invoices'][] = $newInvoice;
            save_db_data($db);
            echo "  [+] Generated new renewal invoice {$renewalOrderId} for {$user['name']} (Total: Rp " . number_format($totalAmount, 0, ',', '.') . ")\n";
        }

        // Trigger reminders on 7 days, 3 days, 1 day, or day of expiry
        if ($daysLeft == 7 || $daysLeft == 3 || $daysLeft == 1 || $daysLeft == 0) {
            echo "Found expiring subscription: User {$user['name']} ({$user['email']}) - {$daysLeft} days remaining.\n";
            
            $reminderHtml = get_email_bill_due_reminder($sub, $user, $daysLeft, $payUrl);
            $subject = ($daysLeft == 0)
                ? "[Jatuh Tempo Hari Ini] Segera Lakukan Pembayaran Langganan CCTV ({$sub['plan_name']})"
                : "[Peringatan Tagihan] Masa Aktif Langganan CCTV Berakhir {$daysLeft} Hari Lagi ({$sub['plan_name']})";
            
            $sent = send_loewix_email($user['email'], $user['name'], $subject, $reminderHtml);
            if ($sent) {
                $remindersSent++;
                echo "  -> Reminder email successfully dispatched to {$user['email']}.\n";
            }
        }
    }
}

save_db_data($db);
echo "[" . date('Y-m-d H:i:s') . "] FINISHED SCAN. Total Reminders Sent: {$remindersSent}\n";
