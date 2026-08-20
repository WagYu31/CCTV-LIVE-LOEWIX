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
DVR_IP = os.getenv("LOEWIX_DVR_IP", "")               # Leave blank for auto-discovery
DVR_PORT = int(os.getenv("LOEWIX_DVR_PORT", "554"))
DVR_USER = os.getenv("LOEWIX_DVR_USER", "admin")
DVR_PASS = os.getenv("LOEWIX_DVR_PASS", "LoewixL12")
DVR_SN = os.getenv("LOEWIX_DVR_SN", "848f3922aa2875eb")
TOTAL_CHANNELS = int(os.getenv("LOEWIX_CHANNELS", "16"))

SERVER_HOST = os.getenv("LOEWIX_SERVER_HOST", "stream.loewixcctv.com")
SERVER_RTSP_PORT = int(os.getenv("LOEWIX_SERVER_RTSP_PORT", "8554"))
SERVER_RTMP_PORT = int(os.getenv("LOEWIX_SERVER_RTMP_PORT", "1935"))
WEB_API_URL = "https://loewixcctv.com/api/cameras.php"

# ============================================================
# DVR AUTO-DISCOVERY
# ============================================================
def discover_local_dvr() -> Optional[str]:
    """Scans local subnet for Xiongmai / Loewix DVR on port 34567 or 554"""
    print("🔍 Mencari alamat IP DVR di jaringan lokal...")
    try:
        # Get local IP to determine subnet
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(("8.8.8.8", 80))
        local_ip = s.getsockname()[0]
        s.close()
    except Exception:
        local_ip = "192.168.1.50"

    parts = local_ip.split('.')
    subnet = f"{parts[0]}.{parts[1]}.{parts[2]}"
    print(f"📡 Subnet jaringan terdeteksi: {subnet}.0/24 (IP Komputer ini: {local_ip})")

    # Common DVR candidate IPs
    candidates = [
        f"{subnet}.100", f"{subnet}.10", f"{subnet}.200", f"{subnet}.101",
        f"{subnet}.2", f"{subnet}.3", f"{subnet}.4", f"{subnet}.5",
        f"{subnet}.128", f"{subnet}.108", f"{subnet}.168", f"{subnet}.188"
    ]
    # Add full scan range
    for i in range(1, 255):
        ip = f"{subnet}.{i}"
        if ip not in candidates:
            candidates.append(ip)

    for ip in candidates:
        for port in [34567, 554]:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(0.15)
            res = sock.connect_ex((ip, port))
            sock.close()
            if res == 0:
                print(f"✅ DVR Ditemukan di IP: {ip} (Port {port})")
                return ip

    return None

# ============================================================
# STREAM PUSHER WORKER
# ============================================================
def push_channel(dvr_ip: str, channel: int):
    """Pushes a single DVR channel stream to the VPS server"""
    stream_path = f"xmeye_{DVR_SN}_ch{channel}"
    
    # Sub-stream for smooth, bandwidth-efficient web streaming (subtype=1 or stream=1)
    source_url = f"rtsp://{DVR_USER}:{DVR_PASS}@{dvr_ip}:{DVR_PORT}/user={DVR_USER}&password={DVR_PASS}&channel={channel}&stream=1.sdp"
    
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

    print(f"🚀 [CH {channel}] Memulai streaming {stream_path} -> {SERVER_HOST}...")

    while True:
        try:
            p = subprocess.Popen(cmd, stdout=subprocess.DEVNULL, stderr=subprocess.PIPE)
            _, err = p.communicate()
            if err:
                # If RTSP push failed, try RTMP push fallback
                cmd_rtmp = [
                    "ffmpeg", "-nostdin", "-loglevel", "warning",
                    "-rtsp_transport", "tcp", "-i", source_url,
                    "-c", "copy", "-f", "flv", target_rtmp
                ]
                p2 = subprocess.Popen(cmd_rtmp, stdout=subprocess.DEVNULL, stderr=subprocess.PIPE)
                p2.communicate()
        except Exception as e:
            print(f"⚠️ [CH {channel}] Koneksi terputus: {e}. Menghubungkan ulang dalam 5 detik...")
        
        time.sleep(5)

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
    print(f"🌐 Server Tujuan: {SERVER_HOST}\n")

    threads = []
    for ch in range(1, TOTAL_CHANNELS + 1):
        t = threading.Thread(target=push_channel, args=(dvr_ip, ch), daemon=True)
        t.start()
        threads.append(t)
        time.sleep(0.3)

    print(f"✅ Seluruh {TOTAL_CHANNELS} channel aktif mengirim siaran video!")
    print("📌 Tekan Ctrl+C untuk menghentikan.\n")

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\n🛑 Loewix Pusher dihentikan.")

if __name__ == '__main__':
    main()
