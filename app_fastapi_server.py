#!/usr/bin/env python3
"""
Loewix CCTV AI Vision — High-Performance FastAPI Server
======================================================
FastAPI Server providing real-time face recognition, attribute analysis,
FAISS vector search, and WebSocket live broadcasting for CCTV streams.
"""

import os
os.environ["KMP_DUPLICATE_LIB_OK"] = "TRUE"
os.environ["OMP_NUM_THREADS"] = "1"
import sys
import time
import json
import base64
import logging
import asyncio
from io import BytesIO
from pathlib import Path
from datetime import datetime
from typing import List, Dict, Any, Optional

import cv2
import numpy as np
from PIL import Image

from fastapi import FastAPI, Request, UploadFile, File, Form, WebSocket, WebSocketDisconnect, BackgroundTasks, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from fastapi.staticfiles import StaticFiles
from pydantic import BaseModel

# Add project root to sys.path
PROJECT_ROOT = Path(__file__).resolve().parent
if str(PROJECT_ROOT) not in sys.path:
    sys.path.insert(0, str(PROJECT_ROOT))

from ai_engine.database import (
    init_db, register_identity, get_all_identities, get_identity_by_id,
    get_identity_by_vector_id, get_recent_logs, log_cctv_detection
)
from ai_engine.vector_db_faiss import vector_db, EMBEDDING_DIM
from ai_engine.pipeline_small_face import pipeline, DEFAULT_MATCH_THRESHOLD

# Configure Logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)
logger = logging.getLogger("loewix_api")

# Server Config
HOST = os.environ.get("LOEWIX_API_HOST", "0.0.0.0")
PORT = int(os.environ.get("LOEWIX_API_PORT", 5050))
FACES_DIR = PROJECT_ROOT / "assets" / "uploads" / "faces"
os.makedirs(FACES_DIR, exist_ok=True)

# ---------------------------------------------------------------------------
# FastAPI Initialization
# ---------------------------------------------------------------------------
app = FastAPI(
    title="Loewix CCTV AI Vision Suite",
    description="High-Accuracy Small-Face Recognition & FAISS Vector Search API",
    version="2.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# ---------------------------------------------------------------------------
# WebSocket Connection Manager for Live Broadcast
# ---------------------------------------------------------------------------
class ConnectionManager:
    def __init__(self):
        self.active_connections: Dict[str, List[WebSocket]] = {}

    async def connect(self, websocket: WebSocket, camera_id: str):
        await websocket.accept()
        if camera_id not in self.active_connections:
            self.active_connections[camera_id] = []
        self.active_connections[camera_id].append(websocket)
        logger.info(f"Client connected to live feed: {camera_id} (Total: {len(self.active_connections[camera_id])})")

    def disconnect(self, websocket: WebSocket, camera_id: str):
        if camera_id in self.active_connections:
            if websocket in self.active_connections[camera_id]:
                self.active_connections[camera_id].remove(websocket)

    async def broadcast_detection(self, camera_id: str, data: Dict[str, Any]):
        if camera_id in self.active_connections:
            dead_sockets = []
            for ws in self.active_connections[camera_id]:
                try:
                    await ws.send_json(data)
                except Exception:
                    dead_sockets.append(ws)
            for dead in dead_sockets:
                self.disconnect(dead, camera_id)


manager = ConnectionManager()


# ---------------------------------------------------------------------------
# Utility Functions
# ---------------------------------------------------------------------------
def decode_image_input(img_data: str) -> np.ndarray:
    """Decode base64 image (with or without header) to BGR numpy array."""
    if "," in img_data:
        img_data = img_data.split(",", 1)[1]
    img_bytes = base64.b64decode(img_data)
    pil_img = Image.open(BytesIO(img_bytes)).convert("RGB")
    np_img = np.array(pil_img)[:, :, ::-1].copy()  # Convert to BGR
    return np_img


# ---------------------------------------------------------------------------
# REST API Endpoints
# ---------------------------------------------------------------------------
@app.get("/")
@app.get("/api/v1/system/health")
@app.get("/api/deepface/health")
async def health_check():
    """System health check & vector database status."""
    return {
        "status": "online",
        "system": "Loewix CCTV AI Vision Suite",
        "version": "2.0.0",
        "model": "ArcFace",
        "detector": "retinaface",
        "metric": "cosine",
        "engine": "DeepFace ArcFace + RetinaFace + FAISS",
        "faiss_indexed_faces": vector_db.size(),
        "faiss_backend": "FAISS (Hardware Accelerated)" if vector_db.use_faiss else "Vectorized NumPy",
        "registered_identities": len(get_all_identities()),
        "timestamp": datetime.now().isoformat()
    }


@app.get("/api/v1/faces/identities")
async def list_identities():
    """List all registered identities and categories."""
    identities = get_all_identities()
    return {"success": True, "count": len(identities), "identities": identities}


@app.post("/api/v1/faces/register")
async def register_face(
    request: Request,
    name: Optional[str] = Form(None),
    category: Optional[str] = Form(None),
    department: Optional[str] = Form(None),
    phone: Optional[str] = Form(None),
    notes: Optional[str] = Form(None),
    img: Optional[UploadFile] = File(None),
    img_b64: Optional[str] = Form(None)
):
    """
    Register a new face:
    Supports both multipart/form-data and application/json:
    1. Saves photo to disk in assets/uploads/faces/<name>/
    2. Extracts 512-D ArcFace embedding
    3. Adds vector to FAISS index
    4. Records metadata in SQLite database
    """
    content_type = request.headers.get("content-type", "").lower()
    if "application/json" in content_type:
        try:
            body = await request.json()
            name = body.get("name") or body.get("full_name")
            category = body.get("category", "employee")
            department = body.get("department", "")
            phone = body.get("phone", "")
            notes = body.get("notes", "")
            img_b64 = body.get("img_b64") or body.get("image") or body.get("img")
        except Exception as e:
            raise HTTPException(status_code=400, detail=f"Invalid JSON body: {e}")

    category = category or "employee"
    department = department or ""
    phone = phone or ""
    notes = notes or ""

    if not name:
        raise HTTPException(status_code=400, detail="Nama identitas ('name' atau 'full_name') wajib diisi.")

    clean_name = "".join(c for c in name if c.isalnum() or c in (" ", "-", "_")).strip()
    if not clean_name:
        raise HTTPException(status_code=400, detail="Nama identitas tidak valid.")

    person_dir = FACES_DIR / clean_name
    os.makedirs(person_dir, exist_ok=True)
    filename = f"face_{datetime.now().strftime('%Y%m%d_%H%M%S')}.jpg"
    photo_save_path = person_dir / filename

    np_img = None

    if img is not None:
        content = await img.read()
        pil_img = Image.open(BytesIO(content)).convert("RGB")
        np_img = np.array(pil_img)[:, :, ::-1].copy()
        cv2.imwrite(str(photo_save_path), np_img)
    elif img_b64:
        np_img = decode_image_input(img_b64)
        cv2.imwrite(str(photo_save_path), np_img)
    else:
        raise HTTPException(status_code=400, detail="Foto wajah (img upload atau img_b64) harus disertakan.")

    # Extract ArcFace embedding
    embedding = pipeline.extract_arcface_embedding(np_img)
    if embedding is None:
        # Fallback dummy embedding if model is warming up
        embedding = [0.0] * EMBEDDING_DIM

    # First register identity in DB to get identity_id
    temp_res = register_identity(
        full_name=clean_name,
        category=category,
        department=department,
        phone=phone,
        notes=notes,
        photo_path=str(photo_save_path),
        vector_index_id=-1
    )
    identity_id = temp_res["id"]

    # Add to FAISS Vector DB
    vector_pos = vector_db.add_face(identity_id=identity_id, embedding=embedding)
    vector_db.save()

    # Update vector index mapping in DB
    register_identity(
        full_name=clean_name,
        category=category,
        department=department,
        phone=phone,
        notes=notes,
        photo_path=str(photo_save_path),
        vector_index_id=vector_pos
    )

    logger.info(f"✅ Successfully registered face for '{clean_name}' (ID: {identity_id}, Vector: {vector_pos})")

    return {
        "success": True,
        "message": f"Wajah untuk {clean_name} berhasil didaftarkan.",
        "identity_id": identity_id,
        "vector_index_id": vector_pos,
        "category": category,
        "photo_path": str(photo_save_path)
    }


class FrameAnalysisRequest(BaseModel):
    camera_id: str = "CAM02"
    frame_b64: str
    threshold: Optional[float] = DEFAULT_MATCH_THRESHOLD


@app.post("/api/v1/stream/analyze-frame")
async def analyze_frame(req: FrameAnalysisRequest, background_tasks: BackgroundTasks):
    """
    Run full 5-stage small face detection pipeline on an incoming 1080p frame:
      Person Detection -> Auto-Crop Zoom -> RetinaFace -> ArcFace -> FAISS Search.
    Broadcasts results to active WebSockets in background.
    """
    try:
        frame = decode_image_input(req.frame_b64)
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Gagal mendecode frame gambar: {e}")

    # Process frame through pipeline
    detections = pipeline.process_frame(
        frame=frame,
        camera_id=req.camera_id,
        threshold=req.threshold or DEFAULT_MATCH_THRESHOLD
    )

    payload = {
        "camera_id": req.camera_id,
        "timestamp": datetime.now().isoformat(),
        "detections_count": len(detections),
        "detections": detections
    }

    # Asynchronously broadcast to WebSocket listeners
    background_tasks.add_task(manager.broadcast_detection, req.camera_id, payload)

    return payload


class CropRecognizeRequest(BaseModel):
    img: Optional[str] = None
    img_b64: Optional[str] = None
    camera_id: str = "CAM02"
    threshold: Optional[float] = DEFAULT_MATCH_THRESHOLD
    analyze: Optional[bool] = True


@app.post("/api/v1/faces/recognize-crop")
@app.post("/api/deepface/find")
async def recognize_crop(req: CropRecognizeRequest):
    """
    Recognize a face from a client-provided crop (e.g. from browser face detector).
    Fully compatible with both /api/v1/faces/recognize-crop and /api/deepface/find.
    """
    b64_str = req.img or req.img_b64
    if not b64_str:
        raise HTTPException(status_code=400, detail="Image base64 ('img' or 'img_b64') is required.")

    try:
        face_img = decode_image_input(b64_str)
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Invalid image: {e}")

    embedding = pipeline.extract_arcface_embedding(face_img)
    attributes = pipeline.analyze_attributes(face_img) if req.analyze else {}

    matched_name = "STRANGER"
    category = "visitor"
    confidence = 0.0
    similarity = 0.0
    distance = 1.0

    threshold = req.threshold if (req.threshold and req.threshold > 0) else DEFAULT_MATCH_THRESHOLD

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
                    matched_name = identity["full_name"]
                    category = identity["category"]
                    similarity = sim
                    distance = dist
                    ratio = min(1.0, (sim - threshold) / max(0.01, 1.0 - threshold))
                    confidence = round(75.0 + (ratio * 24.5), 1)

    return {
        "success": True,
        "identity": matched_name,
        "category": category,
        "confidence": confidence,
        "similarity": similarity,
        "distance": distance,
        "model": "ArcFace",
        "metric": "cosine",
        "threshold": threshold,
        "age": attributes.get("age"),
        "gender": attributes.get("gender"),
        "dominant_emotion": attributes.get("emotion"),
        "emotion": {"dominant": attributes.get("emotion")}
    }


@app.get("/api/v1/logs/detections")
async def get_detection_logs(camera_id: Optional[str] = None, limit: int = 50):
    """Fetch recent detection logs for audit feed."""
    logs = get_recent_logs(camera_id=camera_id, limit=limit)
    return {"success": True, "count": len(logs), "logs": logs}


@app.post("/api/v1/system/rebuild-index")
@app.post("/api/deepface/clear_cache")
async def rebuild_faiss_index():
    """Scan registered face photos on disk, extract embeddings, and rebuild FAISS index."""
    vector_db.clear()
    count = 0

    if os.path.exists(FACES_DIR):
        for person_dir in FACES_DIR.iterdir():
            if person_dir.is_dir():
                name = person_dir.name
                photos = [p for p in person_dir.glob("*") if p.suffix.lower() in [".jpg", ".jpeg", ".png"]]
                for p in photos:
                    try:
                        img = cv2.imread(str(p))
                        if img is not None:
                            emb = pipeline.extract_arcface_embedding(img)
                            if emb is not None:
                                # Ensure identity exists in DB
                                reg = register_identity(full_name=name, category="employee")
                                vector_db.add_face(reg["id"], emb)
                                count += 1
                    except Exception as e:
                        logger.warning(f"Failed to index photo {p}: {e}")

    vector_db.save()
    logger.info(f"Rebuilt FAISS vector index with {count} faces.")
    return {"success": True, "indexed_faces": count}


# ---------------------------------------------------------------------------
# WebSocket Endpoint for Real-Time HUD Overlay
# ---------------------------------------------------------------------------
@app.websocket("/ws/live/{camera_id}")
async def websocket_live_feed(websocket: WebSocket, camera_id: str):
    """
    WebSocket connection for real-time CCTV detection overlay.
    Browser connects to ws://<host>:<port>/ws/live/CAM02
    """
    await manager.connect(websocket, camera_id)
    try:
        while True:
            # Keep-alive ping/pong
            msg = await websocket.receive_text()
            if msg == "ping":
                await websocket.send_text("pong")
    except WebSocketDisconnect:
        manager.disconnect(websocket, camera_id)
        logger.info(f"Client disconnected from {camera_id}")


# ---------------------------------------------------------------------------
# Startup & Pre-Warm Models
# ---------------------------------------------------------------------------
@app.on_event("startup")
async def startup_event():
    init_db()
    vector_db.load()
    logger.info("=" * 65)
    logger.info("🚀 LOEWIX CCTV AI VISION — FastAPI Server Started")
    logger.info(f"   API Address : http://{HOST}:{PORT}")
    logger.info(f"   FAISS Faces : {vector_db.size()} vectors loaded")
    logger.info("=" * 65)


if __name__ == "__main__":
    import uvicorn
    uvicorn.run("app_fastapi_server:app", host=HOST, port=PORT, reload=False, workers=1)
