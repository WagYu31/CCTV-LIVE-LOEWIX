#!/usr/bin/env python3
"""
Loewix CCTV AI Vision — Hierarchical Small-Face Detection & Recognition Pipeline
================================================================================
Specialized for long-distance, high-angle CCTV cameras (such as CAM02 Showroom).

Stages:
  1. Full-Body Person Localization (YOLOv8 / Background ROI)
  2. Dynamic High-Res Head Crop & Digital Zoom
  3. Precision Face Detection & 5-Point Landmark Alignment (RetinaFace / YuNet)
  4. ArcFace 512-D Embedding Extraction (DeepFace)
  5. Sub-Millisecond Vector Search (FAISS)
"""

import os
os.environ["KMP_DUPLICATE_LIB_OK"] = "TRUE"
os.environ["OMP_NUM_THREADS"] = "1"
import cv2
import time
import logging
import numpy as np
from pathlib import Path
from typing import List, Dict, Any, Optional, Tuple

try:
    import torch
    torch.set_num_threads(1)
except ImportError:
    pass

cv2.setNumThreads(1)

from ai_engine.vector_db_faiss import vector_db
from ai_engine.database import get_identity_by_id, get_identity_by_vector_id, log_cctv_detection

logger = logging.getLogger("loewix_pipeline")
PROJECT_ROOT = Path(__file__).resolve().parent.parent

# ArcFace Cosine Distance Threshold (Distance <= 0.32 <=> Similarity >= 0.68)
DEFAULT_MATCH_THRESHOLD = float(os.environ.get("LOEWIX_MATCH_THRESHOLD", 0.68))


class SmallFaceRecognitionPipeline:
    """End-to-End Hierarchical Small Face Recognition Engine."""

    def __init__(self):
        self.yolo_model = None
        self.yunet_detector = None
        self.deepface_module = None
        self.models_loaded = False
        self._init_models()

    def _init_models(self):
        """Initialize models lazily."""
        # 1. OpenCV YuNet Face Detector (Built-in to OpenCV 4.5.4+, ultra-fast CNN with 5 landmarks)
        try:
            model_path = os.environ.get("YUNET_MODEL_PATH", "")
            if not model_path or not os.path.exists(model_path):
                # Search common locations or project root
                candidates = [
                    Path(__file__).parent / "face_detection_yunet_2023mar.onnx",
                    Path(__file__).parent.parent / "face_detection_yunet_2023mar.onnx",
                    Path("/tmp/face_detection_yunet_2023mar.onnx")
                ]
                for c in candidates:
                    if c.exists():
                        model_path = str(c)
                        break

            if model_path and os.path.exists(model_path):
                self.yunet_detector = cv2.FaceDetectorYN.create(
                    model=model_path,
                    config="",
                    input_size=(320, 320),
                    score_threshold=0.30,
                    nms_threshold=0.3,
                    top_k=5000
                )
                logger.info(f"✅ YuNet small-face detector loaded from {model_path}")
        except Exception as e:
            logger.warning(f"ℹ️ YuNet init notice: {e}")

        # 2. YOLOv8 Person Detector
        try:
            from ultralytics import YOLO
            self.yolo_model = YOLO("yolov8n.pt")
            logger.info("✅ YOLOv8 Person Detector loaded successfully.")
        except Exception as e:
            logger.warning(f"ℹ️ YOLOv8 not loaded yet: {e}. Will use sliding window & YuNet.")

    def get_deepface(self):
        """Lazy load DeepFace module."""
        if self.deepface_module is None:
            try:
                from deepface import DeepFace
                self.deepface_module = DeepFace
                logger.info("✅ DeepFace module loaded.")
            except ImportError:
                logger.warning("⚠️ DeepFace not yet installed in active environment.")
        return self.deepface_module

    # -----------------------------------------------------------------------
    # Tahap 1: Person Localization
    # -----------------------------------------------------------------------
    def detect_persons(self, frame: np.ndarray) -> List[Tuple[int, int, int, int]]:
        """
        Detect human bodies in the full-frame image.
        Returns bounding boxes as [(x, y, w, h), ...]
        """
        h, w = frame.shape[:2]
        persons = []

        if self.yolo_model is not None:
            try:
                results = self.yolo_model(frame, classes=[0], verbose=False, conf=0.35)
                for r in results:
                    boxes = r.boxes.xyxy.cpu().numpy()
                    for box in boxes:
                        x1, y1, x2, y2 = map(int, box)
                        pw = max(1, x2 - x1)
                        ph = max(1, y2 - y1)
                        persons.append((x1, y1, pw, ph))
                return persons
            except Exception as e:
                logger.warning(f"YOLO person detection error: {e}")

        # Fallback: Treat full frame or upper sections as potential search regions
        return [(0, 0, w, h)]

    # -----------------------------------------------------------------------
    # Tahap 2: Auto-Crop & Digital Zoom ROI
    # -----------------------------------------------------------------------
    def extract_and_zoom_head_roi(
        self,
        full_frame: np.ndarray,
        person_box: Tuple[int, int, int, int]
    ) -> Tuple[np.ndarray, Tuple[int, int, int, int]]:
        """
        Crop the top 35% of a detected person (head & shoulders) directly from
        the native high-resolution frame, then apply enhancement.
        """
        frame_h, frame_w = full_frame.shape[:2]
        px, py, pw, ph = person_box

        # If person box is the full frame (fallback), return it directly
        if pw >= frame_w and ph >= frame_h:
            return full_frame, (0, 0, frame_w, frame_h)

        # Dynamic margins (25% horizontal padding, 15% top padding)
        pad_x = int(pw * 0.25)
        pad_y = int(ph * 0.15)

        hx1 = max(0, px - pad_x)
        hy1 = max(0, py - pad_y)
        hx2 = min(frame_w, px + pw + pad_x)
        # Head is roughly top 38% of body
        hy2 = min(frame_h, py + int(ph * 0.38) + pad_y)

        crop = full_frame[hy1:hy2, hx1:hx2].copy()
        crop_h, crop_w = crop.shape[:2]

        if crop_h < 10 or crop_w < 10:
            return crop, (hx1, hy1, crop_w, crop_h)

        # Digital Zoom & Enhancement:
        # If crop is small (< 160px), upscale with Bicubic interpolation
        # and enhance contrast using CLAHE (Contrast Limited Adaptive Histogram Equalization)
        if crop_w < 160 or crop_h < 160:
            scale = max(2.0, 160.0 / max(crop_w, crop_h))
            new_w = int(crop_w * scale)
            new_h = int(crop_h * scale)
            crop_zoomed = cv2.resize(crop, (new_w, new_h), interpolation=cv2.INTER_CUBIC)

            # CLAHE on L channel
            lab = cv2.cvtColor(crop_zoomed, cv2.COLOR_BGR2LAB)
            l, a, b = cv2.split(lab)
            clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8))
            cl = clahe.apply(l)
            enhanced = cv2.cvtColor(cv2.merge((cl, a, b)), cv2.COLOR_LAB2BGR)
            return enhanced, (hx1, hy1, crop_w, crop_h)

        return crop, (hx1, hy1, crop_w, crop_h)

    # -----------------------------------------------------------------------
    # Tahap 3: Precision Face Detection on Zoomed Crop
    # -----------------------------------------------------------------------
    def detect_face_in_crop(self, crop: np.ndarray) -> Optional[Dict[str, Any]]:
        """
        Detect face inside the zoomed crop.
        Returns { 'box': (x, y, w, h), 'landmarks': [...], 'aligned_face': np.ndarray }
        """
        h, w = crop.shape[:2]

        # Method A: OpenCV YuNet if available
        if self.yunet_detector is not None:
            self.yunet_detector.setInputSize((w, h))
            _, faces = self.yunet_detector.detect(crop)
            if faces is not None and len(faces) > 0:
                f = faces[0]
                fx, fy, fw, fh = map(int, f[:4])
                conf = float(f[14])
                landmarks = f[4:14].reshape((5, 2))
                
                # Crop aligned face
                face_img = crop[max(0, fy):min(h, fy+fh), max(0, fx):min(w, fx+fw)]
                return {
                    "box": (fx, fy, fw, fh),
                    "confidence": conf,
                    "landmarks": landmarks.tolist(),
                    "face_img": face_img
                }

        # Method B: DeepFace internal extraction
        df = self.get_deepface()
        if df is not None:
            try:
                from deepface.modules import detection
                extracted = detection.extract_faces(
                    img_path=crop,
                    detector_backend="retinaface",
                    enforce_detection=False,
                    align=True
                )
                if extracted and len(extracted) > 0:
                    item = extracted[0]
                    fa = item.get("facial_area", {})
                    fx = fa.get("x", 0)
                    fy = fa.get("y", 0)
                    fw = fa.get("w", w)
                    fh = fa.get("h", h)
                    face_arr = (item["face"] * 255).astype(np.uint8)[:, :, ::-1]
                    return {
                        "box": (fx, fy, fw, fh),
                        "confidence": float(item.get("confidence", 0.9)),
                        "landmarks": [],
                        "face_img": face_arr
                    }
            except Exception:
                pass

        # Fallback: Assume the central portion of the head crop is the face
        fx, fy = int(w * 0.15), int(h * 0.1)
        fw, fh = int(w * 0.7), int(h * 0.8)
        return {
            "box": (fx, fy, fw, fh),
            "confidence": 0.5,
            "landmarks": [],
            "face_img": crop[fy:fy+fh, fx:fx+fw]
        }

    # -----------------------------------------------------------------------
    # Tahap 4 & 5: ArcFace Embeddings & FAISS Vector Matching
    # -----------------------------------------------------------------------
    def extract_arcface_embedding(self, face_img: np.ndarray, detect_if_needed: bool = True) -> Optional[List[float]]:
        """
        Extract 512-D ArcFace embedding vector using DeepFace.
        If the image is a full portrait/photo (dimensions > 140px), detects and crops
        the face region first to guarantee clean facial embeddings.
        """
        if face_img is None or face_img.size == 0:
            return None

        df = self.get_deepface()
        if df is None:
            return None

        # Auto-crop face if receiving a raw uncropped photo or portrait
        target_img = face_img
        if detect_if_needed and min(face_img.shape[:2]) > 140:
            face_info = self.detect_face_in_crop(face_img)
            if face_info and face_info.get("face_img") is not None and face_info["face_img"].size > 0:
                target_img = face_info["face_img"]

        try:
            results = df.represent(
                img_path=target_img,
                model_name="ArcFace",
                detector_backend="skip",
                enforce_detection=False,
                align=True
            )
            if results and len(results) > 0:
                return results[0]["embedding"]
        except Exception as e:
            logger.warning(f"ArcFace embedding error: {e}")
        return None

    def analyze_attributes(self, face_img: np.ndarray) -> Dict[str, Any]:
        """Analyze age, gender, and emotion if pre-cached weights exist, otherwise return defaults."""
        weights_dir = Path.home() / ".deepface" / "weights"
        # Only invoke heavy deepface analyze if weights are already cached
        if not (weights_dir / "facial_expression_model_weights.h5").exists():
            return {"age": 28, "gender": "Unknown", "emotion": "neutral"}

        df = self.get_deepface()
        if df is None:
            return {"age": 28, "gender": "Unknown", "emotion": "neutral"}

        try:
            res = df.analyze(
                img_path=face_img,
                actions=["emotion", "gender"],
                detector_backend="skip",
                enforce_detection=False,
                silent=True
            )
            if res and len(res) > 0:
                r = res[0]
                return {
                    "age": int(r.get("age", 28)),
                    "gender": "Laki-laki" if r.get("dominant_gender", "").lower() in ["man", "male"] else "Perempuan",
                    "emotion": r.get("dominant_emotion", "neutral")
                }
        except Exception:
            pass
        return {"age": 28, "gender": "Unknown", "emotion": "neutral"}

    # -----------------------------------------------------------------------
    # Full Frame Pipeline Execution
    # -----------------------------------------------------------------------
    def process_frame(
        self,
        frame: np.ndarray,
        camera_id: str = "CAM02",
        threshold: float = DEFAULT_MATCH_THRESHOLD
    ) -> List[Dict[str, Any]]:
        """
        Execute full multi-scale pipeline on uncompressed CCTV frame:
        1. Full-frame YuNet detection (captures tiny faces down to 8px in background)
        2. YOLOv8 Person-level ROI and head-crop zoom (captures people from afar)
        3. ArcFace 512-D Embedding & FAISS Vector Matching
        """
        start_time = time.time()
        frame_h, frame_w = frame.shape[:2]
        candidate_faces = []  # [{ "box": (x, y, w, h), "person_box": (px, py, pw, ph), "conf": float, "face_crop": np.ndarray }]

        # 1. Method A: High-Sensitivity Small Face Detection via YuNet
        if self.yunet_detector is not None:
            try:
                self.yunet_detector.setInputSize((frame_w, frame_h))
                _, yn_faces = self.yunet_detector.detect(frame)
                if yn_faces is not None:
                    for f in yn_faces:
                        fx, fy, fw, fh = map(int, f[:4])
                        conf = float(f[14])
                        if conf >= 0.35 and fw >= 6 and fh >= 6:
                            # Expand crop slightly (25% margin)
                            pad_x = max(4, int(fw * 0.35))
                            pad_y = max(4, int(fh * 0.35))
                            cx1 = max(0, fx - pad_x)
                            cy1 = max(0, fy - pad_y)
                            cx2 = min(frame_w, fx + fw + pad_x)
                            cy2 = min(frame_h, fy + fh + pad_y)
                            
                            face_crop = frame[cy1:cy2, cx1:cx2].copy()
                            if face_crop.shape[0] >= 6 and face_crop.shape[1] >= 6:
                                candidate_faces.append({
                                    "box": (fx, fy, fw, fh),
                                    "person_box": (cx1, cy1, cx2 - cx1, int((cy2 - cy1) * 2.8)),
                                    "conf": conf,
                                    "face_crop": face_crop,
                                    "source": "yunet_small_face"
                                })
            except Exception as e:
                logger.warning(f"YuNet full-frame detection notice: {e}")

        # 2. Method B: YOLOv8 Person Detection -> Auto Crop & Digital Zoom
        persons = self.detect_persons(frame)
        for person_box in persons:
            px, py, pw, ph = person_box
            if pw >= frame_w and ph >= frame_h and len(candidate_faces) > 0:
                continue  # Skip whole-frame fallback if we already found specific faces

            zoomed_crop, roi_offset = self.extract_and_zoom_head_roi(frame, person_box)
            if zoomed_crop is None or zoomed_crop.size == 0:
                continue

            face_info = self.detect_face_in_crop(zoomed_crop)
            if face_info is not None:
                face_img = face_info["face_img"]
                if face_img is not None and face_img.size > 0:
                    hx, hy, rw, rh = roi_offset
                    scale_x = rw / max(1, zoomed_crop.shape[1])
                    scale_y = rh / max(1, zoomed_crop.shape[0])
                    fx, fy, fw, fh = face_info["box"]
                    gx = int(hx + (fx * scale_x))
                    gy = int(hy + (fy * scale_y))
                    gw = max(8, int(fw * scale_x))
                    gh = max(8, int(fh * scale_y))

                    # Check if already covered by Method A
                    is_dup = False
                    for existing in candidate_faces:
                        ex, ey, ew, eh = existing["box"]
                        if abs(ex - gx) < 20 and abs(ey - gy) < 20:
                            is_dup = True
                            break

                    if not is_dup:
                        candidate_faces.append({
                            "box": (gx, gy, gw, gh),
                            "person_box": person_box,
                            "conf": face_info.get("confidence", 0.75),
                            "face_crop": face_img,
                            "source": "person_zoom"
                        })

        # 3. Recognition & Attribute Analysis for each detected face
        detections = []
        for idx, item in enumerate(candidate_faces):
            gx, gy, gw, gh = item["box"]
            face_img = item["face_crop"]
            person_box = item["person_box"]

            # Digital Zoom & Enhancement for Recognition:
            # Upscale face image if small (< 112px) for ArcFace input
            ch, cw = face_img.shape[:2]
            if cw < 112 or ch < 112:
                scale = max(2.0, 112.0 / max(cw, ch, 1))
                face_zoomed = cv2.resize(face_img, (int(cw * scale), int(ch * scale)), interpolation=cv2.INTER_CUBIC)
            else:
                face_zoomed = face_img

            # Extract 512-D ArcFace embedding
            embedding = self.extract_arcface_embedding(face_zoomed)

            matched_name = "STRANGER"
            category = "visitor"
            confidence = 0.0
            similarity = 0.0
            distance = 1.0
            identity_id = None

            # FAISS Vector Search
            if embedding is not None and vector_db.size() > 0:
                matches = vector_db.search(embedding, top_k=1)
                if matches:
                    best = matches[0]
                    sim = best["similarity"]
                    dist = best["distance"]

                    if sim >= threshold:
                        target_id = int(best["identity_id"])
                        identity = get_identity_by_id(target_id) or get_identity_by_vector_id(target_id)
                        if identity:
                            identity_id = identity["id"]
                            matched_name = identity["full_name"]
                            category = identity["category"]
                            similarity = sim
                            distance = dist
                            ratio = min(1.0, (sim - threshold) / max(0.01, 1.0 - threshold))
                            confidence = round(75.0 + (ratio * 24.5), 1)

            # Analyze attributes
            attributes = self.analyze_attributes(face_zoomed)

            # Save snapshot
            snapshot_rel_path = None
            try:
                snapshots_dir = PROJECT_ROOT / "assets" / "uploads" / "snapshots"
                os.makedirs(snapshots_dir, exist_ok=True)
                snap_filename = f"snap_{camera_id}_{int(time.time()*1000)}_{idx}.jpg"
                snap_full_path = snapshots_dir / snap_filename
                cv2.imwrite(str(snap_full_path), face_zoomed)
                snapshot_rel_path = f"/assets/uploads/snapshots/{snap_filename}"
            except Exception as e:
                logger.warning(f"Could not save snapshot: {e}")

            # Record detection in SQLite database
            log_cctv_detection(
                camera_id=camera_id,
                identity_id=identity_id,
                matched_name=matched_name,
                category=category,
                similarity_score=similarity,
                distance=distance,
                bounding_box={"x": gx, "y": gy, "w": gw, "h": gh},
                age=attributes.get("age"),
                gender=attributes.get("gender"),
                emotion=attributes.get("emotion"),
                snapshot_path=snapshot_rel_path,
                is_alert=(category in ["blacklist", "vip"])
            )

            detections.append({
                "identity": matched_name,
                "category": category,
                "confidence": confidence,
                "similarity": similarity,
                "distance": distance,
                "bounding_box": {"x": gx, "y": gy, "w": gw, "h": gh},
                "person_box": {"x": person_box[0], "y": person_box[1], "w": person_box[2], "h": person_box[3]},
                "attributes": attributes,
                "snapshot_url": snapshot_rel_path,
                "detection_source": item["source"]
            })

        duration_ms = round((time.time() - start_time) * 1000, 1)
        logger.info(f"Processed frame for {camera_id}: {len(detections)} detections in {duration_ms}ms")
        return detections


# Global singleton instance
pipeline = SmallFaceRecognitionPipeline()
