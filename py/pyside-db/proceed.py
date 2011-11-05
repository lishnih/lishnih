#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-03

import logging
from PySide import QtCore

import save
from register import Register
from lib.items import DirItem, FileItem


# Функция пролистывает директорию, строит дерево, записывает в БД
def ProceedDir(directory, Reg, tree_item):
    if isinstance(directory, QtCore.QFileInfo):
        filename = directory.absoluteFilePath()
    else:
        filename = directory
        directory = QtCore.QDir(directory)
        directory.setFilter(QtCore.QDir.Dirs | QtCore.QDir.Files | QtCore.QDir.NoSymLinks | QtCore.QDir.NoDotAndDotDot | QtCore.QDir.Hidden)
        directory.setSorting(QtCore.QDir.DirsFirst)

#   logging.debug(filename)

    Dir = save.dir(Reg, 'dir', filename)

    # Добавляем к tree_item
    dir_item = DirItem(directory, tree_item)

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


# Файлы выбираются по расширению, для каждого расширения своя функция обработки
def ProceedFile(entry, Reg, tree_item):
    if isinstance(entry, QtCore.QFileInfo):
        filename = entry.absoluteFilePath()
    else:
        filename = entry
        entry = QtCore.QFileInfo(entry)

#   logging.debug(filename)

    file_size = entry.size()

    File = save.file(Reg, 'file', filename, file_size)

    # Добавляем к tree_item
    file_item = FileItem(entry, tree_item)

    # Собираем следующую информацию о файле
    summary = dict(size=file_size)

#     ext = entry.suffix()
# 
#     ext_py = "%s.py" % ext
#     if self.ext_dir.exists(ext_py):
# 
#         # Загружаем модуль, если ещё не загружен
#         if ext not in self.ext_func:
#             try:
#                 mod = __import__('ext', globals(), locals(), [str(ext)])
#             except:
#                 File.SetError("Module error in %s" % ext)
#                 logging.exception(filename)
#                 return
# 
#             mod = getattr(mod, str(ext))
#             if 'Proceed' in dir(mod):
#                 self.ext_func[ext] = mod.Proceed
#             else:
#                 File.SetError("Proceed not found in %s" % ext)
#                 logging.exception(filename)
#                 return
# 
#         # Выполняем
#         try:
#             if isinstance(filename, QtCore.QFileInfo):
#                 filename = filename.absoluteFilePath()
#             output, error = self.ext_func[ext](filename, reg)
#             if output:
#                 File.setData(0, QtCore.Qt.UserRole, output)
#             if error:
#                 File.SetError(error)
#         except:
#             File.SetError(u"Handler error in %s" % filename)
#             logging.exception(filename)
#             return

    return file_item, summary