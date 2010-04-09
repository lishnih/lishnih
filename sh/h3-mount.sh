#!/bin/sh
# Stan December 21, 2006

if mount | grep /media/iso; then
  echo 'Файловая система /media/iso используется! Пробуем отмонтировать...';
  umount /media/iso;
else
  echo 'Файловая система /media/iso не используется!';
fi;

if [ -e /mnt/link/iso ]; then
  echo 'Удаляем ссылку /mnt/link/iso';
  unlink /mnt/link/iso;
fi;
echo 'Создаём новую ссылку /mnt/link/iso -> h3.iso';
ln -s /home/stan/HoMM3\ SoD.iso /mnt/link/iso

echo 'Монтируем /mnt/link/iso к /media/iso';
mount /media/iso

echo 'Запускаем игру!';
cd /home/stan/.wine/drive_c/Program\ Files/h3/
wine Heroes3.exe

echo 'Отмонтируем /media/iso и удаляем ссылку!'
if mount | grep /media/iso > /dev/null; then
  umount /media/iso;
fi;
if [ -e /mnt/link/iso ]; then
  unlink /mnt/link/iso;
fi;
