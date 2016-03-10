<?php

/*
 * Recon Accounting Data
 */

    function get_sorter_currency_by_rec_id($id){
        global $db;
        $sql='
            SELECT
                Currency.CurrName,
                Currency.CurrYear
            FROM 
                `SorterAccountingData`
            LEFT JOIN
                Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
            LEFT JOIN
                Denoms ON Denoms.DenomId = Valuables.DenomId
            LEFT JOIN
                Currency ON Currency.CurrencyId = Denoms.CurrencyId
            LEFT JOIN
                DepositRuns ON DepositRuns.DepositRunId=SorterAccountingData.DepositRunId
            LEFT JOIN
                DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
            WHERE 
                DepositRuns.DepositRecId = "'.addslashes($id).'"
            GROUP BY Currency.CurrName
        ;';
        return get_array_from_sql($sql);
    }
?>
