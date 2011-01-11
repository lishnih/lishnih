' Генерация_Заключений_ВИК_РД (November 13, 2010)
' ©2010 Stan http://lishnih.net
' lishnih@gmail.com
' Rev.2

Private Sub Создать_Заключение_ВИК_РД(joint_str, weld_date, dia_thick, welders, i)
    sh_sample_name = "_образец"

    Sheets(sh_sample_name).Copy Before:=Sheets(1)
    Sheets(1).Cells(7, 3) = weld_date
    Sheets(1).Cells(11, 3) = dia_thick
    Sheets(1).Cells(13, 1) = joint_str
    Sheets(1).Cells(17, 1) = welders

    weld_date_str = Replace(CStr(weld_date), "/", ".")
    Sheets(1).Name = Format(i, "000") + " " + weld_date_str
End Sub

Sub Генерация_Заключений_ВИК_РД()
    On Error Resume Next
    Application.ScreenUpdating = False

    sh_list_name = "Журнал сварочных работ"
    start_i = 4
    max_i = 100

    weld_date_prev = 0
    dia_thick_prev = 0
    welders_date_prev = 0
    joints_text = ""
    
    For i = start_i To max_i
        joint_str = Sheets(sh_list_name).Cells(i, 6)
        ' Обрабатываем, если задано название стыка
        If joint_str Then
            weld_date = Sheets(sh_list_name).Cells(i, 2)
            dia_thick = Sheets(sh_list_name).Cells(i + 1, 3) & " мм"
            welders = "Бригадное кл. № " & Sheets(sh_list_name).Cells(i, 16)
            ' если дата сменилась, то генерим заключение
            If weld_date <> weld_date_prev Or _
               dia_thick <> dia_thick_prev Or _
               welders <> welders_prev Then
                If weld_date_prev Then
                    Создать_Заключение_ВИК_РД joints_text, weld_date_prev, dia_thick_prev, welders_prev, i
                    joints_text = ""
                End If
                weld_date_prev = weld_date
                dia_thick_prev = dia_thick
                welders_prev = welders
            End If
            If joints_text <> "" Then
                joints_text = joints_text & ", "
            End If
            joints_text = joints_text & joint_str
        End If
    Next i

    ' Завершаем цикл
    If weld_date_prev Then
        Создать_Заключение_ВИК_РД joints_text, weld_date_prev, dia_thick_prev, welders_date_prev, i
    End If

    Application.ScreenUpdating = True
End Sub

