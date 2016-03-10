<?php
/*
 * Редактирование записи (пример работы)
 */
        if (!isset($c)) exit;

        
        $data['title'] = $_SESSION[$program]['lang']['sorter_edit_title'];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        include_once './app/model/system_log.php';

        if (isset($_POST['MachineLogicallyDeleted']) 
                AND $_POST['MachineLogicallyDeleted'] == 1
                AND isset($_POST['confirmation'])) {
            $_POST['MachineConnectionOn'] = 0;
            system_log('Machine '.$_POST['SorterName'].' was logically deleted by: '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' IP: '.$_SERVER['REMOTE_ADDR']);
            
        };
        
        include 'app/model/record/admin_sorter_record.php';
        include_once 'app/view/draw_record_edit.php';                
        draw_record_edit( $record );            
        
?>