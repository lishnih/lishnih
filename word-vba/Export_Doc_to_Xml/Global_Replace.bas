Attribute VB_Name = "Global_Replace"
' Функции замены определённой строки на заданную в документах doc (2011-06-11)
' Проект "Бованенкого-Ухта", Подрядчик "СГК-Трубопроводстрой"
' ©2011 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.1 2011-06-11

Sub Replace_Text(searching_text, replacement_text, Optional FileName = "")
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
        If FileName <> "" Then
            text = FileName
        Else
            text = "Найдено!"
        End If
        Debug.Print text
    End If
End Sub

Sub Replace_Text_WalkDir(searching_text, replacement_text, dir_text, fs)
    If Right(dir_text, 1) <> "\" Then
        dir_text = dir_text & "\"
    End If

    Set F = fs.GetFolder(dir_text)
    Set fc = F.SubFolders
    For Each fDir In fc
        Replace_Text_WalkDir searching_text, replacement_text, fDir, fs
    Next

    lstAttr = vbNormal + vbReadOnly + vbHidden + vbSystem
    filename_text = Dir(dir_text, lstAttr)

    Do While filename_text <> ""
        If filename_text <> "." And filename_text <> ".." Then
            ext = Right(filename_text, 4)
            If ext = ".doc" Then
                fullname_text = dir_text & filename_text

                Documents.Open FileName:=fullname_text, _
                    ConfirmConversions:=False, ReadOnly:=False, AddToRecentFiles:=False, _
                    PasswordDocument:="", PasswordTemplate:="", Revert:=False, _
                    WritePasswordDocument:="", WritePasswordTemplate:="", Format:=wdOpenFormatAuto, _
                    DocumentDirection:=wdLeftToRight

                Replace_Text searching_text, replacement_text, fullname_text
                ActiveDocument.Close (True)
            End If
        End If

        filename_text = Dir
    Loop
End Sub

'''''''''''''''''''''''''
' Стартовые макросы XML '
'''''''''''''''''''''''''

' Пропишите в обоих функциях необходимые пути

Sub Replace_Text_dir()
    Application.ScreenUpdating = False

    dir_text = "D:\home\bu42\reports\_Заключения_doc"
    searching_text = "Варданян  Ф.В."
    replacement_text = "Варданян Ф.В."

    Set fs = CreateObject("Scripting.FileSystemObject")

    Replace_Text_WalkDir searching_text, replacement_text, dir_text, fs
End Sub

Sub Replace_Text_opened()
'   Application.ScreenUpdating = False

    searching_text = "Варданян  Ф.В."
    replacement_text = "Варданян Ф.В."

    Replace_Text searching_text, replacement_text
End Sub

