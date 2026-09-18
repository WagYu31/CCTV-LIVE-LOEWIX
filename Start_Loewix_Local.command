#!/bin/bash
# ================================================================
# LOEWIX Face Detection - Desktop Launcher (macOS)
# PT. LOEWIX INDONESIA
# ================================================================

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$DIR"

echo "================================================================"
echo "        LOEWIX FACE DETECTION - DESKTOP & LAN EDITION (MAC)"
echo "                   PT. LOEWIX INDONESIA"
echo "================================================================"
echo ""

# Check for PHP
if ! command -v php &> /dev/null; then
    echo "[!] PHP tidak ditemukan di sistem Mac Anda."
    echo "[*] Anda dapat menginstal PHP melalui Homebrew: brew install php"
    read -p "Tekan Enter untuk keluar..."
    exit 1
fi

echo "[+] PHP Terdeteksi: $(php -v | head -n 1)"
echo "[*] Menjalankan server lokal di port 8088..."
echo "[*] Membuka http://localhost:8088/local/ di browser..."
echo ""

# Open browser after 1 second in background
(sleep 1 && open "http://localhost:8088/local/") &

# Start PHP server
php -S 127.0.0.1:8088
