@echo off
REM تشغيل migrations على قاعدة Aiven من جهازك المحلي (بدون Shell في Render)
cd /d "%~dp0"
echo.
echo === ZINOU TV - Migrate Aiven Database ===
echo تأكد من ضبط بيانات Aiven في ملف .env قبل التشغيل
echo.
php artisan migrate --force
echo.
pause
