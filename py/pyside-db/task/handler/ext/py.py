#!/usr/bin/env python
# coding=utf-8
# Stan 2011-07-03

import sys


def Proceed(filename, reg=None, tree_item=None):
    res, summary = 0, {}

    f = open(filename)
    try:
        summary['text'] = f.read()
    except Exception, e:
        summary['err'] = e
        res = -2
    finally:
        f.close()

    return res, summary



def main():
    if len(sys.argv) > 1:
        res, summary = Proceed(sys.argv[1])
        print res
        print summary
        return res



if __name__ == '__main__':
    sys.exit(main())
