#!/usr/bin/env python3
"""
Loewix CCTV AI Vision — Enroll Webcam & Biometric Encoding Generator
===================================================================
Captures face from webcam or existing photo, extracts facial encodings,
and synchronizes directly to data/encoding.json & data/loewix_db.json.

Usage:
  # Live interactive webcam enrollment:
  python3 Enroll_webcam.py --name "tess"

  # Headless enrollment from an existing photo file:
  python3 Enroll_webcam.py --name "tess" --image "assets/uploads/faces/tess.jpg"

  # Sync all existing photos in database to encoding.json:
  python3 Enroll_webcam.py --sync-all
"""

import os
import sys
import json
import time
import math
import argparse
from pathlib import Path
from datetime import datetime

try:
    import cv2
except ImportError:
    cv2 = None

try:
    import numpy as np
except ImportError:
    np = None

try:
    from PIL import Image
except ImportError:
    Image = None

PROJECT_ROOT = Path(__file__).resolve().parent
DATA_DIR = PROJECT_ROOT / "data"
ENCODING_FILE = DATA_DIR / "encoding.json"
LOEWIX_DB_FILE = DATA_DIR / "loewix_db.json"
FACES_UPLOAD_DIR = PROJECT_ROOT / "assets" / "uploads" / "faces"
FACES_UPLOAD_DIR.mkdir(parents=True, exist_ok=True)
DATA_DIR.mkdir(parents=True, exist_ok=True)


def load_encoding_db():
    if ENCODING_FILE.exists():
        try:
            with open(ENCODING_FILE, "r", encoding="utf-8") as f:
                return json.load(f)
        except Exception:
            pass
    return {"description": "Loewix CCTV AI Vision Face Encodings Database", "updated_at": "", "faces": []}


def save_encoding_db(db):
    db["updated_at"] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with open(ENCODING_FILE, "w", encoding="utf-8") as f:
        json.dump(db, f, indent=2, ensure_ascii=False)
    print(f"✅ Encodings saved to: {ENCODING_FILE}")


def load_web_db():
    if LOEWIX_DB_FILE.exists():
        try:
            with open(LOEWIX_DB_FILE, "r", encoding="utf-8") as f:
                return json.load(f)
        except Exception:
            pass
    return {"users": [], "ai_faces": []}


def save_web_db(db):
    with open(LOEWIX_DB_FILE, "w", encoding="utf-8") as f:
        json.dump(db, f, indent=4, ensure_ascii=False)
    print(f"✅ Web Database synced: {LOEWIX_DB_FILE}")


def compute_pure_python_128d(raw_bytes: bytes):
    """Generates a stable 128D normalized feature vector in pure Python (zero external dependencies)."""
    if not raw_bytes:
        return [0.0] * 128
    vec = [0.0] * 128
    step = max(1, len(raw_bytes) // 128)
    for i in range(128):
        idx = min(len(raw_bytes) - 1, i * step)
        val = (raw_bytes[idx] / 255.0) - 0.5
        vec[i] = round(val, 4)
    norm = math.sqrt(sum(x * x for x in vec)) + 1e-7
    return [round(x / norm, 6) for x in vec]


def extract_face_encoding(bgr_img=None, raw_bytes: bytes = None):
    """
    Extract high-precision biometric face encoding.
    Attempts:
      1. DeepFace (ArcFace / Facenet)
      2. OpenCV SFace / Normalization (if cv2 & np present)
      3. PIL Image resizing (if PIL present)
      4. Fallback: Pure Python byte distribution (zero dependencies)
    """
    # Method 1: DeepFace
    if cv2 is not None and bgr_img is not None and getattr(bgr_img, 'size', 0) > 0:
        try:
            from deepface import DeepFace
            rgb = cv2.cvtColor(bgr_img, cv2.COLOR_BGR2RGB)
            reps = DeepFace.represent(
                img_path=rgb,
                model_name="Facenet",
                enforce_detection=False,
                detector_backend="opencv"
            )
            if reps and len(reps) > 0 and "embedding" in reps[0]:
                emb = reps[0]["embedding"]
                if len(emb) == 128:
                    return [float(x) for x in emb]
                elif len(emb) > 128:
                    return [float(x) for x in emb[:128]]
        except Exception:
            pass

    # Method 2: dlib / face_recognition if present
    try:
        import face_recognition
        import io
        if raw_bytes:
            img_arr = face_recognition.load_image_file(io.BytesIO(raw_bytes))
        elif bgr_img is not None:
            img_arr = cv2.cvtColor(bgr_img, cv2.COLOR_BGR2RGB)
        else:
            img_arr = None
        if img_arr is not None:
            encs = face_recognition.face_encodings(img_arr)
            if encs and len(encs) > 0 and len(encs[0]) == 128:
                return [float(x) for x in encs[0]]
    except Exception:
        pass

    # Notice: If no deep neural model (DeepFace/face_recognition) is installed in Python,
    # return None so the browser's WebGL Face-API ResNet engine computes the authentic 128D embedding.
    return None


def register_encoding_record(name: str, encoding: list, category="employee", role="Staff", photo_rel_path=""):
    # 1. Update encoding.json
    enc_db = load_encoding_db()
    existing = False
    for f in enc_db["faces"]:
        if f.get("name", "").lower() == name.lower():
            f["encoding"] = encoding
            f["category"] = category
            f["role"] = role
            if photo_rel_path:
                f["photo"] = photo_rel_path
            f["updated_at"] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            existing = True
            break
    if not existing:
        enc_db["faces"].append({
            "name": name,
            "category": category,
            "role": role,
            "photo": photo_rel_path,
            "encoding": encoding,
            "created_at": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        })
    save_encoding_db(enc_db)

    # 2. Update data/loewix_db.json
    web_db = load_web_db()
    if "ai_faces" not in web_db:
        web_db["ai_faces"] = []
    
    face_existing = False
    for wf in web_db["ai_faces"]:
        if wf.get("name", "").lower() == name.lower():
            wf["descriptor"] = encoding
            wf["category"] = category
            wf["role_title"] = role
            if photo_rel_path:
                wf["photo"] = photo_rel_path
            face_existing = True
            break
    
    if not face_existing:
        new_id = max([f.get("id", 0) for f in web_db["ai_faces"]] or [0]) + 1
        web_db["ai_faces"].append({
            "id": new_id,
            "user_id": 1,
            "name": name,
            "category": category,
            "role_title": role,
            "photo": photo_rel_path or "assets/image/avatar-default.png",
            "descriptor": encoding,
            "notes": "Enrolled via Enroll_webcam.py",
            "created_at": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        })
    save_web_db(web_db)
    print(f"\n🎉 SUCCESS: Wajah '{name}' ({category.upper()}) berhasil terdaftar ke database biometrik!\n")


def enroll_from_webcam(name: str, category="employee", role="Staff", cam_index=0):
    if cv2 is None:
        print("=" * 65)
        print("❌ OpenCV (cv2) belum terpasang di Python server ini.")
        print("   Jalankan di terminal server: pip install opencv-python-headless")
        print("   Atau daftarkan wajah langsung via Web Browser di:")
        print("   https://loewixcctv.com/customer/index.php (Tab AI Analytics -> Tambah Wajah)")
        print("=" * 65)
        return False

    print("=" * 65)
    print(f"📸 MEMBUKA WEBCAM UNTUK ENROLLMENT: {name.upper()}")
    print("   Instruksi: Posisikan wajah di tengah oval hijau.")
    print("   Tekan [SPASI] atau [ENTER] untuk mengambil foto & mengekstrak encoding.")
    print("   Tekan [ESC] atau [q] untuk batal.")
    print("=" * 65)

    cap = cv2.VideoCapture(cam_index)
    if not cap.isOpened():
        print(f"❌ Error: Tidak dapat mengakses kamera index {cam_index}.")
        print("   Tips: Jika di server headless (tanpa GUI), gunakan mode: --image <path_foto>")
        return False

    cap.set(cv2.CAP_PROP_FRAME_WIDTH, 640)
    cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 480)

    captured_frame = None

    while True:
        ret, frame = cap.read()
        if not ret:
            print("⚠️ Gagal membaca frame dari webcam.")
            break

        # Mirror view for intuitive user experience
        display_frame = cv2.flip(frame, 1)
        h, w = display_frame.shape[:2]

        # Draw Biometric Target Oval
        center = (w // 2, h // 2)
        axes = (w // 5, int(h // 3.2))
        cv2.ellipse(display_frame, center, axes, 0, 0, 360, (0, 255, 128), 2)
        cv2.putText(
            display_frame,
            f"ENROLL: {name.upper()} | TEKAN [SPASI] UNTUK SIMPAN",
            (20, 40),
            cv2.FONT_HERSHEY_SIMPLEX,
            0.65,
            (0, 255, 255),
            2
        )

        cv2.imshow("Loewix Biometric Face Enrollment", display_frame)
        key = cv2.waitKey(1) & 0xFF
        if key in (32, 13):  # Space or Enter
            captured_frame = frame
            break
        elif key in (27, ord('q')):
            print("🚫 Pendaftaran dibatalkan.")
            break

    cap.release()
    cv2.destroyAllWindows()

    if captured_frame is not None:
        # Save photo
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        safe_name = "".join(c for c in name if c.isalnum() or c in ("-", "_")).lower()
        filename = f"face_{safe_name}_{timestamp}.jpg"
        abs_path = FACES_UPLOAD_DIR / filename
        cv2.imwrite(str(abs_path), captured_frame)
        rel_path = f"assets/uploads/faces/{filename}"

        print("🧠 Mengekstrak vektor biometrik 128-D...")
        encoding = extract_face_encoding(bgr_img=captured_frame)
        if encoding:
            register_encoding_record(name, encoding, category, role, rel_path)
            return True
        else:
            print("❌ Gagal mengekstrak encoding wajah. Pastikan wajah terlihat jelas.")
    return False


def enroll_from_image(name: str, image_path: str, category="employee", role="Staff"):
    p = Path(image_path)
    if not p.is_absolute():
        p = PROJECT_ROOT / image_path
    
    if not p.exists():
        print(f"❌ Error: File foto tidak ditemukan: {p}")
        return False

    raw_bytes = p.read_bytes()
    img = None
    if cv2 is not None:
        try:
            img = cv2.imread(str(p))
        except Exception:
            pass

    print(f"🧠 Mengekstrak vektor biometrik dari: {p.name}...")
    encoding = extract_face_encoding(bgr_img=img, raw_bytes=raw_bytes)
    if encoding:
        rel_path = str(p.relative_to(PROJECT_ROOT)) if p.is_relative_to(PROJECT_ROOT) else f"assets/uploads/faces/{p.name}"
        register_encoding_record(name, encoding, category, role, rel_path)
        return True
    else:
        print("❌ Gagal mengekstrak encoding wajah dari foto.")
        return False


def sync_all_existing_faces():
    """Reads all faces in data/loewix_db.json and updates data/encoding.json"""
    print("🔄 Sinkronisasi semua wajah dari data/loewix_db.json ke data/encoding.json...")
    web_db = load_web_db()
    faces = web_db.get("ai_faces", [])
    count = 0
    for f in faces:
        name = f.get("name", "")
        if not name:
            continue
        photo = f.get("photo", "")
        desc = f.get("descriptor")
        cat = f.get("category", "employee")
        role = f.get("role_title", "Staff")

        def is_authentic_descriptor(d):
            if not isinstance(d, list) or len(d) != 128:
                return False
            sum_sq = sum(float(x) * float(x) for x in d)
            max_val = max(abs(float(x)) for x in d)
            return 0.65 <= sum_sq <= 1.45 and max_val <= 0.60

        if desc and is_authentic_descriptor(desc):
            register_encoding_record(name, desc, cat, role, photo)
            count += 1
        elif photo:
            img = None
            raw_bytes = None
            if photo.startswith("data:image") or ";base64," in photo:
                try:
                    import base64
                    b64_data = photo.split(",", 1)[1] if "," in photo else photo
                    raw_bytes = base64.b64decode(b64_data)
                    if cv2 is not None and np is not None:
                        nparr = np.frombuffer(raw_bytes, np.uint8)
                        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
                except Exception as eB64:
                    pass
            else:
                img_p = PROJECT_ROOT / photo.lstrip("/")
                if img_p.exists():
                    raw_bytes = img_p.read_bytes()
                    if cv2 is not None:
                        img = cv2.imread(str(img_p))

            enc = extract_face_encoding(bgr_img=img, raw_bytes=raw_bytes)
            if enc and is_authentic_descriptor(enc):
                register_encoding_record(name, enc, cat, role, photo if not photo.startswith("data:") else "")
                count += 1
            else:
                print(f"ℹ️ Face '{name}' akan diekstrak otomatis oleh WebGL neural network di browser.")
    print(f"✅ Selesai: {count} profil wajah biometrik terverifikasi di encoding.json!")


def main():
    parser = argparse.ArgumentParser(description="Loewix CCTV AI Vision Face Enrollment")
    parser.add_argument("--name", type=str, help="Nama lengkap orang yang didaftarkan")
    parser.add_argument("--category", type=str, default="employee", choices=["vip", "employee", "resident", "blacklist", "guest"])
    parser.add_argument("--role", type=str, default="Staff", help="Jabatan / Role")
    parser.add_argument("--image", type=str, help="Jalur file foto jika mendaftar dari file")
    parser.add_argument("--cam", type=int, default=0, help="Index webcam (default 0)")
    parser.add_argument("--sync-all", action="store_true", help="Sinkronkan semua wajah yang ada ke encoding.json")

    args = parser.parse_args()

    if args.sync_all:
        sync_all_existing_faces()
        return

    name = args.name
    if not name:
        name = input("Masukkan Nama Lengkap yang akan didaftarkan: ").strip()
        if not name:
            print("Nama tidak boleh kosong.")
            sys.exit(1)

    if args.image:
        enroll_from_image(name, args.image, args.category, args.role)
    else:
        enroll_from_webcam(name, args.category, args.role, args.cam)


if __name__ == "__main__":
    main()
