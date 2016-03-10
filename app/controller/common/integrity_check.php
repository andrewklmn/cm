<?php

/*
 * Проверяет изменения в исходных файлах кешмастера и сообщает об изменениях
 * Интегрити check
 * 
 */

    include_once 'app/model/db/connection.php';
    include_once 'app/model/system_log.php';
    
    $output = array();
    $count = 0;
    $integrity_check_ok = true;
    
    
    // Находим все файлы PHP в проекте
    exec('find . -name "*.php" -not -path "./backups/*" -not -path "./docs/*"', $output);
    // Находим все файлы JS в проекте
    exec('find . -name "*.js" -not -path "./backups/*" -not -path "./docs/*"', $output);
    
    $files = array();
    
    foreach ($output as $key => $value) {
    // Проверяем есть ли такой файл в списке
        $rows = get_array_from_sql('
            SELECT
                `IntegrityCheck`.`FileName`,
                `IntegrityCheck`.`Hash`
            FROM 
                `IntegrityCheck`
            WHERE
                `IntegrityCheck`.`FileName`="'.addslashes($value).'"
        ;');
        if (count($rows) < 1) {
            $integrity_check_ok = false;
            system_log('New file '.$value.' was found!');
        } else {
            if ($rows[0][1]!=md5_file($value)) {
                $integrity_check_ok = false;
                system_log('Updated file '.$value.' was found!');
            };            
        };
        $count++;
        $files[] = '"'.addslashes($value).'"';
    };

    $row = fetch_row_from_sql('
        SELECT
            count(*)
        FROM
            IntegrityCheck
    ;');
    
    if ($row[0]!=$count) {
        $integrity_check_ok = false;
        if ($row[0] > $count) {
            system_log(( $row[0] - $count ).' files are missing!');
        } else {
            system_log(( $row[0] - $count ).' extra application files!');
        };
    };

    
    // Устанавливаем флаг коррумпированности файлов системы
    if ($integrity_check_ok!=true) {
        do_sql('
            UPDATE 
                `SystemGlobals`
            SET
                `FilesCorrupted` = "1"
            WHERE 
                `SystemGlobalsId` = "1"
        ;');
        system_log('Application files corruption was detected!');
    };
    
?>
