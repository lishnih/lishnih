@rem Stan 2011-08-03, 2012-03-16
@echo off


set arcname=archive
set arcname_7z=%arcname%.7z


"%ProgramFiles%\7-Zip\7z.exe" a %arcname_7z% *
set el=%ERRORLEVEL%


for %%a in (%arcname_7z%) do set curdate=%%~ta
for /f %%a in ("%curdate%") do set curdate=%%a

set arcname_date_7z=%arcname%_%curdate%.7z
rename %arcname_7z% %arcname_date_7z%


IF NOT %el% == 0 pause
