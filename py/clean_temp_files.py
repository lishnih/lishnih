#!/usr/bin/env python
# coding=utf-8
# Stan 2015-05-26

from __future__ import (division, absolute_import,
                        print_function, unicode_literals)

import os
import shutil
import tempfile


os.path.samefile = lambda f1, f2: (
    f1 and f2 and os.path.abspath(f1).lower() == os.path.abspath(f2).lower()
)

dirname = os.getcwdu()

n = 0
tempdir = None

for root, dirs, files in os.walk(dirname):
    if os.path.samefile(root, tempdir):
        print("Temporary dir passed", root)
        continue

    printed = 0

    for i in dirs:
        if i[0:2] == "~$":
            if not printed:
                print("Current dir:", root)
                printed = 1

            print("  Temp dir:", i, "- only logging")

    for i in files:
        if i[0:2] == "~$":
            n = n + 1

            if not tempdir:
                tempdir = tempfile.mkdtemp()
                print("Temporary dir:", tempdir)

            if not printed:
                print("Current dir:", root)
                printed = 1

            print("  Temp file:", i)

            src = os.path.join(root, i)
            dst = os.path.join(tempdir, "{0}_{1}".format(i, n))
            try:
                shutil.move(src, dst)
            except OSError as e:
                print("  File is read only:", i)
            except Exception as e:
                print(e.message)    # 'args', 'errno', 'filename', 'message', 'strerror'
                print(i)


if tempdir:
    print("Temporary dir with the files will be opened!")
    os.startfile(tempdir)
else:
    print("Temp files not found!")
