<?php

/*
 * Сбор данных с Кобры
 */
    if (!isset($c)) exit;
    
    $query = '
        SELECT
            BATCH.DBID,
            Process.Description,
            BatchIdentifier,
            BatchSeparatorId,
            BatchStartTimestamp,
            BatchEndTimestamp,
            SorterOperatorName,
            SorterShiftName
        FROM dbo.BATCH
            INNER JOIN dbo.PROCESS ON BATCH.ProcessDBID=PROCESS.DBID
            INNER Join dbo.MACHINE ON BATCH.MachineDBID=MACHINE.DBID
        WHERE 
            IsReconciled = 0
            AND SourceKind = 0
            AND MACHINE.Name = "'.$sorter['SorterName'].'"
    ;';
    $result = mssql_query($query);
    while($row = mssql_fetch_array($result)) {
    
	$t = date_parse($row['BatchStartTimestamp']);
        $start_date_time = $t['year'].'-';
        $start_date_time.=($t['month']<10)?'0'.$t['month'].'-':$t['month'].'-';
        $start_date_time.=($t['day']<10)?'0'.$t['day'].' ':$t['day'].' ';
        $start_date_time.=($t['hour']<10)?'0'.$t['hour'].':':$t['hour'].':';
        $start_date_time.=($t['minute']<10)?'0'.$t['minute'].':':$t['minute'].':';
        $start_date_time.=($t['second']<10)?'0'.$t['second']:$t['second'];

        $t = date_parse($row['BatchEndTimestamp']);
        $end_date_time = $t['year'].'-';
        $end_date_time.=($t['month']<10)?'0'.$t['month'].'-':$t['month'].'-';
        $end_date_time.=($t['day']<10)?'0'.$t['day'].' ':$t['day'].' ';
        $end_date_time.=($t['hour']<10)?'0'.$t['hour'].':':$t['hour'].':';
        $end_date_time.=($t['minute']<10)?'0'.$t['minute'].':':$t['minute'].':';
        $end_date_time.=($t['second']<10)?'0'.$t['second']:$t['second'];
        
        // Создаем депозитран 
        $sql='
            INSERT INTO `cashmaster`.`DepositRuns`
                (
                    `DataSortCardNumber`,
                    `MachineDBId`,
                    `ShiftId`,
                    `BatchId`,
                    `DepositInBatchId`,
                    `DepositStartTimeStamp`,
                    `DepositEndTimeStamp`,
                    `OperatorName`,
                    `SupervisorName`,
                    `IndexName`,
                    `SortModeName`
                )
            VALUES
                (
                    "'.addslashes($row['BatchSeparatorId']).'",
                    "'.addslashes($sorter['MachineDBId']).'",
                    1,
                    1,
                    "'.addslashes($row['BatchIdentifier']).'",
                    "'.$start_date_time.'",
                    "'.$end_date_time.'",
                    "'.addslashes($row['SorterOperatorName']).'",
                    "",
                    "'.addslashes($row['SorterShiftName']).'",
                    "'.addslashes($row['Description']).'"
                );

    
        ;';
        do_sql(iconv('cp1251','UTF-8',$sql));
        // получаем номер вновьдобавленного депозитрана
        
        $t = fetch_row_from_sql('
            SELECT
                MAX(`DepositRuns`.`DepositRunId`)
            FROM 
                `cashmaster`.`DepositRuns`    
        ;');
        $last_run_id = $t[0];
        
        // Получаем сортер эккаунтинг дата.
        $query = '
            SELECT
                CategoryName,
                ActualCount,
                CullCount
            FROM 
                dbo.BATCHCOUNT
            INNER JOIN 
                dbo.CATEGORYMAP ON CATEGORYMAP.DBID = BATCHCOUNT.CategoryMapDBID
            WHERE 
                BATCHCOUNT.BatchDBID = "'.$row['DBID'].'"
        ';
        
        // Если запущено из браузера, то отображаем данные
        if (!isset($argv)) {
            echo '<pre>';
            print_r($row);
            echo '</pre>';
        };
        
        $result2 = mssql_query($query);
        while($r = mssql_fetch_array($result2)) {
            
            // Если запущено из браузера, то отображаем данные
            if (!isset($argv)) {
                echo '<pre>';
                print_r($r);
                echo '</pre>';
            };
            
            
            if ($r['ActualCount']>0) {
                //Проверяем есть ли такой CategoryName у нас в Valuables
                $sql ='
                     SELECT
                            count(*)
                     FROM
                            Valuables
                     WHERE
                            CategoryName="'.addslashes($r['CategoryName']).'"
                            AND SorterTypeId="'.addslashes($sorter['SorterTypeId']).'"
                ;';
                $t = fetch_row_from_sql($sql);
                if ($t[0]==0) {
                    // Если нет, то
                    // добавляем эту новую категорию ===============
                    $sql = '
                        INSERT INTO Valuables 
                            ( SorterTypeId, CategoryName, DenomId, ValuableTypeId ) 
                        VALUES ( "'.addslashes($sorter['SorterTypeId']).'","'.addslashes($r['CategoryName']).'","0","0" ) 
                    ;';
                    do_sql($sql);
                };
                // Получаем ID ценности
                $sql = '
                     SELECT
                            ValuableId
                     FROM
                            Valuables
                     WHERE
                            CategoryName="'.addslashes($r['CategoryName']).'"
                            AND SorterTypeId="'.addslashes($sorter['SorterTypeId']).'"                
                ;';
                $t = fetch_row_from_sql($sql);
                $valuable_id = $t[0];
                // Добавляем запись в SorterAccountingData
                $sql = '
                     INSERT INTO SorterAccountingData 
                        (DepositRunId,ValuableId,ActualCount) 
                    VALUES 
                        ("'.$last_run_id.'", "'.$valuable_id.'", "'.$r['ActualCount'].'") 
                ;';
                do_sql($sql);
            };
            
            // Если были возвраты, то заполняем таблицы Возвратов
            if ($r['CullCount']>0) {
            
                //Проверяем есть ли такой CategoryName у нас в Rejects
                $sql ='
                     SELECT
                            count(*)
                     FROM
                            Rejects
                     WHERE
                            RejectName="'.addslashes($r['CategoryName']).'"
                            AND SorterTypeId="'.addslashes($sorter['SorterTypeId']).'"
                ;';
                $t = fetch_row_from_sql($sql);
                if ($t[0]==0) {
                    // Если нет, то
                    // добавляем эту новую категорию ===============
                    do_sql('
                    INSERT INTO `cashmaster`.`Rejects`
                        (
                            `RejectName`,
                            `SorterTypeId`,
                            `RejectMappingId`,
                            `RejectDescription`
                        )
                    VALUES
                        (
                            "'.addslashes($r['CategoryName']).'",
                            '.addslashes($sorter['SorterTypeId']).',
                            0,
                            ""
                        )
                    ;');
                };
                // Получаем ID нужного Reject
                $sql ='
                     SELECT
                            RejectId
                     FROM
                            Rejects
                     WHERE
                            RejectName="'.addslashes($r['CategoryName']).'"
                            AND SorterTypeId="'.addslashes($sorter['SorterTypeId']).'"
                ;';
                $t = fetch_row_from_sql($sql);
                $reject_id = $t[0];
                
                // Добавляем запись в SorterRejectData
                $sql = '
                     INSERT INTO `cashmaster`.`SorterRejectData`
                        (
                            `DepositRunId`,
                            `RejectId`,
                            `CullCount`
                        )
                    VALUES
                        (
                            "'.addslashes($last_run_id).'",
                            "'.addslashes($reject_id).'",
                            "'.addslashes($r['CullCount']).'"
                        ); 
                ;';
                do_sql($sql);
            };
            // Обновляем поля на CobraPC информируя её о том, что данные считаны
            $query = '
                UPDATE
                    dbo.BATCH
                SET
                    SourceKind = 1,
                    IsReconciled = 1
                WHERE
                    DBID = "'.$row['DBID'].'"
            ;';
            mssql_query($query);
        };    
    };
    
?>
