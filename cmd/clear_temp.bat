@echo off
rem Stan 29.09.2006
rem Stan 10.04.2010

del /F/S/Q %TEMP%\*
del /F/S/Q/A:H %TEMP%\*
del /F/S/Q %windir%\temp\*
del /F/S/Q/A:H %windir%\temp\*

cd /d %TEMP%
for /D %%i in (*) do rmdir /S/Q "%%i"

cd /d %windir%\temp
for /D %%i in (*) do rmdir /S/Q "%%i"

pause
