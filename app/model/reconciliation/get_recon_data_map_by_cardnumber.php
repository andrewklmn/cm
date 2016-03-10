<?php

/*
 * Возвращает карту данных ручного пересчета для контроля многопользовательского доступа
 */
    function get_recon_data_map_by_cardnumber($card_number){
        global $db;
        $row = get_array_from_sql('
            SELECT
                CONCAT(`ReconAccountingData`.`DenomId`,"*",
                `ReconAccountingData`.`GradeId`,"*",
                `ReconAccountingData`.`CullCount`)
            FROM 
                `cashmaster`.`ReconAccountingData`
            LEFT JOIN
                DepositRecs ON DepositRecs.DepositRecId=ReconAccountingData.DepositRecId
            LEFT JOIN
                DepositRuns ON DepositRuns.DepositRecId=DepositRecs.DepositRecId
            WHERE   
                DepositRuns.DataSortCardNumber="'.addslashes($card_number).'"
                AND IFNULL(DepositRecs.ReconcileStatus,0)="0"
            ORDER BY `ReconAccountingData`.`id` ASC
        ;');
        $map = array();
        foreach ($row as $value) {
            $map[]=$value[0];
        };
        
        $row=  fetch_row_from_sql('
            SELECT
                DepositRuns.DepositRecId
            FROM
                DepositRuns
            LEFT JOIN
                DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
            WHERE   
                DepositRuns.DepositRecId > 0
                AND IFNULL(DepositRecs.ReconcileStatus,0)="0"
            GROUP BY 
                DepositRuns.DepositRecId
        ;');
        $id=$row[0];
        
        $row = get_array_from_sql('
            SELECT
                CONCAT(`DepositDenomTotal`.`DenomId`,"*",
                `DepositDenomTotal`.`ExpectedCount`)
            FROM 
                `cashmaster`.`DepositDenomTotal`
            WHERE   
                `DepositDenomTotal`.`DepositReclId`="'.addslashes($id).'"
            ORDER BY `DepositDenomTotal`.`id` ASC
        ;');
        foreach ($row as $value) {
            $map[]=$value[0];
        };
        return implode('**', $map);
    };
?>
