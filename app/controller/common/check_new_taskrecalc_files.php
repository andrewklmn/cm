<?php

/* 
 * 
 */
    if (!isset($c)) exit;

    include_once 'app/model/taskrecalc/check_new_taskrecalc_files.php';
    
    if (check_new_taskrecalc_files()) {
        
        $data['text'] = $_SESSION[$program]['lang']['taskrecalc_new_files'].'! <a href="?c=taskrecalc" class="btn btn-small btn-danger">'.
                $_SESSION[$program]['lang']['taskrecalc_go_to_button'].'</a>';
        $data['header'] = $_SESSION[$program]['lang']['attention'];
        include 'app/view/warning_message.php';
        //echo '<pre>';
        //print_r(get_taskrecalc_files_list());
        //echo '</pre>';
    };
?>
