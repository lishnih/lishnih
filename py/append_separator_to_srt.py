#!/usr/bin/env python
# coding=utf-8
# Stan 2016-03-21

from __future__ import ( division, absolute_import,
                         print_function, unicode_literals )

import os


ext_list = ['txt', 'srt']
eol = None


def proceed(filename, dirname=None, eol=None):
    if dirname:
        filename = os.path.join(dirname, filename)

    with open(filename, 'rb') as f:
        new_lines = []

#       while 1:
#           line = f.readline()
#           if not line:
#               break

        lines = f.readlines()
        for line in lines:

            if not eol:
                eol = determine_eol(line)
                print('EOL determined:', repr(eol))

            line = line.rstrip(b'\r\n')
            line = proceed_line(line)
            line = line + eol
#           print(line, end="")
            new_lines.append(line)

    name, ext = os.path.splitext(filename)
    new_filename = name + ' (new)' + ext
    with open(new_filename, 'wb') as f:
        f.writelines(new_lines)


def proceed_line(line):
    try:
        if line:
            first_symbol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-'
            if line[0] in first_symbol:
                line = line + b' | '
    except UnicodeDecodeError:
        pass

    return line


def determine_eol(line):
    if line[-2:] == b'\r\n':
        eol = b'\r\n'
    else:
        eol = line[-1:]

    return eol


def main():
    dirname = os.getcwdu()

    for root, dirs, files in os.walk(dirname):
#       for i in dirs:
#           pass

        for i in files:
            ext = os.path.splitext(i)[1]
            ext = ext[1:]
            if ext in ext_list:
                print(i)
                proceed(i, root, eol)


if __name__ == "__main__":
    main()
