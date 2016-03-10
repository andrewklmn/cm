<?php

/*
 * Select field
 */


        echo '<td>';
        // получаем список значений для селекта
        $list = get_array_from_sql($record['select'][$key]);
        foreach ($list as $val) {
            if ($d==$val[0]) {
                echo htmlfix($val[1]);
                break;
            };
        };
        echo '</td>';
?>
