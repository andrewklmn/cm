<?php

/*
 * Get Scenario Currency as array
 */

    function get_sorter_accounting_data_currencies($separator_id) {
        global $db;
        global $program;
        return get_array_from_sql('
            SELECT
                   Currency.CurrencyId,
                   Currency.CurrSymbol,
                   Currency.CurrCode,
                   Currency.CurrYear,
                   Currency.CurrName        
            FROM
                SorterAccountingData
            LEFT JOIN
                Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
            LEFT JOIN
                Denoms ON Denoms.DenomId = Valuables.DenomId
            LEFT JOIN
                Currency ON Currency.CurrencyId=Denoms.CurrencyId
            LEFT JOIN
                (SELECT 
                    * 
                 FROM 
                    ValuablesGrades
                 WHERE  
                    ScenarioId="'.$_SESSION[$program]['scenario'][0].'") as t1 ON t1.ValuableId = Valuables.ValuableId
            LEFT JOIN
                Grades ON Grades.GradeId=t1.GradeId
            LEFT JOIN
                DepositRuns ON DepositRuns.DepositRunId=SorterAccountingData.DepositRunId
            WHERE
                    DataSortCardNumber="'.addslashes($separator_id).'"
                    #AND `DepositRuns`.`DepositRecId` is NULL
                    
            GROUP BY Currency.CurrencyId
            ORDER BY Grades.GradeLabel ASC

        ;');
    };
?>
