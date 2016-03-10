<?php

/*
 * Проверяет не сверена ли сверка
 */
        if (!isset($c)) exit;
        
        $row = fetch_row_from_sql('
            SELECT
                RecLastChangeDatetime,
                ReconcileStatus
            FROM
                DepositRecs
            WHERE
                `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
        ;');
        if($row[1]=="1") {
            do_sql('UNLOCK TABLES;');
            echo 2;
            exit;
        };
        $last_update_time = $row[0];
?>
