<?php

/*
 * Проверяет не сверена ли сверка
 */
        if (!isset($c)) exit;
        
        include_once './app/model/reconciliation/get_recon_data_map_by_cardnumber.php';
        
        if ( get_recon_data_map_by_cardnumber($data_sort_cardnumber)!=$_REQUEST['rec_data_map']) {
            do_sql('UNLOCK TABLES;');
            echo '3';
            exit;
        };
        
?>
