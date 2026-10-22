@echo off
chcp 65001 > nul
title تثبيت نظام الطباعة التلقائي
echo ====================================================
echo    تثبيت وكيل الطباعة ليعمل تلقائياً مع الجهاز
echo ====================================================
echo.

set "startupFolder=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup"
set "shortcutName=ShishaPrintAgent.vbs"
set "targetVbs=%~dp0start_agent_hidden.vbs"

echo [1] جاري إعداد التشغيل التلقائي...
copy /Y "%targetVbs%" "%startupFolder%\%shortcutName%" > nul

echo [2] جاري تشغيل وكيل الطباعة في الخلفية الآن...
cscript.exe //nologo "%~dp0start_agent_hidden.vbs"

echo.
echo ====================================================
echo [نجاح] تم التثبيت! وكيل الطباعة يعمل الآن في الخلفية
echo        وسيعمل تلقائياً كل مرة يتم فيها تشغيل الكمبيوتر.
echo        لا داعي لتشغيل أي ملف يدوياً بعد اليوم.
echo ====================================================
echo.
pause
