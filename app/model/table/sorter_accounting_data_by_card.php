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
                Currency.CurrSymbol,
                Denoms.Value,
                SUM(`SorterAccountingData`.`ActualCount`)
        FROM `cashmaster`.`SorterAccountingData`
        LEFT JOIN
                DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
        LEFT JOIN
                Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
		LEFT JOIN
				Denoms ON Denoms.DenomId = Valuables.DenomId
		LEFT JOIN
				Currency ON Currency.CurrencyId = Denoms.CurrencyId
        WHERE
                DepositRuns.DataSortCardNumber="'.addslashes($_GET['card']).'"
		GROUP BY Currency.CurrencyId, Denoms.DenomId
		ORDER BY CurrSymbol,Denoms.Value ASC
    ;');
    $table['header'] = explode('|', 'Валюта|Номинал|Счёт');
    $table['width'] = array( 100,100,100);
    $table['align'] = explode('|','center|right|right');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']=';';
    $table['title'] = '';
    //$table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>