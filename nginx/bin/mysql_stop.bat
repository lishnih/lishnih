@rem stan 2011-08-04
@echo off

cd ..\local\mysql

bin\mysqladmin.exe -uroot -p54321 shutdown

if not %ERRORLEVEL% == 0 pause
