<?php

/*
 * Проверяет однономинальность в данных пересчета
 */

    if (!isset($c)) exit;
    
    $denoms_amount = get_array_from_sql('
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
                    AND `DepositRuns`.`DepositRecId`  = "'.$DepositRecId.'"

            GROUP BY Denoms.DenomId
            ORDER BY Denoms.DenomId ASC
    ;');
    
    if (count($denoms_amount)>1 AND $scenario['SingleDenomDeposits']==1){
            include './app/view/reconciliation/messages/non_single_denom.php';
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
?>
