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
    
    if (isset($_POST['CategoryName'])
            AND isset($_POST['SorterType'])
            AND isset($_POST['DenomLabel'])
            AND isset($_POST['CurrSymbol'])
            AND isset($_POST['ValuableTypeLabel'])) {
        $table['data'] = get_array_from_sql('
            SELECT
                `Valuables`.`ValuableId`,
                `SorterTypes`.`SorterType`,
                `Valuables`.`CategoryName`,
                `Denoms`.`Value`,
                `Currency`.`CurrSymbol`,
                `ValuableTypes`.`ValuableTypeLabel`
            FROM 
                `cashmaster`.`Valuables`
            LEFT JOIN
                Denoms ON Denoms.DenomId = `Valuables`.`DenomId`
            LEFT JOIN
                Currency ON Currency.CurrencyId = Denoms.CurrencyId
            LEFT JOIN
                SorterTypes ON `SorterTypes`.`SorterTypeId`= `Valuables`.`SorterTypeId`
            LEFT JOIN
                ValuableTypes ON `ValuableTypes`.`ValuableTypeId`= `Valuables`.`ValuableTypeId`
            WHERE
                `SorterTypes`.`SorterType` like "%'.addslashes($_POST['SorterType']).'%"
                AND `Valuables`.`CategoryName` like "%'.addslashes($_POST['CategoryName']).'%"
                AND `Denoms`.`Value` like "%'.addslashes($_POST['DenomLabel']).'%"
                AND `Currency`.`CurrSymbol` like "%'.addslashes($_POST['CurrSymbol']).'%"
                AND `ValuableTypes`.`ValuableTypeLabel` like "%'.addslashes($_POST['ValuableTypeLabel']).'%"
            ORDER BY 
                 `Valuables`.`SorterTypeId`,`Valuables`.`CategoryName` ASC
        ;');           
        
    } else {
        $table['data'] = get_array_from_sql('
            SELECT
                `Valuables`.`ValuableId`,
                `SorterTypes`.`SorterType`,
                `Valuables`.`CategoryName`,
                `Denoms`.`Value`,
                `Currency`.`CurrSymbol`,
                `ValuableTypes`.`ValuableTypeLabel`
            FROM 
                `cashmaster`.`Valuables`
            LEFT JOIN
                Denoms ON Denoms.DenomId = `Valuables`.`DenomId`
            LEFT JOIN
                Currency ON Currency.CurrencyId = Denoms.CurrencyId
            LEFT JOIN
                SorterTypes ON `SorterTypes`.`SorterTypeId`= `Valuables`.`SorterTypeId`
            LEFT JOIN
                ValuableTypes ON `ValuableTypes`.`ValuableTypeId`= `Valuables`.`ValuableTypeId`
            ORDER BY 
                 `Valuables`.`SorterTypeId`,`Valuables`.`CategoryName` ASC
        ;');           
    };
   
    $table['header'] = explode('|', $_SESSION[$program]['lang']['valuables_table_headers']);
    $table['width'] = array(60,200,80,60,60);
    $table['align'] = explode('|','center|left|left|right|center|center');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']='open_valuable(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>