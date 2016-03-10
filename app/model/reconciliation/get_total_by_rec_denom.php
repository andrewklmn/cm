<?php

/*
 * Возвращает данные пересчета и ручного ввода по результатам сверки
 */
    function get_total_by_rec_denom( $rec_id, $denom) {
        global $db;
        $row = fetch_row_from_sql('
            SELECT
                IFNULL(SUM(`ReconAccountingData`.`CullCount`),0)
            FROM 
                `cashmaster`.`ReconAccountingData`
            WHERE   
                `ReconAccountingData`.`DepositRecId`= "'.addslashes($rec_id).'"
                AND `ReconAccountingData`.`DenomId` = "'.addslashes($denom).'"
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
                    DepositRuns ON DepositRuns.DepositRunId=SorterAccountingData.DepositRunId
                WHERE
                        `DepositRuns`.`DepositRecId`="'.addslashes($rec_id).'" 
                        AND Denoms.DenomId = "'.addslashes($denom).'"
                        
                GROUP BY Denoms.Value
        ;');
        $summ += $row[0];
        return $summ;
    };
?>
