<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        function open_denom(elem){
            window.location.replace('?c=denom_edit&id=' + elem.id);            
        };
    </script>
<?php
    
    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
            `Denoms`.`DenomId`,
            `Currency`.`CurrSymbol`,
            `Denoms`.`Value`,
            `Denoms`.`DenomLabel`
        FROM 
            `cashmaster`.`Denoms`
        LEFT JOIN
            Currency ON  `Denoms`.`CurrencyId`=`Currency`.`CurrencyId`
        ORDER BY 
            `Currency`.`CurrSymbol`,`Denoms`.`Value` ASC
    ;');      
    $table['header'] = explode('|', $_SESSION[$program]['lang']['denoms_denoms_header']);
    $table['width'] = array( 60,60,100);
    $table['align'] = explode('|','center|center|right|right');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']='open_denom(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>