# PRODUCT REQUIREMENT DOCUMENT (PRD) — PRODUCTION LOCKED
## Loewix AI Responsive 3D Biometric Facial Mesh & Adaptive Landmark Engine (V6.0)

- **Status Dokumen:** ACTIVE / PRODUCTION VERIFIED (Terverifikasi Berfungsi Penuh di Live Stream & Webcam)
- **Tanggal Rilis:** 16 September 2026
- **Target File Implementasi:** `customer/index.php` & `customer/index.html` (Wajib Selalu Byte-Identik)
- **Engine Baseline:** Google MediaPipe 468 3D FaceMesh + Face-API 68 Landmarks + DeepFace ArcFace + Canvas HUD 60 FPS
- **Commit Referensi Produksi:** `8c47fa3` ("fix(scanner): responsive 3D face mesh tracking with 60fps MediaPipe and roll/pitch/jaw deformation")

---

## 1. Executive Summary & Status Keberhasilan

Dokumen ini adalah **kontrak spesifikasi teknis permanen (Locked PRD)** untuk sistem pemindaian biometrik wajah Loewix CCTV. Sistem ini telah diverifikasi bekerja 100% responsif pada webcam live dan stream CCTV:
1. **Miring Kanan & Kiri (Head Roll, Yaw & Pitch):** Seluruh jaring 3D (*triangulation wireframe*), titik permata putih (*jewel nodes*), dan awan titik cyan (*constellation cloud*) berputar dan memiring secara presisi mengikuti sumbu wajah pengguna.
2. **Mangap (Buka Mulut / Jaw Drop):** Node bibir bawah (`lipBot`) dan ujung rahang dagu (`chinTip`) turun secara elastis saat pengguna membuka mulut, memperlihatkan bukaan mulut (*diamond mouth expansion*) secara anatomis nyata.
3. **Merem (Kedip Mata / Eyelid Tracking):** Node kelopak mata atas dan bawah menutup rapat mengikuti kedipan mata pengguna.
4. **Ngelock Wajah (Snug Biometric Bracket & Floating Badge):** Bracket neon lime `[ ]` membingkai wajah pas dari puncak dahi hingga dagu (*snug fit* 4%), dan badge identitas (`WAHYU UTOMO [VIP] 96.8%` atau `STRANGER [Pengunjung] 96.5%`) menempel kuat tanpa lepas atau melorot ke dada/hoodie.

---

## 2. Analisis Kegagalan Versi Terdahulu (Regresi Gambar 1 vs Keberhasilan Gambar 2)

### 2.1 Mengapa Sempat Kaku & Tidak Mau Miring (Gambar 1)?
Pada versi terdahulu (commit `6fa819b` s/d `9cb6d91`), scanner menampilkan kerangka statis berbentuk kotak tegak simetris di tengah muka yang tidak ikut bergerak saat kepala miring atau mulut mangap, diakibatkan oleh:
1. **MediaPipe Tidak Terinisialisasi Sempurna:** `directMediaPipeFaceMesh.initialize()` tidak di-`await`, dan options `refineLandmarks` bernilai `false`. Engine WASM gagal memproses frame dan error teredam di `.catch(() => {})`.
2. **Frame Pump Tabrakan (Race Condition):** Pemanggilan `directMediaPipeFaceMesh.send({ image: video })` dipanggil di dua tempat bersamaan (dalam `setInterval(50)` dan dalam `runFaceAPIDetection`), sehingga flag `_isMediaPipeInFlight` selalu tertahan dan frame video terlewat.
3. **Landmark Hilang di Jalur Fallback Face-API:** Ketika fallback `frameCanvas` atau `ssdMobilenetv1` terpanggil, fungsi `faceapi.detectAllFaces` dijalankan **tanpa** merantai `.withFaceLandmarks(true)`. Akibatnya, objek deteksi tidak memiliki landmark sama sekali.
4. **Jatuh ke Formula 10-Point Static Box:** Karena tidak ada landmark fisik, kode mengeksekusi rumus statis `[ { x: box.x + box.width * 0.30, y: box.y + box.height * 0.08 }, ... ]` yang kaku dan simetris, menghasilkan kerangka datar yang tidak bisa berotasi.
5. **Clobbering oleh Background Detection:** Loop face-api yang lambat (10–20 FPS) menimpa `lastFaceAPIResult` dengan data tanpa 468 titik, menghapus data rotasi 3D yang dihasilkan MediaPipe.

---

## 3. Arsitektur Teknis & Pipeline Eksekusi 60 FPS

```
[Webcam / CCTV Video Stream]
         │
         ├───> [MediaPipe Camera Pump (requestAnimationFrame @ 60 FPS)]
         │           │
         │           ▼
         │     [FaceMesh WASM Engine (refineLandmarks: true)]
         │           │
         │           ▼ (468 3D Landmarks: x, y, z)
         │     [onMediaPipeFaceMeshResults(results)]
         │           ├──> Hitung Snug Box: min/max (X, Y) + 4% pad
         │           ├──> Ekstrak 25 Canonical Nodes (roll/yaw/pitch/jaw)
         │           ├──> Update Langsung activeAIEntities[0] (Zero-Latency)
         │           └──> Update lastFaceAPIResult (468 points preserved)
         │
         └───> [Face-API & DeepFace Background Worker (SetInterval @ 50ms)]
                     │
                     ▼
               [withFaceLandmarks(true) — TinyFace / SSD / ArcFace]
                     │
                     ▼
               [Landmark Clobber Guard]
               (Jika MediaPipe aktif < 1200ms, jangan timpa mesh 3D!)
```

### 3.1 Inisialisasi MediaPipe FaceMesh
```javascript
directMediaPipeFaceMesh = new FaceMesh({
  locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh@0.4.1633559619/${file}`
});
directMediaPipeFaceMesh.setOptions({
  maxNumFaces: 1,
  refineLandmarks: true,           // Mengaktifkan pelacakan pupil, iris, dan kelopak mata
  minDetectionConfidence: 0.25,    // Mengunci wajah seketika meski sudut ekstrem
  minTrackingConfidence: 0.25
});
directMediaPipeFaceMesh.onResults(onMediaPipeFaceMeshResults);
await directMediaPipeFaceMesh.initialize(); // WAJIB DI-AWAIT!
startMediaPipeCameraPump();
```

### 3.2 Sequential Camera Pump (Bebas Race Condition)
```javascript
function startMediaPipeCameraPump() {
  if (_mpRafId) return;
  async function pump() {
    _mpRafId = requestAnimationFrame(pump);
    const video = document.getElementById('ai-video-player');
    if (!directMediaPipeFaceMesh || _isMediaPipeInFlight || !isAutoTrackingActive || !video) return;
    if (video.readyState < 2 || video.paused || video.ended || video.videoWidth === 0) return;

    try {
      _isMediaPipeInFlight = true;
      await directMediaPipeFaceMesh.send({ image: video });
    } catch (e) {
      try {
        const frameCanvas = getDetectionFrame(video);
        if (frameCanvas) await directMediaPipeFaceMesh.send({ image: frameCanvas });
      } catch (e2) {}
    } finally {
      _isMediaPipeInFlight = false;
    }
  }
  _mpRafId = requestAnimationFrame(pump);
}
```

---

## 4. Pemetaan 25 Canonical Biometric Nodes (MediaPipe 468 vs Face-API 68)

Setiap simpul persendian kawat biometrik dihubungkan ke indeks tulang fisik wajah:

| Simpul Node | Index MediaPipe 468 | Index Face-API 68 | Perilaku Gerakan Fisik |
| :--- | :--- | :--- | :--- |
| `foreheadTopL` | Index `109` | $P_{19} + \hat{u} \cdot d_{\text{fh}}$ | Ikut miring saat kepala tilt kiri/kanan |
| `foreheadTopR` | Index `338` | $P_{24} + \hat{u} \cdot d_{\text{fh}}$ | Ikut miring saat kepala tilt kiri/kanan |
| `templeL` | Index `127` | Index `0` | Batas pelipis kiri luar |
| `templeR` | Index `356` | Index `16` | Batas pelipis kanan luar |
| `glabella` | Index `168` | Index `27` | Pusat dahi bawah / antara dua alis |
| `browMidL` | Index `105` | Index `19` | Puncak alis kiri |
| `browMidR` | Index `334` | Index `24` | Puncak alis kanan |
| `eyeL` | Index `468` / Mid(`159`,`145`) | Mid(`37`,`41`) | Melacak kornea & kedipan mata kiri (*merem*) |
| `eyeR` | Index `473` / Mid(`386`,`374`) | Mid(`43`,`47`) | Melacak kornea & kedipan mata kanan (*merem*) |
| `noseBridge` | Index `6` | Index `27` | Pangkal batang hidung atas |
| `noseMid` | Index `197` | Index `29` | Batang hidung tengah |
| `noseTip` | Index `4` | Index `30` | Puncak hidung (*pronasale*) |
| `nostrilL` | Index `98` | Index `31` | Cuping hidung kiri |
| `nostrilR` | Index `327` | Index `35` | Cuping hidung kanan |
| `cheekUpperL` | Index `116` | Index `1` | Tulang pipi atas kiri (*zygomatic*) |
| `cheekUpperR` | Index `345` | Index `15` | Tulang pipi atas kanan (*zygomatic*) |
| `cheekLowerL` | Index `147` | Index `3` | Pipi bawah kiri |
| `cheekLowerR` | Index `376` | Index `13` | Pipi bawah kanan |
| `philtrum` | Index `2` | Index `33` / `51` | Lekukan vertikal di atas bibir tengah |
| `mouthL` | Index `61` | Index `48` | Sudut bibir kiri luar |
| `mouthR` | Index `291` | Index `54` | Sudut bibir kanan luar |
| **`lipBot`** | **Index `17`** | **Index `57`** | **Bibir bawah — turun saat mangap** |
| `chinL` | Index `172` | Index `5` | Sudut rahang bawah kiri |
| `chinR` | Index `397` | Index `11` | Sudut rahang bawah kanan |
| **`chinTip`** | **Index `152`** | **Index `8`** | **Ujung dagu — bergerak turun saat rahang terbuka** |

### 4.1 Vektor Orientasi Kepala 3D (Chin $\to$ Glabella Unit Vector)
Jika sistem berjalan dalam mode Face-API 68 titik, dahi atas dihitung menggunakan sumbu tegak wajah agar tetap dapat berotasi saat kepala miring:
$$\vec{V}_{\text{up}} = \begin{pmatrix} P_{27}.x - P_8.x \\ P_{27}.y - P_8.y \end{pmatrix}, \quad L_{\text{face}} = \|\vec{V}_{\text{up}}\|, \quad \hat{u} = \frac{\vec{V}_{\text{up}}}{L_{\text{face}}}$$
$$P_{\text{foreheadTopL}} = P_{19} + \hat{u} \times (L_{\text{face}} \times 0.32), \quad P_{\text{foreheadTopR}} = P_{24} + \hat{u} \times (L_{\text{face}} \times 0.32)$$

---

## 5. Topologi Poligon Wireframe & Spesifikasi Visual

### 5.1 Graf Garis Poligon (Edges)
Garis kawat menghubungkan 25 simpul menjadi jaring berlian 3D:
1. **Dahi & Pelipis:** `(foreheadTopL, foreheadTopR)`, `(foreheadTopL, templeL)`, `(foreheadTopR, templeR)`, `(foreheadTopL, glabella)`, `(foreheadTopR, glabella)`.
2. **Alis & Mata:** `(templeL, browMidL)`, `(templeR, browMidR)`, `(browMidL, glabella)`, `(browMidR, glabella)`, `(browMidL, eyeL)`, `(browMidR, eyeR)`, `(glabella, eyeL)`, `(glabella, eyeR)`.
3. **Batang Hidung:** `(glabella, noseBridge)`, `(eyeL, noseBridge)`, `(eyeR, noseBridge)`, `(noseBridge, noseMid)`, `(noseMid, noseTip)`, `(noseMid, nostrilL)`, `(noseMid, nostrilR)`, `(nostrilL, noseTip)`, `(nostrilR, noseTip)`, `(nostrilL, nostrilR)`.
4. **Pipi:** `(templeL, cheekUpperL)`, `(templeR, cheekUpperR)`, `(eyeL, cheekUpperL)`, `(eyeR, cheekUpperR)`, `(cheekUpperL, nostrilL)`, `(cheekUpperR, nostrilR)`, `(cheekUpperL, cheekLowerL)`, `(cheekUpperR, cheekLowerR)`.
5. **Mulut & Rahang:** `(noseTip, philtrum)`, `(nostrilL, philtrum)`, `(nostrilR, philtrum)`, `(philtrum, mouthL)`, `(philtrum, mouthR)`, `(philtrum, lipBot)`, `(mouthL, lipBot)`, `(mouthR, lipBot)`, `(cheekLowerL, mouthL)`, `(cheekLowerR, mouthR)`, `(cheekLowerL, chinL)`, `(cheekLowerR, chinR)`, `(mouthL, chinL)`, `(mouthR, chinR)`, `(lipBot, chinL)`, `(lipBot, chinR)`, `(lipBot, chinTip)`, `(chinL, chinTip)`, `(chinR, chinTip)`.

### 5.2 Spesifikasi Visual Efek (Gambar 2 Certified)
- **Gleaming White Triangulation Lines:** `rgba(255, 255, 255, 0.88)`, `shadowColor: 'rgba(255, 255, 255, 0.70)'`, `shadowBlur: 6`, tebal `1.5px`.
- **Glowing White Jewel Nodes (3-Pass Multi-Layer):**
  - Layer A: Radiant outer glow aura (`#ffffff`, `shadowBlur: 22`, radius `9.0px + pulse`).
  - Layer B: Cyan energy halo (`#00f0ff`, `shadowBlur: 12`, radius `5.8px`).
  - Layer C: Crisp diamond core (`#ffffff`, radius `3.2px`).
- **Dense Cyan Constellation Cloud:** Menampilkan 468 titik riil wajah dengan warna `rgba(0, 240, 255, 0.75)`, radius `1.3px`, `shadowBlur: 4`.
- **Holographic Mask Tint:** Gradien ungu-biru lembut di area wajah:
  - Top: `rgba(139, 92, 246, 0.16)`
  - Mid: `rgba(99, 102, 241, 0.10)`
  - Bot: `rgba(59, 130, 246, 0.08)`
- **Snug Reticle Brackets `[ ]`:** Electric Neon Lime `#ccff00` / `#ffff00`, tebal `3.8px`, panjang lengan `20px`, `lineCap = 'round'`.
- **Adaptive 60 FPS LERP:**
  $$P(t) = P(t-1) + \big(P_{\text{target}} - P(t-1)\big) \times \text{factor}$$
  dengan $\text{factor} = 0.88 \sim 0.97$ (sangat cepat untuk pergerakan dekat tanpa lagging).

---

## 6. Golden Rules (Aturan Pantangan & Anti-Regresi)

> [!CAUTION]
> **DILARANG KERAS MELANGGAR ATURAN-ATURAN BERIKUT DALAM PENGEMBANGAN KE DEPAN:**
> 1. **Jangan hapus `await directMediaPipeFaceMesh.initialize()`:** Tanpa baris ini, WASM tidak akan ter-compile di memori browser dan `send()` akan selalu error.
> 2. **Jangan panggil `directMediaPipeFaceMesh.send()` di dalam `setInterval`:** Pemanggilan frame hanya boleh dilakukan satu pintu oleh `startMediaPipeCameraPump()` via `requestAnimationFrame` untuk mencegah tabrakan frame.
> 3. **Semua pemanggilan `faceapi.detectAllFaces` WAJIB menyertakan `.withFaceLandmarks(true)`:** Jangan pernah memanggil `detectAllFaces` polos tanpa landmark di jalur fallback apapun.
> 4. **Jangan biarkan Face-API menimpa MediaPipe:** Jika `_lastMediaPipeFaceTime` masih segar (<1200ms), `lastFaceAPIResult` tidak boleh ditimpa oleh hasil deteksi yang tidak memiliki 468 titik.
> 5. **Jangan ganti `resolveBiometricMeshNodes` dengan formula kotak statis:** Semua simpul wajib terhubung ke titik indeks anatomis riil.
> 6. **Selalu sinkronkan `customer/index.php` dan `customer/index.html`:** Kedua file harus identik secara fungsional.

---

## 7. Verifikasi & Pengujian Berkelanjutan

Untuk memvalidasi bahwa sistem tetap berjalan normal setelah modifikasi file di masa mendatang:
1. **Pemeriksaan Sintaks PHP:**
   ```bash
   php -l customer/index.php
   ```
2. **Pemeriksaan Sinkronisasi HTML & PHP:**
   ```bash
   diff customer/index.html customer/index.php
   ```
   *(Harus menghasilkan output kosong / identik)*
3. **Pengujian Visual Langsung di Browser:**
   - Buka `https://loewixcctv.com/customer/index.php` atau `http://127.0.0.1:8899/customer/index.php`.
   - Buka webcam laptop.
   - Gerakkan kepala miring ke kiri $\to$ mesh ikut miring ke kiri.
   - Gerakkan kepala miring ke kanan $\to$ mesh ikut miring ke kanan.
   - Buka mulut lebar-lebar ("mangap") $\to$ node `lipBot` dan `chinTip` turun secara elastis mengikuti rahang.
   - Tutup mata ("merem") $\to$ kelopak mata menutup di visualisasi.
   - Mundur dan mendekat $\to$ bracket membingkai pas wajah tanpa melorot ke baju/hoodie.
