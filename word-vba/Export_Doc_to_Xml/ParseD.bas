Attribute VB_Name = "ParseD"
' ������� �������� �������� ��� ������ Global_Export
' Rev. 2011-06-12

''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
' ����� �������
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

Function ������_��������(������_��������)
    Dim A_defects(10)

    inpStr = ������_��������
    Set objRegExp = CreateObject("VBScript.RegExp")
    With objRegExp
        .MultiLine = True
        .Global = True
        .IgnoreCase = True
        .Pattern = "([^;\n]+)"
    End With
    Set objMatches = objRegExp.Execute(inpStr)
    For i = 0 To objMatches.Count - 1
        A_defects(i) = Trim(objMatches.Item(i).Value)
    Next i

    ������_�������� = A_defects
End Function

Function ���������_������_sub(defect_str)
    Set objRegExp = CreateObject("VBScript.RegExp")
    
    With objRegExp
       .Global = True
'       .IgnoreCase = True
'       .MultiLine = True
        .Pattern = "(Bd|Bd|Da|Da|Da|Dc|Dc) *(\d) *"
    End With

    defect_str = objRegExp.Replace(defect_str, "$1<Sub>$2</Sub> ")

    ���������_������_sub = defect_str
End Function



''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
' �������
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

Function ������_��������_xml(������_��������)
    ������_��������_xml = Array(Len(������_��������), ������_��������)

    If ������_�������� = "���" Or ������_�������� = "" Or ������_�������� = "---" Then
        Exit Function
    Else
        ������_��������_sub = ""
        �����_������ = 0
        A_defects = ������_��������(������_��������)
        For Each defect_str In A_defects
            defect_str = Trim(defect_str)
            If defect_str <> "" Then
                �����_������ = �����_������ + Len(defect_str)
                defect_str = Replace(defect_str, "<", "&lt;")   ' ��������� "<"
                defect_str = Replace(defect_str, ">", "&gt;")   ' ��������� ">"

                defect_str = ���������_������_sub(defect_str)

                defect_str = Replace(defect_str, "&", "&amp;")  ' ��������� "&"
                defect_str = Replace(defect_str, "<", "&lt;")   ' ��������� "<"
                defect_str = Replace(defect_str, ">", "&gt;")   ' ��������� ">"
                
                If ������_��������_sub <> "" Then
                    �����_������ = �����_������ + 2
                    ������_��������_sub = ������_��������_sub + "; "
                End If
                ������_��������_sub = ������_��������_sub + defect_str
            End If
        Next

        ������_��������_xml = Array(�����_������, ������_��������_sub)
    End If
End Function

