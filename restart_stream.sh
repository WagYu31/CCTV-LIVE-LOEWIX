#!/bin/bash
# Loewix MediaMTX Safe Restart Script

echo "Stopping existing MediaMTX processes..."
sudo killall -9 mediamtx 2>/dev/null || sudo pkill -9 -x mediamtx 2>/dev/null || true
sleep 1

echo "Starting MediaMTX..."
nohup /home/loewix/mediamtx /www/wwwroot/CCTV-LIVE-LOEWIX/mediamtx.yml > /tmp/mediamtx.log 2>&1 &

sleep 2

if pgrep -x "mediamtx" > /dev/null; then
    echo "✅ MediaMTX is running successfully! (PID: $(pgrep -x mediamtx))"
    echo "--- Recent Logs ---"
    tail -n 10 /tmp/mediamtx.log
else
    echo "❌ MediaMTX failed to start. Logs:"
    cat /tmp/mediamtx.log
fi
