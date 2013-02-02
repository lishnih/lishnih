@echo off
rem Stan 2011-08-03, 2012-03-16, 2012-10-09


rem Имя директории для резервных копий
rem Закомментируйте переменную, если не хотите помещать архив в директорию
set arcdir=backup

rem Корень имени архива
set arcname=backup
set arcname_7z=%arcname%.7z


set p7z="%ProgramFiles%\7-Zip\7z.exe"
if exist archive.list (
  %p7z% a %arcname_7z% -x@archive.list *
) else (
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
