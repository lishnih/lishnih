Attribute VB_Name = "Module1"
' Генерация_Заключений_ВИК_РД (November 13, 2010)
' ©2010 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.1

Sub Генерация_Заключений_ВИК_РД()
    On Error Resume Next
    Application.ScreenUpdating = False
    
    max_i = 10
    sh_list_name = "Журнал сварочных работ"
    sh_sample_name = "_образец"

    For i = 4 To max_i
        joint_str = Sheets(sh_list_name).Cells(i, 6)
        If joint_str <> "" Then
            weld_date = Sheets(sh_list_name).Cells(i, 2)
            dia_thick = Sheets(sh_list_name).Cells(i + 1, 3) & " мм"
            welders = "Бригадное кл. № " & Sheets(sh_list_name).Cells(i, 16)
            
            Sheets(sh_sample_name).Copy Before:=Sheets(1)
            Sheets(1).Cells(7, 3) = weld_date
            Sheets(1).Cells(11, 3) = dia_thick
            Sheets(1).Cells(13, 1) = joint_str
            Sheets(1).Cells(17, 1) = welders

            weld_date_str = Replace(CStr(weld_date), "/", ".")
            Sheets(1).Name = Format(i, "000") + " " + weld_date_str
        End If
    Next i

    Application.ScreenUpdating = True
End Sub

