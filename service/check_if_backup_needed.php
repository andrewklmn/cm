<?php

/*
 * Проверяем не пора ли делать бекап
 */

     date_default_timezone_set('Europe/Moscow');

    include '../app/model/db/connection.php';
    include '../app/model/directories.php';
    include '../app/model/english_events.php';
     

    //1. Получаем дату последнего бекапа в базе
    $list = scandir('../'.$backup_directory);
    
    $backup_list = array();
    foreach ($list as $value) {
        if (is_dir('../'.$backup_directory.'/'.$value)
                AND $value!='.'
                AND $value!='..') {
            
            $backup_list[] = $value;
        };
    };
    rsort($backup_list);
    
    if (isset($backup_list[0])) {
        $last_backup_date = $backup_list[0];
    } else {
        $last_backup_date = '1972-11-29 00:00:00';
    };
    
    //2. Получаем текущую дату
    $now_date = date('Y-m-d H:i:s',time());

    //3. Получаем кол-во дней из SystemGlobals до следубщего бекапа
    $row = fetch_assoc_row_from_sql('
        SELECT
            *  
        FROM 
            `cashmaster`.`SystemGlobals`
    ;');
    $auto_archive_period = $row['AutoArchivePeriod'];

            
    
    //4. Если кол-во дней превысило критический, то backup, если нет - выход
    if (Round((strtotime($now_date)-strtotime($last_backup_date))/86400) >=  $auto_archive_period) {
        
        exec('cd ../'.$service_directory.'; php -f backup.php "'
                            .$now_date.'" "autobackup" > /dev/null &', $output);
        echo '<pre>';
        print_r($output);
        echo '</pre>';

    } else {
        echo "\n\nBackup is up to date\n\n";
    };
    
?>
