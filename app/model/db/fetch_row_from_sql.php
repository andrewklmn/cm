<?php

    /**
     * Получение строки таблицы в виде массива из SQL запроса
     * 
     * @param string $sql
     * @return array возвращает строку в виде массиве
     */    

    function fetch_row_from_sql($sql) {
        
        global $db;
        
        $result = mysqli_query($db, $sql);
        if (mysqli_errno($db)) {
            echo '<pre>',$sql,'</pre>';
            printf("<br>SQL error: %s\n", mysqli_error($db));
            exit;
        }        
        $row=mysqli_fetch_row($result);
        mysqli_free_result($result);
        return $row;
    }

?>
