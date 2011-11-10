#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-24

import os, logging
from PySide import QtCore, QtGui, QtSql


script_dir = os.path.dirname(__file__)


class Db(object):
    def __init__(self, conf):
        self.conf = conf

        self.db = QtSql.QSqlDatabase(conf['type'])
        self.db.setDatabaseName(conf['filename'])

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


    def save(self, table_name, record, unique):
        keys = ','.join(record)
        quotted_values = []
        for value in record.values():
            if isinstance(value, basestring):
                value = value.replace("'", "''")
            quotted_values.append(value)
        values = ','.join([u"'%s'" % i for i in quotted_values])
        sql = u"INSERT INTO %s (%s) VALUES (%s)" % (table_name, keys, values)
#       logging.debug(sql)
        pkid = self.insert(sql)
        return pkid


    def update(self, table_name, record, pk_id):
        expr = u""
        for key, value in record.items():
            if isinstance(value, basestring):
                value = value.replace("'", "''")
            if expr: expr += ', '
            expr += u"%s=%s" % (key, value)
        sql = u"UPDATE %s SET %s WHERE ROWID='%s'" % (table_name, expr, pk_id)
#       logging.debug(sql)
        res = self.request(sql)
        return res


    def insert(self, sql):
        query = QtSql.QSqlQuery(self.db)
        query.prepare(sql)
        res = query.exec_()
        if not res:
            lastError = query.lastError()
            logging.error(u"%s (%s)" % (lastError.text(), lastError.type()))
            logging.error(sql)
        return query.lastInsertId() if res else 0


    def request(self, sql):
        query = QtSql.QSqlQuery(self.db)
        query.prepare(sql)
        res = query.exec_()
        if not res:
            lastError = query.lastError()
            logging.error(u"%s (%s)" % (lastError.text(), lastError.type()))
            logging.error(sql)
        return res


    def get_sql_filename(self, sql_name):
        sql_type = 'sqlite'
        sql_filename = u"%s-%s.sql" % (sql_name, sql_type)
        sql_fullname = os.path.join(script_dir, "sql", sql_filename)
        return sql_fullname


    def exec_sql_file(self, filename):
        f = QtCore.QFile(filename)

        if not f.exists():
            warn_str = u"File '%s' is not exists!" % filename
            logging.warning(warn_str)
            return False

        if not f.open(QtCore.QIODevice.ReadOnly | QtCore.QIODevice.Text):
            warn_str = u"Could not to open file: '%s'!" % filename
            logging.warning(warn_str)
            return False

        sql = f.readAll()
        result = sql.split(';')
        for sql in result:
            sql = sql.trimmed()
            sql = sql.__str__()

            if sql:
                res = self.request(sql)
                if not res:
                    warn_str = u"Query not executed: '%s'!" % sql
                    logging.warning(warn_str)

        return True
