<?php

/*
 * Recon Accounting Data
 */

    function get_sorter_accounting_data_by_run_id($id){
        global $db;
        $sql='
            SELECT
                `SorterAccountingData`.`Id`,
                `SorterAccountingData`.`DepositRunId`,
                `SorterAccountingData`.`ValuableId`,
                `SorterAccountingData`.`ActualCount`
            FROM 
                `cashmaster`.`SorterAccountingData`
            WHERE 
                `SorterAccountingData`.`DepositRunId` = "'.addslashes($id).'"
            
        ;';
        return get_array_from_sql($sql);
    }
?>
