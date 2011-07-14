#!/usr/bin/env python
# coding=utf-8
# Stan December 27, 2009
"""Filling is the GUI tree control through which a user can navigate
the local namespace or any object.

This is GTK version of filling.py (wxPython) by
Patrick K. O'Brien <pobrien@orbtech.com>

Замеченные неточности:
При запуске скрипта выполняется построение дерева rootObject на тот
момент, каким он был. Если затем нажать на пункт "locals()", то
текстовый виджет обновится, а дерево - нет. Для обновления дерева
необходимо будет свернуть и раскрыть его заново.

При раскрытии нескольких окон закрытие любого окна приведёт к
завершению скрипта.

Добивает сортировка дерева, как-нить можно изменить порядок?

Пока не очень ясно, как задействовать
self.cell.set_property("is-expander", True)

В текстовой панели не выводится полное имя объекта из выбранного пункта.

Сортировка индексов массивов машинная.

Пока не знаю, как получить разрешение экрана.
"""

__author__  = "Stan Ovchinnikov <lishnih@gmail.com>"
__version__ = "0.01"

import pygtk
pygtk.require("2.0")
import gtk

import wx.py.introspect as introspect

import types, inspect


#~ from wx.py.filling import COMMONTYPES, SIMPLETYPES
COMMONTYPES = [getattr(types, t) for t in dir(types) \
               if not t.startswith('_') \
               and t not in ('ClassType', 'InstanceType', 'ModuleType')]

DOCTYPES = ('BuiltinFunctionType', 'BuiltinMethodType', 'ClassType',
            'FunctionType', 'GeneratorType', 'InstanceType',
            'LambdaType', 'MethodType', 'ModuleType',
            'UnboundMethodType', 'method-wrapper')

SIMPLETYPES = [getattr(types, t) for t in dir(types) \
               if not t.startswith('_') and t not in DOCTYPES]

del DOCTYPES, t


class fillingTreeView:
    ################################
    # Дерево (левая панель)        #
    ################################
    def objHasChildren(self, obj):
        """Возращает True, если объект obj имеет свойства"""
        return True if self.objGetChildren(obj) else False

    def objGetChildren(self, obj):
        """Возращает свойства или содержимое объекта obj"""
        otype = type(obj)
        if otype is types.DictType and hasattr(obj, 'keys'):
            return obj
        d = {}
        if otype is types.ListType or otype is types.TupleType:
            for n in range(len(obj)):
                key = "[%02d]" % n
                d[key] = obj[n]
        if otype not in COMMONTYPES:
            for key in introspect.getAttributeNames(obj):
                try:
                    d[key] = getattr(obj, key)
                except:
                    pass
        return d

    def add_properties(self, parent_iter, obj, isNamespace=False):
        children = self.objGetChildren(obj)
        if children:
            keys = children.keys()
            keys.sort(lambda x, y: cmp(str(x).lower(), str(y).lower()))
            for key in keys:
                child = children[key]

                # Элементы массива заключаем в одинарные кавычки
                # за исключением locals()
                if type(obj) is types.DictType and \
                   type(key) is types.StringType and not isNamespace:
                    key = repr(key)

                piter = self.treestore.append(parent_iter, [key, child])
                if self.objHasChildren(child):
                    #~ self.cell.set_property("is-expander", True)
                    self.treestore.append(piter, ["null", None])    # !!!

    def create_tree(self, obj, label, isNamespace):
        # Создаём объект данных
        self.treestore = gtk.TreeStore(str, object)
        # Создаём объект отображения данных
        self.treeview = gtk.TreeView(self.treestore)
        # Настройки отображения
        self.treeview.set_headers_visible(0)
        self.treeview.set_search_column(0)

        # Создаём колонку и присваиваем ей объект отображения
        self.cell = gtk.CellRendererText()
        self.tvcolumn = gtk.TreeViewColumn(None, self.cell, text=0)
        self.tvcolumn.set_sort_column_id(0)
        self.treeview.append_column(self.tvcolumn)

        # Добавляем данные
        # 0 - название каждого пункта в дереве
        # 1 - ассоциированный объект с данным пунктом
        self.root = self.treestore.append(None, [label, obj])
        # Для корня сразу добавляем в дерево свойства объекта obj
        self.add_properties(self.root, obj, isNamespace)
        # Раскрываем дерево с корня
        self.treeview.expand_row(0, open_all=0)

        # События
        self.treeview.connect("test-expand-row",   self.onRowExpanding)
        self.treeview.connect("cursor-changed", self.onCursorChanged)
        self.treeview.connect("row-activated",  self.onRowActivated)

        scrolled_window = gtk.ScrolledWindow()
        scrolled_window.set_policy(gtk.POLICY_AUTOMATIC, gtk.POLICY_AUTOMATIC)
        scrolled_window.add_with_viewport(self.treeview)
        return scrolled_window

    # Обработчики событий
    def onRowExpanding(self, treeview, iter, path):
        # Раскрытие пункта
        child_iter = self.treestore.iter_children(iter)
        while child_iter != None:
            self.treestore.remove(child_iter)
            child_iter = self.treestore.iter_children(iter)
        property = self.treestore.get_value(iter, 1)
        self.add_properties(iter, property)

    def onCursorChanged(self, treeview):
        # Выбор пункта
        treestore, iter = treeview.get_selection().get_selected()
        # При запуске скрипта iter = None
        if not iter:
            iter = self.treestore.get_iter(0)
        property = treestore.get_value(iter, 1)
        self.insert_text(property, iter)

    def onRowActivated(self, treeview, path, tvcolumn):
        # Двойной клик на пункте
        if path != (0,):
            iter = self.treestore.get_iter(path)
            name     = self.treestore.get_value(iter, 0)
            property = self.treestore.get_value(iter, 1)
            fillingTreeView(property, name)

    ################################
    # Текст (правая панель)        #
    ################################
    def insert_text(self, obj, iter=None):
        name_str = "%s\n\n" % self.treestore.get_value(iter, 0) \
                   if iter else ""  # !!!
        otype = type(obj)
        try:
            value_str = unicode(obj)
        except:
            value_str = repr(obj)

        doc_str = ""
        try:
            if otype not in SIMPLETYPES:
                doc_str = '\n\nDocstring:\n\n"""%s"""' % \
                          inspect.getdoc(obj).strip()
        except:
            pass

        class_str = ""
        try:
            if otype is types.InstanceType:
                class_str = "\n\nClass Definition:\n\n%s" % \
                            inspect.getsource(obj.__class__)
            else:
                class_str = "\n\nSource Code:\n\n%s" % \
                            inspect.getsource(obj)
        except:
            pass
        text = """%sType: %s

Value: %s%s%s""" % (name_str, str(otype), value_str, doc_str, class_str)
        self.buffer.set_text(text)
   
    def create_text(self):
        view = gtk.TextView()
        view.set_wrap_mode(gtk.WRAP_WORD)
        self.buffer = view.get_buffer()

        scrolled_window = gtk.ScrolledWindow()
        scrolled_window.set_policy(gtk.POLICY_AUTOMATIC, gtk.POLICY_AUTOMATIC)
        scrolled_window.add(view)
        return scrolled_window

    ################################
    # Конструктор                  #
    ################################
    def __init__(self, rootObject=None, rootLabel="..."):
        if rootObject == None:
            import __main__
            rootObject = __main__.__dict__
            rootIsNamespace = True
            rootLabel = "locals()"
        else:
            rootIsNamespace = False

        # Окно
        self.window = gtk.Window(gtk.WINDOW_TOPLEVEL)
        self.window.set_size_request(800, 600)
        self.window.set_border_width(4)
        self.window.set_title("pyGTK_Filling")

        self.window.connect("destroy", lambda w: gtk.main_quit())
        #~ self.window.connect("delete_event", lambda w, e: gtk.main_quit())

        # Горизонтальное расположение панелей в окне
        hpaned = gtk.HPaned()
        hpaned.set_position(200)
        self.window.add(hpaned)

        # Левая панель: Дерево свойств объекта rootObject
        self.tree = self.create_tree(rootObject, rootLabel, rootIsNamespace)
        hpaned.add1(self.tree)

        # Правая панель: Описание выбранного свойства
        text = self.create_text()
        hpaned.add2(text)

        self.window.show_all()


def main():
    #~ from wx.py.PyFilling import main
    tv = fillingTreeView()
    gtk.main()

if __name__ == "__main__":
    main()
