#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-03

import logging
from PySide import QtCore

import proceed, save
from register import Register
from items import DirItem



def TaskDir(entry, tree_item):
    taskname = 'default'

    if isinstance(entry, QtCore.QFileInfo):
        filename = entry.absoluteFilePath()
#       basename = entry.fileName()
    else:
        filename = entry
#       entry = QtCore.QFileInfo(filename)
#       basename = entry.fileName()

    logging.debug("TaskDir: %s" % filename)

    # Создаём Регистратор
    Reg = Register(taskname)

    # Делаем запись о задании
    Task = save.task(Reg, type='dir', taskname=taskname, source=filename)

    dir_item, dir_summary = proceed.ProceedDir(filename, Task, tree_item)

    dir_item.setExpanded(True)
    # Эта команда выдаст такую лабуду:
    # QObject::startTimer: timers cannot be started from another thread
    # но своё дело сделает ))


def TaskFile(entry, tree_item):
    taskname = 'default'

    if isinstance(entry, QtCore.QFileInfo):
        filename = entry.absoluteFilePath()
#       basename = entry.fileName()
    else:
        filename = entry
        entry = QtCore.QFileInfo(filename)
#       basename = entry.fileName()

    logging.debug("TaskFile: %s" % filename)

    # Создаём Регистратор
    Reg = Register(taskname)

    # Делаем запись о задании
    Task = save.task(Reg, type='file', taskname=taskname, source=filename)

    # Получаем и записываем информацию о директории
    directory = entry.absoluteDir()
    Dir = save.dir(Task, dirname=directory.absolutePath(), volume=-1)

    # Добавляем к tree_item
    dir_item = DirItem(directory.dirName(), tree_item)

    file_item, file_summary = proceed.ProceedFile(entry, Dir, dir_item)

    dir_item.setExpanded(True)
    # Эта команда тоже выдаст такую лабуду:
    # QObject::startTimer: timers cannot be started from another thread
    # но своё дело сделает ))
