#!/usr/bin/env python3
"""
Loewix CCTV AI Vision — FAISS Vector Database Engine
====================================================
Sub-millisecond similarity search for ArcFace 512-dimensional embeddings.
Uses faiss.IndexFlatIP (Inner Product / Cosine Similarity on L2-normalized vectors).
Includes seamless vectorized NumPy fallback if FAISS is not yet installed.
"""

import os
import logging
from pathlib import Path
from typing import List, Tuple, Optional, Dict, Any
import numpy as np

logger = logging.getLogger("loewix_faiss")

# Dimensionality of ArcFace embedding vector
EMBEDDING_DIM = 512

# Default path for persistent FAISS index file
PROJECT_ROOT = Path(__file__).resolve().parent.parent
DEFAULT_INDEX_PATH = os.environ.get(
    "LOEWIX_FAISS_PATH",
    str(PROJECT_ROOT / "assets" / "uploads" / "faces" / "faiss_arcface.index")
)


class VectorDatabase:
    """FAISS Vector Database with high-speed Cosine similarity matching."""

    def __init__(self, dim: int = EMBEDDING_DIM, index_path: str = DEFAULT_INDEX_PATH):
        self.dim = dim
        self.index_path = index_path
        self.use_faiss = False
        self.faiss_module = None
        self.index = None
        self.id_map: List[int] = []  # Maps internal index position -> database identity_id
        self.vectors_mem: List[np.ndarray] = []  # In-memory buffer for numpy fallback / persistence

        self._init_backend()
        self.load()

    def _init_backend(self):
        """Try loading FAISS library, otherwise fall back to vectorized NumPy."""
        try:
            import faiss
            self.faiss_module = faiss
            self.index = faiss.IndexFlatIP(self.dim)
            self.use_faiss = True
            logger.info("⚡ FAISS engine initialized successfully (Hardware acceleration active).")
        except ImportError:
            self.use_faiss = False
            logger.info("ℹ️ FAISS not installed yet. Using high-speed Vectorized NumPy engine.")

    def _normalize(self, v: np.ndarray) -> np.ndarray:
        """L2-normalize vector so Inner Product equals Cosine Similarity."""
        norm = np.linalg.norm(v)
        if norm > 1e-10:
            return v / norm
        return v

    def add_face(self, identity_id: int, embedding: List[float]) -> int:
        """
        Add a 512-D face embedding to the index.
        Returns the index position.
        """
        v = np.array(embedding, dtype=np.float32)
        if v.shape[0] != self.dim:
            raise ValueError(f"Embedding dimension mismatch: expected {self.dim}, got {v.shape[0]}")

        v_norm = self._normalize(v)

        if self.use_faiss and self.index is not None:
            self.index.add(np.expand_dims(v_norm, axis=0))
        
        self.id_map.append(int(identity_id))
        self.vectors_mem.append(v_norm)

        idx_pos = len(self.id_map) - 1
        logger.debug(f"Added face to vector index: ID {identity_id} at position {idx_pos}")
        return idx_pos

    def search(self, query_embedding: List[float], top_k: int = 1) -> List[Dict[str, Any]]:
        """
        Search for the most similar faces in the database.
        Returns list of matches sorted by similarity score descending:
        [{ "identity_id": int, "similarity": float, "distance": float }]
        """
        if len(self.id_map) == 0:
            return []

        v = np.array(query_embedding, dtype=np.float32)
        v_norm = self._normalize(v)

        results = []

        if self.use_faiss and self.index is not None and self.index.ntotal > 0:
            # Query FAISS index
            k = min(top_k, self.index.ntotal)
            sims, idxs = self.index.search(np.expand_dims(v_norm, axis=0), k)
            
            for sim, idx in zip(sims[0], idxs[0]):
                if idx >= 0 and idx < len(self.id_map):
                    similarity = float(sim)
                    distance = float(max(0.0, 1.0 - similarity))
                    results.append({
                        "identity_id": int(self.id_map[idx]),
                        "similarity": round(similarity, 4),
                        "distance": round(distance, 4)
                    })
        else:
            # Vectorized NumPy fallback
            mat = np.array(self.vectors_mem, dtype=np.float32)  # Shape: (N, 512)
            sims = np.dot(mat, v_norm)  # Dot product = Cosine similarity
            
            # Sort top_k descending
            top_indices = np.argsort(sims)[::-1][:top_k]
            for idx in top_indices:
                similarity = float(sims[idx])
                distance = float(max(0.0, 1.0 - similarity))
                results.append({
                    "identity_id": int(self.id_map[idx]),
                    "similarity": round(similarity, 4),
                    "distance": round(distance, 4)
                })

        return results

    def save(self, filepath: Optional[str] = None):
        """Save the vector index and ID mapping to disk."""
        target_path = filepath or self.index_path
        os.makedirs(os.path.dirname(target_path), exist_ok=True)

        meta_path = f"{target_path}.meta.npy"
        vec_path = f"{target_path}.vec.npy"

        # Save ID map and vectors
        np.save(meta_path, np.array(self.id_map, dtype=np.int32))
        if len(self.vectors_mem) > 0:
            np.save(vec_path, np.array(self.vectors_mem, dtype=np.float32))

        # Save native FAISS index if available
        if self.use_faiss and self.index is not None and self.faiss_module is not None:
            self.faiss_module.write_index(self.index, target_path)

        logger.info(f"💾 Saved {len(self.id_map)} face vectors to {target_path}")

    def load(self, filepath: Optional[str] = None) -> bool:
        """Load vector index and ID mapping from disk."""
        target_path = filepath or self.index_path
        meta_path = f"{target_path}.meta.npy"
        vec_path = f"{target_path}.vec.npy"

        if not os.path.exists(meta_path):
            return False

        try:
            self.id_map = [int(x) for x in np.load(meta_path)]
            if os.path.exists(vec_path):
                self.vectors_mem = list(np.load(vec_path))

            if self.use_faiss and os.path.exists(target_path) and self.faiss_module is not None:
                self.index = self.faiss_module.read_index(target_path)
            elif self.use_faiss and self.index is not None and len(self.vectors_mem) > 0:
                self.index.reset()
                mat = np.array(self.vectors_mem, dtype=np.float32)
                self.index.add(mat)

            logger.info(f"📂 Loaded {len(self.id_map)} face vectors from {target_path}")
            return True
        except Exception as e:
            logger.warning(f"⚠️ Could not load vector index: {e}")
            return False

    def clear(self):
        """Reset index and in-memory cache."""
        self.id_map = []
        self.vectors_mem = []
        if self.use_faiss and self.index is not None:
            self.index.reset()
        logger.info("🗑️ Cleared vector database index.")

    def size(self) -> int:
        """Return number of registered vectors in index."""
        return len(self.id_map)


# Global singleton instance
vector_db = VectorDatabase()
