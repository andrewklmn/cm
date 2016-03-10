<?php

/*
 * Возвращает значение для заданной комбинации 
 * Кода Типа отчета и Кода РепортСета и названия параметра
 */

    function get_value_from_report_saves( $key ) {
        
        global $db, $program, $report;
        
        $row = fetch_row_from_sql('
            SELECT 
                `ReportSaves`.`Value`
            FROM 
                cashmaster.ReportSaves
            WHERE
                `ReportSaves`.`ReportSetId`="'.addslashes($_POST['report_set_id']).'"
                AND `ReportSaves`.`ReportTypeId`= "'.addslashes($report['ReportTypeId']).'"
                AND `ReportSaves`.`Key`= "'.addslashes($key).'"
        ;');
        
        return $row[0];
    };

?>
