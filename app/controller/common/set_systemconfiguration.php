<?php

/*
 * Подгружает данные конфигурации системы в сессию
 */

    $_SESSION[$program]['SystemConfiguration'] = fetch_assoc_row_from_sql('
        SELECT * FROM cashmaster.SystemGlobals
    ;');
    
?>
