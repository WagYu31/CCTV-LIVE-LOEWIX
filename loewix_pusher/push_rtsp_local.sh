#!/usr/bin/env bash
# ================================================================
# LOEWIX CCTV - LOCAL RTSP BRIDGE TO CLOUD (LINUX / MACOS / PI)
# PT. LOEWIX INDONESIA
# ================================================================

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_FILE="$DIR/bridge_config.ini"

# Default values
RTSP_LOCAL="rtsp://admin:@192.168.11.182:554/user=admin&password=&channel=1&stream=0.sdp"
STREAM_NAME="cam_live_5018"
CLOUD_SERVER="stream.loewixcctv.com"
CLOUD_PORT="8554"

# Read config if exists
if [ -f "$CONFIG_FILE" ]; then
    while IFS='=' read -r key val; do
        [[ "$key" =~ ^#.*$ ]] && continue
        key=$(echo "$key" | xargs)
        val=$(echo "$val" | xargs)
        if [ "$key" = "RTSP_LOCAL" ]; then RTSP_LOCAL="$val"; fi
        if [ "$key" = "STREAM_NAME" ]; then STREAM_NAME="$val"; fi
        if [ "$key" = "CLOUD_SERVER" ]; then CLOUD_SERVER="$val"; fi
        if [ "$key" = "CLOUD_RTSP_PORT" ]; then CLOUD_PORT="$val"; fi
    done < "$CONFIG_FILE"
fi

CLOUD_TARGET="rtsp://${CLOUD_SERVER}:${CLOUD_PORT}/${STREAM_NAME}"

echo "================================================================"
echo "   LOEWIX LOCAL RTSP BRIDGE (NO PUBLIC IP REQUIRED)"
echo "   PT. LOEWIX INDONESIA - OUTBOUND STREAM RELAY"
echo "================================================================"
echo "Sumber RTSP Lokal : $RTSP_LOCAL"
echo "Target Cloud Media : $CLOUD_TARGET"
echo "Output Web HLS     : https://${CLOUD_SERVER}/${STREAM_NAME}/index.m3u8"
echo "================================================================"

if ! command -v ffmpeg &> /dev/null; then
    echo "[ERROR] FFmpeg belum terpasang. Jalankan: sudo apt install ffmpeg atau brew install ffmpeg"
    exit 1
fi

echo "Memulai outbound streaming..."
while true; do
    ffmpeg -nostdin -loglevel warning -rtsp_transport tcp -re -i "$RTSP_LOCAL" -c copy -f rtsp -rtsp_transport tcp "$CLOUD_TARGET"
    echo "[$(date)] Stream terputus. Mencoba menghubungkan kembali dalam 5 detik..."
    sleep 5
done
