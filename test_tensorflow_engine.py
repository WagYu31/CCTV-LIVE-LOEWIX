#!/usr/bin/env python3
"""
Loewix CCTV AI Vision Analytics - TensorFlow Engine Verification
Validates official TensorFlow Face Detection and MediaPipe FaceMesh model configs.
PT. LOEWIX INDONESIA
"""

import sys
import json
import urllib.request

TENSORFLOW_CDN_URLS = [
    ("TensorFlow.js Core v3.18.0", "https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.18.0/dist/tf.min.js"),
    ("MediaPipe FaceMesh Runtime v0.4", "https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh@0.4.1633559619/face_mesh.js"),
    ("TF Face Landmarks Detection v1.0.6", "https://cdn.jsdelivr.net/npm/@tensorflow-models/face-landmarks-detection@1.0.6/dist/face-landmarks-detection.min.js"),
    ("MediaPipe Face Detection Runtime v0.4", "https://cdn.jsdelivr.net/npm/@mediapipe/face_detection@0.4.1646425229/face_detection.js"),
    ("TF Face Detection v1.0.3", "https://cdn.jsdelivr.net/npm/@tensorflow-models/face-detection@1.0.3/dist/face-detection.min.js"),
    ("MediaPipe FaceMesh Packed Assets", "https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh@0.4.1633559619/face_mesh_solution_packed_assets.data")
]

def verify_cdn_endpoints():
    print("=" * 70)
    print("🔍 VERIFIKASI AKSES DAN KETERSEDIAAN MODEL TENSORFLOW.ORG (CDN)")
    print("=" * 70)
    all_ok = True
    for name, url in TENSORFLOW_CDN_URLS:
        try:
            req = urllib.request.Request(url, method="HEAD", headers={"User-Agent": "Loewix-AIVerifier/1.0"})
            with urllib.request.urlopen(req, timeout=8) as response:
                status = response.status
                cors = response.headers.get("access-control-allow-origin", "none")
                print(f"✅ {name:40} : HTTP {status} (CORS: {cors})")
        except Exception as e:
            print(f"❌ {name:40} : Gagal ({e})")
            all_ok = False
    print("=" * 70)
    return all_ok

if __name__ == "__main__":
    success = verify_cdn_endpoints()
    if success:
        print("🎉 Seluruh CDN resmi TensorFlow.org & MediaPipe siap digunakan secara optimal!")
        sys.exit(0)
    else:
        print("⚠️ Ada satu atau lebih endpoint yang mengalami kendala koneksi.")
        sys.exit(1)
