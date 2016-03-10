<?php

/*
 * Возвращает данные пересчета и ручного ввода по результатам сверки
 */
    function get_total_by_rec_denom_grade( $rec_id, $denom, $grade, $scenario) {
        global $db;
        $row = fetch_row_from_sql('
            SELECT
                IFNULL(SUM(`ReconAccountingData`.`CullCount`),0)
            FROM 
                `cashmaster`.`ReconAccountingData`
            WHERE   
                `ReconAccountingData`.`DepositRecId`= "'.addslashes($rec_id).'"
                AND `ReconAccountingData`.`DenomId` = "'.addslashes($denom).'"
                AND `ReconAccountingData`.`GradeId` = "'.addslashes($grade).'"
        ;');
        $summ = $row[0];
        
        $row = fetch_row_from_sql('
                SELECT
                    IFNULL(SUM(SorterAccountingData.ActualCount),"-")
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
                        ScenarioId="'.addslashes($scenario).'") as t1 ON t1.ValuableId = Valuables.ValuableId
                LEFT JOIN
                    Grades ON Grades.GradeId=t1.GradeId
                LEFT JOIN
                    DepositRuns ON DepositRuns.DepositRunId=SorterAccountingData.DepositRunId
                WHERE
                        `DepositRuns`.`DepositRecId`="'.addslashes($rec_id).'" 
                        AND Denoms.DenomId = "'.addslashes($denom).'"
                        AND Grades.GradeId = "'.addslashes($grade).'"
                GROUP BY Denoms.Value,Grades.GradeName
        ;');
        $summ += $row[0];
        return $summ;
    };
?>
