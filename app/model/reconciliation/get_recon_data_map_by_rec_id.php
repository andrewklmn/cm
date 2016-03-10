<?php

/*
 * Возвращает карту данных ручного пересчета для контроля многопользовательского доступа
 */
    function get_recon_data_map_by_rec_id($id){
        global $db;
        $row = get_array_from_sql('
            SELECT
                CONCAT(`ReconAccountingData`.`DenomId`,"*",
                `ReconAccountingData`.`GradeId`,"*",
                `ReconAccountingData`.`CullCount`)
            FROM 
                `cashmaster`.`ReconAccountingData`
            WHERE   
                `ReconAccountingData`.`DepositRecId`="'.addslashes($id).'"
            ORDER BY `ReconAccountingData`.`id` ASC
        ;');
        $map = array();
        foreach ($row as $value) {
            $map[]=$value[0];
        };
        
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
        
        $row = get_array_from_sql('
            SELECT
                CONCAT(`DepositCurrencyTotal`.`CurrencyId`,"*",
                `DepositCurrencyTotal`.`ExpectedDepositValue`)
            FROM 
                `cashmaster`.`DepositCurrencyTotal`
            WHERE   
                `DepositCurrencyTotal`.`DepositRecId`="'.addslashes($id).'"
        ;');
        foreach ($row as $value) {
            $map[]=$value[0];
        };
        
        
        return implode('**', $map);
    };
?>
