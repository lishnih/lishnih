@echo off
rem Stan 2013-05-29

FOR /R %%i IN (*) DO (
  IF "%%~ni"=="settings.py" (
    echo %%~dpnxj
  )
)

pause
