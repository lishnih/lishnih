Attribute VB_Name = "RecJoints_RT"
' Функции извлечения данных о стыках для модуля Global_Export (РК Заключения)
' Rev. 2011-06-17

Function rec(a)
    rec = 0
    delimiter = Chr(9)

''' Стык '''
    x_m = ActiveDocument.Tables(4).Columns.Count
    y_m = ActiveDocument.Tables(4).Rows.Count

    With ActiveDocument.Tables(4)
        joint_id_text = Trim_white_spaces(.Cell(3, 2))
        dt_text = Trim_white_spaces(.Cell(3, 3))
        welders_text = Trim_white_spaces(.Cell(3, 4))
        decision_text = Trim_white_spaces(.Cell(3, 8))
    End With

'   Парсим необходимые
    arr = ParseJ.Разобрать_номер_стыка(joint_id_text)
    joint_pid = CStr(arr(0))
    joint_kp = arr(1)
    joint_type = arr(2)
    Joint_Id = arr(3)
    joint_sign = arr(4)

    dt_text = Parse.Parse_dia_thick(dt_text)
    welders_text = Parse.Parse_welders(welders_text)
    decision_text = UCase(decision_text)

'   Записываем
    Write_tag a, "Joint_Id", joint_id_text
    Write_tag a, "Joint_Id_pid", joint_pid, 2
    Write_tag a, "Joint_Id_kp", joint_kp, 2
    Write_tag a, "Joint_Id_type", joint_type, 2
    Write_tag a, "Joint_Id_id", Joint_Id, 2
    Write_tag a, "Joint_Id_sign", joint_sign, 2
    
    Write_tag a, "Diameter_And_Thickness", dt_text
    Write_tag a, "Welders", welders_text
    Write_tag a, "Decision", decision_text
    a.WriteLine ("")

''' Дефекты '''
    d_rows = 0
    a.WriteLine ("  <Defects>")
    For y = 3 To y_m
        number_text = Trim_white_spaces(ActiveDocument.Tables(4).Cell(y, 5))
        def_text = Trim_white_spaces(ActiveDocument.Tables(4).Cell(y, 7))
        coord_text = Trim_white_spaces(ActiveDocument.Tables(4).Cell(y, 9))

'       Парсим необходимые
        arr = ParseD.Строка_дефектов_xml(def_text)
        def_count = CStr(arr(0))
        def_text = arr(1)

'       Записываем
        text = "    <Defect><number>" + number_text + "</number><count>" + def_count + "</count><def>" + _
               def_text + "</def><coord>" + coord_text + "</coord></Defect>"
        
        
        text = "    <Defect>" + _
               "      <number>" + number_text + "</number>" + _
               "      <count>" + def_count + "</count>" + _
               "      <coord>" + coord_text + "</coord>" + _
               "    </Defect>"
        
        
        a.WriteLine (text)
        d_rows = d_rows + 1
    Next y
    a.WriteLine ("  </Defects>")
    Write_tag a, "Defects_Rows", d_rows
    a.WriteLine ("")

'   Добавляем служебную информацию для xml
    a.WriteLine ("  <Heights><Height1>30</Height1><Height2>15.75</Height2></Heights>")
    a.WriteLine ("  <Rows><Row1>5</Row1><Row2>15</Row2></Rows>")
    a.WriteLine ("  <space> </space>")
    a.WriteLine ("")

'   Возвращаем строку для csv
    rec = joint_id_text & delimiter & _
             joint_pid & delimiter & _
             joint_kp & delimiter & _
             joint_type & delimiter & _
             Joint_Id & delimiter & _
             joint_sign & delimiter & _
           dt_text & delimiter & _
           welders_text & delimiter & _
           decision_text & delimiter & _
           d_rows
End Function
