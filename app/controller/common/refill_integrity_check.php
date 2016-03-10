<?php

/*
 * Проверяет изменения в исходных файлах кешмастера и перезаполняет таблицу
 * Интегрити check
 * 
 */

    if (!isset($c)) exit;
    
    $output = array();
    $count = 0;
    $integrity_check_ok = true;
    


    // Находим все файлы PHP в проекте из папка cashmaster
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
            system_log('Hash  for file '.$value.' was added!');
            // Добавляем новую записть про этот файл
            do_sql('
                INSERT INTO `cashmaster`.`IntegrityCheck`
                    (
                        `FileName`,
                        `Hash`
                    )
                VALUES
                    (
                        "'.addslashes($value).'",
                        "'.md5_file($value).'"
                    )
            ;');
        } else {
            if ($rows[0][1]!=md5_file($value)) {
                $integrity_check_ok = false;
                system_log('Hash  for file '.$value.' was updated.');
                // Обновляем хеш
                do_sql('
                    UPDATE 
                        `IntegrityCheck`
                    SET
                        `Hash` = "'.md5_file($value).'"
                    WHERE 
                        `FileName` = "'.addslashes($value).'"
                ;');
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
            system_log('Was deleted: '.( $row[0] - $count ).' system files!');
        } else {
            system_log('Was added: '.( $row[0] - $count ).' system files!');
        };
    };

    // выбираем файлы, которые были удалены чтобы убрать их из таблицы чеков
    $deleted = get_array_from_sql('
        SELECT
            `IntegrityCheck`.`FileName`
        FROM
            IntegrityCheck
        WHERE
            `IntegrityCheck`.`FileName` NOT IN ('.  implode(',', $files).');
    ;');
    
    if ( count($deleted)>0 ) {
        do_sql('
            DELETE FROM 
                `IntegrityCheck`
            WHERE 
                `IntegrityCheck`.`FileName` NOT IN ('.  implode(',', $files).');
        ;');
    };
    
    // Создаем новые архивы с эталонными файлами
    exec('tar -zcf backups/app.tar.gz app');
    exec('tar -zcf backups/bootstrap.tar.gz bootstrap');
    exec('tar -zcf backups/Bootstrap_files.tar.gz Bootstrap_files');
    exec('tar -zcf backups/js.tar.gz js');
    exec('tar -zcf backups/service.tar.gz service');
    exec('tar -zcf backups/index.tar.gz index.php');
    
    
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
