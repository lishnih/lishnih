Attribute VB_Name = "Parse"
' Функции парсинга для модуля Global_Export
' Rev. 2011-06-14


Function Parse_report_id(text)
    If text = "" Then
        Parse_report_id = Array("", "", "")
        Exit Function
    End If
    
    Set objRegExp = CreateObject("VBScript.RegExp")
    objRegExp.IgnoreCase = True
    On Error Resume Next

    objRegExp.Pattern = "([^\d- ]*)[- ]*(\d+) *(.*)"
    Set objMatches = objRegExp.Execute(text)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    id_pre = UCase(Trim(objSubmatches.Item(0)))
    id_text = Trim(objSubmatches.Item(1))
    sign_text = UCase(Trim(objSubmatches.Item(2)))
    If Left(sign_text, 1) = "-" Then
        sign_text = Replace(sign_text, "-", "/", 1, 1)
    End If
    
    If id_text Then
        Parse_report_id = Array(id_pre, id_text, sign_text)
        Exit Function
    End If
    
    Parse_report_id = Array("...", "...", "...")
End Function

Function Parse_date(text)
    If text = "" Then
        Parse_date = ""
        Exit Function
    End If

    text = Replace(text, " ", "")
    text = Replace(text, "года", "", , , vbTextCompare)

    text = Replace(text, "января", ".01.", , , vbTextCompare)
    text = Replace(text, "февраля", ".02.", , , vbTextCompare)
    text = Replace(text, "марта", ".03.", , , vbTextCompare)
    text = Replace(text, "апреля", ".04.", , , vbTextCompare)
    text = Replace(text, "мая", ".05.", , , vbTextCompare)
    text = Replace(text, "июня", ".06.", , , vbTextCompare)
    text = Replace(text, "июля", ".07.", , , vbTextCompare)
    text = Replace(text, "августа", ".08.", , , vbTextCompare)
    text = Replace(text, "сентября", ".09.", , , vbTextCompare)
    text = Replace(text, "октября", ".10.", , , vbTextCompare)
    text = Replace(text, "ноября", ".11.", , , vbTextCompare)
    text = Replace(text, "декабря", ".12.", , , vbTextCompare)

    Set objRegExp = CreateObject("VBScript.RegExp")
    objRegExp.IgnoreCase = True
    On Error Resume Next

    objRegExp.Pattern = "(\d+).(\d+).(\d+)"
    Set objMatches = objRegExp.Execute(text)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    day_text = Trim(objSubmatches.Item(0))
    month_text = Trim(objSubmatches.Item(1))
    year_text = Trim(objSubmatches.Item(2))
    
    If day_text Then
        Parse_date = year_text + "-" + month_text + "-" + day_text
        Exit Function
    End If

    Parse_date = "! " + text
End Function

Function Parse_dia_thick(text)
    If text = "" Then
        Parse_dia_thick = ""
        Exit Function
    End If

    text = Replace(text, ".", ",")

    Set objRegExp = CreateObject("VBScript.RegExp")
    objRegExp.IgnoreCase = True
    On Error Resume Next

    objRegExp.Pattern = "(\d+)[^\d]+(\d.+)"
    Set objMatches = objRegExp.Execute(text)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    dia_text = Trim(objSubmatches.Item(0))
    thick_text = Trim(objSubmatches.Item(1))

    If dia_text Then
        Parse_dia_thick = dia_text + "x " + thick_text
        Exit Function
    End If

    Parse_dia_thick = "! " + text
End Function

Function Parse_welders(text)
    text = Replace(text, " ", "")
    text = Replace(text, "C", "С")      ' Меняем английские на русские
    text = Replace(text, "M", "М")

    Parse_welders = text
End Function

Function Parse_name(text)
    text = Replace(text, "Ф.И.О..", "")
    text = Replace(text, "Ф.И.О.", "")
    text = Trim(text)

    If text = "" Then
        name_id = 0

    ElseIf text = "Манакова Л.О." Then
        name_id = 1
    ElseIf text = "Корнило В.Н." Then
        name_id = 2
    ElseIf text = "Овчинников С.В." Then
        name_id = 3
    ElseIf text = "Варданян Ф.В." Then
        name_id = 4

    ElseIf text = "Целуйко Ю.В." Then
        name_id = 6

    ElseIf text = "Арсланов А.Ф." Then
        name_id = 21
    ElseIf text = "Гарусов Ю.М." Then
        name_id = 22
    ElseIf text = "Золотухин А.В." Then
        name_id = 23
    ElseIf text = "Неумывакин В.И." Then
        name_id = 24
    ElseIf text = "Серяков Д.Ю." Then
        name_id = 25
    ElseIf text = "Хохлов А.В." Then
        name_id = 26
    ElseIf text = "Чернявский Э.В." Then
        name_id = 27
    ElseIf text = "Чуйко О.Г." Then
        name_id = 28
    Else
        name_id = -1
    End If

    Parse_name = Array(name_id, text)
End Function

Function Parse_cert(text)
    text = Replace(text, "Уровень квалификации, №  удостоверения:", "")
    text = Replace(text, "Уровень квалификации, № удостоверения:", "")
    text = Replace(text, "Уровень квалификации, №  уд .", "")
    text = Trim(text)

    Parse_cert = text
End Function

