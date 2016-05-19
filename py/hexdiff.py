#!/usr/bin/env python
# coding=utf-8
# Stan 2016-03-30

from __future__ import ( division, absolute_import,
                         print_function, unicode_literals )

import os


even = 16
column_width = 4 * even + 2
column_separator = '        '


file_list = [
             r'111.opd',
             r'112.opd',
            ]


def proceed_2(filename1, filename2):
    print('          ', end='')
    f1 = filename1 if len(filename1) < column_width else '..' + filename1[-(column_width-2):]
    print(("{:{width}}").format(f1, width=column_width), end='')
    print(column_separator, end='')
    f2 = filename2 if len(filename2) < column_width else '..' + filename2[-(column_width-2):]
    print(f2)

    with open(filename1, 'rb') as f1:
        with open(filename2, 'rb') as f2:

            x1 = x2 = 1
            offset = 0
            while x1 or x2:
                x1 = f1.read(even)
                x2 = f2.read(even)

                print('{:08x}: '.format(offset), end='')
                offset = offset + even

                if x1:
                    print_line(x1)
                print(column_separator, end='')
                if x2:
                    print_line(x2, 0 if x1 else 1)
                print()


def print_line(x, step=0):
    if step:
        print(" " * step * column_width, end='')

    printed = 0
    buf = ''
    for s in x:
        o = ord(s)
        if o < 32:
            s = '.'
        if o >= 128:
            s = '.'

        h = hex(o)[2:]
        if o < 16:
            h = '0' + h
        print(h, end=' ')
        printed = printed + 1
        buf = buf + s

    for i in range(even - printed):
        print('   ', end='')

    print('|', "{:{width}}".format(buf, width=even), end='')


def main():
    filename1 = file_list[0]
    filename2 = file_list[1]

#     dirname = os.getcwdu()
#     if dirname:
#         filename1 = os.path.join(dirname, filename1)
#         filename2 = os.path.join(dirname, filename2)

    proceed_2(filename1, filename2)


if __name__ == "__main__":
    main()
