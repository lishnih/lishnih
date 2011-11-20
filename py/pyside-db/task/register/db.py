#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-24

import os, logging
import sqlite3 as sqlite


script_dir = os.path.dirname(__file__)


class Db(object):
    def __init__(self, conf):
        self._errors = []

        self.conf = conf

        self.conn = sqlite.connect(conf['filename'])
        self.cur = self.conn.cursor()

        self.sql_type = conf['type']

        for i in conf['sql_files']:
            filename = self.get_sql_filename(i)
            res = self.exec_sql_file(filename)  # также обновляет список таблиц


    def __del__(self):
        self.commit()
        self.cur.close()
        self.conn.close()


#     def __nonzero__(self):
#         return self.ok


    # обновляет список таблиц
    def update_tables(self):
        sql = "SELECT name FROM sqlite_master WHERE type='table' " \
              "UNION ALL SELECT name FROM sqlite_temp_master " \
              "WHERE type='table' ORDER BY name";
        self.tables = self.select(sql)


    def error_at_once(self, msg):
        if not msg in self._errors:
            logging.error(msg)
        self._errors.append(msg)


###

    def select_record(self, table_name, record):
        if not table_name in self.tables:
            self.error_at_once("Tables '%s' is not exists!" % table_name)
            return None

        expr = self.serialize_for_select(record)
        sql = u"SELECT ROWID FROM %s WHERE %s" % (table_name, expr)
        pkid_list = self.select(sql)

        if not pkid_list:
            return None
        if len(pkid_list) > 1:
            logging.warning(u"Запись {%r} повторяется в таблице '%s'!" % (record, table_name))
        return pkid_list[0]


    def append_record(self, table_name, record):
        if not table_name in self.tables:
            self.error_at_once("Tables '%s' is not exists!" % table_name)
            return None

        keys, values = self.serialize_for_insert(record)
        sql = u"INSERT INTO %s (%s) VALUES (%s)" % (table_name, keys, values)
        pkid = self.insert(sql)
        return pkid


    # (unique_tuple=None) = append_record
    def save_record(self, table_name, record, unique_tuple=None):
        if not unique_tuple:
            return self.append_record(table_name, record)

        if not table_name in self.tables:
            self.error_at_once("Tables '%s' is not exists!" % table_name)
            return None

        unique_record = {}
        for i in unique_tuple:
            try:
                unique_record[i] = record[i]
            except KeyError:
                logging.warning(u"Ключ '%s' не найден в записи 'record', но задан в 'unique_tuple'!" % i)
        pkid = self.select_record(table_name, unique_record)

        if not pkid:
            pkid = self.append_record(table_name, record)
        return pkid


    def update_record(self, table_name, record, pk_id):
        if not table_name in self.tables:
            self.error_at_once("Tables '%s' is not exists!" % table_name)
            return None

        expr = self.serialize_for_update(record)
        sql = u"UPDATE %s SET %s WHERE ROWID='%s'" % (table_name, expr, pk_id)
        res = self.request(sql)
        return res

###

    def serialize_for_select(self, record):
        expr = u""
        for key, value in record.items():
            if isinstance(value, basestring):
                value = value.replace("'", "''")
            if expr: expr += ' AND '
            expr += u"%s='%s'" % (key, value)
        return expr


    def serialize_for_insert(self, record):
        keys = ','.join(record)
        quotted_values = []
        for value in record.values():
            if isinstance(value, basestring):
                value = value.replace("'", "''")
            quotted_values.append(value)
        values = ','.join([u"'%s'" % i for i in quotted_values])
        return keys, values


    def serialize_for_update(self, record):
        expr = u""
        for key, value in record.items():
            if isinstance(value, basestring):
                value = value.replace("'", "''")
            if expr: expr += ', '
            expr += u"%s='%s'" % (key, value)
        return expr

###

    def commit(self):
        self.conn.commit()


    def request(self, sql):
        try:
            return self.cur.execute(sql)
        except:
            logging.exception(sql)
            return None


    def select(self, sql):
        pkid_list = []
        res = self.request(sql)
        if res:
            for i in self.cur:
                pkid_list.append(i[0])
        return pkid_list


    def insert(self, sql):
        res = self.request(sql)
        pkid = self.cur.lastrowid
        return pkid

###

    def get_sql_filename(self, sql_name, from_dir=None):
        sql_filename = u"%s-%s.sql" % (sql_name, self.sql_type)
        if not from_dir:
            from_dir = script_dir
        sql_fullname = os.path.join(from_dir, "sql", sql_filename)
        return sql_fullname


    def exec_sql_file(self, filename):
        if not os.path.isfile(filename):
            warn_str = u"File '%s' is not exists!" % filename
            logging.warning(warn_str)
            return False

        with open(filename) as f:
            sql = f.read()

        result = sql.split(';')
        for sql in result:
            sql = sql.strip()

            if sql:
                res = self.request(sql)
                if not res:
                    warn_str = u"Query not executed: '%s'!" % sql
                    logging.warning(warn_str)

        self.update_tables()

        return True
