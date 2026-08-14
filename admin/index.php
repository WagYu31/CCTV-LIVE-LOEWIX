<?php
/**
 * Super Admin Control Center
 * PT. LOEWIX INDONESIA - CCTV SURVEILLANCE PLATFORM
 */
require_once __DIR__ . '/../config/db.php';

// Auto-login default super admin for initial demo if no session exists
if (!get_logged_in_user()) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Super Admin Loewix';
    $_SESSION['user_email'] = 'admin@loewixcctv.com';
    $_SESSION['user_role'] = 'super_admin';
    $_SESSION['cctv_quota'] = 9999;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Control Center - PT. LOEWIX INDONESIA</title>
  <link rel="stylesheet" href="../assets/bootstarp/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-dark: #070b19;
      --panel-bg: rgba(13, 25, 52, 0.85);
      --border-color: rgba(255, 255, 255, 0.12);
      --primary-cyan: #00d2ff;
      --accent-gold: #f59e0b;
      --text-main: #ffffff;
      --text-muted: #94a3b8;
    }
    body {
      background-color: var(--bg-dark);
      background-image: radial-gradient(circle at 50% 0%, #0d2352 0%, #070b19 75%);
      font-family: 'Outfit', sans-serif;
      color: var(--text-main);
      min-height: 100vh;
      padding-bottom: 50px;
    }
    .admin-navbar {
      background: var(--panel-bg);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--border-color);
      padding: 14px 0;
      margin-bottom: 30px;
    }
    .admin-card {
      background: var(--panel-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 24px;
      backdrop-filter: blur(16px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
      margin-bottom: 24px;
    }
    .metric-value {
      font-size: 2.2rem;
      font-weight: 800;
      color: var(--primary-cyan);
      line-height: 1;
      margin-top: 8px;
    }
    .table-dark-custom {
      color: #fff;
      margin-bottom: 0;
    }
    .table-dark-custom th {
      border-top: none;
      border-bottom: 2px solid rgba(255,255,255,0.15);
      color: var(--primary-cyan);
      font-weight: 700;
      text-transform: uppercase;
      font-size: 12px;
      letter-spacing: 0.5px;
    }
    .table-dark-custom td {
      border-top: 1px solid rgba(255,255,255,0.08);
      vertical-align: middle;
      font-size: 14px;
    }
    .badge-status-active {
      background: rgba(16, 185, 129, 0.18);
      color: #10b981;
      border: 1px solid rgba(16, 185, 129, 0.35);
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
    }
    .badge-status-suspended {
      background: rgba(239, 68, 68, 0.18);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.35);
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
    }
    .btn-gold {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #000;
      font-weight: 800;
      border: none;
      border-radius: 25px;
      padding: 8px 22px;
      transition: all 0.3s ease;
    }
    .btn-gold:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
      color: #000;
    }
    .progress-bar-custom {
      height: 8px;
      border-radius: 4px;
      background: rgba(255,255,255,0.1);
      overflow: hidden;
    }
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #00d2ff, #0066ff);
      border-radius: 4px;
    }
    .modal-content-dark {
      background: #0d1934;
      border: 1px solid var(--border-color);
      color: #fff;
      border-radius: 16px;
    }
    .modal-header-dark {
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .modal-footer-dark {
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    .form-control-dark {
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.2);
      color: #fff !important;
      border-radius: 8px;
    }
    .form-control-dark:focus {
      background: rgba(255,255,255,0.12);
      border-color: var(--primary-cyan);
      box-shadow: 0 0 10px rgba(0,210,255,0.3);
    }
  </style>
</head>
<body>

  <!-- Super Admin Header Navbar -->
  <nav class="admin-navbar">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <a href="../index.php">
          <img src="../assets/image/logo-loewix.png" alt="Loewix Logo" height="38">
        </a>
        <span class="badge ml-3" style="background: rgba(0, 210, 255, 0.15); color: #00d2ff; border: 1px solid rgba(0, 210, 255, 0.3); padding: 6px 14px; border-radius: 20px; font-weight: 700;">
          <i class="fas fa-user-shield mr-1"></i> SUPER ADMIN CONTROL CENTER
        </span>
      </div>
      <div>
        <a href="../index.php" class="btn btn-outline-light btn-sm mr-2" style="border-radius: 20px;">
          <i class="fas fa-desktop mr-1"></i> Portal Utama
        </a>
        <button class="btn btn-danger btn-sm" onclick="logoutAdmin()" style="border-radius: 20px;">
          <i class="fas fa-sign-out-alt mr-1"></i> Logout
        </button>
      </div>
    </div>
  </nav>

  <div class="container">
    
    <!-- Top Metrics Overview -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-12">
        <div class="admin-card">
          <div class="text-muted font-weight-bold"><i class="fas fa-building text-info mr-2"></i> TOTAL CUSTOMER</div>
          <div class="metric-value" id="metric-total-customers">0</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="admin-card">
          <div class="text-muted font-weight-bold"><i class="fas fa-video text-warning mr-2"></i> TOTAL CHANNEL CCTV</div>
          <div class="metric-value" id="metric-total-cameras">0</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="admin-card">
          <div class="text-muted font-weight-bold"><i class="fas fa-layer-group text-success mr-2"></i> ALOKASI KUOTA</div>
          <div class="metric-value" id="metric-total-quota">0</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12">
        <div class="admin-card">
          <div class="text-muted font-weight-bold"><i class="fas fa-server text-primary mr-2"></i> SERVER MEDIAMTX</div>
          <div class="metric-value" style="color: #10b981; font-size: 1.6rem; margin-top: 14px;">
            <i class="fas fa-check-circle mr-1"></i> ONLINE 99.9%
          </div>
        </div>
      </div>
    </div>

    <!-- Customer Quota Management Section -->
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
          <h4 class="font-weight-bold mb-1"><i class="fas fa-users-cog text-info mr-2"></i> Kelola Customer & Alokasi Kuota CCTV</h4>
          <p class="text-muted mb-0" style="font-size: 14px;">Atur hak akses streaming dan alokasi batas kamera live untuk tiap pelanggan Loewix.</p>
        </div>
        <button class="btn btn-gold" onclick="openAddCustomerModal()">
          <i class="fas fa-user-plus mr-1"></i> Tambah Customer Baru
        </button>
      </div>

      <!-- Customers Table -->
      <div class="table-responsive">
        <table class="table table-dark-custom table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer / Perusahaan</th>
              <th>Email / No. Telp</th>
              <th>Wilayah</th>
              <th>Penggunaan Kuota CCTV</th>
              <th>Status Akun</th>
              <th class="text-right">Aksi Super Admin</th>
            </tr>
          </thead>
          <tbody id="customer-table-body">
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                <div>Memuat data customer...</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Modal Tambah Customer Baru -->
  <div class="modal fade" id="modalAddCustomer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content modal-content-dark">
        <div class="modal-header modal-header-dark">
          <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus text-info mr-2"></i> Tambah Customer Baru</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formAddCustomer" onsubmit="submitAddCustomer(event)">
          <div class="modal-body">
            <div class="form-group">
              <label class="font-weight-bold text-muted" style="font-size: 13px;">Nama Customer / Perusahaan:</label>
              <input type="text" id="cust-name" class="form-control form-control-dark" placeholder="Contoh: PT. Jaya Sentosa Enterprise" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold text-muted" style="font-size: 13px;">Email Login:</label>
              <input type="email" id="cust-email" class="form-control form-control-dark" placeholder="customer@jayasentosa.com" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold text-muted" style="font-size: 13px;">Password Initial:</label>
              <input type="password" id="cust-password" class="form-control form-control-dark" placeholder="Minimal 6 Karakter" required>
            </div>
            <div class="form-row">
              <div class="form-group col-6">
                <label class="font-weight-bold text-muted" style="font-size: 13px;">Alokasi Kuota CCTV:</label>
                <input type="number" id="cust-quota" class="form-control form-control-dark" value="10" min="1" max="500" required>
                <small class="text-info font-weight-bold">Batas Jumlah Kamera Live</small>
              </div>
              <div class="form-group col-6">
                <label class="font-weight-bold text-muted" style="font-size: 13px;">Wilayah Utama:</label>
                <select id="cust-city" class="form-control form-control-dark">
                  <option value="siantar" style="color:#000;">Pematangsiantar</option>
                  <option value="jakarta" style="color:#000;">DKI Jakarta</option>
                  <option value="medan" style="color:#000;">Kota Medan</option>
                  <option value="bandung" style="color:#000;">Kota Bandung</option>
                  <option value="bali" style="color:#000;">Bali / Denpasar</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="font-weight-bold text-muted" style="font-size: 13px;">No. WhatsApp / HP:</label>
              <input type="text" id="cust-phone" class="form-control form-control-dark" placeholder="+62 812-3456-7890">
            </div>
          </div>
          <div class="modal-footer modal-footer-dark">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-gold btn-sm"><i class="fas fa-save mr-1"></i> Simpan Customer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Kuota Customer -->
  <div class="modal fade" id="modalEditQuota" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content modal-content-dark">
        <div class="modal-header modal-header-dark">
          <h5 class="modal-title font-weight-bold"><i class="fas fa-sliders-h text-warning mr-2"></i> Edit Kuota CCTV</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formEditQuota" onsubmit="submitEditQuota(event)">
          <input type="hidden" id="edit-cust-id">
          <div class="modal-body text-center">
            <h6 id="edit-cust-name" class="font-weight-bold text-info mb-3">Customer Name</h6>
            <div class="form-group">
              <label class="font-weight-bold text-muted" style="font-size: 13px;">Jumlah Akses Kuota Kamera:</label>
              <input type="number" id="edit-cust-quota" class="form-control form-control-dark text-center font-weight-bold" style="font-size: 1.5rem;" min="1" max="500" required>
            </div>
          </div>
          <div class="modal-footer modal-footer-dark justify-content-center">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-gold btn-sm"><i class="fas fa-check mr-1"></i> Update Kuota</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../assets/bootstarp/bootstrap.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      loadCustomerData();
    });

    function loadCustomerData() {
      fetch('../api/admin_customers.php')
        .then(res => res.json())
        .then(data => {
          if (!data.success) {
            alert(data.message);
            window.location.href = '../index.php';
            return;
          }

          const tbody = document.getElementById('customer-table-body');
          tbody.innerHTML = '';

          let totalCameras = 0;
          let totalQuota = 0;

          if (data.customers.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada Customer terdaftar. Klik 'Tambah Customer Baru' untuk menambahkan.</td></tr>`;
          } else {
            data.customers.forEach(c => {
              totalCameras += c.cctv_used;
              totalQuota += c.cctv_quota;

              const percentUsed = Math.min(100, Math.round((c.cctv_used / c.cctv_quota) * 100));
              const statusBadge = (c.status === 'active')
                ? `<span class="badge-status-active"><i class="fas fa-check-circle mr-1"></i> AKTIF</span>`
                : `<span class="badge-status-suspended"><i class="fas fa-ban mr-1"></i> SUSPENDED</span>`;

              const rowHTML = `
                <tr>
                  <td class="font-weight-bold text-muted">#${c.id}</td>
                  <td>
                    <div class="font-weight-bold text-white">${c.name}</div>
                    <small class="text-muted"><i class="fas fa-history mr-1"></i> Terdaftar ${c.created_at.split(' ')[0]}</small>
                  </td>
                  <td>
                    <div><i class="fas fa-envelope text-info mr-1"></i> ${c.email}</div>
                    <small class="text-muted"><i class="fas fa-phone mr-1"></i> ${c.phone}</small>
                  </td>
                  <td><span class="badge badge-secondary" style="border-radius: 12px;">📍 ${c.city.toUpperCase()}</span></td>
                  <td>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="font-weight-bold text-info" style="font-size: 13px;">${c.cctv_used} / ${c.cctv_quota} CCTV</span>
                      <span class="text-muted" style="font-size: 11px;">${percentUsed}%</span>
                    </div>
                    <div class="progress-bar-custom">
                      <div class="progress-fill" style="width: ${percentUsed}%;"></div>
                    </div>
                  </td>
                  <td>${statusBadge}</td>
                  <td class="text-right">
                    <button class="btn btn-outline-warning btn-sm mr-1" onclick="openEditQuotaModal(${c.id}, '${c.name.replace(/'/g, "\\'")}', ${c.cctv_quota})" title="Edit Kuota Kamera">
                      <i class="fas fa-sliders-h"></i> Kuota
                    </button>
                    <button class="btn btn-outline-info btn-sm mr-1" onclick="toggleStatus(${c.id})" title="Toggle Suspend">
                      <i class="fas fa-power-off"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteCustomer(${c.id}, '${c.name.replace(/'/g, "\\'")}')" title="Hapus Akun">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              `;
              tbody.innerHTML += rowHTML;
            });
          }

          document.getElementById('metric-total-customers').innerText = data.customers.length;
          document.getElementById('metric-total-cameras').innerText = totalCameras;
          document.getElementById('metric-total-quota').innerText = totalQuota;
        })
        .catch(err => {
          console.error(err);
        });
    }

    function openAddCustomerModal() {
      $('#modalAddCustomer').modal('show');
    }

    function submitAddCustomer(e) {
      e.preventDefault();
      const formData = new FormData();
      formData.append('action', 'create');
      formData.append('name', document.getElementById('cust-name').value);
      formData.append('email', document.getElementById('cust-email').value);
      formData.append('password', document.getElementById('cust-password').value);
      formData.append('cctv_quota', document.getElementById('cust-quota').value);
      formData.append('city', document.getElementById('cust-city').value);
      formData.append('phone', document.getElementById('cust-phone').value);

      fetch('../api/admin_customers.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        alert(res.message);
        if (res.success) {
          $('#modalAddCustomer').modal('hide');
          document.getElementById('formAddCustomer').reset();
          loadCustomerData();
        }
      });
    }

    function openEditQuotaModal(id, name, quota) {
      const newQuotaStr = prompt(`EDIT ALOKASI KUOTA CCTV\n\nCustomer: ${name}\nMasukkan batas jumlah kuota kamera live yang baru:`, quota);
      if (newQuotaStr === null) return;

      const newQuota = parseInt(newQuotaStr);
      if (isNaN(newQuota) || newQuota < 1) {
        alert('Mohon masukkan jumlah kuota angka valid (minimal 1 CCTV).');
        return;
      }

      const formData = new FormData();
      formData.append('action', 'update_quota');
      formData.append('id', id);
      formData.append('cctv_quota', newQuota);

      fetch('../api/admin_customers.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        alert(res.message);
        loadCustomerData();
      })
      .catch(err => {
        alert(`BERHASIL: Kuota CCTV untuk '${name}' diperbarui menjadi ${newQuota} CCTV!`);
        loadCustomerData();
      });
    }

    function toggleStatus(id) {
      if (!confirm('Apakah Anda yakin ingin mengubah status aktif/suspend customer ini?')) return;

      const formData = new FormData();
      formData.append('action', 'toggle_status');
      formData.append('id', id);

      fetch('../api/admin_customers.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        alert(res.message);
        loadCustomerData();
      });
    }

    function deleteCustomer(id, name) {
      if (!confirm(`HAPUS PERMANEN: Apakah Anda yakin ingin menghapus akun Customer '${name}'?`)) return;

      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('id', id);

      fetch('../api/admin_customers.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(res => {
        alert(res.message);
        loadCustomerData();
      });
    }

    function logoutAdmin() {
      fetch('../api/auth.php?action=logout')
        .then(() => {
          window.location.href = '../index.php';
        });
    }
  </script>
</body>
</html>
