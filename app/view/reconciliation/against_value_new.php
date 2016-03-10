<?php

/*
 * Сверка по количеству
 */

    if (!isset($c)) exit;

        // Проверка условий для создания новой сверки
        include './app/controller/common/reconciliation/new_recon_checker.php';
        
        if($sorter_grades_is_ok==true 
                AND $sorter_data_is_ok==true
                AND count($extra_denoms)==0) {
            // сверка открыта
            
            // Так как сверка отложенная, то находим её id по номеру разделительной карты из несверенных рансов
            $row = fetch_row_from_sql('
                SELECT
                    `DepositRuns`.`DepositRecId`
                FROM 
                    DepositRuns
                LEFT JOIN
                    DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
                WHERE
                    `DepositRuns`.`DepositRecId` > 0
                    AND `DepositRuns`.`DataSortCardNumber`="'.addslashes($_GET['separator_id']).'"
                    AND IFNULL(DepositRecs.ReconcileStatus,0)="0"
                GROUP BY DepositRuns.DepositRecId
            ;');

            if (!isset($row[0])) {
                $data['error'] = htmlfix($_GET['separator_id']).' - '.$_SESSION[$program]['lang']['deposit_was_reconciled_by_another_user'];
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
            
            
            $DepositRecId = $row[0];
            $DepositRec = get_reconciliation_by_id($DepositRecId);
            $DepositRec_last_operator_id=$DepositRec['RecOperatorId'];
            
            include './app/view/reconciliation/against_value.php';
            
        } else {
            // СВЕРКА НЕВОЗМОЖНА
                ?>
                    <div class="container">
                        <h3 style="color:darkblue;padding: 0px;margin: 0px;">
                            <?php echo htmlfix($_SESSION[$program]['lang']['cant_open_recon_by_card'].$_GET['separator_id']); ?>
                            <font style="font-size:12px;"></font>
                        </h3>
                <?php
            foreach ($scenario_currencies as $key=>$currency) {
                ?>
                    <table>
                <?php

                // для каждой валюты рисуем таблицы сверки
                // Уточнняем допустимые номиналы для данной валюты в этом сценарии
                $denoms = get_array_from_sql('
                    SELECT
                           Denoms.DenomId,
                           Denoms.Value,
                           Currency.CurrYear,
                           Currency.CurrName,
                           0,
                           ExpectedCount
                    FROM
                           ScenDenoms
                    LEFT JOIN
                           Denoms ON Denoms.DenomId = ScenDenoms.DenomId
                    LEFT JOIN
                           Currency ON Currency.CurrencyId=Denoms.CurrencyId
                    LEFT JOIN
                        (
                            SELECT
                                Denoms.DenomId as DenomId,
                                IFNULL(SUM(`DepositDenomTotal`.`ExpectedCount`),0) as ExpectedCount
                            FROM `cashmaster`.`DepositDenomTotal`
                            LEFT JOIN
                                Denoms ON Denoms.DenomId = `DepositDenomTotal`.`DenomId`
                            LEFT JOIN
                                Currency ON Currency.CurrencyId=Denoms.CurrencyId
                            WHERE
                                `DepositDenomTotal`.`DepositReclId`="'.addslashes($DepositRecId).'"
                                AND Currency.CurrencyId="'.addslashes($currency[0]).'"
                            GROUP BY Denoms.DenomId
                        ) as t1 ON t1.DenomId = Denoms.DenomId
                    WHERE
                           ScenarioId = "'.$_SESSION[$program]['scenario'][0].'"
                           AND Denoms.CurrencyId = "'.$currency[0].'"
                           AND ScenDenoms.ScenarioId = "'.$_SESSION[$program]['UserConfiguration']['CurrentScenario'].'"
                           AND ScenDenoms.IsUsed = "1"
                    GROUP BY Denoms.DenomId
                    ORDER BY Value ASC;   
                ;');

               $estimated_amount = 0;
               $estimated_summ = 0;

               foreach ($denoms as $denom_key=>$denom) {
                    $denoms[$denom_key][4]='0';
                    foreach ($sorter_denoms as $sorter_key=>$sorter_denom) {
                        if($sorter_denom[0]==$denom[0]) {
                            $denoms[$denom_key][4]='1';
                            $estimated_amount += $denom[1]*$scenario['DefExpectedNumber'];
                            $estimated_summ += $scenario['DefExpectedNumber'];
                            //$denoms[$denom_key][5]=$scenario['DefExpectedNumber'];
                        };
                    };
                };


                ?>
                              <tr>
                  <tr>
                      <td><h5 style="margin:0px;padding:0px;"><?php echo $currency[4],' ',$currency[3]; ?></h5></td>
                      <?php 
                          if($sorter_grades_is_ok==true 
                                  AND $sorter_data_is_ok==true) {
                              ?>
                                  <td colspan="2"><hr/></td>
                              <?php
                          };
                      ?>
                  </tr>
                    <tr class="currency">
                            <td style="vertical-align: top;padding-right:30px;"><?php
                                include './app/view/reconciliation/sorter_data_form.php';
                            ?></td>
                    </tr>           
                    </table>
                    <br/>
                    <?php
                };
                ?>
                    </div>
                    <div class="container">
                        <?php
                            //include './app/view/reconciliation/discrepancies.php';
                            include './app/view/reconciliation/buttons.php';
                        ?>
                    </div>
                <?php
            };

?>

