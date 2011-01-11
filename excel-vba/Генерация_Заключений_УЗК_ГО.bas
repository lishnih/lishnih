Attribute VB_Name = "Module1"
' © 2010 stan http://lishnih.net
' lishnih@gmail.com
'   stan August 27, 2010
'   v0.01

Sub Генерация_Заключений_УЗК_ГО()
    max_i = 500
    sh_list_name = "_список"
    sh_sample_name = "_образец"

    For i = 2 To max_i
        go_angle = Sheets(sh_list_name).Cells(i, 27)
        If go_angle <> "" Then
            pipe_number = CStr(Sheets(sh_list_name).Cells(i, 2))
            joint_id = Replace(CStr(Sheets(sh_list_name).Cells(i, 38)), "ст", "АС")
            joint_str = joint_id + " " + CStr(Sheets(sh_list_name).Cells(i, 37))
            weld_date = Sheets(sh_list_name).Cells(i, 39)

            Sheets(sh_sample_name).Copy Before:=Sheets(1)
            Sheets(1).Cells(17, 5) = weld_date
            Sheets(1).Cells(35, 8) = joint_str
            Sheets(1).Cells(35, 12) = pipe_number

            weld_date_str = Replace(CStr(weld_date), "/", ".")
            Sheets(1).Name = Format(i, "000") + " " + weld_date_str
        End If
    Next i
End Sub

Sub Генерация_Заключений_УЗК_ГО_с_диаметром()
'   stan v0.01a August 28, 2010
    i_from = 2
    i_to = 500
    sh_list_name = "_список"
    sh_sample_name = "_образец"

    For i = i_from To i_to
        go_angle = Sheets(sh_list_name).Cells(i, 27)
        weld_date = Sheets(sh_list_name).Cells(i, 39)
        If go_angle <> "" And weld_date <> "" Then
            pipe_diameter = CStr(Sheets(sh_list_name).Cells(i, 1))
            pipe_number = CStr(Sheets(sh_list_name).Cells(i, 2))
            joint_id = Replace(CStr(Sheets(sh_list_name).Cells(i, 38)), "ст", "АС")
            joint_str = joint_id + " " + CStr(Sheets(sh_list_name).Cells(i, 37))

            Sheets(sh_sample_name).Copy Before:=Sheets(1)

            Sheets(1).Cells(17, 5) = weld_date
            Sheets(1).Cells(35, 8) = joint_str
            Sheets(1).Cells(35, 10) = "1220x" + pipe_diameter
            Sheets(1).Cells(35, 12) = pipe_number

            weld_date_str = Replace(CStr(weld_date), "/", ".")
            Sheets(1).Name = Format(i, "000") + " " + pipe_diameter + " " + weld_date_str
        End If
    Next i
End Sub

