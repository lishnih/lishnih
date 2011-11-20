#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-30

from PySide import QtCore, QtGui


class Item(QtGui.QTreeWidgetItem):
    def __init__(self, parent, item_name, res=None, summary=None):
        super(Item, self).__init__(parent)
        self.setText(0, item_name)


    def update(self, res=None, summary=None):
        self.setSummary(summary)
        self.setResult(res)


# Условно принимаем следующие коды для res:
#  0 - норм
# -1 - неопределено, присваевается перед обработкой, если не изменилось после
#      обработки, значит, что-то забыл обновить))
# -2 - warning
# -3 - error
# -5 - exception

    def setResult(self, res=None):
        if   res == -1:
            self.setForeground(0, QtGui.QBrush(QtCore.Qt.gray))
        elif res == -2:
            self.setForeground(0, QtGui.QBrush(QtCore.Qt.darkYellow))
        elif res == -3:
            self.setForeground(0, QtGui.QBrush(QtCore.Qt.red))
        elif res == -5:
            self.setForeground(0, QtGui.QBrush(QtCore.Qt.darkRed))


    def setSummary(self, summary=None):
        if summary:
            self.setForeground(0, QtGui.QBrush(QtCore.Qt.blue))
            self.setData(0, QtCore.Qt.UserRole, summary)
        else:
            self.setForeground(0, QtGui.QBrush(QtCore.Qt.black))
            self.setData(0, QtCore.Qt.UserRole, None)



# Элемент дерева - директория
class DirItem(Item):
    def __init__(self, parent, filename, res=None, summary=None):
        super(DirItem, self).__init__(parent, filename, res=res, summary=summary)

        font = self.font(0)
        font.setBold(True)
        self.setFont(0, font)



# Элемент дерева - файл
class FileItem(Item):
    def __init__(self, parent, filename, res=None, summary=None):
        super(FileItem, self).__init__(parent, filename, res=res, summary=summary)
