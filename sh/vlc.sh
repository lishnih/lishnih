#!/bin/sh

v4l2-compliance

#vlc v4l2:///dev/video0

d=`date +%F-%H-%M-%S`

#vlc v4l2:///dev/video0 --sout '#display'
#vlc v4l2:///dev/video0 --sout '#standard{access=file,mux=avi,dst=cam_'$d'.avi}'
vlc v4l2:///dev/video0 --sout '#duplicate{dst=display,dst=standard{access=file,mux=avi,dst=cam_'$d'.avi}}'
