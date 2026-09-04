@echo off
title LOEWIX CCTV - LOCAL RTSP BRIDGE TO CLOUD
color 0b
echo ================================================================
echo    LOEWIX LOCAL RTSP BRIDGE (NO PUBLIC IP REQUIRED)
echo    PT. LOEWIX INDONESIA - OUTBOUND STREAM RELAY
echo ================================================================
echo.

:: Default settings
set RTSP_LOCAL=rtsp://admin:@192.168.11.182:554/user=admin^&password=^&channel=1^&stream=0.sdp
set STREAM_NAME=cam_live_5018
set CLOUD_SERVER=stream.loewixcctv.com
set CLOUD_PORT=8554

:: Read bridge_config.ini if available
if exist "%~dp0bridge_config.ini" (
    for /f "usebackq tokens=1,* delims==" %%A in ("%~dp0bridge_config.ini") do (
        set lineKey=%%A
        set lineVal=%%B
        if /i "%%A"=="RTSP_LOCAL" set RTSP_LOCAL=%%B
        if /i "%%A"=="STREAM_NAME" set STREAM_NAME=%%B
        if /i "%%A"=="CLOUD_SERVER" set CLOUD_SERVER=%%B
        if /i "%%A"=="CLOUD_RTSP_PORT" set CLOUD_PORT=%%B
    )
)

set CLOUD_TARGET=rtsp://%CLOUD_SERVER%:%CLOUD_PORT%/%STREAM_NAME%

echo [1/3] Konfigurasi Streaming:
echo - Sumber RTSP Lokal : %RTSP_LOCAL%
echo - Target Cloud Media : %CLOUD_TARGET%
echo - Output Web HLS     : https://%CLOUD_SERVER%/%STREAM_NAME%/index.m3u8
echo.

echo [2/3] Memeriksa FFmpeg...
ffmpeg -version >nul 2>&1
if %errorlevel% neq 0 (
    if exist "%~dp0ffmpeg.exe" (
        set "PATH=%PATH%;%~dp0"
    ) else (
        echo [INFO] FFmpeg belum terpasang. Mengunduh FFmpeg portable resmi...
        curl -L -o "%~dp0ffmpeg.zip" "https://github.com/BtbN/FFmpeg-Builds/releases/download/latest/ffmpeg-master-latest-win64-gpl.zip"
        if exist "%~dp0ffmpeg.zip" (
            tar -xf "%~dp0ffmpeg.zip" -C "%~dp0"
            for /d %%D in ("%~dp0ffmpeg-master-latest-win64-gpl*") do (
                copy "%%D\bin\ffmpeg.exe" "%~dp0" >nul
            )
            del "%~dp0ffmpeg.zip" >nul 2>&1
        )
    )
)

echo.
echo ================================================================
echo [3/3] STATUS: STREAMING AKTIF KE CLOUD LOEWIX!
echo       Video CCTV lokal sedang ditransmisikan ke web.
echo       Kamera di https://loewixcctv.com/customer/index.php sudah live!
echo.
echo       CATATAN: JANGAN TUTUP JENDELA INI SELAMA INGIN LIVE STREAMING.
echo       Jika koneksi Wi-Fi/LAN terputus, sistem otomatis reconnect.
echo ================================================================
echo.

:LOOP
ffmpeg -nostdin -loglevel warning -rtsp_transport tcp -re -i "%RTSP_LOCAL%" -c copy -f rtsp -rtsp_transport tcp "%CLOUD_TARGET%"
echo.
echo [%date% %time%] Stream terputus. Menghubungkan ulang dalam 5 detik...
timeout /t 5 >nul
goto LOOP
