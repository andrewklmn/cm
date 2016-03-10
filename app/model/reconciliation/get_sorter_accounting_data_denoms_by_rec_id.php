<?php

/*
 * Get sorter accounting data denoms by cardnumber
 */

    function get_sorter_accounting_data_denoms_by_rec_id($id) {
        
        global $db;
        global $program;
        return get_assoc_array_from_sql('
            SELECT
                Denoms.DenomId as DenomId
            FROM
                SorterAccountingData
            LEFT JOIN
                Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
            LEFT JOIN
                Denoms ON Denoms.DenomId = Valuables.DenomId
            LEFT JOIN
                DepositRuns ON DepositRuns.DepositRunId=SorterAccountingData.DepositRunId
            WHERE
                DepositRuns.DepositRecId = "'.addslashes($id).'"
            GROUP BY Denoms.DenomId
        ;');
    };

?>
