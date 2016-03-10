<?php

    /**
     * Выполнение SQL запроса
     * 
     * @param string $sql
     * @return number количество обработанных строк
     */    

    function do_sql($sql) {
        
        global $db;
        
        
        mysqli_multi_query($db, 'set foreign_key_checks=0;');
        mysqli_multi_query($db, $sql);
//        mysqli_query($db, $sql);
        if (mysqli_errno($db)) {
            echo '<pre>',$sql,'</pre>';
            printf("<br>SQL error: %s\n", mysqli_error($db));
            exit;
        }
        return mysqli_affected_rows($db);
    }

?>
