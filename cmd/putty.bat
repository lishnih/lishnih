@echo off
rem Stan 2013-03-11


regedit /e "%userprofile%\desktop\putty.reg" HKEY_CURRENT_USER\Software\Simontatham
regedit /e "%userprofile%\desktop\putty-sessions.reg" HKEY_CURRENT_USER\Software\SimonTatham\PuTTY\Sessions

pause
