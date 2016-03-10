<?php

/*
 * Reconciliation data update
 */

        if (!isset($c)) exit;

        
        include './app/view/html_header.php';
        
                //проверяем входные параметры
        if(!(isset($_REQUEST['deposit_rec_id']) AND $_REQUEST['deposit_rec_id']>0)
                OR !(isset($_REQUEST['currency_id']) AND $_REQUEST['currency_id']>0)
                OR !isset($_REQUEST['expected'])
                OR !isset($_REQUEST['oldvalue'])
                OR !isset($_REQUEST['last_update_time'])
            ) {
            echo 'Wrong update data';
            exit;
        };
        
        
        // Блокируем таблицу
        do_sql('LOCK TABLES DepositCurrencyTotal WRITE,SorterIndexes WRITE,DepositRuns WRITE, ReconAccountingData WRITE, DepositRecs WRITE, DepositDenomTotal WRITE;');

        //include './app/controller/common/reconciliation/check_recon_status.php';
        //include './app/controller/common/reconciliation/check_recon_indexes.php';
        //include './app/controller/common/reconciliation/check_recon_accounting_data.php';
        //include './app/controller/common/reconciliation/check_sorter_accounting_data.php';
    
        $update_date = date("Y-m-d H:i:s", time());
        
        // Проверяем есть ли данные для таких rec и currency
        $row = fetch_row_from_sql('
            SELECT
                `DepositCurrencyTotal`.`ExpectedDepositValue`,
                `DepositRecs`.`RecLastChangeDatetime`
            FROM 
                `cashmaster`.`DepositCurrencyTotal`
            LEFT JOIN
                DepositRecs ON DepositRecs.DepositRecId = DepositCurrencyTotal.DepositRecId
            WHERE
                `DepositCurrencyTotal`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                AND `DepositCurrencyTotal`.`CurrencyId`="'.addslashes($_REQUEST['currency_id']).'"
        ;');
        
        
        if (isset($row[0])) {
            
            // Проверяем старое значение
            if ((int)$row[0]==(int)$_REQUEST['oldvalue'] 
                    AND $row[1]==$_REQUEST['last_update_time']) {
                // Можно обновлять 
                do_sql('
                    UPDATE 
                        `cashmaster`.`DepositCurrencyTotal`
                    SET
                        `ExpectedDepositValue` = "'.addslashes($_REQUEST['expected']).'"
                    WHERE
                        `DepositCurrencyTotal`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                        AND `DepositCurrencyTotal`.`CurrencyId`="'.addslashes($_REQUEST['currency_id']).'"                  
                ;');
                
                if ($_SESSION[$program]['user_role_id']!=2) {
                    do_sql('
                        UPDATE `cashmaster`.`DepositRecs`
                        SET
                            `ScenarioId` = "'.$_SESSION[$program]['scenario'][0].'",
                            `RecLastChangeDatetime` = "'.$update_date.'"
                            #`RecSupervisorId` = "'.$_SESSION[$program]['user_id'].'"
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
                echo (int)$row[0],'|',$row[1];
                do_sql('UNLOCK TABLES;');
                exit;
            };
            
        } else {
            // записи нет, можно создать новую
            do_sql('
                INSERT INTO `cashmaster`.`DepositCurrencyTotal`
                    (
                        `DepositRecId`,
                        `CurrencyId`,
                        `ExpectedDepositValue`,
                        `ValuableTypeId`
                    )
                VALUES
                    (
                        "'.addslashes($_REQUEST['deposit_rec_id']).'",
                        "'.addslashes($_REQUEST['currency_id']).'",
                        "'.addslashes($_REQUEST['expected']).'",
                        "2"
                    )

            ;');
            if ($_SESSION[$program]['user_role_id']!=2) {
                do_sql('
                    UPDATE `cashmaster`.`DepositRecs`
                    SET
                         `ScenarioId` = "'.$_SESSION[$program]['scenario'][0].'",
                        `RecLastChangeDatetime` = CURRENT_TIMESTAMP
                        #`RecOperatorId` = "'.$_SESSION[$program]['user_id'].'"
                    WHERE 
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');                
            } else {
                do_sql('
                    UPDATE `cashmaster`.`DepositRecs`
                    SET
                         `ScenarioId` = "'.$_SESSION[$program]['scenario'][0].'",
                        `RecLastChangeDatetime` = CURRENT_TIMESTAMP
                        #`RecSupervisorId` = "'.$_SESSION[$program]['user_id'].'"
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
        };
        
        
        // Снимаем блокировку с таблиц 
        do_sql('UNLOCK TABLES;');

        
?>
