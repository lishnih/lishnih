#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-03

import logging
from PySide import QtCore

from register import Register
from lib.items import DirItem, FileItem


def ProceedDir(filename, tree_item):
    # Создаём экземпляр регистратора и передаём ему название задания
    Reg = Register(filename)


def ProceedFile(filename, tree_item):
    # Создаём экземпляр регистратора и передаём ему название задания
    Reg = Register(filename)


# Файлы выбираются по расширению, для каждого расширения своя функция обработки
def ProceedFile1(self, filename, reg, tree_item):
    # Создаём Файл-объект
    File = FileItem(filename, tree_item)

    # Записываем информацию о файле
    reg.save(File)

    ext = File.entry.suffix()
#       logging.debug(filename)

    ext_py = "%s.py" % ext
    if self.ext_dir.exists(ext_py):

        # Загружаем модуль, если ещё не загружен
        if ext not in self.ext_func:
            try:
                mod = __import__('ext', globals(), locals(), [str(ext)])
            except:
                File.SetError("Module error in %s" % ext)
                logging.exception(filename)
                return

            mod = getattr(mod, str(ext))
            if 'Proceed' in dir(mod):
                self.ext_func[ext] = mod.Proceed
            else:
                File.SetError("Proceed not found in %s" % ext)
                logging.exception(filename)
                return

        # Выполняем
        try:
            if isinstance(filename, QtCore.QFileInfo):
                filename = filename.absoluteFilePath()
            output, error = self.ext_func[ext](filename, reg)
            if output:
                File.setData(0, QtCore.Qt.UserRole, output)
            if error:
                File.SetError(error)
        except:
            File.SetError(u"Handler error in %s" % filename)
            logging.exception(filename)
            return


# Функция пролистывает директорию, строит дерево, записывает в БД
# и вызывает функцию-обработчик для файла
def ProceedDir1(self, filename, reg, tree_item):
    # Создаём Директорию-объект
    Dir = DirItem(filename, tree_item)

    # Раскрываем корень
    if isinstance(tree_item, QtGui.QTreeWidget):
        Dir.setExpanded(True)

    # Записываем информацию о директории
    reg.save(Dir)

    # Собираем следующую информацию о директории
    dir_dirs   = 0
    dir_files  = 0
    dir_volume = 0

    # Пролистываем содержимое директории
    for entry in Dir:
        # Директория
        if entry.isDir():
            SubDir = self.ProceedDir(entry.absoluteFilePath(), reg, Dir)
            dir_dirs1, dir_files1, dir_volume1 = SubDir.GetSummary()
            dir_dirs   += 1
            dir_dirs   += dir_dirs1
            dir_files  += dir_files1
            dir_volume += dir_volume1

        # Файл
        else:
            # Функция обработки файлов
            File = self.ProceedFile(entry, reg, Dir)
            dir_files  += 1
            dir_volume += entry.size()

    # Обновляем информацию о директории
    Dir.SetSummary(dir_dirs, dir_files, dir_volume)
    reg.update(Dir)
    return Dir
