@echo off
:menu
cls
echo ==========================================
echo       AGENDA EGOV - DEV TOOLS
echo ==========================================
echo [1] Clear DB ^& Cache (Fresh Migrate)
echo [2] Reset DB + Full Reseed
echo [3] Clear All Caches Only
echo [4] Exit
echo ==========================================
set /p opt="Pilih menu (1-4): "

if "%opt%"=="1" goto clear_db
if "%opt%"=="2" goto reseed
if "%opt%"=="3" goto clear_cache
if "%opt%"=="4" goto exit
goto menu

:clear_db
echo.
echo Cleaning Database and Caches...
php artisan migrate:fresh && php artisan optimize:clear
echo.
echo [DONE] Database has been wiped and migrated.
echo [DONE] All application caches have been cleared.
pause
goto menu

:reseed
echo.
echo Resetting Database and Seeding...
php artisan migrate:fresh --seed
echo.
echo [DONE] Database reset and seeded with initial data.
pause
goto menu

:clear_cache
echo.
echo Clearing Caches...
php artisan optimize:clear
echo.
echo [DONE] Application cache, route, config, and view cleared.
pause
goto menu

:exit
exit
