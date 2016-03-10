<?php

/*
 * Updates scenario record
 */


    if (!isset($c)) exit;
    
    
    
    // Проверяем не изменилась ли запись 
    
    // если изменилась, то сообщаем новые данные
    include './app/view/html_header.php';
    echo '1';
    echo $_POST['oldvalues'];
    
    
    // если не изменилось, то апдаём и сообщаем в ответ новое значение
    include './app/view/html_header.php';
    //echo '0';
    //echo $_POST['values'];
    
?>
