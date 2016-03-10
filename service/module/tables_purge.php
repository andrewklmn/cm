<?php

/*
 * Модуль очистки таблиц по устарешвшей дате.
 */

    // Модуль можно запускать только для службы backup
    //if (!isset($c) OR $c!='backup') exit;
    
    // ============ Блокируем таблицы на запись перед очисткой =================
    do_sql('
        LOCK TABLES 
            UserConfiguration WRITE, 
            DepositRuns WRITE, 
            DepositRecs WRITE, 
            SorterAccountingData WRITE, 
            SorterRejectData WRITE, 
            ReportSets WRITE, 
            DepositCurrencyTotal WRITE, 
            DepositDenomTotal WRITE, 
            SuspectSerialNumbs WRITE, 
            ReconAccountingData WRITE, 
            Acts WRITE, 
            Reports WRITE, 
            ReportSaves WRITE, 
            SystemLog WRITE,
            SystemGlobals WRITE
    ;');
    
    // 1. Определяем дату после которой нужно всё вычистить.
    // =========================================================================
    $system_globals = fetch_assoc_row_from_sql('
        SELECT
            *
        FROM 
            `cashmaster`.`SystemGlobals`
        WHERE   
            `SystemGlobals`.`SystemGlobalsId`="1"
    ;');
    
    // Если включен сервисный флаг, то чистим таблицы
    if ($system_globals['ServiceMode']=="1") {
        
        // Определяем дату запуска бекапа из второго параметра
        $now_date = $argv[1];
        
        // Определяем дату репортсета закрытого по которому будем делаем очистку таблиц
        $cleanup_date = date( 'Y-m-d H:i:s', strtotime($now_date) - 86400 * $system_globals['LeaveDataFor']);
        $row = fetch_row_from_sql('
            SELECT
                MAX(`ReportSets`.`SetDateTime`)
            FROM 
                `ReportSets`
            WHERE
                `ReportSets`.`SetDateTime` <= "'.$cleanup_date.'"
                AND `ReportSets`.`SetDateTime`<>"0000-00-00 00:00:00"
                AND `ReportSets`.`SetDateTime` is not null
        ;');
        $cleanup_date = $row[0];
        
        if ($cleanup_date!='' AND $cleanup_date!='0000-00-00 00:00:00') {
            
            // Определяем депозитраны, которые надо удалить
            $rows = get_array_from_sql('
                SELECT DISTINCT
                    DepositRuns.DepositRunId
                FROM
                    DepositRuns
                LEFT JOIN
                    DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
                WHERE
                    DepositRecs.RecLastChangeDatetime <= "'.addslashes($cleanup_date).'"
                    AND DepositRecs.ReconcileStatus = 1
            ;');
            $runs = array();
            foreach ($rows as $value) {
                $runs[] = $value[0];
            };

//            echo '<pre>';
//            print_r($runs);
//            echo '</pre>';
            
            // Если есть депозитраны в этом периоде то чистим всё что к ним относится
            if (count($runs)>0) { 
                // Удаляем данные сортера
                do_sql('
                    DELETE FROM 
                        `SorterAccountingData`
                    WHERE
                        DepositRunId IN ('.  implode(',', $runs).')
                ;');

                // Удаляем данные данные ручного ввода
                do_sql('
                    DELETE FROM 
                        `SorterRejectData`
                    WHERE
                        DepositRunId IN ('.  implode(',', $runs).')
                ;');

                // Удаляем депозит раны которые уже не нужны
                do_sql('
                    DELETE FROM 
                        `DepositRuns`
                    WHERE
                        DepositRunId IN ('.  implode(',', $runs).')
                ;');             
            };

        
            // Получаем все депозитреки, которые использованы
            $rows = get_array_from_sql('
                SELECT DISTINCT
                    DepositRecs.DepositRecId
                FROM
                    DepositRecs
                WHERE
                    DepositRecs.RecLastChangeDatetime <= "'.addslashes($cleanup_date).'"
                    AND DepositRecs.ReconcileStatus = 1
            ;');
            $recs = array();
            foreach ($rows as $value) {
                $recs[] = $value[0];
            };
            
//            echo '<pre>';
//            print_r($recs);
//            echo '</pre>';
            
            // Если есть депозитреки в этом периоде то чистим всё что к ним относится
            if (count($recs)>0) { 
                do_sql('
                    DELETE FROM 
                        `DepositCurrencyTotal`
                    WHERE
                        DepositRecId IN ('.  implode(',', $recs).')
                ;');
                
                do_sql('
                    DELETE FROM 
                        `DepositDenomTotal`
                    WHERE
                        DepositReclId IN ('.  implode(',', $recs).')
                ;');
                
                do_sql('
                    DELETE FROM 
                        `SuspectSerialNumbs`
                    WHERE
                        DepositRecId IN ('.  implode(',', $recs).')
                ;');
                
                do_sql('
                    DELETE FROM 
                        `ReconAccountingData`
                    WHERE
                        DepositRecId IN ('.  implode(',', $recs).')
                ;');

                do_sql('
                    DELETE FROM 
                        `Acts`
                    WHERE
                        DepositId IN ('.  implode(',', $recs).')
                ;');
                do_sql('
                    DELETE FROM 
                        `DepositRecs`
                    WHERE
                        DepositRecId IN ('.  implode(',', $recs).')
                ;');
            };
            
            // Находим все репортсеты удовлетворящие дате бекапа
            $rows = get_array_from_sql('
                SELECT
                    `ReportSets`.`SetId`
                FROM 
                    `ReportSets`
                WHERE
                    `ReportSets`.`SetDateTime` <= "'.$cleanup_date.'"
                    AND `ReportSets`.`SetDateTime`<>"0000-00-00 00:00:00"
                    AND `ReportSets`.`SetDateTime` is not null
            ;');
            
            $reports = array();
            foreach ($rows as $value) {
                $reports[] = $value[0];
            };
            
//            echo '<pre>';
//            print_r($reports);
//            echo '</pre>';
            
            if (count($reports)>0) {
                // Чистим всё что касается репортсетов
                do_sql('
                    DELETE FROM 
                        `Reports`
                    WHERE
                        ReportSetId IN ('.  implode(',', $reports).')
                ;');   
                         
                do_sql('
                    DELETE FROM 
                        `ReportSaves`
                    WHERE
                        ReportSetId IN ('.  implode(',', $reports).')
                ;'); 
                  
                do_sql('
                    DELETE FROM 
                        `ReportSets`
                    WHERE
                        SetId IN ('.  implode(',', $reports).')
                ;');  
            };
            
            // Чистим системный лог от устаревших сообщений
            /*
            do_sql('
                DELETE FROM 
                    `SystemLog`
                WHERE
                    DateAndTime <= "'.$cleanup_date.'"
            ;');
             * 
             */
        };
    };
    
    // Снимаем режим коррупции файл
    do_sql('
        UPDATE 
            `SystemGlobals`
        SET
            `FilesCorrupted` = "0"
        WHERE 
            SystemGlobalsId = "1"
    ;');
    
    
    do_sql('UNLOCK TABLES;');
    
    
?>
