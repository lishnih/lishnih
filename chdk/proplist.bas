rem stan 2014-03-04

@title Prop List

@param a from
@default a 1
@param b to
@default b 297

x = get_capture_mode
print "Capture mode", x
if x = 0 then
    x = 100
endif
print_screen x

rem Returns 1 for propset1, 2 for propset2, 3 for propset3 etc.
p = get_propset
print "Propset", p

sleep 1000

k = 0
for n = a to b
    get_prop n x
    if x <> 0 then
        if k = 5 then
            sleep 1000
            cls
            k = 0
        endif

        print n, x
        k = k + 1
    endif
next n
end