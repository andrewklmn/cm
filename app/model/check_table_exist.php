<?php

/*
 * Check if table exist in cashmaster database
 */

    function check_table_exist($name) {
        global $db;
        $sql = 'SHOW TABLES;';
        $tables = get_array_from_sql($sql);
        foreach ($tables as $key => $value) {
            if ($value[0]==$name) return true;
        };
        return false;
    };
    
?>
