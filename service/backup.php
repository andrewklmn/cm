<?php

    /*
     * Backup utility
     */
     date_default_timezone_set('Europe/Moscow');

     $c = 'backup';
     
     //делаем вид что были заданы агрументы для отладки не из под консоли
     /*
     $argv = array(
         1,
         $now_date = date('Y-m-d H:i:s'),
         'TEST'
     );
      */ 
     
     if (count($argv)==3) {
         
        include '../app/model/db/connection.php';
        include '../app/model/directories.php';
        include '../app/model/english_events.php';
        
        // Включаем сервисный режим
        do_sql('
            UPDATE 
                `cashmaster`.`SystemGlobals`
            SET
                `ServiceMode` = 1
            WHERE 
                SystemGlobalsId="1"         
        ;');
        
        // ============ Создаем файлы бекапа ===================================
        include '../service/module/make_files.php';
       
        // ===Очищаем таблицы от устаревших данных =============================
        include '../service/module/tables_purge.php';
        
        // Отключаем сервисный режим и сохраняем новую дату в глобальные настройки
        do_sql('
            UPDATE 
                `cashmaster`.`SystemGlobals`
            SET
                `ServiceMode` = 0,
                `LastArchiveDate` = "'.$argv[1].'"
            WHERE 
                SystemGlobalsId="1"         
        ;');
        
        // Пишем состемный лог про это
        do_sql('
            INSERT INTO `cashmaster`.`SystemLog`
                (`Comment`)
            VALUES
                ("'.addslashes($events[104].' '.$argv[2]).'")
        ;');
     } else {
         echo "\nNot enoght parameters!\n\n";
     };

?>
