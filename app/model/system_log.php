<?php

/*
 * Сохранение системного лога обновленное, последняя версия
 */

    include_once 'app/model/english_events.php';

    function system_log($text) {
        global $program, $db;
        do_sql('
            INSERT INTO `cashmaster`.`SystemLog`
                (`Comment`)
            VALUES
                ("'.addslashes($text).'")
        ;');
    };

?>