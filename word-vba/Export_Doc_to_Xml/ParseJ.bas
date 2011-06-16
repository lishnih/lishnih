Attribute VB_Name = "ParseJ"
' ������� ��� ������� ������ ����� ��� doc-���������� (2011-01-13)
' ������ "�����������-����", ��������� "���-����������������"
' �2011 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.3 2011-06-12

Function ���������_�����_�����(inpStr, Optional debugpoint = False)

' ������� ���������� Array: pattern_id, kp, type, joint, sign

' ��������� ���������� �����
    If inpStr = "" Then
        ���������_�����_����� = Array("", "", "", "", "")
        Exit Function
    End If

    If Left(inpStr, 2) = "��" Then
        ���������_�����_����� = Array(-1, "", "", "", "")
        Exit Function
    End If

    If InStr(1, inpStr, "��", vbTextCompare) Then
        ���������_�����_����� = Array(-2, "", "", "", "")
        Exit Function
    End If

    If InStr(1, inpStr, "���", vbTextCompare) Then
        ���������_�����_����� = Array(-3, "", "", "", "")
        Exit Function
    End If

    Set objRegExp = CreateObject("VBScript.RegExp")
    With objRegExp
'       .MultiLine = False
'       .Global = True
       .IgnoreCase = True
    End With

    On Error Resume Next

    If debugpoint Then
        debugpoint = debugpoint     ' ������� ��� ���������, ���������� ����� ����� ��������
    End If

    separator_ptrn = "[ \.]*"
    separator_ext_ptrn = "[ \.\-]*"
    type_ptrn = "(��|��|��)"
    kp_ptrn = "��[ \.]*(\d+)"
    joint_id_ptrn = "��" & separator_ext_ptrn & "(\d+)" & separator_ptrn & "([������]*)"
    joint_id_ext_ptrn = "��" & separator_ext_ptrn & "([\d-]+)" & separator_ptrn & "([������]*)"

' �� ��-84 �� 688           ������� ������: �� ��.1-8 ��.724
    objRegExp.Pattern = type_ptrn & separator_ptrn & joint_id_ext_ptrn & separator_ptrn & kp_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    pattern_id = 1
    type_text = Trim(objSubmatches.Item(0))
    joint_text = Trim(objSubmatches.Item(1))
    sign_text = UCase(Trim(objSubmatches.Item(2)))
    kp_text = Trim(objSubmatches.Item(3))
    
    If kp_text Then
        ���������_�����_����� = Array(pattern_id, kp_text, type_text, joint_text, sign_text)
        Exit Function
    End If

' ��.710-��-��.89
    objRegExp.Pattern = kp_ptrn & separator_ext_ptrn & type_ptrn & separator_ext_ptrn & joint_id_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    pattern_id = 2
    kp_text = Trim(objSubmatches.Item(0))
    type_text = Trim(objSubmatches.Item(1))
    joint_text = Trim(objSubmatches.Item(2))
    sign_text = UCase(Trim(objSubmatches.Item(3)))
    
    If kp_text Then
        ���������_�����_����� = Array(pattern_id, kp_text, type_text, joint_text, sign_text)
        Exit Function
    End If

' �� (02) ��.01 ��.22       ������� ������: ����.711 ��.1
    objRegExp.Pattern = type_ptrn & separator_ptrn & "\(?.*\)?" & separator_ptrn & kp_ptrn & separator_ptrn & joint_id_ptrn
    Set objMatches = objRegExp.Execute(inpStr)
    Set objMatch = objMatches.Item(0)
    Set objSubmatches = objMatch.SubMatches
    pattern_id = 3
    type_text = Trim(objSubmatches.Item(0))
    kp_text = Trim(objSubmatches.Item(1))
    joint_text = Trim(objSubmatches.Item(2))
    sign_text = UCase(Trim(objSubmatches.Item(3)))
    
    If kp_text Then
        ���������_�����_����� = Array(pattern_id, kp_text, type_text, joint_text, sign_text)
        Exit Function
    End If
    
    ���������_�����_����� = Array("...", "...", "...", "...", "...")
End Function

' ��� ������� ��� Excel

Function Get_Pid(�����_�����, Optional debugpoint = False)
' ��� ������� ��� ������� - ��������� ����� ��������, ������� ������������� ��� ������� ������ �����
    A_defect = ���������_�����_�����(�����_�����, debugpoint)
    Get_Pid = A_defect(0)
End Function

Function Get_Kp(�����_�����, Optional debugpoint = False)
    A_defect = ���������_�����_�����(�����_�����, debugpoint)
    Get_Kp = A_defect(1)
End Function

Function Get_Type(�����_�����, Optional debugpoint = False)
    A_defect = ���������_�����_�����(�����_�����, debugpoint)
    Get_Type = A_defect(2)
End Function

Function Get_Id(�����_�����, Optional debugpoint = False)
    A_defect = ���������_�����_�����(�����_�����, debugpoint)
    Get_Id = A_defect(3)
End Function

Function Get_Sign(�����_�����, Optional debugpoint = False)
    A_defect = ���������_�����_�����(�����_�����, debugpoint)
    Get_Sign = A_defect(4)
End Function

