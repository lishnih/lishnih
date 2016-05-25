@echo off
rem Stan 2016-04-25


title Title...
color 0A
mode con: cols=120 lines=40


set CURRENT_DIR=%~dp0
cd /d "%CURRENT_DIR%"

for %%a in (".") do set CURRENT_DIR_NAME=%%~na


set CURRENT_FILE=%~n0
set CURRENT_FILE_EXT=%~n0%~x0


echo %CURRENT_DIR%
echo %CURRENT_DIR_NAME%
echo %CURRENT_FILE%
echo %CURRENT_FILE_EXT%


set el=%ERRORLEVEL%


IF %el% NEQ 0 echo %el% && pause
pause
