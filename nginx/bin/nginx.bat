@rem stan 2011-07-20
@echo off

cd ..\local\nginx

IF (%1)==() (
    nginx
) ELSE (
    nginx -s %1
)

if not %ERRORLEVEL% == 0 pause
