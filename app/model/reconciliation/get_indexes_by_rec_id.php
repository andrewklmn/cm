<?php

/*
 * Возвращает список индексов в депозитах в виде массива по коду сверки
 */
    function get_indexes_by_rec_id($id) {
        global $db;
        global $program;
        $row = get_array_from_sql('
            SELECT
                `DepositRuns`.`IndexName`
            FROM 
                `cashmaster`.`DepositRuns`
            WHERE
                `DepositRuns`.`DepositRecId`="'.addslashes($id).'"
            GROUP BY `DepositRuns`.`IndexName`
            ORDER BY `DepositRuns`.`IndexName` ASC
        ;');
        $a = array();
        foreach ($row as $value) {
            $a[]=$value[0];
        };
        return $a;
    };
?>
