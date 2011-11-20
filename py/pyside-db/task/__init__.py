#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-03

import logging
from PySide import QtCore

import proceed, save
from register import Register
from items import DirItem



def TaskDir(entry, tree_item):
    taskname = 'default'        # !!!
    tasktype = 'dir'

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
    Task = save.task(Reg, taskname=taskname, tasktype=tasktype, source=filename)

    # Обрабатываем
    res, summary = proceed.ProceedDir(filename, Task, tree_item)

    logging.debug("Task '%s' завершён: %s, %r" % (taskname, res, summary))

    h = tree_item.itemAt(0, 0)
    h.setExpanded(True)
    # Эта команда выдаст такую лабуду:
    # QObject::startTimer: timers cannot be started from another thread
    # но своё дело сделает ))


def TaskFile(entry, tree_item):
    taskname = 'default'        # !!!
    tasktype = 'file'

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
    Task = save.task(Reg, taskname=taskname, tasktype=tasktype, source=filename)

    # Получаем и записываем информацию о директории
    directory = entry.absoluteDir()
    Dir = save.dir(Task, dirname=directory.absolutePath(), volume=-1)

    # Добавляем к tree_item
    dir_item = DirItem(tree_item, directory.dirName())

    # Обрабатываем
    res, summary = proceed.ProceedFile(entry, Dir, dir_item)

    logging.debug("Task '%s' завершён: %s, %r" % (taskname, res, summary))

    dir_item.setExpanded(True)
    # Эта команда тоже выдаст такую лабуду:
    # QObject::startTimer: timers cannot be started from another thread
    # но своё дело сделает ))
