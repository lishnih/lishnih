#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-03

import logging
from PySide import QtCore

import save, handler
from items import DirItem, FileItem


# Функция пролистывает директорию, строит дерево, записывает в БД
def ProceedDir(entry, Reg, tree_item):
    if isinstance(entry, QtCore.QFileInfo):
        filename = entry.absoluteFilePath()
        basename = entry.fileName()
    else:
        filename = entry
        entry = QtCore.QFileInfo(filename)
        basename = entry.fileName()

#   logging.debug(u"ProceedDir: %s" % filename)

    # Делаем запись в БД
    Dir = save.dir(Reg, dirname=filename)

    # Добавляем к tree_item
    dir_item = DirItem(tree_item, basename)

    # Информация о процессе выполнения / директории
    summary = dict(dirs=0, files=0, volume=0)

    directory = QtCore.QDir(filename)
    directory.setFilter(QtCore.QDir.Dirs | QtCore.QDir.Files | QtCore.QDir.NoSymLinks | QtCore.QDir.NoDotAndDotDot | QtCore.QDir.Hidden)
    directory.setSorting(QtCore.QDir.DirsFirst)

    # Пролистываем содержимое директории
    for entry in directory.entryInfoList():
        # Директория
        if entry.isDir():
            subdir_res, subdir_summary = ProceedDir(entry.absoluteFilePath(), Reg, dir_item)
            summary['dirs'] += 1
            summary['dirs'] += subdir_summary['dirs']
            summary['files'] += subdir_summary['files']
            summary['volume'] += subdir_summary['volume']

        # Файл
        else:
            file_res, file_summary = ProceedFile(entry, Dir, dir_item)
            summary['files'] += 1
            summary['volume'] += file_summary['size']

    # Обновляем информацию о директории
    Dir.update(summary)

    res = 0
    dir_item.update(res, summary)

    return res, summary


# Файл передаётся обработкику
def ProceedFile(entry, Reg, tree_item):
    if isinstance(entry, QtCore.QFileInfo):
        filename = entry.absoluteFilePath()
        basename = entry.fileName()
    else:
        filename = entry
        entry = QtCore.QFileInfo(filename)
        basename = entry.fileName()

#   logging.debug(u"ProceedFile: %s" % filename)

    # Делаем запись в БД
    File = save.file(Reg, filename=basename)

    # Добавляем к tree_item
    file_item = FileItem(tree_item, basename)

    # Информация о процессе выполнения / файле
    summary = dict(size=entry.size())

    try:
        res, file_summary = handler.file(entry, File, file_item)
        summary.update(file_summary)
    except Exception, e:
        res = -5
        error_str = u"Обработка файла '%s' завершилась с ошибкой: %s" % (filename, e)
        logging.exception(error_str)

    file_item.update(res, summary)

    return res, summary
