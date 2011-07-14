-- Stan 2011-06-25
-- Database of the functions


-- rev. 20110630
CREATE TABLE if not exists dirs (
  id                INTEGER PRIMARY KEY,
  dirname           VARCHAR,                -- имя директории
  dirs              INTEGER default 0,      -- кол-во поддиректорий
  files             INTEGER default 0,      -- кол-во файлов
  volume            INTEGER default 0       -- объём директории
);


-- rev. 20110626
CREATE TABLE if not exists files (
  id                INTEGER PRIMARY KEY,
  dir_id_           INTEGER,                -- -> dirs
  filename          VARCHAR,                -- имя файла
  size              INTEGER default 0       -- размер файла
);
