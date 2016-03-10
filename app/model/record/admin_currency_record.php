<?php

    /*
     * Sorter record model
     */
    
    $record['table'] = 'Currency';
    $record['labels'] = explode('|',$_SESSION[$program]['lang']['currency_edit_labels']);
    $record['formula'] = explode('|', 'CurrSymbol|CurrCode|CurrYear|CurrName|CurrSign|SeriaLength|NumberLength');
    //$record['default'] = explode('|', 'CurrSymbol|CurrCode|CurrYear|CurrName|CurrSign|SeriaLength|NumberLength');
    $record['type'] = explode('|','text|text|text|text|text|text|text');
    $record['type_for_new'] = explode('|','text|text|text|text|text|text|text');
    $record['select'] = explode('|','|');
    $record['width'] = explode('|','300|300|300|300|300|300|300');
    $record['back_page'] = '?c=denoms';
    // ================ Possible action ==========
    $record['confirm_update'] = false;
    $record['clone'] = false;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
    
?>
