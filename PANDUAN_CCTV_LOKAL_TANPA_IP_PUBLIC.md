# 📹 PANDUAN INTEGRASI CCTV JARINGAN LOKAL (LAN) TANPA IP PUBLIK
### PT. LOEWIX INDONESIA — Solusi Arsitektur CCTV Cloud & On-Premise

> Dokumen ini dirancang untuk tim teknis dan Owner/Manajemen perusahaan yang ingin menghubungkan kamera CCTV lokal (`192.168.x.x`) ke platform **Loewix Live & AI Vision** tanpa perlu sewa IP Publik statis dan tanpa membuka port router.

---

## 📊 Perbandingan 3 Solusi untuk Klien / Perusahaan

| Kriteria | Solusi 1: Loewix Local Bridge | Solusi 2: XMeye P2P Cloud ID | Solusi 3: Full On-Premise (Local Server) |
| :--- | :--- | :--- | :--- |
| **Kebutuhan IP Publik** | ❌ **TIDAK BUTUH** | ❌ **TIDAK BUTUH** | ❌ **TIDAK BUTUH** |
| **Setting Port Router** | ❌ **TIDAK PERLU** (Outbound) | ❌ **TIDAK PERLU** (P2P Hole Punch) | ❌ **TIDAK PERLU** (Intranet) |
| **Akses dari Luar Kantor**| ✅ **BISA** (via Web/HP Cloud) | ✅ **BISA** (via Web/HP Cloud) | ❌ **HANYA DI KANTOR** (Kecuali ada VPN) |
| **Biaya Tambahan** | Gratis (Pakai PC kantor yang ada) | Gratis (Bawaan hardware DVR) | Perlu 1 PC/Server khusus di kantor |
| **Kelebihan Utama** | Sangat fleksibel untuk semua merek RTSP | Sangat instan, tanpa PC relay tambahan | Data 100% private, tidak keluar ke internet |
| **Cocok Untuk** | Kantor dengan DVR/IP Cam RTSP biasa | Kantor dengan DVR Loewix / XMeye | Bank, Pabrik Rahasia, Militer, Rumah Sakit |

---

## 🚀 SOLUSI 1: LOEWIX LOCAL BRIDGE (Enterprise Outbound Relay)

### Prinsip Kerja
1. Router kantor **TIDAK PERLU** dibuka port sama sekali (aman 100% dari serangan hacker/port scan).
2. Cukup ada **1 PC / Laptop / Mini PC di kantor** yang menyala dan terhubung ke jaringan LAN CCTV.
3. PC tersebut menjalankan script **Loewix Bridge** yang menyedot RTSP lokal (`rtsp://admin:@192.168.11.182:554/...`) dan "mendorong" (push outbound) ke server MediaMTX cloud Loewix (`stream.loewixcctv.com`).

---

### Cara Menjalankan di Windows (1-Klik):

1. Masuk ke folder: `loewix_pusher/`
2. Buka file konfigurasi `bridge_config.ini`, sesuaikan jika ada perubahan IP/Stream:
   ```ini
   RTSP_LOCAL=rtsp://admin:@192.168.11.182:554/user=admin&password=&channel=1&stream=0.sdp
   STREAM_NAME=cam_live_5018
   CLOUD_SERVER=stream.loewixcctv.com
   CLOUD_RTSP_PORT=8554
   ```
3. Klik 2x file **`push_rtsp_local.bat`**.
   * Script akan otomatis mengecek `ffmpeg`. Jika belum ada, script akan mengunduh versi portable secara otomatis.
   * Streaming langsung aktif ke cloud!
   * Di web portal `loewixcctv.com/customer/index.php`, kamera **RTSP LOCAL** akan langsung muncul live!
   * Dilengkapi fitur **Auto-Reconnect**: Jika CCTV restart atau Wi-Fi drop, script otomatis menghubungkan ulang setiap 5 detik.

### Cara Menjalankan Otomatis saat Komputer Menyala (Auto-Startup):
1. Tekan tombol `Windows + R`, ketik `shell:startup`, lalu tekan **Enter**.
2. Buat *Shortcut* dari file `push_rtsp_local.bat`, lalu paste ke dalam folder Startup tersebut.
3. Sekarang setiap kali komputer kantor dinyalakan, bridge CCTV akan otomatis aktif di background!

---

### Cara Menjalankan Banyak Kamera Sekaligus (Multi-Camera Bridge):
Jika kantor memiliki 4, 8, atau 16 kamera lokal sekaligus:
1. Buka file `loewix_pusher/cameras.json`.
2. Tambahkan daftar RTSP dan nama stream-nya:
   ```json
   [
     {
       "title": "Kamera 1 (Parkiran)",
       "rtsp_url": "rtsp://admin:@192.168.11.182:554/user=admin&password=&channel=1&stream=0.sdp",
       "stream_name": "cam_live_5018"
     },
     {
       "title": "Kamera 2 (Gudang)",
       "rtsp_url": "rtsp://admin:@192.168.11.183:554/user=admin&password=&channel=1&stream=0.sdp",
       "stream_name": "cam_live_5019"
     }
   ]
   ```
3. Jalankan: `python multi_camera_bridge.py`

---

## 🌐 SOLUSI 2: MENGGUNAKAN CLOUD ID / SERIAL NUMBER (XMeye P2P)

Jika DVR/NVR di kantor tersebut bermerek **Loewix / XMeye / Xiongmai / JF Tech**:
Solusi ini **paling praktis** karena tidak memerlukan PC tambahan sama sekali.

### Cara Menemukan Serial Number / Cloud ID:
1. **Lewat Layar TV/Monitor DVR**:
   * Klik kanan mouse di DVR -> **Main Menu** -> **Info / System** -> **Version**.
   * Cari kolom **Serial Number / Nat Code** (terdiri dari 16 digit huruf dan angka, contoh: `848f3922aa2875eb`).
   * Bisa juga scan **QR Code Cloud** yang ada di layar TV atau stiker bawah DVR.
2. **Lewat VMS / CMS di PC**:
   * Buka VMS -> *Device Manager* -> Klik perangkat CCTV -> Kolom *Cloud ID / Serial No*.

### Cara Input di Web Loewix:
1. Buka menu **Kamera Semua Tenant** di `https://loewixcctv.com/customer/index.php`.
2. Klik tombol **Edit** (ikon gerigi) pada kamera.
3. Ubah **Tipe Koneksi Stream** menjadi:  
   👉 **`XMeye P2P Cloud (Cloud ID / Serial Number)`**.
4. Isi form:
   * **Serial Number**: Masukkan 16 digit ID (contoh: `848f3922aa2875eb`).
   * **Channel**: Nomor channel kamera (1, 2, 3, dst).
   * **Username**: `admin`
   * **Password**: Password DVR Anda.
5. Klik **Simpan Kamera**.
6. Server Loewix otomatis melakukan handshake Cloud P2P via gateway bawaan tanpa perlu IP Publik!

---

## 🏢 SOLUSI 3: DEPLOYMENT FULL LOKAL / ON-PREMISE (Intranet Kantor)

Solusi ini ditujukan untuk perusahaan dengan regulasi keamanan ketat (Perbankan, BUMN, Rumah Sakit, Lembaga Pemerintah) di mana **data video CCTV dan AI Face Recognition dilarang keluar ke internet**.

### Prinsip Kerja:
Seluruh ekosistem software Loewix dipasang langsung di satu komputer server / PC di dalam gedung kantor klien:
* Web Server (PHP/Apache/Nginx)
* Media Server (MediaMTX)
* AI Neural Vision Suite (Face Recognition & ANPR)
* Database (`loewix_db.json` / SQLite)

### Langkah Implementasi On-Premise:
1. Siapkan 1 PC Server di kantor klien dengan OS Windows 10/11 atau Ubuntu Linux.
2. Berikan IP Statis lokal, misalnya: `192.168.11.100`.
3. Copy folder project `CCTV LIVE` ke PC tersebut.
4. Di file konfigurasi `mediamtx.yml` lokal, masukkan langsung IP lokal kamera:
   ```yaml
   paths:
     kamera_lokal_1:
       source: rtsp://admin:@192.168.11.182:554/user=admin&password=&channel=1&stream=0.sdp
       sourceOnDemand: yes
   ```
5. Akses dari komputer mana pun di kantor melalui browser:
   * URL: `http://192.168.11.100/customer/index.php`
   * Atau buatkan domain lokal: `http://cctv.kantor.local`

### Keunggulan Solusi 3:
* **0 KB Kuota Internet**: Tidak memakan bandwidth internet kantor sama sekali.
* **Latensi Nyaris 0 Detik**: Streaming instan karena berada di dalam jaringan kabel LAN Gigabit.
* **100% Privacy Compliance**: Wajah karyawan, nomor plat, dan rekaman tidak pernah dikirim ke cloud pihak ketiga.

---

## 💡 Ringkasan Rekomendasi untuk Disampaikan ke Owner:

> *"Pak/Bu Owner, kita siapkan 3 pilihan tanpa perlu keluar biaya sewa IP Publik:*
> 1. **Solusi 1 (Rekomendasi Cepat & Fleksibel)**: Kita pasang aplikasi **Loewix Local Bridge** di 1 PC kantor yang sudah ada. Tinggal klik 2x, kamera langsung online ke web dan bisa dipantau dari HP Bos di mana saja secara aman.
> 2. **Solusi 2 (Tanpa PC Tambahan)**: Jika DVR di lokasi sudah tipe Loewix/XMeye, kita cukup ambil **Cloud ID (16 digit)** dari menu DVR, kamera langsung otomatis live ke web.
> 3. **Solusi 3 (Khusus Private Intranet)**: Jika kantor menghendaki video 100% tidak boleh keluar gedung, kita deploy server web & AI Loewix langsung di jaringan lokal kantor."*
