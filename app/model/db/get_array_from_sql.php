<?php

    /**
     * Получение таблицы в виде массива из SQL запроса
     * 
     * @param string $sql
     * @return array возвращает таблицу в двухмерном массиве
     */    

    function get_array_from_sql($sql) {
        
        global $db;
        $answer=array();
        $i=0;
        
        $result = mysqli_query($db, $sql);
        if (mysqli_errno($db)) {
            echo '<pre>',$sql,'</pre>';
            printf("<br>SQL error: %s\n", mysqli_error($db));
            exit;
        }        
        while ($row=mysqli_fetch_array($result,MYSQLI_NUM)) {
            $answer[$i]=$row;
            $i++;
        };
        mysqli_free_result($result);
        return $answer;
    }

?>
