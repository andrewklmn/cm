<?php

    /*
     * Sorter record
     */

    $record['table'] = 'DepositIndex';
    $record['labels'] = explode('|', $_SESSION[$program]['lang']['indexes_deposit_index_headers']);
    $record['formula'] = explode('|', 'IndexValue|IndexLabel');
    //$default = explode('|', '|');
    $record['type'] = explode('|','text|text');
    $record['type_for_new'] = explode('|','text|text');
    $record['select'] = explode('|','|
        SELECT
            `DepositIndex`.`DepositIndexId`,
            CONCAT(`DepositIndex`.`IndexValue`," - ",`DepositIndex`.`IndexLabel`)
        FROM `DepositIndex`
        ORDER BY `DepositIndex`.`IndexValue` ASC;
    ');
    $record['width'] = explode('|','200|200');
    $record['back_page'] = '?c=indexes';
    $record['confirm_update'] = false;
    // ================ Possible action ==========
    $record['clone'] = false;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
        
?>
