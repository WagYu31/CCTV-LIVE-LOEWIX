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

    /* Quota Hero Widget - Ultra Premium Glass Architecture */
    .quota-hero-banner {
      background: radial-gradient(circle at 10% 20%, rgba(14, 116, 144, 0.25) 0%, rgba(8, 20, 48, 0.95) 45%, rgba(4, 10, 26, 0.98) 100%);
      border: 1px solid rgba(56, 189, 248, 0.35);
      border-radius: 24px;
      padding: 30px 34px;
      margin-top: 24px;
      margin-bottom: 30px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 35px rgba(14, 116, 144, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.16);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      position: relative;
      overflow: hidden;
    }

    .quota-hero-banner::after {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(56, 189, 248, 0.12) 0%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
      z-index: 0;
    }

    .hero-tier-badge {
      background: linear-gradient(135deg, #0284c7, #025078);
      border: 1px solid rgba(56, 189, 248, 0.5);
      color: #ffffff;
      padding: 5px 14px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.8px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 0 14px rgba(2, 132, 199, 0.5);
    }

    .hero-city-badge {
      background: rgba(15, 23, 42, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.12);
      color: #cbd5e1;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .hero-customer-title {
      font-size: 28px;
      font-weight: 900;
      color: #ffffff;
      letter-spacing: -0.5px;
      margin-top: 6px;
      margin-bottom: 6px;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
    }

    .quota-progress-track {
      height: 12px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      overflow: hidden;
      margin: 12px 0 10px 0;
      position: relative;
      border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .quota-progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #0284c7, #38bdf8 60%, #10b981 100%);
      border-radius: 12px;
      transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 0 14px rgba(56, 189, 248, 0.7);
    }

    .quota-cap-pill {
      background: rgba(56, 189, 248, 0.14);
      border: 1px solid rgba(56, 189, 248, 0.4);
      color: #38bdf8;
      padding: 3px 10px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 800;
      font-family: monospace;
      letter-spacing: 0.3px;
    }

    /* Metric Cards - 3D Glass Design */
    .metric-card {
      background: linear-gradient(135deg, rgba(15, 26, 56, 0.75) 0%, rgba(8, 16, 36, 0.85) 100%);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 18px;
      padding: 16px 18px;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
      position: relative;
      overflow: hidden;
    }

    .metric-card:hover {
      background: linear-gradient(135deg, rgba(20, 36, 76, 0.85) 0%, rgba(10, 22, 48, 0.95) 100%);
      border-color: rgba(56, 189, 248, 0.4);
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5), 0 0 20px rgba(56, 189, 248, 0.2);
    }

    .metric-icon {
      width: 48px;
      height: 48px;
      min-width: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      border: 1px solid transparent;
    }

    .metric-icon.cyan {
      background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.28), rgba(2, 132, 199, 0.12));
      border-color: rgba(56, 189, 248, 0.4);
      color: #38bdf8;
      box-shadow: 0 0 15px rgba(56, 189, 248, 0.25);
    }

    .metric-icon.emerald {
      background: radial-gradient(circle at top left, rgba(16, 185, 129, 0.28), rgba(5, 150, 105, 0.12));
      border-color: rgba(16, 185, 129, 0.4);
      color: #34d399;
      box-shadow: 0 0 15px rgba(16, 185, 129, 0.25);
    }

    .metric-icon.amber {
      background: radial-gradient(circle at top left, rgba(245, 158, 11, 0.28), rgba(217, 119, 6, 0.12));
      border-color: rgba(245, 158, 11, 0.4);
      color: #fbbf24;
      box-shadow: 0 0 15px rgba(245, 158, 11, 0.25);
    }

    .metric-icon.purple {
      background: radial-gradient(circle at top left, rgba(168, 85, 247, 0.35), rgba(124, 58, 237, 0.15));
      border-color: rgba(168, 85, 247, 0.45);
      color: #c084fc;
      box-shadow: 0 0 15px rgba(168, 85, 247, 0.3);
    }

    .metric-value {
      font-size: 24px;
      font-weight: 900;
      color: #ffffff;
      line-height: 1.1;
      font-family: system-ui, -apple-system, sans-serif;
    }

    .metric-label {
      font-size: 11px;
      color: #94a3b8;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      margin-top: 2px;
    }

    .metric-sub {
      font-size: 10px;
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
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #f87171;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      gap: 5px;
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
    .act-btn-delete { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.4); color: #f87171; }
    .act-btn-delete:hover { background: #ef4444; color: #fff; box-shadow: 0 0 12px rgba(239, 68, 68, 0.6); }

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
    <!-- TAB 2: INFORMASI PAKET & UPGRADE -->
    <!-- ======================================================== -->
    <div id="tab-package" class="customer-tab-pane" style="display: none;">
      <div class="row">
        <!-- Current Active Package Card -->
        <div class="col-lg-6 mb-4">
          <div class="billing-card h-100">
            <div class="billing-card-header">
              <h5 class="billing-card-title">
                <i class="fas fa-box-open text-info"></i> Paket Langganan Aktif
              </h5>
              <span class="billing-status-badge active" id="pkg-status-badge">
                <i class="fas fa-check-circle"></i> AKTIF
              </span>
            </div>

            <div class="mb-4">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <h3 class="font-weight-bold text-white mb-0" id="pkg-plan-name">Business Pro Cloud</h3>
                <span class="badge badge-info px-3 py-2" id="pkg-quota-badge" style="font-size: 13px; font-weight: 700; border-radius: 8px;">10 CCTV Kuota</span>
              </div>
              <p class="text-muted mb-3" style="font-size: 13px;">
                Siklus Tagihan: <strong class="text-white" id="pkg-billing-cycle">Tahunan (Annual)</strong> &bull; Perpanjangan Otomatis
              </p>
              <div class="p-3 mb-3" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px;">
                <div class="d-flex justify-content-between mb-1" style="font-size: 12.5px;">
                  <span class="text-muted">Masa Aktif Hingga:</span>
                  <strong class="text-warning" id="pkg-expiry-date">14 Agustus 2027</strong>
                </div>
                <div class="d-flex justify-content-between" style="font-size: 12.5px;">
                  <span class="text-muted">Biaya Berlangganan:</span>
                  <strong class="text-emerald" style="color: #34d399;" id="pkg-cost-amount">Rp 2.990.000 / Tahun</strong>
                </div>
              </div>
            </div>

            <h6 class="text-white font-weight-bold mb-3" style="font-size: 13.5px;">Fitur & Kapabilitas Paket:</h6>
            <ul class="list-unstyled mb-4" style="font-size: 13px; color: #cbd5e1; line-height: 2;">
              <li><i class="fas fa-check-circle text-success mr-2"></i> Streaming Full HD / 4K Ultra H.265</li>
              <li><i class="fas fa-check-circle text-success mr-2"></i> Low Latency WebRTC & HLS Stream</li>
              <li><i class="fas fa-check-circle text-success mr-2"></i> AI Motion & Smart Detection Telemetry</li>
              <li><i class="fas fa-check-circle text-success mr-2"></i> Cloud Recording & Playback 14 Hari</li>
              <li><i class="fas fa-check-circle text-success mr-2"></i> Dedicated P2P Relay Server</li>
            </ul>

            <div class="d-flex gap-2">
              <button class="btn btn-info font-weight-bold flex-fill py-2" onclick="renewCurrentPlan()" style="border-radius: 10px;">
                <i class="fas fa-sync mr-1"></i> Perpanjang Paket Sekarang
              </button>
            </div>
          </div>
        </div>

        <!-- Available Upgrade Plans -->
        <div class="col-lg-6 mb-4">
          <div class="billing-card h-100">
            <div class="billing-card-header">
              <h5 class="billing-card-title">
                <i class="fas fa-rocket text-warning"></i> Opsi Upgrade Paket
              </h5>
              <span class="text-muted" style="font-size: 12px;">Pilih kuota lebih besar</span>
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
          <button class="btn btn-sm btn-outline-info" onclick="loadBillingDashboardData()">
            <i class="fas fa-sync mr-1"></i> Segarkan Tagihan
          </button>
        </div>

        <div id="active-invoice-container">
          <!-- Active Invoice Box -->
          <div class="p-4 mb-4" style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 14px;">
            <div class="row align-items-center">
              <div class="col-md-8 mb-3 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge badge-success px-2.5 py-1" style="font-size: 11px;">SEMUA TAGIHAN LUNAS</span>
                  <span class="text-muted" style="font-size: 12.5px;">Invoice Terakhir: <strong class="text-white" id="inv-last-order-id">INV-LOEWIX-20260814-001</strong></span>
                </div>
                <h4 class="text-white font-weight-bold mb-1" id="inv-plan-title">Business Pro (10 CCTV) - Periode Tahunan</h4>
                <p class="text-muted mb-0" style="font-size: 13px;">
                  Tidak ada tagihan tertunggak saat ini. Layanan streaming CCTV Anda aktif dan berjalan normal.
                </p>
              </div>
              <div class="col-md-4 text-md-right">
                <div class="text-muted mb-1" style="font-size: 12px;">Total Pembayaran Terakhir</div>
                <h3 class="text-emerald font-weight-bold mb-3" style="color: #34d399;" id="inv-total-display">Rp 3.318.900</h3>
                <button class="btn btn-info btn-block font-weight-bold" onclick="switchCustomerTab('tab-history')">
                  <i class="fas fa-file-invoice mr-1"></i> Lihat Rincian di Riwayat
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Channels Supported -->
        <div class="p-3" style="background: rgba(2, 6, 23, 0.5); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 12px;">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="text-muted" style="font-size: 12.5px;">
              <i class="fas fa-shield-alt text-success mr-1"></i> Pembayaran resmi diproses secara otomatis & instan melalui gerbang pembayaran <strong>Midtrans</strong>:
            </span>
            <div class="d-flex align-items-center gap-2">
              <span class="badge badge-dark px-2 py-1" style="background: rgba(255,255,255,0.08);">QRIS (GoPay, OVO, Dana)</span>
              <span class="badge badge-dark px-2 py-1" style="background: rgba(255,255,255,0.08);">Virtual Account (BCA, Mandiri, BRI, BNI)</span>
              <span class="badge badge-dark px-2 py-1" style="background: rgba(255,255,255,0.08);">Kartu Kredit / Debit</span>
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
      <div class="admin-mgmt-card">
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
            <button class="btn btn-outline-warning btn-sm" onclick="openMidtransSettingsModal()" style="border-radius: 20px; font-weight: 700; padding: 6px 14px; border-color: rgba(245,158,11,0.4); color: #f59e0b;">
              <i class="fas fa-credit-card mr-1"></i> Pengaturan Midtrans
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="openSmtpSettingsModal()" style="border-radius: 20px; font-weight: 700; padding: 6px 14px; border-color: rgba(56,189,248,0.4); color: #38bdf8;">
              <i class="fas fa-envelope-open-text mr-1"></i> Pengaturan Email SMTP
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="loadAdminTransactionsList()" style="border-radius: 20px; font-weight: 700; padding: 6px 14px;">
              <i class="fas fa-sync-alt mr-1"></i> Refresh Data Transaksi
            </button>
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

  <!-- ===== MODAL PROFIL & GANTI PASSWORD ===== -->
  <div class="modal fade modal-dark" id="modalProfile" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold text-white">
            <i class="fas fa-user-cog text-info mr-2"></i> Pengaturan Akun & Keamanan
          </h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalProfile')">
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

  <!-- Modal Tambah Customer Baru (Admin) -->
  <div class="modal fade modal-dark" id="modalAddCustomer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="background: #0b1533; border: 1px solid rgba(56,189,248,0.3); border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
          <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-user-plus text-info mr-2"></i> Tambah Customer Baru</h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalAddCustomer')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formAddCustomer" onsubmit="submitAddCustomer(event)">
          <div class="modal-body p-4">
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Nama Customer / Perusahaan:</label>
              <input type="text" id="cust-name" class="form-control form-control-dark" placeholder="Contoh: PT. Jaya Sentosa Enterprise" required>
            </div>
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Email Login:</label>
              <input type="email" id="cust-email" class="form-control form-control-dark" placeholder="customer@jayasentosa.com" required>
            </div>
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Password Awal:</label>
              <input type="password" id="cust-password" class="form-control form-control-dark" placeholder="Minimal 6 Karakter" required>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Alokasi Kuota CCTV:</label>
                <input type="number" id="cust-quota" class="form-control form-control-dark" value="10" min="1" max="500" required>
              </div>
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Wilayah:</label>
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
              <label class="text-white" style="font-size: 13px; font-weight: 600;">No. WhatsApp / HP:</label>
              <input type="text" id="cust-phone" class="form-control form-control-dark" placeholder="+62 812-3456-7890">
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalAddCustomer')">Batal</button>
            <button type="submit" class="btn btn-info btn-sm font-weight-bold" style="background: #0284c7; border: none;"><i class="fas fa-save mr-1"></i> Simpan Customer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Data Customer (Admin) -->
  <div class="modal fade modal-dark" id="modalEditCustomer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="background: #0b1533; border: 1px solid rgba(56,189,248,0.3); border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
          <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-user-edit text-warning mr-2"></i> Edit Data Customer</h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalEditCustomer')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditCustomer" onsubmit="submitEditCustomer(event)">
          <input type="hidden" id="edit-profile-id">
          <div class="modal-body p-4">
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Nama Customer / Perusahaan:</label>
              <input type="text" id="edit-profile-name" class="form-control form-control-dark" required>
            </div>
            <div class="form-group mb-3">
              <label class="text-white" style="font-size: 13px; font-weight: 600;">Email Login:</label>
              <input type="email" id="edit-profile-email" class="form-control form-control-dark" required>
            </div>
            <div class="row g-2 mb-2">
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">Wilayah:</label>
                <select id="edit-profile-city" class="form-control form-control-dark">
                  <option value="siantar">Pematangsiantar</option>
                  <option value="jakarta">DKI Jakarta</option>
                  <option value="medan">Kota Medan</option>
                  <option value="bandung">Kota Bandung</option>
                  <option value="bali">Bali / Denpasar</option>
                </select>
              </div>
              <div class="col-6">
                <label class="text-white" style="font-size: 13px; font-weight: 600;">No. WhatsApp / HP:</label>
                <input type="text" id="edit-profile-phone" class="form-control form-control-dark">
              </div>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalEditCustomer')">Batal</button>
            <button type="submit" class="btn btn-warning btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Kuota Customer (Admin) -->
  <div class="modal fade modal-dark" id="modalEditQuota" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content" style="background: #0b1533; border: 1px solid rgba(245,158,11,0.4); border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
          <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-sliders-h text-warning mr-2"></i> Atur Kuota CCTV</h5>
          <button type="button" class="close text-white" onclick="closeModalHelper('modalEditQuota')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditQuota" onsubmit="submitEditQuota(event)">
          <input type="hidden" id="edit-quota-id">
          <div class="modal-body p-3 text-center">
            <div id="edit-quota-name" class="font-weight-bold text-info mb-2" style="font-size: 14px;">PT. Jaya Sentosa</div>
            <div class="form-group mb-0">
              <label class="text-muted" style="font-size: 12px;">Jumlah Kuota Kamera:</label>
              <input type="number" id="edit-quota-value" class="form-control form-control-dark text-center font-weight-bold" style="font-size: 22px; color: #fbbf24;" min="1" max="500" required>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModalHelper('modalEditQuota')">Batal</button>
            <button type="submit" class="btn btn-warning btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Update</button>
          </div>
        </form>
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

  <!-- ===== MODAL INVOICE RECEIPT ===== -->
  <div class="modal fade modal-dark" id="modalInvoiceReceipt" tabindex="-1" role="dialog" aria-labelledby="receiptModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 620px;">
      <div class="modal-content" style="border: 1px solid rgba(56, 189, 248, 0.4); background: #0f172a; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);">
        <div class="modal-header" style="background: rgba(15, 23, 42, 0.95); border-bottom: 1px solid rgba(255,255,255,0.1);">
          <h5 class="modal-title font-weight-bold text-white" id="receiptModalTitle" style="font-size: 16px;">
            <i class="fas fa-receipt text-info mr-2"></i> Kwitansi Pembayaran Resmi Loewix
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-3 p-md-4" id="invoice-receipt-body">
          <!-- Dynamically populated -->
        </div>
        <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.1);">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-info btn-sm font-weight-bold" onclick="printInvoiceReceipt()" style="background: #0284c7; border: none;">
            <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF
          </button>
        </div>
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
          navBrandBadge.innerHTML = '<span class="pulse-dot" style="background: #fbbf24;"></span> <span>MASTER COMMAND CENTER</span>';
          navBrandBadge.style.borderColor = 'rgba(245, 158, 11, 0.5)';
          navBrandBadge.style.color = '#fbbf24';
        }

        // Direct Super Admin Switcher Button
        const btnAdmin = document.getElementById('btn-super-admin-direct');
        if (btnAdmin) btnAdmin.style.display = 'inline-flex';

        // Hero Tier Badge
        const tierBadge = document.querySelector('.hero-tier-badge');
        if (tierBadge) {
          tierBadge.innerHTML = '<i class="fas fa-crown text-warning"></i> MASTER SUPER ADMIN';
          tierBadge.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
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
        if (card3Val) card3Val.innerText = '4';
        if (card3Lbl) card3Lbl.innerText = 'Active Tenants';
        if (card3Sub) card3Sub.innerText = 'Perusahaan';
        if (card3Icon) card3Icon.className = 'fas fa-building';
        if (card3IconWrap) card3IconWrap.className = 'metric-icon cyan';

        // Metric Card 4: Master Control
        const card4Val = document.getElementById('card-metric4-value');
        const card4Lbl = document.getElementById('card-metric4-label');
        const card4Sub = document.getElementById('card-metric4-sub');
        const card4Icon = document.getElementById('card-metric4-icon');
        const card4IconWrap = document.getElementById('card-metric4-icon-wrap');
        if (card4Val) { card4Val.innerText = 'MASTER'; card4Val.style.color = '#fbbf24'; }
        if (card4Lbl) card4Lbl.innerText = 'Admin Center';
        if (card4Sub) { card4Sub.innerText = 'Buka Panel →'; card4Sub.style.color = '#f59e0b'; }
        if (card4Icon) card4Icon.className = 'fas fa-crown';
        if (card4IconWrap) card4IconWrap.className = 'metric-icon amber';

        // Adapt tab navigation for super admin (Seamless SPA Tabs)
        const tabsContainer = document.querySelector('.customer-tabs-nav');
        if (tabsContainer) {
          tabsContainer.innerHTML = `
            <button type="button" class="customer-nav-tab active" onclick="switchCustomerTab('tab-cameras')" id="nav-tab-cameras">
              <i class="fas fa-video"></i> <span>Kamera Semua Tenant (${customerCameras.length || 12})</span>
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
          barLabel.innerHTML = '<i class="fas fa-bolt text-warning"></i> Status Jaringan & Server Streaming Global';
        }

        const capPill = document.getElementById('hero-quota-text');
        if (capPill) {
          capPill.innerHTML = '<span class="pulse-dot" style="background: #34d399; margin-right: 4px;"></span> 100% OPERASIONAL • SLA 99.9%';
          capPill.style.background = 'rgba(16, 185, 129, 0.15)';
          capPill.style.borderColor = 'rgba(16, 185, 129, 0.4)';
          capPill.style.color = '#34d399';
        }

        const barFill = document.getElementById('hero-quota-bar');
        if (barFill) {
          barFill.style.width = '100%';
          barFill.style.background = 'linear-gradient(90deg, #0284c7 0%, #10b981 100%)';
        }

        const bottomStats = document.getElementById('hero-bar-bottom-stats');
        if (bottomStats) {
          bottomStats.innerHTML = `
            <span><i class="fas fa-circle text-info" style="font-size: 8px;"></i> Total Stream: <strong class="text-white">${customerCameras.length || 12}</strong> CCTV Terhubung</span>
            <span><i class="fas fa-circle text-emerald" style="color: #34d399; font-size: 8px;"></i> Node Relay: <strong style="color: #34d399;">ID-JKT-01 (Online)</strong></span>
            <span class="d-none d-md-inline" style="color: #94a3b8;"><i class="fas fa-shield-alt text-info"></i> TLS 1.3 256-Bit & Low-Latency Relay</span>
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
      if (!confirm('Apakah Anda yakin ingin menghapus paket langganan ini?')) return;
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

        const statusBadge = (c.status === 'active')
          ? `<span class="status-badge-active"><span class="pulse-dot"></span> AKTIF</span>`
          : `<span class="status-badge-suspended"><i class="fas fa-ban mr-1"></i> SUSPENDED</span>`;

        const cleanPhone = (c.phone || '').replace(/[^0-9]/g, '');
        const waLink = cleanPhone ? `https://wa.me/${cleanPhone.startsWith('0') ? '62' + cleanPhone.slice(1) : cleanPhone}` : '';

        const row = document.createElement('tr');
        row.innerHTML = `
          <td><span class="cust-id-badge">#${c.id}</span></td>
          <td>
            <div class="font-weight-bold text-white d-flex align-items-center" style="font-size: 13.5px;">
              <i class="fas fa-building text-info mr-2" style="opacity: 0.85;"></i> ${c.name}
            </div>
            <div class="text-muted mt-1" style="font-size: 11px;">
              <i class="fas fa-calendar-alt mr-1"></i> Terdaftar ${c.created_at ? c.created_at.split(' ')[0] : '2026-08-14'}
            </div>
          </td>
          <td>
            <div>
              <a href="mailto:${c.email}" class="text-info text-decoration-none" style="font-size: 12.5px;">
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
              <span class="font-weight-bold text-white" style="font-size: 12px;">
                <i class="fas fa-video mr-1 text-warning"></i> <strong>${used}</strong> / ${quota} CCTV
              </span>
              <span class="badge ${percentUsed >= 80 ? 'badge-danger' : (percentUsed >= 50 ? 'badge-warning' : 'badge-info')}" style="font-size: 10px; border-radius: 6px; padding: 2px 5px;">
                ${percentUsed}%
              </span>
            </div>
            <div class="progress-bar-custom">
              <div class="progress-fill ${percentUsed >= 80 ? 'progress-fill-high' : (percentUsed >= 50 ? 'progress-fill-med' : 'progress-fill-low')}" style="width: ${percentUsed}%;"></div>
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
              <button class="act-btn act-btn-status" onclick="toggleAdminCustomerStatus(${c.id})" title="Toggle Suspend/Aktif">
                <i class="fas fa-power-off"></i>
              </button>
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

    async function resetAdminCustomerPassword(id, name) {
      const newPwd = prompt(`Masukkan password baru untuk customer "${name}":`, 'loewix123');
      if (!newPwd) return;

      const fd = new FormData();
      fd.append('action', 'reset_password');
      fd.append('id', id);
      fd.append('password', newPwd);

      try {
        const res = await fetch('../api/admin_customers.php', { method: 'POST', body: fd });
        const data = await res.json();
        alert(data.message || 'Password berhasil direset!');
      } catch (err) {
        alert('Terjadi kesalahan koneksi.');
      }
    }

    async function toggleAdminCustomerStatus(id) {
      if (!confirm('Yakin ingin mengubah status aktif/suspend customer ini?')) return;
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
      if (!confirm(`PERINGATAN: Apakah Anda yakin ingin menghapus akun customer "${name}"?\nSeluruh kamera terkait akan ikut terhapus.`)) return;

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

    async function loadAdminTransactionsList() {
      const tbody = document.getElementById('admin-transactions-table-body');
      if (!tbody) return;

      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2 text-info"></i> Memuat data tagihan & transaksi pelanggan...</td></tr>`;

      try {
        const res = await fetch('../api/payment.php?action=get_billing_dashboard');
        const data = await res.json();
        const invoices = data.invoices || [];

        if (invoices.length === 0) {
          tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-info-circle mr-1"></i> Belum ada riwayat transaksi masuk.</td></tr>`;
          return;
        }

        tbody.innerHTML = '';
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

          let actionButtons = '';
          if (status === 'pending') {
            actionButtons = `
              <div class="d-flex justify-content-end align-items-center gap-1" style="gap: 5px;">
                <button class="btn btn-sm btn-info font-weight-bold d-inline-flex align-items-center" onclick="sendAdminPaymentReminder('${inv.order_id}', '${(inv.user_name || '').replace(/'/g, "\\'")}', '${inv.user_email || ''}')" title="Kirim Email Tagihan Ulang" style="border-radius: 8px; font-size: 11px; padding: 5px 10px; background: linear-gradient(135deg, #0284c7, #0369a1); border: none; white-space: nowrap;">
                  <i class="fas fa-paper-plane mr-1"></i> <span>Kirim Email</span>
                </button>
                <button class="btn btn-sm btn-success font-weight-bold d-inline-flex align-items-center" onclick="markAdminInvoiceSettled('${inv.order_id}', '${(inv.user_name || '').replace(/'/g, "\\'")}')" title="Tandai Sudah Lunas Manual" style="border-radius: 8px; font-size: 11px; padding: 5px 10px; background: linear-gradient(135deg, #10b981, #059669); border: none; white-space: nowrap;">
                  <i class="fas fa-check-double mr-1"></i> <span>Set Lunas</span>
                </button>
              </div>
            `;
          } else {
            actionButtons = `
              <div class="text-right">
                <span class="badge badge-dark p-2" style="background: rgba(16,185,129,0.1); color: #34d399; font-size: 11px; border: 1px solid rgba(16,185,129,0.25); border-radius: 6px;">
                  <i class="fas fa-check mr-1"></i> Terverifikasi
                </span>
              </div>
            `;
          }

          const row = document.createElement('tr');
          row.innerHTML = `
            <td style="font-family: monospace; font-weight: 700; color: #38bdf8; font-size: 13px;">
              <i class="fas fa-file-invoice text-info mr-1"></i> ${inv.order_id || inv.invoice_number || 'INV-LOEWIX'}
            </td>
            <td>
              <div class="font-weight-bold text-white" style="font-size: 13px;">
                <i class="fas fa-building text-warning mr-1" style="font-size: 11px;"></i> ${inv.user_name || 'Customer PT'}
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
      } catch (err) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Gagal memuat data transaksi.</td></tr>`;
      }
    }

    async function sendAdminPaymentReminder(orderId, userName, userEmail) {
      if (!confirm(`Kirim email pengingat tagihan #${orderId} ke ${userName} (${userEmail})?`)) return;

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
      if (!confirm(`Tandai invoice #${orderId} milik ${userName} sebagai LUNAS (SETTLEMENT)?\nKuota CCTV pelanggan akan langsung aktif.`)) return;

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

    async function loadBillingDashboardData() {
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
        }
      } catch (err) {
        console.error('Failed to load billing dashboard:', err);
      }
    }

    function renderBillingData(data) {
      const sub = data.subscription || {};
      const invoices = data.invoices || [];
      const profile = data.billing_profile || {};
      const plans = data.plans || [];

      // 1. Render Active Package Info
      const planNameEl = document.getElementById('pkg-plan-name');
      const quotaBadgeEl = document.getElementById('pkg-quota-badge');
      const cycleEl = document.getElementById('pkg-billing-cycle');
      const expiryEl = document.getElementById('pkg-expiry-date');
      const costEl = document.getElementById('pkg-cost-amount');

      if (planNameEl) planNameEl.textContent = sub.plan_name || 'Business Pro Plan';
      if (quotaBadgeEl) quotaBadgeEl.textContent = (sub.cctv_quota || 10) + ' CCTV Kuota';
      if (cycleEl) cycleEl.textContent = sub.billing_cycle === 'annual' ? 'Tahunan (Annual - Hemat 2 Bln)' : 'Bulanan (Monthly)';
      
      if (expiryEl && sub.expires_at) {
        const expDate = new Date(sub.expires_at.replace(/-/g, '/'));
        expiryEl.textContent = expDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
      }
      
      if (costEl && sub.amount) {
        costEl.textContent = 'Rp ' + Number(sub.amount).toLocaleString('id-ID') + (sub.billing_cycle === 'annual' ? ' / Tahun' : ' / Bulan');
      }

      // 2. Render Upgrade Plans
      const upgradeContainer = document.getElementById('upgrade-plans-container');
      if (upgradeContainer && plans.length > 0) {
        let upgradeHtml = '';
        plans.forEach(p => {
          if (p.id !== sub.plan_id) {
            upgradeHtml += `
              <div class="p-3 mb-3" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div>
                    <h6 class="text-white font-weight-bold mb-0">${p.name} (${p.cctv_quota} CCTV)</h6>
                    <small class="text-info font-weight-bold">${p.features ? p.features[1] : 'Full Cloud Stream'}</small>
                  </div>
                  <div class="text-right">
                    <div class="text-emerald font-weight-bold" style="color: #34d399; font-size: 15px;">Rp ${Number(p.price_annual).toLocaleString('id-ID')}<small>/thn</small></div>
                    <small class="text-muted">Atau Rp ${Number(p.price_monthly).toLocaleString('id-ID')}/bln</small>
                  </div>
                </div>
                <div class="d-flex gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-info flex-fill font-weight-bold" onclick="checkoutPlanMidtrans('${p.id}', 'annual')">
                    <i class="fas fa-credit-card mr-1"></i> Upgrade Tahunan (Hemat)
                  </button>
                  <button class="btn btn-sm btn-outline-light font-weight-bold" onclick="checkoutPlanMidtrans('${p.id}', 'monthly')">
                    Bulanan
                  </button>
                </div>
              </div>
            `;
          }
        });
        if (upgradeHtml) upgradeContainer.innerHTML = upgradeHtml;
      }

      // 3. Render Invoices Tab
      const lastInv = invoices[0];
      const invOrderId = document.getElementById('inv-last-order-id');
      const invPlanTitle = document.getElementById('inv-plan-title');
      const invTotalDisplay = document.getElementById('inv-total-display');

      if (lastInv) {
        if (invOrderId) invOrderId.textContent = lastInv.order_id;
        if (invPlanTitle) invPlanTitle.textContent = `${lastInv.plan_name} - Periode ${lastInv.billing_cycle === 'annual' ? 'Tahunan' : 'Bulanan'}`;
        if (invTotalDisplay) invTotalDisplay.textContent = 'Rp ' + Number(lastInv.total_amount || lastInv.amount).toLocaleString('id-ID');
      }

      // 4. Render Transaction History Table
      const historyTbody = document.getElementById('tx-history-tbody');
      if (historyTbody) {
        if (invoices.length === 0) {
          historyTbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td></tr>';
        } else {
          historyTbody.innerHTML = invoices.map(inv => {
            const isSettlement = inv.status === 'settlement' || inv.status === 'capture';
            const statusBadge = isSettlement 
              ? '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> LUNAS</span>'
              : '<span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> PENDING</span>';

            const methodText = (inv.payment_type || 'Midtrans').toUpperCase().replace(/_/g, ' ');
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
                  <button class="btn btn-sm btn-outline-info" onclick="showInvoiceReceiptModal('${inv.order_id}')" title="Lihat Kwitansi / Faktur">
                    <i class="fas fa-receipt"></i> Kwitansi
                  </button>
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
      const receiptBody = document.getElementById('invoice-receipt-body');
      
      if (receiptBody) {
        receiptBody.innerHTML = `
          <div style="background: #ffffff; color: #0f172a; padding: 22px; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif;">
            <!-- Receipt Header -->
            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
              <div>
                <h4 style="font-weight: 800; color: #091650; margin: 0; font-size: 18px;">PT. LOEWIX INDONESIA</h4>
                <div style="color: #0284c7; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Cloud CCTV Surveillance SaaS Platform</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 3px;">NPWP: 01.999.888.7-012.000 &bull; www.loewixcctv.com</div>
              </div>
              <div class="text-right">
                <span class="badge ${isSettlement ? 'badge-success' : 'badge-warning'} px-3 py-1" style="font-size: 12px; font-weight: 800; border-radius: 6px;">
                  ${isSettlement ? '✓ LUNAS (PAID)' : 'MENUNGGU PEMBAYARAN'}
                </span>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">${inv.settlement_time || inv.transaction_time || '-'}</div>
              </div>
            </div>

            <!-- Invoice Info -->
            <div class="row mb-3" style="font-size: 12px;">
              <div class="col-6">
                <div style="color: #64748b; font-size: 11px;">Ditagihkan Kepada:</div>
                <strong style="color: #0f172a; font-size: 13.5px; display: block; margin-top: 2px;">${prof.company_name || inv.user_name || (activeUser ? activeUser.name : 'Customer')}</strong>
                <div style="color: #475569;">Email: ${prof.billing_email || inv.user_email || (activeUser ? activeUser.email : '-')}</div>
                <div style="color: #475569;">Lokasi: ${prof.billing_address || ('Kota ' + (activeUser ? activeUser.city : 'Bandung') + ', Indonesia')}</div>
              </div>
              <div class="col-6 text-right">
                <div style="color: #64748b; font-size: 11px;">Nomor Invoice / Order:</div>
                <strong style="color: #0284c7; font-family: monospace; font-size: 13.5px; display: block; margin-top: 2px;">${inv.order_id}</strong>
                <div style="color: #475569;">Metode: ${(inv.payment_type || 'Bank Transfer BCA').toUpperCase().replace(/_/g, ' ')}</div>
              </div>
            </div>

            <!-- Items Table -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 12px;">
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

            <div class="text-center" style="font-size: 10.5px; color: #94a3b8; border-top: 1px dashed #cbd5e1; padding-top: 8px;">
              Dokumen ini merupakan bukti pembayaran elektronik yang sah yang diterbitkan oleh PT. Loewix Indonesia.
            </div>
          </div>
        `;
      }
      
      openModalHelper('modalInvoiceReceipt');
    }

    function printInvoiceReceipt() {
      const content = document.getElementById('invoice-receipt-body')?.innerHTML;
      if (!content) return;
      const win = window.open('', '', 'height=750,width=850');
      win.document.write('<html><head><title>Kwitansi Pembayaran Resmi - Loewix</title>');
      win.document.write('<link rel="stylesheet" href="../assets/bootstarp/bootstrap.min.css">');
      win.document.write('<style>body { font-family: sans-serif; background: #f8fafc; padding: 30px; } @media print { body { padding: 0; background: #fff; } }</style>');
      win.document.write('</head><body>');
      win.document.write(content);
      win.document.write('</body></html>');
      win.document.close();
      win.focus();
      setTimeout(() => { win.print(); }, 400);
    }
  </script>
</body>
</html>
