-- Stan 2011-06-25
-- Определения базовых таблиц БД


-- rev. 20111120
CREATE TABLE if not exists tasks (
  id                INTEGER PRIMARY KEY,
  taskname          VARCHAR,                -- имя задания
  tasktype          VARCHAR,                -- файл/директория
  source            VARCHAR,                -- источник (имя файла)
  start             INTEGER                 -- время создания задания
);


-- rev. 20111120
CREATE TABLE if not exists dirs (
  id                INTEGER PRIMARY KEY,
  _tasks_id         INTEGER,                -- -> tasks/id
  dirname           VARCHAR,                -- имя директории
  dirs              INTEGER,                -- кол-во поддиректорий
  files             INTEGER,                -- кол-во файлов
  volume            INTEGER                 -- объём директории
);


-- rev. 20111120
CREATE TABLE if not exists files (
  id                INTEGER PRIMARY KEY,
  _dirs_id          INTEGER,                -- -> dirs/id
  filename          VARCHAR,                -- имя файла
  size              INTEGER,                -- размер файла
  proceed           INTEGER                 -- файл обрабатывался?
);
