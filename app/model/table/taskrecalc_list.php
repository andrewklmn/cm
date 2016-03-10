<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        function open_item(elem){
            var tds = $(elem).find('td');
            window.location.replace('?c=taskrecalc_file_view&id=' + encodeURI(tds[1].innerHTML));
        };
    </script>
<?php
    unset($table);
    
    $i = 1;
    foreach ($list as $value) {
        $table['data'][$i] = array( $i, $value );
        $i++;
    };
    
    $table['header'] = explode('|', $_SESSION[$program]['lang']['taskrecalc_list_table_header']);
    $table['width'] = array( 90,450,350);
    $table['align'] = explode('|','center|center|center');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']='open_item(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 0;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>