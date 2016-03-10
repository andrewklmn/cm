<?php

/*
 * 
 */
        if (!isset($c)) exit;

        if (count($data['grade'])==0
                AND count($data['currency'])==0
                AND count($data['denom'])==0) {
            $data['grade'] = array('Счет');
            $t = get_sorter_accounting_data_currencies_by_rec_id($_REQUEST['id']);
            $a = array();
            $currency = array();
            foreach ($t as $value) {
                $a[]=$value[4];
                $currency[] = $value[0];
            };
            $data['currency'] = $a;
            
            $all_data = array();
            $i=0;
            foreach ($currency as $value) {
                $all_data[$i] = array();
                $denoms = get_denoms_by_currency($value); 
                $j=0;
                foreach ($denoms as $denom) {
                    $a = array();
                    $a[] = $denom['Value'];
                    $s = 0;
                    $count = get_total_by_rec_denom(
                                            $_REQUEST['id'], 
                                            $denom['DenomId']);
                    $a[] = $count;
                    $s+=$count;
                    
                    //$count = get_expected_by_denom_and_rec_id(
                    //                    $denom['DenomId'], 
                    //                    $DepositRec['DepositRecId']);
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
        
        };
?>
