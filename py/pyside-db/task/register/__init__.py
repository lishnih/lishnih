#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-23

import os, logging
from PySide import QtSql
import yaml

from db import Db
# from records import Records
from record import Record


script_dir = os.path.dirname(__file__)


# class Register_set(object):
#     def __init__(self, db_set='default'):
# 
#         # Загружаем конфигурацию
#         self.conf_filename = os.path.join(script_dir, "register.yaml")
#         stream = file(self.conf_filename)
#         self.config = yaml.load(stream)
# 
#         # Массивы драйверов БД
#         self.db_list = []
# 
#         db_conn_dict = self.config['db_conn']
#         db_dict = self.config['db']
# 
#         db_name_list = current_task_dict['db']
# 
#         for db_name in db_name_list:
#             logging.debug(u"Инициализируем настройки БД: %s" % db_name)
#             current_db_dict = db_dict[db_name]
#     
#             db_conn_name = current_db_dict['conn']
#             current_db_conn_dict = db_conn_dict[db_conn_name]
#     
#             if current_db_conn_dict:
#                 current_db_dict.update(current_db_conn_dict)
#     
#             try:
#                 db = Db(current_db_dict)
#                 self.db_list.append(db)
#             except:
#                 self.mod = None
#                 error_str = "Db Init for '%s' threw an exception!" % db_name
#                 logging.exception(error_str)
# 
#         self.task = task
#         self.source = source
# 
# 
#     def __iter__(self):
#         for i in self.db_list:
#             yield i
# 
# 
#     def save(self, table_name, record, unique=None):
#         Recs = Records(table_name, self)
#         for i in self:
#             try:
#                  id = i.save(table_name, record, unique)
#                  Recs.append(id, i)
#             except:
#                 logging.exception(record)
#         return Recs


class Register(object):
    def __init__(self, db='default'):

        # Загружаем конфигурацию
        self.conf_filename = os.path.join(script_dir, "register.yaml")
        stream = file(self.conf_filename)
        self.config = yaml.load(stream)

        logging.debug(u"Инициализируем настройки БД: %s" % db)

        # Определяемся с настройками
        db_conf = self.config['db'][db]
        db_conn = db_conf['db_conn']
        del db_conf['db_conn']
        db_conn.update(db_conf)

        try:
            self.Db = Db(db_conn)
        except:
            self.Db = None
            error_str = "Db Driver for '%s' threw an exception!" % db
            logging.exception(error_str)


    def save(self, table_name, record, unique=None):
        try:
            id = self.Db.save(table_name, record, unique)
            Rec = Record(self, table_name, id)
        except:
            Rec = None
            logging.exception(record)
        return Rec



if __name__ == '__main__':
    conf_filename = os.path.join(script_dir, "register.yaml")
    stream = file(conf_filename)
    config = yaml.load(stream)
    for key, value in config.items():
        print key, value

    print
    r = Register()
