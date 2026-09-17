# PRODUCT REQUIREMENT DOCUMENT (PRD) — BENCHMARK & ADOPTION PLAN
## Evaluasi Teknologi: Loewix AI Vision vs Raray Vision (GitHub: dedin7766/rarayvision)

- **Status Dokumen:** STRATEGIC ROADMAP & COMPARATIVE BENCHMARK
- **Tanggal Analisis:** 17 September 2026
- **Objek Komparasi:** 
  1. **Loewix AI Neural Vision Suite** (Dual-Engine: 3D Biometric Mesh + CCTV Surveillance + FAISS Vector DB + ANPR)
  2. **Raray Vision** ([github.com/dedin7766/rarayvision](https://github.com/dedin7766/rarayvision)) — API Face Recognition berbasis FastAPI + InsightFace + SCRFD + ArcFace + Anti-Spoofing ONNX.

---

## 1. Executive Summary & Putusan Akhir

> [!IMPORTANT]
> **KESIMPULAN BENCHMARK:**
> **Secara keseluruhan, teknologi Loewix AI Vision JAUH LEBIH UNGGUL dan JAUH LEBIH KOMPLEKS (Level Enterprise CCTV/VMS)** dibanding Raray Vision (Level API microservice absensi dasar).
>
> Namun, ada **1 fitur kunci Raray Vision yang sangat berharga untuk kita adopsi ke dalam Loewix:**
> **Anti-Spoofing Liveness Detection (ONNX Model)** — untuk mencegah kecurangan foto/video HP di depan webcam saat absensi/pendaftaran biometrik.

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                                   KATEGORI PENILAIAN                                    │
├──────────────────────────────┬────────────────────────────┬─────────────────────────────┤
│ Fitur / Domain               │ Loewix AI Vision (Kita)    │ Raray Vision (dedin7766)    │
├──────────────────────────────┼────────────────────────────┼─────────────────────────────┤
│ Arsitektur Video/CCTV        │ ⭐⭐⭐⭐⭐ Multi-RTSP, HLS,  │ ⭐ Upload File / WebRTC     │
│                              │ P2P XMeye, MediaMTX Engine │ Snapshot HTTP dasar         │
├──────────────────────────────┼────────────────────────────┼─────────────────────────────┤
│ Visualisasi Landmark         │ ⭐⭐⭐⭐⭐ 468 Titik 3D    │ ⭐⭐ 5 Titik Landmark 2D    │
│                              │ Mesh + Roll/Yaw/Jaw 60 FPS │ (Standar InsightFace)       │
├──────────────────────────────┼────────────────────────────┼─────────────────────────────┤
│ Kecepatan 1:N Matching       │ ⭐⭐⭐⭐⭐ FAISS Vector DB   │ ⭐⭐ Cosine Similarity Loop │
│                              │ Sub-milidetik (100.000 ID) │ (Lambat jika database besar)│
├──────────────────────────────┼────────────────────────────┼─────────────────────────────┤
│ Deteksi Jarak Jauh/Plafon    │ ⭐⭐⭐⭐⭐ COCO-SSD +      │ ❌ Tidak Ada                │
│                              │ Pedestrian + Re-ID Handover│ (Wajah hilang = Buta total) │
├──────────────────────────────┼────────────────────────────┼─────────────────────────────┤
│ ANPR (Plat Nomor)            │ ⭐⭐⭐⭐⭐ Ada (Plat Indo)  │ ❌ Tidak Ada                │
├──────────────────────────────┼────────────────────────────┼─────────────────────────────┤
│ Anti-Spoofing (Liveness)     │ ⭐⭐ Rule-based Blinking   │ ⭐⭐⭐⭐⭐ Dedicated ONNX    │
│                              │ / MediaPipe Eyelid Motion  │ Silent Anti-Spoofing Model  │
└──────────────────────────────┴────────────────────────────┴─────────────────────────────┘
```

---

## 2. Bedah Arsitektur Mendalam: Mengapa Loewix Jauh Lebih Unggul?

### 2.1 Skalabilitas Database & Kecepatan Pencarian (FAISS vs Array Loop)
- **Raray Vision:** Menggunakan pencarian linear manual di Python (`np.dot` atau Cosine Similarity) dengan mengambil data dari SQLite/MySQL. Jika ada 10.000 data wajah, server akan mengeksekusi 10.000 perkalian matriks satu demi satu di setiap frame, menyebabkan latensi melonjak tajam (*CPU bottleneck*).
- **Loewix AI:** Menggunakan **FAISS (Facebook AI Similarity Search)** berbasis C++/Python (`ai_engine/vector_db_faiss.py`). Menggunakan indeks vektor teroptimasi yang mampu menemukan 1 orang dari ratusan ribu database wajah dalam waktu **kurang dari 1 milidetik**.

### 2.2 Kemampuan Pengawasan CCTV Nyata (Pedestrian & Re-ID Handover)
- **Raray Vision:** Hanya mengenali **wajah frontal jarak dekat** (Face-only). Jika pekerja di pabrik/gudang membelakangi kamera CCTV atau menunduk, Raray Vision **gagal mendeteksi apa pun**.
- **Loewix AI:** Memiliki sistem **Surveillance Dual-Engine**:
  1. Jika wajah terlihat dekat: memindai biometrik 3D Mesh.
  2. Jika dari kejauhan/plafon: sistem otomatis melacak **seluruh tubuh manusia (Pedestrian Body Reticle)** dan mengidentifikasi orang berdasarkan warna pakaian, postur, serta riwayat kamera sebelumnya via **Cross-Camera Re-ID Handover**.

### 2.3 Visualisasi & Pengalaman Pengguna (Client-Side HUD 60 FPS)
- **Raray Vision:** Hanya antarmuka web Vue 3 standar dengan kotak border 2D statis.
- **Loewix AI:** Mengintegrasikan **Google MediaPipe 468 3D FaceMesh** langsung di browser pengguna (Client-Side WebAssembly). Jaring biometrik kawat, titik permata berkilau (*jewel nodes*), rotasi miring kepala, mulut terbuka (*jaw drop*), dan kedipan mata diproses pada 60 FPS tanpa membebani GPU/CPU server sama sekali.

### 2.4 Integrasi Kendaraan & ANPR
- **Loewix AI:** Mendukung ANPR (Automatic Number Plate Recognition) untuk plat kendaraan Indonesia, mengaitkan mobil/motor dengan data karyawan/pemiliknya (*Vehicle-to-Owner Association*).
- **Raray Vision:** Tidak memiliki kapabilitas kendaraan sama sekali.

---

## 3. Apa yang Hebat dari Raray Vision & Wajib Kita Adopsi?

### 3.1 Model Anti-Spoofing / Silent Liveness Detection ONNX
Raray Vision memiliki modul unggulan:
```
models/anti_spoofing.onnx
```
- **Fungsi:** Mendeteksi apakah objek di depan kamera adalah **manusia asli berkulit hidup** atau sekadar **foto yang dicetak di kertas / video di layar HP / topeng**.
- **Teknologi:** Model klasifikasi citra berbasis tekstur pantulan cahaya layar, moiré pattern, dan kontur bayangan 3D.
- **Output:** Nilai `liveness_score` ($0.0 \sim 1.0$). Jika $\text{score} < 0.85$, sistem langsung menolak dengan status `SPOOF_ATTACK_DETECTED`.

---

## 4. Rencana Adopsi & Roadmap Upgrade Loewix (PRD V8.0)

Untuk membuat Loewix AI Vision menjadi sistem **100% Sempurna & Tanpa Tanding**, kita akan mengadopsi teknologi terbaik dari Raray Vision:

### 4.1 Roadmap Fitur Baru yang Diadopsi:
1. **Penyematan Modul Anti-Spoofing ONNX di `app_fastapi_server.py`:**
   - Menambahkan endpoint `/api/v1/faces/verify_liveness`.
   - Menggunakan `onnxruntime` untuk menjalankan inferensi anti-spoofing berbobot ringan (<5ms).
2. **Indikator Liveness di UI Loewix:**
   - Menambahkan badge HUD: `LIVENESS: 99.4% REAL HUMAN` (Hijau) atau `ALERT: PHOTO SPOOF DETECTED` (Merah).
3. **Pencegahan Fraud pada Pendaftaran Wajah Baru:**
   - Saat pengguna menekan `+ Daftarkan Wajah Baru`, AI akan memverifikasi liveness terlebih dahulu sebelum vektor biometrik disimpan ke database.

---

## 5. Kesimpulan Rekomendasi untuk Tim Loewix

| Pertanyaan | Jawaban & Rekomendasi |
| :--- | :--- |
| **Bagusan mana teknologinya?** | **Punya kita (Loewix) jauh lebih canggih, lengkap, dan berkelas industri CCTV Enterprise.** Raray Vision adalah proyek microservice absensi sederhana. |
| **Apakah Raray Vision layak dipelajari?** | **Sangat layak**, khususnya arsitektur **Anti-Spoofing ONNX model**-nya yang ringkas dan siap kita integrasikan ke server FastAPI Loewix kita. |
| **Langkah Selanjutnya:** | Pertahankan keunggulan 3D Mesh & Dual-Engine Loewix, lalu tambahkan modul Anti-Spoofing ONNX untuk melengkapi fitur keamanan biometrik level perbankan! |
