<?php

/*
 * Simulator DepositRuns collector
 */

        if (!isset($c)) exit;
        
        $files_were_processed = 0;
        $files_were_skiped = 0;
        $new_deposits_were_added = 0;
        $new_sorter_accounting_data_were_added = 0;
        $new_category_names_were_added = 0;
        
        $list = scandir($value['MachineConnectionDirectory']);
        foreach ($list as $k=>$v) {
            if($v!='.' AND $v!='..') {
                //$xml = file_get_contents($value['MachineConnectionDirectory'].'/'.$v);
                //$root = new SimpleXMLElement($xml);
                
                $files_were_processed++;
                
                $root = simplexml_load_file($value['MachineConnectionDirectory'].'/'.$v);
                if (!$root) {
                    $target_name = '/var/www/html/cashmaster/error'.'/'.$v;
                    // Файл не соответствует стандарту, перекладываем его в Эррор
                    while (file_exists($target_name)) {
                        $d = explode('.', $target_name);
                        $target_name = $d[0].'1.'.$d[1];
                    };
                    rename(
                            $value['MachineConnectionDirectory'].'/'.$v, 
                            $target_name 
                    );
                    continue;
                };
                
                
                foreach ( $root->deposit as $kk=>$vv) {
                    
                    // Проверяем есть ли такая запись в депозитранс?
                    do_sql('LOCK TABLES DepositRuns WRITE;');
                    $sql = '
                        SELECT
                               count(*)
                        FROM
                               DepositRuns
                        WHERE  
                               DepositStartTimeStamp="'.addslashes($root->deposit->DepositStartTimeStamp).'"
                               AND DepositEndTimeStamp="'.addslashes($root->deposit->DepositEndTimeStamp).'"
                               AND DepositRuns.DataSortCardNumber="'.addslashes($root->deposit->DataSortCardNumber).'"
                               AND OperatorName="'.addslashes($root->deposit->OperatorName).'"
                               AND SupervisorName="'.addslashes($root->deposit->SupervisorName).'"
                               AND IndexName="'.addslashes($root->deposit->IndexName).'"
                               AND SortModeName="'.addslashes($root->deposit->SortModeName).'"
                    ;';
                    $row = fetch_row_from_sql($sql);
                    if ($row[0] == 0) {
                        // Добавляем эту запись запись в DepositRuns
                        $sql = '
                            INSERT INTO DepositRuns 
                                   (MachineDBID, 
                                    ShiftId, 
                                    BatchId, 
                                    DepositInBatchId, 
                                    DepositStartTimeStamp,
                                    DepositEndTimeStamp, 
                                    DataSortCardNumber, 
                                    OperatorName, 
                                    SupervisorName, 
                                    IndexName, 
                                    SortModeName)
                            VALUES 
                                   ('.addslashes($value['MachineDBId']).', 
                                    '.addslashes($root->deposit->ShiftId).', 
                                    '.addslashes($root->deposit->BatchId).', 
                                    '.addslashes($root->deposit->DepositInBatchId).',
                                    "'.addslashes($root->deposit->DepositStartTimeStamp).'",
                                    "'.addslashes($root->deposit->DepositEndTimeStamp).'",
                                    "'.addslashes($root->deposit->DataSortCardNumber).'",
                                    "'.addslashes($root->deposit->OperatorName).'",
                                    "'.addslashes($root->deposit->SupervisorName).'",
                                    "'.addslashes($root->deposit->IndexName).'",
                                    "'.addslashes($root->deposit->SortModeName).'")
                        ;';
                        do_sql($sql);
                        $new_deposits_were_added++;
                    } else {
                        $files_were_skiped++;
                    };
                    $sql = '
                        SELECT
                               DepositRunId
                        FROM
                               DepositRuns
                        WHERE  
                               DepositStartTimeStamp="'.addslashes($root->deposit->DepositStartTimeStamp).'"
                               AND DepositEndTimeStamp="'.addslashes($root->deposit->DepositEndTimeStamp).'"
                               AND DepositRuns.DataSortCardNumber="'.addslashes($root->deposit->DataSortCardNumber).'"
                               AND OperatorName="'.addslashes($root->deposit->OperatorName).'"
                               AND SupervisorName="'.addslashes($root->deposit->SupervisorName).'"
                               AND IndexName="'.addslashes($root->deposit->IndexName).'"
                               AND SortModeName="'.addslashes($root->deposit->SortModeName).'"
                    ;';
                    $row = fetch_row_from_sql($sql);
                    $DepositRunId = $row[0];
                    do_sql('UNLOCK TABLES;');
                    
                    // Обрабатываем данные пересчета из файла XML                    
                    do_sql('LOCK TABLES Valuables WRITE, SorterAccountingData WRITE, DepositRuns WRITE;');

                    foreach ($vv->SorterAccountingData as $kkk => $vvv) {
                        $sql = '
                            SELECT
                                   count(*)
                            FROM
                                   SorterAccountingData
                            LEFT JOIN 
                                   DepositRuns ON DepositRuns.DepositRunId=SorterAccountingData.DepositRunId
                            LEFT JOIN
                                   Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
                            WHERE
                                    DepositRuns.DepositStartTimeStamp="'.addslashes($root->deposit->DepositStartTimeStamp).'"
                                    AND DepositRuns.DepositEndTimeStamp="'.addslashes($root->deposit->DepositEndTimeStamp).'"
                                    AND DepositRuns.DataSortCardNumber="'.addslashes($root->deposit->DataSortCardNumber).'"
                                    AND Valuables.CategoryName="'.addslashes($vvv->CategoryName).'"
                                    AND SorterAccountingData.ActualCount="'.addslashes($vvv->ActualCount).'"
                        ;';
                        $row = fetch_row_from_sql($sql);
                        if ($row[0]==0) {
                            //Проверяем есть ли такой CategoryName у нас в Valuables
                            $sql ='
                                 SELECT
                                        count(*)
                                 FROM
                                        Valuables
                                 WHERE
                                        CategoryName="'.addslashes($vvv->CategoryName).'"
                                        AND SorterTypeId="'.addslashes($_SESSION[$program]['simulated_sorter']).'"
                            ;';
                            $row = fetch_row_from_sql($sql);
                            if ($row[0]==0) {
                                // Если нет, то
                                // добавляем эту новую категорию ===============
                                $sql = '
                                    INSERT INTO Valuables 
                                        ( SorterTypeId, CategoryName, DenomId, ValuableTypeId ) 
                                    VALUES ( "'.$_SESSION[$program]['simulated_sorter'].'","'.addslashes($vvv->CategoryName).'","0","0" ) 
                                ;';
                                do_sql($sql);
                                $new_category_names_were_added++;
                            };
                            // Получаем ValuableId =============================
                            $sql ='
                                 SELECT
                                        ValuableId
                                        
                                 FROM
                                        Valuables
                                 WHERE
                                        CategoryName="'.addslashes($vvv->CategoryName).'"
                                        AND SorterTypeId="'.$_SESSION[$program]['simulated_sorter'].'"
                            ;';
                            $row = fetch_row_from_sql($sql);
                            $ValuableId = $row[0];
                            
                            
                            // добавляем эту запись ============================
                            $sql = '
                                 INSERT INTO SorterAccountingData 
                                    (DepositRunId,ValuableId,ActualCount) 
                                VALUES 
                                    ("'.$DepositRunId.'", "'.$ValuableId.'", "'.addslashes($vvv->ActualCount).'") 
                            ;';
                            do_sql($sql);
                            $new_sorter_accounting_data_were_added++;
                        };
                    };
                    do_sql('UNLOCK TABLES;');
                };
                // Добавление окончено - удаляем файл с пересчетом
                //if (file_exists($value['MachineConnectionDirectory'].'/'.$v)) 
                //        unlink($value['MachineConnectionDirectory'].'/'.$v) ;
            };
        };
        
?>
