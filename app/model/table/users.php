<?php

/*
 * List of unreconciled deposits
 * 
 * $roles = array( 1,2,3,4,5 ); - массив допустимых ролей в таблице
 * 
 */

    if (!isset($c)) exit;
    
    ?>
    <script>
        function order_by(name,order) {
            $('input#order_by').attr('value',name);
            $('input#order_type').attr('value',order);
            $('form#order').submit();
        }
        function open_user(elem) {
            window.location.replace('?c=user_edit&id=' + elem.id);
        };
    </script>
    <?php
    
    if(isset($_POST['code']) AND isset($_POST['name']) AND isset($_POST['kassa'])) {
        $users = get_array_from_sql('
            SELECT
                `UserConfiguration`.`UserId`,
                CONCAT(`UserConfiguration`.`UserFamilyName`," ",
                `UserConfiguration`.`UserFirstName`," ",
                `UserConfiguration`.`UserPatronymic`),
                `UserConfiguration`.`UserPost`,
                RoleLabel,
                Phone,
                IFNULL(CashRoomName,""),
                IF(Blind="1","X",""),
                ScenarioName
            FROM 
                `cashmaster`.`UserConfiguration`
            LEFT JOIN
                Scenario ON Scenario.ScenarioId = UserConfiguration.CurrentScenario
            LEFT JOIN
                Roles ON Roles.RoleId = UserConfiguration.UserRoleId
            LEFT JOIN
                CashRooms ON CashRooms.Id=UserConfiguration.CashRoomId
            WHERE
                UserLogicallyDeleted="0"
                AND UserRoleId IN ('.  implode(',', $roles).')
                AND UserFamilyName like "%'.  addslashes($_POST['name']).'%"
                AND UserPost like "%'.  addslashes($_POST['code']).'%"
                AND IFNULL(CashRoomName,"") like "%'.addslashes($_POST['kassa']).'%"
                #AND UserConfiguration.UserId<>"'.$_SESSION[$program]['UserConfiguration']['UserId'].'"
            '.$order.'
        ;');            
    } else {
        $users = get_array_from_sql('
            SELECT
                `UserConfiguration`.`UserId`,
                CONCAT(`UserConfiguration`.`UserFamilyName`," ",
                `UserConfiguration`.`UserFirstName`," ",
                `UserConfiguration`.`UserPatronymic`),
                `UserConfiguration`.`UserPost`,
                RoleLabel,
                Phone,
                IFNULL(CashRoomName,""),
                IF(Blind="1","X",""),
                ScenarioName
            FROM 
                `cashmaster`.`UserConfiguration`
            LEFT JOIN
                Scenario ON Scenario.ScenarioId = UserConfiguration.CurrentScenario
            LEFT JOIN
                Roles ON Roles.RoleId = UserConfiguration.UserRoleId
            LEFT JOIN
                CashRooms ON CashRooms.Id=UserConfiguration.CashRoomId
            WHERE
                UserLogicallyDeleted="0"
                AND UserRoleId IN ('.  implode(',', $roles).')
                #AND UserConfiguration.UserId<>"'.$_SESSION[$program]['UserConfiguration']['UserId'].'"
            '.$order.'
        ;');            
    };

    
    ($_POST['order_by']=='CustomerName' AND $_POST['order_type']=='ASC') ? '"CustomerName","DESC"':'"CustomerName","ASC"';
    
    unset($table);
    $table['data'] = $users;
    $table['header'] = explode('|',$_SESSION[$program]['lang']['users_table_header']);
    $table['width'] = array( 220,130,100,100,60,80,350);
    $table['align'] = array( 'left','left','left','left','left','center','center','left');
    $table['th_onclick']=array(
        'order_by('.(($_POST['order_by']=='UserFamilyName' AND $_POST['order_type']=='ASC') ? '\'UserFamilyName\',\'DESC\'':'\'UserFamilyName\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='UserPost' AND $_POST['order_type']=='ASC') ? '\'UserPost\',\'DESC\'':'\'UserPost\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='RoleLabel' AND $_POST['order_type']=='ASC') ? '\'RoleLabel\',\'DESC\'':'\'RoleLabel\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='CashRoomName' AND $_POST['order_type']=='ASC') ? '\'CashRoomName\',\'DESC\'':'\'CashRoomName\',\'ASC\'').');',
        ';',';',';',';',';',';');
    if ($_SESSION[$program]['UserConfiguration']['UserRoleId']==2) {
        $table['tr_onclick']='open_user(this.parentNode);';
    } else {
        $table['tr_onclick']=';';
    };
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);




?>
