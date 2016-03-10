<?php

/*
 * Reconciliation Model
 */

    include_once './app/model/reconciliation/recon_function_load.php';
    
    function get_reconciliation_by_id($id){
        global $db;        
        $sql='
            SELECT
                *
            FROM 
                `cashmaster`.`DepositRecs`
            LEFT JOIN
                UserConfiguration ON UserId=`DepositRecs`.`RecOperatorId`
            LEFT JOIN
                (SELECT
                    DepositRecId,
                    DataSortCardNumber
                From
                    DepositRuns
                GROUP BY DepositRecId) as t1 ON `DepositRecs`.`DepositRecId`=t1.DepositRecId
            WHERE 
                `DepositRecs`.`DepositRecId` = "'.addslashes($id).'"
                
        ;';
        return fetch_assoc_row_from_sql($sql);
    }
?>
