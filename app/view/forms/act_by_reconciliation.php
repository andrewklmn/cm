<?php

/*
 * Finished reconciliation form
 */
    if (!isset($c)) exit;
    
        include_once './app/model/reconciliation/get_reconciled_deposit_by_rec_id.php';
        include_once './app/model/reconciliation/get_sorter_accounting_data_currencies_by_rec_id.php';
        include_once 'app/model/nmr2str_by_currency.php';
        include_once 'app/model/int2str.php';
        
        $data = get_reconciled_deposit_by_rec_id($_REQUEST['id']);
        include 'app/controller/common/reconciliation/set_count_if_no_scenario.php';
        
        $data['title'] = $_SESSION[$program]['lang']['recon_report_header'].' №'.$data['card_number'];
        include './app/view/html_header.php';
        include './app/view/html_head_bootstrap.php';
        //include './app/view/html_head_for_report.php';
        
        $row = fetch_assoc_row_from_sql('
            SELECT
                *
            FROM
                DepositRecs
            WHERE
                DepositRecId = "'.addslashes($_GET['id']).'"
        ;');
        $scenario = get_scenario_by_id($row['ScenarioId']);
        $against_value=$scenario['ReconcileAgainstValue'];

    $action_name = 'data_report';
    include 'app/view/reports/printed_0402145_complex_fordeposit.php';

?>