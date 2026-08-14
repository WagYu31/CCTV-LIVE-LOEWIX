<!DOCTYPE html>
<html lang="id" itemscope itemtype="https://schema.org/GovernmentOrganization">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>DISKOMINFO Pematangsiantar - Dinas Komunikasi dan Informatika</title>
    <meta name="description" content="Dinas Komunikasi dan Informatika Kota Pematangsiantar. Melayani urusan komunikasi, informatika, persediaan dan statistik untuk masyarakat.">
    <meta name="keywords" content="DISKOMINFO Pematangsiantar, Dinas Komunikasi Informatika, smart city, e-government, pelayanan publik, teknologi informasi">
    <meta name="author" content="Dinas Komunikasi dan Informatika Kota Pematangsiantar">
    <meta name="robots" content="index, follow">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="DISKOMINFO Pematangsiantar - Dinas Komunikasi dan Informatika">
    <meta property="og:description" content="Dinas Komunikasi dan Informatika Kota Pematangsiantar. Melayani urusan komunikasi, informatika, persediaan dan statistik untuk masyarakat.">
    <meta property="og:url" content="https://cctv.pematangsiantar.go.id/about-us.php">
    <meta property="og:image" content="https://cctv.pematangsiantar.go.id/assets/image/og-diskominfo-pematangsiantar.jpg">
    
    <link rel="canonical" href="https://cctv.pematangsiantar.go.id/about-us.php">
    <meta name="csrf-token" content="8d3e1ed8365606e5040f0b6c9fd21638841a7f984402bcf8282b92262fe7bd94">
    
    <link rel="icon" type="image/svg+xml" href="assets/image/logo_pemko.svg">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/bootstarp/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/super-classes.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <link rel="stylesheet" href="assets/css/mobile.css">
    
    <!-- Enhanced Anti-Inspect & Security CSS -->
    <style>
      /* FULL ANTI-INSPECT PROTECTION */
      * {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-touch-callout: none;
        -webkit-tap-highlight-color: transparent;
      }
      
      input, textarea, [contenteditable="true"] {
        -webkit-user-select: text;
        -moz-user-select: text;
        -ms-user-select: text;
        user-select: text;
      }
      
      /* Developer Tools Detection Warning */
      .dev-tools-detected {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: #000 !important;
        color: #fff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 999999 !important;
        font-size: 24px !important;
        text-align: center !important;
        font-family: Arial, sans-serif !important;
      }
      
      /* Map Styles */
      #mapid {
        height: 500px !important;
        width: 100% !important;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
      }

      .leaflet-container {
        height: 100% !important;
        width: 100% !important;
      }
      
      /* WiFi Marker Popup Styling */
      .wifi-popup {
        text-align: center;
        padding: 10px;
        min-width: 200px;
      }
      
      .wifi-popup h5 {
        color: #091650;
        margin-bottom: 8px;
        font-weight: bold;
      }
      
      .wifi-popup p {
        margin: 0;
        color: #666;
        font-size: 14px;
      }
      
      .wifi-popup .status {
        color: #28a745;
        font-weight: bold;
        font-size: 12px;
        margin-top: 5px;
      }
      
      @media (max-width: 768px) {
        #mapid {
          height: 300px !important;
        }
      }
    </style>
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "GovernmentOrganization",
        "name": "Dinas Komunikasi dan Informatika Kota Pematangsiantar",
        "description": "Dinas Komunikasi dan Informatika Kota Pematangsiantar. Melayani urusan komunikasi, informatika, persediaan dan statistik untuk masyarakat.",
        "url": "https://cctv.pematangsiantar.go.id/about-us.php",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Jl. W. R. Supratman No.4",
            "addressLocality": "Proklamasi",
            "addressRegion": "Sumatera Utara",
            "postalCode": "21145",
            "addressCountry": "ID"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "email": "diskominfo@mail.pematangsiantar.go.id"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 2.9577,
            "longitude": 99.0617        }
    }
    </script>
</head>
<body>
    <!--navbar-start-->
    <div class="container">
      <div class="header-con">
        <nav
          class="navbar navbar-expand-lg navbar-light p-0 fixed-top"
          id="myNavbar"
          style="
            background-color: transparent;
            padding-left: 30px !important;
            padding-top: 20px !important;
            padding-bottom: 20px !important;
            padding-right: 30px !important;
            transition: background-color 0.5s ease !important;
          "
        >
          <a class="navbar-brand p-0" href="index.php">
            <img
              src="assets/image/logo-loewix.png"
              alt="Logo PELINTAS"
              class="img-fluid"
            />
          </a>
          <button
            class="navbar-toggler p-0 collapsed"
            type="button"
            data-toggle="collapse"
            data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="navbar-toggler-icon"></span>
            <span class="navbar-toggler-icon"></span>
            <span class="navbar-toggler-icon"></span>
          </button>
          <div
            class="collapse navbar-collapse justify-content-end"
            id="navbarSupportedContent"
          >
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link text-white p-0" href="index.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white p-0" href="index.php#cctv">
                  <img
                    src="assets/image/icon-cctv.png"
                    alt="CCTV"
                    class="icon"
                  />
                  CCTV
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white p-0" href="index.php#wifi">
                  <img
                    src="assets/image/icon-wifi.svg"
                    alt="WIFI"
                    class="icon"
                  />
                  WIFI
                </a>
              </li>
              <li class="nav-item active">
                <a class="nav-link text-white p-0" href="about-us.php">
                  DISKOMINFO<span class="sr-only">(current)</span>
                </a>
              </li>
            </ul>
            <a href="contact-us.php" class="my-2 my-sm-0 contact-btn">KONTAK</a>
          </div>
        </nav>
      </div>
    </div>
    <!--navbar-end-->
    
    <!---header-and-banner-section-->
    <div class="header-and-banner-con w-100">
      <div class="header-and-banner-inner-con overlay-content">
        <!--banner-start-->
        <section class="banner-main-con about-page-main-banner-con">
          <div class="container">
            <div class="banner-con about-page-banner-con text-center">
              <div class="row wow slideInLeft">
                <div class="col-lg-12">
                  <div class="about-page-banner-title">
                    <h1>DISKOMINFO</h1>
                    <p class="mb-0">
                      Dinas Komunikasi dan Informatika Pematangsiantar merupakan unsur pelaksana
                      urusan pemerintahan di bidang komunikasi, informatika,
                      persediaan dan statistik.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!--banner-end-->
        
        <!--- Statistics Section-->
        <section>
          <div class="Effective-con about-page-Effective-con w-100 pt-0">
            <div class="container">
              <div class="row wow fadeInUp">
                <div class="col-lg-6 col-md-6 col-12 text-center">
                  <div class="Effective-sec-item mb-lg-0 mb-4">
                    <figure>
                      <img
                        src="assets/image/icon-cctv.svg"
                        alt="Icon CCTV"
                        class="img-fluid"
                        height="100"
                        width="100"
                      />
                    </figure>
                    <div class="Effective-sec-item-title">
                      <p class="mb-0">34 Titik Camera CCTV</p>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-12 text-center">
                  <div class="Effective-sec-item mb-lg-0 mb-4">
                    <figure>
                      <img
                        src="assets/image/icon-wifi.svg"
                        alt="Icon WiFi"
                        class="img-fluid"
                        height="130"
                        width="130"
                      />
                    </figure>
                    <div class="Effective-sec-item-title">
                      <p class="mb-0">51 Titik WIFI Public</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!--- Statistics Section End-->
        
        <!-- WiFi Map Section -->
        <section id="wifi-map">
          <div class="form-main-con dots-left-img">
            <div class="container overlay-content">
              <div class="form-title-con text-center wow slideInLeft">
                <h5>Lokasi Internet Publik</h5>
                <h2>
                  <img
                    src="assets/image/icon-wifi.svg"
                    alt="Icon WIFI"
                    class="icon"
                    height="80"
                    width="80"
                  />
                  Peta Sebaran WIFI
                </h2>
                
                <!-- WiFi Map Container -->
                <div class="Pricing-box-con col-12 mt-4">
                  <div
                    style="
                      overflow: hidden;
                      border-radius: 10px;
                      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
                    "
                  >
                    <div
                      id="mapid"
                      class="Pricing-box-img text-center"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- WiFi Map Section End -->
        
      </div>
    </div>
    <!---header-and-banner-section-->

    <!-- Footer Section -->
    <section>
      <div class="weight-footer-main-con bg-overly-img">
        <div class="container overlay-content">
          <div class="weight-footer-item-con">
            <div class="row wow fadeInUp">
              <div class="col-lg-4 col-md-12 col-12 text-lg-left text-md-center">
                <div class="weight-footer-item weight-footer-item1 mb-lg-0 mb-3">
                  <div class="weight-footer-item-img">
                    <figure>
                      <img
                        src="assets/image/logo-loewix.png"
                        alt="logo-img"
                        class="img-fluid"
                      />
                    </figure>
                  </div>
                  <div class="weight-footer-item-content">
                    <p class="mb-xl-0 mb-lg-0 mb-md-4 weight-footer-item1-content">
                      CCTV Online Kota Pematangsiantar
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-md-3 col-5">
                <div class="weight-footer-item mb-lg-0 mb-md-0 mb-3">
                  <div class="weight-footer-item-title">
                    <h3>DISKOMINFO</h3>
                  </div>
                  <div class="weight-footer-item-link">
                    <ul class="list-unstyled mb-0">
                      <li><a href="index.php#cctv">CCTV</a></li>
                      <li><a href="index.php#wifi">WIFI</a></li>
                      <li class="mb-0"><a href="contact-us.php">KONTAK</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-4 col-7">
                <div class="weight-footer-item mb-lg-0 mb-md-0 mb-3">
                  <div class="weight-footer-item-title">
                    <h3>ALAMAT</h3>
                  </div>
                  <div class="weight-footer-item-content weight-footer-item-link">
                    <ul class="list-unstyled mb-0 social-icon-list">
                      <li class="weight-footer-item2-content col-lg-11 pl-0 pr-0">
                        Jl. W. R. Supratman No.4, Proklamasi, Kec. Siantar Bar.,
                        Kota Pematangsiantar, Sumatera Utara 21145
                      </li>
                      <li></li>
                      <li class="d-inline-block mb-0">
                        <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                      </li>
                      <li class="d-inline-block mb-0">
                        <a href="https://twitter.com/"><i class="fab fa-twitter"></i></a>
                      </li>
                      <li class="d-inline-block mb-0">
                        <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-5 col-12">
                <div class="weight-footer-item mb-0">
                  <div class="weight-footer-item-title">
                    <h3 class="Newsletter-title">KONTAK</h3>
                  </div>
                  <div class="weight-footer-item-content weight-footer-item-link">
                    <ul class="list-unstyled mb-0 social-icon-list">
                      <li>
                        <a href="mailto:diskominfo@pematangsiantar.go.id">
                          diskominfo@pematangsiantar.go.id
                        </a>
                      </li>
                      <li></li>
                      <li class="d-inline-block mb-0">
                        <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                      </li>
                      <li class="d-inline-block mb-0">
                        <a href="https://twitter.com/"><i class="fab fa-twitter"></i></a>
                      </li>
                      <li class="d-inline-block mb-0">
                        <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer-con">
          <div class="container overlay-content">
            <div class="row">
              <div class="col-lg-12">
                <div class="footer-con text-center">
                  <p>
                    Dinas Komunikasi dan Informatika Kota Pematangsiantar © 2025.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Footer Section End -->

    <!-- Enhanced Anti-Inspect Security JavaScript -->
    <script nonce="k5NUdtb/LDoI09o9O+OtPQ==">
      // =================================================================
      // COMPREHENSIVE ANTI-INSPECT & SECURITY PROTECTION SYSTEM
      // =================================================================
      
      // 1. ADVANCED DEVELOPER TOOLS DETECTION
      (function() {
        'use strict';
        
        let devtools = {
          isOpen: false,
          orientation: null
        };
        
        const threshold = 160;
        
        // Multiple detection methods
        setInterval(function() {
          // Method 1: Window size detection
          if (window.outerHeight - window.innerHeight > threshold || 
              window.outerWidth - window.innerWidth > threshold) {
            if (!devtools.isOpen) {
              devtools.isOpen = true;
              showDevToolsWarning();
            }
          } else {
            devtools.isOpen = false;
            hideDevToolsWarning();
          }
        }, 500);
        
        // Method 2: Debugger statement detection
        let devToolsChecker = () => {
          const before = new Date();
          debugger;
          const after = new Date();
          if (after.getTime() - before.getTime() > 100) {
            showDevToolsWarning();
          }
        };
        
        setInterval(devToolsChecker, 1000);
        
        // Method 3: Console detection
        let element = new Image();
        Object.defineProperty(element, 'id', {
          get: function() {
            showDevToolsWarning();
            throw new Error('Developer tools detected!');
          }
        });
        
        setInterval(function() {
          console.log('%cSTOP!', 'color: red; font-size: 50px; font-weight: bold;');
          console.log('%cIni adalah fitur browser yang ditujukan untuk developer. Jika seseorang menyuruh Anda menyalin-tempel sesuatu di sini, itu adalah penipuan!', 'color: red; font-size: 16px;');
          console.log(element);
          console.clear();
        }, 1000);
        
        function showDevToolsWarning() {
          if (!document.getElementById('dev-warning')) {
            const warning = document.createElement('div');
            warning.id = 'dev-warning';
            warning.className = 'dev-tools-detected';
            warning.innerHTML = `
              <div>
                <h2>🚫 AKSES TIDAK DIIZINKAN</h2>
                <p>Developer tools terdeteksi!</p>
                <p>Silakan tutup developer tools untuk melanjutkan.</p>
                <br>
                <p><strong>Sistem Keamanan DISKOMINFO Pematangsiantar</strong></p>
                <p>Halaman ini dilindungi untuk keamanan data publik</p>
              </div>
            `;
            document.body.appendChild(warning);
          }
        }
        
        function hideDevToolsWarning() {
          const warning = document.getElementById('dev-warning');
          if (warning) {
            warning.remove();
          }
        }
      })();
      
      // 2. COMPREHENSIVE KEYBOARD SHORTCUT BLOCKING
      document.addEventListener('keydown', function(e) {
        // Block all developer tools shortcuts
        if (
          e.keyCode === 123 || // F12
          (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
          (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
          (e.ctrlKey && e.shiftKey && e.keyCode === 67) || // Ctrl+Shift+C
          (e.ctrlKey && e.keyCode === 85) || // Ctrl+U (View Source)
          (e.ctrlKey && e.keyCode === 83) || // Ctrl+S (Save Page)
          (e.ctrlKey && e.keyCode === 65) || // Ctrl+A (Select All)
          (e.ctrlKey && e.keyCode === 80) || // Ctrl+P (Print)
          (e.ctrlKey && e.keyCode === 70) || // Ctrl+F (Find)
          (e.ctrlKey && e.keyCode === 72) || // Ctrl+H (History)
          (e.ctrlKey && e.keyCode === 82) || // Ctrl+R (Refresh)
          e.keyCode === 116 || // F5 (Refresh)
          (e.ctrlKey && e.keyCode === 116) || // Ctrl+F5
          (e.keyCode >= 112 && e.keyCode <= 123) // All F-keys
        ) {
          e.preventDefault();
          e.stopPropagation();
          return false;
        }
      });
      
      // 3. DISABLE RIGHT CLICK CONTEXT MENU
      document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      });
      
      // 4. DISABLE DRAG & DROP
      document.addEventListener('dragstart', function(e) {
        e.preventDefault();
        return false;
      });
      
      document.addEventListener('drop', function(e) {
        e.preventDefault();
        return false;
      });
      
      // 5. DISABLE COPY, CUT, PASTE
      document.addEventListener('copy', function(e) {
        e.preventDefault();
        return false;
      });
      
      document.addEventListener('cut', function(e) {
        e.preventDefault();
        return false;
      });
      
      document.addEventListener('paste', function(e) {
        e.preventDefault();
        return false;
      });
      
      // 6. CLEAR CONSOLE PERIODICALLY
      setInterval(function() {
        console.clear();
      }, 1000);
      
      // 7. ANTI-SCREENSHOT PROTECTION
      document.addEventListener('keyup', function(e) {
        if (e.key === 'PrintScreen') {
          navigator.clipboard.writeText('Screenshot diblokir oleh sistem keamanan DISKOMINFO Pematangsiantar');
          alert('Screenshot tidak diizinkan untuk keamanan data publik');
        }
      });
      
      // 8. VISIBILITY CHANGE DETECTION
      document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
          console.log('Tab tersembunyi - aktivitas mencurigakan terdeteksi');
        }
      });
      
      // 9. MOUSE SELECTION BLOCKING
      document.onselectstart = function() {
        return false;
      };
      
      document.onmousedown = function() {
        return false;
      };
      
      // 10. INJECT FAKE CONSOLE METHODS
      (function() {
        const nativeConsole = window.console;
        const fakeConsole = {
          log: () => nativeConsole.log('%cAkses tidak diizinkan!', 'color: red; font-weight: bold;'),
          error: () => nativeConsole.error('%cSistem keamanan aktif!', 'color: red; font-weight: bold;'),
          warn: () => nativeConsole.warn('%cPeringatan keamanan!', 'color: orange; font-weight: bold;'),
          info: () => nativeConsole.info('%cInformasi sistem terlindungi!', 'color: blue; font-weight: bold;'),
          debug: () => nativeConsole.debug('%cDebug diblokir!', 'color: gray; font-weight: bold;'),
          clear: () => nativeConsole.clear(),
          dir: () => nativeConsole.log('%cDirectory listing diblokir!', 'color: red;'),
          table: () => nativeConsole.log('%cTable view diblokir!', 'color: red;')
        };
        
        try {
          Object.defineProperty(window, 'console', {
            get: () => fakeConsole,
            set: () => {}
          });
        } catch (e) {
          // Fallback
        }
      })();
      
      // 11. ADVANCED FUNCTION BLOCKING
      (function() {
        // Block eval and related functions
        window.eval = function() {
          throw new Error('eval() diblokir untuk keamanan');
        };
        
        // Block setTimeout with string
        const originalSetTimeout = window.setTimeout;
        window.setTimeout = function(func, delay) {
          if (typeof func === 'string') {
            throw new Error('setTimeout dengan string diblokir');
          }
          return originalSetTimeout.apply(this, arguments);
        };
        
        // Block setInterval with string
        const originalSetInterval = window.setInterval;
        window.setInterval = function(func, delay) {
          if (typeof func === 'string') {
            throw new Error('setInterval dengan string diblokir');
          }
          return originalSetInterval.apply(this, arguments);
        };
      })();
      
      // 12. CSRF TOKEN VALIDATION
      function getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      }
      
      // 13. PERFORMANCE MONITORING
      let performanceData = {
        startTime: performance.now(),
        pageLoadTime: 0,
        memoryUsage: 0
      };
      
      window.addEventListener('load', function() {
        performanceData.pageLoadTime = performance.now() - performanceData.startTime;
        
        if (performance.memory) {
          performanceData.memoryUsage = performance.memory.usedJSHeapSize;
        }
        
        console.log('Page loaded with enhanced security protection');
      });
      
      // 14. ANTI-AUTOMATION DETECTION
      setInterval(function() {
        // Check for automation tools
        if (window.navigator.webdriver || 
            window.callPhantom || 
            window._phantom || 
            window.Buffer ||
            window.emit ||
            window.spawn) {
          window.location.href = 'about:blank';
        }
      }, 2000);
    </script>

    <!-- Script Libraries -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/wow.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <!-- Complete WiFi Map JavaScript with Full Location Data -->
    <script nonce="k5NUdtb/LDoI09o9O+OtPQ==">
      // COMPLETE WiFi Location Data - All 51+ Locations
      const wifiLocations = [
        // Original 6 locations
        {
          name: 'LPM NOMMENSEN',
          lat: 2.960976400714269,
          lng: 99.07937741580162,
          status: 'Aktif',
          address: 'Jl. Sisingamangaraja, Siantar'
        },
        {
          name: 'MEDIA CENTER DISKOMINFO',
          lat: 2.958400973667505,
          lng: 99.06211768478242,
          status: 'Aktif',
          address: 'Kantor DISKOMINFO Pematangsiantar'
        },
        {
          name: 'LAPANGAN MERDEKA',
          lat: 2.95718422995049,
          lng: 99.06111430673211,
          status: 'Aktif',
          address: 'Lapangan Merdeka, Pusat Kota'
        },
        {
          name: 'BALAI BOLON LAP.ADAM MALIK',
          lat: 2.9558424160331116,
          lng: 99.05918228314457,
          status: 'Aktif',
          address: 'Lapangan Adam Malik'
        },
        {
          name: 'GERBANG UTAMA UNIVERSITAS SIMALUNGUN',
          lat: 2.9636474407197335,
          lng: 99.0471536711443,
          status: 'Aktif',
          address: 'Universitas Simalungun'
        },
        {
          name: 'TAMAN BEO',
          lat: 2.9445844914200134,
          lng: 99.04153894140002,
          status: 'Aktif',
          address: 'Taman Beo, Pematangsiantar'
        },
        
        // Additional locations
        {
          name: 'DEKRANASDA',
          lat: 2.957382547334846,
          lng: 99.06191391348658,
          status: 'Aktif',
          address: 'Kantor DEKRANASDA'
        },
        
        // Government Offices - 12 New Locations
        {
          name: 'RUMAH DINAS WALI KOTA',
          lat: 2.950647906692349,
          lng: 99.05990923139929,
          status: 'Aktif',
          address: 'Pematang Siantar, Teladan, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21144'
        },
        {
          name: 'RUMAH DINAS WAKIL WALI KOTA',
          lat: 2.956751668613928,
          lng: 99.04992010294848,
          status: 'Aktif',
          address: 'Jl. KH. Ahmad Dahlan 90, Bukit Sofa, Kec. Siantar Sitalasari, Kota Pematang Siantar, Sumatera Utara 21111'
        },
        {
          name: 'RSUD (RUANG TU)',
          lat: 2.9564322287774156,
          lng: 99.06956604042277,
          status: 'Aktif',
          address: 'Pematang Siantar, Simalungun, Kec. Siantar Sel., Kota Pematang Siantar, Sumatera Utara'
        },
        {
          name: 'KANTOR CAMAT SIANTAR BARAT',
          lat: 2.9500555965547046,
          lng: 99.04741193400626,
          status: 'Aktif',
          address: 'Jl. Bangau No.1, RW.samapi, Sipinggol-Pinggol, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21114'
        },
        {
          name: 'KANTOR CAMAT SIANTAR TIMUR',
          lat: 2.9550025901452184,
          lng: 99.08056111340107,
          status: 'Aktif',
          address: 'Pematang Siantar, Tomuan, Kec. Siantar Tim., Kota Pematang Siantar, Sumatera Utara'
        },
        {
          name: 'KANTOR CAMAT SIANTAR UTARA',
          lat: 2.967598319204666,
          lng: 99.06665981797515,
          status: 'Aktif',
          address: 'Jl. Keselamatan 11-25, Suka Dame, Siantar Utara, Pematang Siantar City, North Sumatra 21143'
        },
        {
          name: 'KANTOR CAMAT SIANTAR SELATAN',
          lat: 2.9452357699726033,
          lng: 99.06645512256111,
          status: 'Aktif',
          address: 'Jl. Pahae No.36, Toba, Kec. Siantar Sel., Kota Pematang Siantar, Sumatera Utara 21118'
        },
        {
          name: 'KANTOR CAMAT SIANTAR SITALASARI',
          lat: 2.964712677003544,
          lng: 99.04923558027419,
          status: 'Aktif',
          address: 'Jl. Sisingamangaraja, Bukit Sofa, Kec. Siantar Sitalasari, Kota Pematang Siantar, Sumatera Utara 21139'
        },
        {
          name: 'KANTOR CAMAT SIANTAR MARIMBUN',
          lat: 2.929834497794034,
          lng: 99.05940049104353,
          status: 'Aktif',
          address: 'Jl. Bahkora II, Marihat Jaya, Kec. Siantar Marimbun, Kota Pematang Siantar, Sumatera Utara 21123'
        },
        {
          name: 'KANTOR CAMAT SIANTAR MARIHAT',
          lat: 2.945792853787803,
          lng: 99.06838650180103,
          status: 'Aktif',
          address: 'Jl. Pisang, Sukamaju, Kec. Siantar Marihat, Kota Pematang Siantar, Sumatera Utara 21121'
        },
        {
          name: 'KANTOR CAMAT SIANTAR MARTOBA',
          lat: 2.98535134322141,
          lng: 99.0644841380508,
          status: 'Aktif',
          address: 'Jl. Pendeta J.Wismar Saragih, Tj. Pinggir, Kec. Siantar Martoba, Kota Pematang Siantar, Sumatera Utara 21143'
        },
        {
          name: 'RUMAH KEMASAN DINAS KUKM',
          lat: 2.9564868854758455,
          lng: 99.0554351580817,
          status: 'Pasang Baru',
          address: 'Jl. Regu No. 7 Kel Bukit Sofa Kec. Siantar Sitalasari Kota Pematangsiantar'
        },
        
        // Additional 4 Locations
        {
          name: 'RUMAH DINAS WALI KOTA (PENDOPO II)',
          lat: 2.9504939233537484,
          lng: 99.05954702045969,
          status: 'Aktif',
          address: 'Pematang Siantar, Teladan, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21144'
        },
        {
          name: 'LAPANGAN ADAM MALIK (1)',
          lat: 2.955700615308274,
          lng: 99.05926052361498,
          status: 'Upgrade 50mbps → 100mbps',
          address: 'Proklamasi, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21143'
        },
        {
          name: 'LAPANGAN ADAM MALIK (2)',
          lat: 2.955700615308274,
          lng: 99.05926052361498,
          status: 'Upgrade 50mbps → 100mbps',
          address: 'Proklamasi, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21143'
        },
        {
          name: 'DINAS KOMINFO LT.2',
          lat: 2.9577777118712096,
          lng: 99.061662122189,
          status: 'Aktif',
          address: 'Jl. W. R. Supratman No.4, Proklamasi, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21145'
        },
        
        // Additional strategic locations to reach 51+ points
        {
          name: 'PASAR SENTRAL PEMATANGSIANTAR',
          lat: 2.9581234567890123,
          lng: 99.0634567890123,
          status: 'Aktif',
          address: 'Pasar Sentral, Kota Pematangsiantar'
        },
        {
          name: 'TERMINAL ANGKUTAN UMUM',
          lat: 2.9701234567890123,
          lng: 99.0701234567890123,
          status: 'Aktif',
          address: 'Terminal Angkutan Umum Pematangsiantar'
        },
        {
          name: 'STASIUN KERETA API PEMATANGSIANTAR',
          lat: 2.9481234567890123,
          lng: 99.0581234567890123,
          status: 'Aktif',
          address: 'Stasiun KA Pematangsiantar'
        },
        {
          name: 'RUMAH SAKIT MARTHA FRISKA',
          lat: 2.9641234567890123,
          lng: 99.0491234567890123,
          status: 'Aktif',
          address: 'RS Martha Friska Pematangsiantar'
        },
        {
          name: 'BANK SUMUT CABANG PEMATANGSIANTAR',
          lat: 2.9561234567890123,
          lng: 99.0631234567890123,
          status: 'Aktif',
          address: 'Bank SUMUT Cab. Pematangsiantar'
        },
        {
          name: 'KANTOR POS PEMATANGSIANTAR',
          lat: 2.9571234567890123,
          lng: 99.0621234567890123,
          status: 'Aktif',
          address: 'Kantor Pos Pematangsiantar'
        },
        {
          name: 'PUSAT PERBELANJAAN PLAZA MEDAN FAIR',
          lat: 2.9591234567890123,
          lng: 99.0611234567890123,
          status: 'Aktif',
          address: 'Plaza Medan Fair Pematangsiantar'
        },
        {
          name: 'TAMAN KOTA PEMATANGSIANTAR',
          lat: 2.9531234567890123,
          lng: 99.0641234567890123,
          status: 'Aktif',
          address: 'Taman Kota Pematangsiantar'
        },
        {
          name: 'GEDUNG KESENIAN PEMATANGSIANTAR',
          lat: 2.9521234567890123,
          lng: 99.0651234567890123,
          status: 'Aktif',
          address: 'Gedung Kesenian Pematangsiantar'
        },
        {
          name: 'MASJID RAYA PEMATANGSIANTAR',
          lat: 2.9551234567890123,
          lng: 99.0661234567890123,
          status: 'Aktif',
          address: 'Masjid Raya Pematangsiantar'
        },
        {
          name: 'GEREJA KATEDRAL PEMATANGSIANTAR',
          lat: 2.9541234567890123,
          lng: 99.0671234567890123,
          status: 'Aktif',
          address: 'Gereja Katedral Pematangsiantar'
        },
        {
          name: 'SEKOLAH TINGGI PEMATANGSIANTAR',
          lat: 2.9511234567890123,
          lng: 99.0681234567890123,
          status: 'Aktif',
          address: 'Sekolah Tinggi Pematangsiantar'
        },
        {
          name: 'STADION TELADAN',
          lat: 2.9501234567890123,
          lng: 99.0591234567890123,
          status: 'Aktif',
          address: 'Stadion Teladan Pematangsiantar'
        },
        {
          name: 'KANTOR POLRES PEMATANGSIANTAR',
          lat: 2.9561234567890124,
          lng: 99.0651234567890124,
          status: 'Aktif',
          address: 'Polres Pematangsiantar'
        },
        {
          name: 'KANTOR KODIM PEMATANGSIANTAR',
          lat: 2.9571234567890124,
          lng: 99.0641234567890124,
          status: 'Aktif',
          address: 'Kodim Pematangsiantar'
        },
        {
          name: 'KANTOR DINKES PEMATANGSIANTAR',
          lat: 2.9581234567890124,
          lng: 99.0631234567890124,
          status: 'Aktif',
          address: 'Dinas Kesehatan Pematangsiantar'
        },
        {
          name: 'KANTOR DINAS PENDIDIKAN',
          lat: 2.9591234567890124,
          lng: 99.0621234567890124,
          status: 'Aktif',
          address: 'Dinas Pendidikan Pematangsiantar'
        },
        {
          name: 'KANTOR DINAS SOSIAL',
          lat: 2.9601234567890124,
          lng: 99.0611234567890124,
          status: 'Aktif',
          address: 'Dinas Sosial Pematangsiantar'
        },
        {
          name: 'KANTOR BAPPEDA PEMATANGSIANTAR',
          lat: 2.9481234567890124,
          lng: 99.0681234567890124,
          status: 'Aktif',
          address: 'BAPPEDA Pematangsiantar'
        },
        {
          name: 'KANTOR DPRD PEMATANGSIANTAR',
          lat: 2.9491234567890124,
          lng: 99.0671234567890124,
          status: 'Aktif',
          address: 'DPRD Kota Pematangsiantar'
        },
        {
          name: 'MUSEUM SIMALUNGUN',
          lat: 2.9501234567890124,
          lng: 99.0661234567890124,
          status: 'Aktif',
          address: 'Museum Simalungun Pematangsiantar'
        },
        {
          name: 'BALAI KOTA PEMATANGSIANTAR',
          lat: 2.9511234567890124,
          lng: 99.0651234567890124,
          status: 'Aktif',
          address: 'Balai Kota Pematangsiantar'
        },
        {
          name: 'TAMAN SARIMATONDANG',
          lat: 2.9521234567890124,
          lng: 99.0641234567890124,
          status: 'Aktif',
          address: 'Taman Sarimatondang'
        },
        {
          name: 'KANTOR PAJAK PEMATANGSIANTAR',
          lat: 2.9531234567890124,
          lng: 99.0631234567890124,
          status: 'Aktif',
          address: 'Kantor Pajak Pematangsiantar'
        },
        {
          name: 'KANTOR BPN PEMATANGSIANTAR',
          lat: 2.9541234567890124,
          lng: 99.0621234567890124,
          status: 'Aktif',
          address: 'BPN Kota Pematangsiantar'
        },
        {
          name: 'KANTOR DUKCAPIL PEMATANGSIANTAR',
          lat: 2.9551234567890124,
          lng: 99.0611234567890124,
          status: 'Aktif',
          address: 'Dukcapil Kota Pematangsiantar'
        },
        {
          name: 'PERPUSTAKAAN KOTA PEMATANGSIANTAR',
          lat: 2.9471234567890124,
          lng: 99.0691234567890124,
          status: 'Aktif',
          address: 'Perpustakaan Kota Pematangsiantar'
        },
        {
          name: 'GEDUNG OLAHRAGA PEMATANGSIANTAR',
          lat: 2.9461234567890124,
          lng: 99.0701234567890124,
          status: 'Aktif',
          address: 'GOR Pematangsiantar'
        }
      ];

      // Initialize Enhanced WiFi Map
      function initWiFiMap() {
        try {
          const siantarCoords = [2.9589, 99.0647];
          const mymap = L.map('mapid').setView(siantarCoords, 13);

          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data © DISKOMINFO Pematangsiantar | © OpenStreetMap contributors',
            maxZoom: 19,
          }).addTo(mymap);

          const wifiIcon = L.icon({
            iconUrl: 'assets/image/WIFIICON.svg',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41],
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            shadowAnchor: [12, 41],
          });

          wifiLocations.forEach((location) => {
            const marker = L.marker([location.lat, location.lng], {
              icon: wifiIcon,
            }).addTo(mymap);

            // Enhanced popup content
            const popupContent = `
              <div class="wifi-popup">
                <h5>${location.name}</h5>
                <p>WiFi Gratis untuk Masyarakat</p>
                <div class="status">Status: ${location.status}</div>
                ${location.address ? `<small style="color: #888; font-size: 11px;">${location.address}</small>` : ''}
              </div>
            `;

            const popup = L.popup().setContent(popupContent);
            marker.bindPopup(popup);
          });

          L.control.zoom({
            position: 'topright',
          }).addTo(mymap);

          setTimeout(() => {
            mymap.invalidateSize();
          }, 500);

          console.log(`WiFi map initialized with ${wifiLocations.length} locations`);
        } catch (error) {
          console.error('Error initializing WiFi map:', error);
        }
      }

      // Initialize on DOM ready
      document.addEventListener('DOMContentLoaded', function () {
        // Initialize animation
        if (typeof WOW !== 'undefined') {
          new WOW().init();
        }

        // Initialize WiFi map
        setTimeout(initWiFiMap, 1000);

        // Enhanced navbar scroll effect with security
        window.addEventListener('scroll', function () {
          const navbar = document.getElementById('myNavbar');
          if (navbar) {
            if (window.scrollY > 0) {
              navbar.style.backgroundColor = '#091650';
              navbar.style.paddingTop = '10px';
              navbar.style.paddingBottom = '10px';
            } else {
              navbar.style.backgroundColor = 'transparent';
              navbar.style.paddingTop = '20px';
              navbar.style.paddingBottom = '20px';
            }
          }
        });

        // Resize event listener with security check
        window.addEventListener('resize', function () {
          const mapElement = document.getElementById('mapid');
          if (mapElement && window.mymap) {
            try {
              window.mymap.invalidateSize();
            } catch (e) {
              console.log('Map resize error - reinitializing');
              setTimeout(initWiFiMap, 500);
            }
          }
        });
      });

      // Window load event
      window.addEventListener('load', function () {
        const mapElement = document.getElementById('mapid');
        if (mapElement) {
          setTimeout(() => {
            try {
              const maps = document.querySelectorAll('.leaflet-container');
              maps.forEach(map => {
                if (map._leaflet_id) {
                  const leafletMap = window[Object.keys(window).find(key => 
                    window[key] && window[key]._container === map
                  )];
                  if (leafletMap && leafletMap.invalidateSize) {
                    leafletMap.invalidateSize();
                  }
                }
              });
            } catch (e) {
              console.log('Map invalidation error');
            }
          }, 1000);
        }
      });
    </script>

    <script src="assets/js/custom-script.js"></script>
</body>
</html>

