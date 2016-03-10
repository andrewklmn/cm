<?php

/*
 * Таблица бекапов системы
 */

    if (!isset($c)) exit;

        
    ?>
    <script>
        function restore_backup(elem) {
            var time = $(elem).find('td')[0].innerHTML;
            window.location.replace('?c=restore&time=' + encodeURI(time));
        };
    </script>
    <?php
    
    
    unset($table);
    $table['data'] = $backups;
    $table['header'] = explode('|',$_SESSION[$program]['lang']['backup_admin_table_header']);
    $table['width'] = array( 200,400);
    $table['align'] = array( 'center','left');
    $table['th_onclick']=array( ';',';',';',';',';',';' );
    $table['tr_onclick']='restore_backup(this.parentNode);';
    $table['title'] = 'Click on backup for start restoring:';
    $table['hide_id'] = 0;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);




?>
