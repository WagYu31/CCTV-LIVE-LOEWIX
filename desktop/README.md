# Loewix CCTV Live & AI Face Recognition - Desktop App (Offline & Online)

Aplikasi Desktop Resmi PT. Loewix Indonesia untuk sistem monitoring CCTV dan AI Deteksi Wajah, mendukung sistem operasi **Windows (.exe)** dan **macOS (.dmg)**.

---

## Fitur Utama Desktop:
1. **100% Offline Capable**: Bisa memonitor RTSP CCTV lokal (`192.168.x.x`) secara langsung tanpa perlu koneksi internet.
2. **Built-in PHP Engine**: Menjalankan backend PHP secara mandiri di port internal `127.0.0.1:28080`.
3. **Local Stream Player**: Memutar stream HLS / WebRTC dengan zero latency.
4. **AI Face Recognition Lokal**: Deteksi wajah menggunakan TensorFlow.js & WebGL langsung di GPU/CPU komputer pengguna.
5. **Auto Cleanup**: Secara otomatis mematikan semua proses backend (PHP & MediaMTX) saat aplikasi ditutup agar hemat RAM.

---

## Cara Menjalankan Versi Desktop (Development):

Pastikan Node.js (v18+) sudah terpasang di komputer Anda.

```bash
cd desktop
npm install
npm start
```

---

## Cara Membuat Installer (Packaging):

### 1. Untuk macOS (.dmg & .app):
Jalankan di komputer Mac:
```bash
cd desktop
npm run build:mac
```
*Hasil installer:* file `.dmg` akan otomatis dibuat di folder `desktop/dist/`.

### 2. Untuk Windows (.exe Installer & Portable):
Jalankan perintah:
```bash
cd desktop
npm run build:win
```
*Hasil installer:* file `Loewix CCTV Live Setup 1.0.0.exe` akan dibuat di folder `desktop/dist/`.

### 3. Bundling PHP Portable untuk Windows (Agar 100% Offline Tanpa Install PHP):
Untuk mendistribusikan installer Windows ke komputer klien yang belum punya PHP:
1. Download **PHP Windows Non-Thread Safe (NTS) zip** dari [https://windows.php.net/download/](https://windows.php.net/download/).
2. Ekstrak isinya ke folder: `desktop/bin/php/` (sehingga ada file `desktop/bin/php/php.exe`).
3. Jalankan `npm run build:win`. Electron akan secara otomatis mengemas PHP tersebut ke dalam installer Windows.
