@echo off
rem Stan 2012-05-27

set iv_path=C:\Program Files (x86)\IrfanView
set pkg_path=%CD%

cd print

echo * ���⠥� ���� ��࠭���...
"%iv_path%\i_view32.exe" *.pdf /ini=%pkg_path%\even /print

echo * �������� ����砭�� ����!
echo * ����� ���ன� ������ ����� �ਭ��!

echo *
echo *

echo * ��������! ����� ����� PRINT �㤥� ��饭�!
echo * ���ன� ���᮫�, �᫨ ��� �⬥���� 㤠����� 䠩���!

echo *
echo *

pause

cd %pkg_path%

del print\*.pdf

IF NOT %ERRORLEVEL% == 0 pause
