@title Switch to Infinity


rem Open lens
x = get_capture_mode
if x = 0 then
    set_record 1
endif
sleep 3000


rem Switch to MF
click "left"
click "right"
sleep 500


rem Lock focus
rem set_aflock 1
rem sleep 500


rem Set focus
set_focus 65530
sleep 500


exit_alt


end