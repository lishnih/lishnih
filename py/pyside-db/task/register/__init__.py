#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-23

import os, logging
import yaml

from db import Db
from record import Record


script_dir = os.path.dirname(__file__)


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
        if self.Db:
            try:
                id = self.Db.save_record(table_name, record, unique)
                Rec = Record(self, table_name, id)
            except:
                Rec = None          # !!!
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
