Attribute VB_Name = "bu42"
' ������� ��� ������� ������ ����� ��� doc-���������� (2011-01-13)
' ������ "�����������-����", ��������� "���-����������������"
' �2011 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.2 2011-01-15

Function ���������_�����_�����(inpStr As String, Optional debugpoint = False)
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
        sign_text = Trim(objSubmatches.Item(3))
    If kp_text Then
        ���������_�����_����� = Array(pattern_id, kp_text, type_text, joint_text, sign_text)
        Exit Function
    End If

' �� (02) ��.01 ��.22       ������� ������: ����.711 ��.1
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
        ���������_�����_����� = Array(pattern_id, kp_text, type_text, joint_text, sign_text)
        Exit Function
    End If
    
    ���������_�����_����� = Array("???", "???", "???")
End Function

Function Get_Pid(�����_����� As String, Optional debugpoint = False)
' ��� ������� ��� ������� - ��������� ����� ��������, ������� ������������� ��� ������� ������ �����
    If �����_����� = "" Then
        Get_Pid = ""
        Exit Function
    Else
        A_defect = ���������_�����_�����(�����_�����, debugpoint)
        Get_Pid = A_defect(0)
    End If
End Function

Function Get_Kp(�����_����� As String, Optional debugpoint = False)
    If �����_����� = "" Then
        Get_Kp = ""
        Exit Function
    Else
        A_defect = ���������_�����_�����(�����_�����, debugpoint)
        Get_Kp = A_defect(1)
    End If
End Function

Function Get_Type(�����_����� As String, Optional debugpoint = False)
    If �����_����� = "" Then
        Get_Type = ""
        Exit Function
    Else
        A_defect = ���������_�����_�����(�����_�����, debugpoint)
        Get_Type = A_defect(2)
    End If
End Function

Function Get_Id(�����_����� As String, Optional debugpoint = False)
    If �����_����� = "" Then
        Get_Id = ""
        Exit Function
    Else
        A_defect = ���������_�����_�����(�����_�����, debugpoint)
        Get_Id = A_defect(3)
    End If
End Function

Function Get_Sign(�����_����� As String, Optional debugpoint = False)
    If �����_����� = "" Then
        Get_Sign = ""
        Exit Function
    Else
        A_defect = ���������_�����_�����(�����_�����, debugpoint)
        Get_Sign = A_defect(4)
    End If
End Function

