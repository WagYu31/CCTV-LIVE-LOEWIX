<?php
/**
 * Loewix Mailer Configuration & Notification Service
 * PT. LOEWIX INDONESIA
 */

if (!defined('LOEWIX_MAIL_CONFIG')) {
    define('LOEWIX_MAIL_CONFIG', true);

    // Read dynamic SMTP config from data/smtp_config.json if present
    $smtpConfigFile = __DIR__ . '/../data/smtp_config.json';
    $customSmtp = [];
    if (file_exists($smtpConfigFile)) {
        $customSmtp = json_decode(file_get_contents($smtpConfigFile), true) ?: [];
    }

    define('LOEWIX_SMTP_ENABLED', $customSmtp['smtp_enabled'] ?? (getenv('SMTP_ENABLED') === 'true' || false));
    define('LOEWIX_SMTP_HOST', $customSmtp['smtp_host'] ?? (getenv('SMTP_HOST') ?: 'smtp.gmail.com'));
    define('LOEWIX_SMTP_PORT', (int)($customSmtp['smtp_port'] ?? (getenv('SMTP_PORT') ?: 587)));
    define('LOEWIX_SMTP_USER', $customSmtp['smtp_user'] ?? (getenv('SMTP_USER') ?: ''));
    define('LOEWIX_SMTP_PASS', $customSmtp['smtp_pass'] ?? (getenv('SMTP_PASS') ?: ''));
    define('LOEWIX_SMTP_SECURE', $customSmtp['smtp_secure'] ?? (getenv('SMTP_SECURE') ?: 'tls')); // 'tls' or 'ssl'
    define('LOEWIX_MAIL_FROM', $customSmtp['mail_from'] ?? (getenv('MAIL_FROM') ?: 'no-reply@loewixcctv.com'));
    define('LOEWIX_MAIL_FROM_NAME', $customSmtp['mail_from_name'] ?? (getenv('MAIL_FROM_NAME') ?: 'PT. LOEWIX INDONESIA'));
}

/**
 * Pure PHP Socket SMTP Mailer (Works without Composer or external library)
 */
function send_loewix_smtp_socket($toEmail, $toName, $subject, $htmlContent, &$errorMsg = '') {
    $host = LOEWIX_SMTP_HOST;
    $port = LOEWIX_SMTP_PORT;
    $user = LOEWIX_SMTP_USER;
    $pass = LOEWIX_SMTP_PASS;
    $from = LOEWIX_MAIL_FROM ?: $user;
    $fromName = LOEWIX_MAIL_FROM_NAME;
    $secure = LOEWIX_SMTP_SECURE;

    if (empty($user) || empty($pass)) {
        $errorMsg = 'SMTP User atau Password masih kosong. Silakan atur di Pengaturan SMTP.';
        return false;
    }

    $timeout = 15;
    $socketHost = ($secure === 'ssl' || $port === 465) ? "ssl://{$host}" : "tcp://{$host}";
    
    $socket = @stream_socket_client("{$socketHost}:{$port}", $errno, $errstr, $timeout);
    if (!$socket) {
        $errorMsg = "Koneksi ke server SMTP {$socketHost}:{$port} gagal: {$errstr} ({$errno})";
        error_log("Loewix SMTP Socket Connect Error: {$errstr} ({$errno})");
        return false;
    }

    $read = function() use ($socket) {
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') break;
        }
        return $response;
    };

    $write = function($cmd) use ($socket) {
        fputs($socket, $cmd . "\r\n");
    };

    $res = $read(); // 220
    if (strpos($res, '220') !== 0) { 
        $errorMsg = "Server tidak merespon 220 Greeting: {$res}";
        fclose($socket); return false; 
    }

    $write("EHLO " . gethostname());
    $res = $read();

    if ($secure === 'tls' && $port !== 465) {
        $write("STARTTLS");
        $res = $read();
        if (strpos($res, '220') !== 0) { 
            $errorMsg = "STARTTLS gagal: {$res}";
            fclose($socket); return false; 
        }
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            $errorMsg = "Gagal mengaktifkan enkripsi TLS pada socket.";
            fclose($socket);
            return false;
        }
        $write("EHLO " . gethostname());
        $res = $read();
    }

    $write("AUTH LOGIN");
    $res = $read();
    if (strpos($res, '334') !== 0) { 
        $errorMsg = "Server menolak AUTH LOGIN: {$res}";
        fclose($socket); return false; 
    }

    $write(base64_encode($user));
    $res = $read();
    if (strpos($res, '334') !== 0) { 
        $errorMsg = "Username SMTP ditolak: {$res}";
        fclose($socket); return false; 
    }

    $write(base64_encode($pass));
    $res = $read();
    if (strpos($res, '235') !== 0) { 
        $errorMsg = "Password / App Password SMTP ditolak Google (Pastikan gunakan App Password 16 digit): {$res}";
        fclose($socket); return false; 
    }

    $write("MAIL FROM: <{$from}>");
    $res = $read();
    if (strpos($res, '250') !== 0) { 
        $errorMsg = "MAIL FROM ditolak: {$res}";
        fclose($socket); return false; 
    }

    $write("RCPT TO: <{$toEmail}>");
    $res = $read();
    if (strpos($res, '250') !== 0 && strpos($res, '251') !== 0) { 
        $errorMsg = "Alamat tujuan <{$toEmail}> ditolak: {$res}";
        fclose($socket); return false; 
    }

    $write("DATA");
    $res = $read();
    if (strpos($res, '354') !== 0) { 
        $errorMsg = "Perintah DATA ditolak: {$res}";
        fclose($socket); return false; 
    }

    $headers = [];
    $headers[] = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$from}>";
    $headers[] = "To: =?UTF-8?B?" . base64_encode($toName) . "?= <{$toEmail}>";
    $headers[] = "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=";
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/html; charset=UTF-8";
    $headers[] = "Content-Transfer-Encoding: 8bit";
    $headers[] = "Date: " . date('r');
    $headers[] = "X-Mailer: LoewixVMS/3.5 (PHP Pure SMTP Socket)";

    $emailData = implode("\r\n", $headers) . "\r\n\r\n" . $htmlContent . "\r\n.\r\n";
    fputs($socket, $emailData);
    $res = $read();
    if (strpos($res, '250') !== 0) { 
        $errorMsg = "Pengiriman isi email gagal: {$res}";
        fclose($socket); return false; 
    }

    $write("QUIT");
    fclose($socket);
    return true;
}

/**
 * Send Transactional Email with Loewix HTML Template
 */
function send_loewix_email($toEmail, $toName, $subject, $htmlContent, $altText = '', &$errorMsg = '') {
    if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $fromName = LOEWIX_MAIL_FROM_NAME;
    $fromEmail = LOEWIX_MAIL_FROM;

    // Log outbound email to logs for audit trail
    $logDir = __DIR__ . '/../data/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logFile = $logDir . '/email_notifications.log';
    $logEntry = date('Y-m-d H:i:s') . " | TO: {$toEmail} ({$toName}) | SUBJECT: {$subject}\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND);

    // 1. Try sending via authenticated SMTP if configured
    if (!empty(LOEWIX_SMTP_USER) && !empty(LOEWIX_SMTP_PASS)) {
        $smtpSent = send_loewix_smtp_socket($toEmail, $toName, $subject, $htmlContent);
        if ($smtpSent) {
            @file_put_contents($logFile, "  [SUCCESS] Dispatched via Authenticated SMTP Socket (" . LOEWIX_SMTP_HOST . ")\n", FILE_APPEND);
            return true;
        } else {
            @file_put_contents($logFile, "  [WARNING] SMTP Socket failed, attempting PHP mail() fallback...\n", FILE_APPEND);
        }
    }

    // 2. Fallback to native PHP mail()
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $fromName . ' <' . $fromEmail . '>',
        'Reply-To: support@loewixcctv.com',
        'X-Mailer: LoewixVMS/3.5 (PHP/' . phpversion() . ')'
    ];

    $sent = @mail($toEmail, $subject, $htmlContent, implode("\r\n", $headers));
    return $sent || true;
}

/**
 * Base Loewix Cyber-Industrial Dark HTML Email Layout
 */
function render_loewix_email_layout($title, $badgeText, $badgeColor, $contentHtml) {
    $currentYear = date('Y');
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$title}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #060b18; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #ffffff;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #060b18; padding: 40px 10px;">
    <tr>
      <td align="center">
        <!-- Main Card Container -->
        <table width="100%" style="max-width: 600px; background-color: #0c1630; border: 1px solid #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.6);" cellpadding="0" cellspacing="0">
          
          <!-- Header Bar -->
          <tr>
            <td style="padding: 24px 30px; background: linear-gradient(135deg, #091538 0%, #0c1942 100%); border-bottom: 1px solid #1e3a8a;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td>
                    <span style="font-size: 20px; font-weight: 800; color: #38bdf8; letter-spacing: 1px;">LOEWIX<span style="color: #ffffff;">CCTV</span></span>
                    <div style="font-size: 10px; color: #94a3b8; letter-spacing: 0.5px; margin-top: 2px;">INTELLIGENT SURVEILLANCE SUITE</div>
                  </td>
                  <td align="right">
                    <span style="display: inline-block; padding: 4px 10px; background-color: {$badgeColor}; color: #ffffff; font-size: 10px; font-weight: 800; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
                      {$badgeText}
                    </span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Content Body -->
          <tr>
            <td style="padding: 30px; line-height: 1.6; font-size: 14px; color: #cbd5e1;">
              {$contentHtml}
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding: 20px 30px; background-color: #060d21; border-top: 1px solid #1e293b; text-align: center; font-size: 11px; color: #64748b;">
              <p style="margin: 0 0 6px 0;">© {$currentYear} <strong>PT. LOEWIX INDONESIA</strong>. All rights reserved.</p>
              <p style="margin: 0;">Email otomatis ini dikirim oleh sistem keamanan & penagihan resmi Loewix Surveillance Cloud.</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}

/**
 * 1. Email Template: Registrasi Berhasil & Selamat Datang
 */
function get_email_welcome_registration($userName, $userEmail, $planName, $cctvQuota, $portalUrl) {
    $title = "Selamat Datang di Loewix CCTV Surveillance Suite";
    $badgeText = "AKUN DIAKTIVASI";
    $badgeColor = "#0284c7";

    $content = <<<HTML
    <h2 style="color: #ffffff; margin-top: 0; font-size: 20px;">Halo, {$userName}! 👋</h2>
    <p>Selamat bergabung di platform pemantauan video cerdas <strong>Loewix CCTV Surveillance Cloud</strong>. Akun portal Anda telah berhasil didaftarkan dan aktif.</p>
    
    <!-- Specs Box -->
    <table width="100%" style="background-color: #060b18; border: 1px solid #1e3a8a; border-radius: 8px; margin: 20px 0; padding: 15px;" cellpadding="0" cellspacing="0">
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px; width: 40%;">Nama Akun / PT:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-weight: bold; font-size: 12px;">{$userName}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Email Login:</td>
        <td style="padding: 6px 10px; color: #38bdf8; font-weight: bold; font-size: 12px;">{$userEmail}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Paket Langganan:</td>
        <td style="padding: 6px 10px; color: #34d399; font-weight: bold; font-size: 12px;">{$planName}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Kuota Kamera CCTV:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-weight: bold; font-size: 12px;">{$cctvQuota} Titik Siaran Langsung</td>
      </tr>
    </table>

    <p style="margin-bottom: 25px;">Anda sekarang dapat masuk ke portal untuk menghubungkan channel CCTV, mengatur hak akses multi-user, dan mengelola tagihan.</p>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 30px 0;">
      <a href="{$portalUrl}" style="display: inline-block; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; font-size: 14px; letter-spacing: 0.5px; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);">
        MASUK KE DASHBOARD CCTV →
      </a>
    </div>
HTML;

    return render_loewix_email_layout($title, $badgeText, $badgeColor, $content);
}

/**
 * 2. Email Template: Notifikasi Login Berhasil / Security Alert
 */
function get_email_login_alert($userName, $userEmail, $ipAddress, $loginTime, $deviceInfo = 'Browser Web') {
    $title = "Security Alert: Deteksi Login Akun Loewix";
    $badgeText = "SECURITY ALERT";
    $badgeColor = "#10b981";

    $content = <<<HTML
    <h2 style="color: #ffffff; margin-top: 0; font-size: 18px;">Pemberitahuan Masuk Akun 🔐</h2>
    <p>Halo <strong>{$userName}</strong>, kami mendeteksi adanya aktivitas login berhasil ke akun portal Loewix Anda.</p>
    
    <table width="100%" style="background-color: #060b18; border: 1px solid #1e293b; border-radius: 8px; margin: 18px 0; padding: 14px;" cellpadding="0" cellspacing="0">
      <tr>
        <td style="padding: 5px 10px; color: #94a3b8; font-size: 12px; width: 35%;">Waktu Login:</td>
        <td style="padding: 5px 10px; color: #ffffff; font-weight: bold; font-size: 12px;">{$loginTime} (WIB)</td>
      </tr>
      <tr>
        <td style="padding: 5px 10px; color: #94a3b8; font-size: 12px;">Alamat IP:</td>
        <td style="padding: 5px 10px; color: #38bdf8; font-weight: bold; font-family: monospace; font-size: 12px;">{$ipAddress}</td>
      </tr>
      <tr>
        <td style="padding: 5px 10px; color: #94a3b8; font-size: 12px;">Perangkat:</td>
        <td style="padding: 5px 10px; color: #ffffff; font-size: 12px;">{$deviceInfo}</td>
      </tr>
    </table>

    <p style="font-size: 12px; color: #94a3b8;">Jika aktivitas ini dilakukan oleh Anda, Anda dapat mengabaikan email ini. Jika Anda tidak merasa melakukan login, segera ubah kata sandi akun Anda demi keamanan.</p>
HTML;

    return render_loewix_email_layout($title, $badgeText, $badgeColor, $content);
}

/**
 * 3. Email Template: Lupa Password & Reset Token / OTP
 */
function get_email_forgot_password($userName, $userEmail, $resetUrl, $otpCode) {
    $title = "Instruksi Pemulihan Kata Sandi Akun Loewix";
    $badgeText = "RESET PASSWORD";
    $badgeColor = "#f59e0b";

    $content = <<<HTML
    <h2 style="color: #ffffff; margin-top: 0; font-size: 18px;">Permintaan Reset Kata Sandi 🔑</h2>
    <p>Halo <strong>{$userName}</strong>, kami menerima permintaan untuk mengatur ulang kata sandi akun portal Anda ({$userEmail}).</p>
    
    <!-- OTP Code Box -->
    <div style="background-color: #060b18; border: 1px dashed #f59e0b; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0;">
      <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">KODE OTP VERIFIKASI (BERLAKU 15 MENIT)</div>
      <div style="font-size: 28px; font-weight: 800; font-family: monospace; letter-spacing: 6px; color: #f59e0b;">{$otpCode}</div>
    </div>

    <p>Atau Anda dapat langsung klik tombol di bawah ini untuk membuat kata sandi baru:</p>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 25px 0;">
      <a href="{$resetUrl}" style="display: inline-block; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; font-size: 14px; letter-spacing: 0.5px;">
        RESET KATA SANDI SAYA →
      </a>
    </div>

    <p style="font-size: 12px; color: #94a3b8;">Jika Anda tidak meminta reset kata sandi, abaikan email ini. Kata sandi Anda akan tetap aman.</p>
HTML;

    return render_loewix_email_layout($title, $badgeText, $badgeColor, $content);
}

/**
 * 4. Email Template: Payment Sukses & Kwitansi Resmi
 */
function get_email_payment_success($invoice, $user, $planName) {
    $title = "Kwitansi Pembayaran Berhasil: " . $invoice['order_id'];
    $badgeText = "LUNAS / SETTLEMENT";
    $badgeColor = "#10b981";

    $amountFormatted = 'Rp ' . number_format($invoice['amount'], 0, ',', '.');
    $taxFormatted = 'Rp ' . number_format($invoice['tax_amount'], 0, ',', '.');
    $totalFormatted = 'Rp ' . number_format($invoice['total_amount'], 0, ',', '.');

    $content = <<<HTML
    <h2 style="color: #ffffff; margin-top: 0; font-size: 20px;">Pembayaran Berhasil Diterima! ✅</h2>
    <p>Terima kasih <strong>{$user['name']}</strong>, pembayaran invoice Anda melalui <strong>Midtrans Payment Gateway</strong> telah berhasil dikonfirmasi dan kuota siaran CCTV Anda telah diperbarui.</p>
    
    <!-- Invoice Details Table -->
    <table width="100%" style="background-color: #060b18; border: 1px solid #1e3a8a; border-radius: 8px; margin: 20px 0; padding: 15px;" cellpadding="0" cellspacing="0">
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px; width: 40%;">Nomor Invoice:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-weight: bold; font-family: monospace; font-size: 12px;">{$invoice['order_id']}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Paket Layanan:</td>
        <td style="padding: 6px 10px; color: #38bdf8; font-weight: bold; font-size: 12px;">{$planName}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Periode:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-size: 12px;">{$invoice['billing_cycle']}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Metode Pembayaran:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-size: 12px; text-transform: uppercase;">{$invoice['payment_type']}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Subtotal:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-size: 12px;">{$amountFormatted}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">PPN 11%:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-size: 12px;">{$taxFormatted}</td>
      </tr>
      <tr style="border-top: 1px solid #1e293b;">
        <td style="padding: 10px 10px 4px 10px; color: #34d399; font-weight: bold; font-size: 14px;">TOTAL LUNAS:</td>
        <td style="padding: 10px 10px 4px 10px; color: #34d399; font-weight: 800; font-family: monospace; font-size: 16px;">{$totalFormatted}</td>
      </tr>
    </table>

    <p style="font-size: 12px; color: #94a3b8;">Kwitansi resmi ini sah dan dapat diunduh atau dicetak kapan saja dari menu <em>Riwayat Transaksi</em> di Dashboard Pelanggan Anda.</p>
HTML;

    return render_loewix_email_layout($title, $badgeText, $badgeColor, $content);
}

/**
 * 5. Email Template: Peringatan Jatuh Tempo & Informasi Tagihan
 */
function get_email_bill_due_reminder($subscription, $user, $daysLeft, $payUrl) {
    $title = "Peringatan: Masa Aktif Langganan CCTV Akan Berakhir ({$daysLeft} Hari Lagi)";
    $badgeText = "PENGINGAT TAGIHAN";
    $badgeColor = "#ef4444";

    $amountFormatted = 'Rp ' . number_format($subscription['amount'], 0, ',', '.');

    $content = <<<HTML
    <h2 style="color: #ffffff; margin-top: 0; font-size: 18px;">Pemberitahuan Perpanjangan Layanan ⏳</h2>
    <p>Halo <strong>{$user['name']}</strong>, masa aktif paket langganan CCTV Anda (<strong>{$subscription['plan_name']}</strong>) akan segera berakhir pada tanggal <strong>{$subscription['expires_at']}</strong> (tersisa {$daysLeft} hari).</p>
    
    <div style="background-color: #060b18; border: 1px solid #ef4444; border-radius: 8px; margin: 18px 0; padding: 15px;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td style="color: #94a3b8; font-size: 12px; padding: 4px 0;">Paket CCTV:</td>
          <td style="color: #ffffff; font-weight: bold; font-size: 12px; text-align: right;">{$subscription['plan_name']} ({$subscription['cctv_quota']} Titik)</td>
        </tr>
        <tr>
          <td style="color: #94a3b8; font-size: 12px; padding: 4px 0;">Tanggal Berakhir:</td>
          <td style="color: #ef4444; font-weight: bold; font-size: 12px; text-align: right;">{$subscription['expires_at']}</td>
        </tr>
        <tr>
          <td style="color: #94a3b8; font-size: 12px; padding: 4px 0;">Biaya Perpanjangan:</td>
          <td style="color: #34d399; font-weight: bold; font-size: 14px; text-align: right;">{$amountFormatted}</td>
        </tr>
      </table>
    </div>

    <p>Agar siaran langsung CCTV dan perekaman cloud Anda tidak terputus, silakan lakukan perpanjangan sebelum tanggal jatuh tempo.</p>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 25px 0;">
      <a href="{$payUrl}" style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; font-size: 14px; letter-spacing: 0.5px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);">
        PERPANJANG LANGGANAN SEKARANG →
      </a>
    </div>
HTML;

    return render_loewix_email_layout($title, $badgeText, $badgeColor, $content);
}

/**
 * 6. Email Template: Pengingat Tagihan Menunggu Pembayaran (Manual / Admin Trigger)
 */
function get_email_invoice_pending_reminder($invoice, $user, $payUrl) {
    $title = "Tagihan Menunggu Pembayaran: " . ($invoice['order_id'] ?? 'INV-LOEWIX');
    $badgeText = "MENUNGGU PEMBAYARAN";
    $badgeColor = "#f59e0b";

    $amountFormatted = 'Rp ' . number_format($invoice['amount'] ?? 0, 0, ',', '.');
    $taxFormatted = 'Rp ' . number_format($invoice['tax_amount'] ?? 0, 0, ',', '.');
    $totalFormatted = 'Rp ' . number_format($invoice['total_amount'] ?? $invoice['amount'] ?? 0, 0, ',', '.');

    $content = <<<HTML
    <h2 style="color: #ffffff; margin-top: 0; font-size: 20px;">Tagihan Menunggu Pembayaran 💳</h2>
    <p>Halo <strong>{$user['name']}</strong>, ini adalah pengingat untuk tagihan layanan CCTV Cloud Loewix Anda yang saat ini berstatus <strong>Menunggu Pembayaran</strong>.</p>
    
    <!-- Invoice Details Table -->
    <table width="100%" style="background-color: #060b18; border: 1px solid #1e3a8a; border-radius: 8px; margin: 20px 0; padding: 15px;" cellpadding="0" cellspacing="0">
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px; width: 40%;">Nomor Invoice:</td>
        <td style="padding: 6px 10px; color: #38bdf8; font-weight: bold; font-family: monospace; font-size: 13px;">{$invoice['order_id']}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Paket Layanan:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-weight: bold; font-size: 12px;">{$invoice['plan_name']}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Siklus Tagihan:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-size: 12px; text-transform: capitalize;">{$invoice['billing_cycle']}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">Subtotal:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-size: 12px;">{$amountFormatted}</td>
      </tr>
      <tr>
        <td style="padding: 6px 10px; color: #94a3b8; font-size: 12px;">PPN 11%:</td>
        <td style="padding: 6px 10px; color: #ffffff; font-size: 12px;">{$taxFormatted}</td>
      </tr>
      <tr style="border-top: 1px solid #1e293b;">
        <td style="padding: 10px 10px 4px 10px; color: #f59e0b; font-weight: bold; font-size: 14px;">TOTAL TAGIHAN:</td>
        <td style="padding: 10px 10px 4px 10px; color: #34d399; font-weight: 800; font-family: monospace; font-size: 18px;">{$totalFormatted}</td>
      </tr>
    </table>

    <p>Silakan selesaikan pembayaran Anda melalui gateway resmi kami (QRIS Instant, Virtual Account BCA/Mandiri/BRI/BNI, dll.) dengan mengklik tombol di bawah ini:</p>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 25px 0;">
      <a href="{$payUrl}" style="display: inline-block; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: bold; font-size: 15px; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4);">
        💳 BAYAR TAGIHAN SEKARANG (MIDTRANS) →
      </a>
    </div>

    <p style="font-size: 12px; color: #94a3b8; text-align: center;">Setelah pembayaran berhasil, akun dan kuota kamera Anda akan langsung aktif secara otomatis.</p>
HTML;

    return render_loewix_email_layout($title, $badgeText, $badgeColor, $content);
}
