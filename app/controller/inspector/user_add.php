<?php

/*
 * Контроллер для работы со списком клиентов
 */
        if (!isset($c)) exit;

        $data['title'] = "Создание инспектора";
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';

        $data = explode('|','|||||||||||');

        if (isset($_POST['action']) AND $_POST['action']=='add_new') {
            if (isset($_POST['confirmation'])) {
                echo '<div class="container">';
                $names = explode('|','UserFamilyName|UserFirstName|UserPatronymic|UserPatronymic|UserRoleId|UserLogin');
                $data = explode('|',$_POST['newdata']);
                
                do_sql('LOCK TABLES UserConfiguration WRITE, SystemLog WRITE;');
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
                    $login = $data[4];
                    $pass = rand(1000, 9999);
                    
                    foreach ($data as $key=>$value) {
                        $data[$key] = '"'.addslashes($value).'"';
                    };
                    
                    do_sql('
                            INSERT INTO `cashmaster`.`UserConfiguration`
                                (
                                    `UserFamilyName`,
                                    `UserFirstName`,
                                    `UserPatronymic`,
                                    `UserPost`,
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
                                    '.$data[1].',
                                    '.$data[2].',
                                    '.$data[3].',
                                    4,
                                    CURRENT_TIMESTAMP,
                                    '.$data[4].',
                                    "'.md5($pass).'",
                                    "depricated",
                                    0,
                                    0,
                                    0,
                                    1,
                                    CURRENT_TIMESTAMP,
                                    "'.$_SESSION[$program]['UserConfiguration']['UserId'].'"
                                )
                    ;');
                    system_log($events[3].' login:'.$data[4]
                            .' user: '.$data[0].' '.$data[1].'  '.$data[2].' post: '.$data[3]
                            .' creator: '.$_SESSION[$program]['user_fio']);
                    $data['success'] = 'Пользователь '.$family.' успешно добавлен.';
                    include 'app/view/success_message.php';
                    ?>  
                         <br/>
                         Логин: <font style="font-size: 16px; color: blue;"><?php echo $login; ?></font>
                         <br/>
                         Пароль: <font style="font-size: 16px; color: blue;"><?php echo $pass; ?></font>
                         <br/>
                         <br/>
                    <?php
                } else {
                    //такой клиент уже есть
                    $data['error'] = 'Пользователь '.$data[0].' с такими данными уже существует, добавить невозможно.';
                    include 'app/view/error_message.php';
                };
                    ?>
                        <a href="?c=users" class="btn btn-large btn-primary">Назад к списку</a>
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
                        $data['error'] = 'Пользователь с такими данными уже существует, добавить невозможно.';
                        include 'app/view/error_message.php';
                        ?>
                            <a href="?c=users" class="btn btn-large btn-primary">Назад к списку</a>
                        <?php
                        echo '</div>';
                        exit;
                    };
                    
                        
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
                            <h3>Подтвердите создание пользователя:</h3>
                            <br/>
                            <table class="confirm">
                                <tr>
                                    <th>ФИО: </th>
                                    <td><?php echo htmlfix($data[0].' '.$data[1].' '.$data[2]); ?></td>
                                </tr>
                                <tr>
                                    <th>Должность: </th>
                                    <td><?php echo htmlfix($data[3]); ?></td>
                                </tr>
                                <tr>
                                    <th>Логин: </th>
                                    <td><?php echo htmlfix($data[4]); ?></td>
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
                                           name="confirmation" value="Подтвердить добавление" />
                                    <input type="submit"
                                           class="btn btn-warning btn-large"
                                           name="cancelation" value="Отменить" />
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
                || inputs[4].value=='') {
            if (inputs[0].value=='') $(inputs[0]).css('background-color','yellow');
            if (inputs[1].value=='') $(inputs[1]).css('background-color','yellow');
            if (inputs[2].value=='') $(inputs[2]).css('background-color','yellow');
            if (inputs[3].value=='') $(inputs[3]).css('background-color','yellow');
            if (inputs[4].value=='') $(inputs[4]).css('background-color','yellow');
            alert('Поля помеченне звездочкой обязательны к заполнению');
            return false;
        } else {
            return true;
        };
    };
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
            <th>Фамилия:</th>
            <td>
                <input 
                    id="family"
                    class='span3 stat'
                    placeholder='Фамилия' 
                    type='text' 
                    name='UserFamilyName'
                    onfocus="$(this).css('background-color','white');"
                    value='<?php echo htmlfix($data[0]); ?>' 
                    onblur="make_login();"
                    maxlength='24'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <tr>
            <th>Имя:</th>
            <td>
                <input 
                    id="name"
                    class='span3 stat'
                    placeholder='Имя' 
                    type='text' 
                    name='UserFirstName'
                    onfocus="$(this).css('background-color','white');"
                    value='<?php echo htmlfix($data[1]); ?>' 
                    onblur="make_login();"
                    maxlength='24'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <tr>
            <th>Отчество:</th>
            <td>
                <input 
                    id="patronymic"
                    class='span3 stat'
                    placeholder='Отчество' 
                    type='text' 
                    name='UserPatronymic'
                    onfocus="$(this).css('background-color','white');"
                    onblur="make_login();"
                    value='<?php echo htmlfix($data[2]); ?>' 
                    maxlength='24'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <tr>
            <th>Должность:</th>
            <td>
                <input 
                    class='span3 stat'
                    placeholder='Должность' 
                    type='text' 
                    name='UserPost'
                    onfocus="$(this).css('background-color','white');"
                    value='<?php echo htmlfix($data[3]); ?>' 
                    maxlength='80'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <tr>
            <th>Логин:</th>
            <td>
                <input 
                    id="login"
                    class='span2 stat'
                    placeholder='Логин' 
                    type='text' 
                    name='UserLogin'
                    onfocus="$(this).css('background-color','white');"
                    value='<?php echo htmlfix($data[4]); ?>' 
                    maxlength='16'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
                <br/>
                <font style='font-family: serif;font-size: 15px;color:red;'>*</font>
                <font style='font-size: 10px;color:red;'>
                     - поля обязательны для заполнения
            </td>
        </tr>
    </table>
    <br/>
        <button onclick="add_client();" 
            class='btn btn-large btn-info'>Добавить</button>
        <button onclick="window.location.replace('?c=users');" 
                class='btn btn-large btn-warning'>Отменить</button>
    <form style="display:none;" id="add" action="?c=user_add" method="POST">
        <input type="hidden" name="action" value="add_new"/>
        <input id="newdata" type="hidden" name="newdata" value=""/>
        <input id="names" type="hidden" name="names" value=""/>
    </form>
</div>
