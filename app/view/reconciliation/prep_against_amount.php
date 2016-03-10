<?php

/*
 * Сверка по количеству
 */

    if (!isset($c)) exit;

?>
<script>
    
</script>
<div class="container">
    <input type="hidden" id="RecLastChangeDatetime" value="<?php echo $DepositRec['RecLastChangeDatetime']; ?>"/>
    <h3 style="color:darkblue;padding: 0px;margin: 0px;">
        <?php echo htmlfix($_SESSION[$program]['lang']['preparation_by_card_number'].$_GET['separator_id']); ?>
        <font style="font-size:12px;">(<?php 
                echo htmlfix(get_post_and_short_fio_by_user_id($DepositRec['PrepOperatorId'])); 
            ?>)</font>
    </h3>
    <?php

        $sorter_data_is_ok = true;

        include './app/controller/common/reconciliation/check_currencies.php';
        include './app/controller/common/reconciliation/check_denoms.php';
        include './app/controller/common/reconciliation/check_grades.php';



        foreach ($scenario_currencies as $key=>$currency) {
                    ?>
                    <table>
                    <?php
                        // список категорий пересчета по депозиту
                        //$table['data'] = $grades;
                        //draw_simple_table($table);

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
                                   AND ScenDenoms.IsUsed="1"
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
                              <td>
                                  <input type="hidden" class="currency_name" value="<?php echo $currency[1],' ',$currency[3]; ?>"/>
                                  <h4 style="margin-bottom: 10px;padding:0px;"><?php echo $currency[4],' ',$currency[3]; ?></h4></td>
                                  <?php 
                                      if($sorter_grades_is_ok==true 
                                              AND $sorter_data_is_ok==true
                                              AND $recon_grades_is_ok==true) {
                                          ?>
                                              <td colspan="2"><hr/></td>
                                          <?php
                                      };
                                  ?>
                                  </tr>

                                    <tr class="currency">
                                    <td style="<?php 
                                        echo 'display:none;';
                                    ?>vertical-align: top;padding-right:30px;"><?php
                                        include './app/view/reconciliation/amount/sorter_data_form.php';
                                    ?></td>
                                    <?php 
                                    if($sorter_grades_is_ok==true 
                                            AND $sorter_data_is_ok==true
                                            AND $recon_grades_is_ok==true) {
                                    ?>
                                        <td style="display:none;vertical-align: top;padding-right:30px;"><?php 
                                            include './app/view/reconciliation/amount/recon_data_input_form.php';
                                        ?></td>
                                        <td style="vertical-align: top;"><?php 
                                            include './app/view/preparation/amount/recon_data.php';
                                        ?></td>
                                    <?php
                                };
                            ?>
                            </tr>          
                        </table>
                        <br/>
                            <?php
                        };
                    ?>
</div>    
<div class="container">
    <?php
    
        include './app/view/preparation/requisits.php';
        include './app/view/preparation/buttons.php';
        include './app/view/set_rs_to_stat.php';
        //include './app/view/reconciliation/keyboard_driver.php';
        
        
    ?>
</div>

