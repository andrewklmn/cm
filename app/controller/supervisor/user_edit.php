<?php

/*
 * Редактирование кассы пересчета для работника
 */

        if (!isset($c)) exit;
        
        $l = explode('|', $_SESSION[$program]['lang']['user_edit_labels']);
        $b = explode('|', $_SESSION[$program]['lang']['user_edit_buttons']); 
        $s = explode('|', $_SESSION[$program]['lang']['user_edit_states']); 

        if(isset($_POST['action']) AND $_POST['action']=='update') {
            include 'app/view/html_header.php';
            $oldvalue = explode('|', $_POST['olddata']);
            $newvalue = explode('|', $_POST['newdata']);
            
            do_sql('LOCK TABLES UserConfiguration WRITE;');
            
            $row = fetch_row_from_sql('
                SELECT
                    `UserConfiguration`.`CashRoomId`,
                    `UserConfiguration`.`Blind`,
                    `UserConfiguration`.`CurrentScenario`
                FROM 
                    `cashmaster`.`UserConfiguration`
                WHERE
                    `UserConfiguration`.`UserId`="'.  addslashes($_REQUEST['id']).'"
            ;');
            
            if ($oldvalue==$row) {
                do_sql('
                    UPDATE 
                        `cashmaster`.`UserConfiguration`
                    SET
                        `CashRoomId` = "'.addslashes($newvalue[0]).'",
                        `Blind` = "'.addslashes($newvalue[1]).'",
                        `CurrentScenario` = "'.addslashes($newvalue[2]).'"
                    WHERE 
                        `UserId` = "'.addslashes($_REQUEST['id']).'"                    
                ;');
                echo 0;
            } else {
                echo 1;
                echo $row[0];
            };            
            do_sql('UNLOCK TABLES;');  
            exit;
        };
        
        
        $l = explode('|', $_SESSION[$program]['lang']['user_edit_labels']);
        $b = explode('|', $_SESSION[$program]['lang']['user_edit_buttons']);        
        
        
        // Проверяем есть ли сверки в работе с другим сценарием по этому оператору
        if(isset($_POST['action']) AND $_POST['action']=='check_scenario') {
            include 'app/view/html_header.php';
            // Получаем текущий сценарий оператора
            $rows = get_assoc_array_from_sql('
                SELECT
                    `UserConfiguration`.`CurrentScenario`
                FROM 
                    `cashmaster`.`UserConfiguration`
                WHERE
                    `UserConfiguration`.`UserRoleId`="3"
                    AND `UserConfiguration`.`UserId`="'.addslashes($_REQUEST['id']).'"
            ;');
            if (count($rows)!=1) {
                // Если пользователь не оператор, то ничего не проверяем
                echo 0;
                exit;
            } else {
                // Проверяем есть ли незакрытые сверки со сценарием отличным от предыдущего
                
                $rows = get_array_from_sql('
                    SELECT
                        `DepositRecs`.`ScenarioId`
                    FROM 
                        `cashmaster`.`DepositRecs`                        
                    WHERE
                        `DepositRecs`.`RecOperatorId`="'.addslashes($_REQUEST['id']).'"
                        AND `DepositRecs`.`ReconcileStatus`<>"1"
                        AND  `DepositRecs`.`ServiceRec`<>"1"
                        AND `DepositRecs`.`ScenarioId`<>"'.addslashes($_REQUEST['scenario']).'"
                    GROUP BY 
                        `DepositRecs`.`ScenarioId`
                ;');
                if (count($rows)>0) {
                    echo htmlfix($_SESSION[$program]['lang']['operator_has_unfinished_recon']);
                } else {
                    echo 0;
                };
                exit;
            };
        };
        
        
        $data['title'] = $l[0];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        include './app/view/update_record.php';
        include './app/model/check_access_to_roles.php';
        
        $roles = array(2,3); // массив редактируемых ролей
        
        if (isset($_REQUEST['id']) AND $_REQUEST['id']>0) {
            // ID задан и больше 0
            if (check_access_to_roles($_REQUEST['id'], $roles)==false) {
                $data['error'] = $w[0];
            };
        } else {
            $data['error'] = $w[1];
        };
        if(isset($data['error'])) {
            echo '<div class="container">';
            include 'app/view/error_message.php';
            ?>
                <button 
                    onclick='window.location.replace("?c=users");'
                    class="btn btn-primary btn-large"><?php 
                        echo htmlfix($_SESSION[$program]['lang']['back_to_list']);
                    ?></button>
            <?php
            echo '</div>';
            exit;
        };
        
        $user = get_user_config($_REQUEST['id']);

        $cash_rooms = get_array_from_sql('
            SELECT
                `CashRooms`.`Id`,
                `CashRooms`.`CashRoomName`
            FROM 
                `cashmaster`.`CashRooms`
            ORDER BY CashRoomName ASC
        ;');
        
        $scenarios = get_array_from_sql('
            SELECT
                `Scenario`.`ScenarioId`,
                `Scenario`.`ScenarioName`
            FROM 
                `cashmaster`.`Scenario`
            WHERE
                `Scenario`.`LogicallyDeleted`=0
            ORDER BY 
                ScenarioName ASC
        ;');
        
        
        $recs_in_work = false;
        
        $row = fetch_row_from_sql('
            SELECT
                count(*)
            FROM 
                `DepositRecs`
            WHERE
                (PrepOperatorId="'.addslashes($_GET['id']).'"
                    OR RecOperatorId="'.addslashes($_GET['id']).'")
                AND ReconcileStatus="0"
                AND ServiceRec="0"
        ;');
        if ($row[0]>0) {
            $recs_in_work = true;
        };
?>
<style>
    table.user td {
        text-align: left;
        padding: 5px;
    }
    table.user th {
        text-align: right;
        padding: 5px;
    }
</style>
<script>
    function update(elem){
        if ($(elem).val()==$(elem).attr('oldvalue')) return true;
        update_record(elem,'?c=user_edit&id=' + <?php echo $_REQUEST['id']; ?>);
    };
    
    function check_user_scenario(elem){
        $.ajax({
            type: "POST",
            url: '?c=user_edit&id=<?php echo $_REQUEST['id']; ?>',
            async: false,
            data: {
                action: 'check_scenario',
                scenario: $(elem).val()
            },
            error: function() {
                alert("Connection error, Can't update.");
                for(var i=0; i<inputs.length;i++) {
                    inputs[i].value = oldvalues[i];
                    $(inputs[i]).css('color','red');
                };
                remove_wait();
            },
            success: function(answer){
                switch(answer) {
                    case '0':
                        $('span#info').html(' ');
                        break;
                    default:
                        $('span#info').html(answer);
                        break;
                }
            }
        });
    };
    
    function back_to_old() {
        var sel = $('select.stat');
        var old = $('input.oldvalue');
        
        $(sel[0]).val(old[0].value);
        $(sel[1]).val(old[2].value);
        
        var input = $('input.stat')[0];
        $(input).val(old[1].value);
        if (old[1].value=="1") {
            $('input#checkbox').prop('checked', true);
        } else {
            $('input#checkbox').prop('checked', false);
        };        
        update(input);
    };
</script>
<div class="container">
    <table class='user'>
        <tr>
            <th><?php echo htmlfix($l[16]); ?>:</th>
            <td><?php 
                    echo htmlfix($user['UserFamilyName']
                            .' '.$user['UserFirstName']
                            .' '.$user['UserPatronymic']); 
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[1]); ?>:</th>
            <td><?php 
                    echo htmlfix($user['Phone']); 
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[2]); ?>:</th>
            <td><?php 
                    echo htmlfix($user['UserPost']); 
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[3]); ?>:</th>
            <td><?php 
                    echo htmlfix($user['RoleLabel']); 
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[4]); ?>:</th>
            <td>
                <select 
                    onchange='update(this);' 
                    <?php 
                        if ($recs_in_work) {
                            echo 'readonly';
                        };
                    ?>
                    class='stat'
                    oldvalue="<?php echo $user['CashRoomId']; ?>">
                    <?php 
                        if ($user['CashRoomId']=='') echo '<option value="" selected></option>';
                        if ($user['CashRoomId']=='0') echo '<option value="0" selected></option>';
                        
                        if ($recs_in_work) {
                            foreach ($cash_rooms as $key=>$value) {
                                if ($value[0]==$user['CashRoomId']) {
                                    echo '<option value="',
                                        htmlfix($value[0]),'" selected>',
                                        htmlfix($value[1]),'</option>';
                                };
                            };                            
                        } else {
                            foreach ($cash_rooms as $key=>$value) {
                                if ($value[0]==$user['CashRoomId']) {
                                    echo '<option value="',
                                        htmlfix($value[0]),'" selected>',
                                        htmlfix($value[1]),'</option>';
                                } else {
                                echo '<option value="',
                                        htmlfix($value[0]),'">',
                                        htmlfix($value[1]),'</option>';
                                };
                            };
                        };
                    ?>
                </select>
                <?php 
                    if ($recs_in_work) {
                        echo '<font style="color:red;">'
                            .htmlfix($_SESSION[$program]['lang']['cannot_change_cashroom'])
                            .'!</font>';
                    };
                ?>
                <input type="hidden" class="oldvalue" value="<?php echo $user['CashRoomId']; ?>"/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[17]); ?>:</th>
            <td>
                <input type="hidden" 
                       class='stat'
                       oldvalue="<?php echo $user['Blind']; ?>" 
                       value="<?php echo $user['Blind']; ?>" 
                       name="Blind" class="field">
                <input type="checkbox" 
                       id ="checkbox"
                       onclick="
                           var input = $(this.parentNode).find('input.stat')[0];
                           if ($(this).prop('checked')==true) {
                               $(input).val('1');
                           } else {
                               $(input).val('0');
                           };
                           update(input);
                       "
                       <?php echo ($user['Blind']==1)?'checked':'';  ?> />
                <input type="hidden" class="oldvalue" value="<?php echo $user['Blind']; ?>"/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[18]); ?>:</th>
            <td>
                <select 
                    onchange='check_user_scenario(this);' 
                    class='stat span8'
                    oldvalue="<?php echo $user['CurrentScenario']; ?>">
                    <?php 
                        if ($user['CurrentScenario']=='') echo '<option value="" selected></option>';
                        if ($user['CurrentScenario']=='0') echo '<option value="0" selected></option>';
                        
                        foreach ($scenarios as $key=>$value) {
                            if ($value[0]==$user['CurrentScenario']) {
                                echo '<option value="',
                                    htmlfix($value[0]),'" selected>',
                                    htmlfix($value[1]),'</option>';
                            } else {
                            echo '<option value="',
                                    htmlfix($value[0]),'">',
                                    htmlfix($value[1]),'</option>';
                            };
                        };
                    ?>
                </select>
                <input type="hidden" class="oldvalue" value="<?php echo $user['CurrentScenario']; ?>"/>
            </td>
        </tr>
    </table>
    <span id="info" style="color:red;font-size: 16px;"></span><br/>
    <br/>
    <button 
        onclick='back_to_old();window.location.replace("?c=users");'
        class="btn btn-primary btn-large"><?php echo htmlfix($b[1]); ?></button>
    <button 
        onclick='$(".stat").each(function(){ update(this); });window.location.replace("?c=users");'
        class="btn btn-warning btn-large"><?php echo htmlfix($b[0]); ?></button>
</div>
<?php 
    include './app/view/set_rs_to_stat.php';
?>
