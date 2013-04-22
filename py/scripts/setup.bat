@echo off
rem stan 2012-02-12

call clean_pyc.bat

py setup.py sdist --formats=gztar
py setup.py install

pause
