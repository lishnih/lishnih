#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-03

import time, logging
from PySide import QtCore

import proceed
from register import Register
from lib.items import DirItem, FileItem


def task(Reg, type, filename):
    task_dict = dict(
                      taskname = 'some_task',
                      type     = type,
                      source   = filename,
                      start    = int(time.time())
                    )
    unique_tuple = 'taskname', 'type', 'source'
    Task = Reg.save('tasks', task_dict, unique=unique_tuple)
    return Task


def dir(Task, type, filename, volume=-1):
    dir_dict = dict(
                     dirname = filename,
                     volume = volume
                   )
    unique_tuple = 'dirname',
    Dir = Task.save('dirs', dir_dict, unique=unique_tuple)
    return Dir


def file(Dir, type, filename, size=-1):
    file_dict = dict(
                      filename = filename,
                      size = size
                    )
    unique_tuple = 'filename',
    File = Dir.save('files', file_dict, unique=unique_tuple)
    return File
