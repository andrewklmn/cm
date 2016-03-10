<?php

/* 
 * Проверяем не является ли номер сепаратора штрих-кодом квитанции
 */


    if (!isset($c)) exit;
    
    if (substr($_GET['separator_id'], 0, 2)=='CM') {
        // Парсин штрихкод квитанции
        $codes = explode('-', $_GET['separator_id']);
        // проверяем отделение банка
        if (isset($codes[1]) 
                AND isset($codes[2]) 
                AND (int)$codes[2]>0
                AND $_SESSION[$program]['SystemConfiguration']['CashCenterCode']==$codes[1]) {
            // Проверяем кассу пересчета сверки по этой квитанции
            $rec = fetch_assoc_row_from_sql('
                SELECT
                    `DepositRecs`.`DepositRecId`,
                    `DepositRecs`.`ScenarioId`,
                    `DepositRecs`.`CustomerId`,
                    `DepositRecs`.`DepositPackingDate`,
                    `DepositRecs`.`PackingOperatorName`,
                    `DepositRecs`.`PackIntegrity`,
                    `DepositRecs`.`StrapsIntegrity`,
                    `DepositRecs`.`SealNumber`,
                    `DepositRecs`.`SealType`,
                    `DepositRecs`.`PackId`,
                    `DepositRecs`.`RecOperatorId`,
                    `DepositRecs`.`RecCreateDatetime`,
                    `DepositRecs`.`RecLastChangeDatetime`,
                    `DepositRecs`.`IsBalanced`,
                    `DepositRecs`.`RecSupervisorId`,
                    `DepositRecs`.`ReconcileStatus`,
                    `DepositRecs`.`FwdToSupervisor`,
                    `DepositRecs`.`StrapType`,
                    `DepositRecs`.`SealIntegrity`,
                    `DepositRecs`.`PackType`,
                    `DepositRecs`.`DepositIndexId`,
                    `DepositRecs`.`Reported`,
                    `DepositRecs`.`ServiceRec`,
                    `DepositRecs`.`CardNumber`,
                    `DepositRecs`.`PrepOperatorId`
                FROM 
                    `cashmaster`.`DepositRecs`
                WHERE
                    `DepositRecs`.`DepositRecId`="'.addslashes($codes[2]).'"
            ;');
            
            //echo '<pre>';
            //print_r($rec);
            //echo '</pre>';
            
            // Проверяем есть ли оператор сверки и какой он кассы, если нет, то
            if ((int)$rec['RecOperatorId']>0 AND (int)$rec['PrepOperatorId']>0) {
                // Проверяем кассу по оператору сверки
                $row = fetch_assoc_row_from_sql('
                    SELECT
                        `UserConfiguration`.`CashRoomId`
                    FROM 
                        `cashmaster`.`UserConfiguration`
                    WHERE
                        `UserConfiguration`.`UserId`="'.addslashes($rec['RecOperatorId']).'"
                ;');
                if ($row['CashRoomId']==$_SESSION[$program]['UserConfiguration']['CashRoomId']){
                    // Проверяем состояние сверки
                    if ($rec['ReconcileStatus']=='1') {
                        // Выводим отчет о сверке 
                        include 'app/view/preparation/reconciled_deposit_report.php';
                        exit;
                    } else {
                        // Выводи сообщение
                        include 'app/view/preparation/recon_in_progress.php';
                        exit;
                    };
                } else {
                    // Сверка в другой кассе
                    include 'app/view/preparation/recon_in_another_cashroom.php';
                    exit;
                };
            } else {
                if ((int)$rec['PrepOperatorId']>0) {
                    // Проверяем есть ли оператор подготовки и какой он кассы
                    $row = fetch_assoc_row_from_sql('
                        SELECT
                            `UserConfiguration`.`CashRoomId`
                        FROM 
                            `cashmaster`.`UserConfiguration`
                        WHERE
                            `UserConfiguration`.`UserId`="'.addslashes($rec['PrepOperatorId']).'"
                    ;');
                    if ($row['CashRoomId']==$_SESSION[$program]['UserConfiguration']['CashRoomId']){
                        // Проверяем состояние сверки
                        if ($rec['ReconcileStatus']=='1') {
                            // Выводим отчет о сверке 
                            include 'app/view/preparation/reconciled_deposit_report.php';
                            exit;
                        } else {
                            // Выводи сообщение
                            include 'app/view/preparation/recon_in_progress.php';
                            exit;
                        };
                    } else {
                        // Сверка в другой кассе
                        include 'app/view/preparation/recon_in_another_cashroom.php';
                        exit;
                    };
                } else {
                    include 'app/view/preparation/wrong_receipt.php';
                    exit;
                };
            };
            exit;
            
        } else {
            // Номер не найден;
            include 'app/view/preparation/wrong_receipt.php';
            exit;
        };
    };
    
?>