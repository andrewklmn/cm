<?php

/*
 * Discrepancies table
 */
     
        if (!isset($c)) exit;
        
        $client_id = ($DepositRec['CustomerId']=="0")?'':$DepositRec['CustomerId'];
        
        // Получаем список клиентов
        $clients = get_array_from_sql('
            SELECT
                `Customers`.`CustomerId`,
                `Customers`.`CustomerCode`,
                `Customers`.`CustomerName`                
            FROM 
                `cashmaster`.`Customers`
            ORDER BY `Customers`.`CustomerCode` ASC
        ;');
        
        // проверяем заполнялись ли акты расхождений
        include './app/controller/common/reconciliation/check_discr_form_fill.php';

?>
<link rel="stylesheet" type="text/css" href="bootstrap/css/datepicker.css"> 
<script type="text/javascript" src="bootstrap/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap-datepicker.ru.js"></script>
<script>
    function update_client_id(elem) {
        if (elem.id=="client_name") $("select#client_code").val(elem.value);
        if (elem.id=="client_code") $("select#client_name").val(elem.value);
    };
    
    function update_recon_discr(elem) {
        var inputs = $('table.stat_table').find('.stat');
        var values = [];
        var oldvalues = [];
        for(var i=1; i<inputs.length; i++) {
            values[values.length] = $(inputs[i]).val(); 
            oldvalues[oldvalues.length] = $(inputs[i]).attr('oldvalue'); 
        };
        $.ajax({
            type: "POST",
            url: "?c=recon_discr_update",
            async: false,
            data: {
                deposit_rec_id: "<?php echo $DepositRecId; ?>",
                last_update_time: $('input#RecLastChangeDatetime')[0].value,
                rec_data_map: $('input#rec_data_map')[0].value,
                values: values.join("|"),
                oldvalues: oldvalues.join("|")
            },
            error: function() {
                alert("Connection error, Can't reconcile.");
                remove_wait();
            },
            success: function(answer){
                switch(answer[0]) {
                    case '0':
                        answer = answer.substring(1);
                        var t = answer.split('|');
                        $(inputs[0]).val(values[0]);
                        for(var i=1; i<inputs.length; i++) {
                            $(inputs[i]).val( values[i-1]);
                            $(inputs[i]).attr('oldvalue', values[i-1]);
                        };
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
                        alert('Record was changed by another user');    
                        answer = answer.substring(1);
                        var t = answer.split('|||');
                        var new_value = t[0].split('|');
                        $(inputs[0]).val(new_value[0]);
                        $(inputs[0]).css('color','red');
                        for(var i=1; i<inputs.length; i++) {
                            $(inputs[i]).val(new_value[i-1]);
                            $(inputs[i]).attr('oldvalue',new_value[i-1]);
                            $(inputs[i]).css('color','red');
                        };
                        t = t[1].split('|');
                        $('input#RecLastChangeDatetime').attr('value',t[0]);
                        $('input#rec_data_map').attr('value',t[1]);
                    break;
                    default:
                        alert(answer);
                };
            }
        });
    };
    
    function act_update(elem,type) {
        if(elem.value==$(elem).attr('oldvalue')) return true;
        $.ajax({
            type: "POST",
            url: "?c=recon_act_update",
            async: false,
            data: {
                deposit_rec_id: "<?php echo $DepositRecId; ?>",
                last_update_time: $('input#RecLastChangeDatetime')[0].value,
                rec_data_map: $('input#rec_data_map')[0].value,
                discr_type: type,
                discr_comment: elem.value,
                discr_oldvalue: $(elem).attr('oldvalue')
            },
            error: function() {
                alert("Connection error, Can't reconcile.");
                
                remove_wait();
            },
            success: function(answer){
                switch(answer[0]) {
                    case '0':
                        answer = answer.substring(1);
                        var t = answer.split('|');
                        $(elem).attr('oldvalue',elem.value);
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
                        alert('Field was changed by another user');    
                        answer = answer.substring(1);
                        var t = answer.split('|');
                        elem.value = t[0];
                        $(elem).attr('oldvalue',elem.value);
                        $(elem).css('color','red');
                        $('input#RecLastChangeDatetime').attr('value',t[1]);
                        $('input#rec_data_map').attr('value',t[2]);
                    break;
                    default:
                        alert(answer);
                };
            }
        });
    };
</script>
<?php 
    $t = explode('|', $_SESSION[$program]['lang']['discrepancy_tables']);
    $h = explode('|', $_SESSION[$program]['lang']['discrepancy_tables_headers']);
    $d = explode('|', $_SESSION[$program]['lang']['discrepancy_form_labels']);
    $s = explode('|', $_SESSION[$program]['lang']['discrepancy_states']);
    
?>
<div id="discrepancies" 
     class="alert alert-error" 
     style="<?php 
        if ($scenario['ForceDepositDetails']==0
                AND $_SESSION[$program]['UserConfiguration']['Blind']==0
                AND $_SESSION[$program]['UserConfiguration']['UserRoleId']==3) {
                
             echo 'display:none;';
        };
     ?>width:<?php echo $width*10; ?>px;">
    <table class="discrepancies" style="">
        <tr>
            <td id="over" style="display:none;vertical-align: top;">
                <h5 style="margin-bottom: 0px;"><?php echo $t[0]; ?></h5>
                <table id="over" class="info_table" style="margin-right: 30px;">
                    <colgroup>
                        <col width="<?php echo $width; ?>px">
                        <col width="<?php echo $width; ?>px">
                        <col width="<?php echo $width; ?>px">
                    </colgroup>
                    <tr>
                        <th>
                            <?php echo $h[0]; ?>
                        </th>
                        <th>
                            <?php echo $h[1]; ?>
                        </th>
                        <th>
                            <?php echo $h[2]; ?>
                        </th>
                    </tr>
                </table>
                <textarea 
                    onblur="act_update(this,3);"
                    class="stat" 
                    placeholder="<?php echo $t[3]; ?>" 
                    oldvalue="<?php echo htmlfix($comment_over); ?>"
                    style="width:<?php echo $width*3-15; ?>px;"><?php echo htmlfix($comment_over); ?></textarea>
            </td>
            <td id="deficit" style="display:none;vertical-align: top;">
                <h5 style="margin-bottom: 0px;"><?php echo $t[1]; ?></h5>
                <table id="deficit" class="info_table" style="margin-right: 30px;">
                    <colgroup>
                        <col width="<?php echo $width; ?>px">
                        <col width="<?php echo $width; ?>px">
                        <col width="<?php echo $width; ?>px">
                    </colgroup>
                    <tr>
                        <th>
                            <?php echo $h[0]; ?>
                        </th>
                        <th>
                            <?php echo $h[1]; ?>
                        </th>
                        <th>
                            <?php echo $h[2]; ?>
                        </th>
                    </tr>
                </table>
                <textarea 
                    onblur="act_update(this,2);"
                    class="stat" 
                    placeholder="<?php echo $t[3]; ?>"  
                    oldvalue="<?php echo htmlfix($comment_deficit); ?>"
                    style="width:<?php echo $width*3-15; ?>px;"><?php echo htmlfix($comment_deficit); ?></textarea>
            </td>
            <td id="suspect" style="display:none;vertical-align: top;">
                <h5 style="margin-bottom: 0px;"><?php echo $t[2]; ?></h5>
                <table id="suspect" class="info_table">
                    <colgroup>
                        <col width="<?php echo $width; ?>px">
                        <col width="<?php echo $width; ?>px">
                        <col width="<?php echo $width; ?>px">
                    </colgroup>
                    <tr>
                        <th>
                            <?php echo $h[0]; ?>
                        </th>
                        <th>
                            <?php echo $h[1]; ?>
                        </th>
                        <th>
                            <?php echo $h[2]; ?>
                        </th>
                    </tr>
                </table>
                <textarea 
                    onblur="act_update(this,1);"
                    id='suspect' 
                    class="stat" 
                    placeholder="<?php echo $t[3]; ?>" 
                    oldvalue="<?php echo htmlfix($comment_suspect); ?>"
                    style="width:<?php echo $width*3-15; ?>px;"><?php echo htmlfix($comment_suspect); ?></textarea>
            </td>
        </tr>
    </table>
    <style>
        table.stat_table th {
            vertical-align: middle;
            padding: 0px;
        }
        table.stat_table td {
            vertical-align: middle;
            padding: 0px;
        }
        table.stat_table input {
            margin:0px;
        }
        table.stat_table select {
            margin:0px;
        }
        
    </style>
    <table class="stat_table" width="100%" style='margin-bottom: 10px;'>
        <tr>
            <th align="left">
                <?php echo $d[0]; ?>:
            </th>
            <th align="left">
                <select 
                    class="stat" 
                    id="client_code" 
                    onchange="update_client_id(this);" 
                    onblur="update_recon_discr(this);"
                    oldvalue="<?php echo $client_id; ?>"
                    name="CustomerId"
                    style="width:130px;">
                    <?php 
                        if ($client_id=='') { 
                            echo '<option value="" selected></option>'; 
                        } else {
                            echo '<option value=""></option>'; 
                        };
                        foreach ($clients as $client) {
                            if ($client_id==$client[0]) {
                                echo '<option value="',$client[0],'" selected>',$client[1],'</option>';
                            } else {
                                echo '<option value="',$client[0],'">',$client[1],'</option>';                                
                            }
                        };
                    ?>
                </select>         
            </th>
            <th align="left">
                <?php echo $d[1]; ?>:
            </th>
            <th align="left">
                <select 
                    oldvalue="<?php echo $client_id; ?>"
                    class="stat" 
                    id="client_name" 
                    onchange="update_client_id(this);" 
                    onblur="update_recon_discr(this);"
                    name="CustomerId"
                    style="width:364px;">
                    <?php 
                        if ($client_id=='') { 
                            echo '<option value="" selected></option>'; 
                        } else {
                            echo '<option value=""></option>'; 
                        };
                        foreach ($clients as $client) {
                            if ($client_id==$client[0]) {
                                echo '<option value="',$client[0],'" selected>',$client[2],'</option>';
                            } else {
                                echo '<option value="',$client[0],'">',$client[2],'</option>';
                            };
                        };
                    ?>
                </select>         
            </th>
        </tr>
        <tr style='<?php 
            if ($scenario['UseDepositPackingDate']!=1
                    AND $scenario['UsePackingOperatorName']!=1) {
                echo 'display:none;';
            };
        ?>'>
            <th align="left">
                <span style="<?php echo ($scenario['UseDepositPackingDate']==1)?'':'display:none;';?>">
                    <?php echo $d[2]; ?>:
                </span>
            </th>
            <td> 
                <div class="input-append date" id="dp3" data-date-format="yyyy-mm-dd" style="padding: 0px;margin: 0px;">
                    <input 
                           class="stat"
                           type="text"
                           id="DepositPackingDate"
                           data-date-language="ru" 
                           style="<?php echo ($scenario['UseDepositPackingDate']==1)?'':'display:none;';?>width:90px;text-align: center;" 
                           name="DepositPackingDate"
                           onchange="update_recon_discr(this);"
                           oldvalue="<?php echo $DepositRec['DepositPackingDate']; ?>"
                           value="<?php echo $DepositRec['DepositPackingDate']; ?>"/>
                    <span class="add-on" style="<?php echo ($scenario['UseDepositPackingDate']==1)?'':'display:none;';?>"><i class="icon-th"></i></span>
                </div>
            </td>
            <th align="left" style="<?php echo ($scenario['UsePackingOperatorName']==1)?'':'display:none;';?>">
                <?php echo $d[3]; ?>:
            </th>
            <td>
                <input class="stat" 
                       type="text" 
                       style="<?php echo ($scenario['UsePackingOperatorName']==1)?'':'display:none;';?>width:350px;" 
                       name="PackingOperatorName"
                       onblur="update_recon_discr(this);"
                       oldvalue="<?php echo htmlfix($DepositRec['PackingOperatorName']); ?>"
                       value="<?php echo htmlfix($DepositRec['PackingOperatorName']); ?>"
                       />                 
            </td>
        </tr>
    </table>
    <table  
        class="stat_table" 
        width="100%" 
        style="">
        <tr style="<?php 
                if($scenario['UsePackType']!=1 
                        AND $scenario['UsePackIntegrity']!=1
                        AND $scenario['UsePackId']!=1) {
                    echo 'display:none;';
                }
            ?>">
            <th align="left"><?php echo $d[4]; ?>:</th>
            <th align="left">
                <select 
                        style="<?php echo ($scenario['UsePackType']==1)?'':'display:none;';?>width:140px;" 
                        class="stat"
                        onblur="update_recon_discr(this);"
                        oldvalue="<?php echo $DepositRec['PackType'];?>"
                        name="PackType">
                    <option value="1" <?php echo ($DepositRec['PackType']==1) ?'selected':''; ?>><?php echo $s[0]; ?></option>
                    <option value="0" <?php echo ($DepositRec['PackType']==0) ?'selected':''; ?>><?php echo $s[1]; ?></option>
                    <option value="2" <?php echo ($DepositRec['PackType']==2) ?'selected':''; ?>><?php echo $s[8]; ?></option>
                </select>
                <select 
                        style="<?php echo ($scenario['UsePackIntegrity']==1)?'':'display:none;';?>width:140px;" 
                        class="stat" 
                        onblur="update_recon_discr(this);"
                        oldvalue="<?php echo $DepositRec['PackIntegrity'];?>"
                        name="PackIntegrity">
                    <option value="1" <?php echo ($DepositRec['PackIntegrity']==1) ?'selected':''; ?>><?php echo $s[6]; ?></option>
                    <option value="0" <?php echo ($DepositRec['PackIntegrity']==1) ?'':'selected'; ?>><?php echo $s[7]; ?></option>
                </select>
                <span style="<?php echo ($scenario['UsePackId']==1)?'':'display:none;';?>width:200px;"> № </span>
                <input type="text" 
                       style="<?php echo ($scenario['UsePackId']==1)?'':'display:none;';?>width:200px;" 
                       class="stat" 
                       name="PackId" 
                       onblur="update_recon_discr(this);"
                       oldvalue="<?php echo htmlfix($DepositRec['PackId']);?>"
                       value="<?php echo htmlfix($DepositRec['PackId']); ?>"/>
            </th>
        </tr>
        <tr style="<?php 
                if($scenario['UseSealType']!=1 
                        AND $scenario['UseSealIntegrity']!=1
                        AND $scenario['UseSealNumber']!=1) {
                    echo 'display:none;';
                }
            ?>">
            <th align="left"><?php echo $d[5]; ?>:</th>
            <th align="left">
                <select 
                    style="<?php echo ($scenario['UseSealType']==1)?'':'display:none;';?>width:140px;" 
                    onblur="update_recon_discr(this);"
                    oldvalue="<?php echo htmlfix($DepositRec['SealType']);?>"                    
                    class="stat" 
                    name="SealType">
                    <option value="1" <?php echo ($DepositRec['SealType']==1) ?'selected':''; ?>><?php echo $s[2]; ?></option>
                    <option value="0" <?php echo ($DepositRec['SealType']==1) ?'':'selected'; ?>><?php echo $s[3]; ?></option>
                </select>
                <select 
                    style="<?php echo ($scenario['UseSealIntegrity']==1)?'':'display:none;';?>width:140px;" 
                    class="stat" 
                    onblur="update_recon_discr(this);"
                    oldvalue="<?php echo htmlfix($DepositRec['SealIntegrity']);?>"
                    name="SealIntegrity">
                    <option value="1" <?php echo ($DepositRec['SealIntegrity']==1) ?'selected':''; ?>><?php echo $s[6]; ?></option>
                    <option value="0" <?php echo ($DepositRec['SealIntegrity']==1) ?'':'selected'; ?>><?php echo $s[7]; ?></option>
                </select>
                <span style="<?php echo ($scenario['UseSealNumber']==1)?'':'display:none;';?>">№</span>
                <input 
                       type="text" 
                       style="<?php echo ($scenario['UseSealNumber']==1)?'':'display:none;';?>width:200px;" 
                       onblur="update_recon_discr(this);"
                       oldvalue="<?php echo htmlfix($DepositRec['SealNumber']);?>"
                       class="stat" 
                       name="SealNumber" value="<?php echo htmlfix($DepositRec['SealNumber']); ?>"/>
            </th>
        </tr>
        <tr style="<?php 
                if($scenario['UseStrapsIntegrity']!=1 
                        AND $scenario['UseStrapType']!=1) {
                    echo 'display:none;';
                }
            ?>">
            <th align="left"><?php echo $d[6]; ?>:</th>
            <th align="left">
                <select 
                    style="<?php echo ($scenario['UseStrapType']==1)?'':'display:none;';?>width:140px;" 
                    class="stat" 
                    onblur="update_recon_discr(this);"
                    oldvalue="<?php echo htmlfix($DepositRec['StrapType']);?>"
                    name="StrapType">
                    <option value="1" <?php echo ($DepositRec['StrapType']==1) ?'selected':''; ?>><?php echo $s[4]; ?></option>
                    <option value="0" <?php echo ($DepositRec['StrapType']==1) ?'':'selected'; ?>><?php echo $s[5]; ?></option>
                </select>
                <select 
                    style="<?php echo ($scenario['UseStrapsIntegrity']==1)?'':'display:none;';?>width:140px;" 
                    class="stat" 
                    onblur="update_recon_discr(this);"
                    oldvalue="<?php echo htmlfix($DepositRec['StrapsIntegrity']);?>"
                    name="StrapsIntegrity">
                    <option value="1" <?php echo ($DepositRec['StrapsIntegrity']==1) ?'selected':''; ?>><?php echo $s[6]; ?></option>
                    <option value="0" <?php echo ($DepositRec['StrapsIntegrity']==1) ?'':'selected'; ?>><?php echo $s[7]; ?></option>
                </select>
            </th>
        </tr>
    </table> 
</div>
<script>
    $("input#DepositPackingDate").each(function(){
       if( this.value=='0000-00-00' ){ 
           this.value='' 
       }; 
    });
    $("input#DepositPackingDate").datepicker({
        format:  'yyyy-mm-dd',
        autoclose: true
    });
    $('.stat').bind( "click", function() {
        $(this).css('background-color','white');
    });
</script>

