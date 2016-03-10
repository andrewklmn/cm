<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        function open_valuable_type(elem){
            window.location.replace('?c=valuable_type_edit&id=' + elem.id);            
        };
    </script>
<?php
    
    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
            `ValuableTypes`.`ValuableTypeId`,
            `ValuableTypes`.`ValuableTypeName`,
            `ValuableTypes`.`ValuableTypeLabel`
        FROM 
            `cashmaster`.`ValuableTypes`
        ORDER BY 
            `ValuableTypes`.`ValuableTypeName` ASC
    ;');      
    $table['header'] = explode('|', $_SESSION[$program]['lang']['denoms_valuables_header']);
    $table['width'] = array( 60,150,150);
    $table['align'] = explode('|','center|left|center');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']='open_valuable_type(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>