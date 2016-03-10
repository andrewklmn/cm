<?php

/*
 * Reconciliation data update
 */

        if (!isset($c)) exit;

        include './app/view/html_header.php';
        
        
        //проверяем входные параметры
        if(!(isset($_REQUEST['deposit_rec_id']) AND $_REQUEST['deposit_rec_id']>0)
                OR !(isset($_REQUEST['discr_type']) AND $_REQUEST['discr_type']>0)
                OR !isset($_REQUEST['discr_comment'])
                OR !isset($_REQUEST['discr_oldvalue'])
                OR !isset($_REQUEST['last_update_time'])
                OR !isset($_REQUEST['rec_data_map'])
            ) {
            echo 'Wrong update data';
            exit;
        };
        
        
        // Блокируем таблицу
        do_sql('LOCK TABLES DepositCurrencyTotal WRITE,SorterIndexes WRITE, DepositRuns WRITE,Acts WRITE, ReconAccountingData WRITE, DepositRecs WRITE, DepositDenomTotal WRITE;');

        include './app/controller/common/reconciliation/check_recon_status.php';
        include './app/controller/common/reconciliation/check_recon_indexes.php';
        include './app/controller/common/reconciliation/check_recon_accounting_data.php';
        include './app/controller/common/reconciliation/check_sorter_accounting_data.php';
        
        $update_date = date("Y-m-d H:i:s", time());
        
        // Проверяем есть ли акт для таких сверки
        $row = fetch_row_from_sql('
            SELECT
                `Acts`.`DiscrepancyDescr`
            FROM 
                `cashmaster`.`Acts`
            WHERE
                `Acts`.`DiscrepancyKindId` = "'.addslashes($_REQUEST['discr_type']).'"
                AND `Acts`.`DepositId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
        ;');
        
        if(isset($row[0])) {
            // Акт есть 
            if ($row[0]==$_REQUEST['discr_oldvalue']) {
                // старое значение не изменилось, значит можно добавлять
                do_sql('
                    UPDATE `cashmaster`.`Acts`
                    SET
                        `DiscrepancyDescr` = "'.addslashes($_REQUEST['discr_comment']).'"
                    WHERE 
                        `DepositId` = "'.addslashes($_REQUEST['deposit_rec_id']).'"
                        AND `DiscrepancyKindId` = "'.addslashes($_REQUEST['discr_type']).'"
                ;');
                // обновляем дату изменения сверки
                do_sql('
                    UPDATE `cashmaster`.`DepositRecs`
                    SET
                        `ScenarioId` = "'.$_SESSION[$program]['scenario'][0].'",
                        `RecLastChangeDatetime` = "'.$update_date.'"
                    WHERE 
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');   
                $row = fetch_row_from_sql('
                    SELECT
                        RecLastChangeDatetime
                    FROM
                        DepositRecs
                    WHERE
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');
                echo 0,$row[0],'|',get_recon_data_map_by_rec_id($_REQUEST['deposit_rec_id']);
                do_sql('UNLOCK TABLES;');
                exit; 
            } else {
                // старое значение изменилось, возвращаем новые данные
                $new_value = $row[0];
                $row = fetch_row_from_sql('
                    SELECT
                        RecLastChangeDatetime
                    FROM
                        DepositRecs
                    WHERE
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');
                // Нельзя обновлять, возвращаем новое значение
                echo '1';
                echo $new_value,'|',$row[0],'|',get_recon_data_map_by_rec_id($_REQUEST['deposit_rec_id']);
                do_sql('UNLOCK TABLES;');
                exit;                
            };
        } else {
            // Акта еще нет - добавляем новую запись 
            do_sql('
                INSERT INTO `cashmaster`.`Acts`
                    (
                        `DepositId`,
                        `DiscrepancyKindId`,
                        `DiscrepancyDescr`
                    )
                VALUES
                    (
                        "'.$_REQUEST['deposit_rec_id'].'",
                        "'.$_REQUEST['discr_type'].'",
                        "'.$_REQUEST['discr_comment'].'"
                    )
            ;');
            // обновляем дату изменения сверки
            do_sql('
                UPDATE `cashmaster`.`DepositRecs`
                SET
                    `ScenarioId` = "'.$_SESSION[$program]['scenario'][0].'",
                    `RecLastChangeDatetime` = "'.$update_date.'"
                WHERE 
                    `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
            ;');   
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
