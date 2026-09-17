# PRODUCT REQUIREMENT DOCUMENT (PRD) — PRODUCTION LOCKED V7.0
## Loewix AI Dual-Engine Architecture: 3D Biometric Facial Mesh & Smart CCTV Surveillance Engine

- **Status Dokumen:** ACTIVE / PRODUCTION LOCKED (Terverifikasi Penuh di Live Stream CCTV & Webcam)
- **Versi Dokumen:** V7.0 (Dual-Engine: Close-Range 3D FaceMesh + Long-Range CCTV Surveillance Reticles)
- **Tanggal Pembaruan Terakhir:** 17 September 2026
- **Target File Implementasi:** `customer/index.php` & `customer/index.html` (Wajib Selalu Byte-Identik)
- **Engine Baseline:** 
  1. Google MediaPipe 468 3D FaceMesh + Face-API 128D ResNet (Webcam Jarak Dekat)
  2. COCO-SSD Deep Neural Network + ArcFace DeepFace Server-Side + Re-ID Multi-Camera Handover (CCTV Jarak Jauh)
- **Commit Referensi:** `f5bac30` ("fix(sync): sync customer/index.html to be byte-identical with customer/index.php")

---

## 1. Executive Summary: Arsitektur Sensor Ganda (Dual-Engine AI)

Sistem Loewix AI Vision menggunakan arsitektur **Dual-Engine** cerdas yang secara otomatis menyesuaikan jenis sensor visual berdasarkan jenis kamera yang dipilih pengguna:

```
                                  [Sumber Video Loewix]
                                            │
               ┌────────────────────────────┴────────────────────────────┐
               ▼                                                         ▼
       [GAMBAR 1: WEBCAM]                                       [GAMBAR 2: CCTV STREAM]
   (Live Webcam Laptop / Gerbang)                            (Pabrik, Gudang, Siantar, dsb)
               │                                                         │
               ▼                                                         ▼
  [ENGINE 1: 3D BIOMETRIC FACEMESH]                          [ENGINE 2: SURVEILLANCE RETICLE]
 ├── 468 Titik Anatomi Riil 3D                              ├── Deteksi Tubuh Manusia (Pedestrian)
 ├── 25-Node Canonical Wireframe Mesh                       ├── Deteksi Kendaraan (Mobil, Motor, Truk)
 ├── Jewel Nodes + Constellation Cloud                      ├── Sensor Kotak-Kotak Reticle Pengawasan
 ├── Responsif Miring, Mangap & Merem                       ├── DeepFace ArcFace Server-Side Recognition
 └── Status: STRANGER / [VIP] Terdaftar                     └── Multi-Camera Re-ID Handover Tracking
```

### 1.1 Perbandingan Karakteristik Sensor (Gambar 1 vs Gambar 2)

| Parameter | Mode 1: Webcam Jarak Dekat (Gambar 1) | Mode 2: CCTV Jarak Jauh (Gambar 2) |
| :--- | :--- | :--- |
| **Tujuan Utama** | Pengenalan biometrik presisi tinggi untuk absensi, akses pintu, dan verifikasi wajah. | Pengawasan area luas, keamanan perimeter, pelacakan pekerja, dan deteksi kendaraan. |
| **Bentuk Sensor Visual** | **Landmark Jaring 3D Wireframe Mesh** (Segitiga kawat putih bersinar, titik permata *jewel nodes*, awan titik cyan di dahi, pipi, hidung, mulut, dan rahang). | **Sensor Kotak-Kotak Reticle (Bounding Box)** futuristik dengan garis sudut siku, crosshair pusat, dan estimasi jarak. |
| **Perilaku AI saat 0 Data** | **Tetap memindai normal** dengan status **`STRANGER [MEMINDAI...]`** (kuning/emas). Landmark 3D tetap menempel di wajah. | **Tetap mendeteksi normal** dengan status **`PENGUNJUNG`** atau **`ORANG`** (biru/cyan). Kotak reticle tetap melacak tubuh pekerja. |
| **Model AI Aktif** | Google MediaPipe FaceMesh (468 3D Nodes) + Face-API (128D ResNet Embeddings). | COCO-SSD (Person & Vehicle Detection) + DeepFace ArcFace (YuNet + YOLOv8 + ArcFace). |

---

## 2. Spesifikasi Teknis Engine 1: Biometric 3D Facial Mesh (Webcam)

### 2.1 Anatomi & Respon Gerak Fisik Wajah (Zero-Latency 60 FPS)
Engine 1 wajib merespons setiap gerakan fisik wajah pengguna secara anatomis riil tanpa lag:
1. **Head Tilt / Roll (Miring Kiri & Kanan):** Seluruh jaring kawat (*triangulation lines*) dan titik *jewel nodes* berputar mengikuti kemiringan sumbu glabella-dagu wajah.
2. **Head Yaw & Pitch (Menoleh & Mengangguk):** Node pipi (*zygomatic*) dan cuping hidung memendek/memanjang secara perspektif 3D mengikuti orientasi kedalaman ($z$).
3. **Jaw Drop ("Mangap" / Buka Mulut):** Node bibir bawah (`lipBot` / index `17`) dan ujung dagu (`chinTip` / index `152`) turun secara elastis saat rahang terbuka.
4. **Eyelid Tracking ("Merem" / Kedip Mata):** Node kelopak mata kiri (`eyeL`) dan kanan (`eyeR`) menyempit dan menutup saat mata berkedip.
5. **Snug Fit Bracket:** Kotak pembungkus wajah menempel rapat (*margin 4%*) dari puncak dahi hingga ujung dagu, **dilarang melorot ke leher, jaket, atau hoodie**.

### 2.2 Pemetaan 25 Canonical Biometric Nodes (MediaPipe 468 vs Face-API 68)

| Simpul Node | Index MediaPipe 468 | Index Face-API 68 | Perilaku Gerakan Fisik |
| :--- | :--- | :--- | :--- |
| `foreheadTopL` | Index `109` | $P_{19} + \hat{u} \cdot d_{\text{fh}}$ | Batas dahi kiri atas (ikut miring saat tilt) |
| `foreheadTopR` | Index `338` | $P_{24} + \hat{u} \cdot d_{\text{fh}}$ | Batas dahi kanan atas (ikut miring saat tilt) |
| `templeL` | Index `127` | Index `0` | Batas pelipis kiri luar |
| `templeR` | Index `356` | Index `16` | Batas pelipis kanan luar |
| `glabella` | Index `168` | Index `27` | Pusat dahi bawah / di antara dua alis |
| `browMidL` | Index `105` | Index `19` | Puncak lengkung alis kiri |
| `browMidR` | Index `334` | Index `24` | Puncak lengkung alis kanan |
| `eyeL` | Index `468` / Mid(`159`,`145`) | Mid(`37`,`41`) | Pusat kornea & kelopak mata kiri |
| `eyeR` | Index `473` / Mid(`386`,`374`) | Mid(`43`,`47`) | Pusat kornea & kelopak mata kanan |
| `noseBridge` | Index `6` | Index `27` | Pangkal batang hidung atas |
| `noseMid` | Index `197` | Index `29` | Batang hidung tengah |
| `noseTip` | Index `4` | Index `30` | Puncak ujung hidung (*pronasale*) |
| `nostrilL` | Index `98` | Index `31` | Cuping hidung kiri |
| `nostrilR` | Index `327` | Index `35` | Cuping hidung kanan |
| `cheekUpperL` | Index `116` | Index `1` | Tulang pipi atas kiri (*zygomatic*) |
| `cheekUpperR` | Index `345` | Index `15` | Tulang pipi atas kanan (*zygomatic*) |
| `cheekLowerL` | Index `147` | Index `3` | Pipi bawah kiri |
| `cheekLowerR` | Index `376` | Index `13` | Pipi bawah kanan |
| `philtrum` | Index `2` | Index `33` / `51` | Lekukan filtrum di atas bibir atas |
| `mouthL` | Index `61` | Index `48` | Sudut bibir kiri luar |
| `mouthR` | Index `291` | Index `54` | Sudut bibir kanan luar |
| **`lipBot`** | **Index `17`** | **Index `57`** | **Bibir bawah tengah — turun saat mangap** |
| `chinL` | Index `172` | Index `5` | Sudut rahang bawah kiri |
| `chinR` | Index `397` | Index `11` | Sudut rahang bawah kanan |
| **`chinTip`** | **Index `152`** | **Index `8`** | **Ujung dagu tengah — turun saat rahang terbuka** |

### 2.3 Standar Efek Visual Biometric Wireframe
- **Gleaming White Triangulation Lines:** `rgba(255, 255, 255, 0.88)`, `shadowColor: 'rgba(255, 255, 255, 0.70)'`, `shadowBlur: 6`, tebal `1.5px`.
- **Glowing White Jewel Nodes (3-Lapisan Multi-Pass):**
  - Lapisan Luar: Radiant aura glow (`#ffffff`, `shadowBlur: 22`, radius `9.0px`).
  - Lapisan Tengah: Cyan energy halo (`#00f0ff`, `shadowBlur: 12`, radius `5.8px`).
  - Lapisan Inti: Crisp diamond core (`#ffffff`, radius `3.2px`).
- **Dense Cyan Constellation Cloud:** Menampilkan 468 titik riil wajah dengan warna `rgba(0, 240, 255, 0.75)`, radius `1.3px`, `shadowBlur: 4`.
- **Holographic Mask Gradient:** Gradien ungu-biru lembut pada poligon wajah (`rgba(139, 92, 246, 0.16)` s/d `rgba(59, 130, 246, 0.08)`).
- **Corner Brackets:**
  - Status Terdaftar / VIP: Electric Neon Lime `#ccff00` / Cyan `#00f0ff`.
  - Status Belum Terdaftar (Stranger / Scanning): Golden Yellow `#ffd700`.
  - Status Blacklist: Red Coral `#ef4444`.

---

## 3. Spesifikasi Teknis Engine 2: Surveillance Reticle AI (CCTV Jarak Jauh)

### 3.1 Deteksi Pedestrian & Kendaraan Jarak Jauh
Pada kamera CCTV plafon/sudut ruangan (jarak 5–30 meter), wajah manusia berukuran kecil (<25 piksel) sehingga algoritma 468 titik FaceMesh otomatis dialihkan ke **Surveillance Reticle Engine**:
1. **Deteksi Seluruh Tubuh (COCO-SSD Pedestrian):**
   - Menghasilkan kotak reticle pengawasan yang membungkus kepala hingga kaki orang yang sedang bekerja/berjalan.
   - Dilengkapi atribut estimasi pakaian atas (*clothing profile*) dan penanda pelacak Re-ID.
2. **Deteksi Kendaraan (Vehicle Recognition):**
   - Mendeteksi jenis kendaraan: `MOTOR`, `MOBIL`, `TRUK`, `BUS`, `SEPEDA`.
   - Menghubungkan kendaraan dengan plat nomor terdaftar dan pemiliknya (*Vehicle-to-Owner Association*).
3. **DeepFace Server-Side ArcFace:**
   - Melakukan cropping wajah otomatis dan mengirimkannya ke API server backend untuk pencocokan embedding ArcFace berakurasi tinggi (threshold distance $\le 0.58$).

---

## 4. Siklus Data Wajah & Zero-State Lifecycle

### 4.1 Perilaku Saat Database Kosong (0 Wajah)
1. **Dilarang Menghilangkan Landmark:** Jika database wajah dikosongkan (`Wajah Terdaftar: 0`), HUD scanner **wajib tetap aktif 100%**.
2. **Klasifikasi Stranger:** Wajah yang terdeteksi di kamera wajib dilabeli sebagai:
   - `STRANGER [MEMINDAI...]` saat progress scan $0\% - 99\%$.
   - `STRANGER` (Kuning Emas `#ffd700`) saat mencapai $100\%$ lock.
3. **Pendaftaran Wajah Baru:**
   - Pengguna menekan tombol **`+ Daftarkan Wajah Baru`**.
   - Sistem mengambil foto webcam atau berkas gambar, mengekstrak 128 embedding vektor ResNet, dan menyimpannya ke `data/encoding.json` serta database server.
   - Begitu tersimpan, scanner secara otomatis mengenali wajah pengguna tanpa perlu restart server.

### 4.2 Pembersihan Data (Reset All Faces)
- Endpoint `api/ai_analytics.php?action=reset_all_faces` mengosongkan seluruh cache biometrik dan daftar wajah menjadi array kosong `[]`.
- Sinkronisasi instan memastikan tidak ada *ghost data* atau wajah lama yang tertinggal di memori.

---

## 5. Aturan Pantangan & Pencegahan Regresi (Golden Rules V7.0)

> [!CAUTION]
> **ATURAN MUTLAK ARSITEKTUR SISTEM (DILARANG DILANGGAR):**
>
> 1. **Sinkronisasi File PHP & HTML (Wajib Byte-Identik):**
>    Server Nginx pada aaPanel memprioritaskan penyajian `customer/index.html` daripada `customer/index.php`. Oleh karena itu, **setiap perubahan pada `customer/index.php` WAJIB disalin langsung ke `customer/index.html`** sebelum melakukan commit dan push ke Git:
>    ```bash
>    cp customer/index.php customer/index.html
>    ```
> 2. **Pencegahan JavaScript Temporal Dead Zone (TDZ):**
>    Variabel filter tipe entitas (`isPerson`, `isVehicle`, `isNonFace`) **wajib dideklarasikan di awal iterasi loop deteksi**, sebelum kondisi pemrosesan descriptor dijalankan.
> 3. **Watchdog pada Detection Loop:**
>    Flag `faceAPIDetectionRunning` wajib dilindungi oleh timeout watchdog ($>2000\text{ ms}$) agar pemindaian tidak pernah macet (*stuck*) jika terjadi interupsi jaringan atau frame canvas kosong.
> 4. **Pemisahan Jalur MediaPipe & CCTV:**
>    - Jalur MediaPipe 468 FaceMesh dijalankan khusus untuk mode Webcam Laptop (`currentAICamera.id === 'webcam'`).
>    - Jalur COCO-SSD & DeepFace Surveillance Reticles dijalankan untuk kamera CCTV streaming (`currentAICamera.id !== 'webcam'`).
> 5. **Jangan Gunakan Hardcoded Identity:**
>    Pengenalan identitas pengguna wajib murni berasal dari kecocokan biometrik 128D ResNet (jarak Euclidean $\le 0.58$) atau DeepFace ArcFace, bukan dari nilai hardcoded statis.

---

## 6. Prosedur Verifikasi Produksi

Setiap kali melakukan deployment ke server produksi aaPanel:
1. **Cek Integritas Sintaks:**
   ```bash
   php -l customer/index.php
   ```
2. **Cek Kesamaan File:**
   ```bash
   cmp customer/index.php customer/index.html
   ```
   *(Harus menghasilkan kode keluar 0 / tanpa perbedaan)*.
3. **Uji Kasus Langsung di Browser:**
   - **Kasus A (Webcam - Gambar 1):** Buka webcam $\to$ Landmark jaring kawat 3D, titik permata, dan kotak sudut L-Bracket kuning muncul menempel di wajah. Saat kepala miring/mulut mangap, jaring kawat ikut bergerak responsif.
   - **Kasus B (CCTV Siantar - Gambar 2):** Buka channel CCTV Siantar $\to$ Kotak sensor reticle pengawasan membungkus tubuh para pekerja di ruangan.
   - **Kasus C (Pendaftaran Wajah Baru):** Daftarkan wajah via modal $\to$ Kotak kuning `STRANGER` seketika berubah menjadi hijau `[VIP] / [KARYAWAN]` dengan nama yang terdaftar.
