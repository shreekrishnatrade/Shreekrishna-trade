@echo off
echo Adding favicon to all HTML files...

REM List of HTML files to update (excluding error pages and utility pages)
set FILES=login.html register.html service-request.html membership.html projects.html

for %%f in (%FILES%) do (
    echo Processing %%f...
    powershell -Command "(Get-Content '%%f') -replace '(\s*<link rel=\"stylesheet\" href=\"css/style.css\">)', '$1`r`n  `r`n  <!-- Favicon -->`r`n  <link rel=\"icon\" type=\"image/png\" href=\"img/favicon.png\">' | Set-Content '%%f'"
)

echo.
echo Favicon added to all files!
pause
