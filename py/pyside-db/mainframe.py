#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Stan 2011-06-22

from PySide import QtCore, QtGui
from mainframe_ui import Ui_MainWindow
from items import DirItem, FileItem
from registry import Registry
from thread1 import th


# Основное окно
class MainFrame(QtGui.QMainWindow):
    def __init__(self, parent=None):
        super(MainFrame, self).__init__()

        self.ui = Ui_MainWindow()
        self.ui.setupUi(self)

        self.cwd = QtCore.QDir.current()
        self.ext_dir = QtCore.QDir(self.cwd.filePath('ext'))
        self.ext_func = dict()


# Сервисные функции


    # Файлы выбираются по расширению, для каждого расширения своя функция обработки
    def ProceedFile(self, File, reg):
        filename = File.entry.absoluteFilePath()
        ext = File.entry.suffix()

        # Если модуль был загружен ранее, то выполняем функцию-обработчик файла
        if ext in self.ext_func:
            try:
                return self.ext_func[ext](filename, reg)
            except:
                File.SetError("Handler error in %s" % filename)
                return

        # Иначе, пробуем загрузить модуль, если он описан
        ext_py = "%s.py" % ext
        if self.ext_dir.exists(ext_py):
            try:
                mod = __import__('ext', globals(), locals(), [str(ext)])
            except:
                File.SetError("Module error in %s" % ext)
                return

            mod = getattr(mod, str(ext))
            if 'Proceed' in dir(mod):
                self.ext_func[ext] = mod.Proceed
            else:
                File.SetError("Proceed not found in %s" % ext)


    # Функция пролистывает директорию, строит дерево, записывает в БД
    # и вызывает функцию-обработчик для файла
    def WalkDir(self, selected_dir, tree_item, reg):
        # Создаём Директорию-объект
        Dir = DirItem(selected_dir, tree_item)

        # Раскрываем корень
        if isinstance(tree_item, QtGui.QTreeWidget):
            Dir.setExpanded(True)

        # Делаем запись в БД
        dir_id = reg.OnDir(selected_dir)
        if not dir_id:
            Dir.SetError(reg.getError())
            return Dir

        # Собираем следующую информацию о директории
        dir_dirs   = 0
        dir_files  = 0
        dir_volume = 0

        # Пролистываем содержимое директории
        for entry in Dir:
            # Если директория, то пролистываем её
            if entry.isDir():
                SubDir = self.WalkDir(entry.absoluteFilePath(), Dir, reg)
                dir_dirs1, dir_files1, dir_volume1 = SubDir.GetSummary()
                dir_dirs   += dir_dirs1 + 1 # Считаем и саму вложенную директорию
                dir_files  += dir_files1
                dir_volume += dir_volume1

            # Если файл, то создаём Файл-объект и записываем в БД
            else:
                File = FileItem(entry, Dir)
                dir_files  += 1
                dir_volume += entry.size()

                file_id = reg.OnFile(entry, dir_id)
                if not file_id:
                    File.SetError(reg.getError())
                    continue

                # Функция обработки файлов
                self.ProceedFile(File, reg)

        # Обновляем информацию о директории
        Dir.SetSummary(dir_dirs, dir_files, dir_volume)
        resp = reg.OnDirUpdate(dir_id, dir_dirs, dir_files, dir_volume)
        if not resp:
            Dir.SetError(reg.getError())
        return Dir


# Слоты


    def OnFileNew(self):
        if th.isRunning():
            print "running..."
            return

        # Предлагаем выбрать пользователю директорию
        dialog = QtGui.QFileDialog(None, "Select Project Dir")
        dialog.setFileMode(QtGui.QFileDialog.Directory)
        dialog.setOption(QtGui.QFileDialog.ShowDirsOnly, True)
        if dialog.exec_():
            # Выбираем директорию
            fileNames = dialog.selectedFiles()
            selected_dir = fileNames[0]

            # Инициализуем БД
            Reg = Registry(selected_dir, True)  # True - обнуляем существующую БД
            if not Reg:
                QtGui.QMessageBox.critical(None, "Database Error", \
                    Reg.getError(), QtGui.QMessageBox.Ok)
                return

            # Статусбар отображает имя файла базы данных
            self.ui.statusbar.showMessage(Reg.db_fullname)

            # Строим дерево
            self.ui.tree.clear()
            th.set(self.WalkDir, selected_dir, self.ui.tree, Reg)
            th.start()


    def OnFileOpen(self):
        if th.isRunning():
            print "running..."
            return

        pass


    def OnFileClose(self):
        if th.isRunning():
            print "running..."
            return

        self.tree.clear()
