<?php

/*
 * Machines data collector script
 */

    //if (!isset($c)) exit;
    $c = 'collector';
    
    // получаем список машины из БД
    include '../../model/db/connection.php';
    $sql='
        SELECT 
            * 
        FROM 
            Machines
        WHERE
            MachineLogicallyDeleted=0
        ORDER BY MachineDBId ASC
    ;';
    $machines = get_assoc_array_from_sql($sql);
    
    // Собираем данные с машины в зависимости от типа.
    foreach ($machines as $key=>$value) {
        switch ($value['SorterTypeId']) {
            case '1':
                    include 'toshiba_collector.php';                    
                break;
            case '2':
                    include 'cobra_collector.php';
                break;
            case '3':
                    include 'glory_collector.php';
                break;
            case '4':
                    // Отключаем автоматический опрос симулятора Сортера
                    //include 'simulator_collector.php';
                break;
            default:
                break;
        }
    };
    
?>
