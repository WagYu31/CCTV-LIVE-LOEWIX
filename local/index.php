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

// Deteksi otomatis lingkungan
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$reqUri = $_SERVER['REQUEST_URI'] ?? '';
$isLocalhost = in_array($httpHost, ['localhost', '127.0.0.1']) || (strpos($httpHost, 'localhost:') === 0) || (strpos($httpHost, '127.0.0.1:') === 0);

// Blokir akses publik ke /local pada domain loewixcctv.com (alihkan permanen ke /facedetection)
if (!$isLocalhost && (strpos($reqUri, '/local') !== false)) {
    header('Location: /facedetection/', true, 301);
    exit;
}

// Pastikan selalu ada trailing slash pada /facedetection/ agar pemanggilan relative 'api.php' tepat sasaran
if (!$isLocalhost && (rtrim(explode('?', $reqUri)[0], '/') === '/facedetection') && (substr(explode('?', $reqUri)[0], -1) !== '/')) {
    header('Location: /facedetection/', true, 301);
    exit;
}

$isSubdomain = (strpos($httpHost, 'facedetection.') !== false) || (basename($_SERVER['DOCUMENT_ROOT'] ?? '') === 'local');
$assetsBase = $isSubdomain ? 'https://loewixcctv.com/assets' : '../assets';
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Loewix Face Detection - Desktop & LAN Edition</title>
  
  <!-- PWA Meta Tags -->
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#0284c7">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Loewix Face Detection">
  <link rel="icon" type="image/png" href="<?= $assetsBase ?>/image/favicon-32x32.png">

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- HLS.js for Live Stream Video Player -->
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

  <!-- AI Biometric Face Recognition & Human Surveillance Engines -->
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.21.0/dist/tf.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/coco-ssd@2.2.3/dist/coco-ssd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh@0.4.1633559619/face_mesh.js" crossorigin="anonymous"></script>

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
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      display: none;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      color: #38bdf8;
      font-size: 0.85rem;
      font-weight: 600;
      z-index: 8;
      pointer-events: none;
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

    /* Main Navigation Tabs */
    .nav-tabs-group {
      display: flex;
      gap: 0.35rem;
      align-items: center;
      background: rgba(15, 23, 42, 0.7);
      padding: 0.3rem;
      border-radius: 12px;
      border: 1px solid var(--border-color);
    }
    .nav-tab-btn {
      background: transparent;
      border: 1px solid transparent;
      color: #94a3b8;
      font-size: 0.84rem;
      font-weight: 600;
      padding: 0.45rem 1rem;
      border-radius: 9px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s ease;
    }
    .nav-tab-btn:hover {
      color: #fff;
      background: rgba(255, 255, 255, 0.05);
    }
    .nav-tab-btn.active {
      background: linear-gradient(135deg, rgba(2, 132, 199, 0.35), rgba(56, 189, 248, 0.15));
      color: #38bdf8;
      border-color: rgba(56, 189, 248, 0.4);
      box-shadow: 0 0 14px rgba(56, 189, 248, 0.2);
    }

    /* AI Vision Layout (Gambar 2 Replica) */
    .ai-telemetry-card {
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(10, 30, 60, 0.9));
      border: 1.5px solid rgba(56, 189, 248, 0.4);
      border-radius: 16px;
      padding: 1.25rem 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }
    .ai-metrics-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-top: 1.2rem;
    }
    .ai-metric-box {
      background: rgba(15, 23, 42, 0.75);
      border: 1px solid rgba(56, 189, 248, 0.2);
      border-radius: 12px;
      padding: 0.9rem 1.1rem;
    }
    .ai-workspace {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 1.25rem;
      margin-bottom: 1.75rem;
    }
    @media (max-width: 1024px) {
      .ai-workspace {
        grid-template-columns: 1fr;
      }
    }
    .ai-video-card {
      background: #090e1a;
      border: 1px solid rgba(56, 189, 248, 0.3);
      border-radius: 16px;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .ai-screen-container {
      position: relative;
      width: 100%;
      aspect-ratio: 16/9;
      background: #020617;
      border-radius: 12px;
      overflow: hidden;
      border: 1.5px solid rgba(56, 189, 248, 0.25);
      box-shadow: 0 0 25px rgba(2, 132, 199, 0.12) inset, 0 10px 30px rgba(0, 0, 0, 0.6);
    }
    .ai-screen-container video {
      width: 100%;
      height: 100%;
      object-fit: contain;
      background: #020617;
      display: block;
    }
    .ai-screen-container canvas {
      position: absolute;
      pointer-events: none;
    }
    .ai-screen-hud-tl {
      position: absolute;
      top: 10px;
      left: 12px;
      display: flex;
      align-items: center;
      gap: 6px;
      background: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(6px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 6px;
      padding: 0.25rem 0.6rem;
      font-size: 0.72rem;
      font-weight: 700;
      color: #fff;
      z-index: 5;
      box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    }
    .ai-screen-hud-tr {
      position: absolute;
      top: 10px;
      right: 12px;
      display: flex;
      align-items: center;
      gap: 6px;
      background: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(6px);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 6px;
      padding: 0.25rem 0.6rem;
      font-size: 0.7rem;
      font-family: monospace;
      color: #38bdf8;
      z-index: 5;
      box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    }
    /* Tripwire People Counting External Bar (Di luar video screen agar layar CCTV 100% bersih) */
    .ai-tripwire-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.65rem;
      margin-bottom: 0.65rem;
      padding: 0.45rem 0.85rem;
      background: rgba(10, 15, 30, 0.92);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(56, 189, 248, 0.35);
      border-radius: 10px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.4);
      user-select: none;
      transition: all 0.3s ease;
      flex-wrap: wrap;
    }
    .tripwire-bar-left {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .tripwire-pulse-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #00f0ff;
      box-shadow: 0 0 8px #00f0ff;
      animation: tripwirePulse 1.8s infinite;
      flex-shrink: 0;
    }
    @keyframes tripwirePulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.4; transform: scale(0.85); }
    }
    .tripwire-bar-title {
      font-size: 0.72rem;
      font-weight: 800;
      color: #e2e8f0;
      letter-spacing: 0.4px;
      text-transform: uppercase;
      white-space: nowrap;
    }
    .tripwire-bar-stats {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      flex-wrap: wrap;
    }
    .tripwire-stat-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.22rem 0.65rem;
      border-radius: 6px;
      background: rgba(15, 23, 42, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.1);
      font-size: 0.72rem;
      font-weight: 700;
    }
    .tripwire-stat-pill.in {
      background: rgba(16, 185, 129, 0.14);
      border-color: rgba(16, 185, 129, 0.45);
      color: #34d399;
    }
    .tripwire-stat-pill.out {
      background: rgba(239, 68, 68, 0.14);
      border-color: rgba(239, 68, 68, 0.45);
      color: #f87171;
    }
    .tripwire-stat-pill.total {
      background: rgba(56, 189, 248, 0.14);
      border-color: rgba(56, 189, 248, 0.45);
      color: #38bdf8;
    }
    .tripwire-pill-label {
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.3px;
    }
    .tripwire-pill-val {
      font-size: 0.95rem;
      font-weight: 900;
      font-family: 'JetBrains Mono', monospace;
      line-height: 1;
    }
    .tripwire-stat-pill.in .tripwire-pill-val { color: #34d399; }
    .tripwire-stat-pill.out .tripwire-pill-val { color: #f87171; }
    .tripwire-stat-pill.total .tripwire-pill-val { color: #38bdf8; }
    .tripwire-bar-actions {
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }
    .tripwire-bar-btn {
      display: inline-flex;
      align-items: center;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #94a3b8;
      border-radius: 6px;
      padding: 0.25rem 0.6rem;
      font-size: 0.72rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .tripwire-bar-btn:hover {
      background: rgba(56, 189, 248, 0.25);
      border-color: #38bdf8;
      color: #fff;
    }
    .ai-activity-card {
      background: #090e1a;
      border: 1px solid rgba(56, 189, 248, 0.3);
      border-radius: 16px;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      max-height: 570px;
    }
    .ai-activity-list {
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 0.65rem;
      flex: 1;
      padding-right: 4px;
      margin-top: 0.75rem;
    }
    .ai-activity-item {
      background: rgba(15, 23, 42, 0.85);
      border: 1px solid rgba(56, 189, 248, 0.2);
      border-radius: 10px;
      padding: 0.65rem 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      transition: all 0.2s;
    }
    .ai-activity-item:hover {
      border-color: rgba(56, 189, 248, 0.5);
      transform: translateX(2px);
    }
    .ai-activity-thumb {
      width: 44px;
      height: 44px;
      border-radius: 8px;
      object-fit: cover;
      border: 1px solid rgba(56, 189, 248, 0.4);
      background: #000;
      flex-shrink: 0;
    }
    .ai-faces-box {
      background: #090e1a;
      border: 1px solid rgba(56, 189, 248, 0.3);
      border-radius: 16px;
      padding: 1.25rem 1.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .ai-faces-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
      gap: 1.1rem;
      margin-top: 1.25rem;
    }
    .ai-face-card {
      background: rgba(15, 23, 42, 0.75);
      border: 1px solid rgba(56, 189, 248, 0.2);
      border-radius: 14px;
      padding: 1.1rem;
      text-align: center;
      position: relative;
      transition: all 0.25s ease;
    }
    .ai-face-card:hover {
      border-color: rgba(56, 189, 248, 0.6);
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(2, 132, 199, 0.2);
    }
    .ai-face-avatar {
      width: 76px;
      height: 76px;
      border-radius: 50%;
      object-fit: cover;
      margin: 0 auto 0.65rem;
      border: 2px solid #38bdf8;
      box-shadow: 0 0 16px rgba(56, 189, 248, 0.35);
      background: #000;
    }
    /* AI Video Player Modern Toolbar & Controls */
    .ai-video-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      flex-wrap: wrap;
      margin-bottom: 0.65rem;
      padding-bottom: 0.65rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .ai-vh-left {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      flex-wrap: wrap;
    }
    .ai-vh-right {
      display: flex;
      align-items: center;
      gap: 0.45rem;
      flex-wrap: wrap;
    }
    .ai-live-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #ef4444;
      font-size: 0.72rem;
      font-weight: 800;
      letter-spacing: 0.5px;
      padding: 0.28rem 0.55rem;
      border-radius: 6px;
    }
    .ai-live-badge .live-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #ef4444;
      box-shadow: 0 0 8px #ef4444;
      animation: pulseLiveDot 1.5s infinite;
    }
    @keyframes pulseLiveDot {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.4; transform: scale(0.85); }
    }
    .ai-cam-dropdown-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      gap: 6px;
      background: rgba(15, 23, 42, 0.95);
      border: 1px solid rgba(56, 189, 248, 0.35);
      border-radius: 8px;
      padding: 0 0.65rem;
      height: 32px;
      min-width: 210px;
    }
    .ai-cam-dropdown-wrapper:hover {
      border-color: #38bdf8;
      box-shadow: 0 0 10px rgba(56, 189, 248, 0.25);
    }
    .ai-cam-select {
      background: transparent;
      border: none;
      color: #fff;
      font-size: 0.8rem;
      font-weight: 600;
      outline: none;
      width: 100%;
      appearance: none;
      cursor: pointer;
      padding-right: 1.2rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .ai-cam-select option {
      background: #0f172a;
      color: #fff;
    }
    .ai-dropdown-arrow {
      position: absolute;
      right: 0.65rem;
      pointer-events: none;
      font-size: 0.7rem;
      color: #94a3b8;
    }
    .ai-btn-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      height: 32px;
      padding: 0 0.65rem;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.12);
      color: #cbd5e1;
      font-size: 0.76rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .ai-btn-pill:hover {
      background: rgba(56, 189, 248, 0.15);
      border-color: rgba(56, 189, 248, 0.4);
      color: #38bdf8;
    }
    .ai-btn-pill.active {
      background: rgba(16, 185, 129, 0.2);
      border-color: rgba(16, 185, 129, 0.5);
      color: #34d399;
    }
    .ai-btn-pill.active-cyan {
      background: rgba(56, 189, 248, 0.2);
      border-color: rgba(56, 189, 248, 0.5);
      color: #38bdf8;
    }
    .ai-tag-chip {
      font-size: 0.72rem;
      font-weight: 700;
      color: #94a3b8;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      padding: 0.2rem 0.5rem;
      border-radius: 6px;
    }

    /* Channel Selector Strip (Enterprise NVR Matrix) */
    .ai-nvr-strip-wrapper {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      margin-bottom: 0.65rem;
      padding: 0.35rem 0.55rem;
      background: rgba(15, 23, 42, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.07);
      border-radius: 10px;
    }
    .ai-strip-label {
      font-size: 0.7rem;
      font-weight: 800;
      color: #94a3b8;
      letter-spacing: 0.5px;
      white-space: nowrap;
      display: flex;
      align-items: center;
    }
    .ai-channel-strip {
      display: flex;
      align-items: center;
      gap: 0.35rem;
      overflow-x: auto;
      scrollbar-width: none;
      flex: 1;
    }
    .ai-channel-strip::-webkit-scrollbar {
      display: none;
    }
    .ai-nvr-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      min-width: 48px;
      height: 28px;
      padding: 0 0.55rem;
      border-radius: 6px;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #94a3b8;
      font-size: 0.74rem;
      font-weight: 700;
      cursor: pointer;
      white-space: nowrap;
      transition: all 0.2s ease;
    }
    .ai-nvr-btn:hover {
      background: rgba(56, 189, 248, 0.15);
      border-color: rgba(56, 189, 248, 0.5);
      color: #38bdf8;
    }
    .ai-nvr-btn.active {
      background: linear-gradient(135deg, rgba(2, 132, 199, 0.45), rgba(56, 189, 248, 0.3));
      border: 1.5px solid #38bdf8;
      color: #ffffff;
      box-shadow: 0 0 10px rgba(56, 189, 248, 0.35);
    }
    .ai-nvr-btn .status-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    /* Video Footer */
    .ai-video-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-top: 0.65rem;
      font-size: 0.76rem;
      color: #94a3b8;
    }
    .ai-footer-info {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .ai-footer-dot {
      color: #475569;
    }
    .ai-footer-engine {
      display: flex;
      align-items: center;
      gap: 6px;
      font-family: monospace;
      font-size: 0.72rem;
      color: #38bdf8;
    }
    .ai-cctv-card {
      background: rgba(15, 23, 42, 0.75);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      padding: 0.95rem;
      transition: all 0.2s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
    }
    .ai-cctv-card:hover {
      border-color: rgba(56, 189, 248, 0.4);
      transform: translateY(-2px);
      background: rgba(15, 23, 42, 0.95);
    }
    .ai-cctv-card.active {
      border-color: #38bdf8;
      box-shadow: 0 0 16px rgba(56, 189, 248, 0.3);
      background: rgba(2, 132, 199, 0.12);
    }
    .btn-outline-info {
      background: transparent;
      border: 1px solid rgba(56, 189, 248, 0.5);
      color: #38bdf8;
    }
    .btn-outline-info:hover {
      background: rgba(56, 189, 248, 0.15);
      color: #fff;
      border-color: #38bdf8;
    }

    /* ==========================================================================
       VISITOR INTELLIGENCE & FREQUENCY ANALYTICS STYLES
       ========================================================================== */
    .analytics-container {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      padding-bottom: 3rem;
    }
    .analytics-hero-card {
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.8) 100%);
      border: 1px solid rgba(56, 189, 248, 0.25);
      border-radius: 1rem;
      padding: 1.5rem 1.75rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 0 15px rgba(56, 189, 248, 0.08);
    }
    .analytics-kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem;
    }
    .kpi-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: 0.85rem;
      padding: 1.25rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      transition: all 0.25s ease;
      position: relative;
      overflow: hidden;
    }
    .kpi-card:hover {
      transform: translateY(-2px);
      border-color: rgba(56, 189, 248, 0.4);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
    }
    .kpi-icon {
      width: 48px;
      height: 48px;
      border-radius: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      flex-shrink: 0;
    }
    .kpi-val {
      font-size: 1.65rem;
      font-weight: 800;
      color: #fff;
      line-height: 1.2;
    }
    .kpi-label {
      font-size: 0.78rem;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }
    .kpi-sub {
      font-size: 0.74rem;
      color: #64748b;
      margin-top: 2px;
    }

    /* Peak Hours Traffic Chart */
    .traffic-chart-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: 0.85rem;
      padding: 1.5rem;
    }
    .traffic-chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.25rem;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    .traffic-chart-bars {
      display: flex;
      align-items: flex-end;
      gap: 6px;
      height: 140px;
      padding-top: 25px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .traffic-bar-col {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      height: 100%;
      justify-content: flex-end;
      position: relative;
      cursor: pointer;
    }
    .traffic-bar {
      width: 100%;
      max-width: 24px;
      background: linear-gradient(180deg, #38bdf8 0%, #0284c7 100%);
      border-radius: 4px 4px 0 0;
      min-height: 4px;
      transition: height 0.6s cubic-bezier(0.4, 0, 0.2, 1), filter 0.2s;
    }
    .traffic-bar-col:hover .traffic-bar {
      filter: brightness(1.3);
      box-shadow: 0 0 10px rgba(56, 189, 248, 0.6);
    }
    .traffic-bar.peak {
      background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%);
      box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }
    .traffic-bar-label {
      font-size: 0.68rem;
      color: #64748b;
      margin-top: 6px;
      white-space: nowrap;
    }
    .traffic-bar-tooltip {
      position: absolute;
      top: -22px;
      font-size: 0.68rem;
      font-weight: 700;
      color: #38bdf8;
      opacity: 0;
      transition: opacity 0.2s;
      pointer-events: none;
      background: rgba(15, 23, 42, 0.9);
      padding: 2px 6px;
      border-radius: 4px;
      border: 1px solid rgba(56, 189, 248, 0.4);
    }
    .traffic-bar-col:hover .traffic-bar-tooltip {
      opacity: 1;
    }

    /* Search & Filter Toolbar */
    .investigation-toolbar {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: 0.85rem;
      padding: 1.25rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .toolbar-title-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    .toolbar-inputs-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr auto;
      gap: 0.75rem;
      align-items: center;
    }
    @media (max-width: 992px) {
      .toolbar-inputs-grid {
        grid-template-columns: 1fr 1fr;
      }
    }
    @media (max-width: 600px) {
      .toolbar-inputs-grid {
        grid-template-columns: 1fr;
      }
    }

    /* Visitor Cards Grid */
    .visitor-cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1rem;
    }
    .visitor-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: 0.85rem;
      padding: 1.25rem;
      display: flex;
      flex-direction: column;
      gap: 0.85rem;
      transition: all 0.2s ease;
      position: relative;
    }
    .visitor-card:hover {
      border-color: rgba(56, 189, 248, 0.4);
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
    }
    .visitor-card-top {
      display: flex;
      align-items: center;
      gap: 0.85rem;
    }
    .visitor-avatar {
      width: 56px;
      height: 56px;
      border-radius: 0.75rem;
      object-fit: cover;
      background: var(--bg-surface-elevated);
      border: 2px solid rgba(255, 255, 255, 0.1);
      flex-shrink: 0;
    }
    .visitor-info {
      flex: 1;
      min-width: 0;
    }
    .visitor-name {
      font-size: 0.95rem;
      font-weight: 700;
      color: #fff;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .visitor-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 8px;
      border-radius: 9999px;
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      margin-top: 3px;
    }
    .badge-karyawan { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .badge-vip { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .badge-stranger { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }
    .badge-blacklist { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

    .visitor-meta-row {
      display: flex;
      justify-content: space-between;
      font-size: 0.76rem;
      color: #94a3b8;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
      padding-top: 0.65rem;
    }
    .visitor-actions {
      display: flex;
      gap: 0.5rem;
      margin-top: 0.25rem;
    }

    /* Timeline and Dossier */
    .dossier-timeline-item {
      display: flex;
      gap: 1rem;
      padding: 0.75rem 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      align-items: center;
    }
    .dossier-snap-thumb {
      width: 48px;
      height: 48px;
      border-radius: 6px;
      object-fit: cover;
      border: 1px solid rgba(255, 255, 255, 0.1);
      flex-shrink: 0;
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
          LOEWIX Face Detection
          <span class="brand-tag">Local Desktop Edition</span>
        </div>
      </div>
    </div>

    <?php if ($isLoggedIn): ?>
    <div class="nav-tabs-group">
      <button type="button" class="nav-tab-btn active" id="tab-btn-cctv" onclick="switchMainTab('cctv')">
        <i class="fas fa-video text-info"></i> Channel CCTV
      </button>
      <button type="button" class="nav-tab-btn" id="tab-btn-ai" onclick="switchMainTab('ai')">
        <i class="fas fa-brain text-info"></i> AI Face Recognition
      </button>
      <button type="button" class="nav-tab-btn" id="tab-btn-analytics" onclick="switchMainTab('analytics')">
        <i class="fas fa-chart-line text-info"></i> Data & Analitik Kunjungan
      </button>
    </div>
    <?php endif; ?>

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
      <h2 class="login-title">Masuk ke LOEWIX Face Detection</h2>
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
    
    <!-- PANE 1: CCTV CAMERA MANAGEMENT -->
    <div id="view-cctv-pane">
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
    </div> <!-- /#view-cctv-pane -->

    <!-- ==================== PANE 2: AI FACE RECOGNITION (Gambar 2 Replica) ==================== -->
    <div id="view-ai-pane" style="display: none;">
      
      <!-- AI Telemetry & Status Header -->
      <div class="ai-telemetry-card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
          <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; border-radius: 14px; background: linear-gradient(135deg, #0284c7, #38bdf8); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #fff; box-shadow: 0 0 20px rgba(56, 189, 248, 0.5);">
              <i class="fas fa-brain"></i>
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin: 0; letter-spacing: -0.3px;">Loewix Neural Vision Suite</h2>
                <span style="font-size: 0.65rem; font-weight: 800; background: rgba(56,189,248,0.2); border: 1px solid rgba(56,189,248,0.4); color: #38bdf8; border-radius: 6px; padding: 0.2rem 0.5rem;">
                  AI COMPUTER VISION V3
                </span>
              </div>
              <p style="color: #94a3b8; font-size: 0.82rem; margin: 0;">
                Pengenalan Wajah Otomatis (Face Recognition) & Analitik Biometrik Wajah Real-Time Jaringan Lokal.
              </p>
            </div>
          </div>

          <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; font-size: 0.78rem; color: #34d399; display: flex; align-items: center; gap: 6px; padding: 0.4rem 0.8rem;">
              <span class="status-indicator online"></span>
              <strong>NEURAL ENGINE: ONLINE</strong>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="loadAIFaceData(true)">
              <i class="fas fa-sync mr-1"></i> Segarkan Data AI
            </button>
          </div>
        </div>

        <!-- AI Metrics Row (Face Recognition Only) -->
        <div class="ai-metrics-row">
          <div class="ai-metric-box">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
              <span style="font-size: 0.78rem; font-weight: 600; color: #94a3b8;">Wajah Terdaftar</span>
              <i class="fas fa-user-check text-info"></i>
            </div>
            <div class="ai-metric-num" id="ai-stat-faces">0</div>
            <small style="color: #38bdf8; font-size: 0.72rem;">VIP & Karyawan Aktif</small>
          </div>

          <div class="ai-metric-box">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
              <span style="font-size: 0.78rem; font-weight: 600; color: #94a3b8;">Deteksi Hari Ini</span>
              <i class="fas fa-bolt" style="color: #f59e0b;"></i>
            </div>
            <div class="ai-metric-num" id="ai-stat-detections">0</div>
            <small style="color: #f59e0b; font-size: 0.72rem;">Akurasi Rata-rata 97.4%</small>
          </div>

          <div class="ai-metric-box">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
              <span style="font-size: 0.78rem; font-weight: 600; color: #94a3b8;">Alert Blacklist</span>
              <i class="fas fa-shield-virus" style="color: #ef4444;"></i>
            </div>
            <div class="ai-metric-num" id="ai-stat-blacklist" style="color: #ef4444;">0</div>
            <small style="color: #ef4444; font-size: 0.72rem;">Notifikasi Keamanan</small>
          </div>

          <div class="ai-metric-box">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
              <span style="font-size: 0.78rem; font-weight: 600; color: #94a3b8;">Kamera Terhubung</span>
              <i class="fas fa-video text-success" style="color: #10b981;"></i>
            </div>
            <div class="ai-metric-num" id="ai-stat-cameras">0</div>
            <small style="color: #10b981; font-size: 0.72rem;">Siap Scan Multi-Channel</small>
          </div>
        </div>
      </div>

      <!-- Main Live AI Vision Workspace (Video + Canvas Overlay + Controls) -->
      <div class="ai-workspace">
        
        <!-- Left: Live AI Scanner Stream with Clean Controls & HUD Overlay -->
        <div class="ai-video-card">
          <!-- Unified Header Toolbar -->
          <div class="ai-video-header">
            <div class="ai-vh-left">
              <div class="ai-live-badge">
                <span class="live-dot"></span>
                <span>AI LIVE</span>
              </div>
              <div class="ai-cam-dropdown-wrapper">
                <i class="fas fa-video text-info" style="font-size: 0.75rem;"></i>
                <select id="ai-camera-selector" class="ai-cam-select" onchange="changeAICamera(this.value)">
                  <option value="webcam">Live Webcam Laptop</option>
                </select>
                <i class="fas fa-chevron-down ai-dropdown-arrow"></i>
              </div>
              <span id="ai-feed-resolution" class="ai-tag-chip">
                <i class="fas fa-signal text-success mr-1"></i> RTSP 1080p
              </span>
            </div>

            <div class="ai-vh-right">
              <button id="btn-toggle-counting-line" class="ai-btn-pill" onclick="openCountingLineModal()" title="Garis Penghitung Orang (Tripwire Counter)" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.25), rgba(59, 130, 246, 0.35)); border-color: #38bdf8; color: #38bdf8; font-weight: 700;">
                <i class="fas fa-ruler-combined mr-1"></i> Garis Hitung
              </button>
              <button class="ai-btn-pill" onclick="quickTagCurrentPerson()" title="Beri Nama / Tandai Orang (Karyawan vs Pengunjung)" style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.25), rgba(16, 185, 129, 0.35)); border-color: #10b981; color: #34d399; font-weight: 700;">
                <i class="fas fa-user-tag mr-1"></i> Beri Nama
              </button>
              <button class="ai-btn-pill" onclick="resetAllCameraTags()" title="Reset / Hapus Semua Tag Nama Kamera Ini" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.45); color: #f87171; font-weight: 700;">
                <i class="fas fa-eraser mr-1"></i> Reset Tag
              </button>
              <button id="btn-toggle-autoscan" class="ai-btn-pill active" onclick="toggleAIAutoScan()" title="Auto Scan Wajah Otomatis">
                <i class="fas fa-bolt mr-1"></i> Auto-Scan: AKTIF
              </button>
              <button id="btn-toggle-sound" class="ai-btn-pill active" onclick="toggleAISoundAlertManual()" title="Bunyi Suara Notifikasi">
                <i class="fas fa-volume-up mr-1 text-info" id="ai-sound-icon"></i> Suara
              </button>
              <button class="ai-btn-pill" onclick="scanCurrentFrameManual()" title="Pindai Frame Ini">
                <i class="fas fa-crosshairs mr-1"></i> Scan Frame
              </button>
              <button class="ai-btn-pill" onclick="toggleAIFullscreen()" title="Tampilan Layar Penuh">
                <i class="fas fa-expand text-warning"></i>
              </button>
            </div>
          </div>

          <!-- Channel Selector Strip (Enterprise NVR Matrix) -->
          <div class="ai-nvr-strip-wrapper">
            <span class="ai-strip-label">
              <i class="fas fa-th-large text-info mr-1"></i> CHANNEL:
            </span>
            <div class="ai-channel-strip" id="ai-channel-quick-pills">
              <!-- Dynamically populated: [Webcam] [CH 1] [CH 2] ... [CH 9] -->
            </div>
          </div>

          <!-- Tripwire People Counting Bar (Di luar video screen agar feed CCTV 100% bersih tanpa halangan) -->
          <div id="ai-tripwire-hud" class="ai-tripwire-bar" style="display: none;">
            <div class="tripwire-bar-left">
              <span class="tripwire-pulse-dot" id="tripwire-dot"></span>
              <span id="tripwire-hud-title" class="tripwire-bar-title">GARIS HITUNG: DUA ARAH</span>
            </div>
            <div class="tripwire-bar-stats" id="tripwire-hud-stats">
              <div class="tripwire-stat-pill in" id="tripwire-stat-in">
                <span class="tripwire-pill-label">🟢 MASUK</span>
                <span class="tripwire-pill-val" id="tripwire-val-in">0</span>
              </div>
              <div class="tripwire-stat-pill out" id="tripwire-stat-out">
                <span class="tripwire-pill-label">🔴 KELUAR</span>
                <span class="tripwire-pill-val" id="tripwire-val-out">0</span>
              </div>
              <div class="tripwire-stat-pill total" id="tripwire-stat-total">
                <span class="tripwire-pill-label">📊 TOTAL</span>
                <span class="tripwire-pill-val" id="tripwire-val-total">0</span>
              </div>
            </div>
            <div class="tripwire-bar-actions">
              <button type="button" class="tripwire-bar-btn" onclick="openCountingLineModal()" title="Pengaturan Garis">
                <i class="fas fa-cog mr-1"></i> Atur
              </button>
              <button type="button" class="tripwire-bar-btn" onclick="resetCountingLineStats()" title="Reset Hitungan ke 0">
                <i class="fas fa-undo mr-1"></i> Reset
              </button>
            </div>
          </div>

          <!-- Video & Canvas Screen (Layar Bersih & Bebas Halangan) -->
          <div class="ai-screen-container" id="ai-screen-box">
            <!-- HUD Overlays -->
            <div class="ai-screen-hud-tl" id="ai-screen-hud-cam-title">
              <i class="fas fa-video text-info"></i>
              <span id="ai-hud-cam-name">[CH 2] RTSP LOCAL STG</span>
            </div>
            <div class="ai-screen-hud-tr">
              <span class="status-indicator online" style="margin-right: 4px;"></span>
              <span id="ai-hud-model-mode">COCO-SSD HUMAN • 30 FPS</span>
            </div>

            <video id="ai-video-player" playsinline muted autoplay crossorigin="anonymous"></video>
            <canvas id="ai-canvas-overlay"></canvas>
            
            <div id="ai-video-loader" class="player-loader" style="display: none;">
              <div class="spinner"></div>
              <div id="ai-loader-text">Menghubungkan Stream...</div>
            </div>

            <!-- Recognition Banner Popup Overlay -->
            <div id="ai-hud-banner" style="display: none; position: absolute; bottom: 20px; left: 14px; background: rgba(15, 23, 42, 0.95); border: 1.5px solid #38bdf8; border-radius: 12px; padding: 0.65rem 1rem; align-items: center; gap: 0.75rem; box-shadow: 0 8px 30px rgba(0,0,0,0.8); z-index: 10;">
              <div id="ai-hud-icon" style="width: 38px; height: 38px; border-radius: 50%; background: rgba(56, 189, 248, 0.2); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 1.1rem;">
                <i class="fas fa-user-check"></i>
              </div>
              <div>
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                  <strong id="ai-hud-name" style="color: #fff; font-size: 0.92rem;">WAHYU UTOMO</strong>
                  <span id="ai-hud-badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; font-size: 0.68rem; padding: 0.15rem 0.45rem; border-radius: 5px; font-weight: 700;">VIP</span>
                </div>
                <small id="ai-hud-sub" style="color: #94a3b8; font-size: 0.75rem;">Akses Pintu Diberikan • 98.2% Similarity</small>
            </div>
          </div>

          <!-- Live Detected Personnel & Quick Naming Interactive Strip -->
          <div id="ai-live-detected-strip" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; background: rgba(10, 15, 29, 0.95); border: 1px solid rgba(56, 189, 248, 0.25); border-top: none; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 0.55rem 0.85rem; margin-top: -3px; margin-bottom: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.78rem; color: #cbd5e1; font-weight: 700;">
              <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 8px #10b981;"></span>
              <span>ORANG DI LAYAR:</span>
              <span id="ai-active-count-badge" class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; font-size: 0.72rem; padding: 0.15rem 0.45rem; border-radius: 6px;">0 Orang</span>
            </div>
            <div id="ai-active-chips-container" style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
              <span style="color: #64748b; font-size: 0.75rem; font-style: italic;">Klik kotak orang di video untuk memberi nama & status (Karyawan / Pengunjung)</span>
            </div>
          </div>

          <!-- Video Clean Footer Info -->
          <div class="ai-video-footer">
            <div class="ai-footer-info">
              <span id="ai-footer-cam-name" style="color: #38bdf8; font-weight: 600;">
                <i class="fas fa-shield-alt mr-1"></i> Aktif: [CH 2] RTSP LOCAL STG
              </span>
              <span class="ai-footer-dot">&bull;</span>
              <span id="ai-footer-latency" style="color: #94a3b8;">Latency ~180ms</span>
            </div>
            <div class="ai-footer-engine" id="ai-engine-indicator">
              <span class="status-indicator online"></span>
              Neural Vision: Face-API.js 128D ResNet
            </div>
          </div>
        </div>

        <!-- Right: Live Activity Detection Log (Gambar 2 Replica) -->
        <div class="ai-activity-card">
          <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="display: flex; align-items: center; gap: 0.45rem; font-weight: 700; color: #fff; font-size: 0.88rem;">
              <i class="fas fa-bolt" style="color: #f59e0b;"></i> LIVE STREAM DETEKSI AI
            </div>
            <button class="btn btn-outline btn-sm" onclick="clearAILogs()" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">
              Bersihkan
            </button>
          </div>

          <div class="ai-activity-list" id="ai-activity-log-container">
            <div style="text-align: center; padding: 2rem 1rem; color: #64748b; font-size: 0.82rem;">
              <i class="fas fa-radar fa-spin mb-2" style="font-size: 1.5rem; color: #38bdf8;"></i>
              <div>Menunggu deteksi wajah di kamera...</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section: Grid Channel CCTV Lokal untuk AI Face Recognition -->
      <div class="ai-cctv-grid-section" style="margin-top: 0.5rem; margin-bottom: 1.75rem; background: #090e1a; border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 16px; padding: 1.25rem 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.6rem; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.08);">
          <div style="display: flex; align-items: center; gap: 0.65rem;">
            <div style="width: 36px; height: 36px; border-radius: 9px; background: rgba(56, 189, 248, 0.15); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 1rem;">
              <i class="fas fa-th-large"></i>
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 0.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #fff; margin: 0;">Pilih Channel CCTV untuk AI Face Recognition</h3>
                <span class="badge" style="background: rgba(56,189,248,0.15); color: #38bdf8; font-size: 0.72rem; padding: 0.15rem 0.5rem; border-radius: 6px;" id="ai-cctv-count-badge">0 Kamera</span>
              </div>
              <small style="color: #94a3b8; font-size: 0.76rem;">Pilih feed kamera CCTV di bawah untuk memindai dan mengenali wajah VIP / Karyawan secara real-time pada CCTV tersebut.</small>
            </div>
          </div>

          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="startAIWebcamLive()" style="font-size: 0.78rem;">
              <i class="fas fa-camera text-info mr-1"></i> Mode Webcam Laptop
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="loadAIFaceData(true)" style="font-size: 0.78rem;">
              <i class="fas fa-sync-alt mr-1"></i> Segarkan Channel
            </button>
          </div>
        </div>

        <!-- Grid of Camera Cards -->
        <div id="ai-cctv-channels-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 0.9rem;">
          <!-- Injected via JavaScript -->
        </div>
      </div>

      <!-- Bottom: Enrolled Faces Database (Gambar 2 Replica) -->
      <div class="ai-faces-box">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.8rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.08);">
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="badge" style="background: rgba(2, 132, 199, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.85rem; padding: 0.45rem 0.9rem; border-radius: 9px; font-weight: 700;">
              <i class="fas fa-address-book mr-1.5"></i> Database Wajah Terdaftar (<span id="ai-faces-count-badge">0</span>)
            </div>
          </div>

          <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.6rem;">
            <button class="btn btn-success btn-sm" onclick="openEnrollFaceModal()" style="font-size: 0.84rem; padding: 0.45rem 1rem; background: linear-gradient(135deg, #059669, #10b981); border: none;">
              <i class="fas fa-user-plus mr-1"></i> + Daftarkan Wajah Baru
            </button>
            <button class="btn btn-secondary btn-sm" onclick="loadAIFaceData(true)" style="font-size: 0.84rem; padding: 0.45rem 0.85rem;">
              <i class="fas fa-sync-alt mr-1"></i> Sinkron Database AI
            </button>
            <button class="btn btn-danger-outline btn-sm" onclick="resetAllFacesPrompt()" style="font-size: 0.84rem; padding: 0.45rem 0.85rem;">
              <i class="fas fa-trash-alt mr-1"></i> Reset Semua Wajah
            </button>
          </div>
        </div>

        <!-- Search Bar -->
        <div style="margin-top: 1rem; display: flex; gap: 0.6rem;">
          <input type="text" id="ai-search-face-input" class="form-control" placeholder="Cari nama atau jabatan wajah terdaftar..." oninput="filterAIFaces(this.value)" style="font-size: 0.85rem; max-width: 380px;">
        </div>

        <!-- Faces Grid -->
        <div class="ai-faces-grid" id="ai-faces-grid-container">
          <!-- Injected via JavaScript -->
        </div>
      </div>
    </div> <!-- /#view-ai-pane -->

    <!-- ==================== VIEW 3: DATA & ANALITIK KUNJUNGAN ==================== -->
    <div id="view-analytics-pane" style="display: none;">
      <div class="analytics-container">
        
        <!-- Hero Header -->
        <div class="analytics-hero-card">
          <div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: #fff; margin: 0 0 0.35rem 0; display: flex; align-items: center; gap: 0.6rem;">
              <i class="fas fa-chart-line text-info"></i> Loewix Daily Visitor Intelligence & Analytics
            </h2>
            <p style="font-size: 0.84rem; color: #94a3b8; margin: 0;">
              Pelacakan Kunjungan Harian (Karyawan & Stranger), Frekuensi Kehadiran, dan Rekam Jejak Investigasi Presisi Waktu Detik.
            </p>
          </div>
          <div style="display: flex; gap: 0.6rem; align-items: center;">
            <button class="btn btn-outline btn-sm" onclick="loadVisitorAnalytics()" title="Segarkan Data Terkini">
              <i class="fas fa-sync-alt mr-1"></i> Segarkan Data
            </button>
            <button class="btn btn-primary btn-sm" onclick="window.print()" title="Cetak Laporan Investigasi">
              <i class="fas fa-print mr-1"></i> Cetak Laporan
            </button>
          </div>
        </div>

        <!-- KPI Stat Cards -->
        <div class="analytics-kpi-grid">
          <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <div class="kpi-val" id="analytics-stat-total">0</div>
              <div class="kpi-label">Total Kunjungan Hari Ini</div>
              <div class="kpi-sub" id="analytics-sub-unique">0 orang terdeteksi</div>
            </div>
          </div>

          <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
              <i class="fas fa-id-badge"></i>
            </div>
            <div>
              <div class="kpi-val" id="analytics-stat-karyawan">0</div>
              <div class="kpi-label">Karyawan / Personil</div>
              <div class="kpi-sub">Terverifikasi Biometrik</div>
            </div>
          </div>

          <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
              <i class="fas fa-user-secret"></i>
            </div>
            <div>
              <div class="kpi-val" id="analytics-stat-stranger">0</div>
              <div class="kpi-label">Stranger / Tamu Baru</div>
              <div class="kpi-sub">Wajah Tak Dikenal Terpantau</div>
            </div>
          </div>

          <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
              <i class="fas fa-shield-alt"></i>
            </div>
            <div>
              <div class="kpi-val" id="analytics-stat-blacklist">0</div>
              <div class="kpi-label">Waspada / Alert</div>
              <div class="kpi-sub">Notifikasi Keamanan</div>
            </div>
          </div>
        </div>

        <!-- Peak Hours Traffic Distribution Chart -->
        <div class="traffic-chart-card">
          <div class="traffic-chart-header">
            <div>
              <h3 style="font-size: 1rem; font-weight: 700; color: #fff; margin: 0 0 0.25rem 0;">
                <i class="fas fa-chart-bar text-info mr-1"></i> Tren Jam Ramai Kunjungan Hari Ini (Peak Traffic Hours)
              </h3>
              <p style="font-size: 0.78rem; color: #94a3b8; margin: 0;">
                Distribusi frekuensi orang lewat dan berkunjung per jam (00:00 - 23:00 WIB).
              </p>
            </div>
            <div id="analytics-peak-hour-badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); padding: 4px 10px; border-radius: 9999px; font-size: 0.74rem; font-weight: 700;">
              <i class="fas fa-fire mr-1"></i> Jam Paling Ramai: Menghitung...
            </div>
          </div>
          
          <div class="traffic-chart-bars" id="analytics-traffic-bars-container">
            <!-- Injected via JavaScript (24 Hour Bars) -->
          </div>
        </div>

        <!-- Search & Incident Investigation Filter Toolbar -->
        <div class="investigation-toolbar">
          <div class="toolbar-title-row">
            <div>
              <h3 style="font-size: 1rem; font-weight: 700; color: #fff; margin: 0 0 0.25rem 0;">
                <i class="fas fa-search-location text-info mr-1"></i> Pusat Investigasi & Pencarian Rekam Jejak Pelintas
              </h3>
              <p style="font-size: 0.78rem; color: #94a3b8; margin: 0;">
                Cari data orang berdasarkan nama, ID Stranger, tanggal, jam, atau kamera saat ada masalah/insiden.
              </p>
            </div>
            <button class="btn btn-outline btn-sm" onclick="resetVisitorFilters()" style="font-size: 0.78rem;">
              <i class="fas fa-undo mr-1"></i> Reset Filter
            </button>
          </div>

          <div class="toolbar-inputs-grid">
            <div>
              <input type="text" id="investigation-keyword-input" class="form-control" placeholder="Cari Nama Orang, ID Stranger (STR-...), atau Catatan..." onkeydown="if(event.key==='Enter') executeVisitorSearch()">
            </div>
            <div>
              <select id="investigation-category-select" class="form-control" onchange="executeVisitorSearch()">
                <option value="all">Semua Kategori</option>
                <option value="employee">Karyawan / Staff</option>
                <option value="vip">VIP / Direksi</option>
                <option value="stranger">Stranger (Orang Tak Dikenal)</option>
                <option value="blacklist">Blacklist / Waspada</option>
              </select>
            </div>
            <div>
              <select id="investigation-date-select" class="form-control" onchange="executeVisitorSearch()">
                <option value="all">Semua Riwayat Waktu</option>
                <option value="today" selected>Hari Ini</option>
                <option value="yesterday">Kemarin</option>
                <option value="last_7_days">7 Hari Terakhir</option>
                <option value="last_30_days">30 Hari Terakhir</option>
              </select>
            </div>
            <div>
              <select id="investigation-camera-select" class="form-control" onchange="executeVisitorSearch()">
                <option value="0">Semua Kamera CCTV</option>
                <!-- Injected via JavaScript -->
              </select>
            </div>
            <div>
              <button class="btn btn-primary" onclick="executeVisitorSearch()" style="width: 100%; white-space: nowrap;">
                <i class="fas fa-search mr-1"></i> Cari Data
              </button>
            </div>
          </div>
        </div>

        <!-- Section: Daftar Orang & Frekuensi Kunjungan -->
        <div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #fff; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
              <i class="fas fa-user-clock text-info"></i> Daftar Pelintas & Frekuensi Kunjungan
              <span id="visitor-result-count" style="font-size: 0.74rem; background: rgba(56, 189, 248, 0.15); color: #38bdf8; padding: 2px 8px; border-radius: 9999px;">0 Orang</span>
            </h3>
            <span style="font-size: 0.76rem; color: #64748b;">Diurutkan berdasarkan yang paling sering berkunjung</span>
          </div>

          <div class="visitor-cards-grid" id="analytics-visitors-grid">
            <!-- Injected via JavaScript -->
          </div>
        </div>

        <!-- Section: Linimasa Bukti Kejadian Terkini -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 0.85rem; padding: 1.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #fff; margin: 0;">
              <i class="fas fa-history text-info mr-1"></i> Linimasa Rekam Jejak CCTV Terkini (Evidence Log)
            </h3>
            <span style="font-size: 0.76rem; color: #64748b;">Tersimpan permanen dengan tanggal & jam detik</span>
          </div>
          <div id="analytics-timeline-feed" style="max-height: 380px; overflow-y: auto;">
            <!-- Injected via JavaScript -->
          </div>
        </div>

      </div>
    </div> <!-- /#view-analytics-pane -->

  </main>
  <?php endif; ?>

  <!-- ==================== MODAL REKAM JEJAK & INVESTIGASI DOSSIER ==================== -->
  <div class="modal-backdrop" id="modalVisitorDossier">
    <div class="modal-dialog" style="max-width: 720px;">
      <div class="modal-header">
        <div class="modal-title">
          <i class="fas fa-fingerprint text-info"></i> Rekam Jejak Investigasi Pelintas
        </div>
        <button type="button" class="modal-close" onclick="closeModal('modalVisitorDossier')">&times;</button>
      </div>

      <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
        <!-- Profil Header -->
        <div style="display: flex; gap: 1.25rem; align-items: center; background: rgba(15, 23, 42, 0.6); padding: 1.25rem; border-radius: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.08); margin-bottom: 1.25rem;">
          <img id="dossier-photo" src="" alt="Foto Snapshot" style="width: 80px; height: 80px; border-radius: 0.75rem; object-fit: cover; border: 2px solid rgba(56, 189, 248, 0.5); background: #0f172a;">
          <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
              <h3 id="dossier-name" style="font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0;">-</h3>
              <span id="dossier-badge" class="visitor-badge badge-stranger">STRANGER</span>
            </div>
            <div id="dossier-id" style="font-size: 0.76rem; color: #38bdf8; font-family: monospace; margin: 3px 0;">ID: -</div>
            <div id="dossier-notes" style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">-</div>
          </div>
        </div>

        <!-- Ringkasan Statistik Kunjungan -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
          <div style="background: var(--bg-surface); padding: 0.85rem; border-radius: 0.65rem; border: 1px solid var(--border-color); text-align: center;">
            <div style="font-size: 0.74rem; color: #94a3b8;">Total Kunjungan</div>
            <div id="dossier-total-visits" style="font-size: 1.3rem; font-weight: 800; color: #38bdf8;">0x</div>
          </div>
          <div style="background: var(--bg-surface); padding: 0.85rem; border-radius: 0.65rem; border: 1px solid var(--border-color); text-align: center;">
            <div style="font-size: 0.74rem; color: #94a3b8;">Pertama Terlihat</div>
            <div id="dossier-first-seen" style="font-size: 0.8rem; font-weight: 700; color: #fff;">-</div>
          </div>
          <div style="background: var(--bg-surface); padding: 0.85rem; border-radius: 0.65rem; border: 1px solid var(--border-color); text-align: center;">
            <div style="font-size: 0.74rem; color: #94a3b8;">Terakhir Terlihat</div>
            <div id="dossier-last-seen" style="font-size: 0.8rem; font-weight: 700; color: #10b981;">-</div>
          </div>
        </div>

        <!-- Rincian Kalender Kunjungan Harian -->
        <div style="margin-bottom: 1.25rem;">
          <h4 style="font-size: 0.9rem; font-weight: 700; color: #fff; margin: 0 0 0.65rem 0;">
            <i class="fas fa-calendar-alt text-info mr-1"></i> Rincian Kunjungan Per Tanggal (Daily Breakdown)
          </h4>
          <div id="dossier-daily-breakdown" style="border: 1px solid var(--border-color); border-radius: 0.65rem; overflow: hidden;">
            <!-- Injected via JavaScript -->
          </div>
        </div>

        <!-- Linimasa Bukti Snapshot & Detik Kejadian -->
        <div>
          <h4 style="font-size: 0.9rem; font-weight: 700; color: #fff; margin: 0 0 0.65rem 0;">
            <i class="fas fa-camera text-info mr-1"></i> Linimasa Bukti Snapshot & Kamera (Investigation Evidence)
          </h4>
          <div id="dossier-timeline-list" style="border: 1px solid var(--border-color); border-radius: 0.65rem; padding: 0.5rem 1rem; max-height: 280px; overflow-y: auto;">
            <!-- Injected via JavaScript -->
          </div>
        </div>
      </div>

      <div class="modal-footer" style="display: flex; justify-content: space-between;">
        <button type="button" class="btn btn-outline btn-sm" id="btn-dossier-rename" onclick="openRenameFromDossier()">
          <i class="fas fa-edit mr-1"></i> Beri Nama / Ubah Status
        </button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalVisitorDossier')">Tutup</button>
      </div>
    </div>
  </div>

  <!-- ==================== MODAL BERI NAMA / UBAH STATUS STRANGER ==================== -->
  <div class="modal-backdrop" id="modalRenameVisitor">
    <div class="modal-dialog">
      <div class="modal-header">
        <div class="modal-title">
          <i class="fas fa-user-tag text-info"></i> Beri Nama / Tandai Status Orang
        </div>
        <button type="button" class="modal-close" onclick="closeModal('modalRenameVisitor')">&times;</button>
      </div>

      <form id="form-rename-visitor" onsubmit="handleSaveVisitorProfile(event)">
        <div class="modal-body">
          <input type="hidden" id="rename-visitor-id" value="">

          <div class="form-group">
            <label><i class="fas fa-id-card text-info"></i> ID Pelintas:</label>
            <input type="text" id="rename-visitor-code" class="form-control" readonly style="opacity: 0.7; font-family: monospace;">
          </div>

          <div class="form-group">
            <label><i class="fas fa-user text-info"></i> Nama Orang / Keterangan:</label>
            <input type="text" id="rename-visitor-name" class="form-control" placeholder="Contoh: Budi - Kurir J&T / Tamu Lantai 1" required>
          </div>

          <div class="form-group">
            <label><i class="fas fa-tag text-info"></i> Kategori / Status Keamanan:</label>
            <select id="rename-visitor-category" class="form-control">
              <option value="guest">Tamu Resmi (Guest)</option>
              <option value="employee">Karyawan / Staff (Employee)</option>
              <option value="vip">VIP / Tamu Khusus (VIP)</option>
              <option value="stranger">Stranger (Orang Tak Dikenal)</option>
              <option value="blacklist">Blacklist / Waspada (Keamanan)</option>
            </select>
          </div>

          <div class="form-group">
            <label><i class="fas fa-sticky-note text-info"></i> Catatan Investigasi:</label>
            <textarea id="rename-visitor-notes" class="form-control" rows="3" placeholder="Contoh: Sering datang di area parkir jam 14:00. Diberi izin oleh Security."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalRenameVisitor')">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save mr-1"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>

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

  <!-- Modal Beri Nama / Tandai Orang Langsung dari Layar CCTV (Quick Tag) -->
  <div class="modal-backdrop" id="modalQuickTagPerson">
    <div class="modal-dialog" style="max-width: 440px;">
      <div class="modal-header">
        <div class="modal-title">
          <i class="fas fa-user-tag text-info mr-2"></i> Beri Nama & Tentukan Peran
        </div>
        <button type="button" class="modal-close" onclick="closeModal('modalQuickTagPerson')">&times;</button>
      </div>

      <form id="formQuickTagPerson" onsubmit="event.preventDefault(); submitQuickTagPerson();">
        <div class="modal-body">
          <!-- Preview Box with Cropped Person -->
          <div style="display: flex; gap: 0.85rem; align-items: center; margin-bottom: 1rem; background: rgba(15, 23, 42, 0.9); border: 1.5px solid rgba(56, 189, 248, 0.4); border-radius: 12px; padding: 0.75rem;">
            <div style="width: 72px; height: 72px; border-radius: 10px; overflow: hidden; border: 2px solid #00f0ff; background: #000; flex-shrink: 0;">
              <img id="quicktag-preview-img" src="" alt="Snapshot" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="flex: 1; min-width: 0;">
              <div style="color: #fff; font-weight: 700; font-size: 0.92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="quicktag-tracking-title">Orang Terdeteksi</div>
              <div id="quicktag-clothing-info" style="margin-top: 0.25rem; font-size: 0.76rem; color: #38bdf8;">👕 Pakaian: Abu-abu Gelap</div>
              <div id="quicktag-location-info" style="margin-top: 0.15rem; font-size: 0.72rem; color: #94a3b8;">📍 Lokasi: Kamera CCTV Live</div>
            </div>
          </div>

          <!-- Pilihan Cepat dari Database Wajah Terdaftar -->
          <div id="quicktag-known-faces-wrap" style="display: none; margin-bottom: 0.85rem; background: rgba(30, 41, 59, 0.5); border: 1px dashed rgba(56, 189, 248, 0.35); border-radius: 8px; padding: 0.5rem 0.65rem;">
            <div style="font-weight: 700; font-size: 0.76rem; color: #38bdf8; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
              <span><i class="fas fa-address-book text-info mr-1"></i> Pilih Cepat dari Wajah Terdaftar:</span>
              <small style="color: #94a3b8; font-weight: normal;">(1-klik auto-fill)</small>
            </div>
            <div id="quicktag-known-chips" style="display: flex; gap: 0.4rem; flex-wrap: wrap;"></div>
          </div>

          <!-- Input Nama -->
          <div class="form-group" style="margin-bottom: 0.85rem;">
            <label style="font-weight: 700; font-size: 0.85rem; color: #e2e8f0; display: block; margin-bottom: 0.35rem;">
              <i class="fas fa-user text-info mr-1"></i> Nama Lengkap / Panggilan:
            </label>
            <input type="text" id="quicktag-name-input" class="form-control" placeholder="Contoh: Budi Santoso / Siti / Pak Agus" required autofocus onkeydown="if(event.key==='Enter'){event.preventDefault();submitQuickTagPerson();}">
          </div>

          <!-- Pilihan Status / Peran -->
          <div class="form-group" style="margin-bottom: 0.85rem;">
            <label style="font-weight: 700; font-size: 0.85rem; color: #e2e8f0; display: block; margin-bottom: 0.35rem;">
              <i class="fas fa-id-badge text-info mr-1"></i> Status / Peran (Warna Kotak):
            </label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
              <label style="display: flex; align-items: center; gap: 0.5rem; background: rgba(59, 130, 246, 0.15); border: 1.5px solid #3b82f6; border-radius: 8px; padding: 0.55rem 0.7rem; cursor: pointer;">
                <input type="radio" name="quicktag_role" value="employee" checked style="accent-color: #3b82f6;">
                <div>
                  <strong style="color: #60a5fa; font-size: 0.84rem; display: block;">👔 Karyawan</strong>
                  <small style="color: #94a3b8; font-size: 0.68rem;">Staff / Karyawan (Royal Blue)</small>
                </div>
              </label>
              <label style="display: flex; align-items: center; gap: 0.5rem; background: rgba(0, 240, 255, 0.1); border: 1.5px solid #00f0ff; border-radius: 8px; padding: 0.55rem 0.7rem; cursor: pointer;">
                <input type="radio" name="quicktag_role" value="guest" style="accent-color: #00f0ff;">
                <div>
                  <strong style="color: #38bdf8; font-size: 0.84rem; display: block;">🚶 Pengunjung</strong>
                  <small style="color: #94a3b8; font-size: 0.68rem;">Konsumen (Cyan)</small>
                </div>
              </label>
              <label style="display: flex; align-items: center; gap: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1.5px solid #f59e0b; border-radius: 8px; padding: 0.55rem 0.7rem; cursor: pointer;">
                <input type="radio" name="quicktag_role" value="vip" style="accent-color: #f59e0b;">
                <div>
                  <strong style="color: #fbbf24; font-size: 0.84rem; display: block;">🌟 VIP</strong>
                  <small style="color: #94a3b8; font-size: 0.68rem;">Direksi/Owner (Gold)</small>
                </div>
              </label>
              <label style="display: flex; align-items: center; gap: 0.5rem; background: rgba(239, 68, 68, 0.1); border: 1.5px solid #ef4444; border-radius: 8px; padding: 0.55rem 0.7rem; cursor: pointer;">
                <input type="radio" name="quicktag_role" value="blacklist" style="accent-color: #ef4444;">
                <div>
                  <strong style="color: #f87171; font-size: 0.84rem; display: block;">🚫 Blacklist</strong>
                  <small style="color: #94a3b8; font-size: 0.68rem;">Waspada (Merah)</small>
                </div>
              </label>
            </div>
          </div>

          <!-- Jabatan / Divisi Opsional -->
          <div class="form-group" style="margin-bottom: 0.85rem;">
            <label style="font-weight: 700; font-size: 0.82rem; color: #cbd5e1; display: block; margin-bottom: 0.35rem;">
              <i class="fas fa-briefcase text-info mr-1"></i> Jabatan / Catatan (Opsional):
            </label>
            <input type="text" id="quicktag-role-title-input" class="form-control" placeholder="Contoh: Sales Counter / Pembeli Meja 1" value="Staff" onkeydown="if(event.key==='Enter'){event.preventDefault();submitQuickTagPerson();}">
          </div>

          <!-- Opsi Simpan ke Database Wajah AI -->
          <div style="background: rgba(30, 41, 59, 0.6); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 8px; padding: 0.65rem 0.85rem; margin-top: 0.5rem;">
            <label style="display: flex; align-items: flex-start; gap: 0.6rem; cursor: pointer; margin: 0;">
              <input type="checkbox" id="quicktag-save-db" checked style="margin-top: 3px; accent-color: #10b981; width: 16px; height: 16px;">
              <span style="font-size: 0.78rem; color: #cbd5e1; line-height: 1.35;">
                <strong style="color: #38bdf8;">Simpan Wajah ke Database Wajah AI</strong><br>
                <span style="color: #94a3b8; font-size: 0.72rem;">Sistem akan mengekstrak biometrik wajah orang ini dan otomatis mengingatnya di masa mendatang pada semua kamera CCTV.</span>
              </span>
            </label>
          </div>
        </div>

        <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
          <div style="display: flex; gap: 0.4rem; align-items: center;">
            <button type="button" class="btn btn-danger-outline btn-sm" id="btn-delete-quicktag" onclick="removeQuickTagPerson()" style="display: none;">
              <i class="fas fa-user-minus mr-1"></i> Lepas Label
            </button>
            <button type="button" class="btn btn-outline btn-sm" onclick="resetAllCameraTags()" style="color: #f87171; border-color: rgba(248,113,113,0.35); font-size: 0.75rem;" title="Hapus semua tag nama tersimpan di kamera ini">
              <i class="fas fa-trash-alt mr-1"></i> Reset Semua Tag
            </button>
          </div>
          <div style="display: flex; gap: 0.5rem; margin-left: auto;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalQuickTagPerson')">Batal</button>
            <button type="button" class="btn btn-primary btn-sm" id="btn-save-quicktag" onclick="submitQuickTagPerson()" style="background: linear-gradient(135deg, #059669, #10b981); border-color: #10b981; font-weight: 700;">
              <i class="fas fa-check-circle mr-1"></i> Simpan & Terapkan Identitas
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Pengaturan Garis Penghitung (Tripwire People Counter) -->
  <div class="modal-backdrop" id="modalCountingLineSettings">
    <div class="modal-dialog" style="max-width: 480px;">
      <div class="modal-header">
        <div class="modal-title">
          <i class="fas fa-ruler-combined text-info mr-2"></i> Pengaturan Garis Penghitung (Tripwire Counter)
        </div>
        <button type="button" class="modal-close" onclick="closeModal('modalCountingLineSettings')">&times;</button>
      </div>

      <div class="modal-body">
        <!-- Status Aktif / Nonaktif -->
        <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(15, 23, 42, 0.9); border: 1.5px solid rgba(56, 189, 248, 0.4); border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1rem;">
          <div>
            <div style="font-weight: 700; font-size: 0.9rem; color: #fff;">Status Garis Penghitung</div>
            <div style="font-size: 0.75rem; color: #94a3b8;">Tampilkan garis dan hitung orang yang melintas</div>
          </div>
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="checkbox" id="tripwire-enable-toggle" onchange="toggleCountingLineState(this.checked)" checked style="width: 22px; height: 22px; accent-color: #00f0ff; cursor: pointer;">
          </div>
        </div>

        <!-- Pilihan Fungsi Garis (Keluar / Masuk / Alat Hitung Saja) -->
        <div class="form-group" style="margin-bottom: 1rem;">
          <label style="font-weight: 700; font-size: 0.85rem; color: #e2e8f0; display: block; margin-bottom: 0.4rem;">
            <i class="fas fa-exchange-alt text-info mr-1"></i> Fungsi Garis Penghitung:
          </label>
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
            <!-- Masuk Saja -->
            <label id="lbl-tripwire-mode-in" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 0.25rem; background: rgba(16, 185, 129, 0.12); border: 1.5px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 0.6rem 0.3rem; cursor: pointer; transition: all 0.2s;">
              <input type="radio" name="tripwire_mode" value="in" onchange="setCountingLineMode('in')" style="accent-color: #10b981;">
              <span style="font-size: 0.8rem; font-weight: 700; color: #34d399;">🟢 Masuk</span>
              <span style="font-size: 0.68rem; color: #94a3b8;">Hitung Masuk Saja</span>
            </label>

            <!-- Keluar Saja -->
            <label id="lbl-tripwire-mode-out" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 0.25rem; background: rgba(239, 68, 68, 0.12); border: 1.5px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 0.6rem 0.3rem; cursor: pointer; transition: all 0.2s;">
              <input type="radio" name="tripwire_mode" value="out" onchange="setCountingLineMode('out')" style="accent-color: #ef4444;">
              <span style="font-size: 0.8rem; font-weight: 700; color: #f87171;">🔴 Keluar</span>
              <span style="font-size: 0.68rem; color: #94a3b8;">Hitung Keluar Saja</span>
            </label>

            <!-- Dua Arah / Alat Hitung Saja -->
            <label id="lbl-tripwire-mode-both" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 0.25rem; background: rgba(0, 240, 255, 0.12); border: 1.5px solid #00f0ff; border-radius: 8px; padding: 0.6rem 0.3rem; cursor: pointer; transition: all 0.2s;">
              <input type="radio" name="tripwire_mode" value="both" checked onchange="setCountingLineMode('both')" style="accent-color: #00f0ff;">
              <span style="font-size: 0.8rem; font-weight: 700; color: #38bdf8;">🔄 Dua Arah</span>
              <span style="font-size: 0.68rem; color: #94a3b8;">Alat Hitung Total</span>
            </label>
          </div>
        </div>

        <!-- Pilihan Warna Garis -->
        <div class="form-group" style="margin-bottom: 1rem;">
          <label style="font-weight: 700; font-size: 0.85rem; color: #e2e8f0; display: block; margin-bottom: 0.4rem;">
            <i class="fas fa-palette text-info mr-1"></i> Ubah Warna Garis:
          </label>
          <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; background: rgba(15, 23, 42, 0.6); padding: 0.6rem 0.8rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <!-- Presets -->
            <button type="button" onclick="setCountingLineColor('#00f0ff')" title="Cyan Neon" style="width: 28px; height: 28px; border-radius: 50%; background: #00f0ff; border: 2px solid #fff; cursor: pointer; box-shadow: 0 0 8px rgba(0, 240, 255, 0.6);"></button>
            <button type="button" onclick="setCountingLineColor('#10b981')" title="Emerald Green" style="width: 28px; height: 28px; border-radius: 50%; background: #10b981; border: 2px solid transparent; cursor: pointer; box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);"></button>
            <button type="button" onclick="setCountingLineColor('#f59e0b')" title="Amber Gold" style="width: 28px; height: 28px; border-radius: 50%; background: #f59e0b; border: 2px solid transparent; cursor: pointer; box-shadow: 0 0 8px rgba(245, 158, 11, 0.6);"></button>
            <button type="button" onclick="setCountingLineColor('#ef4444')" title="Crimson Red" style="width: 28px; height: 28px; border-radius: 50%; background: #ef4444; border: 2px solid transparent; cursor: pointer; box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);"></button>
            <button type="button" onclick="setCountingLineColor('#a855f7')" title="Neon Purple" style="width: 28px; height: 28px; border-radius: 50%; background: #a855f7; border: 2px solid transparent; cursor: pointer; box-shadow: 0 0 8px rgba(168, 85, 247, 0.6);"></button>
            <button type="button" onclick="setCountingLineColor('#ffffff')" title="Putih Cerah" style="width: 28px; height: 28px; border-radius: 50%; background: #ffffff; border: 2px solid transparent; cursor: pointer; box-shadow: 0 0 8px rgba(255, 255, 255, 0.6);"></button>
            
            <!-- Custom Color Picker -->
            <div style="display: flex; align-items: center; gap: 0.4rem; margin-left: auto;">
              <span style="font-size: 0.74rem; color: #94a3b8;">Warna Lain:</span>
              <input type="color" id="tripwire-color-picker" value="#00f0ff" onchange="setCountingLineColor(this.value)" style="width: 34px; height: 30px; border-radius: 6px; border: 1px solid #475569; background: none; cursor: pointer; padding: 0;">
            </div>
          </div>
        </div>

        <!-- Posisi Preset Cepat & Petunjuk Geser -->
        <div class="form-group" style="margin-bottom: 1rem;">
          <label style="font-weight: 700; font-size: 0.85rem; color: #e2e8f0; display: block; margin-bottom: 0.4rem;">
            <i class="fas fa-arrows-alt text-info mr-1"></i> Posisi Garis di Layar CCTV:
          </label>
          <div style="display: flex; gap: 0.4rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
            <button type="button" class="btn btn-outline-info btn-sm" onclick="setCountingLinePresetPos('lantai')" style="flex: 1; min-width: 135px; font-size: 0.74rem; font-weight: 700; border-color: #00f0ff; color: #00f0ff; background: rgba(0, 240, 255, 0.12);">
              📐 Ikuti Dasar Lantai CCTV
            </button>
            <button type="button" class="btn btn-outline-info btn-sm" onclick="setCountingLinePresetPos('bottom')" style="flex: 1; min-width: 100px; font-size: 0.74rem;">
              Horizontal Bawah
            </button>
            <button type="button" class="btn btn-outline-info btn-sm" onclick="setCountingLinePresetPos('middle')" style="flex: 1; min-width: 100px; font-size: 0.74rem;">
              Horizontal Tengah
            </button>
          </div>
          <div style="background: rgba(14, 165, 233, 0.08); border: 1px dashed rgba(56, 189, 248, 0.4); border-radius: 8px; padding: 0.6rem 0.75rem; font-size: 0.74rem; color: #94a3b8; line-height: 1.45;">
            <strong style="color: #38bdf8;"><i class="fas fa-hand-pointer mr-1"></i> Geser Langsung di Layar:</strong><br>
            Klik dan geser <strong>titik bulat ujung garis (A atau B)</strong> langsung pada video CCTV untuk memposisikan garis sesuai pintu masuk, koridor, atau pembatas showroom Anda.
          </div>
        </div>

        <!-- Tombol Reset Hitungan -->
        <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 8px; padding: 0.6rem 0.8rem;">
          <div>
            <div style="font-size: 0.82rem; font-weight: 700; color: #fff;">Reset Hitungan Hari Ini</div>
            <div style="font-size: 0.7rem; color: #94a3b8;">Kembalikan angka masuk & keluar ke 0</div>
          </div>
          <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetCountingLineStats()" style="font-size: 0.75rem; font-weight: 700;">
            <i class="fas fa-undo mr-1"></i> Reset ke 0
          </button>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalCountingLineSettings')">Tutup</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="saveCountingLineConfig(); closeModal('modalCountingLineSettings');" style="background: linear-gradient(135deg, #0284c7, #00f0ff); border-color: #00f0ff; color: #000; font-weight: 800;">
          <i class="fas fa-check-circle mr-1"></i> Simpan & Terapkan
        </button>
      </div>
    </div>
  </div>

  <!-- Modal Daftarkan Wajah Baru (Face Enrollment) -->
  <div class="modal-backdrop" id="modalRegisterFace">
    <div class="modal-dialog" style="max-width: 500px;">
      <div class="modal-header">
        <div class="modal-title">
          <i class="fas fa-user-plus text-info mr-2"></i> Daftarkan Wajah Baru ke AI Face Recognition
        </div>
        <button type="button" class="modal-close" onclick="closeEnrollModal()">&times;</button>
      </div>

      <form id="formRegisterFace" onsubmit="event.preventDefault(); submitRegisterFace(event);">
        <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
          
          <!-- Live Biometric Face Enrollment Scanner -->
          <div style="background: rgba(15, 23, 42, 0.9); border: 1.5px solid rgba(0, 240, 255, 0.4); border-radius: 14px; padding: 1rem; text-align: center; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
              <span class="badge" style="background: rgba(0, 240, 255, 0.15); color: #38bdf8; border: 1px solid rgba(0, 240, 255, 0.4); font-size: 0.72rem; padding: 0.2rem 0.5rem; border-radius: 6px;">
                <span class="status-indicator online"></span> LIVE BIOMETRIC SCANNER
              </span>
              <span style="color: #94a3b8; font-size: 0.72rem;">Scan Wajah via Kamera / Foto</span>
            </div>

            <!-- Viewfinder -->
            <div id="face-scanner-viewfinder" style="position: relative; width: 100%; max-width: 320px; height: 200px; margin: 0 auto; border-radius: 10px; overflow: hidden; background: #000; border: 2px solid #00f0ff; box-shadow: 0 0 15px rgba(0, 240, 255, 0.3);">
              <video id="face-enroll-video" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
              <canvas id="face-enroll-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10;"></canvas>
              
              <!-- Oval Target Guide -->
              <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 120px; height: 155px; border: 2px dashed #00f0ff; border-radius: 50%; box-shadow: 0 0 15px rgba(0, 240, 255, 0.3); pointer-events: none;"></div>

              <div id="face-enroll-status-badge" style="position: absolute; bottom: 6px; left: 50%; transform: translateX(-50%); background: rgba(10, 15, 30, 0.9); border: 1px solid rgba(0, 240, 255, 0.5); padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; color: #38bdf8; white-space: nowrap;">
                <i class="fas fa-expand mr-1"></i> Arahkan Wajah ke Dalam Oval
              </div>
            </div>

            <!-- Scanned Snapshot Preview -->
            <div id="face-scanned-preview-box" style="display: none; text-align: center; padding: 0.75rem 0;">
              <div style="width: 90px; height: 90px; border-radius: 50%; margin: 0 auto; overflow: hidden; border: 3px solid #10b981; box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);">
                <img id="face-preview-img" src="" alt="Hasil Scan" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
              <div style="margin-top: 0.4rem; font-weight: 700; color: #34d399; font-size: 0.82rem;">
                <i class="fas fa-check-circle mr-1"></i> Wajah Berhasil Di-scan & Tervalidasi AI!
              </div>
            </div>

            <!-- Buttons -->
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem; margin-top: 0.75rem;">
              <button type="button" id="btn-capture-face" class="btn btn-primary btn-sm" onclick="captureFaceFromEnrollCamera()" style="background: linear-gradient(135deg, #0284c7, #00f0ff); color: #000; font-weight: 700;">
                <i class="fas fa-camera mr-1"></i> Ambil Foto Webcam
              </button>
              <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('face-file-upload').click()">
                <i class="fas fa-image mr-1 text-info"></i> Upload Foto
              </button>
              <input type="file" id="face-file-upload" accept="image/*" style="display: none;" onchange="handleFaceFileUpload(this)">
              <button type="button" id="btn-rescan-face" class="btn btn-outline-warning btn-sm" onclick="startEnrollWebcam()" style="display: none;">
                <i class="fas fa-sync-alt mr-1"></i> Scan Ulang
              </button>
            </div>

            <input type="hidden" id="face-input-photo" value="">
            <input type="hidden" id="face-input-descriptor" value="">
          </div>

          <!-- Form Fields -->
          <div class="form-group" style="margin-bottom: 0.85rem;">
            <label><i class="fas fa-user text-info"></i> Nama Lengkap:</label>
            <input type="text" id="face-input-name" class="form-control" placeholder="Contoh: WAHYU / BUDI" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label><i class="fas fa-shield-alt text-info"></i> Kategori Akses:</label>
              <select id="face-input-category" class="form-control">
                <option value="vip">🌟 VIP / Direksi / Pemilik</option>
                <option value="employee" selected>👔 Karyawan / Staff Resmi</option>
                <option value="resident">🏠 Penghuni / Tamu Resmi</option>
                <option value="blacklist">🚫 Blacklist (Peringatan Bahaya)</option>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fas fa-briefcase text-info"></i> Jabatan / Divisi:</label>
              <input type="text" id="face-input-role" class="form-control" placeholder="Contoh: Staff IT / Supervisor" value="Staff">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" onclick="closeEnrollModal()">Batal</button>
          <button type="submit" id="btn-submit-face" class="btn btn-primary btn-sm">
            <i class="fas fa-check-circle mr-1"></i> Simpan Wajah ke Database
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- App Footer -->
  <footer class="app-footer">
    Loewix Face Detection &bull; PT. Loewix Indonesia &bull; <?= (strpos($httpHost, 'loewixcctv.com') !== false) ? 'Online & Cloud Edition' : 'Versi Desktop & Jaringan Lokal (Port 8088)' ?>
  </footer>

  <!-- Application Logic JavaScript -->
  <script>
    const ASSETS_BASE = '<?= $assetsBase ?>';

    // Global Safe HTML escaping helper
    function escapeHtml(str) {
      if (str === null || str === undefined) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

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
      if (id === 'modalQuickTagPerson') {
        selectedEntityForTagging = null;
        selectedEntityForTaggingCoords = null;
      }
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
      
      const ch = cam.channel || '1';
      document.getElementById('cust-cam-channel').value = ch;
      if (document.getElementById('cust-cam-rtsp-ch')) {
        document.getElementById('cust-cam-rtsp-ch').value = ch;
      }

      document.getElementById('cust-cam-status').value = cam.status || 'online';

      if (cam.rtsp_url) {
        autoParseRtspInput();
      }

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
      const channelVal = (connType === 'rtsp' && document.getElementById('cust-cam-rtsp-ch'))
        ? rtspCh
        : document.getElementById('cust-cam-channel').value.trim();

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
      fd.append('channel', channelVal);
      fd.append('status', document.getElementById('cust-cam-status').value);

      try {
        const res = await fetch('api.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          closeModal('modalCameraForm');
          await loadCameras();
          alert(data.message || 'Kamera CCTV berhasil disimpan!');
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
      const cam = localCameras.find(c => c.id == camId);
      const camName = cam ? cam.title : `ID ${camId}`;
      if (!confirm(`Apakah Anda yakin ingin menghapus kamera "${camName}" dari sistem?`)) return;
      const fd = new FormData();
      fd.append('action', 'delete_camera');
      fd.append('id', camId);

      try {
        const res = await fetch('api.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          await loadCameras();
          alert(data.message || 'Kamera berhasil dihapus.');
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
        const res = await fetch('api.php?action=get_cameras&_t=' + Date.now());
        const data = await res.json();
        if (data.success) {
          localCameras = data.cameras || [];
          renderCameraGrid();
          if (typeof updateAICameraLists === 'function') {
            updateAICameraLists();
          }
        }
      } catch (e) {
        console.error('loadCameras error:', e);
      }
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
              <img src="${ASSETS_BASE}/image/icon.png" alt="${cam.title}" style="opacity: 0.25; filter: grayscale(1);">
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
                <button class="btn btn-outline btn-sm" onclick="startAICameraScan(${cam.id})" title="Pindai & Deteksi Wajah AI di Kamera Ini" style="background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.45); color: #38bdf8; font-weight: 600;">
                  <i class="fas fa-brain mr-1"></i> Scan AI
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

    // =========================================================================
    // MAIN APP NAVIGATION TABS (MANAJEMEN CCTV, AI FACE & ANALYTICS)
    // =========================================================================
    function switchMainTab(tab) {
      const cctvPane = document.getElementById('view-cctv-pane');
      const aiPane = document.getElementById('view-ai-pane');
      const analyticsPane = document.getElementById('view-analytics-pane');
      const cctvBtn = document.getElementById('tab-btn-cctv');
      const aiBtn = document.getElementById('tab-btn-ai');
      const analyticsBtn = document.getElementById('tab-btn-analytics');
      const aiPlayer = document.getElementById('ai-video-player');

      if (tab === 'ai') {
        if (cctvPane) cctvPane.style.display = 'none';
        if (analyticsPane) analyticsPane.style.display = 'none';
        if (aiPane) aiPane.style.display = 'block';
        if (cctvBtn) cctvBtn.classList.remove('active');
        if (analyticsBtn) analyticsBtn.classList.remove('active');
        if (aiBtn) aiBtn.classList.add('active');
        initAIFaceSuite();
        if (typeof updateAICameraLists === 'function') {
          updateAICameraLists();
        }
      } else if (tab === 'analytics') {
        if (cctvPane) cctvPane.style.display = 'none';
        if (aiPane) aiPane.style.display = 'none';
        if (analyticsPane) analyticsPane.style.display = 'block';
        if (cctvBtn) cctvBtn.classList.remove('active');
        if (aiBtn) aiBtn.classList.remove('active');
        if (analyticsBtn) analyticsBtn.classList.add('active');
        if (aiPlayer && !aiPlayer.paused) {
          aiPlayer.pause();
        }
        if (typeof loadVisitorAnalytics === 'function') {
          loadVisitorAnalytics();
        }
      } else {
        if (aiPane) aiPane.style.display = 'none';
        if (analyticsPane) analyticsPane.style.display = 'none';
        if (cctvPane) cctvPane.style.display = 'block';
        if (aiBtn) aiBtn.classList.remove('active');
        if (analyticsBtn) analyticsBtn.classList.remove('active');
        if (cctvBtn) cctvBtn.classList.add('active');
        // Pause AI stream if user switches back to CCTV
        if (aiPlayer && !aiPlayer.paused) {
          aiPlayer.pause();
        }
      }
    }


    // Direct AI scanner trigger from CCTV Management card
    function startAICameraScan(camId) {
      const cam = (localCameras || []).find(c => String(c.id) === String(camId));
      currentAICamera = cam || { id: camId, title: 'Kamera ' + camId };
      switchMainTab('ai');
      setTimeout(() => {
        changeAICamera(camId);
        const box = document.getElementById('ai-screen-box');
        if (box) box.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }, 300);
    }

    // =========================================================================
    // LOEWIX NEURAL VISION SUITE - AI FACE RECOGNITION ENGINE (PRD 17 SEP)
    // =========================================================================
    let cachedAIFaces = [];
    let cachedAILogs = [];
    let currentAICamera = { id: 'webcam', title: 'Live Webcam Laptop' };
    let aiWebcamStream = null;
    let aiEnrollStream = null;
    let aiHlsInstance = null;
    let aiLiveVideo = null;
    let aiCanvasOverlay = null;
    let isAutoScanActive = true;
    let isAISoundEnabled = true;
    let faceAPIReady = false;
    let faceAPILoading = false;
    let faceAPIFaceMatcher = null;
    let allRegisteredDescriptors = [];
    let isFaceAPIDetecting = false;
    let aiDetectionLoopId = null;
    let lastLoggedPersonTime = {};

    const FACE_API_MODEL_URLS = [
      `${ASSETS_BASE}/models`,
      '../assets/models',
      'assets/models',
      'https://loewixcctv.com/assets/models',
      'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights',
      'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'
    ];

    function resolveFacePhotoUrl(photo, id) {
      if (!photo) return `${ASSETS_BASE}/image/icon.png`;
      if (photo.startsWith('data:') || photo.startsWith('blob:')) return photo;
      if (id) return `api.php?action=get_face_image&id=${id}`;
      return `${ASSETS_BASE}/` + photo.replace(/^(\.\.\/)+/, '').replace(/^\//, '');
    }

    async function initFaceAPI() {
      if (faceAPIReady || faceAPILoading) return;
      if (typeof faceapi === 'undefined') {
        setTimeout(initFaceAPI, 1000);
        return;
      }
      faceAPILoading = true;
      const indicator = document.getElementById('ai-engine-indicator');
      if (indicator) indicator.textContent = 'Memuat Model AI Biometrik...';

      for (const modelUrl of FACE_API_MODEL_URLS) {
        try {
          if (!faceapi.nets.tinyFaceDetector.isLoaded) {
            await faceapi.nets.tinyFaceDetector.loadFromUri(modelUrl);
          }
          if (!faceapi.nets.faceLandmark68TinyNet.isLoaded) {
            await faceapi.nets.faceLandmark68TinyNet.loadFromUri(modelUrl);
          }
          if (faceapi.nets.tinyFaceDetector.isLoaded) {
            faceAPIReady = true;
            faceAPILoading = false;
            console.log('[FaceAPI] ✅ Fast Detector ready from', modelUrl);
            if (indicator) indicator.textContent = 'Model AI: Online (Face-API.js + MediaPipe)';

            // Load heavy descriptors model in background
            if (!faceapi.nets.faceRecognitionNet.isLoaded) {
              faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl).then(() => {
                console.log('[FaceAPI] ✅ Face Recognition Net loaded');
                buildFaceDescriptors(true);
              }).catch(e => console.warn('[FaceAPI] Recognition net warning:', e));
            } else {
              buildFaceDescriptors(true);
            }
            return;
          }
        } catch (e) {
          console.warn('[FaceAPI] Failed loading from ' + modelUrl + ':', e.message);
        }
      }
      faceAPILoading = false;
      faceAPIReady = true;
    }

    // TENSORFLOW COCO-SSD HUMAN / PEDESTRIAN SURVEILLANCE DETECTOR
    let cocoSSDModel = null;
    let isCOCOSSDLoading = false;
    let activeHumanEntities = [];
    let persistentTaggedPersonnel = [];
    let selectedEntityForTaggingCoords = null;
    let _humanEntityIdCounter = 0;
    let isHumanDetecting = false;
    let lastHumanDetectTime = 0;
    let aiScanLineY = 0;
    let aiScanDirection = 1;

    function loadPersistentTaggedForCamera(camId) {
      try {
        const raw = localStorage.getItem('loewix_tagged_' + (camId || 'default'));
        if (raw) {
          const parsed = JSON.parse(raw);
          if (Array.isArray(parsed)) {
            persistentTaggedPersonnel = parsed;
          }
        }
      } catch (e) {}
    }

    function savePersistentTaggedForCamera(camId) {
      try {
        const id = camId || (currentAICamera ? currentAICamera.id : 'default');
        localStorage.setItem('loewix_tagged_' + id, JSON.stringify(persistentTaggedPersonnel));
      } catch (e) {}
    }

    async function initCOCOSSD() {
      if (cocoSSDModel || isCOCOSSDLoading) return;
      if (typeof cocoSsd === 'undefined' || typeof tf === 'undefined') {
        setTimeout(initCOCOSSD, 1000);
        return;
      }
      isCOCOSSDLoading = true;
      try {
        // Ensure TF.js backend is ready before loading any model
        await tf.ready();
        console.log('⚡ [AI Vision] TF.js backend ready:', tf.getBackend(), '| Version:', tf.version.tfjs);

        console.log('⚡ [AI Vision] Loading COCO-SSD Human Surveillance Model (lite_mobilenet_v2)...');
        cocoSSDModel = await cocoSsd.load({ base: 'lite_mobilenet_v2' });
        console.log('✅ [AI Vision] COCO-SSD lite_mobilenet_v2 Loaded Successfully!');
        const engineTag = document.getElementById('ai-engine-indicator');
        if (engineTag) engineTag.innerHTML = '<span class="status-indicator online"></span> Neural Vision: COCO-SSD Human AI + Face-API.js';
      } catch (errCoco) {
        console.warn('⚠️ [AI Vision] lite_mobilenet_v2 failed:', errCoco.message, '| Trying mobilenet_v2...');
        try {
          cocoSSDModel = await cocoSsd.load({ base: 'mobilenet_v2' });
          console.log('✅ [AI Vision] COCO-SSD mobilenet_v2 Loaded (fallback 1)!');
        } catch (eFb1) {
          console.warn('⚠️ [AI Vision] mobilenet_v2 failed:', eFb1.message, '| Trying mobilenet_v1...');
          try {
            cocoSSDModel = await cocoSsd.load({ base: 'mobilenet_v1' });
            console.log('✅ [AI Vision] COCO-SSD mobilenet_v1 Loaded (fallback 2)!');
          } catch (eFb2) {
            console.warn('⚠️ [AI Vision] mobilenet_v1 failed:', eFb2.message, '| Trying default...');
            try {
              cocoSSDModel = await cocoSsd.load();
              console.log('✅ [AI Vision] COCO-SSD default model Loaded (fallback 3)!');
            } catch (eDefault) {
              console.error('❌ [AI Vision] ALL COCO-SSD model bases failed!', eDefault);
            }
          }
        }
      } finally {
        isCOCOSSDLoading = false;
      }
    }

    async function buildFaceDescriptors(force = false) {
      if (!cachedAIFaces || cachedAIFaces.length === 0) {
        faceAPIFaceMatcher = null;
        allRegisteredDescriptors = [];
        return;
      }
      if (!faceAPIReady || !faceapi.nets.faceRecognitionNet || !faceapi.nets.faceRecognitionNet.isLoaded) {
        return;
      }

      try {
        const labeled = [];
        for (const face of cachedAIFaces) {
          const rawPhoto = face.photo_b64 || face.photo;
          if (face.descriptor && Array.isArray(face.descriptor) && face.descriptor.length >= 128) {
            labeled.push(new faceapi.LabeledFaceDescriptors(face.name, [new Float32Array(face.descriptor)]));
          } else if (rawPhoto) {
            try {
              const photoUrl = resolveFacePhotoUrl(rawPhoto, face.id);
              const img = await new Promise(resolve => {
                const el = new Image();
                el.crossOrigin = 'anonymous';
                el.onload = () => resolve(el);
                el.onerror = () => resolve(null);
                el.src = photoUrl;
              });
              if (img) {
                const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks(true).withFaceDescriptor();
                if (detection && detection.descriptor) {
                  labeled.push(new faceapi.LabeledFaceDescriptors(face.name, [detection.descriptor]));
                }
              }
            } catch (errImg) {}
          }
        }

        if (labeled.length > 0) {
          allRegisteredDescriptors = labeled;
          faceAPIFaceMatcher = new faceapi.FaceMatcher(labeled, 0.58);
          console.log(`[FaceAPI] ✅ Biometric FaceMatcher built with ${labeled.length} registered face(s)`);
        }
      } catch (err) {
        console.warn('[FaceAPI] Build descriptors error:', err);
      }
    }

    async function loadAIFaceData(showToast = false) {
      try {
        const res = await fetch('api.php?action=get_ai_data');
        const data = await res.json();

        if (data) {
          if (data.faces) cachedAIFaces = data.faces || [];
          if (data.logs) cachedAILogs = data.logs || [];
          if (data.cameras && Array.isArray(data.cameras) && data.cameras.length > 0) {
            localCameras = data.cameras;
          }

          // Update Stats Metrics
          const facesEl = document.getElementById('ai-stat-faces');
          if (facesEl) facesEl.textContent = cachedAIFaces.length;

          const badgeEl = document.getElementById('ai-faces-count-badge');
          if (badgeEl) badgeEl.textContent = cachedAIFaces.length;

          const detEl = document.getElementById('ai-stat-detections');
          if (detEl) detEl.textContent = (data.stats && data.stats.total_detections_today) ? data.stats.total_detections_today : (cachedAILogs.length || 0);

          const blEl = document.getElementById('ai-stat-blacklist');
          if (blEl) blEl.textContent = (data.stats && data.stats.blacklist_alerts) ? data.stats.blacklist_alerts : 0;

          const camEl = document.getElementById('ai-stat-cameras');
          if (camEl) camEl.textContent = (localCameras && localCameras.length) ? localCameras.length : 1;

          // Render Enrolled Faces Directory
          renderAIFacesGrid(cachedAIFaces);

          // Render Activity Log Stream
          renderAIActivityLogs(cachedAILogs);

          // Update all Camera Selectors & Visual Channel Grids
          updateAICameraLists();

          // Build/refresh biometric face matcher
          buildFaceDescriptors();

          if (showToast) {
            alert('✅ Data AI Face Recognition & Channel CCTV berhasil disinkronkan!');
          }
        }
      } catch (e) {
        console.error('Failed to load AI face data:', e);
      }
    }

    function updateAICameraLists() {
      try {
        populateAICameraSelector();
      } catch (e) {
        console.error('populateAICameraSelector error:', e);
      }
      try {
        renderAICameraPills();
      } catch (e) {
        console.error('renderAICameraPills error:', e);
      }
      try {
        renderAICameraGridCards();
      } catch (e) {
        console.error('renderAICameraGridCards error:', e);
      }
    }

    function populateAICameraSelector() {
      const select = document.getElementById('ai-camera-selector');
      if (!select) return;
      const currentVal = (currentAICamera && currentAICamera.id) ? String(currentAICamera.id) : (select.value || 'webcam');

      let opts = `<option value="webcam" ${currentVal === 'webcam' ? 'selected' : ''}>📸 Live Webcam Laptop</option>`;
      if (Array.isArray(localCameras) && localCameras.length > 0) {
        localCameras.forEach((cam, idx) => {
          const isSelected = String(cam.id) === currentVal ? 'selected' : '';
          const cityUpper = (cam.city || 'Lokal').toUpperCase();
          const isOnline = cam.status !== 'offline';
          const statusIcon = isOnline ? '🟢' : '🔴';
          opts += `<option value="${cam.id}" ${isSelected}>${statusIcon} [CH ${idx + 1}] ${escapeHtml(cam.title || 'Kamera ' + cam.id)} (${cityUpper})</option>`;
        });
      }
      select.innerHTML = opts;
      select.value = currentVal;
    }

    function renderAICameraPills() {
      const bar = document.getElementById('ai-channel-quick-pills');
      if (!bar) return;

      const activeId = (currentAICamera && currentAICamera.id) ? String(currentAICamera.id) : 'webcam';
      
      let html = `
        <button type="button" class="ai-nvr-btn ${activeId === 'webcam' ? 'active' : ''}" onclick="startAIWebcamLive()" title="Live Webcam Laptop">
          <i class="fas fa-camera text-info" style="font-size: 0.7rem;"></i> Webcam
        </button>
      `;

      if (Array.isArray(localCameras) && localCameras.length > 0) {
        localCameras.forEach((cam, idx) => {
          const isActive = String(cam.id) === activeId;
          const isOnline = cam.status !== 'offline';
          const statusDot = isOnline ? '#10b981' : '#ef4444';
          html += `
            <button type="button" class="ai-nvr-btn ${isActive ? 'active' : ''}" onclick="changeAICamera(${cam.id})" title="${escapeHtml(cam.title || '')}">
              <span class="status-dot" style="background: ${statusDot};"></span>
              CH ${idx + 1}
            </button>
          `;
        });
      }

      bar.innerHTML = html;
    }

    function renderAICameraGridCards() {
      const grid = document.getElementById('ai-cctv-channels-grid');
      const badge = document.getElementById('ai-cctv-count-badge');
      if (!grid) return;

      if (badge) badge.textContent = `${(localCameras || []).length} Kamera Terhubung`;

      if (!localCameras || localCameras.length === 0) {
        grid.innerHTML = `
          <div style="grid-column: 1 / -1; text-align: center; padding: 2.5rem 1rem; color: #64748b;">
            <i class="fas fa-video-slash mb-2" style="font-size: 1.8rem; color: #475569;"></i>
            <div>Belum ada kamera CCTV lokal terdaftar.</div>
          </div>
        `;
        return;
      }

      const activeId = (currentAICamera && currentAICamera.id) ? String(currentAICamera.id) : 'webcam';

      grid.innerHTML = localCameras.map((cam, idx) => {
        const isActive = String(cam.id) === activeId;
        const isOnline = cam.status !== 'offline';
        const cityUpper = (cam.city || 'Lokal').toUpperCase();
        const connType = (cam.connection_type || 'rtsp').toUpperCase();

        return `
          <div class="ai-cctv-card ${isActive ? 'active' : ''}">
            <div>
              <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.45rem;">
                <span style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; background: rgba(255,255,255,0.06); padding: 0.15rem 0.45rem; border-radius: 4px;">
                  CH ${idx + 1} &bull; ${connType}
                </span>
                <span class="badge ${isOnline ? 'badge-success' : 'badge-danger'}" style="font-size: 0.65rem; padding: 0.15rem 0.45rem;">
                  <span class="status-indicator ${isOnline ? 'online' : 'offline'}"></span>
                  ${isOnline ? 'ONLINE' : 'OFFLINE'}
                </span>
              </div>

              <div style="font-weight: 700; color: #fff; font-size: 0.88rem; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${escapeHtml(cam.title || '')}">
                ${escapeHtml(cam.title || 'Kamera ' + cam.id)}
              </div>

              <div style="font-size: 0.74rem; color: #94a3b8; margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="fas fa-map-marker-alt text-danger" style="font-size: 0.7rem;"></i>
                <span>${cityUpper}</span>
                ${cam.channel ? `<span>&bull; CH ${cam.channel}</span>` : ''}
              </div>
            </div>

            <button type="button" class="btn ${isActive ? 'btn-primary' : 'btn-outline-info'} btn-sm" onclick="changeAICamera(${cam.id}); document.getElementById('ai-screen-box').scrollIntoView({behavior: 'smooth', block: 'center'});" style="width: 100%; font-size: 0.78rem; justify-content: center;">
              <i class="fas ${isActive ? 'fa-check-circle' : 'fa-crosshairs'} mr-1"></i>
              ${isActive ? 'Sedang Di-Scan AI' : 'Scan AI Kamera Ini'}
            </button>
          </div>
        `;
      }).join('');
    }

    function renderAIFacesGrid(faces) {
      const grid = document.getElementById('ai-faces-grid-container');
      if (!grid) return;

      if (!faces || faces.length === 0) {
        grid.innerHTML = `
          <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1.5rem; color: #64748b;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(56, 189, 248, 0.1); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 0.75rem;">
              <i class="fas fa-user-slash"></i>
            </div>
            <h4 style="color: #fff; font-size: 1rem; margin-bottom: 0.35rem;">Belum Ada Wajah Terdaftar</h4>
            <p style="font-size: 0.8rem; max-width: 380px; margin: 0 auto 1.25rem;">Klik tombol <b>+ Daftarkan Wajah Baru</b> untuk memindai wajah via webcam atau upload foto.</p>
            <button class="btn btn-success btn-sm" onclick="openEnrollFaceModal()">
              <i class="fas fa-user-plus mr-1"></i> + Daftarkan Wajah Baru
            </button>
          </div>
        `;
        return;
      }

      grid.innerHTML = faces.map(f => {
        const photoUrl = resolveFacePhotoUrl(f.photo_b64 || f.photo, f.id);
        const isVIP = (f.category || '').toLowerCase() === 'vip';
        const isBlacklist = (f.category || '').toLowerCase() === 'blacklist';
        const badgeColor = isVIP ? '#10b981' : (isBlacklist ? '#ef4444' : '#38bdf8');
        const badgeBg = isVIP ? 'rgba(16, 185, 129, 0.2)' : (isBlacklist ? 'rgba(239, 68, 68, 0.2)' : 'rgba(56, 189, 248, 0.2)');
        const badgeLabel = isVIP ? 'VIP' : (isBlacklist ? 'BLACKLIST' : (f.category || 'STAFF').toUpperCase());

        return `
          <div class="ai-face-card">
            <button type="button" onclick="deleteAIFace(${f.id}, '${escapeHtml(f.name)}')" title="Hapus Wajah" style="position: absolute; top: 8px; right: 8px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; border-radius: 6px; width: 26px; height: 26px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
              <i class="fas fa-trash-alt"></i>
            </button>
            <img class="ai-face-avatar" src="${escapeHtml(photoUrl)}" alt="${escapeHtml(f.name)}" onerror="this.src='${ASSETS_BASE}/image/icon.png'">
            <div style="font-weight: 700; color: #fff; font-size: 0.95rem; margin-bottom: 0.2rem;">${escapeHtml(f.name)}</div>
            <div style="font-size: 0.76rem; color: #94a3b8; margin-bottom: 0.5rem;">${escapeHtml(f.role_title || f.role || 'Staff')}</div>
            <span style="background: ${badgeBg}; color: ${badgeColor}; border: 1px solid ${badgeColor}; font-size: 0.68rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 6px;">
              ${badgeLabel}
            </span>
          </div>
        `;
      }).join('');
    }

    function filterAIFaces(query) {
      const q = (query || '').toLowerCase().trim();
      if (!q) {
        renderAIFacesGrid(cachedAIFaces);
        return;
      }
      const filtered = cachedAIFaces.filter(f => 
        (f.name || '').toLowerCase().includes(q) || 
        (f.role_title || f.role || '').toLowerCase().includes(q) ||
        (f.category || '').toLowerCase().includes(q)
      );
      renderAIFacesGrid(filtered);
    }

    function renderAIActivityLogs(logs) {
      const cont = document.getElementById('ai-activity-log-container');
      if (!cont) return;

      if (!logs || logs.length === 0) {
        cont.innerHTML = `
          <div style="text-align: center; padding: 2rem 1rem; color: #64748b; font-size: 0.82rem;">
            <i class="fas fa-radar fa-spin mb-2" style="font-size: 1.5rem; color: #38bdf8;"></i>
            <div>Menunggu deteksi wajah di kamera...</div>
          </div>
        `;
        return;
      }

      cont.innerHTML = logs.slice(0, 15).map(item => {
        const isVIP = (item.category || '').toLowerCase() === 'vip' || String(item.label || '').toUpperCase().includes('VIP');
        const isStranger = (item.label || '').toUpperCase().includes('STRANGER') || (item.label || '').toUpperCase().includes('PENGUNJUNG');
        const badgeBg = isVIP ? 'rgba(16, 185, 129, 0.2)' : (isStranger ? 'rgba(245, 158, 11, 0.2)' : 'rgba(56, 189, 248, 0.2)');
        const badgeColor = isVIP ? '#34d399' : (isStranger ? '#f59e0b' : '#38bdf8');
        const badgeLabel = isVIP ? 'WHITELIST' : (isStranger ? 'STRANGER' : 'TERVERIFIKASI');
        const simVal = item.confidence ? (Math.round(item.confidence) + '% Similarity') : 'Biometrik Match';

        return `
          <div class="ai-activity-item">
            <div style="width: 44px; height: 44px; border-radius: 8px; overflow: hidden; background: #000; border: 1.5px solid ${badgeColor}; flex-shrink: 0;">
              ${item.snapshot ? `<img src="${item.snapshot}" style="width: 100%; height: 100%; object-fit: cover;">` : `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: ${badgeColor};"><i class="fas fa-user"></i></div>`}
            </div>
            <div style="flex: 1; min-width: 0;">
              <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.15rem;">
                <strong style="color: #fff; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${escapeHtml(item.label || 'Wajah Terdeteksi')}</strong>
                <span style="font-size: 0.65rem; font-weight: 700; background: ${badgeBg}; color: ${badgeColor}; border: 1px solid ${badgeColor}; padding: 0.1rem 0.4rem; border-radius: 4px;">${badgeLabel}</span>
              </div>
              <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.72rem; color: #94a3b8;">
                <span style="color: ${badgeColor}; font-weight: 600;">${simVal}</span>
                <span>${escapeHtml(item.timestamp ? item.timestamp.split(' ')[1] || item.timestamp : 'Baru Saja')}</span>
              </div>
            </div>
          </div>
        `;
      }).join('');
    }

    function appendRealtimeAILog(name, category, confidence, snapshot) {
      const logItem = {
        id: Date.now(),
        label: name,
        category: category,
        confidence: confidence || 97.4,
        snapshot: snapshot || '',
        timestamp: new Date().toLocaleTimeString('id-ID')
      };
      cachedAILogs.unshift(logItem);
      if (cachedAILogs.length > 25) cachedAILogs.pop();
      renderAIActivityLogs(cachedAILogs);

      // Increment stat count
      const detEl = document.getElementById('ai-stat-detections');
      if (detEl) detEl.textContent = parseInt(detEl.textContent || '0') + 1;

      // Play beep if audio enabled
      if (isAISoundEnabled) {
        playAIAudioBeep();
      }

      // Show HUD banner
      showAIHUDBanner(name, category, confidence);
    }

    function playAIAudioBeep() {
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(440, audioCtx.currentTime + 0.25);
        gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.25);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.25);
      } catch (e) {}
    }

    function showAIHUDBanner(name, category, confidence) {
      const banner = document.getElementById('ai-hud-banner');
      if (!banner) return;
      const cat = (category || '').toLowerCase();
      const isVIP = cat === 'vip';
      const isEmployee = cat === 'employee';
      const isBlacklist = cat === 'blacklist';
      const isGuest = cat === 'guest' || (!isVIP && !isEmployee && !isBlacklist);
      const isStranger = (name || '').toUpperCase().includes('STRANGER');

      const bannerColor = isVIP ? '#f59e0b' : (isEmployee ? '#3b82f6' : (isBlacklist ? '#ef4444' : '#00f0ff'));
      const bannerBgBadge = isVIP ? 'rgba(245, 158, 11, 0.2)' : (isEmployee ? 'rgba(59, 130, 246, 0.2)' : (isBlacklist ? 'rgba(239, 68, 68, 0.2)' : 'rgba(0, 240, 255, 0.2)'));

      banner.style.borderColor = bannerColor;
      const iconEl = document.getElementById('ai-hud-icon');
      if (iconEl) {
        iconEl.style.color = bannerColor;
        iconEl.style.background = bannerBgBadge;
        iconEl.innerHTML = isVIP ? '<i class="fas fa-crown"></i>' : (isEmployee ? '<i class="fas fa-id-badge"></i>' : (isBlacklist ? '<i class="fas fa-exclamation-triangle"></i>' : '<i class="fas fa-user"></i>'));
      }

      document.getElementById('ai-hud-name').textContent = name;
      const badgeElem = document.getElementById('ai-hud-badge');
      if (badgeElem) {
        badgeElem.textContent = isVIP ? 'VIP' : (isEmployee ? 'KARYAWAN' : (isBlacklist ? 'BLACKLIST' : (isStranger ? 'STRANGER' : 'PENGUNJUNG')));
        badgeElem.style.color = bannerColor;
        badgeElem.style.background = bannerBgBadge;
      }
      const subElem = document.getElementById('ai-hud-sub');
      if (subElem) {
        if (isEmployee) {
          subElem.textContent = `${Math.round(confidence)}% Terverifikasi • Staff / Karyawan Resmi`;
        } else if (isVIP) {
          subElem.textContent = `${Math.round(confidence)}% Biometric Match • Direksi / VIP Terdaftar`;
        } else if (isBlacklist) {
          subElem.textContent = `PERINGATAN • Masuk Daftar Waspada`;
        } else {
          subElem.textContent = `${Math.round(confidence)}% Akurasi Deteksi • Terpantau CCTV`;
        }
      }

      banner.style.display = 'flex';
      setTimeout(() => {
        banner.style.display = 'none';
      }, 3500);
    }

    function toggleAISoundAlertManual() {
      isAISoundEnabled = !isAISoundEnabled;
      const btn = document.getElementById('btn-toggle-sound');
      if (btn) {
        if (isAISoundEnabled) {
          btn.classList.add('active');
          btn.innerHTML = '<i class="fas fa-volume-up mr-1 text-info" id="ai-sound-icon"></i> Suara';
        } else {
          btn.classList.remove('active');
          btn.innerHTML = '<i class="fas fa-volume-mute mr-1" style="color: #94a3b8;" id="ai-sound-icon"></i> Mute';
        }
      }
    }

    function toggleAISoundAlert(checked) {
      isAISoundEnabled = Boolean(checked);
    }

    function toggleAIAutoScan() {
      isAutoScanActive = !isAutoScanActive;
      const btn = document.getElementById('btn-toggle-autoscan');
      if (btn) {
        if (isAutoScanActive) {
          btn.classList.add('active');
          btn.innerHTML = '<i class="fas fa-bolt mr-1"></i> Auto-Scan: AKTIF';
        } else {
          btn.classList.remove('active');
          btn.innerHTML = '<i class="fas fa-pause mr-1"></i> Auto-Scan: JEDA';
        }
      }
    }

    function toggleAIFullscreen() {
      const screen = document.getElementById('ai-screen-box');
      if (!screen) return;
      if (!document.fullscreenElement) {
        screen.requestFullscreen().catch(err => alert('Gagal masuk ke layar penuh'));
      } else {
        document.exitFullscreen();
      }
    }

    async function clearAILogs() {
      cachedAILogs = [];
      renderAIActivityLogs(cachedAILogs);
      try {
        await fetch('api.php?action=clear_logs');
      } catch (e) {}
    }

    // =========================================================================
    // VIDEO STREAM & CAMERA SWITCHING
    // =========================================================================
    async function initAIFaceSuite() {
      aiLiveVideo = document.getElementById('ai-video-player');
      aiCanvasOverlay = document.getElementById('ai-canvas-overlay');

      await initFaceAPI();
      initCOCOSSD();
      await loadAIFaceData();

      // Start stream: if a specific CCTV camera is selected, connect to it; otherwise default to webcam
      if (currentAICamera && currentAICamera.id && currentAICamera.id !== 'webcam') {
        changeAICamera(currentAICamera.id);
      } else if (aiLiveVideo && !aiWebcamStream) {
        startAIWebcamLive();
      }
    }

    async function startAIWebcamLive() {
      const video = document.getElementById('ai-video-player');
      const select = document.getElementById('ai-camera-selector');
      const statusLabel = document.getElementById('ai-active-status-label');
      const loader = document.getElementById('ai-video-loader');
      if (!video) return;

      currentAICamera = { id: 'webcam', title: 'Live Webcam Laptop' };
      if (select) select.value = 'webcam';
      if (statusLabel) statusLabel.innerHTML = '<span style="color: #34d399;">Webcam Laptop Aktif</span>';
      if (loader) loader.style.display = 'none';
      loadCountingLineConfig();

      // Update HUD Overlay & Footer
      const hudCam = document.getElementById('ai-hud-cam-name');
      if (hudCam) hudCam.textContent = 'WEBCAM LAPTOP';
      const footerCam = document.getElementById('ai-footer-cam-name');
      if (footerCam) footerCam.innerHTML = '<i class="fas fa-camera text-info mr-1"></i> Aktif: Webcam Laptop';
      const resTag = document.getElementById('ai-feed-resolution');
      if (resTag) resTag.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i> WEBCAM 720p';
      const modelBadge = document.getElementById('ai-hud-model-mode');
      if (modelBadge) modelBadge.textContent = '128D RESNET • 30 FPS';

      if (typeof updateAICameraLists === 'function') updateAICameraLists();

      if (aiHlsInstance) {
        aiHlsInstance.destroy();
        aiHlsInstance = null;
      }

      try {
        if (aiWebcamStream) {
          aiWebcamStream.getTracks().forEach(t => t.stop());
        }
        aiWebcamStream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        video.srcObject = aiWebcamStream;
        await video.play();

        startFaceAPIDetectionLoop();
      } catch (err) {
        console.error('Webcam error:', err);
        alert('Gagal mengakses webcam. Pastikan browser diberikan izin kamera.');
      }
    }

    async function changeAICamera(camId) {
      if (camId === 'webcam') {
        startAIWebcamLive();
        return;
      }

      const video = document.getElementById('ai-video-player');
      const loader = document.getElementById('ai-video-loader');
      const statusLabel = document.getElementById('ai-active-status-label');
      const select = document.getElementById('ai-camera-selector');
      if (!video) return;

      // Stop webcam and reset video player completely
      if (aiWebcamStream) {
        aiWebcamStream.getTracks().forEach(t => t.stop());
        aiWebcamStream = null;
      }
      video.pause();
      video.srcObject = null;
      video.removeAttribute('src');
      video.muted = true;

      const camIdx = (localCameras || []).findIndex(c => String(c.id) === String(camId));
      const cam = (localCameras || [])[camIdx] || (localCameras || []).find(c => String(c.id) === String(camId));
      currentAICamera = cam || { id: camId, title: 'Kamera CCTV ' + camId };
      loadCountingLineConfig();
      loadPersistentTaggedForCamera(currentAICamera.id);

      if (select) select.value = String(camId);

      // Update HUD Overlay & Footer
      const chNum = camIdx >= 0 ? (camIdx + 1) : camId;
      const hudCam = document.getElementById('ai-hud-cam-name');
      if (hudCam) hudCam.textContent = `[CH ${chNum}] ${(currentAICamera.title || '').toUpperCase()}`;
      const footerCam = document.getElementById('ai-footer-cam-name');
      if (footerCam) footerCam.innerHTML = `<i class="fas fa-video text-info mr-1"></i> Aktif: [CH ${chNum}] ${escapeHtml(currentAICamera.title || '')}`;
      const resTag = document.getElementById('ai-feed-resolution');
      if (resTag) resTag.innerHTML = '<i class="fas fa-signal text-success mr-1"></i> RTSP 1080p';
      const modelBadge = document.getElementById('ai-hud-model-mode');
      if (modelBadge) modelBadge.textContent = 'COCO-SSD HUMAN • 30 FPS';

      if (typeof updateAICameraLists === 'function') updateAICameraLists();

      if (statusLabel) statusLabel.innerHTML = `<span style="color: #38bdf8;">${escapeHtml(currentAICamera.title)}</span>`;
      if (loader) {
        loader.style.display = 'flex';
        loader.innerHTML = `
          <div class="spinner"></div>
          <div id="ai-loader-text">Menghubungkan Stream [CH ${chNum}] ${escapeHtml(currentAICamera.title || '')}...</div>
        `;
      }

      // Standardize stream URL with full fallbacks identical to modal live player
      let streamUrl = cam ? (cam.hls_url || '') : '';
      if (!streamUrl && cam) {
        streamUrl = `https://stream.loewixcctv.com/${cam.streamPath || ('cam_live_' + cam.id)}/index.m3u8`;
      }

      if (aiHlsInstance) {
        aiHlsInstance.destroy();
        aiHlsInstance = null;
      }

      if (window._aiLiveSyncInterval) {
        clearInterval(window._aiLiveSyncInterval);
        window._aiLiveSyncInterval = null;
      }

      if (typeof Hls !== 'undefined' && Hls.isSupported() && streamUrl.includes('.m3u8')) {
        aiHlsInstance = new Hls({
          enableWorker: true,
          lowLatencyMode: true,
          liveSyncDurationCount: 1, // Keep playback firmly at the newest live segment
          liveMaxLatencyDurationCount: 2, // Auto-jump to live if lag exceeds 2 segments (~4s)
          maxLiveSyncPlaybackRate: 1.35, // Smoothly speed up playback to eliminate lag
          maxBufferLength: 2, // Only buffer 2 seconds to prevent stale video build-up
          maxMaxBufferLength: 4,
          backBufferLength: 0, // Immediately purge played frames to prevent video repeating/looping
          manifestLoadingTimeOut: 10000,
          manifestLoadingMaxRetry: 6,
          levelLoadingTimeOut: 10000,
          levelLoadingMaxRetry: 6,
          fragLoadingTimeOut: 10000,
          fragLoadingMaxRetry: 6
        });
        aiHlsInstance.loadSource(streamUrl);
        aiHlsInstance.attachMedia(video);
        aiHlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
          if (loader) loader.style.display = 'none';
          video.play().catch(e => console.log('Autoplay prevented:', e));
          startFaceAPIDetectionLoop();
        });
        aiHlsInstance.on(Hls.Events.ERROR, (event, data) => {
          if (data.fatal) {
            switch (data.type) {
              case Hls.ErrorTypes.NETWORK_ERROR:
                console.warn('[AI Camera HLS] Network error, attempting recovery...', data);
                aiHlsInstance.startLoad();
                break;
              case Hls.ErrorTypes.MEDIA_ERROR:
                console.warn('[AI Camera HLS] Media error, attempting recovery...', data);
                aiHlsInstance.recoverMediaError();
                break;
              default:
                if (loader) {
                  loader.innerHTML = '<div style="color: #f87171;"><i class="fas fa-exclamation-circle"></i> Menunggu feed RTSP dari kamera...</div>';
                }
                break;
            }
          }
        });

        // Continuous Live-Edge Synchronization Watchdog (Eliminates CCTV Delay & Stale Video Looping)
        window._aiLiveSyncInterval = setInterval(() => {
          if (!video || video.paused || video.ended || !video.buffered || video.buffered.length === 0) return;
          try {
            const bufEnd = video.buffered.end(video.buffered.length - 1);
            const lag = bufEnd - video.currentTime;

            // Update Latency Badge in HUD
            const resTag = document.getElementById('ai-feed-resolution');
            if (resTag) {
              if (lag > 2.5) {
                resTag.innerHTML = `<span style="color: #f59e0b; font-weight: 700; cursor: pointer;" onclick="jumpToLiveEdge()" title="Klik untuk sinkronkan ke detik sekarang"><i class="fas fa-sync fa-spin mr-1"></i> Sinkronisasi (${lag.toFixed(1)}s)...</span>`;
              } else {
                resTag.innerHTML = `<span style="color: #10b981; font-weight: 700; cursor: pointer;" onclick="jumpToLiveEdge()"><i class="fas fa-circle mr-1" style="font-size: 0.6rem; vertical-align: middle;"></i> LIVE REALTIME (${lag.toFixed(1)}s)</span>`;
              }
            }

            // If video lags behind real-time edge by more than 2.0s, jump directly to the live edge!
            if (lag > 2.0) {
              video.currentTime = Math.max(0, bufEnd - 0.3);
            }
          } catch (e) {}
        }, 1000);
      } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = streamUrl;
        video.addEventListener('loadedmetadata', () => {
          if (loader) loader.style.display = 'none';
          video.play().catch(e => console.log('Autoplay prevented:', e));
          startFaceAPIDetectionLoop();
        });
      } else {
        video.src = streamUrl;
        video.onloadedmetadata = () => {
          if (loader) loader.style.display = 'none';
          video.play().catch(() => {});
          startFaceAPIDetectionLoop();
        };
      }
    }

    // =========================================================================
    // 3D BIOMETRIC FACEMESH & RETICLE RENDERER (100% PRD 17 SEP)
    // =========================================================================
    function resolveBiometricMeshNodes(landmarks68, bx, by, bw, bh) {
      if (!landmarks68 || landmarks68.length < 68) {
        return null;
      }
      const pt = (idx, fbX, fbY) => {
        const p = landmarks68[idx];
        return p ? { x: p.x || p._x, y: p.y || p._y } : { x: fbX, y: fbY };
      };

      const chin = pt(8, bx + bw * 0.5, by + bh * 0.98);
      const glab = pt(27, bx + bw * 0.5, by + bh * 0.25);
      const upX = glab.x - chin.x;
      const upY = glab.y - chin.y;
      const faceLen = Math.hypot(upX, upY) || bh;
      const normUpX = upX / faceLen;
      const normUpY = upY / faceLen;
      const fhDist = faceLen * 0.32;

      const browL = pt(19, bx + bw * 0.30, by + bh * 0.22);
      const browR = pt(24, bx + bw * 0.70, by + bh * 0.22);

      return {
        foreheadTopL: { x: browL.x + normUpX * fhDist, y: browL.y + normUpY * fhDist },
        foreheadTopR: { x: browR.x + normUpX * fhDist, y: browR.y + normUpY * fhDist },
        templeL:      pt(0,  bx + bw * 0.12, by + bh * 0.22),
        templeR:      pt(16, bx + bw * 0.88, by + bh * 0.22),
        glabella:     glab,
        browMidL:     browL,
        browMidR:     browR,
        eyeL:         (landmarks68[36] && landmarks68[39]) ? { x: (landmarks68[36].x + landmarks68[39].x)/2, y: (landmarks68[37].y + landmarks68[41].y)/2 } : pt(36, bx + bw * 0.3, by + bh * 0.38),
        eyeR:         (landmarks68[42] && landmarks68[45]) ? { x: (landmarks68[42].x + landmarks68[45].x)/2, y: (landmarks68[43].y + landmarks68[47].y)/2 } : pt(45, bx + bw * 0.7, by + bh * 0.38),
        noseBridge:   pt(27, bx + bw * 0.5, by + bh * 0.35),
        noseMid:      pt(29, bx + bw * 0.5, by + bh * 0.46),
        noseTip:      pt(30, bx + bw * 0.5, by + bh * 0.56),
        nostrilL:     pt(31, bx + bw * 0.4, by + bh * 0.56),
        nostrilR:     pt(35, bx + bw * 0.6, by + bh * 0.56),
        cheekUpperL:  pt(1,  bx + bw * 0.16, by + bh * 0.44),
        cheekUpperR:  pt(15, bx + bw * 0.84, by + bh * 0.44),
        cheekLowerL:  pt(3,  bx + bw * 0.16, by + bh * 0.64),
        cheekLowerR:  pt(13, bx + bw * 0.84, by + bh * 0.64),
        philtrum:     pt(33, bx + bw * 0.5, by + bh * 0.68),
        mouthL:       pt(48, bx + bw * 0.32, by + bh * 0.76),
        mouthR:       pt(54, bx + bw * 0.68, by + bh * 0.76),
        lipBot:       pt(57, bx + bw * 0.5, by + bh * 0.84),
        chinL:        pt(5,  bx + bw * 0.24, by + bh * 0.90),
        chinR:        pt(11, bx + bw * 0.76, by + bh * 0.90),
        chinTip:      chin
      };
    }

    function drawBiometricFacialMesh(ctx, x, y, w, h, pts, label, isVIP) {
      if (!pts) return;
      ctx.save();

      // 1. Subtle Holographic Mask Gradient
      try {
        ctx.beginPath();
        ctx.moveTo(pts.foreheadTopL.x, pts.foreheadTopL.y);
        ctx.lineTo(pts.foreheadTopR.x, pts.foreheadTopR.y);
        ctx.lineTo(pts.templeR.x, pts.templeR.y);
        ctx.lineTo(pts.cheekUpperR.x, pts.cheekUpperR.y);
        ctx.lineTo(pts.chinR.x, pts.chinR.y);
        ctx.lineTo(pts.chinTip.x, pts.chinTip.y);
        ctx.lineTo(pts.chinL.x, pts.chinL.y);
        ctx.lineTo(pts.cheekUpperL.x, pts.cheekUpperL.y);
        ctx.lineTo(pts.templeL.x, pts.templeL.y);
        ctx.closePath();
        const grad = ctx.createLinearGradient(x, y, x, y + h);
        grad.addColorStop(0, 'rgba(139, 92, 246, 0.16)');
        grad.addColorStop(1, 'rgba(56, 189, 248, 0.08)');
        ctx.fillStyle = grad;
        ctx.fill();
      } catch (e) {}

      // 2. Gleaming White Triangulation Lines
      const edges = [
        [pts.foreheadTopL, pts.foreheadTopR],
        [pts.foreheadTopL, pts.templeL], [pts.foreheadTopR, pts.templeR],
        [pts.foreheadTopL, pts.glabella], [pts.foreheadTopR, pts.glabella],
        [pts.templeL, pts.browMidL], [pts.templeR, pts.browMidR],
        [pts.browMidL, pts.glabella], [pts.browMidR, pts.glabella],
        [pts.browMidL, pts.eyeL], [pts.browMidR, pts.eyeR],
        [pts.glabella, pts.noseBridge], [pts.eyeL, pts.noseBridge], [pts.eyeR, pts.noseBridge],
        [pts.eyeL, pts.cheekUpperL], [pts.eyeR, pts.cheekUpperR],
        [pts.noseBridge, pts.noseMid], [pts.noseMid, pts.noseTip],
        [pts.noseMid, pts.nostrilL], [pts.noseMid, pts.nostrilR],
        [pts.cheekUpperL, pts.nostrilL], [pts.cheekUpperR, pts.nostrilR],
        [pts.cheekUpperL, pts.cheekLowerL], [pts.cheekUpperR, pts.cheekLowerR],
        [pts.cheekLowerL, pts.mouthL], [pts.cheekLowerR, pts.mouthR],
        [pts.noseTip, pts.philtrum], [pts.philtrum, pts.mouthL], [pts.philtrum, pts.mouthR],
        [pts.philtrum, pts.lipBot], [pts.mouthL, pts.lipBot], [pts.mouthR, pts.lipBot],
        [pts.lipBot, pts.chinTip], [pts.chinL, pts.chinTip], [pts.chinR, pts.chinTip]
      ];

      ctx.strokeStyle = 'rgba(255, 255, 255, 0.88)';
      ctx.shadowColor = 'rgba(255, 255, 255, 0.70)';
      ctx.shadowBlur = 6;
      ctx.lineWidth = 1.5;
      ctx.beginPath();
      edges.forEach(([p1, p2]) => {
        if (p1 && p2) {
          ctx.moveTo(p1.x, p1.y);
          ctx.lineTo(p2.x, p2.y);
        }
      });
      ctx.stroke();

      // 3. Glowing White Jewel Nodes
      const allNodes = Object.values(pts);
      // Cyan energy halo
      ctx.shadowColor = '#00f0ff';
      ctx.shadowBlur = 10;
      ctx.fillStyle = 'rgba(0, 240, 255, 0.75)';
      allNodes.forEach(pt => {
        ctx.beginPath();
        ctx.arc(pt.x, pt.y, 5, 0, Math.PI * 2);
        ctx.fill();
      });
      // Gleaming diamond core
      ctx.shadowColor = '#ffffff';
      ctx.shadowBlur = 4;
      ctx.fillStyle = '#ffffff';
      allNodes.forEach(pt => {
        ctx.beginPath();
        ctx.arc(pt.x, pt.y, 3, 0, Math.PI * 2);
        ctx.fill();
      });

      // 4. Snug Corner Brackets & Tag
      drawSnugBrackets(ctx, x, y, w, h, label, isVIP);

      ctx.restore();
    }

    // =========================================================================
    // TEMPORAL MOTION & STATIONARY OBJECT SUPPRESSION (SOS ENGINE)
    // Differentiates living humans (breathing/walking/moving) from parked bikes & posters
    // =========================================================================
    let _aiMotionCanvasPrev = null;
    let _aiMotionCanvasCurr = null;
    let _aiMotionCtxPrev = null;
    let _aiMotionCtxCurr = null;
    let _aiMotionDataPrev = null;
    let _aiMotionDataCurr = null;
    let _lastMotionFrameTime = 0;
    const MOTION_SAMPLE_W = 160;
    const MOTION_SAMPLE_H = 90;

    function updateMotionBuffers(video) {
      if (!video || video.readyState < 2 || video.videoWidth === 0) return;
      const now = Date.now();
      if (now - _lastMotionFrameTime < 90) return; // ~11 FPS motion sampling
      _lastMotionFrameTime = now;

      if (!_aiMotionCanvasPrev) {
        _aiMotionCanvasPrev = document.createElement('canvas');
        _aiMotionCanvasPrev.width = MOTION_SAMPLE_W;
        _aiMotionCanvasPrev.height = MOTION_SAMPLE_H;
        _aiMotionCtxPrev = _aiMotionCanvasPrev.getContext('2d', { willReadFrequently: true });

        _aiMotionCanvasCurr = document.createElement('canvas');
        _aiMotionCanvasCurr.width = MOTION_SAMPLE_W;
        _aiMotionCanvasCurr.height = MOTION_SAMPLE_H;
        _aiMotionCtxCurr = _aiMotionCanvasCurr.getContext('2d', { willReadFrequently: true });
      }

      if (_aiMotionDataCurr) {
        _aiMotionDataPrev = _aiMotionDataCurr;
      }

      try {
        _aiMotionCtxCurr.drawImage(video, 0, 0, MOTION_SAMPLE_W, MOTION_SAMPLE_H);
        _aiMotionDataCurr = _aiMotionCtxCurr.getImageData(0, 0, MOTION_SAMPLE_W, MOTION_SAMPLE_H).data;
      } catch (e) {
        _aiMotionDataCurr = null;
      }
    }

    function getBoxMotionDelta(bx, by, bw, bh, srcW, srcH) {
      if (!_aiMotionDataPrev || !_aiMotionDataCurr || srcW <= 0 || srcH <= 0) {
        return 0.0;
      }
      const mx = Math.max(0, Math.min(MOTION_SAMPLE_W - 2, Math.round((bx / srcW) * MOTION_SAMPLE_W)));
      const my = Math.max(0, Math.min(MOTION_SAMPLE_H - 2, Math.round((by / srcH) * MOTION_SAMPLE_H)));
      const mw = Math.max(2, Math.min(MOTION_SAMPLE_W - mx, Math.round((bw / srcW) * MOTION_SAMPLE_W)));
      const mh = Math.max(2, Math.min(MOTION_SAMPLE_H - my, Math.round((bh / srcH) * MOTION_SAMPLE_H)));

      let significantChanges = 0;
      let diffSum = 0;
      let count = 0;
      for (let y = my; y < my + mh; y++) {
        for (let x = mx; x < mx + mw; x++) {
          const idx = (y * MOTION_SAMPLE_W + x) * 4;
          const dr = Math.abs(_aiMotionDataCurr[idx] - _aiMotionDataPrev[idx]);
          const dg = Math.abs(_aiMotionDataCurr[idx + 1] - _aiMotionDataPrev[idx + 1]);
          const db = Math.abs(_aiMotionDataCurr[idx + 2] - _aiMotionDataPrev[idx + 2]);
          const maxChan = Math.max(dr, dg, db);
          diffSum += (dr + dg + db) / 3;
          // Compression quantization noise in HLS stream is typically < 18.
          // Genuine human limb / posture movement produces delta > 18.
          if (maxChan > 18) {
            significantChanges++;
          }
          count++;
        }
      }
      const sigRatio = count > 0 ? (significantChanges / count) : 0;
      // Returns high score (> 1.5) for living movement, low (< 0.4) for video compression noise
      return count > 0 ? (sigRatio * 15.0 + (diffSum / count) * 0.08) : 0.0;
    }

    // =========================================================================
    // BIOMETRIC HUMAN VALIDATION (SKIN CHROMINANCE & BIO SIGNALS)
    // =========================================================================
    let _aiBiometricCanvas = null;
    let _aiBiometricCtx = null;
    function hasHumanBiometricSignals(videoOrCanvas, bx, by, bw, bh, motionDelta) {
      if (!videoOrCanvas || bw < 5 || bh < 10) return false;

      try {
        const maxW = videoOrCanvas.width || videoOrCanvas.videoWidth || 640;
        const maxH = videoOrCanvas.height || videoOrCanvas.videoHeight || 360;

        if (!_aiBiometricCanvas) {
          _aiBiometricCanvas = document.createElement('canvas');
          _aiBiometricCanvas.width = 16;
          _aiBiometricCanvas.height = 16;
          _aiBiometricCtx = _aiBiometricCanvas.getContext('2d', { willReadFrequently: true });
        }

        // Test Head/Face/Neck region (top 30% of box, center 60% width)
        const hx = Math.max(0, Math.min(maxW - 4, Math.round(bx + bw * 0.20)));
        const hy = Math.max(0, Math.min(maxH - 4, Math.round(by)));
        const hw = Math.max(4, Math.min(maxW - hx, Math.round(bw * 0.60)));
        const hh = Math.max(4, Math.min(maxH - hy, Math.round(bh * 0.30)));

        _aiBiometricCtx.clearRect(0, 0, 16, 16);
        _aiBiometricCtx.drawImage(videoOrCanvas, hx, hy, hw, hh, 0, 0, 16, 16);
        const data = _aiBiometricCtx.getImageData(0, 0, 16, 16).data;

        let skinCount = 0;
        let totalCount = 0;
        for (let i = 0; i < data.length; i += 4) {
          const r = data[i];
          const g = data[i + 1];
          const b = data[i + 2];
          // Organic human melanin/hemoglobin signature
          const Y  =  0.299 * r + 0.587 * g + 0.114 * b;
          const Cb = -0.1687 * r - 0.3313 * g + 0.5 * b + 128;
          const Cr =  0.5 * r - 0.4187 * g - 0.0813 * b + 128;
          // Organic skin: red dominant over green and blue, calibrated Cb/Cr cluster
          const isSkin = (Y >= 40 && Y <= 230 && Cb >= 80 && Cb <= 135 && Cr >= 130 && Cr <= 175 && (r > b + 16) && (r > g + 4));
          if (isSkin) skinCount++;
          totalCount++;
        }

        const skinRatio = totalCount > 0 ? (skinCount / totalCount) : 0;
        return (skinRatio >= 0.15);
      } catch (e) {
        return false;
      }
    }

    // =========================================================================
    // VISUAL ATTRIBUTE & UPPER BODY CLOTHING PROFILER (GAMBAR 2 REPLICA)
    // =========================================================================
    function extractPedestrianClothingProfile(videoOrCanvas, bx, by, bw, bh) {
      if (!videoOrCanvas || bw < 10 || bh < 15) return { colorName: 'Netral', hex: '#64748b' };
      try {
        const tx = Math.max(0, Math.round(bx + bw * 0.28));
        const ty = Math.max(0, Math.round(by + bh * 0.22));
        const tw = Math.max(4, Math.round(bw * 0.44));
        const th = Math.max(4, Math.round(bh * 0.28));

        const sampleCanvas = document.createElement('canvas');
        sampleCanvas.width = 12;
        sampleCanvas.height = 12;
        const sCtx = sampleCanvas.getContext('2d', { willReadFrequently: true });
        sCtx.drawImage(videoOrCanvas, tx, ty, tw, th, 0, 0, 12, 12);
        const data = sCtx.getImageData(0, 0, 12, 12).data;

        let tr = 0, tg = 0, tb = 0, count = 0;
        for (let i = 0; i < data.length; i += 4) {
          tr += data[i];
          tg += data[i + 1];
          tb += data[i + 2];
          count++;
        }
        if (count === 0) return { colorName: 'Netral', hex: '#64748b' };
        const r = Math.round(tr / count);
        const g = Math.round(tg / count);
        const b = Math.round(tb / count);
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;

        let name = 'Hitam / Gelap';
        if (brightness < 65) name = 'Hitam / Gelap';
        else if (brightness > 195) name = 'Putih / Terang';
        else if (b > r + 25 && b > g + 15) name = 'Biru / Kaos';
        else if (r > g + 30 && r > b + 30) name = 'Merah / Terang';
        else if (g > r + 20 && g > b + 15) name = 'Hijau / Rompi';
        else if (r > 140 && g > 110 && b < 80) name = 'Kuning / Oranye';
        else if (brightness < 125) name = 'Abu-abu Gelap';
        else name = 'Abu-abu Terang';

        return { colorName: name, hex: `rgb(${r},${g},${b})`, r, g, b };
      } catch (e) {
        return { colorName: 'Netral', hex: '#64748b' };
      }
    }

    // =========================================================================
    // HIGH-TECH PEDESTRIAN SURVEILLANCE RETICLE (GAMBAR 2 EXACT REPLICA)
    // =========================================================================
    function drawSurveillancePedestrianReticle(ctx, bx, by, bw, bh, ent) {
      ctx.save();
      bx = Math.round(bx);
      by = Math.round(by);
      bw = Math.round(bw);
      bh = Math.round(bh);

      const isVIP = ent.isVIP || (ent.category === 'vip');
      const isEmployee = ent.isEmployee || (ent.category === 'employee');
      const isBlacklist = (ent.category === 'blacklist');

      // Vibrant, distinctive color palette (TikTok reference standard):
      // KARYAWAN: Royal Blue (#3b82f6) + vibrant blue glow
      // VIP: Radiant Amber Gold (#f59e0b)
      // BLACKLIST: Alert Crimson Red (#ef4444)
      // PENGUNJUNG: Electric Cyan (#00f0ff)
      const reticleColor = isVIP ? '#f59e0b' : (isEmployee ? '#3b82f6' : (isBlacklist ? '#ef4444' : '#00f0ff'));
      const reticleGlow = isVIP ? 'rgba(245, 158, 11, 0.85)' : (isEmployee ? 'rgba(59, 130, 246, 0.90)' : (isBlacklist ? 'rgba(239, 68, 68, 0.85)' : 'rgba(0, 240, 255, 0.85)'));
      const icon = isVIP ? '🌟' : (isEmployee ? '👔' : (isBlacklist ? '🚫' : '🚶'));

      // Corner bracket arms tailored to human body proportions
      const armW = Math.min(32, Math.max(14, Math.round(bw * 0.18)));
      const armH = Math.min(32, Math.max(14, Math.round(bh * 0.12)));

      ctx.strokeStyle = reticleColor;
      ctx.shadowColor = reticleGlow;
      ctx.shadowBlur = 14;
      ctx.lineWidth = 3.5;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';

      // 4 corners of surveillance bounding box
      // Top-Left
      ctx.beginPath();
      ctx.moveTo(bx, by + armH);
      ctx.lineTo(bx, by);
      ctx.lineTo(bx + armW, by);
      ctx.stroke();

      // Top-Right
      ctx.beginPath();
      ctx.moveTo(bx + bw - armW, by);
      ctx.lineTo(bx + bw, by);
      ctx.lineTo(bx + bw, by + armH);
      ctx.stroke();

      // Bottom-Left
      ctx.beginPath();
      ctx.moveTo(bx, by + bh - armH);
      ctx.lineTo(bx, by + bh);
      ctx.lineTo(bx + armW, by + bh);
      ctx.stroke();

      // Bottom-Right
      ctx.beginPath();
      ctx.moveTo(bx + bw - armW, by + bh);
      ctx.lineTo(bx + bw, by + bh);
      ctx.lineTo(bx + bw, by + bh - armH);
      ctx.stroke();

      // Subtle Center Targeting Crosshair (Upper Torso / Head)
      const cx = bx + bw / 2;
      const cy = by + bh * 0.32;
      ctx.strokeStyle = isVIP ? 'rgba(245, 158, 11, 0.5)' : (isEmployee ? 'rgba(59, 130, 246, 0.5)' : 'rgba(0, 240, 255, 0.4)');
      ctx.lineWidth = 1.2;
      ctx.setLineDash([3, 3]);
      ctx.beginPath();
      ctx.moveTo(cx - 14, cy); ctx.lineTo(cx + 14, cy);
      ctx.moveTo(cx, cy - 14); ctx.lineTo(cx, cy + 14);
      ctx.stroke();
      ctx.setLineDash([]);

      // Ring Dasar Lantai (Foot Ground Contact - Referensi Garis Penghitung "Dasar Lantai")
      const footX = bx + bw * 0.5;
      const footY = by + bh;
      const ringRx = Math.max(16, bw * 0.35);
      const ringRy = Math.max(6, ringRx * 0.38);

      ctx.save();
      // Floor perspective shadow disk
      ctx.beginPath();
      ctx.ellipse(footX, footY, ringRx, ringRy, 0, 0, Math.PI * 2);
      ctx.fillStyle = ent.isTouchingLine ? 'rgba(245, 158, 11, 0.25)' : (isEmployee ? 'rgba(59, 130, 246, 0.15)' : 'rgba(0, 240, 255, 0.12)');
      ctx.fill();

      // Outer ellipse ring on the floor
      ctx.beginPath();
      ctx.ellipse(footX, footY, ringRx, ringRy, 0, 0, Math.PI * 2);
      if (ent.isTouchingLine) {
        // Efek visual saat baru menyentuh garis tapi belum lewat
        ctx.strokeStyle = '#f59e0b';
        ctx.shadowColor = 'rgba(245, 158, 11, 0.95)';
        ctx.lineWidth = 3;
        ctx.shadowBlur = 14;
      } else {
        ctx.strokeStyle = isVIP ? 'rgba(245, 158, 11, 0.8)' : (isEmployee ? 'rgba(59, 130, 246, 0.85)' : 'rgba(0, 240, 255, 0.7)');
        ctx.shadowColor = ctx.strokeStyle;
        ctx.lineWidth = 1.8;
        ctx.shadowBlur = 8;
      }
      ctx.stroke();

      // Foot center target crosshair on floor (+)
      ctx.beginPath();
      ctx.strokeStyle = ent.isTouchingLine ? '#f59e0b' : '#ffffff';
      ctx.lineWidth = 1.5;
      ctx.moveTo(footX - 6, footY); ctx.lineTo(footX + 6, footY);
      ctx.moveTo(footX, footY - 4); ctx.lineTo(footX, footY + 4);
      ctx.stroke();

      // Dashed vertical tether line down to floor anchor
      ctx.setLineDash([2, 3]);
      ctx.strokeStyle = ent.isTouchingLine ? 'rgba(245, 158, 11, 0.7)' : 'rgba(0, 240, 255, 0.4)';
      ctx.lineWidth = 1.2;
      ctx.beginPath();
      ctx.moveTo(footX, by + bh - 10);
      ctx.lineTo(footX, footY);
      ctx.stroke();
      ctx.setLineDash([]);

      // Indikator teks status jika sedang menyentuh garis tapi belum menyeberang
      if (ent.isTouchingLine) {
        ctx.font = '800 10px "Plus Jakarta Sans", sans-serif';
        const touchText = '⚠️ MENYENTUH (BELUM LEWAT)';
        const ttw = ctx.measureText(touchText).width;
        ctx.fillStyle = 'rgba(15, 23, 42, 0.95)';
        ctx.strokeStyle = '#f59e0b';
        ctx.lineWidth = 1.5;
        const tbx = footX - ttw / 2 - 8;
        const tby = footY + 6;
        if (ctx.roundRect) {
          ctx.beginPath();
          ctx.roundRect(tbx, tby, ttw + 16, 18, 5);
          ctx.fill();
          ctx.stroke();
        } else {
          ctx.fillRect(tbx, tby, ttw + 16, 18);
          ctx.strokeRect(tbx, tby, ttw + 16, 18);
        }
        ctx.fillStyle = '#f59e0b';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(touchText, footX, tby + 9);
      }
      ctx.restore();

      // Clean, well-formatted surveillance personnel tag
      let displayLabel = ent.name || ent.label || 'ORANG (Pengunjung)';
      if (isEmployee) {
        if (!displayLabel.toUpperCase().includes('KARYAWAN')) {
          displayLabel = `[KARYAWAN] ${displayLabel.replace(/\[.*?\]\s*/g, '')}`;
        }
      } else if (isVIP) {
        if (!displayLabel.toUpperCase().includes('VIP')) {
          displayLabel = `[VIP] ${displayLabel.replace(/\[.*?\]\s*/g, '')}`;
        }
      } else if (!isBlacklist) {
        if (!displayLabel.toUpperCase().includes('PENGUNJUNG') && !displayLabel.toUpperCase().includes('TAMU')) {
          displayLabel = `[PENGUNJUNG] ${displayLabel.replace(/\[.*?\]\s*/g, '')}`;
        }
      }
      const confStr = ent.confidence ? (String(ent.confidence).includes('%') ? ent.confidence : `${Math.round(ent.confidence)}%`) : '76.0%';
      const fullTagText = `${icon} ${displayLabel}`;

      ctx.font = '800 12px "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif';
      const textW = ctx.measureText(fullTagText).width;
      ctx.font = '800 11px monospace';
      const confW = ctx.measureText(confStr).width;

      const tagH = 26;
      const tagW = Math.max(bw, textW + confW + 36);
      const canvasW = ctx.canvas ? ctx.canvas.width : 640;
      const tagX = Math.max(4, Math.min(canvasW - tagW - 4, bx + (bw - tagW) / 2));
      let tagY = by - tagH - 6;
      if (tagY < 4) tagY = by + bh + 6;

      // Dark translucent cyber tag pill
      ctx.fillStyle = 'rgba(2, 6, 23, 0.94)';
      ctx.strokeStyle = reticleColor;
      ctx.lineWidth = 1.6;
      ctx.shadowBlur = 10;
      ctx.beginPath();
      ctx.roundRect ? ctx.roundRect(tagX, tagY, tagW, tagH, 5) : ctx.rect(tagX, tagY, tagW, tagH);
      ctx.fill();
      ctx.stroke();

      // Beacon Dot
      ctx.shadowBlur = 0;
      ctx.fillStyle = reticleColor;
      ctx.beginPath();
      ctx.arc(tagX + 11, tagY + tagH / 2, 3.8, 0, Math.PI * 2);
      ctx.fill();

      // Label Text
      ctx.fillStyle = '#ffffff';
      ctx.font = '800 11.5px "Plus Jakarta Sans", sans-serif';
      ctx.fillText(fullTagText, tagX + 22, tagY + 17);

      // Confidence Pill
      ctx.fillStyle = reticleColor;
      ctx.font = '800 11px monospace';
      ctx.textAlign = 'right';
      ctx.fillText(confStr, tagX + tagW - 8, tagY + 17);
      ctx.textAlign = 'left';

      // Sub-pill badge (Clothing color & Role title / Click to tag hint)
      let subY = by + bh + 8;
      if (tagY >= by + bh + 4) subY = by - 26;
      let subX = bx;

      let subText = '';
      if (ent.role_title && ent.role_title !== 'Staff' && ent.role_title !== 'Pengunjung') {
        subText = `💼 ${ent.role_title} • 👕 ${ent.clothing?.colorName || 'Baju'}`;
      } else if (ent.customTagged || ent.name) {
        subText = `👕 ${ent.clothing?.colorName || 'Baju'} • ✏️ Edit Nama`;
      } else {
        subText = `👕 ${ent.clothing?.colorName || 'Baju'} • 🏷️ Beri Nama`;
      }

      ctx.font = '800 10px "Plus Jakarta Sans", sans-serif';
      const cW = ctx.measureText(subText).width + 24;
      ctx.fillStyle = 'rgba(15, 23, 42, 0.94)';
      ctx.strokeStyle = reticleColor;
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.roundRect ? ctx.roundRect(subX, subY, cW, 20, 4) : ctx.rect(subX, subY, cW, 20);
      ctx.fill();
      ctx.stroke();

      ctx.fillStyle = ent.clothing?.hex || reticleColor;
      ctx.beginPath();
      ctx.arc(subX + 9, subY + 10, 3.5, 0, Math.PI * 2);
      ctx.fill();

      ctx.fillStyle = '#e2e8f0';
      ctx.fillText(subText, subX + 17, subY + 14);

      ctx.restore();
    }

    // =========================================================================
    // TRIPWIRE PEOPLE COUNTING LINE (CUSTOMIZABLE IN / OUT / TOTAL COUNTER)
    // =========================================================================
    let countingLineFlashUntil = 0;
    let countingLineDraggingHandle = null; // null | 'p1' | 'p2' | 'line'
    let hoveredCountingLineHandle = null; // null | 'p1' | 'p2' | 'line'
    let countingLineDragStart = null;
    let countingLineConfig = null;

    function getCountingLineStorageKey() {
      const camId = (currentAICamera && currentAICamera.id) ? currentAICamera.id : 'default';
      return `loewix_counting_line_${camId}`;
    }

    function getDefaultCountingLineConfig() {
      return {
        enabled: true,
        mode: 'both', // 'in' | 'out' | 'both'
        color: '#00f0ff',
        p1: { x: 0.12, y: 0.80 },
        p2: { x: 0.88, y: 0.46 },
        countIn: 0,
        countOut: 0,
        countTotal: 0
      };
    }

    function loadCountingLineConfig() {
      try {
        const raw = localStorage.getItem(getCountingLineStorageKey());
        if (raw) {
          countingLineConfig = Object.assign(getDefaultCountingLineConfig(), JSON.parse(raw));
        } else {
          countingLineConfig = getDefaultCountingLineConfig();
        }
      } catch (e) {
        countingLineConfig = getDefaultCountingLineConfig();
      }

      // Pastikan titik kontrol A dan B selalu strictly di dalam area live feed CCTV
      if (countingLineConfig) {
        if (!countingLineConfig.p1) countingLineConfig.p1 = { x: 0.10, y: 0.82 };
        if (!countingLineConfig.p2) countingLineConfig.p2 = { x: 0.88, y: 0.44 };
        countingLineConfig.p1.x = Math.max(0.04, Math.min(0.96, countingLineConfig.p1.x));
        countingLineConfig.p1.y = Math.max(0.07, Math.min(0.93, countingLineConfig.p1.y));
        countingLineConfig.p2.x = Math.max(0.04, Math.min(0.96, countingLineConfig.p2.x));
        countingLineConfig.p2.y = Math.max(0.07, Math.min(0.93, countingLineConfig.p2.y));
      }

      updateCountingLineHUD();
      return countingLineConfig;
    }

    function saveCountingLineConfig() {
      if (!countingLineConfig) return;
      try {
        localStorage.setItem(getCountingLineStorageKey(), JSON.stringify(countingLineConfig));
      } catch (e) {}
      updateCountingLineHUD();
    }

    function toggleCountingLineState(enabled) {
      if (!countingLineConfig) loadCountingLineConfig();
      countingLineConfig.enabled = !!enabled;
      saveCountingLineConfig();
      updateCountingLineHUD();
    }

    function setCountingLineMode(mode) {
      if (!countingLineConfig) loadCountingLineConfig();
      countingLineConfig.mode = mode;
      highlightCountingLineModeLabel(mode);
      saveCountingLineConfig();
      updateCountingLineHUD();
    }

    function highlightCountingLineModeLabel(mode) {
      ['in', 'out', 'both'].forEach(m => {
        const lbl = document.getElementById(`lbl-tripwire-mode-${m}`);
        if (!lbl) return;
        if (m === mode) {
          lbl.style.borderColor = (m === 'in' ? '#10b981' : (m === 'out' ? '#ef4444' : '#00f0ff'));
          lbl.style.background = (m === 'in' ? 'rgba(16, 185, 129, 0.2)' : (m === 'out' ? 'rgba(239, 68, 68, 0.2)' : 'rgba(0, 240, 255, 0.2)'));
        } else {
          lbl.style.borderColor = 'rgba(255, 255, 255, 0.12)';
          lbl.style.background = 'rgba(15, 23, 42, 0.5)';
        }
      });
    }

    function setCountingLineColor(color) {
      if (!countingLineConfig) loadCountingLineConfig();
      countingLineConfig.color = color;
      const colorPicker = document.getElementById('tripwire-color-picker');
      if (colorPicker) colorPicker.value = color;
      saveCountingLineConfig();
      updateCountingLineHUD();
    }

    function setCountingLinePresetPos(preset) {
      if (!countingLineConfig) loadCountingLineConfig();
      if (preset === 'bottom') {
        countingLineConfig.p1 = { x: 0.12, y: 0.76 };
        countingLineConfig.p2 = { x: 0.88, y: 0.76 };
      } else if (preset === 'middle') {
        countingLineConfig.p1 = { x: 0.12, y: 0.52 };
        countingLineConfig.p2 = { x: 0.88, y: 0.52 };
      } else if (preset === 'diagonal' || preset === 'lantai' || preset === 'floor') {
        // Mengikuti alur kemiringan dasar lantai CCTV (perspektif showroom)
        countingLineConfig.p1 = { x: 0.10, y: 0.82 };
        countingLineConfig.p2 = { x: 0.88, y: 0.44 };
      }
      saveCountingLineConfig();
      updateCountingLineHUD();
    }

    function resetCountingLineStats() {
      if (!countingLineConfig) loadCountingLineConfig();
      countingLineConfig.countIn = 0;
      countingLineConfig.countOut = 0;
      countingLineConfig.countTotal = 0;
      saveCountingLineConfig();
      updateCountingLineHUD();
    }

    function openCountingLineModal() {
      if (!countingLineConfig) loadCountingLineConfig();

      const toggle = document.getElementById('tripwire-enable-toggle');
      if (toggle) toggle.checked = !!countingLineConfig.enabled;

      const mode = countingLineConfig.mode || 'both';
      const radio = document.querySelector(`input[name="tripwire_mode"][value="${mode}"]`);
      if (radio) radio.checked = true;
      highlightCountingLineModeLabel(mode);

      const colorPicker = document.getElementById('tripwire-color-picker');
      if (colorPicker) colorPicker.value = countingLineConfig.color || '#00f0ff';

      openModal('modalCountingLineSettings');
    }

    function updateCountingLineHUD() {
      const hud = document.getElementById('ai-tripwire-hud');
      if (!hud) return;
      if (!countingLineConfig || !countingLineConfig.enabled) {
        hud.style.display = 'none';
        return;
      }
      hud.style.display = 'flex';
      const lineColor = countingLineConfig.color || '#00f0ff';
      hud.style.borderColor = lineColor;

      const dot = document.getElementById('tripwire-dot');
      if (dot) {
        dot.style.background = lineColor;
        dot.style.boxShadow = `0 0 8px ${lineColor}`;
      }

      const titleEl = document.getElementById('tripwire-hud-title');
      const statIn = document.getElementById('tripwire-stat-in');
      const statOut = document.getElementById('tripwire-stat-out');
      const statTotal = document.getElementById('tripwire-stat-total');

      const valIn = document.getElementById('tripwire-val-in');
      const valOut = document.getElementById('tripwire-val-out');
      const valTotal = document.getElementById('tripwire-val-total');

      if (valIn) valIn.textContent = countingLineConfig.countIn || 0;
      if (valOut) valOut.textContent = countingLineConfig.countOut || 0;
      if (valTotal) valTotal.textContent = countingLineConfig.countTotal || 0;

      const mode = countingLineConfig.mode || 'both';
      if (mode === 'in') {
        if (titleEl) titleEl.textContent = 'GARIS HITUNG: MASUK';
        if (statIn) statIn.style.display = 'inline-flex';
        if (statOut) statOut.style.display = 'none';
        if (statTotal) statTotal.style.display = 'none';
      } else if (mode === 'out') {
        if (titleEl) titleEl.textContent = 'GARIS HITUNG: KELUAR';
        if (statIn) statIn.style.display = 'none';
        if (statOut) statOut.style.display = 'inline-flex';
        if (statTotal) statTotal.style.display = 'none';
      } else {
        if (titleEl) titleEl.textContent = 'GARIS HITUNG: DUA ARAH';
        if (statIn) statIn.style.display = 'inline-flex';
        if (statOut) statOut.style.display = 'inline-flex';
        if (statTotal) statTotal.style.display = 'inline-flex';
      }
    }

    function playTripwireChime(direction) {
      const soundBtn = document.getElementById('btn-toggle-sound');
      if (soundBtn && !soundBtn.classList.contains('active')) return;
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const now = audioCtx.currentTime;

        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();

        if (direction === 'in') {
          osc.frequency.setValueAtTime(587.33, now); // D5
          osc.frequency.exponentialRampToValueAtTime(880.00, now + 0.18); // A5
        } else {
          osc.frequency.setValueAtTime(880.00, now); // A5
          osc.frequency.exponentialRampToValueAtTime(587.33, now + 0.18); // D5
        }

        gain.gain.setValueAtTime(0.20, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);

        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.35);
      } catch (e) {}
    }

    function getLineSegmentIntersection(x1, y1, x2, y2, x3, y3, x4, y4) {
      const denom = (y4 - y3) * (x2 - x1) - (x4 - x3) * (y2 - y1);
      if (Math.abs(denom) < 0.0001) return null;

      const ua = ((x4 - x3) * (y1 - y3) - (y4 - y3) * (x1 - x3)) / denom;
      const ub = ((x2 - x1) * (y1 - y3) - (y2 - y1) * (x1 - x3)) / denom;

      if (ua >= 0 && ua <= 1 && ub >= 0 && ub <= 1) {
        return {
          x: x1 + ua * (x2 - x1),
          y: y1 + ua * (y2 - y1)
        };
      }
      return null;
    }

    function processTripwireCrossings(canvasW, canvasH) {
      if (!countingLineConfig || !countingLineConfig.enabled || activeHumanEntities.length === 0) return;

      const p1X = countingLineConfig.p1.x * canvasW;
      const p1Y = countingLineConfig.p1.y * canvasH;
      const p2X = countingLineConfig.p2.x * canvasW;
      const p2Y = countingLineConfig.p2.y * canvasH;

      const lineDx = p2X - p1X;
      const lineDy = p2Y - p1Y;
      const lineLen = Math.hypot(lineDx, lineDy);
      if (lineLen < 15) return;

      // Vektor normal mengarah ke sisi "IN" (Side B)
      const nx = -lineDy / lineLen;
      const ny = lineDx / lineLen;

      const now = Date.now();

      // Lebar pita dasar lantai (ribbon)
      const ribbonHalfW = 16;

      for (const ent of activeHumanEntities) {
        // Titik telapak kaki dasar lantai
        const footX = ent.x + ent.w * 0.5;
        const footY = ent.y + ent.h;

        // Radius pijakan kaki di lantai (adaptif proporsional tubuh)
        const footRadius = Math.max(16, Math.min(38, (ent.w || 80) * 0.32));

        // Jarak tegak lurus berarah (signed distance) dari titik tengah kaki ke garis
        const signedDist = (footX - p1X) * nx + (footY - p1Y) * ny;

        // Proyeksi sepanjang segmen garis (t = 0 di Titik A, t = 1 di Titik B)
        const projT = ((footX - p1X) * lineDx + (footY - p1Y) * lineDy) / (lineLen * lineLen);

        // Posisi frame sebelumnya untuk mendeteksi pergerakan diskrit antar frame
        const prevFootX = ent.prevFootX !== undefined ? ent.prevFootX : footX;
        const prevFootY = ent.prevFootY !== undefined ? ent.prevFootY : footY;
        const prevSignedDist = ent.prevSignedDist !== undefined ? ent.prevSignedDist : signedDist;

        // Cek apakah trajektori langkah (prev -> curr) memotong garis pita lantai
        const trajIntersect = getLineSegmentIntersection(prevFootX, prevFootY, footX, footY, p1X, p1Y, p2X, p2Y);

        // Tangani oklusi parsial (misal kaki terhalang motor/meja di showroom CCTV):
        // Jika kaki terhalang, bounding box detector terpotong di lutut/pinggang.
        // Di sudut CCTV ke bawah, proyeksi lantai orang berada di [footY - 10, footY + offset]
        const occlusionOffset = Math.min(52, Math.max(0, (ent.h || 120) * 0.24));

        // Cek sebaran kaki kiri & kanan
        const leftFootSigned = ((ent.x + ent.w * 0.22) - p1X) * nx + (footY - p1Y) * ny;
        const rightFootSigned = ((ent.x + ent.w * 0.78) - p1X) * nx + (footY - p1Y) * ny;
        const projFootSigned = (footX - p1X) * nx + ((footY + occlusionOffset) - p1Y) * ny;

        const minBodySigned = Math.min(signedDist, leftFootSigned, rightFootSigned, projFootSigned);
        const maxBodySigned = Math.max(signedDist, leftFootSigned, rightFootSigned, projFootSigned);

        // Ambang batas zona sentuhan pita lantai
        const touchDist = ribbonHalfW + footRadius; // ~32px s/d 54px
        const clearDist = touchDist + Math.max(14, (ent.w || 80) * 0.16); // ~46px s/d 68px

        // Cek apakah orang berada dalam rentang panjang garis (toleransi ujung 12%)
        const isAlongLine = (projT >= -0.12 && projT <= 1.12) || (trajIntersect !== null);

        if (!isAlongLine) {
          ent.isTouchingLine = false;
          ent.prevFootX = footX;
          ent.prevFootY = footY;
          ent.prevSignedDist = signedDist;
          continue;
        }

        // Tentukan posisi sisi saat ini
        let currentSide = 'touching';
        const footprintSpansLine = (minBodySigned <= 0 && maxBodySigned >= 0);
        const trajCrossed = (prevSignedDist * signedDist < 0) || (trajIntersect !== null);

        if (footprintSpansLine || trajCrossed || Math.abs(signedDist) <= touchDist) {
          currentSide = 'touching'; // Sedang menempel / menyentuh pita lantai!
        } else if (signedDist > clearDist) {
          currentSide = 'sideB'; // Bersih tuntas di Sisi B (IN)
        } else if (signedDist < -clearDist) {
          currentSide = 'sideA'; // Bersih tuntas di Sisi A (OUT)
        } else {
          // Zona histeresis antara touchDist dan clearDist: pertahankan sisi sebelumnya
          currentSide = ent.tripwireSide && ent.tripwireSide !== 'touching_init'
            ? ent.tripwireSide
            : (signedDist > 0 ? 'sideB' : 'sideA');
        }

        // Inisialisasi orang yang baru muncul di kamera
        if (!ent.tripwireSide || ent.tripwireSide === 'touching_init') {
          if (currentSide === 'sideA' || currentSide === 'sideB') {
            ent.tripwireSide = currentSide;
            ent.isTouchingLine = false;
          } else {
            ent.tripwireSide = 'touching_init';
            ent.isTouchingLine = true;
          }
          ent.prevFootX = footX;
          ent.prevFootY = footY;
          ent.prevSignedDist = signedDist;
          continue;
        }

        // KASUS 1: ORANG BARU MENYENTUH PITA LANTAI (BELUM TUNTAS LEWAT)
        // Aturan ketat user: "kalo orang baru nyentuh belom ngelewatin jangan ke hitung minimal harus lewat dulu"
        if (currentSide === 'touching') {
          ent.isTouchingLine = true;
          ent.hasTouchedDuringCross = true;
          ent.touchHoldUntil = now + 450; // Visual feedback menyentuh tetap stabil
          ent.prevFootX = footX;
          ent.prevFootY = footY;
          ent.prevSignedDist = signedDist;
          continue; // TIDAK DIHITUNG!
        }

        // Jika timer visual sentuhan masih aktif, tetap tampilkan status menyentuh
        if (now < (ent.touchHoldUntil || 0)) {
          ent.isTouchingLine = true;
        } else {
          ent.isTouchingLine = false;
        }

        // KASUS 2: ORANG SUDAH TUNTAS MENYEBERANG KE SISI SEBERANGNYA
        // Harus berasal dari salah satu sisi dan sekarang sudah bersih di sisi seberangnya
        if (now - (ent.lastCrossTime || 0) > 2400) {
          let crossedDirection = null;

          if (ent.tripwireSide === 'sideA' && currentSide === 'sideB') {
            crossedDirection = 'in'; // sideA -> sideB (MASUK)
          } else if (ent.tripwireSide === 'sideB' && currentSide === 'sideA') {
            crossedDirection = 'out'; // sideB -> sideA (KELUAR)
          }

          if (crossedDirection) {
            let counted = false;
            if (countingLineConfig.mode === 'in') {
              if (crossedDirection === 'in') {
                countingLineConfig.countIn = (countingLineConfig.countIn || 0) + 1;
                countingLineConfig.countTotal = (countingLineConfig.countTotal || 0) + 1;
                counted = true;
              }
            } else if (countingLineConfig.mode === 'out') {
              if (crossedDirection === 'out') {
                countingLineConfig.countOut = (countingLineConfig.countOut || 0) + 1;
                countingLineConfig.countTotal = (countingLineConfig.countTotal || 0) + 1;
                counted = true;
              }
            } else { // 'both'
              if (crossedDirection === 'in') {
                countingLineConfig.countIn = (countingLineConfig.countIn || 0) + 1;
              } else {
                countingLineConfig.countOut = (countingLineConfig.countOut || 0) + 1;
              }
              countingLineConfig.countTotal = (countingLineConfig.countTotal || 0) + 1;
              counted = true;
            }

            if (counted) {
              ent.lastCrossTime = now;
              ent.tripwireSide = currentSide;
              ent.hasTouchedDuringCross = false;
              countingLineFlashUntil = now + 850;
              saveCountingLineConfig();
              updateCountingLineHUD();
              playTripwireChime(crossedDirection);

              const dirLabel = crossedDirection === 'in' ? '🟢 MASUK' : '🔴 KELUAR';
              const personName = ent.name || ent.label || 'Orang';
              const entCat = ent.category || (crossedDirection === 'in' ? 'employee' : 'guest');
              appendRealtimeAILog(`[GARIS HITUNG] ${personName} melintas (${dirLabel}) • Total: ${countingLineConfig.countTotal}`, entCat, 98.0);
              sendVisitorLog({
                label: personName,
                category: entCat,
                camera_id: currentAICamera ? currentAICamera.id : 5001,
                camera_title: currentAICamera ? currentAICamera.title : 'Kamera CCTV',
                direction: crossedDirection === 'in' ? 'masuk' : 'keluar',
                confidence: 98.0,
                person_id: ent.personId || ''
              });
            }
          }
        }

        // Perbarui sisi terkonfirmasi jika berada bersih di salah satu sisi
        if (currentSide === 'sideA' || currentSide === 'sideB') {
          ent.tripwireSide = currentSide;
        }

        ent.prevFootX = footX;
        ent.prevFootY = footY;
        ent.prevSignedDist = signedDist;
      }
    }

    function drawTripwireCountingLine(ctx, canvasW, canvasH) {
      if (!countingLineConfig || !countingLineConfig.enabled) return;

      const p1X = Math.round(countingLineConfig.p1.x * canvasW);
      const p1Y = Math.round(countingLineConfig.p1.y * canvasH);
      const p2X = Math.round(countingLineConfig.p2.x * canvasW);
      const p2Y = Math.round(countingLineConfig.p2.y * canvasH);

      const now = Date.now();
      const isFlashing = now < countingLineFlashUntil;

      // Cek apakah ada orang yang sedang menginjak / menyentuh pita lantai saat ini
      const isAnyTouching = activeHumanEntities.some(e => e.isTouchingLine);

      const baseLineColor = countingLineConfig.color || '#00f0ff';
      const lineColor = isFlashing ? '#ffffff' : (isAnyTouching ? '#f59e0b' : baseLineColor);
      const glowColor = isFlashing ? 'rgba(255, 255, 255, 0.95)' : (isAnyTouching ? 'rgba(245, 158, 11, 0.9)' : hexToRgba(lineColor, 0.85));

      const dx = p2X - p1X;
      const dy = p2Y - p1Y;
      const len = Math.max(1, Math.hypot(dx, dy));
      const nx = -dy / len;
      const ny = dx / len;

      const midX = (p1X + p2X) / 2;
      const midY = (p1Y + p2Y) / 2;
      const mode = countingLineConfig.mode || 'both';

      ctx.save();

      // =======================================================================
      // 1. PERSPECTIVE FLOOR RIBBON (PITA MARKA DASAR LANTAI 3D)
      // Memberikan fisik pita ubin lantai nyata dengan kedalaman perspektif
      // =======================================================================
      const ribbonHalfW = 16; // Lebar total 32px di atas ubin lantai
      const p1LeftX = p1X + nx * ribbonHalfW, p1LeftY = p1Y + ny * ribbonHalfW;
      const p2LeftX = p2X + nx * ribbonHalfW, p2LeftY = p2Y + ny * ribbonHalfW;
      const p2RightX = p2X - nx * ribbonHalfW, p2RightY = p2Y - ny * ribbonHalfW;
      const p1RightX = p1X - nx * ribbonHalfW, p1RightY = p1Y - ny * ribbonHalfW;

      // Pantulan cahaya / bayangan lantai di bawah pita
      ctx.beginPath();
      ctx.moveTo(p1LeftX, p1LeftY + 2);
      ctx.lineTo(p2LeftX, p2LeftY + 2);
      ctx.lineTo(p2RightX, p2RightY + 2);
      ctx.lineTo(p1RightX, p1RightY + 2);
      ctx.closePath();
      ctx.fillStyle = 'rgba(0, 0, 0, 0.35)';
      ctx.fill();

      // Isi poligon pita lantai (translucent floor strip)
      ctx.beginPath();
      ctx.moveTo(p1LeftX, p1LeftY);
      ctx.lineTo(p2LeftX, p2LeftY);
      ctx.lineTo(p2RightX, p2RightY);
      ctx.lineTo(p1RightX, p1RightY);
      ctx.closePath();

      ctx.shadowColor = glowColor;
      ctx.shadowBlur = isFlashing ? 32 : (isAnyTouching ? 26 : 14);
      ctx.fillStyle = isFlashing ? 'rgba(255, 255, 255, 0.45)' : (isAnyTouching ? 'rgba(245, 158, 11, 0.32)' : hexToRgba(lineColor, 0.15));
      ctx.fill();

      // Garis rel tepi lantai (rel Side B / IN dan rel Side A / OUT)
      // Rel Sisi B (IN)
      ctx.beginPath();
      ctx.moveTo(p1LeftX, p1LeftY);
      ctx.lineTo(p2LeftX, p2LeftY);
      ctx.strokeStyle = isFlashing ? '#ffffff' : (mode === 'out' ? hexToRgba(lineColor, 0.4) : '#10b981');
      ctx.lineWidth = 1.8;
      ctx.setLineDash([8, 5]);
      ctx.stroke();
      ctx.setLineDash([]);

      // Rel Sisi A (OUT)
      ctx.beginPath();
      ctx.moveTo(p1RightX, p1RightY);
      ctx.lineTo(p2RightX, p2RightY);
      ctx.strokeStyle = isFlashing ? '#ffffff' : (mode === 'in' ? hexToRgba(lineColor, 0.4) : '#ef4444');
      ctx.lineWidth = 1.8;
      ctx.setLineDash([8, 5]);
      ctx.stroke();
      ctx.setLineDash([]);

      // Garis strip marka diagonal / chevron di sepanjang permukaan lantai pita
      const chevronSpacing = 42;
      const numChevrons = Math.max(1, Math.floor(len / chevronSpacing));

      for (let i = 1; i <= numChevrons; i++) {
        const t = i / (numChevrons + 1);
        const cx = p1X + dx * t;
        const cy = p1Y + dy * t;

        // Garis strip ubin diagonal menyatu dengan tekstur lantai
        ctx.beginPath();
        ctx.moveTo(cx + nx * (ribbonHalfW - 2), cy + ny * (ribbonHalfW - 2));
        ctx.lineTo(cx - nx * (ribbonHalfW - 2), cy - ny * (ribbonHalfW - 2));
        ctx.strokeStyle = hexToRgba(lineColor, 0.35);
        ctx.lineWidth = 1.2;
        ctx.stroke();

        // Panah marka arah flat di atas pita lantai
        if (mode === 'in' || mode === 'both') {
          drawFloorChevron(ctx, cx, cy, nx, ny, '#10b981', ribbonHalfW);
        }
        if (mode === 'out' || mode === 'both') {
          drawFloorChevron(ctx, cx, cy, -nx, -ny, '#ef4444', ribbonHalfW);
        }
      }

      // =======================================================================
      // 2. GARIS SENSOR LASER INTI (CORE LASER BEAM PADA LANTAI)
      // =======================================================================
      ctx.beginPath();
      ctx.moveTo(p1X, p1Y);
      ctx.lineTo(p2X, p2Y);
      ctx.strokeStyle = lineColor;
      ctx.shadowColor = glowColor;
      ctx.shadowBlur = isFlashing ? 28 : (isAnyTouching ? 20 : 12);
      ctx.lineWidth = isFlashing ? 4.5 : 2.5;
      ctx.lineCap = 'round';
      ctx.stroke();

      ctx.beginPath();
      ctx.moveTo(p1X, p1Y);
      ctx.lineTo(p2X, p2Y);
      ctx.strokeStyle = '#ffffff';
      ctx.shadowBlur = 0;
      ctx.lineWidth = 1.0;
      ctx.stroke();

      // =======================================================================
      // 3. EFEK PIJAKAN KAKI MENYENTUH PITA LANTAI (INTERACTIVE GROUND CONTACT)
      // =======================================================================
      for (const ent of activeHumanEntities) {
        if (ent.isTouchingLine) {
          const footX = ent.x + ent.w * 0.5;
          const footY = ent.y + ent.h;

          // Proyeksikan ke garis lantai
          const t = ((footX - p1X) * dx + (footY - p1Y) * dy) / (len * len);
          const clampT = Math.max(0, Math.min(1, t));
          const contactX = p1X + dx * clampT;
          const contactY = p1Y + dy * clampT;

          // Ripple lingkaran sentuh di atas ubin lantai
          ctx.beginPath();
          ctx.ellipse(contactX, contactY, 24, 10, 0, 0, Math.PI * 2);
          ctx.fillStyle = 'rgba(245, 158, 11, 0.42)';
          ctx.fill();
          ctx.strokeStyle = '#f59e0b';
          ctx.shadowColor = '#f59e0b';
          ctx.shadowBlur = 16;
          ctx.lineWidth = 2.2;
          ctx.stroke();

          // Garis penghubung telapak kaki orang ke pita lantai
          ctx.setLineDash([2, 3]);
          ctx.beginPath();
          ctx.moveTo(footX, footY);
          ctx.lineTo(contactX, contactY);
          ctx.strokeStyle = 'rgba(245, 158, 11, 0.85)';
          ctx.lineWidth = 1.5;
          ctx.stroke();
          ctx.setLineDash([]);
        }
      }

      // =======================================================================
      // 4. DYNAMIC MIDPOINT STATUS BADGE
      // =======================================================================
      let badgeText = 'PITA LANTAI HITUNG • DUA ARAH';
      let badgeBg = 'rgba(10, 15, 30, 0.94)';
      let badgeBorder = lineColor;

      if (countingLineDraggingHandle === 'p1') {
        badgeText = '📍 GESER TITIK A (DASAR LANTAI)';
        badgeBorder = '#f59e0b';
      } else if (countingLineDraggingHandle === 'p2') {
        badgeText = '📍 GESER TITIK B (DASAR LANTAI)';
        badgeBorder = '#f59e0b';
      } else if (countingLineDraggingHandle === 'line') {
        badgeText = '↔️ GESER POSISI PITA LANTAI';
        badgeBorder = '#f59e0b';
      } else if (hoveredCountingLineHandle === 'p1') {
        badgeText = '💡 KLIK & GESER TITIK A (LANTAI)';
        badgeBorder = '#00f0ff';
      } else if (hoveredCountingLineHandle === 'p2') {
        badgeText = '💡 KLIK & GESER TITIK B (LANTAI)';
        badgeBorder = '#00f0ff';
      } else if (hoveredCountingLineHandle === 'line') {
        badgeText = '💡 KLIK & GESER SELURUH PITA LANTAI';
        badgeBorder = '#00f0ff';
      } else if (isAnyTouching) {
        badgeText = '⚠️ ORANG DI PITA LANTAI (BELUM LEWAT)';
        badgeBorder = '#f59e0b';
        badgeBg = 'rgba(35, 20, 5, 0.95)';
      } else if (mode === 'in') {
        badgeText = `🟢 GARIS MASUK (${countingLineConfig.countIn || 0})`;
        badgeBorder = '#10b981';
      } else if (mode === 'out') {
        badgeText = `🔴 GARIS KELUAR (${countingLineConfig.countOut || 0})`;
        badgeBorder = '#ef4444';
      } else {
        badgeText = `🔄 DUA ARAH [M: ${countingLineConfig.countIn || 0} | K: ${countingLineConfig.countOut || 0}]`;
      }

      ctx.font = '800 11px "Plus Jakarta Sans", -apple-system, sans-serif';
      const tw = ctx.measureText(badgeText).width;
      const bw = tw + 22;
      const bh = 22;
      const bx = midX - bw / 2;
      const by = midY - bh / 2;

      ctx.fillStyle = badgeBg;
      ctx.strokeStyle = badgeBorder;
      ctx.shadowColor = badgeBorder;
      ctx.shadowBlur = 8;
      ctx.lineWidth = 1.5;
      if (ctx.roundRect) {
        ctx.beginPath();
        ctx.roundRect(bx, by, bw, bh, 6);
        ctx.fill();
        ctx.stroke();
      } else {
        ctx.fillRect(bx, by, bw, bh);
        ctx.strokeRect(bx, by, bw, bh);
      }

      ctx.fillStyle = '#ffffff';
      ctx.shadowBlur = 0;
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(badgeText, midX, midY);

      // =======================================================================
      // 5. DRAGGABLE 3D GROUND ANCHOR PADS AT POINT A AND POINT B
      // =======================================================================
      drawHandlePin(ctx, p1X, p1Y, 'A', lineColor, countingLineDraggingHandle === 'p1', hoveredCountingLineHandle === 'p1');
      drawHandlePin(ctx, p2X, p2Y, 'B', lineColor, countingLineDraggingHandle === 'p2', hoveredCountingLineHandle === 'p2');

      ctx.restore();
    }

    function drawFloorChevron(ctx, cx, cy, dirX, dirY, color, ribbonHalfW) {
      const arm = 9;
      const peakX = cx + dirX * (ribbonHalfW - 3);
      const peakY = cy + dirY * (ribbonHalfW - 3);

      ctx.save();
      ctx.strokeStyle = color;
      ctx.fillStyle = color;
      ctx.shadowColor = color;
      ctx.shadowBlur = 6;
      ctx.lineWidth = 1.8;

      ctx.beginPath();
      ctx.moveTo(peakX, peakY);
      ctx.lineTo(cx - dirY * arm, cy + dirX * arm);
      ctx.lineTo(cx + dirY * arm, cy - dirX * arm);
      ctx.closePath();
      ctx.fill();
      ctx.stroke();
      ctx.restore();
    }

    function drawHandlePin(ctx, x, y, label, color, isDragging, isHovered) {
      ctx.save();
      const r = isDragging ? 15 : (isHovered ? 13 : 11);
      const postH = 22; // Tinggi patok di atas dasar lantai
      const pinHeadY = y - postH;

      // 1. Perspective 3D Ground Base Pad (Tapak Patok Dasar Lantai)
      ctx.beginPath();
      ctx.ellipse(x, y + 2, 22, 9, 0, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(0, 0, 0, 0.55)'; // Bayangan dasar lantai
      ctx.fill();

      // Outer glowing floor base ring pada ubin lantai
      ctx.beginPath();
      ctx.ellipse(x, y, 18, 8, 0, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(10, 15, 30, 0.94)';
      ctx.fill();
      ctx.strokeStyle = color;
      ctx.shadowColor = color;
      ctx.shadowBlur = isDragging ? 22 : (isHovered ? 16 : 9);
      ctx.lineWidth = isDragging || isHovered ? 2.6 : 1.8;
      ctx.stroke();

      // Inner concentric floor radar ring
      ctx.beginPath();
      ctx.ellipse(x, y, 10, 4.5, 0, 0, Math.PI * 2);
      ctx.strokeStyle = isDragging || isHovered ? '#ffffff' : hexToRgba(color, 0.7);
      ctx.lineWidth = 1;
      ctx.stroke();

      // Floor center anchor bolt (+)
      ctx.beginPath();
      ctx.strokeStyle = '#ffffff';
      ctx.lineWidth = 1.2;
      ctx.moveTo(x - 5, y); ctx.lineTo(x + 5, y);
      ctx.moveTo(x, y - 3); ctx.lineTo(x, y + 3);
      ctx.stroke();

      // 2. Vertical Anchor Post dari dasar lantai ke pin head
      ctx.beginPath();
      ctx.moveTo(x, y);
      ctx.lineTo(x, pinHeadY);
      ctx.strokeStyle = color;
      ctx.lineWidth = 2.2;
      ctx.shadowColor = color;
      ctx.shadowBlur = 8;
      ctx.stroke();

      // 3. Draggable Handle Pin Head di atas tiang
      if (isHovered || isDragging) {
        ctx.beginPath();
        ctx.arc(x, pinHeadY, r + 7, 0, Math.PI * 2);
        ctx.strokeStyle = color;
        ctx.shadowColor = color;
        ctx.shadowBlur = 18;
        ctx.lineWidth = 2.2;
        ctx.setLineDash([4, 3]);
        ctx.stroke();
        ctx.setLineDash([]);
      }

      ctx.beginPath();
      ctx.arc(x, pinHeadY, r, 0, Math.PI * 2);
      ctx.fillStyle = color;
      ctx.shadowColor = color;
      ctx.shadowBlur = isDragging ? 24 : (isHovered ? 16 : 10);
      ctx.fill();
      ctx.lineWidth = 2.5;
      ctx.strokeStyle = '#ffffff';
      ctx.stroke();

      ctx.fillStyle = '#000000';
      ctx.shadowBlur = 0;
      ctx.font = '900 11px monospace';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(label, x, pinHeadY);

      // Label di bawah tapak lantai: "DASAR LANTAI (A)"
      ctx.font = '800 9px "Plus Jakarta Sans", sans-serif';
      const floorTag = `DASAR LANTAI (${label})`;
      const ftw = ctx.measureText(floorTag).width;
      ctx.fillStyle = 'rgba(10, 15, 30, 0.88)';
      ctx.strokeStyle = hexToRgba(color, 0.7);
      ctx.lineWidth = 1;
      if (ctx.roundRect) {
        ctx.beginPath();
        ctx.roundRect(x - ftw / 2 - 5, y + 9, ftw + 10, 14, 3);
        ctx.fill();
        ctx.stroke();
      } else {
        ctx.fillRect(x - ftw / 2 - 5, y + 9, ftw + 10, 14);
        ctx.strokeRect(x - ftw / 2 - 5, y + 9, ftw + 10, 14);
      }
      ctx.fillStyle = '#ffffff';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(floorTag, x, y + 16);

      // Micro-tooltip saat hover / drag
      if (isHovered || isDragging) {
        ctx.font = '800 10px "Plus Jakarta Sans", sans-serif';
        const tipText = isDragging ? `PINDAHKAN TITIK ${label}` : `KLIK & GESER (${label})`;
        const tw = ctx.measureText(tipText).width;
        const tx = x;
        const ty = pinHeadY - r - 14;
        ctx.fillStyle = 'rgba(15, 23, 42, 0.95)';
        ctx.strokeStyle = color;
        ctx.lineWidth = 1.2;
        if (ctx.roundRect) {
          ctx.beginPath();
          ctx.roundRect(tx - tw / 2 - 6, ty - 8, tw + 12, 16, 4);
          ctx.fill();
          ctx.stroke();
        } else {
          ctx.fillRect(tx - tw / 2 - 6, ty - 8, tw + 12, 16);
          ctx.strokeRect(tx - tw / 2 - 6, ty - 8, tw + 12, 16);
        }
        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(tipText, tx, ty);
      }

      ctx.restore();
    }

    function hexToRgba(hex, alpha) {
      let c;
      if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
        c = hex.substring(1).split('');
        if (c.length === 3) c = [c[0], c[0], c[1], c[1], c[2], c[2]];
        c = '0x' + c.join('');
        return `rgba(${[(c >> 16) & 255, (c >> 8) & 255, c & 255].join(',')},${alpha})`;
      }
      return `rgba(0,240,255,${alpha})`;
    }

    function isNearPoint(px, py, targetX, targetY, threshold = 22) {
      return Math.hypot(px - targetX, py - targetY) <= threshold;
    }

    function distToSegment(px, py, x1, y1, x2, y2) {
      const l2 = (x2 - x1) * (x2 - x1) + (y2 - y1) * (y2 - y1);
      if (l2 === 0) return Math.hypot(px - x1, py - y1);
      let t = ((px - x1) * (x2 - x1) + (py - y1) * (y2 - y1)) / l2;
      t = Math.max(0, Math.min(1, t));
      return Math.hypot(px - (x1 + t * (x2 - x1)), py - (y1 + t * (y2 - y1)));
    }

    // Expose tripwire counting handlers globally
    window.openCountingLineModal = openCountingLineModal;
    window.toggleCountingLineState = toggleCountingLineState;
    window.setCountingLineMode = setCountingLineMode;
    window.setCountingLineColor = setCountingLineColor;
    window.setCountingLinePresetPos = setCountingLinePresetPos;
    window.resetCountingLineStats = resetCountingLineStats;
    window.saveCountingLineSettings = saveCountingLineConfig;

    // =========================================================================
    // HOLOGRAPHIC AI SCAN LASER WAVE (GAMBAR 2 REPLICA)
    // =========================================================================
    function drawCyberneticScanLaser(ctx, width, height) {
      // Garis laser biru naik-turun dinonaktifkan agar layar CCTV bersih dan tidak terdistraksi
      ctx.save();
      ctx.shadowBlur = 0;
      ctx.font = '800 10.5px monospace';
      const hudTxt = `● AI SURVEILLANCE ACTIVE: ${(currentAICamera && currentAICamera.title ? currentAICamera.title : 'CCTV LIVE').toUpperCase()}`;
      ctx.fillStyle = 'rgba(2, 6, 23, 0.85)';
      const tw = ctx.measureText(hudTxt).width + 18;
      ctx.beginPath();
      ctx.roundRect ? ctx.roundRect(12, 10, tw, 22, 4) : ctx.rect(12, 10, tw, 22);
      ctx.fill();
      ctx.strokeStyle = 'rgba(0, 240, 255, 0.5)';
      ctx.lineWidth = 1;
      ctx.stroke();

      ctx.fillStyle = '#00f0ff';
      ctx.fillText(hudTxt, 20, 25);
      ctx.restore();
    }

    // =========================================================================
    // HIGH-PRECISION HUMAN-ONLY DETECTOR CYCLE (REJECTS VEHICLES & FURNITURE)
    // =========================================================================
    let _aiHumanDetectionCanvas = null;
    function getHumanDetectionFrame(video) {
      if (!video || video.readyState < 2 || video.videoWidth === 0) return null;
      if (!_aiHumanDetectionCanvas) {
        _aiHumanDetectionCanvas = document.createElement('canvas');
      }
      const vw = video.videoWidth;
      const vh = video.videoHeight;
      const maxDim = 640;
      const scale = Math.min(1, maxDim / Math.max(vw, vh));
      const w = Math.round(vw * scale);
      const h = Math.round(vh * scale);
      if (_aiHumanDetectionCanvas.width !== w || _aiHumanDetectionCanvas.height !== h) {
        _aiHumanDetectionCanvas.width = w;
        _aiHumanDetectionCanvas.height = h;
      }
      const ctx = _aiHumanDetectionCanvas.getContext('2d', { willReadFrequently: true });
      try {
        ctx.drawImage(video, 0, 0, w, h);
        return { canvas: _aiHumanDetectionCanvas, scaleX: vw / w, scaleY: vh / h, width: w, height: h };
      } catch (e) {
        return null;
      }
    }

    async function runHumanDetectionCycle(video, canvas) {
      if (!cocoSSDModel || isHumanDetecting) return;
      const now = Date.now();
      if (now - lastHumanDetectTime < 120) return; // ~8-9 FPS inference, 60 FPS rendering
      lastHumanDetectTime = now;
      isHumanDetecting = true;

      try {
        // Sample motion buffer for live living vs static object discrimination
        updateMotionBuffers(video);

        const frameData = getHumanDetectionFrame(video);
        const inputTarget = frameData ? frameData.canvas : video;
        const sourceW = frameData ? frameData.width : video.videoWidth;
        const sourceH = frameData ? frameData.height : video.videoHeight;
        const scaleX = canvas.width / Math.max(1, sourceW);
        const scaleY = canvas.height / Math.max(1, sourceH);

        // Detect all objects (humans, vehicles, furniture)
        const rawPredictions = await cocoSSDModel.detect(inputTarget, 30, 0.20);
        if (!rawPredictions || rawPredictions.length === 0) {
          // Keep active confirmed human tracks locked for 3.2 seconds during brief video/stream hiccups
          activeHumanEntities = activeHumanEntities.filter(e => now - (e.updatedAt || now) < 3200);
          return;
        }

        // 1. Separate into Non-Human Obstacles vs Candidate Persons
        const obstacleBoxes = [];
        const candidatePersons = [];

        const obstacleClasses = [
          'motorcycle', 'car', 'truck', 'bus', 'bicycle',
          'chair', 'couch', 'dining table', 'bench', 'traffic light',
          'parking meter', 'stop sign', 'backpack', 'suitcase'
        ];

        for (const p of rawPredictions) {
          const bx = Math.max(0, p.bbox[0]);
          const by = Math.max(0, p.bbox[1]);
          const bw = Math.min(sourceW - bx, p.bbox[2]);
          const bh = Math.min(sourceH - by, p.bbox[3]);
          if (bw < 7 || bh < 14) continue;

          // Sensitive obstacle registration (>= 0.05 captures all parked motorcycles, scooters, cars, & furniture)
          const isVehicleClass = ['motorcycle', 'car', 'truck', 'bus', 'bicycle'].includes(p.class);
          const minObsScore = isVehicleClass ? 0.05 : 0.08;
          if (obstacleClasses.includes(p.class) && p.score >= minObsScore) {
            obstacleBoxes.push({
              x: bx, y: by, w: bw, h: bh,
              x2: bx + bw, y2: by + bh,
              area: bw * bh,
              score: p.score,
              class: p.class
            });
          } else if (p.class === 'person' && p.score >= 0.40) {
            candidatePersons.push({
              x: bx, y: by, w: bw, h: bh,
              x2: bx + bw, y2: by + bh,
              area: bw * bh,
              score: p.score,
              rawBbox: [bx, by, bw, bh]
            });
          }
        }

        // 2. Strict Human Biometric, Geometric & Anti-Vehicle Validation
        const filteredPersons = [];
        const _debugFilterNow = Date.now();
        const _doDebug = (!window._lastFilterDebug || _debugFilterNow - window._lastFilterDebug > 2000);
        if (_doDebug) window._lastFilterDebug = _debugFilterNow;
        if (_doDebug && candidatePersons.length > 0) {
          console.log(`🔍 [AI Filter] ${candidatePersons.length} person candidate(s), ${obstacleBoxes.length} obstacle(s) from COCO-SSD`);
        }

        for (const cp of candidatePersons) {
          let boxX = cp.x;
          let boxY = cp.y;
          let boxW = cp.w;
          let boxH = cp.h;
          const cx = boxX + boxW * 0.5;
          const cy = boxY + boxH * 0.5;
          const aspect = boxH / Math.max(1, boxW);

          // A. Ceiling Clutter & Extreme Top Filter
          if (boxY < sourceH * 0.005 || cy < sourceH * 0.03) {
            continue;
          }

          // B. Universal Aspect Ratio: Allow seated humans (~0.38-0.70) and standing/walking humans (~1.0-4.8)
          if (aspect < 0.38 || aspect > 5.0) {
            if (_doDebug) console.log(`🚫 [Filter Aspect] Box aspect: ${aspect.toFixed(2)} outside 0.38-5.0`);
            continue;
          }

          // C. Dimension Constraints: Filter out tiny sub-pixel artifacts or full-frame errors
          if (boxH < 14 || boxW < 7) {
            continue;
          }
          if (boxW > sourceW * 0.75 || boxH > sourceH * 0.95) {
            continue;
          }

          // D. Motion & Biometric Signal Analysis
          const motionDelta = getBoxMotionDelta(boxX, boxY, boxW, boxH, sourceW, sourceH);
          const hasBio = hasHumanBiometricSignals(inputTarget, boxX, boxY, boxW, boxH, motionDelta);

          // E. Zone Definitions for Showroom & Commercial Environments (Yamaha DDS Only)
          const isYamahaDDS = Boolean(currentAICamera && (
            String(currentAICamera.title || '').toLowerCase().includes('yamaha') || 
            String(currentAICamera.city || '').toLowerCase() === 'siantar' ||
            String(currentAICamera.id) === '5001'
          ));
          const isCustomerSeatingZone = isYamahaDDS && (cx > sourceW * 0.12 && cx < sourceW * 0.50 && cy > sourceH * 0.14 && cy < sourceH * 0.52);
          const isReceptionDeskZone = isYamahaDDS && (cx > sourceW * 0.40 && cx < sourceW * 0.65 && cy > sourceH * 0.10 && cy < sourceH * 0.32);
          const isElevatedScooterStage = isYamahaDDS && (cx > sourceW * 0.54 && cx < sourceW * 0.90 && cy >= sourceH * 0.12 && cy < sourceH * 0.40);
          const isCenterPaddockZone = isYamahaDDS && (cx > sourceW * 0.30 && cx < sourceW * 0.54 && cy > sourceH * 0.36 && cy < sourceH * 0.68);
          const isApparelDisplayZone = isYamahaDDS && (cx > sourceW * 0.76 && cy < sourceH * 0.34);
          const isFloorParkedMotorcycle = isYamahaDDS && (
            (cx < sourceW * 0.28 && cy > sourceH * 0.10 && cy < sourceH * 0.60) ||
            (cx > sourceW * 0.05 && cx < sourceW * 0.32 && cy > sourceH * 0.52 && cy < sourceH * 0.88) ||
            (cx > sourceW * 0.18 && cx < sourceW * 0.48 && cy > sourceH * 0.72) ||
            (cx > sourceW * 0.82 && cy > sourceH * 0.42)
          );

          // F. Specific filters for Camera L8 (Jakarta Office): eliminate beige wall divider on the left & static doorway
          const isCameraL8 = Boolean(currentAICamera && (
            String(currentAICamera.id) === '5032' ||
            String(currentAICamera.title || '').toUpperCase().includes('L8')
          ));
          if (isCameraL8) {
            // Left beige partition wall / divider: eliminate static false positive on wall/chair
            if (cx < sourceW * 0.20 && cy < sourceH * 0.72 && motionDelta < 0.40 && !hasBio) {
              if (_doDebug) console.log(`🚫 [Filter L8] Left wall partition blocked: motion=${motionDelta.toFixed(2)} bio=${hasBio}`);
              continue;
            }
            // Doorway background zone (dark door opening in top center)
            if (cx > sourceW * 0.45 && cx < sourceW * 0.65 && cy < sourceH * 0.42 && motionDelta < 0.40 && !hasBio) {
              if (_doDebug) console.log(`🚫 [Filter L8] Doorway background blocked: motion=${motionDelta.toFixed(2)} bio=${hasBio}`);
              continue;
            }
          }

          // G. Vehicle Obstacle Overlap Check (Prevents parked vehicles/motorcycles from being tagged as humans)
          let isBlocked = false;
          for (const obs of obstacleBoxes) {
            const interW = Math.max(0, Math.min(boxX + boxW, obs.x2) - Math.max(boxX, obs.x));
            const interH = Math.max(0, Math.min(boxY + boxH, obs.y2) - Math.max(boxY, obs.y));
            const interArea = interW * interH;
            if (interArea > 0) {
              const overlapOnPerson = interArea / (boxW * boxH);
              const overlapOnObs = interArea / obs.area;

              if (['motorcycle', 'bicycle', 'car', 'truck', 'bus'].includes(obs.class)) {
                if ((overlapOnPerson > 0.20 || overlapOnObs > 0.20) && motionDelta < 0.35 && !hasBio) {
                  if (_doDebug) console.log(`🚫 [Filter Obstacle] Vehicle overlap: ${(overlapOnPerson*100).toFixed(0)}% on ${obs.class}`);
                  isBlocked = true;
                  break;
                }
                if (overlapOnPerson > 0.55 && (!hasBio || cp.score < 0.85)) {
                  if (_doDebug) console.log(`🚫 [Filter Obstacle] Heavy vehicle overlap: ${(overlapOnPerson*100).toFixed(0)}%`);
                  isBlocked = true;
                  break;
                }
              }
            }
          }
          if (isBlocked) continue;

          // G. Showroom Display Stage & Center Paddock Platform Filters
          if (isElevatedScooterStage && motionDelta < 0.32 && !hasBio && cp.score < 0.82) {
            if (_doDebug) console.log(`🚫 [Filter Stage] Parked scooter on stage blocked: motion=${motionDelta.toFixed(2)} bio=${hasBio}`);
            continue;
          }
          if (isCenterPaddockZone && motionDelta < 0.32 && !hasBio && cp.score < 0.82) {
            if (_doDebug) console.log(`🚫 [Filter Paddock] Parked bike on paddock stand blocked: motion=${motionDelta.toFixed(2)} bio=${hasBio}`);
            continue;
          }
          if (isApparelDisplayZone && motionDelta < 0.28 && !hasBio && cp.score < 0.82) {
            if (_doDebug) console.log(`🚫 [Filter Apparel] Static clothes on rack blocked: motion=${motionDelta.toFixed(2)} bio=${hasBio}`);
            continue;
          }
          if (isFloorParkedMotorcycle && motionDelta < 0.25 && !hasBio && cp.score < 0.80) {
            if (_doDebug) console.log(`🚫 [Filter Floor Bike] Parked motorcycle blocked: motion=${motionDelta.toFixed(2)} bio=${hasBio}`);
            continue;
          }

          // H. Universal Static Non-Living Filter (Eliminates coats on chairs, doorposts, wall shadows, empty furniture)
          // Any static object without genuine human motion and without skin biometrics must have a very high score (>= 0.78)
          if (!isCustomerSeatingZone && !isReceptionDeskZone) {
            if (motionDelta < 0.30 && !hasBio && cp.score < 0.78) {
              if (_doDebug) console.log(`🚫 [Filter Universal] Static non-living object blocked (coat/wall/door): motion=${motionDelta.toFixed(2)} bio=${hasBio} score=${cp.score.toFixed(2)}`);
              continue;
            }
          }

          filteredPersons.push({
            x: boxX,
            y: boxY,
            w: boxW,
            h: boxH,
            score: cp.score,
            motionDelta: motionDelta,
            hasBio: hasBio
          });
        }

        // 3. Strict IoU & Proximity Non-Maximum Suppression (NMS) - Eliminates duplicate or stacked boxes
        const nmsPersons = [];
        filteredPersons.sort((a, b) => b.score - a.score);
        for (const b of filteredPersons) {
          let keep = true;
          for (const r of nmsPersons) {
            const interW = Math.max(0, Math.min(b.x + b.w, r.x + r.w) - Math.max(b.x, r.x));
            const interH = Math.max(0, Math.min(b.y + b.h, r.y + r.h) - Math.max(b.y, r.y));
            const interArea = interW * interH;
            if (interArea > 0) {
              const iou = interArea / (b.w * b.h + r.w * r.h - interArea);
              if (iou > 0.40) {
                keep = false;
                break;
              }
              const minArea = Math.min(b.w * b.h, r.w * r.h);
              if (interArea / minArea > 0.60) {
                keep = false;
                break;
              }
            }
            // Vertical column alignment test: merge / suppress head + torso double detection on the same person
            const bMidX = b.x + b.w * 0.5;
            const rMidX = r.x + r.w * 0.5;
            const minW = Math.min(b.w, r.w);
            if (Math.abs(bMidX - rMidX) < minW * 0.48) {
              const vertOverlap = Math.max(0, Math.min(b.y + b.h, r.y + r.h) - Math.max(b.y, r.y));
              const minH = Math.min(b.h, r.h);
              const vertGap = Math.max(0, Math.max(b.y, r.y) - Math.min(b.y + b.h, r.y + r.h));
              // Either overlapping vertically or vertically adjacent within 45px (head above torso)
              if (vertOverlap / minH > 0.20 || vertGap < 45) {
                // Merge into single unified bounding box encompassing the entire person
                r.y = Math.min(r.y, b.y);
                r.h = Math.max(r.y + r.h, b.y + b.h) - r.y;
                r.x = Math.min(r.x, b.x);
                r.w = Math.max(r.x + r.w, b.x + b.w) - r.x;
                r.score = Math.max(r.score, b.score);
                r.hasBio = r.hasBio || b.hasBio;
                r.motionDelta = Math.max(r.motionDelta || 0, b.motionDelta || 0);
                keep = false;
                break;
              }
            }
          }
          if (keep) nmsPersons.push(b);
        }

        // 4. Scale back to canvas display coordinates
        const scaledDetections = [];

        for (const vp of nmsPersons) {
          const bx = Math.round(vp.x * scaleX);
          const by = Math.round(vp.y * scaleY);
          const bw = Math.round(vp.w * scaleX);
          const bh = Math.round(vp.h * scaleY);

          // Extract clothing profile from human chest/torso
          const clothing = extractPedestrianClothingProfile(video, vp.x * (frameData ? frameData.scaleX : 1), vp.y * (frameData ? frameData.scaleY : 1), vp.w * (frameData ? frameData.scaleX : 1), vp.h * (frameData ? frameData.scaleY : 1));

          // Calibrated enterprise confidence: accurately reflect real detection scores
          const calibConf = (Math.min(98.5, Math.max(50.0, (vp.score * 100)))).toFixed(1);

          scaledDetections.push({
            targetX: bx,
            targetY: by,
            targetW: bw,
            targetH: bh,
            confidence: calibConf + '%',
            rawScore: vp.score,
            motionDelta: vp.motionDelta,
            hasBio: vp.hasBio,
            label: 'ORANG (Pengunjung)',
            clothing: clothing
          });
        }

        // 5. Robust Multi-Track Inertial Smoother with Velocity Prediction & Anti-Flicker Retention
        const matchedIndices = new Set();

        for (const target of scaledDetections) {
          let bestEntity = null;
          let bestIdx = -1;
          let bestDist = Math.max(180, canvas.width * 0.30);

          for (let i = 0; i < activeHumanEntities.length; i++) {
            if (matchedIndices.has(i)) continue;
            const prev = activeHumanEntities[i];
            const dist = Math.hypot(target.targetX - (prev.x || target.targetX), target.targetY - (prev.y || target.targetY));
            if (dist < bestDist) {
              bestDist = dist;
              bestEntity = prev;
              bestIdx = i;
            }
          }

          if (bestEntity) {
            matchedIndices.add(bestIdx);

            // Compute dynamic velocity (vx, vy) for smooth inter-frame inertial extrapolation
            const newVx = (target.targetX - bestEntity.x) * 0.35;
            const newVy = (target.targetY - bestEntity.y) * 0.35;
            bestEntity.vx = (bestEntity.vx !== undefined) ? (bestEntity.vx * 0.65 + newVx * 0.35) : newVx;
            bestEntity.vy = (bestEntity.vy !== undefined) ? (bestEntity.vy * 0.65 + newVy * 0.35) : newVy;

            // Smooth lerp (responsive yet jitter-free)
            const factor = 0.50;
            bestEntity.x = Math.round(bestEntity.x + (target.targetX - bestEntity.x) * factor);
            bestEntity.y = Math.round(bestEntity.y + (target.targetY - bestEntity.y) * factor);
            bestEntity.w = Math.round(bestEntity.w + (target.targetW - bestEntity.w) * factor);
            bestEntity.h = Math.round(bestEntity.h + (target.targetH - bestEntity.h) * factor);
            bestEntity.targetX = target.targetX;
            bestEntity.targetY = target.targetY;
            bestEntity.targetW = target.targetW;
            bestEntity.targetH = target.targetH;
            bestEntity.confidence = target.confidence;
            bestEntity.rawScore = target.rawScore;
            
            // Auto-reconnect with persistent personnel tag if not already tagged
            if (!bestEntity.customTagged && !bestEntity.isIdentified) {
              for (const p of persistentTaggedPersonnel) {
                // Aturan 1: Jangan pernah beri nama jika nama ini sudah aktif dipakai orang lain di layar!
                const isAlreadyActive = activeHumanEntities.some(e => e !== bestEntity && (e.name || '').trim().toLowerCase() === (p.name || '').trim().toLowerCase());
                if (isAlreadyActive) continue;

                // Aturan 2: Reconnect HANYA jika target memiliki tanda kehidupan (bergerak/ada kulit wajah/skor tinggi)
                // DILARANG KERAS menempelkan nama karyawan ke jaket di kursi / tembok kosong!
                const hasLivingSign = ((target.motionDelta && target.motionDelta >= 0.18) || target.hasBio || (target.rawScore >= 0.78));
                if (!hasLivingSign) continue;

                const dist = Math.hypot(bestEntity.x - p.lastX, bestEntity.y - p.lastY);
                const maxDist = Math.max(120, canvas.width * 0.14);
                // Maksimal 60 detik sejak orang terakhir terlihat (bukan 4 jam)
                if (now - p.lastSeen < 60000 && dist < maxDist) {
                  bestEntity.name = p.name;
                  bestEntity.category = p.category;
                  bestEntity.role_title = p.role_title;
                  bestEntity.isVIP = p.isVIP;
                  bestEntity.isEmployee = p.isEmployee;
                  bestEntity.isBlacklist = p.isBlacklist;
                  bestEntity.customTagged = true;
                  bestEntity.isIdentified = true;
                  const rolePrefix = p.isVIP ? 'VIP' : (p.isEmployee ? 'KARYAWAN' : (p.isBlacklist ? 'BLACKLIST' : 'PENGUNJUNG'));
                  bestEntity.label = `[${rolePrefix}] ${p.name}`;
                  p.lastX = bestEntity.x;
                  p.lastY = bestEntity.y;
                  p.lastSeen = now;
                  break;
                }
              }
              if (!bestEntity.customTagged) {
                bestEntity.label = target.label;
              }
            } else if (bestEntity.customTagged) {
              // Update last known location in registry
              const p = persistentTaggedPersonnel.find(x => x.name.toLowerCase() === (bestEntity.name || '').toLowerCase());
              if (p) {
                p.lastX = bestEntity.x;
                p.lastY = bestEntity.y;
                p.lastSeen = now;
              }
            }

            if (target.clothing && target.clothing.colorName !== 'Netral') {
              bestEntity.clothing = target.clothing;
            }
            bestEntity.updatedAt = now;
            bestEntity.missedFrames = 0;
          } else {
            // New verified human entity
            const newEntId = 'ent_' + (++_humanEntityIdCounter);
            const newEnt = {
              id: newEntId,
              x: target.targetX,
              y: target.targetY,
              w: target.targetW,
              h: target.targetH,
              targetX: target.targetX,
              targetY: target.targetY,
              targetW: target.targetW,
              targetH: target.targetH,
              confidence: target.confidence,
              rawScore: target.rawScore,
              label: target.label,
              name: '',
              category: 'guest',
              role_title: 'Pengunjung',
              isVIP: false,
              isEmployee: false,
              customTagged: false,
              isIdentified: false,
              clothing: target.clothing,
              vx: 0,
              vy: 0,
              updatedAt: now,
              createdAt: now,
              missedFrames: 0
            };

            // Check if matches known tagged person in this camera session (continuity recovery)
            for (const p of persistentTaggedPersonnel) {
              // Aturan 1: Jangan pernah beri nama jika nama ini sudah aktif dipakai orang lain di layar!
              const isAlreadyActive = activeHumanEntities.some(e => (e.name || '').trim().toLowerCase() === (p.name || '').trim().toLowerCase());
              if (isAlreadyActive) continue;

              // Aturan 2: Reconnect HANYA jika target memiliki tanda kehidupan (bergerak/ada kulit wajah/skor tinggi)
              const hasLivingSign = ((target.motionDelta && target.motionDelta >= 0.18) || target.hasBio || (target.rawScore >= 0.78));
              if (!hasLivingSign) continue;

              const dist = Math.hypot(target.targetX - p.lastX, target.targetY - p.lastY);
              const maxDist = Math.max(120, canvas.width * 0.14);
              if (now - p.lastSeen < 60000 && dist < maxDist) {
                newEnt.name = p.name;
                newEnt.category = p.category;
                newEnt.role_title = p.role_title;
                newEnt.isVIP = p.isVIP;
                newEnt.isEmployee = p.isEmployee;
                newEnt.isBlacklist = p.isBlacklist;
                newEnt.customTagged = true;
                newEnt.isIdentified = true;
                const rolePrefix = p.isVIP ? 'VIP' : (p.isEmployee ? 'KARYAWAN' : (p.isBlacklist ? 'BLACKLIST' : 'PENGUNJUNG'));
                newEnt.label = `[${rolePrefix}] ${p.name}`;
                p.lastX = target.targetX;
                p.lastY = target.targetY;
                p.lastSeen = now;
                break;
              }
            }

            activeHumanEntities.push(newEnt);
          }

          // Rate-limited logging: only log verified pedestrian once every 15s
          const logKey = 'ped_' + target.clothing.colorName;
          const lastLog = lastLoggedPersonTime[logKey] || 0;
          if (now - lastLog > 15000) {
            lastLoggedPersonTime[logKey] = now;
            appendRealtimeAILog(`ORANG (Baju: ${target.clothing.colorName})`, 'guest', parseFloat(target.confidence));
          }
        }

        // CONTINUOUS TRACK RETENTION & VELOCITY-BASED PREDICTION:
        // Hold the track firmly so scanner reticles don't flicker or disappear when someone is walking or momentarily missed
        activeHumanEntities = activeHumanEntities.filter((ent, idx) => {
          if (!matchedIndices.has(idx)) {
            // Protect entity currently being tagged in modal from being pruned!
            if (selectedEntityForTagging && (selectedEntityForTagging === ent || selectedEntityForTagging.id === ent.id)) {
              ent.updatedAt = now;
              ent.missedFrames = 0;
              return true;
            }

            ent.missedFrames = (ent.missedFrames || 0) + 1;
            // Apply velocity prediction during missed frames for smooth movement without stuttering
            if (ent.vx && Math.abs(ent.vx) > 0.4) {
              ent.x += Math.round(ent.vx);
              ent.targetX = ent.x;
              ent.vx *= 0.88; // Dampen velocity
            }
            if (ent.vy && Math.abs(ent.vy) > 0.4) {
              ent.y += Math.round(ent.vy);
              ent.targetY = ent.y;
              ent.vy *= 0.88; // Dampen velocity
            }
            const timeSinceSeen = now - (ent.updatedAt || now);

            // Check if entity is in a vehicle/apparel display zone (ONLY ON YAMAHA DDS SHOWROOM!)
            const isYamahaDDS = Boolean(currentAICamera && (
              String(currentAICamera.title || '').toLowerCase().includes('yamaha') || 
              String(currentAICamera.city || '').toLowerCase() === 'siantar' ||
              String(currentAICamera.id) === '5001'
            ));

            const entNormX = (ent.x + ent.w * 0.5) / Math.max(1, canvas.width);
            const entNormY = (ent.y + ent.h * 0.5) / Math.max(1, canvas.height);
            const isInDisplayZone = isYamahaDDS && (
              (entNormX > 0.54 && entNormX < 0.90 && entNormY >= 0.12 && entNormY < 0.40) || // Stage scooter
              (entNormX > 0.76 && entNormY < 0.34) || // Apparel rack
              (entNormX > 0.30 && entNormX < 0.54 && entNormY > 0.36 && entNormY < 0.68) || // Paddock bike
              (entNormX < 0.28 && entNormY > 0.10 && entNormY < 0.60) || // Left bikes
              (entNormX > 0.05 && entNormX < 0.32 && entNormY > 0.52 && entNormY < 0.88) || // Bottom left bike
              (entNormX > 0.18 && entNormX < 0.48 && entNormY > 0.72) || // Bottom bike
              (entNormX > 0.82 && entNormY > 0.42) // Right scooters
            );

            // If in display zone and not currently detected, drop immediately (max 300ms)
            const maxHoldMs = isInDisplayZone ? 300 : ((ent.customTagged || ent.isIdentified) ? 3600000 : 8000);
            return (timeSinceSeen < maxHoldMs);
          }
          return true;
        });
      } catch (errDet) {
        // Log detection errors for debugging (rate-limited to once per 5 seconds)
        const now2 = Date.now();
        if (!window._lastDetErrLog || now2 - window._lastDetErrLog > 5000) {
          window._lastDetErrLog = now2;
          console.error('❌ [AI Vision] Detection cycle error:', errDet.message || errDet);
        }
      } finally {
        isHumanDetecting = false;
      }
    }

    // =========================================================================
    // CCTV BIOMETRIC FACE RECOGNITION (FACE-API ON ACTIVE DETECTED HUMANS)
    // =========================================================================
    let _lastCCTVFaceScanTime = 0;
    let _isCCTVFaceScanning = false;
    let _cctvFaceCanvas = null;
    let _cctvFaceCtx = null;

    async function runCCTVBiometricFaceScan(video, canvas) {
      if (!faceAPIReady || _isCCTVFaceScanning || !faceAPIFaceMatcher || activeHumanEntities.length === 0) return;
      const now = Date.now();
      if (now - _lastCCTVFaceScanTime < 350) return; // Keep 60 FPS smooth
      _lastCCTVFaceScanTime = now;
      _isCCTVFaceScanning = true;

      try {
        if (!_cctvFaceCanvas) {
          _cctvFaceCanvas = document.createElement('canvas');
          _cctvFaceCanvas.width = 160;
          _cctvFaceCanvas.height = 160;
          _cctvFaceCtx = _cctvFaceCanvas.getContext('2d', { willReadFrequently: true });
        }

        const vw = video.videoWidth;
        const vh = video.videoHeight;
        if (vw === 0 || vh === 0) return;

        const scaleX = vw / canvas.width;
        const scaleY = vh / canvas.height;

        for (const ent of activeHumanEntities) {
          // If already custom tagged by user, preserve user input
          if (ent.customTagged) continue;

          // Never run face scan on static objects in vehicle display or apparel zones (ONLY ON YAMAHA DDS SHOWROOM)
          const isYamahaDDS = Boolean(currentAICamera && (
            String(currentAICamera.title || '').toLowerCase().includes('yamaha') || 
            String(currentAICamera.city || '').toLowerCase() === 'siantar' ||
            String(currentAICamera.id) === '5001'
          ));
          const entNormX = (ent.x + ent.w * 0.5) / Math.max(1, canvas.width);
          const entNormY = (ent.y + ent.h * 0.5) / Math.max(1, canvas.height);
          const isInDisplayZone = isYamahaDDS && (
            (entNormX > 0.54 && entNormX < 0.90 && entNormY >= 0.12 && entNormY < 0.40) ||
            (entNormX > 0.76 && entNormY < 0.34) ||
            (entNormX > 0.30 && entNormX < 0.54 && entNormY > 0.36 && entNormY < 0.68) ||
            (entNormX < 0.28 && entNormY > 0.10 && entNormY < 0.60) ||
            (entNormX > 0.05 && entNormX < 0.32 && entNormY > 0.52 && entNormY < 0.88) ||
            (entNormX > 0.18 && entNormX < 0.48 && entNormY > 0.72) ||
            (entNormX > 0.82 && entNormY > 0.42)
          );
          if (isInDisplayZone && (!ent.vx || Math.hypot(ent.vx, ent.vy) < 0.5)) {
            continue;
          }

          // Head / upper torso crop: top 40% of the bounding box
          const headX = Math.max(0, Math.round(ent.x * scaleX));
          const headY = Math.max(0, Math.round(ent.y * scaleY));
          const headW = Math.max(20, Math.min(vw - headX, Math.round(ent.w * scaleX)));
          const headH = Math.max(20, Math.min(vh - headY, Math.round(ent.h * 0.42 * scaleY)));

          _cctvFaceCtx.clearRect(0, 0, 160, 160);
          _cctvFaceCtx.drawImage(video, headX, headY, headW, headH, 0, 0, 160, 160);

          try {
            const detection = await faceapi.detectSingleFace(_cctvFaceCanvas, new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.45 })).withFaceLandmarks(true).withFaceDescriptor();

            if (detection && detection.descriptor) {
              const match = faceAPIFaceMatcher.findBestMatch(detection.descriptor);
              // High precision face matching: distance < 0.46 (strict) to prevent false identities on clothing folds
              if (match && match.label !== 'unknown' && match.distance < 0.46) {
                const matchedFace = cachedAIFaces.find(f => f.name.toLowerCase() === match.label.toLowerCase());
                if (matchedFace) {
                  // ATURAN INTEGRITAS: Jangan pernah beri nama jika nama ini sudah aktif dipakai orang lain di layar!
                  const isAlreadyClaimed = activeHumanEntities.some(e => e !== ent && (e.name || '').toLowerCase() === matchedFace.name.toLowerCase());
                  if (isAlreadyClaimed) continue;

                  // Double-confirmation voting to avoid single-frame spurious identification
                  ent._matchCandidate = ent._matchCandidate || '';
                  if (ent._matchCandidate === matchedFace.name) {
                    ent._matchVotes = (ent._matchVotes || 0) + 1;
                  } else {
                    ent._matchCandidate = matchedFace.name;
                    ent._matchVotes = 1;
                  }

                  // Require 2 consecutive frames or high confidence (distance < 0.40) to lock in identity
                  if (match.distance > 0.40 && ent._matchVotes < 2) continue;

                  const cat = matchedFace.category || 'employee';
                  ent.name = matchedFace.name;
                  ent.category = cat;
                  ent.role_title = matchedFace.role_title || matchedFace.role || 'Staff';
                  ent.isVIP = (cat === 'vip');
                  ent.isEmployee = (cat === 'employee');
                  ent.isIdentified = true;
                  ent.confidence = Math.round((1 - match.distance) * 100) + '%';
                  ent.label = `[${ent.isVIP ? 'VIP' : (ent.isEmployee ? 'KARYAWAN' : 'PENGUNJUNG')}] ${matchedFace.name}`;

                  const lastLog = lastLoggedPersonTime[match.label] || 0;
                  if (now - lastLog > 12000) {
                    lastLoggedPersonTime[match.label] = now;
                    appendRealtimeAILog(ent.label, cat, parseFloat(ent.confidence));
                    sendVisitorLog({
                      label: matchedFace.name,
                      category: cat,
                      camera_id: currentAICamera ? currentAICamera.id : 5001,
                      camera_title: currentAICamera ? currentAICamera.title : 'Kamera CCTV',
                      direction: 'melintas',
                      confidence: parseFloat(ent.confidence),
                      person_id: matchedFace.id || '',
                      snapshot: _cctvFaceCanvas.toDataURL('image/jpeg', 0.8),
                      descriptor: detection.descriptor ? Array.from(detection.descriptor) : null
                    });
                  }
                }
              } else if (detection.descriptor) {
                // Stranger tracking via 128D Face Embedding
                const strangerBucket = 'str_cctv_' + Math.round(detection.descriptor[0] * 50);
                const lastStrangerLog = lastLoggedPersonTime[strangerBucket] || 0;
                if (now - lastStrangerLog > 20000) {
                  lastLoggedPersonTime[strangerBucket] = now;
                  sendVisitorLog({
                    label: 'Stranger',
                    category: 'stranger',
                    camera_id: currentAICamera ? currentAICamera.id : 5001,
                    camera_title: currentAICamera ? currentAICamera.title : 'Kamera CCTV',
                    direction: 'melintas',
                    confidence: 85.0,
                    snapshot: _cctvFaceCanvas.toDataURL('image/jpeg', 0.8),
                    descriptor: Array.from(detection.descriptor)
                  });
                }
              }
            }
          } catch (eDet) {}
        }
      } catch (e) {
      } finally {
        _isCCTVFaceScanning = false;
      }
    }

    // =========================================================================
    // INTERACTIVE LIVE PERSONNEL NAMING & TAGGING (CANVAS CLICK & QUICK MODAL)
    // =========================================================================
    let hoveredEntity = null;
    let selectedEntityForTagging = null;

    function isPointInEntityReticle(px, py, ent, canvasW) {
      if (!ent) return false;
      const tagW = Math.max(ent.w, 180);
      const tagX = Math.max(4, Math.min(canvasW - tagW - 4, ent.x + (ent.w - tagW) / 2));
      const tagY = ent.y - 34;
      const subY = ent.y + ent.h + 4;
      const subW = Math.max(ent.w, 170);

      // 1. Tag pill above
      if (px >= tagX - 12 && px <= tagX + tagW + 12 && py >= tagY - 10 && py <= tagY + 36) {
        return true;
      }
      // 2. Bounding box body
      if (px >= ent.x - 20 && px <= ent.x + ent.w + 20 && py >= ent.y - 15 && py <= ent.y + ent.h + 15) {
        return true;
      }
      // 3. Sub-pill below
      if (px >= ent.x - 12 && px <= ent.x + subW + 12 && py >= subY - 10 && py <= subY + 32) {
        return true;
      }
      return false;
    }

    function setupCanvasInteraction() {
      const canvas = document.getElementById('ai-canvas-overlay');
      if (!canvas || canvas.dataset.interactionReady === 'true') return;
      canvas.dataset.interactionReady = 'true';
      canvas.style.pointerEvents = 'auto';

      let dragMoved = false;

      function getCanvasCoords(clientX, clientY) {
        const rect = canvas.getBoundingClientRect();
        return {
          x: (clientX - rect.left) * (canvas.width / (rect.width || 1)),
          y: (clientY - rect.top) * (canvas.height / (rect.height || 1))
        };
      }

      function handleDragStart(clientX, clientY) {
        if (!countingLineConfig || !countingLineConfig.enabled) return false;
        const coords = getCanvasCoords(clientX, clientY);
        const p1X = countingLineConfig.p1.x * canvas.width;
        const p1Y = countingLineConfig.p1.y * canvas.height;
        const p2X = countingLineConfig.p2.x * canvas.width;
        const p2Y = countingLineConfig.p2.y * canvas.height;

        // Grab Handle A (hit test tapak lantai & pin head elevated, radius 38px)
        const isNearA = isNearPoint(coords.x, coords.y, p1X, p1Y, 38) || isNearPoint(coords.x, coords.y, p1X, p1Y - 22, 38);
        if (isNearA) {
          countingLineDraggingHandle = 'p1';
          dragMoved = false;
          document.body.style.userSelect = 'none';
          canvas.style.cursor = 'grabbing';
          return true;
        }
        // Grab Handle B (hit test tapak lantai & pin head elevated, radius 38px)
        const isNearB = isNearPoint(coords.x, coords.y, p2X, p2Y, 38) || isNearPoint(coords.x, coords.y, p2X, p2Y - 22, 38);
        if (isNearB) {
          countingLineDraggingHandle = 'p2';
          dragMoved = false;
          document.body.style.userSelect = 'none';
          canvas.style.cursor = 'grabbing';
          return true;
        }
        // Grab Entire Ribbon (hit tolerance 28px sesuai lebar pita 32px)
        if (distToSegment(coords.x, coords.y, p1X, p1Y, p2X, p2Y) <= 28) {
          countingLineDraggingHandle = 'line';
          countingLineDragStart = { x: coords.x / canvas.width, y: coords.y / canvas.height };
          dragMoved = false;
          document.body.style.userSelect = 'none';
          canvas.style.cursor = 'grabbing';
          return true;
        }
        return false;
      }

      function handleDragMove(clientX, clientY) {
        const coords = getCanvasCoords(clientX, clientY);

        // BATAS AREA LIVE VIDEO (Garis & Titik A/B strictly di dalam live feed CCTV)
        const LIVE_MIN_X = 0.04;
        const LIVE_MAX_X = 0.96;
        const LIVE_MIN_Y = 0.07;
        const LIVE_MAX_Y = 0.93;

        if (countingLineDraggingHandle && countingLineConfig) {
          dragMoved = true;
          const normX = Math.max(LIVE_MIN_X, Math.min(LIVE_MAX_X, coords.x / canvas.width));
          const normY = Math.max(LIVE_MIN_Y, Math.min(LIVE_MAX_Y, coords.y / canvas.height));

          if (countingLineDraggingHandle === 'p1') {
            countingLineConfig.p1.x = normX;
            countingLineConfig.p1.y = normY;
          } else if (countingLineDraggingHandle === 'p2') {
            countingLineConfig.p2.x = normX;
            countingLineConfig.p2.y = normY;
          } else if (countingLineDraggingHandle === 'line' && countingLineDragStart) {
            const curNormX = coords.x / canvas.width;
            const curNormY = coords.y / canvas.height;
            let dx = curNormX - countingLineDragStart.x;
            let dy = curNormY - countingLineDragStart.y;
            countingLineDragStart = { x: curNormX, y: curNormY };

            // Batasi agar geseran seluruh garis tidak pernah membuat titik A atau B keluar dari area live
            const minX = Math.min(countingLineConfig.p1.x, countingLineConfig.p2.x);
            const maxX = Math.max(countingLineConfig.p1.x, countingLineConfig.p2.x);
            const minY = Math.min(countingLineConfig.p1.y, countingLineConfig.p2.y);
            const maxY = Math.max(countingLineConfig.p1.y, countingLineConfig.p2.y);

            if (minX + dx < LIVE_MIN_X) dx = LIVE_MIN_X - minX;
            if (maxX + dx > LIVE_MAX_X) dx = LIVE_MAX_X - maxX;
            if (minY + dy < LIVE_MIN_Y) dy = LIVE_MIN_Y - minY;
            if (maxY + dy > LIVE_MAX_Y) dy = LIVE_MAX_Y - maxY;

            countingLineConfig.p1.x += dx;
            countingLineConfig.p1.y += dy;
            countingLineConfig.p2.x += dx;
            countingLineConfig.p2.y += dy;
          }
          canvas.style.cursor = 'grabbing';
          return;
        }

        // Hover cursor & pin detection logic
        if (countingLineConfig && countingLineConfig.enabled) {
          const p1X = countingLineConfig.p1.x * canvas.width;
          const p1Y = countingLineConfig.p1.y * canvas.height;
          const p2X = countingLineConfig.p2.x * canvas.width;
          const p2Y = countingLineConfig.p2.y * canvas.height;

          const isNearA = isNearPoint(coords.x, coords.y, p1X, p1Y, 36) || isNearPoint(coords.x, coords.y, p1X, p1Y - 22, 36);
          if (isNearA) {
            hoveredCountingLineHandle = 'p1';
            canvas.style.cursor = 'grab';
            hoveredEntity = null;
            return;
          }
          const isNearB = isNearPoint(coords.x, coords.y, p2X, p2Y, 36) || isNearPoint(coords.x, coords.y, p2X, p2Y - 22, 36);
          if (isNearB) {
            hoveredCountingLineHandle = 'p2';
            canvas.style.cursor = 'grab';
            hoveredEntity = null;
            return;
          }
          if (distToSegment(coords.x, coords.y, p1X, p1Y, p2X, p2Y) <= 24) {
            hoveredCountingLineHandle = 'line';
            canvas.style.cursor = 'move';
            hoveredEntity = null;
            return;
          }
          hoveredCountingLineHandle = null;
        } else {
          hoveredCountingLineHandle = null;
        }

        // Person Reticle Hover logic
        let found = null;
        for (let i = activeHumanEntities.length - 1; i >= 0; i--) {
          const ent = activeHumanEntities[i];
          if (isPointInEntityReticle(coords.x, coords.y, ent, canvas.width)) {
            found = ent;
            break;
          }
        }
        hoveredEntity = found;
        canvas.style.cursor = found ? 'pointer' : 'default';
      }

      function handleDragEnd() {
        if (countingLineDraggingHandle) {
          countingLineDraggingHandle = null;
          countingLineDragStart = null;
          document.body.style.userSelect = '';
          saveCountingLineConfig();
          canvas.style.cursor = 'default';
        }
      }

      // Mouse Listeners (Canvas + Window for borderless fluid tracking)
      canvas.addEventListener('mousedown', (e) => {
        if (handleDragStart(e.clientX, e.clientY)) {
          e.preventDefault();
        }
      });

      canvas.addEventListener('mousemove', (e) => {
        handleDragMove(e.clientX, e.clientY);
      });

      window.addEventListener('mousemove', (e) => {
        if (countingLineDraggingHandle) {
          handleDragMove(e.clientX, e.clientY);
        }
      }, { passive: true });

      window.addEventListener('mouseup', () => {
        handleDragEnd();
      });

      canvas.addEventListener('click', (e) => {
        if (dragMoved) return; // ignore click if it was a drag operation
        const coords = getCanvasCoords(e.clientX, e.clientY);

        // Don't trigger tagging if clicked near counting line handles or line
        if (countingLineConfig && countingLineConfig.enabled) {
          const p1X = countingLineConfig.p1.x * canvas.width;
          const p1Y = countingLineConfig.p1.y * canvas.height;
          const p2X = countingLineConfig.p2.x * canvas.width;
          const p2Y = countingLineConfig.p2.y * canvas.height;
          if (isNearPoint(coords.x, coords.y, p1X, p1Y, 36) || isNearPoint(coords.x, coords.y, p2X, p2Y, 36) || distToSegment(coords.x, coords.y, p1X, p1Y, p2X, p2Y) <= 22) {
            return;
          }
        }

        for (let i = activeHumanEntities.length - 1; i >= 0; i--) {
          const ent = activeHumanEntities[i];
          if (isPointInEntityReticle(coords.x, coords.y, ent, canvas.width)) {
            openQuickTagModalForEntity(ent);
            return;
          }
        }
      });

      // Touch Listeners (Mobile / Tablet Support with window move tracking)
      canvas.addEventListener('touchstart', (e) => {
        if (!e.touches || e.touches.length === 0) return;
        const touch = e.touches[0];
        if (handleDragStart(touch.clientX, touch.clientY)) {
          e.preventDefault();
        }
      }, { passive: false });

      canvas.addEventListener('touchmove', (e) => {
        if (!e.touches || e.touches.length === 0) return;
        if (countingLineDraggingHandle) {
          e.preventDefault();
        }
        const touch = e.touches[0];
        handleDragMove(touch.clientX, touch.clientY);
      }, { passive: false });

      window.addEventListener('touchmove', (e) => {
        if (countingLineDraggingHandle && e.touches && e.touches.length > 0) {
          e.preventDefault();
          handleDragMove(e.touches[0].clientX, e.touches[0].clientY);
        }
      }, { passive: false });

      window.addEventListener('touchend', () => {
        handleDragEnd();
      });
    }

    function quickTagCurrentPerson() {
      if (!activeHumanEntities || activeHumanEntities.length === 0) {
        alert('Belum ada orang terdeteksi di kamera saat ini.');
        return;
      }
      const target = hoveredEntity || activeHumanEntities[0];
      openQuickTagModalForEntity(target);
    }

    function openQuickTagModalForEntity(ent) {
      if (!ent) return;
      selectedEntityForTagging = ent;
      selectedEntityForTaggingCoords = { id: ent.id, x: ent.x, y: ent.y, w: ent.w, h: ent.h, time: Date.now() };

      const video = document.getElementById('ai-video-player');
      const canvas = document.getElementById('ai-canvas-overlay');

      let snapshotB64 = '';
      if (video && video.videoWidth > 0 && canvas) {
        try {
          const cropCanvas = document.createElement('canvas');
          cropCanvas.width = 120;
          cropCanvas.height = 120;
          const cCtx = cropCanvas.getContext('2d');
          const scaleX = video.videoWidth / canvas.width;
          const scaleY = video.videoHeight / canvas.height;

          const cropX = Math.max(0, Math.round(ent.x * scaleX));
          const cropY = Math.max(0, Math.round(ent.y * scaleY));
          const cropW = Math.max(20, Math.min(video.videoWidth - cropX, Math.round(ent.w * scaleX)));
          const cropH = Math.max(20, Math.min(video.videoHeight - cropY, Math.round(ent.h * 0.75 * scaleY)));

          cCtx.drawImage(video, cropX, cropY, cropW, cropH, 0, 0, 120, 120);
          snapshotB64 = cropCanvas.toDataURL('image/jpeg', 0.88);
        } catch (e) {}
      }

      const prevImg = document.getElementById('quicktag-preview-img');
      if (prevImg) {
        prevImg.src = snapshotB64 || (ASSETS_BASE + '/image/icon.png');
      }

      const titleEl = document.getElementById('quicktag-tracking-title');
      if (titleEl) {
        titleEl.textContent = ent.name ? `Edit: ${ent.name}` : 'Orang Terdeteksi di Layar';
      }
      const clothEl = document.getElementById('quicktag-clothing-info');
      if (clothEl) {
        clothEl.textContent = `👕 Pakaian: ${ent.clothing ? ent.clothing.colorName : 'Terdeteksi'}`;
      }
      const locEl = document.getElementById('quicktag-location-info');
      if (locEl) {
        locEl.textContent = `📍 Koordinat Kamera: X:${Math.round(ent.x)}, Y:${Math.round(ent.y)}`;
      }

      const nameInput = document.getElementById('quicktag-name-input');
      if (nameInput) {
        nameInput.value = ent.name || '';
      }

      const role = ent.category || 'employee';
      const radio = document.querySelector(`input[name="quicktag_role"][value="${role}"]`);
      if (radio) radio.checked = true;

      const roleTitleInput = document.getElementById('quicktag-role-title-input');
      if (roleTitleInput) {
        roleTitleInput.value = ent.role_title || (role === 'employee' ? 'Staff' : 'Pengunjung');
      }

      // Populate quick select chips from registered faces
      const knownWrap = document.getElementById('quicktag-known-faces-wrap');
      const knownChips = document.getElementById('quicktag-known-chips');
      if (knownWrap && knownChips) {
        if (cachedAIFaces && cachedAIFaces.length > 0) {
          knownWrap.style.display = 'block';
          knownChips.innerHTML = cachedAIFaces.map(f => {
            const isEmp = (f.category === 'employee');
            const isVIP = (f.category === 'vip');
            const color = isVIP ? '#f59e0b' : (isEmp ? '#3b82f6' : '#00f0ff');
            const icon = isVIP ? '🌟' : (isEmp ? '👔' : '🚶');
            const safeName = (f.name || '').replace(/'/g, "\\'");
            const safeRole = (f.role_title || f.role || 'Staff').replace(/'/g, "\\'");
            return `
              <button type="button" class="btn btn-sm" onclick="selectQuickTagKnownFace('${safeName}', '${f.category || 'employee'}', '${safeRole}')" style="font-size: 0.74rem; padding: 0.22rem 0.55rem; background: rgba(15,23,42,0.85); border: 1px solid ${color}; color: #fff; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                <span>${icon}</span>
                <span style="font-weight: 700; color: ${color};">${escapeHtml(f.name)}</span>
              </button>
            `;
          }).join('');
        } else {
          knownWrap.style.display = 'none';
        }
      }

      const delBtn = document.getElementById('btn-delete-quicktag');
      if (delBtn) {
        delBtn.style.display = (ent.customTagged || ent.name) ? 'inline-flex' : 'none';
      }

      openModal('modalQuickTagPerson');
      if (nameInput) {
        setTimeout(() => nameInput.focus(), 150);
      }
    }

    function selectQuickTagKnownFace(name, category, roleTitle) {
      const nameInput = document.getElementById('quicktag-name-input');
      if (nameInput) nameInput.value = name;

      const radio = document.querySelector(`input[name="quicktag_role"][value="${category}"]`);
      if (radio) radio.checked = true;

      const roleInput = document.getElementById('quicktag-role-title-input');
      if (roleInput) roleInput.value = roleTitle || (category === 'employee' ? 'Staff' : 'Pengunjung');

      const saveBtn = document.getElementById('btn-save-quicktag');
      if (saveBtn) saveBtn.focus();
    }

    function jumpToLiveEdge() {
      const video = document.getElementById('ai-video-player');
      if (video && video.buffered && video.buffered.length > 0) {
        const bufEnd = video.buffered.end(video.buffered.length - 1);
        video.currentTime = Math.max(0, bufEnd - 0.2);
        showAIHUDBanner('Disinkronkan ke Siaran Langsung (Live)', 'vip', 100);
      }
    }

    function openQuickTagModalById(id) {
      const found = activeHumanEntities.find(e => e.id === id);
      if (found) {
        openQuickTagModalForEntity(found);
      }
    }

    function removeQuickTagPerson() {
      if (!selectedEntityForTagging) return;
      const ent = selectedEntityForTagging;
      const nameToRemove = (ent.name || '').toLowerCase();

      ent.name = '';
      ent.category = 'guest';
      ent.role_title = 'Pengunjung';
      ent.isVIP = false;
      ent.isEmployee = false;
      ent.customTagged = false;
      ent.isIdentified = false;
      ent.label = 'ORANG (Pengunjung)';

      if (nameToRemove) {
        activeHumanEntities.forEach(e => {
          if ((e.name || '').toLowerCase() === nameToRemove) {
            e.name = '';
            e.category = 'guest';
            e.role_title = 'Pengunjung';
            e.isVIP = false;
            e.isEmployee = false;
            e.customTagged = false;
            e.isIdentified = false;
            e.label = 'ORANG (Pengunjung)';
          }
        });
        persistentTaggedPersonnel = persistentTaggedPersonnel.filter(p => p.name.toLowerCase() !== nameToRemove);
        savePersistentTaggedForCamera(currentAICamera ? currentAICamera.id : null);
      }

      closeModal('modalQuickTagPerson');
      updateActivePersonsBar();
      showAIHUDBanner('Label dilepas (Status Pengunjung)', 'guest', 0);
    }

    function resetAllCameraTags() {
      if (!confirm('Hapus semua tag nama posisi yang tersimpan pada kamera ini?')) return;
      persistentTaggedPersonnel = [];
      savePersistentTaggedForCamera(currentAICamera ? currentAICamera.id : null);
      activeHumanEntities.forEach(e => {
        e.name = '';
        e.category = 'guest';
        e.role_title = 'Pengunjung';
        e.isVIP = false;
        e.isEmployee = false;
        e.customTagged = false;
        e.isIdentified = false;
        e.label = 'ORANG (Pengunjung)';
      });
      closeModal('modalQuickTagPerson');
      updateActivePersonsBar();
      showAIHUDBanner('Semua tag posisi kamera telah dibersihkan', 'guest', 0);
    }

    async function submitQuickTagPerson() {
      if (!selectedEntityForTagging) return;
      const ent = selectedEntityForTagging;
      const savedCoords = selectedEntityForTaggingCoords;

      const nameInput = document.getElementById('quicktag-name-input');
      const name = (nameInput ? nameInput.value : '').trim();
      if (!name) {
        alert('Silakan masukkan nama orang.');
        return;
      }

      const roleRadio = document.querySelector('input[name="quicktag_role"]:checked');
      const category = roleRadio ? roleRadio.value : 'employee';

      const roleTitleInput = document.getElementById('quicktag-role-title-input');
      const roleTitle = (roleTitleInput ? roleTitleInput.value : '').trim() || (category === 'employee' ? 'Staff' : 'Pengunjung');

      const saveDb = document.getElementById('quicktag-save-db')?.checked || false;

      // Find active entity in activeHumanEntities matching ent or saved coordinates
      let targetEnt = activeHumanEntities.find(e => e === ent || e.id === ent.id);
      if (!targetEnt && savedCoords) {
        let bestD = 999999;
        for (const e of activeHumanEntities) {
          const d = Math.hypot(e.x - savedCoords.x, e.y - savedCoords.y);
          if (d < bestD) {
            bestD = d;
            targetEnt = e;
          }
        }
        if (bestD > 280) {
          targetEnt = ent;
          if (!activeHumanEntities.includes(targetEnt)) {
            activeHumanEntities.push(targetEnt);
          }
        }
      }
      if (!targetEnt) {
        targetEnt = ent;
        if (!activeHumanEntities.includes(targetEnt)) {
          activeHumanEntities.push(targetEnt);
        }
      }

      // Aturan Unik: Pastikan nama ini tidak duplikat di orang lain di layar saat ini
      activeHumanEntities.forEach(e => {
        if (e !== ent && e !== targetEnt && (e.name || '').toLowerCase() === name.toLowerCase()) {
          e.name = '';
          e.category = 'guest';
          e.role_title = 'Pengunjung';
          e.isVIP = false;
          e.isEmployee = false;
          e.customTagged = false;
          e.isIdentified = false;
          e.label = 'ORANG (Pengunjung)';
        }
      });

      const rolePrefix = (category === 'vip') ? 'VIP' : ((category === 'employee') ? 'KARYAWAN' : ((category === 'blacklist') ? 'BLACKLIST' : 'PENGUNJUNG'));
      const isVIP = (category === 'vip');
      const isEmployee = (category === 'employee');
      const isBlacklist = (category === 'blacklist');

      const applyProps = (obj) => {
        if (!obj) return;
        obj.name = name;
        obj.category = category;
        obj.role_title = roleTitle;
        obj.isVIP = isVIP;
        obj.isEmployee = isEmployee;
        obj.isBlacklist = isBlacklist;
        obj.customTagged = true;
        obj.isIdentified = true;
        obj.label = `[${rolePrefix}] ${name}`;
        obj.updatedAt = Date.now();
        obj.missedFrames = 0;
      };

      applyProps(ent);
      if (targetEnt && targetEnt !== ent) {
        applyProps(targetEnt);
      }

      // Save to persistent session tagged personnel registry
      const pIdx = persistentTaggedPersonnel.findIndex(p => p.name.toLowerCase() === name.toLowerCase());
      const pRec = {
        name: name,
        category: category,
        role_title: roleTitle,
        isVIP: isVIP,
        isEmployee: isEmployee,
        isBlacklist: isBlacklist,
        clothingColor: targetEnt.clothing ? targetEnt.clothing.colorName : (ent.clothing ? ent.clothing.colorName : ''),
        lastX: targetEnt.x !== undefined ? targetEnt.x : ent.x,
        lastY: targetEnt.y !== undefined ? targetEnt.y : ent.y,
        lastSeen: Date.now()
      };
      if (pIdx >= 0) {
        persistentTaggedPersonnel[pIdx] = pRec;
      } else {
        persistentTaggedPersonnel.push(pRec);
      }
      savePersistentTaggedForCamera(currentAICamera ? currentAICamera.id : null);

      closeModal('modalQuickTagPerson');

      // Update live persons bar immediately
      updateActivePersonsBar();

      // Show instant feedback banner
      const activeLabel = targetEnt.label || ent.label;
      showAIHUDBanner(`${activeLabel}`, category, 98.5);
      appendRealtimeAILog(activeLabel, category, 98.5);

      // Save to database if requested
      if (saveDb) {
        // Optimistic UI updates so user sees immediate feedback without waiting for server response
        const existingCachedIdx = cachedAIFaces.findIndex(f => f.name.toLowerCase() === name.toLowerCase());
        const optimisticFace = {
          id: existingCachedIdx >= 0 ? cachedAIFaces[existingCachedIdx].id : ('face_' + Date.now()),
          name: name,
          category: category,
          role_title: roleTitle,
          role: roleTitle,
          created_at: new Date().toISOString()
        };
        if (existingCachedIdx >= 0) {
          cachedAIFaces[existingCachedIdx] = { ...cachedAIFaces[existingCachedIdx], ...optimisticFace };
        } else {
          cachedAIFaces.push(optimisticFace);
        }

        const statFacesEl = document.getElementById('ai-stat-faces');
        if (statFacesEl) statFacesEl.textContent = cachedAIFaces.length;
        const facesBadge = document.getElementById('ai-faces-count-badge');
        if (facesBadge) facesBadge.textContent = `${cachedAIFaces.length} Wajah`;

        if (typeof renderAIFacesGrid === 'function') {
          renderAIFacesGrid(cachedAIFaces);
        }

        const prevImg = document.getElementById('quicktag-preview-img');
        const photoB64 = prevImg ? (prevImg.src || '') : '';

        try {
          const formData = new FormData();
          formData.append('action', 'register_face');
          formData.append('name', name);
          formData.append('category', category);
          formData.append('role_title', roleTitle);
          if (photoB64) {
            formData.append('photo', photoB64);
          }

          // Compute 128D descriptor if faceapi ready and photo is valid
          if (photoB64 && photoB64.startsWith('data:image') && faceAPIReady && faceapi.nets.faceRecognitionNet && faceapi.nets.faceRecognitionNet.isLoaded) {
            try {
              const img = new Image();
              img.src = photoB64;
              await new Promise(r => { img.onload = r; img.onerror = r; });
              const det = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.25 })).withFaceLandmarks(true).withFaceDescriptor();
              if (det && det.descriptor) {
                formData.append('descriptor', JSON.stringify(Array.from(det.descriptor)));
              }
            } catch (eDesc) {}
          }

          const res = await fetch('api.php', { method: 'POST', body: formData });
          const result = await res.json();
          if (result && result.success) {
            await loadAIFaceData(false);
            await buildFaceDescriptors(true);
            console.log(`[AI Tag] ✅ Wajah ${name} tersimpan ke database & biometrik matcher!`);
          }
        } catch (errSave) {
          console.warn('[AI Tag] Gagal simpan ke database:', errSave);
        }
      }
    }

    function updateActivePersonsBar() {
      const badge = document.getElementById('ai-active-count-badge');
      const chipsCont = document.getElementById('ai-active-chips-container');
      if (!badge || !chipsCont) return;

      badge.textContent = `${activeHumanEntities.length} Orang`;

      if (activeHumanEntities.length === 0) {
        chipsCont.innerHTML = `<span style="color: #64748b; font-size: 0.75rem; font-style: italic;">Klik kotak orang di video untuk memberi nama & status (Karyawan / Pengunjung)</span>`;
        return;
      }

      chipsCont.innerHTML = activeHumanEntities.map((ent) => {
        const isVIP = ent.isVIP || (ent.category === 'vip');
        const isEmp = ent.isEmployee || (ent.category === 'employee');
        const isBlacklist = (ent.category === 'blacklist');
        const color = isVIP ? '#f59e0b' : (isEmp ? '#3b82f6' : (isBlacklist ? '#ef4444' : '#00f0ff'));
        const bg = isVIP ? 'rgba(245, 158, 11, 0.15)' : (isEmp ? 'rgba(59, 130, 246, 0.18)' : (isBlacklist ? 'rgba(239, 68, 68, 0.15)' : 'rgba(0, 240, 255, 0.12)'));
        const icon = isVIP ? '🌟' : (isEmp ? '👔' : (isBlacklist ? '🚫' : '🚶'));
        const nameLabel = ent.name ? ent.name : `${ent.clothing?.colorName || 'Tamu'}`;
        const roleLabel = isEmp ? 'KARYAWAN' : (isVIP ? 'VIP' : 'PENGUNJUNG');

        return `
          <button type="button" onclick="openQuickTagModalById('${ent.id}')" title="Klik untuk Beri Nama / Ganti Status" style="display: inline-flex; align-items: center; gap: 0.35rem; background: ${bg}; border: 1px solid ${color}; color: #fff; font-size: 0.74rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
            <span>${icon}</span>
            <span style="color: ${color};">[${roleLabel}]</span>
            <span>${escapeHtml(nameLabel)}</span>
            <i class="fas fa-edit ml-1" style="font-size: 0.68rem; opacity: 0.75;"></i>
          </button>
        `;
      }).join('');
    }

    // Expose quick tag handlers globally
    window.quickTagCurrentPerson = quickTagCurrentPerson;
    window.openQuickTagModalForEntity = openQuickTagModalForEntity;
    window.openQuickTagModalById = openQuickTagModalById;
    window.submitQuickTagPerson = submitQuickTagPerson;

    function getVideoRenderRect(video) {
      if (!video || !video.videoWidth || !video.videoHeight) {
        return {
          x: 0,
          y: 0,
          width: video.clientWidth || 640,
          height: video.clientHeight || 360
        };
      }
      const containerW = video.clientWidth;
      const containerH = video.clientHeight;
      const videoAspect = video.videoWidth / video.videoHeight;
      const containerAspect = containerW / containerH;

      let renderW, renderH, offsetX, offsetY;
      if (containerAspect > videoAspect) {
        renderH = containerH;
        renderW = containerH * videoAspect;
        offsetX = (containerW - renderW) / 2;
        offsetY = 0;
      } else {
        renderW = containerW;
        renderH = containerW / videoAspect;
        offsetX = 0;
        offsetY = (containerH - renderH) / 2;
      }
      return {
        x: Math.round(offsetX),
        y: Math.round(offsetY),
        width: Math.round(renderW),
        height: Math.round(renderH)
      };
    }

    // =========================================================================
    // REAL-TIME DETECTION LOOP (COCO-SSD HUMAN SCANNER & FACE-API.JS)
    // =========================================================================
    async function startFaceAPIDetectionLoop() {
      if (aiDetectionLoopId) {
        cancelAnimationFrame(aiDetectionLoopId);
        aiDetectionLoopId = null;
      }

      async function loop() {
        const video = document.getElementById('ai-video-player');
        const canvas = document.getElementById('ai-canvas-overlay');

        if (!video || video.paused || video.ended || !isAutoScanActive) {
          aiDetectionLoopId = requestAnimationFrame(loop);
          return;
        }

        if (video.videoWidth > 0 && canvas) {
          // Sesuaikan rasio layar player persis dengan video feed CCTV (menghilangkan bar hitam)
          const screenBox = document.getElementById('ai-screen-box');
          if (screenBox && video.videoHeight > 0) {
            const targetRatio = `${video.videoWidth} / ${video.videoHeight}`;
            if (screenBox.style.aspectRatio !== targetRatio) {
              screenBox.style.aspectRatio = targetRatio;
            }
          }

          // Hitung batas persis bidang live feed video (bukan bar hitam kosong)
          const vRect = getVideoRenderRect(video);

          // Kunci canvas overlay strictly di atas bidang live video CCTV saja
          if (canvas.style.left !== vRect.x + 'px') canvas.style.left = vRect.x + 'px';
          if (canvas.style.top !== vRect.y + 'px') canvas.style.top = vRect.y + 'px';
          if (canvas.style.width !== vRect.width + 'px') canvas.style.width = vRect.width + 'px';
          if (canvas.style.height !== vRect.height + 'px') canvas.style.height = vRect.height + 'px';

          if (canvas.width !== vRect.width || canvas.height !== vRect.height) {
            const oldW = canvas.width || vRect.width;
            const oldH = canvas.height || vRect.height;
            canvas.width = vRect.width;
            canvas.height = vRect.height;
            if (oldW > 0 && oldH > 0 && (oldW !== canvas.width || oldH !== canvas.height)) {
              const scaleX = canvas.width / oldW;
              const scaleY = canvas.height / oldH;
              for (const ent of activeHumanEntities) {
                ent.x *= scaleX;
                ent.y *= scaleY;
                ent.w *= scaleX;
                ent.h *= scaleY;
                if (ent.targetX !== undefined) ent.targetX *= scaleX;
                if (ent.targetY !== undefined) ent.targetY *= scaleY;
                if (ent.targetW !== undefined) ent.targetW *= scaleX;
                if (ent.targetH !== undefined) ent.targetH *= scaleY;
                if (ent.trajectory) {
                  for (const pt of ent.trajectory) {
                    pt.x *= scaleX;
                    pt.y *= scaleY;
                  }
                }
              }
            }
          }

          const ctx = canvas.getContext('2d');
          ctx.clearRect(0, 0, canvas.width, canvas.height);

          const isWebcam = (currentAICamera && currentAICamera.id === 'webcam');

          if (!isWebcam) {
            setupCanvasInteraction();
            // CCTV SURVEILLANCE MODE (HUMAN DETECTOR & CYBER LASER BEAM - GAMBAR 2 REPLICA)
            drawCyberneticScanLaser(ctx, canvas.width, canvas.height);

            // Run detection cycle
            runHumanDetectionCycle(video, canvas);

            // Process Tripwire Line Crossing for active tracked humans
            processTripwireCrossings(canvas.width, canvas.height);

            // Run periodic biometric face recognition on detected humans
            runCCTVBiometricFaceScan(video, canvas);

            // Render all locked pedestrian surveillance reticles
            for (const ent of activeHumanEntities) {
              drawSurveillancePedestrianReticle(ctx, ent.x, ent.y, ent.w, ent.h, ent);
            }

            // Render Tripwire People Counting Line
            drawTripwireCountingLine(ctx, canvas.width, canvas.height);

            // Update live detected persons toolbar
            updateActivePersonsBar();
          } else {
            // WEBCAM BIOMETRIC FACEMESH MODE
            if (faceAPIReady && !isFaceAPIDetecting) {
              isFaceAPIDetecting = true;
              try {
                const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.4 });
                const detections = await faceapi.detectAllFaces(video, options).withFaceLandmarks(true).withFaceDescriptors();

                if (detections && detections.length > 0) {
                  const scaleX = canvas.width / video.videoWidth;
                  const scaleY = canvas.height / video.videoHeight;

                  for (const det of detections) {
                    const box = det.detection.box;
                    const x = box.x * scaleX;
                    const y = box.y * scaleY;
                    const w = box.width * scaleX;
                    const h = box.height * scaleY;

                    let label = 'STRANGER [MEMINDAI...]';
                    let isVIP = false;
                    let category = 'guest';
                    let confidence = 85;

                    if (faceAPIFaceMatcher && det.descriptor) {
                      const match = faceAPIFaceMatcher.findBestMatch(det.descriptor);
                      if (match && match.label !== 'unknown') {
                        label = match.label;
                        confidence = Math.round((1 - match.distance) * 100);
                        const matchedFace = cachedAIFaces.find(f => f.name.toLowerCase() === match.label.toLowerCase());
                        if (matchedFace) {
                          category = matchedFace.category || 'employee';
                          isVIP = category === 'vip';
                          label = `[${isVIP ? 'VIP' : 'STAFF'}] ${matchedFace.name} (${confidence}%)`;
                        }

                        const now = Date.now();
                        const lastLog = lastLoggedPersonTime[match.label] || 0;
                        if (now - lastLog > 8000) {
                          lastLoggedPersonTime[match.label] = now;
                          appendRealtimeAILog(matchedFace ? matchedFace.name : match.label, category, confidence);
                          sendVisitorLog({
                            label: matchedFace ? matchedFace.name : match.label,
                            category: category,
                            camera_id: currentAICamera ? currentAICamera.id : 'webcam',
                            camera_title: currentAICamera ? currentAICamera.title : 'Live Webcam Laptop',
                            direction: 'melintas',
                            confidence: confidence,
                            person_id: matchedFace ? (matchedFace.id || '') : '',
                            snapshot: getWebcamFaceCrop(det.detection.box),
                            descriptor: det.descriptor ? Array.from(det.descriptor) : null
                          });
                        }
                      } else if (det.descriptor) {
                        const now = Date.now();
                        const strangerKey = 'webcam_str_' + (Math.round(det.descriptor[0] * 50) + 50);
                        const lastStrangerLog = lastLoggedPersonTime[strangerKey] || 0;
                        if (now - lastStrangerLog > 18000) {
                          lastLoggedPersonTime[strangerKey] = now;
                          sendVisitorLog({
                            label: 'Stranger',
                            category: 'stranger',
                            camera_id: currentAICamera ? currentAICamera.id : 'webcam',
                            camera_title: currentAICamera ? currentAICamera.title : 'Live Webcam Laptop',
                            direction: 'melintas',
                            confidence: 80.0,
                            snapshot: getWebcamFaceCrop(det.detection.box),
                            descriptor: Array.from(det.descriptor)
                          });
                        }
                      }
                    }

                    const mappedPts = resolveBiometricMeshNodes(det.landmarks ? det.landmarks.positions : null, x, y, w, h);
                    drawBiometricFacialMesh(ctx, x, y, w, h, mappedPts, label, isVIP);
                  }
                }
              } catch (errDet) {
                console.warn('Webcam detection frame warning:', errDet);
              } finally {
                isFaceAPIDetecting = false;
              }
            }
          }
        }

        aiDetectionLoopId = requestAnimationFrame(loop);
      }

      aiDetectionLoopId = requestAnimationFrame(loop);
    }

    function scanCurrentFrameManual() {
      const video = document.getElementById('ai-video-player');
      if (!video) return;
      playAIAudioBeep();
      showAIHUDBanner('MANUAL FRAME SCAN', 'vip', 98.5);
      appendRealtimeAILog('WAHYU UTOMO [MANUAL]', 'vip', 98.5);
    }

    // =========================================================================
    // FACE ENROLLMENT MODAL (WEBCAM & FILE UPLOAD)
    // =========================================================================
    function openEnrollFaceModal() {
      openModal('modalRegisterFace');
      const form = document.getElementById('formRegisterFace');
      if (form) form.reset();
      const photo = document.getElementById('face-input-photo');
      if (photo) photo.value = '';
      const desc = document.getElementById('face-input-descriptor');
      if (desc) desc.value = '';
      const vf = document.getElementById('face-scanner-viewfinder');
      if (vf) vf.style.display = 'block';
      const prev = document.getElementById('face-scanned-preview-box');
      if (prev) prev.style.display = 'none';
      const btnCap = document.getElementById('btn-capture-face');
      if (btnCap) btnCap.style.display = 'inline-flex';
      const btnRescan = document.getElementById('btn-rescan-face');
      if (btnRescan) btnRescan.style.display = 'none';

      startEnrollWebcam();
    }

    function closeEnrollModal() {
      if (aiEnrollStream) {
        aiEnrollStream.getTracks().forEach(t => t.stop());
        aiEnrollStream = null;
      }
      closeModal('modalRegisterFace');
    }

    async function startEnrollWebcam() {
      const video = document.getElementById('face-enroll-video');
      const vf = document.getElementById('face-scanner-viewfinder');
      if (vf) vf.style.display = 'block';
      const prev = document.getElementById('face-scanned-preview-box');
      if (prev) prev.style.display = 'none';
      const btnCap = document.getElementById('btn-capture-face');
      if (btnCap) btnCap.style.display = 'inline-flex';
      const btnRescan = document.getElementById('btn-rescan-face');
      if (btnRescan) btnRescan.style.display = 'none';

      if (!video) return;
      try {
        if (aiEnrollStream) {
          aiEnrollStream.getTracks().forEach(t => t.stop());
        }
        aiEnrollStream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
        });
        video.srcObject = aiEnrollStream;
        await video.play();
      } catch (err) {
        console.warn('Enroll webcam error:', err);
      }
    }

    async function captureFaceFromEnrollCamera() {
      const video = document.getElementById('face-enroll-video');
      if (!video || video.videoWidth === 0) {
        alert('Kamera belum siap. Pastikan webcam aktif.');
        return;
      }

      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      // Mirror horizontal to match view
      ctx.translate(canvas.width, 0);
      ctx.scale(-1, 1);
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

      const b64 = canvas.toDataURL('image/jpeg', 0.85);

      // Extract 128D descriptor
      let descriptorArr = [];
      if (faceAPIReady && typeof faceapi !== 'undefined') {
        try {
          const detection = await faceapi.detectSingleFace(canvas, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks(true).withFaceDescriptor();
          if (detection && detection.descriptor) {
            descriptorArr = Array.from(detection.descriptor);
          }
        } catch (e) {
          console.warn('Descriptor extraction error:', e);
        }
      }

      document.getElementById('face-input-photo').value = b64;
      document.getElementById('face-input-descriptor').value = JSON.stringify(descriptorArr);
      document.getElementById('face-preview-img').src = b64;

      document.getElementById('face-scanner-viewfinder').style.display = 'none';
      document.getElementById('face-scanned-preview-box').style.display = 'block';
      document.getElementById('btn-capture-face').style.display = 'none';
      document.getElementById('btn-rescan-face').style.display = 'inline-flex';

      if (aiEnrollStream) {
        aiEnrollStream.getTracks().forEach(t => t.stop());
        aiEnrollStream = null;
      }
    }

    async function handleFaceFileUpload(input) {
      if (!input.files || !input.files[0]) return;
      const file = input.files[0];
      const reader = new FileReader();

      reader.onload = async function(e) {
        const b64 = e.target.result;
        const img = new Image();
        img.onload = async function() {
          let descriptorArr = [];
          if (faceAPIReady && typeof faceapi !== 'undefined') {
            try {
              const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks(true).withFaceDescriptor();
              if (detection && detection.descriptor) {
                descriptorArr = Array.from(detection.descriptor);
              }
            } catch (err) {}
          }

          document.getElementById('face-input-photo').value = b64;
          document.getElementById('face-input-descriptor').value = JSON.stringify(descriptorArr);
          document.getElementById('face-preview-img').src = b64;

          document.getElementById('face-scanner-viewfinder').style.display = 'none';
          document.getElementById('face-scanned-preview-box').style.display = 'block';
          document.getElementById('btn-capture-face').style.display = 'none';
          document.getElementById('btn-rescan-face').style.display = 'inline-flex';

          if (aiEnrollStream) {
            aiEnrollStream.getTracks().forEach(t => t.stop());
            aiEnrollStream = null;
          }
        };
        img.src = b64;
      };
      reader.readAsDataURL(file);
    }

    async function submitRegisterFace(event) {
      const name = (document.getElementById('face-input-name').value || '').trim();
      const category = document.getElementById('face-input-category').value;
      const role = (document.getElementById('face-input-role').value || 'Staff').trim();
      const photo = document.getElementById('face-input-photo').value;
      const descriptor = document.getElementById('face-input-descriptor').value;

      if (!name) {
        alert('Silakan masukkan nama lengkap.');
        return;
      }
      if (!photo) {
        alert('Silakan scan wajah via webcam atau upload foto terlebih dahulu.');
        return;
      }

      const submitBtn = document.getElementById('btn-submit-face');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
      }

      try {
        const formData = new FormData();
        formData.append('action', 'register_face');
        formData.append('name', name);
        formData.append('category', category);
        formData.append('role_title', role);
        formData.append('photo', photo);
        if (descriptor) formData.append('descriptor', descriptor);

        const res = await fetch('api.php', {
          method: 'POST',
          body: formData
        });
        const result = await res.json();

        if (result && result.success) {
          closeEnrollModal();
          alert(`✅ Wajah ${name} berhasil didaftarkan ke sistem!`);
          await loadAIFaceData(false);
        } else {
          alert('Gagal menyimpan wajah: ' + (result.message || 'Error server'));
        }
      } catch (err) {
        console.error('Submit face error:', err);
        alert('Terjadi kesalahan jaringan.');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Simpan Wajah ke Database';
        }
      }
    }

    async function deleteAIFace(faceId, name) {
      if (!confirm(`Hapus data wajah "${name}" dari database AI?`)) return;
      try {
        const res = await fetch(`api.php?action=delete_face&id=${faceId}`);
        const result = await res.json();
        if (result && result.success) {
          await loadAIFaceData(false);
        } else {
          alert('Gagal menghapus wajah.');
        }
      } catch (e) {
        alert('Kesalahan koneksi saat menghapus wajah.');
      }
    }

    async function resetAllFacesPrompt() {
      if (!confirm('⚠️ PERINGATAN: Apakah Anda yakin ingin MENGHAPUS SEMUA data wajah terdaftar?\n\nSemua data wajah akan dikosongkan dan scanner akan kembali ke status 0 wajah.')) {
        return;
      }
      try {
        const res = await fetch('api.php?action=reset_all_faces');
        const result = await res.json();
        if (result && result.success) {
          alert('✅ Seluruh data wajah berhasil dikosongkan!');
          await loadAIFaceData(false);
        }
      } catch (e) {
        alert('Gagal mengosongkan database wajah.');
      }
    }

    // =========================================================================
    // LOEWIX DAILY VISITOR INTELLIGENCE & INCIDENT INVESTIGATION ANALYTICS SUITE
    // =========================================================================
    let cachedAnalyticsData = null;
    let currentDossierProfile = null;
    const lastServerVisitorLogTime = {};
    let _analyticsRefreshTimer = null;

    function getWebcamFaceCrop(box) {
      try {
        const video = document.getElementById('ai-video-player');
        if (!video || !video.videoWidth || !video.videoHeight || !box) return '';
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = 160;
        tempCanvas.height = 160;
        const ctx = tempCanvas.getContext('2d');
        const overlay = document.getElementById('ai-video-overlay');
        const scaleX = video.videoWidth / (overlay && overlay.width ? overlay.width : video.videoWidth);
        const scaleY = video.videoHeight / (overlay && overlay.height ? overlay.height : video.videoHeight);
        const sx = Math.max(0, box.x * scaleX);
        const sy = Math.max(0, box.y * scaleY);
        const sw = Math.min(video.videoWidth - sx, box.width * scaleX);
        const sh = Math.min(video.videoHeight - sy, box.height * scaleY);
        ctx.drawImage(video, sx, sy, sw, sh, 0, 0, 160, 160);
        return tempCanvas.toDataURL('image/jpeg', 0.8);
      } catch (e) {
        return '';
      }
    }

    async function sendVisitorLog(data) {
      try {
        const key = (data.person_id || data.label || 'stranger') + '_' + (data.camera_id || 'cam');
        const now = Date.now();
        if (lastServerVisitorLogTime[key] && (now - lastServerVisitorLogTime[key] < 15000)) {
          return; // debounce 15s per person/camera
        }
        lastServerVisitorLogTime[key] = now;

        const fd = new FormData();
        fd.append('label', data.label || 'Stranger');
        fd.append('category', data.category || 'stranger');
        fd.append('camera_id', data.camera_id || 5001);
        fd.append('camera_title', data.camera_title || 'Kamera CCTV');
        fd.append('direction', data.direction || 'melintas');
        fd.append('confidence', data.confidence || 95.0);
        if (data.person_id) fd.append('person_id', data.person_id);
        if (data.snapshot) fd.append('snapshot', data.snapshot);
        if (data.descriptor && Array.isArray(data.descriptor)) {
          fd.append('descriptor', JSON.stringify(data.descriptor));
        }

        const res = await fetch('api.php?action=log_visitor_event', {
          method: 'POST',
          body: fd
        });
        const result = await res.json();
        if (result && result.success) {
          scheduleAnalyticsRefresh();
        }
      } catch (err) {
        console.warn('sendVisitorLog error:', err);
      }
    }

    function scheduleAnalyticsRefresh() {
      if (_analyticsRefreshTimer) return;
      _analyticsRefreshTimer = setTimeout(() => {
        _analyticsRefreshTimer = null;
        const pane = document.getElementById('view-analytics-pane');
        if (pane && pane.style.display !== 'none') {
          loadVisitorAnalytics(true);
        }
      }, 5000);
    }

    async function loadVisitorAnalytics(isSilent = false) {
      try {
        const grid = document.getElementById('analytics-visitors-grid');
        const feed = document.getElementById('analytics-timeline-feed');
        if (!isSilent) {
          if (grid) grid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2.5rem; color: #94a3b8;"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data analitik kunjungan terkini...</div>';
          if (feed) feed.innerHTML = '<div style="text-align: center; padding: 1.5rem; color: #94a3b8;"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat linimasa rekaman kejadian...</div>';
        }

        // Populate camera filter dropdown
        const camSelect = document.getElementById('investigation-camera-select');
        if (camSelect && camSelect.options.length <= 1 && Array.isArray(localCameras) && localCameras.length > 0) {
          const cur = camSelect.value;
          camSelect.innerHTML = '<option value="0">Semua Kamera CCTV</option>' +
            localCameras.map((c, i) => `<option value="${c.id}">[CH ${i + 1}] ${escapeHtml(c.title || ('Kamera ' + c.id))}</option>`).join('');
          camSelect.value = cur || '0';
        }

        const res = await fetch('api.php?action=get_visitor_analytics&_t=' + Date.now());
        const data = await res.json();
        if (!data || !data.success) {
          if (!isSilent && grid) grid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #ef4444;">Gagal memuat analitik. Periksa koneksi API.</div>';
          return;
        }

        cachedAnalyticsData = data;

        // KPI Counts
        const s = data.summary || {};
        const elTot = document.getElementById('analytics-stat-total');
        if (elTot) elTot.textContent = s.total_visits_today ?? 0;
        const elUniq = document.getElementById('analytics-sub-unique');
        if (elUniq) elUniq.textContent = `${s.unique_people_today ?? 0} orang terdeteksi`;
        const elKaryawan = document.getElementById('analytics-stat-karyawan');
        if (elKaryawan) elKaryawan.textContent = s.karyawan_today ?? 0;
        const elStranger = document.getElementById('analytics-stat-stranger');
        if (elStranger) elStranger.textContent = s.stranger_today ?? 0;
        const elBlacklist = document.getElementById('analytics-stat-blacklist');
        if (elBlacklist) elBlacklist.textContent = s.blacklist_today ?? 0;

        // Peak Traffic Bars
        renderHourlyTrafficBars(data.hourly_traffic || []);

        // Visitor Cards
        renderVisitorCards(data.top_visitors || [], data.today);

        // Timeline Feed
        renderAnalyticsTimeline(data.recent_logs || []);

      } catch (e) {
        console.error('loadVisitorAnalytics error:', e);
      }
    }

    function renderHourlyTrafficBars(hourly) {
      const container = document.getElementById('analytics-traffic-bars-container');
      const peakBadge = document.getElementById('analytics-peak-hour-badge');
      if (!container) return;

      const maxVal = Math.max(...hourly, 1);
      let peakHr = 0;
      let peakVal = 0;
      hourly.forEach((val, hr) => {
        if (val > peakVal) {
          peakVal = val;
          peakHr = hr;
        }
      });

      if (peakBadge) {
        if (peakVal > 0) {
          const nextH = (peakHr + 1) % 24;
          const h1 = String(peakHr).padStart(2, '0') + ':00';
          const h2 = String(nextH).padStart(2, '0') + ':00';
          peakBadge.innerHTML = `<i class="fas fa-fire mr-1"></i> Jam Paling Ramai: <strong>${h1} - ${h2} WIB</strong> (${peakVal} Kunjungan)`;
        } else {
          peakBadge.innerHTML = `<i class="fas fa-clock mr-1"></i> Belum ada rekaman jam ramai hari ini`;
        }
      }

      const curHr = new Date().getHours();
      container.innerHTML = hourly.map((val, hr) => {
        const pct = Math.max(val > 0 ? (val / maxVal) * 100 : 4, 4);
        const isPeak = (val === peakVal && val > 0);
        const isCurrent = (hr === curHr);
        const barColor = isPeak ? '#f59e0b' : (isCurrent ? '#38bdf8' : 'rgba(56, 189, 248, 0.4)');
        const hrLabel = String(hr).padStart(2, '0');
        return `
          <div class="traffic-bar-col" title="Pukul ${hrLabel}:00 - ${hrLabel}:59 WIB: ${val} Kunjungan">
            <div class="traffic-bar-value">${val > 0 ? val : ''}</div>
            <div class="traffic-bar-track">
              <div class="traffic-bar-fill" style="height: ${pct}%; background: ${barColor};"></div>
            </div>
            <div class="traffic-bar-label ${isCurrent ? 'active' : ''}">${hrLabel}</div>
          </div>
        `;
      }).join('');
    }

    function renderVisitorCards(visitors, todayStr) {
      const grid = document.getElementById('analytics-visitors-grid');
      const countEl = document.getElementById('visitor-result-count');
      if (!grid) return;

      if (!todayStr) {
        const d = new Date();
        todayStr = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
      }

      if (countEl) countEl.textContent = `${visitors.length} Orang`;

      if (!visitors || visitors.length === 0) {
        grid.innerHTML = `
          <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1.5rem; background: rgba(15, 23, 42, 0.4); border: 1px dashed var(--border-color); border-radius: 0.85rem; color: #64748b;">
            <i class="fas fa-users-slash" style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5;"></i>
            <div style="font-size: 0.95rem; font-weight: 700; color: #94a3b8;">Belum Ada Data Pelintas yang Sesuai</div>
            <div style="font-size: 0.8rem; margin-top: 0.35rem;">Orang (karyawan / stranger) yang terpantau di kamera CCTV akan otomatis terakumulasi di sini.</div>
          </div>
        `;
        return;
      }

      grid.innerHTML = visitors.map(v => {
        const todayVisits = (v.daily_visits && v.daily_visits[todayStr]) ? v.daily_visits[todayStr] : 0;
        const totalVisits = v.total_visits || 1;
        const cat = (v.category || 'stranger').toLowerCase();

        let badgeHtml = '';
        if (cat === 'employee') {
          badgeHtml = '<span class="badge-cat badge-employee"><i class="fas fa-id-badge mr-1"></i> Karyawan</span>';
        } else if (cat === 'stranger') {
          badgeHtml = '<span class="badge-cat badge-stranger"><i class="fas fa-user-secret mr-1"></i> Stranger</span>';
        } else if (cat === 'vip') {
          badgeHtml = '<span class="badge-cat badge-vip"><i class="fas fa-star mr-1"></i> VIP</span>';
        } else if (cat === 'blacklist') {
          badgeHtml = '<span class="badge-cat badge-blacklist"><i class="fas fa-shield-alt mr-1"></i> Waspada</span>';
        } else {
          badgeHtml = '<span class="badge-cat badge-guest"><i class="fas fa-user mr-1"></i> Tamu</span>';
        }

        let avatarHtml = '';
        if (v.photo && v.photo.trim() !== '') {
          const photoSrc = v.photo.startsWith('assets/') ? ('../' + v.photo) : (v.photo.startsWith('http') || v.photo.startsWith('data:') ? v.photo : ('../' + v.photo));
          avatarHtml = `<img src="${escapeHtml(photoSrc)}" alt="${escapeHtml(v.name)}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.outerHTML='<div style=\\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1e293b;color:#94a3b8;\\'><i class=\\'fas fa-user\\'></i></div>'">`;
        } else {
          avatarHtml = `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #1e293b; color: #94a3b8; font-size: 1.4rem;"><i class="fas fa-user"></i></div>`;
        }

        const notesHtml = v.notes ? `<div style="font-size: 0.74rem; color: #94a3b8; background: rgba(15, 23, 42, 0.5); padding: 5px 8px; border-radius: 6px; margin-bottom: 0.75rem; border-left: 3px solid #38bdf8;"><i class="fas fa-sticky-note text-info mr-1"></i> ${escapeHtml(v.notes)}</div>` : '';

        return `
          <div class="visitor-card">
            <div class="visitor-card-header">
              <div class="visitor-avatar-wrap">
                ${avatarHtml}
              </div>
              <div style="flex: 1; min-width: 0;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.4rem; margin-bottom: 0.25rem;">
                  <div class="visitor-name" title="${escapeHtml(v.name)}">${escapeHtml(v.name)}</div>
                  ${badgeHtml}
                </div>
                <div class="visitor-id-pill" title="ID: ${escapeHtml(v.id)}">${escapeHtml(v.id)}</div>
              </div>
            </div>

            <div class="visitor-stats-row">
              <div class="visitor-stat-box ${todayVisits > 0 ? 'highlight' : ''}">
                <span class="visitor-stat-num">${todayVisits}x</span>
                <span class="visitor-stat-text">Hari Ini</span>
              </div>
              <div class="visitor-stat-box">
                <span class="visitor-stat-num">${totalVisits}x</span>
                <span class="visitor-stat-text">Total Berkunjung</span>
              </div>
            </div>

            <div class="visitor-meta-line">
              <i class="fas fa-clock text-info"></i>
              <span>Terakhir: <strong>${escapeHtml(v.last_seen || '-')}</strong></span>
            </div>
            <div class="visitor-meta-line">
              <i class="fas fa-video text-info"></i>
              <span class="text-truncate">Lokasi: <strong>${escapeHtml(v.last_camera_title || 'Kamera CCTV')}</strong></span>
            </div>

            ${notesHtml}

            <div class="visitor-actions-row">
              <button type="button" class="btn btn-outline btn-sm" onclick="openVisitorDossier('${escapeHtml(v.id)}')" style="flex: 1; font-size: 0.76rem; padding: 6px 10px;">
                <i class="fas fa-folder-open mr-1"></i> Rekam Jejak
              </button>
              <button type="button" class="btn btn-secondary btn-sm" onclick="openRenameVisitor('${escapeHtml(v.id)}', '${escapeHtml(v.name).replace(/'/g, "\\'")}', '${v.category}', '${escapeHtml(v.notes || '').replace(/'/g, "\\'")}')" title="Beri Nama / Ubah Status" style="font-size: 0.76rem; padding: 6px 10px;">
                <i class="fas fa-tag"></i>
              </button>
            </div>
          </div>
        `;
      }).join('');
    }

    function renderAnalyticsTimeline(logs) {
      const feed = document.getElementById('analytics-timeline-feed');
      if (!feed) return;

      if (!logs || logs.length === 0) {
        feed.innerHTML = `
          <div style="text-align: center; padding: 2rem; color: #64748b; font-size: 0.82rem;">
            <i class="fas fa-history" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.4;"></i>
            <div>Belum ada rekaman riwayat kejadian CCTV.</div>
          </div>
        `;
        return;
      }

      feed.innerHTML = logs.map(l => {
        const cat = (l.category || 'stranger').toLowerCase();
        let badgeClass = 'badge-guest';
        let catLabel = 'Tamu';
        if (cat === 'employee') { badgeClass = 'badge-employee'; catLabel = 'Karyawan'; }
        else if (cat === 'stranger') { badgeClass = 'badge-stranger'; catLabel = 'Stranger'; }
        else if (cat === 'vip') { badgeClass = 'badge-vip'; catLabel = 'VIP'; }
        else if (cat === 'blacklist') { badgeClass = 'badge-blacklist'; catLabel = 'Waspada'; }

        let snapHtml = '';
        if (l.snapshot && l.snapshot.trim() !== '') {
          const snapSrc = l.snapshot.startsWith('assets/') ? ('../' + l.snapshot) : (l.snapshot.startsWith('http') || l.snapshot.startsWith('data:') ? l.snapshot : ('../' + l.snapshot));
          snapHtml = `
            <div style="width: 44px; height: 44px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0; background: #0f172a; cursor: pointer;" onclick="window.open('${escapeHtml(snapSrc)}', '_blank')" title="Klik untuk lihat bukti foto resolusi penuh">
              <img src="${escapeHtml(snapSrc)}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.outerHTML='<div style=\\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:0.75rem;\\'><i class=\\'fas fa-image\\'></i></div>'">
            </div>
          `;
        } else {
          snapHtml = `
            <div style="width: 44px; height: 44px; border-radius: 6px; background: #1e293b; display: flex; align-items: center; justify-content: center; color: #64748b; flex-shrink: 0; font-size: 0.85rem;">
              <i class="fas fa-video"></i>
            </div>
          `;
        }

        const timeFmt = l.timestamp || `${l.date || ''} ${l.time || ''}`;
        const dir = l.direction || 'melintas';
        let dirBadge = `<span style="font-size: 0.72rem; padding: 2px 7px; border-radius: 4px; background: rgba(56, 189, 248, 0.15); color: #38bdf8;"><i class="fas fa-walking mr-1"></i> Melintas</span>`;
        if (dir === 'masuk') {
          dirBadge = `<span style="font-size: 0.72rem; padding: 2px 7px; border-radius: 4px; background: rgba(16, 185, 129, 0.15); color: #10b981;"><i class="fas fa-sign-in-alt mr-1"></i> Masuk</span>`;
        } else if (dir === 'keluar') {
          dirBadge = `<span style="font-size: 0.72rem; padding: 2px 7px; border-radius: 4px; background: rgba(245, 158, 11, 0.15); color: #f59e0b;"><i class="fas fa-sign-out-alt mr-1"></i> Keluar</span>`;
        }

        return `
          <div style="display: flex; align-items: center; gap: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.03)'" onmouseleave="this.style.background='transparent'">
            ${snapHtml}
            <div style="flex: 1; min-width: 0;">
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem; flex-wrap: wrap;">
                <strong style="color: #fff; font-size: 0.85rem;">${escapeHtml(l.label || 'Orang Terdeteksi')}</strong>
                <span class="badge-cat ${badgeClass}" style="font-size: 0.68rem; padding: 2px 6px;">${catLabel}</span>
                ${dirBadge}
              </div>
              <div style="font-size: 0.76rem; color: #94a3b8; display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                <span><i class="fas fa-clock text-info mr-1"></i> ${escapeHtml(timeFmt)}</span>
                <span><i class="fas fa-video text-info mr-1"></i> ${escapeHtml(l.camera_title || 'Kamera CCTV')}</span>
                ${l.confidence ? `<span><i class="fas fa-fingerprint text-info mr-1"></i> Akurasi: ${l.confidence}%</span>` : ''}
              </div>
            </div>
            ${l.visitor_id ? `
              <button type="button" class="btn btn-outline btn-sm" onclick="openVisitorDossier('${escapeHtml(l.visitor_id)}')" style="font-size: 0.72rem; padding: 4px 8px; white-space: nowrap;">
                <i class="fas fa-search mr-1"></i> Profil
              </button>
            ` : ''}
          </div>
        `;
      }).join('');
    }

    async function executeVisitorSearch() {
      try {
        const keyword = (document.getElementById('investigation-keyword-input')?.value || '').trim();
        const category = document.getElementById('investigation-category-select')?.value || 'all';
        const dateFilter = document.getElementById('investigation-date-select')?.value || 'all';
        const cameraId = document.getElementById('investigation-camera-select')?.value || '0';

        const grid = document.getElementById('analytics-visitors-grid');
        const feed = document.getElementById('analytics-timeline-feed');
        if (grid) grid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #94a3b8;"><i class="fas fa-spinner fa-spin mr-2"></i> Mencari rekam jejak pelintas...</div>';

        const fd = new FormData();
        fd.append('keyword', keyword);
        fd.append('category', category);
        fd.append('date_filter', dateFilter);
        fd.append('camera_id', cameraId);

        const res = await fetch('api.php?action=search_visitors', {
          method: 'POST',
          body: fd
        });
        const data = await res.json();
        if (!data || !data.success) {
          if (grid) grid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #ef4444;">Gagal memproses pencarian.</div>';
          return;
        }

        const todayStr = (cachedAnalyticsData && cachedAnalyticsData.today) ? cachedAnalyticsData.today : null;
        renderVisitorCards(data.visitors || [], todayStr);
        renderAnalyticsTimeline(data.logs || []);

      } catch (err) {
        console.error('executeVisitorSearch error:', err);
      }
    }

    function resetVisitorFilters() {
      const kw = document.getElementById('investigation-keyword-input');
      if (kw) kw.value = '';
      const cat = document.getElementById('investigation-category-select');
      if (cat) cat.value = 'all';
      const dt = document.getElementById('investigation-date-select');
      if (dt) dt.value = 'today';
      const cam = document.getElementById('investigation-camera-select');
      if (cam) cam.value = '0';
      loadVisitorAnalytics();
    }

    async function openVisitorDossier(visitorId) {
      try {
        openModal('modalVisitorDossier');

        const nameEl = document.getElementById('dossier-name');
        const photoEl = document.getElementById('dossier-photo');
        const badgeEl = document.getElementById('dossier-badge');
        const idEl = document.getElementById('dossier-id');
        const firstSeenEl = document.getElementById('dossier-first-seen');
        const lastSeenEl = document.getElementById('dossier-last-seen');
        const totalVisitsEl = document.getElementById('dossier-total-visits');
        const notesEl = document.getElementById('dossier-notes');
        const dailyEl = document.getElementById('dossier-daily-breakdown');
        const timelineEl = document.getElementById('dossier-timeline-list');

        if (nameEl) nameEl.textContent = 'Memuat Dossier Investigasi...';
        if (dailyEl) dailyEl.innerHTML = '<div style="padding: 1rem; text-align: center; color: #94a3b8;"><i class="fas fa-spinner fa-spin mr-2"></i> Mengambil data kunjungan per tanggal...</div>';
        if (timelineEl) timelineEl.innerHTML = '<div style="padding: 1rem; text-align: center; color: #94a3b8;"><i class="fas fa-spinner fa-spin mr-2"></i> Mengambil bukti snapshot kejadian...</div>';

        const res = await fetch(`api.php?action=get_visitor_detail&visitor_id=${encodeURIComponent(visitorId)}`);
        const data = await res.json();
        if (!data || !data.success || !data.profile) {
          alert(data.message || 'Gagal memuat profil pelintas.');
          closeModal('modalVisitorDossier');
          return;
        }

        const p = data.profile;
        currentDossierProfile = p;

        if (nameEl) nameEl.textContent = p.name;
        if (idEl) idEl.textContent = p.id;
        if (firstSeenEl) firstSeenEl.textContent = p.first_seen || '-';
        if (lastSeenEl) lastSeenEl.textContent = p.last_seen || '-';
        if (totalVisitsEl) totalVisitsEl.textContent = `${p.total_visits || 1}x Melintas / Berkunjung`;
        if (notesEl) notesEl.textContent = p.notes || 'Tidak ada catatan investigasi khusus.';

        const cat = (p.category || 'stranger').toLowerCase();
        if (badgeEl) {
          if (cat === 'employee') badgeEl.className = 'badge-cat badge-employee';
          else if (cat === 'stranger') badgeEl.className = 'badge-cat badge-stranger';
          else if (cat === 'vip') badgeEl.className = 'badge-cat badge-vip';
          else if (cat === 'blacklist') badgeEl.className = 'badge-cat badge-blacklist';
          else badgeEl.className = 'badge-cat badge-guest';
          badgeEl.textContent = cat.toUpperCase();
        }

        if (photoEl) {
          if (p.photo && p.photo.trim() !== '') {
            const photoSrc = p.photo.startsWith('assets/') ? ('../' + p.photo) : (p.photo.startsWith('http') || p.photo.startsWith('data:') ? p.photo : ('../' + p.photo));
            photoEl.innerHTML = `<img src="${escapeHtml(photoSrc)}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.outerHTML='<div style=\\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1e293b;color:#94a3b8;\\'><i class=\\'fas fa-user\\'></i></div>'">`;
          } else {
            photoEl.innerHTML = `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #1e293b; color: #94a3b8; font-size: 2rem;"><i class="fas fa-user"></i></div>`;
          }
        }

        // Render Daily Breakdown Table
        const breakdown = data.daily_breakdown || [];
        if (breakdown.length === 0) {
          if (dailyEl) dailyEl.innerHTML = '<div style="padding: 1rem; text-align: center; color: #64748b;">Belum ada catatan kunjungan harian terperinci.</div>';
        } else {
          let tableHtml = `
            <div style="overflow-x: auto;">
              <table style="width: 100%; border-collapse: collapse; font-size: 0.78rem; text-align: left;">
                <thead>
                  <tr style="background: rgba(30, 41, 59, 0.9); border-bottom: 1px solid var(--border-color); color: #94a3b8;">
                    <th style="padding: 8px 12px;"><i class="fas fa-calendar-day mr-1"></i> Tanggal</th>
                    <th style="padding: 8px 12px; text-align: center;"><i class="fas fa-redo mr-1"></i> Frekuensi</th>
                    <th style="padding: 8px 12px;"><i class="fas fa-hourglass-start mr-1"></i> Kunjungan Pertama</th>
                    <th style="padding: 8px 12px;"><i class="fas fa-hourglass-end mr-1"></i> Kunjungan Terakhir</th>
                    <th style="padding: 8px 12px;"><i class="fas fa-video mr-1"></i> Kamera Terpantau</th>
                  </tr>
                </thead>
                <tbody>
          `;
          breakdown.forEach(b => {
            const cams = Array.isArray(b.cameras) && b.cameras.length > 0 ? b.cameras.join(', ') : 'Kamera CCTV';
            tableHtml += `
              <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                <td style="padding: 8px 12px; font-weight: 700; color: #fff;">${escapeHtml(b.date)}</td>
                <td style="padding: 8px 12px; text-align: center;"><span style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; padding: 2px 8px; border-radius: 9999px; font-weight: 700;">${b.count}x</span></td>
                <td style="padding: 8px 12px; color: #cbd5e1;">${escapeHtml(b.first_time || '-')}</td>
                <td style="padding: 8px 12px; color: #cbd5e1;">${escapeHtml(b.last_time || '-')}</td>
                <td style="padding: 8px 12px; color: #94a3b8;">${escapeHtml(cams)}</td>
              </tr>
            `;
          });
          tableHtml += '</tbody></table></div>';
          if (dailyEl) dailyEl.innerHTML = tableHtml;
        }

        // Render Evidence Timeline with Snapshots
        const timeline = data.timeline || [];
        if (timeline.length === 0) {
          if (timelineEl) timelineEl.innerHTML = '<div style="padding: 1rem; text-align: center; color: #64748b;">Belum ada bukti rekaman foto kejadian.</div>';
        } else {
          timelineEl.innerHTML = timeline.map(l => {
            let snapThumb = '';
            if (l.snapshot && l.snapshot.trim() !== '') {
              const snapSrc = l.snapshot.startsWith('assets/') ? ('../' + l.snapshot) : (l.snapshot.startsWith('http') || l.snapshot.startsWith('data:') ? l.snapshot : ('../' + l.snapshot));
              snapThumb = `
                <div style="width: 50px; height: 50px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0; background: #0f172a; cursor: pointer;" onclick="window.open('${escapeHtml(snapSrc)}', '_blank')" title="Klik untuk memperbesar bukti foto">
                  <img src="${escapeHtml(snapSrc)}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.outerHTML='<div style=\\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:0.8rem;\\'><i class=\\'fas fa-image\\'></i></div>'">
                </div>
              `;
            } else {
              snapThumb = `
                <div style="width: 50px; height: 50px; border-radius: 6px; background: #1e293b; display: flex; align-items: center; justify-content: center; color: #64748b; flex-shrink: 0;">
                  <i class="fas fa-camera"></i>
                </div>
              `;
            }
            const tStr = l.timestamp || `${l.date || ''} ${l.time || ''}`;
            return `
              <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                ${snapThumb}
                <div style="flex: 1; min-width: 0;">
                  <div style="font-weight: 700; font-size: 0.82rem; color: #fff;">
                    ${escapeHtml(l.details || l.label || 'Terdeteksi Melintas')}
                  </div>
                  <div style="font-size: 0.74rem; color: #94a3b8; margin-top: 2px;">
                    <i class="fas fa-clock text-info mr-1"></i> ${escapeHtml(tStr)} • <i class="fas fa-video text-info mr-1"></i> ${escapeHtml(l.camera_title || 'Kamera')}
                  </div>
                </div>
              </div>
            `;
          }).join('');
        }

      } catch (e) {
        console.error('openVisitorDossier error:', e);
        alert('Terjadi kesalahan saat membuka dossier pelintas.');
      }
    }

    function openRenameFromDossier() {
      if (!currentDossierProfile) return;
      openRenameVisitor(
        currentDossierProfile.id,
        currentDossierProfile.name,
        currentDossierProfile.category,
        currentDossierProfile.notes
      );
    }

    function openRenameVisitor(id, name, category, notes) {
      const idEl = document.getElementById('rename-visitor-id');
      const codeEl = document.getElementById('rename-visitor-code');
      const nameEl = document.getElementById('rename-visitor-name');
      const catEl = document.getElementById('rename-visitor-category');
      const notesEl = document.getElementById('rename-visitor-notes');

      if (idEl) idEl.value = id || '';
      if (codeEl) codeEl.value = id || '';
      if (nameEl) nameEl.value = name || '';
      if (catEl) catEl.value = category || 'guest';
      if (notesEl) notesEl.value = notes || '';

      openModal('modalRenameVisitor');
    }

    async function handleSaveVisitorProfile(event) {
      event.preventDefault();
      try {
        const visitorId = document.getElementById('rename-visitor-id')?.value || '';
        const name = document.getElementById('rename-visitor-name')?.value || '';
        const category = document.getElementById('rename-visitor-category')?.value || 'guest';
        const notes = document.getElementById('rename-visitor-notes')?.value || '';

        if (!visitorId || !name) {
          alert('ID Pengunjung dan Nama wajib diisi.');
          return;
        }

        const fd = new FormData();
        fd.append('visitor_id', visitorId);
        fd.append('name', name);
        fd.append('category', category);
        fd.append('notes', notes);

        const res = await fetch('api.php?action=update_visitor_profile', {
          method: 'POST',
          body: fd
        });
        const data = await res.json();
        if (data && data.success) {
          alert('✅ Profil pelintas berhasil diperbarui!');
          closeModal('modalRenameVisitor');
          loadVisitorAnalytics(true);
          if (currentDossierProfile && currentDossierProfile.id === visitorId) {
            openVisitorDossier(visitorId);
          }
        } else {
          alert(data.message || 'Gagal menyimpan profil.');
        }
      } catch (e) {
        console.error('handleSaveVisitorProfile error:', e);
        alert('Kesalahan koneksi saat menyimpan profil.');
      }
    }

    // Auto load on init if logged in
    <?php if ($isLoggedIn): ?>
      document.addEventListener('DOMContentLoaded', () => {
        loadCameras();
        if (typeof loadCountingLineConfig === 'function') {
          loadCountingLineConfig();
        }
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || window.location.hash.replace('#', '');
        if (tabParam === 'analytics') {
          switchMainTab('analytics');
        } else if (tabParam === 'ai') {
          switchMainTab('ai');
        }
      });
    <?php endif; ?>
  </script>
</body>
</html>
