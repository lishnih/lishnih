#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Stan 2011-06-22

import sys, os
import logging.config
import yaml
from PySide import QtCore, QtGui    # GUI

import mainframe                    # Основное окно


def init_logging():
    global text_log_filename, html_log_filename
    current_dir = os.path.dirname(__file__)
    log_dir = os.path.join(current_dir, "logs")
    # log_dir = os.path.join(os.path.expanduser("~"), "pydata")
    if not os.path.isdir(log_dir):
        os.makedirs(log_dir)
    text_log_filename = os.path.join(log_dir, "log.txt")
    html_log_filename = os.path.join(log_dir, "log.html")

    log_conf_filename = os.path.join("conf", "log.yaml")
    stream = file(log_conf_filename)
    config = yaml.load(stream)
    logging.config.dictConfig(config)


def init_translator():
    translator = QtCore.QTranslator()
    translator.load("ru")
    app.installTranslator(translator)


def main():
#   init_translator()                       # Настройка i18n

    init_logging()                          # Настройка логов
    logging.debug("Start!")

    app = QtGui.QApplication(sys.argv)      # Приложение
    frame = mainframe.MainFrame()           # Создаём окно
    frame.show()                            # Отображаем!
    res = app.exec_()                       # Цикл

    logging.debug("Normal exiting!")
    return res


if __name__ == '__main__':
    sys.exit(main())
