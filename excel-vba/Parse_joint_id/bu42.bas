Attribute VB_Name = "bu42"
' Функция для разбора номера стыка для doc-заключений (2011-01-13)
' Проект "Бованенкого-Ухта", Подрядчик "СГК-Трубопроводстрой"
' ©2011 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.2 2011-01-15

Function Разобрать_номер_стыка(inpStr As String, Optional debugpoint = False)
    Set objRegExp = CreateObject("VBScript.RegExp")
    With objRegExp
'       .MultiLine = False
'       .Global = True
       .IgnoreCase = True
    End With

    On Error Resume Next

    If debugpoint Then
        debugpoint = debugpoint     ' Ловушка для отладчика, установите здесь точку останова
    End If

    separator_ptrn = "[ \.]*"
    separator_ext_ptrn = "[ \.\-]*"
    type_ptrn = "(АХ|АС|РД)"
    kp_ptrn = "км[ \.]*(\d+)"
    joint_id_ptrn = "ст" & separator_ext_ptrn & "(\d+)" & separator_ptrn & "([АБЛЗВР]*)"
    joint_id_ext_ptrn = "ст" & separator_ext_ptrn & "([\d-]+)" & separator_ptrn & "([АБЛЗВР]*)"

' АХ ст-84 км 688           Частный случай: АХ ст.1-8 км.724
'   objRegExp.Pattern = type_ptrn & separator_ptrn & joint_id_ptrn & separator_ptrn & kp_ptrn
    objRegExp.Pattern = type_ptrn & separator_ptrn & joint_id_ext_ptrn & separator_ptrn & kp_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
        Set objMatch = objMatches.Item(0)
        Set objSubmatches = objMatch.SubMatches
        pattern_id = 1
        type_text = Trim(objSubmatches.Item(0))
        joint_text = Trim(objSubmatches.Item(1))
        sign_text = Trim(objSubmatches.Item(2))
        kp_text = Trim(objSubmatches.Item(3))
    If kp_text Then
        Разобрать_номер_стыка = Array(pattern_id, kp_text, type_text, joint_text, sign_text)
        Exit Function
    End If

' Км.710-АХ-ст.89
    objRegExp.Pattern = kp_ptrn & separator_ext_ptrn & type_ptrn & separator_ext_ptrn & joint_id_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
        Set objMatch = objMatches.Item(0)
        Set objSubmatches = objMatch.SubMatches
        pattern_id = 2
        kp_text = Trim(objSubmatches.Item(0))
        type_text = Trim(objSubmatches.Item(1))
        joint_text = Trim(objSubmatches.Item(2))
        sign_text = Trim(objSubmatches.Item(3))
    If kp_text Then
        Разобрать_номер_стыка = Array(pattern_id, kp_text, type_text, joint_text, sign_text)
        Exit Function
    End If

' АХ (02) км.01 ст.22       Частный случай: АХкм.711 ст.1
'   objRegExp.Pattern = type_ptrn & separator_ptrn & "\(.*\)" & separator_ptrn & kp_ptrn & separator_ptrn & joint_id_ptrn
    objRegExp.Pattern = type_ptrn & separator_ptrn & "\(?.*\)?" & separator_ptrn & kp_ptrn & separator_ptrn & joint_id_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
        Set objMatch = objMatches.Item(0)
        Set objSubmatches = objMatch.SubMatches
        pattern_id = 3
        type_text = Trim(objSubmatches.Item(0))
        kp_text = Trim(objSubmatches.Item(1))
        joint_text = Trim(objSubmatches.Item(2))
        sign_text = Trim(objSubmatches.Item(3))
    If kp_text Then
        Разобрать_номер_стыка = Array(pattern_id, kp_text, type_text, joint_text, sign_text)
        Exit Function
    End If
    
    Разобрать_номер_стыка = Array("???", "???", "???")
End Function

Function Get_Pid(Номер_стыка As String, Optional debugpoint = False)
' Эта функция для отладки - возращает номер паттерна, который использовался для разбора номера стыка
    If Номер_стыка = "" Then
        Get_Pid = ""
        Exit Function
    Else
        A_defect = Разобрать_номер_стыка(Номер_стыка, debugpoint)
        Get_Pid = A_defect(0)
    End If
End Function

Function Get_Kp(Номер_стыка As String, Optional debugpoint = False)
    If Номер_стыка = "" Then
        Get_Kp = ""
        Exit Function
    Else
        A_defect = Разобрать_номер_стыка(Номер_стыка, debugpoint)
        Get_Kp = A_defect(1)
    End If
End Function

Function Get_Type(Номер_стыка As String, Optional debugpoint = False)
    If Номер_стыка = "" Then
        Get_Type = ""
        Exit Function
    Else
        A_defect = Разобрать_номер_стыка(Номер_стыка, debugpoint)
        Get_Type = A_defect(2)
    End If
End Function

Function Get_Id(Номер_стыка As String, Optional debugpoint = False)
    If Номер_стыка = "" Then
        Get_Id = ""
        Exit Function
    Else
        A_defect = Разобрать_номер_стыка(Номер_стыка, debugpoint)
        Get_Id = A_defect(3)
    End If
End Function

Function Get_Sign(Номер_стыка As String, Optional debugpoint = False)
    If Номер_стыка = "" Then
        Get_Sign = ""
        Exit Function
    Else
        A_defect = Разобрать_номер_стыка(Номер_стыка, debugpoint)
        Get_Sign = A_defect(4)
    End If
End Function

