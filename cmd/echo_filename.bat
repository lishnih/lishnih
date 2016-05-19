@echo off
rem Stan 2016-04-25


title Title...
color 0A
mode con: cols=120 lines=40


echo %~n0
set el=%ERRORLEVEL%


IF NOT %el% == 0 pause
