<?php

    /*
     * Sorter record
     */

    $record['table'] = 'Grades';
    $record['labels'] = explode('|',$_SESSION[$program]['lang']['grade_edit_labels']);
    $record['formula'] = explode('|', 'GradeName|GradeLabel');
    //$record['default'] = explode('|', '');
    $record['type'] = explode('|','text|text');
    $record['type_for_new'] = explode('|','text|text');
    $record['select'] = explode('|','|');
    $record['width'] = explode('|','300|300');
    $record['back_page'] = '?c=denoms';
    // ================ Possible action ==========
    $record['confirm_update'] = false;
    $record['clone'] = false;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
        
?>
