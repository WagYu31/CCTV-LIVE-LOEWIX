#!/bin/bash
# =============================================================================
# Loewix CCTV AI Vision — FastAPI Server Launcher
# =============================================================================
# Starts the high-performance AI engine with:
# - Hierarchical Small-Face Detection (Auto Crop & Zoom)
# - DeepFace ArcFace 512-D Embeddings
# - FAISS Sub-Millisecond Vector Search
# - Real-time WebSockets & REST API
# =============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

echo "================================================================="
echo "🚀 LOEWIX CCTV AI VISION — STARTING SERVER"
echo "================================================================="

# 1. Check Virtual Environment
if [ -d "venv_ai" ]; then
    echo "✅ Using dedicated virtual environment: venv_ai"
    PYTHON_EXEC="./venv_ai/bin/python3"
    UVICORN_EXEC="./venv_ai/bin/uvicorn"
else
    echo "⚠️ venv_ai not found. Falling back to system python3..."
    PYTHON_EXEC="python3"
    UVICORN_EXEC="uvicorn"
fi

# 2. Server Configuration
export KMP_DUPLICATE_LIB_OK="TRUE"
export OMP_NUM_THREADS="1"
export LOEWIX_API_HOST="0.0.0.0"
export LOEWIX_API_PORT="5050"
export LOEWIX_MATCH_THRESHOLD="0.68"
export LOEWIX_DB_PATH="$SCRIPT_DIR/data/loewix_ai.db"
export LOEWIX_FAISS_PATH="$SCRIPT_DIR/assets/uploads/faces/faiss_arcface.index"

echo "   Host        : http://$LOEWIX_API_HOST:$LOEWIX_API_PORT"
echo "   Database    : $LOEWIX_DB_PATH"
echo "   Vector DB   : $LOEWIX_FAISS_PATH"
echo "   Threshold   : $LOEWIX_MATCH_THRESHOLD"
echo "================================================================="

# 3. Launch FastAPI Server via Uvicorn
exec $UVICORN_EXEC app_fastapi_server:app \
    --host "$LOEWIX_API_HOST" \
    --port "$LOEWIX_API_PORT" \
    --workers 1 \
    --log-level info
