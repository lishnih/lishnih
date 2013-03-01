@echo off
rem Stan 2013-02-26


FOR /D %%i IN (*) DO @echo %%~ni>>list.txt

FOR %%i IN (*) DO @echo %%~ni>>list.txt

pause
