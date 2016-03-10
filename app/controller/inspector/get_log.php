<?php

/* 
 * CSV log file controller 
 */

    if (!isset($c)) exit;

    if (isset($_GET['name'])) {
        
        $name = $_GET['name'].'.csv';
        header("Content-type: text/csv; charset=windows-1251");
        header("Content-Disposition: attachment; filename=$name");
        
        echo file_get_contents($log_backup_directory.'/'.$name);
        
    } else {
        $name = date('Y-m-d_H-i-s',time()).'_'.$_SESSION[$program]['user'].'.csv';
        header("Content-type: text/csv; charset=windows-1251");
        header("Content-Disposition: attachment; filename=$name");

        $system_log = get_assoc_array_from_sql('
            SELECT
                `SystemLog`.`DateAndTime`,
                `SystemLog`.`Comment`
            FROM 
                `cashmaster`.`SystemLog`
            '.$where.'
            ORDER BY `SystemLog`.`Id`DESC
        ;');

        foreach ($system_log as $value) {
            echo '"',$value['DateAndTime'],'","',iconv('UTF-8','cp1251',$value['Comment']),'"
';
        };

    };
    
?>