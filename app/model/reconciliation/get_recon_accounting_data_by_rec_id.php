<?php

/*
 * Recon Accounting Data
 */

    function get_recon_accounting_data_by_rec_id($id){
        global $db;
        $sql='
            SELECT
                `ReconAccountingData`.`id`,
                `ReconAccountingData`.`DepositRecId`,
                `ReconAccountingData`.`DenomId`,
                `ReconAccountingData`.`GradeId`,
                `ReconAccountingData`.`CullCount`
            FROM 
                `cashmaster`.`ReconAccountingData`
            WHERE 
                `ReconAccountingData`.`DepositRecId` = "'.addslashes($id).'"
        ;';
        return get_array_from_sql($sql);
    }
?>
