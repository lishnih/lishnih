@rem stan 2011-07-20
@echo off

cd ..\local\php

php-cgi -b 127.0.0.1:9000

if not %ERRORLEVEL% == 0 pause
