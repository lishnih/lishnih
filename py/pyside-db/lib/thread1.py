#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-22

from PySide import QtCore


# Функции обработки запускаем отдельным процессом
# (чтобы не замораживать Gui)
class Thread(QtCore.QThread):
    def __init__(self):
        super(Thread, self).__init__()
        self.res = ""


    def start(self, func, *args):
        self.func = func
        self.args = args
        super(Thread, self).start()
        return self.res


    def run(self):
        if self.func:
            self.res = self.func(*self.args)


th = Thread()
