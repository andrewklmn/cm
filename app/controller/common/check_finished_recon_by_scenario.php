<?php

/*
 * Проверяет есть ли по сценарию сверенные сверки
 * и устанавливает $flag = false
 */
        if (!isset($c)) exit;
        
        $count = count_rows_from_sql('
            SELECT
                *
            FROM 
                `cashmaster`.`DepositRecs`
            WHERE
                `DepositRecs`.`ScenarioId`="'.addslashes($scen_id).'"
                AND `DepositRecs`.`ReconcileStatus`=1
                AND `DepositRecs`.`ServiceRec`=0
        ;');
        if ( $count>0 ) $flag = false;
        
?>
