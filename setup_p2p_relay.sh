#!/bin/bash
# Loewix CCTV - Server-Side P2P & DVRIP Bridge Setup Script
# PT. LOEWIX INDONESIA

set -e

echo "=========================================="
echo "🚀 Setting up Loewix P2P/DVRIP Video Bridge"
echo "=========================================="

WORKDIR="/www/wwwroot/CCTV-LIVE-LOEWIX"
cd "$WORKDIR"

# 1. Download go2rtc (Native DVRIP & RTSP Multi-Protocol Engine)
if [ ! -f "/usr/local/bin/go2rtc" ]; then
    echo "📥 Downloading go2rtc streaming engine..."
    curl -L -o /tmp/go2rtc_linux_amd64 https://github.com/AlexxIT/go2rtc/releases/latest/download/go2rtc_linux_amd64
    sudo mv /tmp/go2rtc_linux_amd64 /usr/local/bin/go2rtc || mv /tmp/go2rtc_linux_amd64 /usr/local/bin/go2rtc
    chmod +x /usr/local/bin/go2rtc
    echo "✅ go2rtc installed successfully."
fi

# 2. Restart services
echo "🔄 Starting P2P/DVRIP Bridge & MediaMTX..."
bash restart_stream.sh

echo "=========================================="
echo "🎉 Setup Complete! P2P Bridge is Online."
echo "=========================================="
