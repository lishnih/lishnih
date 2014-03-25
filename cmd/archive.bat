@echo off
rem Stan 2011-08-03


title Archiving...
color 0A
mode con: cols=120 lines=40


rem Имя директории для резервных копий / Dir name for backups
rem Закомментируйте переменную, если не хотите помещать архив в директорию
rem Comment if replacing not required
set arcdir=backup

rem Корень имени архива / Archive name base
set arcname=backup
set arcname_7z=%arcname%.7z


rem Путь к архиватору / Archiver path
set p7z="%ProgramFiles%\7-Zip\7z.exe"


if not exist %p7z% (
  echo Архиватор не найден / A file archiver not found!
  timeout /t 5
  exit -1
)


if exist archive_inc.list (
  echo Archiving files from archive_inc.list
  %p7z% a %arcname_7z% -i@archive_inc.list
  set archived_by_p7z=1
)
if exist archive.list (
  echo Archiving all files from current directory excluding ones from archive.list
  %p7z% a %arcname_7z% -x@archive.list *
  set archived_by_p7z=2
)
if not defined archived_by_p7z (
  echo Archiving all files from current directory
  %p7z% a %arcname_7z% *
)
set el=%ERRORLEVEL%


for %%a in (%arcname_7z%) do set curdate=%%~ta
for /f %%a in ("%curdate%") do set curdate=%%a

set arcname_date_7z=%arcname%_%curdate%.7z


if defined arcdir (
  mkdir %arcdir%
  move %arcname_7z% %arcdir%\%arcname_date_7z%
) else (
  rename %arcname_7z% %arcname_date_7z%
)


IF NOT %el% == 0 pause
