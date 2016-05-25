@echo off
rem stan 2007-10-25

del /F/S/Q *.pyc
del /F/S/Q *.pyo
del /F/S/Q *.py~

FOR /R %%d IN (__pycache__) DO IF EXIST %%d echo %%d && rmdir /S/Q %%d

FOR /D %%d IN (*.egg-info) DO echo %%d && rmdir /S/Q %%d

pause
