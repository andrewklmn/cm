<?php

/*
 * Смена пароля пользователя
 */
        if (!isset($c)) exit;
        
        $p = explode('|', $_SESSION[$program]['lang']['change_password_results']); 

        if(isset($_POST['i'])
                AND base64_decode($_POST['i'])=='check_history'
                AND isset($_POST['h'])) {
            include './app/view/html_header.php';
            // Проверяем последние 6 хешей пользователя
            $hash = get_assoc_array_from_sql('
                SELECT
                    `UserHashHistory`.`OldHash`
                FROM 
                    `cashmaster`.`UserHashHistory`
                WHERE
                    `UserHashHistory`.`UserId`="'.$_SESSION[$program]['UserConfiguration']['UserId'].'"
                ORDER BY `UserHashHistory`.`id` DESC
                LIMIT 0,6
            ;');
            $a = 0;
            foreach ($hash as $value) {
                if ($value['OldHash']==md5(base64_decode($_POST['h']))) {
                    $a=1;
                };
            };
            echo $a;
            exit;
        };
        
        if(isset($_POST['newhash'])) {
            if(strlen($_POST['newhash'])==160) { 
                // Декодирование строки хеша
                $old_new_hash = substr($_POST['newhash'], 0,32);
                $old_hash = substr($_POST['newhash'], 32,32);
                $new_hash = substr($_POST['newhash'], 64,32);
                $old_new_repeat_hash = substr($_POST['newhash'], 96,32);
                $repeat_hash = substr($_POST['newhash'], 128,32);
                // проверяем корректность шифрованной строки 
                $flag = true;
                if (md5($old_hash.$new_hash)!=$old_new_hash) $flag=false;
                if (md5($old_hash.$new_hash.$repeat_hash)!=$old_new_repeat_hash) $flag=false;
                if ($_SESSION[$program]['UserConfiguration']['UserLogicallyDeleted']==1) $flag=false;
                if ($_SESSION[$program]['UserConfiguration']['UserIsBlocked']==1) $flag=false;
                if ($_SESSION[$program]['UserConfiguration']['UserPassword']!=$old_hash) $flag=false;
                
                if ($flag==true) {
                   // обновляем и назад в профиль
                   do_sql('
                        UPDATE 
                            `cashmaster`.`UserConfiguration`
                        SET
                            `UserPassword` = "'.addslashes($new_hash).'",
                            `ChangePassword` = 0,
                            `LastChangePasswordDate` = CURRENT_TIMESTAMP
                        WHERE 
                            `UserId` = "'.$_SESSION[$program]['UserConfiguration']['UserId'].'";
                   ');
                   // сохраняем в историю хешей пользователя
                   do_sql('                       
                        INSERT INTO `cashmaster`.`UserHashHistory`
                            (
                                `UserId`,
                                `OldHash`
                            )
                        VALUES
                            (
                                "'.$_SESSION[$program]['UserConfiguration']['UserId'].'",
                                "'.addslashes($new_hash).'"
                            );
                   ');
                   system_log($events[5].' '.$_SESSION[$program]['user_fio']);
                   $_SESSION[$program]['UserConfiguration']['UserPassword']=$new_hash;
                   include './app/view/password_was_changed.php';
                   exit;
                } else {
                   // данные некорректны, сообщаем об ошибке
                   include './app/view/password_was_not_changed.php';
                   exit;
                };
            };
        };
        
        if(isset($_POST['hash'])) {
            //Проверка хеша текущего пользователя
            include './app/view/html_header.php';
            if ($_POST['hash']==$_SESSION[$program]['UserConfiguration']['UserPassword']) {
                echo 0;
            } else {
                echo 1;
            };
            exit;
        };
        
        $data['title'] = $_SESSION[$program]['lang']['change_password'];
        include './app/model/menu.php';
        
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/forms/details_css.php';
        //print_r($_SESSION[$program]['UserConfiguration']);
        //exit;
        
?>  
<style>
    span.false {
        font-size: 30px;
        color:red;
        font-family: sans-serif;
        margin: 0px;
        padding: 0px;
        font-weight: bold;
    }
    span.true {
        font-size: 30px;
        color:green;
        font-family: sans-serif;
        margin: 0px;
        padding: 0px;
        font-weight: bold;
    }
    span#message{
        color:red;
        font-size: 12px;
    }
</style>
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript" src="js/base64_decode.js"></script>
<script type="text/javascript" src="js/base64_encode.js"></script>
<script>
    var t;
    function check_pass_history(password) {
        var flag=false;
        $.ajax({
            type: "POST",
            url: "?c=change_password",
            async: false,
            data: {
                i: base64_encode('check_history'),
                h: base64_encode(password)
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
                if (answer[0]=="0") { 
                    flag = true; 
                } else {
                    if (answer[0]!="1") alert(answer);
                };
            }
        });   
        return flag;
    };
    function keyup_old_pass(elem) {
        clearTimeout(t);
        if (elem.value.length==0) return true;
        t = setTimeout('check_old_pass("' + elem.value + '");',500); 
    };
    function check_old_pass(password) {
        $.ajax({
            type: "POST",
            url: "?c=change_password",
            async: false,
            data: {
                hash: MD5(password)
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
                if (answer=='0') {
                    $('span#false_old_pass').hide();
                    $('span#true_old_pass').show();
                    $('tr#2').show();
                    $('input[name="old_password"]').attr('readonly', true);
                    $($('input[name="new_password"]')[0]).focus();
                } else {
                    $('span#true_old_pass').hide();
                    $('span#false_old_pass').show();
                    $('tr#2').hide();
                };        
            }
        });
    };
    function keyup_new_pass(elem) {
        $('span#message').html('');
        var old = $('input[name="old_password"]')[0].value;
        var repeat = $('input[name="repeat_password"]')[0].value;
        if(repeat!=elem.value) {
            $('tr#3').hide();
            $('tr#4').hide();
            $('input[name="repeat_password"]').val('');
            //return true;            
        };
        if (elem.value.length==0) { 
            $('tr#3').hide();
            $('tr#4').hide();
            $('input[name="repeat_password"]').val('');
            //return true;
        };
        if (old==elem.value) {
            $('span#message').html($('span#message').html() 
                    + 'Пароль должен отличаться от старого.<br/>');
            flag = false;
        };
        var flag = true;
        if (elem.value.replace(/[a-zA-Z0-9\@\#\$\%\&\*]/g,'')!='') { 
            $('span#message').html($('span#message').html() 
                    + 'Недопустимый символ: ' 
                    + elem.value.replace(/[a-zA-Z0-9\@\#\$\%\&\*]/g,'') + '<br/>');
            alert('Недопустимый символ: ' 
                    + elem.value.replace(/[a-zA-Z0-9\@\#\$\%\&\*]/g,''));
            flag = false;            
            elem.value = elem.value.replace(/[^a-zA-Z0-9\@\#\$\%\&\*]/g,'');
        };
        if (elem.value.replace(/[a-zA-Z\@\#\$\%\&\*]/g,'')=='') { 
            $('span#message').html($('span#message').html() 
                    + 'В пароле должны быть цифры.<br/>');
            flag = false;
        };
        if (elem.value.replace(/[a-z0-9\@\#\$\%\&\*]/g,'')=='') {
            $('span#message').html($('span#message').html() 
                    + 'В пароле должны быть ПРОПИСНЫЕ буквы.<br/>');
            flag = false;
        };
        if (elem.value.replace(/[A-Z0-9\@\#\$\%\&\*]/g,'')=='') {
            $('span#message').html($('span#message').html() 
                    + 'В пароле должны быть строчные буквы.<br/>');
            flag = false;
        };
        if (elem.value.replace(/[a-zA-Z0-9]/g,'')=='') {
            $('span#message').html($('span#message').html() 
                    + 'В пароле должен быть хотя бы один символ - @#$%&*.<br/>');
            flag = false;
        };
        if (elem.value.length < <?php echo $_SESSION[$program]['UserConfiguration']['PasswordMinLength']; ?>) {
            $('span#message').html($('span#message').html() 
                    + 'В пароле должно быть не менее ' 
                    + '<?php echo $_SESSION[$program]['UserConfiguration']['PasswordMinLength']; ?>'
                    + '-ти символов.<br/>');
            flag = false;
        } else {
            // проверяем на совпадение со старым паролем половиной символов
            var match = 0;
            var max = Math.ceil(elem.value.length/2);
            for (var i=0; i < elem.value.length; i++) {
                if (elem.value[i]==old[i]) match++;
            };
            if ( max <= match) {
                $('span#message').html($('span#message').html() 
                        + 'Пароль должен отличаться от старого не менее чем на половину символов.<br/>');
                flag = false;               
            };
        };
        
        if(flag==true) {
            if (check_pass_history(elem.value)==false) {
                $('span#message').html($('span#message').html() 
                        + 'Такой пароль уже был использован ранее.<br/>');
                flag = false;             
            };
        };
        
        if (flag==true) {
            $('span#false_new_pass').hide();
            $('span#true_new_pass').show();
            $('tr#3').show();
            $('tr#4').hide();
        } else {
            $('span#false_new_pass').show();
            $('span#true_new_pass').hide();
            $('tr#3').hide();
            $('tr#4').hide();
        };
    };
    function keyup_repeat_pass(elem) {
        var newpass = $('input[name="new_password"]')[0].value;
        if (newpass==elem.value) {
            $('span#false_repeat_pass').hide();
            $('span#true_repeat_pass').show();
            $('tr#4').show();            
        } else {
            $('span#false_repeat_pass').show();
            $('span#true_repeat_pass').hide();
            $('tr#4').hide();
        };
    };
    function save_pass() {
        var inputs = $('form#save').find('input');
        inputs[0].value = MD5(MD5($('input[name="old_password"]')[0].value) 
                            + MD5($('input[name="new_password"]')[0].value))
                        + MD5($('input[name="old_password"]')[0].value)
                        + MD5($('input[name="new_password"]')[0].value)
                        + MD5(MD5($('input[name="old_password"]')[0].value) 
                            + MD5($('input[name="new_password"]')[0].value)
                            + MD5($('input[name="repeat_password"]')[0].value))        
                        + MD5($('input[name="repeat_password"]')[0].value);
        $('form#save').submit();
    };
</script>
<div class="container">
    <?php 
        if (isset($data['text'])) {
            $data['error']=$data['text'];
            include 'app/view/error_message.php';
        };
        
        $l = explode('|', $_SESSION[$program]['lang']['change_password_labels']);
    ?>
    <table class="details">
        <tr id="1">
            <th><?php echo htmlfix($l[0]); ?>:</th>
            <td>
                <input onkeyup="keyup_old_pass(this);" type="password" name="old_password" value=""/>
                <span id="false_old_pass" class="false">✗</span>
                <span id="true_old_pass" style="display:none;" class="true">✓</span>
            </td>
        </tr>
        <tr id="2" style="display:none;">
            <th><?php echo htmlfix($l[1]); ?>:</th>
            <td>
                <input onkeyup="keyup_new_pass(this);" type="password" name="new_password" value=""/>
                <span id="false_new_pass" class="false">✗</span>
                <span id="true_new_pass" style="display:none;" class="true">✓</span>
            </td>
        </tr>
        <tr id="3" style="display:none;">
            <th><?php echo htmlfix($l[2]); ?>:</th>
            <td>
                <input onkeyup="keyup_repeat_pass(this);" type="password" name="repeat_password" value=""/>
                <span id="false_repeat_pass" class="false">✗</span>
                <span id="true_repeat_pass" style="display:none;" class="true">✓</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="left">
                <span id="message"></span>
            </td>
        </tr>
        <tr id="4" style="display:none;">
            <td colspan="2" align="left">
                <form id='save' method="POST" action="?c=change_password">
                    <input type="hidden" name="newhash" value=''/>
                </form>
                <button onclick="save_pass();" class="btn btn-primary btn-large"><?php echo htmlfix($l[3]); ?></button>
            </td>
        </tr>
    </table>
</div>