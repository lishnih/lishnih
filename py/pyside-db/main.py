#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-22

import sys, logging
from PySide import QtCore, QtGui    # GUI

import init_logging                 # Настраиваем логи
import mainframe                    # Основное окно


# def init_translator():
#     translator = QtCore.QTranslator()
#     translator.load("ru")
#     app.installTranslator(translator)


def main():
#   init_translator()                       # Настройка i18n

    logging.debug("Start!")

    app = QtGui.QApplication(sys.argv)      # Приложение
    frame = mainframe.MainFrame(sys.argv)   # Интерфейс
    frame.show()                            # Показываем
    res = app.exec_()                       # Цикл

    logging.debug("Normal exiting!")
    return res


if __name__ == '__main__':
    sys.exit(main())
