Attribute VB_Name = "atg"
' Функция для разбора номера стыка (2011-01-13)
' Проект "ATG-EPC08", Заказчик "ATG", Подрядчик "CPECC"
' ©2011-2014 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.2 2014-03-17


Function Разобрать_номер_стыка(inpStr As String, Optional debugpoint = False)
    Разобрать_номер_стыка = Array(0, "", "", "", "", "", "", "", "", "", "")
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
    inch_ptrn = "(\d{1,2})"
    type_ptrn = "(CO|FG|IA|LO|NG|SG|TH|VG)"
    line_ptrn = "(\d{6})"
    class_ptrn = "(CH|CN|CL|SH|SN|SL|GN)(\d{2})(?:(/\d{1}))?"
    seq_ptrn = "(\d{1,2})"

    If debugpoint Then
        debugpoint = debugpoint     ' Ловушка для отладчика, установите здесь точку останова
    End If

' 2-VG-020112-CN15-2
    objRegExp.Pattern = inch_ptrn & separator_ptrn & type_ptrn & separator_ptrn & line_ptrn & separator_ptrn & class_ptrn & separator_ptrn & seq_ptrn
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
        seq_text = Trim(objSubmatches.Item(6))
        
        Разобрать_номер_стыка = Array(pattern_id, type_text, line_text, class1_text, class2_text, class3_text, seq_text, inch_text, "", "", "")
        Exit Function
    End If

' VG-180004-CL60-20
    objRegExp.Pattern = type_ptrn & separator_ptrn & line_ptrn & separator_ptrn & class_ptrn & separator_ptrn & seq_ptrn
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
        seq_text = Trim(objSubmatches.Item(5))
        
        Разобрать_номер_стыка = Array(pattern_id, type_text, line_text, class1_text, class2_text, class3_text, seq_text, "", "", "", "")
        Exit Function
    End If

    Разобрать_номер_стыка = Array(-1, "*", "*", "*", "*", "*", "*", "*", "*", "*", "*")
End Function

Function Номер_стыка(inpStr As String)
' Эта функция для отладки - возращает номер стыка после распознавания
    Arr = Разобрать_номер_стыка(inpStr, 1)
    Pid = Arr(0)
    If Pid = 0 Then
        Номер_стыка_упрощённо = ""
        Exit Function
    End If
    If Pid = -1 Then
        Номер_стыка_упрощённо = "!!! " & inpStr
        Exit Function
    End If
        
    Номер_стыка = Arr(0) & "_" & Arr(1) & "_" & Arr(2) & "_" & Arr(3) & "_" & Arr(4) & "_" & Arr(5) & "_" & Arr(6) & "_" & Arr(7) & "_" & Arr(8) & "_" & Arr(9) & "_" & Arr(10)
End Function

Function Номер_стыка_упрощённо(inpStr As String, Optional debugpoint = False)
    Arr = Разобрать_номер_стыка(inpStr, debugpoint)
    Pid = Arr(0)
    If Pid = 0 Then
        Номер_стыка_упрощённо = ""
        Exit Function
    End If
    If Pid = -1 Then
        Номер_стыка_упрощённо = "!!! " & inpStr
        Exit Function
    End If
    
    Номер_стыка_упрощённо = Arr(1) & "-" & Arr(2) & "-" & Arr(4) & Arr(5) & "-" & Arr(6)
End Function

Function Get_Pid(Номер_стыка As String, Optional debugpoint = False)
    Arr = Разобрать_номер_стыка(Номер_стыка, debugpoint)
    Get_Pid = Arr(0)
End Function

Function Get_Type(Номер_стыка As String, Optional debugpoint = False)
    Arr = Разобрать_номер_стыка(Номер_стыка, debugpoint)
    Get_Kp = Arr(1)
End Function

