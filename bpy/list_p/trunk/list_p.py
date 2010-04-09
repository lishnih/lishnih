#!BPY
# coding=utf-8
# stan July 25, 2009

"""
Name: 'list_p'
Blender: 249
Group: 'Debug'
Tooltip: 'List primitives of object from active scene'
"""

########################################
#   v 0.1 July 25, 2009                #
########################################

import Blender
from Blender import *

# Полезная фича для отладки, но работает только один раз, повторное
# применение завершает выполнение Blender'а :(
try:
    wx = 1
    from wx.py.PyFilling import main as filling
    #import wx.py.filling as filling
except:
    wx = 0

letter_height = 14      # Высота одной строки
max_lines     = 100     # главное, чтобы было больше кол-ва физически видимых строк
button_lines  = 3       # кол-во строк, отданых для кнопок (внизу области)

#message = ""
output  = []
line = [letter_height*i for i in range(max_lines)]

### Пользовательские функции ###

def get_client_rect():
    scissorbox = BGL.Buffer(BGL.GL_FLOAT, 4)
    BGL.glGetFloatv(BGL.GL_SCISSOR_BOX, scissorbox)
#   left   = int(scissorbox[0])
#   base   = int(scissorbox[1])
    width  = int(scissorbox[2])
    height = int(scissorbox[3])
    return [width, height]

def print_output(text=None):
    if text == None:
        text_list = output
    else:
        text_list = [text]
    width, height = get_client_rect()
    height_lines = int(height / letter_height)
    i = 1
    for str in text_list:
        if i > height_lines - button_lines:
            break
        BGL.glRasterPos2i(8, height - line[i])
        Draw.Text(str)
        i += 1

### Функции нажатий клавиш ###

def onSelect_and_list_object(evt, val):
    scn = Scene.GetCurrent()
    menu = u""
    for i in scn.objects:
        menu = menu + u"|" + unicode(i)
    name = "Object%t" + menu    # если %xN не задано, то нумерация начинается с 1
    result = Draw.PupMenu(name)
    if result:
        print result

def onList_selected_object(evt, val):
    global output, message
    scn = Scene.GetCurrent()
    output = []
    for i in scn.objects.selected:
        output.append(str(i))
        print i
        print u"Faces:"
        for i1 in i.data.faces:
            output.append("  " + str(i1))
            print "  ", i1
        print u"Edges:"
        for i2 in i.data.edges:
            output.append("  " + str(i2))
            print "  ", i2
        print u"Verts:"
        for i3 in i.data.verts:
            output.append("  " + str(i3))
            print "  ", i3
    Draw.Redraw(1)

def onFilling(evt, val):
    filling()
    #fillingFrame = filling.FillingFrame()

### Функции оконного интерфейса Draw (см. http://www.blender.org/documentation/249PythonDoc/) ###

def event(evt, val):    # Функция, вызываемая при различных событиях
    pass
    Draw.Redraw(1)

def button_event(evt):  # Функция, вызываемая при нажатии клавиш
    pass
    #Draw.Redraw(1)

def gui():              # Функция перерисовки окна
    Draw.PushButton(u"Select Object",         0,   5, 5, 100, 20, u"Select Object",         onSelect_and_list_object)
    Draw.PushButton(u"List Selected Objects", 0, 110, 5, 140, 20, u"List Selected Objects", onList_selected_object)
    if wx:
        Draw.PushButton(u"F",                 0, 255, 5,  20, 20, u"Filling",               onFilling)

    if output:
        print_output()
    else:
        print_output(u"empty")

#   scrl = Draw.Scrollbar(101, 0, 40, 10, 20, 20, 0, 100)

Draw.Register(gui, event, button_event)     # регистрируем все три вызова

"""(event, x, y, width, height, initial, min, max, [update, tooltip]) - Create a new Scrollbar

(event) The event number to pass to the button event function when activated
(x, y) The lower left coordinate of the button
(width, height) The button width and height
(initial, min, max) Three values (int or float) specifying the initial and limit values.
[update=1] A value controlling whether the slider will emit events as it is edited.
        A non-zero value (default) enables the events. A zero value supresses them.
[tooltip=] The button's tooltip"""
