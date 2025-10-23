@echo off
echo ========================================
echo  BayanihanCebu Queue Worker
echo ========================================
echo.
echo Starting Laravel queue worker...
echo This will process blockchain recording jobs.
echo.
echo Press CTRL+C to stop the worker.
echo ========================================
echo.

php artisan queue:work --tries=3 --timeout=200 --verbose

pause
