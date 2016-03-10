<?php

/*
 * get sorter accounting data grades by cardnumber
 */

    function get_sorter_accounting_data_grades_by_cardnumber($separator_id) {
        
        global $db;
        global $program;
        $sql = '
            SELECT
                IFNULL(Grades.GradeId,""),
                IFNULL(Grades.GradeName,"-"),
                IFNULL(Grades.GradeLabel,"-")
            FROM
                Denoms 
            LEFT JOIN
                Valuables ON Denoms.DenomId = Valuables.DenomId
            LEFT JOIN
                SorterAccountingData ON Valuables.ValuableId=SorterAccountingData.ValuableId
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
                    
            GROUP BY Grades.GradeName
            ORDER BY Grades.GradeLabel ASC
        ;'; 
        return get_array_from_sql($sql); 
    }
    
?>
