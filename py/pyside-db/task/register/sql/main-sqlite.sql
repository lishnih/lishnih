-- Stan 2011-06-25
-- Определения базовых таблиц БД


-- rev. 20111104
CREATE TABLE if not exists tasks (
  id                INTEGER PRIMARY KEY,
  taskname          VARCHAR,                -- имя задания
  type              VARCHAR,                -- файл/директория
  source            VARCHAR,                -- источник (имя файла)
  start             INTEGER default 0       -- время создания задания
);


-- rev. 20111102
CREATE TABLE if not exists dirs (
  id                INTEGER PRIMARY KEY,
  _tasks_id         INTEGER,                -- -> tasks/id
  dirname           VARCHAR,                -- имя директории
  dirs              INTEGER default 0,      -- кол-во поддиректорий
  files             INTEGER default 0,      -- кол-во файлов
  volume            INTEGER default 0       -- объём директории
);


-- rev. 20111106
CREATE TABLE if not exists files (
  id                INTEGER PRIMARY KEY,
  _dirs_id          INTEGER,                -- -> dirs/id
  filename          VARCHAR,                -- имя файла
  size              INTEGER default 0,      -- размер файла
  proceed           INTEGER default 0       -- файл обрабатывался?
);
