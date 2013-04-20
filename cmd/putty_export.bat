@rem stan 2011-08-05
@rem http://guruadmin.ru/page/kak-peredat-nastrojki-putty-na-druguyu-windows-mashinu
@echo off

regedit /e "%userprofile%\desktop\putty-registry.reg" HKEY_CURRENT_USER\Software\Simontatham

pause
