<?php

/* 
 * Проверяет наличие новых файлов в таксрекальках по кассе пользователя
 */
    include_once 'app/model/taskrecalc/get_taskrecalc_files_list.php';

    function check_new_taskrecalc_files() {

        $list = get_taskrecalc_files_list();
        if (count($list)>0) {
            return true;
        } else {
            return false;
        };
    }
?>