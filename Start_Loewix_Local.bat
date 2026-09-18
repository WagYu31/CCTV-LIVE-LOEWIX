@echo off
title LOEWIX Face Detection - Desktop Launcher (Windows)
color 0b
cls

echo ================================================================
echo         LOEWIX FACE DETECTION - DESKTOP & LAN EDITION
echo                    PT. LOEWIX INDONESIA
echo ================================================================
echo.

cd /d "%~dp0"

:: Check for PHP installation
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [!] PHP tidak terdeteksi di PATH sistem Windows Anda.
    echo [*] Memeriksa instalasi lokal XAMPP / Laragon / aaPanel...
    if exist "C:\xampp\php\php.exe" (
        set "PATH=C:\xampp\php;%PATH%"
    ) else if exist "C:\laragon\bin\php\php-*\php.exe" (
        for /d %%i in ("C:\laragon\bin\php\php-*") do set "PATH=%%i;%PATH%"
    ) else (
        echo [ERROR] PHP tidak ditemukan. Silakan pasang PHP atau XAMPP terlebih dahulu.
        pause
        exit /b 1
    )
)

echo [+] PHP Terdeteksi.
echo [*] Memulai Local Server di port 8088...
echo [*] URL Akses: http://localhost:8088/local/
echo.

:: Launch browser after 1 second in background
start "" cmd /c "timeout /t 2 >nul & start http://localhost:8088/local/"

:: Run PHP built-in server
php -S 127.0.0.1:8088
pause
