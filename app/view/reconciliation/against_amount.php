<?php

/*
 * Сверка по количеству
 */

    if (!isset($c)) exit;

?>
<script>
            function recon(elem) {
                
                    if (<?php echo ($scenario['ForceDepositDetails']==1)?'true':'false'; ?>) {
                        // Проверяем введены ли реквизиты, если нет, то сообщаем что
                        // их нужно внести
                        var stat=$('.stat:visible');
                        var flag = true;
                        $(stat).each(function(){
                            if (this.value=='') {
                                $(this).css('background-color','yellow');
                                flag = false;
                            };
                        });
                        if(flag==false) {
                           alert('<?php echo htmlfix($_SESSION[$program]['lang']['fill_required_fields']) ?>');
                           return true;
                        };
                    };
            
                    var currency = $('tr.currency');
                    var currency_name = $('input.currency_name');
                    var sverka = $('tr.sverka_currency');

                    var over = [];
                    var deficit = [];
                    var suspect = [];

                    for (var c=0; c < currency.length; c++){
                        var sorter_trs = $($(currency[c]).find('table#sorter')).find('tr.denom');
                        var recon_trs = $($(currency[c]).find('table#hand')).find('tr.denom');
                        var total_trs = $($(currency[c]).find('table#total')).find('tr.denom');
                        for (var i=0; i<total_trs.length; i++) {
                            var summa = 0;
                            var denom = parseInt(sorter_trs[i].firstChild.innerHTML);
                            var sorter_summ = $(sorter_trs[i]).find('td.data');
                            if(sorter_summ[0].innerHTML!='') {
                                summa += parseInt(sorter_summ[0].innerHTML);
                            };
                            var recon_summ = $(recon_trs[i]).find('input.data');
                            for (var j=0; j<recon_summ.length; j++) {
                                var inputs = $(recon_summ[j].parentNode).find('input');
                                if (inputs[2].value=='1') {
                                    if(inputs[3].value!=='') {
                                        // подозрительная банкнота
                                        suspect[suspect.length]=[
                                            currency_name[c].value,denom,inputs[3].value
                                        ];
                                        if (recon_summ[j].value!='') {
                                            summa += parseInt(recon_summ[j].value);
                                        };
                                    };
                                } else {
                                    // нормальная банкнота
                                    if (recon_summ[j].value!='') {
                                        summa += parseInt(recon_summ[j].value);
                                    };
                                }
                            };

                            var total_summ = $(total_trs[i]).find('td');
                            var estimated = $(total_summ[1]).find('input.data')[0].value;
                            if (estimated=='') estimated=0;
                            if ((parseInt(summa)-parseInt(estimated)) > 0) {
                                // излишек
                                over[over.length]=[
                                    currency_name[c].value, denom, parseInt(summa)-parseInt(estimated)
                                ];
                            } else {
                                if ((parseInt(summa)-parseInt(estimated)) < 0) {
                                    // недостача
                                    deficit[deficit.length]=[
                                        currency_name[c].value, denom, parseInt(estimated)-parseInt(summa)
                                    ];
                                };
                            };

                        };
                    };
                    
                    if (suspect.length>0 || over.length>0 || deficit.length>0 ) {
                        if(suspect.length>0) {
                            $('table#suspect > tbody > tr.d').remove();
                            for (var i=0; i< suspect.length; i++) {
                                $('table#suspect > tbody').append('<tr class="d"><td>' + suspect[i][0] 
                                            + '</td><td>' + suspect[i][1] + '</td><td>' 
                                            + suspect[i][2] + '</td></tr>')
                            };
                            $('td#suspect').show();
                        }
                        if(over.length>0 ) {
                            $('table#over > tbody > tr.d').remove();
                            for (var i=0; i< over.length; i++) {
                                $('table#over > tbody').append('<tr class="d"><td>' + over[i][0] 
                                            + '</td><td>' + over[i][1] + '</td><td>' 
                                            + over[i][2] + '</td></tr>');
                            };
                            if (<?php echo ($_SESSION[$program]['UserConfiguration']['Blind']==1)?'true':'false'; ?>) {
                                $('td#over').hide();
                            } else {
                                $('td#over').show();
                            };
                        }
                        if(deficit.length>0) {
                            $('table#deficit > tbody > tr.d').remove();
                            for (var i=0; i< deficit.length; i++) {
                                $('table#deficit > tbody').append('<tr class="d"><td>' + deficit[i][0] 
                                            + '</td><td>' + deficit[i][1] + '</td><td>' 
                                            + deficit[i][2] + '</td></tr>');
                            };
                            if (<?php echo ($_SESSION[$program]['UserConfiguration']['Blind']==1)?'true':'false'; ?>) {
                                $('td#deficit').hide();
                            } else {
                                $('td#deficit').show();
                            };
                        };
                        if (<?php echo ($_SESSION[$program]['UserConfiguration']['Blind']==1)?'true':'false'; ?>) {
                            if(suspect.length>0) {
                                $('div#discrepancies').show();
                                $('input#finish').hide();
                                $('input#control').show();                                
                            } else {
                                $('div#discrepancies').show();
                                $('input#finish').show();
                                control(elem);
                            };
                        } else {
                            $('div#discrepancies').show();
                            $('input#finish').hide();
                            $('input#control').show();
                        };
                       //=========================================
                    } else {
                        reconcile_deposit();
                    };
                }
                
                function control(elem) {
                   var stat=$('.stat:visible');
                   var flag = true;
                   $(stat).each(function(){
                       if (this.value=='') {
                           $(this).css('background-color','yellow');
                           flag = false;
                       };
                   });
                   if(flag==false) {
                       alert('<?php echo htmlfix($_SESSION[$program]['lang']['fill_required_fields']) ?>');
                   } else {
                        $.ajax({
                            type: "POST",
                            url: "?c=recon_to_control",
                            async: false,
                            data: {
                                deposit_rec_id: "<?php echo $DepositRecId; ?>",
                                last_update_time: $('input#RecLastChangeDatetime')[0].value,
                                rec_data_map: $('input#rec_data_map')[0].value
                            },
                            error: function() {
                                alert("Connection error, Can't reconcile.");
                                inputs[3].value = $(inputs[3]).attr('oldvalue');
                                remove_wait();
                            },
                            success: function(answer){
                                switch(answer[0]) {
                                    case '0':
                                        back_to_workflow();
                                    break;
                                    case '2':
                                        alert('Deposit was reconciled by another user');
                                        back_to_workflow();
                                    break;
                                    case '3':
                                        alert('Deposit was changed by another user');
                                        window.location.reload();
                                    break;
                                    case '4':
                                        alert('Deposit has ambiguos indexes');
                                        window.location.reload();
                                    break;
                                    case '5':
                                        alert('SorterData was updated');
                                        window.location.reload();
                                    break;
                                    case '6':
                                        window.location.href='?c=suspect_enter&deposit_rec_id=' + '<?php echo htmlfix($DepositRecId); ?>';
                                    break;
                                    default:
                                        alert(answer);
                                };
                            }
                        });
                   };
                };
                function recon_with_discrep() {
                   var stat=$('.stat:visible');
                   var flag = true;
                   $(stat).each(function(){
                       if (this.value=='') {
                           $(this).css('background-color','yellow');
                           flag = false;
                       };
                   });
                   if(flag==false) {
                       alert('<?php echo htmlfix($_SESSION[$program]['lang']['fill_required_fields']) ?>');
                       return true;
                   };
                    $.ajax({
                        type: "POST",
                        url: "?c=recon_reconcile_with_discrep",
                        async: false,
                        data: {
                            deposit_rec_id: "<?php echo $DepositRecId; ?>",
                            last_update_time: $('input#RecLastChangeDatetime')[0].value,
                            rec_data_map: $('input#rec_data_map')[0].value
                        },
                        error: function() {
                            alert("Connection error, Can't reconcile.");
                            inputs[3].value = $(inputs[3]).attr('oldvalue');
                            remove_wait();
                        },
                        success: function(answer){
                            switch(answer[0]) {
                                case '0':
                                    back_to_workflow();
                                break;
                                case '2':
                                    alert('Deposit was reconciled by another user');
                                    back_to_workflow();
                                break;
                                case '3':
                                    alert('Deposit was changed by another user');
                                    window.location.reload();
                                break;
                                case '4':
                                    alert('Deposit has ambiguos indexes');
                                    window.location.reload();
                                break;
                                case '5':
                                    alert('SorterData was updated');
                                    window.location.reload();
                                break;
                                case '6':
                                    window.location.href='?c=suspect_enter&deposit_rec_id=' + '<?php echo htmlfix($DepositRecId); ?>';
                                break;
                                case '1':
                                    alert('Record was changed by another user');
                                    window.location.reload()
                                break;
                                default:
                                    alert(answer);
                            };
                        }
                    });
                };
                function reconcile_deposit(){
                
                    var stat=$('.stat:visible');
                    var flag = true;
                    $(stat).each(function(){
                       if (this.value=='') {
                           $(this).css('background-color','yellow');
                           flag = false;
                       };
                    });
                    if(flag==false) {
                       alert('<?php echo htmlfix($_SESSION[$program]['lang']['fill_required_fields']) ?>');
                       return true;
                    };
                    
                    $.ajax({
                        type: "POST",
                        url: "?c=recon_reconcile",
                        async: false,
                        data: {
                            deposit_rec_id: "<?php echo $DepositRecId; ?>",
                            last_update_time: $('input#RecLastChangeDatetime')[0].value,
                            rec_data_map: $('input#rec_data_map')[0].value
                        },
                        error: function() {
                            alert("Connection error, Can't reconcile.");
                            inputs[3].value = $(inputs[3]).attr('oldvalue');
                            remove_wait();
                        },
                        success: function(answer){
                            switch(answer[0]) {
                                case '0':
                                    back_to_workflow();
                                break;
                                case '2':
                                    alert('Deposit was reconciled by another user');
                                    back_to_workflow();
                                break;
                                case '3':
                                    alert('Deposit was changed by another user');
                                    window.location.reload();
                                break;
                                case '4':
                                    alert('Deposit has ambiguos indexes');
                                    window.location.reload();
                                break;
                                case '5':
                                    alert('SorterData was updated');
                                    window.location.reload();
                                break;
                                case '1':
                                    alert('Record was changed by another user');
                                    window.location.reload()
                                break;
                                default:
                                    alert(answer);
                            };
                        }
                    });        
                }
</script>
<div class="container">
    <input type="hidden" id="RecLastChangeDatetime" value="<?php echo $DepositRec['RecLastChangeDatetime']; ?>"/>
    <input type="hidden" id="rec_data_map" value="<?php echo get_recon_data_map_by_rec_id($DepositRecId); ?>"/>
    <h3 style="color:darkblue;padding: 0px;margin: 0px;">
        <?php echo htmlfix($_SESSION[$program]['lang']['recon_by_card_number'].$_GET['separator_id']); ?>
        <font style="font-size:12px;">(<?php 
                echo htmlfix(get_post_and_short_fio_by_user_id($DepositRec_last_operator_id)); 
            ?>)</font>
    </h3>
    <?php

        $sorter_data_is_ok = true;

        // Переподвязываем вновьпоступившие депозиты с таким номером карты
        rebind_deposits_to_recon($_GET['separator_id'],$DepositRecId);

        // Проверяем данные пересчета
        include './app/controller/common/reconciliation/check_indexes.php';
        include './app/controller/common/reconciliation/check_single_denom.php';
        include './app/controller/common/reconciliation/check_new_valuables.php';
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
                                        echo ($_SESSION[$program]['UserConfiguration']['Blind']==1
                                                AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==3)?'display:none;':''; 
                                    ?>vertical-align: top;padding-right:30px;"><?php
                                        include './app/view/reconciliation/amount/sorter_data_form.php';
                                    ?></td>
                                    <?php 
                                    if($sorter_grades_is_ok==true 
                                            AND $sorter_data_is_ok==true
                                            AND $recon_grades_is_ok==true) {
                                    ?>
                                        <td style="vertical-align: top;padding-right:30px;"><?php 
                                            include './app/view/reconciliation/amount/recon_data_input_form.php';
                                        ?></td>
                                        <td style="vertical-align: top;"><?php 
                                            include './app/view/reconciliation/amount/recon_data_total.php';
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
        include './app/view/reconciliation/amount/discrepancies.php';
        include './app/view/reconciliation/buttons.php';
        include './app/view/set_rs_to_stat.php';
        include './app/view/reconciliation/keyboard_driver.php';
    ?>
</div>

