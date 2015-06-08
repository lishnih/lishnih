Attribute VB_Name = "atg"
' Функция для разбора номера стыка (2011-01-13)
' Проект "ATG-EPC08", Заказчик "ATG", Подрядчик "CPECC"
' ©2011-2014 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.6 2014-03-26


Function ext(inpStr, Optional separator = "-")
    ext = ""
    If inpStr <> "" Then
        ext = separator & CStr(inpStr)
    End If
End Function


Function Разобрать_номер_стыка(inpStr As String, Optional debugpoint = False)
    Разобрать_номер_стыка = Array(0)
    If inpStr = "" Then
        Exit Function
    End If

    Set objRegExp = CreateObject("VBScript.RegExp")
    With objRegExp
'       .MultiLine = False
'       .Global = True
       .IgnoreCase = True
    End With

    On Error Resume Next

    separator_ptrn = "-"
    inch_ptrn = "([\d/]{1,3})"
    type_ptrn = "(CO|FE|FG|IA|LO|NG|SG|TH|VG)"
    line_ptrn = "(\d{6})"
    class_ptrn = "(CH|CN|CL|SH|SN|SL|GN)(\d{2})(?:(/\d{1}))?"
    seq_ptrn = "(\d{1,2})(?:-(\d{1,2}))?"
    sign_ptrn = "(.*)"

    If debugpoint Then
        debugpoint = debugpoint     ' Ловушка для отладчика, установите здесь точку останова
    End If

' 2-VG-020112-CN15-2
    objRegExp.Pattern = inch_ptrn & separator_ptrn & type_ptrn & separator_ptrn & line_ptrn & separator_ptrn & class_ptrn & separator_ptrn & seq_ptrn & sign_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    If objSubmatches Then
        pattern_id = 1
        inch_text = Trim(objSubmatches.Item(0))
        type_text = Trim(objSubmatches.Item(1))
        line_text = Trim(objSubmatches.Item(2))
        class1_text = Trim(objSubmatches.Item(3))
        class2_text = Trim(objSubmatches.Item(4))
        class3_text = Trim(objSubmatches.Item(5))
        seq1_text = Trim(objSubmatches.Item(6))
        seq2_text = Trim(objSubmatches.Item(7))
        sign_text = Trim(objSubmatches.Item(8))
        '                             0           1          2          3            4            5            6          7          8          9
        Разобрать_номер_стыка = Array(pattern_id, type_text, line_text, class1_text, class2_text, class3_text, seq1_text, seq2_text, sign_text, inch_text)
        Exit Function
    End If

' VG-180004-CL60-20
    objRegExp.Pattern = type_ptrn & separator_ptrn & line_ptrn & separator_ptrn & class_ptrn & separator_ptrn & seq_ptrn & sign_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    If objSubmatches Then
        pattern_id = 2
        type_text = Trim(objSubmatches.Item(0))
        line_text = Trim(objSubmatches.Item(1))
        class1_text = Trim(objSubmatches.Item(2))
        class2_text = Trim(objSubmatches.Item(3))
        class3_text = Trim(objSubmatches.Item(4))
        seq1_text = Trim(objSubmatches.Item(5))
        seq2_text = Trim(objSubmatches.Item(6))
        sign_text = Trim(objSubmatches.Item(7))
        '                             0           1          2          3            4            5            6          7          8          9
        Разобрать_номер_стыка = Array(pattern_id, type_text, line_text, class1_text, class2_text, class3_text, seq1_text, seq2_text, sign_text, "")
        Exit Function
    End If

' 2-VG-020112-CN15 - Для номера линии!
    objRegExp.Pattern = inch_ptrn & separator_ptrn & type_ptrn & separator_ptrn & line_ptrn & separator_ptrn & class_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    If objSubmatches Then
        pattern_id = 11
        inch_text = Trim(objSubmatches.Item(0))
        type_text = Trim(objSubmatches.Item(1))
        line_text = Trim(objSubmatches.Item(2))
        class1_text = Trim(objSubmatches.Item(3))
        class2_text = Trim(objSubmatches.Item(4))
        class3_text = Trim(objSubmatches.Item(5))
        '                             0           1          2          3            4            5            6   7   8   9
        Разобрать_номер_стыка = Array(pattern_id, type_text, line_text, class1_text, class2_text, class3_text, "", "", "", inch_text)
        Exit Function
    End If

' VG-180004-CL60 - Для номера линии!
    objRegExp.Pattern = type_ptrn & separator_ptrn & line_ptrn & separator_ptrn & class_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    If objSubmatches Then
        pattern_id = 12
        type_text = Trim(objSubmatches.Item(0))
        line_text = Trim(objSubmatches.Item(1))
        class1_text = Trim(objSubmatches.Item(2))
        class2_text = Trim(objSubmatches.Item(3))
        class3_text = Trim(objSubmatches.Item(4))
        '                             0           1          2          3            4            5            6   7   8   9
        Разобрать_номер_стыка = Array(pattern_id, type_text, line_text, class1_text, class2_text, class3_text, "", "", "", "")
        Exit Function
    End If

    Разобрать_номер_стыка = Array(-1)
End Function


Function Get_Pid(inpStr As String)
    Arr = Разобрать_номер_стыка(inpStr)
    Get_Pid = Arr(0)
End Function


Function Номер_стыка_отл(inpStr As String)
' Эта функция для отладки - возращает номер стыка после распознавания
    Arr = Разобрать_номер_стыка(inpStr, 1)
    Pid = Arr(0)
    If Pid = 0 Then
        outStr = ""
    ElseIf Pid = -1 Then
        outStr = "!!! " & inpStr
    Else
        outStr = Arr(0) & "_" & Arr(1) & "_" & Arr(2) & "_" & Arr(3) & "_" & Arr(4) & "_" & Arr(5) & "_" & Arr(6) & "_" & Arr(7) & "_" & Arr(8) & "_" & Arr(9)
    End If

    Номер_стыка_отл = outStr
End Function


Function Номер_стыка_упрощённо(inpStr As String, Optional debugpoint = False)
    Arr = Разобрать_номер_стыка(inpStr, debugpoint)
    Pid = Arr(0)
    If Pid = 0 Then
        outStr = ""
    ElseIf Pid = -1 Then
        outStr = "!!! " & inpStr
    Else
        outStr = Arr(1) & "-" & Arr(2) & ext(Arr(5)) & "-" & Arr(6) & ext(Arr(7))
    End If

    Номер_стыка_упрощённо = outStr
End Function


Function Номер_линии(inpStr As String, Optional debugpoint = False)
    Arr = Разобрать_номер_стыка(inpStr, debugpoint)
    Pid = Arr(0)
    If Pid = 0 Then
        outStr = ""
    ElseIf Pid = -1 Then
        outStr = "!!! " & inpStr
    Else
        outStr = Arr(1) & "-" & Arr(2) & "-" & Arr(3) & Arr(4) & Arr(5)
    End If

    Номер_линии = outStr
End Function


Function Номер_линии_упрощённо(inpStr As String, Optional debugpoint = False)
    Arr = Разобрать_номер_стыка(inpStr, debugpoint)
    Pid = Arr(0)
    If Pid = 0 Then
        outStr = ""
    ElseIf Pid = -1 Then
        outStr = "!!! " & inpStr
    Else
        outStr = Arr(1) & "-" & Arr(2) & ext(Arr(5))
    End If

    Номер_линии_упрощённо = outStr
End Function


Function Get_Type(inpStr As String)
    Arr = Разобрать_номер_стыка(inpStr)
    Pid = Arr(0)
    If Pid = 0 Then
        outStr = ""
    ElseIf Pid = -1 Then
        outStr = "!!! " & inpStr
    Else
        outStr = Arr(1)
    End If

    Get_Type = outStr
End Function


Function Get_Sign(inpStr As String)
    Arr = Разобрать_номер_стыка(inpStr)
    Pid = Arr(0)
    If Pid = 0 Then
        outStr = ""
    ElseIf Pid = -1 Then
        outStr = "!!! " & inpStr
    Else
        outStr = Arr(8)
    End If

    Get_Sign = outStr
End Function

