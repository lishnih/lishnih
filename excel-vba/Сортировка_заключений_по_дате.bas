Attribute VB_Name = "Module1"
' � 2010 stan http://lishnih.net
' lishnih@gmail.com
'   stan August 23, 2010
'   v0.02

Sub ����������_����������_��_����_���������_2()
    moves = 0
    MsgBox (Sheets.Count)
Start:
    moves = moves + 1
    moveshasbeen = 0
    For i = 1 To Sheets.Count - 3
        If Sheets(i).Cells(17, 5) < Sheets(i + 1).Cells(17, 5) Then
            Sheets(i).Move After:=Sheets(i + 1)
            moveshasbeen = 1
        End If
    Next i
    If moveshasbeen Then
        GoTo Start
    End If
    MsgBox (moves)
End Sub

Sub ����������_����������_��_����_���������()
    moves = 0
    MsgBox (Sheets.Count)
Start:
    moves = moves + 1
    moveshasbeen = 0
    For i = 1 To Sheets.Count - 1
        If Sheets(i).Cells(17, 5) < Sheets(i + 1).Cells(17, 5) Then
            Sheets(i).Move After:=Sheets(i + 1)
            moveshasbeen = 1
        End If
    Next i
    If moveshasbeen Then
        GoTo Start
    End If
    MsgBox (moves)
End Sub

Sub ����������_����������_��_����()
    MsgBox (Sheets.Count)
    moveshasbeen = 0
    For j = 1 To Sheets.Count / 4
        For i = 1 To Sheets.Count - 1
            If Sheets(i).Cells(17, 5) < Sheets(i + 1).Cells(17, 5) Then
                Sheets(i).Move After:=Sheets(i + 1)
                moveshasbeen = 1
            End If
        Next i
    Next j
    If moveshasbeen Then
        MsgBox ("����������� ����, ��������� ������!")
    Else
        MsgBox ("����������� �� ����, ���������� ���������!")
    End If
End Sub

