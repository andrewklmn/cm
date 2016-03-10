<?php

/*
 * Проверяет все классы в сценарии
 */

        if (!isset($c)) exit;

        
        // Уточнняем допустимые номиналы в этом сценарии
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
            ORDER BY Value ASC
        ;');


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
                    DepositRuns.DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                    AND `DepositRuns`.`DepositRecId`  = "'.$DepositRecId.'"

            GROUP BY Denoms.DenomId
            ORDER BY Denoms.DenomId ASC

        ;');
        $extra_denoms = array();

        foreach ($sorter_denoms as $sorter_key=>$sorter_denom) {
            $flag=false;
            foreach ($denoms as $denom_key=>$denom) {
                $denoms[$denom_key][4]='0';
                if($sorter_denom[0]==$denom[0]) {
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
            $data['error'] = 'В данных пересчета неподходящие номиналы: '.implode(', ',$extra_denoms);
            include './app/view/error_message.php';
            $no_sverka_button = true;
        };
?>
