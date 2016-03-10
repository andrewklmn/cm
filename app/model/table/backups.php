<?php

/*
 * Таблица бекапов системы
 */

    if (!isset($c)) exit;

        
    ?>
    <script>
        function open_backup(elem) {
            //window.location.replace('?c=user_edit&id=' + elem.id);
        };
    </script>
    <?php
    
    $list = scandir($backup_directory);
    rsort($list);
    
    $backups = array();
    foreach ($list as $value) {
        if ($value!='.' 
                AND $value!='..'
                AND is_dir($backup_directory.'/'.$value)) {
            $creator = '';
            include $backup_directory.'/'.$value.'/ticket.php';
            $backups[]=array( 
                $value,
                $creator
            );
        };
    };
    
    unset($table);
    $table['data'] = $backups;
    $table['header'] = explode('|',$_SESSION[$program]['lang']['backup_admin_table_header']);
    $table['width'] = array( 200,400);
    $table['align'] = array( 'center','left');
    $table['th_onclick']=array( ';',';',';',';',';',';' );
    $table['tr_onclick']='open_backup(this.parentNode);';
    $table['title'] = $_SESSION[$program]['lang']['backup_list'];
    $table['hide_id'] = 0;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);




?>
