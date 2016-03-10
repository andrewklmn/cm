<?php

/*
 * Reconciliation total data updater
 */

        if (!isset($c)) exit;

        include './app/view/html_header.php';        
        
        //проверяем входные параметры
        if(!(isset($_REQUEST['deposit_rec_id']) AND $_REQUEST['deposit_rec_id']>0)
                OR !(isset($_REQUEST['denom_id']) AND $_REQUEST['denom_id']>0)
                OR !isset($_REQUEST['value'])
                OR !isset($_REQUEST['oldvalue'])
                OR !isset($_REQUEST['last_update_time'])
                ) {
            echo 'Wrong update data';
            exit;
        };
        
        // Блокируем таблицу
        do_sql('LOCK TABLES 
                    DepositCurrencyTotal WRITE, 
                    DepositDenomTotal WRITE,
                    DepositRecs WRITE
        ;');
        
        include './app/controller/common/reconciliation/check_recon_status.php';
        
        $update_date = date("Y-m-d H:i:s", time());
        
        // Проверяем есть ли данные для такого Деном
        $row = fetch_row_from_sql('
            SELECT
                count(*)
            FROM 
                `cashmaster`.`DepositDenomTotal`
            WHERE
                `DepositDenomTotal`.`DepositReclId` = "'.addslashes($_REQUEST['deposit_rec_id']).'"
                AND `DepositDenomTotal`.`DenomId` = "'.addslashes($_REQUEST['denom_id']).'"
        ;');
        if ($row[0]>0) {
            //Получаем ключ существующей записи
            $row = fetch_row_from_sql('
                SELECT
                    `DepositDenomTotal`.`id`,
                    `DepositDenomTotal`.`DepositReclId`,
                    `DepositDenomTotal`.`DenomId`,
                    `DepositDenomTotal`.`ExpectedCount`,
                    `DepositRecs`.`RecLastChangeDatetime`
                FROM 
                    `cashmaster`.`DepositDenomTotal`
                LEFT JOIN
                    DepositRecs ON DepositRecs.DepositRecId = `DepositDenomTotal`.`DepositReclId`
                WHERE
                    `DepositDenomTotal`.`DepositReclId` = "'.addslashes($_REQUEST['deposit_rec_id']).'"
                    AND `DepositDenomTotal`.`DenomId` = "'.addslashes($_REQUEST['denom_id']).'"
            ;');
            //print_r($row);
            // Проверяем старое значение
            if ((int)$row[3]==(int)$_REQUEST['oldvalue']
                    AND $row[4]==$_REQUEST['last_update_time']) {
                // Можно обновлять 
                do_sql('
                    UPDATE `cashmaster`.`DepositDenomTotal`
                    SET
                        `ExpectedCount` = "'.addslashes($_REQUEST['value']).'",
                        `ValuableTypeId` = "2"
                    WHERE  `DepositDenomTotal`.`id`= "'.addslashes($row[0]).'"
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
                echo 0,$row[0];   // is OK
                
            } else {
                // Нельзя обновлять, возвращаем новое значение
                echo '1';
                echo (int)$row[3],'|',$row[4];
            };
            
        } else {
            // записи нет, можно создать новую
            do_sql('
                INSERT INTO `cashmaster`.`DepositDenomTotal`
                    (
                    `DepositReclId`,
                    `DenomId`,
                    `ExpectedCount`,
                    `ValuableTypeId`
                    )
                VALUES
                    (
                        "'.addslashes($_REQUEST['deposit_rec_id']).'",
                        "'.addslashes($_REQUEST['denom_id']).'",
                        "'.addslashes($_REQUEST['value']).'",
                        "2"
                    );

            ;');
                $row = fetch_row_from_sql('
                    SELECT
                        RecLastChangeDatetime
                    FROM
                        DepositRecs
                    WHERE
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');
                echo 0,$row[0];   // is OK
        };
        
        
        // Снимаем блокировку с таблиц 
        do_sql('UNLOCK TABLES;');

        
?>
