Attribute VB_Name = "Global_Export"
' Функции экспорта заключений doc заключения xml и таблицу csv (2010-12-02)
' Проект "Бованенкого-Ухта", Подрядчик "СГК-Трубопроводстрой"
' ©2010 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.5 2011-06-14

Sub CreateFolder(dirname)
    Set fs = CreateObject("Scripting.FileSystemObject")
    If Not fs.FolderExists(dirname) Then
        fs.CreateFolder (dirname)
    End If
End Sub

Function Trim_white_spaces(text)
    text = Replace(text, Chr(95), "")   ' Подчёркивание '_'
    text = Replace(text, Chr(13), " ")  ' CR (заменяем на пробел)
    text = Replace(text, Chr(10), "")   ' LF
    text = Replace(text, Chr(7), "")    ' Табуляция
    Trim_white_spaces = Trim(text)
End Function

Sub Run_Find(search_text)
    Selection.Find.ClearFormatting
    With Selection.Find
        .text = search_text
        .Replacement.text = ""
        .Forward = True
        .Wrap = wdFindContinue
        .Format = False
        .MatchCase = False
        .MatchWholeWord = False
        .MatchWildcards = False
        .MatchSoundsLike = False
        .MatchAllWordForms = False
    End With
    Selection.Find.Execute
End Sub

Function Get_report_type()
    Get_report_type = ""

    Run_Find "Заключение по результатам визуально-измерительного контроля"
    If Selection.Find.Found Then
        Get_report_type = "VT"
        Exit Function
    End If

    Run_Find "Заключение по результатам радиографического контроля"
    If Selection.Find.Found Then
        Get_report_type = "RT"
        Exit Function
    End If

    Run_Find "Заключение по результатам ультразвукового  контроля"
    If Selection.Find.Found Then
        Get_report_type = "UT"
        Exit Function
    End If
End Function

Function Get_text_after_find(search_text)
    Run_Find search_text
    If Selection.Find.Found Then
        Selection.MoveRight Unit:=wdCharacter, Count:=1
        Selection.EndKey Unit:=wdLine, Extend:=wdExtend
        Get_text_after_find = Trim_white_spaces(Selection.text)
    Else
'       Response = MsgBox(search_text, Title:=ActiveDocument.FullName)
        Get_text_after_find = "### Not found ###"
    End If
End Function

'''''''''''
'   XML   '
'''''''''''
Sub Write_tag(file_desc, tag_name, tag_value, Optional level = 1)
    sp = Space(level * 2)
    file_desc.WriteLine (sp & "<" & tag_name & ">" & tag_value & "</" & tag_name & ">")
End Sub

Sub Recognize_and_Save_Xml(xmlFileName, csvFile)
' Определяем тип заключения
    type_text = Get_report_type()
    If type_text = "" Then
        delimiter = Chr(9)
        text = ActiveDocument.FullName & delimiter & "### Undefined ###"
        csvFile.WriteLine (text)
        Exit Sub
    End If

' Сначала записываем файл XML
    Set fs = CreateObject("Scripting.FileSystemObject")
    Set a = fs.CreateTextFile(xmlFileName, True, True)
    a.WriteLine ("<?xml version=""1.0"" encoding=""UTF-16LE"" ?>")
    a.WriteLine ("<?xml-stylesheet type=""text/xsl"" href=""D:\home\tmpl\" + type_text + ".xsl""?>")
    a.WriteLine ("")
    a.WriteLine ("<root>")

    Dim arr As Variant

''' Данные распознавания '''
    Write_tag a, "dir", ActiveDocument.Path
    Write_tag a, "file", ActiveDocument.Name
    Write_tag a, "date", Date
    Write_tag a, "time", Time
    Write_tag a, "type", type_text
    a.WriteLine ("")

'   Данные заключения
    report_id_text = Get_text_after_find("З А К Л Ю Ч Е Н И Е №")
    date_text = Get_text_after_find("От")

'   Парсим необходимые
    arr = Parse.Parse_report_id(report_id_text)
    report_pre = arr(0)
    report_id = arr(1)
    report_sign = arr(2)
    date_text = Parse.Parse_date(date_text)

'   Записываем
    Write_tag a, "Report_Id", report_id_text
    Write_tag a, "Report_Id_pre", report_pre, 2
    Write_tag a, "Report_Id_id", report_id, 2
    Write_tag a, "Report_Id_sign", report_sign, 2
    Write_tag a, "Date", date_text
    a.WriteLine ("")

''' Данные из таблицы стыков '''
    joint_id_text = ""
    dt_text = ""
    welders_text = ""
    decision_text = ""

    If type_text <> "UT" Then
        x_m = ActiveDocument.Tables(4).Columns.Count
        y_m = ActiveDocument.Tables(4).Rows.Count

        With ActiveDocument.Tables(4)
            joint_id_text = Trim_white_spaces(.Cell(3, 2))
            dt_text = Trim_white_spaces(.Cell(3, 3))
            welders_text = Trim_white_spaces(.Cell(3, 4))
            decision_text = Trim_white_spaces(.Cell(3, 8))
        End With
    End If

'   Парсим необходимые
    arr = ParseJ.Разобрать_номер_стыка(joint_id_text)
    joint_pid = arr(0)
    joint_kp = arr(1)
    joint_type = arr(2)
    joint_id = arr(3)
    joint_sign = arr(4)

    dt_text = Parse.Parse_dia_thick(dt_text)
    welders_text = Parse.Parse_welders(welders_text)
    decision_text = UCase(decision_text)

'   Записываем
    Write_tag a, "Joint_Id", joint_id_text
    Write_tag a, "Joint_Id_pid", joint_pid, 2
    Write_tag a, "Joint_Id_kp", joint_kp, 2
    Write_tag a, "Joint_Id_type", joint_type, 2
    Write_tag a, "Joint_Id_id", joint_id, 2
    Write_tag a, "Joint_Id_sign", joint_sign, 2
    
    Write_tag a, "Diameter_And_Thickness", dt_text
    Write_tag a, "Welders", welders_text
    Write_tag a, "Decision", decision_text
    a.WriteLine ("")

''' Данные из таблицы стыков / дефекты '''
    d_rows = 0
    If type_text = "RT" Then
        a.WriteLine ("  <Defects>")
        For y = 3 To y_m
            number_text = Trim_white_spaces(ActiveDocument.Tables(4).Cell(y, 5))
            def_text = Trim_white_spaces(ActiveDocument.Tables(4).Cell(y, 7))
            coord_text = Trim_white_spaces(ActiveDocument.Tables(4).Cell(y, 9))

            arr = ParseD.Строка_дефектов_xml(def_text)
            def_count = CStr(arr(0))
            def_text = arr(1)

            text = "    <Defect><number>" + number_text + "</number><count>" + def_count + "</count><def>" + _
                   def_text + "</def><coord>" + coord_text + "</coord></Defect>"
            a.WriteLine (text)
            d_rows = d_rows + 1
        Next y
        a.WriteLine ("  </Defects>")
        Write_tag a, "Defects_Rows", d_rows
        a.WriteLine ("")
'       Здесь мы добавляем служебную информацию для xml
        a.WriteLine ("  <Heights><Height1>30</Height1><Height2>15.75</Height2></Heights>")
        a.WriteLine ("  <Rows><Row1>5</Row1><Row2>15</Row2></Rows>")
        a.WriteLine ("  <space> </space>")
        a.WriteLine ("")
    End If

''' Данные из таблицы инспекторов '''
    inspector1_text = ""
    inspector1_cert_text = ""
    inspector2_text = ""
    inspector2_cert_text = ""

    If type_text = "RT" Then
        With ActiveDocument.Tables(5)
            inspector1_text = Trim_white_spaces(.Cell(1, 2))
            inspector1_cert_text = Trim_white_spaces(.Cell(1, 3))
            inspector2_text = Trim_white_spaces(.Cell(3, 2))
            inspector2_cert_text = Trim_white_spaces(.Cell(3, 3))
        End With
    End If

'   Парсим необходимые
    arr = Parse.Parse_name(inspector1_text)
    inspector1_id = arr(0)
    inspector1_text = arr(1)
    inspector1_cert_text = Parse.Parse_cert(inspector1_cert_text)
    arr = Parse.Parse_name(inspector2_text)
    inspector2_id = arr(0)
    inspector2_text = arr(1)
    inspector2_cert_text = Parse.Parse_cert(inspector2_cert_text)

'   Записываем
    Write_tag a, "Inspected_By_Id", inspector1_id
    Write_tag a, "Inspected_By", inspector1_text
    Write_tag a, "Inspected_Cert", inspector1_cert_text
    Write_tag a, "Decision_By_Id", inspector2_id
    Write_tag a, "Decision_By", inspector2_text
    Write_tag a, "Decision_Cert", inspector2_cert_text
    
    a.WriteLine ("</root>")
    a.Close

' Теперь записываем файл CSV
    delimiter = Chr(9)
    text = ActiveDocument.Path & delimiter & _
           ActiveDocument.Name & delimiter & _
           type_text & delimiter & _
           report_id_text & delimiter & _
             report_pre & delimiter & _
             report_id & delimiter & _
             report_sign & delimiter & _
           date_text & delimiter & _
           joint_id_text & delimiter & _
             joint_pid & delimiter & _
             joint_kp & delimiter & _
             joint_type & delimiter & _
             joint_id & delimiter & _
             joint_sign & delimiter & _
           dt_text & delimiter & _
           welders_text & delimiter & _
           decision_text & delimiter & _
           d_rows & delimiter & _
           inspector1_id & delimiter & _
           inspector1_text & delimiter & _
           inspector1_cert_text & delimiter & _
           inspector2_id & delimiter & _
           inspector2_text & delimiter & _
           inspector2_cert_text
    csvFile.WriteLine (text)
End Sub

Sub Export_Doc_to_Xml_WalkDir(sDocsPath, sXmlsPath, csvFile, fs)
    If Right(sDocsPath, 1) <> "\" Then
        sDocsPath = sDocsPath & "\"
    End If

    CreateFolder (sXmlsPath)

    Set F = fs.GetFolder(sDocsPath)
    Set fc = F.SubFolders
    For Each fDir In fc
        Export_Doc_to_Xml_WalkDir fDir, sXmlsPath & "\" & fDir.Name, csvFile, fs
    Next

    lstAttr = vbNormal + vbReadOnly + vbHidden + vbSystem       ' + vbDirectory
    sFileName = Dir(sDocsPath, lstAttr)

    Do While sFileName <> ""
        If sFileName <> "." And sFileName <> ".." Then
            ext = Right(sFileName, 4)
            If ext = ".doc" Then
'               csvFile.WriteLine (sDocsPath & sFileName)
                sFullName = sDocsPath & sFileName
                xmlFileName = sXmlsPath & "\" & Replace(sFileName, ".doc", ".xml")

                Documents.Open FileName:=sFullName, _
                    ConfirmConversions:=False, ReadOnly:=True, AddToRecentFiles:=False, _
                    PasswordDocument:="", PasswordTemplate:="", Revert:=False, _
                    WritePasswordDocument:="", WritePasswordTemplate:="", Format:=wdOpenFormatAuto, _
                    DocumentDirection:=wdLeftToRight

                Recognize_and_Save_Xml xmlFileName, csvFile
                ActiveDocument.Close
            End If
        End If

        sFileName = Dir
    Loop
End Sub



'''''''''''''''''''''''''
' Стартовые макросы XML '
'''''''''''''''''''''''''

' Пропишите в обоих функциях необходимые пути

Sub Export_Doc_to_Xml_dir()
    Application.ScreenUpdating = False

    Path1 = "D:\home\bu42"
    sDocsPath = Path1 + "\reports\_Заключения_doc"
    sDocsPath = Path1 + "\reports\РК -733- 734- 735- 736"
    sXmlsPath = Path1 + "\xml_reports_rt"
    sCsvFile = Path1 + "\reports.csv"

'   On Error Resume Next
    Set fs = CreateObject("Scripting.FileSystemObject")
    Set csvFile = fs.CreateTextFile(sCsvFile, True, True)

    Export_Doc_to_Xml_WalkDir sDocsPath, sXmlsPath, csvFile, fs

    csvFile.Close
End Sub

Sub Export_Doc_to_Xml_opened()
'   Application.ScreenUpdating = False

    Path1 = "D:\home\bu42"
    sXmlsPath = Path1 + "\xml_report1"
    sCsvFile = Path1 + "\report1.csv"

'   On Error Resume Next
    Set fs = CreateObject("Scripting.FileSystemObject")
    Set csvFile = fs.CreateTextFile(sCsvFile, True, True)

    CreateFolder sXmlsPath
    xmlFileName = sXmlsPath & "\" & Replace(ActiveDocument.Name, ".doc", ".xml")
    Recognize_and_Save_Xml xmlFileName, csvFile

    csvFile.Close
End Sub

