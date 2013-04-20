@echo off
rem Stan 2013-04-20


mkdir copies

FOR /F "delims=" %%i IN (list.txt) DO IF NOT EXIST %%i (ECHO File not found: %%i) else (ECHO %%i & COPY "%%i" copies>NUL)

pause
