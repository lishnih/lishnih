#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-03

import time, logging
from PySide import QtCore

import proceed, save
from register import Register
from lib.items import DirItem, FileItem



def TaskDir(filename, tree_item):
    # Создаём Регистратор
    Reg = Register('reports_db')

    # Делаем запись о задании
    Task = save.task(Reg, 'dir', filename)

    dir_item, dir_summary = proceed.ProceedDir(filename, Task, tree_item)

    dir_item.setExpanded(True)


def TaskFile(filename, tree_item):
    # Создаём Регистратор
    Reg = Register('reports_db')

    # Делаем запись о задании
    Task = save.task(Reg, 'file', filename)

    # Получаем и записываем информацию о директории
    entry = QtCore.QFileInfo(filename)
    directory = entry.absoluteDir()
    Dir = save.dir(Task, 'dirs', directory.absolutePath())

    # Добавляем к tree_item
    dir_item = DirItem(directory, tree_item)

    file_item, file_summary = proceed.ProceedFile(entry, Dir, dir_item)

    dir_item.setExpanded(True)
