# Setup MediaMTX - Panduan Lengkap

## Arsitektur
```
Kamera CCTV (RTSP) → VPS (MediaMTX) → HLS (m3u8) → Website (HLS.js)
```

## 1. Install MediaMTX di VPS

### Download & Install (Ubuntu/Debian)
```bash
# Download MediaMTX terbaru
wget https://github.com/bluenviron/mediamtx/releases/download/v1.9.0/mediamtx_v1.9.0_linux_amd64.tar.gz

# Extract
tar -xzf mediamtx_v1.9.0_linux_amd64.tar.gz

# Pindahkan ke /usr/local/bin
sudo mv mediamtx /usr/local/bin/
sudo mv mediamtx.yml /etc/mediamtx.yml
```

## 2. Konfigurasi MediaMTX (`/etc/mediamtx.yml`)

```yaml
# ===== SERVER SETTINGS =====
hlsAddress: :8888
rtspAddress: :8554
webrtcAddress: :8889
apiAddress: :9997

# ===== CORS - PENTING untuk web! =====
hlsAllowOrigin: '*'

# ===== PATHS - KAMERA CCTV =====
paths:
  # ============================
  # KAMERA VIA RTSP URL
  # ============================
  cam1:
    source: rtsp://admin:password@192.168.1.100:554/stream1
    sourceOnDemand: yes
    
  cam2:
    source: rtsp://admin:password@192.168.1.101:554/stream1
    sourceOnDemand: yes
    
  # ============================
  # KAMERA VIA DDNS
  # ============================
  cam3:
    source: rtsp://admin:password@kameraku.ddns.net:554/stream1
    sourceOnDemand: yes
    
  # ============================
  # FORMAT RTSP BERBAGAI MERK
  # ============================
  
  # Hikvision
  cam_hikvision:
    source: rtsp://admin:password@IP:554/Streaming/Channels/101
    sourceOnDemand: yes
  
  # Dahua
  cam_dahua:
    source: rtsp://admin:password@IP:554/cam/realmonitor?channel=1&subtype=0
    sourceOnDemand: yes
  
  # Reolink
  cam_reolink:
    source: rtsp://admin:password@IP:554/h264Preview_01_main
    sourceOnDemand: yes
```

## 3. Setup Nginx + SSL

```nginx
server {
    listen 80;
    server_name stream.loewix.com;
    
    location / {
        proxy_pass http://127.0.0.1:8888;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        add_header 'Access-Control-Allow-Origin' '*' always;
    }
}
```

```bash
sudo certbot --nginx -d stream.loewix.com
```

## 4. Systemd Service

```ini
[Unit]
Description=MediaMTX RTSP/HLS Server
After=network.target

[Service]
ExecStart=/usr/local/bin/mediamtx /etc/mediamtx.yml
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable mediamtx && sudo systemctl start mediamtx
```

## 5. Update Website

Di `index.php` ganti:
```javascript
const STREAM_BASE = 'https://stream.loewix.com';
```

## 6. Tambah Kamera Baru

1. Edit mediamtx.yml → tambah path
2. `sudo systemctl restart mediamtx`
3. Edit index.php → tambah entry di mediamtxData

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Stream tidak muncul | Cek RTSP URL di VLC dulu |
| CORS error | Pastikan hlsAllowOrigin: '*' |
| Lag/delay | hlsSegmentCount: 3, hlsSegmentDuration: 1s |
