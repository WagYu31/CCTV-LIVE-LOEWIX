#!/usr/bin/env python3
"""
Loewix CCTV AI Vision — Relational Database Management
======================================================
Stores identities, registration photos, detection audit logs, and attendance events.
Default: SQLite (file-based: data/loewix_ai.db) with automatic fallback/migration to PostgreSQL.
"""

import os
import json
import sqlite3
import logging
from pathlib import Path
from datetime import datetime
from typing import List, Dict, Any, Optional

logger = logging.getLogger("loewix_db")

PROJECT_ROOT = Path(__file__).resolve().parent.parent
DB_PATH = os.environ.get("LOEWIX_DB_PATH", str(PROJECT_ROOT / "data" / "loewix_ai.db"))


def get_db_connection():
    """Create a thread-safe connection to the SQLite database with row-as-dict support."""
    os.makedirs(os.path.dirname(DB_PATH), exist_ok=True)
    conn = sqlite3.connect(DB_PATH, timeout=10.0, check_same_thread=False)
    conn.row_factory = sqlite3.Row
    return conn


def init_db():
    """Initialize database tables and indexes."""
    conn = get_db_connection()
    cur = conn.cursor()

    # 1. Registered Identities
    cur.execute("""
    CREATE TABLE IF NOT EXISTS registered_identities (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        identity_code TEXT UNIQUE NOT NULL,
        full_name TEXT NOT NULL,
        category TEXT NOT NULL DEFAULT 'visitor', -- employee, vip, resident, blacklist, visitor
        department TEXT DEFAULT '',
        phone TEXT DEFAULT '',
        notes TEXT DEFAULT '',
        is_active INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    """)

    # 2. Face Embeddings Metadata
    cur.execute("""
    CREATE TABLE IF NOT EXISTS face_embeddings_meta (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        identity_id INTEGER NOT NULL,
        photo_path TEXT NOT NULL,
        vector_index_id INTEGER NOT NULL,
        quality_score REAL DEFAULT 1.0,
        pitch_angle REAL DEFAULT 0.0,
        yaw_angle REAL DEFAULT 0.0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (identity_id) REFERENCES registered_identities(id) ON DELETE CASCADE
    );
    """)

    # 3. Detection Audit Logs
    cur.execute("""
    CREATE TABLE IF NOT EXISTS cctv_detection_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        camera_id TEXT NOT NULL,
        identity_id INTEGER,
        matched_name TEXT NOT NULL DEFAULT 'STRANGER',
        category TEXT NOT NULL DEFAULT 'visitor',
        similarity_score REAL NOT NULL,
        distance REAL NOT NULL,
        bounding_box_json TEXT NOT NULL,
        estimated_age INTEGER,
        detected_gender TEXT,
        detected_emotion TEXT,
        snapshot_path TEXT,
        full_frame_path TEXT,
        is_alert_triggered INTEGER DEFAULT 0,
        detected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (identity_id) REFERENCES registered_identities(id) ON DELETE SET NULL
    );
    """)

    # Indexes for fast querying
    cur.execute("CREATE INDEX IF NOT EXISTS idx_detection_time ON cctv_detection_logs(detected_at);")
    cur.execute("CREATE INDEX IF NOT EXISTS idx_detection_camera ON cctv_detection_logs(camera_id);")
    cur.execute("CREATE INDEX IF NOT EXISTS idx_detection_identity ON cctv_detection_logs(identity_id);")
    cur.execute("CREATE INDEX IF NOT EXISTS idx_identity_name ON registered_identities(full_name);")

    conn.commit()
    conn.close()
    logger.info(f"✅ Loewix AI Database initialized at: {DB_PATH}")


def register_identity(
    full_name: str,
    category: str = "employee",
    department: str = "",
    phone: str = "",
    notes: str = "",
    photo_path: str = "",
    vector_index_id: int = -1
) -> Dict[str, Any]:
    """Register or update a person in the database."""
    conn = get_db_connection()
    cur = conn.cursor()

    # Generate identity code if new
    timestamp = datetime.now().strftime("%Y%m%d%H%M%S")
    clean_name = "".join(c for c in full_name if c.isalnum()).upper()[:8]
    identity_code = f"ID_{clean_name}_{timestamp}"

    # Check if name already exists
    cur.execute("SELECT id FROM registered_identities WHERE LOWER(full_name) = LOWER(?)", (full_name.strip(),))
    row = cur.fetchone()

    if row:
        identity_id = row["id"]
        cur.execute("""
            UPDATE registered_identities 
            SET category = ?, department = ?, phone = ?, notes = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        """, (category, department, phone, notes, identity_id))
    else:
        cur.execute("""
            INSERT INTO registered_identities (identity_code, full_name, category, department, phone, notes)
            VALUES (?, ?, ?, ?, ?, ?)
        """, (identity_code, full_name.strip(), category, department, phone, notes))
        identity_id = cur.lastrowid

    if photo_path and vector_index_id >= 0:
        cur.execute("""
            INSERT INTO face_embeddings_meta (identity_id, photo_path, vector_index_id)
            VALUES (?, ?, ?)
        """, (identity_id, photo_path, vector_index_id))

    conn.commit()
    conn.close()

    return {
        "id": identity_id,
        "full_name": full_name,
        "category": category,
        "department": department,
        "vector_index_id": vector_index_id
    }


def get_all_identities() -> List[Dict[str, Any]]:
    """Return all active registered identities with their photo count and vector info."""
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("""
        SELECT r.*, COUNT(m.id) as photo_count, GROUP_CONCAT(m.vector_index_id) as vector_ids
        FROM registered_identities r
        LEFT JOIN face_embeddings_meta m ON r.id = m.identity_id
        WHERE r.is_active = 1
        GROUP BY r.id
        ORDER BY r.full_name ASC
    """)
    rows = [dict(r) for r in cur.fetchall()]
    conn.close()
    return rows


def get_identity_by_id(identity_id: int) -> Optional[Dict[str, Any]]:
    """Lookup person directly by their registered_identities.id."""
    try:
        identity_id = int(identity_id)
    except (ValueError, TypeError):
        return None
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("SELECT * FROM registered_identities WHERE id = ? LIMIT 1", (identity_id,))
    row = cur.fetchone()
    conn.close()
    return dict(row) if row else None


def get_identity_by_vector_id(id_val: int) -> Optional[Dict[str, Any]]:
    """Lookup person details from their FAISS identity_id or vector index id."""
    try:
        id_val = int(id_val)
    except (ValueError, TypeError):
        return None
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("""
        SELECT r.*, m.photo_path
        FROM registered_identities r
        LEFT JOIN face_embeddings_meta m ON r.id = m.identity_id
        WHERE r.id = ? OR m.vector_index_id = ?
        LIMIT 1
    """, (id_val, id_val))
    row = cur.fetchone()
    conn.close()
    return dict(row) if row else None


def log_cctv_detection(
    camera_id: str,
    identity_id: Optional[int],
    matched_name: str,
    category: str,
    similarity_score: float,
    distance: float,
    bounding_box: Dict[str, Any],
    age: Optional[int] = None,
    gender: Optional[str] = None,
    emotion: Optional[str] = None,
    snapshot_path: Optional[str] = None,
    full_frame_path: Optional[str] = None,
    is_alert: bool = False
) -> int:
    """Record a detection event in the audit log."""
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("""
        INSERT INTO cctv_detection_logs (
            camera_id, identity_id, matched_name, category, similarity_score, distance,
            bounding_box_json, estimated_age, detected_gender, detected_emotion,
            snapshot_path, full_frame_path, is_alert_triggered
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    """, (
        camera_id, identity_id, matched_name, category,
        round(similarity_score, 4), round(distance, 4),
        json.dumps(bounding_box), age, gender, emotion,
        snapshot_path, full_frame_path, 1 if is_alert else 0
    ))
    log_id = cur.lastrowid
    conn.commit()
    conn.close()
    return log_id


def get_recent_logs(camera_id: Optional[str] = None, limit: int = 50) -> List[Dict[str, Any]]:
    """Retrieve recent detection logs for frontend live feed."""
    conn = get_db_connection()
    cur = conn.cursor()
    if camera_id:
        cur.execute("""
            SELECT * FROM cctv_detection_logs 
            WHERE camera_id = ? 
            ORDER BY id DESC LIMIT ?
        """, (camera_id, limit))
    else:
        cur.execute("""
            SELECT * FROM cctv_detection_logs 
            ORDER BY id DESC LIMIT ?
        """, (limit,))
    rows = [dict(r) for r in cur.fetchall()]
    conn.close()
    return rows


# Run initialization on import
init_db()
