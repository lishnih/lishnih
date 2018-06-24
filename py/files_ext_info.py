#!/usr/bin/env python
# coding=utf-8
# Stan 2015-05-30

from __future__ import (division, absolute_import,
                        print_function, unicode_literals)

import os


dirname = os.getcwdu()

ext = {}

for root, dirs, files in os.walk(dirname):
    for i in dirs:
        pass

    for i in files:
        e = os.path.splitext(i)[1]
        if e in ext:
            ext[e] = ext[e] + 1
        else:
            ext[e] = 1


for i in ext:
    print("{0:10} {1}".format(i, ext[i]))
