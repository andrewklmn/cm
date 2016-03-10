<?php

/*
 * Reconciliation total summ by currency
 */

        if (!isset($c)) exit;

        $row= fetch_row_from_sql('
            SELECT
                SUM(`DepositDenomTotal`.`ExpectedCount`)
            FROM `cashmaster`.`DepositDenomTotal`
            LEFT JOIN
                Denoms ON Denoms.DenomId = `DepositDenomTotal`.`DenomId`
            LEFT JOIN
                Currency ON Currency.CurrencyId=Denoms.CurrencyId
            WHERE
                `DepositDenomTotal`.`DepositReclId`="'.addslashes($DepositRecId).'"
                AND Currency.CurrencyId="'.addslashes($currency[0]).'"
        ;');
        $expected_banknotes = (count($row)>0) ? $row[0]:0;


        $row= fetch_row_from_sql('
            SELECT
                SUM(`DepositCurrencyTotal`.`ExpectedDepositValue`)
            FROM 
                `cashmaster`.`DepositCurrencyTotal`
            WHERE
                `DepositCurrencyTotal`.`DepositRecId`="'.addslashes($DepositRecId).'"
                AND `DepositCurrencyTotal`.`CurrencyId`="'.  addslashes($currency[0]).'"
        ;');
        $expected_summ = (count($row)>0) ? $row[0]:0;




        if($sorter_grades_is_ok==true AND $sorter_data_is_ok==true) {
            $colspan = 'colspan="3"';
        } else {
            $colspan = '';
        };
        
        if ((($expected_summ-$vsego_deneg)==0 AND ($expected_banknotes-$vsego_banknot)==0)
                OR $rashozhdenie == false) {
            $display_stat='display:none;';
            $display_banknot = 'display:none;';
            $display_deneg = 'display:none;';
        } else {
            $display_button=false;
            $display_stat='';
            $display_banknot = '';
            $display_deneg = '';
        };
        
?>
<tr class="sverka_currency">
    <td <?php echo $colspan; ?> align="left">
            <?php 
                if($sorter_grades_is_ok==true AND $sorter_data_is_ok==true) {
                    ?>
                    <style>
                        input.sverka {
                            width:100px;
                            background-color: #D0F0FF;
                            text-align: center;
                            font-size: 14px;
                            color: darkblue;
                            margin: 0px;
                            padding: 0px;
                            font-weight: bold;
                            height:19px;
                        }
                        table.stat th{
                            text-align: center;
                            padding: 0px;
                        }
                    </style>
                    <script>
                        function stat_keyup(event) {
                            var key = event.keyCode;
                            var elem = ( event.target ) ? event.target : event.srcElement;
                            switch(key){
                                        case 27:
                                            elem.value = $(elem).attr('oldvalue');                    
                                        break;
                                        case 13:
                                            $(elem).blur();
                                            $(elem).focus();
                                            $(elem).select();
                                        break;
                                        default:
                            };                                    
                        };
                        function stat_blur(elem) {
                            elem.value = elem.value.replace(',','.');
                            elem.value = elem.value.replace(/[^0-9\.]/g,'');
                            if (elem.value=='') elem.value=0;
                            elem.value=parseFloat(elem.value).toFixed(2).replace(',', '').replace('.',',');
                            elem.value=elem.value.replace('.',',');
                            if (elem.value==0) elem.value='';
                            update_stat_data(elem);
                        };
                        function update_stat_data(elem){
                            if (elem.value!=$(elem).attr('oldvalue')) {
                            var inputs = $(elem.parentNode).find('input');
                            $.ajax({
                                type: "POST",
                                url: "?c=recon_stat_update",
                                async: false,
                                data: {
                                    deposit_rec_id: inputs[1].value,
                                    currency_id: inputs[2].value,
                                    value: inputs[0].value,
                                    oldvalue: ($(inputs[0]).attr('oldvalue')=='')?0:$(inputs[0]).attr('oldvalue')
                                },
                                error: function() {
                                    alert("Connection error, Can't update.");
                                    inputs[0].value = $(inputs[0]).attr('oldvalue');
                                    remove_wait();
                                },
                                success: function(answer){
                                    switch(answer[0]) {
                                        case '0':
                                            $(inputs[0]).attr(
                                                    'oldvalue',
                                                    inputs[0].value
                                                );
                                             $(inputs[0]).css('color','darkblue');
                                        break;
                                        case '1':
                                            answer = answer.substring(1);
                                            inputs[0].value = parseFloat(answer).toFixed(2).replace(',', '').replace('.',',');
                                            $(inputs[0]).attr('oldvalue',parseFloat(answer).toFixed(2).replace(',', '').replace('.',','));
                                            $(inputs[0]).css('color','red');
                                            alert('Record was changed by another user');
                                        break;
                                        default:
                                            inputs[0].value = $(inputs[0]).attr('oldvalue');
                                            alert(answer);
                                    };
                                }
                            });        
                            recalc_stat_data();
                            };
                        };
                        function recalc_stat_data(){
                            var currency = $('tr.currency');
                            var sverka = $('tr.sverka_currency');
                            var button_sverka = true;
                            for (var c=0; c < currency.length; c++){
                                var banknot = parseInt($(sverka[c]).find('td#expected_banknotes')[0].innerHTML);
                                var deneg = parseFloat($(sverka[c]).find('input#expected_summ')[0].value.replace(',','.'));
                                var real_banknote = parseInt($(sverka[c]).find('span.banknot')[0].innerHTML);
                                var real_summ = parseFloat($(sverka[c]).find('span.total')[0].innerHTML.replace(',','.'));
                                
                                var delta_amount = $(sverka[c]).find('td#delta_amount')[0];
                                var delta_summ = $(sverka[c]).find('td#delta_summ')[0];
                                
                                if ((real_banknote - banknot)!==0) {
                                    $(delta_amount).html(
                                        parseInt(real_banknote - banknot)
                                    );
                                    $(delta_amount.parentNode).show();
                                } else {
                                    $(delta_amount).html('');
                                    $(delta_amount.parentNode).hide();
                                };

                                if ((real_summ - deneg)!==0) {
                                    $(delta_summ).html(
                                        (real_summ - deneg).toFixed(2).replace(',', '').replace('.',',')
                                    );
                                    $(delta_summ.parentNode).show();
                                } else {
                                    $(delta_summ).html('');
                                    $(delta_summ.parentNode).hide();
                                };
                                if($(delta_amount.parentNode).css('display')=='none'
                                    && $(delta_summ.parentNode).css('display')=='none') {
                                    $(delta_amount.parentNode.parentNode.parentNode).hide();
                                    
                                } else {
                                    $(delta_amount.parentNode.parentNode.parentNode).show();
                                    button_sverka = false;
                                };
                            };
                            if(button_sverka) {
                                $('button#finish').show();
                                $('button#finish2').hide();
                            } else {
                                $('button#finish').hide();
                                $('button#finish2').show();
                            };
                        }
                    </script>
                        <table style="<?php echo $display_stat; ?>margin-top: 20px;" class="info_table stat">
                            <tr>
                                <th style="width:150px;">Итоги</th>
                                <th>Ожидаемое</th>
                                <th>Реальное</th>
                                <th>Расхождение</th>
                                <!--
                                <th>Подробности</th>
                                -->
                            </tr>
                            <tr class="sverka" style="<?php echo $display_banknot; ?>">
                                <th>Общее кол-во банкнот, шт</th>
                                <td id="expected_banknotes" style="font-size: 14px;margin: 0px;padding: 0px;">
                                    <?php echo $expected_banknotes; ?>
                                </td>
                                <td style="width:120px;font-size: 14px;"><span class="banknot"><?php echo $vsego_banknot; ?></span></td>
                                <td id="delta_amount" style="color:red;width:120px;font-size: 14px;"><?php echo $vsego_banknot-$expected_banknotes; ?></td>
                                    <!--
                                <td style="width:250px;margin: 0px;padding: 0px;">
                                    <textarea 
                                        id="comments"
                                        style="font-size:10px;margin: 0px;height:36px;width:300px;background-color: #D0F0FF;" 
                                        class="sverka"
                                        name="" ><?php 
                                            if (($expected_summ-$vsego_deneg)!=0 OR ($vsego_banknot-$expected_banknotes)!=0)
                                            //echo 'Я невиноватая. Это Юлька из соседнего отдела украла, наверное...'; ?></textarea>
                                </td>
                                    -->
                            </tr>
                            <tr class="sverka" style="<?php echo $display_banknot; ?>">
                                <th>Общая сумма, <?php echo $currency[1],' ',$currency[3]; ?></th>
                                <td style="background-color: #D0F0FF;margin: 0px;padding: 0px;"><input 
                                        id="expected_summ"
                                        style="text-align: center;"
                                        class="sverka" 
                                        type="text" 
                                        onblur="stat_blur(this);"
                                        onkeyup="stat_keyup(event);"
                                        onfocus="$(this).select();"
                                        name="estimated_summ" 
                                        value="<?php echo str_replace('.',',',str_replace(',', '', number_format($expected_summ, 2))); ?>"
                                        oldvalue="<?php echo str_replace('.',',',str_replace(',', '', number_format($expected_summ, 2))); ?>"
                                        />
                                    <input type="hidden" name="deposit_rec_id" value="<?php echo $DepositRecId; ?>"/>
                                    <input type="hidden" name="currency_id" value="<?php echo $currency[0]; ?>"/>
                                </td>

                                <td style="width:120px;font-size: 14px;text-align: center;"><span class="total"><?php 
                                echo str_replace('.',',',str_replace(',', '', number_format($vsego_deneg, 2))); 
                            ?></span></td>
                                <td id="delta_summ" style="color:red;width:120px;font-size: 14px;">
                                    <?php echo str_replace('.',',',str_replace(',', '', number_format($vsego_deneg - $expected_summ, 2))); ?>
                                </td>
                            <!--
                                <td style="width:250px;margin: 0px;padding: 0px;">
                                    <textarea 
                                        id="comments"
                                        style="font-size:10px;margin: 0px;height:36px;width:300px;background-color: #D0F0FF;" 
                                        class="sverka"
                                        name="" ><?php 
                                            //echo 'Я невиноватая. Это Юлька из соседнего отдела украла, наверное...'; 
                                        ?></textarea>
                                </td>
                                    -->
                            </tr>
                        </table>
                    <?php
                };
            ?>
    </td>
</tr>