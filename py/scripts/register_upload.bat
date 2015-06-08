@echo off
rem stan 2012-02-12

call clean_pyc.bat

setup.py register
pause

py -2 setup.py sdist --formats=gztar upload
py -2 setup.py bdist_wininst upload

py -2 setup.py bdist_egg upload
py -3 setup.py bdist_egg upload

pause
