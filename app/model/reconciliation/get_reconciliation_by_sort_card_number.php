<?php

/*
 * Reconciliation Model
 */
    function get_reconciliation_by_sort_card_number( $number, $reconcile_status){
        global $db;
        $sql='
            SELECT
                *
            FROM 
                DepositRecs
            LEFT JOIN
                UserConfiguration ON UserId=`DepositRecs`.`RecOperatorId`
            LEFT JOIN
                (SELECT
                    DepositRecId,
                    DataSortCardNumber
                From
                    DepositRuns
                WHERE
                    DepositRecId>0
                GROUP BY DataSortCardNumber) as t1 ON `DepositRecs`.`DepositRecId`=t1.DepositRecId
            WHERE 
                DataSortCardNumber = "'.addslashes($number).'"
                AND IFNULL(ReconcileStatus,"0")="'.addslashes($reconcile_status).'"
                
        ;';
        return fetch_assoc_row_from_sql($sql);
    }
?>
