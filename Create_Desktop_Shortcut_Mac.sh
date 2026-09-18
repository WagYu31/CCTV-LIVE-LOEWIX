#!/bin/bash
# ================================================================
# Buat Shortcut Desktop Mac - Loewix Local VMS
# ================================================================

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
chmod +x "$DIR/Start_Loewix_Local.command"

DESKTOP_SHORTCUT="$HOME/Desktop/Loewix Local VMS.command"
ln -sf "$DIR/Start_Loewix_Local.command" "$DESKTOP_SHORTCUT"
chmod +x "$DESKTOP_SHORTCUT"

echo "================================================================"
echo "[+] BERHASIL! Shortcut telah dibuat di Desktop Mac Anda:"
echo "    $DESKTOP_SHORTCUT"
echo "================================================================"
echo "Anda sekarang bisa langsung klik 2x icon di Desktop untuk membuka aplikasi."
