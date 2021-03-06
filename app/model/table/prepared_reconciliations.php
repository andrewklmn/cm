<?php

/*
 * List of deffered reconciliations
 */

    if (!isset($c)) exit;

        
    ?>
    <script>
        function open_recs(elem) {
            var card = $(elem).find('td')[0].innerHTML;
            var input = $('input#focus')[0];
            $(input).prop('value',card);
            $(input).focus();
        }
    </script>
    <?php
    $sql = '
        SELECT
            DepositRecs.DepositRecId
        FROM
            DepositRecs
        LEFT JOIN
            UserConfiguration ON UserConfiguration.UserId = DepositRecs.PrepOperatorId
        WHERE
            `UserConfiguration`.`CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            AND DepositRecs.FwdToSupervisor<>1
            AND IFNULL(DepositRecs.ReconcileStatus,0)=0
            AND `DepositRecs`.`RecOperatorId` = 0
        GROUP BY DepositRecs.DepositRecId
    ;';
    $row = get_array_from_sql($sql);

    if(count($row)>0) {
        $where = array();
        foreach ($row as $key => $value) {
            $where[] = 'DepositRecs.DepositRecId='.$value[0];
        };
        unset($table);
        $sql = '
            SELECT
                DepositRecs.DepositRecId,
                DepositRecs.CardNumber,
                `CashRooms`.`CashRoomName`,
                CONCAT(
                    `UserConfiguration`.`UserFamilyName`," ",
                    `UserConfiguration`.`UserFirstName`," ",
                    `UserConfiguration`.`UserPatronymic`),
                DATE_FORMAT(`DepositRecs`.`RecCreateDatetime`,"%d/%m/%Y %H:%i:%s"),
                DATE_FORMAT(`DepositRecs`.`RecLastChangeDatetime`,"%d/%m/%Y %H:%i:%s")
            FROM 
                DepositRecs
            LEFT JOIN
                UserConfiguration ON UserConfiguration.UserId=`DepositRecs`.`PrepOperatorId`
            LEFT JOIN
                CashRooms ON CashRooms.Id = UserConfiguration.CashRoomId
            WHERE
                `UserConfiguration`.`CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                AND DepositRecs.FwdToSupervisor<>1
                AND IFNULL(DepositRecs.ReconcileStatus,0)=0
                AND `DepositRecs`.`RecOperatorId` = 0
            GROUP BY DepositRecs.DepositRecId
        ;';

        
        $table['data'] = get_array_from_sql($sql);
        $table['header'] = explode('|', $_SESSION[$program]['lang']['prepared_reconciliations_header']);
        $table['width'] = array( 100,100,280,150,150);
        $table['align'] = array( 'center','center','center','center','center','center');
        $table['th_onclick']=array(';',';',';',';');
        $table['tr_onclick']='open_recs(this.parentNode);';
        $table['title'] = $_SESSION[$program]['lang']['prepared_reconciliations'];
        $table['hide_id'] = 1;
        //$table['selector'] = true;
        include_once 'app/view/draw_select_table.php';
        draw_select_table($table);
        unset($table);
    };


?>
