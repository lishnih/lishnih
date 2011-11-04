#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-22

import logging
from PySide import QtCore, QtGui, __version__

from mainframe_ui import Ui_MainWindow
from lib.thread1 import th              # Поток (уже созданный)
import proceed                          # Модуль обработки


class MainFrame(QtGui.QMainWindow):
    def __init__(self, parent=None):
        super(MainFrame, self).__init__()

        self.ui = Ui_MainWindow()
        self.ui.setupUi(self)

        self.cwd = QtCore.QDir.current()
        self.ext_dir = QtCore.QDir(self.cwd.filePath('ext'))
        self.ext_func = dict()

        welcome_sb_str = "PySide version: %s; Qt version: %s" % (__version__, QtCore.__version__)
        self.ui.statusbar.showMessage(welcome_sb_str)

# Слоты

    def OnProceedDir(self):
        if th.isRunning():
            print "running..."
            return

        # Предлагаем выбрать пользователю директорию
        dialog = QtGui.QFileDialog(None, "Select Dir")
        dialog.setFileMode(QtGui.QFileDialog.Directory)
        dialog.setOption(QtGui.QFileDialog.ShowDirsOnly, True)
        if dialog.exec_():
            # Выбираем директорию
            fileNames = dialog.selectedFiles()
            selected_dir = fileNames[0]

            self.ui.tree.clear()

            # Запускаем обработку
            th.start(proceed.ProceedDir, selected_dir, self.ui.tree)

            self.ui.tree.invisibleRootItem()
            #self.ui.tree.topLevelItem().setExpanded(True)

            # Отображаем путь в Статусбаре
            self.ui.statusbar.showMessage(selected_dir)


    def OnProceedFile(self):
        if th.isRunning():
            print "running..."
            return

        # Предлагаем выбрать пользователю файл
        dialog = QtGui.QFileDialog(None, "Select File")
        if dialog.exec_():
            # Выбираем файл
            fileNames = dialog.selectedFiles()
            selected_file = fileNames[0]

            self.ui.tree.clear()

            # Запускаем обработку
            th.start(proceed.ProceedFile, selected_file, self.ui.tree)

            # Отображаем путь в Статусбаре
            self.ui.statusbar.showMessage(selected_file)


    def OnClose(self):
        if th.isRunning():
            print "running..."
            return

        self.ui.tree.clear()


    def OnAbout(self):
        print "rev20111009"


    def OnTreeItemSelected(self):
        ti = self.ui.tree.currentItem()
        out = ti.data(0, QtCore.Qt.UserRole)
        err = ti.data(1, QtCore.Qt.UserRole)
        if not isinstance(out, basestring):
            out = repr(out)
        if not isinstance(err, basestring):
            err = repr(err)
        self.ui.text1.setPlainText(out)
        self.ui.text2.setPlainText(err)
