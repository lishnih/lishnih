Attribute VB_Name = "Global_Export_Text"
' Функции экспорта doc в txt (2015-02-16)
' ©2010-2015 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.2 2015-02-26

Function Trim_white_spaces(text)
    text = Replace(text, Chr(95), " ")  ' Подчёркивание '_'
    text = Replace(text, Chr(13), " ")  ' CR (заменяем на пробел)
    text = Replace(text, Chr(10), " ")  ' LF
    text = Replace(text, Chr(7), " ")   ' Табуляция
    Do While InStr(text, "  ")
        text = Replace(text, "  ", " ")
    Loop
    Trim_white_spaces = Trim(text)
End Function

Sub Export_Doc_to_Txt_WalkDir(sDocsPath, txtFile, fs)
    If Right(sDocsPath, 1) <> "\" Then
        sDocsPath = sDocsPath & "\"
    End If

    Set f = fs.GetFolder(sDocsPath)
    Set fc = f.SubFolders
    For Each fDir In fc
        Export_Doc_to_Txt_WalkDir fDir, txtFile, fs
    Next

    lstAttr = vbNormal + vbReadOnly + vbHidden + vbSystem       ' + vbDirectory
    sFileName = Dir(sDocsPath, lstAttr)

    Do While sFileName <> ""
        If sFileName <> "." And sFileName <> ".." Then
            ext = Right(sFileName, 4)
            If ext = ".doc" Then
                sFullName = sDocsPath & sFileName

                Documents.Open filename:=sFullName, _
                    ConfirmConversions:=False, ReadOnly:=True, AddToRecentFiles:=False, _
                    PasswordDocument:="", PasswordTemplate:="", Revert:=False, _
                    WritePasswordDocument:="", WritePasswordTemplate:="", Format:=wdOpenFormatAuto, _
                    DocumentDirection:=wdLeftToRight

                Selection.WholeStory
                txt = Trim_white_spaces(Selection.text)
                txtFile.WriteLine (txt)

                ActiveDocument.Close
            End If
        End If

        sFileName = Dir
    Loop
End Sub


''''''''''''''''''''
' Стартовый макрос '
''''''''''''''''''''

Sub Export_Doc_to_Txt_dir()
    Application.ScreenUpdating = False

    Path1 = "D:\opt\home\recast"            ' Пропишите необходимые пути
    sDocsPath = Path1 + "\docs"
    sTxtFile = Path1 + "\reports_vt.txt"    ' Сохраняем всю текстовку одной строкой

'   On Error Resume Next
    Set fs = CreateObject("Scripting.FileSystemObject")
    Set txtFile = fs.CreateTextFile(sTxtFile, True, True)

    Export_Doc_to_Txt_WalkDir sDocsPath, txtFile, fs

    txtFile.Close
End Sub

