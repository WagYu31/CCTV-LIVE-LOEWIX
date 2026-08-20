#!/usr/bin/env python3
"""
Loewix Local CCTV Outbound Pusher
Reads local DVR video streams and pushes them directly to the Loewix VPS server.
No router port forwarding or public IP required.
PT. LOEWIX INDONESIA
"""

import os
import sys
import time
import socket
import subprocess
import threading
import json
import urllib.request
import urllib.parse
from typing import List, Optional

# ============================================================
# CONFIGURATION
# ============================================================
DVR_IP = os.getenv("LOEWIX_DVR_IP", "192.168.11.161")
DVR_PORT = int(os.getenv("LOEWIX_DVR_PORT", "554"))
DVR_USER = os.getenv("LOEWIX_DVR_USER", "admin")
DVR_PASS = os.getenv("LOEWIX_DVR_PASS", "LoewixL12")
DVR_SN = os.getenv("LOEWIX_DVR_SN", "848f3922aa2875eb")
TOTAL_CHANNELS = int(os.getenv("LOEWIX_CHANNELS", "16"))

SERVER_HOST = os.getenv("LOEWIX_SERVER_HOST", "103.121.180.157")
SERVER_RTSP_PORT = int(os.getenv("LOEWIX_SERVER_RTSP_PORT", "8554"))
SERVER_RTMP_PORT = int(os.getenv("LOEWIX_SERVER_RTMP_PORT", "1935"))
WEB_API_URL = "https://loewixcctv.com/api/cameras.php"

# ============================================================
# SNAPSHOT CAPTURE & UPLOAD WORKER
# ============================================================
def snapshot_sync_worker(dvr_ip: str):
    """Periodically captures real-time snapshots from all channels and uploads to server"""
    print("📸 [Snapshot Sync] Memulai sinkronisasi foto realtime 16 channel...")
    import base64

    while True:
        for ch in range(1, TOTAL_CHANNELS + 1):
            source_url = f"rtsp://{DVR_USER}:{DVR_PASS}@{dvr_ip}:{DVR_PORT}/cam/realmonitor?channel={ch}&subtype=1"
            cmd = [
                "ffmpeg", "-nostdin", "-loglevel", "quiet",
                "-rtsp_transport", "tcp",
                "-i", source_url,
                "-vframes", "1",
                "-f", "image2pipe",
                "-vcodec", "mjpeg",
                "-"
            ]
            try:
                p = subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.DEVNULL)
                img_data, _ = p.communicate(timeout=4)
                if img_data and len(img_data) > 1000:
                    b64_img = base64.b64encode(img_data).decode('utf-8')
                    post_data = urllib.parse.urlencode({
                        'action': 'upload_snapshot',
                        'sn': DVR_SN,
                        'channel': ch,
                        'image': b64_img
                    }).encode('utf-8')
                    req = urllib.request.Request(WEB_API_URL, data=post_data, headers={'User-Agent': 'LoewixPusher/1.0'})
                    with urllib.request.urlopen(req, timeout=5) as resp:
                        pass
            except Exception:
                pass
            time.sleep(0.5)

        time.sleep(15)

# ============================================================
# STREAM PUSHER WORKER
# ============================================================
def push_channel(dvr_ip: str, channel: int):
    """Pushes a single DVR channel stream to the VPS server"""
    stream_path = f"xmeye_{DVR_SN}_ch{channel}"
    
    # Official XMeye/Dahua/Loewix sub-stream URL
    source_url = f"rtsp://{DVR_USER}:{DVR_PASS}@{dvr_ip}:{DVR_PORT}/cam/realmonitor?channel={channel}&subtype=1"
    
    # Target RTMP / RTSP URL on MediaMTX
    target_rtsp = f"rtsp://{SERVER_HOST}:{SERVER_RTSP_PORT}/{stream_path}"
    target_rtmp = f"rtmp://{SERVER_HOST}:{SERVER_RTMP_PORT}/{stream_path}"

    cmd = [
        "ffmpeg",
        "-nostdin",
        "-loglevel", "warning",
        "-rtsp_transport", "tcp",
        "-i", source_url,
        "-c", "copy",
        "-f", "rtsp",
        "-rtsp_transport", "tcp",
        target_rtsp
    ]

    print(f"🚀 [CH {channel:02d}] Streaming: {stream_path} -> {SERVER_HOST}...")

    while True:
        try:
            p = subprocess.Popen(cmd, stdout=subprocess.DEVNULL, stderr=subprocess.PIPE)
            _, err = p.communicate()
            if err:
                # If RTSP push fails, fallback to RTMP push
                cmd_rtmp = [
                    "ffmpeg", "-nostdin", "-loglevel", "warning",
                    "-rtsp_transport", "tcp", "-i", source_url,
                    "-c", "copy", "-f", "flv", target_rtmp
                ]
                p2 = subprocess.Popen(cmd_rtmp, stdout=subprocess.DEVNULL, stderr=subprocess.PIPE)
                p2.communicate()
        except Exception as e:
            print(f"⚠️ [CH {channel:02d}] Koneksi terputus: {e}. Menghubungkan ulang...")
        
        time.sleep(3)

# ============================================================
# MAIN
# ============================================================
def main():
    print("==========================================================")
    print("🚀 LOEWIX CCTV LOCAL OUTBOUND PUSHER")
    print("   PT. LOEWIX INDONESIA")
    print("==========================================================")

    # Check FFmpeg
    try:
        subprocess.run(["ffmpeg", "-version"], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        print("✅ FFmpeg terdeteksi.")
    except FileNotFoundError:
        print("❌ FFmpeg belum terpasang di komputer ini.")
        print("   Silakan unduh FFmpeg atau pasang dengan mudah (lihat README).")
        sys.exit(1)

    dvr_ip = DVR_IP
    if not dvr_ip:
        dvr_ip = discover_local_dvr()

    if not dvr_ip:
        print("⚠️ DVR tidak terdeteksi otomatis. Silakan masukkan IP lokal DVR manual:")
        try:
            dvr_ip = input("Masukkan IP DVR (contoh: 192.168.1.100): ").strip()
        except (KeyboardInterrupt, EOFError):
            sys.exit(0)

    if not dvr_ip:
        print("❌ IP DVR tidak boleh kosong.")
        sys.exit(1)

    print(f"\n🎥 Memulai transmisi {TOTAL_CHANNELS} channel dari DVR ({dvr_ip}) ke Server Loewix...")
    print(f"🌐 Server Tujuan: {SERVER_HOST} ({WEB_API_URL})\n")

    # Start snapshot sync thread
    snap_thread = threading.Thread(target=snapshot_sync_worker, args=(dvr_ip,), daemon=True)
    snap_thread.start()

    # Start video push threads
    threads = []
    for ch in range(1, TOTAL_CHANNELS + 1):
        t = threading.Thread(target=push_channel, args=(dvr_ip, ch), daemon=True)
        t.start()
        threads.append(t)
        time.sleep(0.2)

    print(f"\n✅ Seluruh {TOTAL_CHANNELS} channel aktif mengirim siaran video & foto realtime!")
    print("📌 Tekan Ctrl+C untuk menghentikan.\n")

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\n🛑 Loewix Pusher dihentikan.")

if __name__ == '__main__':
    main()
