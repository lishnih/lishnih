#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Stan 2011-06-23

from PySide import QtSql
from db import Db


# Логика программы
class Registry(Db):
    def __init__(self, selected_dir = None, new = None):
        super(Registry, self).__init__(selected_dir, new)


    #
    def OnDir(self, filename):
        query = QtSql.QSqlQuery(self.db)
        query.prepare("INSERT INTO dirs (dirname) "
                      "VALUES (:dirname)")
        query.bindValue(":dirname", filename)
        resp = query.exec_()
        if not resp:
            self.lastError = query.lastError()
            self.status = "Query {\n'''%s'''\n} within OnDir(%s) is not executed!" % (query.lastQuery(), filename)
            return False
        return query.lastInsertId()


    #
    def OnDirUpdate(self, dir_id, dir_dirs, dir_files, dir_volume):
        query = QtSql.QSqlQuery(self.db)
#         query.prepare("UPDATE dirs SET (volume, dirs, files) "
#                       "VALUES (:volume, :dirs, :files) WHERE ROWID = :dir_id")
        query.prepare("UPDATE dirs SET volume=:volume, dirs=:dirs, files=:files WHERE ROWID = :dir_id")
        query.bindValue(":volume", dir_volume)
        query.bindValue(":dirs", dir_dirs)
        query.bindValue(":files", dir_files)
        query.bindValue(":dir_id", dir_id)
        resp = query.exec_()
        if not resp:
            self.lastError = query.lastError()
            self.status = "Query {\n'''%s'''\n} within OnDirUpdate is not executed!" % query.lastQuery()
            return False
        return True


    #
    def OnFile(self, entry, dir_id):
        filename = entry.fileName()
        query = QtSql.QSqlQuery(self.db)
        query.prepare("INSERT INTO files (dir_id_, filename, size) "
                      "VALUES (:dir_id_, :filename, :size)")
        query.bindValue(":dir_id_", dir_id)
        query.bindValue(":filename", filename)
        query.bindValue(":size", entry.size())
        resp = query.exec_()
        if not resp:
            self.lastError = query.lastError()
            self.status = "Query {\n'''%s'''\n} within OnFile(%s) is not executed!" % (query.lastQuery(), filename)
            return False
        return True
