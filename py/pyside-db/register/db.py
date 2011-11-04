#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Stan 2011-06-24

import os, logging
from PySide import QtCore, QtGui, QtSql


script_dir = os.path.dirname(__file__)


class Db(object):
    def __init__(self, conf):
        self.conf = conf
        db_fullname = conf['filename']

        self.db = QtSql.QSqlDatabase("QSQLITE")
        self.db.setDatabaseName(db_fullname)

        self.ok = self.db.open()
        if not self.ok:
            logging.error(self.db.lastError())
            return

        filename = self.get_sql_filename("main")
        self.ok = self.exec_sql_file(filename)


    def __del__(self):
        pass


    def __nonzero__(self):
        return self.ok


    def save(self, record, table):
        keys = ','.join(record)
        values = ','.join([u"'%s'" % i for i in record.values()])
        sql = u"INSERT INTO %s (%s) VALUES (%s)" % (table, keys, values)
        logging.info(sql)
        self.Insert(sql)


    def Select(self, sql):
        query = QtSql.QSqlQuery(self.db)
        resp = query.exec_(sql)
#         fieldNo = query.record().indexOf("country")
#         while query.next():
#             country = query.value(fieldNo)
#             doSomething(country)


    def Insert(self, sql):
        query = QtSql.QSqlQuery(self.db)
        resp = query.exec_(sql)
        if not resp:
            self.lastError = query.lastError()
            self.status = "Query {\n'''%s'''\n} is not executed!" % sql
            return False


    def get_sql_filename(self, sql_name):
        sql_type = 'sqlite'
        sql_filename = "%s-%s.sql" % (sql_name, sql_type)
        sql_fullname = os.path.join(script_dir, "sql", sql_filename)
        return sql_fullname


    def exec_sql_file(self, filename):
        f = QtCore.QFile(filename)
        query = QtSql.QSqlQuery(self.db)

        if not f.exists():
            self.status = "Filename '%s' is not exists!" % filename
            return False

        if not f.open(QtCore.QIODevice.ReadOnly | QtCore.QIODevice.Text):
            self.status = "Filename '%s' is not opened!" % filename
            return False

        sql = f.readAll()
        result = sql.split(';')
        for sql in result:
            sql = sql.trimmed()
            sql = sql.__str__()

            if sql:
                resp = query.exec_(sql)
                if not resp:
                    self.lastError = query.lastError()
                    logging.error(u"Query from '%s' is not executed!" % filename)
                    logging.error(sql)
                    return False

        self.status = "Filename '%s' is executed!" % filename
        return True
