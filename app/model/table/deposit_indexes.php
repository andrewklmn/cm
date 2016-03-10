<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        function open_deposit_index(elem){
            window.location.replace('?c=deposit_index_edit&id=' + elem.id);              
        };
    </script>
<?php
    
    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
            `DepositIndex`.`DepositIndexId`,
            `DepositIndex`.`IndexValue`,
            `DepositIndex`.`IndexLabel`
        FROM `cashmaster`.`DepositIndex`
        ORDER BY 
            `DepositIndex`.`IndexValue` ASC
    ;');      
    $table['header'] = explode('|', $_SESSION[$program]['lang']['indexes_deposit_index_headers']);
    $table['width'] = array( 100,300);
    $table['align'] = explode('|','center|center|left');
    $table['tr_onclick']='open_deposit_index(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once './app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>