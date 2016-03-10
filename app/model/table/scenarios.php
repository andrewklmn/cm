<?php

/*
 * Таблица списка машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        var a;
        function open_scen(elem){
            window.location.replace('?c=scen_edit&id=' + elem.id);
        };
    </script>
<?php

    
    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
               ScenarioId,
               ScenarioName,
               IF(DefaultScenario=1,"'.addslashes($_SESSION[$program]['lang']['yes']).'",""),
               IF(LogicallyDeleted=1,"X","")
        FROM
               Scenario
        ORDER BY ScenarioName ASC
    ;');      
    
    $table['header'] = explode('|',$_SESSION[$program]['lang']['scens_table_headers']);
    $table['width'] = array( 600 );
    $table['align'] = array( 'left','left','center','center');
    $table['th_onclick']=array(';',';',';',';',';',';');
    $table['tr_onclick']='open_scen(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once './app/view/draw_select_table.php';
    draw_select_table($table);

?>
<script>
    $('table.info_table').find('tr').each(function(){
        var tds = $(this).find('td');
        if ('<?php echo htmlfix($st[1]); ?>'==$(tds[3]).html()) {
            $(tds[3]).css('color','red');
        };
        if ('<?php echo htmlfix($st[0]); ?>'==$(tds[3]).html()) {
            $(tds[3]).css('color','green');
        };
    });
</script>