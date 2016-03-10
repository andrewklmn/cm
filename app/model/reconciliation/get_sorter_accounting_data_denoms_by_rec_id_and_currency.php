<?php

/*
 * Get sorter accounting data denoms by cardnumber
 */

    function get_sorter_accounting_data_denoms_by_rec_id_and_currency($id,$currency) {
        
        global $db;
        global $program;
        return get_assoc_array_from_sql('
            SELECT
                Denoms.DenomId as DenomId,
                Denoms.Value as Value                
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
                AND Denoms.CurrencyId = "'.  addslashes($currency).'"
            GROUP BY Denoms.DenomId
        ;');
    };

?>
