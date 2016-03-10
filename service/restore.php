<?php

    /*
     * Backup utility
     */
     date_default_timezone_set('Europe/Moscow');

     $c = 'restore';
     
    include '../app/model/db/connection.php';
    include '../app/model/directories.php';
    include '../app/model/system_log.php';
    
     //делаем вид что были заданы агрументы для отладки не из под консоли
     
     /*
     $argv = array(
         1,
         $now_date = '2014-02-10 17:36:40',
         'TEST'
     );
     */
     
     //echo 'gunzip < "../'.$backup_directory.'/'.$argv[1].'/sql_dump_'.str_replace(' ', '_', $argv[1]).'.sql.gz" | '
     //           .' mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_BASE.' '."\n\n";
        
     if (count($argv)==3) {
        
         
        // Восстанавливаем из бекапа базу данных================================
        exec('gunzip < "../'.$backup_directory.'/'.$argv[1].'/sql_dump_'.str_replace(' ', '_', $argv[1]).'.sql.gz" | '
                .' mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_BASE.' ');
        
        // Восстанавливаем hash 
        include '../app/controller/common/refill_integrity_check_restore.php';
        
     } else {
         echo "\nNot enoght parameters!\n\n";
     };

?>
