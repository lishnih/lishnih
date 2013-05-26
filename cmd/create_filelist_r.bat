@echo off
rem Stan 2013-05-26


rem Имя директории для filename
rem Закомментируйте переменную, если не хотите помещать filename в директорию
set arcdir=backup

rem Имя файла
set filename=list


FOR /R %%i IN (*) DO @echo %%i>>%filename%

for %%a in (%filename%) do set curdate=%%~ta
for /f %%a in ("%curdate%") do set curdate=%%a


if defined arcdir (
  mkdir %arcdir%
  move %filename% %arcdir%\%filename%_%curdate%.txt
) else (
  rename %filename% %filename%_%curdate%.txt
)


pause
