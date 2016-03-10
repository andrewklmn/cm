<?php

/*
 * Модель данных для отчета сверенного депозита по номеру карты
 */

    include_once './app/model/reconciliation/recon_function_load.php';
    
    function get_reconciled_deposit_by_rec_id($rec_id) {
        
        global $db;
        global $program;
        
        $DepositRec = get_reconciliation_by_id($rec_id,1);
        //print_r($DepositRec);
        //exit;
        
        
        $data['card_number'] = $DepositRec['DataSortCardNumber'];
        $sql = '
            SELECT
                    DepositRuns.SortModeName
             FROM
                    DepositRuns
             WHERE
                    `DepositRuns`.`DepositRecId`="'.$DepositRec['DepositRecId'].'"
             GROUP BY DepositRuns.SortModeName;
        ;';
        $rows = get_array_from_sql($sql);
        $mode_names=array();
        foreach ($rows as $row) {
            $mode_names[] = htmlfix($row[0]);
        };
        $t = implode(',',$mode_names);
        if ($t=='') $t=' нет данных';
        $data['sort_mode'] = $t;
        
        
        
        $data['sort_operator'] = implode(', ',get_sort_operators_by_rec_id($DepositRec['DepositRecId']));
        $data['recon_operator'] = get_short_fio_by_user_id($DepositRec['RecOperatorId']);
        $data['supervisor'] = get_short_fio_by_user_id($DepositRec['RecSupervisorId']);
        
        
        $t = get_sorter_start_and_stop_time_by_rec_id($DepositRec['DepositRecId']);
        $data['sort_start_time'] = $t[0];
        $data['sort_stop_time'] = $t[1];
        $data['recon_start_time'] = $DepositRec['RecCreateDatetime'];
        $data['recon_stop_time'] = $DepositRec['RecLastChangeDatetime'];

        $data['index'] = implode(', ',get_indexes_by_rec_id($DepositRec['DepositRecId']));
        $data['machine'] = implode(', ',get_machines_by_rec_id($DepositRec['DepositRecId']));

        $t = get_client_name_and_code_by_id( $DepositRec['CustomerId']);
        $data['client_name'] = $t['CustomerName'];
        $data['client_code'] = $t['CustomerCode'];

        $data['pack_date'] = $DepositRec['DepositPackingDate'];
        $data['packman'] = $DepositRec['PackingOperatorName'];

        $data['pack_type'] = ($DepositRec['PackType']==1) ? 'пачка':'мешок';
        $data['pack_integrity'] = ($DepositRec['PackIntegrity']==1) ? 'целая':'поврежденная';
        $data['pack_number'] = $DepositRec['PackId'];
        
        $data['seal_type'] = ($DepositRec['SealType']==1) ? 'пломба':'клише';
        $data['seal_integrity'] = ($DepositRec['SealIntegrity']==1) ? 'целая':'поврежденная';
        $data['seal_number'] = $DepositRec['SealNumber'];
        
        $data['strap_type'] = ($DepositRec['StrapType']==1) ? 'поперечная':'полной длины';
        $data['strap_integrity'] = ($DepositRec['StrapsIntegrity']==1) ? 'целая':'поврежденная';
        
        $data['grade'] = get_all_grades_by_scenario_id($DepositRec['ScenarioId']);
        
        
        $t = get_scenario_currency($DepositRec['ScenarioId']);
        $a = array();
        $currency = array();
        foreach ($t as $value) {
            $a[]=$value[4];
            $currency[] = $value[0];
        };
        $data['currency'] = $a;
        
        $grades = get_all_grade_ids_by_scenario_id($DepositRec['ScenarioId']);
        $all_data = array();
        $i=0;
        foreach ($currency as $value) {
            $all_data[$i] = array();
            $denoms = get_scenario_denoms_by_id_and_currency($DepositRec['ScenarioId'], $value); 
            $j=0;
            foreach ($denoms as $denom) {
                $a = array();
                $a[] = $denom['Value'];
                $s = 0;
                foreach ($grades as $grade) {
                    $count = get_total_by_rec_denom_grade(
                                            $DepositRec['DepositRecId'], 
                                            $denom['DenomId'], 
                                            $grade,
                                            $DepositRec['ScenarioId']);
                    $a[] = $count;
                    $s+=$count;
                };
                $count = get_expected_by_denom_and_rec_id(
                                    $denom['DenomId'], 
                                    $DepositRec['DepositRecId']);
                $a[] = $count;
                $s += $count;
                if ($s>0) {
                    $all_data[$i][$j] = $a;
                }
                $j++;
            };
            $i++;
        };
        
        
        $data['denom'] = $all_data;
        
        $t = get_recon_acts_comments_by_id($DepositRec['DepositRecId']);
        
        $data['comment_over'] = $t[0];
        $data['comment_deficit'] = $t[2];
        $data['comment_suspect'] = $t[1];
        
        $data['is_balanced'] = ($DepositRec['IsBalanced']==1)?true:false;
        
        return $data;
    };

?>
