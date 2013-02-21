@echo off
rem Stan 2007-10-25


del /F/S/Q *.pyc
del /F/S/Q *.pyo
del /F/S/Q *.py~

FOR /D %%d IN (*.egg-info) DO rmdir /S/Q %%d


pause
