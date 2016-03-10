<?php

/*
 * result and estimated data
 * 
 */
        if (!isset($c)) exit;
        
        $s = explode('|',$_SESSION[$program]['lang']['sum_and_total']);
        
        
        // Получаем текущее значение ожидаемой суммы
        $row = fetch_row_from_sql('
            SELECT
                `DepositCurrencyTotal`.`ExpectedDepositValue`
            FROM 
                `cashmaster`.`DepositCurrencyTotal`
            WHERE
                `DepositCurrencyTotal`.`DepositRecId`="'.addslashes($DepositRecId).'"
                AND `DepositCurrencyTotal`.`CurrencyId`="'.addslashes($currency[0]).'"
        ;');
        
        
        if(isset($row[0])) {
            $expected = $row[0];    
        } else {
            $expected = 0;
        };
?>
<script>
function blur_estimated( elem ){
        elem.value=elem.value.replace(/[^0-9\.]/g,'');
        if (elem.value=='') elem.value=0;
        elem.value = parseFloat(elem.value).toFixed(2);
        if (elem.value!=$(elem).attr('oldvalue')) {
            // Обновляем ожидаемое значение в таблице базы
            $.ajax({
                type: "POST",
                url: "?c=prep_expected_update",
                async: false,
                data: {
                    deposit_rec_id: "<?php echo $DepositRecId; ?>",
                    currency_id: "<?php echo $currency[0]; ?>",
                    expected: elem.value,
                    oldvalue: $(elem).attr('oldvalue'),
                    last_update_time: $('input#RecLastChangeDatetime')[0].value
                },
                error: function() {
                    alert("Connection error, Can't update.");
                    inputs[3].value = $(inputs[3]).attr('oldvalue');
                    remove_wait();
                },
                success: function(answer){
                    switch(answer[0]) {
                        case '0':
                            $(elem).attr('oldvalue', elem.value);
                            answer = answer.substring(1);
                            var t = answer.split('|');
                            $('input#RecLastChangeDatetime').attr('value',t[0]);
                            //$('input#rec_data_map').attr('value',t[1]);
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
                            answer = answer.substring(1);
                            var t = answer.split('|');
                            t[0] = parseFloat(t[0]).toFixed(2);
                            elem.value = t[0];
                            $(elem).attr('oldvalue',t[0]);
                            $(elem).css('color','red');
                            $('input#RecLastChangeDatetime').attr('value',t[1]);
                            //$('input#rec_data_map').attr('value',t[2]);
                            alert('Record was changed by another user');
                        break;
                        default:
                            elem.value = $(elem).attr('value');
                            alert(answer);
                    };
                }
            });
        };
    };
</script>
        <br/>
        <?php echo htmlspecialchars($l[3]); ?>: 
        <input type="text" 
               id="estimated"
               style="
                    background-color: #D0F0FF;
                    color:darkblue;
                    font-weight: bold;
                    text-align: right;
                    border-color: darkblue;
                "
               onblur="blur_estimated(this);"
               onclick="$(this).css('color','darkblue');"
               class="span2 search-query" name="estimated" 
               oldvalue="<?php echo $expected; ?>"
               value="<?php echo $expected; ?>"/>
        <?php echo htmlfix($currency[1]); ?>
    </td>