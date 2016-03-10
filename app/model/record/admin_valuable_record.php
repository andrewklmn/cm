<?php

    /*
     * Sorter record
     */

    $record['table'] = 'Valuables';
    $record['labels'] = explode('|', $_SESSION[$program]['lang']['valuable_edit_labels']);
    $record['formula'] = array(
        0 => 'CategoryName',
        1 => 'SorterTypeId',
        2 => 'DenomId',
        3 => 'ValuableTypeId'
    );
    $record['default'] = explode('|', '|1|1|1');
    $record['type'] = explode('|','text|select|select|select');
    $record['type_for_new'] = explode('|','text|select|select|select');
    $record['select'] = explode('|','|
        SELECT
            `SorterTypes`.`SorterTypeId`, `SorterTypes`.`SorterType`
        FROM `cashmaster`.`SorterTypes`
        ORDER BY `SorterTypes`.`SorterType` ASC
    ;|
        SELECT
                `Denoms`.`DenomId`,
                CONCAT(`Denoms`.`Value`," ",CurrSymbol)
        FROM 
                `cashmaster`.`Denoms`
        LEFT JOIN
                Currency ON Currency.CurrencyId=`Denoms`.`CurrencyId`
        ORDER BY 
                CurrSymbol,`Denoms`.`Value` ASC
    ;|
        SELECT
            `ValuableTypes`.`ValuableTypeId`,
            `ValuableTypes`.`ValuableTypeLabel`
        FROM 
            `cashmaster`.`ValuableTypes`
    ;');
    $record['width'] = explode('|','400|200|200|200');
    $record['back_page'] = '?c=valuables';
    // ================ Possible action ==========
    $record['confirm_update'] = true;
    $record['clone'] = false;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
        
?>
