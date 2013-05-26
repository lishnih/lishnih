@echo off
rem Stan 2013-04-23

mkdir copies
echo.>list1.txt

FOR /F %%i IN (list.txt) DO (
  FOR /R %%j IN (*) DO (
    IF "%%~nj"=="%%i" (
      echo %%~dpnxj
      echo %%~nj>>list1.txt
      copy /Y "%%j" copies > NUL
    )
  )
  rem IF "%CURRENT%"=="0" echo "Entry not fount:", %%i
)

pause
