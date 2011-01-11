Attribute VB_Name = "Module1"
' © 2010 stan http://lishnih.net
' lishnih@gmail.com
'   stan September 2, 2010
'   v0.01

Sub Номер_заключения_в_название_ярлыка()
    For i = 1 To Sheets.Count - 1
        name_str = CStr(Sheets(i).Cells(16, 6))
        name_str = Mid(name_str, 3)
        date_str = CStr(Sheets(i).Cells(17, 5))
        report_flag = Left(Sheets(i).Name, 1)
        If report_flag <> "-" And report_flag <> "_" Then
            report_flag = ""
        End If
        Sheets(i).Name = report_flag + name_str
    Next i
End Sub

