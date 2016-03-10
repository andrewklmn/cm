<?php

/*
 * Редактирование пользователя по списку
 */
        $roles = array(1,2,3,4); // массив редактируемых ролей

        if (!isset($c)) exit;

        include 'app/controller/common/set_systemconfiguration.php';
        
        $l = explode('|', $_SESSION[$program]['lang']['user_edit_labels']);
        $b = explode('|', $_SESSION[$program]['lang']['user_edit_buttons']); 
        $s = explode('|', $_SESSION[$program]['lang']['user_edit_states']); 
        $w = explode('|', $_SESSION[$program]['lang']['user_edit_warnings']); 
        
        if(isset($_POST['action']) AND $_POST['action']=='get') { 
            include 'app/view/html_header.php';
            if ($_SESSION[$program]['hash']==base64_decode($_POST['p'])) {
                $pass=rand(1000, 9999);
                
                do_sql('
                    UPDATE 
                        `cashmaster`.`UserConfiguration`
                    SET
                        `BadLogAttempts` = "0",
                        `UserIsBlocked` = "0",
                        `ChangePassword` = "1",
                        `LastChangePasswordDate` = CURRENT_TIMESTAMP,
                        `UserPassword` = "'.md5($pass).'"
                    WHERE 
                        `UserId` = "'.base64_decode($_POST['i']).'"
                ;');
       
                echo 0;
                echo base64_encode(base64_encode(base64_encode($pass)));
                exit;
            } else {
                echo 1;
                exit;
            };
        };
        
        if(isset($_POST['action']) AND $_POST['action']=='update') {
            include 'app/view/html_header.php';
            $oldvalue = explode('|', $_POST['olddata']);
            $newvalue = explode('|', $_POST['newdata']);
            
            do_sql('LOCK TABLES UserConfiguration WRITE, SystemLog WRITE;');
            
            $row = fetch_row_from_sql('
                SELECT
                    `UserConfiguration`.`LoggingAttemptsLimit`,
                    `UserConfiguration`.`ValidDays`,
                    `UserConfiguration`.`UserIsBlocked`
                FROM 
                    `cashmaster`.`UserConfiguration`
                WHERE
                    `UserConfiguration`.`UserId`="'.  addslashes($_REQUEST['id']).'"
            ;');
            
            if ($oldvalue==$row) {
                if ( $newvalue[2]=='0' ) {
                    do_sql('
                        UPDATE 
                            `cashmaster`.`UserConfiguration`
                        SET
                            `UserConfiguration`.`LoggingAttemptsLimit`= "'.addslashes($newvalue[0]).'",
                            `UserConfiguration`.`ValidDays`= "'.addslashes($newvalue[1]).'",
                            `UserConfiguration`.`UserIsBlocked`= "'.addslashes($newvalue[2]).'",
                            `UserConfiguration`.`OldUserFamilyName`=`UserConfiguration`.`UserFamilyName`,
                            `UserConfiguration`.`OldUserFirstName`=`UserConfiguration`.`UserFirstName`,
                            `UserConfiguration`.`OldUserPatronymic`=`UserConfiguration`.`UserPatronymic`,
                            `UserConfiguration`.`OldGenetiveName`=`UserConfiguration`.`GenetiveName`,
                            `UserConfiguration`.`OldInstrName`=`UserConfiguration`.`InstrName`,
                            `UserConfiguration`.`OldUserPost`=`UserConfiguration`.`UserPost`,
                            `UserConfiguration`.`OldPhone`=`UserConfiguration`.`Phone`
                        WHERE 
                            `UserId` = "'.addslashes($_REQUEST['id']).'"                    
                    ;');                    
                } else {
                    do_sql('
                        UPDATE 
                            `cashmaster`.`UserConfiguration`
                        SET
                            `UserConfiguration`.`LoggingAttemptsLimit`= "'.addslashes($newvalue[0]).'",
                            `UserConfiguration`.`ValidDays`= "'.addslashes($newvalue[1]).'",
                            `UserConfiguration`.`UserIsBlocked`= "'.addslashes($newvalue[2]).'"
                        WHERE 
                            `UserId` = "'.addslashes($_REQUEST['id']).'"                    
                    ;');                    
                };

            $u_row = fetch_row_from_sql('
                SELECT
                    `UserConfiguration`.`UserLogin`
                FROM 
                    `cashmaster`.`UserConfiguration`
                WHERE
                    `UserConfiguration`.`UserId`="'.  addslashes($_REQUEST['id']).'"
            ;');
                
                if ($newvalue[2] == 1 AND $oldvalue[2] == 0) {
					system_log($u_row[0].' '.$events[17].' : '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' / IP: '.$_SERVER['REMOTE_ADDR']); // was blocked
				} else if ($newvalue[2] == 0 AND $oldvalue[2] == 1) {
					system_log($u_row[0].' '.$events[16].' : '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' / IP: '.$_SERVER['REMOTE_ADDR']); // was unblocked
				} else if ($newvalue[0] != $oldvalue[0]) {
					system_log($u_row[0].' '.$events[18].' : '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' / '.$oldvalue[0].' / '.$newvalue[0].' / IP: '.$_SERVER['REMOTE_ADDR']); // login attempts changed
				} else if ($newvalue[1] != $oldvalue[1]) {
					system_log($u_row[0].' '.$events[19].' : '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' / '.$oldvalue[1].' / '.$newvalue[1].' / IP: '.$_SERVER['REMOTE_ADDR']); // password validity days changed
				};
                echo 0;
            } else {
                echo 1;
                echo $row[0],"|",$row[1],"|",$row[2];
            };            
            do_sql('UNLOCK TABLES;');  
            exit;
        };

        $data['title'] = $l[0];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        include './app/view/update_record.php';
        include './app/model/check_access_to_roles.php';
                
        if (isset($_REQUEST['id']) AND $_REQUEST['id']>0) {
            // ID задан и больше 0
            if (check_access_to_roles($_REQUEST['id'], $roles)==false
                    OR $_REQUEST['id']==$_SESSION[$program]['UserConfiguration']['UserId']) {
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
                    class="btn btn-primary btn-large"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
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
        
        $creator = get_user_config($user['CreatedBy']);
        $_SESSION[$program]['hash'] = base64_encode(rand(10000000,99999999)); 
        
        
?>
<script src="js/base64_encode.js"></script>
<script src="js/base64_decode.js"></script>
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
    p = "<?php echo $_SESSION[$program]['hash']; ?>";
    function get_new_password() {
        set_wait();
        $.ajax({
            type: "POST",
            url: '?c=user_edit&id=' + <?php echo $_REQUEST['id']; ?>,
            async: false,
            data: {
                action: 'get',
                p: base64_encode(p),
                i: base64_encode("<?php echo $_REQUEST['id']; ?>")
            },
            error: function() {
                alert("Connection error, Can't update.");
                remove_wait();
            },
            success: function(answer){
                switch(answer[0]){
                    case "0":
                        answer = answer.substring(1);
                        $('th#podpis').html('<?php echo htmlfix($_SESSION[$program]['lang']['temporary_password']); ?>: ');
                        $('span#pass').html(base64_decode(base64_decode(base64_decode(answer))));
                        $('input#block').hide();
                        break;
                    case "1":
                        alert('Wrong encrypted data.');
                        break;
                    default:
                        alert(answer);
                };
            }
        });
        remove_wait();
    };
    function update(elem){
        update_record(elem,'?c=user_edit&id=' + <?php echo $_REQUEST['id']; ?>);
    };
    function back_to_old() {

    };
    function stat_blur(elem) {
        if($(elem).attr('name')=='UserIsBlocked'){
            if($(elem).prop("checked")==true) {
                if ($(elem).attr('oldvalue')=="0") {
                    $('span#pass').html('');
                    update(elem);
                };
            } else {
                if ($(elem).attr('oldvalue')=="1") {
                    update(elem);
                    if ($(elem).attr('oldvalue')=="0") {
                        // разблокировка пользователя вызывает смену пароля на временный
                        // с выводом на экран инспектору
                        get_new_password();
                    };
                };
            };
        } else {
            if ($(elem).val()!=$(elem).attr('oldvalue')) {
                update(elem);
            };
        };
    };
</script>
<div class="container">
    <table class='user'>
        <tr>
            <th><?php echo htmlfix($l[5]); ?>:</th>
            <td><?php 
                    echo htmlfix($user['UserLogin']);
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[16]); ?>:</th>
            <td><?php 
            
                    if ($user['UserFamilyName']!=$user['OldUserFamilyName']) {
                        echo '<font style="color:red;">'.htmlfix($user['UserFamilyName']).'</font>';
                    } else {
                        echo htmlfix($user['UserFamilyName']);
                    };
                    
                    if ($user['UserFirstName']!=$user['OldUserFirstName']) {
                        echo ' <font style="color:red;">'.htmlfix($user['UserFirstName']).'</font>';
                    } else {
                        echo ' '.htmlfix($user['UserFirstName']);
                    };

                    if ($user['UserPatronymic']!=$user['OldUserPatronymic']) {
                        echo ' <font style="color:red;">'.htmlfix($user['UserPatronymic']).'</font>';
                    } else {
                        echo ' '.htmlfix($user['UserPatronymic']);
                    };
                    if ($_SESSION[$program]['SystemConfiguration']['UseGenitiveName']==1) {
                        if ($user['GenetiveName']!=$user['OldGenetiveName']) {
                            echo ', <font style="color:red;">'.htmlfix($user['GenetiveName']).'</font>';
                        } else {
                            echo ', '.htmlfix($user['GenetiveName']);
                        };
                        if ($user['InstrName']!=$user['OldInstrName']) {
                            echo ', <font style="color:red;">'.htmlfix($user['InstrName']).'</font>';
                        } else {
                            echo ', '.htmlfix($user['InstrName']);
                        };
                    };
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[1]); ?>:</th>
            <td><?php 
                    if ($user['Phone']!=$user['OldPhone']) {
                        echo '<font style="color:red;">'.htmlfix($user['Phone']).'</font>';
                    } else {
                        echo htmlfix($user['Phone']);
                    };
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[2]); ?>:</th>
            <td><?php 
                    if ($user['UserPost']!=$user['OldUserPost']) {
                        echo '<font style="color:red;">'.htmlfix($user['UserPost']).'</font>';
                    } else {
                        echo htmlfix($user['UserPost']);
                    };
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[3]); ?>:</th>
            <td><?php 
                    echo htmlfix($user['RoleLabel']); 
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[7]); ?>:</th>
            <td><?php 
                    echo substr($user['UserCreateDate'],0,10); 
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[8]); ?>:</th>
            <td><?php 
                    echo htmlfix($creator['UserFamilyName']
                            .' '.$creator['UserFirstName']
                            .' '.$creator['UserPatronymic']);
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[9]); ?>:</th>
            <td><?php 
                    echo substr($user['LastChangePasswordDate'],0,10); 
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[10]); ?>:</th>
            <td>
                <input 
                    type="text" 
                    class="stat" 
                    name="LoggingAttemptsLimit" 
                    style="width: 40px;"
                    onblur="stat_blur(this);"
                    oldvalue="<?php echo $user['LoggingAttemptsLimit']; ?>"
                    value="<?php echo $user['LoggingAttemptsLimit']; ?>"/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[11]); ?>:</th>
            <td>
                <input 
                    type="text" 
                    class="stat" 
                    name="LoggingAttemptsLimit" 
                    style="width: 40px;"
                    onblur="stat_blur(this);"
                    oldvalue="<?php echo $user['ValidDays']; ?>"
                    value="<?php echo $user['ValidDays']; ?>"/>
            </td>
        </tr>
        <tr>
            <th id="podpis"><?php echo htmlfix($l[12]); ?>:</th>
            <td>
                <input 
                    id ="block"
                    type="checkbox" 
                    class="stat" 
                    name="UserIsBlocked" 
                    style="width: 40px;"
                    onchange="stat_blur(this);"
                    oldvalue="<?php echo $user['UserIsBlocked']; ?>"
                    <?php 
                        if ($user['UserIsBlocked']=="1") echo 'checked';
                    ?>/>
                    <span id="pass" style="font-size: 18px; color: darkblue;"></span>
            </td>
        </tr>
    </table>
    <button 
        onclick='window.location.replace("?c=users");'
        class="btn btn-primary btn-large"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
</div>
