#!/usr/bin/env python3
"""
Loewix Server-Side P2P & RTSP Video Bridge Daemon
Handles direct streaming between DVRs/NVRs and MediaMTX
PT. LOEWIX INDONESIA
"""

import os
import sys
import time
import json
import socket
import logging
import subprocess
import threading
from typing import Dict, Any, Optional

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] [P2P-Bridge] %(message)s'
)

CONFIG_DIR = os.path.dirname(os.path.abspath(__file__))
PROJECT_ROOT = os.path.abspath(os.path.join(CONFIG_DIR, '..', '..'))
DB_PATH = os.path.join(PROJECT_ROOT, 'data', 'loewix_db.json')
MEDIAMTX_CONFIG = os.path.join(PROJECT_ROOT, 'mediamtx.yml')

class LoewixP2PBridge:
    def __init__(self):
        self.active_streams = {}
        self.running = True

    def load_cameras(self) -> list:
        if not os.path.exists(DB_PATH):
            return []
        try:
            with open(DB_PATH, 'r', encoding='utf-8') as f:
                data = json.load(f)
                return data.get('cameras', [])
        except Exception as e:
            logging.error(f"Failed to load camera database: {e}")
            return []

    def sync_mediamtx_config(self):
        """Auto-generates mediamtx.yml with all registered cameras"""
        cameras = self.load_cameras()
        logging.info(f"Syncing {len(cameras)} cameras to MediaMTX configuration...")
        
        paths = {}
        # Existing demo / standard paths
        paths['cctv_loewix_1'] = {
            'source': 'rtsp://103.164.101.50:8203/user=admin&password=admin1234&channel=1&stream=0.sdp',
            'rtspTransport': 'tcp',
            'sourceOnDemand': 'yes'
        }
        paths['cctv_loewix_2'] = {
            'source': 'rtsp://103.164.101.50:8203/user=admin&password=admin1234&channel=2&stream=0.sdp',
            'rtspTransport': 'tcp',
            'sourceOnDemand': 'yes'
        }
        paths['cctv_loewix_3'] = {
            'source': 'rtsp://103.164.101.50:8203/user=admin&password=admin1234&channel=3&stream=0.sdp',
            'rtspTransport': 'tcp',
            'sourceOnDemand': 'yes'
        }
        paths['cctv_loewix_h264'] = {'source': 'publisher'}

        for cam in cameras:
            stream_path = cam.get('streamPath')
            if not stream_path:
                continue
            
            conn_type = cam.get('connection_type', 'rtsp')
            if conn_type == 'xmeye_p2p':
                sn = cam.get('serial_number', '').strip()
                ch = cam.get('channel', 1)
                user = cam.get('device_user', 'admin')
                pwd = cam.get('device_pass', 'LoewixL12')
                
                # Direct RTSP / DVRIP source mapping
                paths[stream_path] = {
                    'source': f'rtsp://{user}:{pwd}@103.121.245.177:554/user={user}_password={pwd}_channel={ch}_stream=1.sdp',
                    'rtspTransport': 'tcp',
                    'sourceOnDemand': 'yes'
                }
            elif conn_type == 'rtsp' and cam.get('hls_url'):
                hls_url = cam.get('hls_url')
                if hls_url.startswith('rtsp://'):
                    paths[stream_path] = {
                        'source': hls_url,
                        'rtspTransport': 'tcp',
                        'sourceOnDemand': 'yes'
                    }

        # Write YAML
        yaml_lines = [
            "# Auto-Generated MediaMTX Configuration by Loewix P2P Bridge",
            "hlsAlwaysRemux: true",
            "hlsAllowOrigins: ['*']",
            "",
            "paths:"
        ]
        for name, cfg in paths.items():
            yaml_lines.append(f"  {name}:")
            for k, v in cfg.items():
                yaml_lines.append(f"    {k}: {v}")
            yaml_lines.append("")

        with open(MEDIAMTX_CONFIG, 'w', encoding='utf-8') as f:
            f.write("\n".join(yaml_lines))
        logging.info("✅ MediaMTX configuration updated successfully.")

    def run(self):
        logging.info("🚀 Loewix Server-Side P2P Bridge started.")
        self.sync_mediamtx_config()
        
        while self.running:
            try:
                time.sleep(30)
            except KeyboardInterrupt:
                self.running = False
                break

if __name__ == '__main__':
    bridge = LoewixP2PBridge()
    bridge.run()
