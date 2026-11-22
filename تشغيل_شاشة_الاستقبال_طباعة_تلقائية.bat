@echo off
chcp 65001 > nul
title تشغيل شاشة الاستقبال - الطباعة التلقائية المباشرة
echo ====================================================
echo    تشغيل شاشة الاستقبال مع تفعيل الطباعة المباشرة
echo ====================================================
echo.

set "PORT=8001"
set "URL=http://127.0.0.1:%PORT%/reception"

:: البحث عن متصفح Google Chrome
set "BROWSER="
if exist "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" (
    set "BROWSER=C:\Program Files (x86)\Google\Chrome\Application\chrome.exe"
) else if exist "C:\Program Files\Google\Chrome\Application\chrome.exe" (
    set "BROWSER=C:\Program Files\Google\Chrome\Application\chrome.exe"
) else if exist "%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe" (
    set "BROWSER=%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe"
) else if exist "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe" (
    set "BROWSER=C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
) else if exist "C:\Program Files\Microsoft\Edge\Application\msedge.exe" (
    set "BROWSER=C:\Program Files\Microsoft\Edge\Application\msedge.exe"
)

if "%BROWSER%"=="" (
    echo [خطأ] لم يتم العثور على Google Chrome أو Edge!
    pause
    exit /b
)

echo [OK] المتصفح: %BROWSER%
echo [OK] الرابط: %URL%
echo.
echo جاري تشغيل شاشة الاستقبال مع ميزة الطباعة الصامتة المباشرة...
echo عند إصدار التذكرة ستطبع بالطابعة فوراً وبدون أي نوافذ أو حوارات.
echo.

start "" "%BROWSER%" --kiosk-printing --app="%URL%"

:: إنشاء اختصار على سطح المكتب إذا لم يكن موجوداً
set "DESKTOP=%USERPROFILE%\Desktop"
set "SHORTCUT=%DESKTOP%\شاشة الاستقبال - طباعة تلقائية.lnk"
if not exist "%SHORTCUT%" (
    powershell -NoProfile -ExecutionPolicy Bypass -Command "$ws = New-Object -ComObject WScript.Shell; $s = $ws.CreateShortcut('%SHORTCUT%'); $s.TargetPath = '%~dp0تشغيل_شاشة_الاستقبال_طباعة_تلقائية.bat'; $s.WorkingDirectory = '%~dp0'; $s.IconLocation = '%BROWSER%,0'; $s.Description = 'تشغيل شاشة الاستقبال مع الطباعة التلقائية'; $s.Save()" > nul 2>&1
    echo [OK] تم إنشاء اختصار على سطح المكتب: "شاشة الاستقبال - طباعة تلقائية"
)

exit

