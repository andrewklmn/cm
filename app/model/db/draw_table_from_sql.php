<?php

/*
 * Draw HTML table from SQL
 */

    function draw_table_from_sql($sql) {
        global $db;
        
        $result = mysqli_query($db, $sql);
        if (mysqli_errno($db)) {
            echo '<pre>',$sql,'</pre>';
            printf("<br>SQL error: %s\n", mysqli_error($db));
            exit;
        }
        $row=mysqli_fetch_fields($result);
        //print_r($row);
        $names = array();
        foreach ($row as $key=>$value) {
            //$names[]=$value->table.'.'.$value->name;
            $names[]=$value->table.'<br/>'.$value->name;
            //$names[]=$value->name;
            
        };
        $data = array();
        while ($row=mysqli_fetch_array($result,MYSQLI_NUM)) {
            $data[]=$row;
        };
        mysqli_free_result($result);

    ?>
        <style>
            table.sql th,td {
                font-family: sans-serif;
                font-size: 10px;
                margin: 0px;
                padding: 2px;
                border: 1px solid gray;
                text-align: center;
            }
            table.sql th {
                background-color: lightgray;
            }
        </style>
    <?php
        
        echo '<table class="sql">';
        echo '<tr>';
        foreach ($names as $key=>$value) {
            echo '<th>',$value,'</th>';
        };
        echo '</tr>';
        foreach ($data as $key=>$value) {
            echo '<tr>';
            foreach ($value as $k=>$v) {
                echo '<td>',  htmlfix($v),'</td>';
            };
            echo '</tr>';
        }
        echo '</table>';
        
    };

?>
