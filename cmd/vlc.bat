@echo off
rem stan 2014-05-15


set PATH=%PATH%;C:\Program Files (x86)\VideoLAN\VLC
set SOUT=cam.avi


rem vlc.exe dshow:// :dshow-vdev="USB2.0 UVC VGA WebCam" --sout "#display"
rem vlc.exe dshow:// :dshow-vdev="USB2.0 UVC VGA WebCam" --sout "#standard{access=file,mux=avi,dst=%SOUT%}"
vlc.exe dshow:// :dshow-vdev="USB2.0 UVC VGA WebCam" --sout "#duplicate{dst=display,dst=standard{access=file,mux=avi,dst=%SOUT%}}"


for %%a in (%SOUT%) do set curdate=%%~ta& set sout_n=%%~na& set sout_x=%%~xa
for /f "tokens=1,2,3 delims=: " %%a in ("%curdate%") do set curdate=%%a& set curhour=%%b& set curminute=%%c


rename %SOUT% "%sout_n%_%curdate%_%curhour%%curminute%%sout_x%"

