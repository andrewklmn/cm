<?php

/* 
Правила именования файла TASKRECALC таковы:

TASKRECALC_CCYY-MM-DDTHH24-MI-SS_NNNN.xml

CCYY - четырёхзначный год текущего операционного дня
MM - месяц с ведущим нулём текущего операционного дня
DD - день с ведущим нулём текущего операционного дня
T - символьная константа (во как круто назвали букву Т).
HH24 - двузначный номер часа формирования файла в сутках с
ведущим нулём (ё@баные спецы! цифра 24 не участвует в
строке. То есть просто HH по 24 часовой системе)
MI - минуты с ведущим нулём формирования файла
SS - секунды с нулём формирования файла
NNNN - порядковый номер для нумерации файлов, имеющих одно
время формирования (с ведущими нулями)

Пример имени файла:
TASKRECALC_2008-04-07T19-03-07_0001.xml

 */

    function get_taskrecalc_files_list() {
        global $program;
        
        $f = array();
        // Проверяем есть ли папка для текущей кассы и считываем список 
        $path = 'input/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'];
        //$path = 'input/5';
        if (file_exists($path)) {
            $files = scandir($path);
        } else {
            $data['error'] = "Cashroom directory doesn't exist! Call service.";
            include 'app/view/error_message.php';
            exit;
        };
        
        foreach ($files as $value) {
            if ($value!='.' AND $value!='..')
            $f[] = $value;
        };
        
        return $f;
    }
?>