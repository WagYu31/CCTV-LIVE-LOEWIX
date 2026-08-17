<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Control Center - PT. LOEWIX INDONESIA</title>
  <link rel="stylesheet" href="../assets/bootstarp/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <style>
    :root {
      --bg-dark: #070b19;
      --panel-bg: rgba(13, 27, 62, 0.75);
      --border-color: rgba(255, 255, 255, 0.12);
      --primary-cyan: #00d2ff;
      --accent-gold: #f59e0b;
      --text-main: #ffffff;
      --text-muted: #94a3b8;
    }
    
    body {
      background-color: var(--bg-dark);
      background-image: radial-gradient(circle at 50% 0%, #0d2352 0%, #070b19 80%);
      font-family: 'Outfit', sans-serif;
      color: var(--text-main);
      min-height: 100vh;
      padding-bottom: 50px;
    }

    /* Floating Glass Header */
    .admin-navbar {
      background: rgba(13, 27, 62, 0.85);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border-color);
      padding: 14px 0;
      margin-bottom: 30px;
      position: sticky;
      top: 0;
      z-index: 1030;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    /* Metric Cards Taste-Skill */
    .admin-card {
      background: var(--panel-bg);
      border: 1px solid var(--border-color);
      border-radius: 18px;
      padding: 24px;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
      margin-bottom: 24px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .admin-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #00d2ff, #0066ff);
      opacity: 0.8;
    }

    .admin-card:hover {
      transform: translateY(-4px);
      border-color: rgba(0, 210, 255, 0.3);
      box-shadow: 0 20px 40px rgba(0, 210, 255, 0.15);
    }

    .metric-value {
      font-size: 2.5rem;
      font-weight: 800;
      color: var(--primary-cyan);
      line-height: 1;
      margin-top: 10px;
      letter-spacing: -1px;
    }

    /* Search & Filter Toolbar */
    .filter-toolbar {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 14px;
      padding: 14px;
      margin-bottom: 24px;
    }

    .search-input {
      background: rgba(255, 255, 255, 0.08) !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      color: #fff !important;
      border-radius: 25px !important;
      padding-left: 40px !important;
      height: 42px !important;
      font-size: 14px !important;
    }

    .search-input:focus {
      background: rgba(255, 255, 255, 0.12) !important;
      border-color: var(--primary-cyan) !important;
      box-shadow: 0 0 12px rgba(0, 210, 255, 0.3) !important;
    }

    /* Table Taste-Skill */
    .table-dark-custom {
      color: #fff;
      margin-bottom: 0;
    }

    .table-dark-custom th {
      border-top: none;
      border-bottom: 2px solid rgba(255,255,255,0.15);
      color: var(--primary-cyan);
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      padding: 16px;
      background: rgba(0, 0, 0, 0.2);
    }

    .table-dark-custom td {
      border-top: 1px solid rgba(255,255,255,0.06);
      padding: 16px;
      vertical-align: middle;
      font-size: 14px;
    }

    .table-dark-custom tr:hover {
      background: rgba(0, 210, 255, 0.05);
    }

    /* Action Buttons */
    .action-btn-group {
      display: flex;
      gap: 8px;
    }

    .act-btn {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid transparent;
      transition: all 0.2s ease;
      cursor: pointer;
      color: #fff;
    }

    .act-btn-edit { background: rgba(0, 210, 255, 0.15); border-color: rgba(0, 210, 255, 0.3); color: #00d2ff; }
    .act-btn-edit:hover { background: #00d2ff; color: #000; box-shadow: 0 0 15px rgba(0, 210, 255, 0.5); }

    .act-btn-quota { background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.3); color: #f59e0b; }
    .act-btn-quota:hover { background: #f59e0b; color: #000; box-shadow: 0 0 15px rgba(245, 158, 11, 0.5); }

    .act-btn-reset { background: rgba(168, 85, 247, 0.15); border-color: rgba(168, 85, 247, 0.3); color: #c084fc; }
    .act-btn-reset:hover { background: #a855f7; color: #fff; box-shadow: 0 0 15px rgba(168, 85, 247, 0.5); }

    .act-btn-cctv { background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); color: #10b981; }
    .act-btn-cctv:hover { background: #10b981; color: #fff; box-shadow: 0 0 15px rgba(16, 185, 129, 0.5); }

    .act-btn-status { background: rgba(59, 130, 246, 0.15); border-color: rgba(59, 130, 246, 0.3); color: #60a5fa; }
    .act-btn-status:hover { background: #3b82f6; color: #fff; box-shadow: 0 0 15px rgba(59, 130, 246, 0.5); }

    .act-btn-delete { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #f87171; }
    .act-btn-delete:hover { background: #ef4444; color: #fff; box-shadow: 0 0 15px rgba(239, 68, 68, 0.5); }

    /* Custom Badges */
    .status-badge-active {
      background: rgba(16, 185, 129, 0.15);
      color: #10b981;
      border: 1px solid rgba(16, 185, 129, 0.3);
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 700;
      font-size: 11px;
      letter-spacing: 0.5px;
    }

    .status-badge-inactive {
      background: rgba(239, 68, 68, 0.15);
      color: #f87171;
      border: 1px solid rgba(239, 68, 68, 0.3);
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 700;
      font-size: 11px;
      letter-spacing: 0.5px;
    }

    .city-badge {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 12px;
      padding: 4px 10px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      color: #cbd5e1;
    }

    .btn-gold {
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      color: #000;
      font-weight: 800;
      border: none;
      border-radius: 25px;
      padding: 10px 24px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .btn-gold:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5);
      color: #000;
    }

    .progress-bar-custom {
      height: 8px;
      border-radius: 4px;
      background: rgba(255,255,255,0.1);
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #00d2ff, #0066ff);
      border-radius: 4px;
    }

    .modal-content-dark {
      background: #0d1934;
      border: 1px solid var(--border-color);
      color: #fff;
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6);
    }

    .modal-header-dark { border-bottom: 1px solid rgba(255,255,255,0.1); padding: 18px 24px; }
    .modal-footer-dark { border-top: 1px solid rgba(255,255,255,0.1); padding: 16px 24px; }

    .form-control-dark {
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.2);
      color: #fff !important;
      border-radius: 10px;
      height: 44px;
    }

    .form-control-dark:focus {
      background: rgba(255,255,255,0.12);
      border-color: var(--primary-cyan);
      box-shadow: 0 0 12px rgba(0,210,255,0.3);
    }

    /* Connection Mode Switcher Tabs */
    .conn-mode-tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      background: rgba(0, 0, 0, 0.35);
      padding: 6px;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .conn-tab-btn {
      flex: 1;
      padding: 10px 14px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 13px;
      text-align: center;
      cursor: pointer;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid transparent;
      background: transparent;
      color: #94a3b8;
      outline: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .conn-tab-btn:hover {
      color: #fff;
      background: rgba(255, 255, 255, 0.05);
    }

    .conn-tab-btn.active {
      background: linear-gradient(135deg, rgba(0, 210, 255, 0.25), rgba(0, 102, 255, 0.25));
      border-color: #00d2ff;
      color: #ffffff;
      box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
    }

    /* QR Code Scanner Box */
    .qr-scanner-box {
      position: relative;
      width: 100%;
      max-width: 380px;
      height: 280px;
      margin: 0 auto 16px;
      border-radius: 16px;
      overflow: hidden;
      background: #000;
      border: 2px solid #00d2ff;
      box-shadow: 0 0 30px rgba(0, 210, 255, 0.35);
    }

    .qr-scanner-laser {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, transparent, #00d2ff, #10b981, transparent);
      box-shadow: 0 0 15px #00d2ff;
      animation: qrScanLaser 2.2s linear infinite;
      z-index: 10;
      pointer-events: none;
    }

    @keyframes qrScanLaser {
      0% { top: 6%; opacity: 0.8; }
      50% { top: 92%; opacity: 1; }
      100% { top: 6%; opacity: 0.8; }
    }

    /* Modal Form Pseudo Checkbox Fix Override */
    .modal label::before, .modal label::after, .form-group label::before, .form-group label::after {
      display: none !important;
      content: none !important;
      border: none !important;
    }

    /* ISO Responsive Breakpoints */
    @media (max-width: 991px) {
      .metric-value { font-size: 2rem; }
      .admin-navbar { padding: 10px 0; }
    }

    @media (max-width: 768px) {
      .conn-mode-tabs { flex-direction: column; gap: 6px; }
      .modal-body { padding: 16px !important; }
      .qr-scanner-box { height: 230px; }
    }

    @media (max-width: 575px) {
      .table-dark-custom th, .table-dark-custom td { padding: 10px 8px; font-size: 12px; }
      .action-btn-group { gap: 4px; }
      .act-btn { width: 32px; height: 32px; font-size: 11px; }
      .btn-gold { padding: 8px 16px; font-size: 13px; }
    }
  </style>
</head>
<body>

  <!-- Super Admin Header Navbar -->
  <nav class="admin-navbar">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <a href="../index.html">
          <img src="../assets/image/logo-loewix-white.png" alt="Loewix Logo" height="38" style="filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3));">
        </a>
        <span class="badge ml-3" style="background: rgba(0, 210, 255, 0.15); color: #00d2ff; border: 1px solid rgba(0, 210, 255, 0.3); padding: 6px 14px; border-radius: 20px; font-weight: 700;">
          <i class="fas fa-user-shield mr-1"></i> SUPER ADMIN CONTROL CENTER
        </span>
      </div>
      <div>
        <a href="../index.html" class="btn btn-outline-light btn-sm mr-2" style="border-radius: 20px;">
          <i class="fas fa-desktop mr-1"></i> Portal Utama
        </a>
        <button class="btn btn-danger btn-sm" onclick="logoutAdmin()" style="border-radius: 20px;">
          <i class="fas fa-sign-out-alt mr-1"></i> Logout
        </button>
      </div>
    </div>
  </nav>

  <div class="container">
    
    <!-- Top Metrics Overview -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-12">
        <div class="admin-card">
          <div class="text-muted font-weight-bold" style="font-size: 13px;"><i class="fas fa-building text-info mr-2"></i> TOTAL CUSTOMER</div>
          <div class="metric-value" id="metric-total-customers">0</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="admin-card">
          <div class="text-muted font-weight-bold" style="font-size: 13px;"><i class="fas fa-video text-warning mr-2"></i> TOTAL CHANNEL CCTV</div>
          <div class="metric-value" id="metric-total-cameras">0</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="admin-card">
          <div class="text-muted font-weight-bold" style="font-size: 13px;"><i class="fas fa-layer-group text-success mr-2"></i> ALOKASI KUOTA</div>
          <div class="metric-value" id="metric-total-quota">0</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="admin-card">
          <div class="text-muted font-weight-bold" style="font-size: 13px;"><i class="fas fa-server text-primary mr-2"></i> SERVER MEDIAMTX</div>
          <div class="metric-value" style="color: #10b981; font-size: 1.6rem; margin-top: 14px;">
            <i class="fas fa-check-circle mr-1"></i> ONLINE 99.9%
          </div>
        </div>
      </div>
    </div>

    <!-- Customer Quota Management Section -->
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
          <h4 class="font-weight-bold mb-1"><i class="fas fa-users-cog text-info mr-2"></i> Kelola Customer & Alokasi Kuota CCTV</h4>
          <p class="text-muted mb-0" style="font-size: 14px;">Atur hak akses streaming, data profil, dan alokasi kuota live kamera pelanggan Loewix.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-info btn-sm mr-2" style="border-radius: 20px;" data-toggle="modal" data-target="#modalCityManagement" onclick="loadAdminCities()">
            <i class="fas fa-map-marked-alt mr-1"></i> Kelola Wilayah
          </button>
          <button class="btn btn-outline-info btn-sm mr-2" style="border-radius: 20px;" onclick="exportCustomerCSV()">
            <i class="fas fa-download mr-1"></i> Export CSV
          </button>
          <button class="btn btn-gold" onclick="openAddCustomerModal()">
            <i class="fas fa-user-plus mr-1"></i> Tambah Customer Baru
          </button>
        </div>
      </div>

      <!-- Search & Filter Controls -->
      <div class="filter-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div style="position: relative; flex: 1; min-width: 250px;">
          <i class="fas fa-search" style="position: absolute; left: 14px; top: 14px; color: #94a3b8; font-size: 14px;"></i>
          <input type="text" id="search-customer-input" class="form-control search-input" placeholder="🔍 Cari Nama Customer, Email, atau Telepon..." onkeyup="filterCustomerTable()">
        </div>
        <div class="d-flex align-items-center gap-2">
          <select id="filter-city-select" class="form-control form-control-sm" style="background: rgba(255,255,255,0.08); color: #fff; border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 5px 12px;" onchange="filterCustomerTable()">
            <option value="all" style="color:#000;">🌐 Semua Wilayah</option>
            <option value="siantar" style="color:#000;">📍 Pematangsiantar</option>
            <option value="jakarta" style="color:#000;">📍 DKI Jakarta</option>
            <option value="medan" style="color:#000;">📍 Kota Medan</option>
            <option value="bandung" style="color:#000;">📍 Kota Bandung</option>
            <option value="bali" style="color:#000;">📍 Bali / Denpasar</option>
          </select>
          <select id="filter-status-select" class="form-control form-control-sm" style="background: rgba(255,255,255,0.08); color: #fff; border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 5px 12px;" onchange="filterCustomerTable()">
            <option value="all" style="color:#000;">⚡ Semua Status</option>
            <option value="active" style="color:#000;">✅ Status Aktif</option>
            <option value="suspended" style="color:#000;">⛔ Status Suspended</option>
          </select>
        </div>
      </div>

      <!-- Customers Table -->
      <div class="table-responsive">
        <table class="table table-dark-custom table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer / Perusahaan</th>
              <th>Email / No. Telp</th>
              <th>Wilayah</th>
              <th>Penggunaan Kuota CCTV</th>
              <th>Status Akun</th>
              <th class="text-right">Aksi Super Admin</th>
            </tr>
          </thead>
          <tbody id="customer-table-body">
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <div>Memuat data customer...</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Modal Tambah Customer Baru -->
  <div class="modal fade" id="modalAddCustomer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content modal-content-dark">
        <div class="modal-header modal-header-dark">
          <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus text-info mr-2"></i> Tambah Customer Baru</h5>
          <button type="button" class="close text-white" onclick="closeAddCustomerModal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formAddCustomer" onsubmit="submitAddCustomer(event)">
          <div class="modal-body">
            <div class="form-group mb-3">
              <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">Nama Customer / Perusahaan:</label>
              <input type="text" id="cust-name" class="form-control form-control-dark" placeholder="Contoh: PT. Jaya Sentosa Enterprise" required>
            </div>
            <div class="form-group mb-3">
              <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">Email Login:</label>
              <input type="email" id="cust-email" class="form-control form-control-dark" placeholder="customer@jayasentosa.com" required>
            </div>
            <div class="form-group mb-3">
              <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">Password Initial:</label>
              <div style="position: relative; width: 100%;">
                <input type="password" id="cust-password" class="form-control form-control-dark" placeholder="Minimal 6 Karakter" required style="padding-right: 40px;">
                <i class="fas fa-eye" id="toggle-add-cust-password-icon" onclick="toggleAddCustPasswordVisibility()" title="Tampilkan / Sembunyikan Password" style="position: absolute; right: 14px; top: 15px; color: #94a3b8; font-size: 14px; cursor: pointer; z-index: 10;"></i>
              </div>
            </div>
            <div class="form-row mb-3">
              <div class="form-group col-6">
                <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">Alokasi Kuota CCTV:</label>
                <input type="number" id="cust-quota" class="form-control form-control-dark" value="10" min="1" max="500" required>
                <small class="text-info font-weight-bold">Batas Jumlah Kamera Live</small>
              </div>
              <div class="form-group col-6">
                <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">Wilayah Utama:</label>
                <select id="cust-city" class="form-control form-control-dark">
                  <option value="siantar" style="color:#000;">Pematangsiantar</option>
                  <option value="jakarta" style="color:#000;">DKI Jakarta</option>
                  <option value="medan" style="color:#000;">Kota Medan</option>
                  <option value="bandung" style="color:#000;">Kota Bandung</option>
                  <option value="bali" style="color:#000;">Bali / Denpasar</option>
                </select>
              </div>
            </div>
            <div class="form-group mb-2">
              <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">No. WhatsApp / HP:</label>
              <input type="text" id="cust-phone" class="form-control form-control-dark" placeholder="+62 812-3456-7890">
            </div>
          </div>
          <div class="modal-footer modal-footer-dark">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeAddCustomerModal()">Batal</button>
            <button type="submit" class="btn btn-gold btn-sm"><i class="fas fa-save mr-1"></i> Simpan Customer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Data Customer -->
  <div class="modal fade" id="modalEditCustomer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content modal-content-dark">
        <div class="modal-header modal-header-dark">
          <h5 class="modal-title font-weight-bold"><i class="fas fa-user-edit text-warning mr-2"></i> Edit Data Profil Customer</h5>
          <button type="button" class="close text-white" onclick="closeEditCustomerModal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditCustomer" onsubmit="submitEditCustomer(event)">
          <input type="hidden" id="edit-profile-id">
          <div class="modal-body">
            <div class="form-group mb-3">
              <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">Nama Customer / Perusahaan:</label>
              <input type="text" id="edit-profile-name" class="form-control form-control-dark" required>
            </div>
            <div class="form-group mb-3">
              <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">Email Login:</label>
              <input type="email" id="edit-profile-email" class="form-control form-control-dark" required>
            </div>
            <div class="form-row mb-2">
              <div class="form-group col-6">
                <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">Wilayah Utama:</label>
                <select id="edit-profile-city" class="form-control form-control-dark">
                  <option value="siantar" style="color:#000;">Pematangsiantar</option>
                  <option value="jakarta" style="color:#000;">DKI Jakarta</option>
                  <option value="medan" style="color:#000;">Kota Medan</option>
                  <option value="bandung" style="color:#000;">Kota Bandung</option>
                  <option value="bali" style="color:#000;">Bali / Denpasar</option>
                </select>
              </div>
              <div class="form-group col-6">
                <label class="font-weight-bold text-muted" style="font-size: 13px; display: block; margin-bottom: 6px;">No. WhatsApp / HP:</label>
                <input type="text" id="edit-profile-phone" class="form-control form-control-dark">
              </div>
            </div>
          </div>
          <div class="modal-footer modal-footer-dark">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeEditCustomerModal()">Batal</button>
            <button type="submit" class="btn btn-gold btn-sm"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Kelola CCTV Customer -->
  <div class="modal fade" id="modalCustomerCCTV" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content modal-content-dark">
        <div class="modal-header modal-header-dark d-flex justify-content-between align-items-center">
          <div>
            <h5 class="modal-title font-weight-bold mb-1" style="color: #ffffff;"><i class="fas fa-video text-info mr-2"></i> Kelola Channel CCTV Customer</h5>
            <div id="cctv-modal-subtitle" class="text-muted" style="font-size: 13px;">Customer: PT. Jaya Sentosa Enterprise</div>
          </div>
          <button type="button" class="close text-white" onclick="closeCustomerCCTVModal()" aria-label="Close" style="outline: none;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="badge badge-info p-2" id="cctv-modal-quota-badge" style="border-radius: 20px; font-size: 12px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); color: #38bdf8;">
              <i class="fas fa-layer-group mr-1"></i> KUOTA: 0 / 20 CCTV TERPAKAI
            </div>
            <button class="btn btn-gold btn-sm" onclick="openAddCameraForCustomerForm()">
              <i class="fas fa-plus-circle mr-1"></i> Tambah Kamera Baru
            </button>
          </div>

          <!-- Form Tambah Kamera (Collapsible) -->
          <div id="add-camera-form-box" class="p-4 mb-4 rounded" style="background: rgba(13, 27, 62, 0.95); border: 1px solid rgba(0, 210, 255, 0.4); display: none; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
              <h6 class="font-weight-bold text-success mb-0" style="font-size: 15px; letter-spacing: 0.5px;">
                <i class="fas fa-plus-circle mr-1"></i> TAMBAH KAMERA BARU
              </h6>
              <span class="badge badge-info p-2" style="border-radius: 12px; font-size: 11px;">
                <i class="fas fa-bolt mr-1"></i> Auto-Bridge MediaMTX Live
              </span>
            </div>

            <!-- Tab Switcher Tipe Koneksi -->
            <div class="conn-mode-tabs">
              <button type="button" class="conn-tab-btn active" id="tab-add-xmeye" onclick="switchCamConnMode('xmeye_p2p', 'add')">
                <i class="fas fa-qrcode mr-1 text-info"></i> Mode XMeye Cloud P2P (Scan QR / Serial)
              </button>
              <button type="button" class="conn-tab-btn" id="tab-add-rtsp" onclick="switchCamConnMode('rtsp', 'add')">
                <i class="fas fa-video mr-1 text-warning"></i> Mode RTSP / HLS Manual
              </button>
            </div>

            <form onsubmit="submitAddCameraForCustomer(event)">
              <input type="hidden" id="cam-input-conn-type" value="xmeye_p2p">

              <div class="form-group mb-3">
                <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Nama Kamera / Lokasi:</label>
                <input type="text" id="cam-input-title" class="form-control form-control-dark" placeholder="Contoh: Kamera Depan Kasir / Parkiran Utama" required>
              </div>

              <div class="form-group mb-3">
                <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Wilayah / Kota:</label>
                <select id="cam-input-city" class="form-control form-control-dark">
                  <option value="siantar" style="color:#000;">Pematangsiantar</option>
                  <option value="jakarta" style="color:#000;">DKI Jakarta</option>
                  <option value="medan" style="color:#000;">Kota Medan</option>
                  <option value="bandung" style="color:#000;">Kota Bandung</option>
                  <option value="bali" style="color:#000;">Bali / Denpasar</option>
                </select>
              </div>

              <!-- Section 1: XMeye Cloud P2P Fields -->
              <div id="add-xmeye-section" class="p-3 mb-3 rounded" style="background: rgba(0, 210, 255, 0.04); border: 1px dashed rgba(0, 210, 255, 0.3);">
                <div class="form-group mb-3">
                  <label style="font-size: 13px; color: #00d2ff; font-weight: 700; display: block; margin-bottom: 6px;">
                    <i class="fas fa-barcode mr-1"></i> Serial Number / Cloud ID XMeye (16 Digit):
                  </label>
                  <div class="input-group">
                    <input type="text" id="cam-input-xmeye-serial" class="form-control form-control-dark font-family-monospace" placeholder="Contoh: fe8a1234567890ab" maxlength="32">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-info font-weight-bold px-3 d-flex align-items-center" onclick="openQRScannerModal('add')" style="border-radius: 0 10px 10px 0; background: linear-gradient(135deg, #00d2ff, #0077ff); border: none;">
                        <i class="fas fa-camera mr-1"></i> Scan QR
                      </button>
                    </div>
                  </div>
                  <small class="text-muted d-block mt-1" style="font-size: 11px;">
                    💡 Scan QR Code di menu <strong>Main Menu &gt; Info &gt; Version</strong> pada layar DVR XMeye, atau dari stiker dus.
                  </small>
                </div>

                <div class="form-row mb-2">
                  <div class="form-group col-md-6 col-12 mb-2">
                    <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Username Perangkat:</label>
                    <input type="text" id="cam-input-xmeye-user" class="form-control form-control-dark" value="admin">
                  </div>
                  <div class="form-group col-md-6 col-12 mb-2">
                    <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Password DVR / Kamera:</label>
                    <div class="input-group">
                      <input type="password" id="cam-input-xmeye-pass" class="form-control form-control-dark" placeholder="Kosongkan jika tidak ada password">
                      <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassVisibility('cam-input-xmeye-pass')">
                          <i class="fas fa-eye" id="cam-input-xmeye-pass-icon"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-row mb-1">
                  <div class="form-group col-md-6 col-12 mb-2">
                    <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Pilih Channel DVR:</label>
                    <select id="cam-input-xmeye-channel" class="form-control form-control-dark">
                      <option value="1" style="color:#000;">Channel 1 (Kamera 1)</option>
                      <option value="2" style="color:#000;">Channel 2 (Kamera 2)</option>
                      <option value="3" style="color:#000;">Channel 3 (Kamera 3)</option>
                      <option value="4" style="color:#000;">Channel 4 (Kamera 4)</option>
                      <option value="5" style="color:#000;">Channel 5 (Kamera 5)</option>
                      <option value="6" style="color:#000;">Channel 6 (Kamera 6)</option>
                      <option value="7" style="color:#000;">Channel 7 (Kamera 7)</option>
                      <option value="8" style="color:#000;">Channel 8 (Kamera 8)</option>
                      <option value="9" style="color:#000;">Channel 9 (Kamera 9)</option>
                      <option value="10" style="color:#000;">Channel 10 (Kamera 10)</option>
                      <option value="11" style="color:#000;">Channel 11 (Kamera 11)</option>
                      <option value="12" style="color:#000;">Channel 12 (Kamera 12)</option>
                      <option value="13" style="color:#000;">Channel 13 (Kamera 13)</option>
                      <option value="14" style="color:#000;">Channel 14 (Kamera 14)</option>
                      <option value="15" style="color:#000;">Channel 15 (Kamera 15)</option>
                      <option value="16" style="color:#000;">Channel 16 (Kamera 16)</option>
                      <option value="32" style="color:#000;">Channel 32 (Kamera 32)</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6 col-12 mb-2">
                    <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Kualitas Stream Web:</label>
                    <select id="cam-input-xmeye-stream" class="form-control form-control-dark">
                      <option value="sub" style="color:#000;">⚡ Sub Stream (Rekomendasi Cepat & Hemat Kuota Web)</option>
                      <option value="main" style="color:#000;">💎 Main Stream (Kualitas HD)</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Section 2: Manual RTSP Fields -->
              <div id="add-rtsp-section" class="form-group mb-3" style="display: none;">
                <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Stream Path / RTSP Stream ID / HLS URL:</label>
                <input type="text" id="cam-input-path" class="form-control form-control-dark" placeholder="Contoh: cam_bali_1 atau rtsp://admin:pass@IP:554/stream1">
                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                  Gunakan Stream Path MediaMTX (contoh: <span class="text-danger font-weight-bold">cam_bali_1</span>), HLS URL (<span class="text-danger font-weight-bold">.m3u8</span>), atau IPCamLive.
                </small>
              </div>

              <div class="form-row mb-3">
                <div class="form-group col-6 mb-0">
                  <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Latitude GPS:</label>
                  <input type="text" id="cam-input-lat" class="form-control form-control-dark" placeholder="-8.7188">
                </div>
                <div class="form-group col-6 mb-0">
                  <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Longitude GPS:</label>
                  <input type="text" id="cam-input-lng" class="form-control form-control-dark" placeholder="115.1783">
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 mt-3 flex-wrap">
                <button type="button" class="btn btn-secondary btn-sm mr-2" style="border-radius: 8px; padding: 8px 18px;" onclick="closeAddCameraForCustomerForm()">Batal</button>
                <button type="submit" class="btn btn-success btn-sm font-weight-bold" style="border-radius: 8px; padding: 8px 22px; background: #10b981; border: none;">
                  <i class="fas fa-save mr-1"></i> Simpan Konfigurasi
                </button>
              </div>
            </form>
          </div>

          <!-- Form Edit Kamera (Collapsible) -->
          <div id="edit-camera-form-box" class="p-4 mb-4 rounded" style="background: rgba(13, 27, 62, 0.95); border: 1px solid rgba(245, 158, 11, 0.4); display: none; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
              <h6 class="font-weight-bold text-warning mb-0" style="font-size: 15px; letter-spacing: 0.5px;">
                <i class="fas fa-edit mr-1"></i> EDIT KONFIGURASI KAMERA CCTV
              </h6>
            </div>

            <!-- Tab Switcher Edit Mode -->
            <div class="conn-mode-tabs">
              <button type="button" class="conn-tab-btn" id="tab-edit-xmeye" onclick="switchCamConnMode('xmeye_p2p', 'edit')">
                <i class="fas fa-qrcode mr-1 text-info"></i> Mode XMeye Cloud P2P
              </button>
              <button type="button" class="conn-tab-btn" id="tab-edit-rtsp" onclick="switchCamConnMode('rtsp', 'edit')">
                <i class="fas fa-video mr-1 text-warning"></i> Mode RTSP / HLS Manual
              </button>
            </div>

            <form onsubmit="submitEditCameraForCustomer(event)">
              <input type="hidden" id="edit-cam-id">
              <input type="hidden" id="edit-cam-conn-type" value="rtsp">

              <div class="form-group mb-3">
                <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Nama Kamera / Lokasi:</label>
                <input type="text" id="edit-cam-title" class="form-control form-control-dark" required>
              </div>

              <div class="form-group mb-3">
                <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Wilayah / Kota:</label>
                <select id="edit-cam-city" class="form-control form-control-dark">
                  <option value="siantar" style="color:#000;">Pematangsiantar</option>
                  <option value="jakarta" style="color:#000;">DKI Jakarta</option>
                  <option value="medan" style="color:#000;">Kota Medan</option>
                  <option value="bandung" style="color:#000;">Kota Bandung</option>
                  <option value="bali" style="color:#000;">Bali / Denpasar</option>
                </select>
              </div>

              <!-- Edit Section XMeye -->
              <div id="edit-xmeye-section" class="p-3 mb-3 rounded" style="background: rgba(0, 210, 255, 0.04); border: 1px dashed rgba(0, 210, 255, 0.3);">
                <div class="form-group mb-3">
                  <label style="font-size: 13px; color: #00d2ff; font-weight: 700; display: block; margin-bottom: 6px;">
                    <i class="fas fa-barcode mr-1"></i> Serial Number / Cloud ID XMeye (16 Digit):
                  </label>
                  <div class="input-group">
                    <input type="text" id="edit-cam-xmeye-serial" class="form-control form-control-dark font-family-monospace" placeholder="Contoh: fe8a1234567890ab">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-info font-weight-bold px-3 d-flex align-items-center" onclick="openQRScannerModal('edit')" style="border-radius: 0 10px 10px 0; background: linear-gradient(135deg, #00d2ff, #0077ff); border: none;">
                        <i class="fas fa-camera mr-1"></i> Scan QR
                      </button>
                    </div>
                  </div>
                </div>

                <div class="form-row mb-2">
                  <div class="form-group col-md-6 col-12 mb-2">
                    <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Username Perangkat:</label>
                    <input type="text" id="edit-cam-xmeye-user" class="form-control form-control-dark" value="admin">
                  </div>
                  <div class="form-group col-md-6 col-12 mb-2">
                    <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Password DVR (Isi jika diubah):</label>
                    <input type="password" id="edit-cam-xmeye-pass" class="form-control form-control-dark" placeholder="••••••••">
                  </div>
                </div>

                <div class="form-row mb-1">
                  <div class="form-group col-md-6 col-12 mb-2">
                    <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Pilih Channel DVR:</label>
                    <select id="edit-cam-xmeye-channel" class="form-control form-control-dark">
                      <option value="1" style="color:#000;">Channel 1 (Kamera 1)</option>
                      <option value="2" style="color:#000;">Channel 2 (Kamera 2)</option>
                      <option value="3" style="color:#000;">Channel 3 (Kamera 3)</option>
                      <option value="4" style="color:#000;">Channel 4 (Kamera 4)</option>
                      <option value="5" style="color:#000;">Channel 5 (Kamera 5)</option>
                      <option value="6" style="color:#000;">Channel 6 (Kamera 6)</option>
                      <option value="7" style="color:#000;">Channel 7 (Kamera 7)</option>
                      <option value="8" style="color:#000;">Channel 8 (Kamera 8)</option>
                      <option value="16" style="color:#000;">Channel 16 (Kamera 16)</option>
                      <option value="32" style="color:#000;">Channel 32 (Kamera 32)</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6 col-12 mb-2">
                    <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Kualitas Stream Web:</label>
                    <select id="edit-cam-xmeye-stream" class="form-control form-control-dark">
                      <option value="sub" style="color:#000;">⚡ Sub Stream (Rekomendasi Cepat & Hemat Kuota)</option>
                      <option value="main" style="color:#000;">💎 Main Stream (Kualitas HD)</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Edit Section RTSP -->
              <div id="edit-rtsp-section" class="form-group mb-3">
                <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Stream Path / RTSP Stream ID / HLS URL:</label>
                <input type="text" id="edit-cam-path" class="form-control form-control-dark">
              </div>

              <div class="form-row mb-3">
                <div class="form-group col-6 mb-0">
                  <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Latitude GPS:</label>
                  <input type="text" id="edit-cam-lat" class="form-control form-control-dark">
                </div>
                <div class="form-group col-6 mb-0">
                  <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Longitude GPS:</label>
                  <input type="text" id="edit-cam-lng" class="form-control form-control-dark">
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 mt-3 flex-wrap">
                <button type="button" class="btn btn-secondary btn-sm mr-2" style="border-radius: 8px; padding: 8px 18px;" onclick="closeEditCameraForm()">Batal</button>
                <button type="submit" class="btn btn-warning btn-sm font-weight-bold" style="border-radius: 8px; padding: 8px 22px; background: #f59e0b; border: none; color: #000;">
                  <i class="fas fa-save mr-1"></i> Update Kamera
                </button>
              </div>
            </form>
          </div>

          <!-- Table List Kamera -->
          <div class="table-responsive">
            <table class="table table-dark-custom">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama / Lokasi Kamera</th>
                  <th>Wilayah</th>
                  <th>Stream Path / HLS URL</th>
                  <th>Koordinat GPS</th>
                  <th>Status Stream</th>
                  <th class="text-right">Aksi</th>
                </tr>
              </thead>
              <tbody id="cctv-modal-table-body">
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">Belum ada kamera CCTV terpasang untuk customer ini. Klik 'Tambah Kamera Baru'.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Kelola Master Wilayah / Kota -->
  <div class="modal fade" id="modalCityManagement" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content modal-content-dark">
        <div class="modal-header modal-header-dark d-flex justify-content-between align-items-center">
          <div>
            <h5 class="modal-title font-weight-bold mb-1" style="color: #ffffff;"><i class="fas fa-map-marked-alt text-info mr-2"></i> Kelola Master Data Wilayah & Kota</h5>
            <div class="text-muted" style="font-size: 13px;">Tambah kota baru, ubah nama wilayah, serta setel koordinat pusat GPS & zoom level peta.</div>
          </div>
          <button type="button" class="close text-white" data-dismiss="modal" onclick="closeCityManagementModal()" aria-label="Close" style="outline: none;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <span class="badge badge-info p-2" id="cities-count-badge" style="border-radius: 20px; font-size: 12px; background: rgba(0, 210, 255, 0.15); border: 1px solid rgba(0, 210, 255, 0.3); color: #00d2ff;">
              <i class="fas fa-city mr-1"></i> MEMUAT DATA WILAYAH...
            </span>
            <button class="btn btn-gold btn-sm" onclick="toggleAddCityForm()">
              <i class="fas fa-plus-circle mr-1"></i> Tambah Wilayah Baru
            </button>
          </div>

          <!-- Form Tambah / Edit Wilayah (Collapsible) -->
          <div id="city-form-box" class="p-4 mb-4 rounded" style="background: rgba(13, 27, 62, 0.95); border: 1px solid rgba(0, 210, 255, 0.4); display: none; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <h6 id="city-form-title" class="font-weight-bold text-success mb-3" style="font-size: 15px; letter-spacing: 0.5px;"><i class="fas fa-plus-circle mr-1"></i> TAMBAH WILAYAH / KOTA BARU</h6>
            <form onsubmit="submitSaveCity(event)">
              <input type="hidden" id="city-form-action" value="add">
              <div class="form-row mb-3">
                <div class="form-group col-md-6 mb-2">
                  <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Nama Wilayah / Kota:</label>
                  <input type="text" id="city-input-name" class="form-control form-control-dark" placeholder="Contoh: Kota Surabaya" onkeyup="autoGenerateCitySlug()" required>
                </div>
                <div class="form-group col-md-6 mb-2">
                  <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Kode / Slug Wilayah (ID):</label>
                  <input type="text" id="city-input-id" class="form-control form-control-dark" placeholder="surabaya" required>
                  <small class="text-muted" style="font-size: 11px;">Huruf kecil tanpa spasi (contoh: surabaya, bandung, bali)</small>
                </div>
              </div>

              <div class="form-row mb-3">
                <div class="form-group col-md-4 mb-2">
                  <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Latitude GPS Pusat:</label>
                  <input type="text" id="city-input-lat" class="form-control form-control-dark" placeholder="-7.2575" required>
                </div>
                <div class="form-group col-md-4 mb-2">
                  <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Longitude GPS Pusat:</label>
                  <input type="text" id="city-input-lng" class="form-control form-control-dark" placeholder="112.7521" required>
                </div>
                <div class="form-group col-md-4 mb-2">
                  <label style="font-size: 13px; color: #e2e8f0; font-weight: 600; display: block; margin-bottom: 4px;">Zoom Level Peta (1-19):</label>
                  <input type="number" id="city-input-zoom" class="form-control form-control-dark" value="12" min="1" max="19" required>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-secondary btn-sm mr-2" style="border-radius: 8px; padding: 8px 18px;" onclick="toggleAddCityForm(false)">Batal</button>
                <button type="submit" class="btn btn-success btn-sm font-weight-bold" style="border-radius: 8px; padding: 8px 22px; background: #10b981; border: none;"><i class="fas fa-save mr-1"></i> Simpan Wilayah</button>
              </div>
            </form>
          </div>

          <!-- Tabel Daftar Wilayah -->
          <div class="table-responsive">
            <table class="table table-dark-custom table-hover">
              <thead>
                <tr>
                  <th>KODE ID</th>
                  <th>NAMA WILAYAH</th>
                  <th>KOORDINAT PUSAT GPS</th>
                  <th>ZOOM LEVEL</th>
                  <th class="text-right">AKSI</th>
                </tr>
              </thead>
              <tbody id="cities-table-body">
                <tr>
                  <td colspan="5" class="text-center py-3 text-muted">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Memuat data master wilayah...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Scan Barcode / QR Code XMeye -->
  <div class="modal fade" id="modalQRScanner" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
      <div class="modal-content modal-content-dark" style="border: 1px solid #00d2ff; box-shadow: 0 0 40px rgba(0, 210, 255, 0.35);">
        <div class="modal-header modal-header-dark d-flex justify-content-between align-items-center">
          <div>
            <h5 class="modal-title font-weight-bold mb-0 text-info">
              <i class="fas fa-qrcode mr-2"></i> Pindai Barcode / QR Code XMeye
            </h5>
            <div class="text-muted" style="font-size: 12px;">Arahkan kamera ke QR Code Cloud ID di layar DVR / stiker dus</div>
          </div>
          <button type="button" class="close text-white" onclick="closeQRScannerModal()" aria-label="Close" style="outline: none;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-3 text-center">
          <!-- Viewfinder Box with Laser Scan Effect -->
          <div class="qr-scanner-box">
            <div id="qr-reader" style="width: 100%; height: 100%;"></div>
            <div class="qr-scanner-laser"></div>
          </div>

          <!-- Controls & Camera Switch -->
          <div class="d-flex justify-content-center gap-2 mb-3 flex-wrap">
            <button type="button" class="btn btn-outline-info btn-sm font-weight-bold" id="btn-switch-camera" onclick="switchCameraFacing()">
              <i class="fas fa-sync-alt mr-1"></i> Ganti Kamera (Depan/Belakang)
            </button>
            <label class="btn btn-outline-warning btn-sm font-weight-bold mb-0" style="cursor: pointer;">
              <i class="fas fa-image mr-1"></i> Upload Foto QR
              <input type="file" id="qr-file-input" accept="image/*" style="display: none;" onchange="handleQRFileUpload(event)">
            </label>
          </div>

          <div id="qr-scanner-status" class="alert alert-info py-2 mb-0 font-weight-bold" style="font-size: 12px; background: rgba(0, 210, 255, 0.1); border: 1px solid rgba(0, 210, 255, 0.3); color: #00d2ff;">
            <i class="fas fa-camera mr-1"></i> Mengaktifkan kamera scanner...
          </div>
        </div>
        <div class="modal-footer modal-footer-dark justify-content-center">
          <button type="button" class="btn btn-secondary btn-sm px-4" onclick="closeQRScannerModal()">Tutup Scanner</button>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery and Bootstrap JS with reliable CDN fallbacks -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>window.jQuery || document.write('<script src="../assets/js/jquery-3.6.0.min.js"><\/script><script src="../assets/js/bootstrap.min.js"><\/script>')</script>

  <script>
    let defaultCustomers = [
      {
        id: 2,
        name: 'PT. Jaya Sentosa Enterprise',
        email: 'customer@jayasentosa.com',
        phone: '+62 812-3456-7890',
        city: 'siantar',
        cctv_quota: 20,
        cctv_used: 0,
        status: 'active',
        created_at: '2026-08-14'
      },
      {
        id: 3,
        name: 'PT. Berlian Djaya Nusantara',
        email: 'berlian@gmail.com',
        phone: '+6285771593522',
        city: 'jakarta',
        cctv_quota: 20,
        cctv_used: 0,
        status: 'active',
        created_at: '2026-08-14'
      }
    ];

    let cachedCustomers = [];

    function getStoredCustomers() {
      const stored = localStorage.getItem('loewix_customers');
      if (stored) {
        try {
          return JSON.parse(stored);
        } catch(e) {}
      }
      localStorage.setItem('loewix_customers', JSON.stringify(defaultCustomers));
      return defaultCustomers;
    }

    function saveStoredCustomers(list) {
      localStorage.setItem('loewix_customers', JSON.stringify(list));
    }

    const API_SERVER = '../api';

    document.addEventListener('DOMContentLoaded', () => {
      loadCustomerData();
    });

    function loadCustomerData() {
      fetch(`${API_SERVER}/admin_customers.php`)
        .then(res => res.json())
        .then(data => {
          if (!data.success) {
            cachedCustomers = getStoredCustomers();
            renderCustomerTable(cachedCustomers);
            return;
          }
          cachedCustomers = data.customers;
          renderCustomerTable(cachedCustomers);
        })
        .catch(err => {
          cachedCustomers = getStoredCustomers();
          renderCustomerTable(cachedCustomers);
        });
    }

    function renderCustomerTable(customers) {
      const tbody = document.getElementById('customer-table-body');
      tbody.innerHTML = '';

      let totalCameras = 0;
      let totalQuota = 0;

      if (!customers || customers.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada Customer terdaftar atau sesuai kriteria pencarian.</td></tr>`;
      } else {
        customers.forEach(c => {
          totalCameras += (c.cctv_used || 0);
          totalQuota += (c.cctv_quota || 10);

          const percentUsed = Math.min(100, Math.round(((c.cctv_used || 0) / (c.cctv_quota || 10)) * 100));
          const statusBadge = (c.status === 'active')
            ? `<span class="badge-status-active"><i class="fas fa-check-circle mr-1"></i> AKTIF</span>`
            : `<span class="badge-status-suspended"><i class="fas fa-ban mr-1"></i> SUSPENDED</span>`;

          const rowHTML = `
            <tr>
              <td class="font-weight-bold text-muted">#${c.id}</td>
              <td>
                <div class="font-weight-bold text-white" style="font-size: 15px;">${c.name}</div>
                <small class="text-muted"><i class="fas fa-history mr-1"></i> Terdaftar ${c.created_at ? c.created_at.split(' ')[0] : '2026-08-14'}</small>
              </td>
              <td>
                <div><i class="fas fa-envelope text-info mr-1"></i> ${c.email}</div>
                <small class="text-muted"><i class="fas fa-phone mr-1"></i> ${c.phone}</small>
              </td>
              <td><span class="badge badge-secondary" style="border-radius: 12px; padding: 4px 10px;">📍 ${(c.city || 'siantar').toUpperCase()}</span></td>
              <td>
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="font-weight-bold text-info" style="font-size: 13px; cursor: pointer;" onclick="openCustomerCCTVModal(${c.id})" title="Klik untuk Kelola Kamera Customer Ini">
                    <i class="fas fa-video mr-1 text-warning"></i> ${c.cctv_used || 0} / ${c.cctv_quota} CCTV
                  </span>
                  <span class="text-muted" style="font-size: 11px;">${percentUsed}%</span>
                </div>
                <div class="progress-bar-custom" style="cursor: pointer;" onclick="openCustomerCCTVModal(${c.id})" title="Klik untuk Kelola Kamera Customer Ini">
                  <div class="progress-fill" style="width: ${percentUsed}%;"></div>
                </div>
              </td>
              <td>${statusBadge}</td>
              <td class="text-right">
                <div class="action-btn-group">
                  <button class="act-btn" style="background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); color: #10b981;" onclick="openCustomerCCTVModal(${c.id})" title="Kelola Channel Kamera CCTV Customer Ini">
                    <i class="fas fa-video"></i>
                  </button>
                  <button class="act-btn act-btn-edit" onclick="openEditCustomerModal(${c.id})" title="Edit Data Profil Customer">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="act-btn act-btn-quota" onclick="openEditQuotaModal(${c.id}, '${c.name.replace(/'/g, "\\'")}', ${c.cctv_quota})" title="Edit Kuota CCTV">
                    <i class="fas fa-sliders-h"></i>
                  </button>
                  <button class="act-btn act-btn-pass" onclick="resetCustomerPassword(${c.id}, '${c.name.replace(/'/g, "\\'")}')" title="Reset Password Customer">
                    <i class="fas fa-key"></i>
                  </button>
                  <button class="act-btn act-btn-status" onclick="toggleStatus(${c.id})" title="Toggle Suspend/Aktif">
                    <i class="fas fa-power-off"></i>
                  </button>
                  <button class="act-btn act-btn-delete" onclick="deleteCustomer(${c.id}, '${c.name.replace(/'/g, "\\'")}')" title="Hapus Akun Customer">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `;
          tbody.innerHTML += rowHTML;
        });
      }

      document.getElementById('metric-total-customers').innerText = customers.length;
      document.getElementById('metric-total-cameras').innerText = totalCameras;
      document.getElementById('metric-total-quota').innerText = totalQuota;
    }

    function filterCustomerTable() {
      const search = document.getElementById('search-customer-input').value.toLowerCase().trim();
      const city = document.getElementById('filter-city-select').value;
      const status = document.getElementById('filter-status-select').value;

      let filtered = cachedCustomers.filter(c => {
        const matchSearch = c.name.toLowerCase().includes(search) || c.email.toLowerCase().includes(search) || (c.phone && c.phone.includes(search));
        const matchCity = (city === 'all' || (c.city && c.city.toLowerCase() === city));
        const matchStatus = (status === 'all' || c.status === status);
        return matchSearch && matchCity && matchStatus;
      });

      renderCustomerTable(filtered);
    }

    function exportCustomerCSV() {
      let csvContent = "data:text/csv;charset=utf-8,ID,Nama Perusahaan,Email,No Telp,Wilayah,Kuota CCTV,Terpakai,Status\n";
      cachedCustomers.forEach(c => {
        csvContent += `${c.id},"${c.name}",${c.email},${c.phone},${c.city},${c.cctv_quota},${c.cctv_used || 0},${c.status}\n`;
      });
      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", `loewix_customers_${new Date().toISOString().split('T')[0]}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    function openAddCustomerModal() {
      try {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
          $('#modalAddCustomer').modal('show');
        } else {
          document.getElementById('modalAddCustomer').style.display = 'block';
          document.getElementById('modalAddCustomer').classList.add('show');
        }
      } catch(e) {
        document.getElementById('modalAddCustomer').style.display = 'block';
        document.getElementById('modalAddCustomer').classList.add('show');
      }
    }

    function closeAddCustomerModal() {
      try {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
          $('#modalAddCustomer').modal('hide');
        }
      } catch(e) {}
      document.getElementById('modalAddCustomer').style.display = 'none';
      document.getElementById('modalAddCustomer').classList.remove('show');
    }

    function openEditCustomerModal(id) {
      let list = cachedCustomers;
      const cust = list.find(c => c.id === id);
      if (!cust) return;

      document.getElementById('edit-profile-id').value = cust.id;
      document.getElementById('edit-profile-name').value = cust.name;
      document.getElementById('edit-profile-email').value = cust.email;
      document.getElementById('edit-profile-city').value = cust.city || 'siantar';
      document.getElementById('edit-profile-phone').value = cust.phone || '';

      try {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
          $('#modalEditCustomer').modal('show');
        } else {
          document.getElementById('modalEditCustomer').style.display = 'block';
          document.getElementById('modalEditCustomer').classList.add('show');
        }
      } catch(e) {
        document.getElementById('modalEditCustomer').style.display = 'block';
        document.getElementById('modalEditCustomer').classList.add('show');
      }
    }

    function closeEditCustomerModal() {
      try {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
          $('#modalEditCustomer').modal('hide');
        }
      } catch(e) {}
      document.getElementById('modalEditCustomer').style.display = 'none';
      document.getElementById('modalEditCustomer').classList.remove('show');
    }

    function submitAddCustomer(e) {
      if (e) e.preventDefault();
      const name = document.getElementById('cust-name').value.trim();
      const email = document.getElementById('cust-email').value.trim();
      const password = document.getElementById('cust-password').value.trim();
      const quota = parseInt(document.getElementById('cust-quota').value) || 10;
      const city = document.getElementById('cust-city').value;
      const phone = document.getElementById('cust-phone').value.trim() || '-';

      if (!name || !email || !password) {
        alert('Nama, Email, dan Password wajib diisi!');
        return;
      }

      let list = getStoredCustomers();
      const newId = list.length > 0 ? Math.max(...list.map(c => c.id)) + 1 : 1;
      const newCust = {
        id: newId,
        name: name,
        email: email,
        password: password,
        cctv_quota: quota,
        cctv_used: 0,
        city: city,
        phone: phone,
        status: 'active',
        created_at: new Date().toISOString().split('T')[0]
      };
      list.push(newCust);
      saveStoredCustomers(list);
      cachedCustomers = list;

      const formData = new FormData();
      formData.append('action', 'create');
      formData.append('name', name);
      formData.append('email', email);
      formData.append('password', password);
      formData.append('cctv_quota', quota);
      formData.append('city', city);
      formData.append('phone', phone);
      fetch(`${API_SERVER}/admin_customers.php`, { method: 'POST', body: formData }).then(() => loadCustomerData()).catch(e => {});

      alert(`BERHASIL: Customer baru '${name}' berhasil ditambahkan dengan kuota ${quota} CCTV!`);
      closeAddCustomerModal();
      document.getElementById('formAddCustomer').reset();
      renderCustomerTable(list);
    }

    function submitEditCustomer(e) {
      if (e) e.preventDefault();
      const id = parseInt(document.getElementById('edit-profile-id').value);
      const name = document.getElementById('edit-profile-name').value.trim();
      const email = document.getElementById('edit-profile-email').value.trim();
      const city = document.getElementById('edit-profile-city').value;
      const phone = document.getElementById('edit-profile-phone').value.trim() || '-';

      if (!name || !email) {
        alert('Nama dan Email wajib diisi!');
        return;
      }

      let list = getStoredCustomers();
      list.forEach(c => {
        if (c.id === id) {
          c.name = name;
          c.email = email;
          c.city = city;
          c.phone = phone;
        }
      });
      saveStoredCustomers(list);
      cachedCustomers = list;

      const formData = new FormData();
      formData.append('action', 'update_customer');
      formData.append('id', id);
      formData.append('name', name);
      formData.append('email', email);
      formData.append('city', city);
      formData.append('phone', phone);
      fetch(`${API_SERVER}/admin_customers.php`, { method: 'POST', body: formData }).then(() => loadCustomerData()).catch(e => {});

      alert(`BERHASIL: Data Customer '${name}' berhasil diperbarui!`);
      closeEditCustomerModal();
      renderCustomerTable(list);
    }

    function openEditQuotaModal(id, name, quota) {
      const newQuotaStr = prompt(`EDIT ALOKASI KUOTA CCTV\n\nCustomer: ${name}\nMasukkan batas jumlah kuota kamera live yang baru:`, quota);
      if (newQuotaStr === null) return;

      const newQuota = parseInt(newQuotaStr);
      if (isNaN(newQuota) || newQuota < 1) {
        alert('Mohon masukkan jumlah kuota angka valid (minimal 1 CCTV).');
        return;
      }

      let list = getStoredCustomers();
      list.forEach(c => {
        if (c.id === id) {
          c.cctv_quota = newQuota;
        }
      });
      saveStoredCustomers(list);
      cachedCustomers = list;

      const formData = new FormData();
      formData.append('action', 'update_quota');
      formData.append('id', id);
      formData.append('cctv_quota', newQuota);
      fetch(`${API_SERVER}/admin_customers.php`, { method: 'POST', body: formData }).then(() => loadCustomerData()).catch(e => {});

      alert(`BERHASIL: Kuota CCTV untuk '${name}' diperbarui menjadi ${newQuota} CCTV!`);
      renderCustomerTable(list);
    }

    function toggleAddCustPasswordVisibility() {
      const pwdInput = document.getElementById('cust-password');
      const eyeIcon = document.getElementById('toggle-add-cust-password-icon');
      if (!pwdInput || !eyeIcon) return;

      if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
        eyeIcon.style.color = '#00d2ff';
      } else {
        pwdInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
        eyeIcon.style.color = '#94a3b8';
      }
    }

    function resetCustomerPassword(id, name) {
      const newPassword = prompt(`RESET / UBAH PASSWORD CUSTOMER\n\nCustomer: ${name}\nMasukkan Password Baru untuk Customer ini:`);
      if (newPassword === null) return;

      if (!newPassword || newPassword.trim().length < 4) {
        alert('Mohon masukkan password yang valid (minimal 4 karakter).');
        return;
      }

      let list = getStoredCustomers();
      list.forEach(c => {
        if (c.id === id) {
          c.password = newPassword.trim();
        }
      });
      saveStoredCustomers(list);

      const formData = new FormData();
      formData.append('action', 'reset_password');
      formData.append('id', id);
      formData.append('password', newPassword.trim());
      fetch(`${API_SERVER}/admin_customers.php`, { method: 'POST', body: formData }).then(() => loadCustomerData()).catch(e => {});

      alert(`BERHASIL: Password Customer '${name}' telah diubah menjadi '${newPassword.trim()}'!`);
    }

    function toggleStatus(id) {
      let list = getStoredCustomers();
      list.forEach(c => {
        if (c.id === id) {
          c.status = (c.status === 'active') ? 'suspended' : 'active';
          alert(`Status Customer '${c.name}' diubah menjadi ${c.status.toUpperCase()}!`);
        }
      });
      saveStoredCustomers(list);
      cachedCustomers = list;
      renderCustomerTable(list);
    }

    function deleteCustomer(id, name) {
      if (!confirm(`HAPUS PERMANEN: Apakah Anda yakin ingin menghapus akun Customer '${name}'?`)) return;

      let list = getStoredCustomers();
      list = list.filter(c => c.id !== id);
      saveStoredCustomers(list);
      cachedCustomers = list;
      alert(`Customer '${name}' berhasil dihapus!`);
      renderCustomerTable(list);
    }

    function logoutAdmin() {
      localStorage.removeItem('loewix_user');
      window.location.href = '../index.html';
    }

    // ===== CUSTOMER CCTV CHANNEL MANAGER =====
    let currentManagingCustomerId = null;
    let currentCustomerCameras = [];

    function getCustomerCameras(customerId) {
      const stored = localStorage.getItem(`loewix_user_cameras_${customerId}`);
      if (stored) {
        try { return JSON.parse(stored); } catch(e) {}
      }
      return [];
    }

    function saveCustomerCameras(customerId, cameras) {
      localStorage.setItem(`loewix_user_cameras_${customerId}`, JSON.stringify(cameras));
    }

    function syncGlobalCustomCameras(cam) {
      let customList = [];
      try {
        customList = JSON.parse(localStorage.getItem('loewix_custom_cameras') || '[]');
      } catch(e) {}

      customList = customList.filter(c => c.id !== cam.id && c.streamPath !== cam.streamPath);
      customList.push(cam);
      localStorage.setItem('loewix_custom_cameras', JSON.stringify(customList));

      const formData = new FormData();
      formData.append('action', 'admin_add');
      formData.append('user_id', currentManagingCustomerId);
      formData.append('title', cam.title);
      formData.append('city', cam.city || 'siantar');
      formData.append('streamPath', cam.streamPath);
      formData.append('lat', cam.lat || '');
      formData.append('lng', cam.lng || '');
      fetch(`${API_SERVER}/cameras.php`, { method: 'POST', body: formData }).then(() => loadCustomerData()).catch(e => {});
    }

    function openCustomerCCTVModal(customerId) {
      currentManagingCustomerId = customerId;
      const cust = cachedCustomers.find(c => c.id === customerId);
      if (!cust) return;

      document.getElementById('cctv-modal-subtitle').innerText = `Customer: ${cust.name} (${cust.email})`;
      
      const tbody = document.getElementById('cctv-modal-table-body');
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data kamera dari server database...</td></tr>`;
      document.getElementById('cctv-modal-quota-badge').innerHTML = `<i class="fas fa-layer-group mr-1"></i> KUOTA: Memuat... / ${cust.cctv_quota} CCTV TERPAKAI`;

      closeAddCameraForCustomerForm();

      try {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
          $('#modalCustomerCCTV').modal('show');
        } else {
          document.getElementById('modalCustomerCCTV').style.display = 'block';
          document.getElementById('modalCustomerCCTV').classList.add('show');
        }
      } catch(e) {
        document.getElementById('modalCustomerCCTV').style.display = 'block';
        document.getElementById('modalCustomerCCTV').classList.add('show');
      }

      // Fetch cameras from database API
      fetch(`${API_SERVER}/cameras.php?action=list&user_id=${customerId}`)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            currentCustomerCameras = data.cameras || [];
            cust.cctv_used = currentCustomerCameras.length;
            // Sync local storage for offline backward compatibility
            saveCustomerCameras(customerId, currentCustomerCameras);
            renderCustomerCCTVTable(cust, currentCustomerCameras);
          } else {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Gagal memuat data dari server.</td></tr>`;
          }
        })
        .catch(e => {
          tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Gagal menghubungi server.</td></tr>`;
        });
    }

    function closeCustomerCCTVModal() {
      try {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
          $('#modalCustomerCCTV').modal('hide');
        }
      } catch(e) {}
      document.getElementById('modalCustomerCCTV').style.display = 'none';
      document.getElementById('modalCustomerCCTV').classList.remove('show');
      renderCustomerTable(cachedCustomers);
    }

    function renderCustomerCCTVTable(cust, cameras) {
      const tbody = document.getElementById('cctv-modal-table-body');
      tbody.innerHTML = '';

      document.getElementById('cctv-modal-quota-badge').innerHTML = `<i class="fas fa-layer-group mr-1"></i> KUOTA: ${cameras.length} / ${cust.cctv_quota} CCTV TERPAKAI`;

      if (!cameras || cameras.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada kamera CCTV terpasang untuk customer ini. Klik 'Tambah Kamera Baru'.</td></tr>`;
        return;
      }

      cameras.forEach(cam => {
        const cityBadge = `<span class="badge badge-secondary" style="border-radius: 12px; padding: 4px 10px;">📍 ${(cam.city || 'siantar').toUpperCase()}</span>`;
        const gpsCoords = (cam.lat && cam.lng) ? `<code>${cam.lat}, ${cam.lng}</code>` : `<span class="text-muted">-</span>`;
        const streamUrl = cam.streamPath.includes('.m3u8') || cam.streamPath.includes('http')
          ? cam.streamPath
          : `http://stream.loewixcctv.com/${cam.streamPath}/index.m3u8`;

        let streamTypeBadge = '';
        if (cam.connection_type === 'xmeye_p2p') {
          streamTypeBadge = `<span class="badge badge-info mb-1 d-block" style="border-radius: 8px; font-size: 11px; background: rgba(0, 210, 255, 0.15); border: 1px solid rgba(0, 210, 255, 0.3); color: #00d2ff;"><i class="fas fa-qrcode mr-1"></i> XMeye P2P (Ch ${cam.channel || 1})</span>`;
        } else {
          streamTypeBadge = `<span class="badge badge-secondary mb-1 d-block" style="border-radius: 8px; font-size: 11px;"><i class="fas fa-video mr-1"></i> RTSP Direct</span>`;
        }

        const row = `
          <tr>
            <td class="font-weight-bold text-muted">#${cam.id}</td>
            <td class="font-weight-bold text-white">
              <i class="fas fa-video text-info mr-2"></i> ${cam.title}
              ${cam.serial_number ? `<div class="text-muted" style="font-size: 11px; font-family: monospace;">SN: ${cam.serial_number}</div>` : ''}
            </td>
            <td>${cityBadge}</td>
            <td>
              ${streamTypeBadge}
              <code style="font-size: 11px; word-break: break-all;">${cam.streamPath || streamUrl}</code>
            </td>
            <td>${gpsCoords}</td>
            <td><span class="badge badge-success" style="border-radius: 12px; padding: 4px 10px;">● ONLINE MediaMTX</span></td>
            <td class="text-right">
              <div class="action-btn-group">
                <button class="btn btn-outline-warning btn-sm mr-1" onclick="openEditCameraForm(${cam.id})" title="Edit Konfigurasi Kamera Ini">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm" onclick="deleteCustomerCamera(${cam.id})" title="Hapus Channel Kamera Ini">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
        tbody.innerHTML += row;
      });
    }

    // ==========================================
    // XMEYE CLOUD P2P & CONNECTION MODE HANDLERS
    // ==========================================
    function switchCamConnMode(mode, target) {
      target = target || 'add';
      const typeInput = document.getElementById(target === 'add' ? 'cam-input-conn-type' : 'edit-cam-conn-type');
      if (typeInput) typeInput.value = mode;

      const tabXmeye = document.getElementById(`tab-${target}-xmeye`);
      const tabRtsp = document.getElementById(`tab-${target}-rtsp`);
      const secXmeye = document.getElementById(`${target}-xmeye-section`);
      const secRtsp = document.getElementById(`${target}-rtsp-section`);

      if (mode === 'xmeye_p2p') {
        if (tabXmeye) tabXmeye.classList.add('active');
        if (tabRtsp) tabRtsp.classList.remove('active');
        if (secXmeye) secXmeye.style.display = 'block';
        if (secRtsp) secRtsp.style.display = 'none';
      } else {
        if (tabRtsp) tabRtsp.classList.add('active');
        if (tabXmeye) tabXmeye.classList.remove('active');
        if (secRtsp) secRtsp.style.display = 'block';
        if (secXmeye) secXmeye.style.display = 'none';
      }
    }

    function togglePassVisibility(inputId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(`${inputId}-icon`);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
      } else {
        input.type = 'password';
        if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
      }
    }

    // ==========================================
    // QR CODE SCANNER (CAMERA & FILE UPLOAD)
    // ==========================================
    let html5QrScannerInstance = null;
    let currentQRScanTarget = 'add';
    let currentCameraFacing = 'environment';

    function openQRScannerModal(target) {
      currentQRScanTarget = target || 'add';
      $('#modalQRScanner').modal('show');
      const statusBox = document.getElementById('qr-scanner-status');
      if (statusBox) statusBox.innerHTML = '<i class="fas fa-camera mr-1"></i> Mengaktifkan kamera scanner...';
      setTimeout(startQRCameraScanner, 400);
    }

    function closeQRScannerModal() {
      stopQRCameraScanner();
      $('#modalQRScanner').modal('hide');
    }

    function startQRCameraScanner() {
      if (typeof Html5Qrcode === 'undefined') {
        const statusBox = document.getElementById('qr-scanner-status');
        if (statusBox) statusBox.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Modul kamera scanner sedang dimuat, silakan coba upload foto barcode.</span>';
        return;
      }

      const qrBox = document.getElementById('qr-reader');
      if (!qrBox) return;

      stopQRCameraScanner();

      try {
        html5QrScannerInstance = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: { width: 220, height: 220 } };

        html5QrScannerInstance.start(
          { facingMode: currentCameraFacing },
          config,
          (decodedText) => {
            onQRCodeScannedSuccess(decodedText);
          },
          () => {}
        ).then(() => {
          const statusBox = document.getElementById('qr-scanner-status');
          if (statusBox) statusBox.innerHTML = '<span class="text-success"><i class="fas fa-check-circle mr-1"></i> Kamera Aktif! Arahkan ke QR Code di layar DVR / stiker.</span>';
        }).catch(err => {
          console.warn('[QR Scanner Camera Error]', err);
          const statusBox = document.getElementById('qr-scanner-status');
          if (statusBox) statusBox.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle mr-1"></i> Izin kamera belum diberikan. Anda juga bisa klik <strong>Upload Foto QR</strong>.</span>';
        });
      } catch(e) {
        console.error(e);
      }
    }

    function stopQRCameraScanner() {
      if (html5QrScannerInstance) {
        try {
          html5QrScannerInstance.stop().then(() => {
            try { html5QrScannerInstance.clear(); } catch(e) {}
            html5QrScannerInstance = null;
          }).catch(() => {
            html5QrScannerInstance = null;
          });
        } catch(e) {
          html5QrScannerInstance = null;
        }
      }
    }

    function switchCameraFacing() {
      currentCameraFacing = (currentCameraFacing === 'environment') ? 'user' : 'environment';
      startQRCameraScanner();
    }

    function handleQRFileUpload(event) {
      const file = event.target.files && event.target.files[0];
      if (!file) return;

      const statusBox = document.getElementById('qr-scanner-status');
      if (statusBox) statusBox.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Membaca gambar barcode...';

      if (typeof Html5Qrcode === 'undefined') {
        alert('Modul pembaca barcode belum siap.');
        return;
      }

      const html5QrCode = new Html5Qrcode("qr-reader");
      html5QrCode.scanFile(file, true)
        .then(decodedText => {
          onQRCodeScannedSuccess(decodedText);
        })
        .catch(() => {
          if (statusBox) statusBox.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Tidak dapat mendeteksi QR code pada foto ini. Pastikan gambar jelas.</span>';
        });
    }

    function onQRCodeScannedSuccess(decodedText) {
      if (!decodedText) return;
      
      // Clean up scanned text (extract serial if it's a URL or formatted text)
      let cleanSerial = decodedText.trim();
      if (cleanSerial.includes('sn=')) {
        const match = cleanSerial.match(/sn=([a-zA-Z0-9]+)/i);
        if (match) cleanSerial = match[1];
      } else if (cleanSerial.includes('/')) {
        const parts = cleanSerial.split('/');
        cleanSerial = parts[parts.length - 1];
      }
      cleanSerial = cleanSerial.replace(/[^a-zA-Z0-9]/g, '');

      const targetInput = document.getElementById(currentQRScanTarget === 'add' ? 'cam-input-xmeye-serial' : 'edit-cam-xmeye-serial');
      if (targetInput) {
        targetInput.value = cleanSerial;
        targetInput.style.borderColor = '#10b981';
        targetInput.style.boxShadow = '0 0 15px rgba(16, 185, 129, 0.5)';
        setTimeout(() => {
          targetInput.style.borderColor = '';
          targetInput.style.boxShadow = '';
        }, 2500);
      }

      alert(`✅ BERHASIL PINDAI!\nSerial Number XMeye: ${cleanSerial}`);
      closeQRScannerModal();
    }

    function openAddCameraForCustomerForm() {
      const cust = cachedCustomers.find(c => c.id === currentManagingCustomerId);
      if (cust && currentCustomerCameras.length >= cust.cctv_quota) {
        alert(`Batas Kuota Kamera Customer Telah Tercapai (${currentCustomerCameras.length} / ${cust.cctv_quota} CCTV).\n\nSilakan tingkatkan Kuota Customer terlebih dahulu dengan mengklik tombol '⚙️ Kuota'.`);
        return;
      }

      closeEditCameraForm();
      switchCamConnMode('xmeye_p2p', 'add');
      document.getElementById('add-camera-form-box').style.display = 'block';
      document.getElementById('add-camera-form-box').scrollIntoView({ behavior: 'smooth' });
    }

    function closeAddCameraForCustomerForm() {
      document.getElementById('add-camera-form-box').style.display = 'none';
      if (document.getElementById('cam-input-title')) document.getElementById('cam-input-title').value = '';
      if (document.getElementById('cam-input-path')) document.getElementById('cam-input-path').value = '';
      if (document.getElementById('cam-input-xmeye-serial')) document.getElementById('cam-input-xmeye-serial').value = '';
      if (document.getElementById('cam-input-xmeye-pass')) document.getElementById('cam-input-xmeye-pass').value = '';
      if (document.getElementById('cam-input-lat')) document.getElementById('cam-input-lat').value = '';
      if (document.getElementById('cam-input-lng')) document.getElementById('cam-input-lng').value = '';
    }

    function submitAddCameraForCustomer(e) {
      if (e) e.preventDefault();
      const connType = document.getElementById('cam-input-conn-type').value;
      const title = document.getElementById('cam-input-title').value.trim();
      const city = document.getElementById('cam-input-city').value;
      const lat = document.getElementById('cam-input-lat').value.trim();
      const lng = document.getElementById('cam-input-lng').value.trim();

      const cust = cachedCustomers.find(c => c.id === currentManagingCustomerId);
      if (cust && currentCustomerCameras.length >= cust.cctv_quota) {
        alert(`Batas Kuota Kamera Customer Telah Tercapai (${currentCustomerCameras.length} / ${cust.cctv_quota} CCTV).`);
        return;
      }

      const formData = new FormData();
      formData.append('action', 'admin_add');
      formData.append('user_id', currentManagingCustomerId);
      formData.append('title', title);
      formData.append('city', city);
      formData.append('connection_type', connType);
      formData.append('lat', lat);
      formData.append('lng', lng);

      if (connType === 'xmeye_p2p') {
        const serial = document.getElementById('cam-input-xmeye-serial').value.trim();
        const user = document.getElementById('cam-input-xmeye-user').value.trim() || 'admin';
        const pass = document.getElementById('cam-input-xmeye-pass').value.trim();
        const channel = document.getElementById('cam-input-xmeye-channel').value;
        const streamQuality = document.getElementById('cam-input-xmeye-stream').value;

        if (!serial) {
          alert('Mohon isi atau scan Serial Number / Cloud ID XMeye!');
          return;
        }

        formData.append('serial_number', serial);
        formData.append('device_user', user);
        formData.append('device_pass', pass);
        formData.append('channel', channel);
        formData.append('stream_quality', streamQuality);
        formData.append('streamPath', `xmeye_${serial}_ch${channel}`);
      } else {
        const streamPath = document.getElementById('cam-input-path').value.trim();
        if (!streamPath) {
          alert('Mohon isi Stream Path / RTSP URL!');
          return;
        }
        formData.append('streamPath', streamPath);
      }

      fetch(`${API_SERVER}/cameras.php`, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(resData => {
          if (resData.success) {
            alert(`BERHASIL: Kamera '${title}' (${city.toUpperCase()}) ditambahkan ke database server!`);
            closeAddCameraForCustomerForm();
            openCustomerCCTVModal(currentManagingCustomerId);
            loadCustomerData();
          } else {
            alert(`GAGAL: ${resData.message}`);
          }
        })
        .catch(() => alert('Gagal menghubungi server database.'));
    }

    function openEditCameraForm(camId) {
      const cam = currentCustomerCameras.find(c => c.id === camId);
      if (!cam) return;

      closeAddCameraForCustomerForm();
      document.getElementById('edit-cam-id').value = cam.id;
      document.getElementById('edit-cam-title').value = cam.title || '';
      document.getElementById('edit-cam-city').value = cam.city || 'siantar';
      document.getElementById('edit-cam-path').value = cam.streamPath || '';
      document.getElementById('edit-cam-lat').value = cam.lat || '';
      document.getElementById('edit-cam-lng').value = cam.lng || '';

      const connType = cam.connection_type || (cam.serial_number ? 'xmeye_p2p' : 'rtsp');
      switchCamConnMode(connType, 'edit');

      if (document.getElementById('edit-cam-xmeye-serial')) document.getElementById('edit-cam-xmeye-serial').value = cam.serial_number || '';
      if (document.getElementById('edit-cam-xmeye-user')) document.getElementById('edit-cam-xmeye-user').value = cam.device_user || 'admin';
      if (document.getElementById('edit-cam-xmeye-channel')) document.getElementById('edit-cam-xmeye-channel').value = cam.channel || '1';
      if (document.getElementById('edit-cam-xmeye-stream')) document.getElementById('edit-cam-xmeye-stream').value = cam.stream_quality || 'sub';

      document.getElementById('edit-camera-form-box').style.display = 'block';
      document.getElementById('edit-camera-form-box').scrollIntoView({ behavior: 'smooth' });
    }

    function closeEditCameraForm() {
      document.getElementById('edit-camera-form-box').style.display = 'none';
    }

    function submitEditCameraForCustomer(e) {
      if (e) e.preventDefault();
      const camId = parseInt(document.getElementById('edit-cam-id').value);
      const connType = document.getElementById('edit-cam-conn-type').value;
      const title = document.getElementById('edit-cam-title').value.trim();
      const city = document.getElementById('edit-cam-city').value;
      const lat = document.getElementById('edit-cam-lat').value.trim();
      const lng = document.getElementById('edit-cam-lng').value.trim();

      const formData = new FormData();
      formData.append('action', 'admin_edit');
      formData.append('id', camId);
      formData.append('title', title);
      formData.append('city', city);
      formData.append('connection_type', connType);
      formData.append('lat', lat);
      formData.append('lng', lng);

      if (connType === 'xmeye_p2p') {
        const serial = document.getElementById('edit-cam-xmeye-serial').value.trim();
        const user = document.getElementById('edit-cam-xmeye-user').value.trim() || 'admin';
        const pass = document.getElementById('edit-cam-xmeye-pass').value.trim();
        const channel = document.getElementById('edit-cam-xmeye-channel').value;
        const streamQuality = document.getElementById('edit-cam-xmeye-stream').value;

        if (!serial) {
          alert('Mohon isi atau scan Serial Number / Cloud ID XMeye!');
          return;
        }

        formData.append('serial_number', serial);
        formData.append('device_user', user);
        if (pass) formData.append('device_pass', pass);
        formData.append('channel', channel);
        formData.append('stream_quality', streamQuality);
        formData.append('streamPath', `xmeye_${serial}_ch${channel}`);
      } else {
        const streamPath = document.getElementById('edit-cam-path').value.trim();
        if (!streamPath) {
          alert('Mohon isi Stream Path / RTSP URL!');
          return;
        }
        formData.append('streamPath', streamPath);
      }

      fetch(`${API_SERVER}/cameras.php`, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(resData => {
          if (resData.success) {
            alert(`BERHASIL: Konfigurasi Kamera '${title}' berhasil diperbarui!`);
            closeEditCameraForm();
            openCustomerCCTVModal(currentManagingCustomerId);
          } else {
            alert(`GAGAL: ${resData.message}`);
          }
        })
        .catch(() => alert('Gagal menghubungi server database.'));
    }

    function deleteCustomerCamera(camId) {
      if (!confirm('Apakah Anda yakin ingin menghapus channel kamera CCTV ini?')) return;

      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('id', camId);

      fetch(`${API_SERVER}/cameras.php`, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(resData => {
          if (resData.success) {
            alert('Channel Kamera berhasil dihapus dari database server!');
            openCustomerCCTVModal(currentManagingCustomerId);
            loadCustomerData();
          } else {
            alert(`GAGAL: ${resData.message}`);
          }
        })
        .catch(() => alert('Gagal menghubungi server database.'));
    }

    // ==========================================
    // MASTER DATA WILAYAH & KOTA MANAGEMENT
    // ==========================================
    let adminCitiesList = [];

    function loadAdminCities() {
      fetch(`${API_SERVER}/cities.php`)
        .then(res => res.json())
        .then(data => {
          if (data.success && Array.isArray(data.cities)) {
            adminCitiesList = data.cities;
            renderAdminCitiesDropdowns();
            renderAdminCitiesTable();
          }
        })
        .catch(() => {});
    }

    function renderAdminCitiesDropdowns() {
      const filterSel = document.getElementById('filter-city-select');
      if (filterSel) {
        const curVal = filterSel.value;
        let html = '<option value="all" style="color:#000;">🌐 Semua Wilayah</option>';
        adminCitiesList.forEach(c => {
          html += `<option value="${c.id}" style="color:#000;">📍 ${c.name}</option>`;
        });
        filterSel.innerHTML = html;
        if (curVal) filterSel.value = curVal;
      }

      const selects = ['cust-city', 'edit-profile-city', 'cam-input-city', 'edit-cam-city'];
      selects.forEach(id => {
        const sel = document.getElementById(id);
        if (sel) {
          const val = sel.value;
          let html = '';
          adminCitiesList.forEach(c => {
            html += `<option value="${c.id}" style="color:#000;">${c.name}</option>`;
          });
          sel.innerHTML = html;
          if (val) sel.value = val;
        }
      });
    }

    function renderAdminCitiesTable() {
      const tbody = document.getElementById('cities-table-body');
      const badge = document.getElementById('cities-count-badge');
      if (!tbody) return;

      if (badge) {
        badge.innerHTML = `<i class="fas fa-city mr-1"></i> TOTAL: ${adminCitiesList.length} WILAYAH TERDAFTAR`;
      }

      if (adminCitiesList.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data wilayah. Silakan klik 'Tambah Wilayah Baru'.</td></tr>`;
        return;
      }

      let html = '';
      adminCitiesList.forEach(c => {
        html += `
          <tr>
            <td><span class="badge badge-secondary" style="border-radius: 12px; padding: 4px 10px; font-weight: 700;">${c.id}</span></td>
            <td class="font-weight-bold text-white"><i class="fas fa-map-marker-alt text-danger mr-1"></i> ${c.name}</td>
            <td class="text-info font-family-monospace" style="font-size: 13px;">${c.lat}, ${c.lng}</td>
            <td><span class="badge badge-info" style="border-radius: 12px; padding: 4px 8px;">Zoom ${c.zoom || 12}</span></td>
            <td class="text-right">
              <button class="act-btn act-btn-edit mr-1" onclick="openEditCityForm('${c.id}')" title="Edit Data Wilayah">
                <i class="fas fa-edit"></i>
              </button>
              <button class="act-btn act-btn-delete" onclick="deleteAdminCity('${c.id}')" title="Hapus Wilayah">
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
        `;
      });
      tbody.innerHTML = html;
    }

    function openCityManagementModal() {
      const modal = document.getElementById('modalCityManagement');
      if (modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        
        let backdrop = document.getElementById('custom-city-backdrop');
        if (!backdrop) {
          backdrop = document.createElement('div');
          backdrop.id = 'custom-city-backdrop';
          backdrop.className = 'modal-backdrop fade show';
          backdrop.onclick = closeCityManagementModal;
          document.body.appendChild(backdrop);
        }
      }
      if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        try { $('#modalCityManagement').modal('show'); } catch(e) {}
      }
      loadAdminCities();
      toggleAddCityForm(false);
    }

    function closeCityManagementModal() {
      const modal = document.getElementById('modalCityManagement');
      if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        const backdrop = document.getElementById('custom-city-backdrop');
        if (backdrop) backdrop.remove();
      }
      if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        try { $('#modalCityManagement').modal('hide'); } catch(e) {}
      }
    }

    function toggleAddCityForm(show) {
      const box = document.getElementById('city-form-box');
      if (!box) return;
      if (show === undefined) {
        show = box.style.display === 'none';
      }
      box.style.display = show ? 'block' : 'none';
      if (show) {
        document.getElementById('city-form-action').value = 'add';
        document.getElementById('city-form-title').innerHTML = '<i class="fas fa-plus-circle mr-1"></i> TAMBAH WILAYAH / KOTA BARU';
        document.getElementById('city-input-name').value = '';
        document.getElementById('city-input-id').value = '';
        document.getElementById('city-input-id').readOnly = false;
        document.getElementById('city-input-lat').value = '';
        document.getElementById('city-input-lng').value = '';
        document.getElementById('city-input-zoom').value = 12;
      }
    }

    function autoGenerateCitySlug() {
      const action = document.getElementById('city-form-action').value;
      if (action !== 'add') return;
      const name = document.getElementById('city-input-name').value;
      const slug = name.toLowerCase().replace(/kota|kabupaten|provinsi/g, '').trim().replace(/[^a-z0-9]/g, '');
      if (slug) {
        document.getElementById('city-input-id').value = slug;
      }
    }

    function openEditCityForm(id) {
      const city = adminCitiesList.find(c => c.id === id);
      if (!city) return;

      const box = document.getElementById('city-form-box');
      if (box) box.style.display = 'block';

      document.getElementById('city-form-action').value = 'edit';
      document.getElementById('city-form-title').innerHTML = `<i class="fas fa-edit mr-1 text-warning"></i> EDIT WILAYAH: ${city.name.toUpperCase()}`;
      document.getElementById('city-input-name').value = city.name;
      document.getElementById('city-input-id').value = city.id;
      document.getElementById('city-input-id').readOnly = true;
      document.getElementById('city-input-lat').value = city.lat;
      document.getElementById('city-input-lng').value = city.lng;
      document.getElementById('city-input-zoom').value = city.zoom || 12;

      box.scrollIntoView({ behavior: 'smooth' });
    }

    function submitSaveCity(e) {
      e.preventDefault();
      const action = document.getElementById('city-form-action').value;
      const name = document.getElementById('city-input-name').value.trim();
      const id = document.getElementById('city-input-id').value.trim().toLowerCase();
      const lat = parseFloat(document.getElementById('city-input-lat').value);
      const lng = parseFloat(document.getElementById('city-input-lng').value);
      const zoom = parseInt(document.getElementById('city-input-zoom').value) || 12;

      if (!name || !id || isNaN(lat) || isNaN(lng)) {
        alert('Mohon isi semua data nama, ID, dan koordinat GPS dengan benar!');
        return;
      }

      const formData = new FormData();
      formData.append('action', action);
      formData.append('id', id);
      formData.append('name', name);
      formData.append('lat', lat);
      formData.append('lng', lng);
      formData.append('zoom', zoom);

      fetch(`${API_SERVER}/cities.php`, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(resData => {
          if (resData.success) {
            alert(`BERHASIL: ${resData.message}`);
            toggleAddCityForm(false);
            loadAdminCities();
          } else {
            alert(`GAGAL: ${resData.message}`);
          }
        })
        .catch(() => alert('Gagal menghubungi server API wilayah.'));
    }

    function deleteAdminCity(id) {
      const city = adminCitiesList.find(c => c.id === id);
      const cityName = city ? city.name : id;
      if (!confirm(`Apakah Anda yakin ingin menghapus data wilayah '${cityName}'?`)) return;

      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('id', id);

      fetch(`${API_SERVER}/cities.php`, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(resData => {
          if (resData.success) {
            alert(resData.message);
            loadAdminCities();
          } else {
            alert(`GAGAL: ${resData.message}`);
          }
        })
        .catch(() => alert('Gagal menghubungi server API wilayah.'));
    }

    document.addEventListener('DOMContentLoaded', () => {
      loadCustomerData();
      loadAdminCities();
    });

    window.openAddCustomerModal = openAddCustomerModal;
    window.closeAddCustomerModal = closeAddCustomerModal;
    window.submitAddCustomer = submitAddCustomer;
    window.openEditCustomerModal = openEditCustomerModal;
    window.closeEditCustomerModal = closeEditCustomerModal;
    window.submitEditCustomer = submitEditCustomer;
    window.openEditQuotaModal = openEditQuotaModal;
    window.resetCustomerPassword = resetCustomerPassword;
    window.toggleAddCustPasswordVisibility = toggleAddCustPasswordVisibility;
    window.toggleStatus = toggleStatus;
    window.deleteCustomer = deleteCustomer;
    window.exportCustomerCSV = exportCustomerCSV;
    window.filterCustomerTable = filterCustomerTable;
    window.openCustomerCCTVModal = openCustomerCCTVModal;
    window.closeCustomerCCTVModal = closeCustomerCCTVModal;
    window.openAddCameraForCustomerForm = openAddCameraForCustomerForm;
    window.closeAddCameraForCustomerForm = closeAddCameraForCustomerForm;
    window.submitAddCameraForCustomer = submitAddCameraForCustomer;
    window.openEditCameraForm = openEditCameraForm;
    window.closeEditCameraForm = closeEditCameraForm;
    window.submitEditCameraForCustomer = submitEditCameraForCustomer;
    window.deleteCustomerCamera = deleteCustomerCamera;
    window.switchCamConnMode = switchCamConnMode;
    window.togglePassVisibility = togglePassVisibility;
    window.openQRScannerModal = openQRScannerModal;
    window.closeQRScannerModal = closeQRScannerModal;
    window.switchCameraFacing = switchCameraFacing;
    window.handleQRFileUpload = handleQRFileUpload;
    window.openCityManagementModal = openCityManagementModal;
    window.closeCityManagementModal = closeCityManagementModal;
    window.toggleAddCityForm = toggleAddCityForm;
    window.autoGenerateCitySlug = autoGenerateCitySlug;
    window.openEditCityForm = openEditCityForm;
    window.submitSaveCity = submitSaveCity;
    window.deleteAdminCity = deleteAdminCity;
    window.logoutAdmin = logoutAdmin;
  </script>
</body>
</html>
