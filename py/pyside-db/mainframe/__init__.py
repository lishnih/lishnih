#!/usr/bin/env python
# coding=utf-8
# Stan 2011-06-22

import sys, os, time, logging
from PySide import QtCore, QtGui, __version__

from mainframe_ui import Ui_MainWindow
from lib.thread1 import th              # Поток (уже созданный)
import task                             # Модуль обработки


company_section = "PySide"
app_section = "Db"


class MainFrame(QtGui.QMainWindow):
    def __init__(self, argv=None):
        super(MainFrame, self).__init__()

        self.ui = Ui_MainWindow()
        self.ui.setupUi(self)

        # Восстанавливаем состояние окна
        settings = QtCore.QSettings(company_section, app_section)
        self.restoreGeometry(settings.value("geometry"))
        self.restoreState(settings.value("windowState"))

        self.sb_message = "PySide version: %s; Qt version: %s" % (__version__, QtCore.__version__)
        self.ui.statusbar.showMessage(self.sb_message)

        th.set_callback(self.update_func, self.ending_func)

        # Если передан параметр - обрабатываем
        if len(argv) > 1:
            filename = argv[1]
            if   os.path.isdir(filename):
                th.start(task.TaskDir, filename, self.ui.tree)
            elif os.path.isfile(filename):
                th.start(task.TaskFile, filename, self.ui.tree)
            else:
                print u"Необходимо задать имя файла или директории!"
                sys.exit(-1)

# Callback-функции для Таймера

    def convert_time(self, msecs):
        secs = int(msecs / 1000)
        hours = int(secs / 3600)
        secs = secs - hours * 3600
        mins = int(secs / 60)
        secs = secs - mins * 60
        time_str = "%02d:%02d:%02d" % (hours, mins, secs)
        return time_str


    def update_func(self, msecs):
        time_str = self.convert_time(msecs)
        self.ui.statusbar.showMessage(u"%s > Processing %s" % (self.sb_message, time_str))


    def ending_func(self, msecs, message=None):
        time_str = self.convert_time(msecs)
        self.ui.statusbar.showMessage(u"%s > Processed in %s (%s)" % (self.sb_message, time_str, message))

# Слоты

    def OnTaskDir(self):
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

            # Отображаем путь в Статусбаре
            self.sb_message = selected_dir
            self.update_func(0)

            # Запускаем обработку
            th.start(task.TaskDir, selected_dir, self.ui.tree)


    def OnTaskFile(self):
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

            # Отображаем путь в Статусбаре
            self.sb_message = selected_file
            self.update_func(0)

            # Запускаем обработку
            th.start(task.TaskFile, selected_file, self.ui.tree)


    def OnClose(self):
        if th.isRunning():
            print "running..."
            return

        self.ui.tree.clear()


    def OnAbout(self):
        print "rev20111113"


    def OnAbout_Qt(self):
        QtGui.QApplication.aboutQt()


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

#

    def closeEvent(self, event):
#       if self.userReallyWantsToQuit():
#           event.accept()
#       else:
#           event.ignore()

        if th.isRunning():
            th.terminate()

        settings = QtCore.QSettings(company_section, app_section)
        settings.setValue("geometry", self.saveGeometry())
        settings.setValue("windowState", self.saveState())

        while th.isRunning():
            print "Still running..."
            time.sleep(1)
