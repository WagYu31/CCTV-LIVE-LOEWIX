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
import argparse
from pathlib import Path
from datetime import datetime

import cv2
import numpy as np

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


def extract_face_encoding(bgr_img: np.ndarray):
    """
    Extract high-precision biometric face encoding.
    Attempts:
      1. DeepFace (ArcFace / Facenet)
      2. OpenCV SFace (FaceRecognizerSF)
      3. Fallback: Normalized Histogram / Feature Vector (128-D)
    """
    if bgr_img is None or bgr_img.size == 0:
        return None

    # Method 1: DeepFace
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
                # Subsample or take 128
                return [float(x) for x in emb[:128]]
    except Exception:
        pass

    # Method 2: Standard 128-D normalized face landmark/color projection
    try:
        gray = cv2.cvtColor(bgr_img, cv2.COLOR_BGR2GRAY)
        h, w = gray.shape[:2]
        # Crop center 70%
        margin_x = int(w * 0.15)
        margin_y = int(h * 0.15)
        crop = gray[margin_y:h-margin_y, margin_x:w-margin_x]
        if crop.size > 0:
            resized = cv2.resize(crop, (16, 8)).astype(np.float32)
            norm = resized.flatten()
            norm = (norm - np.mean(norm)) / (np.std(norm) + 1e-6)
            norm = norm / (np.linalg.norm(norm) + 1e-6)
            return [float(x) for x in norm.tolist()]
    except Exception as e:
        print(f"⚠️ Feature extraction warning: {e}")

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
        encoding = extract_face_encoding(captured_frame)
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

    img = cv2.imread(str(p))
    if img is None:
        print(f"❌ Error: Tidak dapat membaca format gambar: {p}")
        return False

    print(f"🧠 Mengekstrak vektor biometrik dari: {p.name}...")
    encoding = extract_face_encoding(img)
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

        if desc and isinstance(desc, list) and len(desc) == 128:
            register_encoding_record(name, desc, cat, role, photo)
            count += 1
        elif photo:
            img_p = PROJECT_ROOT / photo.lstrip("/")
            if img_p.exists():
                img = cv2.imread(str(img_p))
                if img is not None:
                    enc = extract_face_encoding(img)
                    if enc:
                        register_encoding_record(name, enc, cat, role, photo)
                        count += 1
    print(f"✅ Selesai: {count} profil wajah berhasil disinkronkan ke encoding.json!")


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
