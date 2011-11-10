#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-30

from PySide import QtCore, QtGui


class Item(QtGui.QTreeWidgetItem):
    def __init__(self, item_name, parent):
        super(Item, self).__init__(parent)
        self.setText(0, item_name)


    def SetData(self, summary):
        self.setForeground(0, QtGui.QBrush(QtCore.Qt.blue))
        self.setData(0, QtCore.Qt.UserRole, summary)


    def SetError(self, error_text):
        self.setForeground(0, QtGui.QBrush(QtCore.Qt.red))
        self.setData(1, QtCore.Qt.UserRole, error_text)



# Элемент дерева - директория
class DirItem(Item):
    def __init__(self, filename, parent):
        super(DirItem, self).__init__(filename, parent)

        font = self.font(0)
        font.setBold(True)
        self.setFont(0, font)

        self.setData(0, QtCore.Qt.UserRole, filename)



# Элемент дерева - файл
class FileItem(Item):
    def __init__(self, filename, parent):
        super(FileItem, self).__init__(filename, parent)

        self.setData(0, QtCore.Qt.UserRole, filename)
