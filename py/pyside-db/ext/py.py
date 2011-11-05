#!/usr/bin/env python
# coding=utf-8
# Stan 2011-07-03


def Proceed(filename, reg=None):
    return filename, None


if __name__ == '__main__':
    if len(sys.argv) > 1:
        out, err = Proceed(sys.argv[1])
        print "Out: %r" % out
        print "Err: %r" % err
