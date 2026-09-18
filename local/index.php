<?php
/**
 * Loewix Local VMS - Desktop & LAN Edition
 * PT. LOEWIX INDONESIA
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = !empty($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? 'Administrator';
$userRole = $_SESSION['user_role'] ?? 'super_admin';
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Loewix Local VMS - Desktop & LAN Edition</title>
  
  <!-- PWA Meta Tags -->
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#0284c7">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Loewix VMS">
  <link rel="icon" type="image/png" href="../assets/image/icon.png">

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- HLS.js for Live Stream Video Player -->
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

  <style>
    :root {
      --bg-base: #080c14;
      --bg-surface: #0f172a;
      --bg-surface-elevated: #1e293b;
      --border-color: rgba(255, 255, 255, 0.08);
      --border-focus: rgba(56, 189, 248, 0.5);
      --primary: #0284c7;
      --primary-hover: #0369a1;
      --accent: #38bdf8;
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --success: #10b981;
      --danger: #ef4444;
      --warning: #f59e0b;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      background-color: var(--bg-base);
      color: var(--text-main);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    ::-webkit-scrollbar-track {
      background: var(--bg-base);
    }
    ::-webkit-scrollbar-thumb {
      background: #334155;
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #475569;
    }

    /* Header & Navigation */
    .app-header {
      background: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--border-color);
      position: sticky;
      top: 0;
      z-index: 100;
      padding: 0.75rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .brand-group {
      display: flex;
      align-items: center;
      gap: 0.85rem;
    }

    .brand-logo {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: linear-gradient(135deg, #0284c7, #0ea5e9);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);
      color: #fff;
      font-size: 1.2rem;
    }

    .brand-title {
      font-weight: 800;
      font-size: 1.15rem;
      letter-spacing: -0.02em;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .brand-tag {
      font-size: 0.65rem;
      font-weight: 700;
      padding: 0.15rem 0.5rem;
      background: rgba(56, 189, 248, 0.15);
      color: var(--accent);
      border: 1px solid rgba(56, 189, 248, 0.3);
      border-radius: 9999px;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .user-pill {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.4rem 0.85rem;
      background: var(--bg-surface-elevated);
      border: 1px solid var(--border-color);
      border-radius: 9999px;
      font-size: 0.82rem;
      font-weight: 600;
      color: #cbd5e1;
    }

    .user-pill .avatar {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: #0369a1;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      color: #fff;
    }

    /* Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.55rem 1.15rem;
      font-size: 0.875rem;
      font-weight: 600;
      border-radius: 8px;
      border: 1px solid transparent;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), #0284c7);
      color: #fff;
      box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, var(--primary-hover), #0369a1);
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(2, 132, 199, 0.4);
    }

    .btn-secondary {
      background: var(--bg-surface-elevated);
      color: #cbd5e1;
      border-color: var(--border-color);
    }
    .btn-secondary:hover {
      background: #334155;
      color: #fff;
    }

    .btn-outline {
      background: transparent;
      border-color: var(--border-color);
      color: #94a3b8;
    }
    .btn-outline:hover {
      border-color: #cbd5e1;
      color: #fff;
      background: rgba(255, 255, 255, 0.05);
    }

    .btn-danger-outline {
      background: transparent;
      border-color: rgba(239, 68, 68, 0.3);
      color: #f87171;
    }
    .btn-danger-outline:hover {
      background: rgba(239, 68, 68, 0.15);
      border-color: #ef4444;
      color: #fff;
    }

    .btn-sm {
      padding: 0.35rem 0.75rem;
      font-size: 0.78rem;
      border-radius: 6px;
    }

    /* Container */
    .app-container {
      max-width: 1280px;
      width: 100%;
      margin: 0 auto;
      padding: 1.75rem 1.5rem;
      flex: 1;
    }

    /* Banner / Hero Section */
    .hero-banner {
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(30, 41, 59, 0.6));
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 1.5rem 1.75rem;
      margin-bottom: 2rem;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 1.25rem;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
    }

    .hero-info h1 {
      font-size: 1.45rem;
      font-weight: 800;
      color: #fff;
      margin-bottom: 0.35rem;
      display: flex;
      align-items: center;
      gap: 0.65rem;
    }

    .hero-info p {
      font-size: 0.88rem;
      color: var(--text-muted);
      line-height: 1.4;
    }

    /* Stats Grid */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      padding: 1rem 1.25rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      transition: border-color 0.2s;
    }
    .stat-card:hover {
      border-color: rgba(56, 189, 248, 0.3);
    }

    .stat-icon {
      width: 44px;
      height: 44px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
    }
    .stat-icon.blue {
      background: rgba(2, 132, 199, 0.15);
      color: #38bdf8;
    }
    .stat-icon.green {
      background: rgba(16, 185, 129, 0.15);
      color: #34d399;
    }
    .stat-icon.purple {
      background: rgba(168, 85, 247, 0.15);
      color: #c084fc;
    }

    .stat-meta .label {
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--text-muted);
      margin-bottom: 0.2rem;
    }
    .stat-meta .value {
      font-size: 1.45rem;
      font-weight: 800;
      color: #fff;
    }

    /* Camera Cards Grid */
    .camera-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 1.25rem;
    }

    .camera-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: 14px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }
    .camera-card:hover {
      border-color: rgba(56, 189, 248, 0.4);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }

    .camera-preview-thumb {
      width: 100%;
      height: 190px;
      background: #030712;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      overflow: hidden;
    }

    .camera-preview-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    .camera-card:hover .camera-preview-thumb img {
      transform: scale(1.03);
    }

    .play-overlay {
      position: absolute;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: rgba(2, 132, 199, 0.85);
      backdrop-filter: blur(8px);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 1.2rem;
      transition: all 0.2s ease;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.5);
    }
    .camera-preview-thumb:hover .play-overlay {
      transform: scale(1.15);
      background: #0284c7;
    }

    .status-badge {
      position: absolute;
      top: 0.75rem;
      left: 0.75rem;
      padding: 0.25rem 0.6rem;
      border-radius: 6px;
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      gap: 0.35rem;
    }
    .status-badge.online {
      background: rgba(16, 185, 129, 0.25);
      color: #34d399;
      border: 1px solid rgba(16, 185, 129, 0.4);
    }
    .status-badge.offline {
      background: rgba(239, 68, 68, 0.25);
      color: #f87171;
      border: 1px solid rgba(239, 68, 68, 0.4);
    }

    .conn-type-badge {
      position: absolute;
      top: 0.75rem;
      right: 0.75rem;
      padding: 0.25rem 0.6rem;
      border-radius: 6px;
      font-size: 0.7rem;
      font-weight: 700;
      background: rgba(15, 23, 42, 0.85);
      color: #93c5fd;
      border: 1px solid rgba(255, 255, 255, 0.1);
      text-transform: uppercase;
    }

    .camera-info {
      padding: 1.15rem;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .camera-title {
      font-size: 1.05rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 0.4rem;
    }

    .camera-submeta {
      font-size: 0.78rem;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 0.85rem;
    }

    .camera-rtsp-path {
      background: var(--bg-base);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 6px;
      padding: 0.45rem 0.65rem;
      font-size: 0.74rem;
      color: #94a3b8;
      font-family: monospace;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-bottom: 1rem;
    }

    .camera-actions {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: auto;
      border-top: 1px solid var(--border-color);
      padding-top: 0.85rem;
    }

    /* Modal Styling */
    .modal-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(4, 7, 13, 0.8);
      backdrop-filter: blur(10px);
      z-index: 1000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }
    .modal-backdrop.active {
      display: flex;
    }

    .modal-dialog {
      background: var(--bg-surface);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 16px;
      width: 100%;
      max-width: 580px;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
      animation: modalSlideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      overflow: hidden;
    }

    @keyframes modalSlideIn {
      from { opacity: 0; transform: scale(0.96) translateY(10px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .modal-header {
      padding: 1.15rem 1.5rem;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-title {
      font-size: 1.15rem;
      font-weight: 700;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .modal-close {
      background: transparent;
      border: none;
      color: #94a3b8;
      font-size: 1.25rem;
      cursor: pointer;
      padding: 0.25rem;
      border-radius: 6px;
      transition: color 0.15s;
    }
    .modal-close:hover {
      color: #fff;
    }

    .modal-body {
      padding: 1.5rem;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 1.15rem;
    }

    .modal-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid var(--border-color);
      background: rgba(15, 23, 42, 0.5);
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 0.75rem;
    }

    /* Form Groups & Inputs */
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
    }

    .form-group label {
      font-size: 0.82rem;
      font-weight: 600;
      color: #e2e8f0;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.85rem;
    }

    .form-control {
      width: 100%;
      background: #0b1120;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 0.65rem 0.85rem;
      color: #f8fafc;
      font-size: 0.88rem;
      transition: all 0.2s;
    }
    .form-control:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
      background: #0f172a;
    }

    .form-helper {
      font-size: 0.72rem;
      color: var(--text-muted);
      line-height: 1.35;
    }

    .input-group {
      display: flex;
      position: relative;
    }
    .input-group .form-control {
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
    }
    .input-group .btn {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
      white-space: nowrap;
    }

    /* Login Gate Layout */
    .login-wrapper {
      min-height: calc(100vh - 120px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .login-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: 20px;
      width: 100%;
      max-width: 440px;
      padding: 2.25rem;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
    }

    .login-logo {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      background: linear-gradient(135deg, #0284c7, #38bdf8);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.75rem;
      color: #fff;
      margin: 0 auto 1.25rem;
      box-shadow: 0 8px 24px rgba(2, 132, 199, 0.4);
    }

    .login-title {
      font-size: 1.45rem;
      font-weight: 800;
      text-align: center;
      color: #fff;
      margin-bottom: 0.35rem;
    }

    .login-subtitle {
      font-size: 0.85rem;
      color: var(--text-muted);
      text-align: center;
      margin-bottom: 1.75rem;
    }

    .alert-box {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      font-size: 0.82rem;
      margin-bottom: 1rem;
      display: none;
    }
    .alert-box.danger {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #f87171;
    }
    .alert-box.success {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.3);
      color: #34d399;
    }

    /* Video Player Modal Special */
    .video-container {
      width: 100%;
      background: #000;
      border-radius: 8px;
      overflow: hidden;
      position: relative;
      min-height: 360px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .video-container video {
      width: 100%;
      height: 100%;
      max-height: 480px;
      display: block;
    }

    .player-loader {
      position: absolute;
      display: none;
      flex-direction: column;
      align-items: center;
      gap: 0.75rem;
      color: #38bdf8;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .spinner {
      width: 36px;
      height: 36px;
      border: 3px solid rgba(56, 189, 248, 0.2);
      border-top-color: #38bdf8;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Footer */
    .app-footer {
      border-top: 1px solid var(--border-color);
      padding: 1.25rem 1.5rem;
      text-align: center;
      font-size: 0.78rem;
      color: #64748b;
      margin-top: auto;
    }
  </style>
</head>
<body>

  <!-- Top App Navigation -->
  <header class="app-header">
    <div class="brand-group">
      <div class="brand-logo">
        <i class="fas fa-video"></i>
      </div>
      <div>
        <div class="brand-title">
          LOEWIX VMS
          <span class="brand-tag">Local Desktop Edition</span>
        </div>
      </div>
    </div>

    <div class="nav-actions">
      <?php if ($isLoggedIn): ?>
        <div class="user-pill">
          <div class="avatar"><i class="fas fa-user"></i></div>
          <span><?= htmlspecialchars($userName) ?></span>
        </div>
        <button class="btn btn-outline btn-sm" id="btn-pwa-install" onclick="promptDesktopInstall()" style="display: none;" title="Download / Install ke Desktop Windows & Mac">
          <i class="fas fa-download text-info"></i> Install Desktop App
        </button>
        <button class="btn btn-danger-outline btn-sm" onclick="handleLogout()" title="Keluar dari Akun">
          <i class="fas fa-sign-out-alt"></i> Keluar
        </button>
      <?php else: ?>
        <div class="user-pill" style="border-color: rgba(56, 189, 248, 0.3); color: var(--accent);">
          <i class="fas fa-shield-alt"></i> Standalone Gate
        </div>
      <?php endif; ?>
    </div>
  </header>

  <?php if (!$isLoggedIn): ?>
  <!-- ==================== LOGIN GATE VIEW ==================== -->
  <main class="login-wrapper">
    <div class="login-card">
      <div class="login-logo">
        <i class="fas fa-shield-halved"></i>
      </div>
      <h2 class="login-title">Masuk ke Loewix VMS</h2>
      <p class="login-subtitle">Akses Pengawasan Kamera CCTV Jaringan Lokal</p>

      <div id="login-alert" class="alert-box danger"></div>

      <form id="form-login" onsubmit="handleLogin(event)">
        <div class="form-group" style="margin-bottom: 1.15rem;">
          <label><i class="fas fa-envelope text-info"></i> Email / Username:</label>
          <input type="text" id="login-email" class="form-control" placeholder="Contoh: admin@loewixcctv.com" value="admin@loewixcctv.com" required autofocus>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
          <label><i class="fas fa-lock text-info"></i> Password:</label>
          <div class="input-group">
            <input type="password" id="login-password" class="form-control" placeholder="Masukkan kata sandi" value="admin123" required>
            <button type="button" class="btn btn-secondary" onclick="togglePasswordVisibility('login-password', this)">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" id="btn-login-submit" style="width: 100%; padding: 0.75rem; font-size: 0.95rem;">
          <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
        </button>
      </form>

      <div style="margin-top: 1.5rem; padding: 0.75rem; background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.1); border-radius: 8px; font-size: 0.76rem; color: #94a3b8; text-align: center;">
        <i class="fas fa-info-circle text-info mr-1"></i> Akun bawaan: <b>admin@loewixcctv.com</b> &bull; Sandi: <b>admin123</b>
      </div>
    </div>
  </main>

  <?php else: ?>
  <!-- ==================== DASHBOARD & CAMERA MANAGEMENT ==================== -->
  <main class="app-container">
    
    <!-- Hero Banner with Add Camera Button -->
    <div class="hero-banner">
      <div class="hero-info">
        <h1>
          <i class="fas fa-video text-info"></i>
          Daftar Channel CCTV Lokal
        </h1>
        <p>Kelola siaran RTSP kamera, XMeye P2P, dan pantau live feed langsung di jaringan lokal tanpa lag.</p>
      </div>

      <button class="btn btn-primary" onclick="openAddCameraModal()" style="font-size: 0.95rem; padding: 0.7rem 1.35rem;">
        <i class="fas fa-plus-circle"></i> Tambah Kamera CCTV Baru
      </button>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon blue">
          <i class="fas fa-camera"></i>
        </div>
        <div class="stat-meta">
          <div class="label">Total Kamera</div>
          <div class="value" id="stat-total-cams">0</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon green">
          <i class="fas fa-circle-check"></i>
        </div>
        <div class="stat-meta">
          <div class="label">Kamera Aktif</div>
          <div class="value" id="stat-online-cams">0</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon purple">
          <i class="fas fa-network-wired"></i>
        </div>
        <div class="stat-meta">
          <div class="label">Tipe Koneksi</div>
          <div class="value" id="stat-conn-types">RTSP / P2P</div>
        </div>
      </div>
    </div>

    <!-- Camera Cards Grid Container -->
    <div id="camera-list-container" class="camera-grid">
      <!-- Injected by JavaScript -->
    </div>

    <!-- Empty State Container -->
    <div id="empty-camera-state" style="display: none; text-align: center; padding: 4rem 1.5rem; background: var(--bg-surface); border: 1px dashed var(--border-color); border-radius: 16px;">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(56, 189, 248, 0.1); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1rem;">
        <i class="fas fa-video-slash"></i>
      </div>
      <h3 style="font-size: 1.15rem; color: #fff; margin-bottom: 0.5rem;">Belum Ada Kamera Terdaftar</h3>
      <p style="color: var(--text-muted); font-size: 0.88rem; max-width: 440px; margin: 0 auto 1.5rem;">Tambahkan kamera CCTV pertama Anda untuk memantau streaming video secara langsung di desktop.</p>
      <button class="btn btn-primary" onclick="openAddCameraModal()">
        <i class="fas fa-plus-circle"></i> Tambah Kamera CCTV Baru
      </button>
    </div>

  </main>
  <?php endif; ?>

  <!-- ==================== MODAL TAMBAH / EDIT KAMERA CCTV ==================== -->
  <div class="modal-backdrop" id="modalCameraForm">
    <div class="modal-dialog">
      <div class="modal-header">
        <div class="modal-title" id="camFormModalTitle">
          <i class="fas fa-plus-circle text-info"></i> Tambah Kamera CCTV Baru
        </div>
        <button type="button" class="modal-close" onclick="closeModal('modalCameraForm')">&times;</button>
      </div>

      <form id="form-camera" onsubmit="handleSaveCamera(event)">
        <div class="modal-body">
          <input type="hidden" id="cust-cam-id" value="0">

          <div class="form-group">
            <label><i class="fas fa-tag text-info"></i> Nama Kamera / Lokasi:</label>
            <input type="text" id="cust-cam-title" class="form-control" placeholder="Contoh: Kamera Depan Gudang / Parkiran" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label><i class="fas fa-map-marker-alt text-info"></i> Wilayah / Penempatan:</label>
              <input type="text" id="cust-cam-city" class="form-control" placeholder="Contoh: Gedung A / Lantai 1" value="Lokal">
            </div>
            <div class="form-group">
              <label><i class="fas fa-plug text-info"></i> Tipe Koneksi:</label>
              <select id="cust-cam-conn-type" class="form-control" onchange="toggleConnTypeFields()">
                <option value="rtsp">RTSP Stream (Lokal / MediaMTX)</option>
                <option value="xmeye_p2p">XMeye P2P (Serial Cloud ID)</option>
                <option value="ipcamlive">IPCamLive</option>
              </select>
            </div>
          </div>

          <!-- RTSP Specific Container -->
          <div id="field-rtsp-container">
            <div class="form-group">
              <label><i class="fas fa-link text-info"></i> Host / IP / URL RTSP Kamera:</label>
              <div class="input-group">
                <input type="text" id="cust-cam-rtsp" class="form-control" placeholder="Contoh: 192.168.11.178 atau rtsp://..." oninput="autoParseRtspInput()">
                <button type="button" class="btn btn-outline" id="btn-detect-rtsp" onclick="testRtspConnection()" title="Uji Koneksi & Test Port Port 554">
                  <i class="fas fa-magic"></i> <span id="btn-detect-text">Cek Port</span>
                </button>
              </div>
              <div id="rtsp-detect-status" style="display: none; margin-top: 0.5rem; padding: 0.6rem 0.85rem; border-radius: 8px; font-size: 0.78rem;"></div>
              <span class="form-helper"><i class="fas fa-info-circle text-info"></i> Bisa masukkan IP lokal saja (contoh: <code>192.168.11.178</code>) atau URL lengkap RTSP.</span>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label><i class="fas fa-shield-alt text-info"></i> Keamanan RTSP:</label>
                <select id="cust-cam-rtsp-auth-type" class="form-control" onchange="toggleRtspAuthFields()">
                  <option value="auth">🔐 Bersandi (Memakai Password)</option>
                  <option value="none">🔓 Tanpa Sandi (Public Stream)</option>
                </select>
              </div>
              <div class="form-group">
                <label><i class="fas fa-layer-group text-info"></i> Port & Channel:</label>
                <div style="display: flex; gap: 0.5rem;">
                  <input type="number" id="cust-cam-rtsp-port" class="form-control" placeholder="Port" value="554" style="max-width: 90px;" title="Port RTSP Default 554">
                  <input type="number" id="cust-cam-rtsp-ch" class="form-control" placeholder="CH" value="1" min="1" max="128" title="Nomor Channel DVR">
                </div>
              </div>
            </div>

            <div class="form-row" id="row-rtsp-credentials">
              <div class="form-group">
                <label><i class="fas fa-user text-info"></i> Username RTSP:</label>
                <input type="text" id="cust-cam-rtsp-user" class="form-control" placeholder="admin" value="admin">
              </div>
              <div class="form-group">
                <label><i class="fas fa-key text-info"></i> Password RTSP:</label>
                <div class="input-group">
                  <input type="password" id="cust-cam-rtsp-pass" class="form-control" placeholder="Password RTSP">
                  <button type="button" class="btn btn-secondary" onclick="togglePasswordVisibility('cust-cam-rtsp-pass', this)">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label style="color: #94a3b8; font-size: 0.78rem;"><i class="fas fa-globe text-muted"></i> Custom Stream Path / HLS URL (Opsional):</label>
              <input type="text" id="cust-cam-hls" class="form-control" placeholder="Otomatis dibuatkan oleh server jika kosong" style="font-size: 0.8rem;">
            </div>
          </div>

          <!-- XMeye Specific Container -->
          <div id="field-xmeye-container" style="display: none;">
            <div class="form-group">
              <label><i class="fas fa-cloud text-info"></i> Serial Number / Cloud ID (16 Digit):</label>
              <input type="text" id="cust-cam-sn" class="form-control" placeholder="Contoh: 848f3922aa2875eb">
            </div>
            <div class="form-group">
              <label><i class="fas fa-list-ol text-info"></i> Channel NVR / DVR:</label>
              <input type="number" id="cust-cam-channel" class="form-control" value="1" min="1" max="128">
            </div>
          </div>

          <div class="form-group">
            <label><i class="fas fa-signal text-info"></i> Status Siaran:</label>
            <select id="cust-cam-status" class="form-control">
              <option value="online">Online (Aktif Live)</option>
              <option value="offline">Offline (Nonaktif)</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline" onclick="closeModal('modalCameraForm')">Batal</button>
          <button type="submit" class="btn btn-primary" id="btn-save-cam-submit">
            <i class="fas fa-save"></i> Simpan Kamera
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==================== MODAL LIVE STREAM PLAYER ==================== -->
  <div class="modal-backdrop" id="modalLivePlayer">
    <div class="modal-dialog" style="max-width: 800px;">
      <div class="modal-header">
        <div class="modal-title" id="playerModalTitle">
          <i class="fas fa-play-circle text-info"></i> Live Video Streaming
        </div>
        <button type="button" class="modal-close" onclick="closeLivePlayer()">&times;</button>
      </div>
      <div class="modal-body" style="padding: 0; background: #000;">
        <div class="video-container">
          <video id="live-video-player" controls autoplay muted playsinline></video>
          <div class="player-loader" id="player-loading-spinner">
            <div class="spinner"></div>
            <div>Menghubungkan ke Stream Kamera...</div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="justify-content: space-between;">
        <div id="player-stream-info" style="font-size: 0.78rem; color: #94a3b8; font-family: monospace;"></div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeLivePlayer()">Tutup Player</button>
      </div>
    </div>
  </div>

  <!-- App Footer -->
  <footer class="app-footer">
    Loewix Local VMS &bull; PT. Loewix Indonesia &bull; Versi Desktop & Jaringan Lokal (Port 8088)
  </footer>

  <!-- Application Logic JavaScript -->
  <script>
    let localCameras = [];
    let currentHlsInstance = null;
    let deferredPrompt = null;

    // PWA Install Handler for Windows & macOS
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
      const installBtn = document.getElementById('btn-pwa-install');
      if (installBtn) installBtn.style.display = 'inline-flex';
    });

    function promptDesktopInstall() {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === 'accepted') {
            console.log('User accepted desktop installation');
          }
          deferredPrompt = null;
          const installBtn = document.getElementById('btn-pwa-install');
          if (installBtn) installBtn.style.display = 'none';
        });
      } else {
        alert('Untuk mengunduh ke Desktop:\n- Chrome / Edge di Windows: Klik ikon instal (komputer dengan panah) di bilah alamat browser.\n- Safari di macOS: Pilih File > Tambahkan ke Dock (Add to Dock).');
      }
    }

    // Modal Helpers
    function openModal(id) {
      const el = document.getElementById(id);
      if (el) el.classList.add('active');
    }
    function closeModal(id) {
      const el = document.getElementById(id);
      if (el) el.classList.remove('active');
    }

    // Toggle Password Visibility
    function togglePasswordVisibility(inputId, btn) {
      const input = document.getElementById(inputId);
      if (!input) return;
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        }
      } else {
        input.type = 'password';
        if (icon) {
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      }
    }

    // Toggle Connection Fields in Form
    function toggleConnTypeFields() {
      const type = document.getElementById('cust-cam-conn-type').value;
      const rtspBox = document.getElementById('field-rtsp-container');
      const xmeyeBox = document.getElementById('field-xmeye-container');
      if (type === 'xmeye_p2p') {
        if (rtspBox) rtspBox.style.display = 'none';
        if (xmeyeBox) xmeyeBox.style.display = 'block';
      } else {
        if (rtspBox) rtspBox.style.display = 'block';
        if (xmeyeBox) xmeyeBox.style.display = 'none';
      }
    }

    function toggleRtspAuthFields() {
      const authType = document.getElementById('cust-cam-rtsp-auth-type').value;
      const row = document.getElementById('row-rtsp-credentials');
      if (row) {
        row.style.display = (authType === 'none') ? 'none' : 'grid';
      }
    }

    function autoParseRtspInput() {
      const input = document.getElementById('cust-cam-rtsp');
      if (!input) return;
      const val = input.value.trim();
      if (val.startsWith('rtsp://')) {
        try {
          const clean = val.replace('rtsp://', '');
          let userInfo = '', hostPort = '';
          if (clean.includes('@')) {
            const parts = clean.split('@');
            userInfo = parts[0];
            hostPort = parts[1];
          } else {
            hostPort = clean;
          }
          if (userInfo.includes(':')) {
            const up = userInfo.split(':');
            const uInput = document.getElementById('cust-cam-rtsp-user');
            const pInput = document.getElementById('cust-cam-rtsp-pass');
            if (uInput) uInput.value = up[0];
            if (pInput) pInput.value = up[1];
            const authSel = document.getElementById('cust-cam-rtsp-auth-type');
            if (authSel) authSel.value = 'auth';
            toggleRtspAuthFields();
          }
          if (hostPort.includes(':')) {
            const hp = hostPort.split('/')[0].split(':');
            const portInput = document.getElementById('cust-cam-rtsp-port');
            if (portInput) portInput.value = hp[1];
          }
        } catch (e) {}
      }
    }

    // RTSP Port Connection Probe
    async function testRtspConnection() {
      const hostInput = document.getElementById('cust-cam-rtsp');
      const portInput = document.getElementById('cust-cam-rtsp-port');
      const statusEl = document.getElementById('rtsp-detect-status');
      const btnText = document.getElementById('btn-detect-text');

      if (!hostInput || !hostInput.value.trim()) {
        alert('Masukkan Host atau IP kamera terlebih dahulu.');
        return;
      }

      if (btnText) btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menguji...';
      if (statusEl) {
        statusEl.style.display = 'block';
        statusEl.style.background = 'rgba(56, 189, 248, 0.1)';
        statusEl.style.color = '#38bdf8';
        statusEl.style.border = '1px solid rgba(56, 189, 248, 0.3)';
        statusEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sedang memeriksa koneksi port kamera...';
      }

      const fd = new FormData();
      fd.append('action', 'test_rtsp');
      fd.append('host', hostInput.value.trim());
      fd.append('port', portInput ? portInput.value.trim() : '554');

      try {
        const res = await fetch('api.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          statusEl.style.background = 'rgba(16, 185, 129, 0.15)';
          statusEl.style.color = '#34d399';
          statusEl.style.border = '1px solid rgba(16, 185, 129, 0.3)';
          statusEl.innerHTML = `<i class="fas fa-check-circle mr-1"></i> ${data.message}`;
        } else {
          statusEl.style.background = 'rgba(239, 68, 68, 0.15)';
          statusEl.style.color = '#f87171';
          statusEl.style.border = '1px solid rgba(239, 68, 68, 0.3)';
          statusEl.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i> ${data.message}`;
        }
      } catch (err) {
        if (statusEl) {
          statusEl.style.background = 'rgba(239, 68, 68, 0.15)';
          statusEl.style.color = '#f87171';
          statusEl.innerHTML = `<i class="fas fa-times-circle mr-1"></i> Gagal menghubungi endpoint server.`;
        }
      } finally {
        if (btnText) btnText.innerHTML = 'Cek Port';
      }
    }

    // Login Handler
    async function handleLogin(e) {
      e.preventDefault();
      const email = document.getElementById('login-email').value.trim();
      const password = document.getElementById('login-password').value.trim();
      const alertBox = document.getElementById('login-alert');
      const submitBtn = document.getElementById('btn-login-submit');

      if (alertBox) alertBox.style.display = 'none';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memverifikasi...';
      }

      const fd = new FormData();
      fd.append('action', 'login');
      fd.append('email', email);
      fd.append('password', password);

      try {
        const res = await fetch('api.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
          window.location.reload();
        } else {
          if (alertBox) {
            alertBox.innerText = data.message || 'Login gagal.';
            alertBox.style.display = 'block';
          }
        }
      } catch (err) {
        if (alertBox) {
          alertBox.innerText = 'Terjadi kesalahan saat menghubungi server lokal.';
          alertBox.style.display = 'block';
        }
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Masuk Sekarang';
        }
      }
    }

    // Logout Handler
    async function handleLogout() {
      if (!confirm('Apakah Anda yakin ingin keluar dari sistem?')) return;
      try {
        await fetch('api.php?action=logout');
      } catch (e) {}
      window.location.reload();
    }

    // Open Modal Tambah Kamera Baru
    function openAddCameraModal() {
      const titleEl = document.getElementById('camFormModalTitle');
      if (titleEl) titleEl.innerHTML = '<i class="fas fa-plus-circle text-info mr-2"></i> Tambah Kamera CCTV Baru';

      document.getElementById('cust-cam-id').value = '0';
      document.getElementById('cust-cam-title').value = '';
      document.getElementById('cust-cam-city').value = 'Lokal';
      document.getElementById('cust-cam-conn-type').value = 'rtsp';
      document.getElementById('cust-cam-rtsp').value = '';
      document.getElementById('cust-cam-hls').value = '';
      document.getElementById('cust-cam-rtsp-auth-type').value = 'auth';
      document.getElementById('cust-cam-rtsp-user').value = 'admin';
      document.getElementById('cust-cam-rtsp-pass').value = '';
      document.getElementById('cust-cam-rtsp-port').value = '554';
      document.getElementById('cust-cam-rtsp-ch').value = '1';
      document.getElementById('cust-cam-sn').value = '';
      document.getElementById('cust-cam-channel').value = '1';
      document.getElementById('cust-cam-status').value = 'online';

      const statusEl = document.getElementById('rtsp-detect-status');
      if (statusEl) statusEl.style.display = 'none';

      toggleConnTypeFields();
      toggleRtspAuthFields();
      openModal('modalCameraForm');
    }

    // Open Modal Edit Kamera
    function openEditCameraModal(camId) {
      const cam = localCameras.find(c => c.id == camId);
      if (!cam) return;

      const titleEl = document.getElementById('camFormModalTitle');
      if (titleEl) titleEl.innerHTML = '<i class="fas fa-cog text-info mr-2"></i> Edit Kamera CCTV';

      document.getElementById('cust-cam-id').value = cam.id;
      document.getElementById('cust-cam-title').value = cam.title || '';
      document.getElementById('cust-cam-city').value = cam.city || 'Lokal';
      document.getElementById('cust-cam-conn-type').value = cam.connection_type || 'rtsp';
      document.getElementById('cust-cam-rtsp').value = cam.rtsp_url || '';
      document.getElementById('cust-cam-hls').value = cam.hls_url || '';
      document.getElementById('cust-cam-sn').value = cam.serial_number || '';
      document.getElementById('cust-cam-channel').value = cam.channel || '1';
      document.getElementById('cust-cam-status').value = cam.status || 'online';

      const statusEl = document.getElementById('rtsp-detect-status');
      if (statusEl) statusEl.style.display = 'none';

      toggleConnTypeFields();
      toggleRtspAuthFields();
      openModal('modalCameraForm');
    }

    // Save Camera Submit Handler
    async function handleSaveCamera(e) {
      e.preventDefault();
      const btnSubmit = document.getElementById('btn-save-cam-submit');
      const origHtml = btnSubmit.innerHTML;

      const connType = document.getElementById('cust-cam-conn-type').value;
      let rtspVal = document.getElementById('cust-cam-rtsp').value.trim();
      const hlsVal = document.getElementById('cust-cam-hls').value.trim();
      const snVal = document.getElementById('cust-cam-sn').value.trim();

      const authType = document.getElementById('cust-cam-rtsp-auth-type').value;
      const rtspUser = document.getElementById('cust-cam-rtsp-user').value.trim() || 'admin';
      const rtspPass = document.getElementById('cust-cam-rtsp-pass').value.trim();
      const rtspPort = document.getElementById('cust-cam-rtsp-port').value.trim() || '554';
      const rtspCh = document.getElementById('cust-cam-rtsp-ch').value.trim() || '1';

      if (connType === 'rtsp') {
        if (!rtspVal && !hlsVal) {
          alert('Silakan masukkan Host / IP atau URL RTSP kamera Anda.');
          return;
        }
        // Auto format IP to RTSP scheme if user entered plain host
        if (rtspVal && !rtspVal.startsWith('rtsp://')) {
          if (authType === 'auth' && rtspPass) {
            rtspVal = `rtsp://${rtspUser}:${rtspPass}@${rtspVal}:${rtspPort}/user=${rtspUser}&password=${rtspPass}&channel=${rtspCh}&stream=0.sdp`;
          } else {
            rtspVal = `rtsp://${rtspVal}:${rtspPort}/stream0`;
          }
        }
      }

      if (connType === 'xmeye_p2p' && !snVal) {
        alert('Silakan masukkan Serial Number (Cloud ID) kamera XMeye Anda.');
        return;
      }

      btnSubmit.disabled = true;
      btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

      const fd = new FormData();
      fd.append('action', 'save_camera');
      fd.append('id', document.getElementById('cust-cam-id').value);
      fd.append('title', document.getElementById('cust-cam-title').value);
      fd.append('city', document.getElementById('cust-cam-city').value);
      fd.append('connection_type', connType);
      fd.append('rtsp_url', rtspVal);
      fd.append('hls_url', hlsVal);
      fd.append('serial_number', snVal);
      fd.append('channel', document.getElementById('cust-cam-channel').value);
      fd.append('status', document.getElementById('cust-cam-status').value);

      try {
        const res = await fetch('api.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          closeModal('modalCameraForm');
          loadCameras();
        } else {
          alert(data.message || 'Gagal menyimpan kamera.');
        }
      } catch (err) {
        alert('Terjadi kesalahan saat menyimpan data kamera.');
      } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = origHtml;
      }
    }

    // Delete Camera
    async function deleteCamera(camId) {
      if (!confirm('Apakah Anda yakin ingin menghapus kamera ini dari daftar sistem?')) return;
      const fd = new FormData();
      fd.append('action', 'delete_camera');
      fd.append('id', camId);

      try {
        const res = await fetch('api.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          loadCameras();
        } else {
          alert(data.message || 'Gagal menghapus kamera.');
        }
      } catch (err) {
        alert('Gagal menghubungi server.');
      }
    }

    // Load Cameras List
    async function loadCameras() {
      try {
        const res = await fetch('api.php?action=get_cameras');
        const data = await res.json();
        if (data.success) {
          localCameras = data.cameras || [];
          renderCameraGrid();
        }
      } catch (e) {}
    }

    // Render Camera Grid
    function renderCameraGrid() {
      const container = document.getElementById('camera-list-container');
      const emptyState = document.getElementById('empty-camera-state');
      if (!container) return;

      const totalEl = document.getElementById('stat-total-cams');
      const onlineEl = document.getElementById('stat-online-cams');
      if (totalEl) totalEl.innerText = localCameras.length;
      if (onlineEl) onlineEl.innerText = localCameras.filter(c => c.status === 'online').length;

      if (!localCameras || localCameras.length === 0) {
        container.style.display = 'none';
        if (emptyState) emptyState.style.display = 'block';
        return;
      }

      container.style.display = 'grid';
      if (emptyState) emptyState.style.display = 'none';

      container.innerHTML = localCameras.map(cam => {
        const isOnline = (cam.status || 'online') === 'online';
        const connLabel = (cam.connection_type === 'xmeye_p2p') ? 'XMeye P2P' : (cam.connection_type === 'ipcamlive' ? 'IPCamLive' : 'RTSP Stream');
        const pathDisplay = cam.rtsp_url || cam.hls_url || (cam.serial_number ? `SN: ${cam.serial_number}` : 'Stream Lokal');

        return `
          <div class="camera-card">
            <div class="camera-preview-thumb" onclick="playLiveStream(${cam.id})">
              <img src="../assets/image/icon.png" alt="${cam.title}" style="opacity: 0.25; filter: grayscale(1);">
              <div class="status-badge ${isOnline ? 'online' : 'offline'}">
                <i class="fas fa-circle" style="font-size: 0.45rem;"></i>
                ${isOnline ? 'Online Live' : 'Offline'}
              </div>
              <div class="conn-type-badge">${connLabel}</div>
              <div class="play-overlay">
                <i class="fas fa-play"></i>
              </div>
            </div>

            <div class="camera-info">
              <h3 class="camera-title">${cam.title}</h3>
              <div class="camera-submeta">
                <span><i class="fas fa-map-marker-alt text-info mr-1"></i> ${cam.city || 'Lokal'}</span>
                <span>&bull;</span>
                <span>CH: ${cam.channel || 1}</span>
              </div>
              <div class="camera-rtsp-path" title="${pathDisplay}">
                <i class="fas fa-link mr-1 text-info"></i> ${pathDisplay}
              </div>

              <div class="camera-actions">
                <button class="btn btn-primary btn-sm" onclick="playLiveStream(${cam.id})" style="flex: 1;">
                  <i class="fas fa-play"></i> Live Preview
                </button>
                <button class="btn btn-outline btn-sm" onclick="openEditCameraModal(${cam.id})" title="Edit Konfigurasi Kamera">
                  <i class="fas fa-cog"></i>
                </button>
                <button class="btn btn-danger-outline btn-sm" onclick="deleteCamera(${cam.id})" title="Hapus Kamera">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        `;
      }).join('');
    }

    // Play Live Stream in Modal
    function playLiveStream(camId) {
      const cam = localCameras.find(c => c.id == camId);
      if (!cam) return;

      const titleEl = document.getElementById('playerModalTitle');
      const infoEl = document.getElementById('player-stream-info');
      const video = document.getElementById('live-video-player');
      const loader = document.getElementById('player-loading-spinner');

      if (titleEl) titleEl.innerHTML = `<i class="fas fa-video text-info mr-2"></i> ${cam.title}`;
      if (infoEl) infoEl.innerText = cam.hls_url || cam.rtsp_url || 'Stream Feed';

      openModal('modalLivePlayer');

      if (loader) loader.style.display = 'flex';

      // Clean existing HLS
      if (currentHlsInstance) {
        currentHlsInstance.destroy();
        currentHlsInstance = null;
      }

      const streamUrl = cam.hls_url || `https://stream.loewixcctv.com/${cam.streamPath || ('cam_live_' + cam.id)}/index.m3u8`;

      if (Hls.isSupported()) {
        const hls = new Hls({
          enableWorker: true,
          lowLatencyMode: true,
          manifestLoadingTimeOut: 15000,
          manifestLoadingMaxRetry: 6
        });
        currentHlsInstance = hls;
        hls.loadSource(streamUrl);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function () {
          if (loader) loader.style.display = 'none';
          video.play().catch(e => console.log('Autoplay prevented:', e));
        });
        hls.on(Hls.Events.ERROR, function (event, data) {
          if (data.fatal) {
            if (loader) loader.innerHTML = '<div style="color: #f87171;"><i class="fas fa-exclamation-circle"></i> Menunggu feed RTSP dari kamera...</div>';
          }
        });
      } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = streamUrl;
        video.addEventListener('loadedmetadata', function () {
          if (loader) loader.style.display = 'none';
          video.play();
        });
      }
    }

    function closeLivePlayer() {
      const video = document.getElementById('live-video-player');
      if (video) {
        video.pause();
        video.src = '';
      }
      if (currentHlsInstance) {
        currentHlsInstance.destroy();
        currentHlsInstance = null;
      }
      closeModal('modalLivePlayer');
    }

    // Auto load on init if logged in
    <?php if ($isLoggedIn): ?>
      document.addEventListener('DOMContentLoaded', () => {
        loadCameras();
      });
    <?php endif; ?>
  </script>
</body>
</html>
