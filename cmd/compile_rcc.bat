@echo off
rem Stan 2013-02-02


set PYSIDE_PATH="C:\Python27\Lib\site-packages\PySide"

FOR %%k IN (*.qrc) DO %PYSIDE_PATH%\pyside-rcc.exe "%%k" -o "%%~nk_rc.py"

pause
