<?php

/*
 * Возвращает список индексов в депозитах в виде массива по коду сверки
 */
    function get_sorter_start_and_stop_time_by_rec_id($id) {
        global $db;
        global $program;
        return fetch_row_from_sql('
            SELECT
                MIN(`DepositRuns`.`DepositStartTimeStamp`),
                MAX(`DepositRuns`.`DepositEndTimeStamp`)
            FROM 
                `cashmaster`.`DepositRuns`
            WHERE
                `DepositRuns`.`DepositRecId`="'.addslashes($id).'"
        ;');
    };
?>
