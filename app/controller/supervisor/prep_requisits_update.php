<?php

/*
 * Reconciliation data update
 */

        if (!isset($c)) exit;

        include './app/view/html_header.php';
        
        //проверяем входные параметры
        if(!(isset($_REQUEST['deposit_rec_id']) AND $_REQUEST['deposit_rec_id']>0)
                OR !isset($_REQUEST['last_update_time'])
                OR !isset($_REQUEST['values'])
                OR !isset($_REQUEST['oldvalues'])
            ) {
            echo 'Wrong update data';
            exit;
        };
        
        
        // Блокируем таблицу
        do_sql('LOCK TABLES DepositCurrencyTotal WRITE,SorterIndexes WRITE, DepositRuns WRITE, ReconAccountingData WRITE, DepositRecs WRITE, DepositDenomTotal WRITE;');

        include './app/controller/common/reconciliation/check_recon_status.php';
        //include './app/controller/common/reconciliation/check_recon_indexes.php';
        //include './app/controller/common/reconciliation/check_recon_accounting_data.php';
        //include './app/controller/common/reconciliation/check_sorter_accounting_data.php';
    
        $update_date = date("Y-m-d H:i:s", time());
        
        //Получаем данные по сверке
        $row = fetch_row_from_sql('
            SELECT
                IF(IFNULL(CustomerId,0)=0,"",CustomerId),
                IF (DepositPackingDate="0000-00-00","",DepositPackingDate),
                PackingOperatorName,
                PackType,
                PackIntegrity,
                PackId,
                SealType,
                SealIntegrity,
                SealNumber,
                StrapType,
                StrapsIntegrity
            FROM 
                DepositRecs 
            WHERE
                `DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
        ;');
        
        
        $old = explode('|',$_REQUEST['oldvalues']);

        
        if ($old[1]=='0000-00-00') {
            $old[1]='';
        };
        
        
        // Проверяем старое значение
        if ($row==$old) {
            // Можно обновлять
            $n =  explode('|', $_REQUEST['values']);
            if ($_SESSION[$program]['user_role_id']!=2) {
                do_sql('
                    UPDATE `cashmaster`.`DepositRecs`
                    SET
                        CustomerId="'.$n[0].'",
                        DepositPackingDate="'.$n[1].'",
                        PackingOperatorName="'.$n[2].'",
                        PackType="'.$n[3].'",
                        PackIntegrity="'.$n[4].'",
                        PackId="'.$n[5].'",
                        SealType="'.$n[6].'",
                        SealIntegrity="'.$n[7].'",
                        SealNumber="'.$n[8].'",
                        StrapType="'.$n[9].'",
                        StrapsIntegrity="'.$n[10].'",
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
                        CustomerId="'.$n[0].'",
                        DepositPackingDate="'.$n[1].'",
                        PackingOperatorName="'.$n[2].'",
                        PackType="'.$n[3].'",
                        PackIntegrity="'.$n[4].'",
                        PackId="'.$n[5].'",
                        SealType="'.$n[6].'",
                        SealIntegrity="'.$n[7].'",
                        SealNumber="'.$n[8].'",
                        StrapType="'.$n[9].'",
                        StrapsIntegrity="'.$n[10].'",
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
            //print_r($row);
            //print_r($old);
            // Нельзя обновлять, возвращаем новое значение
            echo '1';
            $r = fetch_row_from_sql('
                SELECT
                    RecLastChangeDatetime
                FROM 
                    DepositRecs 
                WHERE
                    `DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
            ;');
            echo implode('|',$row),'|||',$r[0],'|',get_recon_data_map_by_rec_id($_REQUEST['deposit_rec_id']);
            do_sql('UNLOCK TABLES;');
            exit;
        };

        
        // Снимаем блокировку с таблиц 
        do_sql('UNLOCK TABLES;');

        
?>
