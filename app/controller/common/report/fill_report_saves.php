<?php

/*
 * Сохраняет данные дополнительные параметры отчетов, если они были созданы
 */

        if (!isset($c)) exit;
        
        //echo '<pre>';
        //print_r($_POST);
        //echo '</pre>';
        
        if (isset($_POST['report_set_id']) AND $_POST['report_set_id']>0 ) {
            foreach ($_POST as $key => $value) {
                if ($key!='report_set_id'
                        AND $key!='action'
                        AND $key!='start_datetime'
                        AND $key!='stop_datetime'
                        AND $key!='selected_signers') {
                    // Проверяем есть ли такая запись в таблице ReportSaves
                    do_sql('LOCK TABLES ReportSaves WRITE;');
                    $t = explode('|', $key);
                    $row = fetch_row_from_sql('
                        SELECT 
                            count(*) 
                        FROM 
                            cashmaster.ReportSaves
                        WHERE
                            `ReportSaves`.`ReportSetId`="'.addslashes($_POST['report_set_id']).'"
                            AND `ReportSaves`.`ReportTypeId`= "'.addslashes($t[0]).'"
                            AND `ReportSaves`.`Key`= "'.addslashes($t[1]).'"
                    ;');
                    if ($row[0] > 0) {
                        // Есть такое сочетание, значит можно просто обновить
                        do_sql('
                            UPDATE 
                                `cashmaster`.`ReportSaves`
                            SET
                                `Value` = "'.addslashes($value).'"
                            WHERE 
                                `ReportSaves`.`ReportSetId`="'.addslashes($_POST['report_set_id']).'"
                                AND `ReportSaves`.`ReportTypeId`= "'.addslashes($t[0]).'"
                                AND `ReportSaves`.`Key`= "'.addslashes($t[1]).'"
                        ;');
                    } else {
                        // Нет такого сочетания значит надо добавить
                        do_sql('
                            INSERT INTO `cashmaster`.`ReportSaves`
                                (
                                    `ReportSetId`,
                                    `ReportTypeId`,
                                    `Key`,
                                    `Value`
                                )
                            VALUES
                                (
                                    "'.addslashes($_POST['report_set_id']).'",
                                    "'.addslashes($t[0]).'",
                                    "'.addslashes($t[1]).'",
                                    "'.addslashes($value).'"
                                )
                        ;');
                    };
                    do_sql('UNLOCK TABLES;');
                };
            };
        };
?>
