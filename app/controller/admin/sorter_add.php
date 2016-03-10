<?php
/*
 * Редактирование записи (пример работы)
 */
        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['sorter_add_title'];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
  
        
        include 'app/model/record/admin_sorter_record.php';
        include_once 'app/view/draw_record_add.php';                
        draw_record_add( $record );  

         
?>