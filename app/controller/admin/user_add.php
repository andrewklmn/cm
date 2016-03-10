<?php

/*
 * Контроллер для работы со списком клиентов
 */
        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['user_add_title'];
        
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';

        $b = explode('|',$_SESSION[$program]['lang']['user_add_buttons']);
        $l = explode('|',$_SESSION[$program]['lang']['user_add_labels']);
        $w = explode('|',$_SESSION[$program]['lang']['user_add_warnings']);
        
        $data = explode('|','|||||||||||');

        if (isset($_POST['action']) AND $_POST['action']=='add_new') {
            if (isset($_POST['confirmation'])) {
                echo '<div class="container">';
                $names = explode('|','UserFamilyName|UserFirstName|UserPatronymic|UserPatronymic|UserRoleId|Phone|UserRoleId|UserLogin');
                $data = explode('|',$_POST['newdata']);
                
                do_sql('LOCK TABLES UsersIP WRITE, UserConfiguration WRITE, SystemLog WRITE;');
                // проверяем есть ли клиент с таким кодом БИКы
                $row = get_array_from_sql('
                    SELECT
                        *
                    FROM 
                        `cashmaster`.`UserConfiguration`
                    WHERE
                        `UserConfiguration`.`UserLogin`="'.addslashes($data[4]).'"
                ;');
                
                if (count($row)==0) {
                    $family = $data[0].' '.$data[1].' '.$data[2];
                    $login = $data[8];
                    $pass = rand(1000, 9999);
                    
                    foreach ($data as $key=>$value) {
                        $data[$key] = '"'.addslashes($value).'"';
                    };
                    
                    do_sql('
                            INSERT INTO `cashmaster`.`UserConfiguration`
                                (
                                    `UserFamilyName`,
                                    `OldUserFamilyName`,
                                    `UserFirstName`,
                                    `OldUserFirstName`,
                                    `UserPatronymic`,
                                    `OldUserPatronymic`,
                                    `GenetiveName`,
                                    `OldGenetiveName`,
                                    `InstrName`,
                                    `OldInstrName`,
                                    `UserPost`,
                                    `OldUserPost`,
                                    `Phone`,
                                    `OldPhone`,
                                    `UserRoleId`,
                                    `UserCreateDate`,
                                    `UserLogin`,
                                    `UserPassword`,
                                    `UserSalt`,
                                    `BadLogAttempts`,
                                    `UserIsBlocked`,
                                    `UserLogicallyDeleted`,
                                    `ChangePassword`,
                                    `LastChangePasswordDate`,
                                    `CreatedBy`
                                 )
                            VALUES
                                (
                                    '.$data[0].',
                                    '.$data[0].',
                                    '.$data[1].',
                                    '.$data[1].',
                                    '.$data[2].',
                                    '.$data[2].',

                                    '.$data[3].',
                                    '.$data[3].',

                                    '.$data[4].',
                                    '.$data[4].',


                                    '.$data[5].',
                                    '.$data[5].',
                                    '.$data[6].',
                                    '.$data[6].',
                                    '.$data[7].',
                                    CURRENT_TIMESTAMP,
                                    '.$data[8].',
                                    "'.md5($pass).'",
                                    "depricated",
                                    0,
                                    1,
                                    0,
                                    1,
                                    CURRENT_TIMESTAMP,
                                    "'.$_SESSION[$program]['UserConfiguration']['UserId'].'"
                                )
                    ;');
                    system_log($events[3].' login: '.$login
                            .' user: '.$data[0].' '.$data[1].'  '.$data[2].' post: '.$data[5]
                            .' by: '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' IP: '.$_SERVER['REMOTE_ADDR']);

                    // Получаем UserId нового пользователя
                    $row = fetch_row_from_sql('
                        SELECT
                            `UserConfiguration`.`UserId`
                        FROM 
                            `cashmaster`.`UserConfiguration`
                        WHERE
                            `UserLogin`="'.addslashes($login).'"
                    ;');
                    
                    // Добавляем любой разрешенный адрес для данного пользователя в таблицу UsersIP
                    do_sql('
                        INSERT INTO `cashmaster`.`UsersIP`
                            (
                                `UserId`,
                                `IP`
                            )
                        VALUES
                            (
                                "'.addslashes($row[0]).'",
                                "%"
                            )
                    ;');
                    
                    $data['success'] = $w[1].': '.$family;
                    include 'app/view/success_message.php';
                    ?>  
                         <br/>
                         <?php echo htmlfix($l[6]); ?>: <font style="font-size: 16px; color: blue;"><?php echo $login; ?></font>
                         <br/>
                         <?php echo htmlfix($_SESSION[$program]['lang']['pass']); ?>: <font style="font-size: 16px; color: blue;"><?php echo $pass; ?></font>
                         <br/>
                         <br/>
                    <?php
                } else {
                    //такой клиент уже есть
                    $data['error'] = $w[0];
                    include 'app/view/error_message.php';
                };
                    ?>
                        <a href="?c=users" class="btn btn-large btn-primary"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></a>
                    <?php
                    echo '</div>';
                do_sql('UNLOCK TABLES;');                
                exit;
            } else {
                
                $data = explode('|',$_POST['newdata']);
                if(!isset($_POST['cancelation'])) {
                    // проверяем есть ли клиент с таким кодом БИКы
                    $row = get_array_from_sql('
                    SELECT
                        *
                    FROM 
                        `cashmaster`.`UserConfiguration`
                    WHERE
                        `UserConfiguration`.`UserLogin`="'.addslashes($data[4]).'"
                    ;');

                    if (count($row)>0) {
                        echo '<div class="container">';
                        //такой клиент уже есть
                        $data['error'] = $w[0];
                        include 'app/view/error_message.php';
                        ?>
                            <a href="?c=users" class="btn btn-large btn-primary"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></a>
                        <?php
                        echo '</div>';
                        exit;
                    };
                    
                    $t = explode('|',$_SESSION[$program]['lang']['user_add_confirm_texts']);     
                    ?>
                        <style>
                            table.confirm th {
                                text-align: left;
                            }
                            table.confirm td {
                                color: blue;
                                font-size: 16px;
                            }
                        </style>
                        <div class="container">
                            <h3><?php echo htmlfix($t[0]); ?>:</h3>
                            <br/>
                            <table class="confirm">
                                <tr>
                                    <th><?php echo htmlfix($t[1]); ?>: </th>
                                    <td><?php echo htmlfix($data[0].' '.$data[1].' '.$data[2]); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo htmlfix($l[4]); ?>: </th>
                                    <td><?php echo htmlfix($data[5]); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo htmlfix($l[5]); ?>: </th>
                                    <td><?php 
                                        $role = fetch_row_from_sql('
                                            SELECT
                                                `Roles`.`RoleLabel`
                                            FROM 
                                                `cashmaster`.`Roles`
                                            WHERE
                                                `Roles`.`RoleId`="'.addslashes($data[5]).'"
                                        ;');
                                        echo htmlfix($role[0]); 
                                    ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo htmlfix($l[6]); ?>: </th>
                                    <td><?php echo htmlfix($data[8]); ?></td>
                                </tr>
                            </table>
                                <form action="?c=user_add" method="POST">
                                    <input type="hidden" name="action" value="add_new"/>
                                    <input 
                                           type="hidden"
                                           name="newdata"
                                           value="<?php echo $_POST['newdata']; ?>"/>
                                    <input 
                                           type="hidden"
                                           name="names"
                                           value="<?php echo $_POST['names']; ?>"/>
                                    <br/>                                    
                                    <br/>
                                    <input type="submit"
                                           class="btn btn-info btn-large"
                                           name="confirmation" value="<?php echo htmlfix($t[2]); ?>" />
                                    <input type="submit"
                                           class="btn btn-warning btn-large"
                                           name="cancelation" value="<?php echo htmlfix($t[3]); ?>" />
                                </form>
                        </div>
                    <?php
                    exit;
                };
            }
        };
        
?>
<script src="js/translit.js"></script>
<script>
    function make_login() {
        var family = $('input#family')[0].value;
        var name = $('input#name')[0].value;
        var patronymic = $('input#patronymic')[0].value;
        var login = family.substr(0,parseInt(Math.random()*5 + 2)) 
                        + name.substr(0,parseInt(Math.random()*5 + 2)) 
                        + patronymic.substr(0,parseInt(Math.random()*5 + 2));
            login = login.substr(0,16);
        $('input#login').val(translit(login.replace("ь",'').toLowerCase()));
    };
    function add_client(){
        var inputs = $('.stat');
        var newdata = [];
        var names = [];
        $(inputs).each(function (){
            $(this).css('background-color','white');
            newdata[newdata.length] = this.value;
            names[names.length] = $(this).attr('name');           
        });
        if (check_fields(inputs)) {
            // Добавляем клиента
            $('input#newdata').val(newdata.join("|"));
            $('input#names').val(names.join("|"));
            $('form#add').submit();
        };
    };
    function check_fields(inputs) {
        if (inputs[0].value=='' 
                || inputs[1].value=='' 
                || inputs[2].value=='' 
                || inputs[3].value=='' 
                || inputs[4].value==''
                || inputs[5].value==''
                || inputs[6].value==''
                || inputs[7].value==''
                || inputs[8].value=='') {
            if (inputs[0].value=='') $(inputs[0]).css('background-color','yellow');
            if (inputs[1].value=='') $(inputs[1]).css('background-color','yellow');
            if (inputs[2].value=='') $(inputs[2]).css('background-color','yellow');
            if (inputs[3].value=='') $(inputs[3]).css('background-color','yellow');
            if (inputs[4].value=='') $(inputs[4]).css('background-color','yellow');
            if (inputs[5].value=='') $(inputs[5]).css('background-color','yellow');
            if (inputs[6].value=='') $(inputs[6]).css('background-color','yellow');
            if (inputs[7].value=='') $(inputs[7]).css('background-color','yellow');
            if (inputs[8].value=='') $(inputs[8]).css('background-color','yellow');
            alert('<?php echo htmlfix($w[2]); ?>');
            return false;
        } else {
            return true;
        };
    };
    $(document).ready(function(e){
        $('.stat').each(function(e){
            $(this).bind('focus',function (){
                $(this).css('background-color','white');
            });
        });
    });
</script>
<div class='container'>
    <style>
        table.edit_client th {
            text-align: right;
            vertical-align: top;
            padding: 5px;
        }
        table.edit_client input {
            margin: 4px;
        }
    </style>
    <table class='edit_client'>
        <tr>
            <th><?php echo htmlfix($l[1]); ?>:</th>
            <td>
                <input 
                    id="family"
                    class='span3 stat'
                    placeholder='<?php echo htmlfix($l[1]); ?>' 
                    type='text' 
                    name='UserFamilyName'
                    value='<?php echo htmlfix($data[0]); ?>' 
                    onblur="make_login();"
                    maxlength='24'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[2]); ?>:</th>
            <td>
                <input 
                    id="name"
                    class='span3 stat'
                    placeholder='<?php echo htmlfix($l[2]); ?>' 
                    type='text' 
                    name='UserFirstName'
                    value='<?php echo htmlfix($data[1]); ?>' 
                    onblur="make_login();"
                    maxlength='24'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[3]); ?>:</th>
            <td>
                <input 
                    id="patronymic"
                    class='span3 stat'
                    placeholder='<?php echo htmlfix($l[3]); ?>' 
                    type='text' 
                    name='UserPatronymic'
                    onblur="make_login();"
                    value='<?php echo htmlfix($data[2]); ?>' 
                    maxlength='24'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        
        <?php 
            if ($_SESSION[$program]['SystemConfiguration']['UseGenitiveName']==1) {
                ?>
                    <tr>
                        <th><?php echo htmlfix($l[11]); ?>:</th>
                        <td>
                            <input 
                                id="patronymic"
                                class='span3 stat'
                                placeholder='<?php echo htmlfix($l[11]); ?>' 
                                type='text' 
                                name='GenetiveName'
                                onblur="make_login();"
                                value='<?php echo htmlfix($data[3]); ?>' 
                                maxlength='24'/>
                            <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo htmlfix($l[12]); ?>:</th>
                        <td>
                            <input 
                                id="patronymic"
                                class='span3 stat'
                                placeholder='<?php echo htmlfix($l[12]); ?>' 
                                type='text' 
                                name='InstrName'
                                onblur="make_login();"
                                value='<?php echo htmlfix($data[4]); ?>' 
                                maxlength='24'/>
                            <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
                        </td>
                    </tr>
                <?php 
            };
        ?>
        
        
        <tr>
            <th><?php echo htmlfix($l[4]); ?>:</th>
            <td>
                <input 
                    class='span3 stat'
                    placeholder='<?php echo htmlfix($l[4]); ?>' 
                    type='text' 
                    name='UserPost'
                    value='<?php echo htmlfix($data[5]); ?>' 
                    maxlength='80'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[10]); ?>:</th>
            <td>
                <input 
                    class='span3 stat'
                    placeholder='<?php echo htmlfix($l[10]); ?>' 
                    type='text' 
                    name='Phone'
                    value='<?php echo htmlfix($data[6]); ?>' 
                    maxlength='80'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <?php 
            
            $roles = get_array_from_sql('
                SELECT
                    `Roles`.`RoleId`,
                    `Roles`.`RoleLabel`
                FROM 
                    `cashmaster`.`Roles`
                WHERE
                    `Roles`.`RoleId`<>"5"
                ORDER BY 
                    `Roles`.`RoleLabel` ASC
            ;');
            
        ?>
        <tr>
            <th><?php echo htmlfix($l[5]); ?>:</th>
            <td>
                <select 
                        class="span3 stat"
                        name="UserRoleId"
                        >
                    <option value=""></option>
                    <?php 
                        foreach ($roles as $value) {
                            echo '<option value="'.htmlfix($value[0]).'">'.htmlfix($value[1]).'</option>';
                        };
                    ?>
                </select>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[6]); ?>:</th>
            <td>
                <input 
                    id="login"
                    class='span2 stat'
                    placeholder='<?php echo htmlfix($l[6]); ?>' 
                    type='text' 
                    name='UserLogin'
                    value='<?php echo htmlfix($data[8]); ?>' 
                    maxlength='16'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
                <br/>
                <font style='font-family: serif;font-size: 15px;color:red;'>*</font>
                <font style='font-size: 10px;color:red;'>
                     - <?php echo htmlfix($l[9]); ?>
            </td>
        </tr>
    </table>
    <br/>
        <button onclick="add_client();" 
            class='btn btn-large btn-info'><?php echo htmlfix($b[0]); ?></button>
        <button onclick="window.location.replace('?c=users');" 
            class='btn btn-large btn-warning'><?php echo htmlfix($b[1]); ?></button>
    <form style="display:none;" id="add" action="?c=user_add" method="POST">
        <input type="hidden" name="action" value="add_new"/>
        <input id="newdata" type="hidden" name="newdata" value=""/>
        <input id="names" type="hidden" name="names" value=""/>
    </form>
</div>
