#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-05

import sys, os, logging
from PySide import QtCore


script_dir = os.path.dirname(__file__)


def file(entry, Reg=None, tree_item=None):
    if isinstance(entry, QtCore.QFileInfo):
        filename = entry.absoluteFilePath()
        basename = entry.fileName()
    else:
        filename = entry
        entry = QtCore.QFileInfo(filename)
        basename = entry.fileName()

    ext = str(entry.suffix())
    mod = __import__('ext', globals(), locals(), [ext])
    ext_mod = getattr(mod, ext) if ext in dir(mod) else None
    if ext_mod:
        ext_func = ext_mod.Proceed if 'Proceed' in dir(ext_mod) else None
        if ext_func:
            try:
                output, error = ext_func(filename, Reg, tree_item)
                tree_item.setData(0, QtCore.Qt.UserRole, output)
                tree_item.setData(1, QtCore.Qt.UserRole, error)
                Reg.update(dict(proceed=1))
            except:
                error_str = u"Handler error in %s" % filename
                logging.exception(error_str)

#     else:
#         logging.debug(u"%s не обрабатывается!" % ext)



if __name__ == '__main__':
    if len(sys.argv) > 1:
        file(sys.argv[1])
