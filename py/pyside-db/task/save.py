#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-03

import time, logging


def table(Reg, table, **kargs):
    Table = Reg.save(table, dict(**kargs))
    return Table


def task(Reg, **kargs):
    task_dict = dict(**kargs)
    task_dict['start'] = int(time.time())

    unique_tuple = 'taskname', 'type', 'source'
    Task = Reg.save('tasks', task_dict, unique=unique_tuple)
    return Task


def dir(Task, **kargs):
    unique_tuple = 'dirname',
    Dir = Task.save('dirs', dict(**kargs), unique=unique_tuple)
    return Dir


def file(Dir, **kargs):
    unique_tuple = 'filename',
    File = Dir.save('files', dict(**kargs), unique=unique_tuple)
    return File
