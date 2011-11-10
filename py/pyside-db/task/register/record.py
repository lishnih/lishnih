#!/usr/bin/env python
# coding=utf-8
# Stan 2011-11-04


class Record(object):
    def __init__(self, Reg, table, pk_id):
        self.Reg = Reg
        self.Db = Reg.Db
        self.table = table
        self.pk = 'id'          # !!!
        self.pk_id = pk_id
        self.foreign_field = '_%s_%s' % (self.table, self.pk)


    def save(self, table_name, record, unique=None):
        record[self.foreign_field] = self.pk_id
        if unique:
            unique += self.foreign_field,
        Rec = self.Reg.save(table_name, record, unique=unique)
        return Rec


    def update(self, record):
        self.Db.update(self.table, record, self.pk_id)
