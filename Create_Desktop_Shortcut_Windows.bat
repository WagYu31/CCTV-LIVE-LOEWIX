@echo off
title Buat Shortcut Desktop Windows - Loewix Local VMS
color 0a
cls

echo ================================================================
echo    MEMBUAT SHORTCUT DESKTOP WINDOWS: LOEWIX LOCAL VMS
echo ================================================================
echo.

set "TARGET_BAT=%~dp0Start_Loewix_Local.bat"
set "ICON_PATH=%~dp0assets\image\icon.png"
set "SHORTCUT_PATH=%USERPROFILE%\Desktop\Loewix Local VMS.lnk"

powershell -Command "$ws = New-Object -ComObject WScript.Shell; $s = $ws.CreateShortcut('%SHORTCUT_PATH%'); $s.TargetPath = '%TARGET_BAT%'; $s.WorkingDirectory = '%~dp0'; $s.Description = 'Loewix Local VMS Desktop'; $s.Save()"

if exist "%SHORTCUT_PATH%" (
    echo [+] BERHASIL! Shortcut "Loewix Local VMS" telah dibuat di Desktop Windows Anda.
    echo [*] Anda sekarang bisa langsung mengklik icon tersebut di Desktop untuk membuka aplikasi.
) else (
    echo [-] Gagal membuat shortcut secara otomatis. Silakan klik kanan file Start_Loewix_Local.bat dan pilih 'Send to -> Desktop'.
)

echo.
pause
