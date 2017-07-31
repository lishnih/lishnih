@echo off
rem Stan 2011-09-28


set SCRIPTS_PATH=C:\Python27\Scripts

if (%1)==() (
  FOR %%k IN (*.ui) DO echo "%%k" && "%SCRIPTS_PATH%\pyside-uic.exe" "%%k" -o "%%~nk_ui.py"
) else (
  echo %1
  "%SCRIPTS_PATH%\pyside-uic.exe" %1 -o "%~dpn1_ui.py"
)

pause
