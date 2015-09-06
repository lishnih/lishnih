@rem stan 2011-09-24
@echo off

echo Необходимо запускать с правами Администратора
pause

cd /d %~dp0
mklink /D Programs D:\stan\Cloud@Mail.Ru\Programs

pause
