Attribute VB_Name = "Global_Replace"
' Функции замены определённой строки на заданную в документах doc (2011-06-11)
' ©2010-2015 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.3 2015-02-26

Sub Replace_Text(searching_text, replacement_text, Optional filename = "")
    Selection.Find.ClearFormatting
    Selection.Find.Replacement.ClearFormatting
    With Selection.Find
        .text = searching_text
        .Replacement.text = replacement_text
        .Forward = True
        .Wrap = wdFindContinue
        .Format = False
        .MatchCase = False
        .MatchWholeWord = False
        .MatchKashida = False
        .MatchDiacritics = False
        .MatchAlefHamza = False
        .MatchControl = False
        .MatchByte = False
        .CorrectHangulEndings = False
        .MatchAllWordForms = False
        .MatchSoundsLike = False
        .MatchWildcards = False
        .MatchFuzzy = False
    End With
    Selection.Find.Execute Replace:=wdReplaceAll

    If Selection.Find.Found Then
        If filename <> "" Then
            text = filename
        Else
            text = "Найдено!"
        End If
        Debug.Print text
    End If
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
            If ext = ".doc" Then
                sFullName = sDocsPath & sFileName

                Documents.Open filename:=sFullName, _
                    ConfirmConversions:=False, ReadOnly:=False, AddToRecentFiles:=False, _
                    PasswordDocument:="", PasswordTemplate:="", Revert:=False, _
                    WritePasswordDocument:="", WritePasswordTemplate:="", Format:=wdOpenFormatAuto, _
                    DocumentDirection:=wdLeftToRight

                Replace_Text searching_text, replacement_text, sFullName

                ActiveDocument.Close (True)
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
    sDocsPath = Path1 + "\docs"
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

