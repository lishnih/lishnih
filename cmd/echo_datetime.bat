@echo off
rem Stan 2016-05-23


set filename=echo_datetime.bat


for %%a in (%filename%) do set fdate=%%~ta
for /f "tokens=1-4 delims=./ " %%a in ("%fdate%") do (
  set fdate=%%c-%%b-%%a
  set fdatetime=%%c-%%b-%%a_%%d
)


echo %fdate%
echo %fdatetime%


set el=%ERRORLEVEL%


IF %el% NEQ 0 echo %el% && pause
pause
