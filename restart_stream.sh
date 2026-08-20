#!/bin/bash
# Loewix MediaMTX & P2P/DVRIP Bridge Restart Script

echo "Stopping existing streaming processes..."
killall -9 mediamtx 2>/dev/null || pkill -9 -x mediamtx 2>/dev/null || true
killall -9 go2rtc 2>/dev/null || pkill -9 -x go2rtc 2>/dev/null || true
sleep 1

# Start go2rtc if installed
if [ -f "/usr/local/bin/go2rtc" ]; then
    echo "Starting go2rtc P2P/DVRIP Engine..."
    nohup /usr/local/bin/go2rtc -config /www/wwwroot/CCTV-LIVE-LOEWIX/go2rtc.yaml > /tmp/go2rtc.log 2>&1 &
    sleep 1
elif [ -f "/home/loewix/go2rtc" ]; then
    echo "Starting go2rtc P2P/DVRIP Engine..."
    nohup /home/loewix/go2rtc -config /www/wwwroot/CCTV-LIVE-LOEWIX/go2rtc.yaml > /tmp/go2rtc.log 2>&1 &
    sleep 1
fi

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
