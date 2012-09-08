@rem stan 2011-07-20
@echo off

cd ..\local\mysql

bin\mysqld --standalone

if not %ERRORLEVEL% == 0 pause
