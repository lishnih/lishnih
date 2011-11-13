#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-22

from PySide import QtCore


# Функции обработки запускаем отдельным процессом
# (чтобы не замораживать Gui)
class Thread(QtCore.QThread):
    def __init__(self):
        super(Thread, self).__init__()

        # Таймер
        self.timer = QtCore.QTimer(self)
        self.update_func = None
        self.ending_func = None
        self.interval = 500
        self.timer.timeout.connect(self.update)

        # Вывод
        self.res = ""


    def set_callback(self, update_func, ending_func=None):
        self.update_func = update_func
        self.ending_func = ending_func


    def update(self):
        if self.isRunning():
            self.secs += self.interval
            if self.update_func:
                self.update_func(self.secs)
        else:
            self.timer.stop()
            if self.ending_func:
                self.ending_func(self.secs)


    def start(self, func, *args):
        self.secs = 0
        self.timer.start(self.interval)

        self.func = func
        self.args = args
        super(Thread, self).start()
        return self.res


    def run(self):
        if self.func:
            self.res = self.func(*self.args)


th = Thread()
