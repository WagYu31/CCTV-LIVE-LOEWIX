<?php
// customer/receipt.php - Official Loewix Payment Receipt & Invoice
require_once __DIR__ . '/../config/db.php';

$orderId = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';

$db = function_exists('get_db_data') ? get_db_data() : (file_exists(__DIR__ . '/../data/loewix_db.json') ? json_decode(file_get_contents(__DIR__ . '/../data/loewix_db.json'), true) : []);
$invoices = $db['invoices'] ?? [];
$users = $db['users'] ?? [];
$billingProfiles = $db['billing_profiles'] ?? [];

$targetInvoice = null;
if (!empty($orderId)) {
    foreach ($invoices as $inv) {
        if (isset($inv['order_id']) && strcasecmp($inv['order_id'], $orderId) === 0) {
            $targetInvoice = $inv;
            break;
        }
    }

    if (!$targetInvoice && !empty($db['subscriptions'])) {
        foreach ($db['subscriptions'] as $sub) {
            if ((isset($sub['order_id']) && strcasecmp($sub['order_id'], $orderId) === 0) || (stripos($orderId, $sub['plan_id'] ?? '') !== false)) {
                $base = (float)($sub['amount'] ?? 1490000);
                $tax = round($base * 0.11);
                $targetInvoice = [
                    'order_id' => $orderId,
                    'user_id' => $sub['user_id'] ?? 3,
                    'user_name' => 'Yamaha DDS Jakarta',
                    'user_email' => 'YamahaDDS09@gmail.com',
                    'plan_name' => ($sub['plan_name'] ?? 'Paket CCTV') . ' (' . ($sub['cctv_quota'] ?? 4) . ' CCTV)',
                    'billing_cycle' => $sub['billing_cycle'] ?? 'annual',
                    'amount' => $base,
                    'tax_amount' => $tax,
                    'total_amount' => $base + $tax,
                    'status' => 'settlement',
                    'payment_type' => 'bank_transfer_bca',
                    'transaction_time' => $sub['start_date'] ?? date('Y-m-d H:i:s'),
                    'settlement_time' => $sub['start_date'] ?? date('Y-m-d H:i:s')
                ];
                break;
            }
        }
    }
}

// Fallback to latest invoice if not specifically found
if (!$targetInvoice && !empty($invoices)) {
    $targetInvoice = end($invoices);
}

// Fallback sample data if DB has no invoices
if (!$targetInvoice) {
    $targetInvoice = [
        'order_id' => !empty($orderId) ? $orderId : 'INV-LWX-' . date('Ymd') . '-001',
        'user_name' => 'Yamaha DDS Jakarta',
        'user_email' => 'YamahaDDS09@gmail.com',
        'plan_name' => 'Enterprise Fleet (20 CCTV)',
        'billing_cycle' => 'annual',
        'amount' => 5490000,
        'tax_amount' => 603900,
        'total_amount' => 6093900,
        'status' => 'settlement',
        'payment_type' => 'bank_transfer_bca',
        'transaction_time' => date('Y-m-d H:i:s'),
        'settlement_time' => date('Y-m-d H:i:s')
    ];
}

// Find user and billing profile
$targetUser = null;
$targetProfile = null;

if (isset($targetInvoice['user_id'])) {
    foreach ($users as $u) {
        if ($u['id'] == $targetInvoice['user_id']) {
            $targetUser = $u;
            break;
        }
    }
    foreach ($billingProfiles as $bp) {
        if ($bp['user_id'] == $targetInvoice['user_id']) {
            $targetProfile = $bp;
            break;
        }
    }
}

if (!$targetUser && isset($targetInvoice['user_email'])) {
    foreach ($users as $u) {
        if (strcasecmp($u['email'] ?? '', $targetInvoice['user_email']) === 0) {
            $targetUser = $u;
            break;
        }
    }
}

$companyName = $targetProfile['company_name'] ?? ($targetInvoice['user_name'] ?? ($targetUser['name'] ?? 'BATAGOR BANDUNG'));
$companyEmail = $targetProfile['billing_email'] ?? ($targetInvoice['user_email'] ?? ($targetUser['email'] ?? 'cingire687@gmail.com'));
$companyPhone = $targetProfile['billing_phone'] ?? ($targetUser['phone'] ?? '+62 812-3456-7890');
$companyCity = $targetProfile['billing_address'] ?? ('Kota ' . ($targetUser['city'] ?? 'Bandung') . ', Jawa Barat, Indonesia');

$isSettlement = in_array(strtolower($targetInvoice['status'] ?? ''), ['settlement', 'capture', 'paid', 'success']);
$paymentMethod = strtoupper(str_replace('_', ' ', $targetInvoice['payment_type'] ?? 'Bank Transfer BCA'));
$periodText = ($targetInvoice['billing_cycle'] ?? 'annual') === 'annual' ? '1 Tahun (12 Bulan)' : '1 Bulan';

$baseAmount = (float)($targetInvoice['amount'] ?? 2990000);
$taxAmount = (float)($targetInvoice['tax_amount'] ?? round($baseAmount * 0.11));
$totalAmount = (float)($targetInvoice['total_amount'] ?? ($baseAmount + $taxAmount));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kwitansi Pembayaran Resmi - <?php echo htmlspecialchars($targetInvoice['order_id'] ?? 'Loewix'); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #090e1a;
      color: #1e293b;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding: 30px 15px;
    }
    .action-bar {
      width: 100%;
      max-width: 720px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 18px;
      font-size: 13px;
      font-weight: 700;
      border-radius: 8px;
      text-decoration: none;
      cursor: pointer;
      border: none;
      transition: all 0.2s ease;
    }
    .btn-print {
      background: #0284c7;
      color: #ffffff;
      box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);
    }
    .btn-print:hover {
      background: #0369a1;
      transform: translateY(-1px);
    }
    .btn-close-tab {
      background: rgba(255, 255, 255, 0.1);
      color: #94a3b8;
    }
    .btn-close-tab:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
    }
    .receipt-card {
      width: 100%;
      max-width: 720px;
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
      padding: 36px 40px;
      position: relative;
      overflow: hidden;
    }
    .receipt-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: linear-gradient(90deg, #0284c7, #38bdf8, #10b981);
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #f1f5f9;
      padding-bottom: 20px;
      margin-bottom: 24px;
    }
    .brand-title {
      font-size: 22px;
      font-weight: 800;
      color: #0c1c44;
      letter-spacing: -0.5px;
    }
    .brand-sub {
      font-size: 11.5px;
      font-weight: 700;
      color: #0284c7;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-top: 2px;
    }
    .brand-meta {
      font-size: 11.5px;
      color: #64748b;
      margin-top: 4px;
      line-height: 1.4;
    }
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 800;
      letter-spacing: 0.3px;
    }
    .status-paid {
      background: #dcfce7;
      color: #15803d;
      border: 1px solid #86efac;
    }
    .status-pending {
      background: #fef3c7;
      color: #b45309;
      border: 1px solid #fcd34d;
    }
    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      margin-bottom: 28px;
      font-size: 12.5px;
    }
    .info-label {
      font-size: 11px;
      font-weight: 600;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }
    .info-val-strong {
      font-size: 15px;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 2px;
    }
    .info-val-order {
      font-family: 'JetBrains Mono', monospace;
      font-size: 14px;
      font-weight: 700;
      color: #0284c7;
      margin-bottom: 2px;
    }
    .info-val-sub {
      color: #475569;
      line-height: 1.4;
    }
    .table-items {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
      font-size: 13px;
    }
    .table-items th {
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      border-bottom: 1px solid #cbd5e1;
      padding: 10px 14px;
      text-align: left;
      font-size: 11.5px;
      font-weight: 700;
      color: #475569;
      text-transform: uppercase;
    }
    .table-items td {
      padding: 14px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
    }
    .table-items .item-title {
      font-weight: 700;
      color: #0f172a;
      font-size: 13.5px;
    }
    .table-items .item-desc {
      font-size: 11.5px;
      color: #64748b;
      margin-top: 2px;
    }
    .total-card {
      background: #f0fdf4;
      border: 1.5px solid #86efac;
      border-radius: 12px;
      padding: 16px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 28px;
    }
    .total-label {
      font-size: 13px;
      font-weight: 800;
      color: #166534;
      text-transform: uppercase;
    }
    .total-val {
      font-size: 20px;
      font-weight: 800;
      color: #059669;
    }
    .footer-stamp {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      border-top: 1px dashed #cbd5e1;
      padding-top: 20px;
      margin-top: 10px;
    }
    .legal-note {
      font-size: 11px;
      color: #94a3b8;
      max-width: 400px;
      line-height: 1.5;
    }
    .signature-box {
      text-align: right;
    }
    .signature-title {
      font-size: 11px;
      font-weight: 700;
      color: #475569;
      text-transform: uppercase;
    }
    .signature-seal {
      font-size: 11.5px;
      font-weight: 800;
      color: #0284c7;
      margin-top: 6px;
      letter-spacing: 0.5px;
    }

    @media print {
      body {
        background: #ffffff !important;
        padding: 0 !important;
      }
      .action-bar {
        display: none !important;
      }
      .receipt-card {
        box-shadow: none !important;
        max-width: 100% !important;
        padding: 20px !important;
      }
    }
  </style>
</head>
<body>

  <!-- Top Action Controls -->
  <div class="action-bar">
    <a href="javascript:window.close()" class="btn-action btn-close-tab">
      <i class="fas fa-arrow-left"></i> Kembali / Tutup
    </a>
    <button onclick="window.print()" class="btn-action btn-print">
      <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>
  </div>

  <!-- Main Receipt Card -->
  <div class="receipt-card">
    
    <!-- Header -->
    <div class="header">
      <div>
        <div class="brand-title">PT. LOEWIX INDONESIA</div>
        <div class="brand-sub">Cloud CCTV Surveillance SaaS Platform</div>
        <div class="brand-meta">
          NPWP: 01.999.888.7-012.000<br>
          Gedung Cyber Loewix Tech Center, Jakarta & Bandung<br>
          Website: <a href="https://loewixcctv.com" style="color: #0284c7; text-decoration: none;">www.loewixcctv.com</a>
        </div>
      </div>
      <div style="text-align: right;">
        <span class="status-badge <?php echo $isSettlement ? 'status-paid' : 'status-pending'; ?>">
          <?php if ($isSettlement): ?>
            <i class="fas fa-check-circle"></i> LUNAS (PAID)
          <?php else: ?>
            <i class="fas fa-clock"></i> PENDING
          <?php endif; ?>
        </span>
        <div style="font-size: 11.5px; color: #64748b; margin-top: 6px; font-weight: 600;">
          <?php echo htmlspecialchars($targetInvoice['settlement_time'] ?? ($targetInvoice['transaction_time'] ?? date('Y-m-d H:i:s'))); ?>
        </div>
      </div>
    </div>

    <!-- Customer & Invoice Info -->
    <div class="info-grid">
      <div>
        <div class="info-label">Ditagihkan Kepada (Customer):</div>
        <div class="info-val-strong"><?php echo htmlspecialchars($companyName); ?></div>
        <div class="info-val-sub">Email: <?php echo htmlspecialchars($companyEmail); ?></div>
        <div class="info-val-sub">No. HP: <?php echo htmlspecialchars($companyPhone); ?></div>
        <div class="info-val-sub">Alamat: <?php echo htmlspecialchars($companyCity); ?></div>
      </div>
      <div style="text-align: right;">
        <div class="info-label">Nomor Invoice & Transaksi:</div>
        <div class="info-val-order"><?php echo htmlspecialchars($targetInvoice['order_id']); ?></div>
        <div class="info-val-sub">Metode: <strong><?php echo htmlspecialchars($paymentMethod); ?></strong></div>
        <div class="info-val-sub">Periode Langganan: <strong><?php echo htmlspecialchars($periodText); ?></strong></div>
        <div class="info-val-sub">Payment Gateway: <strong>Midtrans Verified</strong></div>
      </div>
    </div>

    <!-- Item Details Table -->
    <table class="table-items">
      <thead>
        <tr>
          <th>Deskripsi Layanan</th>
          <th style="text-align: center;">Periode</th>
          <th style="text-align: right;">Harga Satuan</th>
          <th style="text-align: right;">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <div class="item-title">Paket Layanan <?php echo htmlspecialchars($targetInvoice['plan_name'] ?? 'Business Pro (10 CCTV)'); ?></div>
            <div class="item-desc">Akses Streaming Multi-Channel CCTV, Enkripsi Cloud Loewix, & MediaMTX Relay Server</div>
          </td>
          <td style="text-align: center; font-weight: 600; color: #334155;"><?php echo htmlspecialchars($periodText); ?></td>
          <td style="text-align: right; color: #475569;">Rp <?php echo number_format($baseAmount, 0, ',', '.'); ?></td>
          <td style="text-align: right; font-weight: 700; color: #0f172a;">Rp <?php echo number_format($baseAmount, 0, ',', '.'); ?></td>
        </tr>
        <tr>
          <td colspan="3" style="text-align: right; color: #64748b; font-size: 12px;">PPN 11% (Pajak Pertambahan Nilai Resmi)</td>
          <td style="text-align: right; color: #64748b; font-size: 12px; font-weight: 600;">Rp <?php echo number_format($taxAmount, 0, ',', '.'); ?></td>
        </tr>
      </tbody>
    </table>

    <!-- Total Payment Highlight -->
    <div class="total-card">
      <div>
        <div class="total-label">Total Pembayaran Resmi</div>
        <div style="font-size: 11.5px; color: #15803d; margin-top: 2px;">Sudah termasuk PPN 11% & Biaya Layanan Cloud</div>
      </div>
      <div class="total-val">
        Rp <?php echo number_format($totalAmount, 0, ',', '.'); ?>
      </div>
    </div>

    <!-- Stamp & Legal Footer -->
    <div class="footer-stamp">
      <div class="legal-note">
        <strong style="color: #475569;">Ketentuan Dokumen:</strong><br>
        Dokumen ini merupakan bukti pembayaran elektronik sah yang diterbitkan otomatis oleh sistem penagihan PT. Loewix Indonesia. Tidak memerlukan tanda tangan basah.
      </div>
      <div class="signature-box">
        <div class="signature-title">Diterbitkan Secara Digital Oleh</div>
        <div class="signature-seal">PT. LOEWIX INDONESIA</div>
        <div style="font-size: 10px; color: #94a3b8; margin-top: 2px;">SYSTEM CERTIFIED RECEIPT</div>
      </div>
    </div>

  </div>

</body>
</html>
