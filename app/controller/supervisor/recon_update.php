<?php

/*
 * Reconciliation data update
 */

        if (!isset($c)) exit;

        include './app/view/html_header.php';
        
        //проверяем входные параметры
        if(!(isset($_REQUEST['deposit_rec_id']) AND $_REQUEST['deposit_rec_id']>0)
                OR !(isset($_REQUEST['denom_id']) AND $_REQUEST['denom_id']>0)
                OR !(isset($_REQUEST['grade_id']) AND $_REQUEST['grade_id']>0)
                OR !isset($_REQUEST['cull_count'])
                OR !isset($_REQUEST['oldvalue'])
                OR !isset($_REQUEST['last_update_time'])
                OR !isset($_REQUEST['rec_data_map'])
            ) {
            echo 'Wrong update data';
            exit;
        };
        
        
        // Блокируем таблицу
        do_sql('LOCK TABLES DepositCurrencyTotal WRITE,SorterIndexes WRITE,DepositRuns WRITE, ReconAccountingData WRITE, DepositRecs WRITE, DepositDenomTotal WRITE;');

        include './app/controller/common/reconciliation/check_recon_status.php';
        include './app/controller/common/reconciliation/check_recon_indexes.php';
        include './app/controller/common/reconciliation/check_recon_accounting_data.php';
        include './app/controller/common/reconciliation/check_sorter_accounting_data.php';
    
        $update_date = date("Y-m-d H:i:s", time());

        
        // Проверяем есть ли данные для таких Деном и Грейд
        $row = fetch_row_from_sql('
            SELECT
                count(*)
            FROM `cashmaster`.`ReconAccountingData`
            WHERE
                `ReconAccountingData`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                AND `ReconAccountingData`.`DenomId`="'.addslashes($_REQUEST['denom_id']).'"
                AND `ReconAccountingData`.`GradeId`="'.addslashes($_REQUEST['grade_id']).'"
        ;');
        if ($row[0]>0) {
            //Получаем ключ существующей записи
            $row = fetch_row_from_sql('
                SELECT
                    `id`,
                    ReconAccountingData.DepositRecId,
                    `DenomId`,
                    `GradeId`,
                    `CullCount`,
                    `DepositRecs`.`RecLastChangeDatetime`
                FROM `cashmaster`.`ReconAccountingData`
                LEFT JOIN
                    DepositRecs ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
                WHERE
                    `ReconAccountingData`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                    AND `ReconAccountingData`.`DenomId`="'.addslashes($_REQUEST['denom_id']).'"
                    AND `ReconAccountingData`.`GradeId`="'.addslashes($_REQUEST['grade_id']).'"
            ;');
            // Проверяем старое значение
            if ((int)$row[4]==(int)$_REQUEST['oldvalue'] 
                    AND $row[5]==$_REQUEST['last_update_time']) {
                // Можно обновлять 
                do_sql('
                    UPDATE `cashmaster`.`ReconAccountingData`
                    SET
                        `CullCount` = "'.addslashes($_REQUEST['cull_count']).'",
                        `ValuableTypeId` = "2"
                    WHERE `id` = "'.$row[0].'"
                ;');
                if ($_SESSION[$program]['user_role_id']!=2) {
                    do_sql('
                        UPDATE `cashmaster`.`DepositRecs`
                        SET
                            `ScenarioId` = "'.$_SESSION[$program]['scenario'][0].'",
                            `RecLastChangeDatetime` = "'.$update_date.'",
                            `RecSupervisorId` = "'.$_SESSION[$program]['user_id'].'"
                        WHERE 
                            `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                    ;');   
                } else {
                    do_sql('
                        UPDATE `cashmaster`.`DepositRecs`
                        SET
                            `ScenarioId` = "'.$_SESSION[$program]['scenario'][0].'",
                            `RecLastChangeDatetime` = "'.$update_date.'"
                        WHERE 
                            `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                    ;');                    
                };             
                $row = fetch_row_from_sql('
                    SELECT
                        RecLastChangeDatetime
                    FROM
                        DepositRecs
                    WHERE
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');
                echo 0,$row[0],'|',get_recon_data_map_by_rec_id($_REQUEST['deposit_rec_id']);   // is OK
                
            } else {
                // Нельзя обновлять, возвращаем новое значение
                echo '1';
                echo (int)$row[4],'|',$row[5],'|',get_recon_data_map_by_rec_id($_REQUEST['deposit_rec_id']);
                do_sql('UNLOCK TABLES;');
                exit;
            };
            
        } else {
            // записи нет, можно создать новую
            do_sql('
                INSERT INTO `cashmaster`.`ReconAccountingData`
                    (
                        `DepositRecId`,
                        `DenomId`,
                        `GradeId`,
                        `CullCount`,
                        `ValuableTypeId`
                    )
                VALUES
                    (
                        "'.addslashes($_REQUEST['deposit_rec_id']).'",
                        "'.addslashes($_REQUEST['denom_id']).'",
                        "'.addslashes($_REQUEST['grade_id']).'",
                        "'.addslashes($_REQUEST['cull_count']).'",
                        "2"
                    )

            ;');
            if ($_SESSION[$program]['user_role_id']!=2) {
                do_sql('
                    UPDATE `cashmaster`.`DepositRecs`
                    SET
                         `ScenarioId` = "'.$_SESSION[$program]['scenario'][0].'",
                        `RecLastChangeDatetime` = "'.$update_date.'",
                        `RecOperatorId` = "'.$_SESSION[$program]['user_id'].'"
                    WHERE 
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');                
            } else {
                do_sql('
                    UPDATE `cashmaster`.`DepositRecs`
                    SET
                         `ScenarioId` = "'.$_SESSION[$program]['scenario'][0].'",
                        `RecLastChangeDatetime` = "'.$update_date.'",
                        `RecSupervisorId` = "'.$_SESSION[$program]['user_id'].'"
                    WHERE 
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');
            };
            $row = fetch_row_from_sql('
                SELECT
                    RecLastChangeDatetime
                FROM
                    DepositRecs
                WHERE
                    `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
            ;');
            echo 0,$row[0],'|',get_recon_data_map_by_rec_id($_REQUEST['deposit_rec_id']);   // is OK
        };
        
        
        // Снимаем блокировку с таблиц 
        do_sql('UNLOCK TABLES;');

        
?>
