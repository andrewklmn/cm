<?php

    /*
     * Sorter record
     */

    $record['table'] = 'Denoms';
    $record['labels'] = explode('|',$_SESSION[$program]['lang']['denom_edit_labels']);
    $record['formula'] = explode('|', 'Value|CurrencyId|DenomLabel');
    //$record['default'] = explode('|', '');
    $record['type'] = explode('|','text|select|text');
    $record['type_for_new'] = explode('|','text|select|text');
    $record['select'] = explode('|','|
        SELECT
        `Currency`.`CurrencyId`,
        `Currency`.`CurrSymbol`
        FROM `cashmaster`.`Currency`
        ORDER BY
            `Currency`.`CurrSymbol` ASC
    ;|');
    $record['width'] = explode('|','300|300|300');
    $record['back_page'] = '?c=denoms';
    // ================ Possible action ==========
    $record['confirm_update'] = false;
    $record['clone'] = false;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
        
?>
