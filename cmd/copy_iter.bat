@echo off
rem Stan 2014-05-25


set tmpl=Вопрос.docx

FOR /L %%i IN (1,1,60) DO IF EXIST "%%i. %tmpl%" (ECHO File exist: %%i) else (ECHO Create file: %%i & COPY "%tmpl%" "%%i. %tmpl%")


pause
