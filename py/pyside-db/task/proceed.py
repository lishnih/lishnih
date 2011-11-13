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

    logging.debug("ProceedDir: %s" % filename)

    directory = QtCore.QDir(filename)
    directory.setFilter(QtCore.QDir.Dirs | QtCore.QDir.Files | QtCore.QDir.NoSymLinks | QtCore.QDir.NoDotAndDotDot | QtCore.QDir.Hidden)
    directory.setSorting(QtCore.QDir.DirsFirst)

    Dir = save.dir(Reg, dirname=filename, volume=-2)

    # Добавляем к tree_item
    dir_item = DirItem(basename, tree_item)

    # Собираем следующую информацию о директории
    summary = dict(dirs=0, files=0, volume=0)

    # Пролистываем содержимое директории
    for entry in directory.entryInfoList():
        # Директория
        if entry.isDir():
            subdir_item, subdir_summary = ProceedDir(entry.absoluteFilePath(), Reg, dir_item)
            summary['dirs'] += 1
            summary['dirs'] += subdir_summary['dirs']
            summary['files'] += subdir_summary['files']
            summary['volume'] += subdir_summary['volume']

        # Файл
        else:
            file_item, file_summary = ProceedFile(entry, Dir, dir_item)
            summary['files'] += 1
            summary['volume'] += file_summary['size']

    # Обновляем информацию о директории
    Dir.update(summary)

    return dir_item, summary


# Файл передаётся обработкику
def ProceedFile(entry, Reg, tree_item):
    if isinstance(entry, QtCore.QFileInfo):
        filename = entry.absoluteFilePath()
        basename = entry.fileName()
    else:
        filename = entry
        entry = QtCore.QFileInfo(filename)
        basename = entry.fileName()

    logging.debug("ProceedFile: %s" % filename)

    file_size = entry.size()

    File = save.file(Reg, filename=basename, size=file_size)

    # Добавляем к tree_item
    file_item = FileItem(basename, tree_item)

    # Собираем следующую информацию о файле
    summary = dict(size=file_size)

    try:
        handler.file(entry, File, file_item)
    except Exception, e:
        error_str = u"Обработка файла '%s' завершилась с ошибкой: %s" % (filename, e)
        logging.exception(error_str)

    return file_item, summary
