<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        function open_currency(elem){
            window.location.replace('?c=currency_edit&id=' + elem.id);
        };
    </script>
<?php
    
    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
            `Currency`.`CurrencyId`,
            `Currency`.`CurrName`,
            `Currency`.`CurrSymbol`,
            `Currency`.`CurrSign`,
            `Currency`.`CurrCode`,
            `Currency`.`CurrYear`
        FROM 
            `cashmaster`.`Currency`
        ORDER BY 
            `Currency`.`CurrSymbol`,`Currency`.`CurrYear` ASC
    ;');      
    $table['header'] = explode('|', $_SESSION[$program]['lang']['denoms_currencies_header']);
    $table['width'] = array( '300|60|50|50|50');
    $table['align'] = explode('|','center|left|center|center|center|center');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']='open_currency(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>