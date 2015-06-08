--[[
@title Switch to Infinity
--]]


-- Open lens
if ( get_mode() == false ) then
    set_record(1)
    while ( get_mode() == false ) do sleep(100) end
end
sleep(1000)


-- MF2 lock focus using levent for key press
print("MF2 mode")
post_levent_for_npt("PressSw1AndMF") 
sleep(500)


-- Set focus
set_focus(65530)
sleep(500)


exit_alt()
