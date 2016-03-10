<?php

/*
 * Возвращает ожидаемое по деному и коду сверенной сверки
 */
    
    function get_expected_by_denom_and_rec_id($denom,$rec_id) {
        global $db;
        global $program;
        $row = fetch_row_from_sql('
            SELECT
                IFNULL(SUM(`DepositDenomTotal`.`ExpectedCount`),0)
            FROM 
                `cashmaster`.`DepositDenomTotal`
            WHERE
                `DepositDenomTotal`.`DepositReclId`="'.  addslashes($rec_id).'"
                AND `DepositDenomTotal`.`DenomId`="'.  addslashes($denom).'"
        ;');
        return $row[0];
    }
?>
