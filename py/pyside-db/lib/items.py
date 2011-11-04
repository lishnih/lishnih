#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-30

from PySide import QtCore, QtGui

QDir = QtCore.QDir


class Item(QtGui.QTreeWidgetItem):
    def __init__(self, item_name, parent):
        super(Item, self).__init__(parent)
        self.setText(0, item_name)


    def SetError(self, error_text):
        self.setForeground(0, QtGui.QBrush(QtCore.Qt.red))
        self.setData(1, QtCore.Qt.UserRole, error_text)


# Элемент дерева - директория
class DirItem(Item):
    def __init__(self, filename, parent):
        self.directory = QDir(filename)
        self.directory.setFilter(QDir.Dirs | QDir.Files | QDir.NoSymLinks | QDir.NoDotAndDotDot | QDir.Hidden)
        self.directory.setSorting(QDir.DirsFirst)

        self.filename = self.directory.dirName()
        super(DirItem, self).__init__(self.filename, parent)

        font = self.font(0)
        font.setBold(True)
        self.setFont(0, font)

        self.dirs = 0
        self.files = 0
        self.volume = 0


    def __iter__(self):
        for entry in self.directory.entryInfoList():
            yield entry


    def SetSummary(self, dirs, files, volume):
        self.dirs   = dirs
        self.files  = files
        self.volume = volume


    def GetSummary(self):
        return self.dirs, self.files, self.volume


#         self.setData(0, QtCore.Qt.ToolTipRole, tooltip)
#         self.setData(0, QtCore.Qt.UserRole, selected_dir)



# Элемент дерева - директория
class FileItem(Item):
    def __init__(self, entry, parent):
        if not isinstance(entry, QtCore.QFileInfo):
            entry = QtCore.QFileInfo(entry)

        self.entry = entry
        super(FileItem, self).__init__(entry.fileName(), parent)
