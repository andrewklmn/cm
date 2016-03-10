<?php

/*
 * Восстанавливает приложение из архива скриптов, пересчитывает новые хеши сумму 
 * и снимает флаг коррупции системы
 */

        if (!isset($c)) exit;
        
         $output = array();
        
        // Удаляем папки с подозрительными скриптами
        
        exec('rm -rf app/' , $output);
        exec('rm -rf bootstrap/', $output);
        exec('rm -rf Bootstrap_files/', $output);
        exec('rm -rf js/', $output);
        exec('rm -rf service/', $output);
        exec('rm -rf index.php', $output);

        //Восстанавливаем файлы из архива скриптов приложения
        
        exec('tar -xf backups/app.tar.gz');
        exec('tar -xf backups/bootstrap.tar.gz');
        exec('tar -xf backups/Bootstrap_files.tar.gz');
        exec('tar -xf backups/js.tar.gz');
        exec('tar -xf backups/service.tar.gz');
        exec('tar -xf backups/index.tar.gz');
        
        // Устанавливаем правильные права на папки
        exec('chmod -R 777 app');
        exec('chmod -R 777 bootstrap');
        exec('chmod -R 777 Bootstrap_files');
        exec('chmod -R 777 js');
        exec('chmod -R 777 service');
        exec('chmod -R 777 index.php');

    // Симаем флаг коррумпированности файлов системы  
    do_sql('
        UPDATE 
            `SystemGlobals`
        SET
            `FilesCorrupted` = "0"
        WHERE 
            `SystemGlobalsId` = "1"
    ;');
    
    system_log('Application integrity was restored by '
            .$_SESSION[$program]['UserConfiguration']['UserLogin'].' from IP: '.$_SERVER['REMOTE_ADDR']);

?>
