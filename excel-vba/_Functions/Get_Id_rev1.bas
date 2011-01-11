Attribute VB_Name = "Module1"
' Get_Id (December 14, 2010)
' ©2010 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.1

Function Get_Id(Optional inc = 0)
Attribute Get_Id.VB_Description = "©2010 Stan http://lishnih.net"
    file_id = "C:\id.txt"
    ' Windows 7: C:\Users\User\AppData\Local\VirtualStore\id.txt

    Const ForReading = 1, ForWriting = 2, ForAppending = 3
    Const TristateUseDefault = -2, TristateTrue = -1, TristateFalse = 0
    Dim fs, f, ts, s
    Set fs = CreateObject("Scripting.FileSystemObject")

    If fs.FileExists(file_id) Then
        Set f = fs.GetFile(file_id)
        Set ts = f.OpenAsTextStream(ForReading, TristateUseDefault)
        ID = ts.ReadLine
        ts.Close
    Else
        fs.CreateTextFile file_id           ' Create a file
        Set f = fs.GetFile(file_id)
        ID = 1
    End If

    If inc Then
        Set ts = f.OpenAsTextStream(ForWriting, TristateUseDefault)
        ts.Write ID + 1
        ts.Close
    End If

    Get_Id = ID
End Function

