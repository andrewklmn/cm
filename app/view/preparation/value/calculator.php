<?php

/*
 * Recon Data Total
 * 
 */
        if (!isset($c)) exit;

        $l = explode('|', $_SESSION[$program]['lang']['prep_value_labels']);
        
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
    table.info_table {

    }
    table.info_table th {
        padding:3px; 
        font-size:10px; 
        border: 2px solid black;
        overflow: hidden;
    }
    table.info_table td {
        padding:0px; 
        font-size:12px; 
        border: 1px solid gray;
        text-align: center;
        overflow: hidden;
    }
    table.info_table th {
        background-color: lightgray;
    }
</style>
<script>
    
    function cacl_keyup(elem){
        
    };
    function calc_keydown(elem){
        
    };
    function calc_keypress(elem){
        
    };
    function calc_focus(elem){
        
    };
    function calc_blur(elem){
        elem.value=elem.value.replace(/[^0-9]/g,'');
        recalc(elem);
    };
    function recalc(elem) {
        var currency = elem.parentNode.parentNode.parentNode.parentNode.parentNode;
        var summ = 0;
        $($(currency).find('input.data')).each(function(){
            if (this.value!='') {
                summ += parseInt(this.value) * parseFloat($(this.parentNode.parentNode).find('td')[0].innerHTML);
            };
        });
        var estimated = $(currency).find('input#estimated')[0];
        $(estimated).val(summ);
        $(estimated).blur();
    };
    
</script>
<td>
    <table class="hand_table" id="total" 
           style="<?php echo ($_SESSION[$program]['UserConfiguration']['Blind']==1
                                   AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==3)?'display:none;':''; ?>width:<?php echo $width*2; ?>px;">
        <colgroup>
            <col width="<?php echo $width; ?>px">
            <col width="<?php echo $width; ?>px">
            <col width="<?php echo $width; ?>px">
        </colgroup>
        <tr>
            <th colspan="2">
                <?php echo htmlfix($l[0]); ?>
            </th>
        </tr>
        <tr>
            <?php 

                //$t = explode('|', $_SESSION[$program]['lang']['total_table_header']);
                echo '<th>',htmlspecialchars($l[1]),'</th>';
                if ($_SESSION[$program]['UserConfiguration']['Blind']==1
                        AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==3) {
                    echo '<th style="display:none;">',htmlspecialchars($l[2]),'</th>';
                } else {
                    echo '<th>',htmlspecialchars($l[2]),'</th>';
                };
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
            echo '<td style="color:red;">';
            ?>
                <input 
                    onkeyup="cacl_keyup(event);"
                    onkeydown="calc_keydown(event);"
                    onkeypress="calc_keypress(event);"
                    onfocus="calc_focus(this);"
                    onblur="calc_blur(this);"
                    class="data span3" 
                    type="text" 
                    name="cull_count" 
                    value="<?php 
                        if (isset($recon_data[0]) AND $recon_data[0]>0) echo $recon_data[0];
                    ?>"
                    oldvalue="<?php 
                        if (isset($recon_data[0]) AND $recon_data[0]>0) echo $recon_data[0];
                    ?>"/>
            <?php
            echo '</td>';
            echo '</tr>';
        };              
    ?>
    </table>
