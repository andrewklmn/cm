<?php

/*
 *  Проверка валют в данных пересчета на соответствие сценарию
 */

        if (!isset($c)) exit;

        $scenario_currencies=get_scenario_currency($_SESSION[$program]['scenario'][0]);
        $sorter_currencies = get_sorter_accounting_data_currencies($_REQUEST['separator_id']);
        $extra_currencies = array();
        
        // Проверяем совпадают ли валюты сценария с валютами пересчета.
        foreach ($sorter_currencies as $sorter_currency) {
            $flag = false;
            foreach ($scenario_currencies as $scenario_currency) {
                if($sorter_currency==$scenario_currency) {
                    $flag=true;
                };
            };
            if ($flag==false) {
                $extra_currencies[] = $sorter_currency[4].' '.$sorter_currency[3];
            };
        };
        if (count($extra_currencies)>0) {
            $sorter_data_is_ok = false;
            $data['error'] = 'В данных пересчета неподходящие валюты: '.implode(', ',$extra_currencies);
            include './app/view/error_message.php';
            $no_sverka_button = true;
            
        };
?>
