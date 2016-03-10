<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        function open_sorter_index(elem){
            window.location.replace('?c=sorter_index_edit&id=' + elem.id);            
        };
    </script>
<?php
    
    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
            `SorterIndexes`.`Id`,
            `SorterIndexes`.`IndexName`,
            `DepositIndex`.`IndexValue`
        FROM `cashmaster`.`SorterIndexes`
        LEFT JOIN
            `cashmaster`.`DepositIndex` ON `DepositIndex`.`DepositIndexId`=`SorterIndexes`.`DepositIndexId`
        ORDER BY 
            `SorterIndexes`.`IndexName` ASC
    ;');      
    $table['header'] = explode('|', $_SESSION[$program]['lang']['indexes_sorter_index_headers']);
    $table['width'] = array( 300,100);
    $table['align'] = explode('|','center|left|center');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']='open_sorter_index(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>