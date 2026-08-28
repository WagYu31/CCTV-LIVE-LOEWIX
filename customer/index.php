<?php
/**
 * Customer Self-Service Portal & VMS Dashboard
 * PT. LOEWIX INDONESIA
 */
require_once __DIR__ . '/../config/db.php';
$user = get_logged_in_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Control Hub - PT. LOEWIX INDONESIA</title>
  <link rel="stylesheet" href="../assets/bootstarp/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
  <style>
    :root {
      --bg-dark: #060b18;
      --panel-bg: rgba(13, 24, 54, 0.75);
      --panel-bg-hover: rgba(18, 33, 74, 0.9);
      --border-color: rgba(255, 255, 255, 0.1);
      --border-glow: rgba(56, 189, 248, 0.3);
      --primary-cyan: #38bdf8;
      --primary-blue: #0284c7;
      --accent-emerald: #10b981;
      --accent-amber: #f59e0b;
      --accent-rose: #ef4444;
      --text-main: #ffffff;
      --text-muted: #94a3b8;
    }
    
    * { box-sizing: border-box; }
    
    body {
      background-color: var(--bg-dark);
      background-image: 
        radial-gradient(circle at 15% 10%, rgba(56, 189, 248, 0.12) 0%, transparent 45%),
        radial-gradient(circle at 85% 15%, rgba(99, 102, 241, 0.12) 0%, transparent 45%),
        radial-gradient(circle at 50% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
        linear-gradient(180deg, #070d1e 0%, #050814 100%);
      background-attachment: fixed;
      font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
      color: var(--text-main);
      min-height: 100vh;
      padding-bottom: 60px;
      margin: 0;
    }

    /* Custom Futuristic Scrollbar */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: #060b18; }
    ::-webkit-scrollbar-thumb { background: rgba(56, 189, 248, 0.25); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(56, 189, 248, 0.5); }

    /* Top Floating Glass Header */
    .customer-navbar {
      background: rgba(10, 20, 48, 0.82);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border-color);
      padding: 12px 0;
      position: sticky;
      top: 0;
      z-index: 1030;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.45);
    }

    .brand-logo-container {
      display: flex;
      align-items: center;
      gap: 14px;
      text-decoration: none !important;
    }

    .badge-hub-live {
      background: rgba(56, 189, 248, 0.12);
      color: #38bdf8;
      border: 1px solid rgba(56, 189, 248, 0.3);
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.8px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .pulse-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #10b981;
      box-shadow: 0 0 10px #10b981;
      animation: pulseGlow 1.8s infinite;
    }

    @keyframes pulseGlow {
      0% { transform: scale(0.9); opacity: 0.7; }
      50% { transform: scale(1.3); opacity: 1; box-shadow: 0 0 14px #10b981; }
      100% { transform: scale(0.9); opacity: 0.7; }
    }

    .user-profile-pill {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.12);
      padding: 4px 12px 4px 6px;
      border-radius: 25px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      font-weight: 700;
      color: #e2e8f0;
      cursor: pointer;
      transition: all 0.2s;
    }

    .user-profile-pill:hover {
      background: rgba(56, 189, 248, 0.15);
      border-color: #38bdf8;
    }

    .user-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0284c7, #38bdf8);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 12px;
      box-shadow: 0 0 10px rgba(56, 189, 248, 0.4);
    }

    .btn-vms-link {
      background: rgba(56, 189, 248, 0.12);
      border: 1px solid rgba(56, 189, 248, 0.35);
      color: #38bdf8;
      border-radius: 20px;
      padding: 6px 16px;
      font-size: 12px;
      font-weight: 700;
      transition: all 0.25s ease;
      text-decoration: none !important;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .btn-vms-link:hover {
      background: #38bdf8;
      color: #091650;
      box-shadow: 0 0 16px rgba(56, 189, 248, 0.5);
      transform: translateY(-1px);
    }

    /* Glass Cards */
    .glass-card {
      background: var(--panel-bg);
      border: 1px solid var(--border-color);
      border-radius: 18px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      padding: 24px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .glass-card:hover {
      border-color: var(--border-glow);
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5), 0 0 20px rgba(56, 189, 248, 0.15);
    }

    /* Quota Hero Widget */
    .quota-hero-banner {
      background: linear-gradient(135deg, rgba(13, 27, 62, 0.95), rgba(8, 47, 73, 0.85));
      border: 1px solid rgba(56, 189, 248, 0.35);
      border-radius: 20px;
      padding: 26px 30px;
      margin-top: 25px;
      margin-bottom: 28px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(16px);
    }

    .quota-progress-track {
      height: 12px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      overflow: hidden;
      margin: 14px 0 8px 0;
      position: relative;
    }

    .quota-progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #0284c7, #38bdf8, #10b981);
      border-radius: 10px;
      transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 0 12px rgba(56, 189, 248, 0.6);
    }

    /* Metric Cards */
    .metric-card {
      background: rgba(13, 27, 62, 0.65);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 16px;
      transition: all 0.25s ease;
    }

    .metric-card:hover {
      background: rgba(13, 27, 62, 0.9);
      border-color: rgba(56, 189, 248, 0.35);
      transform: translateY(-2px);
    }

    .metric-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }

    .metric-icon.cyan { background: rgba(56, 189, 248, 0.15); color: #38bdf8; }
    .metric-icon.emerald { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .metric-icon.amber { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
    .metric-icon.purple { background: rgba(139, 92, 246, 0.15); color: #a78bfa; }

    .metric-value {
      font-size: 24px;
      font-weight: 800;
      color: #ffffff;
      line-height: 1.2;
    }

    .metric-label {
      font-size: 12px;
      color: #94a3b8;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Action & Filter Toolbar */
    .customer-toolbar {
      background: rgba(13, 27, 62, 0.85);
      border: 1px solid rgba(56, 189, 248, 0.25);
      border-radius: 16px;
      padding: 14px 20px;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      backdrop-filter: blur(14px);
    }

    .btn-add-cam {
      background: linear-gradient(135deg, #0284c7, #38bdf8);
      color: #ffffff;
      border: none;
      padding: 9px 20px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-add-cam:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(56, 189, 248, 0.5);
      color: #ffffff;
    }

    .search-input-box {
      position: relative;
      flex: 1;
      min-width: 220px;
      max-width: 320px;
    }

    .search-input-box input {
      width: 100%;
      height: 38px;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 10px;
      padding: 0 12px 0 34px;
      color: #ffffff;
      font-size: 13px;
      outline: none;
      transition: all 0.2s;
    }

    .search-input-box input:focus {
      border-color: #38bdf8;
      background: rgba(255, 255, 255, 0.1);
      box-shadow: 0 0 10px rgba(56, 189, 248, 0.3);
    }

    .search-input-box i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #38bdf8;
      font-size: 13px;
    }

    .filter-select-pill {
      height: 38px;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 10px;
      color: #ffffff;
      padding: 0 12px;
      font-size: 13px;
      font-weight: 600;
      outline: none;
      cursor: pointer;
    }

    .filter-select-pill option {
      background: #0f172a;
      color: #ffffff;
    }

    /* Camera Cards Grid */
    .cam-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px;
    }

    .cam-card {
      background: rgba(13, 27, 62, 0.75);
      border: 1px solid rgba(255, 255, 255, 0.09);
      border-radius: 16px;
      overflow: hidden;
      transition: all 0.25s ease;
      display: flex;
      flex-direction: column;
    }

    .cam-card:hover {
      border-color: rgba(56, 189, 248, 0.4);
      transform: translateY(-3px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.45);
    }

    .cam-preview-container {
      position: relative;
      width: 100%;
      height: 190px;
      background: #000000;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .cam-preview-container .play-overlay-hint {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(0, 0, 0, 0.25);
      opacity: 0;
      transition: opacity 0.2s ease;
      z-index: 5;
      pointer-events: none;
    }

    .cam-preview-container:hover .play-overlay-hint {
      opacity: 1;
    }

    .play-overlay-hint i {
      font-size: 40px;
      color: rgba(56, 189, 248, 0.95);
      filter: drop-shadow(0 0 12px rgba(56, 189, 248, 0.7));
      transform: scale(0.9);
      transition: transform 0.2s ease;
    }

    .cam-preview-container:hover .play-overlay-hint i {
      transform: scale(1.1);
    }

    .cam-inline-video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: contain;
      background: #000;
      z-index: 4;
    }

    .cam-inline-loading {
      position: absolute;
      inset: 0;
      background: rgba(10, 18, 36, 0.88);
      backdrop-filter: blur(4px);
      z-index: 6;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #38bdf8;
      text-align: center;
      padding: 10px;
    }

    .btn-playing-active {
      background: rgba(239, 68, 68, 0.25) !important;
      border-color: rgba(239, 68, 68, 0.7) !important;
      color: #fca5a5 !important;
      box-shadow: 0 0 10px rgba(239, 68, 68, 0.4) !important;
    }

    .cam-preview-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .cam-card:hover .cam-preview-img {
      transform: scale(1.03);
    }

    .cam-badge-status {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(15, 23, 42, 0.85);
      border: 1px solid rgba(16, 185, 129, 0.4);
      color: #34d399;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      backdrop-filter: blur(8px);
    }

    .cam-badge-status.offline {
      border-color: rgba(239, 68, 68, 0.4);
      color: #f87171;
    }

    .cam-badge-type {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(15, 23, 42, 0.85);
      border: 1px solid rgba(56, 189, 248, 0.3);
      color: #38bdf8;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: 0.5px;
      backdrop-filter: blur(8px);
    }

    .cam-info-body {
      padding: 16px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .cam-title {
      font-size: 15px;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 6px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .cam-meta {
      font-size: 12px;
      color: #94a3b8;
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 12px;
    }

    .cam-actions-row {
      display: flex;
      align-items: center;
      gap: 8px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      padding-top: 12px;
    }

    .btn-cam-action {
      flex: 1;
      height: 32px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 8px;
      color: #ffffff;
      font-size: 12px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-cam-action:hover {
      background: rgba(56, 189, 248, 0.2);
      border-color: #38bdf8;
      color: #38bdf8;
    }

    .btn-cam-action.danger:hover {
      background: rgba(239, 68, 68, 0.2);
      border-color: #ef4444;
      color: #ef4444;
    }

    /* Modal Form Dark Theme */
    .modal-dark .modal-content {
      background: #0d1b3e;
      border: 1px solid rgba(56, 189, 248, 0.35);
      border-radius: 18px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75);
      color: #ffffff;
    }

    .modal-dark .modal-header {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding: 18px 24px;
    }

    .modal-dark .modal-footer {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding: 16px 24px;
    }

    .form-group-dark label {
      font-size: 13px;
      font-weight: 600;
      color: #94a3b8;
      margin-bottom: 6px;
    }

    .form-control-dark {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 10px;
      color: #ffffff !important;
      padding: 10px 14px;
      font-size: 13.5px;
      outline: none;
    }

    .form-control-dark:focus {
      background: rgba(255, 255, 255, 0.1);
      border-color: #38bdf8;
      box-shadow: 0 0 10px rgba(56, 189, 248, 0.35);
    }

    /* Empty state */
    .empty-state-box {
      text-align: center;
      padding: 60px 20px;
      background: rgba(13, 27, 62, 0.5);
      border: 2px dashed rgba(255, 255, 255, 0.12);
      border-radius: 18px;
      grid-column: 1 / -1;
    }

    .empty-state-icon {
      font-size: 48px;
      color: #38bdf8;
      margin-bottom: 16px;
      opacity: 0.8;
    }
  </style>
</head>
<body>

  <!-- Top Glass Header -->
  <nav class="customer-navbar">
    <div class="container-fluid px-lg-5">
      <div class="d-flex align-items-center justify-content-between">
        
        <!-- Logo Brand & Portal Badge -->
        <a href="../index.php" class="brand-logo-container">
          <img src="../assets/image/logo.png" alt="Loewix CCTV" height="36">
          <span class="badge-hub-live d-none d-sm-inline-flex">
            <span class="pulse-dot"></span>
            <span>CUSTOMER CONTROL HUB</span>
          </span>
        </a>

        <!-- Right User Actions -->
        <div class="d-flex align-items-center gap-3">
          
          <a href="../index.php" class="btn-vms-link" title="Buka Tampilan Live Matrix Grid VMS">
            <i class="fas fa-th-large"></i> <span class="d-none d-md-inline">LIVE VMS GRID</span>
          </a>

          <!-- Profile Pill -->
          <div class="user-profile-pill" onclick="openProfileSettingsModal()" title="Pengaturan Akun & Password">
            <div class="user-avatar" id="nav-user-avatar">
              <i class="fas fa-user"></i>
            </div>
            <span id="nav-user-name" class="d-none d-sm-inline">Customer Loewix</span>
            <i class="fas fa-cog text-muted" style="font-size: 11px;"></i>
          </div>

          <!-- Logout Button -->
          <button class="btn btn-sm" onclick="logoutCustomer()" style="background: rgba(239, 68, 68, 0.18); border: 1px solid rgba(239, 68, 68, 0.4); color: #ef4444; border-radius: 20px; padding: 6px 14px; font-weight: 700; font-size: 12px;" title="Keluar dari Akun">
            <i class="fas fa-sign-out-alt"></i> <span class="d-none d-md-inline">Logout</span>
          </button>

        </div>

      </div>
    </div>
  </nav>

  <!-- Main Content Container -->
  <div class="container-fluid px-lg-5 py-4">

    <!-- Quota & Metrics Hero Banner -->
    <div class="quota-hero-banner">
      <div class="row align-items-center">
        
        <div class="col-lg-7 mb-4 mb-lg-0">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge badge-primary px-3 py-1 font-weight-bold" style="background: #0284c7; border-radius: 20px; font-size: 11px; letter-spacing: 0.5px;">
              <i class="fas fa-shield-alt mr-1"></i> ENTERPRISE VIP TIER
            </span>
            <span class="text-muted" style="font-size: 12px;" id="hero-customer-city">📍 Pematangsiantar</span>
          </div>
          <h2 class="font-weight-extrabold mb-1" style="font-size: 26px;" id="hero-customer-name">
            PT. Jaya Sentosa Enterprise
          </h2>
          <p class="text-muted mb-3" style="font-size: 13.5px;">
            Pusat kendali kamera pengawas CCTV multi-channel terisolasi & terenkripsi cloud Loewix.
          </p>

          <!-- Quota Progress Meter -->
          <div class="d-flex justify-content-between align-items-center font-weight-bold" style="font-size: 13px;">
            <span style="color: #38bdf8;"><i class="fas fa-layer-group mr-1"></i> Kuota Kamera Terpakai:</span>
            <span id="hero-quota-text" style="color: #ffffff;">0 / 20 Kamera (0%)</span>
          </div>
          <div class="quota-progress-track">
            <div class="quota-progress-fill" id="hero-quota-bar" style="width: 0%;"></div>
          </div>
          <div class="d-flex justify-content-between text-muted" style="font-size: 11.5px;">
            <span>Terpasang: <strong id="hero-used-count" class="text-white">0</strong> CCTV</span>
            <span>Tersisa: <strong id="hero-remaining-count" class="text-emerald" style="color: #34d399;">0</strong> Slot</span>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="row g-3">
            <div class="col-6">
              <div class="metric-card">
                <div class="metric-icon cyan">
                  <i class="fas fa-video"></i>
                </div>
                <div>
                  <div class="metric-value" id="card-total-cam">0</div>
                  <div class="metric-label">Total CCTV</div>
                </div>
              </div>
            </div>

            <div class="col-6">
              <div class="metric-card">
                <div class="metric-icon emerald">
                  <i class="fas fa-wifi"></i>
                </div>
                <div>
                  <div class="metric-value" id="card-online-cam">0</div>
                  <div class="metric-label">Live Online</div>
                </div>
              </div>
            </div>

            <div class="col-6 mt-3">
              <div class="metric-card">
                <div class="metric-icon amber">
                  <i class="fas fa-hdd"></i>
                </div>
                <div>
                  <div class="metric-value" id="card-quota-max">20</div>
                  <div class="metric-label">Max Kuota</div>
                </div>
              </div>
            </div>

            <div class="col-6 mt-3">
              <div class="metric-card" onclick="openRequestUpgradeModal()" style="cursor: pointer; border-color: rgba(56, 189, 248, 0.3);" title="Klik untuk Upgrade Kuota">
                <div class="metric-icon purple">
                  <i class="fas fa-arrow-circle-up"></i>
                </div>
                <div>
                  <div class="metric-value" style="font-size: 15px; color: #a78bfa;">UPGRADE</div>
                  <div class="metric-label">Tambah Kuota</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Section Header & Toolbar -->
    <div class="customer-toolbar">
      
      <!-- Left: Title & Add Button -->
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <h4 class="font-weight-bold mb-0 text-white" style="font-size: 18px; display: inline-flex; align-items: center; gap: 8px;">
          <i class="fas fa-camera text-info"></i> Daftar Channel CCTV Saya
        </h4>
        <button class="btn-add-cam" onclick="openAddCameraModal()">
          <i class="fas fa-plus"></i> Tambah Kamera CCTV
        </button>
      </div>

      <!-- Right: Search & Filters -->
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="search-input-box">
          <i class="fas fa-search"></i>
          <input type="text" id="filter-search-input" placeholder="Cari nama kamera / lokasi..." onkeyup="applyCameraFilters()">
        </div>

        <select class="filter-select-pill" id="filter-city-select" onchange="applyCameraFilters()">
          <option value="all">🌐 Semua Wilayah</option>
          <option value="siantar">📍 Pematangsiantar</option>
          <option value="jakarta">📍 DKI Jakarta</option>
          <option value="medan">📍 Kota Medan</option>
          <option value="bandung">📍 Kota Bandung</option>
          <option value="bali">📍 Bali / Denpasar</option>
        </select>

        <select class="filter-select-pill" id="filter-status-select" onchange="applyCameraFilters()">
          <option value="all">Semua Status</option>
          <option value="online">Online</option>
          <option value="offline">Offline</option>
        </select>
      </div>

    </div>

    <!-- Cameras Grid List -->
    <div class="cam-grid" id="customer-camera-grid">
      <!-- Loading Skeleton Placeholder -->
      <div class="empty-state-box">
        <div class="spinner-border text-info mb-3" role="status"></div>
        <h5 class="text-white">Memuat Channel Kamera CCTV Anda...</h5>
        <p class="text-muted mb-0">Menghubungkan ke secure streaming gateway Loewix.</p>
      </div>
    </div>

  </div>

  <!-- ===== MODAL TAMBAH / EDIT KAMERA CCTV ===== -->
  <div class="modal fade modal-dark" id="modalCamForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold text-white" id="camModalTitle">
            <i class="fas fa-video text-info mr-2"></i> Tambah Kamera CCTV
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="$('#modalCamForm').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCamCustomer" onsubmit="submitCustomerCamera(event)">
          <div class="modal-body px-4 py-3">
            <input type="hidden" id="cust-cam-id" value="0">
            
            <div class="form-group form-group-dark">
              <label>Nama Kamera / Lokasi:</label>
              <input type="text" id="cust-cam-title" class="form-control form-control-dark" placeholder="Contoh: Depan Gudang Utama" required>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6 form-group-dark">
                <label>Wilayah / Kota:</label>
                <select id="cust-cam-city" class="form-control form-control-dark">
                  <option value="siantar">Pematangsiantar</option>
                  <option value="jakarta">DKI Jakarta</option>
                  <option value="medan">Kota Medan</option>
                  <option value="bandung">Kota Bandung</option>
                  <option value="bali">Bali / Denpasar</option>
                </select>
              </div>
              <div class="form-group col-md-6 form-group-dark">
                <label>Tipe Koneksi Stream:</label>
                <select id="cust-cam-conn-type" class="form-control form-control-dark" onchange="toggleConnFields()">
                  <option value="rtsp">RTSP Stream (MediaMTX)</option>
                  <option value="xmeye_p2p">XMeye P2P (Serial Cloud)</option>
                  <option value="ipcamlive">IPCamLive</option>
                </select>
              </div>
            </div>

            <!-- RTSP Stream Path Fields -->
            <div id="field-rtsp-container">
              <div class="form-group form-group-dark">
                <label>Stream Path / URL HLS:</label>
                <input type="text" id="cust-cam-hls" class="form-control form-control-dark" placeholder="https://stream.loewixcctv.com:8443/cam-01/index.m3u8">
              </div>
              <div class="form-group form-group-dark">
                <label>RTSP Direct Source (Opsional):</label>
                <input type="text" id="cust-cam-rtsp" class="form-control form-control-dark" placeholder="rtsp://admin:pass@192.168.1.100:554/stream1">
              </div>
            </div>

            <!-- XMeye P2P Fields -->
            <div id="field-xmeye-container" style="display: none;">
              <div class="form-group form-group-dark">
                <label>Serial Number (Cloud ID):</label>
                <input type="text" id="cust-cam-sn" class="form-control form-control-dark" placeholder="Contoh: 9ea232cf5188d0da">
              </div>
              <div class="form-group form-group-dark">
                <label>Channel NVR / DVR:</label>
                <input type="number" id="cust-cam-channel" class="form-control form-control-dark" value="1" min="1" max="128">
              </div>
            </div>

            <div class="form-group form-group-dark mb-0">
              <label>Status Siaran:</label>
              <select id="cust-cam-status" class="form-control form-control-dark">
                <option value="online">Online (Aktif Live)</option>
                <option value="offline">Offline (Nonaktif)</option>
              </select>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="$('#modalCamForm').modal('hide')">Batal</button>
            <button type="submit" class="btn btn-info btn-sm font-weight-bold px-3" style="background: #0284c7; border: none;">
              <i class="fas fa-save mr-1"></i> Simpan Kamera
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ===== MODAL LIVE STREAM TEST PLAYER ===== -->
  <div class="modal fade modal-dark" id="modalLivePlayer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold text-white" id="playerModalTitle">
            <i class="fas fa-play-circle text-info mr-2"></i> Live Preview
          </h5>
          <button type="button" class="close text-white" onclick="closeLivePlayerModal()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0" style="background: #000; position: relative; min-height: 380px; display: flex; align-items: center; justify-content: center;">
          <video id="customer-preview-video" style="width: 100%; height: 100%; max-height: 480px; background: #000;" controls autoplay muted playsinline></video>
          <div id="player-loading-spinner" style="position: absolute; display: none; color: #38bdf8; text-align: center;">
            <div class="spinner-border mb-2" role="status"></div>
            <div>Menghubungkan ke Stream...</div>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <span class="text-muted" style="font-size: 12px;" id="playerModalSubtitle">Codec: H.265 / HLS Stream</span>
          <button type="button" class="btn btn-secondary btn-sm" onclick="closeLivePlayerModal()">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== MODAL PROFIL & GANTI PASSWORD ===== -->
  <div class="modal fade modal-dark" id="modalProfile" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold text-white">
            <i class="fas fa-user-cog text-info mr-2"></i> Pengaturan Akun & Keamanan
          </h5>
          <button type="button" class="close text-white" onclick="$('#modalProfile').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body px-4 py-3">
          
          <form id="formUpdateProfile" onsubmit="submitUpdateProfile(event)" class="mb-4 pb-3 border-bottom border-secondary">
            <h6 class="text-info font-weight-bold mb-3"><i class="fas fa-id-card mr-1"></i> Data Profil Perusahaan</h6>
            <div class="form-group form-group-dark">
              <label>Nama Customer / Perusahaan:</label>
              <input type="text" id="prof-name" class="form-control form-control-dark" required>
            </div>
            <div class="form-group form-group-dark">
              <label>Email Akun (Read-only):</label>
              <input type="email" id="prof-email" class="form-control form-control-dark" disabled style="opacity: 0.7;">
            </div>
            <div class="form-row">
              <div class="form-group col-md-6 form-group-dark">
                <label>No. HP / WhatsApp:</label>
                <input type="text" id="prof-phone" class="form-control form-control-dark">
              </div>
              <div class="form-group col-md-6 form-group-dark">
                <label>Kota / Wilayah:</label>
                <input type="text" id="prof-city" class="form-control form-control-dark">
              </div>
            </div>
            <button type="submit" class="btn btn-sm btn-info font-weight-bold" style="background: #0284c7;">
              Simpan Profil
            </button>
          </form>

          <form id="formChangePassword" onsubmit="submitChangePassword(event)">
            <h6 class="text-warning font-weight-bold mb-3"><i class="fas fa-key mr-1"></i> Ganti Password Akun</h6>
            <div class="form-group form-group-dark">
              <label>Password Baru:</label>
              <input type="password" id="new-password" class="form-control form-control-dark" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="form-group form-group-dark">
              <label>Konfirmasi Password Baru:</label>
              <input type="password" id="confirm-password" class="form-control form-control-dark" placeholder="Ulangi password baru" required>
            </div>
            <button type="submit" class="btn btn-sm btn-warning font-weight-bold text-dark">
              Update Password
            </button>
          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- ===== MODAL REQUEST UPGRADE KUOTA ===== -->
  <div class="modal fade modal-dark" id="modalUpgradeQuota" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold text-white">
            <i class="fas fa-rocket text-warning mr-2"></i> Upgrade Kuota CCTV Loewix
          </h5>
          <button type="button" class="close text-white" onclick="$('#modalUpgradeQuota').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body px-4 py-4 text-center">
          <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(56, 189, 248, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px; color: #38bdf8;">
            <i class="fas fa-server"></i>
          </div>
          <h5 class="font-weight-bold text-white mb-2">Ingin Menambah Slot Kamera CCTV?</h5>
          <p class="text-muted" style="font-size: 13.5px;">
            Hubungi Technical Enterprise Support PT. Loewix Indonesia untuk aktivasi kuota tambahan channel stream, bandwidth dedicated, atau integrasi NVR multi-site.
          </p>
          <div class="mt-4">
            <a href="https://wa.me/6281234567890?text=Halo%20Loewix%20CCTV,%20saya%20ingin%20upgrade%20kuota%20kamera%20CCTV%20untuk%20akun%20saya" target="_blank" class="btn btn-success font-weight-bold px-4 py-2" style="border-radius: 25px;">
              <i class="fab fa-whatsapp mr-1"></i> Hubungi via WhatsApp
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="../assets/bootstarp/jquery.min.js"></script>
  <script src="../assets/bootstarp/bootstrap.min.js"></script>
  <script>
    let currentCustomer = null;
    let customerCameras = [];
    let hlsInstance = null;

    // Initialize Customer Dashboard
    document.addEventListener('DOMContentLoaded', () => {
      initCustomerSession();
    });

    async function initCustomerSession() {
      // Check session via API
      try {
        const res = await fetch('../api/customer_portal.php?action=get_profile');
        const data = await res.json();
        
        if (data.success && data.user) {
          currentCustomer = data.user;
          localStorage.setItem('loewix_user', JSON.stringify(currentCustomer));
          renderCustomerUI();
          loadCustomerCameras();
        } else {
          // Fallback to localStorage
          const localUser = localStorage.getItem('loewix_user');
          if (localUser) {
            currentCustomer = JSON.parse(localUser);
            renderCustomerUI();
            loadCustomerCameras();
          } else {
            // Not logged in -> redirect to main page with login
            window.location.href = '../index.php?login=required';
          }
        }
      } catch (err) {
        console.error('Session check failed:', err);
        const localUser = localStorage.getItem('loewix_user');
        if (localUser) {
          currentCustomer = JSON.parse(localUser);
          renderCustomerUI();
          loadCustomerCameras();
        } else {
          window.location.href = '../index.php?login=required';
        }
      }
    }

    function renderCustomerUI() {
      if (!currentCustomer) return;

      document.getElementById('nav-user-name').innerText = currentCustomer.name || 'Customer Loewix';
      document.getElementById('hero-customer-name').innerText = currentCustomer.name || 'Enterprise Customer';
      document.getElementById('hero-customer-city').innerText = '📍 ' + (currentCustomer.city || 'Pematangsiantar').toUpperCase();

      const quota = parseInt(currentCustomer.cctv_quota) || 20;
      const used = parseInt(currentCustomer.cctv_used) || 0;
      const remaining = Math.max(0, quota - used);
      const pct = Math.min(100, Math.round((used / quota) * 100));

      document.getElementById('hero-quota-text').innerText = `${used} / ${quota} Kamera (${pct}%)`;
      document.getElementById('hero-quota-bar').style.width = pct + '%';
      document.getElementById('hero-used-count').innerText = used;
      document.getElementById('hero-remaining-count').innerText = remaining;
      document.getElementById('card-quota-max').innerText = quota;
      document.getElementById('card-total-cam').innerText = used;
    }

    async function loadCustomerCameras() {
      const grid = document.getElementById('customer-camera-grid');
      const userId = currentCustomer ? currentCustomer.id : '';

      try {
        const res = await fetch(`../api/customer_portal.php?action=my_cameras&user_id=${userId}`);
        const data = await res.json();

        if (data.success && Array.isArray(data.cameras)) {
          customerCameras = data.cameras;
          
          // Update card stats
          const onlineCount = customerCameras.filter(c => c.status !== 'offline').length;
          document.getElementById('card-online-cam').innerText = onlineCount;
          document.getElementById('card-total-cam').innerText = customerCameras.length;

          // Update quota meter
          if (currentCustomer) {
            currentCustomer.cctv_used = customerCameras.length;
            renderCustomerUI();
          }

          renderCameraCards(customerCameras);
        } else {
          renderEmptyState('Belum ada kamera CCTV terdaftar di akun Anda. Klik tombol "Tambah Kamera CCTV" di atas untuk menambahkan.');
        }
      } catch (err) {
        console.error('Failed to load customer cameras:', err);
        renderEmptyState('Gagal memuat daftar kamera dari server.');
      }
    }

    function renderCameraCards(list) {
      const grid = document.getElementById('customer-camera-grid');
      if (!grid) return;

      if (!list || list.length === 0) {
        renderEmptyState('Tidak ada kamera yang cocok dengan pencarian / filter.');
        return;
      }

      let html = '';
      list.forEach(cam => {
        const isOnline = (cam.status !== 'offline');
        const statusBadge = isOnline 
          ? `<span class="cam-badge-status"><span class="pulse-dot"></span> ONLINE</span>`
          : `<span class="cam-badge-status offline"><i class="fas fa-times-circle"></i> OFFLINE</span>`;

        const connLabel = (cam.connection_type === 'xmeye_p2p') ? 'XMEYE P2P' : (cam.platform === 'ipcamlive' ? 'IPCAMLIVE' : 'RTSP H.265');
        const thumbUrl = cam.thumbnail || '../assets/image/icon-cctv.png';

        html += `
          <div class="cam-card" id="cam-card-${cam.id}">
            <div class="cam-preview-container" id="cam-preview-${cam.id}" onclick="playCameraInline(${cam.id})" title="Klik untuk memutar siaran langsung">
              <img src="${thumbUrl}" alt="${cam.title}" class="cam-preview-img" id="cam-thumb-${cam.id}" onerror="this.src='../assets/image/icon-cctv.png'">
              <div class="play-overlay-hint" id="play-hint-${cam.id}">
                <i class="fas fa-play-circle"></i>
              </div>
              <video id="cam-video-${cam.id}" class="cam-inline-video" style="display: none;" controls autoplay muted playsinline></video>
              <div id="cam-loading-${cam.id}" class="cam-inline-loading" style="display: none;">
                <div class="spinner-border text-info spinner-border-sm mb-1" role="status"></div>
                <span style="font-size: 11px; font-weight: 600;">Menghubungkan...</span>
              </div>
              ${statusBadge}
              <span class="cam-badge-type">${connLabel}</span>
            </div>
            <div class="cam-info-body">
              <div>
                <div class="cam-title" title="${cam.title}">${cam.title}</div>
                <div class="cam-meta">
                  <span><i class="fas fa-map-marker-alt text-info"></i> ${(cam.city || 'Siantar').toUpperCase()}</span>
                  <span><i class="fas fa-video text-muted"></i> CH ${cam.channel || 1}</span>
                </div>
              </div>
              <div class="cam-actions-row">
                <button class="btn-cam-action" id="btn-live-${cam.id}" onclick="playCameraInline(${cam.id})" title="Live Stream Test Langsung di Sini">
                  <i class="fas fa-play text-info"></i> Live Test
                </button>
                <button class="btn-cam-action" onclick="openEditCameraModal(${cam.id})" title="Edit Pengaturan Kamera">
                  <i class="fas fa-cog"></i> Edit
                </button>
                <button class="btn-cam-action danger" onclick="deleteCustomerCamera(${cam.id}, '${cam.title.replace(/'/g, "\\'")}')" title="Hapus Kamera">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        `;
      });

      grid.innerHTML = html;
    }

    function renderEmptyState(msg) {
      const grid = document.getElementById('customer-camera-grid');
      if (!grid) return;
      grid.innerHTML = `
        <div class="empty-state-box">
          <div class="empty-state-icon"><i class="fas fa-video-slash"></i></div>
          <h5 class="text-white mb-2">${msg}</h5>
          <button class="btn-add-cam mt-3" onclick="openAddCameraModal()">
            <i class="fas fa-plus"></i> Tambah Kamera Pertama
          </button>
        </div>
      `;
    }

    function applyCameraFilters() {
      const searchVal = (document.getElementById('filter-search-input').value || '').toLowerCase();
      const cityVal = document.getElementById('filter-city-select').value;
      const statusVal = document.getElementById('filter-status-select').value;

      const filtered = customerCameras.filter(cam => {
        const matchTitle = (cam.title || '').toLowerCase().includes(searchVal);
        const matchCity = (cityVal === 'all') || (cam.city && cam.city.toLowerCase() === cityVal);
        const matchStatus = (statusVal === 'all') || (statusVal === 'online' && cam.status !== 'offline') || (statusVal === 'offline' && cam.status === 'offline');
        return matchTitle && matchCity && matchStatus;
      });

      renderCameraCards(filtered);
    }

    function toggleConnFields() {
      const type = document.getElementById('cust-cam-conn-type').value;
      const rtspBox = document.getElementById('field-rtsp-container');
      const xmeyeBox = document.getElementById('field-xmeye-container');

      if (type === 'xmeye_p2p') {
        rtspBox.style.display = 'none';
        xmeyeBox.style.display = 'block';
      } else {
        rtspBox.style.display = 'block';
        xmeyeBox.style.display = 'none';
      }
    }

    function openAddCameraModal() {
      // Check quota limit
      const quota = parseInt(currentCustomer.cctv_quota) || 20;
      const used = customerCameras.length;

      if (used >= quota && currentCustomer.role !== 'super_admin') {
        alert(`Batas kuota Anda (${quota} Kamera) telah penuh! Silakan hapus kamera yang tidak terpakai atau ajukan upgrade kuota.`);
        openRequestUpgradeModal();
        return;
      }

      document.getElementById('camModalTitle').innerHTML = '<i class="fas fa-plus-circle text-info mr-2"></i> Tambah Kamera CCTV Baru';
      document.getElementById('cust-cam-id').value = '0';
      document.getElementById('cust-cam-title').value = '';
      document.getElementById('cust-cam-city').value = currentCustomer.city || 'siantar';
      document.getElementById('cust-cam-conn-type').value = 'rtsp';
      document.getElementById('cust-cam-hls').value = '';
      document.getElementById('cust-cam-rtsp').value = '';
      document.getElementById('cust-cam-sn').value = '';
      document.getElementById('cust-cam-channel').value = '1';
      document.getElementById('cust-cam-status').value = 'online';

      toggleConnFields();
      $('#modalCamForm').modal('show');
    }

    function openEditCameraModal(camId) {
      const cam = customerCameras.find(c => c.id == camId);
      if (!cam) return;

      document.getElementById('camModalTitle').innerHTML = '<i class="fas fa-cog text-info mr-2"></i> Edit Pengaturan Kamera';
      document.getElementById('cust-cam-id').value = cam.id;
      document.getElementById('cust-cam-title').value = cam.title || '';
      document.getElementById('cust-cam-city').value = cam.city || 'siantar';
      document.getElementById('cust-cam-conn-type').value = cam.connection_type || 'rtsp';
      document.getElementById('cust-cam-hls').value = cam.hls_url || cam.streamPath || '';
      document.getElementById('cust-cam-rtsp').value = cam.rtsp_url || '';
      document.getElementById('cust-cam-sn').value = cam.serial_number || '';
      document.getElementById('cust-cam-channel').value = cam.channel || 1;
      document.getElementById('cust-cam-status').value = cam.status || 'online';

      toggleConnFields();
      $('#modalCamForm').modal('show');
    }

    async function submitCustomerCamera(e) {
      e.preventDefault();
      const form = document.getElementById('formCamCustomer');
      const formData = new FormData();

      formData.append('action', 'save_camera');
      formData.append('id', document.getElementById('cust-cam-id').value);
      formData.append('title', document.getElementById('cust-cam-title').value);
      formData.append('city', document.getElementById('cust-cam-city').value);
      formData.append('connection_type', document.getElementById('cust-cam-conn-type').value);
      formData.append('hls_url', document.getElementById('cust-cam-hls').value);
      formData.append('rtsp_url', document.getElementById('cust-cam-rtsp').value);
      formData.append('serial_number', document.getElementById('cust-cam-sn').value);
      formData.append('channel', document.getElementById('cust-cam-channel').value);
      formData.append('status', document.getElementById('cust-cam-status').value);
      formData.append('user_id', currentCustomer ? currentCustomer.id : '');

      try {
        const res = await fetch('../api/customer_portal.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        if (data.success) {
          $('#modalCamForm').modal('hide');
          loadCustomerCameras();
        } else {
          alert(data.message || 'Gagal menyimpan kamera.');
        }
      } catch (err) {
        console.error('Save camera error:', err);
        alert('Terjadi kesalahan koneksi server.');
      }
    }

    async function deleteCustomerCamera(camId, title) {
      if (!confirm(`Apakah Anda yakin ingin menghapus kamera '${title}' dari channel Anda?`)) {
        return;
      }

      const formData = new FormData();
      formData.append('action', 'delete_camera');
      formData.append('id', camId);
      formData.append('user_id', currentCustomer ? currentCustomer.id : '');

      try {
        const res = await fetch('../api/customer_portal.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        if (data.success) {
          loadCustomerCameras();
        } else {
          alert(data.message || 'Gagal menghapus kamera.');
        }
      } catch (err) {
        console.error('Delete camera error:', err);
        alert('Terjadi kesalahan koneksi.');
      }
    }

    // ===== INLINE CAMERA PLAYER CONTROLLER (DIRECT CARD PLAYBACK) =====
    const activeInlinePlayers = new Map(); // camId -> { hls, video }

    async function playCameraInline(camId) {
      const cam = customerCameras.find(c => c.id == camId);
      if (!cam) return;

      const container = document.getElementById(`cam-preview-${camId}`);
      const thumb = document.getElementById(`cam-thumb-${camId}`);
      const hint = document.getElementById(`play-hint-${camId}`);
      const video = document.getElementById(`cam-video-${camId}`);
      const loading = document.getElementById(`cam-loading-${camId}`);
      const btn = document.getElementById(`btn-live-${camId}`);

      if (!container || !video) return;

      // If already playing this camera, toggle stop
      if (activeInlinePlayers.has(camId)) {
        stopCameraInline(camId);
        return;
      }

      // Hide hint overlay & show loading
      if (hint) hint.style.display = 'none';
      if (loading) {
        loading.style.display = 'flex';
        loading.innerHTML = `<div class="spinner-border text-info spinner-border-sm mb-1" role="status"></div><span style="font-size: 11px; font-weight: 600;">Menghubungkan Stream...</span>`;
      }
      if (btn) {
        btn.innerHTML = `<i class="fas fa-spinner fa-spin text-info"></i> Loading...`;
      }

      let streamUrl = cam.hls_url || cam.streamPath || '';

      // If XMeye P2P camera without active bcloud URL, resolve via jftech_gateway.php
      if (cam.connection_type === 'xmeye_p2p' || (!streamUrl.includes('bcloud365.net') && cam.serial_number)) {
        const sn = cam.serial_number || (streamUrl.match(/^xmeye_([a-fA-F0-9]+)/) ? streamUrl.match(/^xmeye_([a-fA-F0-9]+)/)[1] : '');
        const ch = cam.channel || 1;
        const devUser = cam.device_user || 'admin';
        const devPass = cam.device_pass || '';

        if (sn) {
          if (loading) {
            loading.innerHTML = `<div class="spinner-border text-info spinner-border-sm mb-1" role="status"></div><span style="font-size: 11px; font-weight: 600;">Cloud P2P (CH ${ch})...</span>`;
          }
          try {
            const res = await fetch(`../api/jftech_gateway.php?action=get_live_stream&sn=${encodeURIComponent(sn)}&channel=${encodeURIComponent(ch)}&stream=1&device_user=${encodeURIComponent(devUser)}&device_pass=${encodeURIComponent(devPass)}`);
            const data = await res.json();
            if (data.success && data.hls_url) {
              streamUrl = data.hls_url;
              cam.hls_url = data.hls_url;
            } else {
              if (loading) {
                loading.innerHTML = `<div class="text-danger mb-1"><i class="fas fa-video-slash"></i></div><span style="font-size: 11px; font-weight: 600; color: #f87171;">Offline</span><button class="btn btn-xs btn-outline-light mt-1" style="font-size: 10px; padding: 2px 6px;" onclick="event.stopPropagation(); playCameraInline(${camId})">Coba Lagi</button>`;
              }
              if (btn) btn.innerHTML = `<i class="fas fa-play text-info"></i> Live Test`;
              return;
            }
          } catch (e) {
            console.error('Failed to resolve XMeye stream:', e);
          }
        }
      }

      // Normalization for MediaMTX / RTSP stream path (e.g. "cctv_loewix_1" or "yamaha_dds")
      if (streamUrl && !streamUrl.startsWith('http://') && !streamUrl.startsWith('https://')) {
        streamUrl = `https://stream.loewixcctv.com/${streamUrl}/index.m3u8`;
      } else if (streamUrl && streamUrl.startsWith('http://stream.loewixcctv.com')) {
        streamUrl = streamUrl.replace('http://', 'https://');
      }

      if (!streamUrl) {
        if (loading) {
          loading.innerHTML = `<div class="text-warning mb-1"><i class="fas fa-exclamation-triangle"></i></div><span style="font-size: 11px; font-weight: 600; color: #fbbf24;">URL Belum Dikonfigurasi</span>`;
        }
        if (btn) btn.innerHTML = `<i class="fas fa-play text-info"></i> Live Test`;
        return;
      }

      function revealVideo() {
        if (loading) loading.style.display = 'none';
        if (thumb) thumb.style.display = 'none';
        if (video) video.style.display = 'block';
        if (btn) {
          btn.innerHTML = `<i class="fas fa-stop text-danger"></i> Stop Test`;
          btn.classList.add('btn-playing-active');
        }
      }

      video.muted = true;
      video.setAttribute('playsinline', 'true');
      video.setAttribute('webkit-playsinline', 'true');
      video.setAttribute('autoplay', '');
      video.setAttribute('muted', '');

      let hls = null;

      if (streamUrl.includes('.m3u8') || streamUrl.includes('bcloud365.net')) {
        if (Hls.isSupported()) {
          hls = new Hls({
            enableWorker: true,
            lowLatencyMode: true,
            liveSyncDurationCount: 1,
            maxBufferLength: 2,
            xhrSetup: function(xhr, url) {
              try {
                xhr.setRequestHeader('Bypass-Tunnel-Reminder', 'true');
                xhr.setRequestHeader('ngrok-skip-browser-warning', 'true');
              } catch (e) {}
            }
          });

          hls.loadSource(streamUrl);
          hls.attachMedia(video);

          hls.on(Hls.Events.MANIFEST_PARSED, () => {
            video.muted = true;
            const p = video.play();
            if (p !== undefined) {
              p.then(revealVideo).catch(e => {
                console.warn('Autoplay prevented:', e);
                revealVideo();
              });
            } else {
              revealVideo();
            }
          });

          hls.on(Hls.Events.ERROR, (event, data) => {
            if (data.fatal) {
              if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                hls.startLoad();
              } else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
                hls.recoverMediaError();
              } else {
                if (loading) {
                  loading.style.display = 'flex';
                  loading.innerHTML = `<div class="text-danger mb-1"><i class="fas fa-exclamation-circle"></i></div><span style="font-size: 11px; font-weight: 600; color: #f87171;">Stream Belum Aktif</span><button class="btn btn-xs btn-outline-light mt-1" style="font-size: 10px; padding: 2px 6px;" onclick="event.stopPropagation(); playCameraInline(${camId})">Retry</button>`;
                }
                if (btn) {
                  btn.innerHTML = `<i class="fas fa-play text-info"></i> Live Test`;
                  btn.classList.remove('btn-playing-active');
                }
                stopCameraInline(camId);
              }
            }
          });

        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
          video.src = streamUrl;
          video.muted = true;
          video.play().then(revealVideo).catch(revealVideo);
        }
      } else {
        video.src = streamUrl;
        video.muted = true;
        video.play().then(revealVideo).catch(revealVideo);
      }

      video.onplaying = revealVideo;
      video.onloadeddata = revealVideo;

      activeInlinePlayers.set(camId, { hls, video });
    }

    function stopCameraInline(camId) {
      const playerObj = activeInlinePlayers.get(camId);
      if (playerObj) {
        if (playerObj.hls) {
          playerObj.hls.destroy();
        }
        if (playerObj.video) {
          playerObj.video.pause();
          playerObj.video.src = '';
          playerObj.video.style.display = 'none';
        }
        activeInlinePlayers.delete(camId);
      }

      const thumb = document.getElementById(`cam-thumb-${camId}`);
      const hint = document.getElementById(`play-hint-${camId}`);
      const loading = document.getElementById(`cam-loading-${camId}`);
      const btn = document.getElementById(`btn-live-${camId}`);

      if (thumb) thumb.style.display = 'block';
      if (hint) hint.style.display = 'flex';
      if (loading) loading.style.display = 'none';
      if (btn) {
        btn.innerHTML = `<i class="fas fa-play text-info"></i> Live Test`;
        btn.classList.remove('btn-playing-active');
      }
    }

    async function openLivePlayerModal(camId) {
      const cam = customerCameras.find(c => c.id == camId);
      if (!cam) return;

      document.getElementById('playerModalTitle').innerHTML = `<i class="fas fa-video text-info mr-2"></i> ${cam.title}`;
      document.getElementById('playerModalSubtitle').innerText = `Lokasi: ${(cam.city || 'Siantar').toUpperCase()} | Tipe: ${cam.connection_type === 'xmeye_p2p' ? 'XMEYE P2P' : (cam.platform === 'ipcamlive' ? 'IPCAMLIVE' : 'RTSP H.265')}`;

      const video = document.getElementById('customer-preview-video');
      const spinner = document.getElementById('player-loading-spinner');

      if (spinner) {
        spinner.style.display = 'block';
        spinner.innerHTML = `<div class="spinner-border text-info mb-2" role="status"></div><div class="text-white font-weight-bold">Menghubungkan ke Stream Live...</div>`;
      }
      if (video) {
        video.style.display = 'none';
        video.muted = true;
        video.setAttribute('playsinline', 'true');
        video.setAttribute('webkit-playsinline', 'true');
        video.setAttribute('autoplay', '');
        video.setAttribute('muted', '');
      }

      $('#modalLivePlayer').modal('show');

      if (hlsInstance) {
        hlsInstance.destroy();
        hlsInstance = null;
      }

      let streamUrl = cam.hls_url || cam.streamPath || '';

      // If XMeye P2P camera without active bcloud URL, resolve via jftech_gateway.php
      if (cam.connection_type === 'xmeye_p2p' || (!streamUrl.includes('bcloud365.net') && cam.serial_number)) {
        const sn = cam.serial_number || (streamUrl.match(/^xmeye_([a-fA-F0-9]+)/) ? streamUrl.match(/^xmeye_([a-fA-F0-9]+)/)[1] : '');
        const ch = cam.channel || 1;
        const devUser = cam.device_user || 'admin';
        const devPass = cam.device_pass || '';

        if (sn) {
          if (spinner) {
            spinner.innerHTML = `<div class="spinner-border text-info mb-2" role="status"></div><div class="text-white font-weight-bold">Menghubungkan ke Cloud P2P...</div><small class="text-muted d-block mt-1">Device SN: ${sn} (CH ${ch})</small>`;
          }
          try {
            const res = await fetch(`../api/jftech_gateway.php?action=get_live_stream&sn=${encodeURIComponent(sn)}&channel=${encodeURIComponent(ch)}&stream=1&device_user=${encodeURIComponent(devUser)}&device_pass=${encodeURIComponent(devPass)}`);
            const data = await res.json();
            if (data.success && data.hls_url) {
              streamUrl = data.hls_url;
              cam.hls_url = data.hls_url;
            } else {
              if (spinner) {
                spinner.innerHTML = `<div class="text-danger mb-2"><i class="fas fa-video-slash fa-2x"></i></div><div class="text-white font-weight-bold">Kamera Sedang Offline / Tidak Merespon</div><small class="text-muted mt-1 d-block">${data.message || 'Periksa koneksi internet atau DVR kamera.'}</small>`;
              }
              return;
            }
          } catch (e) {
            console.error('Failed to resolve XMeye stream:', e);
          }
        }
      }

      // Normalization for MediaMTX / RTSP stream path (e.g. "cctv_loewix_1" or "yamaha_dds")
      if (streamUrl && !streamUrl.startsWith('http://') && !streamUrl.startsWith('https://')) {
        streamUrl = `https://stream.loewixcctv.com/${streamUrl}/index.m3u8`;
      } else if (streamUrl && streamUrl.startsWith('http://stream.loewixcctv.com')) {
        streamUrl = streamUrl.replace('http://', 'https://');
      }

      if (!streamUrl) {
        if (spinner) {
          spinner.innerHTML = `<div class="text-warning mb-2"><i class="fas fa-exclamation-triangle fa-2x"></i></div><div class="text-white font-weight-bold">Stream URL Belum Dikonfigurasi</div><small class="text-muted mt-1 d-block">Silakan klik Edit untuk melengkapi URL RTSP/HLS kamera ini.</small>`;
        }
        return;
      }

      function startPlayback() {
        if (spinner) spinner.style.display = 'none';
        if (video) video.style.display = 'block';
      }

      if (streamUrl.includes('.m3u8') || streamUrl.includes('bcloud365.net')) {
        if (Hls.isSupported()) {
          hlsInstance = new Hls({
            enableWorker: true,
            lowLatencyMode: true,
            liveSyncDurationCount: 1,
            maxBufferLength: 2,
            xhrSetup: function(xhr, url) {
              try {
                xhr.setRequestHeader('Bypass-Tunnel-Reminder', 'true');
                xhr.setRequestHeader('ngrok-skip-browser-warning', 'true');
              } catch (e) {}
            }
          });
          hlsInstance.loadSource(streamUrl);
          hlsInstance.attachMedia(video);
          hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
            video.muted = true;
            const p = video.play();
            if (p !== undefined) {
              p.then(startPlayback).catch(e => {
                console.warn('Autoplay caught:', e);
                startPlayback();
              });
            } else {
              startPlayback();
            }
          });
          hlsInstance.on(Hls.Events.ERROR, (event, data) => {
            if (data.fatal) {
              if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                hlsInstance.startLoad();
              } else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
                hlsInstance.recoverMediaError();
              } else {
                if (spinner) {
                  spinner.style.display = 'block';
                  spinner.innerHTML = `<div class="text-danger mb-2"><i class="fas fa-exclamation-circle fa-2x"></i></div><div class="text-white font-weight-bold">Gagal Memutar Siaran Live</div><small class="text-muted mt-1 d-block">Stream RTSP/HLS belum aktif di server streaming.</small>`;
                }
              }
            }
          });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
          video.src = streamUrl;
          video.muted = true;
          video.play().then(startPlayback).catch(startPlayback);
        }
      } else {
        video.src = streamUrl;
        video.muted = true;
        video.play().then(startPlayback).catch(startPlayback);
      }
    }

    function closeLivePlayerModal() {
      const video = document.getElementById('customer-preview-video');
      const spinner = document.getElementById('player-loading-spinner');
      if (video) {
        video.pause();
        video.src = '';
        video.style.display = 'none';
      }
      if (spinner) {
        spinner.style.display = 'none';
      }
      if (hlsInstance) {
        hlsInstance.destroy();
        hlsInstance = null;
      }
      $('#modalLivePlayer').modal('hide');
    }

    function openProfileSettingsModal() {
      if (!currentCustomer) return;
      document.getElementById('prof-name').value = currentCustomer.name || '';
      document.getElementById('prof-email').value = currentCustomer.email || '';
      document.getElementById('prof-phone').value = currentCustomer.phone || '';
      document.getElementById('prof-city').value = currentCustomer.city || '';
      $('#modalProfile').modal('show');
    }

    async function submitUpdateProfile(e) {
      e.preventDefault();
      const formData = new FormData();
      formData.append('action', 'update_profile');
      formData.append('name', document.getElementById('prof-name').value);
      formData.append('phone', document.getElementById('prof-phone').value);
      formData.append('city', document.getElementById('prof-city').value);
      formData.append('user_id', currentCustomer ? currentCustomer.id : '');

      try {
        const res = await fetch('../api/customer_portal.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          alert('Profil berhasil diperbarui!');
          currentCustomer.name = document.getElementById('prof-name').value;
          currentCustomer.city = document.getElementById('prof-city').value;
          currentCustomer.phone = document.getElementById('prof-phone').value;
          renderCustomerUI();
        } else {
          alert(data.message || 'Gagal update profil.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    async function submitChangePassword(e) {
      e.preventDefault();
      const newP = document.getElementById('new-password').value;
      const confP = document.getElementById('confirm-password').value;

      if (newP !== confP) {
        alert('Konfirmasi password tidak cocok!');
        return;
      }

      const formData = new FormData();
      formData.append('action', 'change_password');
      formData.append('new_password', newP);
      formData.append('user_id', currentCustomer ? currentCustomer.id : '');

      try {
        const res = await fetch('../api/customer_portal.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          alert('Password berhasil diperbarui!');
          document.getElementById('new-password').value = '';
          document.getElementById('confirm-password').value = '';
          $('#modalProfile').modal('hide');
        } else {
          alert(data.message || 'Gagal update password.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    function openRequestUpgradeModal() {
      $('#modalUpgradeQuota').modal('show');
    }

    function logoutCustomer() {
      localStorage.removeItem('loewix_user');
      fetch('../api/auth.php?action=logout').finally(() => {
        window.location.href = '../index.php';
      });
    }
  </script>
</body>
</html>
