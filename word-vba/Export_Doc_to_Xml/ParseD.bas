Attribute VB_Name = "ParseD"
' Функции парсинга дефектов для модуля Global_Export
' Rev. 2011-06-12

''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
' Общие функции
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

Function Массив_дефектов(Строка_дефектов)
    Dim A_defects(10)

    inpStr = Строка_дефектов
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

    Массив_дефектов = A_defects
End Function

Function Разобрать_дефект_sub(defect_str)
    Set objRegExp = CreateObject("VBScript.RegExp")
    
    With objRegExp
       .Global = True
'       .IgnoreCase = True
'       .MultiLine = True
        .Pattern = "(Bd|Bd|Da|Da|Da|Dc|Dc) *(\d) *"
    End With

    defect_str = objRegExp.Replace(defect_str, "$1<Sub>$2</Sub> ")

    Разобрать_дефект_sub = defect_str
End Function



''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
' Макросы
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

Function Строка_дефектов_xml(Строка_дефектов)
    Строка_дефектов_xml = Array(Len(Строка_дефектов), Строка_дефектов)

    If Строка_дефектов = "ДНО" Or Строка_дефектов = "" Or Строка_дефектов = "---" Then
        Exit Function
    Else
        Строка_дефектов_sub = ""
        длина_строки = 0
        A_defects = Массив_дефектов(Строка_дефектов)
        For Each defect_str In A_defects
            defect_str = Trim(defect_str)
            If defect_str <> "" Then
                длина_строки = длина_строки + Len(defect_str)
                defect_str = Replace(defect_str, "<", "&lt;")   ' Маскируем "<"
                defect_str = Replace(defect_str, ">", "&gt;")   ' Маскируем ">"

                defect_str = Разобрать_дефект_sub(defect_str)

                defect_str = Replace(defect_str, "&", "&amp;")  ' Маскируем "&"
                defect_str = Replace(defect_str, "<", "&lt;")   ' Маскируем "<"
                defect_str = Replace(defect_str, ">", "&gt;")   ' Маскируем ">"
                
                If Строка_дефектов_sub <> "" Then
                    длина_строки = длина_строки + 2
                    Строка_дефектов_sub = Строка_дефектов_sub + "; "
                End If
                Строка_дефектов_sub = Строка_дефектов_sub + defect_str
            End If
        Next

        Строка_дефектов_xml = Array(длина_строки, Строка_дефектов_sub)
    End If
End Function

