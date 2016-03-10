<?php

/*
 * Контроллер - свойства системы
 */
    if (!isset($c)) exit;

    include './app/model/menu.php';
    
    $data['title'] = $_SESSION[$program]['lang']['system_edit_title'];
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/forms/details_css.php';
    
    $_GET['id']=1;
    
    include 'app/model/record/admin_system_globals_record.php';
    include_once 'app/view/draw_record_edit.php';                
    draw_record_edit( $record );  
    
?>