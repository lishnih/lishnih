#!/usr/bin/env python
# coding=utf-8
# Stan 2013-02-21

import sys, os

# По задумке, этот код предназначен для того, чтобы загрузать
# один пакет из другого (скрипт сам себя прописывает в sys.path)
# И приём работает ровно до тех пор, тока в обоих пакетах не
# появляются модули с одинаковыми именами.
# Была попытка обернуть загружаемый пакет, но для этого
# необходимо выгрузить все его модули, но это практически
# невозможно, т.к. модули ссылаются друг на друга.
# В общем, в архив ))


# Добавляем текущую директорию к переменной sys.path
index_home = os.path.dirname(__file__)
sys.path.insert(0, index_home)

# Запоминаем загруженные модули перед загрузкой своих
sys_modules = sys.modules.copy()

from export import Proceed
from main import main

del lib
del mainframe
del models
del proceed
del reg

# for key, val in sorted(sys.modules.items()):
#     print key, val

# Возвращаем в исходное состояние
sys.modules = sys_modules

# Удаляем текущую директорию из sys.path
sys.path.pop(0)
