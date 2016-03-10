<?php

/*
 * Проверяет разрешения для генерации отчетов
 */
        if (!isset($c)) exit;

        $input_directory = 'input'; // Папка куда попадает XML TASKOD
        
        $row = fetch_assoc_row_from_sql('
            SELECT
                *
            FROM 
                `cashmaster`.`SystemGlobals`
       ;');
       
       if ($row['CheckOperDay']=="1") {
            //    Если используется, то смотрим есть ли XML файл с новым состоянием
            $files=array();
            $list = scandir($input_directory);
            // Если есть файлы, то обновляем статус в соответствии с последним файлом
            if (count($list) > 2) {
                foreach ($list as $value) {
                     if (!is_dir($input_directory.'/'.$value)) {
                         $files[]=$input_directory.'/'.$value;
                     };
                };
                $files_datetime = array();
                foreach ($files as $key => $value) {
                    $files_datetime[filemtime($value)] = $value;
                };
                ksort($files_datetime);
                // Открываем самый последний файл по времени прихода
                $xml = simplexml_load_file(end($files_datetime));
                $attr=$xml->CurentOD->attributes();
                
                if (isset($attr['DateOD']) AND !$attr['DateOD']=="") {
                    if (isset($attr['StateOD']) AND $attr['StateOD']=="1") {
                        do_sql('
                            UPDATE 
                                `cashmaster`.`SystemGlobals`
                            SET
                                `OperDayStatus` = 1
                            WHERE 
                                `SystemGlobalsId` = '.$row['SystemGlobalsId'].'
                        ;');
                    } else {
                        do_sql('
                            UPDATE 
                                `cashmaster`.`SystemGlobals`
                            SET
                                `OperDayStatus` = 0
                            WHERE 
                                `SystemGlobalsId` = '.$row['SystemGlobalsId'].'
                        ;');
                    };
                };
                foreach ($files as $key => $value) {
                    unlink($value);
                };
            };
            // Проверяем статус разрешения генерации отчёта
           $row = fetch_assoc_row_from_sql('
                SELECT
                    *
                FROM 
                    `cashmaster`.`SystemGlobals`
           ;');
           if ($row['OperDayStatus']==0) {               
               // Генерация отчетов запрещена
               $data['error'] = 'Операционный день закрыт. Создание отчетов невозможно.';
               include 'app/view/error_message.php';
               
               ?>
                    <div class="container">
                        <hr/>
                        <a class="btn btn-primary btn-large" href="?c=reports">
                            <?php 
                                echo htmlfix('Вернуться назад');
                            ?>
                        </a>
                    </div>
               <?php
               exit;
           };
       };

?>
