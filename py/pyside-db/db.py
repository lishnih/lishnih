#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Stan 2011-06-24

from PySide import QtCore, QtSql


# Логика программы
class Db(object):
    def __init__(self, db_dir, new = None):
        self.lastError = None
        self.cwd = QtCore.QDir.current()
        self.db_dir = QtCore.QDir(db_dir)
        self.db_name = ".funcs.db"
        self.db_fullname = self.db_dir.filePath(self.db_name)

        if new:
            self.db_dir.remove(self.db_name)

        self.db = QtSql.QSqlDatabase("QSQLITE")
        self.db.setDatabaseName(self.db_fullname)

        self.ok = self.db.open()
        if self.ok:
            self.status = "Connection established!"
            filename = self.get_sql_filename("main")
            self.ok = self.exec_sql_file(filename)
        else:
            self.lastError = self.db.lastError()
            self.status = "Connection failed!"


    def __nonzero__(self):
        return self.ok


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


    def getError(self):
        if self.lastError:
            return "%s\n\n%s (%s)" % (self.status, self.lastError.text(), self.lastError.type())
        else:
            return self.status 


    def get_sql_filename(self, sql_name):
        sql_type = 'sqlite'
        return self.cwd.filePath("sql/%s-%s.sql" % (sql_name, sql_type))


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
                    self.status = "Query {\n'''%s'''\n} from '%s' is not executed!" % (sql, filename)
                    return False

        self.status = "Filename '%s' is executed!" % filename
        return True
