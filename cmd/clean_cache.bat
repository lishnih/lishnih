@echo off
rem stan 2013-08-26

FOR /D %%d IN (runtime\*) DO echo %%d && rmdir /S/Q %%~dpnxd

FOR /D %%d IN (web\assets\*) DO echo %%d && rmdir /S/Q %%~dpnxd

pause
