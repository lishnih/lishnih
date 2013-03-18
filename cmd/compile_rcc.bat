@echo off
rem Stan 2013-02-02


set PYSIDE_PATH="C:\Python27\Lib\site-packages\PySide"

if (%1)==() (
  FOR %%k IN (*.qrc) DO echo "%%k" && "%PYSIDE_PATH%\pyside-rcc.exe" "%%k" -o "%%~nk_rc.py"
) else (
  echo %1
  "%PYSIDE_PATH%\pyside-rcc.exe" %1 -o "%~dpn1_rc.py"
)

pause
