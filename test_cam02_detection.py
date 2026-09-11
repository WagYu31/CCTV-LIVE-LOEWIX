#!/usr/bin/env python3
"""
Loewix CCTV AI Vision — Field Test Script on CAM02 Showroom Image
================================================================
Validates small-face detection, auto-crop & digital zoom, and ArcFace/FAISS matching
on the user's actual CCTV snapshot from CAM02.
"""

import os
import sys
import time
import cv2
import numpy as np
from pathlib import Path

# Add project root to path
PROJECT_ROOT = Path(__file__).resolve().parent
if str(PROJECT_ROOT) not in sys.path:
    sys.path.insert(0, str(PROJECT_ROOT))

from ai_engine.pipeline_small_face import pipeline
from ai_engine.database import init_db, register_identity
from ai_engine.vector_db_faiss import vector_db

TEST_CANDIDATES = [
    PROJECT_ROOT / "assets" / "uploads" / "cam02_showroom_sample.jpg",
    PROJECT_ROOT / "assets" / "uploads" / "cam02_ai_detected.jpg",
    PROJECT_ROOT / "assets" / "image" / "snapshots" / "cam_5001.jpg",
    Path("/Users/wagyua5/.gemini/antigravity-ide/brain/67216c14-cc99-470d-b507-a24b4d5cc5b8/.user_uploaded/media_1789113911917.jpg")
]

TEST_IMAGE_PATH = None
for p in TEST_CANDIDATES:
    if os.path.exists(str(p)):
        TEST_IMAGE_PATH = str(p)
        break

OUTPUT_IMAGE_PATH = PROJECT_ROOT / "assets" / "uploads" / "cam02_ai_detected.jpg"


def run_field_test():
    print("=" * 65)
    print("🔬 LOEWIX CCTV AI VISION — FIELD TEST: CAM02 SHOWROOM FEED")
    print("=" * 65)

    if not TEST_IMAGE_PATH or not os.path.exists(TEST_IMAGE_PATH):
        print("❌ Test image not found in assets/uploads/ or assets/image/")
        return

    # 1. Initialize Database & Register a Sample Staff Member for testing
    init_db()
    print(f"📁 Vector DB size before test: {vector_db.size()} faces")

    # Load test image
    frame = cv2.imread(TEST_IMAGE_PATH)
    if frame is None:
        print("❌ Failed to read test image via OpenCV.")
        return

    h, w = frame.shape[:2]
    print(f"📷 Frame loaded: {w}x{h} px")

    # 2. Run Pipeline
    start_time = time.time()
    detections = pipeline.process_frame(frame, camera_id="CAM02")
    elapsed_ms = (time.time() - start_time) * 1000

    print(f"\n⚡ Processing complete in {elapsed_ms:.1f} ms")
    print(f"🎯 Total detections: {len(detections)}")

    # 3. Draw Results on Output Frame
    vis_frame = frame.copy()

    for idx, det in enumerate(detections):
        identity = det["identity"]
        category = det["category"]
        conf = det["confidence"]
        bbox = det["bounding_box"]
        attr = det.get("attributes", {})
        age = attr.get("age", "?")
        gender = attr.get("gender", "?")

        bx, by, bw, bh = bbox["x"], bbox["y"], bbox["w"], bbox["h"]
        pbox = det.get("person_box", {})

        print(f"\n--- Detection #{idx+1} ---")
        print(f"  Identity    : {identity} ({category.upper()})")
        print(f"  Confidence  : {conf}%")
        print(f"  Face Box    : [x={bx}, y={by}, w={bw}, h={bh}]")
        print(f"  Person Box  : [x={pbox.get('x')}, y={pbox.get('y')}, w={pbox.get('w')}, h={pbox.get('h')}]")
        print(f"  Attributes  : {gender}, ~{age} y.o.")

        # Box Color: Green for employee/whitelist, Orange for stranger
        color = (0, 255, 0) if category in ["employee", "vip"] else (0, 165, 255)

        # Draw Person Box (dashed/thin)
        if pbox:
            px, py, pw, ph = pbox["x"], pbox["y"], pbox["w"], pbox["h"]
            cv2.rectangle(vis_frame, (px, py), (px + pw, py + ph), (180, 180, 180), 1)

        # Draw Face Box
        cv2.rectangle(vis_frame, (bx, by), (bx + bw, by + bh), color, 2)

        # Label Banner
        label = f"{identity} | {conf:.0f}%" if conf > 0 else f"{identity} ({gender})"
        cv2.putText(vis_frame, label, (bx, max(15, by - 8)), cv2.FONT_HERSHEY_SIMPLEX, 0.45, color, 1)

    # Save visualization
    os.makedirs(OUTPUT_IMAGE_PATH.parent, exist_ok=True)
    cv2.imwrite(str(OUTPUT_IMAGE_PATH), vis_frame)
    print(f"\n✅ Annotated test output saved to: {OUTPUT_IMAGE_PATH}")
    print("=" * 65)


if __name__ == "__main__":
    run_field_test()
