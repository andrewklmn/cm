<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        function open_valuable(elem){
            window.location.replace('?c=valuable_edit&id=' + elem.id);            
        };
    </script>
<?php
    unset($table);
    
    $table['data'] = get_array_from_sql('
        SELECT
            `Valuables`.`ValuableId`,
            `SorterTypes`.`SorterType`,
            `Valuables`.`CategoryName`,
            "-",
            "-",
            "-"
        FROM 
            `cashmaster`.`Valuables`
        LEFT JOIN
            SorterTypes ON `SorterTypes`.`SorterTypeId`= `Valuables`.`SorterTypeId`
        LEFT JOIN
            ValuableTypes ON `ValuableTypes`.`ValuableTypeId`= `Valuables`.`ValuableTypeId`
        WHERE
            `Valuables`.`DenomId`="0"
            AND `Valuables`.`ValuableTypeId`="0"
        ORDER BY 
             `Valuables`.`SorterTypeId`,`Valuables`.`CategoryName` ASC
    ;');      
    $table['header'] = explode('|', $_SESSION[$program]['lang']['valuables_table_headers']);
    $table['width'] = array( 60,200,80,60,60);
    $table['align'] = explode('|','center|left|left|right|center|center|center');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']='open_valuable(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>