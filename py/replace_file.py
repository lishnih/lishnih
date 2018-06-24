#!/usr/bin/env python
# coding=utf-8
# Stan 2018-05-30

from __future__ import (division, absolute_import,
                        print_function, unicode_literals)

import os
import shutil
import time


src = "d:\\tksettings - src.py"
dst = "tksettings.py"
mtime_dst = "2015-06-07 13:14"


if os.path.exists(src):
    stat = os.stat(src)
    mtime_src = time.strftime("%Y-%m-%d %H:%M", time.localtime(stat.st_mtime))
else:
    mtime_src = ""


updated = 0

dirname = os.getcwdu()
for root, dirs, files in os.walk(dirname):
    for i in dirs:
        pass

    for filename in files:
        if filename == dst:
            fullname = os.path.join(root, filename)
            print("{0:100}:".format(fullname), end=" ")

            stat = os.stat(fullname)
            mtime = time.strftime("%Y-%m-%d %H:%M", time.localtime(stat.st_mtime))
            print(mtime, "({0:9}):".format(stat.st_size), end=" ")

            if mtime == mtime_dst:
                shutil.copy2(src, fullname)
                updated += 1
                print("UPDATED")

            else:
                if mtime == mtime_src:
                    print()
                else:
                    print("skipped")


print("Updated {0} files".format(updated))
