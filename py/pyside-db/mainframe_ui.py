#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Stan 2011-07-04

from PySide import QtCore, QtGui


# Основное окно
class MainFrameUI(QtGui.QMainWindow):
    def __init__(self):
        super(MainFrameUI, self).__init__()

        self.setWindowTitle("pySide Skeleton")
        self.resize(700, 544)

        self.centralwidget = QtGui.QWidget(self)
        self.gridLayout = QtGui.QGridLayout(self.centralwidget)
        self.gridLayout.setContentsMargins(0, 0, 0, 0)


        self.menubar = QtGui.QMenuBar(self)

        self.menuFile = QtGui.QMenu(self.menubar)
        self.menuFile.setTitle("File")
        self.menuHelp = QtGui.QMenu(self.menubar)
        self.menuHelp.setTitle("Help")

        self.actionNew = QtGui.QAction(self)
        self.actionNew.setText("New")
        self.menuFile.addAction(self.actionNew)
        self.actionOpen = QtGui.QAction(self)
        self.actionOpen.setText("Open")
        self.menuFile.addAction(self.actionOpen)
        self.actionClose = QtGui.QAction(self)
        self.actionClose.setText("Close")
        self.menuFile.addAction(self.actionClose)
        self.menuFile.addSeparator()
        self.actionExit = QtGui.QAction(self)
        self.actionExit.setText("Exit")
        self.menuFile.addAction(self.actionExit)

        self.actionAbout = QtGui.QAction(self)
        self.actionAbout.setText("About")
        self.menuHelp.addAction(self.actionAbout)

        self.menubar.addAction(self.menuFile.menuAction())
        self.menubar.addAction(self.menuHelp.menuAction())
        self.setMenuBar(self.menubar)


        self.statusbar = QtGui.QStatusBar(self)
        self.setStatusBar(self.statusbar)


        self.toolBar = QtGui.QToolBar(self)
        self.addToolBar(QtCore.Qt.TopToolBarArea, self.toolBar)
        self.toolBar.setWindowTitle("toolBar")


        self.tree = QtGui.QTreeWidget(self.centralwidget)
        sizePolicy = QtGui.QSizePolicy(QtGui.QSizePolicy.MinimumExpanding, QtGui.QSizePolicy.Expanding)
        sizePolicy.setHorizontalStretch(1)
        sizePolicy.setVerticalStretch(0)
        self.tree.setSizePolicy(sizePolicy)
        self.tree.setMinimumSize(QtCore.QSize(250, 0))
        self.tree.headerItem().setText(0, "Filename")
        self.gridLayout.addWidget(self.tree, 0, 0, 1, 1)


        self.tab = QtGui.QTabWidget(self.centralwidget)
        sizePolicy = QtGui.QSizePolicy(QtGui.QSizePolicy.Expanding, QtGui.QSizePolicy.Expanding)
        sizePolicy.setHorizontalStretch(2)
        sizePolicy.setVerticalStretch(0)
        self.tab.setSizePolicy(sizePolicy)
        self.tab1 = QtGui.QWidget()
        self.tab.addTab(self.tab1, "")
        self.tab2 = QtGui.QWidget()
        self.tab.addTab(self.tab2, "")
        self.tab.setTabText(0, "Info")
        self.tab.setTabText(1, "Errors")
        self.gridLayout.addWidget(self.tab, 0, 1, 1, 1)

        self.setCentralWidget(self.centralwidget)



        QtCore.QObject.connect(self.actionExit, QtCore.SIGNAL("triggered()"), self.close)
        QtCore.QObject.connect(self.actionNew, QtCore.SIGNAL("triggered()"), self.OnFileNew)
        QtCore.QObject.connect(self.actionOpen, QtCore.SIGNAL("triggered()"), self.OnFileOpen)
        QtCore.QObject.connect(self.actionClose, QtCore.SIGNAL("triggered()"), self.OnFileClose)
