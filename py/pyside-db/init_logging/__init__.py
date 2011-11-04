#!/usr/bin/env python
# coding=utf-8
# Stan 2011-10-02

import os
import logging.config
import yaml


script_dir = os.path.dirname(__file__)


# Определяем директорию, куда будут складываться логи
log_dir = script_dir
# log_dir = os.path.join(os.path.expanduser("~"), "pydata")
# if not os.path.isdir(log_dir):
#     os.makedirs(log_dir)


# Определяем имена файлов для логов
text_log_filename = os.path.join(log_dir, "log.txt")
html_log_filename = os.path.join(log_dir, "log.html")


# Опция mode=a в секции html_file не работает,
# самостоятельно удаляем файл html_log_filename
if os.path.isfile(html_log_filename):
    os.unlink(html_log_filename)


# Определяем директорию с настройками логов
conf_dir = script_dir


# Загружаем конфигурацию
conf_filename = os.path.join(conf_dir, "log.yaml")
stream = file(conf_filename)
config = yaml.load(stream)

# Настраиваем логи
logging.config.dictConfig(config)
