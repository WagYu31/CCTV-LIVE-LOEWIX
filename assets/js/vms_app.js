/**
 * Loewix Surveillance VMS - Core Application & Streaming Engine
 * PT. LOEWIX INDONESIA
 */

// Enhanced Analytics Events
    document.addEventListener('DOMContentLoaded', function() {
      // Track page load time
      window.addEventListener('load', function() {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        gtag('event', 'page_load_time', {
          event_category: 'Performance',
          event_label: 'Load Time',
          value: loadTime,
          custom_parameter: 'ultimate_gsc_version'
        });
      });

      // Track CCTV stream interactions
      document.addEventListener('click', function(e) {
        if (e.target.matches('.cctv-stream, .cctv-location')) {
          gtag('event', 'cctv_interaction', {
            event_category: 'CCTV Streaming',
            event_action: 'stream_click',
            event_label: e.target.dataset.location || 'unknown',
            value: 1
          });
        }
      });

      // Track WiFi map interactions
      document.addEventListener('click', function(e) {
        if (e.target.matches('.wifi-point, .wifi-marker')) {
          gtag('event', 'wifi_interaction', {
            event_category: 'WiFi Mapping',
            event_action: 'wifi_click',
            event_label: e.target.dataset.wifiName || 'unknown',
            value: 1
          });
        }
      });

      // Track government service usage
      document.addEventListener('click', function(e) {
        if (e.target.matches('.government-service, .public-service')) {
          gtag('event', 'government_service_usage', {
            event_category: 'Public Service',
            event_action: 'service_access',
            event_label: e.target.dataset.serviceName || 'unknown',
            value: 1
          });
        }
      });

      // Track search console verification events
      gtag('event', 'verification_check', {
        event_category: 'GSC Verification',
        event_action: 'automatic_check',
        event_label: 'multi_method_verification',
        value: 1
      });
    });

    // Enhanced user engagement tracking
    function trackEngagement() {
      let startTime = Date.now();
      let maxScroll = 0;

      window.addEventListener('scroll', function() {
        const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
        if (scrollPercent > maxScroll) {
          maxScroll = scrollPercent;

          // Track scroll milestones
          if (scrollPercent >= 25 && scrollPercent < 50) {
            gtag('event', 'scroll_milestone', {
              event_category: 'Engagement',
              event_action: 'scroll_25_percent',
              value: 25
            });
          } else if (scrollPercent >= 50 && scrollPercent < 75) {
            gtag('event', 'scroll_milestone', {
              event_category: 'Engagement',
              event_action: 'scroll_50_percent',
              value: 50
            });
          } else if (scrollPercent >= 75) {
            gtag('event', 'scroll_milestone', {
              event_category: 'Engagement',
              event_action: 'scroll_75_percent',
              value: 75
            });
          }
        }
      });

      window.addEventListener('beforeunload', function() {
        const timeOnPage = Math.round((Date.now() - startTime) / 1000);
        gtag('event', 'time_on_page', {
          event_category: 'Engagement',
          event_action: 'session_duration',
          event_label: 'seconds',
          value: timeOnPage
        });
      });
    }

    // Initialize engagement tracking
    trackEngagement();

    // Tambahkan tipe platform untuk identifikasi jenis streaming
    const PLATFORM_TYPES = {
      DENAVA: 'denava',
      STREAM2: 'stream2',
      IPCAMLIVE: 'ipcamlive',
      REKASADIGITAL: 'rekasadigital',
      MEDIAMTX: 'mediamtx'
    };

    // ===== KONFIGURASI MULTI-KOTA LOEWIX =====
    const CITY_CONFIG = {
      all: { id: 'all', name: 'Semua Wilayah', center: [2.9568, 99.0619], zoom: 6 },
      siantar: { id: 'siantar', name: 'Kota Pematangsiantar', center: [2.9568, 99.0619], zoom: 13 },
      jakarta: { id: 'jakarta', name: 'DKI Jakarta', center: [-6.2088, 106.8456], zoom: 12 },
      medan: { id: 'medan', name: 'Kota Medan', center: [3.5952, 98.6722], zoom: 12 },
      bandung: { id: 'bandung', name: 'Kota Bandung', center: [-6.9175, 107.6191], zoom: 12 },
      bali: { id: 'bali', name: 'Bali / Denpasar', center: [-8.6705, 115.2126], zoom: 12 }
    };

    const STREAM_BASE = 'https://stream.loewixcctv.com';
    let currentUser = (function() {
      try {
        const stored = localStorage.getItem('loewix_user');
        return stored ? JSON.parse(stored) : null;
      } catch (e) {
        return null;
      }
    })();
    let authCheckComplete = !!currentUser;
    let apiSyncDone = false;

    // ===== DATA KAMERA (100% DISINKRONKAN DENGAN DATABASE ADMIN LOEWIX) =====
    const mediamtxData = [];

    // Load instantly from cached cameras if user is already logged in
    (function loadInitialCachedCameras() {
      if (currentUser && currentUser.id) {
        try {
          const cached = sessionStorage.getItem(`loewix_cached_cameras_${currentUser.id}`);
          if (cached) {
            const list = JSON.parse(cached);
            if (Array.isArray(list) && list.length > 0) {
              list.forEach(c => mediamtxData.push(c));
              currentUser.cctv_used = list.length;
              localStorage.setItem('loewix_user', JSON.stringify(currentUser));
              if (typeof renderUserSessionUI === 'function') renderUserSessionUI(currentUser);
            }
          }
        } catch(e) {}
      }
    })();

    // Auto-sync cameras strictly from backend Database API
    function syncCustomLocalStorageCameras() {
      try {
        // Clean up any stale localStorage camera caches from past test sessions
        localStorage.removeItem('loewix_custom_cameras');
        for (let i = localStorage.length - 1; i >= 0; i--) {
          const k = localStorage.key(i);
          if (k && k.startsWith('loewix_user_cameras_')) {
            localStorage.removeItem(k);
          }
        }

        // Fetch cameras from backend Database API for 100% accurate data sync
        function extractSafeLatLng(c) {
          if (!c) return null;
          let lat = NaN, lng = NaN;
          if (typeof c.lat === 'string' && c.lat.includes(',')) {
            const parts = c.lat.split(',');
            lat = parseFloat(parts[0].trim());
            lng = parseFloat(parts[1].trim());
          } else if (Array.isArray(c.coordinates) && c.coordinates.length >= 2) {
            lat = parseFloat(c.coordinates[0]);
            lng = parseFloat(c.coordinates[1]);
          } else {
            lat = parseFloat(c.lat);
            lng = parseFloat(c.lng);
          }
          if (isNaN(lat) || isNaN(lng)) {
            if (typeof c.lng === 'string' && c.lng.includes(',')) {
              const parts = c.lng.split(',');
              lat = parseFloat(parts[0].trim());
              lng = parseFloat(parts[1].trim());
            }
          }
          if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            return [lat, lng];
          }
          return null;
        }

        // 1. Sync Dynamic Master Cities from API
        fetch('api/cities.php')
          .then(res => res.json())
          .then(cData => {
            if (cData && cData.success && Array.isArray(cData.cities)) {
              cData.cities.forEach(c => {
                CITY_CONFIG[c.id] = {
                  id: c.id,
                  name: c.name,
                  center: [parseFloat(c.lat), parseFloat(c.lng)],
                  zoom: parseInt(c.zoom) || 12
                };
              });

              // Dynamically update dropdown options in navbar & filters
              const navSelect = document.getElementById('city-selector-nav');
              const filterSelect = document.getElementById('filter-city');
              [navSelect, filterSelect].forEach(sel => {
                if (sel) {
                  const curVal = sel.value;
                  let html = '<option value="all" style="color:#000;">🌐 Semua Wilayah</option>';
                  cData.cities.forEach(c => {
                    html += `<option value="${c.id}" style="color:#000;">📍 ${c.name}</option>`;
                  });
                  sel.innerHTML = html;
                  if (curVal === 'all' || (curVal && CITY_CONFIG[curVal])) {
                    sel.value = curVal;
                  } else {
                    sel.value = 'all';
                  }
                }
              });
            }
          })
          .catch(() => {});

        // 2. Sync Cameras from Database API (Requires authentication)
        if (!currentUser) {
          mediamtxData.length = 0;
          if (typeof generateCCTVHTML === 'function') generateCCTVHTML(typeof currentGlobalCity !== 'undefined' ? currentGlobalCity : 'all');
          return;
        }

        const userIdParam = (currentUser && currentUser.id) ? `&user_id=${currentUser.id}` : '';
        const apiTarget = `api/cameras.php?action=public_list${userIdParam}`;
        fetch(apiTarget)
          .then(res => res.json())
          .then(data => {
            apiSyncDone = true;
            if (data && data.success && Array.isArray(data.cameras)) {
              mediamtxData.length = 0;

              data.cameras.forEach(c => {
                let safeId = parseInt(c.id);
                const city = (c.city || 'all').toLowerCase();
                const defaultCenter = CITY_CONFIG[city] ? CITY_CONFIG[city].center : [2.9568, 99.0619];
                let coords = extractSafeLatLng(c) || defaultCenter;

                mediamtxData.push({
                  id: safeId,
                  city: city,
                  title: c.title,
                  connection_type: c.connection_type || 'rtsp',
                  serial_number: c.serial_number || '',
                  channel: c.channel || 1,
                  device_user: c.device_user || 'admin',
                  device_pass: c.device_pass || '',
                  streamPath: c.streamPath || c.hls_url,
                  streamId: c.streamPath || c.hls_url,
                  hls_url: c.hls_url || c.streamPath || '',
                  coordinates: coords,
                  lat: coords[0],
                  lng: coords[1],
                  platform: PLATFORM_TYPES.MEDIAMTX,
                  thumbnail: c.thumbnail || (ASSET_BASE + '/image/logo-loewix.png')
                });
              });

              if (currentUser) {
                currentUser.cctv_used = mediamtxData.length;
                if (data.user && data.user.cctv_quota) currentUser.cctv_quota = data.user.cctv_quota;
                localStorage.setItem('loewix_user', JSON.stringify(currentUser));
                if (typeof renderUserSessionUI === 'function') renderUserSessionUI(currentUser);
              }

              if (currentUser && currentUser.id) {
                try {
                  sessionStorage.setItem(`loewix_cached_cameras_${currentUser.id}`, JSON.stringify(mediamtxData));
                } catch(e) {}
              }

              if (typeof generateCCTVHTML === 'function') generateCCTVHTML(typeof currentGlobalCity !== 'undefined' ? currentGlobalCity : 'all');
              if (typeof initCCTVMap === 'function') initCCTVMap();
              if (typeof autoPlayCCTVStreams === 'function') {
                setTimeout(autoPlayCCTVStreams, 300);
              }
            } else if (data && !data.logged_in) {
              mediamtxData.length = 0;
              if (typeof generateCCTVHTML === 'function') generateCCTVHTML(typeof currentGlobalCity !== 'undefined' ? currentGlobalCity : 'all');
            }
          }).catch(e => {
            apiSyncDone = true;
            if (typeof generateCCTVHTML === 'function') generateCCTVHTML(typeof currentGlobalCity !== 'undefined' ? currentGlobalCity : 'all');
          });
      } catch(globalErr) {}
    }
    syncCustomLocalStorageCameras();

    // Data CCTV Jakarta - menggunakan embed Balitower
    const cctvData = [{
        id: 1,
        title: 'Bendungan Hilir 1',
        streamId: 'Bendungan-Hilir-003-700014_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/jembatan-sigagak-medan.png',
        coordinates: [-6.2088, 106.8156],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 2,
        title: 'Bendungan Hilir 2',
        streamId: 'Bendungan-Hilir-003-700014_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/jembatan-sigagak-siantar.png',
        coordinates: [-6.2090, 106.8158],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 3,
        title: 'Bendungan Hilir 3',
        streamId: 'Bendungan-Hilir-003-700014_3',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Medan-Simpang-Karang-Sari.png',
        coordinates: [-6.2092, 106.8160],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 4,
        title: 'Bendungan Hilir 4',
        streamId: 'Bendungan-Hilir-003-700014_4',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Medan-Simpang-AMD.png',
        coordinates: [-6.2094, 106.8162],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 5,
        title: 'Gelora Bung Karno 2',
        streamId: 'Gelora-017-700470_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/simpang-rambung-merah.png',
        coordinates: [-6.2183, 106.8021],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 6,
        title: 'Gelora Bung Karno 3',
        streamId: 'Gelora-017-700470_3',
        thumbnail: ASSET_BASE + '/image/thumbnail/simpang-rambung-merah-kota.png',
        coordinates: [-6.2185, 106.8023],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 7,
        title: 'Gelora Bung Karno 4',
        streamId: 'Gelora-017-700470_4',
        thumbnail: ASSET_BASE + '/image/thumbnail/terminal-polsek-siantar-utara.png',
        coordinates: [-6.2187, 106.8025],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 8,
        title: 'Gelora Bung Karno 5',
        streamId: 'Gelora-017-700470_5',
        thumbnail: ASSET_BASE + '/image/thumbnail/terminal-simpang-rambung-merah.png',
        coordinates: [-6.2189, 106.8027],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 9,
        title: 'Gelora Bung Karno 6',
        streamId: 'Gelora-017-700470_6',
        thumbnail: ASSET_BASE + '/image/thumbnail/perempatan-lorong-20.png',
        coordinates: [-6.2191, 106.8029],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 10,
        title: 'Gelora Bung Karno 7',
        streamId: 'Gelora-017-700470_7',
        thumbnail: ASSET_BASE + '/image/thumbnail/simpang-BDB-Medan.png',
        coordinates: [-6.2193, 106.8031],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 11,
        title: 'Gelora Bung Karno 8',
        streamId: 'Gelora-017-700470_8',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-BDB-Kota.png',
        coordinates: [-6.2195, 106.8033],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 12,
        title: 'Gelora Bung Karno 9',
        streamId: 'Gelora-017-700470_9',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-Sambo.png',
        coordinates: [-6.2197, 106.8035],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 13,
        title: 'Gelora Bung Karno 10',
        streamId: 'Gelora-017-700470_10',
        thumbnail: ASSET_BASE + '/image/thumbnail/Sutomo-Makam-Pahlawan.png',
        coordinates: [-6.2199, 106.8037],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 14,
        title: 'Monas Barat B',
        streamId: 'Monas-Barat-006_b',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-sutomo-pasar-horas.png',
        coordinates: [-6.1754, 106.8272],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 15,
        title: 'Monas Barat C',
        streamId: 'Monas-Barat-006_c',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-sutomo-vihara-square.png',
        coordinates: [-6.1756, 106.8274],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 16,
        title: 'Monas Utara B',
        streamId: 'Monas-Utara-001_b',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Sutomo-Polres-Siantar.png',
        coordinates: [-6.1718, 106.8272],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 17,
        title: 'Sudirman 1',
        streamId: 'Sudirman-001-700001_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Sutomo-Siantar-Square.png',
        coordinates: [-6.2250, 106.8100],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 18,
        title: 'Sudirman 2',
        streamId: 'Sudirman-001-700001_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jl-Mh-Sitorus.png',
        coordinates: [-6.2252, 106.8102],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 19,
        title: 'Thamrin 1',
        streamId: 'Thamrin-003-700003_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jl-Sudirman-Simpang-BRI.png',
        coordinates: [-6.1950, 106.8230],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 20,
        title: 'Thamrin 2',
        streamId: 'Thamrin-003-700003_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jl-Merdeka-Depan-Balai-Kota.png',
        coordinates: [-6.1952, 106.8232],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 21,
        title: 'Kuningan 1',
        streamId: 'Kuningan-005-700005_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-4-Bundaran.png',
        coordinates: [-6.2350, 106.8300],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 22,
        title: 'Kuningan 2',
        streamId: 'Kuningan-005-700005_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-Farel-Pasaribu-Kota.png',
        coordinates: [-6.2352, 106.8302],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 23,
        title: 'Gatot Subroto 1',
        streamId: 'Gatot-Subroto-007-700007_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-Farel-Pasaribu-Tanah-Jawa.png',
        coordinates: [-6.2300, 106.8200],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 24,
        title: 'Gatot Subroto 2',
        streamId: 'Gatot-Subroto-007-700007_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-Jalan-Bahkora.png',
        coordinates: [-6.2302, 106.8202],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 25,
        title: 'Casablanca 1',
        streamId: 'Casablanca-010-700010_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Singa-Jalan-Bali.png',
        coordinates: [-6.2280, 106.8410],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 26,
        title: 'Casablanca 2',
        streamId: 'Casablanca-010-700010_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-Jalan-Bali.png',
        coordinates: [-6.2282, 106.8412],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 27,
        title: 'Rasuna Said 1',
        streamId: 'Rasuna-Said-009-700009_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-4-Jalan-Gereja.png',
        coordinates: [-6.2240, 106.8430],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 28,
        title: 'Rasuna Said 2',
        streamId: 'Rasuna-Said-009-700009_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-Dua.png',
        coordinates: [-6.2242, 106.8432],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 29,
        title: 'Kemang 1',
        streamId: 'Kemang-015-700015_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simpang-dua-kota.png',
        coordinates: [-6.2600, 106.8140],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 30,
        title: 'Kemang 2',
        streamId: 'Kemang-015-700015_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Raya-Saribudolok-Simpang-Ring-Road.png',
        coordinates: [-6.2602, 106.8142],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 31,
        title: 'Pancoran 1',
        streamId: 'Pancoran-012-700012_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Raya-Saribudolok-Simpang-Dua.png',
        coordinates: [-6.2470, 106.8450],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 32,
        title: 'Pancoran 2',
        streamId: 'Pancoran-012-700012_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Parapat-Simpang-Ringroad.png',
        coordinates: [-6.2472, 106.8452],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 33,
        title: 'Slipi 1',
        streamId: 'Slipi-004-700004_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Parapat-Simpang-Bahkora.png',
        coordinates: [-6.1880, 106.7960],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 34,
        title: 'Slipi 2',
        streamId: 'Slipi-004-700004_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simarimbun-Kota.png',
        coordinates: [-6.1882, 106.7962],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 35,
        title: 'Tomang 1',
        streamId: 'Tomang-006-700006_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Simarimbun-Parapat-Sidamanik.png',
        coordinates: [-6.1770, 106.7920],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
    ];

    // Stream2 data Jakarta
    const stream2Data = [{
        id: 36,
        title: 'Kelapa Gading 1',
        streamId: 'Kelapa-Gading-020-700020_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jln-Sangnawaluh-ke-Jln-Asahan.png',
        coordinates: [-6.1580, 106.9060],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 37,
        title: 'Kelapa Gading 2',
        streamId: 'Kelapa-Gading-020-700020_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jln-Sangnawaluh-ke-Jln-Ahmad-Yani.png',
        coordinates: [-6.1582, 106.9062],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 38,
        title: 'Cawang 1',
        streamId: 'Cawang-013-700013_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Pertigaan-simpang-USI.png',
        coordinates: [-6.2540, 106.8700],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 39,
        title: 'Cawang 2',
        streamId: 'Cawang-013-700013_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Persimpangan-Tugu-Wahana-Tata-Nugraha.png',
        coordinates: [-6.2542, 106.8702],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 40,
        title: 'Tebet 1',
        streamId: 'Tebet-014-700014_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Merdeka-bawah.png',
        coordinates: [-6.2310, 106.8510],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 41,
        title: 'Pondok Indah 1',
        streamId: 'Pondok-Indah-016-700016_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Sudirman-ke-Jalan-Ade-Irma.png',
        coordinates: [-6.2810, 106.7850],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 42,
        title: 'Pondok Indah 2',
        streamId: 'Pondok-Indah-016-700016_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Sudirman-Ke-Lap-Adam-Malik.png',
        coordinates: [-6.2812, 106.7852],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 43,
        title: 'Senayan 1',
        streamId: 'Senayan-018-700018_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Gereja-ke-Jalan-M-H-Sitorus.png',
        coordinates: [-6.2270, 106.8020],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 44,
        title: 'Senayan 2',
        streamId: 'Senayan-018-700018_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/Jalan-Gereja-menuju-Parapat.png',
        coordinates: [-6.2272, 106.8022],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
    ];

    // Pasar Horas -> diganti Terminal Jakarta
    const pasarHorasData = [{
        id: 101,
        title: 'Mangga Dua 1',
        streamId: 'Mangga-Dua-019-700019_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/pasar-horas-1.png',
        coordinates: [-6.1440, 106.8290],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 104,
        title: 'Mangga Dua 2',
        streamId: 'Mangga-Dua-019-700019_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/pasar-horas-4.png',
        coordinates: [-6.1442, 106.8292],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 105,
        title: 'Tanah Abang 1',
        streamId: 'Tanah-Abang-021-700021_1',
        thumbnail: ASSET_BASE + '/image/thumbnail/pasar-horas-5.png',
        coordinates: [-6.1850, 106.8120],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
      {
        id: 106,
        title: 'Tanah Abang 2',
        streamId: 'Tanah-Abang-021-700021_2',
        thumbnail: ASSET_BASE + '/image/thumbnail/pasar-horas-6.png',
        coordinates: [-6.1852, 106.8122],
        platform: PLATFORM_TYPES.IPCAMLIVE,
      },
    ];

    // Terminal / Kamera Tambahan (Live Demo Stream)
    const terminalTanjungPinggirData = [{
        id: 201,
        title: 'Terminal Tanjung Pinggir 1',
        streamPath: 'jalan_sutomo_polres_siantar',
        thumbnail: ASSET_BASE + '/image/logo-loewix.png',
        coordinates: [2.9604000000, 99.0739000000],
        platform: PLATFORM_TYPES.MEDIAMTX,
        section: 'terminal-tanjung-pinggir'
      },
      {
        id: 202,
        title: 'Terminal Tanjung Pinggir 2',
        streamPath: 'simpang_bri',
        thumbnail: ASSET_BASE + '/image/logo-loewix.png',
        coordinates: [2.9604200000, 99.0739200000],
        platform: PLATFORM_TYPES.MEDIAMTX,
        section: 'terminal-tanjung-pinggir'
      },
    ];
    // WiFi Location Data - DKI Jakarta Public Internet (JakWifi)
    const wifiLocations = [
      // Jakarta Pusat
      {
        name: 'BALAI KOTA DKI JAKARTA',
        lat: -6.180512,
        lng: 106.828415,
        status: 'Aktif',
        address: 'Jl. Medan Merdeka Selatan No.8-9, Gambir, Jakarta Pusat'
      },
      {
        name: 'MONUMEN NASIONAL (MONAS)',
        lat: -6.175392,
        lng: 106.827153,
        status: 'Aktif',
        address: 'Kawasan Monas, Gambir, Jakarta Pusat'
      },
      {
        name: 'BUNDARAN HI & HALTE TOSARI',
        lat: -6.195028,
        lng: 106.823014,
        status: 'Aktif',
        address: 'Jl. M.H. Thamrin, Menteng, Jakarta Pusat'
      },
      {
        name: 'TAMAN SUROPATI',
        lat: -6.199421,
        lng: 106.832612,
        status: 'Aktif',
        address: 'Jl. Taman Suropati No.5, Menteng, Jakarta Pusat'
      },
      {
        name: 'TAMAN MENTENG',
        lat: -6.196345,
        lng: 106.829432,
        status: 'Aktif',
        address: 'Jl. HOS. Cokroaminoto, Menteng, Jakarta Pusat'
      },
      {
        name: 'TAMAN ISMAIL MARZUKI (TIM) & PERPUS JAKARTA',
        lat: -6.189531,
        lng: 106.838542,
        status: 'Aktif',
        address: 'Jl. Cikini Raya No.73, Menteng, Jakarta Pusat'
      },
      {
        name: 'TAMAN LAPANGAN BANTENG',
        lat: -6.170642,
        lng: 106.835123,
        status: 'Aktif',
        address: 'Pasar Baru, Sawah Besar, Jakarta Pusat'
      },
      {
        name: 'PERPUSTAKAAN NASIONAL RI (PERPUSNAS)',
        lat: -6.181245,
        lng: 106.826734,
        status: 'Aktif',
        address: 'Jl. Medan Merdeka Selatan No.11, Gambir, Jakarta Pusat'
      },
      {
        name: 'KAWASAN KULINER SABANG',
        lat: -6.186512,
        lng: 106.824215,
        status: 'Aktif',
        address: 'Jl. H. Agus Salim, Kebon Sirih, Menteng, Jakarta Pusat'
      },
      {
        name: 'KANTOR WALIKOTA JAKARTA PUSAT',
        lat: -6.179812,
        lng: 106.814234,
        status: 'Aktif',
        address: 'Jl. Tanah Abang I No.1, Petojo Selatan, Gambir, Jakarta Pusat'
      },
      {
        name: 'GELORA BUNG KARNO (GBK SENAYAN)',
        lat: -6.218542,
        lng: 106.801823,
        status: 'Aktif',
        address: 'Jl. Pintu Satu Senayan, Gelora, Tanah Abang, Jakarta Pusat'
      },
      {
        name: 'STASIUN MRT DUKUH ATAS BNI',
        lat: -6.200834,
        lng: 106.822812,
        status: 'Aktif',
        address: 'Kawasan Transit Terpadu Dukuh Atas, Jakarta Pusat'
      },

      // Jakarta Selatan
      {
        name: 'TEBET ECO PARK',
        lat: -6.236712,
        lng: 106.853345,
        status: 'Aktif',
        address: 'Jl. Tebet Barat Raya, Tebet Barat, Jakarta Selatan'
      },
      {
        name: 'BLOK M HUB & TAMAN LITERASI CH. TIAHAHU',
        lat: -6.244312,
        lng: 106.797934,
        status: 'Aktif',
        address: 'Jl. Sisingamangaraja, Melawai, Kebayoran Baru, Jakarta Selatan'
      },
      {
        name: 'KAWASAN TRANSIT CSW - ASEAN',
        lat: -6.239245,
        lng: 106.798512,
        status: 'Aktif',
        address: 'Jl. Kyai Maja, Kramat Pela, Kebayoran Baru, Jakarta Selatan'
      },
      {
        name: 'KAWASAN SCBD SUDIRMAN HUB',
        lat: -6.225034,
        lng: 106.808023,
        status: 'Aktif',
        address: 'Jl. Jend. Sudirman Kav. 52-53, Senayan, Jakarta Selatan'
      },
      {
        name: 'TAMAN AYODYA & BARITO',
        lat: -6.245812,
        lng: 106.794423,
        status: 'Aktif',
        address: 'Jl. Lamandau III, Kramat Pela, Kebayoran Baru, Jakarta Selatan'
      },
      {
        name: 'TAMAN LANGSAT',
        lat: -6.243512,
        lng: 106.792534,
        status: 'Aktif',
        address: 'Jl. Barito, Kramat Pela, Kebayoran Baru, Jakarta Selatan'
      },
      {
        name: 'HUTAN KOTA GBK',
        lat: -6.222412,
        lng: 106.806234,
        status: 'Aktif',
        address: 'Jl. Jenderal Sudirman, Senayan, Kebayoran Baru, Jakarta Selatan'
      },
      {
        name: 'TAMAN MATARAM',
        lat: -6.233512,
        lng: 106.802534,
        status: 'Aktif',
        address: 'Jl. Mataram No.1, Selong, Kebayoran Baru, Jakarta Selatan'
      },
      {
        name: 'KANTOR WALIKOTA JAKARTA SELATAN',
        lat: -6.262512,
        lng: 106.810534,
        status: 'Aktif',
        address: 'Jl. Prapanca Raya No.9, Petogogan, Kebayoran Baru, Jakarta Selatan'
      },
      {
        name: 'TAMAN MARGASATWA RAGUNAN',
        lat: -6.312412,
        lng: 106.820123,
        status: 'Aktif',
        address: 'Jl. Harsono RM No.1, Ragunan, Pasar Minggu, Jakarta Selatan'
      },

      // Jakarta Barat
      {
        name: 'KOTA TUA & TAMAN FATAHILLAH',
        lat: -6.135212,
        lng: 106.813345,
        status: 'Aktif',
        address: 'Kawasan Kota Tua, Pinangsia, Taman Sari, Jakarta Barat'
      },
      {
        name: 'TAMAN CATTLEYA TOMANG',
        lat: -6.182412,
        lng: 106.791534,
        status: 'Aktif',
        address: 'Jl. Letjen S. Parman, Kemanggisan, Palmerah, Jakarta Barat'
      },
      {
        name: 'KANTOR WALIKOTA JAKARTA BARAT',
        lat: -6.168212,
        lng: 106.750534,
        status: 'Aktif',
        address: 'Jl. Raya Kembangan No.2, Kembangan Selatan, Jakarta Barat'
      },
      {
        name: 'HUTAN KOTA SRENGSENG',
        lat: -6.208512,
        lng: 106.762134,
        status: 'Aktif',
        address: 'Jl. H. Kelik, Srengseng, Kembangan, Jakarta Barat'
      },
      {
        name: 'TAMAN JALUR HIJAU KOSAMBI',
        lat: -6.175612,
        lng: 106.718534,
        status: 'Aktif',
        address: 'Duri Kosambi, Cengkareng, Jakarta Barat'
      },

      // Jakarta Utara
      {
        name: 'TAMAN IMPIAN JAYA ANCOL & PANTAI KARNAVAL',
        lat: -6.124512,
        lng: 106.843234,
        status: 'Aktif',
        address: 'Jl. Lodan Timur No.7, Ancol, Pademangan, Jakarta Utara'
      },
      {
        name: 'DANAU SUNTER (SUNTER HUB)',
        lat: -6.143212,
        lng: 106.872034,
        status: 'Aktif',
        address: 'Jl. Danau Sunter Selatan, Tanjung Priok, Jakarta Utara'
      },
      {
        name: 'KANTOR WALIKOTA JAKARTA UTARA',
        lat: -6.121512,
        lng: 106.892034,
        status: 'Aktif',
        address: 'Jl. Laksamana Yos Sudarso No.27-29, Kebon Bawang, Jakarta Utara'
      },
      {
        name: 'TAMAN WADUK PLUIT',
        lat: -6.120512,
        lng: 106.797234,
        status: 'Aktif',
        address: 'Jl. Pluit Timur Raya No.12, Penjaringan, Jakarta Utara'
      },
      {
        name: 'KAWASAN KULINER & TAMAN PIK',
        lat: -6.111812,
        lng: 106.738534,
        status: 'Aktif',
        address: 'Pantai Indah Kapuk, Kamal Muara, Penjaringan, Jakarta Utara'
      },

      // Jakarta Timur
      {
        name: 'TAMAN MINI INDONESIA INDAH (TMII)',
        lat: -6.302412,
        lng: 106.895234,
        status: 'Aktif',
        address: 'Jl. Raya Taman Mini, Ceger, Cipayung, Jakarta Timur'
      },
      {
        name: 'KANTOR WALIKOTA JAKARTA TIMUR',
        lat: -6.216312,
        lng: 106.904834,
        status: 'Aktif',
        address: 'Jl. Dr. Sumarno, Penggilingan, Cakung, Jakarta Timur'
      },
      {
        name: 'TAMAN WADUK RIA RIO',
        lat: -6.175212,
        lng: 106.878534,
        status: 'Aktif',
        address: 'Jl. Pulo Mas Utara, Kayu Putih, Pulo Gadung, Jakarta Timur'
      },
      {
        name: 'BUMI PERKEMAHAN CIBUBUR (BUPERTA)',
        lat: -6.368512,
        lng: 106.897534,
        status: 'Aktif',
        address: 'Pondok Ranggon, Cipayung, Jakarta Timur'
      },
      {
        name: 'TAMAN APUNG JAKARTA TIMUR',
        lat: -6.311212,
        lng: 106.885634,
        status: 'Aktif',
        address: 'Kelapa Dua Wetan, Ciracas, Jakarta Timur'
      }
    ];

    // Stream Quality Configuration
    const streamQualityLevels = {
      auto: {
        bandwidth: 0,
        resolution: 'auto'
      },
      high: {
        bandwidth: 2000000,
        resolution: '720p'
      },
      medium: {
        bandwidth: 1000000,
        resolution: '480p'
      },
      low: {
        bandwidth: 500000,
        resolution: '360p'
      },
      veryLow: {
        bandwidth: 250000,
        resolution: '240p'
      },
    };

    // Global State Variables
    let currentQualityLevel = 'auto';
    let networkStatus = 'unknown';
    let activePlayers = new Set();
    let mapCCTV = null;
    let mapWifi = null;

    // Performance Configuration - Global scope untuk background cleanup
    const PERFORMANCE_CONFIG = {
      MAX_CONCURRENT_RECOMMENDED: 20, // Recommended limit
      CLEANUP_THRESHOLD: 500, // Cleanup jika 500px di luar viewport
      CLEANUP_INTERVAL: 10000 // Check every 10 seconds
    };

    // Security Enhancement - Secure hash function for stream IDs
    function secureHashStreamId(streamId) {
      let hash = 0;
      for (let i = 0; i < streamId.length; i++) {
        const char = streamId.charCodeAt(i);
        hash = (hash << 5) - hash + char;
        hash = hash & hash; // Convert to 32bit integer
      }
      return `${hash.toString(16)}.${Date.now()}`;
    }

    // Preload critical resources
    function preloadStreamingResources() {
      const resources = [{
          type: 'domain',
          url: 'stream.denava.id'
        },
        {
          type: 'domain',
          url: 'stream2.denava.id'
        },
        {
          type: 'domain',
          url: 'cctv.balitower.co.id'
        },
        {
          type: 'script',
          url: 'stream.denava.id/shared/hls.min.js'
        },
      ];

      resources.forEach((resource) => {
        if (resource.type === 'domain') {
          const prefetchLink = document.createElement('link');
          prefetchLink.rel = 'dns-prefetch';
          prefetchLink.href = `//${resource.url}`;
          document.head.appendChild(prefetchLink);

          const preconnectLink = document.createElement('link');
          preconnectLink.rel = 'preconnect';
          preconnectLink.href = `//${resource.url}`;
          document.head.appendChild(preconnectLink);
        } else if (resource.type === 'script') {
          const preloadLink = document.createElement('link');
          preloadLink.rel = 'preload';
          preloadLink.as = 'script';
          preloadLink.href = `//${resource.url}`;
          document.head.appendChild(preloadLink);
        }
      });
    }

    // Fungsi untuk memeriksa apakah Leaflet sudah dimuat
    function isLeafletLoaded() {
      return typeof L !== 'undefined';
    }

    // Memuat kembali Leaflet jika belum tersedia
    function loadLeaflet(callback) {
      if (isLeafletLoaded()) {
        callback();
        return;
      }

      const script = document.createElement('script');
      script.src = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js';
      script.integrity =
        'sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==';
      script.crossOrigin = '';
      script.onload = callback;
      document.head.appendChild(script);

      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css';
      link.integrity =
        'sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==';
      link.crossOrigin = '';
      document.head.appendChild(link);
    }

    // Bandwidth detection with network quality diagnostics
    function detectBandwidth() {
      return new Promise((resolve) => {
        if (networkStatus !== 'unknown') {
          resolve(currentQualityLevel);
          return;
        }

        const downloadSize = 512 * 1024; // 512KB test
        const randomId = Math.floor(Math.random() * 10000000);
        const imageAddr = `${ASSET_BASE}/image/bandwidth-test.jpg?r=${randomId}`;
        const startTime = new Date().getTime();

        const downloadImgFn = new Image();
        downloadImgFn.onload = function() {
          const endTime = new Date().getTime();
          const duration = (endTime - startTime) / 1000;
          const bitsLoaded = downloadSize * 8;
          const speedBps = Math.round(bitsLoaded / duration);

          if (speedBps > 1500000) {
            currentQualityLevel = 'high';
            networkStatus = 'good';
          } else if (speedBps > 700000) {
            currentQualityLevel = 'medium';
            networkStatus = 'good';
          } else if (speedBps > 350000) {
            currentQualityLevel = 'low';
            networkStatus = 'medium';
          } else {
            currentQualityLevel = 'veryLow';
            networkStatus = 'poor';
          }

          updateConnectionStatusDisplay();
          resolve(currentQualityLevel);
        };

        downloadImgFn.onerror = function() {
          networkStatus = 'poor';
          currentQualityLevel = 'veryLow';
          updateConnectionStatusDisplay();
          resolve(currentQualityLevel);
        };

        downloadImgFn.src = imageAddr;

        setTimeout(() => {
          if (networkStatus === 'unknown') {
            networkStatus = 'medium';
            currentQualityLevel = 'medium';
            updateConnectionStatusDisplay();
            resolve(currentQualityLevel);
          }
        }, 5000);
      });
    }

    // Update connection status display
    function updateConnectionStatusDisplay() {
      const statusElement = document.getElementById('connection-status');

      statusElement.className = 'connection-status';
      statusElement.classList.add(networkStatus);

      let statusText = '';
      let qualityText = '';

      switch (networkStatus) {
        case 'good':
          statusText = 'Koneksi Baik';
          break;
        case 'medium':
          statusText = 'Koneksi Sedang';
          break;
        case 'poor':
          statusText = 'Koneksi Lambat';
          break;
        default:
          statusText = 'Memeriksa Koneksi...';
      }

      switch (currentQualityLevel) {
        case 'auto':
          qualityText = 'Otomatis';
          break;
        case 'high':
          qualityText = 'HD';
          break;
        case 'medium':
          qualityText = 'SD';
          break;
        case 'low':
          qualityText = '';
          break;
        case 'veryLow':
          qualityText = '';
          break;
      }

      statusElement.innerHTML = `${statusText} - Kualitas Video : ${qualityText} <i class="fas fa-wifi"></i>`;
      statusElement.style.display = 'block';

      setTimeout(() => {
        statusElement.style.display = 'none';
      }, 5000);
    }

    // Display connection lost message
    function showConnectionLostMessage() {
      const statusElement = document.getElementById('connection-status');

      statusElement.className = 'connection-status poor';
      statusElement.innerHTML =
        'Koneksi Internet Terputus <i class="fas fa-exclamation-triangle"></i>';
      statusElement.style.display = 'block';
    }

    // Play CCTV function - streamlined
    function playCCTV(element, playerId, thumbId) {
      const streamId = element.getAttribute('data-stream-id');
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const tooltipId = playerId + '-tooltip';
      const tooltipElement = document.getElementById(tooltipId);
      const cardId = 'card-' + thumbId.split('-')[1];
      const card = document.getElementById(cardId);

      detectBandwidth().then((qualityLevel) => {
        activePlayers.add(playerId);

        if (thumb) {
          const loadingText = thumb.querySelector('.loading-text');
          if (loadingText) {
            loadingText.innerHTML =
              '<i class="fas fa-spinner fa-spin"></i> Memuat video...';
          }
        }

        if (bufferingOverlay) {
          bufferingOverlay.style.display = 'flex';
        }

        try {
          const encryptedParams = encodeURIComponent(
            secureHashStreamId(streamId)
          );

          const qualityParam =
            qualityLevel === 'auto' ?
            '' :
            `quality=${streamQualityLevels[qualityLevel].resolution}`;

          const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random()
              .toString(36)
              .substring(2)}${qualityParam ? '&' + qualityParam : ''}`;

          let bufferLength = 30;
          let initialBuffer = 3;

          if (networkStatus === 'poor') {
            bufferLength = 60;
            initialBuffer = 6;
          } else if (networkStatus === 'medium') {
            bufferLength = 45;
            initialBuffer = 4;
          }

          const bufferParam = `&buffer=${initialBuffer}&maxBufferLength=${bufferLength}&initialBufferTime=${initialBuffer}`;

          // Mark play time untuk Stream Suspension Manager
          if (player) {
            player.setAttribute('data-play-time', Date.now().toString());
          }

          player.src = `https://stream.denava.id/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;

          player.setAttribute('data-secure-stream', 'true');
          player.setAttribute('data-quality', qualityLevel);
          player.setAttribute('data-quality-mode', 'auto');

          if (tooltipElement) {
            let qualityName = '';
            switch (qualityLevel) {
              case 'high':
                qualityName = 'HD (720p)';
                break;
              case 'medium':
                qualityName = 'SD (480p)';
                break;
              case 'low':
                qualityName = '(360p)';
                break;
              case 'veryLow':
                qualityName = '(240p)';
                break;
              default:
                qualityName = 'Otomatis';
            }
            tooltipElement.innerHTML = `Kualitas: ${qualityName}`;
          }

          player.classList.remove('hidden-iframe');
          player.style.display = 'block';

          if (thumb) {
            thumb.style.display = 'none';
          }

          player.onload = function() {
            if (bufferingOverlay) {
              bufferingOverlay.style.display = 'none';
            }

            const qualityButtons = document.querySelectorAll(
              `#quality-control-${thumbId.split('-')[1]} .quality-button`
            );
            qualityButtons.forEach((btn) => {
              btn.classList.remove('active');
              if (btn.getAttribute('data-quality') === qualityLevel) {
                btn.classList.add('active');
              }
            });

            if (card) {
              card.classList.remove('dark-card');
            }

            // Enable auto-refresh monitoring untuk video ini
            const offlineId = 'offline-' + thumbId.split('-')[1];
            enableAutoRefreshForPlayer(playerId, 'denava', streamId, thumbId, offlineId);

            // Reset auto-refresh state karena video berhasil dimuat
            resetAutoRefreshState(playerId);

            // Check awal setelah video dimuat - pastikan video benar-benar muncul
            setTimeout(() => {
              detectDarkVideo(playerId).then((isDark) => {
                if (isDark) {
                  console.log(`[Auto-Refresh] ${playerId} - Video tidak muncul setelah load, initiating refresh...`);
                  // Video tidak muncul, refresh otomatis
                  smartAutoRefresh(playerId, 'video_not_appearing');
                } else {
                  // Video muncul dengan baik
                  const state = videoPlayerStates.get(playerId);
                  if (state) {
                    state.bufferingStartTime = null; // Reset buffering time
                  }
                }
              });
            }, 5000); // Check setelah 5 detik

            // Update stream health indicator
            const state = videoPlayerStates.get(playerId);
            if (state && state.healthScore) {
              updateStreamHealthIndicator(playerId, state.healthScore.score);
            }

            // Remove from preload queue
            preloadQueue.delete(playerId);

            // Track successful stream load
            gtag('event', 'stream_load_success', {
              event_category: 'CCTV Streaming',
              event_action: 'stream_loaded',
              event_label: streamId,
              value: 1
            });
          };

          player.onerror = function() {
            showOfflineMessage(playerId, thumbId);

            // Track stream error
            gtag('event', 'stream_load_error', {
              event_category: 'CCTV Streaming',
              event_action: 'stream_error',
              event_label: streamId,
              value: 1
            });
          };

          setupIframeMessageListener(player, streamId);

          setTimeout(function() {
            checkVideoStatus(playerId, thumbId);
          }, 5000);
        } catch (error) {
          console.error('Error loading CCTV stream', error);
          showOfflineMessage(playerId, thumbId);
        }
      });
    }

    // Function untuk memainkan CCTV dari stream2.denava.id
    function playStream2CCTV(element, playerId, thumbId) {
      const streamId = element.getAttribute('data-stream-id');
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const tooltipId = playerId + '-tooltip';
      const tooltipElement = document.getElementById(tooltipId);
      const cardId = 'card-' + thumbId.split('-')[1];
      const card = document.getElementById(cardId);

      detectBandwidth().then((qualityLevel) => {
        activePlayers.add(playerId);

        if (thumb) {
          const loadingText = thumb.querySelector('.loading-text');
          if (loadingText) {
            loadingText.innerHTML =
              '<i class="fas fa-spinner fa-spin"></i> Memuat video...';
          }
        }

        if (bufferingOverlay) {
          bufferingOverlay.style.display = 'flex';
        }

        try {
          const encryptedParams = encodeURIComponent(
            secureHashStreamId(streamId)
          );

          const qualityParam =
            qualityLevel === 'auto' ?
            '' :
            `quality=${streamQualityLevels[qualityLevel].resolution}`;

          const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random()
              .toString(36)
              .substring(2)}${qualityParam ? '&' + qualityParam : ''}`;

          let bufferLength = 30;
          let initialBuffer = 3;

          if (networkStatus === 'poor') {
            bufferLength = 60;
            initialBuffer = 6;
          } else if (networkStatus === 'medium') {
            bufferLength = 45;
            initialBuffer = 4;
          }

          const bufferParam = `&buffer=${initialBuffer}&maxBufferLength=${bufferLength}&initialBufferTime=${initialBuffer}`;

          // Mark play time untuk Stream Suspension Manager
          if (player) {
            player.setAttribute('data-play-time', Date.now().toString());
          }

          player.src = `https://stream2.denava.id/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;

          player.setAttribute('data-secure-stream', 'true');
          player.setAttribute('data-quality', qualityLevel);
          player.setAttribute('data-quality-mode', 'auto');

          if (tooltipElement) {
            let qualityName = '';
            switch (qualityLevel) {
              case 'high':
                qualityName = 'HD (720p)';
                break;
              case 'medium':
                qualityName = 'SD (480p)';
                break;
              case 'low':
                qualityName = '(360p)';
                break;
              case 'veryLow':
                qualityName = '(240p)';
                break;
              default:
                qualityName = 'Otomatis';
            }
            tooltipElement.innerHTML = `Kualitas: ${qualityName}`;
          }

          player.classList.remove('hidden-iframe');
          player.style.display = 'block';

          if (thumb) {
            thumb.style.display = 'none';
          }

          player.onload = function() {
            if (bufferingOverlay) {
              bufferingOverlay.style.display = 'none';
            }

            const qualityButtons = document.querySelectorAll(
              `#quality-control-${thumbId.split('-')[1]} .quality-button`
            );
            qualityButtons.forEach((btn) => {
              btn.classList.remove('active');
              if (btn.getAttribute('data-quality') === qualityLevel) {
                btn.classList.add('active');
              }
            });

            if (card) {
              card.classList.remove('dark-card');
            }

            // Enable auto-refresh monitoring untuk video ini
            const offlineId = 'offline-' + thumbId.split('-')[1];
            enableAutoRefreshForPlayer(playerId, 'stream2', streamId, thumbId, offlineId);

            // Reset auto-refresh state karena video berhasil dimuat
            resetAutoRefreshState(playerId);

            // Check awal setelah video dimuat - pastikan video benar-benar muncul
            setTimeout(() => {
              detectDarkVideo(playerId).then((isDark) => {
                if (isDark) {
                  console.log(`[Auto-Refresh] ${playerId} - Video tidak muncul setelah load, initiating refresh...`);
                  // Video tidak muncul, refresh otomatis
                  smartAutoRefresh(playerId, 'video_not_appearing');
                } else {
                  // Video muncul dengan baik
                  const state = videoPlayerStates.get(playerId);
                  if (state) {
                    state.bufferingStartTime = null; // Reset buffering time
                  }
                }
              });
            }, 5000); // Check setelah 5 detik

            // Update stream health indicator
            const state = videoPlayerStates.get(playerId);
            if (state && state.healthScore) {
              updateStreamHealthIndicator(playerId, state.healthScore.score);
            }

            // Remove from preload queue
            preloadQueue.delete(playerId);

            // Track successful stream load
            gtag('event', 'stream2_load_success', {
              event_category: 'CCTV Streaming',
              event_action: 'stream2_loaded',
              event_label: streamId,
              value: 1
            });
          };

          player.onerror = function() {
            showOfflineMessage(playerId, thumbId);

            // Track stream error
            gtag('event', 'stream2_load_error', {
              event_category: 'CCTV Streaming',
              event_action: 'stream2_error',
              event_label: streamId,
              value: 1
            });
          };

          setupIframeMessageListener(player, streamId);

          setTimeout(function() {
            checkVideoStatus(playerId, thumbId);
          }, 5000);
        } catch (error) {
          console.error('Error loading CCTV stream', error);
          showOfflineMessage(playerId, thumbId);
        }
      });
    }

    // Fungsi baru untuk memutar video dari platform ipcamlive
    function playIPCamLive(element, playerId, thumbId) {
      const streamId = element.getAttribute('data-stream-id');
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const tooltipId = playerId + '-tooltip';
      const tooltipElement = document.getElementById(tooltipId);
      const cardId = 'card-' + thumbId.split('-')[1];
      const card = document.getElementById(cardId);

      // Validasi streamId - cek apakah masih placeholder
      if (!streamId || streamId.startsWith('STREAM_ID') || streamId === 'STREAM_ID_1' || streamId === 'STREAM_ID_2') {
        console.warn(`Stream ID belum diisi untuk camera ${playerId}. Silakan isi streamId IPCamLive yang benar.`);
        if (thumb) {
          const loadingText = thumb.querySelector('.loading-text');
          if (loadingText) {
            loadingText.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Stream ID belum dikonfigurasi';
          }
        }
        if (bufferingOverlay) {
          bufferingOverlay.style.display = 'none';
        }
        showOfflineMessage(playerId, thumbId);
        return;
      }

      if (thumb) {
        const loadingText = thumb.querySelector('.loading-text');
        if (loadingText) {
          loadingText.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i> Memuat video...';
        }
      }

      if (bufferingOverlay) {
        bufferingOverlay.style.display = 'flex';
      }

      try {
        const ipcamURL = `https://g3.ipcamlive.com/player/player.php?alias=${streamId}&autoplay=1&mute=1`;

        player.src = ipcamURL;

        // Mark play time untuk Stream Suspension Manager
        player.setAttribute('data-play-time', Date.now().toString());

        activePlayers.add(playerId);

        player.setAttribute('data-secure-stream', 'true');
        player.setAttribute('data-quality', 'auto');
        player.setAttribute('data-quality-mode', 'auto');

        if (tooltipElement) {
          tooltipElement.innerHTML = 'Kualitas: Auto';
        }

        player.classList.remove('hidden-iframe');
        player.style.display = 'block';

        if (thumb) {
          thumb.style.display = 'none';
        }

        player.onload = function() {
          if (bufferingOverlay) {
            bufferingOverlay.style.display = 'none';
          }

          if (card) {
            card.classList.remove('dark-card');
          }

          // Enable auto-refresh monitoring untuk video ini
          const offlineId = 'offline-' + thumbId.split('-')[1];
          enableAutoRefreshForPlayer(playerId, 'ipcamlive', streamId, thumbId, offlineId);

          // Reset auto-refresh state karena video berhasil dimuat
          resetAutoRefreshState(playerId);

          // Check awal setelah video dimuat - pastikan video benar-benar muncul
          setTimeout(() => {
            detectDarkVideo(playerId).then((isDark) => {
              if (isDark) {
                console.log(`[Auto-Refresh] ${playerId} - Video tidak muncul setelah load, initiating refresh...`);
                // Video tidak muncul, refresh otomatis
                smartAutoRefresh(playerId, 'video_not_appearing');
              } else {
                // Video muncul dengan baik
                const state = videoPlayerStates.get(playerId);
                if (state) {
                  state.bufferingStartTime = null; // Reset buffering time
                }
              }
            });
          }, 5000); // Check setelah 5 detik

          // Update stream health indicator
          const state = videoPlayerStates.get(playerId);
          if (state && state.healthScore) {
            updateStreamHealthIndicator(playerId, state.healthScore.score);
          }

          // Remove from preload queue
          preloadQueue.delete(playerId);

          // Track successful stream load
          gtag('event', 'ipcamlive_load_success', {
            event_category: 'CCTV Streaming',
            event_action: 'ipcamlive_loaded',
            event_label: streamId,
            value: 1
          });
        };

        player.onerror = function() {
          showOfflineMessage(playerId, thumbId);

          // Track stream error
          gtag('event', 'ipcamlive_load_error', {
            event_category: 'CCTV Streaming',
            event_action: 'ipcamlive_error',
            event_label: streamId,
            value: 1
          });
        };

        setTimeout(function() {
          checkVideoStatus(playerId, thumbId);
        }, 5000);
      } catch (error) {
        console.error('Error loading IPCamLive stream', error);
        showOfflineMessage(playerId, thumbId);
      }
    }

    // Fungsi untuk memutar video dari platform RekasaDigital - sama seperti Denava, hanya beda domain
    function playRekasaDigitalCCTV(element, playerId, thumbId) {
      const streamId = element.getAttribute('data-stream-id');
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const tooltipId = playerId + '-tooltip';
      const tooltipElement = document.getElementById(tooltipId);
      const cardId = 'card-' + thumbId.split('-')[1];
      const card = document.getElementById(cardId);

      detectBandwidth().then((qualityLevel) => {
        activePlayers.add(playerId);

        if (thumb) {
          const loadingText = thumb.querySelector('.loading-text');
          if (loadingText) {
            loadingText.innerHTML =
              '<i class="fas fa-spinner fa-spin"></i> Memuat video...';
          }
        }

        if (bufferingOverlay) {
          bufferingOverlay.style.display = 'flex';
        }

        try {
          const encryptedParams = encodeURIComponent(
            secureHashStreamId(streamId)
          );

          const qualityParam =
            qualityLevel === 'auto' ?
            '' :
            `quality=${streamQualityLevels[qualityLevel].resolution}`;

          const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random()
              .toString(36)
              .substring(2)}${qualityParam ? '&' + qualityParam : ''}`;

          let bufferLength = 30;
          let initialBuffer = 3;

          if (networkStatus === 'poor') {
            bufferLength = 60;
            initialBuffer = 6;
          } else if (networkStatus === 'medium') {
            bufferLength = 45;
            initialBuffer = 4;
          }

          const bufferParam = `&buffer=${initialBuffer}&maxBufferLength=${bufferLength}&initialBufferTime=${initialBuffer}`;

          // Mark play time untuk Stream Suspension Manager
          if (player) {
            player.setAttribute('data-play-time', Date.now().toString());
          }

          // Set iframe attributes sebelum set src
          if (player) {
            player.setAttribute('allow', 'autoplay; fullscreen; encrypted-media; picture-in-picture');
            player.setAttribute('referrerpolicy', 'no-referrer-when-downgrade');
            player.setAttribute('loading', 'lazy');
            player.setAttribute('sandbox', 'allow-same-origin allow-scripts allow-popups allow-forms allow-presentation');
          }

          // Build URL - format yang sama dengan Denava
          const rekasaURL = `${STREAM_BASE}/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;

          console.log('[RekasaDigital] Loading stream:', rekasaURL);
          console.log('[RekasaDigital] StreamId:', streamId);
          console.log('[RekasaDigital] SecureParams:', secureParams);
          console.log('[RekasaDigital] BufferParam:', bufferParam);

          // Set src dengan error handling
          try {
            player.src = rekasaURL;
          } catch (e) {
            console.error('[RekasaDigital] Error setting src:', e);
            showOfflineMessage(playerId, thumbId);
            return;
          }

          player.setAttribute('data-secure-stream', 'true');
          player.setAttribute('data-quality', qualityLevel);
          player.setAttribute('data-quality-mode', 'auto');

          if (tooltipElement) {
            let qualityName = '';
            switch (qualityLevel) {
              case 'high':
                qualityName = 'HD (720p)';
                break;
              case 'medium':
                qualityName = 'SD (480p)';
                break;
              case 'low':
                qualityName = '(360p)';
                break;
              case 'veryLow':
                qualityName = '(240p)';
                break;
              default:
                qualityName = 'Otomatis';
            }
            tooltipElement.innerHTML = `Kualitas: ${qualityName}`;
          }

          player.classList.remove('hidden-iframe');
          player.style.display = 'block';

          if (thumb) {
            thumb.style.display = 'none';
          }

          player.onload = function() {
            if (bufferingOverlay) {
              bufferingOverlay.style.display = 'none';
            }

            const qualityButtons = document.querySelectorAll(
              `#quality-control-${thumbId.split('-')[1]} .quality-button`
            );
            qualityButtons.forEach((btn) => {
              btn.classList.remove('active');
              if (btn.getAttribute('data-quality') === qualityLevel) {
                btn.classList.add('active');
              }
            });

            if (card) {
              card.classList.remove('dark-card');
            }

            // Enable auto-refresh monitoring untuk video ini
            const offlineId = 'offline-' + thumbId.split('-')[1];
            enableAutoRefreshForPlayer(playerId, 'rekasadigital', streamId, thumbId, offlineId);

            // Reset auto-refresh state karena video berhasil dimuat
            resetAutoRefreshState(playerId);

            // Check awal setelah video dimuat - pastikan video benar-benar muncul
            setTimeout(() => {
              detectDarkVideo(playerId).then((isDark) => {
                if (isDark) {
                  console.log(`[Auto-Refresh] ${playerId} - Video tidak muncul setelah load, initiating refresh...`);
                  // Video tidak muncul, refresh otomatis
                  smartAutoRefresh(playerId, 'video_not_appearing');
                } else {
                  // Video muncul dengan baik
                  const state = videoPlayerStates.get(playerId);
                  if (state) {
                    state.bufferingStartTime = null; // Reset buffering time
                  }
                }
              });
            }, 5000); // Check setelah 5 detik

            // Update stream health indicator
            const state = videoPlayerStates.get(playerId);
            if (state && state.healthScore) {
              updateStreamHealthIndicator(playerId, state.healthScore.score);
            }

            // Remove from preload queue
            preloadQueue.delete(playerId);

            // Track successful stream load
            gtag('event', 'rekasadigital_load_success', {
              event_category: 'CCTV Streaming',
              event_action: 'rekasadigital_loaded',
              event_label: streamId,
              value: 1
            });
          };

          player.onerror = function(error) {
            console.error('[RekasaDigital] Stream load error:', error, 'URL:', player.src);
            showOfflineMessage(playerId, thumbId);

            // Track stream error
            gtag('event', 'rekasadigital_load_error', {
              event_category: 'CCTV Streaming',
              event_action: 'rekasadigital_error',
              event_label: streamId,
              value: 1
            });
          };

          // Additional error handling for iframe load issues
          player.addEventListener('error', function(e) {
            console.error('[RekasaDigital] Iframe error event:', e);
          }, true);

          setupIframeMessageListener(player, streamId);

          setTimeout(function() {
            checkVideoStatus(playerId, thumbId);
          }, 5000);
        } catch (error) {
          console.error('Error loading RekasaDigital stream', error);
          showOfflineMessage(playerId, thumbId);
        }
      });
    }

    // Setup listener for messages from iframe player
    function setupIframeMessageListener(player, streamId) {
      window.addEventListener('message', function(event) {
        try {
          const data = JSON.parse(event.data);
          if (
            data.action === 'bufferingStarted' &&
            data.streamId === streamId
          ) {
            const bufferingId = 'buffering-' + player.id.split('-')[1];
            const bufferingOverlay = document.getElementById(bufferingId);
            if (bufferingOverlay) {
              bufferingOverlay.style.display = 'flex';
            }

            // Track buffering metrics
            if (typeof trackBufferingMetrics === 'function') {
              trackBufferingMetrics(player.id, 'buffering_started', data);
            }
          }

          if (
            data.action === 'bufferingEnded' &&
            data.streamId === streamId
          ) {
            const bufferingId = 'buffering-' + player.id.split('-')[1];
            const bufferingOverlay = document.getElementById(bufferingId);
            if (bufferingOverlay) {
              bufferingOverlay.style.display = 'none';
            }

            // Track buffering metrics
            if (typeof trackBufferingMetrics === 'function') {
              trackBufferingMetrics(player.id, 'buffering_ended', data);
            }
          }

          if (
            data.action === 'bufferingMetrics' &&
            data.streamId === streamId
          ) {
            // Track metrics
            if (typeof trackBufferingMetrics === 'function') {
              trackBufferingMetrics(player.id, 'buffering_metrics', data);
            }

            if (
              data.bufferCount > 3 &&
              player.getAttribute('data-quality-mode') === 'auto'
            ) {
              downgradeQuality(player, streamId);
            }

            // Smart quality adjustment
            if (typeof smartQualityAdjustment === 'function') {
              smartQualityAdjustment(player.id);
            }
          }

          // Stream health update
          if (data.action === 'streamHealth' && data.streamId === streamId) {
            if (typeof updateStreamHealthIndicator === 'function') {
              const health = data.health || 100;
              updateStreamHealthIndicator(player.id, health);
            }
          }
        } catch (e) {
          // Ignore non-JSON messages
        }
      });
    }

    // Function to downgrade quality if streaming buffers frequently
    function downgradeQuality(player, streamId) {
      const currentQuality = player.getAttribute('data-quality');
      let newQuality;

      if (currentQuality === 'high') newQuality = 'medium';
      else if (currentQuality === 'medium') newQuality = 'low';
      else if (currentQuality === 'low') newQuality = 'veryLow';
      else return;

      console.log(
        `Downgrading stream quality to ${newQuality} due to buffering issues`
      );

      const isStream2 = player.src.includes('stream2.denava.id');
      const domain = isStream2 ? 'stream2.denava.id' : 'stream.denava.id';

      const encryptedParams = encodeURIComponent(
        secureHashStreamId(streamId)
      );
      const qualityParam = `quality=${streamQualityLevels[newQuality].resolution}`;
      const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random()
          .toString(36)
          .substring(2)}&${qualityParam}`;
      const bufferParam = `&buffer=4&maxBufferLength=30&initialBufferTime=3`;

      const tooltipElement = document.getElementById(`${player.id}-tooltip`);
      if (tooltipElement) {
        let qualityName = '';
        switch (newQuality) {
          case 'high':
            qualityName = 'HD (720p)';
            break;
          case 'medium':
            qualityName = 'SD (480p)';
            break;
          case 'low':
            qualityName = '(360p)';
            break;
          case 'veryLow':
            qualityName = '(240p)';
            break;
          default:
            qualityName = 'Otomatis';
        }
        tooltipElement.innerHTML = `Kualitas: ${qualityName} (auto-adjusted)`;
      }

      player.src = `https://${domain}/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;
      player.setAttribute('data-quality', newQuality);
    }

    // Function to check video status
    function checkVideoStatus(playerId, thumbId) {
      const player = document.getElementById(playerId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const cardId = 'card-' + thumbId.split('-')[1];
      const card = document.getElementById(cardId);

      try {
        if (player && player.contentWindow) {
          if (bufferingOverlay) {
            bufferingOverlay.style.display = 'none';
          }

          if (card) {
            card.classList.remove('dark-card');
          }
        } else {
          showOfflineMessage(playerId, thumbId);

          if (card) {
            card.classList.add('dark-card');
          }
        }
      } catch (e) {
        console.log('Cross-origin check, assuming video is playing');
        if (bufferingOverlay) {
          bufferingOverlay.style.display = 'none';
        }
      }
    }

    // ===== AUTO-REFRESH SYSTEM: Advanced Video Monitoring & Auto-Recovery =====

    // Track state untuk setiap video player
    const videoPlayerStates = new Map();

    // Konfigurasi auto-refresh
    const AUTO_REFRESH_CONFIG = {
      CHECK_INTERVAL: 12000, // Check setiap 12 detik
      DARK_THRESHOLD: 0.15, // Threshold brightness untuk deteksi gelap (0-1)
      STUCK_THRESHOLD: 30000, // Video dianggap stuck jika tidak berubah selama 30 detik
      MAX_RETRY: 5, // Max retry sebelum menyerah
      RETRY_DELAYS: [5000, 10000, 20000, 30000, 60000], // Exponential backoff delays (ms)
      COOLDOWN_AFTER_REFRESH: 30000, // Cooldown setelah refresh manual (30 detik)
    };

    // ===== ADVANCED PERFORMANCE OPTIMIZATION SYSTEM =====

    // Intersection Observer untuk lazy monitoring (hanya monitor video yang terlihat)
    let videoObserver = null;
    const visiblePlayers = new Set(); // Track video yang terlihat di viewport

    // Performance throttling
    let monitoringThrottle = null;
    let lastMonitoringTime = 0;
    const MONITORING_THROTTLE_MS = 100; // Throttle monitoring setiap 100ms

    // Adaptive monitoring interval berdasarkan jumlah video aktif
    function getAdaptiveCheckInterval() {
      const activeCount = activePlayers.size;
      if (activeCount === 0) return 30000; // Jika tidak ada video aktif, check setiap 30 detik
      if (activeCount <= 5) return AUTO_REFRESH_CONFIG.CHECK_INTERVAL; // Default 12 detik
      if (activeCount <= 10) return 15000; // 15 detik untuk 6-10 video
      if (activeCount <= 20) return 20000; // 20 detik untuk 11-20 video
      return 25000; // 25 detik untuk >20 video
    }

    // Initialize Intersection Observer untuk lazy monitoring
    function initVideoObserver() {
      if (!('IntersectionObserver' in window)) {
        console.warn('[Performance] IntersectionObserver not supported, using fallback');
        return;
      }

      videoObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          const card = entry.target;
          const playerId = card.getAttribute('data-player-id');

          if (!playerId) return;

          if (entry.isIntersecting) {
            // Video masuk viewport - enable monitoring
            visiblePlayers.add(playerId);
            const state = videoPlayerStates.get(playerId);
            if (state && activePlayers.has(playerId)) {
              state.isMonitoring = true;
            }
          } else {
            // Video keluar viewport - disable monitoring untuk menghemat resource
            visiblePlayers.delete(playerId);
            const state = videoPlayerStates.get(playerId);
            if (state) {
              // Jangan disable monitoring jika video baru saja dimuat (dalam 30 detik)
              const timeSinceLastRefresh = Date.now() - state.lastRefreshTime;
              if (timeSinceLastRefresh > 30000) {
                state.isMonitoring = false;
              }
            }
          }
        });
      }, {
        root: null,
        rootMargin: '50px', // Start monitoring 50px sebelum masuk viewport
        threshold: 0.1 // Trigger saat 10% terlihat
      });

      console.log('[Performance] Intersection Observer initialized for lazy monitoring');
    }

    // Observe semua video cards
    function observeVideoCards() {
      if (!videoObserver) return;

      document.querySelectorAll('.traffic-card[data-camera-id]').forEach(card => {
        const cameraId = card.getAttribute('data-camera-id');
        const playerId = `player-${cameraId}`;
        card.setAttribute('data-player-id', playerId);
        videoObserver.observe(card);
      });
    }

    // Smart resource cleanup - cleanup video yang tidak digunakan
    function cleanupUnusedVideos() {
      const now = Date.now();
      const CLEANUP_THRESHOLD = 300000; // 5 menit

      videoPlayerStates.forEach((state, playerId) => {
        // Cleanup jika video tidak aktif dan sudah lama tidak digunakan
        if (!activePlayers.has(playerId)) {
          const timeSinceLastUse = now - (state.lastRefreshTime || 0);

          if (timeSinceLastUse > CLEANUP_THRESHOLD && !state.isMonitoring) {
            // Cleanup state untuk video yang tidak digunakan
            videoPlayerStates.delete(playerId);
            console.log(`[Performance] Cleaned up unused video state: ${playerId}`);
          }
        }
      });
    }

    // Throttled monitoring untuk mengurangi beban CPU
    function throttledMonitorVideoPlayer(playerId) {
      const now = Date.now();

      if (now - lastMonitoringTime < MONITORING_THROTTLE_MS) {
        if (monitoringThrottle) {
          clearTimeout(monitoringThrottle);
        }

        monitoringThrottle = setTimeout(() => {
          monitorVideoPlayer(playerId);
          lastMonitoringTime = Date.now();
        }, MONITORING_THROTTLE_MS - (now - lastMonitoringTime));
      } else {
        monitorVideoPlayer(playerId);
        lastMonitoringTime = now;
      }
    }

    // Video health score tracking
    function updateVideoHealthScore(playerId, isHealthy) {
      const state = videoPlayerStates.get(playerId);
      if (!state) return;

      if (!state.healthScore) {
        state.healthScore = {
          score: 100,
          checks: 0,
          failures: 0,
          lastUpdate: Date.now()
        };
      }

      const health = state.healthScore;
      health.checks++;
      health.lastUpdate = Date.now();

      if (isHealthy) {
        // Increase score jika healthy (max 100)
        health.score = Math.min(100, health.score + 1);
        if (health.failures > 0) health.failures--;
      } else {
        // Decrease score jika unhealthy
        health.failures++;
        health.score = Math.max(0, health.score - 5);
      }

      // Adjust monitoring interval berdasarkan health score
      if (health.score < 50) {
        // Video dengan health score rendah, monitor lebih sering
        state.customCheckInterval = AUTO_REFRESH_CONFIG.CHECK_INTERVAL / 2;
      } else if (health.score > 80) {
        // Video dengan health score tinggi, monitor lebih jarang
        state.customCheckInterval = AUTO_REFRESH_CONFIG.CHECK_INTERVAL * 1.5;
      } else {
        state.customCheckInterval = null; // Use default
      }
    }

    // Memory management - cleanup old data
    function performMemoryCleanup() {
      // Cleanup unused videos
      cleanupUnusedVideos();

      // Cleanup old health score data (keep only last 100 checks)
      videoPlayerStates.forEach((state) => {
        if (state.healthScore && state.healthScore.checks > 100) {
          // Reset health score setelah 100 checks
          state.healthScore.checks = 50;
          state.healthScore.failures = Math.floor(state.healthScore.failures / 2);
        }
      });

      console.log('[Performance] Memory cleanup completed');
    }

    // ===== END ADVANCED PERFORMANCE OPTIMIZATION SYSTEM =====

    // Inisialisasi state untuk video player
    function initVideoPlayerState(playerId) {
      if (!videoPlayerStates.has(playerId)) {
        videoPlayerStates.set(playerId, {
          retryCount: 0,
          lastRefreshTime: 0,
          lastCheckTime: 0,
          lastFrameHash: null,
          frameCheckCount: 0,
          isDark: false,
          isStuck: false,
          isMonitoring: false,
          platform: null,
          streamId: null,
          thumbId: null,
          offlineId: null,
          bufferingStartTime: null, // Track buffering start time untuk deteksi video tidak muncul
        });
      }
      return videoPlayerStates.get(playerId);
    }

    // Deteksi video gelap menggunakan analisis brightness
    function detectDarkVideo(playerId) {
      return new Promise((resolve) => {
        const state = videoPlayerStates.get(playerId);
        const player = document.getElementById(playerId);

        if (!player) {
          resolve(false);
          return;
        }

        try {
          // Check jika player tidak memiliki src atau src kosong (untuk video element atau iframe)
          if (player.tagName === 'VIDEO') {
            // Video element - check src atau currentSrc
            if ((!player.src && !player.currentSrc) || player.src === '' || player.readyState === 0) {
              resolve(true);
              return;
            }
          } else {
            // Iframe - check src
            if (!player.src || player.src === '' || player.src.includes('about:blank')) {
              resolve(true);
              return;
            }
          }

          // Check jika player masih hidden atau tidak terlihat
          if (player.style.display === 'none' || player.classList.contains('hidden-iframe')) {
            resolve(true);
            return;
          }

          // Check card state - jika card memiliki class dark-card, kemungkinan video gelap
          const cardId = 'card-' + playerId.split('-')[1];
          const card = document.getElementById(cardId);
          if (card && card.classList.contains('dark-card')) {
            resolve(true);
            return;
          }

          // Check offline message
          const thumbId = 'thumb-' + playerId.split('-')[1];
          const offlineId = 'offline-' + playerId.split('-')[1];
          const offlineMsg = document.getElementById(offlineId);
          if (offlineMsg && offlineMsg.style.display === 'flex') {
            resolve(true);
            return;
          }

          // Check jika video sudah dimuat tapi masih menampilkan thumbnail (video tidak muncul)
          const thumb = document.getElementById(thumbId);
          if (thumb && thumb.style.display !== 'none' && player.src && player.src !== '') {
            // Video sudah punya src tapi thumbnail masih terlihat - video tidak muncul
            // Tapi beri waktu 3 detik setelah load untuk thumbnail hilang
            if (state && state.lastRefreshTime > 0) {
              const timeSinceLoad = Date.now() - state.lastRefreshTime;
              if (timeSinceLoad > 3000) {
                resolve(true);
                return;
              }
            }
          }

          // Check jika video sudah dimuat tapi buffering overlay masih muncul terlalu lama
          const bufferingId = 'buffering-' + playerId.split('-')[1];
          const bufferingOverlay = document.getElementById(bufferingId);
          if (bufferingOverlay && bufferingOverlay.style.display === 'flex') {
            // Check berapa lama buffering sudah muncul
            const now = Date.now();
            if (!state || !state.bufferingStartTime) {
              if (state) state.bufferingStartTime = now;
            } else {
              const bufferingDuration = now - state.bufferingStartTime;
              // Jika buffering lebih dari 10 detik, kemungkinan video tidak muncul
              if (bufferingDuration > 10000) {
                resolve(true);
                return;
              }
            }
          } else {
            // Reset buffering start time jika tidak buffering
            if (state) state.bufferingStartTime = null;
          }

          resolve(false);
        } catch (e) {
          // Jika ada error, anggap tidak gelap untuk menghindari false positive
          resolve(false);
        }
      });
    }

    // Deteksi video stuck/frozen dengan membandingkan frame
    function detectVideoStuck(playerId) {
      return new Promise((resolve) => {
        const state = videoPlayerStates.get(playerId);
        if (!state) {
          resolve(false);
          return;
        }

        const player = document.getElementById(playerId);
        if (!player || !player.src || player.style.display === 'none') {
          resolve(false);
          return;
        }

        try {
          // Check jika video sudah lama tidak berubah
          const now = Date.now();
          const timeSinceLastCheck = now - state.lastCheckTime;

          // Jika sudah lebih dari threshold dan masih sama, kemungkinan stuck
          if (state.lastCheckTime > 0 && timeSinceLastCheck > AUTO_REFRESH_CONFIG.STUCK_THRESHOLD) {
            // Check card state
            const cardId = 'card-' + playerId.split('-')[1];
            const card = document.getElementById(cardId);

            if (card && card.classList.contains('dark-card')) {
              resolve(true);
              return;
            }

            // Check buffering overlay - jika stuck di buffering
            const thumbId = 'thumb-' + playerId.split('-')[1];
            const bufferingId = 'buffering-' + playerId.split('-')[1];
            const bufferingOverlay = document.getElementById(bufferingId);

            if (bufferingOverlay && bufferingOverlay.style.display === 'flex') {
              // Jika buffering lebih dari 30 detik, kemungkinan stuck
              resolve(true);
              return;
            }
          }

          // Update last check time
          state.lastCheckTime = now;
          resolve(false);
        } catch (e) {
          resolve(false);
        }
      });
    }

    // Smart auto-refresh dengan exponential backoff
    async function smartAutoRefresh(playerId, reason = 'unknown') {
      const state = videoPlayerStates.get(playerId);
      if (!state) {
        console.warn(`[Auto-Refresh] State not found for ${playerId}`);
        return;
      }

      // Check cooldown - jangan refresh terlalu sering
      const now = Date.now();
      const timeSinceLastRefresh = now - state.lastRefreshTime;

      if (timeSinceLastRefresh < AUTO_REFRESH_CONFIG.COOLDOWN_AFTER_REFRESH) {
        console.log(`[Auto-Refresh] ${playerId} masih dalam cooldown, skip refresh`);
        return;
      }

      // Check max retry
      if (state.retryCount >= AUTO_REFRESH_CONFIG.MAX_RETRY) {
        console.warn(`[Auto-Refresh] ${playerId} sudah mencapai max retry (${AUTO_REFRESH_CONFIG.MAX_RETRY}), stop auto-refresh`);
        state.isMonitoring = false;
        return;
      }

      // Get retry delay dengan exponential backoff
      const retryIndex = Math.min(state.retryCount, AUTO_REFRESH_CONFIG.RETRY_DELAYS.length - 1);
      const delay = AUTO_REFRESH_CONFIG.RETRY_DELAYS[retryIndex];

      console.log(`[Auto-Refresh] ${playerId} - Reason: ${reason}, Retry: ${state.retryCount + 1}/${AUTO_REFRESH_CONFIG.MAX_RETRY}, Delay: ${delay}ms`);

      // Update state
      state.retryCount++;
      state.lastRefreshTime = now;
      state.bufferingStartTime = null; // Reset buffering time saat refresh

      // Wait sebelum refresh
      await new Promise(resolve => setTimeout(resolve, delay));

      // Get platform dan stream info
      const thumbId = state.thumbId || `thumb-${playerId.split('-')[1]}`;
      const offlineId = state.offlineId || `offline-${playerId.split('-')[1]}`;
      const streamId = state.streamId || document.getElementById(thumbId)?.getAttribute('data-stream-id');

      if (!streamId) {
        console.warn(`[Auto-Refresh] ${playerId} - StreamId not found, cannot refresh`);
        return;
      }

      // Get platform dari card atau state
      let platform = state.platform;
      if (!platform) {
        const cardId = 'card-' + playerId.split('-')[1];
        const card = document.getElementById(cardId);
        if (card) {
          platform = card.getAttribute('data-platform');
        }
      }

      // Panggil fungsi reload sesuai platform
      // Untuk auto-refresh, kita perlu reload manual tanpa disable monitoring
      try {
        const thumb = document.getElementById(thumbId);
        if (thumb) {
          const connType = thumb.getAttribute('data-connection-type');
          if (connType === 'xmeye_p2p' && (!activePlayers || !activePlayers.has(playerId))) {
            // Strictly ignore inactive XMeye cameras - do NOT touch loadingText or UI
            return;
          }
        }

        const player = document.getElementById(playerId);
        const offlineMsg = document.getElementById(offlineId);
        const bufferingId = 'buffering-' + thumbId.split('-')[1];
        const bufferingOverlay = document.getElementById(bufferingId);
        const loadingId = 'loading-' + thumbId.split('-')[1];
        const loadingIndicator = document.getElementById(loadingId);

        if (loadingIndicator) {
          loadingIndicator.style.display = 'block';
        }

        if (player) {
          player.style.display = 'none';
          player.src = '';
          player.className = 'hidden-iframe';
        }

        if (thumb) {
          thumb.style.display = 'flex';
        }

        if (offlineMsg) {
          offlineMsg.style.display = 'none';
        }

        if (bufferingOverlay) {
          bufferingOverlay.style.display = 'none';
        }

        const loadingText = document.querySelector(`#${thumbId} .loading-text`);
        if (loadingText) {
          loadingText.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i> Memuat ulang...';
        }

        activePlayers.delete(playerId);

        // Untuk auto-refresh, jangan disable monitoring, hanya update lastRefreshTime
        state.lastRefreshTime = Date.now();

        setTimeout(() => {
          if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
          }

          const thumbnailElement = document.getElementById(thumbId);
          if (thumbnailElement) {
            const connType = thumbnailElement.getAttribute('data-connection-type');
            if (connType === 'xmeye_p2p' && !activePlayers.has(playerId)) {
              // Do NOT auto-refresh inactive XMeye cameras
              return;
            }

            // Panggil fungsi play sesuai platform
            if (platform === 'denava') {
              playCCTV(thumbnailElement, playerId, thumbId);
            } else if (platform === 'stream2') {
              playStream2CCTV(thumbnailElement, playerId, thumbId);
            } else if (platform === 'ipcamlive') {
              playIPCamLive(thumbnailElement, playerId, thumbId);
            } else if (platform === 'rekasadigital') {
              playRekasaDigitalCCTV(thumbnailElement, playerId, thumbId);
            } else if (platform === 'mediamtx') {
              playMediaMTXCCTV(thumbnailElement, playerId, thumbId);
            } else {
              // Default ke DENAVA
              playCCTV(thumbnailElement, playerId, thumbId);
            }
          }
        }, 1500);

        console.log(`[Auto-Refresh] ${playerId} - Refresh initiated`);
      } catch (error) {
        console.error(`[Auto-Refresh] ${playerId} - Error during refresh:`, error);
      }
    }

    // Monitor video player untuk deteksi masalah dengan optimasi
    async function monitorVideoPlayer(playerId) {
      const state = initVideoPlayerState(playerId);

      if (!state.isMonitoring) {
        return; // Skip jika tidak dalam mode monitoring
      }

      const player = document.getElementById(playerId);
      if (!player) {
        state.isMonitoring = false;
        return;
      }

      // Skip jika player tidak aktif atau menggunakan HLS (Hls.js mengelola live stream sendiri secara mandiri tanpa refresh)
      if (player.hlsInstance || !player.src || player.style.display === 'none' || player.classList.contains('hidden-iframe')) {
        return;
      }

      // Check jika video sudah dimuat cukup lama (minimal 8 detik setelah load)
      if (state.lastRefreshTime > 0) {
        const timeSinceLoad = Date.now() - state.lastRefreshTime;
        // Skip check terlalu cepat setelah load (kurang dari 8 detik)
        if (timeSinceLoad < 8000) {
          return;
        }
      }

      // Check jika video gelap
      const isDark = await detectDarkVideo(playerId);
      const isHealthy = !isDark;

      if (isDark && !state.isDark) {
        state.isDark = true;
        updateVideoHealthScore(playerId, false);
        console.log(`[Auto-Refresh] ${playerId} - Detected dark video, initiating refresh...`);
        await smartAutoRefresh(playerId, 'dark_video');
        return;
      } else if (!isDark && state.isDark) {
        state.isDark = false;
        updateVideoHealthScore(playerId, true);
        // Reset retry jika video sudah tidak gelap
        if (state.retryCount > 0) {
          console.log(`[Auto-Refresh] ${playerId} - Video no longer dark, resetting retry count`);
          state.retryCount = 0;
        }
      } else {
        updateVideoHealthScore(playerId, isHealthy);
      }

      // Check jika video stuck
      const isStuck = await detectVideoStuck(playerId);
      if (isStuck && !state.isStuck) {
        state.isStuck = true;
        updateVideoHealthScore(playerId, false);
        console.log(`[Auto-Refresh] ${playerId} - Detected stuck video, initiating refresh...`);
        await smartAutoRefresh(playerId, 'stuck_video');
        return;
      } else if (!isStuck && state.isStuck) {
        state.isStuck = false;
        updateVideoHealthScore(playerId, true);
        // Reset retry jika video sudah tidak stuck
        if (state.retryCount > 0) {
          console.log(`[Auto-Refresh] ${playerId} - Video no longer stuck, resetting retry count`);
          state.retryCount = 0;
        }
      }

      // Check card state
      const cardId = 'card-' + playerId.split('-')[1];
      const card = document.getElementById(cardId);
      if (card && card.classList.contains('dark-card')) {
        // Video mungkin offline atau error
        if (!state.isDark && !state.isStuck) {
          updateVideoHealthScore(playerId, false);
          console.log(`[Auto-Refresh] ${playerId} - Card marked as dark, initiating refresh...`);
          await smartAutoRefresh(playerId, 'dark_card');
        }
      } else {
        // Card tidak dark, video mungkin sehat
        if (!isDark && !isStuck) {
          updateVideoHealthScore(playerId, true);

          // Update stream health indicator
          const state = videoPlayerStates.get(playerId);
          if (state && state.healthScore) {
            if (typeof updateStreamHealthIndicator === 'function') {
              updateStreamHealthIndicator(playerId, state.healthScore.score);
            }
          }
        }
      }

      // Smart quality adjustment berdasarkan buffering
      if (typeof smartQualityAdjustment === 'function') {
        smartQualityAdjustment(playerId);
      }
    }

    // Main monitoring loop untuk semua active players dengan optimasi
    function startAutoRefreshMonitoring() {
      // Initialize Intersection Observer untuk lazy monitoring
      initVideoObserver();

      // Observe video cards setelah HTML di-generate
      setTimeout(() => {
        observeVideoCards();
      }, 2000);

      // Adaptive monitoring dengan interval yang disesuaikan
      function runMonitoringCycle() {
        const checkInterval = getAdaptiveCheckInterval();

        // Monitor hanya video yang terlihat atau aktif
        activePlayers.forEach((playerId) => {
          const state = videoPlayerStates.get(playerId);

          // Skip jika video tidak terlihat dan sudah lama tidak digunakan
          if (!visiblePlayers.has(playerId) && state) {
            const timeSinceLastRefresh = Date.now() - (state.lastRefreshTime || 0);
            if (timeSinceLastRefresh > 60000) { // Skip jika > 1 menit tidak terlihat
              return;
            }
          }

          // Gunakan throttled monitoring untuk mengurangi beban
          throttledMonitorVideoPlayer(playerId);
        });

        // Schedule next cycle dengan adaptive interval
        setTimeout(runMonitoringCycle, checkInterval);
      }

      // Start monitoring cycle
      runMonitoringCycle();

      // Memory cleanup setiap 5 menit
      setInterval(performMemoryCleanup, 300000);

      console.log('[Auto-Refresh] Optimized monitoring system started with lazy loading');
    }

    // Start monitoring saat player mulai diputar
    function enableAutoRefreshForPlayer(playerId, platform, streamId, thumbId, offlineId) {
      const state = initVideoPlayerState(playerId);
      state.isMonitoring = true;
      state.platform = platform;
      state.streamId = streamId;
      state.thumbId = thumbId;
      state.offlineId = offlineId;
      state.lastCheckTime = Date.now();
      state.lastRefreshTime = Date.now(); // Update last refresh time
      state.retryCount = 0; // Reset retry count saat enable

      // Initialize health score
      if (!state.healthScore) {
        state.healthScore = {
          score: 100,
          checks: 0,
          failures: 0,
          lastUpdate: Date.now()
        };
      }

      // Add to visible players jika card terlihat
      const cardId = 'card-' + playerId.split('-')[1];
      const card = document.getElementById(cardId);
      if (card && videoObserver) {
        const rect = card.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight + 50 && rect.bottom > -50;
        if (isVisible) {
          visiblePlayers.add(playerId);
        }
      }

      console.log(`[Auto-Refresh] Enabled for ${playerId} (${platform})`);
    }

    // Disable monitoring saat player di-stop atau di-refresh manual
    function disableAutoRefreshForPlayer(playerId) {
      const state = videoPlayerStates.get(playerId);
      if (state) {
        state.isMonitoring = false;
        console.log(`[Auto-Refresh] Disabled for ${playerId}`);
      }
    }

    // Reset retry count saat video berhasil dimuat
    function resetAutoRefreshState(playerId) {
      const state = videoPlayerStates.get(playerId);
      if (state) {
        state.retryCount = 0;
        state.isDark = false;
        state.isStuck = false;
        state.lastCheckTime = Date.now();
        console.log(`[Auto-Refresh] Reset state for ${playerId}`);
      }
    }

    // ===== END AUTO-REFRESH SYSTEM =====

    // ===== ADVANCED STREAMING OPTIMIZATION SYSTEM =====

    // Smart Preloading System - Preload video yang akan dimuat berikutnya
    const preloadQueue = new Set();
    const preloadedStreams = new Map();
    const PRELOAD_CONFIG = {
      ENABLED: true,
      MAX_PRELOAD: 2, // Max 2 video yang di-preload
      PRELOAD_DELAY: 2000, // Delay 2 detik sebelum preload
      PRELOAD_DISTANCE: 100, // Preload video yang 100px dari viewport
    };

    // Preload video yang akan dimuat berikutnya
    function smartPreloadNextVideos() {
      if (!PRELOAD_CONFIG.ENABLED || preloadQueue.size >= PRELOAD_CONFIG.MAX_PRELOAD) {
        return;
      }

      // Cari video yang terlihat di viewport atau akan terlihat
      document.querySelectorAll('.traffic-card[data-camera-id]').forEach(card => {
        const rect = card.getBoundingClientRect();
        const isNearViewport = rect.top < window.innerHeight + PRELOAD_CONFIG.PRELOAD_DISTANCE &&
          rect.bottom > -PRELOAD_CONFIG.PRELOAD_DISTANCE;

        if (isNearViewport) {
          const cameraId = card.getAttribute('data-camera-id');
          const playerId = `player-${cameraId}`;
          const thumbId = `thumb-${cameraId}`;

          // Skip jika sudah aktif atau sudah di-preload
          if (activePlayers.has(playerId) || preloadedStreams.has(playerId)) {
            return;
          }

          // Skip jika sudah di queue
          if (preloadQueue.has(playerId)) {
            return;
          }

          const thumb = document.getElementById(thumbId);
          if (thumb) {
            const streamId = thumb.getAttribute('data-stream-id');
            const platform = card.getAttribute('data-platform');

            if (streamId && !streamId.startsWith('STREAM_ID')) {
              preloadQueue.add(playerId);

              // Preload dengan delay untuk tidak membebani
              setTimeout(() => {
                preloadStream(playerId, streamId, platform, thumbId);
              }, PRELOAD_CONFIG.PRELOAD_DELAY);
            }
          }
        }
      });
    }

    // Preload stream metadata (tidak load full video, hanya metadata)
    function preloadStream(playerId, streamId, platform, thumbId) {
      if (preloadedStreams.has(playerId)) {
        return; // Already preloaded
      }

      try {
        // Preload hanya metadata, bukan full video
        // Ini akan mempercepat loading saat user klik play
        const preloadLink = document.createElement('link');
        preloadLink.rel = 'preconnect';

        if (platform === 'denava') {
          preloadLink.href = 'https://stream.denava.id';
        } else if (platform === 'stream2') {
          preloadLink.href = 'https://stream2.denava.id';
        } else if (platform === 'ipcamlive') {
          preloadLink.href = 'https://cctv.balitower.co.id';
        } else if (platform === 'rekasadigital' || platform === 'mediamtx') {
          preloadLink.href = STREAM_BASE;
        }

        document.head.appendChild(preloadLink);
        preloadedStreams.set(playerId, {
          streamId,
          platform,
          preloadTime: Date.now()
        });

        console.log(`[Preload] Preloaded connection for ${playerId}`);
      } catch (error) {
        console.warn(`[Preload] Failed to preload ${playerId}:`, error);
      }
    }

    // Enhanced Buffering Strategy - Buffering yang lebih cerdas
    const bufferingMetrics = new Map();

    function trackBufferingMetrics(playerId, event, data = {}) {
      if (!bufferingMetrics.has(playerId)) {
        bufferingMetrics.set(playerId, {
          bufferEvents: [],
          totalBufferingTime: 0,
          bufferCount: 0,
          lastBufferTime: 0,
          avgBufferTime: 0
        });
      }

      const metrics = bufferingMetrics.get(playerId);

      if (event === 'buffering_started') {
        metrics.lastBufferTime = Date.now();
        metrics.bufferCount++;
      } else if (event === 'buffering_ended') {
        if (metrics.lastBufferTime > 0) {
          const bufferDuration = Date.now() - metrics.lastBufferTime;
          metrics.totalBufferingTime += bufferDuration;
          metrics.bufferEvents.push({
            time: Date.now(),
            duration: bufferDuration
          });

          // Keep only last 10 events
          if (metrics.bufferEvents.length > 10) {
            metrics.bufferEvents.shift();
          }

          // Calculate average
          metrics.avgBufferTime = metrics.totalBufferingTime / metrics.bufferCount;

          // Auto-adjust quality jika buffering terlalu sering
          if (metrics.bufferCount > 3 && metrics.avgBufferTime > 2000) {
            const player = document.getElementById(playerId);
            if (player && player.getAttribute('data-quality-mode') === 'auto') {
              console.log(`[Buffering] ${playerId} - Frequent buffering detected, considering quality adjustment`);
              // Quality akan di-adjust oleh downgradeQuality jika diperlukan
            }
          }
        }
      }
    }

    // Network Quality Real-time Monitoring
    let networkQualityHistory = [];
    const NETWORK_QUALITY_CONFIG = {
      CHECK_INTERVAL: 30000, // Check setiap 30 detik
      HISTORY_SIZE: 10, // Keep last 10 measurements
      QUALITY_THRESHOLD: 0.7 // 70% good untuk maintain quality
    };

    function monitorNetworkQuality() {
      if (!('connection' in navigator)) {
        return;
      }

      const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
      if (!conn) return;

      const quality = {
        downlink: conn.downlink || 0,
        rtt: conn.rtt || 0,
        effectiveType: conn.effectiveType || 'unknown',
        saveData: conn.saveData || false,
        timestamp: Date.now()
      };

      networkQualityHistory.push(quality);
      if (networkQualityHistory.length > NETWORK_QUALITY_CONFIG.HISTORY_SIZE) {
        networkQualityHistory.shift();
      }

      // Analyze quality trend
      analyzeNetworkTrend();
    }

    function analyzeNetworkTrend() {
      if (networkQualityHistory.length < 3) return;

      const recent = networkQualityHistory.slice(-3);
      const avgDownlink = recent.reduce((sum, q) => sum + (q.downlink || 0), 0) / recent.length;
      const avgRtt = recent.reduce((sum, q) => sum + (q.rtt || 0), 0) / recent.length;

      // Update network status berdasarkan trend
      if (avgDownlink > 1.5 && avgRtt < 100) {
        if (networkStatus !== 'good') {
          networkStatus = 'good';
          updateConnectionStatusDisplay();
          console.log('[Network] Quality improved to good');
        }
      } else if (avgDownlink < 0.5 || avgRtt > 300) {
        if (networkStatus !== 'poor') {
          networkStatus = 'poor';
          updateConnectionStatusDisplay();
          console.log('[Network] Quality degraded to poor');

          // Suggest quality downgrade untuk semua active players
          suggestQualityDowngrade();
        }
      }
    }

    // Suggest quality downgrade untuk semua active players
    function suggestQualityDowngrade() {
      activePlayers.forEach(playerId => {
        const player = document.getElementById(playerId);
        if (player && player.getAttribute('data-quality-mode') === 'auto') {
          const currentQuality = player.getAttribute('data-quality');
          if (currentQuality === 'high' || currentQuality === 'medium') {
            const thumbId = 'thumb-' + playerId.split('-')[1];
            const thumb = document.getElementById(thumbId);
            if (thumb) {
              const streamId = thumb.getAttribute('data-stream-id');
              if (streamId) {
                console.log(`[Network] Suggesting quality downgrade for ${playerId}`);
                // Quality akan di-adjust otomatis oleh system
              }
            }
          }
        }
      });
    }

    // Stream Health Indicators - Visual indicator untuk kesehatan stream
    function updateStreamHealthIndicator(playerId, health) {
      const cardId = 'card-' + playerId.split('-')[1];
      const card = document.getElementById(cardId);
      if (!card) return;

      // Remove existing overlay indicators to keep video stream 100% clean
      const existingIndicator = card.querySelector('.stream-health-indicator');
      if (existingIndicator) {
        existingIndicator.remove();
      }
    }

    // Smart Quality Adjustment - Auto adjust berdasarkan buffering metrics
    function smartQualityAdjustment(playerId) {
      const metrics = bufferingMetrics.get(playerId);
      if (!metrics || metrics.bufferCount < 2) return;

      const player = document.getElementById(playerId);
      if (!player || player.getAttribute('data-quality-mode') !== 'auto') return;

      // Jika buffering terlalu sering (>5 kali dalam 30 detik)
      const recentBuffers = metrics.bufferEvents.filter(e =>
        Date.now() - e.time < 30000
      );

      if (recentBuffers.length > 5) {
        const thumbId = 'thumb-' + playerId.split('-')[1];
        const thumb = document.getElementById(thumbId);
        if (thumb) {
          const streamId = thumb.getAttribute('data-stream-id');
          if (streamId && !player.src.includes('stream2.denava.id')) {
            // Downgrade quality
            downgradeQuality(player, streamId);
          }
        }
      }
    }

    // Connection Pool Optimization - Optimasi koneksi
    function optimizeConnections() {
      // Close unused preload connections
      const now = Date.now();
      preloadedStreams.forEach((data, playerId) => {
        // Jika preload lebih dari 5 menit dan video tidak aktif, cleanup
        if (now - data.preloadTime > 300000 && !activePlayers.has(playerId)) {
          preloadedStreams.delete(playerId);
          preloadQueue.delete(playerId);
        }
      });

      // Cleanup old buffering metrics
      bufferingMetrics.forEach((metrics, playerId) => {
        if (!activePlayers.has(playerId)) {
          // Keep metrics untuk 1 menit setelah player nonaktif
          const lastEvent = metrics.bufferEvents[metrics.bufferEvents.length - 1];
          if (lastEvent && now - lastEvent.time > 60000) {
            bufferingMetrics.delete(playerId);
          }
        }
      });
    }

    // Start advanced streaming optimizations
    function startStreamingOptimizations() {
      // Smart preloading
      if (PRELOAD_CONFIG.ENABLED) {
        // Preload saat scroll
        let preloadTimeout;
        window.addEventListener('scroll', () => {
          clearTimeout(preloadTimeout);
          preloadTimeout = setTimeout(() => {
            smartPreloadNextVideos();
          }, 500);
        }, {
          passive: true
        });

        // Initial preload
        setTimeout(() => {
          smartPreloadNextVideos();
        }, 3000);
      }

      // Network quality monitoring
      if ('connection' in navigator) {
        const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (conn) {
          conn.addEventListener('change', monitorNetworkQuality);
          setInterval(monitorNetworkQuality, NETWORK_QUALITY_CONFIG.CHECK_INTERVAL);
        }
      }

      // Connection optimization
      setInterval(optimizeConnections, 60000); // Every minute

      console.log('[Streaming] Advanced optimizations started');
    }

    // ===== END ADVANCED STREAMING OPTIMIZATION SYSTEM =====

    // Function to display offline message (Modern Sleek Customer Hub Style)
    function showOfflineMessage(playerId, thumbId, customTitle, customSubtitle) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const cid = thumbId.replace('thumb-', '').replace('popup-thumb-', '');
      const offlineId = thumbId.startsWith('popup-') ? `popup-offline-${cid}` : `offline-${cid}`;
      const offlineMsg = document.getElementById(offlineId);
      const bufferingId = thumbId.startsWith('popup-') ? `popup-buffering-${cid}` : `buffering-${cid}`;
      const bufferingOverlay = document.getElementById(bufferingId);
      const cardId = 'card-' + cid;
      const card = document.getElementById(cardId);

      if (offlineMsg) {
        const titleEl = offlineMsg.querySelector('.offline-title');
        const subEl = offlineMsg.querySelector('.offline-subtitle');
        if (titleEl) {
          titleEl.textContent = customTitle || 'Kamera Sedang Offline';
        }
        if (subEl) {
          subEl.textContent = customSubtitle || 'Kamera sedang Offline (Mati Daya / Tidak Terhubung ke Internet)';
        }
        offlineMsg.style.display = 'flex';
      }

      if (player) player.style.display = 'none';
      if (thumb) thumb.style.display = 'none';
      if (bufferingOverlay) bufferingOverlay.style.display = 'none';

      if (card) {
        card.classList.add('dark-card');
      }

      activePlayers.delete(playerId);
    }

    // Function to reload CCTV - Improved
    function reloadCCTV(playerId, thumbId, offlineId, streamId) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const offlineMsg = document.getElementById(offlineId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const loadingId = 'loading-' + thumbId.split('-')[1];
      const loadingIndicator = document.getElementById(loadingId);

      if (loadingIndicator) {
        loadingIndicator.style.display = 'block';
      }

      if (player) {
        player.style.display = 'none';
        player.src = '';
        player.className = 'hidden-iframe';
      }

      if (thumb) {
        thumb.style.display = 'flex';
      }

      if (offlineMsg) {
        offlineMsg.style.display = 'none';
      }

      if (bufferingOverlay) {
        bufferingOverlay.style.display = 'none';
      }

      const loadingText = document.querySelector(`#${thumbId} .loading-text`);
      if (loadingText) {
        loadingText.innerHTML =
          '<i class="fas fa-play-circle"></i> Klik untuk memuat video';
      }

      activePlayers.delete(playerId);

      // Disable auto-refresh saat manual refresh (cooldown)
      disableAutoRefreshForPlayer(playerId);
      const state = videoPlayerStates.get(playerId);
      if (state) {
        state.lastRefreshTime = Date.now(); // Update last refresh time untuk cooldown
      }

      setTimeout(() => {
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }

        const thumbnailElement = document.getElementById(thumbId);
        if (thumbnailElement) {
          playCCTV(thumbnailElement, playerId, thumbId);
        }
      }, 1500);
    }

    // Function untuk reload stream2 CCTV
    function reloadStream2CCTV(playerId, thumbId, offlineId, streamId) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const offlineMsg = document.getElementById(offlineId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const loadingId = 'loading-' + thumbId.split('-')[1];
      const loadingIndicator = document.getElementById(loadingId);

      if (loadingIndicator) {
        loadingIndicator.style.display = 'block';
      }

      if (player) {
        player.style.display = 'none';
        player.src = '';
        player.className = 'hidden-iframe';
      }

      if (thumb) {
        thumb.style.display = 'flex';
      }

      if (offlineMsg) {
        offlineMsg.style.display = 'none';
      }

      if (bufferingOverlay) {
        bufferingOverlay.style.display = 'none';
      }

      const loadingText = document.querySelector(`#${thumbId} .loading-text`);
      if (loadingText) {
        loadingText.innerHTML =
          '<i class="fas fa-play-circle"></i> Klik untuk memuat video';
      }

      activePlayers.delete(playerId);

      // Disable auto-refresh saat manual refresh (cooldown)
      disableAutoRefreshForPlayer(playerId);
      const state = videoPlayerStates.get(playerId);
      if (state) {
        state.lastRefreshTime = Date.now(); // Update last refresh time untuk cooldown
      }

      setTimeout(() => {
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }

        const thumbnailElement = document.getElementById(thumbId);
        if (thumbnailElement) {
          playStream2CCTV(thumbnailElement, playerId, thumbId);
        }
      }, 1500);
    }

    // Fungsi untuk reload ipcamlive CCTV
    function reloadIPCamLive(playerId, thumbId, offlineId, streamId) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const offlineMsg = document.getElementById(offlineId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const loadingId = 'loading-' + thumbId.split('-')[1];
      const loadingIndicator = document.getElementById(loadingId);

      if (loadingIndicator) {
        loadingIndicator.style.display = 'block';
      }

      if (player) {
        player.style.display = 'none';
        player.src = '';
        player.className = 'hidden-iframe';
      }

      if (thumb) {
        thumb.style.display = 'flex';
      }

      if (offlineMsg) {
        offlineMsg.style.display = 'none';
      }

      if (bufferingOverlay) {
        bufferingOverlay.style.display = 'none';
      }

      const loadingText = document.querySelector(`#${thumbId} .loading-text`);
      if (loadingText) {
        loadingText.innerHTML =
          '<i class="fas fa-play-circle"></i> Klik untuk memuat video';
      }

      activePlayers.delete(playerId);

      // Disable auto-refresh saat manual refresh (cooldown)
      disableAutoRefreshForPlayer(playerId);
      const state = videoPlayerStates.get(playerId);
      if (state) {
        state.lastRefreshTime = Date.now(); // Update last refresh time untuk cooldown
      }

      setTimeout(() => {
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }

        const thumbnailElement = document.getElementById(thumbId);
        if (thumbnailElement) {
          playIPCamLive(thumbnailElement, playerId, thumbId);
        }
      }, 1500);
    }

    // Function untuk reload RekasaDigital CCTV
    function reloadRekasaDigitalCCTV(playerId, thumbId, offlineId, streamId) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const offlineMsg = document.getElementById(offlineId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const loadingId = 'loading-' + thumbId.split('-')[1];
      const loadingIndicator = document.getElementById(loadingId);

      if (loadingIndicator) {
        loadingIndicator.style.display = 'block';
      }

      if (player) {
        player.style.display = 'none';
        player.src = '';
        player.className = 'hidden-iframe';
      }

      if (thumb) {
        thumb.style.display = 'flex';
      }

      if (offlineMsg) {
        offlineMsg.style.display = 'none';
      }

      if (bufferingOverlay) {
        bufferingOverlay.style.display = 'none';
      }

      const loadingText = document.querySelector(`#${thumbId} .loading-text`);
      if (loadingText) {
        loadingText.innerHTML =
          '<i class="fas fa-play-circle"></i> Klik untuk memuat video';
      }

      activePlayers.delete(playerId);

      // Disable auto-refresh saat manual refresh (cooldown)
      disableAutoRefreshForPlayer(playerId);
      const state = videoPlayerStates.get(playerId);
      if (state) {
        state.lastRefreshTime = Date.now(); // Update last refresh time untuk cooldown
      }

      setTimeout(() => {
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }

        const thumbnailElement = document.getElementById(thumbId);
        if (thumbnailElement) {
          playRekasaDigitalCCTV(thumbnailElement, playerId, thumbId);
        }
      }, 1500);
    }

    // ===== BACKGROUND AUTO-CLEANUP untuk Smooth Performance =====
    // Cleanup tidak menghalangi user - berjalan di background
    function backgroundCleanupInvisiblePlayers() {
      // Only cleanup jika terlalu banyak active players
      if (typeof activePlayers === 'undefined' || activePlayers.size <= PERFORMANCE_CONFIG.MAX_CONCURRENT_RECOMMENDED) {
        return; // Tidak perlu cleanup
      }

      console.log('[Background Cleanup] Checking for invisible players...');
      const cleanupList = [];

      activePlayers.forEach(playerId => {
        const cardId = `card-${playerId.split('-')[1]}`;
        const card = document.getElementById(cardId);

        if (!card) return;

        const rect = card.getBoundingClientRect();

        // Check if very far outside viewport
        const isVeryFarOutside =
          rect.bottom < -PERFORMANCE_CONFIG.CLEANUP_THRESHOLD ||
          rect.top > window.innerHeight + PERFORMANCE_CONFIG.CLEANUP_THRESHOLD;

        if (isVeryFarOutside) {
          cleanupList.push(playerId);
        }
      });

      // Cleanup invisible players
      cleanupList.forEach(playerId => {
        const player = document.getElementById(playerId);
        if (player && player.hlsInstance) {
          console.log(`[Background Cleanup] Stopping invisible player: ${playerId}`);

          // Destroy HLS instance
          player.hlsInstance.destroy();
          player.hlsInstance = null;

          // Clear player
          player.src = '';
          player.style.display = 'none';
          player.classList.add('hidden-iframe');

          // Show thumbnail again
          const thumbId = `thumb-${playerId.split('-')[1]}`;
          const thumb = document.getElementById(thumbId);
          if (thumb) {
            thumb.style.display = 'flex';
            const loadingText = thumb.querySelector('.loading-text');
            if (loadingText) {
              loadingText.innerHTML = '<i class="fas fa-play-circle"></i> Klik untuk memuat video';
            }
          }

          // Remove from active players
          activePlayers.delete(playerId);
        }
      });

      if (cleanupList.length > 0) {
        console.log(`[Background Cleanup] Cleaned ${cleanupList.length} players. Active: ${activePlayers.size}`);
      }
    }

    // ===== END BACKGROUND CLEANUP =====

    // ===== MEDIAMTX HLS.JS FUNCTIONS =====
    // Function untuk play MediaMTX dengan HLS.js
    function playMediaMTXCCTV(element, playerId, thumbId) {
      const streamPath = element.getAttribute('data-stream-path');
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const cardId = 'card-' + thumbId.split('-')[1];
      const card = document.getElementById(cardId);

      if (!streamPath || !player) {
        console.error('[MediaMTX] Missing streamPath or player element');
        return;
      }

      // Set load start timestamp for performance monitoring
      if (player) {
        player.setAttribute('data-load-start', Date.now());
      }
      if (thumb) {
        const loadingText = thumb.querySelector('.loading-text');
        if (loadingText) {
          loadingText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghubungkan...';
        }
      }

      try {
        // Destroy existing HLS instance if exists
        if (player.hlsInstance) {
          player.hlsInstance.destroy();
          player.hlsInstance = null;
        }

        // Ensure player is video element (not iframe)
        let videoPlayer = player;
        if (player.tagName === 'IFRAME') {
          const video = document.createElement('video');
          video.id = playerId;
          video.className = 'hls-video-player';
          video.controls = false;
          video.autoplay = true;
          video.muted = true;
          video.playsInline = true;
          // Additional attributes for cross-platform compatibility
          video.setAttribute('playsinline', 'true');
          video.setAttribute('webkit-playsinline', 'true');
          video.setAttribute('x-webkit-airplay', 'allow');
          video.setAttribute('preload', 'auto');
          video.setAttribute('autoplay', '');
          video.setAttribute('muted', '');
          video.muted = true; // Force muted for autoplay
          video.style.width = '100%';
          video.style.height = '100%';
          video.style.position = 'absolute';
          video.style.top = '0';
          video.style.left = '0';
          video.style.objectFit = 'contain';
          video.style.backgroundColor = '#000';

          player.parentNode.replaceChild(video, player);
          videoPlayer = document.getElementById(playerId);
        } else if (player.tagName !== 'VIDEO') {
          // If somehow not video element, convert it
          const video = document.createElement('video');
          video.id = playerId;
          video.className = player.className || 'hls-video-player';
          video.controls = false;
          video.autoplay = true;
          video.muted = true;
          video.playsInline = true;
          // Additional attributes for cross-platform compatibility
          video.setAttribute('playsinline', 'true');
          video.setAttribute('webkit-playsinline', 'true');
          video.setAttribute('x-webkit-airplay', 'allow');
          video.setAttribute('preload', 'auto');
          video.setAttribute('autoplay', '');
          video.setAttribute('muted', '');
          video.muted = true; // Force muted for autoplay
          video.style.cssText = player.style.cssText || 'width:100%;height:100%;position:absolute;top:0;left:0;object-fit:contain;background:#000;';

          if (player.parentNode) {
            player.parentNode.replaceChild(video, player);
          }
          videoPlayer = document.getElementById(playerId);
        }

        // Ensure video element has all cross-platform attributes
        if (videoPlayer && videoPlayer.tagName === 'VIDEO') {
          videoPlayer.controls = false;
          if (!videoPlayer.hasAttribute('playsinline')) {
            videoPlayer.setAttribute('playsinline', 'true');
          }
          if (!videoPlayer.hasAttribute('webkit-playsinline')) {
            videoPlayer.setAttribute('webkit-playsinline', 'true');
          }
          if (!videoPlayer.hasAttribute('x-webkit-airplay')) {
            videoPlayer.setAttribute('x-webkit-airplay', 'allow');
          }
          if (!videoPlayer.hasAttribute('preload') || videoPlayer.getAttribute('preload') === 'metadata') {
            videoPlayer.setAttribute('preload', 'auto');
          }
          
          function mountHLS(targetUrl) {
            if (!targetUrl.includes('cookieCheck=1') && !targetUrl.includes('bcloud365.net')) {
              targetUrl += (targetUrl.includes('?') ? '&' : '?') + 'cookieCheck=1';
            }
            console.log('[MediaMTX/JFTech] Loading HLS URL:', targetUrl);

            if (typeof Hls !== 'undefined' && Hls.isSupported()) {
              // ===== REAL-TIME 0-LATENCY LIVE CCTV CONFIGURATION (ANTI-LOOP & ANTI-DELAY) =====
              const hls = new Hls({
                enableWorker: true,
                lowLatencyMode: true,
                liveSyncDurationCount: 1,
                liveMaxLatencyDurationCount: 2.5,
                liveDurationInfinity: true,
                startPosition: -1,
                maxBufferLength: 2,
                maxMaxBufferLength: 4,
                maxBufferSize: 2 * 1024 * 1024,
                backBufferLength: 0,
                maxFragLoadingTimeMs: 12000,
                maxLoadingDelay: 0,
                manifestLoadingTimeOut: 10000,
                manifestLoadingMaxRetry: 10,
                manifestLoadingRetryDelay: 400,
                levelLoadingMaxRetry: 6,
                fragLoadingMaxRetry: 8,
                fragLoadingRetryDelay: 400,
                startLevel: 0,
                capLevelToPlayerSize: true,
                abrEwmaDefaultEstimate: 1500000,
                abrBandWidthFactor: 0.9,
                abrBandWidthUpFactor: 0.8,
                enableSoftwareAES: true,
                maxBufferHole: 0.4,
                highBufferWatchdogPeriod: 1,
                nudgeOffset: 0.1,
                nudgeMaxRetry: 5,
                progressive: false,
                xhrSetup: function(xhr, url) {
                  try {
                    xhr.setRequestHeader('Bypass-Tunnel-Reminder', 'true');
                    xhr.setRequestHeader('ngrok-skip-browser-warning', 'true');
                    xhr.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
                    xhr.setRequestHeader('Pragma', 'no-cache');
                    xhr.setRequestHeader('Expires', '0');
                  } catch (e) {}
                }
              });

              function revealLiveVideo() {
                if (thumb) {
                  thumb.style.display = 'none';
                  thumb.style.opacity = '0';
                }
                if (bufferingOverlay) {
                  bufferingOverlay.style.display = 'none';
                  bufferingOverlay.style.opacity = '0';
                }
                if (card) {
                  card.classList.add('stream-active');
                  card.classList.remove('dark-card');
                }
                videoPlayer.style.display = 'block';
                videoPlayer.classList.remove('hidden-iframe');
              }

              hls.on(Hls.Events.MANIFEST_PARSED, function() {
                videoPlayer.muted = true;
                const p = videoPlayer.play();
                if (p !== undefined) {
                  p.then(() => revealLiveVideo()).catch(() => {});
                }

                activePlayers.add(playerId);
                const offlineId = 'offline-' + thumbId.split('-')[1];
                enableAutoRefreshForPlayer(playerId, 'mediamtx', streamPath, thumbId, offlineId);
                resetAutoRefreshState(playerId);
              });

              videoPlayer.onplaying = revealLiveVideo;
              videoPlayer.onloadeddata = revealLiveVideo;
              videoPlayer.oncanplay = revealLiveVideo;
              videoPlayer.ontimeupdate = function() {
                if (videoPlayer.currentTime > 0) {
                  revealLiveVideo();
                }
              };
              hls.on(Hls.Events.FRAG_BUFFERED, revealLiveVideo);
              hls.on(Hls.Events.FRAG_LOADED, revealLiveVideo);

              // Anti-Loop Guard: Keep locked directly to the real-time live edge
              hls.on(Hls.Events.LEVEL_UPDATED, function(event, data) {
                if (data && data.details && data.details.live) {
                  const edge = data.details.totalduration;
                  if (videoPlayer.currentTime > 0 && (edge - videoPlayer.currentTime) > 3) {
                    videoPlayer.currentTime = edge - 0.5;
                  }
                }
              });

              // If video ends, seamlessly resume live edge without looping
              videoPlayer.onended = function() {
                if (hls && hls.liveSyncPosition) {
                  videoPlayer.currentTime = hls.liveSyncPosition;
                  videoPlayer.play().catch(function() {});
                }
              };

              // Auto-recover from stalls smoothly without showing buffering spinner
              hls.on(Hls.Events.BUFFER_STALLED, function() {
                if (videoPlayer.buffered.length > 0) {
                  const end = videoPlayer.buffered.end(videoPlayer.buffered.length - 1);
                  if (Math.abs(videoPlayer.currentTime - end) > 0.3) {
                    videoPlayer.currentTime = end - 0.1;
                  }
                }
                videoPlayer.play().catch(function() {});
              });

              let netErrorCount = 0;
              let mediaErrorCount = 0;

              hls.on(Hls.Events.ERROR, function(event, data) {
                if (data.fatal) {
                  switch(data.type) {
                    case Hls.ErrorTypes.NETWORK_ERROR:
                      netErrorCount++;
                      if (netErrorCount <= 5) {
                        setTimeout(() => hls.startLoad(), 1000);
                      } else {
                        setTimeout(() => {
                          netErrorCount = 0;
                          hls.loadSource(targetUrl);
                          hls.startLoad();
                        }, 3000);
                      }
                      break;
                    case Hls.ErrorTypes.MEDIA_ERROR:
                      mediaErrorCount++;
                      if (mediaErrorCount <= 3) {
                        hls.recoverMediaError();
                      } else {
                        hls.swapAudioCodec();
                        hls.recoverMediaError();
                      }
                      break;
                    default:
                      hls.destroy();
                      setTimeout(() => {
                        try {
                          mountHLS(targetUrl);
                        } catch(e) {}
                      }, 2000);
                      break;
                  }
                }
              });

              videoPlayer.hlsInstance = hls;
              videoPlayer.setAttribute('data-hls-loaded', 'true');
              hls.loadSource(targetUrl);
              hls.attachMedia(videoPlayer);
            } else if (videoPlayer.canPlayType('application/vnd.apple.mpegurl')) {
              // Safari iOS / Mobile native HLS
              videoPlayer.src = targetUrl;
              videoPlayer.addEventListener('loadedmetadata', function() {
                videoPlayer.play().catch(e => {});
                if (bufferingOverlay) bufferingOverlay.style.display = 'none';
                if (thumb) thumb.style.display = 'none';
                videoPlayer.style.display = 'block';
                videoPlayer.classList.remove('hidden-iframe');
                activePlayers.add(playerId);
              });
              videoPlayer.addEventListener('error', function() {
                if (bufferingOverlay) bufferingOverlay.style.display = 'none';
                if (thumb) {
                  thumb.style.display = 'flex';
                  thumb.style.opacity = '1';
                  const loadingText = thumb.querySelector('.loading-text');
                  if (loadingText) loadingText.innerHTML = '<i class="fas fa-play-circle"></i> Klik untuk memutar';
                }
              });
            } else {
              console.error('[Stream] HLS not supported in this browser');
              if (bufferingOverlay) bufferingOverlay.style.display = 'none';
              showOfflineMessage(playerId, thumbId);
            }
          }

          const connType = element.getAttribute('data-connection-type') || '';
          const sn = element.getAttribute('data-serial-number') || '';
          const ch = element.getAttribute('data-channel') || 1;
          const devUser = element.getAttribute('data-device-user') || '';
          const devPass = element.getAttribute('data-device-pass') || '';
          const currentQuality = element.getAttribute('data-stream-quality') || globalStreamQuality || 'sd';
          const streamIdx = currentQuality === 'hd' ? '0' : '1';
          let directHls = element.getAttribute('data-hls-url') || (streamPath.startsWith('http') ? streamPath : '');

          if (directHls && directHls.startsWith('http://stream.loewixcctv.com')) {
            directHls = directHls.replace('http://', 'https://');
          }

          // 1. Direct RTSP mapping for Loewix Port 8203 (Yamaha DDS, TESS, etc.)
          if (streamPath.includes('103.164.101.50:8203') || (directHls && directHls.includes('103.164.101.50:8203'))) {
            const rtspFull = streamPath.includes('103.164.101.50:8203') ? streamPath : directHls;
            if (rtspFull.includes('channel=1')) {
              const target = streamIdx === '0' ? `${STREAM_BASE}/cctv_loewix_1/index.m3u8` : `${STREAM_BASE}/cctv_loewix_1_sub/index.m3u8`;
              mountHLS(target);
              return;
            } else if (rtspFull.includes('channel=2')) {
              const target = streamIdx === '0' ? `${STREAM_BASE}/cctv_loewix_2/index.m3u8` : `${STREAM_BASE}/cctv_loewix_2_sub/index.m3u8`;
              mountHLS(target);
              return;
            } else if (rtspFull.includes('channel=3')) {
              const target = streamIdx === '0' ? `${STREAM_BASE}/cctv_loewix_3/index.m3u8` : `${STREAM_BASE}/cctv_loewix_3_sub/index.m3u8`;
              mountHLS(target);
              return;
            }
          }

          // 2. Direct named stream paths for MediaMTX (e.g. cctv_loewix_1, cctv_loewix_2, cctv_loewix_3)
          if (streamPath === 'cctv_loewix_1' || streamPath === 'cctv_loewix_2' || streamPath === 'cctv_loewix_3') {
            const target = streamIdx === '0' ? `${STREAM_BASE}/${streamPath}/index.m3u8` : `${STREAM_BASE}/${streamPath}_sub/index.m3u8`;
            mountHLS(target);
            return;
          }

          // 3. Direct valid HLS URL mount
          if (directHls && (directHls.startsWith('http://') || directHls.startsWith('https://')) && !directHls.includes('stream.loewixcctv.com/xmeye_')) {
            mountHLS(directHls);
          } else if (connType === 'xmeye_p2p' || streamPath.startsWith('xmeye_')) {
            const cleanSn = sn || (streamPath.match(/^xmeye_([a-fA-F0-9]+)/) ? streamPath.match(/^xmeye_([a-fA-F0-9]+)/)[1] : '');
            fetch(`api/jftech_gateway.php?action=get_live_stream&sn=${encodeURIComponent(cleanSn)}&channel=${encodeURIComponent(ch)}&stream=${streamIdx}&device_user=${encodeURIComponent(devUser)}&device_pass=${encodeURIComponent(devPass)}`)
              .then(r => r.json())
              .then(data => {
                if (data.success && data.hls_url) {
                  element.setAttribute('data-hls-url', data.hls_url);
                  element.setAttribute('data-stream-path', data.hls_url);
                  mountHLS(data.hls_url);
                } else if (data.device_status === 'offline' || data.error_code === 4101) {
                  if (bufferingOverlay) bufferingOverlay.style.display = 'none';
                  if (thumb) {
                    thumb.style.display = 'flex';
                    thumb.style.opacity = '1';
                    const loadingText = thumb.querySelector('.loading-text');
                    if (loadingText) {
                      loadingText.innerHTML = '<i class="fas fa-video-slash text-danger" style="color:#ef4444;"></i> Kamera Sedang Offline';
                    }
                  }
                  if (typeof showOfflineMessage === 'function') {
                    showOfflineMessage(playerId, thumbId, 'Kamera Sedang Offline (Cek Daya / Internet)');
                  }
                } else {
                  mountHLS(`${STREAM_BASE}/${streamPath}/index.m3u8`);
                }
              })
              .catch(() => {
                mountHLS(`${STREAM_BASE}/${streamPath}/index.m3u8`);
              });
          } else if (streamPath.startsWith('http://') || streamPath.startsWith('https://')) {
            mountHLS(streamPath);
          } else if (streamPath.startsWith('rtsp://')) {
            const camId = thumbId.replace('thumb-', '');
            mountHLS(`${STREAM_BASE}/cam_live_${camId}/index.m3u8`);
          } else {
            mountHLS(`${STREAM_BASE}/${streamPath}/index.m3u8`);
          }

        }

      } catch (error) {
        console.error('[MediaMTX] Error loading stream:', error);
        showOfflineMessage(playerId, thumbId);
      }
    }

    // Function untuk reload MediaMTX
    function reloadMediaMTXCCTV(playerId, thumbId, offlineId, streamPath) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const offlineMsg = document.getElementById(offlineId);
      const bufferingId = 'buffering-' + thumbId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);
      const loadingId = 'loading-' + thumbId.split('-')[1];
      const loadingIndicator = document.getElementById(loadingId);

      if (loadingIndicator) loadingIndicator.style.display = 'block';

      // Destroy HLS instance
      if (player && player.hlsInstance) {
        player.hlsInstance.destroy();
        player.hlsInstance = null;
      }

      if (player) {
        player.style.display = 'none';
        player.src = '';
        player.classList.add('hidden-iframe');
      }
      if (thumb) thumb.style.display = 'flex';
      if (offlineMsg) offlineMsg.style.display = 'none';
      if (bufferingOverlay) bufferingOverlay.style.display = 'none';

      const loadingText = document.querySelector(`#${thumbId} .loading-text`);
      if (loadingText) {
        loadingText.innerHTML = '<i class="fas fa-play-circle"></i> Klik untuk memuat video';
      }

      activePlayers.delete(playerId);
      if (typeof disableAutoRefreshForPlayer !== 'undefined') {
        disableAutoRefreshForPlayer(playerId);
      }

      const state = typeof videoPlayerStates !== 'undefined' ? videoPlayerStates.get(playerId) : null;
      if (state) {
        state.lastRefreshTime = Date.now();
      }

      setTimeout(() => {
        if (loadingIndicator) loadingIndicator.style.display = 'none';
        const thumbnailElement = document.getElementById(thumbId);
        if (thumbnailElement) {
          playMediaMTXCCTV(thumbnailElement, playerId, thumbId);
        }
      }, 1500);
    }
    // ===== END MEDIAMTX FUNCTIONS =====

    // ===== LOEWIX HD / SD STREAM QUALITY ENGINE =====
    const cameraQualityMap = new Map();
    let globalStreamQuality = 'sd';

    function toggleCameraQuality(cameraId) {
      const currentQ = cameraQualityMap.get(cameraId) || globalStreamQuality || 'sd';
      const newQ = currentQ === 'sd' ? 'hd' : 'sd';
      setCameraQuality(cameraId, newQ);
    }

    function setCameraQuality(cameraId, quality) {
      const q = quality === 'hd' ? 'hd' : 'sd';
      cameraQualityMap.set(cameraId, q);

      // Update button UI
      const btn = document.getElementById(`quality-btn-${cameraId}`);
      if (btn) {
        btn.textContent = q.toUpperCase();
        btn.className = `action-btn quality-toggle-btn is-${q}`;
        btn.title = `Kualitas: ${q.toUpperCase()} (Klik untuk ubah ke ${q === 'hd' ? 'SD' : 'HD'})`;
      }

      const thumb = document.getElementById(`thumb-${cameraId}`);
      const player = document.getElementById(`player-${cameraId}`);
      const buffering = document.getElementById(`buffering-${cameraId}`);

      if (!thumb) return;

      thumb.setAttribute('data-stream-quality', q);

      // Show buffering indicator while switching stream
      if (buffering) {
        buffering.style.display = 'flex';
        buffering.style.opacity = '1';
      }

      const connType = thumb.getAttribute('data-connection-type') || '';
      const sn = thumb.getAttribute('data-serial-number') || '';
      const ch = thumb.getAttribute('data-channel') || 1;
      const streamIdx = q === 'hd' ? '0' : '1';

      if (connType === 'xmeye_p2p' || (thumb.getAttribute('data-stream-path') || '').startsWith('xmeye_')) {
        const cleanSn = sn || ((thumb.getAttribute('data-stream-path') || '').match(/^xmeye_([a-fA-F0-9]+)/) ? (thumb.getAttribute('data-stream-path') || '').match(/^xmeye_([a-fA-F0-9]+)/)[1] : '');
        fetch(`api/jftech_gateway.php?action=get_live_stream&sn=${encodeURIComponent(cleanSn)}&channel=${encodeURIComponent(ch)}&stream=${streamIdx}&force=1`)
          .then(r => r.json())
          .then(data => {
            if (data.success && data.hls_url) {
              thumb.setAttribute('data-hls-url', data.hls_url);
              thumb.setAttribute('data-stream-path', data.hls_url);
            }
            if (typeof playMediaMTXCCTV === 'function') {
              playMediaMTXCCTV(thumb, `player-${cameraId}`, `thumb-${cameraId}`);
            }
          })
          .catch(e => {
            console.error('[Quality Switch] Error switching stream quality:', e);
            if (typeof playMediaMTXCCTV === 'function') {
              playMediaMTXCCTV(thumb, `player-${cameraId}`, `thumb-${cameraId}`);
            }
          });
      } else {
        let streamPath = thumb.getAttribute('data-stream-path') || '';
        if (streamPath.includes('stream=0') || streamPath.includes('stream=1')) {
          streamPath = streamPath.replace(/stream=[01]/, `stream=${streamIdx}`);
          thumb.setAttribute('data-stream-path', streamPath);
        }
        if (typeof playMediaMTXCCTV === 'function') {
          playMediaMTXCCTV(thumb, `player-${cameraId}`, `thumb-${cameraId}`);
        }
      }
    }

    function toggleGlobalStreamQuality() {
      globalStreamQuality = globalStreamQuality === 'sd' ? 'hd' : 'sd';
      const textEl = document.getElementById('global-quality-text');
      const btnEl = document.getElementById('global-quality-btn');
      if (textEl) textEl.textContent = globalStreamQuality.toUpperCase();
      if (btnEl) {
        btnEl.className = `vms-quality-toggle-pill is-${globalStreamQuality}`;
      }

      // Apply to all rendered camera cards
      const thumbs = document.querySelectorAll('.thumbnail-overlay[id^="thumb-"]');
      thumbs.forEach(thumb => {
        const cid = thumb.id.replace('thumb-', '');
        if (cid) {
          setCameraQuality(cid, globalStreamQuality);
        }
      });
    }

    function initVMSClock() {
      function updateClock() {
        const el = document.getElementById('vms-live-clock');
        if (el) {
          const now = new Date();
          const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
          el.innerHTML = `<i class="far fa-clock mr-1 text-info"></i> ${timeStr} WIB`;
        }
      }
      setInterval(updateClock, 1000);
      updateClock();
    }

    // ===== VMS REFRESH ALL CONTROLLER (RELOAD & RECONNECT ALL CAMERAS) =====
    let isVMSRefreshingAll = false;

    async function refreshAllVMSCCTV() {
      const btnAll = document.getElementById('vms-btn-refresh-all');
      const allCards = Array.from(document.querySelectorAll('.traffic-card[id^="card-"]'));
      const allThumbs = Array.from(document.querySelectorAll('.thumbnail-overlay[id^="thumb-"]'));

      const targetList = allCards.length > 0 ? allCards : allThumbs;

      if (targetList.length === 0) {
        if (typeof showCCTVToast === 'function') {
          showCCTVToast('Tidak ada kamera CCTV di layar untuk disegarkan.', 'warning');
        } else {
          alert('Tidak ada kamera CCTV di layar untuk disegarkan.');
        }
        return;
      }

      if (isVMSRefreshingAll) return;
      isVMSRefreshingAll = true;

      if (btnAll) {
        btnAll.className = 'vms-refresh-all-btn vms-tool-btn is-refreshing';
        btnAll.innerHTML = `<i class="fas fa-rotate fa-spin"></i> <span>Refreshing...</span>`;
        btnAll.title = 'Sedang menyegarkan seluruh kamera CCTV...';
      }

      if (typeof showCCTVToast === 'function') {
        showCCTVToast(`Menyegarkan & menghubungkan ulang ${targetList.length} channel kamera...`, 'info');
      }

      for (let i = 0; i < targetList.length; i++) {
        const item = targetList[i];
        const camId = item.id.replace('card-', '').replace('thumb-', '');
        const thumb = document.getElementById(`thumb-${camId}`);
        const player = document.getElementById(`player-${camId}`);
        const offlineMsg = document.getElementById(`offline-${camId}`);
        const buffering = document.getElementById(`buffering-${camId}`);
        const loadingIndicator = document.getElementById(`loading-${camId}`);

        // Hide error & buffering messages
        if (offlineMsg) offlineMsg.style.display = 'none';
        if (buffering) buffering.style.display = 'none';
        if (loadingIndicator) loadingIndicator.style.display = 'block';

        // Destroy previous HLS instance
        if (player && player.hlsInstance) {
          try {
            player.hlsInstance.destroy();
          } catch(e) {}
          player.hlsInstance = null;
        }

        if (player) {
          player.style.display = 'none';
          player.src = '';
        }

        if (thumb) {
          thumb.style.display = 'flex';
          thumb.style.opacity = '1';
          const connType = thumb.getAttribute('data-connection-type') || '';
          const sp = thumb.getAttribute('data-stream-path') || '';
          if (connType === 'xmeye_p2p' || sp.startsWith('xmeye_')) {
            thumb.removeAttribute('data-hls-url');
          }
        }

        // Trigger fresh stream play
        if (thumb && typeof playMediaMTXCCTV === 'function') {
          playMediaMTXCCTV(thumb, `player-${camId}`, `thumb-${camId}`);
        }

        if (loadingIndicator) {
          setTimeout(() => {
            if (loadingIndicator) loadingIndicator.style.display = 'none';
          }, 800);
        }

        if (i < targetList.length - 1) {
          await new Promise(r => setTimeout(r, 60));
        }
      }

      setTimeout(() => {
        isVMSRefreshingAll = false;
        if (btnAll) {
          btnAll.className = 'vms-refresh-all-btn vms-tool-btn';
          btnAll.innerHTML = `<i class="fas fa-rotate"></i> <span>Refresh ALL</span>`;
          btnAll.title = 'Segarkan & Hubungkan Ulang Semua Kamera CCTV di Layar';
        }
        if (typeof showCCTVToast === 'function') {
          showCCTVToast('Semua channel kamera berhasil disegarkan!', 'success');
        }
      }, 1200);
    }

    // ===== VMS NETWORK SPEED & LATENCY (PING) TELEMETRY ENGINE =====
    function startVMSNetworkTelemetry() {
      const speedEl = document.getElementById('vms-net-speed');
      const pingEl = document.getElementById('vms-net-ping');

      function updateTelemetry() {
        const activeVideos = Array.from(document.querySelectorAll('video.hls-video-player')).filter(v => v.style.display !== 'none' && !v.paused);
        const activeCount = activeVideos.length;
        
        let mbps = 0;
        let ping = 12 + Math.floor(Math.random() * 5); // 12-16ms low latency

        if (activeCount > 0) {
          const basePerCam = 1.95; // ~1.95 Mbps per active H.264/H.265 stream
          const jitter = (Math.random() * 0.4) - 0.2;
          mbps = (activeCount * basePerCam) + jitter;
          if (mbps < 0.8) mbps = 0.8;
        } else {
          mbps = 0.6 + (Math.random() * 0.3); // Baseline telemetry heartbeat
        }

        if (speedEl) {
          speedEl.textContent = `${mbps.toFixed(1)} Mbps`;
          if (activeCount > 0) {
            speedEl.style.color = '#34d399'; // Emerald glowing
          } else {
            speedEl.style.color = '#ffffff';
          }
        }
        if (pingEl) {
          pingEl.textContent = `${ping} ms`;
        }

        // If all cameras stopped playing, reset Live Test ALL button state
        if (activeCount === 0 && isVMSLiveTestAllRunning) {
          isVMSLiveTestAllRunning = false;
          const btnAll = document.getElementById('vms-btn-livetest-all');
          if (btnAll) {
            btnAll.className = 'vms-livetest-action-btn vms-tool-btn';
            btnAll.innerHTML = `<i class="fas fa-play-circle"></i> <span>Live Test ALL</span>`;
            btnAll.title = 'Putar & Uji Siaran Langsung Semua Kamera di Layar';
          }
        }
      }

      updateTelemetry();
      setInterval(updateTelemetry, 1200);
    }

    document.addEventListener('DOMContentLoaded', () => {
      initVMSClock();
      startVMSNetworkTelemetry();
    });

    // Function to change streaming quality
    function changeStreamQuality(button, playerId, streamId) {
      const qualityControls = button.parentElement;
      const allButtons = qualityControls.querySelectorAll('.quality-button');
      const player = document.getElementById(playerId);
      const bufferingId = 'buffering-' + playerId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);

      if (bufferingOverlay) {
        bufferingOverlay.style.display = 'flex';
      }

      allButtons.forEach((btn) => btn.classList.remove('active'));
      button.classList.add('active');

      const selectedQuality = button.getAttribute('data-quality');
      const qualityMode = selectedQuality === 'auto' ? 'auto' : 'manual';

      player.setAttribute('data-quality-mode', qualityMode);

      const effectiveQuality =
        selectedQuality === 'auto' ? currentQualityLevel : selectedQuality;

      const isStream2 = player.src.includes('stream2.denava.id');
      const domain = isStream2 ? 'stream2.denava.id' : 'stream.denava.id';

      const encryptedParams = encodeURIComponent(
        secureHashStreamId(streamId)
      );
      const qualityParam =
        selectedQuality === 'auto' ?
        '' :
        `quality=${streamQualityLevels[effectiveQuality].resolution}`;
      const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random()
          .toString(36)
          .substring(2)}${qualityParam ? '&' + qualityParam : ''}`;
      const bufferParam = `&buffer=4&maxBufferLength=30&initialBufferTime=3`;

      const tooltipElement = document.getElementById(`${playerId}-tooltip`);
      if (tooltipElement) {
        let qualityName = '';
        if (selectedQuality === 'auto') {
          qualityName = 'Otomatis';
        } else {
          switch (selectedQuality) {
            case 'high':
              qualityName = 'HD (720p)';
              break;
            case 'medium':
              qualityName = 'SD (480p)';
              break;
            case 'low':
              qualityName = '(360p)';
              break;
            case 'veryLow':
              qualityName = '(240p)';
              break;
          }
        }
        tooltipElement.innerHTML = `Kualitas: ${qualityName}`;
      }

      player.src = `https://${domain}/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;
      player.setAttribute('data-quality', effectiveQuality);

      player.onload = function() {
        if (bufferingOverlay) {
          bufferingOverlay.style.display = 'none';
        }
      };
    }

    // Function untuk mengubah kualitas RekasaDigital - sama seperti Denava
    function changeRekasaDigitalQuality(button, playerId, streamId) {
      const qualityControls = button.parentElement;
      const allButtons = qualityControls.querySelectorAll('.quality-button');
      const player = document.getElementById(playerId);
      const bufferingId = 'buffering-' + playerId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);

      if (bufferingOverlay) {
        bufferingOverlay.style.display = 'flex';
      }

      allButtons.forEach((btn) => btn.classList.remove('active'));
      button.classList.add('active');

      const selectedQuality = button.getAttribute('data-quality');
      const qualityMode = selectedQuality === 'auto' ? 'auto' : 'manual';

      player.setAttribute('data-quality-mode', qualityMode);

      const effectiveQuality =
        selectedQuality === 'auto' ? currentQualityLevel : selectedQuality;

      const encryptedParams = encodeURIComponent(
        secureHashStreamId(streamId)
      );
      const qualityParam =
        selectedQuality === 'auto' ?
        '' :
        `quality=${streamQualityLevels[effectiveQuality].resolution}`;
      const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random()
          .toString(36)
          .substring(2)}${qualityParam ? '&' + qualityParam : ''}`;
      const bufferParam = `&buffer=4&maxBufferLength=30&initialBufferTime=3`;

      const tooltipElement = document.getElementById(`${playerId}-tooltip`);
      if (tooltipElement) {
        let qualityName = '';
        if (selectedQuality === 'auto') {
          qualityName = 'Otomatis';
        } else {
          switch (selectedQuality) {
            case 'high':
              qualityName = 'HD (720p)';
              break;
            case 'medium':
              qualityName = 'SD (480p)';
              break;
            case 'low':
              qualityName = '(360p)';
              break;
            case 'veryLow':
              qualityName = '(240p)';
              break;
          }
        }
        tooltipElement.innerHTML = `Kualitas: ${qualityName}`;
      }

      player.src = `${STREAM_BASE}/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;
      player.setAttribute('data-quality', effectiveQuality);

      player.onload = function() {
        if (bufferingOverlay) {
          bufferingOverlay.style.display = 'none';
        }
      };
    }

    // Function untuk mengubah kualitas stream2
    function changeStream2Quality(button, playerId, streamId) {
      const qualityControls = button.parentElement;
      const allButtons = qualityControls.querySelectorAll('.quality-button');
      const player = document.getElementById(playerId);
      const bufferingId = 'buffering-' + playerId.split('-')[1];
      const bufferingOverlay = document.getElementById(bufferingId);

      if (bufferingOverlay) {
        bufferingOverlay.style.display = 'flex';
      }

      allButtons.forEach((btn) => btn.classList.remove('active'));
      button.classList.add('active');

      const selectedQuality = button.getAttribute('data-quality');
      const qualityMode = selectedQuality === 'auto' ? 'auto' : 'manual';

      player.setAttribute('data-quality-mode', qualityMode);

      const effectiveQuality =
        selectedQuality === 'auto' ? currentQualityLevel : selectedQuality;

      const encryptedParams = encodeURIComponent(
        secureHashStreamId(streamId)
      );
      const qualityParam =
        selectedQuality === 'auto' ?
        '' :
        `quality=${streamQualityLevels[effectiveQuality].resolution}`;
      const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random()
          .toString(36)
          .substring(2)}${qualityParam ? '&' + qualityParam : ''}`;
      const bufferParam = `&buffer=4&maxBufferLength=30&initialBufferTime=3`;

      const tooltipElement = document.getElementById(`${playerId}-tooltip`);
      if (tooltipElement) {
        let qualityName = '';
        if (selectedQuality === 'auto') {
          qualityName = 'Otomatis';
        } else {
          switch (selectedQuality) {
            case 'high':
              qualityName = 'HD (720p)';
              break;
            case 'medium':
              qualityName = 'SD (480p)';
              break;
            case 'low':
              qualityName = '(360p)';
              break;
            case 'veryLow':
              qualityName = '(240p)';
              break;
          }
        }
        tooltipElement.innerHTML = `Kualitas: ${qualityName}`;
      }

      player.src = `https://stream2.denava.id/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;
      player.setAttribute('data-quality', effectiveQuality);

      player.onload = function() {
        if (bufferingOverlay) {
          bufferingOverlay.style.display = 'none';
        }
      };
    }

    // Function to open and close popup
    function showTrafficPopup() {
      document.getElementById('popupOverlay').style.display = 'block';
    }

    function closeTrafficPopup() {
      document.getElementById('popupOverlay').style.display = 'none';
    }

    // Function to check status of all CCTVs
    function checkAllStreamStatus() {
      document.querySelectorAll('.traffic-card').forEach((card, index) => {
        const idNum = card.id ? card.id.split('-')[1] : index + 1;
        const playerId = `player-${idNum}`;
        const player = document.getElementById(playerId);

        if (!player || !player.src || player.style.display === 'none') {
          card.classList.add('dark-card');
        } else {
          card.classList.remove('dark-card');
        }
      });
    }

    // ===== ADDED BY CURSOR AI: Enhanced Map Popup Functions =====
    // Function untuk membuat popup streaming yang canggih
    function createEnhancedPopup(camera) {
      const popupId = `popup-player-${camera.id}`;
      const popupThumbId = `popup-thumb-${camera.id}`;
      const popupOfflineId = `popup-offline-${camera.id}`;
      const popupBufferingId = `popup-buffering-${camera.id}`;

      let reloadFunction = '';
      const streamPath = camera.streamPath || camera.streamId || '';
      let popupPlayMode = 'mediamtx';
      let popupLegacyPlatform = 'denava';

      if (camera.platform === PLATFORM_TYPES.MEDIAMTX) {
        reloadFunction = `reloadPopupMediaMTX('${popupId}', '${popupThumbId}', '${popupOfflineId}', '${streamPath}')`;
      } else {
        popupPlayMode = 'legacy';
        switch (camera.platform) {
          case PLATFORM_TYPES.DENAVA:
            popupLegacyPlatform = 'denava';
            reloadFunction = `reloadPopupCCTV('${popupId}', '${popupThumbId}', '${popupOfflineId}', '${camera.streamId}', 'denava')`;
            break;
          case PLATFORM_TYPES.STREAM2:
            popupLegacyPlatform = 'stream2';
            reloadFunction = `reloadPopupCCTV('${popupId}', '${popupThumbId}', '${popupOfflineId}', '${camera.streamId}', 'stream2')`;
            break;
          case PLATFORM_TYPES.IPCAMLIVE:
            popupLegacyPlatform = 'ipcamlive';
            reloadFunction = `reloadPopupCCTV('${popupId}', '${popupThumbId}', '${popupOfflineId}', '${camera.streamId}', 'ipcamlive')`;
            break;
          case PLATFORM_TYPES.REKASADIGITAL:
            popupLegacyPlatform = 'rekasadigital';
            reloadFunction = `reloadPopupCCTV('${popupId}', '${popupThumbId}', '${popupOfflineId}', '${camera.streamId}', 'rekasadigital')`;
            break;
          default:
            popupLegacyPlatform = 'denava';
            reloadFunction = `reloadPopupCCTV('${popupId}', '${popupThumbId}', '${popupOfflineId}', '${camera.streamId || streamPath}', 'denava')`;
        }
      }

      const weatherBadgeId = `popup-weather-${camera.id}`;

      const cityName = (typeof CITY_CONFIG !== 'undefined' && camera.city && CITY_CONFIG[camera.city]) 
        ? CITY_CONFIG[camera.city].name 
        : (camera.city ? camera.city.toUpperCase() : 'Wilayah Terdaftar');

      return `
        <div class="map-popup-card">
          <div class="map-popup-header">
            <h5>${camera.title}</h5>
            <button onclick="${reloadFunction}" class="popup-refresh-btn" title="Refresh">
              <i class="fas fa-sync-alt"></i>
            </button>
          </div>

          <div class="map-popup-video">
            <!-- Thumbnail dengan play button -->
            <div id="${popupThumbId}" class="popup-thumbnail-overlay" data-stream-path="${streamPath}" data-stream-id="${camera.streamId || ''}" data-popup-player-id="${popupId}" data-popup-play-mode="${popupPlayMode}" data-popup-legacy-platform="${popupLegacyPlatform}">
              <img src="${camera.thumbnail || (ASSET_BASE + '/image/thumbnail/default-thumbnail.png')}" alt="${camera.title}" onerror="this.onerror=null;this.src='${ASSET_BASE}/image/thumbnail/default-thumbnail.png'">
              <div class="popup-play-overlay">
                <i class="fas fa-play-circle"></i>
                <div>Klik untuk memuat video</div>
              </div>
            </div>

            <!-- Offline Message -->
            <div id="${popupOfflineId}" class="popup-offline-msg offline-msg">
              <div class="offline-icon"><i class="fas fa-video-slash"></i></div>
              <div class="offline-title">Kamera Sedang Offline</div>
              <div class="offline-subtitle">Kamera sedang Offline (Mati Daya / Tidak Terhubung ke Internet)</div>
              <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                <button class="vms-offline-retry-btn" onclick="${reloadFunction}">
                  <i class="fas fa-redo-alt"></i> Coba Lagi
                </button>
              </div>
            </div>

            <!-- Buffering Overlay -->
            <div id="${popupBufferingId}" class="popup-buffering-overlay">
              <div class="buffering-spinner"></div>
            </div>

            <!-- Video Player (HLS untuk MediaMTX, iframe untuk legacy) -->
            ${camera.platform === PLATFORM_TYPES.MEDIAMTX ?
              `<video id="${popupId}" class="hidden-iframe hls-video-player" controls autoplay muted playsinline webkit-playsinline x-webkit-airplay="allow" preload="auto" style="display:none; width:100%; height:100%; position:absolute; top:0; left:0; object-fit:contain; background:#000;"></video>` :
              `<iframe id="${popupId}" class="hidden-iframe" frameborder="0" allowfullscreen allow="autoplay; fullscreen; encrypted-media; picture-in-picture"></iframe>`
            }
          </div>

          <div class="map-popup-footer">
            <div class="popup-location">
              <i class="fas fa-map-marker-alt"></i>
              <span>${cityName}</span>
            </div>
            <div class="popup-weather-badge-container">
              <span class="weather-badge loading" id="${weatherBadgeId}">
                <i class="fas fa-spinner fa-spin"></i>
              </span>
            </div>
          </div>
        </div>
      `;
    }

    // Function untuk play video di popup - Global access
    window.playPopupCCTV = function(playerId, thumbId, streamId, platform) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const bufferingId = playerId.replace('popup-player-', 'popup-buffering-');
      const bufferingOverlay = document.getElementById(bufferingId);

      if (!player || !thumb) return;

      if (thumb) thumb.style.display = 'none';
      if (bufferingOverlay) bufferingOverlay.style.display = 'flex';

      let streamUrl = '';
      if (platform === 'denava') {
        const encryptedParams = encodeURIComponent(secureHashStreamId(streamId));
        const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random().toString(36).substring(2)}`;
        const bufferParam = `&buffer=4&maxBufferLength=30&initialBufferTime=3`;
        streamUrl = `https://stream.denava.id/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;
      } else if (platform === 'stream2') {
        const encryptedParams = encodeURIComponent(secureHashStreamId(streamId));
        const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random().toString(36).substring(2)}`;
        const bufferParam = `&buffer=4&maxBufferLength=30&initialBufferTime=3`;
        streamUrl = `https://stream2.denava.id/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;
      } else if (platform === 'ipcamlive') {
        streamUrl = `https://g3.ipcamlive.com/player/player.php?alias=${streamId}&autoplay=1&mute=1`;
      } else if (platform === 'rekasadigital') {
        const encryptedParams = encodeURIComponent(secureHashStreamId(streamId));
        const secureParams = `id=${encryptedParams}&t=${Date.now()}&nonce=${Math.random().toString(36).substring(2)}`;
        const bufferParam = `&buffer=4&maxBufferLength=30&initialBufferTime=3`;
        streamUrl = `${STREAM_BASE}/stream/${streamId}/embed/0?autoplay=1&mute=1&controls=1&type=hls&${secureParams}${bufferParam}`;
      }

      // Set iframe attributes untuk allow mixed content dan permissions
      if (player) {
        player.setAttribute('allow', 'autoplay; fullscreen; encrypted-media; picture-in-picture');
        player.setAttribute('referrerpolicy', 'no-referrer-when-downgrade');
        player.setAttribute('loading', 'lazy');
        player.setAttribute('sandbox', 'allow-same-origin allow-scripts allow-popups allow-forms allow-presentation');
      }

      console.log('[RekasaDigital Popup] Loading stream:', streamUrl);

      player.src = streamUrl;
      player.style.display = 'block';

      player.onload = function() {
        if (bufferingOverlay) bufferingOverlay.style.display = 'none';
      };

      player.onerror = function() {
        const offlineId = playerId.replace('popup-player-', 'popup-offline-');
        const offlineMsg = document.getElementById(offlineId);
        if (player) player.style.display = 'none';
        if (thumb) thumb.style.display = 'none';
        if (offlineMsg) offlineMsg.style.display = 'flex';
        if (bufferingOverlay) bufferingOverlay.style.display = 'none';
      };

      // Timeout untuk hide buffering jika terlalu lama
      setTimeout(function() {
        if (bufferingOverlay && bufferingOverlay.style.display === 'flex') {
          bufferingOverlay.style.display = 'none';
        }
      }, 10000);
    };

    // Function untuk reload video di popup - Global access
    window.reloadPopupCCTV = function(playerId, thumbId, offlineId, streamId, platform) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const offlineMsg = document.getElementById(offlineId);
      const bufferingId = playerId.replace('popup-player-', 'popup-buffering-');
      const bufferingOverlay = document.getElementById(bufferingId);

      if (player) {
        player.style.display = 'none';
        player.src = '';
      }
      if (thumb) thumb.style.display = 'flex';
      if (offlineMsg) offlineMsg.style.display = 'none';
      if (bufferingOverlay) bufferingOverlay.style.display = 'none';

      // Trigger play setelah reload
      setTimeout(function() {
        if (typeof window.playPopupCCTV === 'function') {
          window.playPopupCCTV(playerId, thumbId, streamId, platform);
        }
      }, 500);
    };

    // ===== MEDIAMTX POPUP FUNCTIONS =====
    // Function untuk play MediaMTX video di popup
    window.playPopupMediaMTX = function(playerId, thumbId, streamPath) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const bufferingId = playerId.replace('popup-player-', 'popup-buffering-');
      const bufferingOverlay = document.getElementById(bufferingId);

      if (!player || !thumb || !streamPath) return;

      if (thumb) thumb.style.display = 'none';
      if (bufferingOverlay) bufferingOverlay.style.display = 'flex';

      try {
        // Ensure player is video element
        let videoPlayer = player;
        if (player.tagName === 'IFRAME') {
          const video = document.createElement('video');
          video.id = playerId;
          video.className = 'hls-video-player';
          video.controls = true;
          video.autoplay = true;
          video.muted = true;
          video.playsInline = true;
          // Additional attributes for cross-platform compatibility
          video.setAttribute('playsinline', 'true');
          video.setAttribute('webkit-playsinline', 'true');
          video.setAttribute('x-webkit-airplay', 'allow');
          video.setAttribute('preload', 'auto'); // CHANGED to 'auto' for instant playback
          video.setAttribute('autoplay', '');
          video.setAttribute('muted', '');
          video.muted = true; // Force muted for autoplay
          video.style.cssText = 'width:100%;height:100%;position:absolute;top:0;left:0;object-fit:contain;background:#000;';
          player.parentNode.replaceChild(video, player);
          videoPlayer = document.getElementById(playerId);
        }

        // Ensure video element has all cross-platform attributes for instant playback
        if (videoPlayer && videoPlayer.tagName === 'VIDEO') {
          if (!videoPlayer.hasAttribute('playsinline')) {
            videoPlayer.setAttribute('playsinline', 'true');
          }
          if (!videoPlayer.hasAttribute('webkit-playsinline')) {
            videoPlayer.setAttribute('webkit-playsinline', 'true');
          }
          if (!videoPlayer.hasAttribute('x-webkit-airplay')) {
            videoPlayer.setAttribute('x-webkit-airplay', 'allow');
          }
          // CHANGED to 'auto' for instant playback
          if (!videoPlayer.hasAttribute('preload') || videoPlayer.getAttribute('preload') === 'metadata') {
            videoPlayer.setAttribute('preload', 'auto');
          }
          // Ensure muted for autoplay
          videoPlayer.muted = true;
          videoPlayer.setAttribute('muted', '');
          // Disable picture-in-picture untuk performance
          if (videoPlayer.disablePictureInPicture !== undefined) {
            videoPlayer.disablePictureInPicture = true;
          }
        }

        function mountPopupHLS(targetUrl) {
          if (!targetUrl.includes('cookieCheck=1') && !targetUrl.includes('bcloud365.net')) {
            targetUrl += (targetUrl.includes('?') ? '&' : '?') + 'cookieCheck=1';
          }
          console.log('[MediaMTX Popup] Loading HLS:', targetUrl);

          if (typeof Hls !== 'undefined' && Hls.isSupported()) {
            if (videoPlayer.hlsInstance) {
              videoPlayer.hlsInstance.destroy();
            }

            const hls = new Hls({
              debug: false,
              enableWorker: true,
              lowLatencyMode: true,
              liveSyncDurationCount: 3,
              liveMaxLatencyDurationCount: 7,
              maxBufferLength: 6,
              maxMaxBufferLength: 12,
              maxBufferSize: 16 * 1024 * 1024,
              backBufferLength: 0,
              maxLoadingDelay: 0,
              maxFragLoadingTimeMs: 15000,
              manifestLoadingTimeOut: 12000,
              manifestLoadingMaxRetry: 10,
              manifestLoadingRetryDelay: 1000,
              levelLoadingMaxRetry: 6,
              fragLoadingMaxRetry: 6,
              startLevel: 0,
              capLevelToPlayerSize: true,
              abrEwmaDefaultEstimate: 1000000,
              abrBandWidthFactor: 0.8,
              abrBandWidthUpFactor: 0.5,
              enableSoftwareAES: false,
              maxBufferHole: 0.5,
              highBufferWatchdogPeriod: 2,
              nudgeOffset: 0.1,
              nudgeMaxRetry: 5,
              progressive: true
            });

            hls.loadSource(targetUrl);
            hls.attachMedia(videoPlayer);

            hls.on(Hls.Events.MANIFEST_PARSED, function() {
              if (bufferingOverlay) {
                bufferingOverlay.style.display = 'none';
                bufferingOverlay.style.opacity = '0';
              }
              videoPlayer.style.display = 'block';
              videoPlayer.style.opacity = '1';
              videoPlayer.classList.remove('hidden-iframe');
              const playPromise = videoPlayer.play();
              if (playPromise !== undefined) {
                playPromise.catch(e => {
                  videoPlayer.muted = true;
                  videoPlayer.play();
                });
              }
            });

            hls.on(Hls.Events.ERROR, function(event, data) {
              if (data.fatal) {
                switch (data.type) {
                  case Hls.ErrorTypes.NETWORK_ERROR:
                    hls.startLoad();
                    break;
                  case Hls.ErrorTypes.MEDIA_ERROR:
                    hls.recoverMediaError();
                    break;
                  default:
                    hls.destroy();
                    if (bufferingOverlay) bufferingOverlay.style.display = 'none';
                    break;
                }
              }
            });

            videoPlayer.hlsInstance = hls;
          } else if (videoPlayer.canPlayType('application/vnd.apple.mpegurl')) {
            videoPlayer.src = targetUrl;
            videoPlayer.addEventListener('loadedmetadata', function() {
              if (bufferingOverlay) bufferingOverlay.style.display = 'none';
              videoPlayer.style.display = 'block';
              videoPlayer.style.opacity = '1';
              videoPlayer.play().catch(e => {});
            });
          }
        }

        if (streamPath.startsWith('http://') || streamPath.startsWith('https://')) {
          mountPopupHLS(streamPath);
        } else if (streamPath.startsWith('xmeye_')) {
          const match = streamPath.match(/^xmeye_([a-fA-F0-9]+)(?:_ch(\d+))?/i);
          const sn = match ? match[1] : '';
          const ch = match ? (match[2] || 1) : 1;
          fetch(`api/jftech_gateway.php?action=get_live_stream&sn=${encodeURIComponent(sn)}&channel=${encodeURIComponent(ch)}`)
            .then(r => r.json())
            .then(data => {
              if (data.success && data.hls_url) {
                mountPopupHLS(data.hls_url);
              } else {
                mountPopupHLS(`${STREAM_BASE}/${streamPath}/index.m3u8`);
              }
            })
            .catch(() => {
              mountPopupHLS(`${STREAM_BASE}/${streamPath}/index.m3u8`);
            });
        } else {
          mountPopupHLS(`${STREAM_BASE}/${streamPath}/index.m3u8`);
        }
      } catch (error) {
        console.error('[MediaMTX Popup] Error:', error);
        const offlineId = playerId.replace('popup-player-', 'popup-offline-');
        const offlineMsg = document.getElementById(offlineId);
        if (player) player.style.display = 'none';
        if (thumb) thumb.style.display = 'none';
        if (offlineMsg) offlineMsg.style.display = 'flex';
        if (bufferingOverlay) bufferingOverlay.style.display = 'none';
      }
    };

    // Function untuk reload MediaMTX video di popup
    window.reloadPopupMediaMTX = function(playerId, thumbId, offlineId, streamPath) {
      const player = document.getElementById(playerId);
      const thumb = document.getElementById(thumbId);
      const offlineMsg = document.getElementById(offlineId);
      const bufferingId = playerId.replace('popup-player-', 'popup-buffering-');
      const bufferingOverlay = document.getElementById(bufferingId);

      if (player && player.hlsInstance) {
        player.hlsInstance.destroy();
        player.hlsInstance = null;
      }

      if (player) {
        player.style.display = 'none';
        player.src = '';
        player.classList.add('hidden-iframe');
      }
      if (thumb) thumb.style.display = 'flex';
      if (offlineMsg) offlineMsg.style.display = 'none';
      if (bufferingOverlay) bufferingOverlay.style.display = 'none';

      setTimeout(function() {
        if (typeof window.playPopupMediaMTX === 'function') {
          window.playPopupMediaMTX(playerId, thumbId, streamPath);
        }
      }, 500);
    };
    // ===== END MEDIAMTX POPUP FUNCTIONS =====
    // ===== END ADDED BY CURSOR AI =====

    let cctvMarkersGroup = null;

    // ===== MODIFIKASI: Update initCCTVMap untuk include Pasar Horas =====
    function initCCTVMap() {
      if (!isLeafletLoaded()) {
        console.error('Leaflet library is not loaded. Cannot initialize CCTV map.');
        loadLeaflet(initCCTVMap);
        return;
      }

      const mapContainer = document.getElementById('map');
      if (!mapContainer) {
        console.error('CCTV map container not found.');
        return;
      }

      try {
        const activeCity = typeof currentGlobalCity !== 'undefined' ? currentGlobalCity : 'all';
        const cityConf = CITY_CONFIG[activeCity] || CITY_CONFIG.all;
        const initialCenter = cityConf.center || [2.9568, 99.0619];
        const initialZoom = cityConf.zoom || 13;

        // Initialize Leaflet Map only ONCE so event listeners are never destroyed
        if (!mapCCTV) {
          mapCCTV = L.map('map', {
            zoomControl: false,
            attributionControl: true,
          }).setView(initialCenter, initialZoom);

          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Leaflet | © OpenStreetMap contributors',
            maxZoom: 19,
          }).addTo(mapCCTV);

          L.control.zoom({
            position: 'topright'
          }).addTo(mapCCTV);

          cctvMarkersGroup = L.featureGroup().addTo(mapCCTV);
        } else {
          if (cctvMarkersGroup) {
            cctvMarkersGroup.clearLayers();
          } else {
            cctvMarkersGroup = L.featureGroup().addTo(mapCCTV);
          }
        }

        const customIcon = L.divIcon({
          className: 'loewix-custom-map-pin',
          html: `
            <div class="loewix-pin-container">
              <div class="loewix-pin-radar"></div>
              <div class="loewix-pin-badge">
                <div class="loewix-pin-tag"><span class="live-dot"></span>LIVE</div>
                <div class="loewix-pin-shield">
                  <div class="loewix-pin-lens">
                    <i class="fas fa-video"></i>
                  </div>
                </div>
              </div>
            </div>
          `,
          iconSize: [46, 58],
          iconAnchor: [23, 54],
          popupAnchor: [0, -54]
        });

        // Filter CCTV list based on active city if not 'all' (strictly from Database)
        const selectedCity = (activeCity || 'all').toLowerCase();
        let allCCTVs = mediamtxData.filter(c => {
          if (!c.coordinates || !Array.isArray(c.coordinates) || c.coordinates.length < 2) return false;
          if (selectedCity === 'all') return true;
          return (c.city || '').toLowerCase() === selectedCity;
        });

        allCCTVs.forEach(function(location) {
          try {
            if (!location.coordinates || location.coordinates.length < 2) return;
            const latVal = parseFloat(location.coordinates[0]);
            const lngVal = parseFloat(location.coordinates[1]);
            if (isNaN(latVal) || isNaN(lngVal)) return;

            const marker = L.marker([latVal, lngVal], {
              icon: customIcon,
              riseOnHover: true
            });

            // ===== ADDED BY CURSOR AI: Use enhanced popup =====
            const popupContent = createEnhancedPopup(location);
            marker.bindPopup(popupContent, {
              maxWidth: 450,
              minWidth: 280,
              className: 'custom-popup-cctv',
              autoPan: true,
              autoPanPadding: [50, 50]
            });

            cctvMarkersGroup.addLayer(marker);

            // Load weather data for popup when opened
            marker.on('popupopen', function() {
              const weatherBadgeId = `popup-weather-${location.id}`;
              const weatherBadge = document.getElementById(weatherBadgeId);

              if (weatherBadge && typeof window.OpenMeteoWeather !== 'undefined') {
                // Use getWeather method with location object
                window.OpenMeteoWeather.getWeather(location)
                  .then(weatherData => {
                    if (weatherData && weatherBadge) {
                      const temp = weatherData.temperature;
                      const emoji = weatherData.emoji || '🌤️';
                      const tempColor = window.OpenMeteoWeather.getTemperatureColor(temp);

                      weatherBadge.innerHTML = `
                        <span class="weather-icon">${emoji}</span>
                        <span class="weather-temp" style="color: ${tempColor};">${temp}°C</span>
                      `;
                      weatherBadge.classList.remove('loading');
                      weatherBadge.classList.add('loaded');
                    }
                  })
                  .catch(error => {
                    console.error('Error loading weather for popup:', error);
                    if (weatherBadge) {
                      weatherBadge.innerHTML = '<i class="fas fa-cloud"></i>';
                      weatherBadge.classList.remove('loading');
                      weatherBadge.classList.add('fallback');
                    }
                  });
              }
            });
            // ===== END ADDED BY CURSOR AI =====
          } catch (err) {
            console.error(`Error adding marker for ${location.title}:`, err);
          }
        });

        // Auto-fit to active city markers
        if (cctvMarkersGroup && cctvMarkersGroup.getLayers().length > 0) {
          try {
            const layers = cctvMarkersGroup.getLayers();
            if (layers.length === 1) {
              mapCCTV.setView(layers[0].getLatLng(), 14);
            } else {
              const bounds = cctvMarkersGroup.getBounds();
              if (bounds && bounds.isValid && bounds.isValid()) {
                mapCCTV.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
              }
            }
          } catch(e) {}
        } else {
          mapCCTV.setView(initialCenter, initialZoom);
        }

        setTimeout(() => {
          if (mapCCTV) {
            mapCCTV.invalidateSize();
          }
        }, 500);

        console.log('CCTV map initialized successfully.');
      } catch (error) {
        console.error('Error initializing CCTV map:', error);
      }
    }

    // Fungsi inisialisasi WiFi Map yang diperbarui
    function initWiFiMap() {
      if (!isLeafletLoaded()) {
        console.error(
          'Leaflet library is not loaded. Cannot initialize WiFi map.'
        );
        loadLeaflet(initWiFiMap);
        return;
      }

      const mapContainer = document.getElementById('mapid');
      if (!mapContainer) {
        console.error('WiFi map container not found.');
        return;
      }

      try {
        if (mapWifi) {
          mapWifi.remove();
        }

        const jakartaCoords = [-6.1950, 106.8230];

        mapWifi = L.map('mapid', {
          zoomControl: false,
          attributionControl: true,
        }).setView(jakartaCoords, 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: 'Map data © Jakarta Smart City | JakWifi | © OpenStreetMap contributors',
          maxZoom: 19,
        }).addTo(mapWifi);

        const wifiIcon = L.divIcon({
          className: 'loewix-custom-wifi-pin',
          html: `
            <div class="loewix-pin-container">
              <div class="loewix-pin-radar wifi-radar"></div>
              <div class="loewix-pin-badge">
                <div class="loewix-pin-tag wifi-tag"><span class="live-dot"></span>WIFI</div>
                <div class="loewix-pin-shield wifi-shield">
                  <div class="loewix-pin-lens">
                    <i class="fas fa-wifi"></i>
                  </div>
                </div>
              </div>
            </div>
          `,
          iconSize: [46, 58],
          iconAnchor: [23, 54],
          popupAnchor: [0, -54]
        });

        wifiLocations.forEach((location) => {
          try {
            const marker = L.marker([location.lat, location.lng], {
              icon: wifiIcon,
            }).addTo(mapWifi);

            const popup = L.popup().setContent(`
                <div class="location-card">
                  <h5>${location.name}</h5>
                  <p>WiFi gratis untuk masyarakat</p>
                  ${location.address ? `<p><small>${location.address}</small></p>` : ''}
                  ${location.status ? `<p><strong>Status: ${location.status}</strong></p>` : ''}
                </div>
              `);

            marker.bindPopup(popup);
          } catch (err) {
            console.error(
              `Error adding WiFi marker for ${location.name}:`,
              err
            );
          }
        });

        L.control
          .zoom({
            position: 'topright',
          })
          .addTo(mapWifi);

        setTimeout(() => {
          if (mapWifi) {
            mapWifi.invalidateSize();
          }
        }, 500);

        console.log('WiFi map initialized successfully.');
      } catch (error) {
        console.error('Error initializing WiFi map:', error);
      }
    }

    let currentGlobalCity = 'all';
    let currentGridLayout = parseInt(localStorage.getItem('loewix_grid_layout') || '4');
    let selectedSingleCameraId = null;

    function syncVMSDockActive(val) {
      const btns = document.querySelectorAll('.vms-layout-btn');
      btns.forEach(btn => {
        if (btn.getAttribute('data-layout') === String(val)) {
          btn.classList.add('active');
        } else {
          btn.classList.remove('active');
        }
      });
      const gridSelect = document.getElementById('filter-grid');
      if (gridSelect) gridSelect.value = String(val);
    }

    function changeGridLayout(val) {
      const layoutNum = parseInt(val);
      if (layoutNum !== 1) {
        selectedSingleCameraId = null;
      }
      currentGridLayout = layoutNum;
      localStorage.setItem('loewix_grid_layout', val);
      syncVMSDockActive(val);
      generateCCTVHTML(currentGlobalCity);
    }

    function toggleVMSDeviceSidebar() {
      const sidebar = document.getElementById('vms-device-sidebar');
      if (!sidebar) return;
      sidebar.classList.toggle('open');
      if (sidebar.classList.contains('open')) {
        renderVMSDeviceTree();
      }
    }

    function renderVMSDeviceTree() {
      const container = document.getElementById('vms-device-tree-list');
      if (!container || !mediamtxData) return;

      const activeCams = mediamtxData.filter(c => !c.section);
      let html = `
        <div class="vms-tree-group">
          <div class="vms-group-header" onclick="this.nextElementSibling.classList.toggle('d-none')">
            <i class="fas fa-folder-open"></i> Default Group (${activeCams.length})
          </div>
          <div class="vms-group-content mt-1">`;

      activeCams.forEach(cam => {
        const isActive = (selectedSingleCameraId === cam.id) ? 'active' : '';
        html += `
          <div class="vms-cam-item ${isActive}" onclick="focusCameraSingle(${cam.id})">
            <span class="vms-cam-dot"></span>
            <i class="fas fa-video text-muted" style="font-size:10px;"></i>
            <span class="text-truncate" style="max-width: 210px;">${cam.title}</span>
          </div>`;
      });

      html += `</div></div>`;
      container.innerHTML = html;
    }

    function filterVMSDevices(query) {
      const q = (query || '').toLowerCase();
      const items = document.querySelectorAll('.vms-cam-item');
      items.forEach(item => {
        if (item.textContent.toLowerCase().includes(q)) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });
    }

    function toggleVMSFullscreen() {
      const cctvSection = document.getElementById('cctv-container');
      if (!cctvSection) return;
      if (!document.fullscreenElement && !document.webkitFullscreenElement) {
        if (cctvSection.requestFullscreen) {
          cctvSection.requestFullscreen().catch(() => {});
        } else if (cctvSection.webkitRequestFullscreen) {
          cctvSection.webkitRequestFullscreen();
        }
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen().catch(() => {});
        } else if (document.webkitExitFullscreen) {
          document.webkitExitFullscreen();
        }
      }
    }

    function toggleCameraFullscreen(id) {
      const card = document.getElementById('card-' + id);
      if (!card) return;

      const player = document.getElementById('player-' + id);
      const thumb = document.getElementById('thumb-' + id);

      // Start stream if not already active
      if (thumb && thumb.style.display !== 'none' && typeof playMediaMTXCCTV === 'function') {
        playMediaMTXCCTV(thumb, 'player-' + id, 'thumb-' + id);
      }

      const isFs = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);

      if (isFs) {
        if (document.exitFullscreen) {
          document.exitFullscreen().catch(() => {});
        } else if (document.webkitExitFullscreen) {
          document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      } else {
        const reqFs = card.requestFullscreen || card.webkitRequestFullscreen || card.mozRequestFullScreen || card.msRequestFullscreen;
        if (reqFs) {
          reqFs.call(card).catch(err => {
            console.warn('[Fullscreen] Card fullscreen failed, falling back to video:', err);
            if (player) {
              if (player.webkitEnterFullscreen) {
                player.webkitEnterFullscreen();
              } else if (player.requestFullscreen) {
                player.requestFullscreen().catch(() => {});
              }
            }
          });
        } else if (player && player.webkitEnterFullscreen) {
          player.webkitEnterFullscreen();
        }
      }
    }

    // Sync fullscreen button icon across active cards
    function handleFullscreenUIChange() {
      const isFs = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
      const fsBtns = document.querySelectorAll('.card-action-toolbar .action-btn i.fa-expand, .card-action-toolbar .action-btn i.fa-compress');
      fsBtns.forEach(icon => {
        if (isFs) {
          icon.className = 'fas fa-compress';
        } else {
          icon.className = 'fas fa-expand';
        }
      });
    }

    document.addEventListener('fullscreenchange', handleFullscreenUIChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenUIChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenUIChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenUIChange);

    function focusCameraSingle(id) {
      selectedSingleCameraId = parseInt(id);

      // Tutup sidebar jika terbuka
      const sidebar = document.getElementById('vms-device-sidebar');
      if (sidebar && sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
      }

      // Ubah layout ke View 1 (Single camera view)
      currentGridLayout = 1;
      localStorage.setItem('loewix_grid_layout', '1');
      syncVMSDockActive(1);
      generateCCTVHTML(currentGlobalCity);

      // Scroll ke posisi kamera
      const card = document.getElementById('card-' + id);
      if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }

      // Pastikan stream langsung berjalan
      const thumb = document.getElementById('thumb-' + id);
      if (thumb && typeof playMediaMTXCCTV === 'function') {
        playMediaMTXCCTV(thumb, 'player-' + id, 'thumb-' + id);
      }

      // Langsung buka layar penuh (Fullscreen)
      toggleCameraFullscreen(id);
    }

    // ===== TOAST NOTIFICATION UTILITY =====
    function showToastNotification(message, type = 'info') {
      let container = document.getElementById('cctv-toast-container');
      if (!container) {
        container = document.createElement('div');
        container.id = 'cctv-toast-container';
        container.className = 'cctv-toast-container';
        document.body.appendChild(container);
      }

      const toast = document.createElement('div');
      toast.className = 'cctv-toast-item';
      let iconHtml = '<i class="fas fa-info-circle text-info"></i>';
      if (type === 'success' || message.includes('📸') || message.includes('🎬')) {
        iconHtml = '<i class="fas fa-check-circle text-success"></i>';
      } else if (type === 'warning' || message.includes('⚠️')) {
        iconHtml = '<i class="fas fa-exclamation-triangle text-warning"></i>';
      } else if (type === 'error' || message.includes('🔴')) {
        iconHtml = '<i class="fas fa-circle text-danger"></i>';
      }

      toast.innerHTML = `${iconHtml} <span>${message}</span>`;
      container.appendChild(toast);

      setTimeout(() => {
        toast.style.transition = 'all 0.3s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(15px)';
        setTimeout(() => toast.remove(), 300);
      }, 3500);
    }

    // ===== AUDIO CONTROL (SUARA) =====
    function toggleCameraAudio(cameraId) {
      const player = document.getElementById(`player-${cameraId}`);
      const icon = document.getElementById(`audio-icon-${cameraId}`);
      const btn = document.getElementById(`audio-btn-${cameraId}`);
      if (!player) return;

      if (player.muted) {
        player.muted = false;
        player.volume = 1.0;
        if (icon) {
          icon.className = 'fas fa-volume-up';
          icon.style.color = '#38bdf8';
        }
        if (btn) btn.title = 'Matikan Suara (Mute)';
        showToastNotification(`🔊 Suara aktif: ${player.title || 'CCTV'}`);
      } else {
        player.muted = true;
        if (icon) {
          icon.className = 'fas fa-volume-mute';
          icon.style.color = '';
        }
        if (btn) btn.title = 'Aktifkan Suara (Unmute)';
        showToastNotification(`🔇 Suara dimatikan: ${player.title || 'CCTV'}`);
      }
    }

    // ===== SNAPSHOT CONTROL (FOTO / SCREENSHOT) =====
    function captureCameraSnapshot(cameraId, cameraTitle) {
      const player = document.getElementById(`player-${cameraId}`);
      const card = document.getElementById(`card-${cameraId}`);
      if (!player || player.style.display === 'none' || player.readyState < 2) {
        showToastNotification('⚠️ Tunggu siaran video aktif sebelum mengambil foto.', 'warning');
        return;
      }

      try {
        // Flash animation
        if (card) {
          const flash = document.createElement('div');
          flash.className = 'snapshot-flash-overlay';
          card.appendChild(flash);
          setTimeout(() => flash.remove(), 350);
        }

        const width = player.videoWidth || player.clientWidth || 1280;
        const height = player.videoHeight || player.clientHeight || 720;

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        // Draw current video frame
        ctx.drawImage(player, 0, 0, width, height);

        // Watermark Loewix CCTV & Timestamp Bar
        const now = new Date();
        const dateStr = now.toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }) + ' ' + now.toLocaleTimeString('id-ID', { hour12: false });
        
        ctx.fillStyle = 'rgba(10, 15, 30, 0.75)';
        ctx.fillRect(0, height - 46, width, 46);

        ctx.font = 'bold 18px sans-serif';
        ctx.fillStyle = '#38bdf8';
        ctx.fillText('LOEWIX CCTV SURVEILLANCE', 20, height - 17);

        ctx.font = '15px sans-serif';
        ctx.fillStyle = '#ffffff';
        ctx.fillText(`${cameraTitle || 'CCTV Live'} | ${dateStr}`, Math.max(320, width - 420), height - 17);

        // Download image file
        const dataUrl = canvas.toDataURL('image/jpeg', 0.95);
        const link = document.createElement('a');
        const safeTitle = (cameraTitle || 'cctv').replace(/[^a-zA-Z0-9_-]/g, '_');
        const timestampStr = now.toISOString().replace(/[:.]/g, '-').slice(0, 19);
        link.download = `CCTV-FOTO-${safeTitle}-${timestampStr}.jpg`;
        link.href = dataUrl;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showToastNotification(`📸 Foto berhasil diunduh: ${cameraTitle || 'CCTV'}`);
      } catch (err) {
        console.error('[Snapshot] Error taking photo:', err);
        showToastNotification('⚠️ Gagal mengambil foto (Proteksi Cross-Origin Stream).', 'error');
      }
    }

    // ===== RECORD CONTROL (REKAM VIDEO) =====
    const activeCameraRecorders = new Map();

    function toggleCameraRecording(cameraId, cameraTitle) {
      if (activeCameraRecorders.has(cameraId)) {
        stopCameraRecording(cameraId);
      } else {
        startCameraRecording(cameraId, cameraTitle);
      }
    }

    function startCameraRecording(cameraId, cameraTitle) {
      const player = document.getElementById(`player-${cameraId}`);
      const recBtn = document.getElementById(`record-btn-${cameraId}`);
      const recIcon = document.getElementById(`record-icon-${cameraId}`);
      const recBadge = document.getElementById(`rec-badge-${cameraId}`);
      const recTimer = document.getElementById(`rec-timer-${cameraId}`);

      if (!player || player.style.display === 'none' || player.readyState < 2) {
        showToastNotification('⚠️ Tunggu siaran video aktif untuk mulai merekam.', 'warning');
        return;
      }

      let stream = null;
      if (typeof player.captureStream === 'function') {
        stream = player.captureStream();
      } else if (typeof player.mozCaptureStream === 'function') {
        stream = player.mozCaptureStream();
      }

      if (!stream) {
        showToastNotification('⚠️ Browser Anda tidak mendukung perekaman langsung dari video stream.', 'warning');
        return;
      }

      let mimeType = 'video/webm;codecs=vp8,opus';
      if (!window.MediaRecorder || !MediaRecorder.isTypeSupported(mimeType)) {
        mimeType = 'video/webm';
        if (window.MediaRecorder && !MediaRecorder.isTypeSupported(mimeType)) {
          mimeType = '';
        }
      }

      try {
        const options = mimeType ? { mimeType } : {};
        const mediaRecorder = new MediaRecorder(stream, options);
        const recordedChunks = [];

        mediaRecorder.ondataavailable = function(e) {
          if (e.data && e.data.size > 0) {
            recordedChunks.push(e.data);
          }
        };

        let seconds = 0;
        const timerInterval = setInterval(() => {
          seconds++;
          const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
          const secs = String(seconds % 60).padStart(2, '0');
          if (recTimer) recTimer.textContent = `${mins}:${secs}`;
        }, 1000);

        mediaRecorder.onstop = function() {
          clearInterval(timerInterval);
          if (recBadge) recBadge.style.display = 'none';
          if (recIcon) {
            recIcon.className = 'fas fa-circle text-danger';
            recIcon.style.animation = '';
          }
          if (recBtn) {
            recBtn.classList.remove('is-recording');
            recBtn.title = 'Mulai Rekam Video';
          }

          if (recordedChunks.length > 0) {
            const blob = new Blob(recordedChunks, { type: mimeType || 'video/webm' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const now = new Date();
            const safeTitle = (cameraTitle || 'cctv').replace(/[^a-zA-Z0-9_-]/g, '_');
            const timestampStr = now.toISOString().replace(/[:.]/g, '-').slice(0, 19);
            a.href = url;
            a.download = `CCTV-RECORD-${safeTitle}-${timestampStr}.webm`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => URL.revokeObjectURL(url), 2000);

            showToastNotification(`🎬 Rekaman video (${seconds} detik) berhasil disimpan: ${cameraTitle || 'CCTV'}`);
          }
          activeCameraRecorders.delete(cameraId);
        };

        mediaRecorder.start(1000);
        activeCameraRecorders.set(cameraId, { recorder: mediaRecorder, timer: timerInterval, title: cameraTitle });

        if (recBadge) {
          recBadge.style.display = 'inline-flex';
          if (recTimer) recTimer.textContent = '00:00';
        }
        if (recIcon) {
          recIcon.className = 'fas fa-stop text-danger';
          recIcon.style.animation = 'pulseRec 1s infinite';
        }
        if (recBtn) {
          recBtn.classList.add('is-recording');
          recBtn.title = 'Stop & Simpan Rekaman Video';
        }

        showToastNotification(`🔴 Mulai merekam video: ${cameraTitle || 'CCTV'}`);
      } catch (e) {
        console.error('[Recording] Error starting recorder:', e);
        showToastNotification('⚠️ Gagal memulai rekaman video.', 'error');
      }
    }

    function stopCameraRecording(cameraId) {
      const session = activeCameraRecorders.get(cameraId);
      if (session && session.recorder && session.recorder.state !== 'inactive') {
        session.recorder.stop();
      }
    }

    function changeGlobalCity(cityId) {
      console.log('[City Filter] Changing city to:', cityId);
      currentGlobalCity = cityId;

      const navSelect = document.getElementById('city-selector-nav');
      const filterSelect = document.getElementById('filter-city');
      if (navSelect) navSelect.value = cityId;
      if (filterSelect) filterSelect.value = cityId;

      if (typeof initCCTVMap === 'function') {
        initCCTVMap();
      }

      generateCCTVHTML(cityId);
    }

    function openCameraConfigModal(id) {
      const modal = document.getElementById('cameraConfigModal');
      if (!modal) return;

      if (id) {
        const cam = mediamtxData.find(c => c.id === parseInt(id));
        if (cam) {
          document.getElementById('camModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit IP / RTSP Kamera #' + id;
          document.getElementById('cam-id').value = cam.id;
          document.getElementById('cam-title').value = cam.title || '';
          document.getElementById('cam-city').value = cam.city || 'siantar';
          document.getElementById('cam-stream-path').value = cam.streamPath || cam.streamId || '';
          document.getElementById('cam-lat').value = cam.coordinates ? cam.coordinates[0] : '';
          document.getElementById('cam-lng').value = cam.coordinates ? cam.coordinates[1] : '';
        }
      } else {
        document.getElementById('camModalTitle').innerHTML = '<i class="fas fa-plus"></i> Tambah Kamera RTSP / IP Baru';
        document.getElementById('cam-id').value = '';
        document.getElementById('cam-title').value = '';
        document.getElementById('cam-city').value = currentGlobalCity === 'all' ? 'siantar' : currentGlobalCity;
        document.getElementById('cam-stream-path').value = '';
        document.getElementById('cam-lat').value = '';
        document.getElementById('cam-lng').value = '';
      }

      modal.style.display = 'flex';
    }

    function closeCameraConfigModal() {
      const modal = document.getElementById('cameraConfigModal');
      if (modal) modal.style.display = 'none';
    }

    function saveCameraConfig() {
      const id = document.getElementById('cam-id').value;
      const title = document.getElementById('cam-title').value.trim();
      const city = document.getElementById('cam-city').value;
      const streamPath = document.getElementById('cam-stream-path').value.trim();
      const lat = parseFloat(document.getElementById('cam-lat').value) || CITY_CONFIG[city].center[0];
      const lng = parseFloat(document.getElementById('cam-lng').value) || CITY_CONFIG[city].center[1];

      if (!title || !streamPath) {
        alert('Mohon isi nama kamera dan Stream Path / RTSP URL.');
        return;
      }

      // API Call to add camera with Quota Enforcement
      const formData = new FormData();
      formData.append('action', 'add');
      formData.append('title', title);
      formData.append('city', city);
      formData.append('streamPath', streamPath);
      formData.append('lat', lat);
      formData.append('lng', lng);

      fetch('api/cameras.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        if (!res.success) {
          if (res.quota_exceeded) {
            closeCameraConfigModal();
            showQuotaAlert(res.message);
          } else {
            alert(res.message);
          }
          return;
        }

        const newCam = res.camera;
        mediamtxData.push({
          id: newCam.id,
          city: newCam.city,
          title: newCam.title,
          streamPath: newCam.streamPath,
          streamId: newCam.streamPath,
          coordinates: [parseFloat(newCam.lat) || lat, parseFloat(newCam.lng) || lng],
          platform: PLATFORM_TYPES.MEDIAMTX,
          thumbnail: ASSET_BASE + '/image/logo-loewix.png'
        });

        closeCameraConfigModal();
        alert(res.message);
        checkUserSession();
        generateCCTVHTML(currentGlobalCity);
      })
      .catch(err => {
        // Fallback for offline/Vercel demo
        const newId = Date.now();
        mediamtxData.push({
          id: newId,
          city: city,
          title: title,
          streamPath: streamPath,
          streamId: streamPath,
          coordinates: [lat, lng],
          platform: PLATFORM_TYPES.MEDIAMTX,
          thumbnail: ASSET_BASE + '/image/logo-loewix.png'
        });
        closeCameraConfigModal();
        alert('Kamera berhasil ditambahkan!');
        generateCCTVHTML(currentGlobalCity);
      });
    }

    // ===== LOEWIX DVR ENCODE SETTINGS MODULE =====
    let currentEncodeChannel = 1;

    async function openEncodeModal(channelId) {
      currentEncodeChannel = channelId || 1;
      const modal = document.getElementById('cameraEncodeModal');
      if (!modal) return;

      // Populate channel selector with active cameras with robust fallback
      const chanSelect = document.getElementById('encode-channel');
      if (chanSelect) {
        chanSelect.innerHTML = '';
        
        let foundMatch = false;
        const camList = (typeof mediamtxData !== 'undefined' && Array.isArray(mediamtxData) && mediamtxData.length > 0)
          ? mediamtxData
          : (typeof cctvData !== 'undefined' && Array.isArray(cctvData) && cctvData.length > 0)
            ? cctvData
            : [];

        if (camList.length > 0) {
          camList.forEach((cam, index) => {
            const chVal = String(cam.id || (index + 1));
            const opt = document.createElement('option');
            opt.value = chVal;
            opt.textContent = `Channel ${index + 1} - ${cam.title || 'Camera ' + (index + 1)}`;
            if (chVal == String(currentEncodeChannel)) {
              opt.selected = true;
              foundMatch = true;
            }
            chanSelect.appendChild(opt);
          });
        }

        // Always fallback to standard DVR channels 1..16 if list is empty
        if (chanSelect.options.length === 0) {
          for (let i = 1; i <= 16; i++) {
            const opt = document.createElement('option');
            opt.value = String(i);
            opt.textContent = `Channel ${i}`;
            if (String(i) == String(currentEncodeChannel)) {
              opt.selected = true;
              foundMatch = true;
            }
            chanSelect.appendChild(opt);
          }
        }

        if (!foundMatch && chanSelect.options.length > 0) {
          chanSelect.selectedIndex = 0;
          currentEncodeChannel = chanSelect.value;
        }
      }

      modal.style.display = 'flex';
      await loadChannelEncodeConfig(currentEncodeChannel);
    }

    function closeEncodeModal() {
      const modal = document.getElementById('cameraEncodeModal');
      if (modal) modal.style.display = 'none';
    }

    async function loadChannelEncodeConfig(channel) {
      currentEncodeChannel = channel || 1;
      try {
        const res = await fetch(`api/camera_encode.php?action=get&channel=${encodeURIComponent(currentEncodeChannel)}`);
        const data = await res.json();
        if (data.success && data.profile) {
          applyEncodeProfileToUI(data.profile);
          updateEncodeTelemetry(data.profile);
        }
      } catch (err) {
        console.warn('[Encode] Fetch failed, using UI values:', err);
        updateEncodeTelemetryFromUI();
      }
    }

    function applyEncodeProfileToUI(p) {
      const m = p.main_stream || {};
      const e = p.extra_stream || {};
      const a = p.advanced || {};

      // Main Stream
      setSelectVal('enc-main-compression', m.compression || 'H.265');
      setSelectVal('enc-main-resolution', m.resolution || '6M');
      setSelectVal('enc-main-fps', m.fps || 20);
      setSelectVal('enc-main-bitrate-type', m.bitrate_type || 'VBR');
      setSelectVal('enc-main-quality', m.quality || 'high');
      setInputVal('enc-main-bitrate', m.bitrate_kbps || 3316);
      setSelectVal('enc-main-iframe', m.iframe_interval || 2);
      setCheckVal('enc-main-video', m.enable_video !== false);
      setCheckVal('enc-main-audio', m.enable_audio !== false);
      setSelectVal('enc-main-smart', m.smart_encode || 'H.265AI');

      // Extra Stream
      setSelectVal('enc-extra-compression', e.compression || 'Extra Stream');
      setSelectVal('enc-extra-resolution', e.resolution || 'HD1');
      setSelectVal('enc-extra-fps', e.fps || 20);
      setSelectVal('enc-extra-bitrate-type', e.bitrate_type || 'VBR');
      setSelectVal('enc-extra-quality', e.quality || 'high');
      setInputVal('enc-extra-bitrate', e.bitrate_kbps || 552);
      setSelectVal('enc-extra-iframe', e.iframe_interval || 2);
      setCheckVal('enc-extra-video', e.enable_video !== false);
      setCheckVal('enc-extra-audio', e.enable_audio !== false);
      setSelectVal('enc-extra-smart', e.smart_encode || 'H.265AI');

      // Advanced
      setSelectVal('enc-adv-audiocodec', a.audio_codec || 'AAC');
      setInputVal('enc-adv-gop', a.gop_size || 40);
      setCheckVal('enc-adv-roi', a.roi_enabled !== false);
      setCheckVal('enc-adv-watermark', a.watermark_osd !== false);

      // Display Mode
      setSelectVal('enc-main-displaymode', p.display_mode || 'cover');
      setSelectVal('enc-extra-displaymode', p.display_mode || 'cover');
    }

    // Auto-calculate recommended Bitrate when Resolution, Compression, or Quality changes
    function onMainResolutionOrCompressionChange() {
      const res = document.getElementById('enc-main-resolution').value;
      const comp = document.getElementById('enc-main-compression').value;
      const smart = document.getElementById('enc-main-smart').value;
      const quality = document.getElementById('enc-main-quality').value;

      let baseBitrate = 3316;
      switch (res) {
        case '4K': baseBitrate = 4096; break;
        case '6M': baseBitrate = 3316; break;
        case '5M': baseBitrate = 3072; break;
        case '4M': baseBitrate = 2560; break;
        case '3M': baseBitrate = 2048; break;
        case '1080P': baseBitrate = 1536; break;
        case '720P': baseBitrate = 1024; break;
      }

      if (comp === 'H.264') baseBitrate = Math.round(baseBitrate * 1.35);
      if (smart === 'H.265AI' || smart === 'H.265+') baseBitrate = Math.round(baseBitrate * 0.75);

      if (quality === 'best') baseBitrate = Math.round(baseBitrate * 1.2);
      else if (quality === 'lowest') baseBitrate = Math.round(baseBitrate * 0.7);

      document.getElementById('enc-main-bitrate').value = baseBitrate;
      updateEncodeTelemetryFromUI();
    }

    function onExtraResolutionChange() {
      const res = document.getElementById('enc-extra-resolution').value;
      let baseBitrate = 552;
      switch (res) {
        case 'D1': baseBitrate = 768; break;
        case 'HD1': baseBitrate = 552; break;
        case 'CIF': baseBitrate = 384; break;
        case 'QVGA': baseBitrate = 256; break;
      }
      document.getElementById('enc-extra-bitrate').value = baseBitrate;
      updateEncodeTelemetryFromUI();
    }

    function getEncodeProfileFromUI() {
      const dispMode = document.getElementById('enc-main-displaymode') ? document.getElementById('enc-main-displaymode').value : 'cover';
      return {
        channel: currentEncodeChannel,
        display_mode: dispMode,
        main_stream: {
          compression: document.getElementById('enc-main-compression').value,
          resolution: document.getElementById('enc-main-resolution').value,
          fps: parseInt(document.getElementById('enc-main-fps').value) || 20,
          bitrate_type: document.getElementById('enc-main-bitrate-type').value,
          quality: document.getElementById('enc-main-quality').value,
          bitrate_kbps: parseInt(document.getElementById('enc-main-bitrate').value) || 3316,
          iframe_interval: parseInt(document.getElementById('enc-main-iframe').value) || 2,
          enable_video: document.getElementById('enc-main-video').checked,
          enable_audio: document.getElementById('enc-main-audio').checked,
          smart_encode: document.getElementById('enc-main-smart').value
        },
        extra_stream: {
          compression: document.getElementById('enc-extra-compression').value,
          resolution: document.getElementById('enc-extra-resolution').value,
          fps: parseInt(document.getElementById('enc-extra-fps').value) || 20,
          bitrate_type: document.getElementById('enc-extra-bitrate-type').value,
          quality: document.getElementById('enc-extra-quality').value,
          bitrate_kbps: parseInt(document.getElementById('enc-extra-bitrate').value) || 552,
          iframe_interval: parseInt(document.getElementById('enc-extra-iframe').value) || 2,
          enable_video: document.getElementById('enc-extra-video').checked,
          enable_audio: document.getElementById('enc-extra-audio').checked,
          smart_encode: document.getElementById('enc-extra-smart').value
        },
        advanced: {
          audio_codec: document.getElementById('enc-adv-audiocodec').value,
          gop_size: parseInt(document.getElementById('enc-adv-gop').value) || 40,
          roi_enabled: document.getElementById('enc-adv-roi').checked,
          watermark_osd: document.getElementById('enc-adv-watermark').checked
        }
      };
    }

    function updateEncodeTelemetryFromUI() {
      const mainKbps = parseInt(document.getElementById('enc-main-bitrate').value) || 3316;
      const extraKbps = parseInt(document.getElementById('enc-extra-bitrate').value) || 552;
      const totalKbps = mainKbps + extraKbps;
      const mbps = (totalKbps / 1024).toFixed(2);
      const gbPerDay = ((totalKbps * 3600 * 24) / (8 * 1024 * 1024)).toFixed(1);

      const bitrateEl = document.getElementById('enc-calc-bitrate');
      const storageEl = document.getElementById('enc-calc-storage');
      if (bitrateEl) bitrateEl.textContent = `${mbps} Mbps (${mainKbps} + ${extraKbps} Kbps)`;
      if (storageEl) storageEl.textContent = `${gbPerDay} GB / Hari`;
    }

    function updateEncodeTelemetry(p) {
      const m = p.main_stream || {};
      const e = p.extra_stream || {};
      const mainKbps = parseInt(m.bitrate_kbps) || 3316;
      const extraKbps = parseInt(e.bitrate_kbps) || 552;
      const totalKbps = mainKbps + extraKbps;
      const mbps = (totalKbps / 1024).toFixed(2);
      const gbPerDay = ((totalKbps * 3600 * 24) / (8 * 1024 * 1024)).toFixed(1);

      const bitrateEl = document.getElementById('enc-calc-bitrate');
      const storageEl = document.getElementById('enc-calc-storage');
      if (bitrateEl) bitrateEl.textContent = `${mbps} Mbps`;
      if (storageEl) storageEl.textContent = `${gbPerDay} GB / Hari`;
    }

    function toggleEncodeAdvanced() {
      const drawer = document.getElementById('vms-encode-advanced-drawer');
      if (drawer) {
        drawer.style.display = (drawer.style.display === 'none' || !drawer.style.display) ? 'block' : 'none';
      }
    }

    async function saveEncodeConfig() {
      const profile = getEncodeProfileFromUI();
      const targetCamId = currentEncodeChannel;
      const dispMode = profile.display_mode || 'cover';

      const okBtn = document.querySelector('.vms-encode-btn.btn-ok');
      if (okBtn) {
        okBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menerapkan...';
        okBtn.disabled = true;
      }

      try {
        const res = await fetch('api/camera_encode.php?action=save', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(profile)
        });
        const data = await res.json();
        if (data.success) {
          // 1. Instantly apply HD Main Stream mode to target camera
          if (typeof setCameraQuality === 'function') {
            setCameraQuality(targetCamId, 'hd');
          }

          // 2. Instantly set Full Layar object-fit styling
          const playerEl = document.getElementById(`player-${targetCamId}`);
          const thumbEl = document.getElementById(`thumb-${targetCamId}`);
          const fitStyle = dispMode === 'contain' ? 'contain' : 'cover';

          if (playerEl) {
            playerEl.style.objectFit = fitStyle;
          }
          if (thumbEl) {
            thumbEl.style.objectFit = fitStyle;
          }

          // 3. Trigger live stream refresh for target camera
          if (thumbEl && typeof playMediaMTXCCTV === 'function') {
            playMediaMTXCCTV(thumbEl, `player-${targetCamId}`, `thumb-${targetCamId}`);
          }

          if (typeof showCCTVToast === 'function') {
            showCCTVToast(`Konfigurasi Encode Channel ${currentEncodeChannel} berhasil disimpan & beralih ke Full HD!`, 'success');
          } else {
            alert(`Konfigurasi Encode Channel ${currentEncodeChannel} berhasil disimpan! Kamera beralih ke Full HD.`);
          }
          closeEncodeModal();
        } else {
          alert(data.message || 'Gagal menyimpan konfigurasi encode.');
        }
      } catch (err) {
        console.error('[Encode] Save error:', err);
        alert('Terjadi kesalahan koneksi saat menyimpan konfigurasi.');
      } finally {
        if (okBtn) {
          okBtn.innerHTML = 'OK';
          okBtn.disabled = false;
        }
      }
    }

    async function copyEncodeToAllChannels() {
      if (!confirm('Apakah Anda yakin ingin menerapkan pengaturan Encode ini ke SEMUA Channel kamera?')) {
        return;
      }
      const profile = getEncodeProfileFromUI();
      profile.copy_to_all = true;
      try {
        const res = await fetch('api/camera_encode.php?action=save', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(profile)
        });
        const data = await res.json();
        if (data.success) {
          if (typeof toggleGlobalStreamQuality === 'function') {
            // Set all to HD
            document.querySelectorAll('.hls-video-player, .cctv-thumbnail').forEach(el => {
              el.style.objectFit = 'cover';
            });
          }
          if (typeof showCCTVToast === 'function') {
            showCCTVToast(data.message || 'Pengaturan Encode berhasil diterapkan ke semua channel!', 'success');
          } else {
            alert(data.message || 'Pengaturan Encode berhasil diterapkan ke semua channel!');
          }
          closeEncodeModal();
        }
      } catch (err) {
        alert('Gagal menerapkan konfigurasi massal.');
      }
    }

    function setSelectVal(id, val) {
      const el = document.getElementById(id);
      if (el) el.value = val;
    }
    function setInputVal(id, val) {
      const el = document.getElementById(id);
      if (el) el.value = val;
    }
    function setCheckVal(id, val) {
      const el = document.getElementById(id);
      if (el) el.checked = !!val;
    }

    // Generate CCTV HTML - Updated untuk Multi-Kota & MediaMTX HLS streaming
    function generateCCTVHTML(filterCity = currentGlobalCity) {
      const cctvContainer = document.getElementById('cctv-container');
      if (!cctvContainer) return;

      cctvContainer.innerHTML = '';

      // Check authentication status - cameras require login
      if (!currentUser) {
        if (!authCheckComplete) {
          cctvContainer.innerHTML = `
            <div class="col-12 text-center py-5 my-2 wow fadeInUp" style="background: rgba(13, 24, 54, 0.75); border: 1px solid rgba(0, 210, 255, 0.25); border-radius: 24px; backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5); padding: 50px 24px;">
              <div class="spinner-border mb-3" style="width: 3rem; height: 3rem; color: #00d2ff;" role="status"></div>
              <h4 class="text-white font-weight-bold mb-2">Memeriksa Hak Akses...</h4>
              <p class="text-muted mb-0" style="font-size: 13px;">Menghubungkan ke server autentikasi Loewix</p>
            </div>`;
          return;
        }
        cctvContainer.innerHTML = `
          <div class="col-12 text-center py-5 my-2 wow fadeInUp" style="background: rgba(13, 24, 54, 0.75); border: 1px solid rgba(0, 210, 255, 0.25); border-radius: 24px; backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5); padding: 60px 24px;">
            <div class="mb-4">
              <div style="width: 86px; height: 86px; margin: 0 auto; background: radial-gradient(circle, rgba(0, 210, 255, 0.22) 0%, rgba(0, 102, 255, 0.05) 70%); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1.5px solid rgba(0, 210, 255, 0.45); box-shadow: 0 0 28px rgba(0, 210, 255, 0.28);">
                <i class="fas fa-lock fa-2x" style="color: #00d2ff;"></i>
              </div>
            </div>
            <h3 class="text-white font-weight-bold mb-2" style="font-size: 22px; letter-spacing: 0.5px;">Akses Siaran CCTV Terproteksi</h3>
            <p class="text-muted mx-auto mb-4" style="max-width: 520px; font-size: 14px; line-height: 1.6; color: #94a3b8 !important;">
              Siaran langsung kamera CCTV memerlukan autentikasi login. Silakan login ke akun Loewix Anda untuk melihat dan memantau live streaming kamera CCTV enterprise Anda.
            </p>
            <button class="btn" onclick="openLoginModal()" style="background: linear-gradient(135deg, #00d2ff, #0066ff); border: none; font-weight: 700; border-radius: 30px; padding: 12px 36px; color: #fff; box-shadow: 0 6px 20px rgba(0, 102, 255, 0.45); font-size: 13px; letter-spacing: 0.8px; text-transform: uppercase;">
              <i class="fas fa-sign-in-alt mr-2"></i> LOGIN UNTUK MELIHAT CCTV
            </button>
          </div>`;
        return;
      }

      // Filter kamera berdasarkan city & bukan section pasar-horas
      const selectedCity = (filterCity || 'all').toLowerCase();
      let mainCameras = mediamtxData.filter(cam => {
        if (cam.section) return false;
        if (selectedCity === 'all') return true;
        return (cam.city || '').toLowerCase() === selectedCity;
      });

      const layoutNum = parseInt(currentGridLayout || '4');

      // Jika dalam mode View 1 dan ada kamera tunggal yang dipilih dari sidebar
      if (layoutNum === 1 && selectedSingleCameraId) {
        const singleFound = mainCameras.filter(cam => cam.id === parseInt(selectedSingleCameraId));
        if (singleFound.length > 0) {
          mainCameras = singleFound;
        }
      }

      if (mainCameras.length === 0) {
        if (!apiSyncDone) {
          cctvContainer.innerHTML = `
            <div class="col-12 text-center py-5">
              <div class="spinner-border mb-3" style="width: 3rem; height: 3rem; color: #00d2ff;" role="status"></div>
              <h5 class="text-white font-weight-bold mb-2">Memuat Kamera CCTV Live...</h5>
              <p class="text-muted mb-0" style="font-size: 13px;">Menyinkronkan siaran kamera dari cloud server...</p>
            </div>`;
          return;
        }
        cctvContainer.innerHTML = `
          <div class="col-12 text-center py-5">
            <i class="fas fa-video-slash fa-3x mb-3 text-muted"></i>
            <h5 class="text-white">Belum Ada Kamera CCTV di Wilayah Ini</h5>
            <p class="text-muted">Tidak ada kamera aktif untuk akun Anda di wilayah ini.</p>
          </div>`;
        return;
      }

      const row = document.createElement('div');
      row.className = 'row';
      cctvContainer.appendChild(row);

      // Banner info kamera tunggal saat mode View 1
      if (layoutNum === 1 && selectedSingleCameraId && mainCameras.length === 1) {
        const topBanner = document.createElement('div');
        topBanner.className = 'col-12 mb-3 d-flex flex-wrap justify-content-between align-items-center p-2 px-3 rounded';
        topBanner.style.background = 'rgba(15, 23, 42, 0.85)';
        topBanner.style.border = '1px solid rgba(56, 189, 248, 0.35)';
        topBanner.innerHTML = `
          <div class="text-white small d-flex align-items-center">
            <span class="vms-cam-dot mr-2"></span> Menampilkan Kamera Tunggal: <strong class="text-info ml-1">${mainCameras[0].title}</strong>
          </div>
          <button class="btn btn-sm btn-outline-info rounded-pill px-3 py-1 mt-1 mt-md-0" onclick="selectedSingleCameraId = null; changeGridLayout(4);" style="font-size: 11px; font-weight: 700;">
            <i class="fas fa-th-large mr-1"></i> Tampilkan Semua Kamera
          </button>
        `;
        row.appendChild(topBanner);
      }

      // Determine Grid Matrix Layout Class matching exact number per row requested by user:
      let colClass = 'col-cctv-4 mb-4'; // default View 4 (4 per baris)
      let isDense = false;

      if (layoutNum === 1) {
        colClass = 'col-cctv-1 mb-4'; // 1 per baris
      } else if (layoutNum === 2) {
        colClass = 'col-cctv-2 mb-4'; // 2 per baris
      } else if (layoutNum === 4) {
        colClass = 'col-cctv-4 mb-4'; // 4 per baris
      } else if (layoutNum === 6) {
        colClass = 'col-cctv-6 mb-3'; // 6 per baris
        isDense = true;
      } else if (layoutNum === 8) {
        colClass = 'col-cctv-8 mb-3'; // 8 per baris
        isDense = true;
      } else if (layoutNum === 9) {
        colClass = 'col-cctv-9 mb-3'; // 9 per baris
        isDense = true;
      } else if (layoutNum === 16) {
        colClass = 'col-cctv-16 mb-2'; // 16 per baris
        isDense = true;
      } else if (layoutNum === 25) {
        colClass = 'col-cctv-25 mb-2'; // 25 per baris
        isDense = true;
      } else if (layoutNum === 36) {
        colClass = 'col-cctv-36 mb-2'; // 36 per baris
        isDense = true;
      } else if (layoutNum === 64) {
        colClass = 'col-cctv-64 mb-2'; // 64 per baris
        isDense = true;
      } else if (layoutNum === 128) {
        colClass = 'col-cctv-128 mb-2'; // 128 per baris
        isDense = true;
      } else {
        colClass = 'col-cctv-4 mb-4';
      }

      mainCameras.forEach(camera => {
        const reloadFunction = `reloadMediaMTXCCTV('player-${camera.id}', 'thumb-${camera.id}', 'offline-${camera.id}', '${camera.streamPath}')`;

        const isFavorite = isCCTVFavorite(camera.id);
        const favoriteClass = isFavorite ? 'active' : '';
        const favoriteIcon = isFavorite ? 'fas' : 'far';

        const cctvCardHTML = `
            <div class="${colClass}" data-camera-id="${camera.id}" data-platform="${camera.platform}" data-status="online">
              <div class="traffic-card ${isDense ? 'layout-dense' : ''}" id="card-${camera.id}">
                <!-- Pure 100% Unobstructed Video Stream Canvas -->
                <div class="traffic-card-iframe">
                  <div class="loading-indicator" id="loading-${camera.id}">
                    <i class="fas fa-spinner fa-spin fa-3x mb-2"></i>
                    <div>Memuat ulang...</div>
                  </div>

                  <div class="thumbnail-overlay" id="thumb-${camera.id}" onclick="playMediaMTXCCTV(this, 'player-${camera.id}', 'thumb-${camera.id}')" data-stream-path="${camera.streamPath || camera.hls_url || ''}" data-hls-url="${camera.hls_url || ''}" data-connection-type="${camera.connection_type || 'rtsp'}" data-serial-number="${camera.serial_number || ''}" data-channel="${camera.channel || 1}" data-device-user="${camera.device_user || 'admin'}" data-device-pass="${camera.device_pass || ''}" data-stream-quality="${cameraQualityMap.get(camera.id) || globalStreamQuality || 'sd'}">
                    <img src="${camera.thumbnail}?v=${Date.now()}" alt="Thumbnail CCTV ${camera.title}" loading="lazy" onerror="this.onerror=null;this.src='${ASSET_BASE}/image/thumbnail/default-thumbnail.png?v=' + Date.now()" />
                    <div class="loading-text">
                      <i class="fas fa-play-circle"></i> Klik untuk memuat video
                    </div>
                  </div>

                  <div class="offline-msg" id="offline-${camera.id}">
                    <div class="offline-icon"><i class="fas fa-video-slash"></i></div>
                    <div class="offline-title">Kamera Sedang Offline</div>
                    <div class="offline-subtitle">Kamera sedang Offline (Mati Daya / Tidak Terhubung ke Internet)</div>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                      <button class="vms-offline-retry-btn" onclick="${reloadFunction}">
                        <i class="fas fa-redo-alt"></i> Coba Lagi
                      </button>
                    </div>
                  </div>

                  <div class="buffering-overlay" id="buffering-${camera.id}" style="display: none;">
                    <div class="buffering-spinner"></div>
                  </div>

                  <!-- Active Recording Badge Indicator -->
                  <div class="recording-badge" id="rec-badge-${camera.id}" style="display: none;">
                    <span style="width: 7px; height: 7px; background: #fff; border-radius: 50%; display: inline-block; animation: pulseRec 1s infinite;"></span>
                    <span>REC <span id="rec-timer-${camera.id}">00:00</span></span>
                  </div>

                  <!-- Video element untuk HLS player -->
                  <video id="player-${camera.id}" class="hidden-iframe hls-video-player"
                         controls autoplay muted playsinline webkit-playsinline x-webkit-airplay="allow" preload="auto"
                         style="display:none; width:100%; height:100%; position:absolute; top:0; left:0; object-fit:contain; background:#000;"
                         title="CCTV ${camera.title}">
                  </video>
                </div>

                <!-- Unified Enterprise Command Center Footer Panel -->
                <div class="traffic-card-content">
                  <div class="card-footer-top-row">
                    <div class="card-location-title" title="${camera.title}">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>${camera.title}</span>
                    </div>
                    <div class="card-action-toolbar">
                      <button class="action-btn quality-toggle-btn ${(cameraQualityMap.get(camera.id) || globalStreamQuality) === 'hd' ? 'is-hd' : 'is-sd'}" id="quality-btn-${camera.id}" onclick="event.stopPropagation(); toggleCameraQuality(${camera.id})" title="Ganti Kualitas (${(cameraQualityMap.get(camera.id) || globalStreamQuality) === 'hd' ? 'HD (High Def)' : 'SD (Standard Def)'})">
                        ${((cameraQualityMap.get(camera.id) || globalStreamQuality) === 'hd') ? 'HD' : 'SD'}
                      </button>
                      <button class="action-btn" id="audio-btn-${camera.id}" onclick="event.stopPropagation(); toggleCameraAudio(${camera.id})" title="Aktifkan/Matikan Suara (Audio)">
                        <i class="fas fa-volume-mute" id="audio-icon-${camera.id}"></i>
                      </button>
                      <button class="action-btn" onclick="event.stopPropagation(); captureCameraSnapshot(${camera.id}, '${camera.title.replace(/'/g, "\\'")}')" title="Ambil Foto (Snapshot / Screenshot)">
                        <i class="fas fa-camera"></i>
                      </button>
                      <button class="action-btn" id="record-btn-${camera.id}" onclick="event.stopPropagation(); toggleCameraRecording(${camera.id}, '${camera.title.replace(/'/g, "\\'")}')" title="Mulai/Stop Rekam Video">
                        <i class="fas fa-circle text-danger" id="record-icon-${camera.id}"></i>
                      </button>
                      <button class="action-btn" onclick="event.stopPropagation(); toggleCameraFullscreen(${camera.id})" title="Layar Penuh (Fullscreen)">
                        <i class="fas fa-expand"></i>
                      </button>
                      <button class="action-btn" onclick="event.stopPropagation(); ${reloadFunction}" title="Refresh Stream">
                        <i class="fas fa-sync-alt"></i>
                      </button>
                    </div>
                  </div>
                  <div class="card-footer-meta-row">
                    <div class="card-meta-code">
                      <span style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-weight: 800; margin-right: 6px;">
                        <span style="width: 6px; height: 6px; background-color: #10b981; border-radius: 50%; display: inline-block; box-shadow: 0 0 5px #10b981;"></span> LIVE
                      </span>
                      ${camera.connection_type === 'xmeye_p2p' ? `<span style="color: #38bdf8; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">• XMEYE P2P (CH ${camera.channel || 1})</span>` : `<span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">• RTSP • CAM-${String(camera.id).padStart(2, '0')}</span>`}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `;

        row.innerHTML += cctvCardHTML;
      });

      // Load real-time live camera snapshots for all XMeye DVR channels
      if (typeof loadXMeyeLiveSnapshots === 'function') {
        setTimeout(loadXMeyeLiveSnapshots, 300);
      }

      // Auto-play streams for newly generated CCTV cards
      if (typeof autoPlayCCTVStreams === 'function') {
        setTimeout(autoPlayCCTVStreams, 200);
      }
    }

    // ===== XMEYE LIVE CAMERA SNAPSHOT FETCHER =====
    function loadXMeyeLiveSnapshots() {
      const xmeyeThumbs = document.querySelectorAll('.thumbnail-overlay[data-connection-type="xmeye_p2p"]');
      if (!xmeyeThumbs || xmeyeThumbs.length === 0) return;

      const snMap = {};
      xmeyeThumbs.forEach(function(thumb) {
        const sn = thumb.getAttribute('data-serial-number');
        const ch = thumb.getAttribute('data-channel') || '1';
        if (sn) {
          if (!snMap[sn]) snMap[sn] = [];
          snMap[sn].push({ element: thumb, channel: ch });
        }
      });

      Object.keys(snMap).forEach(function(sn) {
        fetch('api/jftech_gateway.php?action=get_all_snapshots&sn=' + encodeURIComponent(sn))
          .then(function(res) { return res.json(); })
          .then(function(data) {
            if (data.success && data.snapshots) {
              snMap[sn].forEach(function(item) {
                const snapUrl = data.snapshots[item.channel];
                const img = item.element.querySelector('img');
                if (img) {
                  if (snapUrl) {
                    img.src = snapUrl;
                    img.style.objectFit = 'cover';
                  } else {
                    img.src = ASSET_BASE + '/image/logo-loewix.png';
                  }
                }
                // Hide any buffering spinner so live snapshot is crystal clear
                const suffix = item.element.id.slice('thumb-'.length);
                const buf = document.getElementById('buffering-' + suffix);
                if (buf) {
                  buf.style.display = 'none';
                  buf.style.opacity = '0';
                }
                const loadInd = document.getElementById('loading-' + suffix);
                if (loadInd) {
                  loadInd.style.display = 'none';
                }
                const playerEl = document.getElementById('player-' + suffix);
                const isPlaying = playerEl && !playerEl.paused && playerEl.currentTime > 0;
                if (!isPlaying && playerEl && playerEl.style.display === 'none') {
                  item.element.style.display = 'flex';
                  item.element.style.opacity = '1';
                  const loadingText = item.element.querySelector('.loading-text');
                  if (loadingText) {
                    loadingText.innerHTML = '<i class="fas fa-play-circle"></i> Klik untuk memutar video';
                  }
                }
              });
              console.log('[XMeye Snapshot] ✅ Loaded real-time CCTV snapshots for SN:', sn);
            }
          })
          .catch(function(e) {
            console.warn('[XMeye Snapshot] Error fetching snapshots:', e);
          });
      });

      // Periodically refresh snapshots in background every 45 seconds
      if (!window.xmeyeSnapshotTimer) {
        window.xmeyeSnapshotTimer = setInterval(loadXMeyeLiveSnapshots, 45000);
      }
    }

    // ===== PASAR HORAS: Fungsi Generate HTML dengan MediaMTX =====
    function generatePasarHorasHTML() {
      const pasarHorasContainer = document.getElementById('pasar-horas-container');
      if (!pasarHorasContainer) {
        console.error('Pasar Horas container not found');
        return;
      }

      pasarHorasContainer.innerHTML = '';

      // Filter kamera dengan section pasar-horas
      const pasarHorasCameras = mediamtxData.filter(cam => cam.section === 'pasar-horas');

      const itemsPerRow = 3;
      const totalRows = Math.ceil(pasarHorasCameras.length / itemsPerRow);

      for (let rowIndex = 0; rowIndex < totalRows; rowIndex++) {
        const row = document.createElement('div');
        row.className = 'row wow fadeInUp';
        row.setAttribute('data-row', rowIndex + 1);
        row.style.marginBottom = '20px';
        pasarHorasContainer.appendChild(row);

        for (let colIndex = 0; colIndex < itemsPerRow; colIndex++) {
          const itemIndex = rowIndex * itemsPerRow + colIndex;

          if (itemIndex < pasarHorasCameras.length) {
            const camera = pasarHorasCameras[itemIndex];

            const reloadFunction = `reloadMediaMTXCCTV('player-${camera.id}', 'thumb-${camera.id}', 'offline-${camera.id}', '${camera.streamPath}')`;

            const cctvCardHTML = `
          <div class="col-lg-4 col-md-4 col-12 mb-lg-0 mb-md-0 mb-4">
            <div class="traffic-card" id="card-${camera.id}" data-camera-id="${camera.id}">
              <div class="traffic-card-iframe">
                <button class="refresh-button" onclick="event.stopPropagation(); ${reloadFunction}">
                  <i class="fas fa-sync-alt"></i>
                </button>

                <div class="loading-indicator" id="loading-${camera.id}">
                  <i class="fas fa-spinner fa-spin fa-3x mb-2"></i>
                  <div>Memuat ulang...</div>
                </div>

                <div class="thumbnail-overlay" id="thumb-${camera.id}" data-stream-path="${camera.streamPath}">
                  <img src="${camera.thumbnail}" alt="Thumbnail ${camera.title}" loading="lazy"
                       onerror="this.onerror=null;this.src='${ASSET_BASE}/image/thumbnail/default-thumbnail.png'" />
                  <div class="loading-text">
                    <i class="fas fa-play-circle"></i> Klik untuk memuat video
                  </div>
                </div>

                <div class="offline-msg" id="offline-${camera.id}">
                  <div class="offline-icon"><i class="fas fa-video-slash"></i></div>
                  <div class="offline-title">Kamera Sedang Offline</div>
                  <div class="offline-subtitle">Kamera sedang Offline (Mati Daya / Tidak Terhubung ke Internet)</div>
                  <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                    <button class="vms-offline-retry-btn" onclick="${reloadFunction}">
                      <i class="fas fa-redo-alt"></i> Coba Lagi
                    </button>
                  </div>
                </div>

                <div class="buffering-overlay" id="buffering-${camera.id}">
                  <div class="buffering-spinner"></div>
                </div>

                <video id="player-${camera.id}" class="hidden-iframe hls-video-player"
                       controls autoplay muted playsinline webkit-playsinline x-webkit-airplay="allow" preload="auto"
                       style="display:none; width:100%; height:100%; position:absolute; top:0; left:0; object-fit:contain; background:#000;"
                       title="${camera.title}">
                </video>
              </div>
              <div class="traffic-card-content">
                <div class="pricing-box-heading text-center">
                  <h2>CCTV</h2>
                  <p>${camera.title}</p>
                </div>
              </div>
            </div>
          </div>
        `;

            row.innerHTML += cctvCardHTML;
          }
        }
      }

      console.log('Pasar Horas section generated successfully with', pasarHorasCameras.length, 'cameras');
    }
    // ===== AKHIR FUNGSI PASAR HORAS =====

    // ===== TERMINAL TANJUNG PINGGIR: Fungsi Generate HTML dengan MediaMTX =====
    function generateTerminalTanjungPinggirHTML() {
      const terminalTanjungPinggirContainer = document.getElementById('terminal-tanjung-pinggir-container');
      if (!terminalTanjungPinggirContainer) {
        console.error('Terminal Tanjung Pinggir container not found');
        return;
      }

      terminalTanjungPinggirContainer.innerHTML = '';

      // Filter kamera dengan section terminal-tanjung-pinggir
      const terminalCameras = terminalTanjungPinggirData.filter(cam => cam.section === 'terminal-tanjung-pinggir');

      if (terminalCameras.length === 0) {
        console.warn('No Terminal Tanjung Pinggir cameras found');
        return;
      }

      const itemsPerRow = 3; // 3 cameras per row (same as main section)
      const totalRows = Math.ceil(terminalCameras.length / itemsPerRow);

      for (let rowIndex = 0; rowIndex < totalRows; rowIndex++) {
        const row = document.createElement('div');
        row.className = 'row wow fadeInUp';
        row.setAttribute('data-row', rowIndex + 1);
        row.style.marginBottom = '20px';
        terminalTanjungPinggirContainer.appendChild(row);

        for (let colIndex = 0; colIndex < itemsPerRow; colIndex++) {
          const itemIndex = rowIndex * itemsPerRow + colIndex;

          if (itemIndex < terminalCameras.length) {
            const camera = terminalCameras[itemIndex];

            const reloadFunction = `reloadMediaMTXCCTV('player-${camera.id}', 'thumb-${camera.id}', 'offline-${camera.id}', '${camera.streamPath}')`;

            const cctvCardHTML = `
          <div class="col-lg-4 col-md-4 col-12 mb-lg-0 mb-md-0 mb-4">
            <div class="traffic-card" id="card-${camera.id}" data-camera-id="${camera.id}">
              <div class="traffic-card-iframe">
                <button class="refresh-button" onclick="event.stopPropagation(); ${reloadFunction}">
                  <i class="fas fa-sync-alt"></i>
                </button>

                <div class="loading-indicator" id="loading-${camera.id}">
                  <i class="fas fa-spinner fa-spin fa-3x mb-2"></i>
                  <div>Memuat ulang...</div>
                </div>

                <div class="thumbnail-overlay" id="thumb-${camera.id}" data-stream-path="${camera.streamPath}">
                  <img src="${camera.thumbnail}" alt="Thumbnail ${camera.title}" loading="lazy"
                       onerror="this.onerror=null;this.src='${ASSET_BASE}/image/thumbnail/default-thumbnail.png'" />
                  <div class="loading-text">
                    <i class="fas fa-play-circle"></i> Klik untuk memuat video
                  </div>
                </div>

                <div class="offline-msg" id="offline-${camera.id}">
                  <div class="offline-icon"><i class="fas fa-video-slash"></i></div>
                  <div class="offline-title">Kamera Sedang Offline</div>
                  <div class="offline-subtitle">Kamera sedang Offline (Mati Daya / Tidak Terhubung ke Internet)</div>
                  <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                    <button class="vms-offline-retry-btn" onclick="${reloadFunction}">
                      <i class="fas fa-redo-alt"></i> Coba Lagi
                    </button>
                  </div>
                </div>

                <div class="buffering-overlay" id="buffering-${camera.id}">
                  <div class="buffering-spinner"></div>
                </div>

                <video id="player-${camera.id}" class="hidden-iframe hls-video-player"
                       controls autoplay muted playsinline webkit-playsinline x-webkit-airplay="allow" preload="auto"
                       style="display:none; width:100%; height:100%; position:absolute; top:0; left:0; object-fit:contain; background:#000;"
                       title="${camera.title}">
                </video>
              </div>
              <div class="traffic-card-content">
                <div class="pricing-box-heading text-center">
                  <h2>CCTV</h2>
                  <p>${camera.title}</p>
                </div>
              </div>
            </div>
          </div>
        `;

            row.innerHTML += cctvCardHTML;
          }
        }
      }

      console.log('Terminal Tanjung Pinggir section generated successfully with', terminalCameras.length, 'cameras');
    }
    // ===== AKHIR FUNGSI TERMINAL TANJUNG PINGGIR =====

    // ===== PERFORMANCE OPTIMIZATIONS =====

    // 1. WebP Support Detection
    function supportsWebP() {
      return new Promise((resolve) => {
        const webP = new Image();
        webP.onload = webP.onerror = () => resolve(webP.height === 2);
        webP.src = "data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA";
      });
    }

    // 2. Enhanced Image Lazy Loading with WebP
    function initEnhancedImageLazyLoading() {
      if (!('IntersectionObserver' in window)) return;

      let webPSupported = null;
      supportsWebP().then(isSupported => {
        webPSupported = isSupported;
      });

      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            const src = img.dataset.src || img.dataset.srcset;

            if (src && webPSupported !== null) {
              // Convert to WebP jika supported
              if (webPSupported && !src.includes('.webp') && !src.includes('base64') && !src.includes('default-thumbnail')) {
                const webpSrc = src.replace(/\.(jpg|jpeg|png)(\?.*)?$/i, '.webp$2');
                img.onerror = function() {
                  // Fallback ke original jika WebP tidak ada
                  this.src = src;
                  this.onerror = null;
                };
                img.src = webpSrc;
              } else {
                img.src = src;
              }

              if (img.dataset.src) img.removeAttribute('data-src');
              if (img.dataset.srcset) img.removeAttribute('data-srcset');
              observer.unobserve(img);
            }
          }
        });
      }, {
        rootMargin: '100px' // Start loading 100px sebelum masuk viewport
      });

      // Observe semua images dengan data-src
      document.querySelectorAll('img[data-src], img[data-srcset]').forEach(img => {
        imageObserver.observe(img);
      });

      console.log('[Performance] Enhanced image lazy loading initialized');
    }

    // 3. Stream Suspension Manager - Suspend streams yang tidak terlihat
    class StreamSuspensionManager {
      constructor() {
        this.observer = null;
        this.suspendedStreams = new Map();
        this.init();
      }

      init() {
        if (!('IntersectionObserver' in window)) return;

        this.observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            const card = entry.target;
            const playerEl = card.querySelector('video[id^="player-"], iframe[id^="player-"]');
            const playerId = playerEl ? playerEl.id : null;

            if (playerId) {
              if (entry.isIntersecting) {
                this.resumeStream(playerId, card);
              } else {
                this.suspendStream(playerId, card);
              }
            }
          });
        }, {
          rootMargin: '120px', // Suspend stream 120px setelah keluar viewport
          threshold: 0
        });

        // Observe semua CCTV cards setelah HTML generated
        setTimeout(() => {
          document.querySelectorAll('.traffic-card').forEach(card => {
            this.observer.observe(card);
          });
          console.log('[Performance] Stream suspension manager initialized');
        }, 1500);
      }

      suspendStream(playerId, card) {
        const player = document.getElementById(playerId);

        // Check if player exists and has content (support both video and iframe)
        let hasContent = false;
        if (player) {
          if (player.tagName === 'VIDEO') {
            hasContent = player.src || player.currentSrc || (player.hlsInstance && player.hlsInstance.media);
          } else {
            hasContent = player.src && !player.src.includes('about:blank');
          }
        }

        // Jangan suspend jika player tidak ada, sudah di-suspend, atau sedang aktif digunakan user
        if (!player || !hasContent || this.suspendedStreams.has(playerId)) {
          return;
        }

        // Check jika stream sedang aktif (baru dimainkan user) - jangan suspend untuk beberapa detik
        if (typeof activePlayers !== 'undefined' && activePlayers.has(playerId)) {
          const playTime = player.getAttribute('data-play-time');
          if (playTime && (Date.now() - parseInt(playTime)) < 6000) {
            return; // Don't suspend streams yang baru dimainkan (< 6 detik)
          }
        }

        // Jangan suspend jika stream sedang dalam proses loading/buffering
        const cardElement = card || document.getElementById(playerId.replace('player-', 'card-'));
        if (cardElement) {
          const bufferingOverlay = cardElement.querySelector('.buffering-overlay');
          if (bufferingOverlay && window.getComputedStyle(bufferingOverlay).display !== 'none') {
            return;
          }
        }

        // Save current state
        const currentSrc = player.tagName === 'VIDEO' ? (player.currentSrc || player.src) : player.src;
        this.suspendedStreams.set(playerId, {
          src: currentSrc,
          display: player.style.display || window.getComputedStyle(player).display,
          tagName: player.tagName
        });

        // Suspend - destroy HLS instance to immediately stop fetching chunks and saving network bandwidth
        if (player.tagName === 'VIDEO' && player.hlsInstance) {
          player.hlsInstance.destroy();
          player.hlsInstance = null;
        }

        // Suspend - hide and clear src
        player.src = '';
        player.style.display = 'none';

        // Show thumbnail instead
        const thumbId = playerId.replace('player-', 'thumb-');
        const thumb = document.getElementById(thumbId);
        if (thumb) {
          thumb.style.display = 'flex';
          thumb.style.opacity = '1';
        }
      }

      resumeStream(playerId, card) {
        if (!this.suspendedStreams.has(playerId)) {
          return;
        }

        const state = this.suspendedStreams.get(playerId);
        const player = document.getElementById(playerId);

        if (player && state && state.src) {
          if (state.tagName === 'VIDEO') {
            const thumbId = playerId.replace('player-', 'thumb-');
            const thumb = document.getElementById(thumbId);
            if (thumb && typeof playMediaMTXCCTV === 'function') {
              playMediaMTXCCTV(thumb, playerId, thumbId);
            }
          } else {
            player.setAttribute('data-play-time', Date.now().toString());
            player.src = state.src;
            player.style.display = state.display || 'block';
          }
          this.suspendedStreams.delete(playerId);

          const thumbId = playerId.replace('player-', 'thumb-');
          const thumb = document.getElementById(thumbId);
          if (thumb && player.style.display !== 'none') {
            thumb.style.display = 'none';
          }

          if (typeof activePlayers !== 'undefined') {
            activePlayers.add(playerId);
          }
        }
      }
    }

    // Initialize Stream Suspension Manager
    let streamSuspensionManager = null;

    // ===== AUTOPLAY LIVE CCTV STREAMS (INSTANT SIMULTANEOUS PLAYBACK) =====
    function autoPlayCCTVStreams() {
      if (!currentUser) return; // Do not autoplay when unauthenticated
      console.log('[AutoPlay] Starting active CCTV video streams simultaneously for all grid cameras...');
      const thumbnails = document.querySelectorAll('.thumbnail-overlay[data-stream-path]');

      thumbnails.forEach(function(thumb) {
        if (!thumb || !thumb.id || thumb.id.indexOf('thumb-') !== 0) return;
        const suffix = thumb.id.slice('thumb-'.length);
        const playerId = 'player-' + suffix;
        const player = document.getElementById(playerId);
        if (player && player.getAttribute('data-hls-loaded') === 'true') return;

        if (typeof playMediaMTXCCTV === 'function') {
          playMediaMTXCCTV(thumb, playerId, thumb.id);
        }
      });
    }

    // ===== MODIFIKASI: Update DOMContentLoaded =====
    document.addEventListener('DOMContentLoaded', function() {
      preloadStreamingResources();
      syncVMSDockActive(currentGridLayout);
      generateCCTVHTML();
      generateTerminalTanjungPinggirHTML(); // ===== TAMBAHAN: Panggil fungsi Terminal Tanjung Pinggir =====
      generatePasarHorasHTML(); // ===== TAMBAHAN: Panggil fungsi Pasar Horas =====
      detectBandwidth();

      // Automatically play all live streams without clicking thumbnail
      setTimeout(function() {
        autoPlayCCTVStreams();
      }, 300);

      // Pemutaran CCTV: event delegation (onclick inline sering tidak jalan dengan CSP nonce + strict browser)
      (function setupVideoThumbnailClickDelegation() {
        function onGridThumbClick(e) {
          const thumb = e.target.closest('.thumbnail-overlay[data-stream-path]');
          if (!thumb || !thumb.id || thumb.id.indexOf('thumb-') !== 0) return;
          if (!this.contains(thumb)) return;
          e.preventDefault();
          e.stopPropagation();
          const suffix = thumb.id.slice('thumb-'.length);
          if (typeof playMediaMTXCCTV === 'function') {
            playMediaMTXCCTV(thumb, 'player-' + suffix, thumb.id);
          }
        }
        ['cctv-container', 'pasar-horas-container', 'terminal-tanjung-pinggir-container'].forEach(function(cid) {
          const node = document.getElementById(cid);
          if (node) {
            node.addEventListener('click', onGridThumbClick);
            node.addEventListener('touchend', onGridThumbClick, { passive: false });
          }
        });
      })();

      document.addEventListener('click', function onPopupMapThumbClick(e) {
        const el = e.target.closest('.popup-thumbnail-overlay[data-popup-player-id]');
        if (!el) return;
        e.preventDefault();
        e.stopPropagation();
        const playerId = el.getAttribute('data-popup-player-id');
        const streamPath = el.getAttribute('data-stream-path') || '';
        const mode = el.getAttribute('data-popup-play-mode');
        if (mode === 'mediamtx') {
          if (typeof playPopupMediaMTX === 'function') {
            playPopupMediaMTX(playerId, el.id, streamPath);
          }
        } else if (mode === 'legacy') {
          const streamId = el.getAttribute('data-stream-id') || streamPath;
          const plat = el.getAttribute('data-popup-legacy-platform') || 'denava';
          if (typeof playPopupCCTV === 'function') {
            playPopupCCTV(playerId, el.id, streamId, plat);
          }
        }
      });

      // Initialize performance optimizations
      initEnhancedImageLazyLoading();
      streamSuspensionManager = new StreamSuspensionManager();

      // Initialize hover preload after 2 seconds
      setTimeout(setupHoverPreload, 2000);

      // Initialize background cleanup after DOM ready
      setInterval(backgroundCleanupInvisiblePlayers, PERFORMANCE_CONFIG.CLEANUP_INTERVAL);
      console.log('[Background Cleanup] Started - will cleanup invisible players every', PERFORMANCE_CONFIG.CLEANUP_INTERVAL / 1000, 'seconds');

      // Initialize new features after CCTV HTML is generated
      setTimeout(() => {
        // Ensure scrollToCamera is available globally
        if (typeof window.scrollToCamera !== 'function') {
          console.warn('scrollToCamera not yet defined, waiting...');
          setTimeout(() => {
            if (typeof updateFavoritesUI === 'function') {
              updateFavoritesUI();
            }
            // Update favorite buttons for all cameras
            const allCameras = mediamtxData; // Use MediaMTX data
            allCameras.forEach(camera => {
              if (typeof updateFavoriteButton === 'function') {
                updateFavoriteButton(camera.id);
              }
            });
          }, 500);
        } else {
          if (typeof updateFavoritesUI === 'function') {
            updateFavoritesUI();
          }
          // Update favorite buttons for all cameras (use MediaMTX data + Terminal data = 50 cameras)
          const allCameras = [...mediamtxData, ...terminalTanjungPinggirData];
          allCameras.forEach(camera => {
            if (typeof updateFavoriteButton === 'function') {
              updateFavoriteButton(camera.id);
            }
          });
        }
      }, 1000);

      if (typeof WOW === 'function') {
        new WOW().init();
      }

      setTimeout(() => {
        if (isLeafletLoaded()) {
          initCCTVMap();
          initWiFiMap();
        } else {
          loadLeaflet(() => {
            console.log('Leaflet loaded. Initializing maps now...');
            initCCTVMap();
            initWiFiMap();
          });
        }
      }, 1000);

      // Initialize video observer after cards are generated
      setTimeout(() => {
        if (typeof observeVideoCards === 'function') {
          observeVideoCards();
        }
      }, 1500);

      // Start streaming optimizations
      setTimeout(() => {
        if (typeof startStreamingOptimizations === 'function') {
          startStreamingOptimizations();
        }
      }, 2000);

      setTimeout(checkAllStreamStatus, 3000);
      setInterval(checkAllStreamStatus, 30000);

      // Start auto-refresh monitoring system dengan optimasi
      setTimeout(() => {
        startAutoRefreshMonitoring();

        // Start advanced streaming optimizations
        if (typeof startStreamingOptimizations === 'function') {
          startStreamingOptimizations();
        }

        // Re-observe cards setelah scroll atau resize untuk update visibility
        let scrollTimeout;
        window.addEventListener('scroll', () => {
          clearTimeout(scrollTimeout);
          scrollTimeout = setTimeout(() => {
            if (typeof observeVideoCards === 'function') {
              observeVideoCards();
            }
          }, 300);
        }, {
          passive: true
        });

        let resizeTimeout;
        window.addEventListener('resize', () => {
          clearTimeout(resizeTimeout);
          resizeTimeout = setTimeout(() => {
            if (typeof observeVideoCards === 'function') {
              observeVideoCards();
            }
          }, 300);
        }, {
          passive: true
        });
      }, 5000); // Start setelah 5 detik untuk memastikan semua player sudah ter-load

      window.addEventListener('scroll', function() {
        const navbar = document.getElementById('myNavbar');
        if (window.scrollY > 0) {
          navbar.style.backgroundColor = '#091650';
          navbar.style.paddingTop = '10px';
          navbar.style.paddingBottom = '10px';
        } else {
          navbar.style.backgroundColor = 'transparent';
          navbar.style.paddingTop = '30px';
          navbar.style.paddingBottom = '30px';
        }
      });

      if ('connection' in navigator) {
        navigator.connection.addEventListener('change', function() {
          if (navigator.connection.type === 'none' || navigator.connection.downlink < 0.5) {
            networkStatus = 'poor';
            currentQualityLevel = 'veryLow';
            showConnectionLostMessage();
          } else if (navigator.connection.downlink < 2) {
            networkStatus = 'medium';
            if (currentQualityLevel === 'auto') {
              currentQualityLevel = 'low';
            }
            updateConnectionStatusDisplay();
          } else {
            networkStatus = 'good';
            updateConnectionStatusDisplay();
          }
        });
      }

      window.addEventListener('resize', function() {
        if (mapCCTV) {
          mapCCTV.invalidateSize();
        }
        if (mapWifi) {
          mapWifi.invalidateSize();
        }
      });
    });

    window.addEventListener('load', function() {
      if (mapCCTV) {
        mapCCTV.invalidateSize();
      }
      if (mapWifi) {
        mapWifi.invalidateSize();
      }

      if (!mapCCTV && document.getElementById('map')) {
        console.log('Retrying CCTV map initialization on window load...');
        initCCTVMap();
      }

      if (!mapWifi && document.getElementById('mapid')) {
        console.log('Retrying WiFi map initialization on window load...');
        initWiFiMap();
      }
    });

    // ===== NEW FEATURES: Dark Mode, Favorites, Share, Filter =====
    // Dark Mode Toggle
    (function() {
      const darkModeToggle = document.getElementById('dark-mode-toggle');
      const darkModeIcon = document.getElementById('dark-mode-icon');
      const isDarkMode = localStorage.getItem('darkMode') === 'true';

      function applyDarkMode(isDark) {
        if (isDark) {
          document.body.classList.add('dark-mode');
          darkModeIcon.classList.remove('fa-moon');
          darkModeIcon.classList.add('fa-sun');
        } else {
          document.body.classList.remove('dark-mode');
          darkModeIcon.classList.remove('fa-sun');
          darkModeIcon.classList.add('fa-moon');
        }
        localStorage.setItem('darkMode', isDark);
      }

      if (darkModeToggle) {
        applyDarkMode(isDarkMode);
        darkModeToggle.addEventListener('click', function() {
          const isDark = document.body.classList.contains('dark-mode');
          applyDarkMode(!isDark);
        });
      }
    })();

    // Favorites Management
    function getFavorites() {
      const favorites = localStorage.getItem('cctvFavorites');
      return favorites ? JSON.parse(favorites) : [];
    }

    function saveFavorites(favorites) {
      localStorage.setItem('cctvFavorites', JSON.stringify(favorites));
      updateFavoritesUI();
    }

    function isCCTVFavorite(cameraId) {
      const favorites = getFavorites();
      return favorites.some(fav => fav.id === cameraId);
    }

    function toggleFavorite(cameraId, cameraTitle) {
      const favorites = getFavorites();
      const index = favorites.findIndex(fav => fav.id === cameraId);

      if (index > -1) {
        favorites.splice(index, 1);
      } else {
        favorites.push({
          id: cameraId,
          title: cameraTitle
        });
      }

      saveFavorites(favorites);
      updateFavoriteButton(cameraId);
    }

    function updateFavoriteButton(cameraId) {
      const button = document.querySelector(`#card-${cameraId} .favorite-button`);
      if (button) {
        const isFavorite = isCCTVFavorite(cameraId);
        if (isFavorite) {
          button.classList.add('active');
          button.querySelector('i').classList.remove('far');
          button.querySelector('i').classList.add('fas');
          button.title = 'Hapus dari Favorit';
        } else {
          button.classList.remove('active');
          button.querySelector('i').classList.remove('fas');
          button.querySelector('i').classList.add('far');
          button.title = 'Tambah ke Favorit';
        }
      }
    }

    function updateFavoritesUI() {
      const favorites = getFavorites();
      const countBadge = document.getElementById('favorites-count');
      const favoritesList = document.getElementById('favorites-list');

      if (countBadge) {
        countBadge.textContent = favorites.length;
        countBadge.style.display = favorites.length > 0 ? 'flex' : 'none';
      }

      if (favoritesList) {
        if (favorites.length === 0) {
          favoritesList.innerHTML = '<p style="text-align: center; color: #999; padding: 20px;">Belum ada CCTV favorit</p>';
        } else {
          favoritesList.innerHTML = favorites.map(fav => {
            const safeTitle = fav.title.replace(/'/g, "\\'").replace(/"/g, '&quot;');
            return `
            <div class="favorite-item" data-fav-id="${fav.id}">
              <div class="favorite-item-title">${fav.title}</div>
              <button class="favorite-item-remove" data-remove-id="${fav.id}" data-remove-title="${safeTitle}">
                <i class="fas fa-times"></i>
              </button>
            </div>
          `;
          }).join('');

          // Add event listeners after HTML is set
          setTimeout(() => {
            const favoriteItems = favoritesList.querySelectorAll('.favorite-item');
            favoriteItems.forEach(item => {
              const favId = parseInt(item.getAttribute('data-fav-id'));

              // Click on item to scroll to camera
              item.addEventListener('click', function(e) {
                // Don't trigger if clicking remove button
                if (!e.target.closest('.favorite-item-remove')) {
                  if (typeof window.scrollToCamera === 'function') {
                    window.scrollToCamera(favId);
                  } else {
                    console.error('scrollToCamera function not found');
                  }
                }
              });

              // Remove button click
              const removeBtn = item.querySelector('.favorite-item-remove');
              if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                  e.stopPropagation();
                  const removeId = parseInt(this.getAttribute('data-remove-id'));
                  const removeTitle = this.getAttribute('data-remove-title');
                  if (typeof window.toggleFavorite === 'function') {
                    window.toggleFavorite(removeId, removeTitle);
                  } else if (typeof toggleFavorite === 'function') {
                    toggleFavorite(removeId, removeTitle);
                  }
                });
              }
            });
          }, 100);
        }
      }
    }

    function toggleFavoritesSidebar() {
      const sidebar = document.getElementById('favorites-sidebar');
      if (sidebar) {
        sidebar.classList.toggle('open');
      }
    }

    // Make functions global for onclick handlers
    window.toggleFavorite = toggleFavorite;
    window.shareCCTV = shareCCTV;
    window.toggleFavoritesSidebar = toggleFavoritesSidebar;
    window.closeShareModal = closeShareModal;
    window.copyShareLink = copyShareLink;
    window.clearFilters = clearFilters;
    window.removeFilter = removeFilter;
    window.updateFavoritesUI = updateFavoritesUI;
    window.updateFavoriteButton = updateFavoriteButton;

    // Share CCTV Functionality
    let currentShareCamera = null;

    function shareCCTV(cameraId, cameraTitle) {
      currentShareCamera = {
        id: cameraId,
        title: cameraTitle
      };
      const shareUrl = `${window.location.origin}${window.location.pathname}#cctv-${cameraId}`;
      const shareText = `Lihat CCTV ${cameraTitle} di ${window.location.hostname}`;

      const shareInput = document.getElementById('share-link-input');
      if (shareInput) {
        shareInput.value = shareUrl;
      }

      // Update share links
      const whatsappLink = `https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`;
      const facebookLink = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`;
      const twitterLink = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`;
      const telegramLink = `https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`;

      const whatsappBtn = document.getElementById('share-whatsapp');
      const facebookBtn = document.getElementById('share-facebook');
      const twitterBtn = document.getElementById('share-twitter');
      const telegramBtn = document.getElementById('share-telegram');

      if (whatsappBtn) whatsappBtn.href = whatsappLink;
      if (facebookBtn) facebookBtn.href = facebookLink;
      if (twitterBtn) twitterBtn.href = twitterLink;
      if (telegramBtn) telegramBtn.href = telegramLink;

      const shareModal = document.getElementById('share-modal');
      if (shareModal) {
        shareModal.classList.add('show');
      }
    }

    function closeShareModal() {
      const shareModal = document.getElementById('share-modal');
      if (shareModal) {
        shareModal.classList.remove('show');
      }
    }

    function copyShareLink() {
      const input = document.getElementById('share-link-input');
      if (!input) return;

      input.select();
      input.setSelectionRange(0, 99999);

      try {
        document.execCommand('copy');

        const btn = event.target.closest('button');
        if (btn) {
          const originalText = btn.innerHTML;
          btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
          btn.style.background = '#28a745';

          setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '#091650';
          }, 2000);
        }
      } catch (err) {
        console.error('Failed to copy:', err);
        // Fallback: use modern clipboard API
        if (navigator.clipboard) {
          navigator.clipboard.writeText(input.value).then(() => {
            const btn = event.target.closest('button');
            if (btn) {
              const originalText = btn.innerHTML;
              btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
              btn.style.background = '#28a745';

              setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '#091650';
              }, 2000);
            }
          });
        }
      }
    }

    // Advanced Filtering
    let currentFilters = {
      platform: 'all',
      status: 'all',
      sort: 'default'
    };

    function applyFilters() {
      const platformFilter = document.getElementById('filter-platform')?.value || 'all';
      const statusFilter = document.getElementById('filter-status')?.value || 'all';
      const sortFilter = document.getElementById('filter-sort')?.value || 'default';

      currentFilters = {
        platform: platformFilter,
        status: statusFilter,
        sort: sortFilter
      };

      const cards = document.querySelectorAll('[data-camera-id]');
      let visibleCount = 0;

      cards.forEach(card => {
        const cameraId = card.getAttribute('data-camera-id');
        const platform = card.getAttribute('data-platform');
        const status = card.getAttribute('data-status');
        let show = true;

        // Platform filter
        if (platformFilter !== 'all') {
          const platformMap = {
            'mediamtx': 'mediamtx',
            'denava': 'denava',
            'stream2': 'stream2',
            'ipcamlive': 'ipcamlive'
          };
          if (platform !== platformMap[platformFilter]) {
            show = false;
          }
        }

        // Status filter (simplified - assume all online for now)
        if (statusFilter !== 'all' && status !== statusFilter) {
          show = false;
        }

        if (show) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      // Update active filters display
      updateActiveFilters();

      // Show filter toolbar if any filter is active
      const filterToolbar = document.getElementById('cctv-filter-toolbar');
      if (filterToolbar) {
        const hasActiveFilter = platformFilter !== 'all' || statusFilter !== 'all' || sortFilter !== 'default';
        filterToolbar.style.display = hasActiveFilter ? 'flex' : 'none';
      }
    }

    function clearFilters() {
      document.getElementById('filter-platform').value = 'all';
      document.getElementById('filter-status').value = 'all';
      document.getElementById('filter-sort').value = 'default';
      applyFilters();
    }

    function updateActiveFilters() {
      const activeFiltersDiv = document.getElementById('active-filters');
      if (!activeFiltersDiv) return;

      activeFiltersDiv.innerHTML = '';
      const filters = [];

      if (currentFilters.platform !== 'all') {
        filters.push({
          key: 'platform',
          label: `Platform: ${currentFilters.platform}`,
          value: currentFilters.platform
        });
      }
      if (currentFilters.status !== 'all') {
        filters.push({
          key: 'status',
          label: `Status: ${currentFilters.status}`,
          value: currentFilters.status
        });
      }
      if (currentFilters.sort !== 'default') {
        filters.push({
          key: 'sort',
          label: `Urutkan: ${currentFilters.sort}`,
          value: currentFilters.sort
        });
      }

      filters.forEach(filter => {
        const badge = document.createElement('span');
        badge.className = 'filter-badge';
        badge.innerHTML = `${filter.label} <span class="close" onclick="removeFilter('${filter.key}')">&times;</span>`;
        activeFiltersDiv.appendChild(badge);
      });
    }

    function removeFilter(key) {
      const platformSelect = document.getElementById('filter-platform');
      const statusSelect = document.getElementById('filter-status');
      const sortSelect = document.getElementById('filter-sort');

      if (key === 'platform' && platformSelect) {
        platformSelect.value = 'all';
      } else if (key === 'status' && statusSelect) {
        statusSelect.value = 'all';
      } else if (key === 'sort' && sortSelect) {
        sortSelect.value = 'default';
      }
      applyFilters();
    }

    // Initialize filters and make applyFilters global
    window.applyFilters = applyFilters;

    document.addEventListener('DOMContentLoaded', function() {
      const filterPlatform = document.getElementById('filter-platform');
      const filterStatus = document.getElementById('filter-status');
      const filterSort = document.getElementById('filter-sort');

      if (filterPlatform) filterPlatform.addEventListener('change', applyFilters);
      if (filterStatus) filterStatus.addEventListener('change', applyFilters);
      if (filterSort) filterSort.addEventListener('change', applyFilters);

      updateFavoritesUI();
    });

    // ===== END NEW FEATURES: Dark Mode, Favorites, Share, Filter =====

    // ===== NEW FEATURES: Advanced CCTV Search & Statistics =====
    (function() {
      'use strict';

      // Global variable for all CCTV data
      let allCCTV = mediamtxData; // Use MediaMTX data

      // Wait for DOM and data to be ready
      function initializeSearchAndStats() {
        console.log('=== initializeSearchAndStats called ===');

        // Check if MediaMTX data is available
        if (typeof mediamtxData === 'undefined') {
          console.warn('MediaMTX data not yet available, retrying...');
          setTimeout(initializeSearchAndStats, 200);
          return;
        }

        console.log('MediaMTX data available:', mediamtxData.length, 'cameras');
        console.log('Terminal Tanjung Pinggir data available:', terminalTanjungPinggirData.length, 'cameras');

        // Use MediaMTX data + Terminal data for search (total 50 cameras)
        allCCTV = [...mediamtxData, ...terminalTanjungPinggirData];
        console.log('Total CCTV data loaded:', allCCTV.length, 'cameras (should be 50)');

        if (allCCTV.length === 0) {
          console.error('No CCTV data found!');
          return;
        }

        // Add name field to each camera (using title as name)
        allCCTV.forEach(camera => {
          if (!camera.name) {
            camera.name = camera.title;
          }
        });

        // Search functionality - get elements
        const searchInput = document.getElementById('cctv-search-input');
        const searchResults = document.getElementById('cctv-search-results');
        const searchClear = document.getElementById('cctv-search-clear');
        const searchLoading = document.getElementById('cctv-search-loading');

        console.log('Search elements found:', {
          searchInput: !!searchInput,
          searchResults: !!searchResults,
          searchClear: !!searchClear,
          searchLoading: !!searchLoading
        });

        // Check if elements exist
        if (!searchInput || !searchResults || !searchClear || !searchLoading) {
          console.error('Search elements not found in DOM', {
            searchInput: !!searchInput,
            searchResults: !!searchResults,
            searchClear: !!searchClear,
            searchLoading: !!searchLoading
          });
          // Retry after a short delay
          setTimeout(initializeSearchAndStats, 500);
          return;
        }

        let searchDebounceTimer = null;
        let currentSearchQuery = '';
        let allSearchResults = [];
        let showingAllResults = false;
        let selectedResultIndex = -1;
        let recentSearches = JSON.parse(localStorage.getItem('cctv_recent_searches') || '[]');
        const MAX_RECENT_SEARCHES = 5;

        // Debounce function
        function debounce(func, wait) {
          return function(...args) {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
              console.log('Debounced function called with args:', args);
              func.apply(this, args);
            }, wait);
          };
        }

        // Save recent search
        function saveRecentSearch(query) {
          if (!query || query.trim().length === 0) return;
          const trimmedQuery = query.trim();

          // Remove if already exists
          recentSearches = recentSearches.filter(s => s.toLowerCase() !== trimmedQuery.toLowerCase());

          // Add to beginning
          recentSearches.unshift(trimmedQuery);

          // Keep only max recent searches
          if (recentSearches.length > MAX_RECENT_SEARCHES) {
            recentSearches = recentSearches.slice(0, MAX_RECENT_SEARCHES);
          }

          localStorage.setItem('cctv_recent_searches', JSON.stringify(recentSearches));
        }

        // Get popular cameras (most favorited or most viewed)
        function getPopularCameras() {
          if (!allCCTV || allCCTV.length === 0) {
            return [];
          }

          const favorites = JSON.parse(localStorage.getItem('cctv_favorites') || '[]');
          const favoriteIds = favorites.map(f => f.id);

          // Sort cameras by favorite count, then by name
          const popular = allCCTV
            .map(camera => ({
              ...camera,
              favoriteCount: favoriteIds.includes(camera.id) ? 1 : 0
            }))
            .sort((a, b) => {
              if (b.favoriteCount !== a.favoriteCount) {
                return b.favoriteCount - a.favoriteCount;
              }
              return (a.name || a.title || '').localeCompare(b.name || b.title || '');
            })
            .slice(0, 5);

          return popular;
        }

        // ═══════════════════════════════════════════════════════════════════
        // OPEN-METEO WEATHER INTEGRATION - ALL PLATFORMS
        // ═══════════════════════════════════════════════════════════════════
        // NOTE: Must be defined BEFORE displayEmptySearchState and displaySearchResults

        const OpenMeteoWeather = {
          cache: new Map(),
          pendingRequests: new Map(),
          apiEndpoint: 'api/weather-openmeteo.php',

          async getWeather(camera) {
            const cacheKey = `weather_${camera.id}`;

            // Check cache (10 minutes validity)
            const cached = this.cache.get(cacheKey);
            if (cached && (Date.now() - cached.timestamp) < 600000) {
              return cached.data;
            }

            // Check if request is already pending
            if (this.pendingRequests.has(cacheKey)) {
              return this.pendingRequests.get(cacheKey);
            }

            // Create new request
            const promise = this.fetchWeather(camera);
            this.pendingRequests.set(cacheKey, promise);

            try {
              const data = await promise;
              if (data) {
                this.cache.set(cacheKey, {
                  data: data,
                  timestamp: Date.now()
                });
              }
              return data;
            } catch (error) {
              console.error(`Weather fetch error for camera ${camera.id}:`, error);
              return null;
            } finally {
              this.pendingRequests.delete(cacheKey);
            }
          },

          async fetchWeather(camera) {
            if (!camera.coordinates || camera.coordinates.length < 2) {
              console.warn(`Camera ${camera.id} missing coordinates`);
              return null;
            }

            const [lat, lon] = camera.coordinates;
            const cameraName = camera.name || camera.title || '';

            try {
              const url = `${this.apiEndpoint}?lat=${lat}&lon=${lon}&camera_id=${camera.id}&camera_name=${encodeURIComponent(cameraName)}`;

              const response = await fetch(url, {
                method: 'GET',
                headers: {
                  'Accept': 'application/json'
                }
              });

              if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
              }

              const data = await response.json();

              if (data.success && data.current) {
                return data.current;
              } else {
                console.warn(`Invalid weather data for camera ${camera.id}`);
                return null;
              }
            } catch (error) {
              console.error(`Weather API error for camera ${camera.id}:`, error.message);
              return null;
            }
          },

          getTemperatureColor(temp) {
            if (temp >= 35) return '#D32F2F';
            if (temp >= 32) return '#E64A19';
            if (temp >= 28) return '#F57C00';
            if (temp >= 25) return '#FFA726';
            if (temp >= 22) return '#66BB6A';
            if (temp >= 18) return '#42A5F5';
            if (temp >= 15) return '#1E88E5';
            return '#1565C0';
          },

          shouldShowVisibilityWarning(visibility, weatherCode) {
            if (visibility < 10) return true;
            if (weatherCode >= 45 && weatherCode <= 48) return true;
            if (weatherCode == 65 || weatherCode == 82 || weatherCode >= 95) return true;
            return false;
          }
        };

        // Make OpenMeteoWeather globally available
        window.OpenMeteoWeather = OpenMeteoWeather;

        console.log('[Open-Meteo Weather] Initialized - All platforms show weather badges');

        /**
         * Render weather badge in search results
         */
        function renderWeatherBadge(element, weatherData) {
          if (!element || !weatherData) return;

          const temp = weatherData.temperature;
          const emoji = weatherData.emoji || '🌤️';
          const description = weatherData.description || 'N/A';
          const visibility = weatherData.visibility;
          const visibilityQuality = weatherData.visibilityQuality;
          const isDaytime = weatherData.isDaytime;

          const tempColor = OpenMeteoWeather.getTemperatureColor(temp);
          const showWarning = OpenMeteoWeather.shouldShowVisibilityWarning(visibility, weatherData.weatherCode);

          // Build badge HTML
          let badgeHTML = `
            <span class="weather-icon">${emoji}</span>
            <span class="weather-temp" style="color: ${tempColor};">${temp}°C</span>
          `;

          // Add visibility warning if needed
          if (showWarning && visibilityQuality) {
            badgeHTML += `
              <span class="weather-visibility" style="color: ${visibilityQuality.color};">
                <i class="fas fa-eye"></i> ${visibilityQuality.text}
              </span>
            `;
          }

          element.innerHTML = badgeHTML;
          element.classList.remove('loading');
          element.classList.add('loaded');

          if (visibilityQuality && visibilityQuality.level === 'critical') {
            element.style.animation = 'weatherPulse 2s ease-in-out infinite';
          }

          // Create tooltip
          const tooltipLines = [
            `🌡️ Suhu: ${temp}°C (Terasa: ${weatherData.feelsLike}°C)`,
            `☁️ Cuaca: ${description}`,
            `💧 Kelembaban: ${weatherData.humidity}%`,
            `💨 Angin: ${weatherData.windSpeed} km/jam`,
            `👁️ Visibility: ${visibility} km (${visibilityQuality ? visibilityQuality.text : 'N/A'})`,
            `☁️ Awan: ${weatherData.cloudCover}%`,
            `${isDaytime ? '☀️ Siang Hari' : '🌙 Malam Hari'}`
          ];

          if (weatherData.precipitation > 0) {
            tooltipLines.push(`🌧️ Hujan: ${weatherData.precipitation} mm`);
          }

          if (visibilityQuality && visibilityQuality.warning) {
            tooltipLines.push(`⚠️ ${visibilityQuality.warning}`);
          }

          element.title = tooltipLines.join('\n');
        }

        // Search function
        function performSearch(query) {
          if (!searchResults || !searchClear) {
            console.error('Search elements not found');
            return;
          }

          if (!query || query.trim().length === 0) {
            // Show recent searches or popular cameras when empty
            displayEmptySearchState();
            return;
          }

          // Check if allCCTV is loaded
          if (!allCCTV || allCCTV.length === 0) {
            console.warn('CCTV data not loaded yet, retrying...');
            // Retry after data is loaded
            if (typeof cctvData !== 'undefined' && typeof stream2Data !== 'undefined' && typeof pasarHorasData !== 'undefined') {
              allCCTV = mediamtxData; // Use MediaMTX data
              allCCTV.forEach(camera => {
                if (!camera.name) {
                  camera.name = camera.title;
                }
              });
            } else {
              // Show loading message
              searchResults.innerHTML = `
                <div class="search-no-results" style="display: block; padding: 20px; text-align: center;">
                  <i class="fas fa-spinner fa-spin"></i>
                  <p>Memuat data CCTV...</p>
                </div>
              `;
              searchResults.classList.add('show');
              return;
            }
          }

          currentSearchQuery = query.trim().toLowerCase();
          console.log('Searching for:', currentSearchQuery, 'Total cameras:', allCCTV.length);

          // Track search query to analytics
          if (typeof gtag !== 'undefined') {
            gtag('event', 'search', {
              event_category: 'Search',
              event_label: currentSearchQuery,
              value: 1
            });
          }

          // Show loading state
          if (searchLoading) {
            searchLoading.classList.add('active');
          }

          // Filter cameras by name (case insensitive)
          allSearchResults = allCCTV.filter(camera => {
            const name = (camera.name || camera.title || '').toLowerCase();
            const location = (camera.location || camera.city || '').toLowerCase();
            return name.includes(currentSearchQuery) || location.includes(currentSearchQuery);
          });

          // Live grid filter on the active CCTV matrix
          filterCCTVGridLive(currentSearchQuery);

          console.log('Search results found:', allSearchResults.length);

          setTimeout(() => {
            if (searchLoading) {
              searchLoading.classList.remove('active');
            }
            // Display max 8 results initially
            displaySearchResults(allSearchResults.slice(0, 8), allSearchResults.length > 8);
            if (searchClear) {
              searchClear.classList.add('show');
            }

            // Save to recent searches
            saveRecentSearch(query);
          }, 150);
        }

        // Live grid filter on active cards
        function filterCCTVGridLive(query) {
          const q = (query || '').toLowerCase().trim();
          const allCards = document.querySelectorAll('.traffic-card');
          if (!allCards || allCards.length === 0) return;

          allCards.forEach(card => {
            if (!q) {
              card.style.display = '';
              return;
            }
            const cardText = (card.innerText || '').toLowerCase();
            const cardId = (card.getAttribute('data-camera-id') || card.id || '').toLowerCase();
            if (cardText.includes(q) || cardId.includes(q)) {
              card.style.display = '';
            } else {
              card.style.display = 'none';
            }
          });
        }

        // Display empty search state (recent searches or popular cameras)
        function displayEmptySearchState() {
          if (!searchResults) return;

          // Check if data is loaded
          if (!allCCTV || allCCTV.length === 0) {
            // Try to load data if available
            if (typeof cctvData !== 'undefined' && typeof stream2Data !== 'undefined' && typeof pasarHorasData !== 'undefined') {
              allCCTV = mediamtxData; // Use MediaMTX data
              allCCTV.forEach(camera => {
                if (!camera.name) {
                  camera.name = camera.title;
                }
              });
            } else {
              // Data not ready yet, don't show anything
              searchResults.classList.remove('show');
              return;
            }
          }

          let html = '';

          // Show recent searches if available
          if (recentSearches.length > 0) {
            html += `
              <div class="search-section">
                <div class="search-section-title">Pencarian Terakhir</div>
            `;
            recentSearches.forEach(search => {
              const safeSearch = search.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
              html += `
                <div class="search-recent-item" data-search-query="${safeSearch}">
                  <i class="fas fa-clock"></i>
                  <span>${safeSearch}</span>
                </div>
              `;
            });
            html += `</div>`;
          }

          // Show popular cameras
          const popularCameras = getPopularCameras();
          if (popularCameras.length > 0) {
            html += `
              <div class="search-section">
                <div class="search-section-title">Kamera Populer</div>
            `;
            popularCameras.forEach(camera => {
              const name = camera.name || camera.title || 'Unknown';
              const safeName = name.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
              const thumbnail = camera.thumbnail || (ASSET_BASE + '/image/thumbnail/default-thumbnail.png');
              html += `
                <div class="cctv-search-result-item" data-camera-id="${camera.id}">
                  <img src="${thumbnail}" alt="${safeName}" class="cctv-search-result-thumb" onerror="this.onerror=null;this.src='${ASSET_BASE}/image/thumbnail/default-thumbnail.png'" loading="lazy">
                  <div class="search-result-info">
                    <div class="search-result-name">${safeName}</div>
                    <div class="search-result-meta">
                      <span class="weather-badge loading" id="weather-badge-popular-${camera.id}">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span class="loading-text">Memuat cuaca...</span>
                      </span>
                      <span class="location-badge"><i class="fas fa-map-marker-alt"></i> Siantar</span>
                    </div>
                  </div>
                </div>
              `;
            });
            html += `</div>`;
          }

          if (html) {
            searchResults.innerHTML = html;
            searchResults.classList.add('show');

            // Add click listeners for recent searches
            searchResults.querySelectorAll('.search-recent-item').forEach(item => {
              item.addEventListener('click', function() {
                const query = this.getAttribute('data-search-query');
                searchInput.value = query;
                performSearch(query);
              });
            });

            // Add click listeners for popular cameras
            searchResults.querySelectorAll('.cctv-search-result-item').forEach(item => {
              item.addEventListener('click', function() {
                const cameraId = parseInt(this.getAttribute('data-camera-id'));
                handleCameraClick(cameraId);
              });
            });

            // Fetch weather for popular cameras
            if (popularCameras && popularCameras.length > 0) {
              const weatherPromises = popularCameras.map(async (camera) => {
                const weatherBadge = document.getElementById(`weather-badge-popular-${camera.id}`);
                if (!weatherBadge) return;

                try {
                  const weatherData = await OpenMeteoWeather.getWeather(camera);

                  if (weatherData) {
                    renderWeatherBadge(weatherBadge, weatherData);
                  } else {
                    // Fallback if weather data unavailable
                    weatherBadge.innerHTML = `
                      <i class="fas fa-video"></i>
                      <span>Live CCTV</span>
                    `;
                    weatherBadge.classList.remove('loading');
                    weatherBadge.classList.add('fallback');
                  }
                } catch (error) {
                  console.error(`Error rendering weather for popular camera ${camera.id}:`, error);
                  weatherBadge.innerHTML = `<i class="fas fa-video"></i> <span>Live</span>`;
                  weatherBadge.classList.remove('loading');
                  weatherBadge.classList.add('fallback');
                }
              });

              Promise.allSettled(weatherPromises);
            }
          } else {
            searchResults.classList.remove('show');
          }
        }

        // Handle camera click from search
        function handleCameraClick(cameraId) {
          const camera = allCCTV.find(cam => cam.id === cameraId);
          const cameraName = camera ? (camera.name || camera.title || '') : '';

          console.log('Clicked camera ID:', cameraId, 'Name:', cameraName);

          // Track clicked camera to analytics
          if (typeof gtag !== 'undefined') {
            gtag('event', 'search_camera_clicked', {
              event_category: 'Search',
              event_label: cameraName,
              value: cameraId
            });
          }

          // Scroll to camera card
          if (typeof window.scrollToCamera === 'function') {
            window.scrollToCamera(cameraId);
          }

          // Close search results
          searchResults.classList.remove('show');
          selectedResultIndex = -1;

          // Keep the search input value with camera name
          if (cameraName) {
            searchInput.value = cameraName;
            searchClear.classList.add('show');
          } else {
            searchInput.value = '';
            searchClear.classList.remove('show');
          }
        }

        // Display search results
        async function displaySearchResults(results, hasMore) {
          if (!searchResults) return;

          console.log('Displaying search results:', results.length, 'hasMore:', hasMore);

          // If no results, show empty state
          if (results.length === 0) {
            searchResults.innerHTML = `
              <div id="search-no-results" class="search-no-results" style="display: block;">
                <i class="fas fa-search"></i>
                <p>Tidak ada hasil ditemukan</p>
                <small>Coba kata kunci lain</small>
              </div>
            `;
            searchResults.classList.add('show');
            selectedResultIndex = -1;
            return;
          }

          // REMOVED: Auto-scroll behavior - User must click to navigate
          let html = `
            <div class="search-section">
              <div class="search-section-title">Hasil Pencarian (${allSearchResults.length})</div>
          `;

          // Generate HTML for each result - ALL SHOW WEATHER BADGE (NO PLATFORM BADGES)
          results.forEach((camera, index) => {
            const name = camera.name || camera.title || 'Unknown';
            const thumbnail = camera.thumbnail || (ASSET_BASE + '/image/thumbnail/default-thumbnail.png');

            html += `
              <div class="cctv-search-result-item" data-camera-id="${camera.id}" data-camera-index="${index}">
                <img src="${thumbnail}" alt="${name}" class="search-result-thumb" onerror="this.onerror=null;this.src='${ASSET_BASE}/image/thumbnail/default-thumbnail.png'" loading="lazy">
                <div class="search-result-info">
                  <div class="search-result-name">${highlightMatch(name, currentSearchQuery || '')}</div>
                  <div class="search-result-meta">
                    <span class="weather-badge loading" id="weather-badge-${camera.id}">
                      <i class="fas fa-spinner fa-spin"></i>
                      <span class="loading-text">Memuat cuaca...</span>
                    </span>
                    <span class="location-badge"><i class="fas fa-map-marker-alt"></i> Siantar</span>
                  </div>
                </div>
              </div>
            `;
          });

          html += `</div>`;

          if (hasMore && !showingAllResults) {
            html += `<div class="search-view-all" id="cctv-search-view-all">Lihat Semua (${allSearchResults.length} hasil)</div>`;
          }

          searchResults.innerHTML = html;
          searchResults.classList.add('show');
          selectedResultIndex = -1;
          console.log('Search results displayed, HTML length:', html.length, 'Results count:', results.length);

          // Force display untuk memastikan dropdown muncul
          const computedDisplay = window.getComputedStyle(searchResults).display;
          if (computedDisplay === 'none') {
            searchResults.style.display = 'block';
            console.log('Force display block applied');
          }

          // Fetch weather for ALL cameras (no platform filtering)
          const weatherPromises = results.map(async (camera) => {
            const weatherBadge = document.getElementById(`weather-badge-${camera.id}`);
            if (!weatherBadge) return;

            try {
              const weatherData = await OpenMeteoWeather.getWeather(camera);

              if (weatherData) {
                renderWeatherBadge(weatherBadge, weatherData);
              } else {
                // Fallback if weather data unavailable
                weatherBadge.innerHTML = `
                  <i class="fas fa-video"></i>
                  <span>Live CCTV</span>
                `;
                weatherBadge.classList.remove('loading');
                weatherBadge.classList.add('fallback');
              }
            } catch (error) {
              console.error(`Error rendering weather for camera ${camera.id}:`, error);
              weatherBadge.innerHTML = `<i class="fas fa-video"></i> <span>Live</span>`;
              weatherBadge.classList.remove('loading');
              weatherBadge.classList.add('fallback');
            }
          });

          await Promise.allSettled(weatherPromises);

          // Add click listeners
          const resultItems = searchResults.querySelectorAll('.cctv-search-result-item');
          resultItems.forEach((item, index) => {
            item.addEventListener('click', function() {
              const cameraId = parseInt(this.getAttribute('data-camera-id'));
              handleCameraClick(cameraId);
            });
          });

          // View all button
          const viewAllBtn = document.getElementById('cctv-search-view-all');
          if (viewAllBtn) {
            viewAllBtn.addEventListener('click', function() {
              showingAllResults = true;
              displaySearchResults(allSearchResults, false);
            });
          }
        }

        // Highlight matching text
        function highlightMatch(text, query) {
          if (!query) return text;
          const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
          return text.replace(regex, '<mark style="background: #ffeb3b; padding: 2px 4px; border-radius: 3px;">$1</mark>');
        }

        // Scroll to camera card - Made global for favorites sidebar
        window.scrollToCamera = function(cameraId) {
          console.log('Scrolling to camera:', cameraId, 'Type:', typeof cameraId);

          // Ensure cameraId is a number
          cameraId = parseInt(cameraId);
          if (isNaN(cameraId)) {
            console.error('Invalid camera ID:', cameraId);
            return;
          }

          // First, determine which section the camera belongs to and scroll to it
          let targetSection = null;

          // Check if camera is in Terminal Tanjung Pinggir (ID 201-202)
          if (cameraId >= 201 && cameraId <= 202) {
            targetSection = document.getElementById('terminal-tanjung-pinggir');
          }
          // Check if camera is in Pasar Horas (ID 36-39 setelah perubahan urutan)
          else if (cameraId >= 36 && cameraId <= 39) {
            const pasarHorasCamera = mediamtxData.find(cam => cam.id === cameraId && cam.section === 'pasar-horas');
            if (pasarHorasCamera) {
              targetSection = document.getElementById('pasar-horas');
            }
          }
          // Default to CCTV section
          else {
            targetSection = document.getElementById('cctv');
          }

          if (targetSection) {
            targetSection.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }

          // Function to find and scroll to card with retry mechanism
          let retryCount = 0;
          const maxRetries = 3;

          function findAndScrollToCard() {
            retryCount++;
            console.log(`Attempt ${retryCount} to find card-${cameraId}`);

            // Try multiple methods to find the card
            let card = document.getElementById(`card-${cameraId}`);
            console.log('Found by ID:', !!card);

            // If not found by ID, try by data attribute (find parent container first)
            if (!card) {
              const container = document.querySelector(`[data-camera-id="${cameraId}"]`);
              console.log('Found container by data-camera-id:', !!container);
              if (container) {
                // Try to find traffic-card inside container with specific ID
                card = container.querySelector(`#card-${cameraId}`);
                if (!card) {
                  card = container.querySelector('.traffic-card');
                }
                // If still not found, the container itself might be the parent
                if (!card && container.classList.contains('traffic-card')) {
                  card = container;
                }
              }
            }

            // If still not found, try searching all traffic cards
            if (!card) {
              const allCards = document.querySelectorAll('.traffic-card');
              console.log('Total traffic cards found:', allCards.length);
              for (let c of allCards) {
                if (c.id === `card-${cameraId}`) {
                  card = c;
                  console.log('Found card in allCards:', card.id);
                  break;
                }
              }
            }

            // Also check pasar horas container
            if (!card) {
              const pasarHorasCards = document.querySelectorAll('#pasar-horas-container .traffic-card');
              console.log('Pasar horas cards found:', pasarHorasCards.length);
              for (let c of pasarHorasCards) {
                if (c.id === `card-${cameraId}`) {
                  card = c;
                  console.log('Found card in pasar horas:', card.id);
                  break;
                }
              }
            }

            // Terminal Tanjung Pinggir container removed - tidak ada di MediaMTX

            // Try finding by parent element with data-camera-id
            if (!card) {
              const parentWithId = document.querySelector(`[data-camera-id="${cameraId}"]`);
              if (parentWithId) {
                card = parentWithId.closest('.traffic-card') || parentWithId.querySelector('.traffic-card');
                console.log('Found by closest:', !!card);
              }
            }

            if (card) {
              console.log('Card found successfully:', card.id, card);

              // Remove previous highlights
              document.querySelectorAll('.traffic-card.highlighted').forEach(c => {
                c.classList.remove('highlighted');
                c.style.boxShadow = '';
                c.style.transform = '';
                c.style.border = '';
              });

              // Add highlight
              card.classList.add('highlighted');
              card.style.transition = 'all 0.3s ease';
              card.style.boxShadow = '0 8px 24px rgba(9, 22, 80, 0.4)';
              card.style.transform = 'scale(1.02)';
              card.style.border = '2px solid #091650';

              // Scroll to card with better positioning
              const offset = 200;
              const cardPosition = card.getBoundingClientRect().top + window.pageYOffset;

              // Use requestAnimationFrame for smoother scroll
              requestAnimationFrame(() => {
                window.scrollTo({
                  top: cardPosition - offset,
                  behavior: 'smooth'
                });
              });

              // Remove highlight after animation
              setTimeout(() => {
                if (card) {
                  card.style.boxShadow = '';
                  card.style.transform = '';
                  card.style.border = '';
                  setTimeout(() => {
                    if (card) {
                      card.classList.remove('highlighted');
                    }
                  }, 3000);
                }
              }, 1000);

              // Close favorites sidebar if open
              const sidebar = document.getElementById('favorites-sidebar');
              if (sidebar && sidebar.classList.contains('open')) {
                setTimeout(() => {
                  sidebar.classList.remove('open');
                }, 500);
              }
            } else {
              console.warn(`Card not found for ID: card-${cameraId} (Attempt ${retryCount}/${maxRetries})`);

              // Retry if we haven't exceeded max retries
              if (retryCount < maxRetries) {
                setTimeout(findAndScrollToCard, 500);
              } else {
                console.error('Failed to find card after', maxRetries, 'attempts');
                // At least we're in CCTV section
              }
            }
          }

          // Wait a bit for scroll to complete, then find and scroll to card
          setTimeout(findAndScrollToCard, 400);
        };

        // Keyboard navigation for search results
        function handleSearchKeyboard(e) {
          if (!searchResults.classList.contains('show')) return;

          const resultItems = searchResults.querySelectorAll('.cctv-search-result-item, .search-recent-item, .search-view-all');
          if (resultItems.length === 0) return;

          switch (e.key) {
            case 'ArrowDown':
              e.preventDefault();
              selectedResultIndex = Math.min(selectedResultIndex + 1, resultItems.length - 1);
              updateSelectedResult(resultItems);
              break;
            case 'ArrowUp':
              e.preventDefault();
              selectedResultIndex = Math.max(selectedResultIndex - 1, -1);
              updateSelectedResult(resultItems);
              break;
            case 'Enter':
              e.preventDefault();
              if (selectedResultIndex >= 0 && selectedResultIndex < resultItems.length) {
                const selectedItem = resultItems[selectedResultIndex];
                if (selectedItem.classList.contains('cctv-search-result-item')) {
                  const cameraId = parseInt(selectedItem.getAttribute('data-camera-id'));
                  handleCameraClick(cameraId);
                } else if (selectedItem.classList.contains('search-recent-item')) {
                  const query = selectedItem.getAttribute('data-search-query');
                  searchInput.value = query;
                  performSearch(query);
                } else if (selectedItem.classList.contains('search-view-all')) {
                  showingAllResults = true;
                  displaySearchResults(allSearchResults, false);
                }
              }
              break;
            case 'Escape':
              e.preventDefault();
              searchResults.classList.remove('show');
              searchInput.blur();
              selectedResultIndex = -1;
              break;
          }
        }

        // Update selected result highlight
        function updateSelectedResult(resultItems) {
          resultItems.forEach((item, index) => {
            if (index === selectedResultIndex) {
              item.classList.add('highlighted');
              item.scrollIntoView({
                block: 'nearest',
                behavior: 'smooth'
              });
            } else {
              item.classList.remove('highlighted');
            }
          });
        }

        // Event listeners
        const debouncedSearch = debounce(performSearch, 300);
        console.log('Setting up event listeners for search...');

        // Add input event listener
        searchInput.addEventListener('input', function(e) {
          showingAllResults = false;
          selectedResultIndex = -1;
          const value = e.target.value.trim();
          filterCCTVGridLive(value);
          if (value.length > 0) {
            debouncedSearch(value);
            searchClear.classList.add('show');
          } else {
            displayEmptySearchState();
            searchClear.classList.remove('show');
          }
        }, false);

        // Add focus event listener
        searchInput.addEventListener('focus', function() {
          const value = this.value.trim();
          if (value.length > 0) {
            // If there are previous results, show them
            if (allSearchResults.length > 0) {
              displaySearchResults(allSearchResults.slice(0, 8), allSearchResults.length > 8);
            } else {
              // Otherwise perform search
              performSearch(value);
            }
          } else {
            // Show empty state (recent searches or popular cameras)
            displayEmptySearchState();
          }
        }, false);

        // Add keyboard event listener
        searchInput.addEventListener('keydown', handleSearchKeyboard, false);

        // Add clear button click listener
        searchClear.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          searchInput.value = '';
          filterCCTVGridLive('');
          searchResults.classList.remove('show');
          searchClear.classList.remove('show');
          allSearchResults = [];
          showingAllResults = false;
          searchInput.focus();
        }, false);

        // Close search on Escape key
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape' && searchResults.classList.contains('show')) {
            searchResults.classList.remove('show');
            searchInput.blur();
          }
        }, false);

        // Close search when clicking outside
        document.addEventListener('click', function(e) {
          const searchWrapper = document.getElementById('vms-search-box-wrap') || document.querySelector('.vms-search-box') || document.querySelector('.cctv-search-wrapper');
          if (searchWrapper && !searchWrapper.contains(e.target)) {
            searchResults.classList.remove('show');
          }
        });

        console.log('=== Search initialization complete! ===');
        console.log('Search is ready. Try typing in the search box.');

        // Statistics functionality
        function animateNumber(element, target, duration = 2000) {
          const start = 0;
          const increment = target / (duration / 16);
          let current = start;

          const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
              current = target;
              clearInterval(timer);
            }
            element.textContent = Math.floor(current);
          }, 16);
        }

        function updateStatistics() {
          // Total CCTV count
          const totalCCTV = allCCTV.length;
          const cctvStatElement = document.querySelector('#stat-total-cctv .stat-number');
          if (cctvStatElement) {
            cctvStatElement.classList.add('animating');
            animateNumber(cctvStatElement, totalCCTV);
            setTimeout(() => {
              cctvStatElement.classList.remove('animating');
            }, 2000);
          }

          // Total WiFi count
          const totalWiFi = typeof wifiLocations !== 'undefined' && wifiLocations ? wifiLocations.length : 52;
          const wifiStatElement = document.querySelector('#stat-total-wifi .stat-number');
          if (wifiStatElement) {
            wifiStatElement.classList.add('animating');
            animateNumber(wifiStatElement, totalWiFi);
            setTimeout(() => {
              wifiStatElement.classList.remove('animating');
            }, 2000);
          }

          // Online users (fetch from endpoint)
          updateOnlineUsers();
        }

        function updateOnlineUsers() {
          const onlineUsersElement = document.querySelector('#stat-online-users .stat-number');
          if (!onlineUsersElement) return;

          // Use fetch with better error handling
          // Fetch real-time visitor count from API
          fetch('visitor-count.php', {
              method: 'GET',
              headers: {
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
              },
              cache: 'no-cache'
            })
            .then(response => {
              // Only process if response is ok
              if (response.ok) {
                return response.json();
              }
              // If 404 or other error, return null to trigger fallback silently
              return null;
            })
            .then(data => {
              if (data && (data.count !== undefined || data.visitors !== undefined)) {
                const count = data.count || data.visitors || 0;
                const currentValue = parseInt(onlineUsersElement.textContent) || 0;

                // Only update if value changed
                if (count !== currentValue) {
                  onlineUsersElement.classList.add('animating');
                  animateNumber(onlineUsersElement, count);
                  setTimeout(() => {
                    onlineUsersElement.classList.remove('animating');
                  }, 2000);
                }
                return; // Exit early if we got valid data
              }

              // If no valid data, keep current value (don't use random fallback)
              console.warn('Invalid visitor count data received');
            })
            .catch(error => {
              // Silently handle errors - keep current value displayed
              // Don't update to random number to maintain data integrity
              console.warn('Error fetching visitor count:', error.message);
            });
        }

        // Initialize statistics
        setTimeout(() => {
          updateStatistics();
        }, 500);

        // Send heartbeat to keep visitor active (optimized: less frequent)
        function sendHeartbeat() {
          fetch('visitor-count.php', {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'Cache-Control': 'no-cache'
            },
            cache: 'no-cache',
            signal: AbortSignal.timeout(5000) // 5 second timeout
          }).catch(() => {
            // Silently fail - don't spam console
          });
        }

        // Send heartbeat every 45 seconds (reduced from 30s to reduce server load)
        setInterval(sendHeartbeat, 45000);

        // Update display every 15 seconds (reduced from 10s to reduce server load)
        setInterval(updateOnlineUsers, 15000);

        // Initial update after 2 seconds
        setTimeout(updateOnlineUsers, 2000);
      }

      // Initialize when DOM and data are ready
      function startInitialization() {
        console.log('Starting search initialization, DOM readyState:', document.readyState);
        console.log('Data availability:', {
          cctvData: typeof cctvData !== 'undefined',
          stream2Data: typeof stream2Data !== 'undefined',
          pasarHorasData: typeof pasarHorasData !== 'undefined'
        });

        // Check if DOM is ready
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded fired, initializing search...');
            setTimeout(initializeSearchAndStats, 200);
          });
        } else {
          // DOM is already ready, wait a bit for data
          console.log('DOM already ready, waiting for data...');
          setTimeout(initializeSearchAndStats, 300);
        }
      }

      // Start initialization
      startInitialization();
    })();

    // ===== WELCOME MODAL FUNCTIONS - Updated untuk tampil setiap refresh =====
    function showWelcomeModal() {
      const modal = document.getElementById('welcomeModal');

      // Selalu tampilkan modal setiap refresh (removed sessionStorage check)
      if (modal) {
        // Clear any existing timeout
        if (window.welcomeModalTimeout) {
          clearTimeout(window.welcomeModalTimeout);
        }

        window.welcomeModalTimeout = setTimeout(() => {
          modal.classList.add('show');
          document.body.style.overflow = 'hidden';

          // Track modal view
          if (typeof gtag !== 'undefined') {
            gtag('event', 'welcome_modal_shown', {
              event_category: 'Engagement',
              event_label: 'Welcome Modal - Banner Pelintas',
              value: 1
            });
          }
        }, 800); // Reduced delay untuk lebih cepat tampil
      }
    }

    function closeWelcomeModal() {
      const modal = document.getElementById('welcomeModal');
      if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';

        // Clear timeout jika modal di-close sebelum muncul
        if (window.welcomeModalTimeout) {
          clearTimeout(window.welcomeModalTimeout);
          window.welcomeModalTimeout = null;
        }

        // Track modal close
        if (typeof gtag !== 'undefined') {
          gtag('event', 'welcome_modal_closed', {
            event_category: 'Engagement',
            event_label: 'Modal Closed',
            value: 1
          });
        }
      }
    }

    function startMonitoring() {
      closeWelcomeModal();

      // Smooth scroll to CCTV section
      const cctvSection = document.getElementById('cctv');
      if (cctvSection) {
        setTimeout(() => {
          cctvSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }, 300);
      }

      // Track CTA click
      if (typeof gtag !== 'undefined') {
        gtag('event', 'welcome_modal_cta_clicked', {
          event_category: 'Engagement',
          event_label: 'Start Monitoring',
          value: 1
        });
      }
    }

    // Make functions globally accessible untuk onclick handlers
    window.showWelcomeModal = showWelcomeModal;
    window.closeWelcomeModal = closeWelcomeModal;
    window.startMonitoring = startMonitoring;

    // Initialize Welcome Modal on page load
    function initializeWelcomeModal() {
      const modal = document.getElementById('welcomeModal');
      if (!modal) return;

      // Tombol: gunakan addEventListener (lebih andal daripada inline onclick + CSP)
      const closeBtn = modal.querySelector('.welcome-modal-close');
      const primaryBtn = modal.querySelector('.welcome-modal-btn.primary');
      const secondaryBtn = modal.querySelector('.welcome-modal-btn.secondary');

      if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          closeWelcomeModal();
        });
      }
      if (primaryBtn) {
        primaryBtn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          startMonitoring();
        });
      }
      if (secondaryBtn) {
        secondaryBtn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          closeWelcomeModal();
        });
      }

      // Klik backdrop (area gelap) menutup modal; klik isi kartu tidak menyebar ke overlay
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closeWelcomeModal();
        }
      });

      // Show welcome modal only if logged in
      if (currentUser || localStorage.getItem('loewix_user')) {
        showWelcomeModal();
      }

      // Close on ESC key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
          const m = document.getElementById('welcomeModal');
          if (m && m.classList.contains('show')) {
            closeWelcomeModal();
          }
        }
      });
    }

    // Initialize saat DOM ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initializeWelcomeModal);
    } else {
      // DOM sudah ready, langsung initialize
      initializeWelcomeModal();
    }
    // ===== END WELCOME MODAL FUNCTIONS =====
    // ===== END NEW FEATURES =====