@echo off
title Loewix CCTV Local Outbound Pusher
echo ==========================================================
echo  LOEWIX CCTV LOCAL STREAM PUSHER (WINDOWS)
echo  PT. LOEWIX INDONESIA
echo ==========================================================
echo.

python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Python belum terinstall di komputer ini.
    echo Silakan download dan install Python dari https://www.python.org/downloads/
    echo Pastikan centang "Add Python to PATH" saat install.
    pause
    exit /b 1
)

ffmpeg -version >nul 2>&1
if %errorlevel% neq 0 (
    echo [INFO] Mengunduh FFmpeg portable...
    curl -L -o ffmpeg.zip https://github.com/BtbN/FFmpeg-Builds/releases/download/latest/ffmpeg-master-latest-win64-gpl.zip
    tar -xf ffmpeg.zip
    set PATH=%PATH%;%CD%\ffmpeg-master-latest-win64-gpl\bin
)

echo Memulai transmisi video ke server Loewix...
python loewix_pusher.py
pause
