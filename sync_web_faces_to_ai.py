#!/usr/bin/env python3
"""
Loewix CCTV AI Vision — Synchronize Web Faces to FAISS ArcFace Vector DB
========================================================================
Reads registered faces from data/loewix_db.json (web directory) and registers
their 512-D ArcFace embeddings into loewix_ai.db and faiss_arcface.index.
"""

import os
os.environ["KMP_DUPLICATE_LIB_OK"] = "TRUE"
os.environ["OMP_NUM_THREADS"] = "1"
os.environ["OBJC_DISABLE_INITIALIZE_FORK_SAFETY"] = "YES"

import sys
import json
import time
import base64
from io import BytesIO
from pathlib import Path

import cv2
cv2.setNumThreads(0)
import numpy as np
from PIL import Image

PROJECT_ROOT = Path(__file__).resolve().parent
if str(PROJECT_ROOT) not in sys.path:
    sys.path.insert(0, str(PROJECT_ROOT))

from ai_engine.database import init_db, register_identity, get_all_identities
from ai_engine.vector_db_faiss import vector_db
from ai_engine.pipeline_small_face import pipeline

FACES_DIR = PROJECT_ROOT / "assets" / "uploads" / "faces"
WEB_DB_FILE = PROJECT_ROOT / "data" / "loewix_db.json"


def decode_image(img_str: str) -> np.ndarray:
    """Decode base64 string or read file path to BGR numpy array."""
    if img_str.startswith("data:image"):
        if "," in img_str:
            img_str = img_str.split(",", 1)[1]
        img_bytes = base64.b64decode(img_str)
        pil_img = Image.open(BytesIO(img_bytes)).convert("RGB")
        return np.array(pil_img)[:, :, ::-1].copy()
    else:
        # File path
        p = PROJECT_ROOT / img_str.lstrip("/")
        if p.exists():
            return cv2.imread(str(p))
    return None


def sync_faces():
    print("=" * 65)
    print("🔄 LOEWIX CCTV AI VISION — SYNCING FACES FROM WEB DIRECTORY")
    print("=" * 65)

    init_db()
    vector_db.load()
    print(f"📊 Current FAISS indexed faces: {vector_db.size()}")

    if not WEB_DB_FILE.exists():
        print(f"⚠️ Web database file not found: {WEB_DB_FILE}")
        return

    try:
        with open(WEB_DB_FILE, "r", encoding="utf-8") as f:
            data = json.load(f)
    except Exception as e:
        print(f"❌ Failed to parse {WEB_DB_FILE}: {e}")
        return

    ai_faces = data.get("ai_faces", [])
    print(f"📋 Found {len(ai_faces)} faces in Web Directory (data/loewix_db.json)\n")

    synced_count = 0
    updated_json = False

    for idx, face in enumerate(ai_faces, 1):
        name = face.get("name", "").strip()
        category = face.get("category", "employee")
        role_title = face.get("role_title", "")
        photo = face.get("photo", "")
        notes = face.get("notes", "")

        print(f"[{idx}/{len(ai_faces)}] Memproses: '{name}' ({category.upper()})...")

        if not name or not photo or photo == "assets/image/avatar-default.png":
            print(f"  ⏭️ Dilewati (tidak ada foto wajah spesifik)")
            continue

        np_img = decode_image(photo)
        if np_img is None or np_img.size == 0:
            print(f"  ❌ Gagal membaca foto untuk '{name}'")
            continue

        # Save photo as clean JPG in assets/uploads/faces/<SafeName>/
        clean_name = "".join(c for c in name if c.isalnum() or c in (" ", "-", "_")).strip()
        person_dir = FACES_DIR / clean_name
        os.makedirs(person_dir, exist_ok=True)
        photo_filename = f"face_{int(time.time())}_{idx}.jpg"
        photo_path = person_dir / photo_filename
        cv2.imwrite(str(photo_path), np_img)

        # Update JSON record with relative path instead of giant base64
        rel_photo_path = f"assets/uploads/faces/{clean_name}/{photo_filename}"
        if face.get("photo") != rel_photo_path:
            face["photo"] = rel_photo_path
            updated_json = True

        # Extract ArcFace 512-D Embedding
        print(f"  🧠 Mengekstrak 512-D ArcFace Biometric Embedding...")
        emb = pipeline.extract_arcface_embedding(np_img)
        if emb is None:
            print(f"  ⚠️ Peringatan: ArcFace tidak dapat mendeteksi landmark wajah di foto ini")
            continue

        # Register into SQLite Database
        identity = register_identity(
            full_name=name,
            category=category,
            department=role_title,
            notes=notes,
            photo_path=rel_photo_path
        )

        # Add to FAISS Vector Index
        vector_db.add_face(identity["id"], emb)
        synced_count += 1
        print(f"  ✅ BERHASIL didaftarkan ke FAISS ArcFace! (Identity ID: {identity['id']})")

    # Also scan any pre-existing folders in assets/uploads/faces/
    print("\n📁 Memeriksa direktori assets/uploads/faces/...")
    if FACES_DIR.exists():
        for p_dir in FACES_DIR.iterdir():
            if p_dir.is_dir():
                p_name = p_dir.name
                # Check if already indexed
                cur_names = [i["full_name"].strip().lower() for i in get_all_identities()]
                if p_name.lower() not in cur_names:
                    jpgs = [p for p in p_dir.glob("*.jpg")]
                    if jpgs:
                        test_img = cv2.imread(str(jpgs[0]))
                        if test_img is not None:
                            emb = pipeline.extract_arcface_embedding(test_img)
                            if emb is not None:
                                ident = register_identity(full_name=p_name, category="employee")
                                vector_db.add_face(ident["id"], emb)
                                print(f"  ✅ Menambahkan folder '{p_name}' ke FAISS (ID: {ident['id']})")
                                synced_count += 1

    if updated_json:
        try:
            with open(WEB_DB_FILE, "w", encoding="utf-8") as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
            print("💾 Database Web (loewix_db.json) diperbarui dengan path foto yang bersih.")
        except Exception as e:
            print(f"⚠️ Gagal menyimpan loewix_db.json: {e}")

    vector_db.save()
    print("=" * 65)
    print(f"🎉 SINKRONISASI SELESAI!")
    print(f"✅ Wajah baru disinkronkan : {synced_count}")
    print(f"🚀 Total wajah aktif di FAISS ArcFace : {vector_db.size()}")
    print("=" * 65)


if __name__ == "__main__":
    sync_faces()
