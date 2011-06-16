Attribute VB_Name = "Parse"
' ������� �������� ��� ������ Global_Export
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
    text = Replace(text, "����", "", , , vbTextCompare)

    text = Replace(text, "������", ".01.", , , vbTextCompare)
    text = Replace(text, "�������", ".02.", , , vbTextCompare)
    text = Replace(text, "�����", ".03.", , , vbTextCompare)
    text = Replace(text, "������", ".04.", , , vbTextCompare)
    text = Replace(text, "���", ".05.", , , vbTextCompare)
    text = Replace(text, "����", ".06.", , , vbTextCompare)
    text = Replace(text, "����", ".07.", , , vbTextCompare)
    text = Replace(text, "�������", ".08.", , , vbTextCompare)
    text = Replace(text, "��������", ".09.", , , vbTextCompare)
    text = Replace(text, "�������", ".10.", , , vbTextCompare)
    text = Replace(text, "������", ".11.", , , vbTextCompare)
    text = Replace(text, "�������", ".12.", , , vbTextCompare)

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
    text = Replace(text, "C", "�")      ' ������ ���������� �� �������
    text = Replace(text, "M", "�")

    Parse_welders = text
End Function

Function Parse_name(text)
    text = Replace(text, "�.�.�..", "")
    text = Replace(text, "�.�.�.", "")
    text = Trim(text)

    If text = "" Then
        name_id = 0

    ElseIf text = "�������� �.�." Then
        name_id = 1
    ElseIf text = "������� �.�." Then
        name_id = 2
    ElseIf text = "���������� �.�." Then
        name_id = 3
    ElseIf text = "�������� �.�." Then
        name_id = 4

    ElseIf text = "������� �.�." Then
        name_id = 6

    ElseIf text = "�������� �.�." Then
        name_id = 21
    ElseIf text = "������� �.�." Then
        name_id = 22
    ElseIf text = "��������� �.�." Then
        name_id = 23
    ElseIf text = "���������� �.�." Then
        name_id = 24
    ElseIf text = "������� �.�." Then
        name_id = 25
    ElseIf text = "������ �.�." Then
        name_id = 26
    ElseIf text = "���������� �.�." Then
        name_id = 27
    ElseIf text = "����� �.�." Then
        name_id = 28
    Else
        name_id = -1
    End If

    Parse_name = Array(name_id, text)
End Function

Function Parse_cert(text)
    text = Replace(text, "������� ������������, �  �������������:", "")
    text = Replace(text, "������� ������������, � �������������:", "")
    text = Replace(text, "������� ������������, �  �� .", "")
    text = Trim(text)

    Parse_cert = text
End Function

