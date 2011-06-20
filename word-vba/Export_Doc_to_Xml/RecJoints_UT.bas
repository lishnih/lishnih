Attribute VB_Name = "RecJoints_UT"
' Функции извлечения данных о стыках для модуля Global_Export (РК Заключения)
' Rev. 2011-06-18

Function rec(a, csvFile_j)
    rec = 0
    delimiter = Chr(9)

''' Стыки '''
    a.WriteLine ("  <Joints>")
    With ActiveDocument.Tables(4)
        x_m = .Columns.Count
        y_m = .Columns(8).Cells.Count
        lines_m = .Rows.Count

        j_rows = 0      ' Всего стыков
        j0_rows = 0     ' Кол-во строк со стыками (учитываются пустые строки)
        d_rows = 3      ' Кол-во строк с дефектами
        For y = 3 To y_m
            joint_id_text = Trim_white_spaces(.Columns(8).Cells(y))
            decision_text = UCase(Trim_white_spaces(.Columns(9).Cells(y)))
'           dt_text = ""
'           welders_text = ""

            ' Определяем положение следующего стыка в таблице
            If y < y_m Then
                r_index = .Columns(9).Cells(y + 1).RowIndex
            Else
                r_index = lines_m + 1
            End If

'            coord_text = ""
'            For i = d_rows To r_index - 1
'                If coord_text <> "" Then
'                    coord_text = coord_text + ", "
'                End If
'                coord_text = coord_text + Trim_white_spaces(.Cell(i, 6))

' Далее все действия внутри цикла, если в строке задан стык
            If joint_id_text <> "" Or decision_text <> "" Then

'               Парсим необходимые
                arr = ParseJ.Разобрать_номер_стыка(joint_id_text)
                joint_pid = CStr(arr(0))
                joint_kp = arr(1)
                joint_type = arr(2)
                Joint_Id = arr(3)
                joint_sign = arr(4)

'               dt_text = Parse.Parse_dia_thick(dt_text)
'               welders_text = Parse.Parse_welders(welders_text)

'               Дефекты
                fst = 1
                For i = d_rows To r_index - 1
                    с2 = Trim_white_spaces3(.Cell(i, 2))
                    с2 = Replace(с2, "<", "&lt;")   ' Маскируем "<"
                    с2 = Replace(с2, ">", "&gt;")   ' Маскируем ">"

                    a.WriteLine ("    <Joint>")
                    a.WriteLine ("      <c1>" + UCase(Trim_white_spaces3(.Cell(i, 1))) + "</c1>")
                    a.WriteLine ("      <c2>" + с2 + "</c2>")
                    a.WriteLine ("      <c3>" + Trim_white_spaces3(.Cell(i, 3)) + "</c3>")
                    a.WriteLine ("      <c4>" + Trim_white_spaces3(.Cell(i, 4)) + "</c4>")
                    a.WriteLine ("      <c5>" + Trim_white_spaces3(.Cell(i, 5)) + "</c5>")
                    a.WriteLine ("      <c6>" + Trim_white_spaces3(.Cell(i, 6)) + "</c6>")
                    a.WriteLine ("      <c7>" + Trim_white_spaces3(.Cell(i, 7)) + "</c7>")

                    If fst Then
                        a.WriteLine ("      <Merge>" + CStr(r_index - d_rows - 1) + "</Merge>")
                        a.WriteLine ("      <Joint_Id>" + joint_id_text + "</Joint_Id>")
                        a.WriteLine ("        <Joint_Id_pid>" + joint_pid + "</Joint_Id_pid>")
                        a.WriteLine ("        <Joint_Id_kp>" + joint_kp + "</Joint_Id_kp>")
                        a.WriteLine ("        <Joint_Id_type>" + joint_type + "</Joint_Id_type>")
                        a.WriteLine ("        <Joint_Id_id>" + Joint_Id + "</Joint_Id_id>")
                        a.WriteLine ("        <Joint_Id_sign>" + joint_sign + "</Joint_Id_sign>")
                        a.WriteLine ("      <Decision>" + decision_text + "</Decision>")
'                       a.WriteLine ("      <Diameter_And_Thickness>" + dt_text + "</Diameter_And_Thickness>")
'                       a.WriteLine ("      <Welders>" + welders_text + "</Welders>")
                        fst = 0

                        text = ActiveDocument.Name & delimiter & _
                               joint_id_text & delimiter & _
                                 joint_pid & delimiter & _
                                 joint_kp & delimiter & _
                                 joint_type & delimiter & _
                                 Joint_Id & delimiter & _
                                 joint_sign & delimiter & _
                               decision_text & delimiter
                        csvFile_j.WriteLine (text)
                    End If
                    a.WriteLine ("    </Joint>")
                Next i

                j_rows = j_rows + 1
            End If
            j0_rows = j0_rows + 1
            d_rows = r_index
        Next y
    End With
    a.WriteLine ("  </Joints>")
    Write_tag a, "Joints_Rows", j_rows
    Write_tag a, "Joints0_Rows", j0_rows
    Write_tag a, "Defect_Rows", lines_m - 2
    a.WriteLine ("")

'   Добавляем служебную информацию для xml
    a.WriteLine ("  <space> </space>")
    a.WriteLine ("")

    rec = j_rows & delimiter & _
          j0_rows & delimiter & _
          lines_m - 2
End Function

