<?php

/*
 * Инициализиурет переменную $report_set_id
 * Используя незавершенный рекордсет или создав новый
 */
    if (!isset($c)) exit;

    do_sql('LOCK TABLES ReportSets WRITE;');
        // Смотрим последний репортсет
        $row = fetch_row_from_sql('
            SELECT
                `ReportSets`.`SetId`,
                `ReportSets`.`SetDateTime`,
                `ReportSets`.`CreatedBy`,
                `ReportSets`.`CashRoomId`
            FROM 
                `cashmaster`.`ReportSets`
            WHERE
                `ReportSets`.`CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            ORDER BY `ReportSets`.`SetId` DESC
            LIMIT 0,1
        ;');
        if (!isset($row[1]) OR (isset($row[1]) AND $row[1]!='0000-00-00 00:00:00' AND $row[1]!='')) {
            // Нет вообще сетов 
            // Или последнем сете есть дата, значит он завершен и нужно добавить новый
            do_sql('
                INSERT INTO `cashmaster`.`ReportSets`
                    (
                        `CreatedBy`,
                        `CashRoomId`
                    )
                VALUES
                    (
                        "'.addslashes($_SESSION[$program]['UserConfiguration']['UserId']).'",
                        "'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                    )
            ;');
        };
        $row = fetch_row_from_sql('
            SELECT
                MAX(`ReportSets`.`SetId`)
            FROM 
                `cashmaster`.`ReportSets`
            WHERE
                `ReportSets`.`CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"            
        ;');
        // Получаем ID текущего рекордсета
        $report_set_id = $row[0];
        do_sql('UNLOCK TABLES;');

?>
