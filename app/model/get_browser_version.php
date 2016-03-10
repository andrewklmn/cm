<?php

/*
 * Возвращает сокращенное название браузера клиента
 */

    $browser = 'IE';
    
    if (strpos( $_SERVER[ 'HTTP_USER_AGENT'] , 'Firefox')) {
        $browser = 'FF';
    };
    if (strpos( $_SERVER[ 'HTTP_USER_AGENT'] , 'Chrome')) {
        $browser = 'GC';
    };
    if (strpos( $_SERVER[ 'HTTP_USER_AGENT'] , 'MSIE 11')) {
        $browser = 'IE11';
    };
    if (strpos( $_SERVER[ 'HTTP_USER_AGENT'] , 'MSIE 10')) {
        $browser = 'IE10';
    };
    if (strpos( $_SERVER[ 'HTTP_USER_AGENT'] , 'MSIE 9')) {
        $browser = 'IE9';
    };
?>
