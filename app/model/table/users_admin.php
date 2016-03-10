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
    
    if(isset($_POST['code']) AND isset($_POST['name'])) {
        $users = get_array_from_sql('
            SELECT
                `UserConfiguration`.`UserId`,
                CONCAT(`UserConfiguration`.`UserFamilyName`," ",
                `UserConfiguration`.`UserFirstName`," ",
                `UserConfiguration`.`UserPatronymic`),
                `UserConfiguration`.`UserPost`,
                RoleLabel,
                Phone,
                IF(`UserConfiguration`.`UserIsBlocked`>0,"X",""),
                IF(`UserConfiguration`.`UserLogicallyDeleted`>0,"X","")
            FROM 
                `cashmaster`.`UserConfiguration`
            LEFT JOIN
                Roles ON Roles.RoleId = UserConfiguration.UserRoleId
            LEFT JOIN
                CashRooms ON CashRooms.Id=UserConfiguration.CashRoomId
            WHERE
                #UserLogicallyDeleted="0"
                UserRoleId IN ('.  implode(',', $roles).')
                AND UserFamilyName like "%'.  addslashes($_POST['name']).'%"
                AND UserPost like "%'.  addslashes($_POST['code']).'%"
                AND UserConfiguration.UserId<>"'.$_SESSION[$program]['UserConfiguration']['UserId'].'"
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
                IF(`UserConfiguration`.`UserIsBlocked`>0,"X",""),
                IF(`UserConfiguration`.`UserLogicallyDeleted`>0,"X","")
            FROM 
                `cashmaster`.`UserConfiguration`
            LEFT JOIN
                Roles ON Roles.RoleId = UserConfiguration.UserRoleId
            LEFT JOIN
                CashRooms ON CashRooms.Id=UserConfiguration.CashRoomId
            WHERE
                #UserLogicallyDeleted="0"
                UserRoleId IN ('.  implode(',', $roles).')
                AND UserConfiguration.UserId<>"'.$_SESSION[$program]['UserConfiguration']['UserId'].'"
            '.$order.'
        ;');            
    };

    
    ($_POST['order_by']=='CustomerName' AND $_POST['order_type']=='ASC') ? '"CustomerName","DESC"':'"CustomerName","ASC"';
    
    unset($table);
    $table['data'] = $users;
    $table['header'] = explode('|',$_SESSION[$program]['lang']['users_admin_table_header']);
    $table['width'] = array( 300,200,120,170,80);
    $table['align'] = array( 'left','left','left','left','left','center','center');
    $table['th_onclick']=array(
        'order_by('.(($_POST['order_by']=='UserFamilyName' AND $_POST['order_type']=='ASC') ? '\'UserFamilyName\',\'DESC\'':'\'UserFamilyName\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='UserPost' AND $_POST['order_type']=='ASC') ? '\'UserPost\',\'DESC\'':'\'UserPost\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='RoleLabel' AND $_POST['order_type']=='ASC') ? '\'RoleLabel\',\'DESC\'':'\'RoleLabel\',\'ASC\'').');',
        ';',
        ';',';');
    $table['tr_onclick']='open_user(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);




?>
