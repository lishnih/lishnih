Attribute VB_Name = "Global_Replace"
' Функции замены определённой строки на заданную в документах xls (2015-02-26)
' ©2010-2015 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.1 2015-02-26

Sub Replace_Text(searching_text, replacement_text, Optional filename = "")
    Cells.Replace What:=searching_text, Replacement:=replacement_text, LookAt:=xlPart, _
        SearchOrder:=xlByRows, MatchCase:=False, SearchFormat:=False, _
        ReplaceFormat:=False
End Sub

Sub Replace_Text_WalkDir(searching_text, replacement_text, sDocsPath, fs)
    If Right(sDocsPath, 1) <> "\" Then
        sDocsPath = sDocsPath & "\"
    End If

    Set f = fs.GetFolder(sDocsPath)
    Set fc = f.SubFolders
    For Each fDir In fc
        Replace_Text_WalkDir searching_text, replacement_text, fDir, fs
    Next

    lstAttr = vbNormal + vbReadOnly + vbHidden + vbSystem       ' + vbDirectory
    sFileName = Dir(sDocsPath, lstAttr)

    Do While sFileName <> ""
        If sFileName <> "." And sFileName <> ".." Then
            ext = Right(sFileName, 4)
            If ext = ".xls" Then
                sFullName = sDocsPath & sFileName

                Workbooks.Open filename:=sFullName

                Replace_Text searching_text, replacement_text, sFullName

                ActiveWorkbook.Save
                ActiveWindow.Close
            End If
        End If

        sFileName = Dir
    Loop
End Sub


'''''''''''''''''''''
' Стартовые макросы '
'''''''''''''''''''''

Sub Replace_Text_dir()
    Application.ScreenUpdating = False

    Path1 = "D:\opt\home\recast"            ' Пропишите необходимые пути
    sDocsPath = Path1 + "\xls"
    searching_text = ""
    replacement_text = ""

'   On Error Resume Next
    If searching_text <> "" Then
        Set fs = CreateObject("Scripting.FileSystemObject")
        Replace_Text_WalkDir searching_text, replacement_text, sDocsPath, fs
    End If
End Sub

Sub Replace_Text_opened()
'   Application.ScreenUpdating = False

    searching_text = ""
    replacement_text = ""

    If searching_text <> "" Then
        Replace_Text searching_text, replacement_text
    End If
End Sub

