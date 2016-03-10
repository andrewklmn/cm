<?php

/*
 * Проиводит необходимые действия по созданию фалой бекапа
 */

        // Модуль можно запускать только для службы backup
        if (!isset($c) OR $c!='backup') exit;

        // создаем папку для бекапа базы данных
        // ==============================================
        if (!file_exists('../'.$backup_directory.'/'.$argv[1])) {
            mkdir( '../'.$backup_directory.'/'.$argv[1], 0777);
            chmod( '../'.$backup_directory.'/'.$argv[1], 0777);
        };
        
        // Сохраняем файл квитацию по архиву ===================================
        $ticket = '<?php $creator="'.str_replace('"', '\"', $argv[2]).'"; ?>';
        $fp = fopen( '../'.$backup_directory.'/'.$argv[1].'/ticket.php', 'w');
        chmod( '../'.$backup_directory.'/'.$argv[1].'/ticket.php', 0777);
        fwrite($fp, $ticket);
        fclose($fp);
        
        
        // 1. снимаем дамп с базы данных =======================================
        exec('cd "../"; mysqldump -u'.DB_USER.' -p'.DB_PASS.' '.DB_BASE.' | gzip > "'
                .$backup_directory.'/'.$argv[1].'/sql_dump_'.str_replace(' ', '_', $argv[1]).'.sql.gz" ');
        chmod( '../'.$backup_directory.'/'.$argv[1].'/sql_dump_'.str_replace(' ', '_', $argv[1]).'.sql.gz', 0777);
        
        
        // 2. сниманием копию с XML архива =====================================
        exec('cd "../"; zip -r "'.$backup_directory.'/'.$argv[1]
                .'/xml_archive_'.str_replace(' ', '_', $argv[1]).'.zip" "'.$xml_archive_directory.'" ');
        chmod( '../'.$backup_directory.'/'.$argv[1].'/xml_archive_'.str_replace(' ', '_', $argv[1]).'.zip', 0777);
        

?>
