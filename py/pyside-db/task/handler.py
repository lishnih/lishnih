#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-05

import logging
from PySide import QtCore

import save
from items import DirItem, FileItem


def file(entry, Reg, tree_item):
    ext = entry.suffix()

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
