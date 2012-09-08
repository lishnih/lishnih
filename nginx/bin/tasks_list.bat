@rem stan 2012-09-07
@echo off

echo nginx
for /f "tokens=2 delims=," %%a in ('tasklist /nh /fo csv^|find /i "nginx"')  do echo %%a

echo mysqld
for /f "tokens=2 delims=," %%a in ('tasklist /nh /fo csv^|find /i "mysqld"') do echo %%a

echo php
for /f "tokens=2 delims=," %%a in ('tasklist /nh /fo csv^|find /i "php"')    do echo %%a

pause
