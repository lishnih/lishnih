#!/usr/bin/env python
# coding=utf-8
# Stan 2018-05-31

from __future__ import (division, absolute_import,
                        print_function, unicode_literals)

import sys
import os
import argparse
import fnmatch
import time


parser = argparse.ArgumentParser(description="Searching for the file")
parser.add_argument('file', nargs=1,
                    help="file to be found")
parser.add_argument('-d', '--dir',
                    help='specify the directory for searching')

if sys.version_info >= (3,):
    argv = sys.argv
else:
    fse = sys.getfilesystemencoding()
    argv = [i.decode(fse) for i in sys.argv]

args = parser.parse_args(argv[1:])


def print_compare(fullname):
    print("{0:100}:".format(fullname), end=" ")

    stat = os.stat(fullname)
    if size_src == stat.st_size and mtime_src == stat.st_mtime:
        print("Files are equal")

    else:
        if size_src != stat.st_size:
            print("Size differs: {0}".format(stat.st_size), end=" ")
        if mtime_src != stat.st_mtime:
            mtime = time.strftime("%Y-%m-%d %H:%M", time.localtime(stat.st_mtime))
            print("MTime differs: {0}".format(mtime, mtime_src, stat.st_mtime), end=" ")
        print()


filename = args.file[0]
if os.path.exists(filename):
    os.stat_float_times(False)
    stat = os.stat(filename)
    size_src = stat.st_size
    mtime_src = stat.st_mtime
    mtime_text = time.strftime("%Y-%m-%d %H:%M", time.localtime(stat.st_mtime))

    src = os.path.basename(filename)
    root, ext = os.path.splitext(src)
    src_ptn = "{0}*{1}".format(root, ext)

    print("Source: {0} ({1}) ({2})".format(filename, size_src, mtime_text))
    print()

    dirname = args.dir if args.dir else os.getcwdu()
    similar = []
    for root, dirs, files in os.walk(dirname):
        for filename in files:
            if filename == src:
                fullname = os.path.join(root, filename)
                print_compare(fullname)

            elif fnmatch.fnmatch(filename, src_ptn):
                fullname = os.path.join(root, filename)
                similar.append(fullname)

    if similar:
        print()
        print(src_ptn)

        for fullname in similar:
            print_compare(fullname)
