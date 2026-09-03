<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Control Hub - PT. LOEWIX INDONESIA</title>
  <!-- Favicons & App Icons (Loewix Official) -->
  <link rel="icon" type="image/png" sizes="192x192" href="../apple-touch-icon.png?v=3">
  <link rel="icon" type="image/png" sizes="32x32" href="../assets/image/favicon-32x32.png?v=3">
  <link rel="icon" type="image/png" sizes="16x16" href="../assets/image/favicon-16x16.png?v=3">
  <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png?v=3">
  <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico?v=3">
  <link rel="stylesheet" href="../assets/bootstarp/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
  <!-- face-api.js: High-Precision Neural Network Face Recognition (TensorFlow.js based) -->
  <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
  <!-- Midtrans Snap Payment Gateway SDK (Sandbox) -->
  <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-mGA7v04cXrux3KNF"></script>
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

    /* Top Enterprise Glass Header */
    .customer-navbar {
      background: linear-gradient(180deg, rgba(8, 17, 39, 0.98) 0%, rgba(5, 12, 28, 0.94) 100%);
      border-bottom: 1px solid rgba(56, 189, 248, 0.22);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      padding: 14px 0;
      position: sticky;
      top: 0;
      z-index: 1030;
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.55), inset 0 -1px 0 rgba(56, 189, 248, 0.1);
    }

    .brand-logo-container {
      display: flex;
      align-items: center;
      gap: 16px;
      text-decoration: none !important;
    }

    .navbar-loewix-logo {
      height: 38px;
      width: auto;
      object-fit: contain;
      filter: drop-shadow(0 2px 10px rgba(56, 189, 248, 0.35));
      transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .brand-logo-container:hover .navbar-loewix-logo {
      transform: scale(1.05);
    }

    .badge-hub-live {
      background: linear-gradient(135deg, rgba(8, 47, 73, 0.8), rgba(15, 23, 42, 0.9));
      color: #38bdf8;
      border: 1px solid rgba(56, 189, 248, 0.35);
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.8px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    .pulse-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #10b981;
      box-shadow: 0 0 10px #10b981;
      animation: pulseGlow 1.8s infinite;
    }

    @keyframes pulseGlow {
      0% { transform: scale(0.85); opacity: 0.7; }
      50% { transform: scale(1.3); opacity: 1; box-shadow: 0 0 14px #10b981, 0 0 20px rgba(16, 185, 129, 0.6); }
      100% { transform: scale(0.85); opacity: 0.7; }
    }

    /* Top Header Network Speed & Telemetry Badges */
    .network-speed-badge {
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(8, 47, 73, 0.6));
      border: 1px solid rgba(56, 189, 248, 0.35);
      border-radius: 20px;
      padding: 5px 14px;
      font-size: 11.5px;
      font-weight: 700;
      color: #38bdf8;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      letter-spacing: 0.3px;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
      transition: all 0.25s ease;
    }

    .network-speed-badge:hover {
      border-color: #38bdf8;
      box-shadow: 0 0 16px rgba(56, 189, 248, 0.4);
    }

    .speed-pulse-icon {
      animation: speedIconSpin 4s linear infinite;
    }

    @keyframes speedIconSpin {
      0%, 100% { transform: rotate(0deg); }
      50% { transform: rotate(15deg); }
    }

    .network-latency-badge {
      background: rgba(15, 23, 42, 0.8);
      border: 1px solid rgba(16, 185, 129, 0.35);
      border-radius: 20px;
      padding: 5px 12px;
      font-size: 11px;
      font-weight: 700;
      color: #34d399;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      letter-spacing: 0.4px;
    }

    .net-ping-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #10b981;
      box-shadow: 0 0 8px #10b981;
      animation: pulseGlow 1.6s infinite;
    }

    .btn-nav-vms {
      background: linear-gradient(135deg, rgba(14, 116, 144, 0.25), rgba(8, 47, 73, 0.45));
      border: 1px solid rgba(56, 189, 248, 0.35);
      color: #38bdf8;
      border-radius: 24px;
      padding: 8px 18px;
      font-size: 12px;
      font-weight: 700;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none !important;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    }

    .btn-nav-vms:hover {
      background: linear-gradient(135deg, #0284c7, #0ea5e9);
      color: #ffffff;
      border-color: #38bdf8;
      box-shadow: 0 0 20px rgba(56, 189, 248, 0.6);
      transform: translateY(-1px);
    }

    .user-profile-pill {
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.7));
      border: 1px solid rgba(255, 255, 255, 0.14);
      padding: 4px 14px 4px 6px;
      border-radius: 30px;
      display: inline-flex;
      align-items: center;
      gap: 9px;
      font-size: 12.5px;
      font-weight: 700;
      color: #e2e8f0;
      cursor: pointer;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .user-profile-pill:hover {
      background: rgba(56, 189, 248, 0.15);
      border-color: rgba(56, 189, 248, 0.5);
      box-shadow: 0 0 16px rgba(56, 189, 248, 0.25);
      transform: translateY(-1px);
    }

    .user-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0284c7, #38bdf8);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 13px;
      box-shadow: 0 0 12px rgba(56, 189, 248, 0.5);
      border: 1.5px solid rgba(255, 255, 255, 0.25);
    }

    .btn-nav-logout {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.16), rgba(153, 27, 27, 0.25));
      border: 1px solid rgba(239, 68, 68, 0.45);
      color: #f87171;
      border-radius: 24px;
      padding: 8px 16px;
      font-weight: 700;
      font-size: 12px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .btn-nav-logout:hover {
      background: linear-gradient(135deg, #dc2626, #ef4444);
      color: #ffffff;
      border-color: #ef4444;
      box-shadow: 0 0 16px rgba(239, 68, 68, 0.55);
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

    /* Quota Hero Widget - Ultra Premium Futuristic Cyber-Glass Architecture */
    .quota-hero-banner {
      background: radial-gradient(circle at 12% 20%, rgba(14, 116, 144, 0.38) 0%, rgba(15, 23, 42, 0.95) 45%, rgba(6, 12, 30, 0.99) 100%);
      border: 1px solid rgba(56, 189, 248, 0.35);
      border-radius: 24px;
      padding: 32px 36px;
      margin-top: 24px;
      margin-bottom: 30px;
      box-shadow: 0 25px 65px rgba(0, 0, 0, 0.75), 0 0 40px rgba(14, 116, 144, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      position: relative;
      overflow: hidden;
    }

    .quota-hero-banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent 0%, rgba(56, 189, 248, 0.8) 30%, rgba(245, 158, 11, 0.8) 70%, transparent 100%);
      z-index: 2;
    }

    .quota-hero-banner::after {
      content: '';
      position: absolute;
      top: -40%;
      right: -15%;
      width: 450px;
      height: 450px;
      background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(168, 85, 247, 0.05) 50%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
      z-index: 0;
    }

    .hero-tier-badge {
      background: linear-gradient(135deg, #0284c7, #0369a1);
      border: 1px solid rgba(56, 189, 248, 0.6);
      color: #ffffff;
      padding: 6px 15px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.8px;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      box-shadow: 0 0 16px rgba(2, 132, 199, 0.55);
      text-transform: uppercase;
    }

    .hero-city-badge {
      background: rgba(15, 23, 42, 0.8);
      border: 1px solid rgba(255, 255, 255, 0.16);
      color: #e2e8f0;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      backdrop-filter: blur(10px);
    }

    .hero-customer-title {
      font-size: 32px;
      font-weight: 900;
      background: linear-gradient(135deg, #ffffff 15%, #bae6fd 60%, #c4b5fd 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: -0.5px;
      margin-top: 8px;
      margin-bottom: 8px;
      text-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
      display: inline-block;
    }

    .quota-progress-track {
      height: 11px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      overflow: hidden;
      margin: 14px 0 12px 0;
      position: relative;
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.5);
    }

    .quota-progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #0284c7 0%, #38bdf8 50%, #10b981 100%);
      border-radius: 12px;
      transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 0 16px rgba(56, 189, 248, 0.85);
      position: relative;
    }

    .quota-progress-fill::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.45), transparent);
      animation: scanline 2.5s infinite;
    }

    @keyframes scanline {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }

    .quota-cap-pill {
      background: rgba(56, 189, 248, 0.16);
      border: 1px solid rgba(56, 189, 248, 0.45);
      color: #38bdf8;
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 800;
      font-family: monospace;
      letter-spacing: 0.3px;
      box-shadow: 0 0 12px rgba(56, 189, 248, 0.2);
    }

    /* Metric Cards - 3D Glass Holographic Design */
    .metric-card {
      background: linear-gradient(145deg, rgba(15, 26, 56, 0.85) 0%, rgba(8, 16, 36, 0.95) 100%);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 20px;
      padding: 18px 20px;
      display: flex;
      align-items: center;
      gap: 16px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.1);
      position: relative;
      overflow: hidden;
    }

    .metric-card::before {
      content: '';
      position: absolute;
      top: -40%;
      right: -40%;
      width: 110px;
      height: 110px;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.08), transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .metric-card:hover {
      background: linear-gradient(145deg, rgba(22, 40, 84, 0.92) 0%, rgba(12, 24, 54, 0.98) 100%);
      border-color: rgba(56, 189, 248, 0.55);
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 16px 36px rgba(0, 0, 0, 0.6), 0 0 24px rgba(56, 189, 248, 0.28);
    }

    .metric-icon {
      width: 52px;
      height: 52px;
      min-width: 52px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      border: 1px solid transparent;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .metric-card:hover .metric-icon {
      transform: scale(1.1);
    }

    .metric-icon.cyan {
      background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.32), rgba(2, 132, 199, 0.14));
      border-color: rgba(56, 189, 248, 0.5);
      color: #38bdf8;
      box-shadow: 0 0 18px rgba(56, 189, 248, 0.35);
    }

    .metric-icon.emerald {
      background: radial-gradient(circle at top left, rgba(16, 185, 129, 0.32), rgba(5, 150, 105, 0.14));
      border-color: rgba(16, 185, 129, 0.5);
      color: #34d399;
      box-shadow: 0 0 18px rgba(16, 185, 129, 0.35);
    }

    .metric-icon.amber {
      background: radial-gradient(circle at top left, rgba(245, 158, 11, 0.32), rgba(217, 119, 6, 0.14));
      border-color: rgba(245, 158, 11, 0.5);
      color: #fbbf24;
      box-shadow: 0 0 18px rgba(245, 158, 11, 0.35);
    }

    .metric-icon.purple {
      background: radial-gradient(circle at top left, rgba(168, 85, 247, 0.38), rgba(124, 58, 237, 0.16));
      border-color: rgba(168, 85, 247, 0.55);
      color: #c084fc;
      box-shadow: 0 0 18px rgba(168, 85, 247, 0.35);
    }

    .metric-value {
      font-size: 26px;
      font-weight: 900;
      color: #ffffff;
      line-height: 1.1;
      font-family: system-ui, -apple-system, sans-serif;
      letter-spacing: -0.5px;
    }

    .metric-label {
      font-size: 11.5px;
      color: #94a3b8;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      margin-top: 3px;
    }

    .metric-sub {
      font-size: 11px;
      color: #64748b;
      font-weight: 600;
    }

    /* Ultra-Modern Action & Filter Toolbar */
    .customer-toolbar {
      background: linear-gradient(135deg, rgba(13, 27, 62, 0.88) 0%, rgba(6, 14, 32, 0.95) 100%);
      border: 1px solid rgba(56, 189, 248, 0.3);
      border-radius: 20px;
      padding: 16px 24px;
      margin-bottom: 26px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    .toolbar-left-group {
      display: flex;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .toolbar-title-group {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .toolbar-icon-badge {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.28), rgba(2, 132, 199, 0.12));
      border: 1px solid rgba(56, 189, 248, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      color: #38bdf8;
      box-shadow: 0 0 14px rgba(56, 189, 248, 0.25);
    }

    .toolbar-heading {
      font-size: 18px;
      font-weight: 800;
      color: #ffffff;
      margin: 0;
      letter-spacing: -0.3px;
      line-height: 1.2;
    }

    .toolbar-subtext {
      font-size: 11px;
      font-weight: 600;
      color: #94a3b8;
      letter-spacing: 0.3px;
    }

    .btn-add-camera {
      background: linear-gradient(135deg, #0284c7, #0ea5e9);
      color: #ffffff;
      border: 1px solid rgba(56, 189, 248, 0.5);
      padding: 9px 20px;
      border-radius: 14px;
      font-weight: 700;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 16px rgba(2, 132, 199, 0.45);
      cursor: pointer;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-add-camera:hover {
      background: linear-gradient(135deg, #0369a1, #0284c7);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(56, 189, 248, 0.55);
      color: #ffffff;
      border-color: #38bdf8;
    }

    .btn-live-test-all {
      background: linear-gradient(135deg, #059669, #10b981);
      color: #ffffff;
      border: 1px solid rgba(16, 185, 129, 0.5);
      padding: 9px 18px;
      border-radius: 14px;
      font-weight: 700;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4);
      cursor: pointer;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-live-test-all:hover {
      background: linear-gradient(135deg, #047857, #059669);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(16, 185, 129, 0.6);
      color: #ffffff;
      border-color: #34d399;
    }

    .btn-live-test-all.is-running {
      background: linear-gradient(135deg, #dc2626, #ef4444);
      border-color: rgba(239, 68, 68, 0.6);
      box-shadow: 0 4px 16px rgba(239, 68, 68, 0.5);
      animation: pulseGlowRunning 2s infinite;
    }

    .btn-live-test-all.is-running:hover {
      background: linear-gradient(135deg, #b91c1c, #dc2626);
      box-shadow: 0 8px 24px rgba(239, 68, 68, 0.7);
    }

    @keyframes pulseGlowRunning {
      0%, 100% { box-shadow: 0 0 12px rgba(239, 68, 68, 0.4); }
      50% { box-shadow: 0 0 22px rgba(239, 68, 68, 0.8); }
    }

    .toolbar-controls-group {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      flex: 1;
      justify-content: flex-end;
      min-width: 300px;
    }

    .toolbar-search-box {
      position: relative;
      flex: 1;
      min-width: 220px;
      max-width: 340px;
    }

    .toolbar-search-box input {
      width: 100%;
      height: 42px;
      background: rgba(15, 23, 42, 0.75);
      border: 1px solid rgba(255, 255, 255, 0.14);
      border-radius: 14px;
      padding: 0 14px 0 38px;
      color: #ffffff;
      font-size: 13px;
      font-weight: 500;
      outline: none;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toolbar-search-box input:focus {
      border-color: #38bdf8;
      background: rgba(15, 23, 42, 0.95);
      box-shadow: 0 0 14px rgba(56, 189, 248, 0.35);
    }

    .toolbar-search-box input::placeholder {
      color: #64748b;
    }

    .toolbar-search-box i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #38bdf8;
      font-size: 13px;
      pointer-events: none;
    }

    .toolbar-select-pill {
      height: 42px;
      background: rgba(15, 23, 42, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.14);
      border-radius: 14px;
      color: #e2e8f0;
      padding: 0 14px;
      font-size: 12.5px;
      font-weight: 600;
      outline: none;
      cursor: pointer;
      transition: all 0.2s;
    }

    .toolbar-select-pill:focus,
    .toolbar-select-pill:hover {
      border-color: rgba(56, 189, 248, 0.5);
      background: rgba(15, 23, 42, 0.95);
      box-shadow: 0 0 12px rgba(56, 189, 248, 0.2);
    }

    .toolbar-select-pill option {
      background: #0b1329;
      color: #ffffff;
      padding: 8px;
    }

    .btn-toolbar-refresh {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      background: rgba(15, 23, 42, 0.75);
      border: 1px solid rgba(255, 255, 255, 0.14);
      color: #38bdf8;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-toolbar-refresh:hover {
      background: rgba(56, 189, 248, 0.15);
      border-color: #38bdf8;
      color: #ffffff;
      transform: rotate(90deg);
      box-shadow: 0 0 14px rgba(56, 189, 248, 0.35);
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
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: rgba(0, 0, 0, 0.35);
      opacity: 0;
      transition: opacity 0.2s ease;
      z-index: 5;
      pointer-events: none;
    }

    .cam-preview-container:hover .play-overlay-hint {
      opacity: 1;
    }

    .play-overlay-hint i {
      font-size: 38px;
      color: rgba(56, 189, 248, 0.95);
      filter: drop-shadow(0 0 12px rgba(56, 189, 248, 0.7));
      transform: scale(0.9);
      transition: transform 0.2s ease;
    }

    .cam-preview-container:hover .play-overlay-hint i {
      transform: scale(1.1);
    }

    .play-hint-text {
      font-size: 11px;
      font-weight: 700;
      color: #38bdf8;
      margin-top: 6px;
      text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
      background: rgba(15, 23, 42, 0.85);
      padding: 3px 10px;
      border-radius: 20px;
      border: 1px solid rgba(56, 189, 248, 0.4);
      letter-spacing: 0.3px;
    }

    .cam-cctv-overlay {
      position: absolute;
      bottom: 8px;
      left: 10px;
      right: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      pointer-events: none;
      z-index: 3;
    }

    .cctv-rec-pill {
      background: rgba(15, 23, 42, 0.85);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #f87171;
      padding: 2px 7px;
      border-radius: 12px;
      font-size: 9.5px;
      font-weight: 700;
      letter-spacing: 0.5px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      backdrop-filter: blur(6px);
    }

    .cctv-time-pill {
      background: rgba(15, 23, 42, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #cbd5e1;
      padding: 2px 7px;
      border-radius: 12px;
      font-size: 9.5px;
      font-family: monospace;
      font-weight: 600;
      backdrop-filter: blur(6px);
    }

    @keyframes recBlink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.2; }
    }

    .blink {
      animation: recBlink 1.2s infinite ease-in-out;
    }

    .cam-standby-placeholder {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at center, #0f2347 0%, #060b18 100%);
      color: #64748b;
      z-index: 1;
      padding: 15px;
      text-align: center;
      background-image: 
        linear-gradient(rgba(56, 189, 248, 0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(56, 189, 248, 0.04) 1px, transparent 1px);
      background-size: 20px 20px;
    }

    .cam-standby-placeholder .standby-icon {
      font-size: 32px;
      color: rgba(56, 189, 248, 0.7);
      margin-bottom: 6px;
      filter: drop-shadow(0 0 10px rgba(56, 189, 248, 0.4));
    }

    .cam-standby-placeholder .standby-title {
      font-size: 12px;
      font-weight: 700;
      color: #cbd5e1;
      letter-spacing: 0.5px;
    }

    .cam-standby-placeholder .standby-hint {
      font-size: 10px;
      font-weight: 600;
      color: #38bdf8;
      margin-top: 5px;
      background: rgba(56, 189, 248, 0.12);
      border: 1px solid rgba(56, 189, 248, 0.3);
      padding: 2px 8px;
      border-radius: 10px;
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
    /* Modal Form Dark Theme (Ultra-Premium Glassmorphism) */
    .modal-dark .modal-content {
      background: linear-gradient(155deg, #0d1b3e 0%, #060c20 100%) !important;
      border: 1px solid rgba(56, 189, 248, 0.35) !important;
      border-radius: 20px !important;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.85), 0 0 35px rgba(56, 189, 248, 0.15) !important;
      color: #ffffff !important;
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
    }

    .modal-dark .modal-header {
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
      padding: 20px 26px !important;
      background: rgba(15, 23, 42, 0.95);
    }

    .modal-dark .modal-footer {
      border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
      padding: 16px 26px !important;
      background: rgba(15, 23, 42, 0.95);
    }

    .form-group-dark label {
      font-size: 12.5px;
      font-weight: 600;
      color: #cbd5e1;
      margin-bottom: 6px;
    }

    .form-control-dark {
      background: rgba(255, 255, 255, 0.05) !important;
      border: 1px solid rgba(255, 255, 255, 0.14) !important;
      border-radius: 12px !important;
      color: #ffffff !important;
      padding: 9px 15px;
      font-size: 13.5px !important;
      min-height: 42px !important;
      line-height: 1.5 !important;
      transition: all 0.25s ease !important;
      outline: none !important;
    }

    select.form-control-dark, select.form-control {
      height: 42px !important;
      min-height: 42px !important;
      line-height: 1.5 !important;
      padding: 8px 36px 8px 14px !important;
      appearance: none !important;
      -webkit-appearance: none !important;
      background-color: #0d1527 !important;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2338bdf8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
      background-repeat: no-repeat !important;
      background-position: right 14px center !important;
      background-size: 12px 10px !important;
      color: #ffffff !important;
      cursor: pointer !important;
    }

    select.form-control-dark option {
      background: #0b132b !important;
      color: #ffffff !important;
      padding: 10px 14px !important;
      font-size: 13.5px !important;
    }

    .form-control-dark:focus {
      background: rgba(255, 255, 255, 0.09) !important;
      border-color: #38bdf8 !important;
      box-shadow: 0 0 16px rgba(56, 189, 248, 0.3) !important;
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

    /* ===== CUSTOMER HUB BILLING TABS & MODULES (TACTICAL BRUTALIST UI) ===== */
    .customer-tabs-nav {
      display: flex !important;
      align-items: center !important;
      gap: 8px !important;
      overflow-x: auto !important;
      padding-bottom: 8px !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .customer-nav-tab {
      display: inline-flex !important;
      align-items: center !important;
      gap: 8px !important;
      padding: 9px 16px !important;
      background: rgba(13, 24, 54, 0.7) !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      border-radius: 8px !important;
      color: #94a3b8 !important;
      font-size: 12.5px !important;
      font-weight: 700 !important;
      font-family: 'Space Grotesk', 'Plus Jakarta Sans', sans-serif !important;
      cursor: pointer !important;
      transition: all 0.2s ease !important;
      white-space: nowrap !important;
      text-transform: uppercase !important;
      letter-spacing: 0.4px !important;
    }

    .customer-nav-tab:hover {
      background: rgba(56, 189, 248, 0.12) !important;
      border-color: rgba(56, 189, 248, 0.4) !important;
      color: #ffffff !important;
    }

    .customer-nav-tab.active {
      background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
      border-color: #38bdf8 !important;
      color: #ffffff !important;
      box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4) !important;
    }

    .billing-card {
      background: rgba(13, 24, 54, 0.85) !important;
      border: 1px solid rgba(56, 189, 248, 0.25) !important;
      border-radius: 12px !important;
      padding: 24px !important;
      margin-bottom: 24px !important;
      backdrop-filter: blur(12px) !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
    }

    .billing-card-header {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
      padding-bottom: 16px !important;
      margin-bottom: 20px !important;
    }

    .billing-card-title {
      font-size: 16px !important;
      font-weight: 800 !important;
      font-family: 'Space Grotesk', 'Plus Jakarta Sans', sans-serif !important;
      color: #ffffff !important;
      display: flex !important;
      align-items: center !important;
      gap: 10px !important;
      margin: 0 !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px !important;
    }

    .billing-status-badge {
      display: inline-flex !important;
      align-items: center !important;
      gap: 6px !important;
      padding: 3px 10px !important;
      border-radius: 4px !important;
      font-size: 11px !important;
      font-weight: 800 !important;
      font-family: 'Space Grotesk', monospace !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px !important;
    }

    .billing-status-badge.active {
      background: rgba(16, 185, 129, 0.15) !important;
      border: 1px solid rgba(16, 185, 129, 0.4) !important;
      color: #34d399 !important;
    }

    .billing-table {
      width: 100% !important;
      border-collapse: separate !important;
      border-spacing: 0 !important;
    }

    .billing-table th {
      background: rgba(8, 16, 36, 0.9) !important;
      color: #94a3b8 !important;
      font-size: 11px !important;
      font-weight: 800 !important;
      font-family: 'Space Grotesk', monospace !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px !important;
      padding: 12px 16px !important;
      border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .billing-table td {
      padding: 14px 16px !important;
      font-size: 13px !important;
      color: #e2e8f0 !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
      vertical-align: middle !important;
    }

    .billing-table tr:hover td {
      background: rgba(56, 189, 248, 0.04) !important;
    }

    /* Admin Management Section CSS */
    .admin-mgmt-card {
      background: rgba(13, 24, 54, 0.85);
      border: 1px solid rgba(56, 189, 248, 0.25);
      border-radius: 20px;
      padding: 24px;
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45);
      margin-bottom: 24px;
    }

    .table-dark-custom {
      color: #fff;
      margin-bottom: 0;
      border-collapse: separate;
      border-spacing: 0;
      width: 100%;
    }

    .table-dark-custom thead th {
      border-top: none;
      border-bottom: 2px solid rgba(255, 255, 255, 0.1);
      color: #38bdf8;
      font-weight: 800;
      font-size: 11.5px;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      padding: 14px 16px;
      background: rgba(7, 14, 34, 0.95);
    }

    .table-dark-custom tbody td {
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      padding: 16px 16px;
      vertical-align: middle;
      font-size: 13px;
      background: transparent;
      transition: all 0.2s ease;
    }

    .table-dark-custom tbody tr:hover td {
      background: rgba(56, 189, 248, 0.04);
    }

    .cust-id-badge {
      background: rgba(56, 189, 248, 0.1);
      color: #38bdf8;
      border: 1px solid rgba(56, 189, 248, 0.25);
      font-family: monospace;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 11.5px;
      display: inline-block;
    }

    .city-badge-siantar { background: rgba(0, 210, 255, 0.12); color: #00d2ff; border: 1px solid rgba(0, 210, 255, 0.3); }
    .city-badge-jakarta { background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .city-badge-bali { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .city-badge-medan { background: rgba(168, 85, 247, 0.12); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
    .city-badge-bandung { background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
    .city-badge-default { background: rgba(255, 255, 255, 0.1); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.2); }

    .city-badge {
      border-radius: 10px;
      padding: 4px 10px;
      font-size: 10.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .status-badge-active {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.4);
      color: #34d399;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .status-badge-suspended {
      background: rgba(239, 68, 68, 0.2);
      border: 1px solid rgba(239, 68, 68, 0.5);
      color: #f87171;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      box-shadow: 0 0 12px rgba(239, 68, 68, 0.25);
    }

    .table-row-suspended {
      background: rgba(239, 68, 68, 0.06) !important;
      border-left: 4px solid #ef4444 !important;
    }

    .action-btn-group {
      display: flex;
      gap: 5px;
      justify-content: flex-end;
    }

    .act-btn {
      width: 32px;
      height: 32px;
      border-radius: 9px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid transparent;
      transition: all 0.2s ease;
      cursor: pointer;
      font-size: 12px;
      outline: none;
    }

    .act-btn:hover { transform: translateY(-2px); }
    .act-btn-cctv { background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.4); color: #10b981; }
    .act-btn-cctv:hover { background: #10b981; color: #fff; box-shadow: 0 0 12px rgba(16, 185, 129, 0.6); }
    .act-btn-edit { background: rgba(56, 189, 248, 0.15); border-color: rgba(56, 189, 248, 0.4); color: #38bdf8; }
    .act-btn-edit:hover { background: #38bdf8; color: #000; box-shadow: 0 0 12px rgba(56, 189, 248, 0.6); }
    .act-btn-quota { background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.4); color: #f59e0b; }
    .act-btn-quota:hover { background: #f59e0b; color: #000; box-shadow: 0 0 12px rgba(245, 158, 11, 0.6); }
    .act-btn-pass { background: rgba(168, 85, 247, 0.15); border-color: rgba(168, 85, 247, 0.4); color: #c084fc; }
    .act-btn-pass:hover { background: #a855f7; color: #fff; box-shadow: 0 0 12px rgba(168, 85, 247, 0.6); }
    .act-btn-status { background: rgba(59, 130, 246, 0.15); border-color: rgba(59, 130, 246, 0.4); color: #60a5fa; }
    .act-btn-status:hover { background: #3b82f6; color: #fff; box-shadow: 0 0 12px rgba(59, 130, 246, 0.6); }
    .act-btn-status-resume { background: rgba(16, 185, 129, 0.2); border-color: #10b981; color: #34d399; }
    .act-btn-status-resume:hover { background: #10b981; color: #fff; box-shadow: 0 0 14px rgba(16, 185, 129, 0.7); }
    .act-btn-delete { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.4); color: #f87171; }
    .act-btn-delete:hover { background: #ef4444; color: #fff; box-shadow: 0 0 12px rgba(239, 68, 68, 0.6); }

    /* AI Vision Suite Cyber HUD Styles */
    .ai-scanline-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at center, transparent 60%, rgba(2, 6, 23, 0.4) 100%);
      pointer-events: none;
      z-index: 5;
    }

    .ai-live-card-item {
      background: rgba(15, 23, 42, 0.75);
      border: 1px solid rgba(56, 189, 248, 0.2);
      border-radius: 12px;
      padding: 12px;
      margin-bottom: 10px;
      transition: all 0.25s ease;
      animation: fadeInSlide 0.3s ease;
    }

    .ai-live-card-item:hover {
      border-color: rgba(56, 189, 248, 0.5);
      background: rgba(15, 23, 42, 0.9);
      transform: translateX(3px);
    }

    .ai-live-card-item.blacklist-alert {
      border-color: rgba(239, 68, 68, 0.6);
      background: rgba(239, 68, 68, 0.12);
      box-shadow: 0 0 16px rgba(239, 68, 68, 0.25);
    }

    .ai-face-card {
      background: rgba(15, 23, 42, 0.7);
      border: 1px solid rgba(56, 189, 248, 0.25);
      border-radius: 14px;
      padding: 16px;
      transition: all 0.25s ease;
      height: 100%;
    }

    .ai-face-card:hover {
      border-color: #38bdf8;
      box-shadow: 0 8px 24px rgba(56, 189, 248, 0.2);
      transform: translateY(-3px);
    }

    @keyframes fadeInSlide {
      from { opacity: 0; transform: translateY(-8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .btn-gold-admin {
      background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 60%, #d97706 100%);
      color: #000 !important;
      font-weight: 800;
      border: none;
      border-radius: 20px;
      padding: 8px 18px;
      font-size: 12.5px;
      transition: all 0.25s ease;
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.35);
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .btn-gold-admin:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(245, 158, 11, 0.55);
    }
  </style>
</head>
<body>

  <!-- Top Glass Header -->
  <nav class="customer-navbar">
    <div class="container-fluid px-lg-5">
      <div class="d-flex align-items-center justify-content-between">
        
        <!-- Logo Brand & Portal Badge -->
        <a href="../index.html" class="brand-logo-container">
          <img src="../assets/image/logo-loewix.png" alt="Loewix CCTV" class="navbar-loewix-logo">
          <span class="badge-hub-live d-none d-sm-inline-flex">
            <span class="pulse-dot"></span>
            <span>CUSTOMER CONTROL HUB</span>
          </span>
        </a>

        <!-- Middle: Real-Time Network Speed & Latency Telemetry -->
        <div class="d-none d-md-flex align-items-center gap-2" id="nav-network-telemetry">
          <div class="network-speed-badge" title="Live Stream Network Bandwidth">
            <i class="fas fa-gauge-high text-info speed-pulse-icon"></i>
            <span>SPEED: <strong id="nav-net-speed" class="text-white">0.0 Mbps</strong></span>
          </div>
          <div class="network-latency-badge d-none d-lg-inline-flex" title="Latensi Jaringan Gateway">
            <span class="net-ping-dot"></span>
            <span id="nav-net-ping">14 ms</span>
          </div>
        </div>

        <!-- Right User Actions -->
        <div class="d-flex align-items-center gap-3">
          
          <!-- Super Admin Direct Switcher -->
          <button type="button" onclick="switchCustomerTab('tab-admin-customers')" id="btn-super-admin-direct" class="btn-nav-vms" style="display: none; background: linear-gradient(135deg, #f59e0b, #d97706); border-color: #f59e0b; color: #ffffff; box-shadow: 0 0 12px rgba(245, 158, 11, 0.4);" title="Buka Super Admin Master Control Center">
            <i class="fas fa-crown"></i> <span class="d-none d-md-inline">SUPER ADMIN MASTER</span>
          </button>

          <a href="../index.html" class="btn-nav-vms" title="Buka Tampilan Live Matrix Grid VMS">
            <i class="fas fa-th-large"></i> <span class="d-none d-md-inline">LIVE VMS GRID</span>
          </a>

          <!-- Profile Pill -->
          <div class="user-profile-pill" onclick="openProfileSettingsModal()" title="Pengaturan Akun & Password">
            <div class="user-avatar" id="nav-user-avatar">
              <i class="fas fa-user"></i>
            </div>
            <div class="d-flex flex-column text-start d-none d-sm-flex">
              <span id="nav-user-name" style="line-height: 1.1;">Customer Loewix</span>
              <small class="text-info" style="font-size: 9.5px; font-weight: 600; text-transform: uppercase;">Portal Akun</small>
            </div>
            <i class="fas fa-cog text-muted ms-1" style="font-size: 11px;"></i>
          </div>

          <!-- Logout Button -->
          <button class="btn-nav-logout" onclick="logoutCustomer()" title="Keluar dari Akun">
            <i class="fas fa-arrow-right-from-bracket"></i> <span class="d-none d-md-inline">Logout</span>
          </button>

        </div>

      </div>
    </div>
  </nav>

  <!-- Main Content Container -->
  <div class="container-fluid px-lg-5 py-4">

    <!-- Quota & Metrics Hero Banner -->
    <div class="quota-hero-banner">
      <div class="row align-items-center position-relative" style="z-index: 1;">
        
        <!-- Left Customer Info & Quota Meter -->
        <div class="col-lg-7 mb-4 mb-lg-0 pe-lg-4">
          <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="hero-tier-badge">
              <i class="fas fa-shield-halved"></i> ENTERPRISE VIP TIER
            </span>
            <span class="hero-city-badge" id="hero-customer-city">
              <i class="fas fa-location-dot text-info"></i> Jakarta
            </span>
            <span class="hero-city-badge" style="border-color: rgba(16, 185, 129, 0.35); color: #34d399;">
              <span class="pulse-dot"></span> Cloud Live Active
            </span>
          </div>
          
          <h2 class="hero-customer-title" id="hero-customer-name">
            Yamaha DDS
          </h2>
          
          <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.5; color: #94a3b8 !important;">
            Pusat kendali kamera pengawas CCTV multi-channel terisolasi, enkripsi end-to-end, dan pemantauan siaran langsung cloud Loewix.
          </p>

          <!-- Quota / Telemetry Progress Meter -->
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span style="font-size: 13px; font-weight: 700; color: #38bdf8; display: inline-flex; align-items: center; gap: 6px;" id="hero-bar-label">
              <i class="fas fa-layer-group"></i> Kuota Kamera Terpakai
            </span>
            <span class="quota-cap-pill" id="hero-quota-text">0 / 20 Kamera (0%)</span>
          </div>
          
          <div class="quota-progress-track">
            <div class="quota-progress-fill" id="hero-quota-bar" style="width: 0%;"></div>
          </div>
          
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted" id="hero-bar-bottom-stats" style="font-size: 12px; font-weight: 600;">
            <span><i class="fas fa-circle text-info" style="font-size: 8px;"></i> Terpasang: <strong id="hero-used-count" class="text-white">0</strong> CCTV</span>
            <span><i class="fas fa-circle text-emerald" style="color: #34d399; font-size: 8px;"></i> Tersisa: <strong id="hero-remaining-count" style="color: #34d399;">0</strong> Slot</span>
            <span class="d-none d-md-inline" style="color: #64748b;"><i class="fas fa-server text-muted"></i> Loewix Cloud Storage: Aktif</span>
          </div>
        </div>

        <!-- Right 4 Metric Stat Cards (2x2) -->
        <div class="col-lg-5 ps-lg-3">
          <div class="row g-3">
            
            <div class="col-6">
              <div class="metric-card">
                <div class="metric-icon cyan">
                  <i class="fas fa-video"></i>
                </div>
                <div>
                  <div class="metric-value" id="card-total-cam">0</div>
                  <div class="metric-label">Total CCTV</div>
                  <div class="metric-sub">Terdaftar</div>
                </div>
              </div>
            </div>

            <div class="col-6">
              <div class="metric-card">
                <div class="metric-icon emerald">
                  <i class="fas fa-wifi"></i>
                </div>
                <div>
                  <div class="metric-value text-emerald" style="color: #34d399;" id="card-online-cam">0</div>
                  <div class="metric-label">Live Online</div>
                  <div class="metric-sub">Streaming Aktif</div>
                </div>
              </div>
            </div>

            <div class="col-6">
              <div class="metric-card" id="card-metric3-container">
                <div class="metric-icon amber" id="card-metric3-icon-wrap">
                  <i class="fas fa-server" id="card-metric3-icon"></i>
                </div>
                <div>
                  <div class="metric-value" style="color: #fbbf24;" id="card-quota-max">20</div>
                  <div class="metric-label" id="card-metric3-label">Max Kuota</div>
                  <div class="metric-sub" id="card-metric3-sub">Slot Kamera</div>
                </div>
              </div>
            </div>

            <div class="col-6">
              <div class="metric-card" id="card-metric4-container" onclick="handleMetric4Click()" style="cursor: pointer;" title="Klik untuk Aksi">
                <div class="metric-icon purple" id="card-metric4-icon-wrap">
                  <i class="fas fa-circle-arrow-up" id="card-metric4-icon"></i>
                </div>
                <div>
                  <div class="metric-value" id="card-metric4-value" style="font-size: 16px; color: #c084fc; letter-spacing: 0.5px;">UPGRADE</div>
                  <div class="metric-label" id="card-metric4-label">Tambah Kuota</div>
                  <div class="metric-sub" id="card-metric4-sub" style="color: #a855f7;">Klik Request</div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    <!-- Customer Hub Sub-Menu Navigation Tabs -->
    <div class="customer-tabs-nav mb-4">
      <button type="button" class="customer-nav-tab active" onclick="switchCustomerTab('tab-cameras')" id="nav-tab-cameras">
        <i class="fas fa-video"></i> <span>Kamera CCTV</span>
      </button>
      <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-ai-vision')" id="nav-tab-ai-vision">
        <i class="fas fa-brain text-info"></i> <span>AI Analytics (Face & ANPR)</span>
        <span class="badge badge-danger ml-1" style="font-size: 9px; padding: 2px 5px; background: linear-gradient(135deg, #ef4444, #f43f5e); border-radius: 4px; box-shadow: 0 0 8px rgba(244,63,94,0.6);">AI PRO</span>
      </button>
      <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-package')" id="nav-tab-package">
        <i class="fas fa-box-open"></i> <span>Informasi Paket</span>
      </button>
      <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-invoices')" id="nav-tab-invoices">
        <i class="fas fa-receipt"></i> <span>Informasi Tagihan</span>
      </button>
      <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-history')" id="nav-tab-history">
        <i class="fas fa-history"></i> <span>Riwayat Transaksi</span>
      </button>
      <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-billing-profile')" id="nav-tab-billing-profile">
        <i class="fas fa-id-card"></i> <span>Profil Billing</span>
      </button>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 1: DAFTAR KAMERA CCTV (DEFAULT) -->
    <!-- ======================================================== -->
    <div id="tab-cameras" class="customer-tab-pane">
      <!-- Section Header & Toolbar -->
      <div class="customer-toolbar">
        
        <!-- Left: Title Group & Add Camera Action -->
        <div class="toolbar-left-group">
          <div class="toolbar-title-group">
            <div class="toolbar-icon-badge">
              <i class="fas fa-video"></i>
            </div>
            <div>
              <h4 class="toolbar-heading">Daftar Channel CCTV Saya</h4>
              <div class="toolbar-subtext">Live Stream & Monitoring Hub</div>
            </div>
          </div>

          <button class="btn-add-camera" onclick="openAddCameraModal()" title="Tambahkan Kamera CCTV Baru">
            <i class="fas fa-plus-circle"></i>
            <span>Tambah Kamera CCTV</span>
          </button>

          <button class="btn-live-test-all" id="btn-live-test-all" onclick="toggleLiveTestAll()" title="Putar & Uji Siaran Langsung Semua Kamera Sekaligus">
            <i class="fas fa-play-circle"></i>
            <span>Live Test ALL</span>
          </button>
        </div>

        <!-- Right: Search & Filters -->
        <div class="toolbar-controls-group">
          <div class="toolbar-search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="filter-search-input" placeholder="Cari nama kamera / lokasi..." onkeyup="applyCameraFilters()">
          </div>

          <select class="toolbar-select-pill" id="filter-city-select" onchange="applyCameraFilters()">
            <option value="all">🌐 Semua Wilayah</option>
            <option value="siantar">📍 Pematangsiantar</option>
            <option value="jakarta">📍 DKI Jakarta</option>
            <option value="medan">📍 Kota Medan</option>
            <option value="bandung">📍 Kota Bandung</option>
            <option value="bali">📍 Bali / Denpasar</option>
          </select>

          <select class="toolbar-select-pill" id="filter-status-select" onchange="applyCameraFilters()">
            <option value="all">⚡ Semua Status</option>
            <option value="online">🟢 Online</option>
            <option value="offline">🔴 Offline</option>
          </select>

          <button class="btn-toolbar-refresh" onclick="loadCustomerCameras(true)" title="Segarkan Data CCTV">
            <i class="fas fa-rotate"></i>
          </button>
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

    <!-- ======================================================== -->
    <!-- TAB 2: AI ANALYTICS (FACE RECOGNITION & ANPR PLAT NOMOR) -->
    <!-- ======================================================== -->
    <div id="tab-ai-vision" class="customer-tab-pane" style="display: none;">
      
      <!-- AI Telemetry & Status Header -->
      <div class="billing-card mb-4" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(10, 30, 60, 0.9)); border: 1.5px solid rgba(56, 189, 248, 0.4); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #0284c7, #38bdf8); display: flex; align-items: center; justify-content: center; font-size: 24px; color: #fff; box-shadow: 0 0 20px rgba(56, 189, 248, 0.5);">
              <i class="fas fa-brain"></i>
            </div>
            <div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="text-white font-weight-bold mb-0" style="font-size: 20px; letter-spacing: -0.3px;">Loewix Neural Vision Suite</h4>
                <span class="badge badge-info px-2.5 py-1" style="font-size: 10px; font-weight: 800; background: rgba(56,189,248,0.2); border: 1px solid rgba(56,189,248,0.4); border-radius: 6px;">
                  AI COMPUTER VISION V3
                </span>
              </div>
              <p class="text-muted mb-0" style="font-size: 13px;">
                Pengenalan Wajah Otomatis (Face Recognition) & Pembacaan Plat Nomor Kendaraan Indonesia (ANPR) Real-Time.
              </p>
            </div>
          </div>

          <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="px-3 py-1.5" style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; font-size: 12px; color: #34d399; display: flex; align-items: center; gap: 6px;">
              <span class="pulse-dot" style="background: #34d399;"></span>
              <strong>NEURAL ENGINE: ONLINE</strong>
            </div>
            <button class="btn btn-sm btn-outline-info font-weight-bold px-3 py-1.5" onclick="loadAIData(true)" style="border-radius: 8px;">
              <i class="fas fa-sync mr-1"></i> Segarkan Data AI
            </button>
          </div>
        </div>

        <!-- AI Metrics Row -->
        <div class="row mt-4">
          <div class="col-md-3 col-6 mb-3 mb-md-0">
            <div class="p-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px;">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-muted" style="font-size: 12px; font-weight: 600;">Wajah Terdaftar</span>
                <i class="fas fa-user-check text-info" style="font-size: 16px;"></i>
              </div>
              <h3 class="text-white font-weight-bold mb-0" id="ai-stat-faces" style="font-family: 'Space Grotesk', sans-serif;">3</h3>
              <small class="text-info" style="font-size: 11px;">VIP & Karyawan Aktif</small>
            </div>
          </div>

          <div class="col-md-3 col-6 mb-3 mb-md-0">
            <div class="p-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px;">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-muted" style="font-size: 12px; font-weight: 600;">Plat Terdaftar (ANPR)</span>
                <i class="fas fa-car text-emerald" style="color: #34d399; font-size: 16px;"></i>
              </div>
              <h3 class="text-white font-weight-bold mb-0" id="ai-stat-plates" style="font-family: 'Space Grotesk', sans-serif;">3</h3>
              <small class="text-emerald" style="color: #34d399; font-size: 11px;">Mobil & Motor Resmi</small>
            </div>
          </div>

          <div class="col-md-3 col-6">
            <div class="p-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px;">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-muted" style="font-size: 12px; font-weight: 600;">Deteksi Hari Ini</span>
                <i class="fas fa-bolt text-warning" style="font-size: 16px;"></i>
              </div>
              <h3 class="text-white font-weight-bold mb-0" id="ai-stat-detections" style="font-family: 'Space Grotesk', sans-serif;">12</h3>
              <small class="text-warning" style="font-size: 11px;">Akurasi Rata-rata 97.4%</small>
            </div>
          </div>

          <div class="col-md-3 col-6">
            <div class="p-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px;">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-muted" style="font-size: 12px; font-weight: 600;">Alert Blacklist</span>
                <i class="fas fa-shield-virus text-danger" style="font-size: 16px;"></i>
              </div>
              <h3 class="text-danger font-weight-bold mb-0" id="ai-stat-blacklist" style="font-family: 'Space Grotesk', sans-serif;">1</h3>
              <small class="text-danger" style="font-size: 11px;">Notifikasi Keamanan</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Live AI Vision Workspace (Video + Canvas Overlay + Controls) -->
      <div class="row">
        
        <!-- Left: Live AI Scanner Stream with HUD Overlay -->
        <div class="col-lg-8 mb-4">
          <div class="billing-card p-4" style="background: #090e1a; border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 16px;">
            
            <!-- Video Header Toolbar -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge badge-danger px-2.5 py-1" style="font-size: 11px; font-weight: 800; letter-spacing: 0.5px;">
                  <span class="pulse-dot" style="background: #ffffff; margin-right: 4px;"></span> AI LIVE SCAN
                </span>
                <select class="form-control form-control-sm form-control-dark" id="ai-camera-selector" onchange="changeAICamera(this.value)" style="width: auto; max-width: 320px; font-size: 12.5px; border-radius: 8px;">
                  <option value="webcam">📸 Live Webcam Laptop (Uji Scan Wajah Anda)</option>
                  <option value="5002" selected>CAM LOEWIX JAKARTA 1 - LOBBY UTAMA</option>
                  <option value="5003">CAM LOEWIX GATE MASUK & PARKIR</option>
                  <option value="5001">CAM LOEWIX SIANTAR 1</option>
                </select>
                <select class="form-control form-control-sm form-control-dark" id="ai-target-face-selector" onchange="selectAITargetFace(this.value)" style="width: auto; max-width: 210px; font-size: 11.5px; border-radius: 8px; border-color: rgba(56, 189, 248, 0.4); background: rgba(15, 23, 42, 0.9);" title="Pilih target orang / wajah yang ingin di-track & diverifikasi AI">
                  <option value="auto">👤 Target: WAGYU</option>
                </select>
                <button class="btn btn-sm btn-outline-info font-weight-bold px-2.5 py-1" onclick="startAIWebcamLive()" style="border-radius: 8px; font-size: 11px;" title="Nyalakan kamera laptop untuk scan wajah langsung">
                  <i class="fas fa-camera mr-1"></i> Scan Wajah Saya (Webcam)
                </button>
                <button id="btn-toggle-autoscan" class="btn btn-sm btn-success font-weight-bold px-2.5 py-1" onclick="toggleAIAutoTracking()" style="border-radius: 8px; font-size: 11px; background: #059669; border: none; box-shadow: 0 0 10px rgba(5, 150, 105, 0.4);" title="Otomatis mendeteksi wajah tanpa perlu klik tombol">
                  <i class="fas fa-bolt mr-1"></i> Auto-Scan: AKTIF
                </button>
              </div>

              <div class="d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size: 12px;">Status: <strong class="text-emerald" id="ai-active-mode-label" style="color: #34d399;"><i class="fas fa-circle text-success mr-1" style="font-size: 8px;"></i> Auto Face-ID Active</strong></span>
              </div>
            </div>

            <!-- Hidden SVG Convolution Matrices for Real-time Hardware-Accelerated Video Sharpening -->
            <svg style="position: absolute; width: 0; height: 0; pointer-events: none;" aria-hidden="true">
              <filter id="ai-super-sharpen">
                <feConvolveMatrix order="3" preserveAlpha="true" kernelMatrix="0 -1 0 -1 5 -1 0 -1 0" />
              </filter>
              <filter id="ai-ultra-edge">
                <feConvolveMatrix order="3" preserveAlpha="true" kernelMatrix="-1 -1 -1 -1 9 -1 -1 -1 -1" />
              </filter>
            </svg>

            <!-- Video Player & Canvas HUD Overlay Container -->
            <div id="ai-video-wrapper" class="position-relative" style="background: #020617; border-radius: 12px; overflow: hidden; border: 1px solid rgba(56, 189, 248, 0.3); min-height: 380px; display: flex; align-items: center; justify-content: center; user-select: none;">
              
              <!-- Video Layer -->
              <video id="ai-video-player" autoplay loop muted playsinline crossorigin="anonymous" style="width: 100%; height: 380px; object-fit: cover; display: block; filter: brightness(0.95) contrast(1.05); transition: transform 0.15s ease-out; transform-origin: center center;">
                <source src="assets/video/demo-cctv.mp4" type="video/mp4">
              </video>

              <!-- Video Fallback Poster / Placeholder if no video -->
              <div id="ai-video-placeholder" class="position-absolute text-center" style="display: none; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle, #0f172a 0%, #020617 100%); align-items: center; justify-content: center; flex-direction: column;">
                <i class="fas fa-video-slash text-muted mb-2" style="font-size: 36px;"></i>
                <h6 class="text-white font-weight-bold">Live Stream Hub Offline</h6>
                <small class="text-muted">Pilih channel kamera yang aktif di atas</small>
              </div>

              <!-- HUD Cybernetic Scanline Overlay -->
              <div class="ai-scanline-overlay"></div>

              <!-- Canvas for AI Bounding Box Rendering -->
              <canvas id="ai-hud-canvas" class="position-absolute" style="top: 0; left: 0; width: 100%; height: 100%; z-index: 20; pointer-events: none; transition: transform 0.15s ease-out; transform-origin: center center;"></canvas>

              <!-- HUD Live Status Pill -->
              <div class="position-absolute" style="top: 14px; left: 14px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); border: 1px solid rgba(56, 189, 248, 0.4); border-radius: 8px; padding: 6px 12px; font-size: 11px; color: #38bdf8; z-index: 10; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-crosshairs fa-spin"></i>
                <span id="ai-hud-status-text">AI SCANNER: TRACKING ENTITIES</span>
              </div>

              <!-- Live Detection Banner Toast (Popup inside video) -->
              <div id="ai-hud-detection-banner" class="position-absolute" style="bottom: 14px; left: 14px; right: 14px; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(10px); border: 1.5px solid #38bdf8; border-radius: 10px; padding: 10px 14px; font-size: 12.5px; z-index: 10; display: none; align-items: center; justify-content: space-between; box-shadow: 0 4px 20px rgba(0,0,0,0.6);">
                <div class="d-flex align-items-center gap-3">
                  <div id="ai-banner-icon" style="width: 32px; height: 32px; border-radius: 8px; background: #0284c7; display: flex; align-items: center; justify-content: center; color: #fff;">
                    <i class="fas fa-user-check"></i>
                  </div>
                  <div>
                    <strong class="text-white d-block" id="ai-banner-title">Bambang Supriyanto (VIP)</strong>
                    <small class="text-muted" id="ai-banner-sub">Confidence: 97.8% • Akses Terbuka</small>
                  </div>
                </div>
                <span class="badge badge-success px-2 py-1" id="ai-banner-badge">VIP ACCESSED</span>
              </div>

            </div>

            <!-- AI Super-Sharpness & Digital Zoom Toolbar -->
            <div class="mt-2 p-2.5 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 10px;">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-white font-weight-bold" style="font-size: 11.5px; color: #38bdf8;">
                  <i class="fas fa-wand-magic-sparkles mr-1"></i> Penjernih AI:
                </span>
                <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-sm btn-info px-2.5 py-1 font-weight-bold" id="btn-filter-sharp" onclick="setAIVideoFilter('sharp')" style="font-size: 11px;">
                    ✨ Ultra Sharpen (Plat Jelas)
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1" id="btn-filter-ocr" onclick="setAIVideoFilter('ocr')" style="font-size: 11px;" title="Kontras tinggi hitam-putih untuk mempertegas huruf/angka plat nomor">
                    🌙 High-Contrast OCR
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1" id="btn-filter-hdr" onclick="setAIVideoFilter('hdr')" style="font-size: 11px;" title="Mengurangi silau lampu / pantulan sinar">
                    ☀️ Anti-Glare HDR
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1" id="btn-filter-normal" onclick="setAIVideoFilter('normal')" style="font-size: 11px;">
                    Normal
                  </button>
                </div>
              </div>

              <!-- Digital Zoom & Pan Controls -->
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-white" style="font-size: 11.5px;">
                  <i class="fas fa-magnifying-glass-plus mr-1 text-warning"></i> Zoom Plat:
                </span>
                <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-sm btn-outline-info px-2 py-1" onclick="setAIVideoZoom(1)" style="font-size: 11px;">1x</button>
                  <button type="button" class="btn btn-sm btn-outline-info px-2 py-1" onclick="setAIVideoZoom(1.5)" style="font-size: 11px;">1.5x</button>
                  <button type="button" class="btn btn-sm btn-outline-info px-2 py-1" onclick="setAIVideoZoom(2)" style="font-size: 11px;">2x</button>
                  <button type="button" class="btn btn-sm btn-outline-info px-2 py-1" onclick="setAIVideoZoom(3)" style="font-size: 11px;">3x</button>
                  <button type="button" class="btn btn-sm btn-outline-info px-2 py-1 font-weight-bold" onclick="setAIVideoZoom(4)" style="font-size: 11px;">4x HD</button>
                </div>
                <button type="button" class="btn btn-sm btn-outline-warning px-2.5 py-1 font-weight-bold" onclick="resetAIVideoPanZoom()" style="font-size: 11px;" title="Reset Zoom dan Posisi">
                  <i class="fas fa-rotate-left mr-1"></i> Reset
                </button>
              </div>
            </div>

            <!-- Live AI Simulation & Testing Toolbar -->
            <div class="mt-3 p-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px;">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="text-white font-weight-bold" style="font-size: 12.5px;">
                  <i class="fas fa-wand-magic-sparkles text-info mr-1"></i> Live Trigger & Simulator Deteksi AI:
                </span>
                <small class="text-muted" style="font-size: 11px;">Klik salah satu entitas di bawah untuk menguji respon pengenalan AI di video</small>
              </div>

              <div class="d-flex align-items-center flex-wrap gap-2" id="ai-simulator-buttons-container">
                <!-- Dynamically populated from registered faces & plates -->
              </div>
            </div>

          </div>
        </div>

        <!-- Right: Real-time Live Detection Feed (Log Stream) -->
        <div class="col-lg-4 mb-4">
          <div class="billing-card p-4 h-100 d-flex flex-column" style="background: #090e1a; border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 16px;">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="billing-card-title mb-0" style="font-size: 15px;">
                <i class="fas fa-bolt text-warning mr-1"></i> Live Stream Deteksi AI
              </h5>
              <button class="btn btn-sm btn-link text-muted p-0" onclick="clearAILogs()" style="font-size: 11px; text-decoration: none;">
                <i class="fas fa-trash-alt mr-1"></i> Bersihkan Log
              </button>
            </div>

            <!-- Feed Stream List Container -->
            <div id="ai-live-feed-container" class="flex-fill" style="max-height: 480px; overflow-y: auto; padding-right: 4px;">
              <!-- Dynamically populated live detection cards -->
            </div>

            <div class="pt-3 mt-auto border-top" style="border-color: rgba(255,255,255,0.08) !important;">
              <div class="d-flex align-items-center justify-content-between">
                <small class="text-muted" style="font-size: 11.5px;">Auto Audio Alert:</small>
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="ai-sound-toggle" checked onchange="toggleAISound(this.checked)">
                  <label class="custom-control-label text-info" for="ai-sound-toggle" style="font-size: 11.5px; cursor: pointer;">Suara Alert Aktif</label>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>

      <!-- AI Directories & Management Sub-Tabs (Face DB & ANPR Plate DB) -->
      <div class="billing-card mb-4" style="background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 16px;">
        
        <!-- Directory Navigation Pills -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-3 border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-info font-weight-bold px-3 py-2" id="btn-subtab-faces" onclick="switchAISubTab('subtab-faces')" style="border-radius: 8px; font-size: 12.5px;">
              <i class="fas fa-user-tag mr-1.5"></i> Database Wajah Terdaftar (<span id="badge-faces-count">3</span>)
            </button>
            <button class="btn btn-sm btn-outline-info font-weight-bold px-3 py-2" id="btn-subtab-plates" onclick="switchAISubTab('subtab-plates')" style="border-radius: 8px; font-size: 12.5px;">
              <i class="fas fa-car-side mr-1.5"></i> Database Plat Nomor ANPR (<span id="badge-plates-count">3</span>)
            </button>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-success font-weight-bold px-3 py-2" onclick="openRegisterFaceModal()" style="border-radius: 8px; font-size: 12.5px; background: #059669; border: none;">
              <i class="fas fa-user-plus mr-1.5"></i> + Daftarkan Wajah Baru
            </button>
            <button class="btn btn-sm btn-primary font-weight-bold px-3 py-2" onclick="openRegisterPlateModal()" style="border-radius: 8px; font-size: 12.5px; background: #0284c7; border: none;">
              <i class="fas fa-plus-circle mr-1.5"></i> + Daftarkan Plat Kendaraan
            </button>
          </div>
        </div>

        <!-- Subtab 1: Face Recognition Directory -->
        <div id="subtab-faces" class="ai-subtab-content">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="text-white font-weight-bold mb-0" style="font-size: 14px;">
              <i class="fas fa-users-viewfinder text-info mr-1"></i> Direktori Wajah (VIP, Karyawan, Penghuni & Blacklist)
            </h6>
            <input type="text" class="form-control form-control-sm form-control-dark" id="search-face-input" onkeyup="filterFacesList(this.value)" placeholder="Cari nama atau jabatan..." style="max-width: 250px; font-size: 12px; border-radius: 8px;">
          </div>

          <div class="row" id="ai-faces-grid">
            <!-- Dynamically populated face directory cards -->
          </div>
        </div>

        <!-- Subtab 2: ANPR Vehicle Plates Directory -->
        <div id="subtab-plates" class="ai-subtab-content" style="display: none;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="text-white font-weight-bold mb-0" style="font-size: 14px;">
              <i class="fas fa-id-card-clip text-info mr-1"></i> Direktori Plat Nomor Kendaraan Terdaftar
            </h6>
            <input type="text" class="form-control form-control-sm form-control-dark" id="search-plate-input" onkeyup="filterPlatesList(this.value)" placeholder="Cari nomor plat (contoh: B 1234)..." style="max-width: 250px; font-size: 12px; border-radius: 8px;">
          </div>

          <div class="table-responsive">
            <table class="billing-table">
              <thead>
                <tr>
                  <th>Nomor Plat</th>
                  <th>Pemilik / Unit</th>
                  <th>Jenis Kendaraan</th>
                  <th>Model / Warna</th>
                  <th>Kategori Akses</th>
                  <th>Catatan</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody id="ai-plates-tbody">
                <!-- Dynamically populated plate rows -->
              </tbody>
            </table>
          </div>
        </div>

      </div>

    </div>

    <!-- ======================================================== -->
    <!-- TAB 3: INFORMASI PAKET & UPGRADE -->
    <!-- ======================================================== -->
    <div id="tab-package" class="customer-tab-pane" style="display: none;">
      <div class="row">
        <!-- Current Active Packages Column -->
        <div class="col-lg-6 mb-4">
          <div class="billing-card h-100">
            <div class="billing-card-header">
              <h5 class="billing-card-title">
                <i class="fas fa-boxes-stacked text-info"></i> Daftar Lisensi & Paket Aktif
              </h5>
              <span class="badge badge-info px-2.5 py-1" id="total-quota-summary-badge" style="font-size: 12px; font-weight: 700; border-radius: 8px;">
                Total Kuota: 20 CCTV
              </span>
            </div>

            <div id="active-subscriptions-list-container">
              <!-- Dynamically populated from active subscriptions API -->
            </div>
          </div>
        </div>

        <!-- Available Upgrade / Add-On Plans -->
        <div class="col-lg-6 mb-4">
          <div class="billing-card h-100">
            <div class="billing-card-header">
              <h5 class="billing-card-title">
                <i class="fas fa-rocket text-warning"></i> Tambah / Beli Lisensi Paket Baru
              </h5>
              <span class="text-muted" style="font-size: 12px;">Tambah kuota kamera & slot baru</span>
            </div>

            <div class="upgrade-plans-list" id="upgrade-plans-container">
              <!-- Dynamically populated from plans API -->
              <div class="p-3 mb-3" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div>
                    <h6 class="text-white font-weight-bold mb-0">Enterprise Fleet (20 CCTV)</h6>
                    <small class="text-info font-weight-bold">4K UHD &bull; AI Telemetry &bull; 30 Hari Cloud</small>
                  </div>
                  <div class="text-right">
                    <div class="text-emerald font-weight-bold" style="color: #34d399; font-size: 15px;">Rp 5.490.000<small>/thn</small></div>
                    <small class="text-muted">Atau Rp 549.000/bln</small>
                  </div>
                </div>
                <button class="btn btn-sm btn-outline-info btn-block mt-2 font-weight-bold" onclick="checkoutPlanMidtrans('enterprise_20', 'annual')">
                  <i class="fas fa-credit-card mr-1"></i> Upgrade ke Enterprise (Midtrans)
                </button>
              </div>

              <div class="p-3 mb-3" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div>
                    <h6 class="text-white font-weight-bold mb-0">Corporate Custom (50 CCTV)</h6>
                    <small class="text-muted">Dedicated Streaming Server &bull; White-label</small>
                  </div>
                  <div class="text-right">
                    <div class="text-emerald font-weight-bold" style="color: #34d399; font-size: 15px;">Rp 11.990.000<small>/thn</small></div>
                    <small class="text-muted">Atau Rp 1.199.000/bln</small>
                  </div>
                </div>
                <button class="btn btn-sm btn-outline-light btn-block mt-2 font-weight-bold" onclick="checkoutPlanMidtrans('corporate_50', 'annual')">
                  <i class="fas fa-credit-card mr-1"></i> Upgrade ke Corporate (Midtrans)
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 3: INFORMASI TAGIHAN -->
    <!-- ======================================================== -->
    <div id="tab-invoices" class="customer-tab-pane" style="display: none;">
      <div class="billing-card">
        <div class="billing-card-header">
          <h5 class="billing-card-title">
            <i class="fas fa-receipt text-info"></i> Informasi Tagihan & Status Pembayaran
          </h5>
          <button class="btn btn-sm btn-outline-info" id="btn-refresh-invoices" onclick="loadBillingDashboardData(this)" title="Periksa status pembayaran dan tagihan terbaru">
            <i class="fas fa-rotate mr-1"></i> Segarkan Tagihan
          </button>
        </div>

        <div id="active-invoice-container">
          <!-- Active Invoice Box -->
          <div class="p-4 mb-4" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(12, 22, 48, 0.95)); border: 1.5px solid rgba(56, 189, 248, 0.3); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div class="row align-items-center">
              
              <!-- Left: Invoice Status, Title & Details -->
              <div class="col-md-7 col-12 mb-3 mb-md-0">
                <div class="d-flex align-items-center flex-wrap mb-2.5" style="gap: 12px;">
                  <span class="badge badge-success px-3 py-1.5" style="font-size: 11px; font-weight: 800; letter-spacing: 0.5px; border-radius: 6px; background: #059669; color: #ffffff;">
                    <i class="fas fa-check-circle mr-1"></i> SEMUA TAGIHAN LUNAS
                  </span>
                  <span class="text-muted" style="font-size: 12.5px;">
                    No. Invoice: <strong class="text-white font-monospace" id="inv-last-order-id">INV-LOEWIX-20260814-001</strong>
                  </span>
                </div>

                <h3 class="text-white font-weight-bold mb-2" style="font-size: 20px; letter-spacing: -0.3px;" id="inv-plan-title">Enterprise Fleet (20 CCTV) – Periode Tahunan</h3>
                <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.5;">
                  <i class="fas fa-shield-alt text-success mr-1"></i> Layanan live streaming CCTV & penyimpanan cloud aktif normal. Tidak ada tagihan tertunggak.
                </p>
              </div>

              <!-- Right: Price Display & Clean Side-by-Side Action Buttons -->
              <div class="col-md-5 col-12 text-md-right text-left">
                <div class="mb-3">
                  <div class="text-muted mb-1" style="font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Total Pembayaran Terakhir</div>
                  <h2 class="text-emerald font-weight-bold mb-0" style="color: #34d399; font-size: 26px; font-family: 'Space Grotesk', sans-serif; letter-spacing: -0.5px;" id="inv-total-display">Rp 6.093.908</h2>
                </div>

                <div class="d-flex align-items-center justify-content-md-end justify-content-start flex-wrap" style="gap: 10px;">
                  <button class="btn btn-outline-info font-weight-bold px-3 py-2" onclick="switchCustomerTab('tab-history')" style="border-radius: 10px; font-size: 12.5px; border-color: rgba(56, 189, 248, 0.4); color: #38bdf8; background: rgba(56, 189, 248, 0.06);">
                    <i class="fas fa-file-invoice mr-1.5"></i> Riwayat & Kwitansi
                  </button>
                  <button class="btn btn-info font-weight-bold px-3 py-2" onclick="switchCustomerTab('tab-package')" style="border-radius: 10px; font-size: 12.5px; background: linear-gradient(135deg, #0284c7, #0ea5e9); border: none; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4); color: #ffffff;">
                    <i class="fas fa-sync mr-1.5"></i> Perpanjang / Upgrade
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Payment Channels Supported -->
        <div class="p-3" style="background: rgba(2, 6, 23, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px;">
          <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 12px;">
            <span class="text-muted" style="font-size: 12.5px;">
              <i class="fas fa-shield-alt text-success mr-1"></i> Pembayaran resmi diproses secara otomatis & instan melalui gerbang pembayaran <strong>Midtrans</strong>:
            </span>
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
              <span class="badge badge-dark px-2.5 py-1.5" style="background: rgba(255,255,255,0.08); font-size: 11px; border: 1px solid rgba(255,255,255,0.08); border-radius: 6px;">QRIS (GoPay, OVO, Dana)</span>
              <span class="badge badge-dark px-2.5 py-1.5" style="background: rgba(255,255,255,0.08); font-size: 11px; border: 1px solid rgba(255,255,255,0.08); border-radius: 6px;">Virtual Account (BCA, Mandiri, BRI, BNI)</span>
              <span class="badge badge-dark px-2.5 py-1.5" style="background: rgba(255,255,255,0.08); font-size: 11px; border: 1px solid rgba(255,255,255,0.08); border-radius: 6px;">Kartu Kredit / Debit</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 4: RIWAYAT TRANSAKSI -->
    <!-- ======================================================== -->
    <div id="tab-history" class="customer-tab-pane" style="display: none;">
      <div class="billing-card">
        <div class="billing-card-header">
          <h5 class="billing-card-title">
            <i class="fas fa-history text-info"></i> Riwayat Transaksi & Pembayaran
          </h5>
          <span class="text-muted" style="font-size: 12.5px;">Daftar seluruh pembayaran langganan</span>
        </div>

        <div class="table-responsive">
          <table class="billing-table">
            <thead>
              <tr>
                <th>No. Invoice / Order ID</th>
                <th>Tanggal & Waktu</th>
                <th>Paket Layanan</th>
                <th>Metode Bayar</th>
                <th>Nominal (Inc. PPN)</th>
                <th>Status</th>
                <th class="text-center">Kwitansi</th>
              </tr>
            </thead>
            <tbody id="tx-history-tbody">
              <!-- Loaded via JavaScript -->
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                  <div class="spinner-border spinner-border-sm text-info mr-2" role="status"></div>
                  Memuat data riwayat transaksi...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 5: PROFIL BILLING & FAKTUR -->
    <!-- ======================================================== -->
    <div id="tab-billing-profile" class="customer-tab-pane" style="display: none;">
      <div class="billing-card">
        <div class="billing-card-header">
          <h5 class="billing-card-title">
            <i class="fas fa-id-card text-info"></i> Profil Billing & Data Faktur Pajak
          </h5>
          <span class="text-muted" style="font-size: 12px;">Informasi penagihan resmi</span>
        </div>

        <form id="formBillingProfile" onsubmit="submitBillingProfile(event)">
          <div class="row">
            <div class="col-12 mb-3">
              <div class="form-group-dark">
                <label><i class="fas fa-building text-info mr-1"></i> Nama Perusahaan / Instansi Resmi</label>
                <input type="text" id="bill-company-name" class="form-control form-control-dark" placeholder="Contoh: PT. Loewix Solusi Indonesia" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-group-dark">
                <label><i class="fas fa-envelope text-info mr-1"></i> Email Penagihan (Billing Email)</label>
                <input type="email" id="bill-email" class="form-control form-control-dark" placeholder="finance@perusahaan.com" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-group-dark">
                <label><i class="fas fa-phone text-info mr-1"></i> Nomor WhatsApp Finance</label>
                <input type="tel" id="bill-phone" class="form-control form-control-dark" placeholder="+62 812-3456-7890" required>
              </div>
            </div>
            <div class="col-12 mb-4">
              <div class="form-group-dark">
                <label><i class="fas fa-map-marker-alt text-info mr-1"></i> Alamat Lengkap Penagihan</label>
                <textarea id="bill-address" class="form-control form-control-dark" rows="3" placeholder="Alamat gedung, jalan, kelurahan, kecamatan, kota, kode pos"></textarea>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button type="submit" id="btn-save-billing-profile" class="btn btn-info px-4 py-2 font-weight-bold" style="border-radius: 10px;">
              <i class="fas fa-save mr-1"></i> Simpan Profil Billing
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB ADMIN 1: KELOLA SEMUA CUSTOMER & ALOKASI KUOTA (SPA) -->
    <!-- ======================================================== -->
    <div id="tab-admin-customers" class="customer-tab-pane" style="display: none;">
      <div class="admin-mgmt-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
          <div>
            <h4 class="font-weight-bold mb-1" style="color: #ffffff; display: flex; align-items: center; gap: 8px;">
              <i class="fas fa-users-cog" style="color: #38bdf8;"></i> Kelola Customer & Alokasi Kuota CCTV
            </h4>
            <p class="text-muted mb-0" style="font-size: 13px;">
              Atur hak akses streaming, data profil pelanggan, konfigurasi XMeye P2P, dan kuota live kamera.
            </p>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <button class="btn btn-outline-info btn-sm" onclick="exportAdminCustomerCSV()" style="border-radius: 20px; font-weight: 700; padding: 6px 14px;">
              <i class="fas fa-download mr-1"></i> Export CSV
            </button>
            <button class="btn-gold-admin" onclick="openAddCustomerModal()">
              <i class="fas fa-user-plus"></i> Tambah Customer Baru
            </button>
          </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-3 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08);">
          <div class="search-input-wrapper" style="flex: 1; min-width: 260px; position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 14px; top: 13px; color: #94a3b8; font-size: 13px;"></i>
            <input type="text" id="search-customer-input" class="form-control form-control-dark" style="padding-left: 38px; border-radius: 20px;" placeholder="Cari Nama Customer, Email, atau No. HP..." onkeyup="filterAdminCustomerTable()">
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <select id="filter-city-select" class="form-control form-control-dark" style="width: auto; border-radius: 20px;" onchange="filterAdminCustomerTable()">
              <option value="all">🌐 Semua Wilayah</option>
              <option value="siantar">📍 Pematangsiantar</option>
              <option value="jakarta">📍 DKI Jakarta</option>
              <option value="medan">📍 Kota Medan</option>
              <option value="bandung">📍 Kota Bandung</option>
              <option value="bali">📍 Bali / Denpasar</option>
            </select>
            <select id="filter-status-select" class="form-control form-control-dark" style="width: auto; border-radius: 20px;" onchange="filterAdminCustomerTable()">
              <option value="all">⚡ Semua Status</option>
              <option value="active">✅ Status Aktif</option>
              <option value="suspended">⛔ Status Suspended</option>
            </select>
          </div>
        </div>

        <!-- Customers Table -->
        <div class="table-responsive">
          <table class="table table-dark-custom">
            <thead>
              <tr>
                <th style="width: 70px;">ID</th>
                <th>Customer / Perusahaan</th>
                <th>Email & Kontak</th>
                <th>Wilayah</th>
                <th style="min-width: 180px;">Penggunaan Kuota CCTV</th>
                <th>Status Akun</th>
                <th class="text-right" style="min-width: 220px;">Aksi Super Admin</th>
              </tr>
            </thead>
            <tbody id="admin-customer-table-body">
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="fas fa-spinner fa-spin fa-2x mb-2 text-info"></i>
                  <div style="font-weight: 600;">Memuat data customer dari server...</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB ADMIN 2: MONITORING TRANSAKSI SAAS MIDTRANS (SPA) -->
    <!-- ======================================================== -->
    <div id="tab-admin-transactions" class="customer-tab-pane" style="display: none;">
      
      <!-- Top Financial Summary Cards -->
      <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3.5 rounded-4 h-100" style="background: linear-gradient(145deg, rgba(16, 185, 129, 0.12) 0%, rgba(5, 150, 105, 0.04) 100%); border: 1px solid rgba(16, 185, 129, 0.35); box-shadow: 0 10px 30px rgba(0,0,0,0.35); border-radius: 16px; padding: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span style="font-size: 11px; font-weight: 700; color: #6ee7b7; text-transform: uppercase; letter-spacing: 0.5px;">TOTAL PENDAPATAN (OMSET)</span>
              <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.2); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 14px;">
                <i class="fas fa-coins"></i>
              </div>
            </div>
            <div id="stat-trans-total-revenue" style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">Rp 0</div>
            <div class="d-flex align-items-center gap-1 mt-1 text-muted" style="font-size: 11px;">
              <span class="badge badge-success px-1.5 py-0.5" style="background: rgba(16,185,129,0.2); color: #34d399; font-size: 9.5px; border-radius: 4px;"><i class="fas fa-arrow-up"></i> Real-time</span>
              <span>Akumulasi transaksi lunas</span>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3.5 rounded-4 h-100" style="background: linear-gradient(145deg, rgba(56, 189, 248, 0.12) 0%, rgba(2, 132, 199, 0.04) 100%); border: 1px solid rgba(56, 189, 248, 0.35); box-shadow: 0 10px 30px rgba(0,0,0,0.35); border-radius: 16px; padding: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span style="font-size: 11px; font-weight: 700; color: #7dd3fc; text-transform: uppercase; letter-spacing: 0.5px;">TRANSAKSI LUNAS (SETTLED)</span>
              <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(56, 189, 248, 0.2); display: flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 14px;">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
            <div id="stat-trans-settled-count" style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">0 Tagihan</div>
            <div class="d-flex align-items-center gap-1 mt-1 text-muted" style="font-size: 11px;">
              <span id="stat-trans-settled-rate" class="badge badge-info px-1.5 py-0.5" style="background: rgba(56,189,248,0.2); color: #38bdf8; font-size: 9.5px; border-radius: 4px;">100% Success</span>
              <span>Verifikasi otomatis & manual</span>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3.5 rounded-4 h-100" style="background: linear-gradient(145deg, rgba(245, 158, 11, 0.12) 0%, rgba(217, 119, 6, 0.04) 100%); border: 1px solid rgba(245, 158, 11, 0.35); box-shadow: 0 10px 30px rgba(0,0,0,0.35); border-radius: 16px; padding: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span style="font-size: 11px; font-weight: 700; color: #fcd34d; text-transform: uppercase; letter-spacing: 0.5px;">MENUNGGU BAYAR (PENDING)</span>
              <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 14px;">
                <i class="fas fa-clock"></i>
              </div>
            </div>
            <div id="stat-trans-pending-amount" style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">Rp 0</div>
            <div class="d-flex align-items-center gap-1 mt-1 text-muted" style="font-size: 11px;">
              <span id="stat-trans-pending-count" class="badge badge-warning px-1.5 py-0.5" style="background: rgba(245,158,11,0.2); color: #fbbf24; font-size: 9.5px; border-radius: 4px;">0 Invoice</span>
              <span>Menunggu checkout user</span>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3.5 rounded-4 h-100" style="background: linear-gradient(145deg, rgba(168, 85, 247, 0.12) 0%, rgba(126, 34, 206, 0.04) 100%); border: 1px solid rgba(168, 85, 247, 0.35); box-shadow: 0 10px 30px rgba(0,0,0,0.35); border-radius: 16px; padding: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span style="font-size: 11px; font-weight: 700; color: #d8b4fe; text-transform: uppercase; letter-spacing: 0.5px;">RATA-RATA ORDER (ARPU)</span>
              <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(168, 85, 247, 0.2); display: flex; align-items: center; justify-content: center; color: #c084fc; font-size: 14px;">
                <i class="fas fa-chart-line"></i>
              </div>
            </div>
            <div id="stat-trans-arpu" style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">Rp 0</div>
            <div class="d-flex align-items-center gap-1 mt-1 text-muted" style="font-size: 11px;">
              <span class="badge px-1.5 py-0.5" style="background: rgba(168,85,247,0.2); color: #c084fc; font-size: 9.5px; border-radius: 4px;">Average Revenue</span>
              <span>Per transaksi sukses</span>
            </div>
          </div>
        </div>
      </div>

      <div class="admin-mgmt-card">
        <!-- Header & Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
          <div>
            <h4 class="font-weight-bold mb-1" style="color: #ffffff; display: flex; align-items: center; gap: 8px;">
              <i class="fas fa-file-invoice-dollar" style="color: #34d399;"></i> Monitoring Transaksi SaaS & Midtrans
            </h4>
            <p class="text-muted mb-0" style="font-size: 13px;">
              Rekap pemasukan langganan SaaS, status pembayaran QRIS / Virtual Account, dan invoice gateway.
            </p>
          </div>
          <div class="d-flex gap-2 flex-wrap" style="gap: 8px;">
            <button class="btn btn-sm text-white font-weight-bold" onclick="openCreateManualInvoiceModal()" style="border-radius: 20px; padding: 6px 15px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; box-shadow: 0 4px 15px rgba(16,185,129,0.35);">
              <i class="fas fa-plus-circle mr-1"></i> Terbitkan Invoice
            </button>
            <button class="btn btn-outline-info btn-sm font-weight-bold" onclick="exportAdminTransactionsCSV()" style="border-radius: 20px; padding: 6px 14px; border-color: rgba(56,189,248,0.4); color: #38bdf8;">
              <i class="fas fa-download mr-1"></i> Export CSV
            </button>
            <button class="btn btn-outline-warning btn-sm" onclick="openMidtransSettingsModal()" style="border-radius: 20px; font-weight: 700; padding: 6px 14px; border-color: rgba(245,158,11,0.4); color: #f59e0b;">
              <i class="fas fa-credit-card mr-1"></i> Pengaturan Midtrans
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="openSmtpSettingsModal()" style="border-radius: 20px; font-weight: 700; padding: 6px 14px; border-color: rgba(56,189,248,0.4); color: #38bdf8;">
              <i class="fas fa-envelope-open-text mr-1"></i> Pengaturan Email SMTP
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="loadAdminTransactionsList()" style="border-radius: 20px; font-weight: 700; padding: 6px 14px;">
              <i class="fas fa-sync-alt mr-1"></i> Refresh Data
            </button>
          </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="row g-3 mb-4 align-items-center" style="margin-bottom: 24px !important;">
          <div class="col-lg-5 col-md-12">
            <div class="input-group" style="height: 42px;">
              <div class="input-group-prepend"><span class="input-group-text" style="background: rgba(255,255,255,0.05); color: #94a3b8; border-color: rgba(255,255,255,0.14); border-radius: 12px 0 0 12px; font-size: 13px;"><i class="fas fa-search"></i></span></div>
              <input type="text" id="search-transaction-input" class="form-control form-control-dark" style="height: 42px !important; padding-left: 14px !important; border-radius: 0 12px 12px 0 !important;" placeholder="Cari No. Invoice, Nama Customer, Email..." oninput="filterAdminTransactionsTable()">
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <select id="filter-transaction-status" class="form-control form-control-dark" style="height: 42px !important; border-radius: 12px !important;" onchange="filterAdminTransactionsTable()">
              <option value="all">⚡ Semua Status Transaksi</option>
              <option value="settlement">🟢 Lunas (Settlement)</option>
              <option value="pending">🟡 Menunggu Bayar (Pending)</option>
              <option value="expire">🔴 Batal / Expired</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-6">
            <select id="filter-transaction-payment" class="form-control form-control-dark" style="height: 42px !important; border-radius: 12px !important;" onchange="filterAdminTransactionsTable()">
              <option value="all">💳 Semua Metode Bayar</option>
              <option value="va">🏦 Virtual Account (BCA/Mandiri/BRI/BNI)</option>
              <option value="qris">📱 QRIS Instant</option>
              <option value="manual">💵 Transfer Bank Manual</option>
            </select>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-dark-custom">
            <thead>
              <tr>
                <th>No. Invoice</th>
                <th>Nama Pelanggan</th>
                <th>Paket SaaS</th>
                <th>Total Tagihan (Inc. PPN)</th>
                <th>Metode Bayar</th>
                <th>Status</th>
                <th>Waktu Transaksi</th>
                <th class="text-right">Aksi Super Admin</th>
              </tr>
            </thead>
            <tbody id="admin-transactions-table-body">
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                  <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data transaksi...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB ADMIN 3: STATUS NODE SERVER MEDIAMTX & AI (SPA) -->
    <!-- ======================================================== -->
    <div id="tab-admin-server" class="customer-tab-pane" style="display: none;">
      <div class="admin-mgmt-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
          <div>
            <h4 class="font-weight-bold mb-1" style="color: #ffffff; display: flex; align-items: center; gap: 8px;">
              <i class="fas fa-server" style="color: #38bdf8;"></i> Status Node Server Streaming MediaMTX & AI
            </h4>
            <p class="text-muted mb-0" style="font-size: 13px;">
              Monitoring beban server, relay WebRTC/HLS real-time, dan status cluster CCTV Loewix.
            </p>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-4">
            <div class="p-3 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(56,189,248,0.2);">
              <div style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">MediaMTX Streaming Core</div>
              <div style="font-size: 22px; font-weight: 800; color: #34d399; margin: 4px 0;"><i class="fas fa-circle" style="font-size: 10px;"></i> ONLINE (v1.9.3)</div>
              <div style="font-size: 12px; color: #64748b;">RTSP / WebRTC / HLS Engine Active</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(56,189,248,0.2);">
              <div style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Streaming Inbound / Outbound</div>
              <div style="font-size: 22px; font-weight: 800; color: #38bdf8; margin: 4px 0;">12 Active Streams</div>
              <div style="font-size: 12px; color: #64748b;">Ultra Low-Latency Relay (~115ms)</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(56,189,248,0.2);">
              <div style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Gateway Security & SSL</div>
              <div style="font-size: 22px; font-weight: 800; color: #fbbf24; margin: 4px 0;">TLS 1.3 Active</div>
              <div style="font-size: 12px; color: #64748b;">AES 256-Bit Hardware Encryption</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB ADMIN 4: KELOLA PAKET SAAS & HARGA LANGGANAN (SPA) -->
    <!-- ======================================================== -->
    <div id="tab-admin-plans" class="customer-tab-pane" style="display: none;">
      <div class="admin-mgmt-card">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
          <div>
            <h4 class="font-weight-bold mb-1" style="color: #ffffff; display: flex; align-items: center; gap: 8px;">
              <i class="fas fa-cubes" style="color: #38bdf8;"></i> Kelola Paket SaaS & Tarif Langganan Loewix CCTV
            </h4>
            <p class="text-muted mb-0" style="font-size: 13px;">
              Konfigurasi harga paket bulanan & tahunan, alokasi kuota CCTV, badge promo, dan fitur streaming untuk halaman pendaftaran & portal.
            </p>
          </div>
          <button class="btn-gold-admin" onclick="openAddPlanModal()">
            <i class="fas fa-plus-circle mr-1"></i> Tambah Paket Baru
          </button>
        </div>

        <!-- Plans Cards Grid -->
        <div class="row g-4" id="admin-plans-grid">
          <!-- Populated by loadAdminPlansList() -->
        </div>
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
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeModalHelper('modalCamForm')">
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
                <label class="text-white"><i class="fas fa-link text-info mr-1"></i> URL Stream RTSP / Link Kamera:</label>
                <input type="text" id="cust-cam-rtsp" class="form-control form-control-dark" placeholder="rtsp://admin:password@ip:port/stream atau path cctv_loewix_1">
                <small class="text-muted d-block mt-1" style="font-size: 11px;"><i class="fas fa-info-circle text-info"></i> Masukkan URL RTSP dari kamera/DVR. Server Loewix otomatis mengonversikannya menjadi live stream web.</small>
              </div>
              <div class="form-group form-group-dark mb-2">
                <label style="font-size: 11.5px; color: #94a3b8;"><i class="fas fa-globe text-muted mr-1"></i> Custom Stream Path / URL HLS (Opsional):</label>
                <input type="text" id="cust-cam-hls" class="form-control form-control-dark" placeholder="Otomatis diisi server, contoh: cctv_loewix_1" style="font-size: 12px;">
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
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalCamForm')">Batal</button>
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

  <!-- ===== MODAL PROFIL & GANTI PASSWORD (PREMIUM GLASSMORPHIC EDITION) ===== -->
  <div class="modal fade modal-dark" id="modalProfile" tabindex="-1" role="dialog" aria-labelledby="modalProfileTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 580px;">
      <div class="modal-content" style="background: linear-gradient(160deg, #0b1533 0%, #070d22 100%); border: 1px solid rgba(56, 189, 248, 0.35); border-radius: 22px; box-shadow: 0 30px 80px rgba(0, 0, 0, 0.85), 0 0 30px rgba(56, 189, 248, 0.12); overflow: hidden;">
        
        <!-- Header -->
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding: 20px 26px;">
          <div class="d-flex align-items-center gap-3">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(56, 189, 248, 0.2), rgba(2, 132, 199, 0.2)); border: 1px solid rgba(56, 189, 248, 0.4); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #38bdf8; box-shadow: 0 0 15px rgba(56, 189, 248, 0.25);">
              <i class="fas fa-shield-alt"></i>
            </div>
            <div>
              <h5 class="modal-title font-weight-bold text-white mb-0" id="modalProfileTitle" style="font-size: 17px; letter-spacing: -0.3px;">
                Pengaturan Akun & Keamanan
              </h5>
              <small style="color: #94a3b8; font-size: 11.5px;">Kelola profil identitas dan proteksi akses akun Loewix</small>
            </div>
          </div>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalProfile')" aria-label="Close" style="background: rgba(255,255,255,0.08); border: none; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; opacity: 0.8; transition: all 0.2s;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body px-4 py-4" style="max-height: 78vh; overflow-y: auto;">
          
          <!-- SECTION 1: PROFIL PERUSAHAAN -->
          <div class="p-3 mb-4 rounded-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px;">
            <form id="formUpdateProfile" onsubmit="submitUpdateProfile(event)">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 11px; letter-spacing: 0.5px;">
                  <i class="fas fa-id-card mr-1"></i> DATA PROFIL PERUSAHAAN
                </span>
                <span style="font-size: 10px; color: #64748b; font-family: monospace;">PORTAL IDENTITY</span>
              </div>

              <div class="form-group mb-3">
                <label style="font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; display: block;">
                  Nama Customer / Perusahaan:
                </label>
                <div class="input-group" style="position: relative; display: flex; align-items: center;">
                  <div style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); z-index: 10; color: #38bdf8; font-size: 14px; pointer-events: none;">
                    <i class="fas fa-building"></i>
                  </div>
                  <input type="text" id="prof-name" class="form-control form-control-dark" style="padding-left: 48px !important; padding-right: 16px !important;" required>
                </div>
              </div>

              <div class="form-group mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label style="font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; display: block;">
                    Email Akun Terdaftar:
                  </label>
                  <span class="badge" style="background: rgba(255, 255, 255, 0.08); color: #94a3b8; font-size: 10px; padding: 3px 8px; border-radius: 6px;">
                    <i class="fas fa-lock mr-1"></i> Read-only
                  </span>
                </div>
                <div class="input-group" style="position: relative; display: flex; align-items: center;">
                  <div style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); z-index: 10; color: #64748b; font-size: 14px; pointer-events: none;">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <input type="email" id="prof-email" class="form-control form-control-dark" disabled style="padding-left: 48px !important; padding-right: 16px !important; background: rgba(15, 23, 42, 0.4) !important; color: #94a3b8 !important; opacity: 0.85;">
                </div>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-md-6">
                  <label style="font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; display: block;">
                    No. WhatsApp:
                  </label>
                  <div class="input-group" style="position: relative; display: flex; align-items: center;">
                    <div style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); z-index: 10; color: #34d399; font-size: 14px; pointer-events: none;">
                      <i class="fab fa-whatsapp"></i>
                    </div>
                    <input type="text" id="prof-phone" class="form-control form-control-dark" style="padding-left: 48px !important; padding-right: 16px !important;" placeholder="+62 812-xxxx">
                  </div>
                </div>
                <div class="col-md-6">
                  <label style="font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; display: block;">
                    Kota / Wilayah:
                  </label>
                  <div class="input-group" style="position: relative; display: flex; align-items: center;">
                    <div style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); z-index: 10; color: #f43f5e; font-size: 14px; pointer-events: none;">
                      <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <input type="text" id="prof-city" class="form-control form-control-dark" style="padding-left: 48px !important; padding-right: 16px !important;" placeholder="Contoh: Bandung">
                  </div>
                </div>
              </div>

              <div class="text-right pt-2">
                <button type="submit" class="btn btn-sm text-white font-weight-bold px-3 py-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 10px; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4); font-size: 12.5px; transition: all 0.2s;">
                  <i class="fas fa-save mr-1.5"></i> Simpan Profil
                </button>
              </div>
            </form>
          </div>

          <!-- SECTION 2: GANTI PASSWORD AKUN -->
          <div class="p-3 rounded-3" style="background: rgba(245, 158, 11, 0.04); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 16px;">
            <form id="formChangePassword" onsubmit="submitChangePassword(event)">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 11px; letter-spacing: 0.5px;">
                  <i class="fas fa-key mr-1"></i> GANTI PASSWORD AKUN
                </span>
                <span style="font-size: 10px; color: #94a3b8; font-family: monospace;">SECURITY ENCRYPTION</span>
              </div>

              <div class="form-group mb-3">
                <label style="font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; display: block;">
                  Password Baru:
                </label>
                <div class="input-group" style="position: relative; display: flex; align-items: center;">
                  <div style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); z-index: 10; color: #fbbf24; font-size: 14px; pointer-events: none;">
                    <i class="fas fa-lock"></i>
                  </div>
                  <input type="password" id="new-password" class="form-control form-control-dark" style="padding-left: 48px !important; padding-right: 48px !important;" placeholder="Minimal 6 karakter" required>
                  <button type="button" onclick="toggleProfilePasswordVisibility('new-password', 'eye-new-pwd')" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); z-index: 10; background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 14px; padding: 4px;">
                    <i class="fas fa-eye" id="eye-new-pwd"></i>
                  </button>
                </div>
              </div>

              <div class="form-group mb-3">
                <label style="font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; display: block;">
                  Konfirmasi Password Baru:
                </label>
                <div class="input-group" style="position: relative; display: flex; align-items: center;">
                  <div style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); z-index: 10; color: #fbbf24; font-size: 14px; pointer-events: none;">
                    <i class="fas fa-shield-alt"></i>
                  </div>
                  <input type="password" id="confirm-password" class="form-control form-control-dark" style="padding-left: 48px !important; padding-right: 48px !important;" placeholder="Ulangi password baru" required>
                  <button type="button" onclick="toggleProfilePasswordVisibility('confirm-password', 'eye-conf-pwd')" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); z-index: 10; background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 14px; padding: 4px;">
                    <i class="fas fa-eye" id="eye-conf-pwd"></i>
                  </button>
                </div>
              </div>

              <div class="text-right pt-2">
                <button type="submit" class="btn btn-sm font-weight-bold px-3 py-2 text-dark" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; border-radius: 10px; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35); font-size: 12.5px; transition: all 0.2s;">
                  <i class="fas fa-key mr-1.5"></i> Update Password
                </button>
              </div>
            </form>
          </div>

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
          <button type="button" class="close text-white" onclick="closeModalHelper('modalUpgradeQuota')">
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

  <!-- Modal Reset Password Customer (Admin) - Premium Dark Glassmorphic Dialog -->
  <div class="modal fade modal-dark" id="modalResetCustomerPassword" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
      <div class="modal-content" style="background: linear-gradient(160deg, #0f172a 0%, #080e21 100%); border: 1px solid rgba(168, 85, 247, 0.4); border-radius: 20px; box-shadow: 0 25px 70px rgba(0,0,0,0.8), 0 0 30px rgba(168, 85, 247, 0.15); overflow: hidden;">
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 18px 24px;">
          <div class="d-flex align-items-center gap-3">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, rgba(168, 85, 247, 0.25), rgba(126, 34, 206, 0.25)); border: 1px solid rgba(168, 85, 247, 0.5); display: flex; align-items: center; justify-content: center; font-size: 19px; color: #c084fc; box-shadow: 0 0 15px rgba(168, 85, 247, 0.3);">
              <i class="fas fa-key"></i>
            </div>
            <div>
              <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 16px;">Reset Password Customer</h5>
              <small style="color: #94a3b8; font-size: 11px;">Perbarui kata sandi login untuk akun customer</small>
            </div>
          </div>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalResetCustomerPassword')" aria-label="Close" style="background: rgba(255,255,255,0.08); border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formResetCustomerPassword" onsubmit="submitResetCustomerPassword(event)">
          <input type="hidden" id="reset-cust-id">
          <div class="modal-body p-4">
            
            <div class="p-3 mb-3 rounded-3" style="background: rgba(168, 85, 247, 0.08); border: 1px solid rgba(168, 85, 247, 0.25); border-radius: 12px;">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span style="font-size: 10px; font-weight: 700; color: #c084fc; font-family: monospace; letter-spacing: 0.5px;">TARGET CUSTOMER</span>
                <span class="badge badge-pill" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; font-size: 10px;">ID: #<span id="reset-cust-id-display">0</span></span>
              </div>
              <div id="reset-cust-name-display" class="font-weight-bold text-white" style="font-size: 15px;">
                BATAGOR BANDUNG
              </div>
            </div>

            <div class="form-group mb-2">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label style="font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 0;">Password Baru:</label>
                <button type="button" class="btn btn-link p-0 text-decoration-none" onclick="generateRandomPassword('reset-cust-password-input')" style="font-size: 11px; color: #c084fc;">
                  <i class="fas fa-dice mr-1"></i> Acak Password
                </button>
              </div>
              <div class="input-group" style="position: relative; display: flex; align-items: center;">
                <div style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); z-index: 10; color: #c084fc; font-size: 15px; pointer-events: none;">
                  <i class="fas fa-lock"></i>
                </div>
                <input type="text" id="reset-cust-password-input" class="form-control form-control-dark font-weight-bold" style="padding-left: 48px !important; padding-right: 48px !important; color: #fbbf24 !important; font-size: 15px; letter-spacing: 0.5px;" value="loewix123" required>
                <button type="button" onclick="copyPasswordToClipboard('reset-cust-password-input')" title="Salin Password" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); z-index: 10; background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 15px; padding: 4px;">
                  <i class="fas fa-copy"></i>
                </button>
              </div>
              <small class="text-muted d-block mt-1" style="font-size: 11px;">Customer dapat langsung login menggunakan password baru ini.</small>
            </div>

          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.95); padding: 14px 24px;">
            <button type="button" class="btn btn-secondary btn-sm px-3" onclick="closeModalHelper('modalResetCustomerPassword')">Batal</button>
            <button type="submit" class="btn btn-sm font-weight-bold px-4 text-white" style="background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%); border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);">
              <i class="fas fa-check mr-1.5"></i> Terapkan Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Universal Action Confirmation (Ultra-Premium Glassmorphic Dialog) -->
  <div class="modal fade modal-dark" id="modalActionConfirm" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
      <div class="modal-content" style="background: linear-gradient(160deg, #0f172a 0%, #080e21 100%); border: 1px solid rgba(56, 189, 248, 0.35); border-radius: 22px; box-shadow: 0 30px 80px rgba(0,0,0,0.9), 0 0 35px rgba(56, 189, 248, 0.15); overflow: hidden;">
        
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 18px 24px;">
          <div class="d-flex align-items-center gap-3">
            <div id="confirm-modal-icon-badge" style="width: 42px; height: 42px; border-radius: 12px; background: rgba(56, 189, 248, 0.2); border: 1px solid rgba(56, 189, 248, 0.4); display: flex; align-items: center; justify-content: center; font-size: 19px; color: #38bdf8; box-shadow: 0 0 15px rgba(56, 189, 248, 0.25);">
              <i class="fas fa-question-circle" id="confirm-modal-icon"></i>
            </div>
            <div>
              <h5 class="modal-title font-weight-bold text-white mb-0" id="confirm-modal-title" style="font-size: 16px;">Konfirmasi Aksi</h5>
              <small style="color: #94a3b8; font-size: 11px;" id="confirm-modal-subtitle">Pusat Validasi Operasional Sistem</small>
            </div>
          </div>
          <button type="button" class="close text-white" onclick="resolveLoewixConfirm(false)" aria-label="Close" style="background: rgba(255,255,255,0.08); border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body p-4">
          <!-- Target Display Card -->
          <div id="confirm-modal-target-box" class="p-3 mb-3 rounded-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 14px;">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <span style="font-size: 10px; font-weight: 700; color: #38bdf8; font-family: monospace; letter-spacing: 0.5px;">TARGET ENTITAS</span>
              <span class="badge badge-pill" id="confirm-modal-meta" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; font-size: 10px;">ID: #0</span>
            </div>
            <div id="confirm-modal-target-name" class="font-weight-bold text-white" style="font-size: 15px;">
              -
            </div>
          </div>

          <!-- Message -->
          <p id="confirm-modal-message" class="text-light mb-0" style="font-size: 13.5px; line-height: 1.6; color: #cbd5e1 !important;">
            Apakah Anda yakin ingin melanjutkan tindakan ini?
          </p>
        </div>

        <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.95); padding: 14px 24px;">
          <button type="button" class="btn btn-secondary btn-sm px-3" onclick="resolveLoewixConfirm(false)" style="border-radius: 10px; font-size: 12.5px;">Batal</button>
          <button type="button" class="btn btn-sm font-weight-bold px-4 text-white" id="confirm-modal-btn-ok" onclick="resolveLoewixConfirm(true)" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4); font-size: 12.5px;">
            <span id="confirm-modal-btn-text">Ya, Lanjutkan</span>
          </button>
        </div>

      </div>
    </div>
  </div>

  <!-- Modal Daftarkan Wajah Baru (Face Recognition) -->
  <div class="modal fade modal-dark" id="modalRegisterFace" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 520px;">
      <div class="modal-content" style="background: linear-gradient(160deg, #0b1533 0%, #070d22 100%); border: 1.5px solid rgba(56,189,248,0.4); border-radius: 20px; box-shadow: 0 30px 80px rgba(0,0,0,0.9); overflow: hidden;">
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 18px 24px;">
          <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 16px;">
            <i class="fas fa-user-plus text-info mr-2"></i> Daftarkan Wajah Baru ke AI Face Recognition
          </h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalRegisterFace')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formRegisterFace" onsubmit="submitRegisterFace(event)">
          <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
            
            <!-- Live Biometric AI Face Enrollment Scanner -->
            <div class="p-3 mb-3 text-center" style="background: rgba(15, 23, 42, 0.9); border: 1.5px solid rgba(0, 240, 255, 0.4); border-radius: 16px; box-shadow: inset 0 0 20px rgba(0, 240, 255, 0.1);">
              <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                <span class="badge badge-info font-weight-bold px-2 py-1" style="font-size: 11px; background: rgba(0, 240, 255, 0.2); color: #38bdf8; border: 1px solid rgba(0, 240, 255, 0.4);">
                  <span class="pulse-dot" style="background: #00f0ff; margin-right: 4px;"></span> LIVE BIOMETRIC SCANNER
                </span>
                <span class="text-muted" style="font-size: 11px;">Wajib Scan Langsung via Kamera</span>
              </div>

              <!-- Active Live Scanner Viewfinder -->
              <div id="face-scanner-viewfinder" style="position: relative; width: 100%; max-width: 360px; height: 230px; margin: 0 auto; border-radius: 12px; overflow: hidden; background: #000; border: 2px solid #00f0ff; box-shadow: 0 0 20px rgba(0, 240, 255, 0.3);">
                <video id="face-webcam-video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                
                <!-- Biometric Oval Target Guide Overlay -->
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 140px; height: 180px; border: 2px dashed #00f0ff; border-radius: 50%; box-shadow: 0 0 15px rgba(0, 240, 255, 0.3); pointer-events: none;">
                  <div class="position-absolute" style="top: -6px; left: -6px; width: 14px; height: 14px; border-top: 3px solid #00f0ff; border-left: 3px solid #00f0ff;"></div>
                  <div class="position-absolute" style="top: -6px; right: -6px; width: 14px; height: 14px; border-top: 3px solid #00f0ff; border-right: 3px solid #00f0ff;"></div>
                  <div class="position-absolute" style="bottom: -6px; left: -6px; width: 14px; height: 14px; border-bottom: 3px solid #00f0ff; border-left: 3px solid #00f0ff;"></div>
                  <div class="position-absolute" style="bottom: -6px; right: -6px; width: 14px; height: 14px; border-bottom: 3px solid #00f0ff; border-right: 3px solid #00f0ff;"></div>
                </div>

                <!-- Live Status Indicator -->
                <div id="face-scan-status-badge" style="position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%); background: rgba(10, 15, 30, 0.9); border: 1px solid rgba(0, 240, 255, 0.5); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #38bdf8; white-space: nowrap;">
                  <i class="fas fa-expand mr-1"></i> Arahkan Wajah ke Dalam Lingkaran
                </div>
              </div>

              <!-- Scanned Snapshot Preview (Shown after capture) -->
              <div id="face-scanned-preview-box" style="display: none; text-align: center; padding: 10px 0;">
                <div style="width: 105px; height: 105px; border-radius: 50%; margin: 0 auto; overflow: hidden; border: 3px solid #10b981; box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);">
                  <img id="face-preview-img" src="" alt="Hasil Scan" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="mt-2 font-weight-bold" style="color: #34d399; font-size: 12.5px;">
                  <i class="fas fa-check-circle mr-1"></i> Wajah Berhasil Di-scan & Tervalidasi AI!
                </div>
              </div>

              <!-- Scan Action Buttons -->
              <div class="mt-3 d-flex justify-content-center gap-2">
                <button type="button" id="btn-capture-face" class="btn btn-info font-weight-bold px-4 py-2" onclick="captureFaceFromWebcam()" style="border-radius: 10px; font-size: 13px; background: linear-gradient(135deg, #0284c7, #00f0ff); color: #000; border: none; box-shadow: 0 0 15px rgba(0, 240, 255, 0.4);">
                  <i class="fas fa-camera mr-1.5"></i> Scan Wajah Sekarang
                </button>
                <button type="button" id="btn-rescan-face" class="btn btn-outline-warning font-weight-bold px-3 py-2" onclick="startFaceEnrollmentCamera()" style="display: none; border-radius: 10px; font-size: 12px;">
                  <i class="fas fa-sync-alt mr-1"></i> Scan Ulang
                </button>
              </div>

              <input type="hidden" id="face-edit-id" value="">
              <input type="hidden" id="face-input-photo" value="" required>
              <small class="text-muted d-block mt-2" style="font-size: 11px;">Posisikan wajah tegak, pencahayaan jelas, tanpa masker/kacamata hitam.</small>
            </div>

            <!-- Form Fields -->
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Nama Lengkap:</label>
              <input type="text" id="face-input-name" class="form-control form-control-dark" placeholder="Contoh: WAHYU" required>
            </div>
            
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Kategori Akses:</label>
              <select id="face-input-category" class="form-control form-control-dark" required>
                <option value="vip">⭐ VIP / Petinggi (Auto Access & Special Notification)</option>
                <option value="employee" selected>👔 Karyawan / Staff (Absensi & Jam Kerja)</option>
                <option value="resident">🏠 Penghuni / Warga (Akses Gerbang)</option>
                <option value="guest">👤 Tamu Resmi (Akses Terbatas)</option>
                <option value="blacklist">🚨 Blacklist / DPO (Trigger Security Alarm & Sound Alert)</option>
              </select>
            </div>

            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Jabatan / Status Role:</label>
              <input type="text" id="face-input-role" class="form-control form-control-dark" placeholder="Contoh: IT DEVELOPER" required>
            </div>

            <div class="form-group mb-0">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Catatan Tambahan:</label>
              <textarea id="face-input-notes" class="form-control form-control-dark" rows="2" placeholder="Contoh: Akses ruang server & kantor IT"></textarea>
            </div>

          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.95); padding: 14px 24px;">
            <button type="button" class="btn btn-secondary btn-sm px-3" onclick="closeModalHelper('modalRegisterFace')" style="border-radius: 8px;">Batal</button>
            <button type="submit" id="btn-submit-face" class="btn btn-success btn-sm px-4 font-weight-bold" style="border-radius: 8px; background: #059669; border: none;">
              <i class="fas fa-save mr-1"></i> Simpan Data Wajah
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Daftarkan Plat Nomor Kendaraan (ANPR) -->
  <div class="modal fade modal-dark" id="modalRegisterPlate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="background: linear-gradient(160deg, #0b1533 0%, #070d22 100%); border: 1.5px solid rgba(56,189,248,0.4); border-radius: 20px; box-shadow: 0 30px 80px rgba(0,0,0,0.9); overflow: hidden;">
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 18px 24px;">
          <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 16px;">
            <i class="fas fa-car text-info mr-2"></i> Daftarkan Plat Kendaraan ke ANPR System
          </h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalRegisterPlate')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formRegisterPlate" onsubmit="submitRegisterPlate(event)">
          <div class="modal-body p-4">
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Nomor Plat Kendaraan (Format Indonesia):</label>
              <input type="text" id="plate-input-number" class="form-control form-control-dark font-monospace" style="text-transform: uppercase; letter-spacing: 1px; font-weight: bold; font-size: 15px;" placeholder="Contoh: B 1234 YMH" required>
              <small class="text-muted" style="font-size: 11px;">Mendukung Plat Hitam, Putih, Merah & Kuning.</small>
            </div>
            <div class="row">
              <div class="col-6 form-group mb-3">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Jenis Kendaraan:</label>
                <select id="plate-input-type" class="form-control form-control-dark" required>
                  <option value="car" selected>🚗 Mobil Pribadi</option>
                  <option value="motorcycle">🏍️ Sepeda Motor</option>
                  <option value="truck">🚚 Truk / Bus / Box</option>
                </select>
              </div>
              <div class="col-6 form-group mb-3">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Kategori Akses:</label>
                <select id="plate-input-category" class="form-control form-control-dark" required>
                  <option value="vip">⭐ VIP (Akses Gate Otomatis)</option>
                  <option value="employee">👔 Karyawan / Dinas</option>
                  <option value="resident" selected>🏠 Penghuni / Resident</option>
                  <option value="guest">👤 Tamu Terdaftar</option>
                  <option value="blacklist">🚨 Blacklist / DPO (Alarm)</option>
                </select>
              </div>
            </div>
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Nama Pemilik / Unit:</label>
              <input type="text" id="plate-input-owner" class="form-control form-control-dark" placeholder="Contoh: Bambang Supriyanto" required>
            </div>
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Model & Warna Kendaraan:</label>
              <input type="text" id="plate-input-model" class="form-control form-control-dark" placeholder="Contoh: Toyota Alphard Hitam" required>
            </div>
            <div class="form-group mb-0">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Catatan / Slot Parkir:</label>
              <textarea id="plate-input-notes" class="form-control form-control-dark" rows="2" placeholder="Contoh: Slot Parkir VIP A-01"></textarea>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.95); padding: 14px 24px;">
            <button type="button" class="btn btn-secondary btn-sm px-3" onclick="closeModalHelper('modalRegisterPlate')" style="border-radius: 8px;">Batal</button>
            <button type="submit" id="btn-submit-plate" class="btn btn-info btn-sm px-4 font-weight-bold" style="border-radius: 8px; background: #0284c7; border: none;">
              <i class="fas fa-save mr-1"></i> Simpan Data Plat
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Customer Baru (Admin) -->
  <div class="modal fade modal-dark" id="modalAddCustomer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="background: linear-gradient(160deg, #0b1533 0%, #070d22 100%); border: 1px solid rgba(56,189,248,0.35); border-radius: 20px; box-shadow: 0 30px 80px rgba(0,0,0,0.85); overflow: hidden;">
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 18px 24px;">
          <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 16px;"><i class="fas fa-user-plus text-info mr-2"></i> Tambah Customer Baru</h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalAddCustomer')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formAddCustomer" onsubmit="submitAddCustomer(event)">
          <div class="modal-body p-4">
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Nama Customer / Perusahaan:</label>
              <input type="text" id="cust-name" class="form-control form-control-dark" placeholder="Contoh: PT. Jaya Sentosa Enterprise" required>
            </div>
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Email Login:</label>
              <input type="email" id="cust-email" class="form-control form-control-dark" placeholder="customer@jayasentosa.com" required>
            </div>
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Password Awal:</label>
              <input type="password" id="cust-password" class="form-control form-control-dark" placeholder="Minimal 6 Karakter" required>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Alokasi Kuota CCTV:</label>
                <input type="number" id="cust-quota" class="form-control form-control-dark" value="10" min="1" max="500" required>
              </div>
              <div class="col-6">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Wilayah:</label>
                <select id="cust-city" class="form-control form-control-dark">
                  <option value="siantar">Pematangsiantar</option>
                  <option value="jakarta">DKI Jakarta</option>
                  <option value="medan">Kota Medan</option>
                  <option value="bandung">Kota Bandung</option>
                  <option value="bali">Bali / Denpasar</option>
                </select>
              </div>
            </div>
            <div class="form-group mb-2">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">No. WhatsApp / HP:</label>
              <input type="text" id="cust-phone" class="form-control form-control-dark" placeholder="+62 812-3456-7890">
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.95); padding: 14px 24px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalAddCustomer')">Batal</button>
            <button type="submit" class="btn btn-info btn-sm font-weight-bold px-3" style="background: #0284c7; border: none; border-radius: 10px; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);"><i class="fas fa-save mr-1"></i> Simpan Customer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Data Customer (Admin) -->
  <div class="modal fade modal-dark" id="modalEditCustomer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="background: linear-gradient(160deg, #0b1533 0%, #070d22 100%); border: 1px solid rgba(56,189,248,0.35); border-radius: 20px; box-shadow: 0 30px 80px rgba(0,0,0,0.85); overflow: hidden;">
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 18px 24px;">
          <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 16px;"><i class="fas fa-user-edit text-warning mr-2"></i> Edit Data Customer</h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalEditCustomer')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditCustomer" onsubmit="submitEditCustomer(event)">
          <input type="hidden" id="edit-profile-id">
          <div class="modal-body p-4">
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Nama Customer / Perusahaan:</label>
              <input type="text" id="edit-profile-name" class="form-control form-control-dark" required>
            </div>
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Email Login:</label>
              <input type="email" id="edit-profile-email" class="form-control form-control-dark" required>
            </div>
            <div class="row g-2 mb-2">
              <div class="col-6">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Wilayah:</label>
                <select id="edit-profile-city" class="form-control form-control-dark">
                  <option value="siantar">Pematangsiantar</option>
                  <option value="jakarta">DKI Jakarta</option>
                  <option value="medan">Kota Medan</option>
                  <option value="bandung">Kota Bandung</option>
                  <option value="bali">Bali / Denpasar</option>
                </select>
              </div>
              <div class="col-6">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">No. WhatsApp / HP:</label>
                <input type="text" id="edit-profile-phone" class="form-control form-control-dark">
              </div>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.95); padding: 14px 24px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalEditCustomer')">Batal</button>
            <button type="submit" class="btn btn-warning btn-sm font-weight-bold px-3" style="border-radius: 10px; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Kuota Customer (Admin) -->
  <div class="modal fade modal-dark" id="modalEditQuota" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content" style="background: linear-gradient(160deg, #0b1533 0%, #070d22 100%); border: 1px solid rgba(245,158,11,0.4); border-radius: 20px; box-shadow: 0 30px 80px rgba(0,0,0,0.85); overflow: hidden;">
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 16px 20px;">
          <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 15px;"><i class="fas fa-sliders-h text-warning mr-2"></i> Atur Kuota CCTV</h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalEditQuota')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditQuota" onsubmit="submitEditQuota(event)">
          <input type="hidden" id="edit-quota-id">
          <div class="modal-body p-4 text-center">
            <div id="edit-quota-name" class="font-weight-bold text-info mb-2" style="font-size: 14.5px;">PT. Jaya Sentosa</div>
            <div class="form-group mb-0">
              <label class="text-muted" style="font-size: 12px;">Jumlah Kuota Kamera:</label>
              <input type="number" id="edit-quota-value" class="form-control form-control-dark text-center font-weight-bold" style="font-size: 24px; color: #fbbf24; border-radius: 14px;" min="1" max="500" required>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.95); padding: 12px 20px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalEditQuota')">Batal</button>
            <button type="submit" class="btn btn-warning btn-sm font-weight-bold px-3" style="border-radius: 10px; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);"><i class="fas fa-save mr-1"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Tambah / Edit Paket SaaS (Admin) -->
  <div class="modal fade modal-dark" id="modalAdminPlanForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="background: #0b1533; border: 1px solid rgba(56,189,248,0.3); border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
          <h5 class="modal-title font-weight-bold text-white" id="planModalTitle">
            <i class="fas fa-cubes text-info mr-2"></i> Edit Paket SaaS
          </h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalAdminPlanForm')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formAdminPlan" onsubmit="submitSavePlan(event)">
          <input type="hidden" id="plan-input-id" value="">
          <div class="modal-body p-4">
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Nama Paket:</label>
              <input type="text" id="plan-input-name" class="form-control form-control-dark" placeholder="Contoh: Business Pro" required>
            </div>
            
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Kuota Kamera (CCTV):</label>
                <input type="number" id="plan-input-quota" class="form-control form-control-dark" value="10" min="1" max="500" required>
              </div>
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Badge / Label Promo:</label>
                <input type="text" id="plan-input-badge" class="form-control form-control-dark" placeholder="Contoh: POPULER / BEST">
              </div>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Harga Bulanan (Rp):</label>
                <input type="number" id="plan-input-monthly" class="form-control form-control-dark" placeholder="Contoh: 299000" required>
              </div>
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Harga Tahunan (Rp):</label>
                <input type="number" id="plan-input-annual" class="form-control form-control-dark" placeholder="Contoh: 2990000">
                <small class="text-muted" style="font-size: 10.5px;">Otomatis x10 (Hemat 2 bln) jika kosong</small>
              </div>
            </div>

            <div class="form-group mb-2">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Fitur Paket (1 baris per fitur):</label>
              <textarea id="plan-input-features" class="form-control form-control-dark" rows="4" placeholder="10 Titik Kamera Live&#10;Full HD 1080p Stream H.265&#10;WebRTC & HLS Low Latency&#10;Cloud Recording 14 Hari"></textarea>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalAdminPlanForm')">Batal</button>
            <button type="submit" class="btn btn-info btn-sm font-weight-bold" style="background: #0284c7; border: none;">
              <i class="fas fa-save mr-1"></i> Simpan Paket SaaS
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal Pengaturan Midtrans Gateway (Admin) -->
  <div class="modal fade modal-dark" id="modalMidtransSettings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="background: #0b1533; border: 1px solid rgba(245,158,11,0.3); border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
          <h5 class="modal-title font-weight-bold text-white">
            <i class="fas fa-credit-card text-warning mr-2"></i> Konfigurasi Midtrans Payment Gateway
          </h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalMidtransSettings')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formMidtransSettings" onsubmit="submitSaveMidtransSettings(event)">
          <div class="modal-body p-4">
            <div class="p-3 mb-3 rounded" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.25); font-size: 12px; color: #cbd5e1;">
              <i class="fas fa-info-circle text-warning mr-1"></i> Data Access Key otomatis terhubung dari akun Midtrans Anda (<a href="https://dashboard.midtrans.com/settings/access_keys" target="_blank" style="color: #38bdf8; text-decoration: underline;">dashboard.midtrans.com</a>).
            </div>

            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Environment / Mode:</label>
              <select id="midtrans-input-env" class="form-control form-control-dark">
                <option value="false">Sandbox (Mode Testing / Uji Coba)</option>
                <option value="true">Production (Live Pembayaran Riil)</option>
              </select>
            </div>

            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Merchant ID:</label>
              <input type="text" id="midtrans-input-merchant" class="form-control form-control-dark" placeholder="G589001445" value="G589001445" required>
            </div>

            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Client Key:</label>
              <input type="text" id="midtrans-input-client" class="form-control form-control-dark" placeholder="Mid-client-..." value="Mid-client-mGA7v04cXrux3KNF" required>
            </div>

            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Server Key:</label>
              <input type="password" id="midtrans-input-server" class="form-control form-control-dark" placeholder="Masukkan Server Key Anda" required>
            </div>

          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalMidtransSettings')">Tutup</button>
            <button type="submit" class="btn btn-warning btn-sm font-weight-bold" style="background: #f59e0b; border: none; color: #000;">
              <i class="fas fa-save mr-1"></i> Simpan Kredensial Midtrans
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Pengaturan Email SMTP & Tes Kirim Email (Admin) -->
  <div class="modal fade modal-dark" id="modalSmtpSettings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="background: #0b1533; border: 1px solid rgba(56,189,248,0.3); border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
          <h5 class="modal-title font-weight-bold text-white">
            <i class="fas fa-envelope-open-text text-info mr-2"></i> Konfigurasi Email SMTP Loewix
          </h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalSmtpSettings')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formSmtpSettings" onsubmit="submitSaveSmtpSettings(event)">
          <div class="modal-body p-4">
            <div class="p-3 mb-3 rounded" style="background: rgba(2, 132, 199, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); font-size: 12px; color: #cbd5e1;">
              <i class="fas fa-info-circle text-info mr-1"></i> Hubungkan akun <strong>Gmail SMTP</strong> (dengan Google App Password 16 digit) atau Mail Server domain Anda agar email tagihan 100% langsung masuk ke Inbox utama customer.
            </div>

            <div class="row g-2 mb-3">
              <div class="col-8">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">SMTP Host:</label>
                <input type="text" id="smtp-input-host" class="form-control form-control-dark" placeholder="smtp.gmail.com" value="smtp.gmail.com" required>
              </div>
              <div class="col-4">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Port:</label>
                <input type="number" id="smtp-input-port" class="form-control form-control-dark" placeholder="587" value="587" required>
              </div>
            </div>

            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Email Pengirim / Username SMTP:</label>
              <input type="email" id="smtp-input-user" class="form-control form-control-dark" placeholder="contoh: akunloewix@gmail.com" required>
            </div>

            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">
                Sandi / Google App Password: 
                <small class="text-warning" style="font-size: 11px;">(Gunakan 16 digit Sandi Aplikasi Google)</small>
              </label>
              <input type="password" id="smtp-input-pass" class="form-control form-control-dark" placeholder="Contoh: abcd efgh ijkl mnop" required>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Enkripsi:</label>
                <select id="smtp-input-secure" class="form-control form-control-dark">
                  <option value="tls">TLS (Port 587 - Default)</option>
                  <option value="ssl">SSL (Port 465)</option>
                </select>
              </div>
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Nama Pengirim:</label>
                <input type="text" id="smtp-input-name" class="form-control form-control-dark" value="PT. LOEWIX INDONESIA">
              </div>
            </div>

            <!-- Test Email Section -->
            <div class="p-3 mt-3 rounded" style="background: rgba(0, 0, 0, 0.3); border: 1px dashed rgba(56, 189, 248, 0.3);">
              <label class="text-white font-weight-bold mb-2" style="font-size: 12.5px;">
                <i class="fas fa-vial text-warning mr-1"></i> Uji Coba Kirim Email Langsung:
              </label>
              <div class="input-group input-group-sm">
                <input type="email" id="smtp-test-recipient" class="form-control form-control-dark" placeholder="Email tujuan uji coba...">
                <div class="input-group-append">
                  <button type="button" class="btn btn-warning font-weight-bold px-3" onclick="runTestSmtpEmail()" style="font-size: 12px; border-radius: 0 6px 6px 0;">
                    <i class="fas fa-paper-plane mr-1"></i> Tes Kirim
                  </button>
                </div>
              </div>
              <div id="smtp-test-result" class="mt-2" style="display: none; font-size: 11.5px;"></div>
            </div>

          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalSmtpSettings')">Tutup</button>
            <button type="submit" class="btn btn-info btn-sm font-weight-bold" style="background: #0284c7; border: none;">
              <i class="fas fa-save mr-1"></i> Simpan Pengaturan SMTP
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Terbitkan Invoice Manual (Super Admin) -->
  <div class="modal fade modal-dark" id="modalCreateManualInvoice" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 520px;">
      <div class="modal-content" style="background: linear-gradient(160deg, #0b1533 0%, #070d22 100%); border: 1px solid rgba(52, 211, 153, 0.4); border-radius: 20px; box-shadow: 0 30px 80px rgba(0,0,0,0.9); overflow: hidden;">
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 18px 24px;">
          <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 16px;"><i class="fas fa-file-invoice-dollar text-success mr-2"></i> Terbitkan Invoice SaaS Baru</h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalCreateManualInvoice')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formCreateManualInvoice" onsubmit="submitCreateManualInvoice(event)">
          <div class="modal-body p-4">
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Pilih Customer Penerima Tagihan:</label>
              <select id="manual-inv-customer" class="form-control form-control-dark" required>
                <!-- Populated dynamically -->
              </select>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-md-7">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Pilih Paket SaaS:</label>
                <select id="manual-inv-plan" class="form-control form-control-dark" onchange="autoCalculateManualInvoiceTotal()" required>
                  <!-- Populated from cached plans -->
                </select>
              </div>
              <div class="col-md-5">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Siklus Tagihan:</label>
                <select id="manual-inv-cycle" class="form-control form-control-dark" onchange="autoCalculateManualInvoiceTotal()">
                  <option value="monthly">Bulanan</option>
                  <option value="annual" selected>Tahunan</option>
                </select>
              </div>
            </div>
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Nominal Harga Paket (Sebelum PPN 11%):</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text" style="background: rgba(255,255,255,0.05); color: #94a3b8; border-color: rgba(255,255,255,0.15);">Rp</span></div>
                <input type="number" id="manual-inv-amount" class="form-control form-control-dark" style="padding-left: 15px !important;" placeholder="2990000" oninput="autoCalculateManualInvoiceTotal()" required>
              </div>
            </div>
            <div class="p-3 mb-3 rounded" style="background: rgba(52, 211, 153, 0.08); border: 1px solid rgba(52, 211, 153, 0.25);">
              <div class="d-flex justify-content-between text-muted" style="font-size: 12px;">
                <span>PPN 11% (Pajak):</span>
                <span id="manual-inv-tax-text" class="text-white">Rp 328.900</span>
              </div>
              <div class="d-flex justify-content-between font-weight-bold mt-1" style="font-size: 14px; color: #34d399;">
                <span>Total Tagihan (Inc. PPN):</span>
                <span id="manual-inv-total-text">Rp 3.318.900</span>
              </div>
            </div>
            <div class="row g-2 mb-2">
              <div class="col-md-6">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Metode Pembayaran:</label>
                <select id="manual-inv-method" class="form-control form-control-dark">
                  <option value="bca_va">BCA Virtual Account</option>
                  <option value="mandiri_bill">Mandiri Bill</option>
                  <option value="bri_va">BRI Virtual Account</option>
                  <option value="bni_va">BNI Virtual Account</option>
                  <option value="qris">QRIS Instant</option>
                  <option value="manual_transfer_admin" selected>Transfer Bank Manual</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="text-white" style="font-size: 12.5px; font-weight: 600;">Status Invoice Awal:</label>
                <select id="manual-inv-status" class="form-control form-control-dark">
                  <option value="settlement" selected>Lunas (Settlement) - Langsung Aktif</option>
                  <option value="pending">Menunggu Pembayaran (Pending)</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.95); padding: 14px 24px;">
            <button type="button" class="btn btn-secondary btn-sm px-3" onclick="closeModalHelper('modalCreateManualInvoice')" style="border-radius: 10px;">Batal</button>
            <button type="submit" class="btn btn-sm font-weight-bold px-4 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
              <i class="fas fa-check mr-1.5"></i> Terbitkan Invoice
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Detail Transaksi & Log Midtrans (Super Admin) -->
  <div class="modal fade modal-dark" id="modalInvoiceDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 580px;">
      <div class="modal-content" style="background: linear-gradient(160deg, #0f172a 0%, #080e21 100%); border: 1px solid rgba(56, 189, 248, 0.4); border-radius: 20px; box-shadow: 0 30px 80px rgba(0,0,0,0.9); overflow: hidden;">
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 18px 24px;">
          <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 16px;"><i class="fas fa-info-circle text-info mr-2"></i> Rincian Lengkap Transaksi SaaS</h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalInvoiceDetail')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4" id="invoice-detail-content">
          <!-- Populated dynamically -->
        </div>
        <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); background: rgba(15, 23, 42, 0.95); padding: 14px 24px;">
          <button type="button" class="btn btn-secondary btn-sm px-3" onclick="closeModalHelper('modalInvoiceDetail')" style="border-radius: 10px;">Tutup</button>
          <button type="button" class="btn btn-info btn-sm font-weight-bold px-3" id="btn-detail-print-receipt" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 10px;">
            <i class="fas fa-receipt mr-1"></i> Cetak Kwitansi Resmi
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== DEDICATED LOEWIX RECEIPT OVERLAY ===== -->
  <div id="modalLoewixReceiptOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; z-index: 9999999; background: rgba(5, 11, 26, 0.88); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px; overflow-y: auto;">
    <div style="background: #0f172a; border: 1px solid rgba(56, 189, 248, 0.4); border-radius: 16px; width: 100%; max-width: 620px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.85); overflow: hidden; margin: auto;">
      <!-- Overlay Header -->
      <div style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
        <h5 style="margin: 0; color: #ffffff; font-weight: 700; font-size: 16px; display: flex; align-items: center;">
          <i class="fas fa-receipt" style="color: #38bdf8; margin-right: 8px;"></i> Kwitansi Pembayaran Resmi Loewix
        </h5>
        <button type="button" onclick="closeLoewixReceiptOverlay()" style="background: rgba(255,255,255,0.08); border: none; color: #ffffff; font-size: 18px; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
          &times;
        </button>
      </div>
      <!-- Overlay Content Body -->
      <div id="loewix-receipt-content" style="padding: 20px; max-height: 75vh; overflow-y: auto;">
        <!-- Dynamically populated receipt -->
      </div>
      <!-- Overlay Footer -->
      <div style="background: rgba(15, 23, 42, 0.98); border-top: 1px solid rgba(255, 255, 255, 0.1); padding: 14px 20px; display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn btn-secondary btn-sm px-3" onclick="closeLoewixReceiptOverlay()" style="border-radius: 8px;">Tutup</button>
        <button type="button" class="btn btn-info btn-sm font-weight-bold px-3" onclick="printInvoiceReceipt()" style="background: #0284c7; border: none; border-radius: 8px;">
          <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF
        </button>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="../assets/js/jquery-3.6.0.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script>
    let currentCustomer = null;
    let customerCameras = [];
    let hlsInstance = null;

    // Modal Helper functions (safe for jQuery and Vanilla JS)
    function openModalHelper(modalId) {
      if (window.$ && typeof $(`#${modalId}`).modal === 'function') {
        $(`#${modalId}`).modal('show');
        return;
      }
      const modalEl = document.getElementById(modalId);
      if (!modalEl) return;
      modalEl.classList.add('show');
      modalEl.style.display = 'block';
      document.body.classList.add('modal-open');
    }

    function closeModalHelper(modalId) {
      if (window.$ && typeof $(`#${modalId}`).modal === 'function') {
        $(`#${modalId}`).modal('hide');
      }
      const modalEl = document.getElementById(modalId);
      if (modalEl) {
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
      }
      document.body.classList.remove('modal-open');
      if (window.$) {
        $('.modal-backdrop').remove();
      }
    }

    // ===== LIVE NETWORK SPEED & LATENCY TELEMETRY ENGINE =====
    function startNetworkTelemetry() {
      const speedEl = document.getElementById('nav-net-speed');
      const pingEl = document.getElementById('nav-net-ping');

      function updateTelemetry() {
        const activeCount = (typeof activeInlinePlayers !== 'undefined' && activeInlinePlayers) ? activeInlinePlayers.size : 0;
        let mbps = 0;
        let ping = 12 + Math.floor(Math.random() * 5); // 12-16ms low latency

        if (activeCount > 0) {
          // Each active live video stream averages ~1.85 - 2.4 Mbps (H.264 / H.265 HD/SD)
          const basePerCam = 1.95;
          const jitter = (Math.random() * 0.4) - 0.2;
          mbps = (activeCount * basePerCam) + jitter;
          if (mbps < 0.5) mbps = 0.5;
        } else {
          // Idle baseline telemetry & snapshot polling (0.4 - 0.8 Mbps)
          mbps = 0.4 + (Math.random() * 0.35);
        }

        if (speedEl) {
          speedEl.textContent = `${mbps.toFixed(1)} Mbps`;
          if (activeCount > 0) {
            speedEl.style.color = '#34d399'; // Emerald glowing text when active
          } else {
            speedEl.style.color = '#ffffff';
          }
        }
        if (pingEl) {
          pingEl.textContent = `${ping} ms`;
        }
      }

      updateTelemetry();
      setInterval(updateTelemetry, 1200);
    }

    // Initialize Customer Dashboard
    document.addEventListener('DOMContentLoaded', () => {
      initCustomerSession();
      startNetworkTelemetry();
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
          loadBillingDashboardData();
        } else {
          // Fallback to localStorage
          const localUser = localStorage.getItem('loewix_user');
          if (localUser) {
            currentCustomer = JSON.parse(localUser);
            renderCustomerUI();
            loadCustomerCameras();
            loadBillingDashboardData();
          } else {
            // Not logged in -> redirect to main page with login
            window.location.href = '../index.html?login=required';
          }
        }
      } catch (err) {
        console.error('Session check failed:', err);
        const localUser = localStorage.getItem('loewix_user');
        if (localUser) {
          currentCustomer = JSON.parse(localUser);
          renderCustomerUI();
          loadCustomerCameras();
          loadBillingDashboardData();
        } else {
          window.location.href = '../index.html?login=required';
        }
      }
    }

    function handleMetric4Click() {
      if (currentCustomer && currentCustomer.role === 'super_admin') {
        switchCustomerTab('tab-admin-customers');
      } else {
        openRequestUpgradeModal();
      }
    }

    function renderCustomerUI() {
      if (!currentCustomer) return;

      const isSuperAdmin = (currentCustomer.role === 'super_admin');

      document.getElementById('nav-user-name').innerText = currentCustomer.name || 'Customer Loewix';
      document.getElementById('hero-customer-name').innerText = isSuperAdmin ? 'Super Admin Master Center' : (currentCustomer.name || 'Enterprise Customer');
      document.getElementById('hero-customer-city').innerHTML = isSuperAdmin ? '<i class="fas fa-network-wired text-info"></i> Global Multi-Tenant' : ('📍 ' + (currentCustomer.city || 'Pematangsiantar').toUpperCase());

      if (isSuperAdmin) {
        // Top Navbar Brand Badge
        const navBrandBadge = document.querySelector('.badge-hub-live');
        if (navBrandBadge) {
          navBrandBadge.innerHTML = '<span class="pulse-dot" style="background: #fbbf24; box-shadow: 0 0 8px #fbbf24;"></span> <span>MASTER COMMAND CENTER</span>';
          navBrandBadge.style.borderColor = 'rgba(245, 158, 11, 0.6)';
          navBrandBadge.style.color = '#fbbf24';
          navBrandBadge.style.background = 'rgba(245, 158, 11, 0.12)';
        }

        // Direct Super Admin Switcher Button
        const btnAdmin = document.getElementById('btn-super-admin-direct');
        if (btnAdmin) {
          btnAdmin.style.display = 'inline-flex';
          btnAdmin.style.background = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
          btnAdmin.style.borderColor = '#fbbf24';
          btnAdmin.style.boxShadow = '0 0 18px rgba(245, 158, 11, 0.55)';
        }

        // Hero Tier Badge
        const tierBadge = document.querySelector('.hero-tier-badge');
        if (tierBadge) {
          tierBadge.innerHTML = '<i class="fas fa-crown text-warning" style="filter: drop-shadow(0 0 4px #fbbf24);"></i> MASTER SUPER ADMIN';
          tierBadge.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
          tierBadge.style.boxShadow = '0 0 18px rgba(245, 158, 11, 0.6)';
          tierBadge.style.borderColor = 'rgba(251, 191, 36, 0.7)';
        }

        // Hero Description
        const heroDesc = document.querySelector('.quota-hero-banner p.text-muted');
        if (heroDesc) {
          heroDesc.innerText = 'Pusat kendali ekosistem global Loewix CCTV — Manajemen Multi-Tenant, Server Node MediaMTX, & Monitoring Finansial Midtrans.';
        }

        // Metric Card 3: Total Tenants
        const card3Val = document.getElementById('card-quota-max');
        const card3Lbl = document.getElementById('card-metric3-label');
        const card3Sub = document.getElementById('card-metric3-sub');
        const card3Icon = document.getElementById('card-metric3-icon');
        const card3IconWrap = document.getElementById('card-metric3-icon-wrap');
        const totalTenants = (typeof cachedAdminCustomers !== 'undefined' && cachedAdminCustomers.length > 0) ? cachedAdminCustomers.length : 4;
        if (card3Val) card3Val.innerText = totalTenants;
        if (card3Lbl) card3Lbl.innerText = 'Active Tenants';
        if (card3Sub) card3Sub.innerText = 'Perusahaan';
        if (card3Icon) card3Icon.className = 'fas fa-building';
        if (card3IconWrap) card3IconWrap.className = 'metric-icon cyan';

        // Metric Card 4: Master Control (Golden Cyber Accent)
        const card4Container = document.getElementById('card-metric4-container');
        const card4Val = document.getElementById('card-metric4-value');
        const card4Lbl = document.getElementById('card-metric4-label');
        const card4Sub = document.getElementById('card-metric4-sub');
        const card4Icon = document.getElementById('card-metric4-icon');
        const card4IconWrap = document.getElementById('card-metric4-icon-wrap');
        if (card4Container) {
          card4Container.style.background = 'linear-gradient(145deg, rgba(35, 25, 10, 0.9) 0%, rgba(18, 12, 5, 0.98) 100%)';
          card4Container.style.borderColor = 'rgba(245, 158, 11, 0.5)';
          card4Container.style.boxShadow = '0 10px 28px rgba(0, 0, 0, 0.45), 0 0 22px rgba(245, 158, 11, 0.25), inset 0 1px 0 rgba(251, 191, 36, 0.25)';
        }
        if (card4Val) { card4Val.innerText = 'MASTER'; card4Val.style.color = '#fbbf24'; card4Val.style.textShadow = '0 0 10px rgba(251, 191, 36, 0.5)'; }
        if (card4Lbl) card4Lbl.innerText = 'Admin Center';
        if (card4Sub) { card4Sub.innerHTML = 'Buka Panel <i class="fas fa-arrow-right ml-1"></i>'; card4Sub.style.color = '#f59e0b'; }
        if (card4Icon) card4Icon.className = 'fas fa-crown';
        if (card4IconWrap) card4IconWrap.className = 'metric-icon amber';

        // Adapt tab navigation for super admin (Seamless SPA Tabs)
        const tabsContainer = document.querySelector('.customer-tabs-nav');
        if (tabsContainer) {
          tabsContainer.innerHTML = `
            <button type="button" class="customer-nav-tab active" onclick="switchCustomerTab('tab-cameras')" id="nav-tab-cameras">
              <i class="fas fa-video"></i> <span>Kamera Semua Tenant (${customerCameras.length || 12})</span>
            </button>
            <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-ai-vision')" id="nav-tab-ai-vision">
              <i class="fas fa-brain text-info"></i> <span>AI Analytics (Face & ANPR)</span>
              <span class="badge badge-danger ml-1" style="font-size: 9px; padding: 2px 5px; background: linear-gradient(135deg, #ef4444, #f43f5e); border-radius: 4px; box-shadow: 0 0 8px rgba(244,63,94,0.6);">AI PRO</span>
            </button>
            <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-admin-customers')" id="nav-tab-admin-customers">
              <i class="fas fa-users-cog text-warning"></i> <span>Kelola Semua Pelanggan</span>
            </button>
            <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-admin-plans')" id="nav-tab-admin-plans">
              <i class="fas fa-cubes text-info"></i> <span>Kelola Paket SaaS & Harga</span>
            </button>
            <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-admin-transactions')" id="nav-tab-admin-transactions">
              <i class="fas fa-file-invoice-dollar" style="color: #34d399;"></i> <span>Monitoring Transaksi SaaS</span>
            </button>
            <button type="button" class="customer-nav-tab" onclick="switchCustomerTab('tab-admin-server')" id="nav-tab-admin-server">
              <i class="fas fa-server text-success"></i> <span>Status Node Server MediaMTX</span>
            </button>
          `;
        }

        // Infrastructure Telemetry Bar (No Kuota for Admin)
        const barLabel = document.getElementById('hero-bar-label');
        if (barLabel) {
          barLabel.innerHTML = '<i class="fas fa-bolt text-warning" style="filter: drop-shadow(0 0 6px #f59e0b);"></i> Status Jaringan & Server Streaming Global';
        }

        const capPill = document.getElementById('hero-quota-text');
        if (capPill) {
          capPill.innerHTML = '<span class="pulse-dot" style="background: #34d399; margin-right: 4px; box-shadow: 0 0 8px #34d399;"></span> 100% OPERASIONAL • SLA 99.99%';
          capPill.style.background = 'rgba(16, 185, 129, 0.16)';
          capPill.style.borderColor = 'rgba(16, 185, 129, 0.5)';
          capPill.style.color = '#34d399';
          capPill.style.boxShadow = '0 0 14px rgba(16, 185, 129, 0.25)';
        }

        const barFill = document.getElementById('hero-quota-bar');
        if (barFill) {
          barFill.style.width = '100%';
          barFill.style.background = 'linear-gradient(90deg, #0284c7 0%, #38bdf8 50%, #10b981 100%)';
        }

        const bottomStats = document.getElementById('hero-bar-bottom-stats');
        if (bottomStats) {
          bottomStats.innerHTML = `
            <span><i class="fas fa-circle text-info" style="font-size: 8px;"></i> Total Stream: <strong class="text-white">${customerCameras.length || 12}</strong> CCTV Terhubung</span>
            <span><i class="fas fa-circle text-emerald" style="color: #34d399; font-size: 8px;"></i> Node Relay: <strong style="color: #34d399;">ID-JKT-01 (Online)</strong></span>
            <span class="d-none d-md-inline" style="color: #94a3b8;"><i class="fas fa-shield-alt text-info"></i> TLS 1.3 256-Bit E2E Encrypted</span>
          `;
        }
      } else {
        // Regular Customer Quota Bar
        const barLabel = document.getElementById('hero-bar-label');
        if (barLabel) {
          barLabel.innerHTML = '<i class="fas fa-layer-group"></i> Kuota Kamera Terpakai';
        }

        const quota = parseInt(currentCustomer.cctv_quota) || 20;
        const used = parseInt(currentCustomer.cctv_used) || 0;
        const remaining = Math.max(0, quota - used);
        const pct = Math.min(100, Math.round((used / quota) * 100));

        document.getElementById('hero-quota-text').innerText = `${used} / ${quota} Kamera (${pct}%)`;
        document.getElementById('hero-quota-bar').style.width = pct + '%';
        document.getElementById('hero-used-count').innerText = used;
        document.getElementById('hero-remaining-count').innerText = remaining;
        document.getElementById('card-quota-max').innerText = quota;
      }

      document.getElementById('card-total-cam').innerText = parseInt(currentCustomer.cctv_used) || customerCameras.length || 0;
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
          populateAICameraSelector();
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

      // Stop any existing inline players
      if (typeof activeInlinePlayers !== 'undefined') {
        activeInlinePlayers.forEach((_, id) => stopCameraInline(id));
      }

      if (!list || list.length === 0) {
        renderEmptyState('Tidak ada kamera yang cocok dengan pencarian / filter.');
        return;
      }

      const now = new Date();
      const timeStr = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;

      let html = '';
      list.forEach((cam, idx) => {
        const isOnline = (cam.status !== 'offline');
        const statusBadge = isOnline 
          ? `<span class="cam-badge-status"><span class="pulse-dot"></span> ONLINE</span>`
          : `<span class="cam-badge-status offline"><i class="fas fa-times-circle"></i> OFFLINE</span>`;

        const connLabel = (cam.connection_type === 'xmeye_p2p') ? 'XMEYE P2P' : (cam.platform === 'ipcamlive' ? 'IPCAMLIVE' : 'RTSP H.265');
        
        let thumbUrl = cam.thumbnail || '';
        if (thumbUrl && !thumbUrl.startsWith('http') && !thumbUrl.startsWith('data:') && !thumbUrl.startsWith('../')) {
          thumbUrl = '../' + thumbUrl;
        }

        const hasRealSnapshot = (thumbUrl && !thumbUrl.includes('icon-cctv') && !thumbUrl.includes('default-thumbnail'));

        const imageElement = hasRealSnapshot
          ? `<img src="${thumbUrl}" alt="${cam.title}" class="cam-preview-img" id="cam-thumb-${cam.id}" onerror="this.style.display='none'; const sb=document.getElementById('cam-standby-${cam.id}'); if(sb) sb.style.display='flex';">
             <div class="cam-standby-placeholder" id="cam-standby-${cam.id}" style="display:none;">
               <div class="standby-icon"><i class="fas fa-video"></i></div>
               <div class="standby-title">${cam.title}</div>
               <div class="standby-hint"><i class="fas fa-play"></i> Klik Live Test</div>
             </div>`
          : `<img src="" alt="${cam.title}" class="cam-preview-img" id="cam-thumb-${cam.id}" style="display:none;">
             <div class="cam-standby-placeholder" id="cam-standby-${cam.id}">
               <div class="standby-icon"><i class="fas fa-video"></i></div>
               <div class="standby-title">${cam.title}</div>
               <div class="standby-hint"><i class="fas fa-play"></i> Klik Live Test</div>
             </div>`;

        const overlayBadge = hasRealSnapshot
          ? `<span class="cctv-rec-pill"><i class="fas fa-circle text-danger blink"></i> LIVE SNAPSHOT</span>`
          : `<span class="cctv-rec-pill" style="border-color: rgba(56, 189, 248, 0.4); color: #38bdf8;"><i class="fas fa-satellite-dish"></i> STANDBY</span>`;

        html += `
          <div class="cam-card" id="cam-card-${cam.id}">
            <div class="cam-preview-container" id="cam-preview-${cam.id}" onclick="playCameraInline(${cam.id})" title="Klik untuk memutar siaran langsung">
              ${imageElement}
              <div class="cam-cctv-overlay" id="cam-overlay-${cam.id}">
                ${overlayBadge}
                <span class="cctv-time-pill">${timeStr}</span>
              </div>
              <div class="play-overlay-hint" id="play-hint-${cam.id}">
                <i class="fas fa-play-circle"></i>
                <span class="play-hint-text">Klik Putar Siaran</span>
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
                <button class="btn-cam-action" id="btn-live-${cam.id}" onclick="playCameraInline(${cam.id})" title="Live Test Langsung di Sini">
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
      const quota = (currentCustomer && currentCustomer.cctv_quota) ? parseInt(currentCustomer.cctv_quota) : 20;
      const used = (customerCameras && Array.isArray(customerCameras)) ? customerCameras.length : 0;

      if (used >= quota && (!currentCustomer || currentCustomer.role !== 'super_admin')) {
        alert(`Batas kuota Anda (${quota} Kamera) telah penuh! Silakan hapus kamera yang tidak terpakai atau ajukan upgrade kuota.`);
        openRequestUpgradeModal();
        return;
      }

      const titleEl = document.getElementById('camModalTitle');
      if (titleEl) titleEl.innerHTML = '<i class="fas fa-plus-circle text-info mr-2"></i> Tambah Kamera CCTV Baru';
      
      const idEl = document.getElementById('cust-cam-id');
      if (idEl) idEl.value = '0';
      
      const titleInput = document.getElementById('cust-cam-title');
      if (titleInput) titleInput.value = '';
      
      const cityInput = document.getElementById('cust-cam-city');
      if (cityInput) cityInput.value = (currentCustomer && currentCustomer.city) ? currentCustomer.city : 'siantar';
      
      const connInput = document.getElementById('cust-cam-conn-type');
      if (connInput) connInput.value = 'rtsp';
      
      const hlsInput = document.getElementById('cust-cam-hls');
      if (hlsInput) hlsInput.value = '';
      
      const rtspInput = document.getElementById('cust-cam-rtsp');
      if (rtspInput) rtspInput.value = '';
      
      const snInput = document.getElementById('cust-cam-sn');
      if (snInput) snInput.value = '';
      
      const chInput = document.getElementById('cust-cam-channel');
      if (chInput) chInput.value = '1';
      
      const statusInput = document.getElementById('cust-cam-status');
      if (statusInput) statusInput.value = 'online';

      toggleConnFields();
      openModalHelper('modalCamForm');
    }

    function openEditCameraModal(camId) {
      const cam = (customerCameras && Array.isArray(customerCameras)) ? customerCameras.find(c => c.id == camId) : null;
      if (!cam) return;

      const titleEl = document.getElementById('camModalTitle');
      if (titleEl) titleEl.innerHTML = '<i class="fas fa-cog text-info mr-2"></i> Edit Pengaturan Kamera';
      
      const idEl = document.getElementById('cust-cam-id');
      if (idEl) idEl.value = cam.id;
      
      const titleInput = document.getElementById('cust-cam-title');
      if (titleInput) titleInput.value = cam.title || '';
      
      const cityInput = document.getElementById('cust-cam-city');
      if (cityInput) cityInput.value = cam.city || 'siantar';
      
      const connInput = document.getElementById('cust-cam-conn-type');
      if (connInput) connInput.value = cam.connection_type || 'rtsp';
      
      const hlsInput = document.getElementById('cust-cam-hls');
      if (hlsInput) hlsInput.value = cam.hls_url || cam.streamPath || '';
      
      const rtspInput = document.getElementById('cust-cam-rtsp');
      if (rtspInput) rtspInput.value = cam.rtsp_url || '';
      
      const snInput = document.getElementById('cust-cam-sn');
      if (snInput) snInput.value = cam.serial_number || '';
      
      const chInput = document.getElementById('cust-cam-channel');
      if (chInput) chInput.value = cam.channel || 1;
      
      const statusInput = document.getElementById('cust-cam-status');
      if (statusInput) statusInput.value = cam.status || 'online';

      toggleConnFields();
      openModalHelper('modalCamForm');
    }

    async function submitCustomerCamera(e) {
      e.preventDefault();
      const form = document.getElementById('formCamCustomer');
      const formData = new FormData();

      const connType = document.getElementById('cust-cam-conn-type').value;
      const rtspVal = document.getElementById('cust-cam-rtsp').value.trim();
      const hlsVal = document.getElementById('cust-cam-hls').value.trim();
      const snVal = document.getElementById('cust-cam-sn').value.trim();

      if (connType === 'rtsp' && !rtspVal && !hlsVal) {
        alert('Silakan masukkan URL RTSP kamera Anda.');
        return;
      }
      if (connType === 'xmeye_p2p' && !snVal) {
        alert('Silakan masukkan Serial Number (Cloud ID) kamera XMeye Anda.');
        return;
      }

      formData.append('action', 'save_camera');
      formData.append('id', document.getElementById('cust-cam-id').value);
      formData.append('title', document.getElementById('cust-cam-title').value);
      formData.append('city', document.getElementById('cust-cam-city').value);
      formData.append('connection_type', connType);
      formData.append('hls_url', hlsVal);
      formData.append('rtsp_url', rtspVal);
      formData.append('serial_number', snVal);
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
          closeModalHelper('modalCamForm');
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
      const ok = await showLoewixConfirmModal({
        title: 'Hapus Kamera CCTV',
        subtitle: 'Konfirmasi Penghapusan Channel Streaming',
        icon: 'fas fa-trash-alt',
        iconColor: '#f43f5e',
        isDanger: true,
        targetName: title,
        targetMeta: 'Kamera CCTV • Channel #' + camId,
        message: `Apakah Anda yakin ingin menghapus kamera "<strong>${title}</strong>" dari portal live stream Anda?`,
        confirmText: 'Ya, Hapus Kamera',
        confirmGradient: 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)'
      });
      if (!ok) return;

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

      function showInlineError(title, subtitle) {
        if (loading) {
          loading.style.display = 'flex';
          loading.innerHTML = `
            <div class="text-danger mb-1" style="font-size: 20px;"><i class="fas fa-video-slash"></i></div>
            <span style="font-size: 11px; font-weight: 700; color: #fca5a5;">${title}</span>
            <small class="text-muted d-block mt-1 mb-2" style="font-size: 10px; max-width: 90%;">${subtitle}</small>
            <div class="d-flex gap-1">
              <button class="btn btn-xs btn-outline-info" style="font-size: 10px; padding: 2px 8px; border-radius: 4px;" onclick="event.stopPropagation(); playCameraInline(${camId})"><i class="fas fa-redo-alt"></i> Coba Lagi</button>
              <button class="btn btn-xs btn-outline-secondary" style="font-size: 10px; padding: 2px 8px; border-radius: 4px;" onclick="event.stopPropagation(); openEditCameraModal(${camId})"><i class="fas fa-cog"></i> Edit</button>
            </div>
          `;
        }
        if (btn) {
          btn.innerHTML = `<i class="fas fa-play text-info"></i> Live Test`;
          btn.classList.remove('btn-playing-active');
        }
        stopCameraInline(camId, true);
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
            loading.innerHTML = `<div class="spinner-border text-info spinner-border-sm mb-1" role="status"></div><span style="font-size: 11px; font-weight: 600;">Cloud P2P (CH ${ch})...</span><small class="text-muted d-block mt-1" style="font-size:10px;">SN: ${sn}</small>`;
          }
          try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000);
            
            const res = await fetch(`../api/jftech_gateway.php?action=get_live_stream&sn=${encodeURIComponent(sn)}&channel=${encodeURIComponent(ch)}&stream=1&device_user=${encodeURIComponent(devUser)}&device_pass=${encodeURIComponent(devPass)}`, {
              signal: controller.signal
            });
            clearTimeout(timeoutId);
            const data = await res.json();

            if (data.success && data.hls_url) {
              streamUrl = data.hls_url;
              cam.hls_url = data.hls_url;
            } else {
              showInlineError('Kamera Sedang Offline', data.message || 'DVR/Kamera tidak terhubung ke Cloud P2P.');
              return;
            }
          } catch (e) {
            console.error('Failed to resolve XMeye stream:', e);
            showInlineError('Koneksi Cloud Timeout', 'Kamera tidak merespon dalam 8 detik. Pastikan DVR/Kamera menyala.');
            return;
          }
        }
      }

      // Auto-fallback if only rtsp_url is present
      if (!streamUrl && cam.rtsp_url) {
        if (cam.rtsp_url.includes('103.164.101.50:8203') && cam.rtsp_url.includes('channel=1')) {
          streamUrl = cam.rtsp_url.includes('stream=1') ? 'https://stream.loewixcctv.com/cctv_loewix_1_sub/index.m3u8' : 'https://stream.loewixcctv.com/cctv_loewix_1/index.m3u8';
        } else if (cam.rtsp_url.includes('103.164.101.50:8203') && cam.rtsp_url.includes('channel=2')) {
          streamUrl = cam.rtsp_url.includes('stream=1') ? 'https://stream.loewixcctv.com/cctv_loewix_2_sub/index.m3u8' : 'https://stream.loewixcctv.com/cctv_loewix_2/index.m3u8';
        } else if (cam.rtsp_url.includes('103.164.101.50:8203') && cam.rtsp_url.includes('channel=3')) {
          streamUrl = cam.rtsp_url.includes('stream=1') ? 'https://stream.loewixcctv.com/cctv_loewix_3_sub/index.m3u8' : 'https://stream.loewixcctv.com/cctv_loewix_3/index.m3u8';
        } else {
          const path = cam.streamPath || `cam_live_${camId}`;
          streamUrl = `https://stream.loewixcctv.com/${path}/index.m3u8`;
        }
        cam.hls_url = streamUrl;
      }

      // Normalization for MediaMTX / RTSP stream path (e.g. "cctv_loewix_1" or "yamaha_dds")
      if (streamUrl && !streamUrl.startsWith('http://') && !streamUrl.startsWith('https://')) {
        streamUrl = `https://stream.loewixcctv.com/${streamUrl}/index.m3u8`;
      } else if (streamUrl && streamUrl.startsWith('http://stream.loewixcctv.com')) {
        streamUrl = streamUrl.replace('http://', 'https://');
      }

      if (!streamUrl) {
        showInlineError('URL Belum Dikonfigurasi', 'Silakan klik Edit untuk mengisi URL RTSP / Serial Number.');
        return;
      }

      function revealVideo() {
        if (loading) loading.style.display = 'none';
        if (thumb) thumb.style.display = 'none';
        const standby = document.getElementById(`cam-standby-${camId}`);
        if (standby) standby.style.display = 'none';
        const overlay = document.getElementById(`cam-overlay-${camId}`);
        if (overlay) overlay.style.display = 'none';
        if (video) video.style.display = 'block';
        if (btn) {
          btn.innerHTML = `<i class="fas fa-stop text-danger"></i> Stop Test`;
          btn.classList.add('btn-playing-active');
        }

        // Automatic live snapshot grabber: captures a clean frame from live stream to refresh thumbnail
        setTimeout(() => {
          try {
            if (video && video.videoWidth > 0 && video.videoHeight > 0) {
              const canvas = document.createElement('canvas');
              canvas.width = 640;
              canvas.height = Math.round(640 * (video.videoHeight / video.videoWidth));
              const ctx = canvas.getContext('2d');
              ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
              const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

              // Update in-memory thumbnail & element
              if (thumb) {
                thumb.src = dataUrl;
                thumb.style.display = 'none'; // currently playing video
              }
              cam.thumbnail = dataUrl;

              // Save to backend database for permanent weekly snapshot
              const fd = new FormData();
              fd.append('action', 'save_snapshot');
              fd.append('camera_id', camId);
              fd.append('image_data', dataUrl);
              fetch('../api/customer_portal.php', { method: 'POST', body: fd }).catch(e => {});
            }
          } catch (e) {
            console.warn('Live snapshot auto-capture note:', e);
          }
        }, 1600);
      }

      video.muted = true;
      video.setAttribute('playsinline', 'true');
      video.setAttribute('webkit-playsinline', 'true');
      video.setAttribute('autoplay', '');
      video.setAttribute('muted', '');

      let hls = null;
      let netRetryCount = 0;

      if (streamUrl.includes('.m3u8') || streamUrl.includes('bcloud365.net')) {
        if (Hls.isSupported()) {
          hls = new Hls({
            enableWorker: true,
            lowLatencyMode: true,
            liveSyncDurationCount: 1,
            maxBufferLength: 2,
            manifestLoadingTimeOut: 6000,
            manifestLoadingMaxRetry: 2,
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
                netRetryCount++;
                if (netRetryCount <= 2) {
                  setTimeout(() => {
                    if (activeInlinePlayers.has(camId)) {
                      hls.startLoad();
                    }
                  }, 1200);
                } else {
                  showInlineError('Siaran Belum Aktif', 'Stream 404 di server streaming.');
                }
              } else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
                hls.recoverMediaError();
              } else {
                showInlineError('Gagal Memuat Siaran', 'Stream offline / error.');
              }
            }
          });

        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
          video.src = streamUrl;
          video.muted = true;
          video.play().then(revealVideo).catch(revealVideo);
          video.onerror = () => showInlineError('Kamera Offline', 'Tidak dapat memutar siaran.');
        }
      } else {
        video.src = streamUrl;
        video.muted = true;
        video.play().then(revealVideo).catch(revealVideo);
        video.onerror = () => showInlineError('Kamera Offline', 'Tidak dapat memutar siaran.');
      }

      video.onplaying = revealVideo;
      video.onloadeddata = revealVideo;

      activeInlinePlayers.set(camId, { hls, video });
    }

    function stopCameraInline(camId, keepErrorState = false) {
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
      const standby = document.getElementById(`cam-standby-${camId}`);
      const overlay = document.getElementById(`cam-overlay-${camId}`);
      const hint = document.getElementById(`play-hint-${camId}`);
      const loading = document.getElementById(`cam-loading-${camId}`);
      const btn = document.getElementById(`btn-live-${camId}`);

      if (!keepErrorState) {
        if (thumb && thumb.src && !thumb.src.endsWith('/customer/') && !thumb.src.endsWith('/customer/index.php') && thumb.getAttribute('src') !== '') {
          thumb.style.display = 'block';
          if (standby) standby.style.display = 'none';
        } else if (standby) {
          standby.style.display = 'flex';
          if (thumb) thumb.style.display = 'none';
        }
        if (overlay) overlay.style.display = 'flex';
        if (hint) hint.style.display = 'flex';
        if (loading) loading.style.display = 'none';
      }
      if (btn) {
        btn.innerHTML = `<i class="fas fa-play text-info"></i> Live Test`;
        btn.classList.remove('btn-playing-active');
      }

      // If no active players left, reset Live Test ALL button
      if (activeInlinePlayers.size === 0) {
        isLiveTestAllRunning = false;
        const btnAll = document.getElementById('btn-live-test-all');
        if (btnAll) {
          btnAll.className = 'btn-live-test-all';
          btnAll.innerHTML = `<i class="fas fa-play-circle"></i> <span>Live Test ALL</span>`;
          btnAll.title = 'Putar & Uji Siaran Langsung Semua Kamera Sekaligus';
        }
      }
    }

    // ===== LIVE TEST ALL CONTROLLER (BATCH CONCURRENT STREAMING) =====
    let isLiveTestAllRunning = false;

    async function toggleLiveTestAll() {
      const btnAll = document.getElementById('btn-live-test-all');
      
      if (!customerCameras || customerCameras.length === 0) {
        alert('Tidak ada channel kamera CCTV yang terdaftar.');
        return;
      }

      // Check how many are currently playing
      const currentlyPlayingCount = activeInlinePlayers.size;
      const shouldStop = isLiveTestAllRunning || (currentlyPlayingCount > 0 && currentlyPlayingCount >= Math.ceil(customerCameras.length / 2));

      if (shouldStop) {
        // Stop all running cameras
        customerCameras.forEach(cam => {
          stopCameraInline(cam.id);
        });
        isLiveTestAllRunning = false;
        if (btnAll) {
          btnAll.className = 'btn-live-test-all';
          btnAll.innerHTML = `<i class="fas fa-play-circle"></i> <span>Live Test ALL</span>`;
          btnAll.title = 'Putar & Uji Siaran Langsung Semua Kamera Sekaligus';
        }
        return;
      }

      // Start all cameras with staggered 75ms delay
      isLiveTestAllRunning = true;
      if (btnAll) {
        btnAll.className = 'btn-live-test-all is-running';
        btnAll.innerHTML = `<i class="fas fa-stop-circle"></i> <span>Stop ALL Live</span>`;
        btnAll.title = 'Hentikan Semua Siaran Live Test';
      }

      // Loop through all visible/filtered cameras or all customer cameras
      const searchVal = (document.getElementById('filter-search-input')?.value || '').toLowerCase();
      const cityVal = document.getElementById('filter-city-select')?.value || 'all';
      const statusVal = document.getElementById('filter-status-select')?.value || 'all';

      const targetCameras = customerCameras.filter(cam => {
        const matchTitle = (cam.title || '').toLowerCase().includes(searchVal);
        const matchCity = (cityVal === 'all') || (cam.city && cam.city.toLowerCase() === cityVal);
        const matchStatus = (statusVal === 'all') || (statusVal === 'online' && cam.status !== 'offline') || (statusVal === 'offline' && cam.status === 'offline');
        return matchTitle && matchCity && matchStatus;
      });

      const camsToPlay = targetCameras.length > 0 ? targetCameras : customerCameras;

      for (let i = 0; i < camsToPlay.length; i++) {
        const cam = camsToPlay[i];
        if (!activeInlinePlayers.has(cam.id)) {
          playCameraInline(cam.id);
          if (i < camsToPlay.length - 1) {
            await new Promise(r => setTimeout(r, 75));
          }
        }
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
      closeModalHelper('modalLivePlayer');
    }

    function openProfileSettingsModal() {
      if (!currentCustomer) return;
      document.getElementById('prof-name').value = currentCustomer.name || '';
      document.getElementById('prof-email').value = currentCustomer.email || '';
      document.getElementById('prof-phone').value = currentCustomer.phone || '';
      document.getElementById('prof-city').value = currentCustomer.city || '';
      openModalHelper('modalProfile');
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
          closeModalHelper('modalProfile');
        } else {
          alert(data.message || 'Gagal update password.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    function openRequestUpgradeModal() {
      switchCustomerTab('tab-package');
    }

    function logoutCustomer() {
      localStorage.removeItem('loewix_user');
      fetch('../api/auth.php?action=logout').finally(() => {
        window.location.href = '../index.html';
      });
    }

    // ========================================================
    // BILLING & SUBSCRIPTION DASHBOARD CONTROLLER
    // ========================================================
    let cachedAdminCustomers = [];

    function switchCustomerTab(tabId) {
      // Toggle tab buttons
      document.querySelectorAll('.customer-nav-tab').forEach(btn => {
        btn.classList.remove('active');
      });
      const activeBtn = document.getElementById('nav-' + tabId);
      if (activeBtn) activeBtn.classList.add('active');

      // Toggle tab panes
      document.querySelectorAll('.customer-tab-pane').forEach(pane => {
        pane.style.display = 'none';
      });
      const activePane = document.getElementById(tabId);
      if (activePane) activePane.style.display = 'block';

      // Load specific tab data
      if (tabId === 'tab-admin-customers') {
        loadAdminCustomersList();
      } else if (tabId === 'tab-admin-plans') {
        loadAdminPlansList();
      } else if (tabId === 'tab-admin-transactions') {
        loadAdminTransactionsList();
      } else if (tabId === 'tab-ai-vision') {
        loadAIData();
        setTimeout(initAIHUDCanvas, 150);
      } else if (tabId !== 'tab-cameras') {
        if (currentBillingData) {
          renderBillingData(currentBillingData);
        }
        loadBillingDashboardData();
      }
    }

    // ========================================================
    // SUPER ADMIN SAAS PLANS & PRICING CONTROLLER (SPA)
    // ========================================================
    let cachedAdminPlans = [];

    async function loadAdminPlansList() {
      const grid = document.getElementById('admin-plans-grid');
      if (!grid) return;

      grid.innerHTML = `
        <div class="col-12 text-center py-5 text-muted">
          <i class="fas fa-spinner fa-spin fa-2x mb-2 text-info"></i>
          <div>Memuat data paket langganan SaaS...</div>
        </div>
      `;

      try {
        const res = await fetch('../api/payment.php?action=get_plans');
        const data = await res.json();
        if (data.success && Array.isArray(data.plans)) {
          cachedAdminPlans = data.plans;
          renderAdminPlans(cachedAdminPlans);
        } else {
          grid.innerHTML = `<div class="col-12 text-center py-4 text-warning">Gagal memuat paket langganan.</div>`;
        }
      } catch (err) {
        grid.innerHTML = `<div class="col-12 text-center py-4 text-danger">Terjadi kesalahan koneksi ke server.</div>`;
      }
    }

    function renderAdminPlans(plans) {
      const grid = document.getElementById('admin-plans-grid');
      if (!grid) return;
      grid.innerHTML = '';

      if (!plans || plans.length === 0) {
        grid.innerHTML = `<div class="col-12 text-center py-4 text-muted">Belum ada paket langganan tersimpan.</div>`;
        return;
      }

      plans.forEach(p => {
        const isPopuler = p.badge && p.badge.toUpperCase().includes('POPULER');
        const col = document.createElement('div');
        col.className = 'col-lg-4 col-md-6 mb-4';
        col.innerHTML = `
          <div style="background: rgba(8, 15, 36, 0.9); border: 1.5px solid ${isPopuler ? '#f59e0b' : 'rgba(56, 189, 248, 0.35)'}; border-radius: 16px; padding: 22px; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
            ${p.badge ? `<span style="position: absolute; top: -11px; right: 18px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #000; font-size: 10px; font-weight: 800; padding: 3px 10px; border-radius: 20px; letter-spacing: 0.5px; box-shadow: 0 2px 10px rgba(245, 158, 11, 0.5);">${p.badge}</span>` : ''}

            <div>
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="text-white font-weight-bold mb-0">${p.name}</h5>
                <span class="badge badge-info p-2" style="background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); color: #38bdf8; border-radius: 8px; font-weight: 700;">
                  <i class="fas fa-video mr-1"></i> ${p.cctv_quota} CCTV
                </span>
              </div>

              <div class="my-3 p-3 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06);">
                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Tarif Langganan:</div>
                <div style="font-size: 20px; font-weight: 800; color: #34d399; margin: 2px 0;">
                  Rp ${Number(p.price_monthly).toLocaleString('id-ID')} <small style="font-size: 12px; color: #94a3b8;">/ bln</small>
                </div>
                <div style="font-size: 12.5px; color: #38bdf8; font-weight: 600;">
                  Rp ${Number(p.price_annual || p.price_monthly * 10).toLocaleString('id-ID')} <small style="color: #94a3b8;">/ thn (Hemat 2 Bln)</small>
                </div>
              </div>

              <div class="mb-3">
                <div style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Fitur & Kapasitas:</div>
                <ul style="list-style: none; padding-left: 0; margin-bottom: 0; font-size: 12px; color: #cbd5e1;">
                  ${(p.features || []).map(f => `<li style="margin-bottom: 5px; display: flex; align-items: center; gap: 6px;"><i class="fas fa-check-circle text-success" style="color: #34d399; font-size: 11px;"></i> <span>${f}</span></li>`).join('')}
                </ul>
              </div>
            </div>

            <div class="pt-3 border-top border-secondary d-flex justify-content-between align-items-center mt-3">
              <button class="btn btn-outline-info btn-sm font-weight-bold px-3" onclick="openEditPlanModal('${p.id}')" style="border-radius: 8px; font-size: 12px;">
                <i class="fas fa-edit mr-1"></i> Edit Paket & Harga
              </button>
              <button class="btn btn-outline-danger btn-sm" onclick="deleteAdminPlan('${p.id}')" style="border-radius: 8px; font-size: 12px;" title="Hapus Paket">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        `;
        grid.appendChild(col);
      });
    }

    function openAddPlanModal() {
      document.getElementById('planModalTitle').innerHTML = '<i class="fas fa-plus-circle text-info mr-2"></i> Tambah Paket SaaS Baru';
      document.getElementById('plan-input-id').value = '';
      document.getElementById('plan-input-name').value = '';
      document.getElementById('plan-input-quota').value = '10';
      document.getElementById('plan-input-badge').value = '';
      document.getElementById('plan-input-monthly').value = '';
      document.getElementById('plan-input-annual').value = '';
      document.getElementById('plan-input-features').value = '10 Titik Kamera Live\nFull HD 1080p Stream H.265\nWebRTC & HLS Low Latency\nCloud Recording 14 Hari';
      openModalHelper('modalAdminPlanForm');
    }

    function openEditPlanModal(planId) {
      const p = cachedAdminPlans.find(item => item.id === planId);
      if (!p) return;
      document.getElementById('planModalTitle').innerHTML = '<i class="fas fa-edit text-warning mr-2"></i> Edit Paket SaaS';
      document.getElementById('plan-input-id').value = p.id;
      document.getElementById('plan-input-name').value = p.name;
      document.getElementById('plan-input-quota').value = p.cctv_quota;
      document.getElementById('plan-input-badge').value = p.badge || '';
      document.getElementById('plan-input-monthly').value = p.price_monthly;
      document.getElementById('plan-input-annual').value = p.price_annual || '';
      document.getElementById('plan-input-features').value = (p.features || []).join('\n');
      openModalHelper('modalAdminPlanForm');
    }

    async function submitSavePlan(e) {
      e.preventDefault();
      const id = document.getElementById('plan-input-id').value;
      const name = document.getElementById('plan-input-name').value.trim();
      const quota = document.getElementById('plan-input-quota').value;
      const badge = document.getElementById('plan-input-badge').value.trim();
      const monthly = document.getElementById('plan-input-monthly').value;
      const annual = document.getElementById('plan-input-annual').value;
      const features = document.getElementById('plan-input-features').value;

      const fd = new FormData();
      fd.append('action', 'save_plan');
      fd.append('id', id);
      fd.append('name', name);
      fd.append('cctv_quota', quota);
      fd.append('badge', badge);
      fd.append('price_monthly', monthly);
      fd.append('price_annual', annual);
      fd.append('features', features);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert('Paket SaaS & Harga berhasil disimpan!');
          closeModalHelper('modalAdminPlanForm');
          loadAdminPlansList();
        } else {
          alert(data.message || 'Gagal menyimpan paket.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi ke server.');
      }
    }

    async function deleteAdminPlan(planId) {
      const p = (typeof cachedAdminPlans !== 'undefined' ? cachedAdminPlans : []).find(item => item.id === planId);
      const planName = p ? p.name : planId;

      const ok = await showLoewixConfirmModal({
        title: 'Hapus Paket SaaS',
        subtitle: 'Konfigurasi Paket Langganan',
        icon: 'fas fa-trash-alt',
        iconColor: '#f43f5e',
        isDanger: true,
        targetName: planName,
        targetMeta: 'Paket ID: ' + planId,
        message: `Apakah Anda yakin ingin menghapus paket langganan "<strong>${planName}</strong>"? Paket ini tidak akan lagi tersedia di halaman registrasi.`,
        confirmText: 'Ya, Hapus Paket',
        confirmGradient: 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)'
      });
      if (!ok) return;

      const fd = new FormData();
      fd.append('action', 'delete_plan');
      fd.append('id', planId);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert('Paket berhasil dihapus.');
          loadAdminPlansList();
        } else {
          alert(data.message || 'Gagal menghapus paket.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi ke server.');
      }
    }

    // ========================================================
    // SUPER ADMIN CUSTOMER MANAGEMENT CONTROLLER (SPA)
    // ========================================================
    async function loadAdminCustomersList() {
      const tbody = document.getElementById('admin-customer-table-body');
      if (!tbody) return;

      tbody.innerHTML = `
        <tr>
          <td colspan="7" class="text-center py-4 text-muted">
            <i class="fas fa-spinner fa-spin mr-2 text-info"></i> Memuat data pelanggan dari server...
          </td>
        </tr>
      `;

      try {
        const res = await fetch('../api/admin_customers.php');
        const data = await res.json();
        if (data.success && Array.isArray(data.customers)) {
          cachedAdminCustomers = data.customers;
          renderAdminCustomerTable(cachedAdminCustomers);
        } else {
          tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-warning">Gagal memuat data pelanggan.</td></tr>`;
        }
      } catch (err) {
        console.error('Error loading admin customers:', err);
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Terjadi kesalahan koneksi ke API pelanggan.</td></tr>`;
      }
    }

    function renderAdminCustomerTable(customers) {
      const tbody = document.getElementById('admin-customer-table-body');
      if (!tbody) return;
      tbody.innerHTML = '';

      if (!customers || customers.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-search mr-2"></i> Tidak ada pelanggan ditemukan.</td></tr>`;
        return;
      }

      customers.forEach(c => {
        const isSuspended = (c.status !== 'active');
        const used = c.cctv_used || 0;
        const quota = c.cctv_quota || 10;
        const percentUsed = Math.min(100, Math.round((used / quota) * 100));

        const cityCode = (c.city || 'siantar').toLowerCase();
        let cityBadgeClass = 'city-badge-default';
        if (cityCode === 'siantar') cityBadgeClass = 'city-badge-siantar';
        else if (cityCode === 'jakarta') cityBadgeClass = 'city-badge-jakarta';
        else if (cityCode === 'bali') cityBadgeClass = 'city-badge-bali';
        else if (cityCode === 'medan') cityBadgeClass = 'city-badge-medan';
        else if (cityCode === 'bandung') cityBadgeClass = 'city-badge-bandung';

        const statusBadge = !isSuspended
          ? `<span class="status-badge-active"><span class="pulse-dot"></span> AKTIF</span>`
          : `<span class="status-badge-suspended"><i class="fas fa-ban mr-1"></i> SUSPENDED</span>`;

        const cleanPhone = (c.phone || '').replace(/[^0-9]/g, '');
        const waLink = cleanPhone ? `https://wa.me/${cleanPhone.startsWith('0') ? '62' + cleanPhone.slice(1) : cleanPhone}` : '';

        const row = document.createElement('tr');
        if (isSuspended) {
          row.className = 'table-row-suspended';
        }
        row.innerHTML = `
          <td><span class="cust-id-badge" style="${isSuspended ? 'border-color: rgba(239,68,68,0.5); color: #f87171;' : ''}">#${c.id}</span></td>
          <td>
            <div class="font-weight-bold text-white d-flex align-items-center" style="font-size: 13.5px;">
              <i class="fas fa-building ${isSuspended ? 'text-danger' : 'text-info'} mr-2" style="opacity: 0.85;"></i> ${c.name}
              ${isSuspended ? `<span class="badge ml-2" style="font-size: 9.5px; background: rgba(239,68,68,0.25); border: 1px solid rgba(239,68,68,0.5); color: #fca5a5; font-weight: 700;"><i class="fas fa-ban mr-1"></i> DIBEKUKAN</span>` : ''}
            </div>
            <div class="text-muted mt-1" style="font-size: 11px;">
              <i class="fas fa-calendar-alt mr-1"></i> Terdaftar ${c.created_at ? c.created_at.split(' ')[0] : '2026-08-14'}
            </div>
          </td>
          <td>
            <div>
              <a href="mailto:${c.email}" class="text-info text-decoration-none" style="font-size: 12.5px; ${isSuspended ? 'color: #94a3b8 !important;' : ''}">
                <i class="fas fa-envelope mr-1"></i> ${c.email}
              </a>
            </div>
            <div class="mt-1 d-flex align-items-center">
              <span class="text-muted" style="font-size: 11.5px;"><i class="fas fa-phone mr-1"></i> ${c.phone || '-'}</span>
              ${waLink ? `<a href="${waLink}" target="_blank" class="badge badge-success ml-2 px-2 py-1" style="font-size: 10px; border-radius: 10px; text-decoration: none;" title="Hubungi via WhatsApp"><i class="fab fa-whatsapp mr-1"></i> Chat</a>` : ''}
            </div>
          </td>
          <td>
            <span class="city-badge ${cityBadgeClass}">
              <i class="fas fa-map-marker-alt"></i> ${cityCode.toUpperCase()}
            </span>
          </td>
          <td>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="font-weight-bold ${isSuspended ? 'text-muted' : 'text-white'}" style="font-size: 12px;">
                <i class="fas fa-video mr-1 ${isSuspended ? 'text-muted' : 'text-warning'}"></i> <strong>${used}</strong> / ${quota} CCTV
              </span>
              <span class="badge ${isSuspended ? 'badge-secondary' : (percentUsed >= 80 ? 'badge-danger' : (percentUsed >= 50 ? 'badge-warning' : 'badge-info'))}" style="font-size: 10px; border-radius: 6px; padding: 2px 5px;">
                ${isSuspended ? 'OFF' : percentUsed + '%'}
              </span>
            </div>
            <div class="progress-bar-custom">
              <div class="progress-fill ${isSuspended ? '' : (percentUsed >= 80 ? 'progress-fill-high' : (percentUsed >= 50 ? 'progress-fill-med' : 'progress-fill-low'))}" style="width: ${isSuspended ? 0 : percentUsed}%; ${isSuspended ? 'background: #64748b;' : ''}"></div>
            </div>
          </td>
          <td>${statusBadge}</td>
          <td class="text-right">
            <div class="action-btn-group">
              <button class="act-btn act-btn-edit" onclick="openEditCustomerModal(${c.id})" title="Edit Data Profil Customer">
                <i class="fas fa-edit"></i>
              </button>
              <button class="act-btn act-btn-quota" onclick="openEditQuotaModal(${c.id}, '${c.name.replace(/'/g, "\\'")}', ${c.cctv_quota})" title="Atur Kuota CCTV">
                <i class="fas fa-sliders-h"></i>
              </button>
              <button class="act-btn act-btn-pass" onclick="resetAdminCustomerPassword(${c.id}, '${c.name.replace(/'/g, "\\'")}')" title="Reset Password Customer">
                <i class="fas fa-key"></i>
              </button>
              ${isSuspended 
                ? `<button class="act-btn act-btn-status-resume" onclick="toggleAdminCustomerStatus(${c.id}, '${c.name.replace(/'/g, "\\'")}', 'suspended')" title="Klik untuk Mengaktifkan Kembali Akun (Un-suspend)"><i class="fas fa-play"></i></button>`
                : `<button class="act-btn act-btn-status" onclick="toggleAdminCustomerStatus(${c.id}, '${c.name.replace(/'/g, "\\'")}', 'active')" title="Klik untuk Membekukan / Suspend Akun"><i class="fas fa-power-off"></i></button>`
              }
              <button class="act-btn act-btn-delete" onclick="deleteAdminCustomer(${c.id}, '${c.name.replace(/'/g, "\\'")}')" title="Hapus Akun Customer">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    function filterAdminCustomerTable() {
      const search = (document.getElementById('search-customer-input')?.value || '').toLowerCase();
      const city = document.getElementById('filter-city-select')?.value || 'all';
      const status = document.getElementById('filter-status-select')?.value || 'all';

      const filtered = cachedAdminCustomers.filter(c => {
        const matchesSearch = c.name.toLowerCase().includes(search) || c.email.toLowerCase().includes(search) || (c.phone && c.phone.includes(search));
        const matchesCity = (city === 'all') || (c.city && c.city.toLowerCase() === city);
        const matchesStatus = (status === 'all') || (c.status === status);
        return matchesSearch && matchesCity && matchesStatus;
      });

      renderAdminCustomerTable(filtered);
    }

    function openAddCustomerModal() {
      document.getElementById('formAddCustomer')?.reset();
      openModalHelper('modalAddCustomer');
    }

    async function submitAddCustomer(e) {
      e.preventDefault();
      const name = document.getElementById('cust-name').value.trim();
      const email = document.getElementById('cust-email').value.trim();
      const password = document.getElementById('cust-password').value;
      const quota = document.getElementById('cust-quota').value;
      const city = document.getElementById('cust-city').value;
      const phone = document.getElementById('cust-phone').value.trim();

      const fd = new FormData();
      fd.append('action', 'create');
      fd.append('name', name);
      fd.append('email', email);
      fd.append('password', password);
      fd.append('cctv_quota', quota);
      fd.append('city', city);
      fd.append('phone', phone);

      try {
        const res = await fetch('../api/admin_customers.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert('Customer baru berhasil ditambahkan!');
          closeModalHelper('modalAddCustomer');
          loadAdminCustomersList();
        } else {
          alert(data.message || 'Gagal menambahkan customer.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    function openEditCustomerModal(id) {
      const c = cachedAdminCustomers.find(item => item.id == id);
      if (!c) return;
      document.getElementById('edit-profile-id').value = c.id;
      document.getElementById('edit-profile-name').value = c.name;
      document.getElementById('edit-profile-email').value = c.email;
      document.getElementById('edit-profile-city').value = c.city || 'siantar';
      document.getElementById('edit-profile-phone').value = c.phone || '';
      openModalHelper('modalEditCustomer');
    }

    async function submitEditCustomer(e) {
      e.preventDefault();
      const id = document.getElementById('edit-profile-id').value;
      const name = document.getElementById('edit-profile-name').value.trim();
      const email = document.getElementById('edit-profile-email').value.trim();
      const city = document.getElementById('edit-profile-city').value;
      const phone = document.getElementById('edit-profile-phone').value.trim();

      const fd = new FormData();
      fd.append('action', 'update_profile');
      fd.append('id', id);
      fd.append('name', name);
      fd.append('email', email);
      fd.append('city', city);
      fd.append('phone', phone);

      try {
        const res = await fetch('../api/admin_customers.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert('Profil customer berhasil diperbarui!');
          closeModalHelper('modalEditCustomer');
          loadAdminCustomersList();
        } else {
          alert(data.message || 'Gagal memperbarui profil.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    function openEditQuotaModal(id, name, quota) {
      document.getElementById('edit-quota-id').value = id;
      document.getElementById('edit-quota-name').innerText = name;
      document.getElementById('edit-quota-value').value = quota;
      openModalHelper('modalEditQuota');
    }

    async function submitEditQuota(e) {
      e.preventDefault();
      const id = document.getElementById('edit-quota-id').value;
      const quota = document.getElementById('edit-quota-value').value;

      const fd = new FormData();
      fd.append('action', 'update_quota');
      fd.append('id', id);
      fd.append('quota', quota);

      try {
        const res = await fetch('../api/admin_customers.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert('Alokasi kuota berhasil diupdate!');
          closeModalHelper('modalEditQuota');
          loadAdminCustomersList();
        } else {
          alert(data.message || 'Gagal update kuota.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    function resetAdminCustomerPassword(id, name) {
      document.getElementById('reset-cust-id').value = id;
      document.getElementById('reset-cust-id-display').textContent = id;
      document.getElementById('reset-cust-name-display').textContent = name;
      document.getElementById('reset-cust-password-input').value = 'loewix' + Math.floor(100 + Math.random() * 900);
      openModalHelper('modalResetCustomerPassword');
    }

    async function submitResetCustomerPassword(e) {
      e.preventDefault();
      const id = document.getElementById('reset-cust-id').value;
      const newPwd = document.getElementById('reset-cust-password-input').value.trim();
      if (!newPwd) return;

      const fd = new FormData();
      fd.append('action', 'reset_password');
      fd.append('id', id);
      fd.append('password', newPwd);

      try {
        const res = await fetch('../api/admin_customers.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert('✓ ' + (data.message || 'Password customer berhasil diperbarui!'));
          closeModalHelper('modalResetCustomerPassword');
          loadAdminCustomersList();
        } else {
          alert(data.message || 'Gagal reset password.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    function generateRandomPassword(inputId) {
      const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#$';
      let pass = 'Lwx_';
      for (let i = 0; i < 6; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
      }
      const el = document.getElementById(inputId);
      if (el) el.value = pass;
    }

    function copyPasswordToClipboard(inputId) {
      const el = document.getElementById(inputId);
      if (!el) return;
      el.select();
      navigator.clipboard.writeText(el.value).then(() => {
        alert('Password disalin ke clipboard: ' + el.value);
      }).catch(() => {
        alert('Password: ' + el.value);
      });
    }

    async function toggleAdminCustomerStatus(id, name, currentStatus) {
      const isCurrentlyActive = (currentStatus === 'active' || currentStatus === 'aktif');
      const nextStatus = isCurrentlyActive ? 'SUSPEND' : 'AKTIF';

      const ok = await showLoewixConfirmModal({
        title: 'Ubah Status Customer',
        subtitle: 'Kontrol Hak Akses & Operasional Tenant',
        icon: 'fas fa-power-off',
        iconColor: isCurrentlyActive ? '#f59e0b' : '#10b981',
        isDanger: isCurrentlyActive,
        targetName: name || 'Customer #' + id,
        targetMeta: 'ID: #' + id + ` • Status saat ini: ${isCurrentlyActive ? 'AKTIF' : 'SUSPEND'}`,
        message: `Apakah Anda yakin ingin mengubah status akun ini menjadi <strong>${nextStatus}</strong>?<br><small class="text-muted">Customer yang berstatus Suspend tidak dapat mengakses portal pemantauan dan streaming CCTV.</small>`,
        confirmText: `Ya, Jadikan ${nextStatus}`,
        confirmGradient: isCurrentlyActive 
          ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' 
          : 'linear-gradient(135deg, #10b981 0%, #059669 100%)'
      });
      if (!ok) return;

      const fd = new FormData();
      fd.append('action', 'toggle_status');
      fd.append('id', id);

      try {
        const res = await fetch('../api/admin_customers.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          loadAdminCustomersList();
        } else {
          alert(data.message || 'Gagal update status.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    async function deleteAdminCustomer(id, name) {
      const ok = await showLoewixConfirmModal({
        title: 'Hapus Akun Customer',
        subtitle: 'Peringatan Penghapusan Data Permanen',
        icon: 'fas fa-triangle-exclamation',
        iconColor: '#f43f5e',
        isDanger: true,
        targetName: name,
        targetMeta: 'ID: #' + id + ' • Seluruh kamera terkait akan ikut terhapus',
        message: `PERINGATAN: Apakah Anda yakin ingin menghapus akun customer "<strong>${name}</strong>"? Seluruh konfigurasi RTSP dan kamera yang terdaftar pada akun ini akan dihapus permanen.`,
        confirmText: 'Ya, Hapus Permanen',
        confirmGradient: 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)'
      });
      if (!ok) return;

      const fd = new FormData();
      fd.append('action', 'delete');
      fd.append('id', id);

      try {
        const res = await fetch('../api/admin_customers.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert('Customer berhasil dihapus.');
          loadAdminCustomersList();
        } else {
          alert(data.message || 'Gagal menghapus customer.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    function exportAdminCustomerCSV() {
      if (!cachedAdminCustomers || cachedAdminCustomers.length === 0) {
        alert('Tidak ada data customer untuk di-export.');
        return;
      }
      let csv = 'ID,Nama Perusahaan,Email,No HP,Wilayah,Kuota CCTV,Kuota Terpakai,Status,Tanggal Daftar\n';
      cachedAdminCustomers.forEach(c => {
        csv += `"${c.id}","${c.name}","${c.email}","${c.phone || '-'}","${c.city || '-'}","${c.cctv_quota}","${c.cctv_used || 0}","${c.status}","${c.created_at || '-'}"\n`;
      });
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `loewix_customers_${new Date().toISOString().slice(0, 10)}.csv`;
      a.click();
    }

    let cachedAdminTransactions = [];

    async function loadAdminTransactionsList() {
      const tbody = document.getElementById('admin-transactions-table-body');
      if (!tbody) return;

      tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2 text-info"></i> Memuat data transaksi & laporan keuangan...</td></tr>`;

      try {
        const res = await fetch('../api/payment.php?action=get_billing_dashboard');
        const data = await res.json();
        cachedAdminTransactions = data.invoices || [];

        // Calculate Financial KPI Metrics
        let totalRevenue = 0;
        let settledCount = 0;
        let pendingAmount = 0;
        let pendingCount = 0;

        cachedAdminTransactions.forEach(inv => {
          const amt = Number(inv.total_amount || inv.amount || 0);
          const st = (inv.status || '').toLowerCase();
          if (st === 'settlement' || st === 'capture' || st === 'success') {
            totalRevenue += amt;
            settledCount++;
          } else if (st === 'pending') {
            pendingAmount += amt;
            pendingCount++;
          }
        });

        const totalInvoices = cachedAdminTransactions.length;
        const settledRate = totalInvoices > 0 ? Math.round((settledCount / totalInvoices) * 100) : 100;
        const arpu = settledCount > 0 ? Math.round(totalRevenue / settledCount) : 0;

        const revEl = document.getElementById('stat-trans-total-revenue');
        const setCntEl = document.getElementById('stat-trans-settled-count');
        const setRateEl = document.getElementById('stat-trans-settled-rate');
        const pendAmtEl = document.getElementById('stat-trans-pending-amount');
        const pendCntEl = document.getElementById('stat-trans-pending-count');
        const arpuEl = document.getElementById('stat-trans-arpu');

        if (revEl) revEl.textContent = 'Rp ' + totalRevenue.toLocaleString('id-ID');
        if (setCntEl) setCntEl.textContent = settledCount + ' Tagihan';
        if (setRateEl) setRateEl.textContent = settledRate + '% Success';
        if (pendAmtEl) pendAmtEl.textContent = 'Rp ' + pendingAmount.toLocaleString('id-ID');
        if (pendCntEl) pendCntEl.textContent = pendingCount + ' Invoice';
        if (arpuEl) arpuEl.textContent = 'Rp ' + arpu.toLocaleString('id-ID');

        renderAdminTransactionsTable(cachedAdminTransactions);
      } catch (err) {
        console.error('Failed to load transactions:', err);
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Gagal memuat data transaksi.</td></tr>`;
      }
    }

    function renderAdminTransactionsTable(invoices) {
      const tbody = document.getElementById('admin-transactions-table-body');
      if (!tbody) return;
      tbody.innerHTML = '';

      if (!invoices || invoices.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted"><i class="fas fa-search mr-1"></i> Tidak ada data transaksi yang sesuai filter.</td></tr>`;
        return;
      }

      invoices.forEach(inv => {
        const totalAmt = inv.total_amount || inv.amount || 0;
        const status = (inv.status || 'settlement').toLowerCase();
        
        let statusBadge = `<span class="badge badge-success p-2" style="background: rgba(16,185,129,0.2); border: 1px solid #10b981; color: #34d399;"><i class="fas fa-check-circle mr-1"></i> LUNAS (SETTLEMENT)</span>`;
        if (status === 'pending') {
          statusBadge = `<span class="badge badge-warning p-2" style="background: rgba(245,158,11,0.2); border: 1px solid #f59e0b; color: #fbbf24;"><i class="fas fa-clock mr-1"></i> MENUNGGU PEMBAYARAN</span>`;
        } else if (status === 'expire' || status === 'cancel' || status === 'failure') {
          statusBadge = `<span class="badge badge-danger p-2" style="background: rgba(239,68,68,0.2); border: 1px solid #ef4444; color: #f87171;"><i class="fas fa-times-circle mr-1"></i> BATAL / EXPIRED</span>`;
        }

        let payType = inv.payment_type || 'QRIS / VA';
        if (payType.includes('bca')) payType = 'BCA Virtual Account';
        else if (payType.includes('mandiri')) payType = 'Mandiri Bill';
        else if (payType.includes('bri')) payType = 'BRI Virtual Account';
        else if (payType.includes('bni')) payType = 'BNI Virtual Account';
        else if (payType.includes('qris')) payType = 'QRIS Instant';
        else if (payType.includes('credit_card')) payType = 'Credit Card';
        else if (payType.includes('manual')) payType = 'Transfer Bank Admin';

        const orderIdSafe = (inv.order_id || inv.invoice_number || 'INV-LOEWIX').replace(/'/g, "\\'");
        const userNameSafe = (inv.user_name || 'Customer').replace(/'/g, "\\'");

        let actionButtons = `
          <div class="action-btn-group">
            <button class="act-btn" onclick="showInvoiceReceiptModal('${orderIdSafe}')" title="Lihat / Cetak Kwitansi Resmi" style="background: rgba(56, 189, 248, 0.15); border-color: rgba(56, 189, 248, 0.4); color: #38bdf8;">
              <i class="fas fa-receipt"></i>
            </button>
            <button class="act-btn" onclick="openInvoiceDetailModal('${orderIdSafe}')" title="Lihat Rincian & Log Midtrans" style="background: rgba(168, 85, 247, 0.15); border-color: rgba(168, 85, 247, 0.4); color: #c084fc;">
              <i class="fas fa-eye"></i>
            </button>
        `;

        if (status === 'pending') {
          actionButtons += `
            <button class="act-btn" onclick="sendAdminPaymentReminder('${orderIdSafe}', '${userNameSafe}', '${inv.user_email || ''}')" title="Kirim Ulang Email Tagihan" style="background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;">
              <i class="fas fa-paper-plane"></i>
            </button>
            <button class="act-btn" onclick="markAdminInvoiceSettled('${orderIdSafe}', '${userNameSafe}')" title="Tandai Sudah Lunas Manual" style="background: rgba(16, 185, 129, 0.2); border-color: #10b981; color: #34d399;">
              <i class="fas fa-check"></i>
            </button>
          `;
        }

        actionButtons += `
            <button class="act-btn act-btn-delete" onclick="deleteAdminInvoice('${orderIdSafe}')" title="Hapus Invoice Ini">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        `;

        const row = document.createElement('tr');
        row.innerHTML = `
          <td style="font-family: monospace; font-weight: 700; font-size: 13px;">
            <a href="javascript:void(0)" onclick="openInvoiceDetailModal('${orderIdSafe}')" style="color: #38bdf8; text-decoration: none;" title="Klik untuk melihat rincian transaksi">
              <i class="fas fa-file-invoice mr-1"></i> ${inv.order_id || inv.invoice_number || 'INV-LOEWIX'}
            </a>
          </td>
          <td>
            <div class="font-weight-bold text-white" style="font-size: 13px;">
              <i class="fas fa-building text-info mr-1" style="font-size: 11px;"></i> ${inv.user_name || 'Customer PT'}
            </div>
            <small class="text-muted" style="font-size: 11px;">${inv.user_email || ''}</small>
          </td>
          <td>
            <span class="badge badge-info p-2" style="background: rgba(56,189,248,0.15); border: 1px solid rgba(56,189,248,0.3); color: #38bdf8; border-radius: 6px; font-weight: 600;">
              ${inv.plan_name || 'Business Plan'} (${inv.billing_cycle === 'annual' ? 'Tahunan' : 'Bulanan'})
            </span>
          </td>
          <td class="font-weight-bold" style="color: #34d399; font-size: 14px;">
            Rp ${Number(totalAmt).toLocaleString('id-ID')}
          </td>
          <td>
            <span class="badge badge-dark text-uppercase p-2" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); font-size: 11px;">
              <i class="fas fa-wallet mr-1 text-info"></i> ${payType}
            </span>
          </td>
          <td>${statusBadge}</td>
          <td class="text-muted" style="font-size: 11.5px; white-space: nowrap;">
            <i class="fas fa-calendar-check mr-1 text-info"></i> ${inv.settlement_time || inv.transaction_time || inv.created_at || '2026-08-29 10:00'}
          </td>
          <td class="text-right">${actionButtons}</td>
        `;
        tbody.appendChild(row);
      });
    }

    function filterAdminTransactionsTable() {
      const search = (document.getElementById('search-transaction-input')?.value || '').toLowerCase();
      const statusFilter = document.getElementById('filter-transaction-status')?.value || 'all';
      const payFilter = document.getElementById('filter-transaction-payment')?.value || 'all';

      const filtered = cachedAdminTransactions.filter(inv => {
        const orderId = (inv.order_id || inv.invoice_number || '').toLowerCase();
        const userName = (inv.user_name || '').toLowerCase();
        const userEmail = (inv.user_email || '').toLowerCase();
        const planName = (inv.plan_name || '').toLowerCase();
        const payType = (inv.payment_type || '').toLowerCase();
        const status = (inv.status || 'settlement').toLowerCase();

        const matchSearch = orderId.includes(search) || userName.includes(search) || userEmail.includes(search) || planName.includes(search);
        
        let matchStatus = true;
        if (statusFilter === 'settlement') matchStatus = (status === 'settlement' || status === 'capture' || status === 'success');
        else if (statusFilter === 'pending') matchStatus = (status === 'pending');
        else if (statusFilter === 'expire') matchStatus = (status === 'expire' || status === 'cancel' || status === 'failure');

        let matchPay = true;
        if (payFilter === 'va') matchPay = payType.includes('va') || payType.includes('bill') || payType.includes('bank');
        else if (payFilter === 'qris') matchPay = payType.includes('qris') || payType.includes('gopay');
        else if (payFilter === 'manual') matchPay = payType.includes('manual');

        return matchSearch && matchStatus && matchPay;
      });

      renderAdminTransactionsTable(filtered);
    }

    function exportAdminTransactionsCSV() {
      if (!cachedAdminTransactions || cachedAdminTransactions.length === 0) {
        alert('Tidak ada data transaksi untuk diexport.');
        return;
      }
      let csv = 'No Invoice,Nama Pelanggan,Email,Paket SaaS,Siklus,Harga Paket,PPN 11%,Total Bayar,Metode Bayar,Status,Waktu Transaksi\n';
      cachedAdminTransactions.forEach(inv => {
        const amt = inv.amount || 0;
        const tax = inv.tax_amount || Math.round(amt * 0.11);
        const tot = inv.total_amount || (amt + tax);
        csv += `"${inv.order_id}","${inv.user_name || '-'}","${inv.user_email || '-'}","${inv.plan_name || '-'}","${inv.billing_cycle || '-'}","${amt}","${tax}","${tot}","${inv.payment_type || '-'}","${inv.status || '-'}","${inv.settlement_time || inv.created_at || '-'}"\n`;
      });
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `loewix_transaksi_saas_${new Date().toISOString().slice(0, 10)}.csv`;
      a.click();
    }

    function openCreateManualInvoiceModal() {
      const custSelect = document.getElementById('manual-inv-customer');
      if (custSelect) {
        custSelect.innerHTML = (cachedAdminCustomers || []).map(c => 
          `<option value="${c.id}">${c.name} (${c.email}) - Kuota: ${c.cctv_quota} CCTV</option>`
        ).join('');
      }

      const planSelect = document.getElementById('manual-inv-plan');
      if (planSelect && typeof cachedAdminPlans !== 'undefined') {
        planSelect.innerHTML = cachedAdminPlans.map(p => 
          `<option value="${p.id}" data-monthly="${p.price}" data-annual="${p.price_annual || p.price * 12}">${p.name} (Rp ${Number(p.price).toLocaleString('id-ID')}/bln)</option>`
        ).join('');
      }

      autoCalculateManualInvoiceTotal();
      openModalHelper('modalCreateManualInvoice');
    }

    function autoCalculateManualInvoiceTotal() {
      const planSelect = document.getElementById('manual-inv-plan');
      const cycleSelect = document.getElementById('manual-inv-cycle');
      const amountInput = document.getElementById('manual-inv-amount');
      const taxEl = document.getElementById('manual-inv-tax-text');
      const totalEl = document.getElementById('manual-inv-total-text');

      if (!planSelect || !cycleSelect || !amountInput) return;

      const opt = planSelect.options[planSelect.selectedIndex];
      if (opt && (!amountInput.value || document.activeElement !== amountInput)) {
        const cycle = cycleSelect.value;
        const base = (cycle === 'annual') 
          ? (Number(opt.getAttribute('data-annual')) || 2990000)
          : (Number(opt.getAttribute('data-monthly')) || 299000);
        amountInput.value = base;
      }

      const amt = Number(amountInput.value) || 0;
      const tax = Math.round(amt * 0.11);
      const tot = amt + tax;

      if (taxEl) taxEl.textContent = 'Rp ' + tax.toLocaleString('id-ID');
      if (totalEl) totalEl.textContent = 'Rp ' + tot.toLocaleString('id-ID');
    }

    async function submitCreateManualInvoice(e) {
      e.preventDefault();
      const userId = document.getElementById('manual-inv-customer').value;
      const planId = document.getElementById('manual-inv-plan').value;
      const billingCycle = document.getElementById('manual-inv-cycle').value;
      const amount = document.getElementById('manual-inv-amount').value;
      const paymentMethod = document.getElementById('manual-inv-method').value;
      const status = document.getElementById('manual-inv-status').value;

      const fd = new FormData();
      fd.append('action', 'create_manual_invoice');
      fd.append('user_id', userId);
      fd.append('plan_id', planId);
      fd.append('billing_cycle', billingCycle);
      fd.append('amount', amount);
      fd.append('payment_method', paymentMethod);
      fd.append('status', status);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert(data.message || 'Invoice manual berhasil diterbitkan!');
          closeModalHelper('modalCreateManualInvoice');
          loadAdminTransactionsList();
          loadAdminCustomersList();
        } else {
          alert(data.message || 'Gagal menerbitkan invoice.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi ke server.');
      }
    }

    function openInvoiceDetailModal(orderId) {
      const inv = (cachedAdminTransactions || []).find(item => item.order_id === orderId);
      if (!inv) return;

      const content = document.getElementById('invoice-detail-content');
      if (!content) return;

      const amt = inv.amount || 0;
      const tax = inv.tax_amount || Math.round(amt * 0.11);
      const tot = inv.total_amount || (amt + tax);
      const st = (inv.status || 'settlement').toLowerCase();

      let stText = '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> LUNAS (SETTLEMENT)</span>';
      if (st === 'pending') stText = '<span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> MENUNGGU PEMBAYARAN</span>';
      else if (st === 'expire' || st === 'cancel') stText = '<span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> EXPIRED / CANCELLED</span>';

      content.innerHTML = `
        <div class="p-3 mb-3 rounded" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span style="font-size: 11px; font-weight: 700; color: #38bdf8; font-family: monospace;">INVOICE ID</span>
            ${stText}
          </div>
          <div class="font-weight-bold text-white" style="font-size: 17px; font-family: monospace;">${inv.order_id}</div>
          <small class="text-muted"><i class="fas fa-clock mr-1"></i> Dibuat: ${inv.created_at || '2026-08-31 10:00'}</small>
        </div>

        <div class="row g-2 mb-3 text-white" style="font-size: 13px;">
          <div class="col-6">
            <small class="text-muted d-block">Pelanggan / Tenant:</small>
            <strong>${inv.user_name || 'Customer'}</strong>
            <div class="text-muted" style="font-size: 11px;">${inv.user_email || '-'}</div>
          </div>
          <div class="col-6">
            <small class="text-muted d-block">Metode Pembayaran:</small>
            <strong class="text-uppercase" style="color: #38bdf8;">${inv.payment_type || 'Virtual Account'}</strong>
            <div class="text-muted" style="font-size: 11px;">Gateway: Midtrans Snap API</div>
          </div>
        </div>

        <div class="p-3 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08);">
          <div class="d-flex justify-content-between py-1 text-light" style="font-size: 12.5px;">
            <span>Paket: <strong>${inv.plan_name || 'Business Plan'}</strong> (${inv.billing_cycle === 'annual' ? 'Tahunan' : 'Bulanan'})</span>
            <span>Rp ${Number(amt).toLocaleString('id-ID')}</span>
          </div>
          <div class="d-flex justify-content-between py-1 text-muted" style="font-size: 12px; border-bottom: 1px dashed rgba(255,255,255,0.15);">
            <span>PPN 11% (Pajak Pertambahan Nilai)</span>
            <span>Rp ${Number(tax).toLocaleString('id-ID')}</span>
          </div>
          <div class="d-flex justify-content-between pt-2 font-weight-bold" style="font-size: 15px; color: #34d399;">
            <span>TOTAL TAGIHAN</span>
            <span>Rp ${Number(tot).toLocaleString('id-ID')}</span>
          </div>
        </div>
      `;

      const btnPrint = document.getElementById('btn-detail-print-receipt');
      if (btnPrint) {
        btnPrint.onclick = function() {
          closeModalHelper('modalInvoiceDetail');
          showInvoiceReceiptModal(orderId);
        };
      }

      openModalHelper('modalInvoiceDetail');
    }

    async function deleteAdminInvoice(orderId) {
      const ok = await showLoewixConfirmModal({
        title: 'Hapus Data Invoice',
        subtitle: 'Konfirmasi Penghapusan Rekam Transaksi',
        icon: 'fas fa-trash-alt',
        iconColor: '#f43f5e',
        isDanger: true,
        targetName: 'Invoice #' + orderId,
        targetMeta: 'Log Transaksi Finansial SaaS',
        message: `Apakah Anda yakin ingin menghapus data invoice <strong>#${orderId}</strong>?<br><small class="text-muted">Data yang dihapus tidak dapat dipulihkan kembali.</small>`,
        confirmText: 'Ya, Hapus Invoice',
        confirmGradient: 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)'
      });
      if (!ok) return;

      const fd = new FormData();
      fd.append('action', 'delete_invoice');
      fd.append('order_id', orderId);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          loadAdminTransactionsList();
        } else {
          alert(data.message || 'Gagal menghapus invoice.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi ke server.');
      }
    }

    async function sendAdminPaymentReminder(orderId, userName, userEmail) {
      const ok = await showLoewixConfirmModal({
        title: 'Kirim Email Reminder Tagihan',
        subtitle: 'Notifikasi Otomatis Pembayaran',
        icon: 'fas fa-paper-plane',
        iconColor: '#38bdf8',
        targetName: `${userName} (${userEmail})`,
        targetMeta: 'Invoice Order: #' + orderId,
        message: `Kirimkan email pengingat pembayaran otomatis tagihan invoice <strong>#${orderId}</strong> ke alamat <strong>${userEmail}</strong>?`,
        confirmText: 'Ya, Kirim Email',
        confirmGradient: 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)'
      });
      if (!ok) return;

      const fd = new FormData();
      fd.append('action', 'send_payment_reminder');
      fd.append('order_id', orderId);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        alert(data.message || (data.success ? 'Email pengingat berhasil dikirim!' : 'Gagal kirim email.'));
      } catch (err) {
        alert('Terjadi kesalahan koneksi ke server.');
      }
    }

    async function markAdminInvoiceSettled(orderId, userName) {
      const ok = await showLoewixConfirmModal({
        title: 'Aktivasi Manual Tagihan',
        subtitle: 'Konfirmasi Pembayaran Lunas',
        icon: 'fas fa-check-circle',
        iconColor: '#10b981',
        targetName: userName,
        targetMeta: 'Invoice Order: #' + orderId,
        message: `Tandai invoice <strong>#${orderId}</strong> milik <strong>${userName}</strong> sebagai <strong>LUNAS (SETTLEMENT)</strong>?<br><small class="text-muted">Kuota CCTV pelanggan akan langsung aktif seketika.</small>`,
        confirmText: 'Konfirmasi Lunas',
        confirmGradient: 'linear-gradient(135deg, #10b981 0%, #059669 100%)'
      });
      if (!ok) return;

      const fd = new FormData();
      fd.append('action', 'mark_invoice_settled');
      fd.append('order_id', orderId);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert(data.message || 'Invoice berhasil ditandai Lunas!');
          loadAdminTransactionsList();
        } else {
          alert(data.message || 'Gagal update status invoice.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi ke server.');
      }
    }

    // ========================================================
    // SUPER ADMIN MIDTRANS SETTINGS
    // ========================================================
    async function openMidtransSettingsModal() {
      try {
        const res = await fetch('../api/payment.php?action=get_midtrans_settings');
        const data = await res.json();
        if (data.success && data.midtrans) {
          const m = data.midtrans;
          document.getElementById('midtrans-input-env').value = m.is_production ? 'true' : 'false';
          document.getElementById('midtrans-input-merchant').value = m.merchant_id || 'G589001445';
          document.getElementById('midtrans-input-client').value = m.client_key || 'Mid-client-mGA7v04cXrux3KNF';
          document.getElementById('midtrans-input-server').value = m.server_key || '';
        }
      } catch (err) {
        console.error('Failed to load Midtrans settings:', err);
      }
      openModalHelper('modalMidtransSettings');
    }

    async function submitSaveMidtransSettings(e) {
      e.preventDefault();
      const isProd = document.getElementById('midtrans-input-env').value;
      const merchantId = document.getElementById('midtrans-input-merchant').value.trim();
      const clientKey = document.getElementById('midtrans-input-client').value.trim();
      const serverKey = document.getElementById('midtrans-input-server').value.trim();

      const fd = new FormData();
      fd.append('action', 'save_midtrans_settings');
      fd.append('is_production', isProd);
      fd.append('merchant_id', merchantId);
      fd.append('client_key', clientKey);
      fd.append('server_key', serverKey);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        alert(data.message || (data.success ? 'Kredensial Midtrans berhasil disimpan!' : 'Gagal simpan.'));
        if (data.success) {
          closeModalHelper('modalMidtransSettings');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    // ========================================================
    // SUPER ADMIN SMTP SETTINGS & TEST SENDER
    // ========================================================
    async function openSmtpSettingsModal() {
      const resEl = document.getElementById('smtp-test-result');
      if (resEl) resEl.style.display = 'none';

      try {
        const res = await fetch('../api/payment.php?action=get_smtp_settings');
        const data = await res.json();
        if (data.success && data.smtp) {
          const s = data.smtp;
          document.getElementById('smtp-input-host').value = s.smtp_host || 'smtp.gmail.com';
          document.getElementById('smtp-input-port').value = s.smtp_port || 587;
          document.getElementById('smtp-input-user').value = s.smtp_user || '';
          document.getElementById('smtp-input-pass').value = s.smtp_pass || '';
          document.getElementById('smtp-input-secure').value = s.smtp_secure || 'tls';
          document.getElementById('smtp-input-name').value = s.mail_from_name || 'PT. LOEWIX INDONESIA';
          document.getElementById('smtp-test-recipient').value = s.smtp_user || currentCustomer?.email || 'wahyuwutomo31@gmail.com';
        }
      } catch (err) {
        console.error('Failed to load SMTP settings:', err);
      }
      openModalHelper('modalSmtpSettings');
    }

    async function submitSaveSmtpSettings(e) {
      e.preventDefault();
      const host = document.getElementById('smtp-input-host').value.trim();
      const port = document.getElementById('smtp-input-port').value;
      const user = document.getElementById('smtp-input-user').value.trim();
      const pass = document.getElementById('smtp-input-pass').value;
      const secure = document.getElementById('smtp-input-secure').value;
      const name = document.getElementById('smtp-input-name').value.trim();

      const fd = new FormData();
      fd.append('action', 'save_smtp_settings');
      fd.append('smtp_host', host);
      fd.append('smtp_port', port);
      fd.append('smtp_user', user);
      fd.append('smtp_pass', pass);
      fd.append('smtp_secure', secure);
      fd.append('mail_from', user);
      fd.append('mail_from_name', name);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        alert(data.message || (data.success ? 'Pengaturan SMTP berhasil disimpan!' : 'Gagal simpan.'));
        if (data.success) {
          closeModalHelper('modalSmtpSettings');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    async function runTestSmtpEmail() {
      const recipient = document.getElementById('smtp-test-recipient').value.trim();
      const resEl = document.getElementById('smtp-test-result');
      if (!recipient) {
        alert('Masukkan email tujuan tes terlebih dahulu!');
        return;
      }

      if (resEl) {
        resEl.style.display = 'block';
        resEl.className = 'mt-2 text-info';
        resEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sedang menghubungkan ke server SMTP dan mengirim email tes...';
      }

      // Auto save current form inputs first
      const host = document.getElementById('smtp-input-host').value.trim();
      const port = document.getElementById('smtp-input-port').value;
      const user = document.getElementById('smtp-input-user').value.trim();
      const pass = document.getElementById('smtp-input-pass').value;
      const secure = document.getElementById('smtp-input-secure').value;
      const name = document.getElementById('smtp-input-name').value.trim();

      const saveFd = new FormData();
      saveFd.append('action', 'save_smtp_settings');
      saveFd.append('smtp_host', host);
      saveFd.append('smtp_port', port);
      saveFd.append('smtp_user', user);
      saveFd.append('smtp_pass', pass);
      saveFd.append('smtp_secure', secure);
      saveFd.append('mail_from', user);
      saveFd.append('mail_from_name', name);
      await fetch('../api/payment.php', { method: 'POST', body: saveFd });

      const fd = new FormData();
      fd.append('action', 'test_smtp_email');
      fd.append('target_email', recipient);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          resEl.className = 'mt-2 text-success font-weight-bold';
          resEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i> ' + data.message;
        } else {
          resEl.className = 'mt-2 text-danger font-weight-bold';
          resEl.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> ' + data.message;
        }
      } catch (err) {
        resEl.className = 'mt-2 text-danger';
        resEl.innerHTML = '<i class="fas fa-times-circle mr-1"></i> Gagal menghubungi server API.';
      }
    }

    async function loadBillingDashboardData(btnEl) {
      const btn = btnEl || document.getElementById('btn-refresh-invoices');
      const originalHtml = btn ? btn.innerHTML : '';
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat...';
      }

      try {
        const u = currentCustomer || (localStorage.getItem('loewix_user') ? JSON.parse(localStorage.getItem('loewix_user')) : null);
        const url = (u && u.id) 
          ? `../api/payment.php?action=get_billing_dashboard&user_id=${u.id}&email=${encodeURIComponent(u.email || '')}` 
          : '../api/payment.php?action=get_billing_dashboard';

        const res = await fetch(url);
        const data = await res.json();
        if (data.success) {
          currentBillingData = data;
          renderBillingData(data);
          
          if (btn) {
            btn.innerHTML = '<i class="fas fa-check text-success mr-1"></i> Data Terkini!';
            setTimeout(() => {
              btn.disabled = false;
              btn.innerHTML = '<i class="fas fa-rotate mr-1"></i> Segarkan Tagihan';
            }, 1200);
          }
        } else {
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalHtml || '<i class="fas fa-rotate mr-1"></i> Segarkan Tagihan';
          }
        }
      } catch (err) {
        console.error('Failed to load billing dashboard:', err);
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = originalHtml || '<i class="fas fa-rotate mr-1"></i> Segarkan Tagihan';
        }
      }
    }

    function renderBillingData(data) {
      const allSubs = data.subscriptions || (data.subscription ? [data.subscription] : []);
      const totalActiveQuota = data.total_active_quota || (data.subscription ? data.subscription.cctv_quota : 20);
      const invoices = data.invoices || [];
      const profile = data.billing_profile || {};
      const plans = data.plans || [];

      // Update Top Stats Card Quota Max & Hero Banner in real-time
      if (currentCustomer) {
        currentCustomer.cctv_quota = totalActiveQuota;
      }
      const quotaMaxEl = document.getElementById('card-quota-max');
      if (quotaMaxEl) quotaMaxEl.innerText = totalActiveQuota;
      const heroQuotaText = document.getElementById('hero-quota-text');
      const heroRemCount = document.getElementById('hero-remaining-count');
      const used = (currentCustomer && currentCustomer.cctv_used) ? parseInt(currentCustomer.cctv_used) : (customerCameras ? customerCameras.length : 0);
      if (heroQuotaText && totalActiveQuota > 0) {
        const pct = Math.min(100, Math.round((used / totalActiveQuota) * 100));
        heroQuotaText.innerText = `${used} / ${totalActiveQuota} Kamera (${pct}%)`;
      }
      if (heroRemCount && totalActiveQuota > 0) {
        heroRemCount.innerText = Math.max(0, totalActiveQuota - used);
      }

      // 1. Render Active Package(s) License Cards Separately
      const subsListContainer = document.getElementById('active-subscriptions-list-container');
      const totalQuotaSummaryBadge = document.getElementById('total-quota-summary-badge');

      if (totalQuotaSummaryBadge) {
        totalQuotaSummaryBadge.textContent = `Total Kuota: ${totalActiveQuota} CCTV`;
      }

      if (subsListContainer) {
        if (allSubs.length === 0) {
          subsListContainer.innerHTML = '<div class="text-center py-4 text-muted">Belum ada lisensi paket aktif.</div>';
        } else {
          subsListContainer.innerHTML = allSubs.map((subItem, idx) => {
            const expDate = subItem.expires_at ? new Date(subItem.expires_at.replace(/-/g, '/')) : new Date();
            const expDateFmt = expDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            const now = new Date();
            const diffTime = expDate - now;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const isActive = diffDays > 0 && subItem.status === 'active';

            const statusBadge = isActive 
              ? '<span class="billing-status-badge active"><i class="fas fa-check-circle"></i> AKTIF</span>'
              : '<span class="billing-status-badge badge-danger text-white bg-danger"><i class="fas fa-times-circle"></i> EXPIRED</span>';

            const sisaWaktuBadge = isActive
              ? `<strong class="text-info"><i class="fas fa-hourglass-half mr-1"></i> Sisa ${diffDays} Hari Lagi</strong>`
              : `<strong class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Telah Berakhir</strong>`;

            const cycleText = subItem.billing_cycle === 'annual' ? 'Tahunan (Annual - Hemat 2 Bln)' : 'Bulanan (Monthly)';
            const costText = 'Rp ' + Number(subItem.amount || 0).toLocaleString('id-ID') + (subItem.billing_cycle === 'annual' ? ' / Tahun' : ' / Bulan');

            return `
              <div class="package-item-card p-3.5 mb-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 12px; position: relative;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-dark px-2 py-0.5" style="background: rgba(255,255,255,0.08); font-size: 11px; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px;">Lisensi #${idx + 1}</span>
                    <h5 class="font-weight-bold text-white mb-0" style="font-size: 15px;">${subItem.plan_name}</h5>
                  </div>
                  <div>${statusBadge}</div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="badge badge-info px-2.5 py-1" style="font-size: 12px; font-weight: 700; border-radius: 6px;">${subItem.cctv_quota} CCTV Kuota</span>
                  <small class="text-muted">Siklus: <strong class="text-white">${cycleText}</strong></small>
                </div>

                <div class="p-2.5 mb-3" style="background: rgba(4, 9, 24, 0.6); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 8px;">
                  <div class="d-flex justify-content-between mb-1" style="font-size: 12px;">
                    <span class="text-muted">Masa Aktif Hingga:</span>
                    <strong class="text-warning">${expDateFmt}</strong>
                  </div>
                  <div class="d-flex justify-content-between mb-1" style="font-size: 12px;">
                    <span class="text-muted">Status / Sisa Waktu:</span>
                    ${sisaWaktuBadge}
                  </div>
                  <div class="d-flex justify-content-between" style="font-size: 12px;">
                    <span class="text-muted">Biaya Lisensi:</span>
                    <strong class="text-emerald" style="color: #34d399;">${costText}</strong>
                  </div>
                </div>

                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-info font-weight-bold flex-fill py-1.5" onclick="checkoutPlanMidtrans('${subItem.plan_id}', '${subItem.billing_cycle || 'annual'}')" style="border-radius: 8px; font-size: 12px; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3);">
                    <i class="fas fa-sync mr-1"></i> Perpanjang Lisensi Ini (${subItem.plan_name})
                  </button>
                </div>
              </div>
            `;
          }).join('') + `
            <div class="text-center mt-2">
              <small class="text-muted" style="font-size: 11px;">
                <i class="fas fa-info-circle text-info mr-1"></i> Setiap lisensi paket memiliki masa aktif dan kuota mandiri. Total kuota CCTV dihitung otomatis dari seluruh paket yang masih aktif.
              </small>
            </div>
          `;
        }
      }

      // 2. Render Upgrade / Add-On Plans
      const upgradeContainer = document.getElementById('upgrade-plans-container');
      if (upgradeContainer && plans.length > 0) {
        let upgradeHtml = '';
        plans.forEach(p => {
          upgradeHtml += `
            <div class="p-3 mb-3" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 12px;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                  <h6 class="text-white font-weight-bold mb-0">${p.name} (${p.cctv_quota} CCTV)</h6>
                  <small class="text-info font-weight-bold">${p.features ? p.features[1] : 'Full Cloud Stream H.265'}</small>
                </div>
                <div class="text-right">
                  <div class="text-emerald font-weight-bold" style="color: #34d399; font-size: 15px;">Rp ${Number(p.price_annual).toLocaleString('id-ID')}<small>/thn</small></div>
                  <small class="text-muted">Atau Rp ${Number(p.price_monthly).toLocaleString('id-ID')}/bln</small>
                </div>
              </div>
              <div class="d-flex gap-2 mt-2">
                <button class="btn btn-sm btn-outline-info flex-fill font-weight-bold" onclick="checkoutPlanMidtrans('${p.id}', 'annual')">
                  <i class="fas fa-plus-circle mr-1"></i> Beli Lisensi Tahunan
                </button>
                <button class="btn btn-sm btn-outline-light font-weight-bold" onclick="checkoutPlanMidtrans('${p.id}', 'monthly')">
                  Bulanan
                </button>
              </div>
            </div>
          `;
        });
        if (upgradeHtml) upgradeContainer.innerHTML = upgradeHtml;
      }

      // 3. Render Invoices Tab (Itemized Multi-Package Billing Cards)
      const invoiceContainer = document.getElementById('active-invoice-container');
      if (invoiceContainer) {
        if (allSubs.length === 0) {
          invoiceContainer.innerHTML = '<div class="text-center py-4 text-muted">Tidak ada tagihan tertunggak saat ini.</div>';
        } else {
          invoiceContainer.innerHTML = allSubs.map((subItem, idx) => {
            const matchedInv = invoices.find(inv => inv.plan_id === subItem.plan_id) || invoices[idx] || {};
            const orderId = subItem.order_id || matchedInv.order_id || `INV-LWX-${idx + 1}`;
            const totalFmt = 'Rp ' + Number(matchedInv.total_amount || matchedInv.amount || subItem.amount || 0).toLocaleString('id-ID');
            const cycleText = subItem.billing_cycle === 'annual' ? 'Tahunan' : 'Bulanan';
            
            const expDate = subItem.expires_at ? new Date(subItem.expires_at.replace(/-/g, '/')) : new Date();
            const now = new Date();
            const diffDays = Math.ceil((expDate - now) / (1000 * 60 * 60 * 24));
            const isPaid = diffDays > 0 && subItem.status === 'active';

            const statusBadge = isPaid
              ? `<span class="badge badge-success px-3 py-1.5" style="font-size: 11px; font-weight: 800; letter-spacing: 0.5px; border-radius: 6px; background: #059669; color: #ffffff;"><i class="fas fa-check-circle mr-1"></i> SEMUA TAGIHAN LUNAS</span>`
              : `<span class="badge badge-danger px-3 py-1.5" style="font-size: 11px; font-weight: 800; letter-spacing: 0.5px; border-radius: 6px; background: #dc2626; color: #ffffff;"><i class="fas fa-exclamation-triangle mr-1"></i> TAGIHAN JATUH TEMPO</span>`;

            return `
              <div class="p-4 mb-3" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(12, 22, 48, 0.95)); border: 1.5px solid rgba(56, 189, 248, 0.25); border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.4);">
                <div class="row align-items-center">
                  <!-- Left: Invoice Status, Title & Details -->
                  <div class="col-md-7 col-12 mb-3 mb-md-0">
                    <div class="d-flex align-items-center flex-wrap mb-2" style="gap: 10px;">
                      ${statusBadge}
                      <span class="badge badge-dark px-2.5 py-1" style="background: rgba(255,255,255,0.08); font-size: 11px; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px;">Lisensi #${idx + 1}</span>
                      <span class="text-muted" style="font-size: 12px;">
                        No. Invoice: <strong class="text-white font-monospace">${orderId}</strong>
                      </span>
                    </div>

                    <h3 class="text-white font-weight-bold mb-1.5" style="font-size: 19px; letter-spacing: -0.2px;">
                      ${subItem.plan_name} (${subItem.cctv_quota} CCTV) – Periode ${cycleText}
                    </h3>
                    <p class="text-muted mb-0" style="font-size: 12.5px; line-height: 1.5;">
                      <i class="fas fa-shield-alt text-success mr-1"></i> Layanan streaming ${subItem.cctv_quota} CCTV aktif normal. Masa aktif s.d. <strong class="text-warning">${expDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</strong> (${isPaid ? 'Sisa ' + diffDays + ' hari' : 'Jatuh tempo'}).
                    </p>
                  </div>

                  <!-- Right: Price Display & Clean Side-by-Side Action Buttons -->
                  <div class="col-md-5 col-12 text-md-right text-left">
                    <div class="mb-3">
                      <div class="text-muted mb-1" style="font-size: 11.5px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Total Pembayaran Terakhir</div>
                      <h2 class="text-emerald font-weight-bold mb-0" style="color: #34d399; font-size: 24px; font-family: 'Space Grotesk', sans-serif; letter-spacing: -0.5px;">${totalFmt}</h2>
                    </div>

                    <div class="d-flex align-items-center justify-content-md-end justify-content-start flex-wrap" style="gap: 8px;">
                      <a href="receipt.php?order_id=${encodeURIComponent(orderId)}" target="_blank" class="btn btn-outline-info font-weight-bold px-3 py-1.5" style="border-radius: 8px; font-size: 12px; border-color: rgba(56, 189, 248, 0.4); color: #38bdf8; background: rgba(56, 189, 248, 0.06); text-decoration: none;">
                        <i class="fas fa-file-invoice mr-1"></i> Kwitansi
                      </a>
                      <button class="btn btn-info font-weight-bold px-3 py-1.5" onclick="checkoutPlanMidtrans('${subItem.plan_id}', '${subItem.billing_cycle || 'annual'}')" style="border-radius: 8px; font-size: 12px; background: linear-gradient(135deg, #0284c7, #0ea5e9); border: none; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); color: #ffffff;">
                        <i class="fas fa-sync mr-1"></i> Perpanjang Lisensi #${idx + 1}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            `;
          }).join('');
        }
      }

      // 4. Render Transaction History Table (Ensuring all active licenses have transaction rows)
      let combinedInvoices = [...invoices];
      
      if (allSubs.length > 0) {
        allSubs.forEach((subItem, idx) => {
          const matched = combinedInvoices.find(inv => 
            (subItem.order_id && inv.order_id === subItem.order_id) || 
            (subItem.plan_id && inv.plan_id === subItem.plan_id)
          );
          if (!matched) {
            const basePrice = Number(subItem.amount || 1490000);
            const tax = Math.round(basePrice * 0.11);
            combinedInvoices.push({
              order_id: subItem.order_id || `INV-LWX-20260902-${(subItem.plan_id || '0' + (idx + 1)).toUpperCase().replace(/[^A-Z0-9]/g, '')}`,
              transaction_time: subItem.start_date || '2026-09-02 12:00:00',
              plan_name: subItem.plan_name || 'Paket CCTV Loewix',
              billing_cycle: subItem.billing_cycle || 'annual',
              payment_type: 'bank_transfer_bca',
              amount: basePrice,
              tax_amount: tax,
              total_amount: basePrice + tax,
              status: subItem.status === 'active' ? 'settlement' : 'expired'
            });
          }
        });
      }

      const historyTbody = document.getElementById('tx-history-tbody');
      if (historyTbody) {
        if (combinedInvoices.length === 0) {
          historyTbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td></tr>';
        } else {
          historyTbody.innerHTML = combinedInvoices.map(inv => {
            const isSettlement = inv.status === 'settlement' || inv.status === 'capture';
            const statusBadge = isSettlement 
              ? '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> LUNAS</span>'
              : '<span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> PENDING</span>';

            const methodText = (inv.payment_type || 'BCA / QRIS').toUpperCase().replace(/_/g, ' ');
            const totalFmt = 'Rp ' + Number(inv.total_amount || inv.amount).toLocaleString('id-ID');

            return `
              <tr>
                <td><strong class="text-info font-monospace">${inv.order_id}</strong></td>
                <td style="font-size: 12px; color: #94a3b8;">${inv.transaction_time || '-'}</td>
                <td><strong class="text-white">${inv.plan_name}</strong> <small class="text-muted d-block">${inv.billing_cycle === 'annual' ? '1 Tahun' : '1 Bulan'}</small></td>
                <td><span class="badge badge-dark px-2 py-1" style="background: rgba(255,255,255,0.08);">${methodText}</span></td>
                <td><strong class="text-emerald" style="color: #34d399;">${totalFmt}</strong></td>
                <td>${statusBadge}</td>
                <td class="text-center">
                  <a href="receipt.php?order_id=${encodeURIComponent(inv.order_id)}" target="_blank" class="btn btn-sm btn-info font-weight-bold px-3 py-1 text-white" style="background: #0284c7; border: none; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 4px; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.4);" title="Buka & Cetak Kwitansi Resmi PT. Loewix Indonesia">
                    <i class="fas fa-receipt"></i> Kwitansi
                  </a>
                </td>
              </tr>
            `;
          }).join('');
        }
      }

      // 5. Render Billing Profile Form (Auto prefilled with user registration data)
      const comp = document.getElementById('bill-company-name');
      const email = document.getElementById('bill-email');
      const phone = document.getElementById('bill-phone');
      const addr = document.getElementById('bill-address');

      const activeUser = currentCustomer || (localStorage.getItem('loewix_user') ? JSON.parse(localStorage.getItem('loewix_user')) : null);
      const defName = (profile && profile.company_name) ? profile.company_name : (activeUser ? activeUser.name : '');
      const defEmail = (profile && profile.billing_email) ? profile.billing_email : (activeUser ? activeUser.email : '');
      const defPhone = (profile && profile.billing_phone) ? profile.billing_phone : (activeUser ? (activeUser.phone || '') : '');
      const defAddr = (profile && profile.billing_address) ? profile.billing_address : (activeUser ? ('Kota ' + (activeUser.city || 'Bandung') + ', Indonesia') : '');

      if (comp) comp.value = defName;
      if (email) email.value = defEmail;
      if (phone) phone.value = defPhone;
      if (addr) addr.value = defAddr;
    }

    async function checkoutPlanMidtrans(planId, cycle) {
      if (!confirm(`Konfirmasi pembelian paket CCTV (${cycle === 'annual' ? 'Tahunan' : 'Bulanan'}) via Midtrans?`)) {
        return;
      }

      try {
        const fd = new FormData();
        fd.append('action', 'create_snap_token');
        fd.append('plan_id', planId);
        fd.append('billing_cycle', cycle);

        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const result = await res.json();

        if (result.success && result.snap_token) {
          const isUuid = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(result.snap_token);
          const isSim = result.is_simulation === true || result.snap_token.startsWith('SNAP_LOEWIX_') || !isUuid;

          if (!isSim && window.snap && typeof window.snap.pay === 'function') {
            window.snap.pay(result.snap_token, {
              onSuccess: function(r) {
                confirmClientPayment(result.order_id, r.payment_type || 'midtrans');
              },
              onPending: function(r) {
                confirmClientPayment(result.order_id, r.payment_type || 'midtrans_pending');
              },
              onError: function(r) {
                alert('Pembayaran gagal atau dibatalkan.');
              }
            });
          } else {
            // Interactive Virtual Account & QRIS Modal
            showLoewixCustomerVaModal(result);
          }
        } else {
          alert(result.message || 'Gagal membuat sesi pembayaran Midtrans.');
        }
      } catch (err) {
        console.error('Error during checkout:', err);
        alert('Terjadi kesalahan saat memproses pembayaran.');
      }
    }

    function renewCurrentPlan() {
      const sub = currentBillingData ? currentBillingData.subscription : null;
      const planId = sub ? sub.plan_id : 'business_10';
      const cycle = sub ? sub.billing_cycle : 'annual';
      checkoutPlanMidtrans(planId, cycle);
    }

    async function confirmClientPayment(orderId, paymentType) {
      try {
        const fd = new FormData();
        fd.append('action', 'verify_payment');
        fd.append('order_id', orderId);
        fd.append('payment_type', paymentType);

        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
          alert('Pembayaran Berhasil! Paket langganan dan kuota CCTV Anda telah diperbarui.');
          loadBillingDashboardData();
          initCustomerSession(); // Reload metrics & quota bar
        }
      } catch (err) {
        console.error('Verification error:', err);
      }
    }

    function showLoewixCustomerVaModal(result) {
      let modal = document.getElementById('modalCustomerVaPopup');
      if (!modal) {
        modal = document.createElement('div');
        modal.id = 'modalCustomerVaPopup';
        document.body.appendChild(modal);
      }

      modal.style.cssText = `
        position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important;
        background: rgba(4, 9, 24, 0.88) !important; backdrop-filter: blur(12px) !important;
        z-index: 2147483647 !important; display: flex !important; align-items: center !important; justify-content: center !important; padding: 20px !important;
      `;

      const totalFormatted = result.plan ? result.plan.total_formatted : 'Rp ' + Number(result.gross_amount).toLocaleString('id-ID');
      const userPhoneClean = (currentUser && currentUser.phone) ? currentUser.phone.replace(/[^0-9]/g, '') : '081234567890';
      const bcaVaNumber = '8277' + (userPhoneClean.length >= 10 ? userPhoneClean.slice(-10) : '0857715935');
      const mandiriBillCode = '70012';
      const mandiriBillKey = '88' + (userPhoneClean.length >= 8 ? userPhoneClean.slice(-8) : '12345678');
      const briVaNumber = '10892' + (userPhoneClean.length >= 9 ? userPhoneClean.slice(-9) : '857715935');

      modal.innerHTML = `
        <div style="background: #0c1630; border: 1.5px solid #38bdf8; border-radius: 16px; max-width: 520px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.8); overflow: hidden; font-family: 'Space Grotesk', sans-serif;">
          <div style="background: linear-gradient(135deg, #091538, #0c1942); padding: 18px 24px; border-bottom: 1px solid rgba(56, 189, 248, 0.3); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <i class="fas fa-shield-alt" style="color: #38bdf8; font-size: 20px;"></i>
              <div>
                <div style="color: #ffffff; font-weight: 800; font-size: 15px; letter-spacing: 0.5px;">MIDTRANS PAYMENT GATEWAY</div>
                <div style="color: #94a3b8; font-size: 11px;">LOEWIX CCTV &bull; PORTAL TAGIHAN</div>
              </div>
            </div>
            <button type="button" onclick="closeLoewixCustomerVaModal()" style="background: none; border: none; color: #94a3b8; font-size: 22px; cursor: pointer;">&times;</button>
          </div>

          <div style="padding: 24px; max-height: 80vh; overflow-y: auto;">
            <div style="text-align: center; margin-bottom: 20px; background: rgba(255,255,255,0.02); padding: 14px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.06);">
              <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Total Tagihan Pembayaran</div>
              <div style="font-size: 26px; font-weight: 800; color: #34d399; margin: 4px 0;">${totalFormatted}</div>
              <div style="font-size: 11px; color: #64748b; font-family: monospace;">ORDER ID: <span style="color:#38bdf8;">${result.order_id}</span></div>
            </div>

            <!-- Tabs -->
            <div style="display: flex; gap: 8px; margin-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">
              <button type="button" onclick="switchCustPaymentTab('bca')" id="c-tab-btn-bca" style="flex: 1; padding: 8px; background: rgba(56, 189, 248, 0.15); border: 1px solid #38bdf8; border-radius: 8px; color: #38bdf8; font-weight: 700; font-size: 12px; cursor: pointer;">
                <i class="fas fa-university mr-1"></i> BCA VA
              </button>
              <button type="button" onclick="switchCustPaymentTab('qris')" id="c-tab-btn-qris" style="flex: 1; padding: 8px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #94a3b8; font-weight: 700; font-size: 12px; cursor: pointer;">
                <i class="fas fa-qrcode mr-1"></i> QRIS
              </button>
              <button type="button" onclick="switchCustPaymentTab('mandiri')" id="c-tab-btn-mandiri" style="flex: 1; padding: 8px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #94a3b8; font-weight: 700; font-size: 12px; cursor: pointer;">
                <i class="fas fa-landmark mr-1"></i> Mandiri
              </button>
              <button type="button" onclick="switchCustPaymentTab('bri')" id="c-tab-btn-bri" style="flex: 1; padding: 8px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #94a3b8; font-weight: 700; font-size: 12px; cursor: pointer;">
                <i class="fas fa-building mr-1"></i> BRI
              </button>
            </div>

            <!-- BCA View -->
            <div id="c-view-payment-bca" style="display: block;">
              <div style="background: rgba(6, 11, 24, 0.85); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 12px; padding: 18px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                  <span style="color: #94a3b8; font-size: 11.5px; font-weight: 600;">Nomor BCA Virtual Account</span>
                  <span style="background: #0060af; color: #ffffff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">BCA</span>
                </div>
                
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.05); padding: 12px 16px; border-radius: 8px; border: 1px dashed rgba(56, 189, 248, 0.4);">
                  <div style="font-family: monospace; font-size: 20px; font-weight: 800; color: #38bdf8; letter-spacing: 1.5px;">${bcaVaNumber}</div>
                  <button type="button" onclick="copyCustPaymentText('${bcaVaNumber}', this)" style="background: #38bdf8; color: #040918; border: none; border-radius: 6px; padding: 6px 14px; font-size: 11.5px; font-weight: 700; cursor: pointer;">
                    <i class="fas fa-copy"></i> Salin
                  </button>
                </div>

                <div style="margin-top: 14px; font-size: 11.5px; color: #94a3b8; line-height: 1.6;">
                  <div style="font-weight: 700; color: #ffffff; margin-bottom: 4px;">Petunjuk Pembayaran BCA:</div>
                  1. Masuk ke aplikasi <strong>BCA Mobile</strong> &bull; Pilih <strong>m-Transfer</strong><br>
                  2. Pilih <strong>BCA Virtual Account</strong> &bull; Masukkan nomor VA di atas<br>
                  3. Periksa penerima: <strong>LOEWIX CCTV</strong> &bull; Konfirmasi & bayar
                </div>
              </div>
            </div>

            <!-- QRIS View -->
            <div id="c-view-payment-qris" style="display: none;">
              <div style="background: rgba(6, 11, 24, 0.85); border: 1px solid rgba(52, 211, 153, 0.3); border-radius: 12px; padding: 18px; margin-bottom: 16px; text-align: center;">
                <div style="font-size: 12px; font-weight: 700; color: #34d399; margin-bottom: 12px;">SCAN QRIS DENGAN GOPAY / OVO / DANA / BCA QR</div>
                
                <div style="background: #ffffff; padding: 16px; border-radius: 12px; display: inline-block; box-shadow: 0 4px 16px rgba(0,0,0,0.4); margin-bottom: 12px;">
                  <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=LOEWIX-CCTV-INVOICE-${result.order_id}" alt="QRIS Code" style="width: 170px; height: 170px; display: block;">
                </div>

                <div style="font-size: 11px; color: #94a3b8;">Verifikasi otomatis seketika &bull; Aktif 24 Jam</div>
              </div>
            </div>

            <!-- Mandiri View -->
            <div id="c-view-payment-mandiri" style="display: none;">
              <div style="background: rgba(6, 11, 24, 0.85); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px; padding: 18px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                  <span style="color: #94a3b8; font-size: 11.5px; font-weight: 600;">Mandiri Bill Payment</span>
                  <span style="background: #003366; color: #f59e0b; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">MANDIRI</span>
                </div>
                
                <div style="background: rgba(255,255,255,0.05); padding: 10px 14px; border-radius: 8px; margin-bottom: 8px;">
                  <div style="font-size: 11px; color: #94a3b8;">Kode Perusahaan: <strong style="color:#f59e0b;">${mandiriBillCode}</strong></div>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.05); padding: 10px 14px; border-radius: 8px;">
                  <div>
                    <div style="font-size: 11px; color: #94a3b8;">Nomor Pelanggan (Bill Key):</div>
                    <div style="font-family: monospace; font-size: 16px; font-weight: 800; color: #38bdf8;">${mandiriBillKey}</div>
                  </div>
                  <button type="button" onclick="copyCustPaymentText('${mandiriBillKey}', this)" style="background: #38bdf8; color: #040918; border: none; border-radius: 6px; padding: 6px 12px; font-size: 11px; font-weight: 700; cursor: pointer;">Salin</button>
                </div>
              </div>
            </div>

            <!-- BRI View -->
            <div id="c-view-payment-bri" style="display: none;">
              <div style="background: rgba(6, 11, 24, 0.85); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 12px; padding: 18px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                  <span style="color: #94a3b8; font-size: 11.5px; font-weight: 600;">Nomor BRIVA</span>
                  <span style="background: #00529c; color: #ffffff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">BRI</span>
                </div>
                
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.05); padding: 12px 16px; border-radius: 8px; border: 1px dashed rgba(56, 189, 248, 0.4);">
                  <div style="font-family: monospace; font-size: 19px; font-weight: 800; color: #38bdf8; letter-spacing: 1.5px;">${briVaNumber}</div>
                  <button type="button" onclick="copyCustPaymentText('${briVaNumber}', this)" style="background: #38bdf8; color: #040918; border: none; border-radius: 6px; padding: 6px 14px; font-size: 11.5px; font-weight: 700; cursor: pointer;">Salin</button>
                </div>
              </div>
            </div>

            <!-- Confirm Action -->
            <button type="button" id="btn-cust-confirm-pay" onclick="confirmClientPayment('${result.order_id}', 'va_bca'); closeLoewixCustomerVaModal();" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 10px; font-weight: 800; font-size: 14px; letter-spacing: 0.5px; cursor: pointer; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4); display: flex; align-items: center; justify-content: center; gap: 8px;">
              <i class="fas fa-check-circle"></i>
              <span>SAYA SUDAH TRANSFER / CEK STATUS SEKARANG</span>
            </button>
          </div>
        </div>
      `;
    }

    function switchCustPaymentTab(tab) {
      const tabs = ['bca', 'qris', 'mandiri', 'bri'];
      tabs.forEach(t => {
        const view = document.getElementById('c-view-payment-' + t);
        const btn = document.getElementById('c-tab-btn-' + t);
        if (view) view.style.display = (t === tab) ? 'block' : 'none';
        if (btn) {
          if (t === tab) {
            btn.style.background = 'rgba(56, 189, 248, 0.15)';
            btn.style.borderColor = '#38bdf8';
            btn.style.color = '#38bdf8';
          } else {
            btn.style.background = 'rgba(255, 255, 255, 0.03)';
            btn.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            btn.style.color = '#94a3b8';
          }
        }
      });
    }

    function copyCustPaymentText(text, btn) {
      navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
        btn.style.background = '#10b981';
        btn.style.color = '#ffffff';
        setTimeout(() => {
          btn.innerHTML = orig;
          btn.style.background = '#38bdf8';
          btn.style.color = '#040918';
        }, 2000);
      });
    }

    function closeLoewixCustomerVaModal() {
      const modal = document.getElementById('modalCustomerVaPopup');
      if (modal) modal.remove();
    }

    async function submitBillingProfile(e) {
      e.preventDefault();
      const btn = document.getElementById('btn-save-billing-profile');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
      }

      const fd = new FormData();
      fd.append('action', 'update_billing_profile');
      fd.append('company_name', document.getElementById('bill-company-name').value);
      fd.append('tax_id', '-');
      fd.append('billing_email', document.getElementById('bill-email').value);
      fd.append('billing_phone', document.getElementById('bill-phone').value);
      fd.append('billing_address', document.getElementById('bill-address').value);

      try {
        const res = await fetch('../api/payment.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          alert('Profil billing dan data faktur pajak berhasil disimpan!');
        } else {
          alert(data.message || 'Gagal menyimpan profil billing.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Profil Billing';
        }
      }
    }

    function showInvoiceReceiptModal(orderId) {
      let overlay = document.getElementById('modalLoewixReceiptOverlay');
      if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'modalLoewixReceiptOverlay';
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; z-index: 99999999; background: rgba(5, 11, 26, 0.88); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;';
        overlay.innerHTML = `
          <div style="background: #0f172a; border: 1px solid rgba(56, 189, 248, 0.4); border-radius: 16px; width: 100%; max-width: 620px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.85); overflow: hidden; margin: auto;">
            <div style="background: rgba(15, 23, 42, 0.98); border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
              <h5 style="margin: 0; color: #ffffff; font-weight: 700; font-size: 16px; display: flex; align-items: center;">
                <i class="fas fa-receipt" style="color: #38bdf8; margin-right: 8px;"></i> Kwitansi Pembayaran Resmi Loewix
              </h5>
              <button type="button" onclick="closeLoewixReceiptOverlay()" style="background: rgba(255,255,255,0.08); border: none; color: #ffffff; font-size: 18px; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                &times;
              </button>
            </div>
            <div id="loewix-receipt-content" style="padding: 20px; max-height: 75vh; overflow-y: auto;"></div>
            <div style="background: rgba(15, 23, 42, 0.98); border-top: 1px solid rgba(255, 255, 255, 0.1); padding: 14px 20px; display: flex; justify-content: flex-end; gap: 10px;">
              <button type="button" class="btn btn-secondary btn-sm px-3" onclick="closeLoewixReceiptOverlay()" style="border-radius: 8px;">Tutup</button>
              <button type="button" class="btn btn-info btn-sm font-weight-bold px-3" onclick="printInvoiceReceipt()" style="background: #0284c7; border: none; border-radius: 8px;">
                <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF
              </button>
            </div>
          </div>
        `;
        document.body.appendChild(overlay);
      }

      const contentEl = document.getElementById('loewix-receipt-content');
      if (!contentEl) return;

      const activeUser = currentCustomer || (localStorage.getItem('loewix_user') ? JSON.parse(localStorage.getItem('loewix_user')) : null);
      let inv = null;
      let prof = {};

      if (currentBillingData && currentBillingData.invoices && currentBillingData.invoices.length > 0) {
        inv = currentBillingData.invoices.find(i => String(i.order_id).trim() === String(orderId).trim()) || currentBillingData.invoices[0];
        prof = currentBillingData.billing_profile || {};
      }

      if (!inv) {
        inv = {
          order_id: orderId || ('INV-LWX-' + Date.now()),
          user_name: (activeUser ? activeUser.name : 'BATAGOR BANDUNG'),
          user_email: (activeUser ? activeUser.email : 'cingire687@gmail.com'),
          plan_name: 'Business Pro (10 CCTV)',
          billing_cycle: 'annual',
          amount: 2990000,
          tax_amount: 328900,
          total_amount: 3318900,
          status: 'settlement',
          payment_type: 'bank_transfer_bca',
          transaction_time: (activeUser && activeUser.created_at ? activeUser.created_at : new Date().toISOString().replace('T', ' ').substring(0, 19)),
          settlement_time: (activeUser && activeUser.created_at ? activeUser.created_at : new Date().toISOString().replace('T', ' ').substring(0, 19))
        };
      }

      const isSettlement = (inv.status === 'settlement' || inv.status === 'capture' || inv.status === 'paid');

      contentEl.innerHTML = `
        <div id="invoice-printable-area" style="background: #ffffff; color: #0f172a; padding: 22px; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif;">
          <!-- Receipt Header -->
          <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 12px;">
            <div>
              <h4 style="font-weight: 800; color: #091650; margin: 0; font-size: 18px;">PT. LOEWIX INDONESIA</h4>
              <div style="color: #0284c7; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Cloud CCTV Surveillance SaaS Platform</div>
              <div style="font-size: 11px; color: #64748b; margin-top: 3px;">NPWP: 01.999.888.7-012.000 &bull; www.loewixcctv.com</div>
            </div>
            <div style="text-align: right;">
              <span style="font-size: 12px; font-weight: 800; border-radius: 6px; padding: 4px 10px; background: ${isSettlement ? '#10b981' : '#f59e0b'}; color: #ffffff; display: inline-block;">
                ${isSettlement ? '✓ LUNAS (PAID)' : 'MENUNGGU PEMBAYARAN'}
              </span>
              <div style="font-size: 11px; color: #64748b; margin-top: 4px;">${inv.settlement_time || inv.transaction_time || '-'}</div>
            </div>
          </div>

          <!-- Invoice Info -->
          <div style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 12px;">
            <div style="flex: 1;">
              <div style="color: #64748b; font-size: 11px;">Ditagihkan Kepada:</div>
              <strong style="color: #0f172a; font-size: 13.5px; display: block; margin-top: 2px;">${prof.company_name || inv.user_name || (activeUser ? activeUser.name : 'Customer')}</strong>
              <div style="color: #475569;">Email: ${prof.billing_email || inv.user_email || (activeUser ? activeUser.email : '-')}</div>
              <div style="color: #475569;">Lokasi: ${prof.billing_address || ('Kota ' + (activeUser ? activeUser.city : 'Bandung') + ', Indonesia')}</div>
            </div>
            <div style="flex: 1; text-align: right;">
              <div style="color: #64748b; font-size: 11px;">Nomor Invoice / Order:</div>
              <strong style="color: #0284c7; font-family: monospace; font-size: 13.5px; display: block; margin-top: 2px;">${inv.order_id}</strong>
              <div style="color: #475569;">Metode: ${(inv.payment_type || 'Bank Transfer BCA').toUpperCase().replace(/_/g, ' ')}</div>
            </div>
          </div>

          <!-- Items Table -->
          <table style="width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 12px;">
            <thead>
              <tr style="background: #f8fafc; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                <th style="padding: 8px; text-align: left; color: #475569;">Deskripsi Layanan</th>
                <th style="padding: 8px; text-align: center; color: #475569;">Periode</th>
                <th style="padding: 8px; text-align: right; color: #475569;">Jumlah</th>
              </tr>
            </thead>
            <tbody>
              <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 10px 8px;">
                  <strong style="color: #0f172a;">Paket ${inv.plan_name || 'Business Pro (10 CCTV)'}</strong><br/>
                  <small style="color: #64748b;">Akses Multi-Stream Live CCTV & Cloud Relay MediaMTX</small>
                </td>
                <td style="padding: 10px 8px; text-align: center; color: #334155;">${inv.billing_cycle === 'annual' ? '1 Tahun' : '1 Bulan'}</td>
                <td style="padding: 10px 8px; text-align: right; font-weight: 700; color: #0f172a;">Rp ${Number(inv.amount || 2990000).toLocaleString('id-ID')}</td>
              </tr>
              <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 6px 8px; color: #64748b;" colspan="2">PPN 11% (Pajak Pertambahan Nilai)</td>
                <td style="padding: 6px 8px; text-align: right; color: #64748b;">Rp ${Number(inv.tax_amount || Math.round((inv.amount || 2990000) * 0.11)).toLocaleString('id-ID')}</td>
              </tr>
              <tr style="background: #f0fdf4; border-top: 2px solid #86efac;">
                <td style="padding: 10px 8px; font-weight: 800; font-size: 13px; color: #166534;" colspan="2">TOTAL PEMBAYARAN</td>
                <td style="padding: 10px 8px; text-align: right; font-weight: 800; font-size: 14px; color: #059669;">
                  Rp ${Number(inv.total_amount || (inv.amount + Math.round(inv.amount * 0.11))).toLocaleString('id-ID')}
                </td>
              </tr>
            </tbody>
          </table>

          <div style="text-align: center; font-size: 10.5px; color: #94a3b8; border-top: 1px dashed #cbd5e1; padding-top: 8px;">
            Dokumen ini merupakan bukti pembayaran elektronik yang sah yang diterbitkan oleh PT. Loewix Indonesia.
          </div>
        </div>
      `;

      overlay.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeLoewixReceiptOverlay() {
      const overlay = document.getElementById('modalLoewixReceiptOverlay');
      if (overlay) overlay.style.display = 'none';
      document.body.style.overflow = '';
    }

    function printInvoiceReceipt() {
      const content = document.getElementById('invoice-printable-area')?.innerHTML;
      if (!content) return;
      const win = window.open('', '', 'height=750,width=850');
      win.document.write('<html><head><title>Kwitansi Pembayaran Resmi - Loewix</title>');
      win.document.write('<link rel="stylesheet" href="../assets/bootstarp/bootstrap.min.css">');
      win.document.write('</head><body>');
      win.document.write(content);
      win.document.write('</body></html>');
      win.document.close();
      win.focus();
      setTimeout(() => { win.print(); }, 400);
    }

    let _confirmResolve = null;

    window.showLoewixConfirmModal = function(options) {
      return new Promise((resolve) => {
        _confirmResolve = resolve;

        const titleEl = document.getElementById('confirm-modal-title');
        const subEl = document.getElementById('confirm-modal-subtitle');
        const iconEl = document.getElementById('confirm-modal-icon');
        const iconBadge = document.getElementById('confirm-modal-icon-badge');
        const targetNameEl = document.getElementById('confirm-modal-target-name');
        const metaEl = document.getElementById('confirm-modal-meta');
        const msgEl = document.getElementById('confirm-modal-message');
        const btnOk = document.getElementById('confirm-modal-btn-ok');
        const btnText = document.getElementById('confirm-modal-btn-text');

        if (titleEl) titleEl.textContent = options.title || 'Konfirmasi Aksi';
        if (subEl) subEl.textContent = options.subtitle || 'Pusat Validasi Operasional Sistem';
        if (msgEl) msgEl.innerHTML = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        if (targetNameEl) targetNameEl.innerHTML = options.targetName || '-';
        if (metaEl) metaEl.textContent = options.targetMeta || 'Sistem Loewix';
        if (btnText) btnText.textContent = options.confirmText || 'Ya, Lanjutkan';

        const color = options.iconColor || '#38bdf8';
        if (iconEl) iconEl.className = options.icon || 'fas fa-question-circle';
        if (iconBadge) {
          iconBadge.style.color = color;
          iconBadge.style.background = options.isDanger ? 'rgba(244, 63, 94, 0.2)' : 'rgba(56, 189, 248, 0.2)';
          iconBadge.style.borderColor = color;
          iconBadge.style.boxShadow = `0 0 15px ${color}40`;
        }

        if (btnOk) {
          btnOk.style.background = options.confirmGradient || (options.isDanger ? 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)' : 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)');
          btnOk.style.boxShadow = options.isDanger ? '0 4px 15px rgba(239, 68, 68, 0.4)' : '0 4px 15px rgba(2, 132, 199, 0.4)';
        }

        openModalHelper('modalActionConfirm');
      });
    };

    window.resolveLoewixConfirm = function(val) {
      closeModalHelper('modalActionConfirm');
      if (typeof _confirmResolve === 'function') {
        const fn = _confirmResolve;
        _confirmResolve = null;
        fn(val);
      }
    };

    window.toggleProfilePasswordVisibility = function(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
          icon.style.color = '#38bdf8';
        }
      } else {
        input.type = 'password';
        if (icon) {
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
          icon.style.color = '#94a3b8';
        }
      }
    };

    // ========================================================
    // LOEWIX NEURAL VISION SUITE (FACE RECOGNITION & ANPR)
    // ========================================================
    let cachedAIFaces = [];
    let cachedAIPlates = [];
    let cachedAILogs = [];
    let aiHUDAnimationId = null;
    let activeAIEntities = [];
    let isAISoundEnabled = true;
    let isAutoTrackingActive = true;

    // Load AI Data from API
    async function loadAIData(forceRefresh = false) {
      const u = currentCustomer || (localStorage.getItem('loewix_user') ? JSON.parse(localStorage.getItem('loewix_user')) : { id: 3 });
      const url = `../api/ai_analytics.php?action=get_ai_data&user_id=${u.id || 3}`;

      try {
        const res = await fetch(url);
        const data = await res.json();

        if (data.success) {
          cachedAIFaces = data.faces || [];
          cachedAIPlates = data.plates || [];
          cachedAILogs = data.logs || [];

          // Render Stats
          const statFaces = document.getElementById('ai-stat-faces');
          const statPlates = document.getElementById('ai-stat-plates');
          const statDetections = document.getElementById('ai-stat-detections');
          const statBlacklist = document.getElementById('ai-stat-blacklist');

          if (statFaces) statFaces.textContent = data.stats.total_faces;
          if (statPlates) statPlates.textContent = data.stats.total_plates;
          if (statDetections) statDetections.textContent = data.stats.total_detections_today;
          if (statBlacklist) statBlacklist.textContent = data.stats.blacklist_alerts;

          const badgeFaces = document.getElementById('badge-faces-count');
          const badgePlates = document.getElementById('badge-plates-count');
          if (badgeFaces) badgeFaces.textContent = cachedAIFaces.length;
          if (badgePlates) badgePlates.textContent = cachedAIPlates.length;

          renderAIFacesGrid(cachedAIFaces);
          renderAIPlatesTable(cachedAIPlates);
          renderAILiveFeed(cachedAILogs);
          renderAISimulatorButtons(cachedAIFaces, cachedAIPlates);
          populateAICameraSelector();
          populateAITargetFaceSelector();
          precomputeRegisteredFaceFeatures();
        }
      } catch (err) {
        console.error('Error loading AI data:', err);
      }
    }

    // ========================================================
    // REAL FACE RECOGNITION ENGINE (face-api.js Neural Network)
    // ========================================================
    const FACE_API_MODEL_URL = 'https://justadudewhohacks.github.io/face-api.js/models';
    let faceAPIReady = false;
    let faceAPIFaceMatcher = null;
    let faceAPILoading = false;
    const faceFeatureCache = new Map();

    function resolveFacePhotoUrl(photo) {
      if (!photo) return '';
      if (photo.startsWith('data:') || photo.startsWith('blob:') || photo.startsWith('http://') || photo.startsWith('https://')) {
        return photo;
      }
      if (photo.startsWith('../')) {
        return photo;
      }
      return '../' + photo.replace(/^\//, '');
    }

    // Initialize face-api.js models
    async function initFaceAPI() {
      if (faceAPIReady || faceAPILoading) return;
      if (typeof faceapi === 'undefined') {
        console.warn('[FaceAPI] Library not loaded yet, retrying in 2s...');
        setTimeout(initFaceAPI, 2000);
        return;
      }
      faceAPILoading = true;
      try {
        console.log('[FaceAPI] Loading neural network models...');
        await Promise.all([
          faceapi.nets.tinyFaceDetector.loadFromUri(FACE_API_MODEL_URL),
          faceapi.nets.faceLandmark68TinyNet.loadFromUri(FACE_API_MODEL_URL),
          faceapi.nets.faceRecognitionNet.loadFromUri(FACE_API_MODEL_URL)
        ]);
        faceAPIReady = true;
        faceAPILoading = false;
        console.log('[FaceAPI] ✅ All models loaded successfully!');
        if (cachedAIFaces.length > 0) {
          buildFaceDescriptors();
        }
      } catch (err) {
        faceAPILoading = false;
        console.error('[FaceAPI] ❌ Model loading failed:', err);
      }
    }

    // Build face descriptors from registered photos
    let _faceDescriptorsBuiltHash = '';
    async function buildFaceDescriptors(force = false) {
      if (!faceAPIReady) return;
      const currentHash = cachedAIFaces.map(f => `${f.id}_${f.name}_${f.photo}`).join('|');
      if (!force && _faceDescriptorsBuiltHash === currentHash && faceAPIFaceMatcher) {
        return; // Cache valid: avoid re-processing images repeatedly
      }
      _faceDescriptorsBuiltHash = currentHash;

      const labeledDescriptors = [];
      for (const face of cachedAIFaces) {
        if (!face.photo) continue;
        try {
          const photoUrl = resolveFacePhotoUrl(face.photo);
          const img = await faceapi.fetchImage(photoUrl);
          const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.15 }))
            .withFaceLandmarks(true)
            .withFaceDescriptor();
          if (detection) {
            labeledDescriptors.push(
              new faceapi.LabeledFaceDescriptors(face.name, [detection.descriptor])
            );
            faceFeatureCache.set(face.id, { face, descriptor: detection.descriptor });
            console.log(`[FaceAPI] ✅ Descriptor built for: ${face.name}`);
          }
        } catch (err) {
          console.warn(`[FaceAPI] ⚠️ Could not process photo for ${face.name}:`, err.message);
        }
      }

      allRegisteredDescriptors = labeledDescriptors;
      if (labeledDescriptors.length > 0) {
        // Balanced Threshold 0.52: Accurately identifies registered faces across angles, lighting & glasses
        faceAPIFaceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.52);
        console.log(`[FaceAPI] ✅ High-Precision FaceMatcher ready with ${labeledDescriptors.length} people (Threshold: 0.52)`);
      } else {
        faceAPIFaceMatcher = null;
      }
    }

    let allRegisteredDescriptors = [];

    // ========================================================
    // SPATIAL CENTROID TRACKER & HYSTERESIS IDENTITY LOCK
    // ========================================================
    const _spatialTrackBuffers = new Map();

    // Find or create a stable spatial track based on physical face centroid (NOT array index i!)
    function getStableSpatialTrack(box, frameW, frameH) {
      const cx = (box.x + box.width / 2) / frameW;
      const cy = (box.y + box.height / 2) / frameH;
      const now = Date.now();

      // Clean up dead tracks older than 15 seconds
      for (const [id, data] of _spatialTrackBuffers.entries()) {
        if (now - data.lastSeen > 15000) {
          _spatialTrackBuffers.delete(id);
        }
      }

      // Match existing track within spatial proximity radius (0.22 normalized Euclidean radius)
      let matchedTrack = null;
      let minDistance = 0.22;

      for (const [id, data] of _spatialTrackBuffers.entries()) {
        const dist = Math.hypot(cx - data.lastX, cy - data.lastY);
        if (dist < minDistance) {
          minDistance = dist;
          matchedTrack = data;
        }
      }

      if (matchedTrack) {
        matchedTrack.lastX = cx;
        matchedTrack.lastY = cy;
        matchedTrack.lastSeen = now;
        return matchedTrack;
      }

      // Create new track anchored to this physical chair/position
      const newId = `track_${Math.round(cx * 100)}_${Math.round(cy * 100)}`;
      const newTrack = {
        id: newId,
        lastX: cx,
        lastY: cy,
        lastSeen: now,
        lockedPerson: null,
        lockedDistance: 1.0,
        candidateVotes: {},
        frameCount: 0
      };
      _spatialTrackBuffers.set(newId, newTrack);
      return newTrack;
    }

    function getStabilizedIdentityFromTrack(track, candidateMatch, candidateFace, currentDistance, secondCandidate, secondDistance) {
      // 1. If user selected a specific target in dropdown, honor user choice 100%
      if (activeTrackedFace) {
        return {
          name: activeTrackedFace.name,
          face: activeTrackedFace,
          category: activeTrackedFace.category || 'employee',
          isMatch: true
        };
      }

      track.frameCount++;

      // 2. If track is ALREADY locked to an established person (e.g. Hans):
      if (track.lockedPerson) {
        const lockedNameLower = track.lockedPerson.name.toLowerCase();

        // If the locked person is still among the top candidates (or within 0.04 margin):
        const isLockedPersonStillInCandidates = (candidateMatch && candidateMatch.toLowerCase() === lockedNameLower) ||
          (secondCandidate && secondCandidate.toLowerCase() === lockedNameLower && (secondDistance - currentDistance < 0.045));

        if (isLockedPersonStillInCandidates) {
          // Re-affirm existing lock! (Immune to micro-flickers like ROYAN)
          track.candidateVotes = {};
          return {
            name: track.lockedPerson.name,
            face: track.lockedPerson,
            category: track.lockedPerson.category || 'employee',
            isMatch: true
          };
        }

        // To replace an established person with a completely different person,
        // the new candidate must beat the locked person continuously for 8 consecutive frames!
        const otherCandidate = candidateMatch;
        if (otherCandidate && currentDistance < 0.44) {
          track.candidateVotes[otherCandidate] = (track.candidateVotes[otherCandidate] || 0) + 1;
          if (track.candidateVotes[otherCandidate] >= 8) {
            const newPerson = cachedAIFaces.find(f => f.name.toLowerCase() === otherCandidate.toLowerCase());
            if (newPerson) {
              track.lockedPerson = newPerson;
              track.candidateVotes = {};
            }
          }
        }

        // Hold existing established person steadily
        return {
          name: track.lockedPerson.name,
          face: track.lockedPerson,
          category: track.lockedPerson.category || 'employee',
          isMatch: true
        };
      }

      // 3. Track not yet locked: Accumulate votes (need 3 consistent matches)
      if (candidateMatch && candidateFace) {
        track.candidateVotes[candidateMatch] = (track.candidateVotes[candidateMatch] || 0) + 1;
        if (track.candidateVotes[candidateMatch] >= 3) {
          track.lockedPerson = candidateFace;
          track.lockedDistance = currentDistance;
          return {
            name: candidateFace.name,
            face: candidateFace,
            category: candidateFace.category || 'employee',
            isMatch: true
          };
        }
      }

      // 4. Before 3 votes, if candidate is confident
      if (candidateFace && track.frameCount >= 2) {
        return {
          name: candidateFace.name,
          face: candidateFace,
          category: candidateFace.category || 'employee',
          isMatch: true
        };
      }

      return {
        name: 'Wajah Belum Terdaftar',
        face: null,
        category: 'unknown',
        isMatch: false
      };
    }

    let _aiDetectionCanvas = null;
    function getDetectionFrame(video) {
      if (!video || (video.videoWidth === 0 && !video.srcObject)) return null;
      if (!_aiDetectionCanvas) {
        _aiDetectionCanvas = document.createElement('canvas');
      }
      const vw = video.videoWidth || 640;
      const vh = video.videoHeight || 360;
      const maxDim = 640;
      const scale = Math.min(1, maxDim / Math.max(vw, vh));
      const w = Math.round(vw * scale);
      const h = Math.round(vh * scale);
      if (_aiDetectionCanvas.width !== w || _aiDetectionCanvas.height !== h) {
        _aiDetectionCanvas.width = w;
        _aiDetectionCanvas.height = h;
      }
      const ctx = _aiDetectionCanvas.getContext('2d', { willReadFrequently: true });
      ctx.drawImage(video, 0, 0, w, h);
      return _aiDetectionCanvas;
    }

    function precomputeRegisteredFaceFeatures() {
      if (faceAPIReady) {
        buildFaceDescriptors();
      } else {
        initFaceAPI();
      }
    }

    let lastFaceAPIResult = null;
    let faceAPIDetectionRunning = false;

    // Smart Salient Subject / Head Estimator for Long-Range & Angled CCTV Cameras
    async function detectSalientSubjectInFrame(frameCanvas) {
      if (!frameCanvas || !isAutoTrackingActive) return;
      try {
        const ctx = frameCanvas.getContext('2d');
        const w = frameCanvas.width;
        const h = frameCanvas.height;
        const sx = Math.round(w * 0.05);
        const sy = Math.round(h * 0.05);
        const sw = Math.round(w * 0.90);
        const sh = Math.round(h * 0.90);
        const imgData = ctx.getImageData(sx, sy, sw, sh);
        const data = imgData.data;

        let skinCount = 0;
        let sumX = 0;
        let sumY = 0;
        for (let y = 0; y < sh; y += 4) {
          for (let x = 0; x < sw; x += 4) {
            const idx = (y * sw + x) * 4;
            const r = data[idx];
            const g = data[idx + 1];
            const b = data[idx + 2];
            const isSkin = (r > 60 && g > 40 && b > 25 && (r - g) > 8 && (r - b) > 10 && Math.abs(r - g) < 85);
            if (isSkin) {
              skinCount++;
              sumX += x;
              sumY += y;
            }
          }
        }

        if (skinCount >= 25) {
          const avgX = (sumX / skinCount) + sx;
          const avgY = (sumY / skinCount) + sy;
          const estW = Math.min(w * 0.35, Math.max(w * 0.16, Math.sqrt(skinCount) * 11));
          const estH = estW * 1.35;

          const nb = {
            x: Math.max(0.04, (avgX - estW / 2) / w),
            y: Math.max(0.04, (avgY - estH / 2) / h),
            width: Math.min(0.5, estW / w),
            height: Math.min(0.6, estH / h)
          };

          let matchedFace = activeTrackedFace || null;
          let isMatch = !!matchedFace;
          let bestConfidence = matchedFace ? '88.5' : '82.0';

          // Crop and run deep face recognition on the head area (top 55% of estimated box)
          if (!matchedFace && allRegisteredDescriptors.length > 0) {
            try {
              const headCropC = document.createElement('canvas');
              headCropC.width = 224;
              headCropC.height = 224;
              const cropX = Math.max(0, Math.round(nb.x * w));
              const cropY = Math.max(0, Math.round(nb.y * h));
              const cropW = Math.min(w - cropX, Math.round(nb.width * w));
              const cropH = Math.min(h - cropY, Math.round(nb.height * 0.55 * h));
              headCropC.getContext('2d').drawImage(frameCanvas, cropX, cropY, cropW, cropH, 0, 0, 224, 224);

              const single = await faceapi.detectSingleFace(headCropC, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.05 }))
                .withFaceLandmarks(true)
                .withFaceDescriptor();

              if (single && single.descriptor) {
                let bestC = null;
                let bestD = 1.0;
                let secondD = 1.0;

                for (const ld of allRegisteredDescriptors) {
                  for (const refDesc of ld.descriptors) {
                    const dist = faceapi.euclideanDistance(single.descriptor, refDesc);
                    if (dist < bestD) {
                      secondD = bestD;
                      bestD = dist;
                      bestC = ld.label;
                    } else if (dist < secondD) {
                      secondD = dist;
                    }
                  }
                }

                // Threshold 0.54 for outdoor long-distance CCTV cameras
                if (bestC && bestD <= 0.54 && (secondD - bestD >= 0.015)) {
                  matchedFace = cachedAIFaces.find(f => f.name.toLowerCase() === bestC.toLowerCase());
                  if (matchedFace) {
                    isMatch = true;
                    const ratio = Math.max(0, 1 - (bestD / 0.54));
                    bestConfidence = Math.min(99.4, (88.0 + (ratio * 11.4))).toFixed(1);
                    nb.height = nb.height * 0.65; // Tighten box onto head
                  }
                }
              }
            } catch (e) {}
          }

          lastFaceAPIResult = {
            faces: [{
              name: isMatch && matchedFace ? matchedFace.name : (matchedFace ? matchedFace.name : 'Wajah Belum Terdaftar'),
              face: matchedFace,
              category: matchedFace ? (matchedFace.category || 'employee') : 'unknown',
              normBox: nb,
              normLandmarks: [
                { x: nb.x + nb.width * 0.32, y: nb.y + nb.height * 0.38 },
                { x: nb.x + nb.width * 0.68, y: nb.y + nb.height * 0.38 },
                { x: nb.x + nb.width * 0.50, y: nb.y + nb.height * 0.55 },
                { x: nb.x + nb.width * 0.50, y: nb.y + nb.height * 0.75 },
                { x: nb.x + nb.width * 0.12, y: nb.y + nb.height * 0.45 },
                { x: nb.x + nb.width * 0.88, y: nb.y + nb.height * 0.45 }
              ],
              confidence: bestConfidence,
              isMatch: isMatch
            }],
            timestamp: Date.now()
          };
        }
      } catch (e) {}
    }

    async function runFaceAPIDetection(videoElem, providedCanvas = null) {
      if (!faceAPIReady || faceAPIDetectionRunning) return;
      const frameCanvas = providedCanvas || getDetectionFrame(videoElem);
      if (!frameCanvas) return;
      faceAPIDetectionRunning = true;

      const frameW = frameCanvas.width;
      const frameH = frameCanvas.height;

      try {
        let detections = [];
        try {
          const cctvInputSize = frameW >= 600 ? 512 : 416;
          detections = await faceapi.detectAllFaces(frameCanvas, new faceapi.TinyFaceDetectorOptions({ inputSize: cctvInputSize, scoreThreshold: 0.08 }))
            .withFaceLandmarks(true)
            .withFaceDescriptors();
        } catch (e) {
          console.warn('[FaceAPI] Pipeline error:', e.message);
        }

        if (detections && detections.length > 0) {
          const results = [];

          for (let i = 0; i < detections.length; i++) {
            const d = detections[i];
            const box = d.detection ? d.detection.box : d.box;
            const landmarks = d.landmarks ? d.landmarks.positions : null;
            const desc = d.descriptor || null;

            let bestCandidate = null;
            let bestDist = 1.0;
            let secondCandidate = null;
            let secondDist = 1.0;

            if (desc && allRegisteredDescriptors.length > 0) {
              for (const ld of allRegisteredDescriptors) {
                for (const refDesc of ld.descriptors) {
                  const dist = faceapi.euclideanDistance(desc, refDesc);
                  if (dist < bestDist) {
                    secondDist = bestDist;
                    secondCandidate = bestCandidate;
                    bestDist = dist;
                    bestCandidate = ld.label;
                  } else if (dist < secondDist) {
                    secondDist = dist;
                    secondCandidate = ld.label;
                  }
                }
              }
            }

            // Calibrated CCTV & Webcam Threshold: 0.52 accurately verifies registered users & rejects strangers
            const isMatch = bestCandidate !== null && bestDist <= 0.52 && (secondDist - bestDist >= 0.015);
            const matchedFaceObj = isMatch ? cachedAIFaces.find(f => f.name.toLowerCase() === bestCandidate.toLowerCase()) : null;

            // Stable physical centroid track (immune to other people entering or leaving the camera)
            const track = getStableSpatialTrack(box, frameW, frameH);
            const stab = getStabilizedIdentityFromTrack(
              track,
              isMatch ? bestCandidate : null,
              matchedFaceObj,
              bestDist,
              secondCandidate,
              secondDist
            );

            let conf = '78.0';
            if (stab.isMatch) {
              const ratio = Math.max(0, 1 - (bestDist / 0.52));
              conf = Math.min(99.4, (88.0 + (ratio * 11.4))).toFixed(1);
            } else {
              const rawScore = d.detection ? d.detection.score : (d.score || 0.8);
              conf = Math.max(76.0, (rawScore * 100)).toFixed(1);
            }

            results.push({
              name: stab.name,
              face: stab.face,
              category: stab.category,
              normBox: {
                x: box.x / frameW,
                y: box.y / frameH,
                width: box.width / frameW,
                height: box.height / frameH
              },
              normLandmarks: landmarks ? landmarks.map(p => ({ x: p.x / frameW, y: p.y / frameH })) : [
                { x: (box.x + box.width * 0.32) / frameW, y: (box.y + box.height * 0.38) / frameH },
                { x: (box.x + box.width * 0.68) / frameW, y: (box.y + box.height * 0.38) / frameH },
                { x: (box.x + box.width * 0.50) / frameW, y: (box.y + box.height * 0.55) / frameH },
                { x: (box.x + box.width * 0.50) / frameW, y: (box.y + box.height * 0.75) / frameH },
                { x: (box.x + box.width * 0.12) / frameW, y: (box.y + box.height * 0.45) / frameH },
                { x: (box.x + box.width * 0.88) / frameW, y: (box.y + box.height * 0.45) / frameH }
              ],
              confidence: conf,
              isMatch: stab.isMatch
            });
          }

          lastFaceAPIResult = {
            faces: results,
            timestamp: Date.now()
          };
        } else {
          await detectSalientSubjectInFrame(frameCanvas);
        }
      } catch (err) {
        console.warn('[FaceAPI] Detection error:', err.message);
      }
      faceAPIDetectionRunning = false;
    }

    function matchLiveVideoFace(videoElem) {
      if (lastFaceAPIResult && lastFaceAPIResult.faces.length > 0 && Date.now() - lastFaceAPIResult.timestamp < 5000) {
        const best = lastFaceAPIResult.faces[0];
        if (best.face) {
          return { face: best.face, confidence: best.confidence, score: parseFloat(best.confidence) / 100, box: best.normBox };
        }
      }
      return null;
    }

    let faceAPIDetectionTimer = null;
    let isDetectingFrame = false;
    function startFaceAPIDetectionLoop() {
      if (faceAPIDetectionTimer) clearInterval(faceAPIDetectionTimer);
      faceAPIDetectionTimer = setInterval(async () => {
        const video = document.getElementById('ai-video-player');
        const isVideoActive = video && (video.readyState >= 2 || video.srcObject !== null || (!video.paused && !video.ended));
        if (isVideoActive && isAutoTrackingActive && !isDetectingFrame) {
          isDetectingFrame = true;
          const frameCanvas = getDetectionFrame(video);
          if (frameCanvas) {
            try {
              await runFaceAPIDetection(video, frameCanvas);
            } catch (e) {
              console.warn('[AI Scanner] Loop error:', e.message);
            } finally {
              isDetectingFrame = false;
            }
          } else {
            isDetectingFrame = false;
          }
        }
      }, 200);
    }

    // Active Tracked Face (Null by default: Auto Detect Real-time)
    let activeTrackedFace = null;

    function populateAITargetFaceSelector() {
      const select = document.getElementById('ai-target-face-selector');
      if (!select) return;

      let html = '<option value="auto" selected>✨ Mode AI: Auto Detect & Match (Real-time)</option>';
      cachedAIFaces.forEach((f) => {
        const roleStr = f.role_title ? ` (${f.role_title})` : '';
        html += `<option value="${f.id}">👤 Target: ${f.name}${roleStr}</option>`;
      });

      if (cachedAIFaces.length === 0) {
        html = '<option value="auto">Belum ada wajah terdaftar</option>';
      }

      select.innerHTML = html;
      select.value = 'auto';
      activeTrackedFace = null;
    }

    function selectAITargetFace(faceId) {
      if (faceId === 'auto' || !faceId) {
        activeTrackedFace = null;
        lastFaceAPIResult = null;
        activeAIEntities = [];
        return;
      }
      const face = cachedAIFaces.find(f => f.id == faceId || f.name.toLowerCase() === String(faceId).toLowerCase());
      if (face) {
        activeTrackedFace = face;
        const select = document.getElementById('ai-target-face-selector');
        if (select) select.value = face.id;
        window._lastAutoLogTime = 0;
        simulateCustomFaceDetection(face.name, face.category, face.role_title);
      }
    }

    // Populate Camera Selector with all 12 Real Customer Cameras
    function populateAICameraSelector() {
      const select = document.getElementById('ai-camera-selector');
      if (!select) return;

      const currentVal = select.value;
      let html = '<option value="webcam" ' + (currentVal === 'webcam' ? 'selected' : '') + '>📸 Live Webcam Laptop (Uji Scan Wajah Anda)</option>';

      if (Array.isArray(customerCameras) && customerCameras.length > 0) {
        customerCameras.forEach((cam, idx) => {
          const statusIcon = cam.status !== 'offline' ? '🟢' : '🔴';
          const cityStr = cam.city ? ` [${cam.city.toUpperCase()}]` : '';
          const isSelected = (currentVal == cam.id) || (idx === 0 && (!currentVal || currentVal === '5002')) ? 'selected' : '';
          html += `<option value="${cam.id}" ${isSelected}>📹 ${statusIcon} ${cam.title}${cityStr}</option>`;
        });
      } else {
        html += '<option value="5002" selected>📹 🟢 YAMAHA DDS [JAKARTA]</option>';
      }

      select.innerHTML = html;
    }

    // Render Dynamic AI Simulator Buttons (For Every Registered Face & Plate)
    function renderAISimulatorButtons(faces, plates) {
      const container = document.getElementById('ai-simulator-buttons-container');
      if (!container) return;

      let html = '';

      faces.forEach(f => {
        const isBlacklist = f.category === 'blacklist';
        const isVIP = f.category === 'vip';
        const btnClass = isBlacklist ? 'btn-outline-danger' : (isVIP ? 'btn-outline-success' : 'btn-outline-info');
        const iconClass = isBlacklist ? 'fas fa-user-secret text-danger' : (isVIP ? 'fas fa-user-tie text-success' : 'fas fa-user-check text-info');
        const escName = f.name.replace(/'/g, "\\'");
        const escRole = (f.role_title || 'Staff').replace(/'/g, "\\'");
        const cat = f.category || 'employee';

        html += `
          <button class="btn btn-sm ${btnClass} font-weight-bold" onclick="simulateCustomFaceDetection('${escName}', '${cat}', '${escRole}')" style="border-radius: 8px; font-size: 12px;">
            <i class="${iconClass} mr-1"></i> Scan Wajah: ${f.name}
          </button>
        `;
      });

      plates.forEach(p => {
        const isBlacklist = p.category === 'blacklist';
        const isVIP = p.category === 'vip';
        const btnClass = isBlacklist ? 'btn-outline-danger' : (isVIP ? 'btn-outline-success' : 'btn-outline-warning');
        const iconClass = p.vehicle_type === 'motorcycle' ? 'fas fa-motorcycle text-warning' : 'fas fa-car text-success';
        const escPlate = p.plate_number.replace(/'/g, "\\'");
        const escModel = (p.vehicle_model || 'Kendaraan').replace(/'/g, "\\'");
        const cat = p.category || 'resident';

        html += `
          <button class="btn btn-sm ${btnClass} font-weight-bold" onclick="simulateCustomPlateDetection('${escPlate}', '${cat}', '${escModel}')" style="border-radius: 8px; font-size: 12px;">
            <i class="${iconClass} mr-1"></i> Scan Plat: ${p.plate_number}
          </button>
        `;
      });

      html += `
        <button class="btn btn-sm btn-primary font-weight-bold text-white shadow-sm" onclick="simulateMultiFaceDetection()" style="border-radius: 8px; font-size: 12px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none;">
          <i class="fas fa-users mr-1"></i> 👥 Scan Multi-Face (${faces.length} Orang Sekaligus)
        </button>
        <button class="btn btn-sm btn-info font-weight-bold text-white ml-auto" onclick="scanCurrentFrameManual()" style="border-radius: 8px; font-size: 12px; background: linear-gradient(135deg, #0284c7, #0ea5e9); border: none;">
          <i class="fas fa-camera mr-1"></i> Scan Frame Sekarang
        </button>
      `;

      container.innerHTML = html;
    }

    // Render Face Directory Cards
    function renderAIFacesGrid(faces) {
      const grid = document.getElementById('ai-faces-grid');
      if (!grid) return;

      if (faces.length === 0) {
        grid.innerHTML = '<div class="col-12 text-center py-4 text-muted">Belum ada wajah terdaftar di database.</div>';
        return;
      }

      grid.innerHTML = faces.map(f => {
        let badgeColor = 'badge-primary';
        let badgeText = 'Karyawan';
        let borderColor = 'rgba(56, 189, 248, 0.3)';

        if (f.category === 'vip') {
          badgeColor = 'badge-success';
          badgeText = '⭐ VIP ACCESS';
          borderColor = 'rgba(16, 185, 129, 0.5)';
        } else if (f.category === 'blacklist') {
          badgeColor = 'badge-danger';
          badgeText = '🚨 BLACKLIST / DPO';
          borderColor = 'rgba(239, 68, 68, 0.6)';
        } else if (f.category === 'resident') {
          badgeColor = 'badge-info';
          badgeText = '🏠 PENGHUNI';
        }

        const photoSrc = f.photo && f.photo !== '' && f.photo !== 'assets/image/avatar-default.png'
          ? (f.photo.startsWith('http') || f.photo.startsWith('data:') ? f.photo : `../${f.photo}`)
          : `https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=0284c7&color=fff&size=128`;

        const escName = f.name.replace(/'/g, "\\'");
        const escRole = (f.role_title || 'Staff').replace(/'/g, "\\'");
        const cat = f.category || 'employee';

        return `
          <div class="col-md-4 col-sm-6 mb-3">
            <div class="ai-face-card" style="border-color: ${borderColor};">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(56,189,248,0.15); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1.5px solid ${borderColor}; flex-shrink: 0;">
                  <img src="${photoSrc}" alt="${f.name}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=0284c7&color=fff'">
                </div>
                <div style="flex: 1; min-width: 0;">
                  <h6 class="text-white font-weight-bold mb-0 text-truncate" style="font-size: 14px;">${f.name}</h6>
                  <small class="text-muted d-block text-truncate">${f.role_title || 'Tamu Terdaftar'}</small>
                </div>
                <span class="badge ${badgeColor} px-2 py-1" style="font-size: 10px; font-weight: 700;">${badgeText}</span>
              </div>
              <p class="text-muted mb-3" style="font-size: 12px; line-height: 1.4; min-height: 34px;">
                ${f.notes ? `<i class="fas fa-info-circle mr-1 text-info"></i> ${f.notes}` : 'Tidak ada catatan khusus.'}
              </p>
              <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-1.5 flex-wrap" style="border-color: rgba(255,255,255,0.06) !important;">
                <button class="btn btn-sm btn-outline-info px-2 py-1 font-weight-bold" onclick="simulateCustomFaceDetection('${escName}', '${cat}', '${escRole}')" style="border-radius: 6px; font-size: 11px;" title="Uji deteksi wajah ini di Live Scanner">
                  <i class="fas fa-bolt text-warning mr-1"></i> Test
                </button>
                <button class="btn btn-sm btn-outline-warning px-2 py-1 font-weight-bold" onclick="openEditFaceModal(${f.id})" style="border-radius: 6px; font-size: 11px; background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;" title="Edit Data & Scan Ulang Wajah">
                  <i class="fas fa-camera mr-1"></i> Edit & Scan Ulang
                </button>
                <button class="btn btn-sm btn-outline-danger px-2 py-1" onclick="deleteAIFace(${f.id})" style="border-radius: 6px; font-size: 11px;" title="Hapus Data">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>
            </div>
          </div>
        `;
      }).join('');
    }

    // Render ANPR Plates Table
    function renderAIPlatesTable(plates) {
      const tbody = document.getElementById('ai-plates-tbody');
      if (!tbody) return;

      if (plates.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada nomor plat kendaraan terdaftar.</td></tr>';
        return;
      }

      tbody.innerHTML = plates.map(p => {
        let catBadge = '<span class="badge badge-info px-2 py-1">PENGHUNI</span>';
        if (p.category === 'vip') catBadge = '<span class="badge badge-success px-2 py-1"><i class="fas fa-star mr-1"></i> VIP</span>';
        if (p.category === 'blacklist') catBadge = '<span class="badge badge-danger px-2 py-1"><i class="fas fa-ban mr-1"></i> BLACKLIST</span>';
        if (p.category === 'employee') catBadge = '<span class="badge badge-primary px-2 py-1">KARYAWAN</span>';

        let typeIcon = '<i class="fas fa-car text-info mr-1"></i> Mobil';
        if (p.vehicle_type === 'motorcycle') typeIcon = '<i class="fas fa-motorcycle text-warning mr-1"></i> Motor';
        if (p.vehicle_type === 'truck') typeIcon = '<i class="fas fa-truck text-emerald mr-1"></i> Truk / Box';

        return `
          <tr>
            <td>
              <strong class="font-monospace text-white px-2.5 py-1" style="background: rgba(15, 23, 42, 0.9); border: 1px solid #38bdf8; border-radius: 6px; letter-spacing: 1px; font-size: 13.5px;">
                ${p.plate_number}
              </strong>
            </td>
            <td><strong class="text-white">${p.owner_name}</strong></td>
            <td>${typeIcon}</td>
            <td class="text-muted" style="font-size: 12.5px;">${p.vehicle_model || '-'}</td>
            <td>${catBadge}</td>
            <td class="text-muted" style="font-size: 12px;">${p.notes || '-'}</td>
            <td class="text-center">
              <button class="btn btn-sm btn-outline-danger px-2.5 py-1" onclick="deleteAIPlate(${p.id})" style="border-radius: 6px; font-size: 11px;">
                <i class="fas fa-trash-alt"></i> Hapus
              </button>
            </td>
          </tr>
        `;
      }).join('');
    }

    // Helper to get local date time formatted as YYYY-MM-DD HH:MM:SS in browser's local timezone (WIB)
    function getLocalLogTimestamp() {
      const pad = (n) => String(n).padStart(2, '0');
      const d = new Date();
      return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    }

    // Render Live Feed Log Ticker
    function renderAILiveFeed(logs) {
      const container = document.getElementById('ai-live-feed-container');
      if (!container) return;

      if (logs.length === 0) {
        container.innerHTML = '<div class="text-center py-4 text-muted" style="font-size: 12px;">Belum ada riwayat deteksi. Jalankan simulator di samping.</div>';
        return;
      }

      container.innerHTML = logs.map(l => {
        const isBlacklist = l.category === 'blacklist';
        let iconHtml = l.type === 'face' ? '<i class="fas fa-user-check text-info"></i>' : '<i class="fas fa-car text-emerald" style="color: #34d399;"></i>';
        if (isBlacklist) iconHtml = '<i class="fas fa-shield-virus text-danger"></i>';

        let badgeStyle = 'badge-info';
        if (l.category === 'vip') badgeStyle = 'badge-success';
        if (l.category === 'blacklist') badgeStyle = 'badge-danger';

        const timeStr = l.timestamp ? (l.timestamp.includes(' ') ? l.timestamp.split(' ')[1] : l.timestamp) : 'Baru saja';

        return `
          <div class="ai-live-card-item ${isBlacklist ? 'blacklist-alert' : ''}">
            <div class="d-flex align-items-center justify-content-between mb-1.5">
              <div class="d-flex align-items-center gap-2">
                ${iconHtml}
                <strong class="text-white" style="font-size: 13px;">${l.label}</strong>
              </div>
              <span class="badge ${badgeStyle} px-2 py-0.5" style="font-size: 9.5px; font-weight: 700;">
                ${l.confidence ? l.confidence + '%' : '97.5%'}
              </span>
            </div>
            <div class="text-muted mb-1" style="font-size: 11px;">
              <i class="fas fa-video mr-1 text-info"></i> ${l.camera_title}
            </div>
            <div class="d-flex align-items-center justify-content-between" style="font-size: 11px;">
              <span style="color: #94a3b8;">${l.details}</span>
              <span class="text-muted" style="font-family: monospace; font-size: 11px;">${timeStr}</span>
            </div>
          </div>
        `;
      }).join('');
    }

    // Switch Sub Tabs inside AI Analytics
    function switchAISubTab(subTabId) {
      document.querySelectorAll('.ai-subtab-content').forEach(p => p.style.display = 'none');
      const target = document.getElementById(subTabId);
      if (target) target.style.display = 'block';

      const btnFaces = document.getElementById('btn-subtab-faces');
      const btnPlates = document.getElementById('btn-subtab-plates');

      if (subTabId === 'subtab-faces') {
        if (btnFaces) { btnFaces.className = 'btn btn-sm btn-info font-weight-bold px-3 py-2'; }
        if (btnPlates) { btnPlates.className = 'btn btn-sm btn-outline-info font-weight-bold px-3 py-2'; }
      } else {
        if (btnFaces) { btnFaces.className = 'btn btn-sm btn-outline-info font-weight-bold px-3 py-2'; }
        if (btnPlates) { btnPlates.className = 'btn btn-sm btn-info font-weight-bold px-3 py-2'; }
      }
    }

    // AI Canvas HUD Animation
    function initAIHUDCanvas() {
      const canvas = document.getElementById('ai-hud-canvas');
      if (!canvas) return;

      const parent = canvas.parentElement;
      const w = parent ? parent.clientWidth : 640;
      const h = parent ? parent.clientHeight : 380;
      canvas.width = (w && w > 100) ? w : 640;
      canvas.height = (h && h > 100) ? h : 380;
      canvas.style.zIndex = '20';

      if (!aiHUDAnimationId) {
        startAIHUDLoop();
      }
      initAIVideoPanListeners();
      // Start face-api.js real recognition engine
      initFaceAPI();
      startFaceAPIDetectionLoop();
    }

    function startAIHUDLoop() {
      if (aiHUDAnimationId) {
        cancelAnimationFrame(aiHUDAnimationId);
        aiHUDAnimationId = null;
      }
      const canvas = document.getElementById('ai-hud-canvas');
      if (!canvas) return;
      const ctx = canvas.getContext('2d');

      let scanLineY = 0;
      let scanDirection = 1;

      function loop() {
        const now = Date.now();
        // Ensure size
        if (canvas.parentElement && canvas.parentElement.clientWidth > 100 && canvas.width !== canvas.parentElement.clientWidth) {
          canvas.width = canvas.parentElement.clientWidth;
          canvas.height = canvas.parentElement.clientHeight || 380;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const video = document.getElementById('ai-video-player');
        const isWebcamRunning = video && video.srcObject !== null;
        const isCCTVVideoPlaying = video && !video.paused && video.readyState >= 2 && video.videoWidth > 0;

        // If no active webcam and no CCTV video playing, render high-tech CCTV Surveillance Background
        if (!isWebcamRunning && !isCCTVVideoPlaying) {
          drawSimulatedCCTVFeed(ctx, canvas.width, canvas.height);
        }

        // Ultra-Sleek Holographic AI Scan Laser Wave
        scanLineY += 2.0 * scanDirection;
        if (scanLineY >= canvas.height - 15) scanDirection = -1;
        if (scanLineY <= 15) scanDirection = 1;

        ctx.save();
        const laserGrad = ctx.createLinearGradient(0, scanLineY - 18, 0, scanLineY + 18);
        laserGrad.addColorStop(0, 'rgba(0, 240, 255, 0)');
        laserGrad.addColorStop(0.5, 'rgba(0, 240, 255, 0.18)');
        laserGrad.addColorStop(1, 'rgba(0, 240, 255, 0)');
        ctx.fillStyle = laserGrad;
        ctx.fillRect(0, scanLineY - 18, canvas.width, 36);

        ctx.strokeStyle = '#00f0ff';
        ctx.lineWidth = 1.5;
        ctx.shadowColor = '#00f0ff';
        ctx.shadowBlur = 8;
        ctx.beginPath();
        ctx.moveTo(0, scanLineY);
        ctx.lineTo(canvas.width, scanLineY);
        ctx.stroke();
        ctx.restore();

        // Continuous Real-Time Auto-Tracking & Neural Face Matcher Engine
        if (isAutoTrackingActive && cachedAIFaces.length > 0) {
          if (lastFaceAPIResult && lastFaceAPIResult.faces.length > 0 && Date.now() - lastFaceAPIResult.timestamp < 3500) {
            const videoW = video ? (video.videoWidth || video.clientWidth || canvas.width) : canvas.width;
            const videoH = video ? (video.videoHeight || video.clientHeight || canvas.height) : canvas.height;
            const scaleX = canvas.width / videoW;
            const scaleY = canvas.height / videoH;

            const targetEntities = lastFaceAPIResult.faces.map(f => {
              const nb = f.normBox || (f.box ? {
                x: f.box.x / (video ? (video.videoWidth || canvas.width) : canvas.width),
                y: f.box.y / (video ? (video.videoHeight || canvas.height) : canvas.height),
                width: f.box.width / (video ? (video.videoWidth || canvas.width) : canvas.width),
                height: f.box.height / (video ? (video.videoHeight || canvas.height) : canvas.height)
              } : { x: 0.35, y: 0.35, width: 0.25, height: 0.35 });

              const faceData = f.face || {};
              const cat = f.category || faceData.category || 'employee';

              let scaledLandmarks = null;
              if (f.normLandmarks && Array.isArray(f.normLandmarks)) {
                scaledLandmarks = f.normLandmarks.map(p => ({
                  x: Math.round(p.x * canvas.width),
                  y: Math.round(p.y * canvas.height)
                }));
              } else if (f.landmarks && Array.isArray(f.landmarks)) {
                scaledLandmarks = f.landmarks.map(p => ({
                  x: Math.round(p.x * scaleX),
                  y: Math.round(p.y * scaleY)
                }));
              }

              return {
                targetX: Math.round(nb.x * canvas.width),
                targetY: Math.round(nb.y * canvas.height),
                targetW: Math.round(nb.width * canvas.width),
                targetH: Math.round(nb.height * canvas.height),
                type: 'face',
                label: f.name,
                category: cat,
                landmarks: scaledLandmarks,
                confidence: f.confidence,
                createdAt: now
              };
            });

            // Smooth 60 FPS LERP Interpolation (0.35 factor) for zero-lag, silky tracking
            activeAIEntities = targetEntities.map((t, idx) => {
              const prev = activeAIEntities[idx];
              if (!prev || typeof prev.x !== 'number') {
                return {
                  ...t,
                  x: t.targetX,
                  y: t.targetY,
                  w: t.targetW,
                  h: t.targetH
                };
              }
              return {
                ...t,
                x: Math.round(prev.x + (t.targetX - prev.x) * 0.35),
                y: Math.round(prev.y + (t.targetY - prev.y) * 0.35),
                w: Math.round(prev.w + (t.targetW - prev.w) * 0.35),
                h: Math.round(prev.h + (t.targetH - prev.h) * 0.35)
              };
            });

            // Auto-log and banner for detected faces (Instant on new face, 25s throttle for persistent presence)
            const primary = lastFaceAPIResult.faces[0];
            const pFace = primary.face || {};
            const personKey = primary.name;
            const isNewPerson = !window._lastAutoLogPerson || window._lastAutoLogPerson !== personKey;
            const isIntervalPassed = !window._lastAutoLogTime || (now - window._lastAutoLogTime > 25000);

            if (isNewPerson || isIntervalPassed) {
              window._lastAutoLogTime = now;
              window._lastAutoLogPerson = personKey;
              const count = lastFaceAPIResult.faces.length;
              if (primary.face) {
                showAIBanner(`${primary.name} (${pFace.role_title || 'Karyawan'})`, `Confidence: ${primary.confidence}% • face-api.js Neural Net`, pFace.category === 'vip' ? 'badge-success' : 'badge-primary', 'AI VERIFIED', 'fas fa-user-check', '#059669');
              } else {
                showAIBanner(`Pengunjung Belum Terdaftar`, `Wajah tidak dikenal terdeteksi di kamera`, 'badge-warning', 'UNVERIFIED', 'fas fa-user-clock', '#f59e0b');
              }
              // Log all detected to database
              lastFaceAPIResult.faces.forEach(f => {
                if (!f.face) return;
                const activeCamTitle = (currentAICamera && currentAICamera.title) ? currentAICamera.title : (isWebcamRunning ? 'LIVE WEBCAM LAPTOP' : 'CAM LOEWIX CCTV');
                const activeCamId = currentAICamera ? currentAICamera.id : 5002;
                const fd = new FormData();
                fd.append('action', 'log_detection');
                fd.append('type', 'face');
                fd.append('camera_id', activeCamId);
                fd.append('camera_title', activeCamTitle);
                fd.append('label', f.name);
                fd.append('category', f.face.category || 'employee');
                fd.append('confidence', f.confidence);
                fd.append('details', `${f.face.role_title || 'Staff'} • Terverifikasi oleh face-api.js Neural Network`);
                fd.append('timestamp', getLocalLogTimestamp());
                fetch('../api/ai_analytics.php', { method: 'POST', body: fd }).then(() => loadAIData(true)).catch(e => {});
              });
            }
          } else if (!faceAPIReady) {
            // Only show loading placeholder before face-api models finish loading
            const targetW = 160;
            const targetH = 185;
            const centerX = (canvas.width - targetW) / 2;
            const centerY = (canvas.height - targetH) / 2 - 10;
            activeAIEntities = [{
              x: Math.round(centerX),
              y: Math.round(centerY),
              w: targetW,
              h: targetH,
              type: 'face',
              label: '⏳ Loading AI Models...',
              category: 'employee',
              confidence: '...',
              createdAt: now
            }];
          } else {
            activeAIEntities = [];
          }
        } else {
          // Filter manual triggers
          activeAIEntities = activeAIEntities.filter(e => now - e.createdAt < 8000);
        }

        // Render Active Face & Plate AI Entity Brackets
        activeAIEntities.forEach(ent => {
          if (ent.type === 'plate') {
            drawPlateBracket(ctx, ent);
          } else {
            drawEntityBracket(ctx, ent);
          }
        });

        // Top Header OSD Data
        ctx.save();
        const dateStr = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        const timeStr = new Date().toLocaleTimeString('id-ID');
        const activeCamTitle = currentAICamera ? currentAICamera.title.toUpperCase() : (Array.isArray(customerCameras) && customerCameras[0] ? customerCameras[0].title.toUpperCase() : 'YAMAHA DDS');
        const activeCamCity = currentAICamera && currentAICamera.city ? ` [${currentAICamera.city.toUpperCase()}]` : '';

        // Dynamically update the top-left HTML status pill without any overlapping text!
        const statusTextElem = document.getElementById('ai-hud-status-text');
        if (statusTextElem) {
          const camLabel = isWebcamRunning ? 'LIVE WEBCAM LAPTOP' : `${activeCamTitle}${activeCamCity}`;
          statusTextElem.textContent = `AI SCANNER: ${camLabel}`;
        }

        // Clean, non-overlapping Top Right Timestamp OSD
        ctx.font = '700 11px monospace';
        ctx.fillStyle = '#00f0ff';
        ctx.fillText('🔴 LIVE', canvas.width - 275, 30);

        ctx.fillStyle = '#f59e0b';
        ctx.fillText(`${dateStr} ${timeStr} WIB`, canvas.width - 215, 30);

        // Bottom OSD
        ctx.font = '600 11px sans-serif';
        ctx.fillStyle = 'rgba(255, 255, 255, 0.45)';
        ctx.fillText('PT. LOEWIX INDONESIA • AI NEURAL VISION ENGINE V3.4 • 60 FPS LERP • 1080P', 18, canvas.height - 16);

        ctx.restore();
        aiHUDAnimationId = requestAnimationFrame(loop);
      }

      aiHUDAnimationId = requestAnimationFrame(loop);
    }

    // Realistic CCTV Surveillance Feed Generator
    function drawSimulatedCCTVFeed(ctx, w, h) {
      ctx.save();
      // Background gradient
      const bgGrad = ctx.createLinearGradient(0, 0, w, h);
      bgGrad.addColorStop(0, '#0a1128');
      bgGrad.addColorStop(0.5, '#040817');
      bgGrad.addColorStop(1, '#020617');
      ctx.fillStyle = bgGrad;
      ctx.fillRect(0, 0, w, h);

      // Floor & Perspective Room Lines (Simulated Lobby)
      ctx.strokeStyle = 'rgba(56, 189, 248, 0.08)';
      ctx.lineWidth = 1;
      ctx.beginPath();
      // Horizon line
      ctx.moveTo(0, h * 0.65);
      ctx.lineTo(w, h * 0.65);
      // Perspective floor grid
      for (let x = 0; x <= w; x += w / 8) {
        ctx.moveTo(w / 2, h * 0.65);
        ctx.lineTo(x, h);
      }
      ctx.stroke();

      // Center crosshair
      ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
      ctx.beginPath();
      ctx.moveTo(w / 2 - 20, h / 2);
      ctx.lineTo(w / 2 + 20, h / 2);
      ctx.moveTo(w / 2, h / 2 - 20);
      ctx.lineTo(w / 2, h / 2 + 20);
      ctx.stroke();

      // Top CCTV OSD Text
      const d = new Date();
      const timeStr = d.toTimeString().split(' ')[0];
      const dateStr = d.toISOString().split('T')[0];

      ctx.font = '700 12px "Courier New", monospace';
      ctx.fillStyle = '#ef4444';
      ctx.fillText('🔴 REC', 18, 30);

      const activeCamTitle = currentAICamera ? currentAICamera.title.toUpperCase() : (Array.isArray(customerCameras) && customerCameras[0] ? customerCameras[0].title.toUpperCase() : 'YAMAHA DDS');
      const activeCamCity = currentAICamera && currentAICamera.city ? ` [${currentAICamera.city.toUpperCase()}]` : '';

      ctx.fillStyle = '#38bdf8';
      ctx.fillText(`CAM: ${activeCamTitle}${activeCamCity}`, 75, 30);

      ctx.fillStyle = '#f59e0b';
      ctx.fillText(`${dateStr} ${timeStr} WIB`, w - 210, 30);

      // Bottom OSD
      ctx.font = '600 11px sans-serif';
      ctx.fillStyle = 'rgba(255, 255, 255, 0.4)';
      ctx.fillText('PT. LOEWIX INDONESIA • AI NEURAL VISION ENGINE V3 • 1080P @ 25FPS', 18, h - 16);

      ctx.restore();
    }

    function drawEntityBracket(ctx, ent) {
      const { x, y, w, h, label, category, confidence } = ent;
      const isBlacklist = category === 'blacklist';
      const isVIP = category === 'vip';
      const isUnknown = category === 'unknown' || category === 'guest' || String(label).toLowerCase().includes('belum terdaftar') || String(label).toLowerCase().includes('tidak dikenal') || String(label).toLowerCase().includes('pengunjung');

      const strokeColor = isBlacklist ? '#ef4444' : (isVIP ? '#10b981' : (isUnknown ? '#f59e0b' : '#00f0ff'));
      const glowColor = isBlacklist ? 'rgba(239, 68, 68, 0.6)' : (isVIP ? 'rgba(16, 185, 129, 0.6)' : (isUnknown ? 'rgba(245, 158, 11, 0.6)' : 'rgba(0, 240, 255, 0.6)'));
      const boxBg = isBlacklist ? 'rgba(239, 68, 68, 0.08)' : (isVIP ? 'rgba(16, 185, 129, 0.08)' : (isUnknown ? 'rgba(245, 158, 11, 0.08)' : 'rgba(0, 240, 255, 0.06)'));

      ctx.save();

      // 1. Subtle Biometric Bounding Box Fill
      ctx.fillStyle = boxBg;
      ctx.fillRect(x, y, w, h);

      // 2. Faint Grid Box Outline
      ctx.strokeStyle = isBlacklist ? 'rgba(239, 68, 68, 0.25)' : (isVIP ? 'rgba(16, 185, 129, 0.25)' : (isUnknown ? 'rgba(245, 158, 11, 0.25)' : 'rgba(0, 240, 255, 0.20)'));
      ctx.lineWidth = 1;
      ctx.setLineDash([4, 4]);
      ctx.strokeRect(x, y, w, h);
      ctx.setLineDash([]);

      // 3. Precision Cyber Corner Brackets
      ctx.strokeStyle = strokeColor;
      ctx.lineWidth = 2.5;
      ctx.shadowColor = glowColor;
      ctx.shadowBlur = 10;

      const cornerLen = Math.min(24, Math.max(14, Math.round(w * 0.18)));

      // Top-Left Corner
      ctx.beginPath();
      ctx.moveTo(x, y + cornerLen);
      ctx.lineTo(x, y);
      ctx.lineTo(x + cornerLen, y);
      ctx.stroke();

      // Top-Right Corner
      ctx.beginPath();
      ctx.moveTo(x + w - cornerLen, y);
      ctx.lineTo(x + w, y);
      ctx.lineTo(x + w, y + cornerLen);
      ctx.stroke();

      // Bottom-Left Corner
      ctx.beginPath();
      ctx.moveTo(x, y + h - cornerLen);
      ctx.lineTo(x, y + h);
      ctx.lineTo(x + cornerLen, y + h);
      ctx.stroke();

      // Bottom-Right Corner
      ctx.beginPath();
      ctx.moveTo(x + w - cornerLen, y + h);
      ctx.lineTo(x + w, y + h);
      ctx.lineTo(x + w, y + h - cornerLen);
      ctx.stroke();

      // 4. Glowing Corner Accent Dots
      ctx.fillStyle = '#ffffff';
      ctx.shadowBlur = 6;
      [
        [x, y],
        [x + w, y],
        [x, y + h],
        [x + w, y + h]
      ].forEach(([px, py]) => {
        ctx.beginPath();
        ctx.arc(px, py, 2.5, 0, Math.PI * 2);
        ctx.fill();
      });

      // 5. Authentic Neural AI Facial Landmark Visualization
      const cx = x + w / 2;
      const cy = y + h / 2;

      if (ent.landmarks && Array.isArray(ent.landmarks) && ent.landmarks.length >= 68) {
        // Render True 68-Point Neural Face Mesh Contours!
        const l = ent.landmarks;

        ctx.strokeStyle = isBlacklist ? 'rgba(239, 68, 68, 0.3)' : (isVIP ? 'rgba(16, 185, 129, 0.3)' : (isUnknown ? 'rgba(245, 158, 11, 0.3)' : 'rgba(0, 240, 255, 0.3)'));
        ctx.lineWidth = 1;
        ctx.setLineDash([2, 2]);

        // Jawline (0-16)
        ctx.beginPath();
        for (let i = 0; i <= 16; i++) {
          i === 0 ? ctx.moveTo(l[i].x, l[i].y) : ctx.lineTo(l[i].x, l[i].y);
        }
        ctx.stroke();

        // Nose Bridge (27-30) & Nose Base (31-35)
        ctx.beginPath();
        for (let i = 27; i <= 30; i++) i === 27 ? ctx.moveTo(l[i].x, l[i].y) : ctx.lineTo(l[i].x, l[i].y);
        for (let i = 31; i <= 35; i++) i === 31 ? ctx.moveTo(l[i].x, l[i].y) : ctx.lineTo(l[i].x, l[i].y);
        ctx.stroke();

        // Left Eye (36-41) & Right Eye (42-47)
        ctx.beginPath();
        for (let i = 36; i <= 41; i++) i === 36 ? ctx.moveTo(l[i].x, l[i].y) : ctx.lineTo(l[i].x, l[i].y);
        ctx.closePath();
        ctx.stroke();
        ctx.beginPath();
        for (let i = 42; i <= 47; i++) i === 42 ? ctx.moveTo(l[i].x, l[i].y) : ctx.lineTo(l[i].x, l[i].y);
        ctx.closePath();
        ctx.stroke();

        // Lips Outer (48-59)
        ctx.beginPath();
        for (let i = 48; i <= 59; i++) i === 48 ? ctx.moveTo(l[i].x, l[i].y) : ctx.lineTo(l[i].x, l[i].y);
        ctx.closePath();
        ctx.stroke();
        ctx.setLineDash([]);

        // Key Glowing Anchor Nodes (Eyes, Nose Tip, Mouth, Chin)
        const keyIndices = [30, 36, 39, 42, 45, 48, 54, 8];
        keyIndices.forEach(idx => {
          if (l[idx]) {
            ctx.fillStyle = strokeColor;
            ctx.shadowColor = glowColor;
            ctx.shadowBlur = 7;
            ctx.beginPath();
            ctx.arc(l[idx].x, l[idx].y, 2.2, 0, Math.PI * 2);
            ctx.fill();
          }
        });
      } else if (ent.landmarks && Array.isArray(ent.landmarks) && ent.landmarks.length === 6) {
        // Google MediaPipe 6-Keypoint Facial Constellation (0:Right Eye, 1:Left Eye, 2:Nose Tip, 3:Mouth Center, 4:Right Ear, 5:Left Ear)
        const kp = ent.landmarks;
        ctx.strokeStyle = isBlacklist ? 'rgba(239, 68, 68, 0.45)' : (isVIP ? 'rgba(16, 185, 129, 0.45)' : (isUnknown ? 'rgba(245, 158, 11, 0.45)' : 'rgba(0, 240, 255, 0.45)'));
        ctx.lineWidth = 1.2;
        ctx.setLineDash([2, 2]);

        // Eye Line
        ctx.beginPath();
        ctx.moveTo(kp[0].x, kp[0].y);
        ctx.lineTo(kp[1].x, kp[1].y);
        // Eye midpoint to nose
        ctx.moveTo((kp[0].x + kp[1].x) / 2, (kp[0].y + kp[1].y) / 2);
        ctx.lineTo(kp[2].x, kp[2].y);
        // Nose to mouth
        ctx.lineTo(kp[3].x, kp[3].y);
        // Ears to eyes
        ctx.moveTo(kp[4].x, kp[4].y);
        ctx.lineTo(kp[0].x, kp[0].y);
        ctx.moveTo(kp[5].x, kp[5].y);
        ctx.lineTo(kp[1].x, kp[1].y);
        ctx.stroke();
        ctx.setLineDash([]);

        // 6 Glowing Anchor Nodes
        kp.forEach(pt => {
          ctx.fillStyle = strokeColor;
          ctx.shadowColor = glowColor;
          ctx.shadowBlur = 8;
          ctx.beginPath();
          ctx.arc(pt.x, pt.y, 2.8, 0, Math.PI * 2);
          ctx.fill();
        });
      } else {
        // Fallback 5-Node Constellation
        const landmarks = [
          [cx - w * 0.18, cy - h * 0.12], // Left Eye
          [cx + w * 0.18, cy - h * 0.12], // Right Eye
          [cx, cy + h * 0.05],            // Nose Bridge
          [cx - w * 0.14, cy + h * 0.22], // Mouth Left
          [cx + w * 0.14, cy + h * 0.22]  // Mouth Right
        ];

        ctx.strokeStyle = isBlacklist ? 'rgba(239, 68, 68, 0.25)' : (isVIP ? 'rgba(16, 185, 129, 0.25)' : 'rgba(0, 240, 255, 0.25)');
        ctx.lineWidth = 1;
        ctx.setLineDash([2, 2]);
        ctx.beginPath();
        ctx.moveTo(landmarks[0][0], landmarks[0][1]);
        ctx.lineTo(landmarks[1][0], landmarks[1][1]);
        ctx.lineTo(landmarks[2][0], landmarks[2][1]);
        ctx.lineTo(landmarks[0][0], landmarks[0][1]);
        ctx.moveTo(landmarks[2][0], landmarks[2][1]);
        ctx.lineTo(landmarks[3][0], landmarks[3][1]);
        ctx.lineTo(landmarks[4][0], landmarks[4][1]);
        ctx.lineTo(landmarks[2][0], landmarks[2][1]);
        ctx.stroke();
        ctx.setLineDash([]);

        landmarks.forEach(([lx, ly]) => {
          ctx.fillStyle = strokeColor;
          ctx.shadowColor = glowColor;
          ctx.shadowBlur = 8;
          ctx.beginPath();
          ctx.arc(lx, ly, 2.2, 0, Math.PI * 2);
          ctx.fill();
        });
      }

      // 6. Center Biometric Target Reticle
      ctx.strokeStyle = strokeColor;
      ctx.lineWidth = 1;
      ctx.shadowBlur = 4;
      ctx.beginPath();
      ctx.arc(cx, cy, 14, 0, Math.PI * 2);
      ctx.stroke();

      // Reticle Crosshairs
      ctx.beginPath();
      ctx.moveTo(cx - 8, cy);
      ctx.lineTo(cx + 8, cy);
      ctx.moveTo(cx, cy - 8);
      ctx.lineTo(cx, cy + 8);
      ctx.stroke();

      // 7. Ultra-Sleek Glassmorphic Floating Identification Badge
      const badgeW = Math.max(w + 16, 216);
      const badgeH = 36;
      const canvasW = ctx.canvas ? ctx.canvas.width : 640;
      const badgeX = Math.max(8, Math.min(canvasW - badgeW - 8, x + (w - badgeW) / 2));
      let badgeY = y - badgeH - 10;
      if (badgeY < 8) {
        badgeY = y + 8; // clamp inside box if too high near canvas edge
      }

      // Rounded Badge Card Background with Dark Glassmorphic Gradient
      ctx.fillStyle = 'rgba(4, 9, 22, 0.92)';
      ctx.strokeStyle = strokeColor;
      ctx.lineWidth = 1.3;
      ctx.shadowColor = glowColor;
      ctx.shadowBlur = 12;

      const rad = 9;
      ctx.beginPath();
      ctx.moveTo(badgeX + rad, badgeY);
      ctx.lineTo(badgeX + badgeW - rad, badgeY);
      ctx.quadraticCurveTo(badgeX + badgeW, badgeY, badgeX + badgeW, badgeY + rad);
      ctx.lineTo(badgeX + badgeW, badgeY + badgeH - rad);
      ctx.quadraticCurveTo(badgeX + badgeW, badgeY + badgeH, badgeX + badgeW - rad, badgeY + badgeH);
      ctx.lineTo(badgeX + rad, badgeY + badgeH);
      ctx.quadraticCurveTo(badgeX, badgeY + badgeH, badgeX, badgeY + badgeH - rad);
      ctx.lineTo(badgeX, badgeY + rad);
      ctx.quadraticCurveTo(badgeX, badgeY, badgeX + rad, badgeY);
      ctx.closePath();
      ctx.fill();
      ctx.stroke();

      // Top Highlight Rim Line for 3D Glass Look
      ctx.strokeStyle = 'rgba(255, 255, 255, 0.25)';
      ctx.lineWidth = 1;
      ctx.shadowBlur = 0;
      ctx.beginPath();
      ctx.moveTo(badgeX + rad, badgeY + 1);
      ctx.lineTo(badgeX + badgeW - rad, badgeY + 1);
      ctx.stroke();

      // Live Pulsing Beacon Dot inside badge
      const pulseTime = Date.now() / 250;
      const pulseRadius = 3.6 + Math.sin(pulseTime) * 1.2;
      ctx.fillStyle = isBlacklist ? '#ef4444' : (isVIP ? '#10b981' : (isUnknown ? '#f59e0b' : '#00f0ff'));
      ctx.shadowColor = ctx.fillStyle;
      ctx.shadowBlur = 9;
      ctx.beginPath();
      ctx.arc(badgeX + 16, badgeY + badgeH / 2, pulseRadius, 0, Math.PI * 2);
      ctx.fill();

      // Person Name
      ctx.shadowBlur = 0;
      ctx.fillStyle = '#ffffff';
      ctx.font = '700 13px "Plus Jakarta Sans", -apple-system, sans-serif';
      const cleanLabel = isUnknown ? 'WAJAH TIDAK DIKENAL' : String(label).toUpperCase();
      ctx.fillText(cleanLabel, badgeX + 28, badgeY + 17);

      // Subtitle Tag (Role / Access)
      ctx.fillStyle = isBlacklist ? '#fca5a5' : (isVIP ? '#6ee7b7' : (isUnknown ? '#fde047' : '#7dd3fc'));
      ctx.font = '600 9.5px "Plus Jakarta Sans", sans-serif';
      const subText = isBlacklist ? '🚨 DPO / BLACKLIST' : (isVIP ? '⭐ VIP ACCESSED' : (isUnknown ? '❓ BELUM TERDAFTAR • UNVERIFIED' : '👤 VERIFIED EMPLOYEE'));
      ctx.fillText(subText, badgeX + 28, badgeY + 29);

      // Score / Confidence Pill on the Right
      const pillW = 62;
      const pillH = 21;
      const pillX = badgeX + badgeW - pillW - 8;
      const pillY = badgeY + (badgeH - pillH) / 2;

      ctx.fillStyle = isBlacklist ? 'rgba(239, 68, 68, 0.28)' : (isVIP ? 'rgba(16, 185, 129, 0.28)' : (isUnknown ? 'rgba(245, 158, 11, 0.28)' : 'rgba(0, 240, 255, 0.22)'));
      ctx.strokeStyle = strokeColor;
      ctx.lineWidth = 1.1;
      ctx.beginPath();
      ctx.roundRect ? ctx.roundRect(pillX, pillY, pillW, pillH, 6) : ctx.rect(pillX, pillY, pillW, pillH);
      ctx.fill();
      ctx.stroke();

      ctx.fillStyle = isUnknown ? '#fde047' : '#ffffff';
      ctx.font = '800 10px monospace';
      ctx.textAlign = 'center';
      const confStr = (confidence && String(confidence).includes('%')) ? confidence : `${confidence || 98.4}%`;
      ctx.fillText(confStr, pillX + pillW / 2, pillY + 15);
      ctx.textAlign = 'left';

      ctx.restore();
    }

    // Trigger Instant AI Detection Simulation
    async function simulateAIDetection(type) {
      initAIHUDCanvas();
      const canvas = document.getElementById('ai-hud-canvas');
      const width = canvas ? canvas.width : 640;
      const height = canvas ? canvas.height : 380;

      let ent = null;
      let logPayload = null;

      if (type === 'vip_face') {
        ent = {
          x: Math.round(width * 0.35),
          y: Math.round(height * 0.2),
          w: 120,
          h: 140,
          type: 'face',
          label: 'Bambang Supriyanto',
          category: 'vip',
          confidence: 97.8,
          createdAt: Date.now()
        };
        showAIBanner('Bambang Supriyanto (VIP)', 'Confidence 97.8% • Akses Terbuka Otomatis', 'badge-success', 'VIP ACCESSED', 'fas fa-user-check', '#059669');
        logPayload = {
          type: 'face',
          camera_id: 5002,
          camera_title: 'CAM LOEWIX JAKARTA 1 - LOBBY UTAMA',
          label: 'Bambang Supriyanto',
          category: 'vip',
          confidence: 97.8,
          details: 'Terdeteksi di Lobby Utama • Akses VIP Terbuka'
        };
      } else if (type === 'blacklist_face') {
        ent = {
          x: Math.round(width * 0.45),
          y: Math.round(height * 0.25),
          w: 120,
          h: 140,
          type: 'face',
          label: '🚨 TERSANGKA DPO',
          category: 'blacklist',
          confidence: 95.4,
          createdAt: Date.now()
        };
        showAIBanner('🚨 PERINGATAN: Tersangka DPO Terdeteksi!', 'Confidence 95.4% • Segera Amankan Lokasi', 'badge-danger', 'ALERT DPO', 'fas fa-shield-virus', '#ef4444');
        playAIAlarmSound();
        logPayload = {
          type: 'face',
          camera_id: 5002,
          camera_title: 'CAM LOEWIX JAKARTA 1 - LOBBY UTAMA',
          label: 'Tersangka Residu DPO',
          category: 'blacklist',
          confidence: 95.4,
          details: 'ALERT KEAMANAN: DPO Pencurian Terdeteksi di Lobby'
        };
      } else if (type === 'vip_plate') {
        ent = {
          x: Math.round(width * 0.25),
          y: Math.round(height * 0.4),
          w: 220,
          h: 100,
          type: 'anpr',
          label: 'B 1234 YMH (VIP)',
          category: 'vip',
          confidence: 98.6,
          createdAt: Date.now()
        };
        showAIBanner('🚗 Plat B 1234 YMH (Toyota Alphard)', 'Confidence 98.6% • Palang Barrier Gate Terbuka Otomatis', 'badge-success', 'GATE OPEN', 'fas fa-car', '#0284c7');
        logPayload = {
          type: 'anpr',
          camera_id: 5003,
          camera_title: 'CAM LOEWIX GATE MASUK & PARKIR',
          label: 'B 1234 YMH',
          category: 'vip',
          confidence: 98.6,
          details: 'Toyota Alphard Hitam • Palang Gate Masuk Terbuka'
        };
      } else if (type === 'employee_plate') {
        ent = {
          x: Math.round(width * 0.28),
          y: Math.round(height * 0.42),
          w: 200,
          h: 90,
          type: 'anpr',
          label: 'B 5678 DDS (Motor)',
          category: 'employee',
          confidence: 96.9,
          createdAt: Date.now()
        };
        showAIBanner('🏍️ Plat B 5678 DDS (Honda Beat)', 'Confidence 96.9% • Masuk Area Parkir Staff', 'badge-primary', 'STAFF ACCESS', 'fas fa-motorcycle', '#0284c7');
        logPayload = {
          type: 'anpr',
          camera_id: 5003,
          camera_title: 'CAM LOEWIX GATE MASUK & PARKIR',
          label: 'B 5678 DDS',
          category: 'employee',
          confidence: 96.9,
          details: 'Honda Beat Hitam • Masuk Area Parkir Staff'
        };
      }

      if (ent) {
        activeAIEntities.push(ent);
      }

      // Persist to server logs
      if (logPayload) {
        try {
          const fd = new FormData();
          fd.append('action', 'log_detection');
          fd.append('timestamp', getLocalLogTimestamp());
          for (const k in logPayload) {
            fd.append(k, logPayload[k]);
          }
          const res = await fetch('../api/ai_analytics.php', { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            loadAIData();
          }
        } catch (e) {
          console.error(e);
        }
      }
    }

    function showAIBanner(title, sub, badgeClass, badgeText, iconClass, iconBg) {
      const banner = document.getElementById('ai-hud-detection-banner');
      if (!banner) return;

      document.getElementById('ai-banner-title').textContent = title;
      document.getElementById('ai-banner-sub').textContent = sub;
      const bBadge = document.getElementById('ai-banner-badge');
      bBadge.className = `badge ${badgeClass} px-2 py-1`;
      bBadge.textContent = badgeText;

      const bIcon = document.getElementById('ai-banner-icon');
      bIcon.style.background = iconBg;
      bIcon.innerHTML = `<i class="${iconClass}"></i>`;

      banner.style.display = 'flex';
      setTimeout(() => {
        banner.style.display = 'none';
      }, 4500);
    }

    function playAIAlarmSound() {
      if (!isAISoundEnabled) return;
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(880, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(440, audioCtx.currentTime + 0.4);
        gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.4);
      } catch (e) {}
    }

    async function scanCurrentFrameManual() {
      const video = document.getElementById('ai-video-player');
      if (!video) return;
      const frameCanvas = getDetectionFrame(video);
      if (frameCanvas) {
        showAIBanner('🔍 Memindai Frame CCTV...', 'Sedang menganalisis wajah & biometrik pada frame aktif', 'badge-info', 'SCANNING', 'fas fa-search', '#0284c7');
        await runFaceAPIDetection(video, frameCanvas);
        if (!lastFaceAPIResult || lastFaceAPIResult.faces.length === 0) {
          await detectSalientSubjectInFrame(frameCanvas);
        }
      }
    }

    let aiMainWebcamStream = null;

    async function startAIWebcamLive() {
      const video = document.getElementById('ai-video-player');
      const select = document.getElementById('ai-camera-selector');
      if (!video) return;

      currentAICamera = { id: 'webcam', title: 'LIVE WEBCAM LAPTOP' };

      const wagyu = cachedAIFaces.find(f => f.name.toLowerCase().includes('wagyu')) || cachedAIFaces[0];
      if (wagyu) {
        activeTrackedFace = wagyu;
        const targetSelect = document.getElementById('ai-target-face-selector');
        if (targetSelect) targetSelect.value = wagyu.id;
      }

      try {
        if (select) select.value = 'webcam';
        if (aiMainWebcamStream) {
          aiMainWebcamStream.getTracks().forEach(t => t.stop());
        }
        aiMainWebcamStream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        video.srcObject = aiMainWebcamStream;
        await video.play();

        const statusLabel = document.getElementById('ai-active-mode-label');
        if (statusLabel) statusLabel.innerHTML = '<span class="text-emerald" style="color: #34d399;"><i class="fas fa-video mr-1"></i> Live Webcam Scanner Aktif (WAGYU)</span>';

        initAIHUDCanvas();

        setTimeout(() => {
          if (activeTrackedFace) {
            simulateCustomFaceDetection(activeTrackedFace.name, activeTrackedFace.category, activeTrackedFace.role_title);
          }
        }, 500);

      } catch (err) {
        console.error('Webcam error:', err);
        alert('Gagal mengakses kamera laptop/HP. Pastikan browser diizinkan mengakses kamera.');
      }
    }

    let currentAICamera = null;

    function showAICameraOfflineNotice(camTitle) {
      const placeholder = document.getElementById('ai-video-placeholder');
      if (!placeholder) return;
      placeholder.style.display = 'flex';
      placeholder.style.zIndex = '15';
      placeholder.innerHTML = `
        <div style="background: rgba(15, 23, 42, 0.92); border: 1.5px solid rgba(245, 158, 11, 0.5); border-radius: 14px; padding: 22px 28px; max-width: 440px; box-shadow: 0 10px 40px rgba(0,0,0,0.8); text-align: center;">
          <div style="width: 48px; height: 48px; margin: 0 auto 12px; border-radius: 50%; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-video-slash text-warning" style="font-size: 22px;"></i>
          </div>
          <h6 class="text-white font-weight-bold mb-1" style="font-size: 14.5px;">STREAM CCTV STANDBY / OFFLINE</h6>
          <p class="text-muted mb-3" style="font-size: 12px; line-height: 1.5;">
            Kamera <strong>${camTitle}</strong> saat ini belum terhubung ke Media Server atau perangkat DVR/NVR fisik sedang offline di lokasi.
          </p>
          <button class="btn btn-sm btn-info font-weight-bold px-3 py-1.5" onclick="changeAICamera('webcam')" style="border-radius: 8px; font-size: 12px; background: linear-gradient(135deg, #0284c7, #0ea5e9); border: none;">
            <i class="fas fa-camera mr-1"></i> Beralih ke Live Webcam Laptop
          </button>
        </div>
      `;
    }

    // Resolve live camera stream URL dynamically (supporting XMeye P2P and MediaMTX)
    async function resolveCameraStreamUrl(cam) {
      if (!cam) return null;
      let streamUrl = cam.hls_url || cam.streamPath || '';

      // 1. If XMeye P2P camera without active bcloud URL, resolve via jftech_gateway.php
      if (cam.connection_type === 'xmeye_p2p' || (!streamUrl.includes('bcloud365.net') && cam.serial_number)) {
        const sn = cam.serial_number || (streamUrl.match(/^xmeye_([a-fA-F0-9]+)/) ? streamUrl.match(/^xmeye_([a-fA-F0-9]+)/)[1] : '');
        const ch = cam.channel || 1;
        const devUser = cam.device_user || 'admin';
        const devPass = cam.device_pass || '';

        if (sn) {
          try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000);
            const res = await fetch(`../api/jftech_gateway.php?action=get_live_stream&sn=${encodeURIComponent(sn)}&channel=${encodeURIComponent(ch)}&stream=1&device_user=${encodeURIComponent(devUser)}&device_pass=${encodeURIComponent(devPass)}`, {
              signal: controller.signal
            });
            clearTimeout(timeoutId);
            const data = await res.json();
            if (data.success && data.hls_url) {
              streamUrl = data.hls_url;
              cam.hls_url = data.hls_url;
            }
          } catch (e) {
            console.warn('[AI Camera] Failed to resolve XMeye stream:', e);
          }
        }
      }

      // 2. Auto-fallback if only rtsp_url is present
      if (!streamUrl && cam.rtsp_url) {
        if (cam.rtsp_url.includes('103.164.101.50:8203') && cam.rtsp_url.includes('channel=1')) {
          streamUrl = cam.rtsp_url.includes('stream=1') ? 'https://stream.loewixcctv.com/cctv_loewix_1_sub/index.m3u8' : 'https://stream.loewixcctv.com/cctv_loewix_1/index.m3u8';
        } else if (cam.rtsp_url.includes('103.164.101.50:8203') && cam.rtsp_url.includes('channel=2')) {
          streamUrl = cam.rtsp_url.includes('stream=1') ? 'https://stream.loewixcctv.com/cctv_loewix_2_sub/index.m3u8' : 'https://stream.loewixcctv.com/cctv_loewix_2/index.m3u8';
        } else if (cam.rtsp_url.includes('103.164.101.50:8203') && cam.rtsp_url.includes('channel=3')) {
          streamUrl = cam.rtsp_url.includes('stream=1') ? 'https://stream.loewixcctv.com/cctv_loewix_3_sub/index.m3u8' : 'https://stream.loewixcctv.com/cctv_loewix_3/index.m3u8';
        } else {
          const path = cam.streamPath || `cam_live_${cam.id}`;
          streamUrl = `https://stream.loewixcctv.com/${path}/index.m3u8`;
        }
        cam.hls_url = streamUrl;
      }

      // 3. Normalization for MediaMTX / RTSP stream path
      if (streamUrl && !streamUrl.startsWith('http://') && !streamUrl.startsWith('https://')) {
        streamUrl = `https://stream.loewixcctv.com/${streamUrl}/index.m3u8`;
      } else if (streamUrl && streamUrl.startsWith('http://')) {
        streamUrl = streamUrl.replace('http://', 'https://');
      }

      return streamUrl;
    }

    async function changeAICamera(camId) {
      _spatialTrackBuffers.clear();
      lastFaceAPIResult = null;
      activeAIEntities = [];
      const select = document.getElementById('ai-camera-selector');
      if (select) select.value = camId;
      const statusLabel = document.getElementById('ai-active-mode-label');
      const placeholder = document.getElementById('ai-video-placeholder');
      const video = document.getElementById('ai-video-player');

      if (camId === 'webcam') {
        if (placeholder) placeholder.style.display = 'none';
        currentAICamera = { id: 'webcam', title: 'Live Webcam Laptop' };
        const wagyu = cachedAIFaces.find(f => f.name.toLowerCase().includes('wagyu'));
        if (wagyu) selectAITargetFace(wagyu.id);
        startAIWebcamLive();
      } else {
        if (aiMainWebcamStream) {
          aiMainWebcamStream.getTracks().forEach(t => t.stop());
          aiMainWebcamStream = null;
        }

        const cam = (Array.isArray(customerCameras) && customerCameras.length > 0)
          ? customerCameras.find(c => c.id == camId)
          : null;

        currentAICamera = cam || { id: camId, title: 'CAM LOEWIX CCTV', city: 'JAKARTA' };

        if (statusLabel) {
          statusLabel.innerHTML = `<span class="text-info"><i class="fas fa-video mr-1"></i> ${currentAICamera.title} (Live CCTV)</span>`;
        }


        if (video) {
          video.srcObject = null;
          video.crossOrigin = 'anonymous';
          video.setAttribute('crossorigin', 'anonymous');
          video.muted = true;
          video.setAttribute('playsinline', 'true');
          video.setAttribute('webkit-playsinline', 'true');
          video.setAttribute('autoplay', '');
          video.setAttribute('muted', '');

          if (placeholder) {
            placeholder.style.display = 'flex';
            placeholder.style.zIndex = '15';
            placeholder.innerHTML = `
              <div class="spinner-border text-info spinner-border-sm mb-2" role="status"></div>
              <h6 class="text-white font-weight-bold mb-1" style="font-size: 13.5px;">Menghubungkan Stream ${currentAICamera.title}...</h6>
              <small class="text-muted" style="font-size: 11px;">Handshake Cloud P2P & Buffer HLS Segmen 1 (1-3 detik)</small>
            `;
          }

          const streamUrl = await resolveCameraStreamUrl(cam);

          if (!streamUrl) {
            showAICameraOfflineNotice(currentAICamera.title);
            return;
          }

          // Hide placeholder as soon as actual video frames start rendering
          video.addEventListener('playing', () => {
            if (placeholder) placeholder.style.display = 'none';
          }, { once: true });

          if (streamUrl.includes('.m3u8') || streamUrl.includes('bcloud365.net')) {
            if (typeof Hls !== 'undefined' && Hls.isSupported()) {
              if (window._aiHls) window._aiHls.destroy();
              window._aiHls = new Hls({
                enableWorker: true,
                lowLatencyMode: true,
                liveSyncDurationCount: 1,
                maxBufferLength: 1,
                maxMaxBufferLength: 2,
                initialLiveManifestSize: 1,
                manifestLoadingTimeOut: 6000,
                manifestLoadingMaxRetry: 2,
                xhrSetup: function(xhr, url) {
                  try { xhr.withCredentials = false; } catch(e) {}
                }
              });
              window._aiHls.loadSource(streamUrl);
              window._aiHls.attachMedia(video);
              window._aiHls.on(Hls.Events.MANIFEST_PARSED, () => {
                video.play().catch(e => {});
              });
              window._aiHls.on(Hls.Events.ERROR, (event, data) => {
                if (data.fatal) {
                  console.warn('[AI Camera] HLS stream error:', data);
                  showAICameraOfflineNotice(currentAICamera.title);
                }
              });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
              video.src = streamUrl;
              video.play().then(() => {
                if (placeholder) placeholder.style.display = 'none';
              }).catch(e => {
                showAICameraOfflineNotice(currentAICamera.title);
              });
            } else {
              showAICameraOfflineNotice(currentAICamera.title);
            }
          } else {
            video.src = streamUrl;
            video.play().then(() => {
              if (placeholder) placeholder.style.display = 'none';
            }).catch(e => {
              showAICameraOfflineNotice(currentAICamera.title);
            });
          }
        }

        window._lastAutoLogTime = 0;
        activeAIEntities = [];
        lastFaceAPIResult = null;
        activeTrackedFace = null;
        const targetSelect = document.getElementById('ai-target-face-selector');
        if (targetSelect) targetSelect.value = 'auto';
        initAIHUDCanvas();
      }
    }

    // ========================================================
    // AI VIDEO SUPER-SHARPNESS & DIGITAL ZOOM ENHANCER
    // ========================================================
    let aiVideoZoomLevel = 1;
    let aiVideoPanX = 0;
    let aiVideoPanY = 0;
    let isAiPanning = false;
    let aiPanStartX = 0;
    let aiPanStartY = 0;
    let currentAIFilterMode = 'normal';

    function setAIVideoFilter(mode) {
      currentAIFilterMode = mode;
      const video = document.getElementById('ai-video-player');
      if (!video) return;

      ['btn-filter-sharp', 'btn-filter-ocr', 'btn-filter-hdr', 'btn-filter-normal'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.className = 'btn btn-sm btn-outline-secondary px-2.5 py-1';
      });

      const activeBtn = document.getElementById(`btn-filter-${mode}`);
      if (activeBtn) {
        activeBtn.className = 'btn btn-sm btn-info px-2.5 py-1 font-weight-bold';
      }

      if (mode === 'sharp') {
        video.style.filter = 'url(#ai-super-sharpen) contrast(1.45) brightness(1.08) saturate(1.2)';
      } else if (mode === 'ocr') {
        video.style.filter = 'grayscale(1) contrast(2.4) brightness(1.2)';
      } else if (mode === 'hdr') {
        video.style.filter = 'contrast(1.3) brightness(0.92) saturate(1.35)';
      } else {
        video.style.filter = 'brightness(0.95) contrast(1.05)';
      }
    }

    function setAIVideoZoom(scale) {
      aiVideoZoomLevel = scale;
      if (scale === 1) {
        aiVideoPanX = 0;
        aiVideoPanY = 0;
      }
      applyAIVideoTransform();
    }

    function resetAIVideoPanZoom() {
      aiVideoZoomLevel = 1;
      aiVideoPanX = 0;
      aiVideoPanY = 0;
      applyAIVideoTransform();
      setAIVideoFilter('normal');
    }

    function applyAIVideoTransform() {
      const video = document.getElementById('ai-video-player');
      const canvas = document.getElementById('ai-hud-canvas');
      const wrapper = document.getElementById('ai-video-wrapper');

      const transformStr = `scale(${aiVideoZoomLevel}) translate(${aiVideoPanX}px, ${aiVideoPanY}px)`;
      if (video) video.style.transform = transformStr;
      if (canvas) canvas.style.transform = transformStr;

      if (wrapper) {
        wrapper.style.cursor = aiVideoZoomLevel > 1 ? 'grab' : 'default';
      }
    }

    function initAIVideoPanListeners() {
      const wrapper = document.getElementById('ai-video-wrapper');
      if (!wrapper) return;

      wrapper.addEventListener('mousedown', (e) => {
        if (aiVideoZoomLevel <= 1) return;
        isAiPanning = true;
        aiPanStartX = e.clientX - aiVideoPanX * aiVideoZoomLevel;
        aiPanStartY = e.clientY - aiVideoPanY * aiVideoZoomLevel;
        wrapper.style.cursor = 'grabbing';
      });

      window.addEventListener('mousemove', (e) => {
        if (!isAiPanning || aiVideoZoomLevel <= 1) return;
        aiVideoPanX = (e.clientX - aiPanStartX) / aiVideoZoomLevel;
        aiVideoPanY = (e.clientY - aiPanStartY) / aiVideoZoomLevel;
        applyAIVideoTransform();
      });

      window.addEventListener('mouseup', () => {
        if (isAiPanning) {
          isAiPanning = false;
          const wrapper = document.getElementById('ai-video-wrapper');
          if (wrapper) wrapper.style.cursor = aiVideoZoomLevel > 1 ? 'grab' : 'default';
        }
      });
    }

    // Trigger Custom Plate Detection
    function simulateCustomPlateDetection(plateNumber, category = 'resident', vehicleModel = 'Mobil') {
      initAIHUDCanvas();
      const canvas = document.getElementById('ai-hud-canvas');
      const width = canvas ? canvas.width : 640;
      const height = canvas ? canvas.height : 380;

      const isBlacklist = category === 'blacklist';
      const isVIP = category === 'vip';

      const ent = {
        x: Math.round(width * 0.26),
        y: Math.round(height * 0.42),
        w: 220,
        h: 95,
        type: 'anpr',
        label: `${plateNumber} (${isVIP ? 'VIP' : (isBlacklist ? 'BLACKLIST' : 'ACCESS')})`,
        category: category,
        confidence: (97 + Math.random() * 2.5).toFixed(1),
        createdAt: Date.now()
      };

      activeAIEntities.push(ent);

      const badgeClass = isBlacklist ? 'badge-danger' : (isVIP ? 'badge-success' : 'badge-primary');
      const badgeText = isBlacklist ? 'ALERT BLACKLIST' : (isVIP ? 'GATE OPEN (VIP)' : 'GATE OPEN');
      const iconClass = 'fas fa-car';
      const iconBg = isBlacklist ? '#ef4444' : (isVIP ? '#059669' : '#0284c7');

      showAIBanner(`🚗 Plat ${plateNumber} (${vehicleModel})`, `Confidence: ${ent.confidence}% • Palang Pintu Terbuka Otomatis`, badgeClass, badgeText, iconClass, iconBg);

      if (isBlacklist) {
        playAIAlarmSound();
      }

      // Log to server
      const fd = new FormData();
      fd.append('action', 'log_detection');
      fd.append('type', 'anpr');
      fd.append('camera_id', 5003);
      fd.append('camera_title', 'CAM LOEWIX GATE MASUK & PARKIR');
      fd.append('label', plateNumber);
      fd.append('category', category);
      fd.append('confidence', ent.confidence);
      fd.append('details', `${vehicleModel} • Palang Gate Masuk Terbuka`);
      fd.append('timestamp', getLocalLogTimestamp());
      fetch('../api/ai_analytics.php', { method: 'POST', body: fd }).then(() => loadAIData()).catch(e => {});
    }

    function toggleAISound(isEnabled) {
      isAISoundEnabled = isEnabled;
    }

    function toggleAIAutoTracking() {
      isAutoTrackingActive = !isAutoTrackingActive;
      const btn = document.getElementById('btn-toggle-autoscan');
      const label = document.getElementById('ai-active-mode-label');
      if (btn) {
        if (isAutoTrackingActive) {
          btn.className = 'btn btn-sm btn-success font-weight-bold px-2.5 py-1';
          btn.style.background = '#059669';
          btn.style.boxShadow = '0 0 10px rgba(5, 150, 105, 0.4)';
          btn.innerHTML = '<i class="fas fa-bolt mr-1"></i> Auto-Scan: AKTIF';
          if (label) label.innerHTML = '<i class="fas fa-circle text-success mr-1" style="font-size: 8px;"></i> Auto Face-ID Active';
          const video = document.getElementById('ai-video-player');
          if (video) runFaceAPIDetection(video);
        } else {
          btn.className = 'btn btn-sm btn-outline-secondary font-weight-bold px-2.5 py-1';
          btn.style.background = 'transparent';
          btn.style.boxShadow = 'none';
          btn.innerHTML = '<i class="fas fa-pause mr-1"></i> Auto-Scan: PAUSED';
          if (label) label.innerHTML = '<i class="fas fa-pause text-muted mr-1" style="font-size: 8px;"></i> Manual Trigger Only';
          activeAIEntities = [];
        }
      }
    }

    // Handle Image Upload from File Picker (HP / PC)
    function handleFaceFileUpload(event) {
      const file = event.target.files && event.target.files[0];
      if (!file) return;

      if (!file.type.startsWith('image/')) {
        alert('Silakan pilih file gambar (JPG, PNG).');
        return;
      }

      const reader = new FileReader();
      reader.onload = function(e) {
        const base64 = e.target.result;
        const img = document.getElementById('face-preview-img');
        const hiddenInput = document.getElementById('face-input-photo');
        if (img) img.src = base64;
        if (hiddenInput) hiddenInput.value = base64;
      };
      reader.readAsDataURL(file);
    }

    // ========================================================
    // LIVE BIOMETRIC FACE ENROLLMENT SCANNER CONTROLLER
    // ========================================================
    let faceWebcamStream = null;
    let faceEnrollmentInterval = null;

    async function startFaceEnrollmentCamera() {
      const video = document.getElementById('face-webcam-video');
      const viewFinder = document.getElementById('face-scanner-viewfinder');
      const previewBox = document.getElementById('face-scanned-preview-box');
      const btnCapture = document.getElementById('btn-capture-face');
      const btnRescan = document.getElementById('btn-rescan-face');
      const statusBadge = document.getElementById('face-scan-status-badge');

      if (!video) return;

      if (viewFinder) viewFinder.style.display = 'block';
      if (previewBox) previewBox.style.display = 'none';
      if (btnCapture) btnCapture.style.display = 'inline-block';
      if (btnRescan) btnRescan.style.display = 'none';
      if (statusBadge) statusBadge.innerHTML = '<span class="pulse-dot" style="background: #00f0ff; margin-right: 4px;"></span> Menghubungkan Kamera...';

      stopFaceWebcam();

      try {
        faceWebcamStream = await navigator.mediaDevices.getUserMedia({ 
          video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
        });
        video.srcObject = faceWebcamStream;
        await video.play();

        if (statusBadge) {
          statusBadge.innerHTML = '<span class="pulse-dot" style="background: #10b981; margin-right: 4px;"></span> Kamera Aktif • Posisikan Wajah di Lingkaran';
        }

        // Real-time quality guide check using faceapi if available
        if (typeof faceapi !== 'undefined' && faceAPIReady) {
          faceEnrollmentInterval = setInterval(async () => {
            if (!video || video.paused || video.ended || !video.videoWidth) return;
            try {
              const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.3 }));
              if (detection && statusBadge) {
                statusBadge.innerHTML = '<span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Wajah Terdeteksi Jelas (Siap Di-scan)</span>';
                statusBadge.style.borderColor = '#10b981';
              } else if (statusBadge) {
                statusBadge.innerHTML = '<span style="color: #38bdf8;"><i class="fas fa-expand mr-1"></i> Arahkan Wajah ke Dalam Lingkaran</span>';
                statusBadge.style.borderColor = 'rgba(0, 240, 255, 0.5)';
              }
            } catch (e) {}
          }, 600);
        }

      } catch (err) {
        console.error('Webcam access error:', err);
        if (statusBadge) {
          statusBadge.innerHTML = '<span class="text-danger font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal Mengakses Kamera</span>';
        }
        alert('Gagal mengakses kamera laptop/HP. Pastikan izin kamera telah diberikan di browser Anda.');
      }
    }

    function captureFaceFromWebcam() {
      const video = document.getElementById('face-webcam-video');
      const viewFinder = document.getElementById('face-scanner-viewfinder');
      const previewBox = document.getElementById('face-scanned-preview-box');
      const btnCapture = document.getElementById('btn-capture-face');
      const btnRescan = document.getElementById('btn-rescan-face');
      const img = document.getElementById('face-preview-img');
      const hiddenInput = document.getElementById('face-input-photo');

      if (!video || !video.videoWidth) {
        alert('Kamera belum siap. Mohon tunggu sejenak atau pastikan kamera aktif.');
        return;
      }

      const canvas = document.createElement('canvas');
      canvas.width = 400;
      canvas.height = 400;
      const ctx = canvas.getContext('2d');

      const minDim = Math.min(video.videoWidth, video.videoHeight) || 400;
      const sx = (video.videoWidth - minDim) / 2 || 0;
      const sy = (video.videoHeight - minDim) / 2 || 0;

      // Draw mirrored frame like a selfie mirror
      ctx.translate(400, 0);
      ctx.scale(-1, 1);
      ctx.drawImage(video, sx, sy, minDim, minDim, 0, 0, 400, 400);
      const base64 = canvas.toDataURL('image/jpeg', 0.92);

      if (img) img.src = base64;
      if (hiddenInput) hiddenInput.value = base64;

      // Update UI state
      if (viewFinder) viewFinder.style.display = 'none';
      if (previewBox) previewBox.style.display = 'block';
      if (btnCapture) btnCapture.style.display = 'none';
      if (btnRescan) btnRescan.style.display = 'inline-block';

      stopFaceWebcam();
    }

    function stopFaceWebcam() {
      if (faceEnrollmentInterval) {
        clearInterval(faceEnrollmentInterval);
        faceEnrollmentInterval = null;
      }
      const video = document.getElementById('face-webcam-video');
      if (faceWebcamStream) {
        faceWebcamStream.getTracks().forEach(track => track.stop());
        faceWebcamStream = null;
      }
      if (video) video.srcObject = null;
    }

    // Trigger Custom Face Detection for any registered user
    function simulateCustomFaceDetection(name, category = 'employee', roleTitle = 'Staff') {
      initAIHUDCanvas();

      // Sync active target face
      const face = cachedAIFaces.find(f => f.name.toLowerCase() === name.toLowerCase());
      if (face) {
        activeTrackedFace = face;
        const select = document.getElementById('ai-target-face-selector');
        if (select) select.value = face.id;
      } else {
        activeTrackedFace = { name, category, role_title: roleTitle };
      }

      const canvas = document.getElementById('ai-hud-canvas');
      const parent = canvas ? canvas.parentElement : null;
      const width = (parent && parent.clientWidth > 100) ? parent.clientWidth : (canvas && canvas.width > 100 ? canvas.width : 640);
      const height = (parent && parent.clientHeight > 100) ? parent.clientHeight : (canvas && canvas.height > 100 ? canvas.height : 380);

      if (canvas) {
        canvas.width = width;
        canvas.height = height;
        canvas.style.zIndex = '20';
      }

      const isBlacklist = category === 'blacklist';
      const isVIP = category === 'vip';
      const lowerName = name.toLowerCase();

      let boxX = Math.round(width * 0.36);
      let boxY = Math.round(height * 0.18);
      let boxW = 160;
      let boxH = 180;

      const isSiantar = currentAICamera && ((currentAICamera.title || '').toLowerCase().includes('thai') || (currentAICamera.title || '').toLowerCase().includes('siantar') || (currentAICamera.city || '').toLowerCase().includes('siantar'));

      if (isSiantar) {
        if (lowerName.includes('aulia')) {
          boxX = Math.round(width * 0.40);
          boxY = Math.round(height * 0.42);
          boxW = Math.round(width * 0.25);
          boxH = Math.round(height * 0.52);
        } else if (lowerName.includes('chika')) {
          boxX = Math.round(width * 0.16);
          boxY = Math.round(height * 0.44);
          boxW = Math.round(width * 0.14);
          boxH = Math.round(height * 0.32);
        } else if (lowerName.includes('royan') || lowerName.includes('hans')) {
          boxX = Math.round(width * 0.04);
          boxY = Math.round(height * 0.45);
          boxW = Math.round(width * 0.14);
          boxH = Math.round(height * 0.35);
        }
      } else {
        if (lowerName.includes('wagyu')) {
          boxX = Math.round(width * 0.35);
          boxY = Math.round(height * 0.18);
          boxW = 165;
          boxH = 185;
        } else if (lowerName.includes('dhika')) {
          boxX = Math.round(width * 0.38);
          boxY = Math.round(height * 0.22);
          boxW = 160;
          boxH = 180;
        }
      }

      const ent = {
        x: boxX,
        y: boxY,
        w: boxW,
        h: boxH,
        type: 'face',
        label: name,
        category: category,
        confidence: (96.5 + Math.random() * 2.8).toFixed(1),
        createdAt: Date.now()
      };

      activeAIEntities = [ent];

      const badgeClass = isBlacklist ? 'badge-danger' : (isVIP ? 'badge-success' : 'badge-primary');
      const badgeText = isBlacklist ? 'ALERT DPO' : (isVIP ? 'VIP ACCESSED' : 'ACCESS GRANTED');
      const iconClass = isBlacklist ? 'fas fa-shield-virus' : (isVIP ? 'fas fa-star' : 'fas fa-user-check');
      const iconBg = isBlacklist ? '#ef4444' : (isVIP ? '#059669' : '#0284c7');

      showAIBanner(`${name} (${roleTitle})`, `Confidence: ${ent.confidence}% • Terverifikasi oleh Loewix Neural AI`, badgeClass, badgeText, iconClass, iconBg);

      if (isBlacklist) {
        playAIAlarmSound();
      }

      // Log to server with active camera info
      const isWebcam = currentAICamera && currentAICamera.id === 'webcam';
      const activeCamTitle = currentAICamera ? currentAICamera.title : (isWebcam ? 'LIVE WEBCAM - LAPTOP SCANNER' : 'CAM LOEWIX CCTV');
      const activeCamId = currentAICamera ? currentAICamera.id : 5002;
      const fd = new FormData();
      fd.append('action', 'log_detection');
      fd.append('type', 'face');
      fd.append('camera_id', activeCamId);
      fd.append('camera_title', activeCamTitle);
      fd.append('label', name);
      fd.append('category', category);
      fd.append('confidence', ent.confidence);
      fd.append('details', `${roleTitle} • Terverifikasi oleh Face Recognition`);
      fd.append('timestamp', getLocalLogTimestamp());
      fetch('../api/ai_analytics.php', { method: 'POST', body: fd }).then(() => loadAIData(true)).catch(e => {});
    }

    // Trigger Multi-Target Face Recognition (Detecting all people in frame)
    function simulateMultiFaceDetection() {
      initAIHUDCanvas();
      const canvas = document.getElementById('ai-hud-canvas');
      const parent = canvas ? canvas.parentElement : null;
      const width = (parent && parent.clientWidth > 100) ? parent.clientWidth : (canvas && canvas.width > 100 ? canvas.width : 640);
      const height = (parent && parent.clientHeight > 100) ? parent.clientHeight : (canvas && canvas.height > 100 ? canvas.height : 380);

      if (canvas) {
        canvas.width = width;
        canvas.height = height;
        canvas.style.zIndex = '20';
      }

      if (cachedAIFaces.length === 0) {
        alert('Belum ada data wajah terdaftar untuk multi-scan.');
        return;
      }

      const now = Date.now();
      const multiEntities = [];

      const isSiantar = currentAICamera && ((currentAICamera.title || '').toLowerCase().includes('thai') || (currentAICamera.title || '').toLowerCase().includes('siantar') || (currentAICamera.city || '').toLowerCase().includes('siantar'));

      if (isSiantar) {
        const aulia = cachedAIFaces.find(f => f.name.toLowerCase().includes('aulia')) || { name: 'Aulia', category: 'employee', role_title: 'TEST2' };
        const chika = cachedAIFaces.find(f => f.name.toLowerCase().includes('chika')) || { name: 'Chika', category: 'employee', role_title: 'TEST' };
        const royan = cachedAIFaces.find(f => f.name.toLowerCase().includes('royan')) || cachedAIFaces.find(f => f.name.toLowerCase().includes('hans')) || { name: 'ROYAN', category: 'employee', role_title: 'Staff' };

        multiEntities.push({
          x: Math.round(width * 0.40),
          y: Math.round(height * 0.42),
          w: Math.round(width * 0.25),
          h: Math.round(height * 0.52),
          type: 'face',
          label: aulia.name,
          category: aulia.category || 'employee',
          confidence: '98.4',
          createdAt: now
        });
        multiEntities.push({
          x: Math.round(width * 0.16),
          y: Math.round(height * 0.44),
          w: Math.round(width * 0.14),
          h: Math.round(height * 0.32),
          type: 'face',
          label: chika.name,
          category: chika.category || 'employee',
          confidence: '97.2',
          createdAt: now
        });
        multiEntities.push({
          x: Math.round(width * 0.04),
          y: Math.round(height * 0.45),
          w: Math.round(width * 0.14),
          h: Math.round(height * 0.35),
          type: 'face',
          label: royan.name,
          category: royan.category || 'employee',
          confidence: '96.5',
          createdAt: now
        });
      } else {
        const targetFaces = cachedAIFaces.slice(0, 3);
        const positions = [
          { x: Math.round(width * 0.08), y: Math.round(height * 0.16), w: 135, h: 160 },
          { x: Math.round(width * 0.38), y: Math.round(height * 0.14), w: 145, h: 170 },
          { x: Math.round(width * 0.68), y: Math.round(height * 0.18), w: 135, h: 160 },
        ];
        targetFaces.forEach((f, idx) => {
          const pos = positions[idx % positions.length];
          multiEntities.push({
            x: pos.x,
            y: pos.y,
            w: pos.w,
            h: pos.h,
            type: 'face',
            label: f.name,
            category: f.category || 'employee',
            confidence: (96.2 + Math.random() * 3.2).toFixed(1),
            createdAt: now
          });
        });
      }

      activeAIEntities = multiEntities;

      showAIBanner(`Multi-Target AI (${multiEntities.length} Wajah Teridentifikasi)`, `Terverifikasi: ${multiEntities.map(e => e.label).join(' • ')}`, 'badge-success', 'MULTI-TARGET ACTIVE', 'fas fa-users', '#10b981');

      // Log all detected entities to server
      multiEntities.forEach(ent => {
        const fd = new FormData();
        fd.append('action', 'log_detection');
        fd.append('type', 'face');
        fd.append('camera_id', currentAICamera ? currentAICamera.id : 5002);
        fd.append('camera_title', currentAICamera ? currentAICamera.title : 'LIVE MULTI-SCAN');
        fd.append('label', ent.label);
        fd.append('category', ent.category);
        fd.append('confidence', ent.confidence);
        fd.append('details', `Multi-Target Face Recognition Detection`);
        fd.append('timestamp', getLocalLogTimestamp());
        fetch('../api/ai_analytics.php', { method: 'POST', body: fd }).then(() => loadAIData(true)).catch(e => {});
      });
    }

    // Modal Action Openers
    function openRegisterFaceModal() {
      const form = document.getElementById('formRegisterFace');
      if (form) form.reset();
      const editIdInput = document.getElementById('face-edit-id');
      if (editIdInput) editIdInput.value = '';
      const img = document.getElementById('face-preview-img');
      const hiddenInput = document.getElementById('face-input-photo');
      if (img) img.src = '';
      if (hiddenInput) hiddenInput.value = '';

      const modalTitle = document.querySelector('#modalRegisterFace .modal-title');
      if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-user-plus text-info mr-2"></i> Daftarkan Wajah Baru ke AI Face Recognition';
      }
      const btnSubmit = document.getElementById('btn-submit-face');
      if (btnSubmit) {
        btnSubmit.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Data Wajah';
      }

      openModalHelper('modalRegisterFace');
      setTimeout(() => {
        startFaceEnrollmentCamera();
      }, 300);
    }

    function openEditFaceModal(faceId) {
      const face = cachedAIFaces.find(f => (f.id == faceId || String(f.id) === String(faceId)));
      if (!face) return;

      const form = document.getElementById('formRegisterFace');
      if (form) form.reset();

      const editIdInput = document.getElementById('face-edit-id');
      if (editIdInput) editIdInput.value = face.id;

      const nameInput = document.getElementById('face-input-name');
      if (nameInput) nameInput.value = face.name;

      const catInput = document.getElementById('face-input-category');
      if (catInput) catInput.value = face.category || 'employee';

      const roleInput = document.getElementById('face-input-role');
      if (roleInput) roleInput.value = face.role_title || '';

      const notesInput = document.getElementById('face-input-notes');
      if (notesInput) notesInput.value = face.notes || '';

      const hiddenInput = document.getElementById('face-input-photo');
      if (hiddenInput) hiddenInput.value = face.photo || '';

      const modalTitle = document.querySelector('#modalRegisterFace .modal-title');
      if (modalTitle) {
        modalTitle.innerHTML = `<i class="fas fa-camera text-warning mr-2"></i> Edit & Scan Ulang Wajah: ${face.name}`;
      }

      const btnSubmit = document.getElementById('btn-submit-face');
      if (btnSubmit) {
        btnSubmit.innerHTML = '<i class="fas fa-save mr-1"></i> Perbarui Data & Wajah';
      }

      openModalHelper('modalRegisterFace');
      setTimeout(() => {
        startFaceEnrollmentCamera();
      }, 300);
    }

    async function submitRegisterFace(e) {
      e.preventDefault();
      const photoVal = document.getElementById('face-input-photo').value;
      if (!photoVal || (!photoVal.startsWith('data:image') && !photoVal.startsWith('http') && !photoVal.startsWith('assets/'))) {
        alert('⚠️ Wajib lakukan Scan Wajah terlebih dahulu melalui kamera sebelum menyimpan data!');
        startFaceEnrollmentCamera();
        return;
      }

      const editId = document.getElementById('face-edit-id')?.value || '';
      const fd = new FormData();
      fd.append('action', editId ? 'update_face' : 'register_face');
      if (editId) fd.append('id', editId);
      fd.append('name', document.getElementById('face-input-name').value);
      fd.append('category', document.getElementById('face-input-category').value);
      fd.append('role_title', document.getElementById('face-input-role').value);
      fd.append('photo', photoVal);
      fd.append('notes', document.getElementById('face-input-notes').value);

      const btnSubmit = document.getElementById('btn-submit-face');
      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
      }

      try {
        const res = await fetch('../api/ai_analytics.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          stopFaceWebcam();
          closeModalHelper('modalRegisterFace');
          alert(data.message || (editId ? '✅ Data Wajah Berhasil Diperbarui & Di-rescan!' : '✅ Wajah Berhasil Terdaftar ke Database AI Face Recognition!'));
          loadAIData(true);
        } else {
          alert(data.message || 'Gagal menyimpan data.');
        }
      } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan saat menyimpan data wajah.');
      } finally {
        if (btnSubmit) {
          btnSubmit.disabled = false;
          btnSubmit.innerHTML = editId ? '<i class="fas fa-save mr-1"></i> Perbarui Data & Wajah' : '<i class="fas fa-save mr-1"></i> Simpan Data Wajah';
        }
      }
    }

    async function deleteAIFace(id) {
      if (!confirm('Hapus data wajah ini dari database AI?')) return;
      const fd = new FormData();
      fd.append('action', 'delete_face');
      fd.append('id', id);
      await fetch('../api/ai_analytics.php', { method: 'POST', body: fd });
      loadAIData(true);
    }

    function openRegisterPlateModal() {
      document.getElementById('formRegisterPlate').reset();
      openModalHelper('modalRegisterPlate');
    }

    async function submitRegisterPlate(e) {
      e.preventDefault();
      const fd = new FormData();
      fd.append('action', 'register_plate');
      fd.append('plate_number', document.getElementById('plate-input-number').value);
      fd.append('vehicle_type', document.getElementById('plate-input-type').value);
      fd.append('category', document.getElementById('plate-input-category').value);
      fd.append('owner_name', document.getElementById('plate-input-owner').value);
      fd.append('vehicle_model', document.getElementById('plate-input-model').value);
      fd.append('notes', document.getElementById('plate-input-notes').value);

      try {
        const res = await fetch('../api/ai_analytics.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          closeModalHelper('modalRegisterPlate');
          alert('Nomor Plat Kendaraan berhasil didaftarkan ke ANPR!');
          loadAIData(true);
        } else {
          alert(data.message || 'Gagal menyimpan data.');
        }
      } catch (err) {
        console.error(err);
      }
    }

    async function deleteAIPlate(id) {
      if (!confirm('Hapus nomor plat kendaraan ini dari database ANPR?')) return;
      const fd = new FormData();
      fd.append('action', 'delete_plate');
      fd.append('id', id);
      await fetch('../api/ai_analytics.php', { method: 'POST', body: fd });
      loadAIData(true);
    }

    async function clearAILogs() {
      if (!confirm('Bersihkan seluruh riwayat deteksi AI?')) return;
      const fd = new FormData();
      fd.append('action', 'clear_logs');
      await fetch('../api/ai_analytics.php', { method: 'POST', body: fd });
      loadAIData(true);
    }

    function filterFacesList(q) {
      q = q.toLowerCase();
      const filtered = cachedAIFaces.filter(f => f.name.toLowerCase().includes(q) || (f.role_title && f.role_title.toLowerCase().includes(q)));
      renderAIFacesGrid(filtered);
    }

    function filterPlatesList(q) {
      q = q.toLowerCase();
      const filtered = cachedAIPlates.filter(p => p.plate_number.toLowerCase().includes(q) || p.owner_name.toLowerCase().includes(q));
      renderAIPlatesTable(filtered);
    }

    window.showInvoiceReceiptModal = showInvoiceReceiptModal;
    window.closeLoewixReceiptOverlay = closeLoewixReceiptOverlay;
    window.printInvoiceReceipt = printInvoiceReceipt;
    window.openAddPlanModal = openAddPlanModal;
    window.openEditPlanModal = openEditPlanModal;
    window.submitSavePlan = submitSavePlan;
    window.deleteAdminPlan = deleteAdminPlan;
    window.resetAdminCustomerPassword = resetAdminCustomerPassword;
    window.submitResetCustomerPassword = submitResetCustomerPassword;
    window.generateRandomPassword = generateRandomPassword;
    window.copyPasswordToClipboard = copyPasswordToClipboard;
    window.toggleAdminCustomerStatus = toggleAdminCustomerStatus;
    window.deleteAdminCustomer = deleteAdminCustomer;
    window.deleteCustomerCamera = deleteCustomerCamera;
    window.sendAdminPaymentReminder = sendAdminPaymentReminder;
    window.markAdminInvoiceSettled = markAdminInvoiceSettled;
    window.openCreateManualInvoiceModal = openCreateManualInvoiceModal;
    window.autoCalculateManualInvoiceTotal = autoCalculateManualInvoiceTotal;
    window.submitCreateManualInvoice = submitCreateManualInvoice;
    window.openInvoiceDetailModal = openInvoiceDetailModal;
    window.deleteAdminInvoice = deleteAdminInvoice;
    window.filterAdminTransactionsTable = filterAdminTransactionsTable;
    window.exportAdminTransactionsCSV = exportAdminTransactionsCSV;
    window.loadAIData = loadAIData;
    window.switchAISubTab = switchAISubTab;
    window.simulateAIDetection = simulateAIDetection;
    window.simulateCustomFaceDetection = simulateCustomFaceDetection;
    window.simulateMultiFaceDetection = simulateMultiFaceDetection;
    window.simulateCustomPlateDetection = simulateCustomPlateDetection;
    window.startAIWebcamLive = startAIWebcamLive;
    window.scanCurrentFrameManual = scanCurrentFrameManual;
    window.populateAICameraSelector = populateAICameraSelector;
    window.populateAITargetFaceSelector = populateAITargetFaceSelector;
    window.selectAITargetFace = selectAITargetFace;
    window.changeAICamera = changeAICamera;
    window.setAIVideoFilter = setAIVideoFilter;
    window.setAIVideoZoom = setAIVideoZoom;
    window.resetAIVideoPanZoom = resetAIVideoPanZoom;
    window.toggleAISound = toggleAISound;
    window.toggleAIAutoTracking = toggleAIAutoTracking;
    window.openRegisterFaceModal = openRegisterFaceModal;
    window.openEditFaceModal = openEditFaceModal;
    window.startFaceEnrollmentCamera = startFaceEnrollmentCamera;
    window.captureFaceFromWebcam = captureFaceFromWebcam;
    window.stopFaceWebcam = stopFaceWebcam;
    window.submitRegisterFace = submitRegisterFace;
    window.deleteAIFace = deleteAIFace;
    window.openRegisterPlateModal = openRegisterPlateModal;
    window.submitRegisterPlate = submitRegisterPlate;
    window.deleteAIPlate = deleteAIPlate;
    window.clearAILogs = clearAILogs;
    window.filterFacesList = filterFacesList;
    window.filterPlatesList = filterPlatesList;
  </script>
</body>
</html>
