<?php

    /*
     * Sorter record
     */

    $record['table'] = 'SorterTypes';
    $record['labels'] = explode('|',$_SESSION[$program]['lang']['sorter_type_edit_labels']);
    $record['formula'] = explode('|', 'SorterType');
    //$record['default'] = explode('|', '');
    $record['type'] = explode('|','text');
    $record['type_for_new'] = explode('|','text');
    $record['select'] = explode('|','');
    $record['width'] = explode('|','300');
    $record['back_page'] = '?c=index';
    $record['confirm_update'] = true;
    // ================ Possible action ==========
    $record['clone'] = true;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = false;
        
?>
