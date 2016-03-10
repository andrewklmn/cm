<?php

/*
 * Проверяет наличие неформализованных valuables в данных пересчета
 */

        if (!isset($c)) exit;
        

        $sql = '
            SELECT 
                Id,
                Valuables.CategoryName,
                ActualCount,
                IFNULL(Denoms.DenomId,"-") as Denom
            FROM 
                SorterAccountingData
            LEFT JOIN
                   Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
            LEFT JOIN
                DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
            LEFT JOIN
                Denoms ON Denoms.DenomId = Valuables.DenomId
            WHERE
                   DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                   AND Valuables.DenomId="0" AND Valuables.ValuableTypeId="0" 
                   AND `DepositRuns`.`DepositRecId`  = "'.$DepositRecId.'"
        ;';
        $new_valuables = get_assoc_array_from_sql($sql);
        if(count($new_valuables)>0) {
            include './app/model/table/new_category_names.php';
        };
?>
