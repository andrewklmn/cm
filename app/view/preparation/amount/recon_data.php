<?php

/*
 * Recon Data Total
 * 
 */
        if (!isset($c)) exit;
        $rashozhdenie = false;

?>
<script>
    function total_input_keyup(event) {
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
    function total_input_focus(elem) {
        $(elem).select();
        $(elem).css('background-color','cyan');
        $(elem.parentNode).css('background-color','cyan');        
    };
    function total_input_blur(elem) {
        elem.value=elem.value.replace(/[^0-9]/g,'');
        if (elem.value=='') elem.value=0;
        elem.value=parseInt(elem.value);
        if (elem.value==0) elem.value='';
        //if (elem.value==$(elem).attr('oldvalue')) {
            $(elem).css('background-color','#D0F0FF');
            $(elem.parentNode).css('background-color','#D0F0FF');
        //} else {
        //    $(elem).css('background-color','#A0F0FF');
        //    $(elem.parentNode).css('background-color','#A0F0FF');
        //};
        update_total_data(elem);
    };
    function update_total_data(elem) {
        if (elem.value!=$(elem).attr('oldvalue')) {
            $('td#suspect').hide();
            $('td#over').hide();
            $('td#deficit').hide();
            if (<?php echo ($scenario['ForceDepositDetails']==0
                    AND $_SESSION[$program]['UserConfiguration']['Blind']!=1
                    AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==3 )?'true':'false'; ?>) {
                $('div#discrepancies').hide();
            };
            $('input#control').hide();
            $('input#finish').show();
            var inputs = $(elem.parentNode).find('input');
            
            $.ajax({
                type: "POST",
                url: "?c=prep_update",
                async: false,
                data: {
                    deposit_rec_id: inputs[0].value,
                    denom_id: inputs[1].value,
                    value: elem.value,
                    oldvalue: ($(inputs[2]).attr('oldvalue')=='')?0:$(inputs[2]).attr('oldvalue'),
                    last_update_time: $('input#RecLastChangeDatetime')[0].value
                },
                error: function() {
                    alert("Connection error, Can't update.");
                    inputs[2].value = $(inputs[2]).attr('oldvalue');
                    remove_wait();
                },
                success: function(answer){
                    switch(answer[0]) {
                        case '0':
                            $(inputs[2]).attr('oldvalue',inputs[2].value);
                            answer = answer.substring(1);
                            var t = answer.split('|');
                            $('input#RecLastChangeDatetime').attr('value',t[0]);
                            $('input#rec_data_map').attr('value',t[1]);
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
                            inputs[2].value = t[0];
                            $(inputs[2]).attr('oldvalue',t[0]);
                            $(inputs[2]).css('color','red');
                            $('input#RecLastChangeDatetime').attr('value',t[1]);
                            //$('input#rec_data_map').attr('value',t[2]);
                            alert('Record was changed by another user');
                        break;
                        default:
                            inputs[2].value = $(inputs[2]).attr('oldvalue');
                            alert(answer);
                    };
                }
            });        
            recalc_total_data();
        };
    };
    function recalc_total_data() {

    };
</script>
<table class="hand_table" id="total" style="width:<?php echo $width*2; ?>px;">
    <colgroup>
        <col width="<?php echo $width; ?>px">
        <col width="<?php echo $width; ?>px">
        <col width="<?php echo $width; ?>px">
        <col width="<?php echo $width; ?>px">
    </colgroup>
    <tr>
        <?php 
            
            $t = explode('|', $_SESSION[$program]['lang']['total_table_header']);
            echo '<th>',htmlspecialchars($t[0]),'</th>';
            echo '<th>',htmlspecialchars($t[1]),'</th>';
        ?>
    </tr>
    <?php 
    
    foreach ($denoms as $denom) {
        if($denom[4]=='1') {
            echo '<tr style="background-color:LemonChiffon;" class="denom">';                    
        } else {
            echo '<tr class="denom">';
        };
        //echo '<tr class="denom">';
        echo '<td>',htmlfix((int)$denom[1]),'</td>';
        echo '<td class="data" style="padding:0px;margin:0px;">';
            ?><input type="hidden" name="deposit_rec_id" value="<?php 
                echo $DepositRecId;
            ?>"/>
            <input type="hidden" name="denom_id" value="<?php 
                echo $denom[0];
            ?>"/>
            <input 
                onkeyup="total_input_keyup(event);"
                onfocus="total_input_focus(this);"
                onblur="total_input_blur(this);"
                class="data" 
                type="text" 
                name="estimated" 
                value="<?php 
                    if (isset($denom[5]) AND $denom[5]>0) echo $denom[5];
                ?>"
                oldvalue="<?php 
                    if (isset($denom[5]) AND $denom[5]>0) echo $denom[5];
                ?>"/>
        <?php
        echo '</td>';
        echo '</tr>';
    };              
?>
</table>