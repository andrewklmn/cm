<?php

    /*
     * Sorter record
     */

    $record['table'] = 'ValuableTypes';
    $record['labels'] = explode('|',$_SESSION[$program]['lang']['valuable_type_edit_labels']);
    $record['formula'] = explode('|', 'ValuableTypeName|ValuableTypeLabel|SerialNumberIsUsed');
    $record['default'] = explode('|', '||1');
    $record['type'] = explode('|','text|text|checker');
    $record['type_for_new'] = explode('|','text|text|checker');
    $record['select'] = explode('|','|');
    $record['width'] = explode('|','300|300|300');
    $record['back_page'] = '?c=denoms';
    // ================ Possible action ==========
    $record['confirm_update'] = false;
    $record['clone'] = false;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
        
?>
