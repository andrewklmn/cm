<?php

/* 
 * Сверенная сверка по квитанции
 */

    if (!isset($c)) exit;

    $_REQUEST['id']=$rec['DepositRecId'];
    include 'app/view/forms/finished_reconciliation.php';
    
?>