<?php

/*
 * Reconciliation Model
 */
    function get_recon_details_by_id($id){
                
        global $db;
        global $program;
        
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
                GROUP BY DataSortCardNumber) as t1 ON `DepositRecs`.`DepositRecId`=t1.DepositRecId
            WHERE 
                `DepositRecs`.`DepositRecId` = "'.addslashes($id).'"
                
        ;';
        
        
        return fetch_assoc_row_from_sql($sql);
    }
?>
