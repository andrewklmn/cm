<?php

/*
 * Recon data input form
 */

        if (!isset($c)) exit;
        
?>
<style>
    table.hand_table {
        table-layout: fixed;
    }
    table.hand_table th {
        padding:3px; 
        font-size:10px; 
        border: 2px solid black;
        overflow: hidden;
    }
    table.hand_table td {
        padding:0px; 
        font-size:12px; 
        border: 1px solid blue;
        text-align: center;
        overflow: hidden;
    }
    table.hand_table td.data {
        background-color: #D0F0FF;
    }
    table.hand_table th {
        background-color: lightgray;
    }
    input.data {
        text-align:center;
        height:19px;
        width:<?php echo $width-2; ?>px;
        padding:0px;
        margin:0px;
        border: 0px solid black;
        font-size: 14px;
        color:darkblue;
        font-weight: bold;
        background-color: #D0F0FF;
    }
</style>  
<script>
    function hand_input_keyup(event) {
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
    function hand_input_keypress(e) {
        var key = e.keyCode;
        switch (key){
            case 13:
                return false;
            break;
        };
    };
    function hand_input_keydown(e) {
        var key = e.keyCode;
        switch (key){
            case 13:
                return false;
            break;
        };
    };
    
    function hand_input_focus(elem) { 
        $(elem).select();
        $(elem).css('background-color','cyan');
        $(elem.parentNode).css('background-color','cyan');
    };
    function hand_input_blur(elem) {
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
        update_data(elem);
    };
    function update_data(elem){
        if (elem.value!=$(elem).attr('oldvalue')) {
            $('td#suspect').hide();
            $('td#over').hide();
            $('td#deficit').hide();
            if (<?php echo ( $scenario['ForceDepositDetails']==0
                    AND $_SESSION[$program]['UserConfiguration']['Blind']!=1
                    AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==3 )?'true':'false'; ?>) {
                $('div#discrepancies').hide();
            };
            $('input#control').hide();
            $('input#finish').show();
            var inputs = $(elem.parentNode).find('input');
            
            $.ajax({
                type: "POST",
                url: "?c=recon_update",
                async: false,
                data: {
                    deposit_rec_id: inputs[0].value,
                    denom_id: inputs[1].value,
                    grade_id: inputs[2].value,
                    cull_count: (inputs[3].value=='')?0:inputs[3].value,
                    oldvalue: ($(inputs[3]).attr('oldvalue')=='')?0:$(inputs[3]).attr('oldvalue'),
                    last_update_time: $('input#RecLastChangeDatetime')[0].value,
                    rec_data_map: $('input#rec_data_map')[0].value
                },
                error: function() {
                    alert("Connection error, Can't update.");
                    inputs[3].value = $(inputs[3]).attr('oldvalue');
                    remove_wait();
                },
                success: function(answer){
                    switch(answer[0]) {
                        case '0':
                            $(inputs[3]).attr('oldvalue',inputs[3].value);
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
                            inputs[3].value = t[0];
                            $(inputs[3]).attr('oldvalue',t[0]);
                            $(inputs[3]).css('color','red');
                            $('input#RecLastChangeDatetime').attr('value',t[1]);
                            $('input#rec_data_map').attr('value',t[2]);
                            alert('Record was changed by another user');
                        break;
                        default:
                            inputs[3].value = $(inputs[3]).attr('oldvalue');
                            alert(answer);
                    };
                }
            });        
            recalc_total();
        };
    };
    function recalc_total() {
        var currency = $('tr.currency');
        var sverka = $('tr.sverka_currency');
        for (var c=0; c < currency.length; c++){
            var real_summ = 0;
            var real_banknote = 0;
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
                        // подозрительная банкнота
                        if (recon_summ[j].value!='') {
                            summa += parseInt(recon_summ[j].value);
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
                if (summa > 0) {
                    $(total_summ[2]).html(parseInt(summa));
                } else {
                    $(total_summ[2]).html('');
                };
                if ((parseInt(summa)-parseInt(estimated)) !== 0) {
                    $(total_summ[3]).html(parseInt(summa)-parseInt(estimated));
                } else {
                    $(total_summ[3]).html('');
                };
                real_summ += summa*denom;
                real_banknote += summa;
            };
        };
    };
</script>
<table class="hand_table" id="hand" style="width:<?php echo $width+count($recon_grades)*$width; ?>px;">
    <colgroup>
        <col width="<?php echo $width; ?>px">
        <?php 
            foreach ($recon_grades as $grade) {
                echo '<col width="'.$width.'px">';
            };
        ?>
    </colgroup>
    <tr>
        <th colspan="<?php 
            echo count($recon_grades)+1;
        ?>"><?php echo htmlfix($_SESSION[$program]['lang']['recon_input_data']); ?></th>
    </tr>
    <tr>
        <th><?php echo htmlfix($_SESSION[$program]['lang']['denom']); ?></th>
            <?php 
                foreach ($recon_grades as $grade) {
                    echo '<th style="width:'.$width.'px;">',$grade[2],'</th>';
                };
            ?>
    </tr>
    <?php 

        foreach ($denoms as $dk=>$denom) {
            if($denom[4]=='1') {
                echo '<tr style="background-color:LemonChiffon;" class="denom">';                    
            } else {
                echo '<tr class="denom">';
            };
            echo '<td>',htmlfix((int)$denom[1]),'</td>';
            $vsego = 0;
            foreach ($recon_grades as $grade) {
                $recon_data=  fetch_row_from_sql('SELECT
                        `ReconAccountingData`.`CullCount`
                    FROM 
                        `cashmaster`.`ReconAccountingData`
                    WHERE 
                        `ReconAccountingData`.`DepositRecId` = "'.addslashes($DepositRecId).'"
                        AND `ReconAccountingData`.`DenomId`="'.$denom[0].'"
                        AND `ReconAccountingData`.`GradeId`="'.$grade[0].'"
                ;');
                
                ?><td class="data" style="padding:0px;margin:0px;">
                    <input type="hidden" name="deposit_rec_id" value="<?php 
                        echo $DepositRecId;
                    ?>"/>
                    <input type="hidden" name="denom_id" value="<?php 
                        echo $denom[0];
                    ?>"/>
                    <input type="hidden" name="grade_id" value="<?php 
                        echo $grade[0];
                    ?>"/>
                    <input 
                        onkeyup="hand_input_keyup(event);"
                        onkeydown="hand_input_keydown(event);"
                        onkeypress="hand_input_keypress(event);"
                        onfocus="hand_input_focus(this);"
                        onblur="hand_input_blur(this);"
                        class="data" 
                        type="text" 
                        name="cull_count" 
                        value="<?php 
                            if (isset($recon_data[0]) AND $recon_data[0]>0) echo $recon_data[0];
                        ?>"
                        oldvalue="<?php 
                            if (isset($recon_data[0]) AND $recon_data[0]>0) echo $recon_data[0];
                        ?>"/>
                    </td>
                <?php
                if ($grade[0]==1) {
                    if (isset($recon_data[0]) AND $recon_data[0]>0) {
                        $denoms[$dk][6] += (int)$recon_data[0];
                        $vsego_deneg += $denom[1]*(int)$recon_data[0];
                        $vsego_banknot += $recon_data[0];
                    };
                } else {
                    if (isset($recon_data[0]) AND $recon_data[0]>0) {
                        $denoms[$dk][6] += (int)$recon_data[0];
                        $vsego_deneg += $denom[1]*(int)$recon_data[0];
                        $vsego_banknot += $recon_data[0];
                    };  
                };
            };
            echo '</tr>';
        };              
    ?>
</table>