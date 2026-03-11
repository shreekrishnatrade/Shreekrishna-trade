@echo off
color 0A
title Shree Krishna Services - Quick Deploy Helper

:MENU
cls
echo.
echo ========================================
echo   SHREE KRISHNA SERVICES
echo   Quick Deployment Helper
echo ========================================
echo.
echo What would you like to do?
echo.
echo [1] Create Deployment Package
echo [2] View Production Checklist
echo [3] Test Local Server
echo [4] View Deployment Guide
echo [5] Exit
echo.
set /p choice="Enter your choice (1-5): "

if "%choice%"=="1" goto CREATE_PACKAGE
if "%choice%"=="2" goto VIEW_CHECKLIST
if "%choice%"=="3" goto TEST_SERVER
if "%choice%"=="4" goto VIEW_GUIDE
if "%choice%"=="5" goto EXIT
    
echo Invalid choice! Press any key to try again...
pause >nul
goto MENU

:CREATE_PACKAGE
cls
echo.
echo ========================================
echo Creating Deployment Package...
echo ========================================
echo.
call create-deployment-package.bat
pause
goto MENU

:VIEW_CHECKLIST
cls
echo.
echo ========================================
echo PRE-DEPLOYMENT CHECKLIST
echo ========================================
echo.
type PRE_DEPLOYMENT_CHECKLIST.md
echo.
pause
goto MENU

:TEST_SERVER
cls
echo.
echo ========================================
echo Starting Local Test Server...
echo ========================================
echo.
echo Server will start at: http://127.0.0.1:8000
echo Press Ctrl+C to stop the server
echo.
pause
C:\xampp\php\php.exe -S 127.0.0.1:8000
goto MENU

:VIEW_GUIDE
cls
echo.
echo ========================================
echo DEPLOYMENT GUIDE
echo ========================================
echo.
echo Opening DEPLOYMENT_GUIDE.md...
start DEPLOYMENT_GUIDE.md
echo.
pause
goto MENU

:EXIT
cls
echo.
echo ========================================
echo Thank you for using Quick Deploy Helper!
echo ========================================
echo.
echo Your site is ready for deployment.
echo Follow PRODUCTION_READY.md for next steps.
echo.
echo Good luck with your launch! 🚀
echo.
pause
exit
