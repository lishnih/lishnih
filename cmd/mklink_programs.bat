@echo off
@rem stan 2011-09-24


echo Необходимо запускать с правами Администратора
pause


set CURRENT_DIR=%~dp0
cd /d "%CURRENT_DIR%"

cd


set DIR=D:\stan\Cloud@Mail.Ru\Programs

for /D %%a in (%DIR%) do set DIR_NAME=%%~na
echo Target: %DIR%
echo Name: %DIR_NAME%

if exist "%DIR%" (
  mklink /D "%DIR_NAME%" "%DIR%"
) else (
  echo Директория не найдена!
)


pause
