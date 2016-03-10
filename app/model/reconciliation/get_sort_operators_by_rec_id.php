<?php

/*
 * Возвращает список индексов в депозитах в виде массива по коду сверки
 */
    function get_sort_operators_by_rec_id($id) {
        global $db;
        global $program;
        $row = get_array_from_sql('
            SELECT
                `DepositRuns`.`OperatorName`
            FROM 
                `cashmaster`.`DepositRuns`
            WHERE
                `DepositRuns`.`DepositRecId`="'.addslashes($id).'"
            GROUP BY `DepositRuns`.`MachineDBId`
            ORDER BY `DepositRuns`.`MachineDBId` ASC
        ;');
        $a = array();
        foreach ($row as $value) {
            $a[]=$value[0];
        };
        return $a;
    };
?>
