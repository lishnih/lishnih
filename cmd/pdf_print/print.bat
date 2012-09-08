@echo off
rem Stan 2012-05-27

set iv_path=C:\Program Files (x86)\IrfanView
set pkg_path=%CD%

cd print

echo * Печатаем нечётные страницы...
"%iv_path%\i_view32.exe" *.pdf /ini=%pkg_path%\odd /print

echo * Дождитесь окончания печати!
echo * Далее откройте заднюю крышку принтера, чтобы
echo * сохранить нормальный порядок листов (по возрастанию)!

echo *
echo *

pause

echo *
echo *

echo * Печатаем чётные страницы...
"%iv_path%\i_view32.exe" *.pdf /ini=%pkg_path%\even /print

echo * Дождитесь окончания печати!
echo * Далее закройте заднюю крышку принтера!

echo *
echo *

echo * Внимание! Сейчас папка PRINT будет очищена!
echo * Закройте консоль, если хотите отменить удаление файлов!

echo *
echo *

pause

cd %pkg_path%

del print\*.pdf

IF NOT %ERRORLEVEL% == 0 pause
