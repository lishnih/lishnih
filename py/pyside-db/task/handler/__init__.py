#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-05

import sys, os, logging
from PySide import QtCore


script_dir = os.path.dirname(__file__)


def file(entry, Reg=None, tree_item=None):
    res, summary = 0, {}

    if isinstance(entry, QtCore.QFileInfo):
        filename = entry.absoluteFilePath()
#       basename = entry.fileName()
    else:
        filename = entry
        entry = QtCore.QFileInfo(filename)
#       basename = entry.fileName()

    logging.debug(u"File '%s' in Handler Manager!" % filename)

    ext = entry.suffix()
    try:
        ext = str(ext)
    except UnicodeEncodeError:
        logging.warning(u"Расширение '%s' не пендосовское!" % ext)
        ext = None

    if ext:
        mod = __import__('ext', globals(), locals(), [ext])
        ext_mod = getattr(mod, ext) if ext in dir(mod) else None
        if ext_mod:
            ext_func = ext_mod.Proceed if 'Proceed' in dir(ext_mod) else None
            if ext_func:
                try:
                    logging.debug("Handling file '%s' with ext '%s'" % (filename, ext))
                    res, summary = ext_func(filename, Reg, tree_item)
                    tree_item.SetData(summary)
                    Reg.update(dict(proceed=1))
                except:
                    error_str = u"Handler error in %s" % filename
                    logging.exception(error_str)

        else:
            logging.debug(u"Расширение '%s' не обрабатывается!" % ext)
    else:
        logging.debug(u"Расширение отсутствует!")

    return res, summary



def main():
    if len(sys.argv) > 1:
        res, summary = file(sys.argv[1])
        print res
        print summary
        return res



if __name__ == '__main__':
    sys.exit(main())
