<?php

/*
 * Таблица бекапов системы
 */

    if (!isset($c)) exit;

        
    ?>
    <script>
        function open_update(elem) {
            //window.location.replace('?c=user_edit&id=' + elem.id);
        };
    </script>
    <?php
    
    $updates = get_array_from_sql('
        SELECT
            `UpdateHistory`.`Id`,
            `UpdateHistory`.`UpdateDateTime`,
            `UpdateHistory`.`UpdateFile`
        FROM 
            `cashmaster`.`UpdateHistory`
        ORDER BY
            `UpdateHistory`.`Id` DESC
    ;');
    
    unset($table);
    $table['data'] = $updates;
    $table['header'] = explode('|',$_SESSION[$program]['lang']['update_table_header']);
    $table['width'] = array( 200,400);
    $table['align'] = array( 'center','left');
    $table['th_onclick']=array( ';',';',';',';',';',';' );
    $table['tr_onclick']='open_update(this.parentNode);';
    $table['title'] = $_SESSION[$program]['lang']['update_list'];
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);




?>
