<?php

/*
 * 
 */

    if (!isset($c)) exit;
    
    ?>
    <script>
        function open_reportset(elem) {
            window.location.replace('?c=reportset_view&id=' + elem.id);
        };
    </script>
    <?php
    
    if(isset($_POST['date'])) {
        $users = get_array_from_sql('
            SELECT
                `ReportSets`.`SetId`,
                `ReportSets`.`SetDateTime`,
                CONCAT(`UserConfiguration`.`UserFamilyName`," ",
                `UserConfiguration`.`UserFirstName`," ",
                `UserConfiguration`.`UserPatronymic`),
                `UserConfiguration`.`UserPost`,
                `CashRooms`.`CashRoomName`
            FROM 
                `cashmaster`.`ReportSets` 
            LEFT JOIN
                UserConfiguration ON `ReportSets`.`CreatedBy`=UserConfiguration.UserId
            LEFT JOIN
                CashRooms ON CashRooms.Id = `ReportSets`.`CashRoomId`
            WHERE
                `ReportSets`.`SetDateTime` like "%'.addslashes($_POST['date']).'%"
                AND `ReportSets`.`SetDateTime`<>"0000-00-00 00:00:00"
                AND `ReportSets`.`CashRoomId` = "'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            ORDER BY 
                `ReportSets`.`SetId` DESC
        ;');            
    } else {
        $users = get_array_from_sql('
            SELECT
                `ReportSets`.`SetId`,
                `ReportSets`.`SetDateTime`,
                CONCAT(`UserConfiguration`.`UserFamilyName`," ",
                `UserConfiguration`.`UserFirstName`," ",
                `UserConfiguration`.`UserPatronymic`),
                `UserConfiguration`.`UserPost`,
                `CashRooms`.`CashRoomName`
            FROM 
                `cashmaster`.`ReportSets`
            LEFT JOIN
                UserConfiguration ON `ReportSets`.`CreatedBy`=UserConfiguration.UserId
            LEFT JOIN
                CashRooms ON CashRooms.Id = `ReportSets`.`CashRoomId`
            WHERE
                `ReportSets`.`CashRoomId` = "'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                AND `ReportSets`.`SetDateTime`<>"0000-00-00 00:00:00"
            ORDER BY 
                `ReportSets`.`SetId` DESC
        ;');            
    };

    
    unset($table);
    $table['data'] = $users;
    $table['header'] = explode('|',$_SESSION[$program]['lang']['reports_archive_table_header']);
    $table['width'] = array( 150,300,300,150);
    $table['align'] = array( 'center','left','left','center','center');
    $table['th_onclick']=array(';',';',';',';',';',';');
    $table['tr_onclick']='open_reportset(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);




?>
