@rem stan 2016-05-19
@echo off

echo Необходимо запускать с правами Администратора
pause

set CURRENT_DIR=%~dp0
cd /d "%CURRENT_DIR%"

for %%a in (".") do set CURRENT_DIR_NAME=%%~na

echo %CURRENT_DIR%
echo %CURRENT_DIR_NAME%

set TARGET_DIR=D:\opt\home\%CURRENT_DIR_NAME%\public_html
set TARGET_DIR_NAME=localhost

mkdir "%TARGET_DIR%"
cd /d "%TARGET_DIR%"

mklink /d "%TARGET_DIR_NAME%" "%CURRENT_DIR%"

pause
