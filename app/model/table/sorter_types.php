<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
    unset($table);
    $table['data'] = get_array_from_sql('
            SELECT
                `SorterTypes`.`SorterType`
            FROM 
                `cashmaster`.`SorterTypes`
            ORDER BY 
                `SorterTypes`.`SorterType` ASC
    ;');      
    $t = explode('|',$_SESSION[$program]['lang']['sorters_table_header']);
    $table['header'] = explode('|', $t[2]);
    $table['width'] = array( 200);
    $table['align'] = array( 'left');
    $table['th_onclick']=array(';');
    $table['tr_onclick']=';';
    $table['title'] = '';
    $table['hide_id'] = 0;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>
    $('table.info_table').find('tr').each(function(){
        var tds = $(this).find('td');
        if ('<?php echo htmlfix($st[1]); ?>'==$(tds[4]).html()) {
            $(tds[4]).css('color','red');
        };
        if ('<?php echo htmlfix($st[0]); ?>'==$(tds[4]).html()) {
            $(tds[4]).css('color','green');
        };
    });
</script>