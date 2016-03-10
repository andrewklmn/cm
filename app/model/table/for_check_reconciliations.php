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
            *
       FROM 
            `cashmaster`.`DepositRecs`
       LEFT JOIN
            UserConfiguration ON UserId=`DepositRecs`.`RecOperatorId`
       LEFT JOIN
            DepositRuns ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
        LEFT JOIN 
            Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
        LEFT JOIN
            CashRooms ON CashRooms.Id=Machines.CashRoomId
       WHERE 
            CashRooms.Id="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            AND (DepositRecs.ReconcileStatus="0" OR DepositRecs.ReconcileStatus is NULL)
            AND FwdToSupervisor = 1
       GROUP BY
            DepositRecs.DepositRecId
    ;';
    $row = get_array_from_sql($sql);
    
    //echo '<pre>';
    //echo $sql;
    //print_r($row);
    //echo '</pre>';
    
    if(count($row)>0) {
        $where = array();
        foreach ($row as $key => $value) {
            $where[] = 'DepositRecs.DepositRecId='.$value[0];
        };
        unset($table);
        $sql = '
            SELECT
                DepositRuns.DataSortCardNumber,
                CashRooms.CashRoomName,
                CONCAT(
                    `UserConfiguration`.`UserFamilyName`," ",
                    `UserConfiguration`.`UserFirstName`," ",
                    `UserConfiguration`.`UserPatronymic`),
                DATE_FORMAT(`DepositRecs`.`RecCreateDatetime`,"%d/%m/%Y %H:%i:%s"),
                DATE_FORMAT(`DepositRecs`.`RecLastChangeDatetime`,"%d/%m/%Y %H:%i:%s")
            FROM 
                 `cashmaster`.`DepositRecs`
            LEFT JOIN
                 UserConfiguration ON UserId=`DepositRecs`.`RecOperatorId`
            LEFT JOIN
                 DepositRuns ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
             LEFT JOIN 
                 Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
             LEFT JOIN
                 CashRooms ON CashRooms.Id=Machines.CashRoomId
            WHERE 
                 CashRooms.Id="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                 AND (DepositRecs.ReconcileStatus="0" OR DepositRecs.ReconcileStatus is NULL)
                 AND FwdToSupervisor = 1
            GROUP BY
                 DepositRecs.DepositRecId

        ;';
        
        $for_check = get_array_from_sql($sql);
        
        if (count($for_check)>0) {
            $table['data'] = $for_check;
            $table['header'] = explode('|', $_SESSION[$program]['lang']['deffered_reconciliations_header']);
            $table['width'] = array( 100,100,280,150,150);
            $table['align'] = array( 'center','center','center','center','center');
            $table['th_onclick']=array(';',';',';',';');
            $table['tr_onclick']='open_recs(this.parentNode);';
            $table['title'] = $_SESSION[$program]['lang']['recon_for_check'];  //'Сверки для контроля';
            include_once './app/view/draw_select_table.php';
            draw_select_table($table);
        };
    };


?>
