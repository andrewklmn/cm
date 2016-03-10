<?php

/*
 * Prints 2D array as html table
 */

function print_array_as_html_table($array) {
    echo '<table border="1">';    
    echo '<tr>';
    echo '<th style="background-color:lightgray;"></th>';
    foreach (reset($array) as $k=>$v) {
        echo '<th style="background-color:lightgray;">',htmlfix($k),'</th>';
    };
    echo '</tr>';
    foreach ($array as $key=>$value){
        echo '<tr>';
        echo '<th style="background-color:lightgray;">',htmlfix($key),'</th>';
        foreach ($value as $k=>$v) {
            echo '<td style="padding:2px;" align="center">',htmlfix($v),'</td>';
        };
        echo '</tr>';
    };
    echo '</table>';
}
    
?>