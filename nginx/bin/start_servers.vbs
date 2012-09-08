' stan 2012-09-05
Option Explicit

Dim wsh, oExec
Set wsh = WScript.CreateObject("WScript.Shell")

wsh.CurrentDirectory = "..\local\mysql"
Set oExec = wsh.Exec("bin\mysqld --standalone")

wsh.CurrentDirectory = "..\php"
Set oExec = wsh.Exec("php-cgi -b 127.0.0.1:9000")
' WScript.Echo oExec.Status
' WScript.Echo oExec.ProcessID
' WScript.Echo oExec.ExitCode

wsh.CurrentDirectory = "..\nginx"
Set oExec = wsh.Exec("nginx")

Set wsh = Nothing
