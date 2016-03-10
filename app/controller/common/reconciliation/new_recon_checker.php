<?php

/*
 * Проверяет можно ли создать новую сверку по этому сценарию
 */
        if (!isset($c)) exit;
        
        
        $sorter_data_is_ok = true;
        
        
        //if($_SESSION[$program]['scenario']==0) {
            // Проверяем индексы
            include './app/controller/common/reconciliation/check_indexes.php';
        //};
        
        include 'app/controller/common/set_systemconfiguration.php';
        
        if($_SESSION[$program]['SystemConfiguration']['AllowRecBySupervisor']==0 
                AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==2) {
                $data['error'] = $_SESSION[$program]['lang']['supervisor_is_not_allowed_to_start_recon'];
                include './app/view/error_message.php';
                ?>
                    <hr/>
                    <div class="container">
                        <button
                            onclick="back_to_workflow();"
                            class="btn-primary btn-large" href="index.php"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
                    </div>
                <?php
                exit;
        };
        
        // Получаем список депозитов с таким номером карты
        //echo 'Список несверенных депозитов с таким номером карты:';
        $sql = '
            SELECT
                    *
             FROM
                    DepositRuns
            LEFT JOIN
                DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
             WHERE
                    DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                    AND IFNULL(DepositRecs.ReconcileStatus,0)="0"
             ORDER BY DepositRunId ASC;
        ;';
        $deposits = get_assoc_array_from_sql($sql);
        
        
        // Проверяем наличие в депозитах неформализованных Valuаbles
        $sql = '
            SELECT 
                Id,
                Valuables.CategoryName,
                ActualCount,
                IFNULL(Denoms.DenomId,"-") as Denom
            FROM 
                SorterAccountingData
            LEFT JOIN
                   Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
            LEFT JOIN
                DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
            LEFT JOIN
                Denoms ON Denoms.DenomId = Valuables.DenomId
            WHERE
                   DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                   AND Valuables.DenomId="0" AND Valuables.ValuableTypeId="0" 
                   AND `DepositRuns`.`DepositRecId` is NULL
        ;';
        $new_valuables = get_assoc_array_from_sql($sql);
        if(count($new_valuables)>0) {
            ?>
            <div class="container">
                <h3 style="color:darkblue;padding: 0px;margin: 0px;">
                    <?php echo $_SESSION[$program]['lang']['cant_open_recon_by_card'],htmlfix($_GET['separator_id']); ?>
                    <font style="font-size:12px;"></font>
                </h3>                
            </div>
            <?php
            include './app/model/table/new_category_names.php';
        };
        
        // Проверка валют на соответствие сценарию ==========================================
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
            $data['error'] = $_SESSION[$program]['lang']['wrong_currency_in_accounting_data'].': '.implode(', ',$extra_currencies);
            include './app/view/error_message.php';
            $no_sverka_button = true;
            
        };
        
        
        
        
        
        
        
        // Проверка классов на соответствие сценарию =======================================
        $scenario_grades = get_scenario_sorter_grades($_SESSION[$program]['scenario'][0]);
        //print_array_as_html_table($scenario_grades);
        //echo '<br/>';
        //echo '<br/>';
        
        $sorter_grades = get_sorter_accounting_data_grades_by_cardnumber($_REQUEST['separator_id']);
        //print_array_as_html_table($sorter_grades);
        //echo '<br/>';
        //echo '<br/>';
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
        

        // для каждой валюты рисуем таблицы сверки
        // Уточнняем допустимые номиналы для данной валюты в этом сценарии
        $denoms = get_array_from_sql('
            SELECT
                   Denoms.DenomId,
                   Denoms.Value,
                   Currency.CurrYear,
                   Currency.CurrName  
            FROM
                   ScenDenoms
            LEFT JOIN
                   Denoms ON Denoms.DenomId = ScenDenoms.DenomId
            LEFT JOIN
                   Currency ON Currency.CurrencyId=Denoms.CurrencyId
            WHERE
                   ScenarioId = "'.$_SESSION[$program]['scenario'][0].'"
            ORDER BY Value ASC;   
        ;');

        //print_array_as_html_table($denoms);


        $sorter_denoms = get_array_from_sql('
            SELECT
                Denoms.DenomId,
                Denoms.Value,
                Currency.CurrYear,
                Currency.CurrName  
            FROM
                SorterAccountingData
            LEFT JOIN
                Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
            LEFT JOIN
                Denoms ON Denoms.DenomId = Valuables.DenomId
            LEFT JOIN
                Currency ON Currency.CurrencyId=Denoms.CurrencyId
            LEFT JOIN
                (SELECT 
                    * 
                 FROM 
                    ValuablesGrades
                 WHERE  
                    ScenarioId="'.$_SESSION[$program]['scenario'][0].'") as t1 ON t1.ValuableId = Valuables.ValuableId
            LEFT JOIN
                Grades ON Grades.GradeId=t1.GradeId
            LEFT JOIN
                DepositRuns ON DepositRuns.DepositRunId=SorterAccountingData.DepositRunId
            WHERE
                    DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                    AND `DepositRuns`.`DepositRecId` is NULL

            GROUP BY Denoms.DenomId
            ORDER BY Denoms.DenomId ASC

        ;');
        
        
        $extra_denoms = array();

        foreach ($sorter_denoms as $sorter_key=>$sorter_denom) {
            $flag=false;
            foreach ($denoms as $denom_key=>$denom) {
                $denoms[$denom_key][4]='0';
                if($sorter_denom[0]==$denom[0]) {
                    //echo $sorter_denom[0],'==',$denom[0],'<br/>';
                    $flag=true;
                    $denoms[$denom_key][4]='1';
                };
            };
            if($flag==false) {
                $extra_denoms[] = ((int)$sorter_denom[1]).' '.$sorter_denom[3].' '.$sorter_denom[2];
            };
        };
        
        
        
        if(count($extra_denoms)>0) {
            $sorter_data_is_ok = false;
            $data['error'] = $_SESSION[$program]['lang']['wrong_denom_in_accounting_data'].': '.implode(', ',$extra_denoms);
            include './app/view/error_message.php';
            $no_sverka_button = true;
        };
        
        if ($sorter_grades_is_ok==FALSE){
            $sql = '
                SELECT
                        DepositRuns.SortModeName
                 FROM
                        DepositRuns
                 WHERE
                        DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                        AND `DepositRuns`.`DepositRecId` is NULL
                 GROUP BY DepositRuns.SortModeName
            ;';
            $rows = get_array_from_sql($sql);
            $mode_names=array();
            foreach ($rows as $row) {
                $mode_names[] = htmlfix($row[0]);
            };
            
            $data['error'] = $_SESSION[$program]['lang']['wrong_grade_in_accounting_data']
                            .'.<br/>'.$_SESSION[$program]['lang']['used_sorter_mode'].': '
                            .implode(',',$mode_names);
            include './app/view/error_message.php';
            $no_sverka_button = true;
           
        };

        $DepositRecId = 0;
        
        
        // Если всё прошло успешно то создаем новую сверку
        if($sorter_grades_is_ok==true AND $sorter_data_is_ok==true) {
            if ($scenario['ReconcileAgainstValue']==1) {
                $DepositRecId = create_new_recon_value(
                        $_SESSION[$program]['UserConfiguration']['CurrentScenario'],
                        $deposits
                );                
            } else {                
                $DepositRecId = create_new_recon(
                        $_SESSION[$program]['UserConfiguration']['CurrentScenario'],
                        $deposits
                );
            };
        };
        
?>
