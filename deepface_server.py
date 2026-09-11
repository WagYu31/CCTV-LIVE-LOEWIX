#!/usr/bin/env python3
"""
Loewix CCTV AI Vision — DeepFace Recognition Server
=====================================================
Flask API server providing face recognition, verification, and facial
attribute analysis powered by DeepFace (https://github.com/serengil/deepface).

Designed to work alongside the existing Loewix CCTV browser-based AI scanner.
The browser handles real-time face *detection* (bounding boxes) via face-api.js
while this server handles high-accuracy face *recognition* and attribute analysis.

PT. LOEWIX INDONESIA
"""

import os
import sys
import json
import time
import base64
import hashlib
import logging
import tempfile
import traceback
from io import BytesIO
from pathlib import Path
from datetime import datetime

import numpy as np
from PIL import Image

from flask import Flask, request, jsonify
from flask_cors import CORS

# ---------------------------------------------------------------------------
# Configuration
# ---------------------------------------------------------------------------

# Server
HOST = os.environ.get("DEEPFACE_HOST", "0.0.0.0")
PORT = int(os.environ.get("DEEPFACE_PORT", 5050))

# DeepFace model settings
MODEL_NAME = os.environ.get("DEEPFACE_MODEL", "ArcFace")          # ArcFace, Facenet512, VGG-Face, etc.
DETECTOR_BACKEND = os.environ.get("DEEPFACE_DETECTOR", "yunet")    # yunet (fast), retinaface (accurate), opencv, mtcnn
DISTANCE_METRIC = os.environ.get("DEEPFACE_METRIC", "cosine")     # cosine, euclidean, euclidean_l2

# Face database directory — stores registered face photos organized by identity
# Structure: DB_PATH/<person_name>/photo1.jpg, photo2.jpg, ...
PROJECT_ROOT = Path(__file__).resolve().parent
DB_PATH = os.environ.get("DEEPFACE_DB_PATH", str(PROJECT_ROOT / "assets" / "uploads" / "faces"))

# Thresholds
VERIFY_THRESHOLD = float(os.environ.get("DEEPFACE_VERIFY_THRESHOLD", 0.0))  # 0 = use model default

# Logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
logger = logging.getLogger("deepface_server")

# ---------------------------------------------------------------------------
# Flask App
# ---------------------------------------------------------------------------

app = Flask(__name__)
CORS(app, resources={r"/api/*": {"origins": "*"}})

# ---------------------------------------------------------------------------
# Lazy-load DeepFace (downloads models on first use)
# ---------------------------------------------------------------------------

_deepface_module = None
_models_warmed = False


def get_deepface():
    """Lazy import + warm-up DeepFace models on first call."""
    global _deepface_module, _models_warmed
    if _deepface_module is None:
        logger.info("⏳ Importing DeepFace library (first time may download models ~500MB)...")
        from deepface import DeepFace
        _deepface_module = DeepFace
        logger.info("✅ DeepFace library imported successfully.")

    if not _models_warmed:
        try:
            # Pre-build model by running a tiny dummy analysis
            logger.info(f"⏳ Warming up model '{MODEL_NAME}' with detector '{DETECTOR_BACKEND}'...")
            # Create a small dummy image for warm-up
            dummy = np.zeros((64, 64, 3), dtype=np.uint8)
            dummy[16:48, 16:48] = [200, 180, 160]  # skin-like patch
            try:
                _deepface_module.represent(
                    img_path=dummy,
                    model_name=MODEL_NAME,
                    detector_backend="skip",
                    enforce_detection=False
                )
            except Exception:
                pass  # Warm-up may fail on dummy, that's OK — model is loaded
            _models_warmed = True
            logger.info(f"✅ Model '{MODEL_NAME}' warmed up and ready!")
        except Exception as e:
            logger.warning(f"⚠️  Model warm-up notice: {e}")
            _models_warmed = True  # Don't retry endlessly

    return _deepface_module


# ---------------------------------------------------------------------------
# Utility Functions
# ---------------------------------------------------------------------------

def decode_base64_image(b64_string: str) -> np.ndarray:
    """Decode a base64-encoded image (with or without data URI prefix) to numpy array (BGR)."""
    if "," in b64_string:
        b64_string = b64_string.split(",", 1)[1]

    img_bytes = base64.b64decode(b64_string)
    pil_img = Image.open(BytesIO(img_bytes)).convert("RGB")
    np_img = np.array(pil_img)
    # Convert RGB to BGR for OpenCV/DeepFace compatibility
    np_img = np_img[:, :, ::-1].copy()
    return np_img


def save_temp_image(np_img: np.ndarray) -> str:
    """Save numpy image to a temporary file and return the path."""
    import cv2
    fd, path = tempfile.mkstemp(suffix=".jpg")
    os.close(fd)
    cv2.imwrite(path, np_img)
    return path


def ensure_db_path():
    """Ensure the face database directory exists."""
    os.makedirs(DB_PATH, exist_ok=True)
    return DB_PATH


def get_registered_faces_info() -> list:
    """Get information about all registered face identities in the DB."""
    db_path = ensure_db_path()
    identities = []
    if os.path.isdir(db_path):
        for name in sorted(os.listdir(db_path)):
            person_dir = os.path.join(db_path, name)
            if os.path.isdir(person_dir):
                photos = [f for f in os.listdir(person_dir)
                          if f.lower().endswith(('.jpg', '.jpeg', '.png', '.webp'))]
                if photos:
                    identities.append({
                        "name": name,
                        "photo_count": len(photos),
                        "photos": photos
                    })
    return identities


# ---------------------------------------------------------------------------
# Embedding Cache — avoid re-computing embeddings for known registered photos
# ---------------------------------------------------------------------------

_embedding_cache = {}  # key: file_hash -> { embedding, model, timestamp }


def _file_hash(filepath: str) -> str:
    """Quick MD5 hash of file for cache key."""
    h = hashlib.md5()
    with open(filepath, "rb") as f:
        for chunk in iter(lambda: f.read(8192), b""):
            h.update(chunk)
    return h.hexdigest()


def get_embedding(img_input, use_cache_path: str = None) -> list:
    """Get face embedding. Uses cache for file-based images."""
    DeepFace = get_deepface()

    cache_key = None
    if use_cache_path and os.path.isfile(use_cache_path):
        cache_key = f"{_file_hash(use_cache_path)}_{MODEL_NAME}"
        if cache_key in _embedding_cache:
            cached = _embedding_cache[cache_key]
            return cached["embedding"]

    result = DeepFace.represent(
        img_path=img_input,
        model_name=MODEL_NAME,
        detector_backend=DETECTOR_BACKEND,
        enforce_detection=False,
        align=True
    )

    if result and len(result) > 0:
        embedding = result[0]["embedding"]
        if cache_key:
            _embedding_cache[cache_key] = {
                "embedding": embedding,
                "model": MODEL_NAME,
                "timestamp": time.time()
            }
        return embedding

    return None


# ---------------------------------------------------------------------------
# API Endpoints
# ---------------------------------------------------------------------------

@app.route("/api/deepface/health", methods=["GET"])
def health_check():
    """Health check — returns server status and configuration."""
    DeepFace = get_deepface()
    db_path = ensure_db_path()
    identities = get_registered_faces_info()

    return jsonify({
        "status": "online",
        "server": "Loewix DeepFace Recognition Server",
        "version": "1.0.0",
        "model": MODEL_NAME,
        "detector": DETECTOR_BACKEND,
        "metric": DISTANCE_METRIC,
        "db_path": db_path,
        "registered_identities": len(identities),
        "identities": identities,
        "models_ready": _models_warmed,
        "timestamp": datetime.now().isoformat()
    })


@app.route("/api/deepface/represent", methods=["POST"])
def represent():
    """
    Generate face embedding from an image.

    Accepts:
      - Form field 'img': base64 encoded image OR file upload
      - Form field 'img_path': path to image file on disk

    Returns:
      - embedding: 128/512-dimensional vector
      - facial_area: detected face bounding box
    """
    DeepFace = get_deepface()

    try:
        img_input = None
        cache_path = None

        # Handle various input types
        if "img" in request.files:
            file = request.files["img"]
            img_bytes = file.read()
            pil_img = Image.open(BytesIO(img_bytes)).convert("RGB")
            img_input = np.array(pil_img)[:, :, ::-1].copy()
        elif request.is_json and "img" in request.json:
            img_input = decode_base64_image(request.json["img"])
        elif "img" in request.form:
            b64 = request.form["img"]
            if b64.startswith("data:") or len(b64) > 260:
                img_input = decode_base64_image(b64)
            elif os.path.isfile(b64):
                img_input = b64
                cache_path = b64
        elif "img_path" in request.form:
            path = request.form["img_path"]
            if os.path.isfile(path):
                img_input = path
                cache_path = path
            else:
                return jsonify({"success": False, "error": f"File not found: {path}"}), 404

        if img_input is None:
            return jsonify({"success": False, "error": "No image provided. Use 'img' (base64/file) or 'img_path'."}), 400

        result = DeepFace.represent(
            img_path=img_input,
            model_name=MODEL_NAME,
            detector_backend=DETECTOR_BACKEND,
            enforce_detection=False,
            align=True
        )

        if result and len(result) > 0:
            return jsonify({
                "success": True,
                "embedding": result[0]["embedding"],
                "facial_area": result[0].get("facial_area", {}),
                "model": MODEL_NAME
            })
        else:
            return jsonify({"success": False, "error": "No face detected in image."}), 400

    except Exception as e:
        logger.error(f"[represent] Error: {e}\n{traceback.format_exc()}")
        return jsonify({"success": False, "error": str(e)}), 500


@app.route("/api/deepface/verify", methods=["POST"])
def verify():
    """
    Verify whether two face images belong to the same person.

    Accepts (JSON or form):
      - img1: base64 image or file path
      - img2: base64 image or file path

    Returns:
      - verified: bool
      - distance: float
      - threshold: float
      - model: string
    """
    DeepFace = get_deepface()

    try:
        data = request.json if request.is_json else request.form

        img1_input = None
        img2_input = None

        for key, target in [("img1", "img1_input"), ("img2", "img2_input")]:
            val = data.get(key, "")
            if val.startswith("data:") or (len(val) > 260 and "," in val):
                result = decode_base64_image(val)
            elif os.path.isfile(val):
                result = val
            else:
                result = decode_base64_image(val)

            if target == "img1_input":
                img1_input = result
            else:
                img2_input = result

        if img1_input is None or img2_input is None:
            return jsonify({"success": False, "error": "Both img1 and img2 are required."}), 400

        # Save to temp if numpy arrays
        img1_path = save_temp_image(img1_input) if isinstance(img1_input, np.ndarray) else img1_input
        img2_path = save_temp_image(img2_input) if isinstance(img2_input, np.ndarray) else img2_input

        try:
            result = DeepFace.verify(
                img1_path=img1_path,
                img2_path=img2_path,
                model_name=MODEL_NAME,
                detector_backend=DETECTOR_BACKEND,
                distance_metric=DISTANCE_METRIC,
                enforce_detection=False,
                align=True
            )

            return jsonify({
                "success": True,
                "verified": result["verified"],
                "distance": result["distance"],
                "threshold": result["threshold"],
                "model": result.get("model", MODEL_NAME),
                "similarity_metric": DISTANCE_METRIC
            })
        finally:
            # Clean up temp files
            for p in [img1_path, img2_path]:
                if p and p.startswith(tempfile.gettempdir()):
                    try:
                        os.unlink(p)
                    except OSError:
                        pass

    except Exception as e:
        logger.error(f"[verify] Error: {e}\n{traceback.format_exc()}")
        return jsonify({"success": False, "error": str(e)}), 500


@app.route("/api/deepface/analyze", methods=["POST"])
def analyze():
    """
    Analyze facial attributes: age, gender, dominant emotion, dominant race.

    Accepts:
      - img: base64 image (JSON or form field)
      - OR file upload via 'img' form field

    Returns:
      - age, gender, dominant_emotion, dominant_race, emotion percentages
    """
    DeepFace = get_deepface()

    try:
        img_input = None

        if "img" in request.files:
            file = request.files["img"]
            img_bytes = file.read()
            pil_img = Image.open(BytesIO(img_bytes)).convert("RGB")
            img_input = np.array(pil_img)[:, :, ::-1].copy()
        elif request.is_json and "img" in request.json:
            img_input = decode_base64_image(request.json["img"])
        elif "img" in request.form:
            b64 = request.form["img"]
            if b64.startswith("data:") or len(b64) > 260:
                img_input = decode_base64_image(b64)
            elif os.path.isfile(b64):
                img_input = b64

        if img_input is None:
            return jsonify({"success": False, "error": "No image provided."}), 400

        tmp_path = None
        if isinstance(img_input, np.ndarray):
            tmp_path = save_temp_image(img_input)
            analysis_input = tmp_path
        else:
            analysis_input = img_input

        try:
            results = DeepFace.analyze(
                img_path=analysis_input,
                actions=["age", "gender", "emotion"],
                detector_backend=DETECTOR_BACKEND,
                enforce_detection=False,
                silent=True
            )

            if results and len(results) > 0:
                r = results[0]
                return jsonify({
                    "success": True,
                    "age": r.get("age"),
                    "gender": r.get("dominant_gender", r.get("gender", "Unknown")),
                    "gender_confidence": r.get("gender", {}),
                    "dominant_emotion": r.get("dominant_emotion", "neutral"),
                    "emotion": r.get("emotion", {}),
                    "facial_area": r.get("region", {}),
                })
            else:
                return jsonify({"success": False, "error": "No face detected for analysis."}), 400
        finally:
            if tmp_path:
                try:
                    os.unlink(tmp_path)
                except OSError:
                    pass

    except Exception as e:
        logger.error(f"[analyze] Error: {e}\n{traceback.format_exc()}")
        return jsonify({"success": False, "error": str(e)}), 500


@app.route("/api/deepface/find", methods=["POST"])
def find_face():
    """
    Find the closest matching identity from the registered face database.
    This is the primary endpoint used by the CCTV live scanner.

    Accepts (JSON or form):
      - img: base64 encoded face crop (from browser's face-api.js detection)
      - analyze: "true"/"false" — also return age/gender/emotion (default: true)
      - threshold: custom distance threshold (optional, 0 = use model default)

    Returns:
      - identity: matched person name or "STRANGER"
      - distance: similarity distance
      - confidence: percentage confidence
      - age, gender, emotion (if analyze=true)
    """
    DeepFace = get_deepface()

    try:
        db_path = ensure_db_path()
        identities = get_registered_faces_info()

        if len(identities) == 0:
            return jsonify({
                "success": True,
                "identity": "STRANGER",
                "distance": 1.0,
                "confidence": 0,
                "message": "No registered faces in database.",
                "db_path": db_path
            })

        # Parse input
        img_input = None
        do_analyze = True
        custom_threshold = VERIFY_THRESHOLD

        if request.is_json:
            data = request.json
            if "img" in data:
                img_input = decode_base64_image(data["img"])
            do_analyze = str(data.get("analyze", "true")).lower() == "true"
            custom_threshold = float(data.get("threshold", VERIFY_THRESHOLD))
        else:
            if "img" in request.files:
                file = request.files["img"]
                img_bytes = file.read()
                pil_img = Image.open(BytesIO(img_bytes)).convert("RGB")
                img_input = np.array(pil_img)[:, :, ::-1].copy()
            elif "img" in request.form:
                img_input = decode_base64_image(request.form["img"])
            do_analyze = str(request.form.get("analyze", "true")).lower() == "true"
            custom_threshold = float(request.form.get("threshold", VERIFY_THRESHOLD))

        if img_input is None:
            return jsonify({"success": False, "error": "No image provided. Send 'img' as base64."}), 400

        # Save query image to temp file
        tmp_path = save_temp_image(img_input)

        try:
            # --- 1. Face Recognition (find closest match) ---
            best_identity = "STRANGER"
            best_distance = 1.0
            best_confidence = 0.0
            best_photo = ""

            # Get query embedding
            query_embedding = None
            try:
                query_results = DeepFace.represent(
                    img_path=tmp_path,
                    model_name=MODEL_NAME,
                    detector_backend="skip",  # Already cropped face from browser
                    enforce_detection=False,
                    align=True
                )
                if query_results and len(query_results) > 0:
                    query_embedding = query_results[0]["embedding"]
            except Exception as e:
                logger.warning(f"[find] Could not extract query embedding: {e}")

            if query_embedding is not None:
                # Compare against all registered faces
                for identity_info in identities:
                    person_name = identity_info["name"]
                    person_dir = os.path.join(db_path, person_name)

                    for photo_name in identity_info["photos"]:
                        photo_path = os.path.join(person_dir, photo_name)
                        try:
                            ref_embedding = get_embedding(photo_path, use_cache_path=photo_path)
                            if ref_embedding is not None:
                                # Compute distance
                                if DISTANCE_METRIC == "cosine":
                                    a = np.array(query_embedding)
                                    b = np.array(ref_embedding)
                                    dist = 1 - np.dot(a, b) / (np.linalg.norm(a) * np.linalg.norm(b) + 1e-10)
                                elif DISTANCE_METRIC == "euclidean_l2":
                                    a = np.array(query_embedding)
                                    b = np.array(ref_embedding)
                                    a = a / (np.linalg.norm(a) + 1e-10)
                                    b = b / (np.linalg.norm(b) + 1e-10)
                                    dist = float(np.linalg.norm(a - b))
                                else:  # euclidean
                                    dist = float(np.linalg.norm(
                                        np.array(query_embedding) - np.array(ref_embedding)
                                    ))

                                if dist < best_distance:
                                    best_distance = dist
                                    best_identity = person_name
                                    best_photo = photo_path
                        except Exception as e:
                            logger.warning(f"[find] Error comparing with {photo_path}: {e}")

                # Determine threshold
                # Default thresholds per model (cosine metric)
                default_thresholds = {
                    "VGG-Face": 0.40, "Facenet": 0.40, "Facenet512": 0.30,
                    "ArcFace": 0.68, "Dlib": 0.07, "SFace": 0.593,
                    "OpenFace": 0.10, "DeepFace": 0.23, "DeepID": 0.015,
                    "GhostFaceNet": 0.65, "Buffalo_L": 0.68
                }
                threshold = custom_threshold if custom_threshold > 0 else default_thresholds.get(MODEL_NAME, 0.50)

                if best_distance <= threshold:
                    # Confidence: map distance [0, threshold] to confidence [100%, 75%]
                    ratio = max(0, 1 - (best_distance / threshold))
                    best_confidence = round(75 + (ratio * 24.6), 1)  # Range: 75% - 99.6%
                else:
                    best_identity = "STRANGER"
                    best_confidence = 0.0

            # --- 2. Facial Attribute Analysis (optional) ---
            attributes = {}
            if do_analyze:
                try:
                    analysis = DeepFace.analyze(
                        img_path=tmp_path,
                        actions=["age", "gender", "emotion"],
                        detector_backend="skip",  # Already cropped face
                        enforce_detection=False,
                        silent=True
                    )
                    if analysis and len(analysis) > 0:
                        a = analysis[0]
                        attributes = {
                            "age": a.get("age"),
                            "gender": a.get("dominant_gender", "Unknown"),
                            "dominant_emotion": a.get("dominant_emotion", "neutral"),
                            "emotion": a.get("emotion", {})
                        }
                except Exception as e:
                    logger.warning(f"[find] Attribute analysis failed: {e}")

            return jsonify({
                "success": True,
                "identity": best_identity,
                "distance": round(best_distance, 4),
                "confidence": best_confidence,
                "threshold": threshold,
                "model": MODEL_NAME,
                "metric": DISTANCE_METRIC,
                **attributes
            })

        finally:
            try:
                os.unlink(tmp_path)
            except OSError:
                pass

    except Exception as e:
        logger.error(f"[find] Error: {e}\n{traceback.format_exc()}")
        return jsonify({"success": False, "error": str(e)}), 500


@app.route("/api/deepface/register", methods=["POST"])
def register_face():
    """
    Register a new face identity by saving photo(s) to the database directory.

    Accepts (form data):
      - name: person's name (used as directory name)
      - img: base64 image or file upload
      - OR img_path: path to existing photo file on disk

    Returns:
      - success: bool
      - identity: registered name
      - photo_path: saved file path
    """
    try:
        name = request.form.get("name", "").strip()
        if not name:
            return jsonify({"success": False, "error": "Name is required."}), 400

        # Sanitize name for directory usage
        safe_name = "".join(c for c in name if c.isalnum() or c in (" ", "-", "_")).strip()
        if not safe_name:
            return jsonify({"success": False, "error": "Invalid name."}), 400

        db_path = ensure_db_path()
        person_dir = os.path.join(db_path, safe_name)
        os.makedirs(person_dir, exist_ok=True)

        # Generate unique filename
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        filename = f"face_{timestamp}.jpg"
        save_path = os.path.join(person_dir, filename)

        saved = False

        # Handle file upload
        if "img" in request.files:
            file = request.files["img"]
            file.save(save_path)
            saved = True
        elif "img" in request.form:
            b64 = request.form["img"]
            np_img = decode_base64_image(b64)
            import cv2
            cv2.imwrite(save_path, np_img)
            saved = True
        elif "img_path" in request.form:
            src_path = request.form["img_path"]
            if os.path.isfile(src_path):
                import shutil
                shutil.copy2(src_path, save_path)
                saved = True
            else:
                return jsonify({"success": False, "error": f"Source file not found: {src_path}"}), 404

        if not saved:
            return jsonify({"success": False, "error": "No image provided."}), 400

        # Pre-compute embedding and cache it
        try:
            get_embedding(save_path, use_cache_path=save_path)
            logger.info(f"✅ Registered face for '{safe_name}': {save_path}")
        except Exception as e:
            logger.warning(f"⚠️  Embedding pre-compute failed for {safe_name}: {e}")

        # Clear any cached DeepFace.find pkl data
        pkl_path = os.path.join(db_path, f"ds_model_{MODEL_NAME}_{DETECTOR_BACKEND}.pkl")
        if os.path.isfile(pkl_path):
            try:
                os.unlink(pkl_path)
            except OSError:
                pass

        return jsonify({
            "success": True,
            "identity": safe_name,
            "photo_path": save_path,
            "db_path": db_path
        })

    except Exception as e:
        logger.error(f"[register] Error: {e}\n{traceback.format_exc()}")
        return jsonify({"success": False, "error": str(e)}), 500


@app.route("/api/deepface/clear_cache", methods=["POST"])
def clear_cache():
    """Clear the embedding cache — useful after updating photos."""
    global _embedding_cache
    count = len(_embedding_cache)
    _embedding_cache = {}

    # Also clear DeepFace pkl cache files
    db_path = ensure_db_path()
    pkl_cleared = 0
    for f in os.listdir(db_path):
        if f.endswith(".pkl"):
            try:
                os.unlink(os.path.join(db_path, f))
                pkl_cleared += 1
            except OSError:
                pass

    logger.info(f"🗑️  Cleared {count} cached embeddings and {pkl_cleared} pkl files.")
    return jsonify({
        "success": True,
        "embeddings_cleared": count,
        "pkl_files_cleared": pkl_cleared
    })


# ---------------------------------------------------------------------------
# Entry Point
# ---------------------------------------------------------------------------

if __name__ == "__main__":
    logger.info("=" * 65)
    logger.info("🚀 LOEWIX CCTV AI VISION — DeepFace Recognition Server")
    logger.info("=" * 65)
    logger.info(f"   Model       : {MODEL_NAME}")
    logger.info(f"   Detector    : {DETECTOR_BACKEND}")
    logger.info(f"   Metric      : {DISTANCE_METRIC}")
    logger.info(f"   DB Path     : {DB_PATH}")
    logger.info(f"   Server      : http://{HOST}:{PORT}")
    logger.info("=" * 65)

    # Ensure DB directory exists
    ensure_db_path()

    # Pre-load models in background
    try:
        get_deepface()
    except Exception as e:
        logger.warning(f"⚠️  Pre-loading notice: {e}")

    app.run(host=HOST, port=PORT, debug=False, threaded=True)
