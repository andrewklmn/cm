<?php

    /*
     * Sorter record
     */

    $record['table'] = 'ExternalUsers';
    $record['labels'] = explode('|', $_SESSION[$program]['lang']['signers_table_header']);
    $record['formula'] = explode('|', 'ExternalUserName|ExternalUserPost|Phone');
    //$record['default'] = explode('|', '|');
    $record['type'] = explode('|','text|text|text');
    $record['type_for_new'] = explode('|','text|text|text');
    $record['select'] = explode('|','|');
    $record['width'] = explode('|','400|600|400');
    $record['back_page'] = '?c=signers';
    $record['confirm_update'] = false;
    // ================ Possible action ==========
    $record['clone'] = false;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
        
?>
