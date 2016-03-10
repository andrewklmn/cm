<?php

/*
 * Сборщик данных пересчета с сортеров
 */
    
    $c='collector';
    
    error_reporting(0);
    
    // Если запущено из браузера, то отображаем данные
    if (!isset($argv)) {
            header('Content-Type: text/html; charset=windows-1251');
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Datum aus Vergangenheit
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache"); 
            
            $output = array();
            exec( 'pstree | grep collector.sh', $output);
            if (count($output)>0) {
                echo "\nOther collector is runing\n\n";
                exit;
            };
    };

    include '../app/model/db/connection.php';
    include '../app/model/system_log.php';
    
    
    // Проверяем не запущен ли сборщик в системе
    $output = array();
    exec( 'pstree | grep collector.sh', $output);
    if (count($output)>1) {
        echo "\nOther collector is runing\n\n";
        exit;
    };
    
    
    function ping($host, $port, $timeout) { 
        $fP = fsockopen($host, $port, $errno, $errstr, $timeout); 
        if (!$fP) { return false; };
        return true; 
    };

    $sorters = get_assoc_array_from_sql('
        SELECT
            *
        FROM 
            `cashmaster`.`Machines`
        LEFT JOIN
            SorterTypes ON `Machines`.`SorterTypeId`=`SorterTypes`.`SorterTypeId`
        WHERE
            SorterName <> "Simulator" AND 
            MachineLogicallyDeleted="0"
    ;'); 
    
    
    foreach ($sorters as $key=>$sorter) {

        if (!isset($argv)) {
            $time_start = microtime(true);
            echo '<pre>';
            echo $sorter['SorterType'],' ',$sorter['SorterName'],' >>>>>>>';
            echo '</pre>';
        };  

        switch ($sorter['SorterType']) {
            case 'Cobra':
                    do_sql('LOCK TABLES 
                                Machines WRITE, 
                                DepositRuns WRITE, 
                                SorterAccountingData WRITE,
                                Valuables WRITE,
                                Rejects WRITE,
                                SorterRejectData WRITE,
                                SystemLog WRITE
                    ;');
                    // 1. Устанавливаем машину в состояние OFF
                    $last_state = $sorter['MachineConnectionOn'];
                    // Отключаем машину для окружающих
                    do_sql('
                        UPDATE 
                            `cashmaster`.`Machines`
                        SET
                            `MachineConnectionOn` = 0
                        WHERE 
                            `MachineDBId` = "'.addslashes($sorter['MachineDBId']).'"
                    ;');
                    
                    $ping_ok = false;
                    // 1. Пингуем хост и порт кобры.
                    if ( ping($sorter['NetworkAddress'],$sorter['NetworkPort'],1 )==true ) {
                        $ping_ok = true;
                    };    
                    
                    
                    if ($ping_ok == true ) {
                        $mssql_ok = true;
                        // 2. Пытаемся установить связь с БД кобры
                        $dbhandle = mssql_connect(
                                                    $sorter['NetworkAddress'], 
                                                    $sorter['MachineLogin'], 
                                                    $sorter['MachinePass']
                                                ) or $mssql_ok = false;
                        if ($mssql_ok==true) {
                            $base_exist_ok = mssql_select_db( 
                                                        $sorter['MachineDatabaseName'], 
                                                        $dbhandle
                                                    );                        
                        } else {
                            $base_exist_ok = FALSE;
                        };
                    } else {
                        $mssql_ok == false;
                        $base_exist_ok = FALSE;
                    };
                     
                    
                    
                    if ( $ping_ok==TRUE 
                            AND $mssql_ok==TRUE
                            AND $base_exist_ok==TRUE) {
                        if ($last_state==0) {
                            if (!isset($argv)) {
                                echo $sorter['SorterType'].' - '.$sorter['SorterName'].' is now ON';
                            };
                            system_log($sorter['SorterType'].' - '.$sorter['SorterName'].' is now ON'); 
                        };
                        //===============================================
                        //
                        //
                        //      Получаем данные пересчета и соханяем себе
                        include '../service/module/collect_from_cobra.php';
                        //
                        //
                        //
                        // Сообщаем что последний статус машины был ВКЛ
                        do_sql('
                            UPDATE 
                                `cashmaster`.`Machines`
                            SET
                                `MachineConnectionOn` = 1
                            WHERE 
                                `MachineDBId` = "'.addslashes($sorter['MachineDBId']).'"
                        ;');
                        mssql_close($dbhandle);
                        
                    }; 
                    
                    
                    if ($ping_ok==true 
                            AND $mssql_ok==false
                            AND $last_state==0) {
                        if (!isset($argv)) {
                            echo $sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, ping is ok, but SQL server connection failed';
                        };
                        system_log($sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, ping is ok, but SQL server connection failed');
                    } else {
                        if ($ping_ok==true 
                                AND $mssql_ok==TRUE
                                AND $base_exist_ok==FALSE
                                AND $last_state==0) {
                            if (!isset($argv)) {
                                echo $sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, ping is ok, SQL server is ok, but Database '.$sorter['MachineDatabaseName'].' is not accessible';
                            };
                            system_log($sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, ping is ok, SQL server is ok, but Database '.$sorter['MachineDatabaseName'].' is not accessible');
                        };
                    };
                    
                    
                    if ($ping_ok==FALSE) {
                        if ($last_state==1) {
                            if (!isset($argv)) {
                                echo $sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, no ping to host';
                            };
                            system_log($sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, no ping to host');                            
                            do_sql('
                                UPDATE 
                                    `cashmaster`.`Machines`
                                SET
                                    `MachineConnectionOn` = 0
                                WHERE 
                                    `MachineDBId` = "'.addslashes($sorter['MachineDBId']).'"
                            ;');
                        };
                    } elseif ( $mssql_ok==FALSE) {
                        if ($last_state==1) {
                            if (!isset($argv)) {
                                echo $sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, SQL server connection failed';
                            };
                            system_log($sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, SQL server connection failed');
                            do_sql('
                                UPDATE 
                                    `cashmaster`.`Machines`
                                SET
                                    `MachineConnectionOn` = 0
                                WHERE 
                                    `MachineDBId` = "'.addslashes($sorter['MachineDBId']).'"
                            ;');
                        };
                    } elseif ( $base_exist_ok==FALSE) {
                        if ($last_state==1) {
                            if (!isset($argv)) {
                                echo $sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, Database '.$sorter['MachineDatabaseName'].' is not accessible';
                            };
                            system_log($sorter['SorterType'].' - '.$sorter['SorterName'].' is OFF, Database '.$sorter['MachineDatabaseName'].' is not accessible');
                            do_sql('
                                UPDATE 
                                    `cashmaster`.`Machines`
                                SET
                                    `MachineConnectionOn` = 0
                                WHERE 
                                    `MachineDBId` = "'.addslashes($sorter['MachineDBId']).'"
                            ;');
                        };
                    };
                    do_sql('UNLOCK TABLES;');
                break;
            default:
                do_sql('
                    UPDATE 
                        `cashmaster`.`Machines`
                    SET
                        `MachineConnectionOn` = 0
                    WHERE 
                        `MachineDBId` = "'.addslashes($sorter['MachineDBId']).'"
                ;');
                break;
        };
        // Если запущено из браузера, то отображаем данные
        if (!isset($argv)) {
            $time_end = microtime(true);
            echo '<pre>';
            echo 'Machine data processing time: ';
            echo number_format(($time_end - $time_start),6),' second';
            echo "\n=================================================";
            echo '</pre>';
        };    
    };
    
?>
