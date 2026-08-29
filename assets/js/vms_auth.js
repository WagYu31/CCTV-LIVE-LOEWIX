/**
 * Loewix Surveillance VMS - Authentication & Session Controller (Login & Registration)
 * PT. LOEWIX INDONESIA
 */

function switchGateAuthMode(mode) {
  const loginForm = document.getElementById('formGateLogin');
  const registerForm = document.getElementById('formGateRegister');
  const forgotForm = document.getElementById('formGateForgot');
  const resetForm = document.getElementById('formGateReset');
  const tabLogin = document.getElementById('tab-btn-login');
  const tabRegister = document.getElementById('tab-btn-register');
  const headerTag = document.getElementById('gate-auth-tag-text');
  const headerTitle = document.getElementById('gate-auth-title-text');
  const headerSub = document.getElementById('gate-auth-sub-text');

  // Hide all
  if (loginForm) loginForm.style.display = 'none';
  if (registerForm) registerForm.style.display = 'none';
  if (forgotForm) forgotForm.style.display = 'none';
  if (resetForm) resetForm.style.display = 'none';

  if (mode === 'register') {
    if (registerForm) registerForm.style.display = 'block';
    if (tabLogin) tabLogin.classList.remove('active');
    if (tabRegister) tabRegister.classList.add('active');

    if (headerTag) headerTag.textContent = 'REGISTRASI AKUN';
    if (headerTitle) headerTitle.textContent = 'Daftar Akun Baru';
    if (headerSub) headerSub.textContent = 'Lengkapi formulir di bawah untuk mendaftarkan akun portal pemantauan CCTV Loewix.';
  } else if (mode === 'forgot') {
    if (forgotForm) forgotForm.style.display = 'block';
    if (tabLogin) tabLogin.classList.remove('active');
    if (tabRegister) tabRegister.classList.remove('active');

    if (headerTag) headerTag.textContent = 'PEMULIHAN AKUN';
    if (headerTitle) headerTitle.textContent = 'Lupa Kata Sandi';
    if (headerSub) headerSub.textContent = 'Masukkan email akun Anda. Kami akan mengirimkan kode OTP & tautan reset kata sandi ke email Anda.';
  } else if (mode === 'reset') {
    if (resetForm) resetForm.style.display = 'block';
    if (tabLogin) tabLogin.classList.remove('active');
    if (tabRegister) tabRegister.classList.remove('active');

    if (headerTag) headerTag.textContent = 'RESET PASSWORD';
    if (headerTitle) headerTitle.textContent = 'Buat Kata Sandi Baru';
    if (headerSub) headerSub.textContent = 'Masukkan kode OTP yang Anda terima di email beserta kata sandi baru Anda.';
  } else {
    // Default login
    if (loginForm) loginForm.style.display = 'block';
    if (tabLogin) tabLogin.classList.add('active');
    if (tabRegister) tabRegister.classList.remove('active');

    if (headerTag) headerTag.textContent = 'SELAMAT DATANG';
    if (headerTitle) headerTitle.textContent = 'Masuk ke Akun Anda';
    if (headerSub) headerSub.textContent = 'Masukkan email dan kata sandi akun Anda untuk membuka portal sistem pemantauan.';
  }
}

async function submitGateForgot(e) {
  e.preventDefault();
  const email = document.getElementById('gate-forgot-email').value.trim();
  const btn = document.getElementById('btn-gate-forgot-submit');

  if (!email) {
    alert('Silakan masukkan email akun Anda.');
    return;
  }

  const origHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengirim OTP...';

  try {
    const formData = new FormData();
    formData.append('action', 'forgot_password');
    formData.append('email', email);

    const res = await fetch('api/auth.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.success) {
      alert(data.message || 'Kode OTP telah dikirim ke email Anda!');
      document.getElementById('gate-reset-email').value = email;
      if (data.otp_simulation) {
        document.getElementById('gate-reset-otp').value = data.otp_simulation;
      }
      switchGateAuthMode('reset');
    } else {
      alert(data.message || 'Gagal memproses permintaan lupa password.');
    }
  } catch (err) {
    console.error(err);
    alert('Terjadi kesalahan koneksi ke server.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = origHtml;
  }
}

async function submitGateReset(e) {
  e.preventDefault();
  const email = document.getElementById('gate-reset-email').value.trim();
  const otp = document.getElementById('gate-reset-otp').value.trim();
  const newPassword = document.getElementById('gate-reset-password').value;
  const confirmPassword = document.getElementById('gate-reset-confirm-password').value;
  const btn = document.getElementById('btn-gate-reset-submit');

  if (!email || !otp || !newPassword) {
    alert('Semua field wajib diisi!');
    return;
  }

  if (newPassword !== confirmPassword) {
    alert('Konfirmasi kata sandi tidak cocok!');
    return;
  }

  if (newPassword.length < 6) {
    alert('Kata sandi minimal 6 karakter!');
    return;
  }

  const origHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memperbarui...';

  try {
    const formData = new FormData();
    formData.append('action', 'reset_password');
    formData.append('email', email);
    formData.append('otp', otp);
    formData.append('new_password', newPassword);

    const res = await fetch('api/auth.php', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.success) {
      alert(data.message || 'Kata sandi berhasil diperbarui! Silakan login.');
      document.getElementById('gate-login-email').value = email;
      switchGateAuthMode('login');
    } else {
      alert(data.message || 'Gagal mereset kata sandi.');
    }
  } catch (err) {
    console.error(err);
    alert('Terjadi kesalahan koneksi ke server.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = origHtml;
  }
}

let selectedRegCycle = 'annual'; // 'monthly' or 'annual'
let selectedRegPlan = 'business_10'; // 'starter_4', 'business_10', 'enterprise_20'

const REG_PLAN_PRICES = {
  starter_4: { name: 'Starter Cloud', quota: 4, monthly: 149000, annual: 1490000 },
  business_10: { name: 'Business Pro', quota: 10, monthly: 299000, annual: 2990000 },
  enterprise_20: { name: 'Enterprise Fleet', quota: 20, monthly: 549000, annual: 5490000 }
};

function selectRegistrationCycle(cycle) {
  selectedRegCycle = cycle;
  const btnMonthly = document.getElementById('reg-cycle-monthly');
  const btnAnnual = document.getElementById('reg-cycle-annual');
  
  if (cycle === 'annual') {
    if (btnAnnual) btnAnnual.classList.add('active');
    if (btnMonthly) btnMonthly.classList.remove('active');
  } else {
    if (btnMonthly) btnMonthly.classList.add('active');
    if (btnAnnual) btnAnnual.classList.remove('active');
  }
  
  updateRegPlanDisplay();
}

function selectRegistrationPlan(planId) {
  selectedRegPlan = planId;
  document.querySelectorAll('.reg-plan-card').forEach(card => {
    if (card.dataset.plan === planId) {
      card.classList.add('active');
    } else {
      card.classList.remove('active');
    }
  });
  updateRegPlanDisplay();
}

function updateRegPlanDisplay() {
  const planInfo = REG_PLAN_PRICES[selectedRegPlan] || REG_PLAN_PRICES.business_10;
  const basePrice = (selectedRegCycle === 'annual') ? planInfo.annual : planInfo.monthly;
  const tax = Math.round(basePrice * 0.11);
  const total = basePrice + tax;

  const priceTagStarter = document.getElementById('price-tag-starter');
  const priceTagBusiness = document.getElementById('price-tag-business');
  const priceTagEnterprise = document.getElementById('price-tag-enterprise');
  
  if (priceTagStarter) {
    priceTagStarter.textContent = selectedRegCycle === 'annual' ? 'Rp 1.490.000/thn' : 'Rp 149.000/bln';
  }
  if (priceTagBusiness) {
    priceTagBusiness.textContent = selectedRegCycle === 'annual' ? 'Rp 2.990.000/thn' : 'Rp 299.000/bln';
  }
  if (priceTagEnterprise) {
    priceTagEnterprise.textContent = selectedRegCycle === 'annual' ? 'Rp 5.490.000/thn' : 'Rp 549.000/bln';
  }

  const summaryText = document.getElementById('reg-summary-text');
  const summaryTotal = document.getElementById('reg-summary-total');
  
  if (summaryText) {
    summaryText.innerHTML = `<strong>${planInfo.name}</strong> (${planInfo.quota} CCTV) &bull; Periode ${selectedRegCycle === 'annual' ? '1 Tahun (Hemat 2 Bln)' : '1 Bulan'}`;
  }
  if (summaryTotal) {
    summaryTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
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
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyiapkan Pembayaran Midtrans...';
  }

  const formData = new FormData();
  formData.append('action', 'register');
  formData.append('name', name);
  formData.append('email', email);
  formData.append('phone', phone);
  formData.append('city', city);
  formData.append('password', password);

  try {
    // 1. Register User Account
    const res = await fetch('api/auth.php', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();

    if (data.success && data.user) {
      currentUser = data.user;
      localStorage.setItem('loewix_user', JSON.stringify(data.user));

      const activePlan = window.selectedRegPlan || (typeof selectedRegPlan !== 'undefined' ? selectedRegPlan : 'business_10');
      const activeCycle = window.selectedRegCycle || (typeof selectedRegCycle !== 'undefined' ? selectedRegCycle : 'annual');

      const payData = new FormData();
      payData.append('action', 'create_snap_token');
      payData.append('plan_id', activePlan);
      payData.append('billing_cycle', activeCycle);
      payData.append('name', name);
      payData.append('email', email);
      payData.append('phone', phone);

      const payRes = await fetch('api/payment.php', {
        method: 'POST',
        body: payData
      });
      const payResult = await payRes.json();

      if (payResult.success && payResult.snap_token) {
        // Genuine Midtrans Snap tokens are UUID strings (36 characters). If not, fallback to Loewix Checkout Modal
        const isUuidToken = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(payResult.snap_token);
        const isSimulation = payResult.is_simulation === true || payResult.snap_token.startsWith('SNAP_LOEWIX_') || !isUuidToken;

        if (!isSimulation && window.snap && typeof window.snap.pay === 'function') {
          window.snap.pay(payResult.snap_token, {
            onSuccess: function(result) {
              completeRegistrationPayment(payResult.order_id, result.payment_type || 'midtrans');
            },
            onPending: function(result) {
              completeRegistrationPayment(payResult.order_id, result.payment_type || 'midtrans_pending');
            },
            onError: function(result) {
              alert('Pembayaran gagal atau dibatalkan. Anda dapat melanjutkan pembayaran di menu Tagihan.');
              showDashboardView(data.user);
            },
            onClose: function() {
              if (confirm('Anda menutup popup pembayaran. Lanjut ke dashboard dan selesaikan pembayaran di menu Tagihan?')) {
                showDashboardView(data.user);
              }
            }
          });
        } else {
          // Launch Loewix Simulation Checkout Modal (Interactive QRIS & VA Simulator)
          showLoewixCheckoutModal(payResult, data.user);
        }
      } else {
        showDashboardView(data.user);
      }
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
      showCCTVToast('Gagal terhubung ke server pembayaran.', 'danger');
    } else {
      alert('Gagal terhubung ke server pembayaran.');
    }
  } finally {
    if (btnSubmit) {
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = '<span>DAFTAR & BAYAR SEKARANG</span> <i class="fas fa-arrow-right"></i>';
    }
  }
}

function showLoewixCheckoutModal(payResult, user) {
  let modal = document.getElementById('modalLoewixCheckoutSim');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'modalLoewixCheckoutSim';
    document.body.appendChild(modal);
  }
  
  modal.style.cssText = `
    position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important;
    background: rgba(4, 9, 24, 0.88) !important; backdrop-filter: blur(12px) !important;
    z-index: 2147483647 !important; display: flex !important; align-items: center !important; justify-content: center !important; padding: 20px !important;
  `;

  modal.innerHTML = `
    <div style="background: #0c1630; border: 1.5px solid #38bdf8; border-radius: 16px; max-width: 480px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.8); overflow: hidden; font-family: 'Space Grotesk', sans-serif;">
      <div style="background: linear-gradient(135deg, #091538, #0c1942); padding: 18px 24px; border-bottom: 1px solid rgba(56, 189, 248, 0.3); display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fas fa-shield-alt" style="color: #38bdf8; font-size: 18px;"></i>
          <span style="color: #ffffff; font-weight: 800; font-size: 15px; letter-spacing: 0.5px;">MIDTRANS PAYMENT GATEWAY</span>
        </div>
        <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #34d399; font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 4px;">SANDBOX SIMULATOR</span>
      </div>

      <div style="padding: 24px;">
        <div style="text-align: center; margin-bottom: 20px;">
          <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Total Tagihan (Inc. PPN 11%)</div>
          <div style="font-size: 26px; font-weight: 800; color: #34d399; margin: 4px 0;">${payResult.plan ? payResult.plan.total_formatted : 'Rp ' + Number(payResult.gross_amount).toLocaleString('id-ID')}</div>
          <div style="font-size: 11px; color: #64748b; font-family: monospace;">ORDER ID: ${payResult.order_id}</div>
        </div>

        <div style="background: rgba(6, 11, 24, 0.8); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 14px; margin-bottom: 20px;">
          <div style="font-size: 11px; font-weight: 700; color: #38bdf8; margin-bottom: 10px; text-transform: uppercase;">Pilih Metode Pembayaran:</div>
          
          <label style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(255,255,255,0.04); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 8px; margin-bottom: 8px; cursor: pointer;">
            <input type="radio" name="sim_payment_method" value="qris" checked style="accent-color: #38bdf8;">
            <i class="fas fa-qrcode" style="color: #34d399; font-size: 16px;"></i>
            <div style="flex: 1;">
              <div style="color: #ffffff; font-weight: 700; font-size: 12.5px;">QRIS (Instant Settlement)</div>
              <div style="color: #94a3b8; font-size: 10px;">GoPay, OVO, Dana, ShopeePay, BCA QR</div>
            </div>
          </label>

          <label style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; margin-bottom: 8px; cursor: pointer;">
            <input type="radio" name="sim_payment_method" value="bank_transfer_bca" style="accent-color: #38bdf8;">
            <i class="fas fa-university" style="color: #38bdf8; font-size: 16px;"></i>
            <div style="flex: 1;">
              <div style="color: #ffffff; font-weight: 700; font-size: 12.5px;">BCA Virtual Account</div>
              <div style="color: #94a3b8; font-size: 10px;">Verifikasi Otomatis 24 Jam</div>
            </div>
          </label>

          <label style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; cursor: pointer;">
            <input type="radio" name="sim_payment_method" value="credit_card" style="accent-color: #38bdf8;">
            <i class="fas fa-credit-card" style="color: #f59e0b; font-size: 16px;"></i>
            <div style="flex: 1;">
              <div style="color: #ffffff; font-weight: 700; font-size: 12.5px;">Kartu Kredit / Debit Online</div>
              <div style="color: #94a3b8; font-size: 10px;">Visa, Mastercard, JCB (3D Secure)</div>
            </div>
          </label>
        </div>

        <button type="button" id="btn-confirm-sim-payment" onclick="processLoewixSimulatedPayment('${payResult.order_id}')" style="width: 100%; padding: 13px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 8px; font-weight: 800; font-size: 13.5px; letter-spacing: 0.5px; cursor: pointer; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4); display: flex; align-items: center; justify-content: center; gap: 8px;">
          <i class="fas fa-check-circle"></i>
          <span>BAYAR SEKARANG (SIMULASI SUKSES)</span>
        </button>

        <div style="text-align: center; margin-top: 12px;">
          <a href="javascript:void(0)" onclick="closeLoewixSimModal()" style="font-size: 11.5px; color: #94a3b8; text-decoration: none;">Bayar Nanti di Dashboard</a>
        </div>
      </div>
    </div>
  `;
}

function closeLoewixSimModal() {
  const modal = document.getElementById('modalLoewixCheckoutSim');
  if (modal) modal.remove();
  window.location.href = 'customer/index.php';
}

async function processLoewixSimulatedPayment(orderId) {
  const btn = document.getElementById('btn-confirm-sim-payment');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengonfirmasi Pembayaran...';
  }

  const selectedMethod = document.querySelector('input[name="sim_payment_method"]:checked')?.value || 'qris';
  await completeRegistrationPayment(orderId, selectedMethod);
}

async function completeRegistrationPayment(orderId, paymentType) {
  try {
    const fd = new FormData();
    fd.append('action', 'verify_payment');
    fd.append('order_id', orderId);
    fd.append('payment_type', paymentType);

    const res = await fetch('api/payment.php', { method: 'POST', body: fd });
    const resData = await res.json();

    if (resData.success) {
      if (typeof showCCTVToast === 'function') {
        showCCTVToast('Pembayaran Berhasil! Paket CCTV Anda telah aktif.', 'success');
      } else {
        alert('Pembayaran Berhasil! Paket CCTV Anda telah aktif.');
      }
    }
  } catch(e) {
    console.error('Error verifying payment:', e);
  }
  
  const modal = document.getElementById('modalLoewixCheckoutSim');
  if (modal) modal.remove();
  window.location.href = 'customer/index.php';
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
            window.location.href = 'customer/index.php';
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
          window.location.href = 'customer/index.php';
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
            <a href="customer/index.php" class="btn btn-sm btn-outline-warning font-weight-bold d-inline-flex align-items-center" style="border-radius: 20px; font-size: 11px; white-space: nowrap; padding: 5px 12px; gap: 5px; background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;" title="Buka Super Admin Master Control Center">
              <i class="fas fa-crown"></i> <span>MASTER COMMAND CENTER</span>
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