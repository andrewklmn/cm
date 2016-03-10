<?php

/*
 * Добавляет сиквенцию РепортСетФйди и РепортТипАйди
 * в таблицу Reports если такой там еще нет
 */

    if (!isset($c)) exit;
    
    // Проверяем есть ли сиквенция такая
    $row = fetch_row_from_sql('
        SELECT
            count(*)
        FROM 
            `cashmaster`.`Reports`
        WHERE
            `Reports`.`ReportTypeId`="'.addslashes($report['ReportTypeId']).'"
            AND `Reports`.`ReportSetId`="'.addslashes($_POST['report_set_id']).'"         
    ;');
    if ( $row[0] == 0 ){
        // Добавляем сиквенцию, потому что такой ещё не было
        do_sql('
            INSERT INTO `cashmaster`.`Reports`
                (
                    `ReportTypeId`,
                    `ReportSetId`
                )
            VALUES
                (
                    "'.addslashes($report['ReportTypeId']).'",
                    "'.addslashes($_POST['report_set_id']).'"
                );
        ;');
    };
    
?>
