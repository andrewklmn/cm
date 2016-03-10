<?php

/*
 * Reconciliation data update
 */

        if (!isset($c)) exit;

        include './app/view/html_header.php';
        
        //проверяем входные параметры
        if(!(isset($_REQUEST['deposit_rec_id']) AND $_REQUEST['deposit_rec_id']>0)
                OR !isset($_REQUEST['last_update_time'])
                OR !isset($_REQUEST['rec_data_map'])) {
            echo 'Wrong update data';
            exit;
        };
        
            
        
        // Блокируем таблицу
        do_sql('LOCK TABLES DepositCurrencyTotal WRITE,SuspectSerialNumbs WRITE, SorterIndexes WRITE, DepositRecs WRITE, DepositRuns WRITE, DepositDenomTotal WRITE, ReconAccountingData WRITE;');
        
        include './app/controller/common/reconciliation/check_recon_status.php';
        include './app/controller/common/reconciliation/check_recon_indexes.php';
        include './app/controller/common/reconciliation/check_recon_accounting_data.php';
        include './app/controller/common/reconciliation/check_sorter_accounting_data.php';
        include './app/controller/common/reconciliation/check_suspect.php';
        
        $update_date = date("Y-m-d H:i:s", time());
        
        // Проверяем время последнего изменения DepositRec 
        if($last_update_time==$_REQUEST['last_update_time']) {
            // можно сверять
            do_sql('
                UPDATE `cashmaster`.`DepositRecs`
                SET
                    `DepositRecs`.`IsBalanced`="0",
                    `DepositRecs`.`FwdToSupervisor`="1",
                    `RecLastChangeDatetime` = "'.$update_date.'"
                WHERE 
                    `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
            ;');                
            echo 0;
        } else {
            // нельзя сверять потому что кто-то менял данные
            echo 3;
        };
        // Снимаем блокировку с таблиц 
        do_sql('UNLOCK TABLES;');

?>
