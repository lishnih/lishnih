#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Stan 2011-06-22

import sys
from PySide import QtCore, QtGui    # GUI
import mainframe                    # Основное окно


def main():
    app = QtGui.QApplication(sys.argv)      # Приложение

#     translator = QtCore.QTranslator()
#     translator.load("ru")
#     app.installTranslator(translator)

    frame = mainframe.MainFrame()           # Окно
    frame.show()                    # Отображаем окно
    return app.exec_()              # Запускаем приложение


if __name__ == '__main__':
    sys.exit(main())
