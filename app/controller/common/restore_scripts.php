<?php

/*
 * Список пользователей
 */

        if (!isset($c)) exit;

        $roles = array(1,2,3,4); // массив отображаемых ролей 
        
        $data['title'] = $_SESSION[$program]['lang']['error'];
        
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        include 'app/view/reload_after_1_min.php';
        
?>
<div class='container'>
    <?php 
        $data['error'] = $_SESSION[$program]['lang']['application_files_corrupted'];
        include 'app/view/error_message.php';
    ?>
</div>