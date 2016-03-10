<?php

    /**
     * Получение свойства полей таблицы в виде массива из SQL запроса
     * 
     * @param string $sql
     * @return array возвращает инфо в виде двухмерного массива
     */    

    function fetch_fields_info_from_sql($sql) {
        
        global $db;
        
        $result = mysqli_query($db, $sql);
        if (mysqli_errno($db)) {
            echo '<pre>',$sql,'</pre>';
            printf("<br>SQL error: %s\n", mysqli_error($db));
            exit;
        }
        $row=mysqli_fetch_fields($result);
        mysqli_free_result($result);
        return $row;
    }

?>
