' stan 2012-09-05
Option Explicit

Dim wsh, oExec
Set wsh = WScript.CreateObject("WScript.Shell")

wsh.CurrentDirectory = "..\local\mysql"
Set oExec = wsh.Exec("bin\mysqld --standalone")

wsh.CurrentDirectory = "..\nginx"
Set oExec = wsh.Exec("nginx")

wsh.CurrentDirectory = "..\php"
wsh.Run "php-cgi -b 127.0.0.1:9000", 0

Set wsh = Nothing
