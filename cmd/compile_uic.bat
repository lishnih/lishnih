@echo off
rem Stan 2011-09-28


set SCRIPTS_PATH="C:\Python27\Scripts"

FOR %%k IN (*.ui) DO %SCRIPTS_PATH%\pyside-uic.exe "%%k" -o "%%~nk_ui.py"

pause
