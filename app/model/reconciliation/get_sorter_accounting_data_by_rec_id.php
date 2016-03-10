<?php

/*
 * Recon Accounting Data
 */

    function get_sorter_accounting_data_by_rec_id($id){
        global $db;
        $sql='
            SELECT
                `Valuables`.`CategoryName`,
                IFNULL(SUM(`SorterAccountingData`.`ActualCount`),0),
                IFNULL(SUM(`SorterAccountingData`.`ActualCount`)*Denoms.Value,0),
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
            GROUP BY `Valuables`.`CategoryName`
        ;';
        return get_array_from_sql($sql);
    }
?>
