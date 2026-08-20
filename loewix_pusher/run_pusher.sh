#!/bin/bash
# Loewix CCTV Local Stream Pusher for Mac / Linux
# PT. LOEWIX INDONESIA

set -e

echo "=========================================================="
echo " LOEWIX CCTV LOCAL STREAM PUSHER (MAC/LINUX)"
echo " PT. LOEWIX INDONESIA"
echo "=========================================================="
echo ""

if ! command -v ffmpeg &> /dev/null; then
    echo "❌ FFmpeg belum terpasang."
    if [[ "$OSTYPE" == "darwin"* ]]; then
        echo "💡 Pasang via Homebrew: brew install ffmpeg"
    else
        echo "💡 Pasang via apt: sudo apt update && sudo apt install -y ffmpeg"
    fi
    exit 1
fi

python3 loewix_pusher.py
