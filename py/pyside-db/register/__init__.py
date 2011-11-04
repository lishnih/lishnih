#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-23

import os, logging
from PySide import QtSql
import yaml

from db import Db


script_dir = os.path.dirname(__file__)


class Register(object):
    def __init__(self, source=None):
        self.error_list = []

        # Загружаем конфигурацию
        self.conf_filename = os.path.join(script_dir, "register.yaml")
        stream = file(self.conf_filename)
        self.config = yaml.load(stream)

        task = source
        # Определяем задание
        if not task in self.config['task']:
            logging.debug(u"Задание не описано: %s, используем параметры по умолчанию!" % task)
            task = 'default'

        # Инициализируем базы данных
        self.db_list = []

        current_task_dict = self.config['task'][task]
        db_conn_dict = self.config['db_conn']
        db_conf_dict = self.config['db']

        db_conf_name_list = current_task_dict['db']

        for db_conf_name in db_conf_name_list:
            logging.debug(u"Инициализируем настройки БД: %s" % db_conf_name)
            current_db_conf_dict = db_conf_dict[db_conf_name]
    
            db_conn_name = current_db_conf_dict['conn']
            current_db_conn_dict = db_conn_dict[db_conn_name]
    
            if current_db_conn_dict:
                current_db_conf_dict.update(current_db_conn_dict)
    
            try:
                db = Db(current_db_conf_dict)
                self.db_list.append(db)
            except:
                self.mod = None
                error_str = "Db Init for '%s' threw an exception!" % db_conf_name
                logging.exception(error_str)
                self.error_list.append(error_str)

        task_dict = dict(taskname=task, source=source)
        self.save(task_dict, 'tasks')

        self.task = task
        self.source = source



    def __del__(self):
        pass


    def __iter__(self):
        for i in self.db_list:
            yield i


    def save(self, record, table):
        for i in self:
            try:
                i.save(record, table)
            except:
                logging.exception(record)


    def update(self, record, table):
        for i in self:
            try:
                i.update(record, table)
            except:
                logging.exception(record)
