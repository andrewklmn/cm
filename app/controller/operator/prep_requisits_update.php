<?php

/*
 * Reconciliation total data updater
 */

        if (!isset($c)) exit;

        include './app/model/reconciliation/recon_function_load.php';
        
        $DepositRec = get_reconciliation_by_id($_REQUEST['deposit_rec_id']);
        $DepositRecId = $DepositRec['DepositRecId'];
        
        //Получаем кассу оператора подготовки
        $row = fetch_row_from_sql('
            SELECT
                `UserConfiguration`.`CashRoomId`
            FROM 
                `UserConfiguration`
            WHERE
                `UserConfiguration`.`UserId`="'.$DepositRec['PrepOperatorId'].'"    
        ;');
        
        if ($row[0]!=$_SESSION[$program]['UserConfiguration']['CashRoomId']) {
                include './app/view/html_header.php';
                echo 'У вас нет прав работать с отложенной сверкой';
        } else {
            include './app/controller/supervisor/prep_requisits_update.php';
        };
?>
