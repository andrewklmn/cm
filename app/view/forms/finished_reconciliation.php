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
        include './app/view/page_header_with_logout.php';
        include './app/view/html_head_for_report.php';
        
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
?>
<div class="container" style="padding-left: 50px;width:640px;">
<table width='100%'>
    <tr>
        <td align="center">
            <h3 style="padding: 0px; margin: 0px;">Отчёт по сверенному депозиту<br/>(карта № <?php echo $data['card_number'];?>)</h3>
            <p style="padding: 0px; margin-top: 0px;">
                Процесс обработки: <?php echo $data['sort_mode']; ?>
                <br>
                Индекс: <?php echo htmlfix($data['index']);?>&nbsp;&nbsp;
                Машина: <?php echo htmlfix($data['machine']);?> 
            </p>
            <table class="report noborder width100">
                <tr>
                    <th style="vertical-align: top;" rowspan="2">Оператор обработки:</th>
                    <td style="vertical-align: top;" rowspan="2"><?php echo htmlfix($data['sort_operator']);?></td>
                    <th align="right">Начало обработки:</th>
                    <td align="left"><?php echo htmlfix($data['sort_start_time']);?></td>
                </tr>
                <tr>
                    <th align="right">Завершение обработки:</th>
                    <td align="left"><?php echo htmlfix($data['sort_stop_time']);?></td>
                </tr>
                <tr>
                    <th>Оператор сверки:</th>
                    <td><?php echo htmlfix($data['recon_operator']);?></td>
                    <th align="right">Начало сверки:</th>
                    <td align="left"><?php echo htmlfix($data['recon_start_time']);?></td>
                </tr>
                <tr>
                    <?php 
                        if($data['is_balanced']!=true) {
                            ?>
                                <th>Контролер:</th>
                                <td><?php echo htmlfix($data['supervisor']);?></td>
                            <?php
                        } else {
                            ?>
                                <th></th>
                                <td></td>
                            <?php
                        };
                    ?>
                    <th align="right">Завершение сверки:</th>
                    <td align="left"><?php echo htmlfix($data['recon_stop_time']);?></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php 
        if($data['is_balanced']!=true) { 
            ?>
            <tr>
                <td align="left">
                        <br/>
                        <table class='report'>
                            <tr>
                                <th>Клиент:</th>
                                <td colspan="5"><?php echo htmlfix($data['client_name']);?></td>
                            </tr>
                            <tr>
                                <th>Код БИК:</th>
                                <td><?php echo htmlfix($data['client_code']);?></td>
                                <th>Дата упаковки:</th>
                                <td><?php echo htmlfix($data['pack_date']);?></td>
                                <th>Упаковщик:</th>
                                <td><?php echo htmlfix($data['packman']);?></td>
                            </tr>
                        </table>
                        <br/>
                        <table class='report'>
                            <tr>
                                <th></th>
                                <th>Тип</th>
                                <th>Целостность</th>
                                <th>Номер</th>
                            </tr>
                            <tr>
                                <th>Упаковка:</th>
                                <td><?php echo htmlfix($data['pack_type']);?></td>
                                <td><?php echo htmlfix($data['pack_integrity']);?></td>
                                <td><?php echo htmlfix($data['pack_number']);?></td>
                            </tr>
                            <tr>
                                <th>Оттиск:</th>
                                <td><?php echo htmlfix($data['seal_type']);?></td>
                                <td><?php echo htmlfix($data['seal_integrity']);?></td>
                                <td><?php echo htmlfix($data['seal_number']);?></td>
                            </tr>
                            <tr>
                                <th>Бандероль:</th>
                                <td><?php echo htmlfix($data['strap_type']);?></td>
                                <td><?php echo htmlfix($data['strap_integrity']);?></td>
                                <th></th>
                            </tr>
                        </table>
                </td>
            </tr>
            <?php 
        };
    ?>
</table>
<?php 
    foreach ($data['currency'] as $key=>$value) {
        
        $total_processed_summ = 0;
        $total_expected_summ = 0;
        $total_discr_summ = 0;
        
        
        //echo '<pre>';
        //print_r($data);
        //echo '</pre>';
        
        $row = fetch_assoc_row_from_sql('
            SELECT
                `DepositCurrencyTotal`.`Id`,
                `DepositCurrencyTotal`.`DepositRecId`,
                `DepositCurrencyTotal`.`CurrencyId`,
                `DepositCurrencyTotal`.`ExpectedDepositValue`,
                `DepositCurrencyTotal`.`ValuableTypeId`
            FROM 
                `cashmaster`.`DepositCurrencyTotal`
            LEFT JOIN
                Currency ON Currency.CurrencyId = `DepositCurrencyTotal`.`CurrencyId`
            WHERE
                `DepositCurrencyTotal`.`DepositRecId`="'.addslashes($_GET['id']).'"
                AND `Currency`.`CurrName`="'.addslashes($value).'"
        ;');
        
        $estimated_summ = $row['ExpectedDepositValue'];
        
        ?>  <br/>
            <table class='report width100'>
                <tr>
                    <th colspan='<?php echo count($data['grade'])+1 ?>'>Валюта: <?php echo htmlfix($value);?></th>
                    <th colspan='2'>Всего обработано</th>
                    <?php 
                        if($data['is_balanced']!=true) {
                            if($against_value==1) {
                                ?>
                                    <th colspan='2' rowspan="2">Ожидалось</th>
                                    <th colspan='2' rowspan="2">Расхождение</th>
                                <?php
                            } else {
                                ?>
                                    <th colspan='2'>Ожидалось</th>
                                    <th colspan='2'>Расхождение</th>
                                <?php
                            };
                        } else {
                            if($against_value==1) {
                                ?>
                                    <th colspan='2' rowspan="2">Ожидалось</th>
                                <?php
                            } else {
                                ?>
                                    <th colspan='2'>Ожидалось</th>
                                <?php
                            };                            
                        };
                    ?>
                </tr>
                <tr>
                    <th>Номинал</th>
                    <?php 
                        foreach ($data['grade'] as $v) {
                            echo '<th>',$v,'</th>';
                        };
                    ?>
                    <th>Кол-во</th>
                    <th>Сумма</th>
                    <?php 
                        if($data['is_balanced']!=true) {
                            if ($against_value==1) {
                                ?>
                                    
                                <?php
                            } else {
                                ?>
                                    <th>Кол-во</th>
                                    <th>Сумма</th>
                                    <th>Кол-во</th>
                                    <th>Сумма</th>
                                <?php
                            };
                        } else {
                            if ($against_value==1) {
                                ?>
                                <?php
                            } else {
                                ?>
                                    <th>Кол-во</th>
                                    <th>Сумма</th>
                                <?php
                            };                            
                        };
                    ?>
                </tr>
                <?php 
                    foreach ($data['denom'][$key] as $k => $v) {
                        $sheets = 0;
                        $summ = 0;
                        echo '<tr>';
                        echo '<td align="center">',$v[0],'</td>';
                        for ($i=1; $i < count($data['grade'])+1; $i++) {
                           echo '<td align="right">',$v[$i],'</td>'; 
                           $sheets += $v[$i];
                           $summ += $v[$i]*$v[0];     
                        }
                        echo '<td align="right">',$sheets,'</td>';
                        echo '<td align="right">',str_replace(".",",",str_replace(",","",number_format($summ,2))),'</td>';
                        if ($against_value==1) {
                            echo '<td align="center"> - </td>';
                            echo '<td align="center"> - </td>';
                        } else {
                            echo '<td align="right">',$v[count($data['grade'])+1],'</td>';    
                            echo '<td align="right">',str_replace(".",",",str_replace(",","",number_format($v[count($data['grade'])+1]*$v[0],2))),'</td>';
                        };
                        if($data['is_balanced']!=true) {
                            if ($against_value==1) { 
                                echo '<td align="center"> - </td>';
                                echo '<td align="center"> - </td>';
                            } else {
                                echo '<td align="right">',$sheets-$v[count($data['grade'])+1],'</td>';
                                echo '<td align="right">',str_replace(".",",",str_replace(",","",number_format($summ - $v[count($data['grade'])+1]*$v[0],2))),'</td>';
                            };
                        };
                        echo '</tr>';
                        $total_processed_summ += $summ;
                        $total_expected_summ += $v[count($data['grade'])+1]*$v[0];
                        $total_discr_summ += $summ - $v[count($data['grade'])+1]*$v[0];
                    };
                ?>
                <tr>
                    <th>Итого:</th>
                    <td colspan="<?php echo count($data['grade']); ?>"></td>
                    <td align="right" colspan="2">
                            <?php echo str_replace(".",",",str_replace(","," ",number_format($total_processed_summ,2))); ?>
                    </td>
                    <td align="right" colspan="2">
                        <?php 
                            if ($against_value==1) {
                                echo str_replace(".",",",str_replace(","," ",number_format($estimated_summ,2))); 
                            } else {
                                echo str_replace(".",",",str_replace(","," ",number_format($total_expected_summ,2))); 
                            };
                        ?>
                    </td>
                    <?php 
                        if($data['is_balanced']!=true) {
                            if ($against_value==1) {
                                ?>
                                    <td align="right" colspan="2"><?php echo str_replace(".",",",str_replace(","," ",number_format($total_processed_summ-$estimated_summ,2))); ?></td>
                                <?php
                            } else {
                                ?>
                                    <td align="right" colspan="2"><?php echo str_replace(".",",",str_replace(","," ",number_format($total_discr_summ,2))); ?></td>
                                <?php
                            };
                        };
                    ?>
                </tr>
            </table>
        <?php
    };
    
    if (($data['comment_over']!='' 
            OR $data['comment_suspect']!='' 
            OR $data['comment_deficit']!='')
                AND $data['is_balanced']!=true) {
        $colspan = 0;
        if ($data['comment_over']!='') $colspan++;
        if ($data['comment_suspect']!='') $colspan++;
        if ($data['comment_deficit']!='') $colspan++;
        ?>
            <br/>
            <table class="report">
                <tr>
                    <th colspan="<?php echo $colspan; ?>">Комментарии к расхождениям</th>
                </tr>
                <tr>
                    <?php 
                        if($data['comment_over']) {
                            ?>  
                                <th>Излишки</th>          
                            <?php
                        };
                        if($data['comment_suspect']) {
                            ?>  
                                <th>Сомнительные</th>
                            <?php
                        };
                        if($data['comment_deficit']) {
                            ?>  
                                <th>Недостача</th>
                            <?php
                        };
                    ?>
                </tr>
                <tr>
                    <?php 
                        if($data['comment_over']) {
                            ?>  
                                <td><?php echo htmlfix($data['comment_over']);?></td>
                            <?php
                        };
                        if($data['comment_suspect']) {
                            ?>  
                                <td><?php echo htmlfix($data['comment_suspect']);?></td>
                            <?php
                        };
                        if($data['comment_deficit']) {
                            ?>  
                                <td><?php echo htmlfix($data['comment_deficit']);?></td>
                            <?php
                        };
                    ?>
                </tr>
            </table>            
        <?php 
            // Проверяем введены ли серийные номера банкнот 

            $serials = get_array_from_sql('
                SELECT
                    Currency.CurrSymbol,
                    `Denoms`.`Value`,
                    `SuspectSerialNumbs`.`LeftSeria`,
                    `SuspectSerialNumbs`.`LeftNumber`,
                    `SuspectSerialNumbs`.`RightSeria`,
                    `SuspectSerialNumbs`.`RightNumber`
                FROM 
                    `cashmaster`.`SuspectSerialNumbs`
                LEFT JOIN
                    Denoms ON Denoms.DenomId = `SuspectSerialNumbs`.`DenomId`
                LEFT JOIN
                    Currency ON Currency.CurrencyId = Denoms.CurrencyId
                WHERE
                    `SuspectSerialNumbs`.`DepositRecId`="'.addslashes($_REQUEST['id']).'"
                ORDER BY Denoms.CurrencyId,`Denoms`.`Value` ASC 
            ;');

            if(count($serials) > 0) {
                echo '<br/><h5 style="margin:0px;">Серийные номера сомнительных банкнот:</h5>';
                echo '<table class="report">';
                echo '<tr>';
                echo '<th>№</th><th>Валюта</th><th>Номинал</th><th>Серия слева</th><th>Номер слева</th><th>Серия справа</th><th>Номер справа</th>';
                echo '</tr>';
                foreach ($serials as $key=>$value) {
                    echo '<tr>';                    
                    echo '<td align="center">',($key+1),'</td>';
                    echo '<td align="center">',$value[0],'</td>';
                    echo '<td align="right">',$value[1],'</td>';
                    echo '<td align="center">',$value[2],'</td>';
                    echo '<td align="center">',$value[3],'</td>';
                    echo '<td align="center">',$value[4],'</td>';
                    echo '<td align="center">',$value[5],'</td>';
                    echo '</tr>';
                };
                echo '</table>';
            };
    };
?>
<?php

    //$action_name = 'data_report';
    //include 'app/view/reports/printed_0402145_complex_fordeposit.php';
    
    $b=  explode('|', $_SESSION[$program]['lang']['recon_report_buttons']);

?>
</div>
</div>
<div style="padding: 50px;"></div>        
<div class="container no-print navbar navbar-fixed-bottom" 
 style="background-color: white; padding: 20px;">
    <button onclick="window.close();" class="btn btn-primary btn-large">
        <?php echo htmlfix($b[0]); ?>
    </button>
    <?php 
        if (($data['comment_over']!='' 
            OR $data['comment_suspect']!='' 
            OR $data['comment_deficit']!='')
                AND $data['is_balanced']!=true) {
            ?>
            <a class="btn btn-warning btn-large" href="?c=act_by_recon?&id=<?php echo htmlfix($_GET['id']); ?>">
                <?php echo htmlfix($b[1]); ?>
            </a>
            <?php
        };
    ?>
</div>