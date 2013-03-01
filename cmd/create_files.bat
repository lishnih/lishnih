@echo off
rem Stan 2013-02-05


mkdir list

FOR /F "delims=" %%i IN (list.txt) DO @echo %%i & @echo.>"list\%%i.txt"

pause
