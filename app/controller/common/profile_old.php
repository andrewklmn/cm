<?php

/*
 * профиль пользователя
 */

    if (!isset($c)) exit;

    if (isset($_POST['action']) AND $_POST['action']=='update') {
        include 'app/view/html_header.php';
        $oldvalue = explode('|', $_POST['olddata']);
        $newvalue = explode('|', $_POST['newdata']);
        // Проверяем не изменились ли данные в базе
        $row = fetch_row_from_sql('
            SELECT
                `UserConfiguration`.`UserFamilyName`,
                `UserConfiguration`.`UserFirstName`,
                `UserConfiguration`.`UserPatronymic`,
                `UserConfiguration`.`UserPost`,
                `UserConfiguration`.`Phone`,
                `UserConfiguration`.`InterfaceLanguage`
            FROM 
                `cashmaster`.`UserConfiguration`
            WHERE
                `UserConfiguration`.`UserId`="'.addslashes($_SESSION[$program]['UserConfiguration']['UserId']).'"
        ;');
        if ( $oldvalue==$row ) {
            do_sql('
                UPDATE `cashmaster`.`UserConfiguration`
                SET
                    `UserFamilyName` = "'.addslashes($newvalue[0]).'",
                    `UserFirstName` = "'.addslashes($newvalue[1]).'",
                    `UserPatronymic` = "'.addslashes($newvalue[2]).'",
                    `UserPost` = "'.addslashes($newvalue[3]).'",
                    `Phone` = "'.addslashes($newvalue[4]).'",
                    `InterfaceLanguage` = "'.addslashes($newvalue[5]).'"
                WHERE
                    `UserConfiguration`.`UserId`="'.addslashes($_SESSION[$program]['UserConfiguration']['UserId']).'"
            ;');
            $_SESSION[$program]['UserConfiguration'] = get_user_config($_SESSION[$program]['user_id']);
            unset($_SESSION[$program]['lang_loaded']);
            echo 0;
            exit;
        } else {
            echo 1;
            echo implode('|', $row);
            exit;
        };
    };
    
    include './app/model/menu.php';
    
    $data['title'] = $_SESSION[$program]['lang']['profile_title'];
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/forms/details_css.php';
    include './app/view/update_record.php';
    
    // Список доступных языков интерфейса
    $list = scandir('app/model/lang/');
    $l = explode('|', $_SESSION[$program]['lang']['profile_labels']);
?>
<div class="container">
    <table class="details">
        <tr>
            <th><?php echo htmlfix($l[0]); ?>:</th>
            <td><input type="text" class="stat" name="UserFamileName" 
                       onblur="update_record(this,'?c=profile');"
                       oldvalue="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['UserFamilyName']) ?>"
                       value="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['UserFamilyName']) ?>"
                       maxlength='24'/></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[1]); ?>:</th>
            <td><input type="text" class="stat" name="UserFirstName" 
                       onblur="update_record(this,'?c=profile');"
                       oldvalue="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['UserFirstName']) ?>"
                       value="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['UserFirstName']) ?>"
                       maxlength='24'/></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[2]); ?>:</th>
            <td><input type="text" class="stat" name="UserPatronymic" 
                       onblur="update_record(this,'?c=profile');"
                       oldvalue="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['UserPatronymic']) ?>"
                       value="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['UserPatronymic']) ?>"
                       maxlength='24'/></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[3]); ?>:</th>
            <td><input type="text" class="stat" name="UserPost" 
                       onblur="update_record(this,'?c=profile');"
                       oldvalue="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['UserPost']) ?>"
                       value="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['UserPost']) ?>"
                       maxlength='80'/></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[8]); ?>:</th>
            <td><input type="text" class="stat" name="Phone" 
                       onblur="update_record(this,'?c=profile');"
                       oldvalue="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['Phone']) ?>"
                       value="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['Phone']) ?>"
                       maxlength='45'/></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[4]); ?>:</th>
            <td style="text-align: left;"><?php echo htmlfix($_SESSION[$program]['UserConfiguration']['UserLogin']) ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[5]); ?>:</th>
            <td style="text-align: left;margin: 0px;">
                <select class="stat" name="InterfaceLanguage" 
                       onchange="update_record(this,'?c=profile');"
                       oldvalue="<?php echo htmlfix($_SESSION[$program]['UserConfiguration']['InterfaceLanguage']) ?>">
                    <?php 
                        foreach ($list as $value) {
                            if ($value!='.' AND $value!='..') {
                                $t = explode('.', $value);
                                $t[0] = ucfirst(strtolower($t[0]));
                                if ($t[0]==$_SESSION[$program]['UserConfiguration']['InterfaceLanguage']) {
                                    echo '<option value="',htmlfix($t[0]),'" selected>',htmlfix($t[0]),'</option>';
                                } else {
                                    echo '<option value="',htmlfix($t[0]),'">',htmlfix($t[0]),'</option>';
                                };
                            };
                        };
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <br/>
    <font style="color:darkred;">
        <?php echo htmlfix($l[6]); ?>:
        <?php echo get_password_exired_date($_SESSION[$program]['UserConfiguration']['UserId']); ?>
    </font>
    <a href="?c=change_password" class="btn btn-mini btn-danger"><?php echo htmlfix($l[7]); ?></a>
    <br/>
    <br/>
    <a href="?c=index" class="btn btn-primary btn-large"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?></a>
</div>