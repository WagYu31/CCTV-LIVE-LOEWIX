/**
 * Loewix Surveillance VMS - Authentication & Session Controller (Login & Registration)
 * PT. LOEWIX INDONESIA
 */

function switchGateAuthMode(mode) {
  const loginForm = document.getElementById('formGateLogin');
  const registerForm = document.getElementById('formGateRegister');
  const tabLogin = document.getElementById('tab-btn-login');
  const tabRegister = document.getElementById('tab-btn-register');
  const headerTag = document.getElementById('gate-auth-tag-text');
  const headerTitle = document.getElementById('gate-auth-title-text');
  const headerSub = document.getElementById('gate-auth-sub-text');

  if (mode === 'register') {
    if (loginForm) loginForm.style.display = 'none';
    if (registerForm) registerForm.style.display = 'block';
    if (tabLogin) tabLogin.classList.remove('active');
    if (tabRegister) tabRegister.classList.add('active');

    if (headerTag) headerTag.textContent = 'REGISTRASI AKUN';
    if (headerTitle) headerTitle.textContent = 'Daftar Akun Baru';
    if (headerSub) headerSub.textContent = 'Lengkapi formulir di bawah untuk mendaftarkan akun portal pemantauan CCTV Loewix.';
  } else {
    if (loginForm) loginForm.style.display = 'block';
    if (registerForm) registerForm.style.display = 'none';
    if (tabLogin) tabLogin.classList.add('active');
    if (tabRegister) tabRegister.classList.remove('active');

    if (headerTag) headerTag.textContent = 'SELAMAT DATANG';
    if (headerTitle) headerTitle.textContent = 'Masuk ke Akun Anda';
    if (headerSub) headerSub.textContent = 'Masukkan email dan kata sandi akun Anda untuk membuka portal sistem pemantauan.';
  }
}

async function submitGateRegister(e) {
  e.preventDefault();
  const name = document.getElementById('gate-reg-name').value.trim();
  const email = document.getElementById('gate-reg-email').value.trim();
  const phone = document.getElementById('gate-reg-phone').value.trim();
  const city = document.getElementById('gate-reg-city').value;
  const password = document.getElementById('gate-reg-password').value;
  const confirmPassword = document.getElementById('gate-reg-confirm-password').value;
  const btnSubmit = document.getElementById('btn-gate-reg-submit');

  if (!name || !email || !password) {
    if (typeof showCCTVToast === 'function') {
      showCCTVToast('Silakan lengkapi Nama, Email, dan Kata Sandi.', 'warning');
    } else {
      alert('Silakan lengkapi Nama, Email, dan Kata Sandi.');
    }
    return;
  }

  if (password.length < 6) {
    if (typeof showCCTVToast === 'function') {
      showCCTVToast('Kata sandi minimal 6 karakter.', 'warning');
    } else {
      alert('Kata sandi minimal 6 karakter.');
    }
    return;
  }

  if (password !== confirmPassword) {
    if (typeof showCCTVToast === 'function') {
      showCCTVToast('Konfirmasi kata sandi tidak cocok!', 'warning');
    } else {
      alert('Konfirmasi kata sandi tidak cocok!');
    }
    return;
  }

  if (btnSubmit) {
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses Pendaftaran...';
  }

  const formData = new FormData();
  formData.append('action', 'register');
  formData.append('name', name);
  formData.append('email', email);
  formData.append('phone', phone);
  formData.append('city', city);
  formData.append('password', password);

  try {
    const res = await fetch('api/auth.php', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();

    if (data.success && data.user) {
      currentUser = data.user;
      localStorage.setItem('loewix_user', JSON.stringify(data.user));
      
      if (typeof showCCTVToast === 'function') {
        showCCTVToast('Pendaftaran akun berhasil! Selamat datang di Loewix VMS.', 'success');
      } else {
        alert('Pendaftaran akun berhasil! Selamat datang di Loewix VMS.');
      }

      showDashboardView(data.user);
    } else {
      if (typeof showCCTVToast === 'function') {
        showCCTVToast(data.message || 'Pendaftaran akun gagal.', 'danger');
      } else {
        alert(data.message || 'Pendaftaran akun gagal.');
      }
    }
  } catch (err) {
    console.error('Registration error:', err);
    if (typeof showCCTVToast === 'function') {
      showCCTVToast('Gagal terhubung ke server autentikasi.', 'danger');
    } else {
      alert('Gagal terhubung ke server autentikasi.');
    }
  } finally {
    if (btnSubmit) {
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = '<span>DAFTAR AKUN BARU</span> <i class="fas fa-arrow-right"></i>';
    }
  }
}

function toggleGatePasswordVisibility(inputId = 'gate-login-password', iconId = 'gate-toggle-pwd') {
  const pwdInput = document.getElementById(inputId);
  const eyeIcon = document.getElementById(iconId);
  if (!pwdInput) return;

  if (pwdInput.type === 'password') {
    pwdInput.type = 'text';
    if (eyeIcon) {
      eyeIcon.classList.remove('fa-eye');
      eyeIcon.classList.add('fa-eye-slash');
      eyeIcon.style.color = '#38bdf8';
    }
  } else {
    pwdInput.type = 'password';
    if (eyeIcon) {
      eyeIcon.classList.remove('fa-eye-slash');
      eyeIcon.classList.add('fa-eye');
      eyeIcon.style.color = '#94a3b8';
    }
  }
}

// Authentication & Quota Engine JavaScript Handlers
    document.addEventListener('DOMContentLoaded', () => {
      checkInitialAuthState();
    });

    function checkInitialAuthState() {
      // 1. Instant check from localStorage
      const stored = localStorage.getItem('loewix_user');
      if (stored) {
        try {
          const user = JSON.parse(stored);
          if (user && user.id) {
            currentUser = user;
            showDashboardView(user);
          }
        } catch(e) {}
      }

      // 2. Authoritative check from server session
      fetch('api/auth.php?action=check_session')
        .then(res => res.json())
        .then(data => {
          authCheckComplete = true;
          if (data.logged_in && data.user) {
            currentUser = data.user;
            localStorage.setItem('loewix_user', JSON.stringify(data.user));
            showDashboardView(data.user);
          } else {
            if (!currentUser) {
              showLoginGateView();
            }
          }
        })
        .catch(err => {
          authCheckComplete = true;
          if (!currentUser) {
            showLoginGateView();
          }
        });
    }

    function showDashboardView(user) {
      if (user) {
        currentUser = user;
        authCheckComplete = true;
      }
      const gate = document.getElementById('loewix-login-gate');
      const content = document.getElementById('main-app-content');
      if (gate) gate.style.display = 'none';
      if (content) content.style.display = 'block';

      renderUserSessionUI(currentUser);
      syncCustomLocalStorageCameras();
      if (typeof generateCCTVHTML === 'function') {
        generateCCTVHTML(typeof currentGlobalCity !== 'undefined' ? currentGlobalCity : 'all');
      }
    }

    function showLoginGateView() {
      const gate = document.getElementById('loewix-login-gate');
      const content = document.getElementById('main-app-content');
      if (gate) gate.style.display = 'flex';
      if (content) content.style.display = 'none';

      currentUser = null;
      renderUserSessionUI(null);
    }

    function submitGateLogin(e) {
      if (e) e.preventDefault();
      const email = document.getElementById('gate-login-email').value.trim();
      const password = document.getElementById('gate-login-password').value.trim();
      const btn = document.getElementById('btn-gate-submit');

      if (!email || !password) {
        alert('Email dan Password wajib diisi!');
        return;
      }

      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> MEMVERIFIKASI...';
      }

      const formData = new FormData();
      formData.append('action', 'login');
      formData.append('email', email);
      formData.append('password', password);

      fetch('api/auth.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          currentUser = res.user;
          localStorage.setItem('loewix_user', JSON.stringify(res.user));
          if (res.user && res.user.role === 'super_admin') {
            window.location.href = 'admin/index.html';
          } else {
            showDashboardView(res.user);
          }
        } else {
          alert(res.message || 'Email atau Password salah!');
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> MASUK KE SISTEM';
          }
        }
      })
      .catch(err => {
        // Fallback for static hosting
        if (email === 'admin@loewixcctv.com' && password === 'admin123') {
          const user = { id: 1, name: 'Super Admin Loewix', email: email, role: 'super_admin', cctv_quota: 9999, cctv_used: 0 };
          currentUser = user;
          localStorage.setItem('loewix_user', JSON.stringify(user));
          window.location.href = 'admin/index.html';
        } else if (email === 'customer@jayasentosa.com' && password === 'customer123') {
          const user = { id: 2, name: 'PT. Jaya Sentosa Enterprise', email: email, role: 'customer', cctv_quota: 10, cctv_used: 0 };
          currentUser = user;
          localStorage.setItem('loewix_user', JSON.stringify(user));
          showDashboardView(user);
        } else {
          alert('Email atau Password salah!');
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> MASUK KE SISTEM';
          }
        }
      });
    }

    function fillGateDemo(email, pwd) {
      const emailInput = document.getElementById('gate-login-email');
      const pwdInput = document.getElementById('gate-login-password');
      if (emailInput) emailInput.value = email;
      if (pwdInput) pwdInput.value = pwd;
    }

    function toggleGatePasswordVisibility() {
      const pwdInput = document.getElementById('gate-login-password');
      const eyeIcon = document.getElementById('gate-toggle-pwd');
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

    function renderUserSessionUI(user) {
      const userArea = document.getElementById('nav-user-area');
      if (!userArea) return;

      if (user) {
        const usedCount = (user && typeof user.cctv_used === 'number' && user.cctv_used > 0) 
          ? user.cctv_used 
          : (Array.isArray(mediamtxData) && mediamtxData.length > 0 ? mediamtxData.length : (user.cctv_used || 0));

        const navDashboardLink = document.getElementById('nav-item-customer-dashboard');
        if (navDashboardLink) navDashboardLink.style.display = 'block';

        if (user.role === 'super_admin') {
          userArea.innerHTML = `
            <a href="admin/index.html" class="btn btn-sm btn-outline-warning font-weight-bold d-inline-flex align-items-center" style="border-radius: 20px; font-size: 11px; white-space: nowrap; padding: 5px 12px; gap: 5px;" title="Buka Super Admin Control Center">
              <i class="fas fa-user-shield"></i> <span>ADMIN PANEL</span>
            </a>
            <a href="customer/index.html" class="btn btn-sm btn-outline-info font-weight-bold d-inline-flex align-items-center" style="border-radius: 20px; font-size: 11px; white-space: nowrap; padding: 5px 12px; gap: 5px; background: rgba(56, 189, 248, 0.1); border-color: rgba(56, 189, 248, 0.35); color: #38bdf8;" title="Buka Dashboard Customer">
              <i class="fas fa-columns"></i> <span>DASHBOARD</span>
            </a>
            <button class="btn btn-sm btn-danger d-inline-flex align-items-center justify-content-center" onclick="logoutUser(event)" style="border-radius: 50%; width: 32px; height: 32px; padding: 0; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #ef4444;" title="Logout Akun">
              <i class="fas fa-sign-out-alt" style="font-size: 13px;"></i>
            </button>
          `;
        } else {
          userArea.innerHTML = `
            <a href="customer/index.html" class="btn btn-sm btn-outline-info font-weight-bold d-inline-flex align-items-center" style="border-radius: 20px; font-size: 11px; white-space: nowrap; padding: 5px 12px; gap: 5px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.4); color: #38bdf8;" title="Buka Dashboard Customer">
              <i class="fas fa-columns"></i> <span>DASHBOARD</span>
            </a>
            <a href="customer/index.html" class="badge badge-info p-2 d-inline-flex align-items-center text-decoration-none" style="border-radius: 20px; font-size: 11px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); color: #38bdf8; white-space: nowrap; gap: 5px; cursor: pointer;" title="Buka Detail Kuota Customer">
              <i class="fas fa-layer-group"></i> <span>KUOTA: ${usedCount} / ${user.cctv_quota || 20} CCTV</span>
            </a>
            <button class="btn btn-sm btn-danger d-inline-flex align-items-center justify-content-center" onclick="logoutUser(event)" style="border-radius: 50%; width: 32px; height: 32px; padding: 0; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #ef4444;" title="Logout Akun">
              <i class="fas fa-sign-out-alt" style="font-size: 13px;"></i>
            </button>
          `;
        }
      } else {
        const navDashboardLink = document.getElementById('nav-item-customer-dashboard');
        if (navDashboardLink) navDashboardLink.style.display = 'none';

        userArea.innerHTML = `
          <button class="btn btn-sm" onclick="showLoginGateView()" style="background: linear-gradient(135deg, #00d2ff, #0066ff); border: none; font-weight: 700; border-radius: 25px; padding: 7px 20px; color: #fff; box-shadow: 0 4px 15px rgba(0, 102, 255, 0.4); text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; white-space: nowrap;">
            <i class="fas fa-sign-in-alt mr-1"></i> LOGIN
          </button>
        `;
      }
    }

    function openLoginModal() {
      showLoginGateView();
    }

    function submitLogin(e) {
      submitGateLogin(e);
    }

    function logoutUser(e) {
      if (e) {
        try { e.preventDefault(); e.stopPropagation(); } catch(err) {}
      }
      localStorage.removeItem('loewix_user');
      for (let i = localStorage.length - 1; i >= 0; i--) {
        const k = localStorage.key(i);
        if (k && k.startsWith('loewix_')) {
          localStorage.removeItem(k);
        }
      }
      try { sessionStorage.clear(); } catch(err) {}
      
      currentUser = null;
      authCheckComplete = true;
      mediamtxData.length = 0;
      
      showLoginGateView();

      fetch('api/auth.php?action=logout')
        .catch(() => {})
        .finally(() => {
          window.location.reload();
        });
    }

    function togglePasswordVisibility() {
      toggleGatePasswordVisibility();
    }

    function showQuotaAlert(msg) {
      document.getElementById('quota-alert-msg').innerText = msg;
      $('#modalQuotaExceeded').modal('show');
    }

    function selectDigitalKeycard(email, pwd, type) {
      const emailInput = document.getElementById('gate-login-email');
      const pwdInput = document.getElementById('gate-login-password');
      if (emailInput) emailInput.value = email;
      if (pwdInput) pwdInput.value = pwd;

      const cardAdmin = document.getElementById('keycard-admin');
      const cardCustomer = document.getElementById('keycard-customer');
      const indicatorText = document.getElementById('keycard-indicator-text');

      if (type === 'admin') {
        if (cardAdmin) cardAdmin.classList.add('active');
        if (cardCustomer) cardCustomer.classList.remove('active');
        if (indicatorText) {
          indicatorText.style.color = '#fbbf24';
          indicatorText.innerHTML = '<i class="fas fa-shield-alt mr-1"></i> KARTU AKTIF: SUPER ADMIN (MASTER ACCESS)';
        }
      } else {
        if (cardCustomer) cardCustomer.classList.add('active');
        if (cardAdmin) cardAdmin.classList.remove('active');
        if (indicatorText) {
          indicatorText.style.color = '#38bdf8';
          indicatorText.innerHTML = '<i class="fas fa-building mr-1"></i> KARTU AKTIF: PT. JAYA SENTOSA (CUSTOMER ACCESS)';
        }
      }
    }

    function switchLoginRole(email, pwd, tabElem) {
      if (email.includes('admin')) {
        selectDigitalKeycard(email, pwd, 'admin');
      } else {
        selectDigitalKeycard(email, pwd, 'customer');
      }
    }

    // ===== 3D INTERACTIVE PARTICLE MESH & GYROSCOPIC PARALLAX ENGINE =====
    (function init3DExperience() {
      // 1. Interactive 3D Three.js Surveillance Mesh
      const canvas = document.getElementById('gate-3d-canvas');
      if (canvas && typeof THREE !== 'undefined') {
        try {
          const scene = new THREE.Scene();
          const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
          camera.position.z = 120;

          const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
          renderer.setSize(window.innerWidth, window.innerHeight);
          renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

          // Cyber Node Sphere Particles
          const geometry = new THREE.BufferGeometry();
          const particleCount = 280;
          const posArray = new Float32Array(particleCount * 3);

          for (let i = 0; i < particleCount * 3; i += 3) {
            const radius = 65 + Math.random() * 20;
            const theta = Math.random() * Math.PI * 2;
            const phi = Math.acos((Math.random() * 2) - 1);

            posArray[i] = radius * Math.sin(phi) * Math.cos(theta);
            posArray[i + 1] = radius * Math.sin(phi) * Math.sin(theta);
            posArray[i + 2] = radius * Math.cos(phi);
          }

          geometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));

          const material = new THREE.PointsMaterial({
            size: 2.2,
            color: 0x38bdf8,
            transparent: true,
            opacity: 0.85,
            blending: THREE.AdditiveBlending
          });

          const particleMesh = new THREE.Points(geometry, material);
          scene.add(particleMesh);

          // Wireframe Inner Core
          const coreGeo = new THREE.IcosahedronGeometry(45, 1);
          const coreMat = new THREE.MeshBasicMaterial({
            color: 0x0284c7,
            wireframe: true,
            transparent: true,
            opacity: 0.18
          });
          const coreMesh = new THREE.Mesh(coreGeo, coreMat);
          scene.add(coreMesh);

          let mouseX = 0;
          let mouseY = 0;

          window.addEventListener('mousemove', (e) => {
            mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
            mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
          });

          function animate3D() {
            requestAnimationFrame(animate3D);
            particleMesh.rotation.y += 0.0025;
            particleMesh.rotation.x += 0.001;
            coreMesh.rotation.y -= 0.003;
            coreMesh.rotation.z += 0.0015;

            // Parallax tracking
            camera.position.x += (mouseX * 15 - camera.position.x) * 0.05;
            camera.position.y += (-mouseY * 15 - camera.position.y) * 0.05;
            camera.lookAt(scene.position);

            renderer.render(scene, camera);
          }
          animate3D();

          window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
          });
        } catch(e) {
          console.warn('Three.js 3D Mesh Init fallback:', e);
        }
      }

      // 2. Interactive 3D Gyroscopic Mouse Parallax on Hero 3D Card
      const heroBox = document.getElementById('stage3dHeroBox');
      const cctvImg = document.getElementById('cctv3dModelImg');
      
      if (heroBox && cctvImg) {
        heroBox.addEventListener('mousemove', (e) => {
          const rect = heroBox.getBoundingClientRect();
          const x = e.clientX - rect.left - rect.width / 2;
          const y = e.clientY - rect.top - rect.height / 2;

          const rotateX = (-y / (rect.height / 2)) * 12;
          const rotateY = (x / (rect.width / 2)) * 14;

          heroBox.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
          cctvImg.style.transform = `translateZ(24px) rotateX(${-rotateX * 0.6}deg) rotateY(${-rotateY * 0.6}deg) scale(1.06)`;
        });

        heroBox.addEventListener('mouseleave', () => {
          heroBox.style.transform = `perspective(800px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
          cctvImg.style.transform = `translateZ(0px) rotateX(0deg) rotateY(0deg) scale(1)`;
        });
      }

      // 3. 3D Tilt for Bento Cards
      const tiltCards = document.querySelectorAll('.stage-feature-card');
      tiltCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
          const rect = card.getBoundingClientRect();
          const x = e.clientX - rect.left - rect.width / 2;
          const y = e.clientY - rect.top - rect.height / 2;
          const rotateX = (-y / (rect.height / 2)) * 10;
          const rotateY = (x / (rect.width / 2)) * 10;
          card.style.transform = `perspective(600px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(12px) scale(1.03)`;
        });
        card.addEventListener('mouseleave', () => {
          card.style.transform = `perspective(600px) rotateX(0deg) rotateY(0deg) translateZ(0px) scale(1)`;
        });
      });
    })();

    window.openLoginModal = openLoginModal;
    window.submitLogin = submitLogin;
    window.submitGateLogin = submitGateLogin;
    window.fillGateDemo = fillGateDemo;
    window.selectDigitalKeycard = selectDigitalKeycard;
    window.switchLoginRole = switchLoginRole;
    window.toggleGatePasswordVisibility = toggleGatePasswordVisibility;
    window.showLoginGateView = showLoginGateView;
    window.showDashboardView = showDashboardView;
    window.logoutUser = logoutUser;
    window.togglePasswordVisibility = togglePasswordVisibility;