<?php

/*
 * List of deffered reconciliations
 */

    if (!isset($c)) exit;

        
    ?>
    <script>
        function open_recs(elem) {
            window.location.replace('?c=recon_view&id=' + elem.id);   
        }
    </script>
    <?php
    $sql = '
        SELECT
            DepositRecs.DepositRecId
        FROM
            DepositRecs
        LEFT JOIN
            DepositRuns ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
        LEFT JOIN 
            Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
        WHERE
            `Machines`.`CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            AND DepositRecs.FwdToSupervisor<>1
            #AND DepositRuns.DepositRecId > 0
            AND IFNULL(DepositRecs.ReconcileStatus,0)=0
        GROUP BY DepositRecs.DepositRecId, DepositRuns.DataSortCardNumber
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
                t1.DataSortCardNumber,
                t1.CashRoomName,
                CONCAT(
                    `UserConfiguration`.`UserFamilyName`," ",
                    `UserConfiguration`.`UserFirstName`," ",
                    `UserConfiguration`.`UserPatronymic`),
                DATE_FORMAT(`DepositRecs`.`RecCreateDatetime`,"%d/%m/%Y %H:%i:%s"),
                DATE_FORMAT(`DepositRecs`.`RecLastChangeDatetime`,"%d/%m/%Y %H:%i:%s")
            FROM 
                DepositRecs
           LEFT JOIN
                UserConfiguration ON UserId=`DepositRecs`.`RecOperatorId`
           LEFT JOIN
                (SELECT
                    DepositRecId,
                    DataSortCardNumber,
                    CashRooms.CashRoomName,
                    CashRooms.Id
                From
                    DepositRuns
                LEFT JOIN 
                    Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
                LEFT JOIN
                    CashRooms ON CashRooms.Id=Machines.CashRoomId
                GROUP BY DepositRecId) as t1 ON `DepositRecs`.`DepositRecId`=t1.DepositRecId
           WHERE 
                t1.Id="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                AND DepositRecs.FwdToSupervisor<>1
                AND IFNULL(DepositRecs.ReconcileStatus,0)=0
        ;';
        
        $table['id'] = 'recs';
        $table['data'] = get_array_from_sql($sql);
        $table['header'] = explode('|', $_SESSION[$program]['lang']['deposit_manager_recs_header']);
        $table['width'] = array( 100,100,280,150,150);
        $table['align'] = array( 'center','center','center','center','center','center');
        $table['th_onclick']=array(';',';',';',';');
        $table['tr_onclick']='open_recs(this.parentNode);';
        $table['title'] = $_SESSION[$program]['lang']['deposit_manager_recs_title'];
        $table['hide_id'] = 1;
        $table['selector'] = true;
        include_once 'app/view/draw_select_table.php';
        draw_select_table($table);
        unset($table);
    };


?>
