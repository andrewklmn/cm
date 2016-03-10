<?php

/*
 * Reconciliation data updater
 */

        if (!isset($c)) exit;

        include './app/model/reconciliation/recon_function_load.php';
        
        $DepositRec = get_reconciliation_by_id($_REQUEST['deposit_rec_id']);
        $DepositRec_last_operator_id=$DepositRec['RecOperatorId'];
        $DepositRecId = $DepositRec['DepositRecId'];
        
        if ($_SESSION[$program]['user_id']!=$DepositRec_last_operator_id
                AND $_SESSION[$program]['user_role_id']!=2) {
                include './app/view/html_header.php';
                echo 'У вас нет прав работать с отложенной сверкой № '.$DepositRec['DataSortCardNumber'];
        } else {
            include './app/controller/supervisor/recon_reconcile.php';
        };
?>
