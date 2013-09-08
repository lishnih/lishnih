@echo off
rem Stan 2013-09-08

echo.>list.txt

FOR /D %%i IN (c:\py*) DO echo %%i>>list.txt

echo.>>list.txt

FOR /D %%i IN (c:\py*) DO dir /S/D %%i>>list.txt

pause
