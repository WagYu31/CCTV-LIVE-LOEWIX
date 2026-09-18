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
  <title>Loewix Face Detection - Desktop & LAN Edition</title>
  
  <!-- PWA Meta Tags -->
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#0284c7">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Loewix Face Detection">
  <link rel="icon" type="image/png" href="../assets/image/icon.png">

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- HLS.js for Live Stream Video Player -->
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

  <!-- AI Biometric Face Recognition Engines (Face-API 128D ResNet & MediaPipe 468 3D Mesh) -->
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.18.0/dist/tf.min.js"></script>
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
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
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

          <!-- Video & Canvas Screen with HUD Overlay -->
          <div class="ai-screen-container" id="ai-screen-box">
            <!-- HUD Overlays -->
            <div class="ai-screen-hud-tl" id="ai-screen-hud-cam-title">
              <i class="fas fa-video text-info"></i>
              <span id="ai-hud-cam-name">[CH 2] RTSP LOCAL STG</span>
            </div>
            <div class="ai-screen-hud-tr">
              <span class="status-indicator online" style="margin-right: 4px;"></span>
              <span>128D RESNET • 30 FPS</span>
            </div>

            <video id="ai-video-player" playsinline muted autoplay></video>
            <canvas id="ai-canvas-overlay"></canvas>
            
            <div id="ai-video-loader" class="player-loader" style="display: none;">
              <div class="spinner"></div>
              <div id="ai-loader-text">Menghubungkan Stream...</div>
            </div>

            <!-- Recognition Banner Popup Overlay -->
            <div id="ai-hud-banner" style="display: none; position: absolute; top: 48px; left: 14px; background: rgba(15, 23, 42, 0.95); border: 1.5px solid #38bdf8; border-radius: 12px; padding: 0.65rem 1rem; align-items: center; gap: 0.75rem; box-shadow: 0 8px 30px rgba(0,0,0,0.8); z-index: 10;">
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
    Loewix Face Detection &bull; PT. Loewix Indonesia &bull; Versi Desktop & Jaringan Lokal (Port 8088)
  </footer>

  <!-- Application Logic JavaScript -->
  <script>
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
    // MAIN APP NAVIGATION TABS (MANAJEMEN CCTV & AI FACE RECOGNITION)
    // =========================================================================
    function switchMainTab(tab) {
      const cctvPane = document.getElementById('view-cctv-pane');
      const aiPane = document.getElementById('view-ai-pane');
      const cctvBtn = document.getElementById('tab-btn-cctv');
      const aiBtn = document.getElementById('tab-btn-ai');

      if (tab === 'ai') {
        if (cctvPane) cctvPane.style.display = 'none';
        if (aiPane) aiPane.style.display = 'block';
        if (cctvBtn) cctvBtn.classList.remove('active');
        if (aiBtn) aiBtn.classList.add('active');
        initAIFaceSuite();
        if (typeof updateAICameraLists === 'function') {
          updateAICameraLists();
        }
      } else {
        if (aiPane) aiPane.style.display = 'none';
        if (cctvPane) cctvPane.style.display = 'block';
        if (aiBtn) aiBtn.classList.remove('active');
        if (cctvBtn) cctvBtn.classList.add('active');
        // Pause AI stream if user switches back to CCTV
        if (aiLiveVideo && !aiLiveVideo.paused) {
          aiLiveVideo.pause();
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
      '../assets/models',
      'assets/models',
      'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights',
      'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights'
    ];

    function resolveFacePhotoUrl(photo, id) {
      if (!photo) return '../assets/image/icon.png';
      if (photo.startsWith('data:') || photo.startsWith('blob:')) return photo;
      if (id) return `api.php?action=get_face_image&id=${id}`;
      return `../${photo.replace(/^\.\.\//, '').replace(/^\//, '')}`;
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
            <img class="ai-face-avatar" src="${escapeHtml(photoUrl)}" alt="${escapeHtml(f.name)}" onerror="this.src='../assets/image/icon.png'">
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
      const isVIP = (category || '').toLowerCase() === 'vip';
      const isStranger = (name || '').toUpperCase().includes('STRANGER');

      document.getElementById('ai-hud-name').textContent = name;
      document.getElementById('ai-hud-badge').textContent = isVIP ? 'VIP' : (isStranger ? 'STRANGER' : 'VERIFIED');
      document.getElementById('ai-hud-badge').style.color = isVIP ? '#34d399' : (isStranger ? '#f59e0b' : '#38bdf8');
      document.getElementById('ai-hud-sub').textContent = `${Math.round(confidence)}% Biometric Match • Akses Diizinkan`;

      banner.style.display = 'flex';
      setTimeout(() => {
        banner.style.display = 'none';
      }, 4000);
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

      // Update HUD Overlay & Footer
      const hudCam = document.getElementById('ai-hud-cam-name');
      if (hudCam) hudCam.textContent = 'WEBCAM LAPTOP';
      const footerCam = document.getElementById('ai-footer-cam-name');
      if (footerCam) footerCam.innerHTML = '<i class="fas fa-camera text-info mr-1"></i> Aktif: Webcam Laptop';
      const resTag = document.getElementById('ai-feed-resolution');
      if (resTag) resTag.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i> WEBCAM 720p';

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

      // Stop webcam if active
      if (aiWebcamStream) {
        aiWebcamStream.getTracks().forEach(t => t.stop());
        aiWebcamStream = null;
        video.srcObject = null;
      }

      const camIdx = (localCameras || []).findIndex(c => String(c.id) === String(camId));
      const cam = (localCameras || [])[camIdx] || (localCameras || []).find(c => String(c.id) === String(camId));
      currentAICamera = cam || { id: camId, title: 'Kamera CCTV ' + camId };

      if (select) select.value = String(camId);

      // Update HUD Overlay & Footer
      const chNum = camIdx >= 0 ? (camIdx + 1) : camId;
      const hudCam = document.getElementById('ai-hud-cam-name');
      if (hudCam) hudCam.textContent = `[CH ${chNum}] ${(currentAICamera.title || '').toUpperCase()}`;
      const footerCam = document.getElementById('ai-footer-cam-name');
      if (footerCam) footerCam.innerHTML = `<i class="fas fa-video text-info mr-1"></i> Aktif: [CH ${chNum}] ${escapeHtml(currentAICamera.title || '')}`;
      const resTag = document.getElementById('ai-feed-resolution');
      if (resTag) resTag.innerHTML = '<i class="fas fa-signal text-success mr-1"></i> RTSP 1080p';

      if (typeof updateAICameraLists === 'function') updateAICameraLists();

      if (statusLabel) statusLabel.innerHTML = `<span style="color: #38bdf8;">${escapeHtml(currentAICamera.title)}</span>`;
      if (loader) {
        loader.style.display = 'flex';
        document.getElementById('ai-loader-text').textContent = `Menghubungkan Stream ${currentAICamera.title}...`;
      }

      let streamUrl = cam ? (cam.hls_url || '') : '';
      if (!streamUrl && cam && cam.streamPath) {
        streamUrl = `https://stream.loewixcctv.com/${cam.streamPath}/index.m3u8`;
      }

      if (aiHlsInstance) {
        aiHlsInstance.destroy();
        aiHlsInstance = null;
      }

      if (typeof Hls !== 'undefined' && Hls.isSupported() && streamUrl.includes('.m3u8')) {
        aiHlsInstance = new Hls({ lowLatencyMode: true, maxBufferLength: 4 });
        aiHlsInstance.loadSource(streamUrl);
        aiHlsInstance.attachMedia(video);
        aiHlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
          if (loader) loader.style.display = 'none';
          video.play().catch(() => {});
          startFaceAPIDetectionLoop();
        });
        aiHlsInstance.on(Hls.Events.ERROR, (event, data) => {
          if (data.fatal && loader) {
            loader.innerHTML = '<div style="color: #f87171;"><i class="fas fa-exclamation-circle"></i> Menunggu feed RTSP dari kamera...</div>';
          }
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

    function drawSurveillanceFaceReticle(ctx, x, y, w, h, label, isVIP) {
      ctx.save();
      drawSnugBrackets(ctx, x, y, w, h, label, isVIP);
      ctx.restore();
    }

    function drawSnugBrackets(ctx, x, y, w, h, label, isVIP) {
      const isStranger = (label || '').toUpperCase().includes('STRANGER');
      const strokeColor = isVIP ? '#10b981' : (isStranger ? '#ffd700' : '#00f0ff');
      const bracketLen = Math.min(24, w * 0.25);

      ctx.strokeStyle = strokeColor;
      ctx.lineWidth = 3;
      ctx.shadowColor = strokeColor;
      ctx.shadowBlur = 8;
      ctx.lineCap = 'round';

      // Top-Left
      ctx.beginPath();
      ctx.moveTo(x, y + bracketLen);
      ctx.lineTo(x, y);
      ctx.lineTo(x + bracketLen, y);
      ctx.stroke();

      // Top-Right
      ctx.beginPath();
      ctx.moveTo(x + w - bracketLen, y);
      ctx.lineTo(x + w, y);
      ctx.lineTo(x + w, y + bracketLen);
      ctx.stroke();

      // Bottom-Left
      ctx.beginPath();
      ctx.moveTo(x, y + h - bracketLen);
      ctx.lineTo(x, y + h);
      ctx.lineTo(x + bracketLen, y + h);
      ctx.stroke();

      // Bottom-Right
      ctx.beginPath();
      ctx.moveTo(x + w - bracketLen, y + h);
      ctx.lineTo(x + w, y + h);
      ctx.lineTo(x + w, y + h - bracketLen);
      ctx.stroke();

      // Label Tag Banner
      ctx.font = 'bold 12px Inter, sans-serif';
      const textWidth = ctx.measureText(label).width;
      const tagBg = isVIP ? 'rgba(16, 185, 129, 0.9)' : (isStranger ? 'rgba(245, 158, 11, 0.9)' : 'rgba(2, 132, 199, 0.9)');
      ctx.fillStyle = tagBg;
      ctx.beginPath();
      ctx.roundRect ? ctx.roundRect(x, y - 24, textWidth + 16, 20, 4) : ctx.rect(x, y - 24, textWidth + 16, 20);
      ctx.fill();

      ctx.fillStyle = '#ffffff';
      ctx.shadowBlur = 0;
      ctx.fillText(label, x + 8, y - 10);
    }

    // =========================================================================
    // REAL-TIME DETECTION LOOP (FACE-API.JS)
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
          if (canvas.width !== video.clientWidth || canvas.height !== video.clientHeight) {
            canvas.width = video.clientWidth;
            canvas.height = video.clientHeight;
          }

          const ctx = canvas.getContext('2d');
          ctx.clearRect(0, 0, canvas.width, canvas.height);

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

                      // Throttle log entry per person (once every 8 seconds)
                      const now = Date.now();
                      const lastLog = lastLoggedPersonTime[match.label] || 0;
                      if (now - lastLog > 8000) {
                        lastLoggedPersonTime[match.label] = now;
                        appendRealtimeAILog(matchedFace ? matchedFace.name : match.label, category, confidence);
                      }
                    }
                  }

                  // 3D Mesh on webcam vs Reticle on CCTV stream
                  if (currentAICamera.id === 'webcam') {
                    const mappedPts = resolveBiometricMeshNodes(det.landmarks ? det.landmarks.positions : null, x, y, w, h);
                    drawBiometricFacialMesh(ctx, x, y, w, h, mappedPts, label, isVIP);
                  } else {
                    drawSurveillanceFaceReticle(ctx, x, y, w, h, label, isVIP);
                  }
                }
              }
            } catch (errDet) {
              console.warn('Detection frame warning:', errDet);
            } finally {
              isFaceAPIDetecting = false;
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

    // Auto load on init if logged in
    <?php if ($isLoggedIn): ?>
      document.addEventListener('DOMContentLoaded', () => {
        loadCameras();
      });
    <?php endif; ?>
  </script>
</body>
</html>
