<?php

/*
 * Проверяет все классы в сценарии
 */

        if (!isset($c)) exit;

        // Проверка классов на соответствие сценарию =======================================
        $scenario_grades = get_scenario_sorter_grades($_SESSION[$program]['scenario'][0]);
        $sorter_grades = get_sorter_accounting_data_grades_by_cardnumber($_REQUEST['separator_id']);
        $sorter_data_is_ok = true;
        
        $sorter_grades_is_ok = true;
        // Дополняем сценарные классы новыми из данных пересчета
        $grades = $scenario_grades;
        foreach ($sorter_grades as $sorter_grade) {
            $flag = false;
            foreach ($grades as $scenario_grade) {
                if ($sorter_grade[1]==$scenario_grade[1] 
                        AND $sorter_grade[2]==$scenario_grade[2]) $flag=true;
            };
            if ($flag==false) {
                $grades[] = $sorter_grade;
                $sorter_grades_is_ok = false;
                $sorter_data_is_ok = false;
            };
        };
        
        
        //определяем классы ручного ввода
        $recon_grades = get_scenario_recon_grades($_SESSION[$program]['scenario'][0]);
        $recon_data_grades = get_recon_accounting_data_grades($DepositRecId);
        
        $extra_recon_grades = array();
        $recon_grades_is_ok = true;
        foreach ($recon_data_grades as $recon_data_grade) {
            $flag = false;
            foreach ($recon_grades as $recon_grade) {
                if ($recon_data_grade[0]==$recon_grade[0]) $flag=true;
            };
            if ($flag==false) {
                $extra_recon_grades[] = $recon_data_grade[2];
                $recon_grades_is_ok = false;
            };
        };
        
        if ($sorter_grades_is_ok == false){
            include './app/view/reconciliation/messages/wrong_sorter_grades.php';
            $no_sverka_button = true;
           
        };
        
        if ($recon_grades_is_ok == false){
            include './app/view/reconciliation/messages/wrong_recon_grades.php';
            $no_sverka_button = true;
        };
        
?>
