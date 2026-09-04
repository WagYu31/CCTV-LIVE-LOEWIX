#!/usr/bin/env python3
"""
Loewix Multi-Camera Local Bridge
Streams multiple local RTSP cameras to Loewix Cloud MediaMTX in parallel.
No public IP or router port forwarding needed.
PT. LOEWIX INDONESIA
"""

import os
import sys
import time
import json
import subprocess
import threading

CONFIG_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), "cameras.json")
CLOUD_SERVER = os.getenv("LOEWIX_CLOUD_SERVER", "stream.loewixcctv.com")
CLOUD_RTSP_PORT = int(os.getenv("LOEWIX_CLOUD_PORT", "8554"))

def stream_worker(cam):
    title = cam.get("title", "Kamera")
    rtsp_url = cam.get("rtsp_url")
    stream_name = cam.get("stream_name")

    if not rtsp_url or not stream_name:
        print(f"[-] [SKIP] {title}: rtsp_url atau stream_name tidak valid.")
        return

    cloud_target = f"rtsp://{CLOUD_SERVER}:{CLOUD_RTSP_PORT}/{stream_name}"
    print(f"[+] [START] {title} -> {cloud_target}")

    while True:
        cmd = [
            "ffmpeg",
            "-nostdin",
            "-loglevel", "warning",
            "-rtsp_transport", "tcp",
            "-re",
            "-i", rtsp_url,
            "-c", "copy",
            "-f", "rtsp",
            "-rtsp_transport", "tcp",
            cloud_target
        ]
        try:
            p = subprocess.Popen(cmd)
            p.wait()
        except Exception as e:
            print(f"[!] [ERROR] {title}: {e}")

        print(f"[!] [RECONNECT] {title} terputus. Mencoba reconnect dalam 5 detik...")
        time.sleep(5)

def main():
    print("=" * 65)
    print("   LOEWIX MULTI-CAMERA LOCAL RTSP BRIDGE")
    print("   PT. LOEWIX INDONESIA - OUTBOUND STREAMING")
    print("=" * 65)

    if not os.path.exists(CONFIG_PATH):
        print(f"[ERROR] File konfigurasi tidak ditemukan: {CONFIG_PATH}")
        sys.exit(1)

    with open(CONFIG_PATH, "r", encoding="utf-8") as f:
        cameras = json.load(f)

    if not cameras or not isinstance(cameras, list):
        print("[ERROR] Daftar kamera di cameras.json kosong.")
        sys.exit(1)

    print(f"[*] Ditemukan {len(cameras)} kamera untuk di-relay ke cloud...")

    threads = []
    for cam in cameras:
        t = threading.Thread(target=stream_worker, args=(cam,), daemon=True)
        t.start()
        threads.append(t)
        time.sleep(1)

    print("[*] Semua thread bridge aktif. Tekan Ctrl+C untuk berhenti.")
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\n[*] Loewix Multi-Camera Bridge dihentikan.")

if __name__ == "__main__":
    main()
