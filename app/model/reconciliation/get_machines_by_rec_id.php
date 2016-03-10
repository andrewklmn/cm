<?php

/*
 * Возвращает список индексов в депозитах в виде массива по коду сверки
 */
    function get_machines_by_rec_id($id) {
        global $db;
        global $program;
        $row = get_array_from_sql('
            SELECT
                SorterName
            FROM 
                `cashmaster`.`DepositRuns`
            LEFT JOIN
                Machines ON Machines.MachineDBId = `DepositRuns`.`MachineDBId`
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
