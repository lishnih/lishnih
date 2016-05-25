@echo off
rem Stan 2011-08-03


set CURRENT_DIR=%~dp0
cd /d "%CURRENT_DIR%"

for %%a in (".") do set CURRENT_DIR_NAME=%%~na


title Archiving '%CURRENT_DIR_NAME%'...
color 0A
mode con: cols=120 lines=40


call clean_pyc.bat


rem Имя директории для резервных копий
rem Закомментируйте переменную, если не хотите помещать архив в директорию
set arcdir=..\backup

rem Корень имени архива
set arcname=backup_%CURRENT_DIR_NAME%
set arcname_7z=%arcname%.7z


set p7z="%ProgramFiles%\7-Zip\7z.exe"
if exist archive.list (
  %p7z% a %arcname_7z% -x@archive.list *
) else (
  %p7z% a %arcname_7z% *
)
set el=%ERRORLEVEL%


for %%a in (%arcname_7z%) do set fdate=%%~ta
for /f "tokens=1-5 delims=./: " %%a in ("%fdate%") do (
  set fdate=%%c%%b%%a
  set fdatetime=%%c%%b%%a_%%d%%e
)

set arcname_date_7z=%arcname%_%fdatetime%.7z


if defined arcdir (
  mkdir %arcdir%
  move "%arcname_7z%" "%arcdir%\%arcname_date_7z%"
) else (
  rename "%arcname_7z%" "%arcname_date_7z%"
)


IF %el% NEQ 0 echo %el% && pause
