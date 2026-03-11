@echo off
REM ========================================
REM Shree Krishna Services - Deployment Package Creator
REM ========================================

echo.
echo ========================================
echo Creating Production Deployment Package
echo ========================================
echo.

REM Create deployment directory
set DEPLOY_DIR=shreekrishna_production_%date:~-4,4%%date:~-10,2%%date:~-7,2%
echo Creating directory: %DEPLOY_DIR%
mkdir "%DEPLOY_DIR%" 2>nul

REM Copy necessary files
echo.
echo Copying files...

REM HTML files
echo - Copying HTML files...
xcopy /Y *.html "%DEPLOY_DIR%\" >nul

REM Directories
echo - Copying admin/...
xcopy /E /I /Y admin "%DEPLOY_DIR%\admin" >nul

echo - Copying backend/...
xcopy /E /I /Y backend "%DEPLOY_DIR%\backend" >nul

echo - Copying css/...
xcopy /E /I /Y css "%DEPLOY_DIR%\css" >nul

echo - Copying js/...
xcopy /E /I /Y js "%DEPLOY_DIR%\js" >nul

echo - Copying img/...
xcopy /E /I /Y img "%DEPLOY_DIR%\img" >nul

echo - Copying user/...
xcopy /E /I /Y user "%DEPLOY_DIR%\user" >nul

echo - Copying includes/...
xcopy /E /I /Y includes "%DEPLOY_DIR%\includes" >nul

REM Configuration files
echo - Copying .htaccess...
copy /Y .htaccess "%DEPLOY_DIR%\" >nul

REM Database schema
echo - Copying database schema...
mkdir "%DEPLOY_DIR%\database" 2>nul
copy /Y database\schema.sql "%DEPLOY_DIR%\database\" >nul

REM Documentation
echo - Copying documentation...
copy /Y DEPLOYMENT_GUIDE.md "%DEPLOY_DIR%\" >nul
copy /Y PRE_DEPLOYMENT_CHECKLIST.md "%DEPLOY_DIR%\" >nul
copy /Y INSTALL.md "%DEPLOY_DIR%\" >nul

REM Create logs directory
echo - Creating logs directory...
mkdir "%DEPLOY_DIR%\logs" 2>nul
copy /Y logs\.htaccess "%DEPLOY_DIR%\logs\" >nul

REM Create README for deployment
echo - Creating deployment README...
(
echo # Shree Krishna Services - Production Package
echo.
echo This package contains all files needed for production deployment.
echo.
echo ## Quick Start:
echo 1. Read DEPLOYMENT_GUIDE.md
echo 2. Upload all files to your web server
echo 3. Import database/schema.sql
echo 4. Edit includes/config.php with your credentials
echo 5. Test the site
echo.
echo ## Important Files:
echo - DEPLOYMENT_GUIDE.md - Complete deployment instructions
echo - PRE_DEPLOYMENT_CHECKLIST.md - Pre-flight checklist
echo - database/schema.sql - Database structure
echo - includes/config.php - Configuration file ^(MUST EDIT^)
echo.
echo ## Support:
echo For issues, check the error logs in logs/ directory
echo.
echo Generated: %date% %time%
) > "%DEPLOY_DIR%\README.txt"

echo.
echo ========================================
echo Package Created Successfully!
echo ========================================
echo.
echo Location: %CD%\%DEPLOY_DIR%
echo.
echo Next Steps:
echo 1. Review the files in %DEPLOY_DIR%
echo 2. Edit includes/config.php with production settings
echo 3. Compress the folder to ZIP
echo 4. Upload to your web server
echo 5. Follow DEPLOYMENT_GUIDE.md
echo.
echo ========================================
pause
