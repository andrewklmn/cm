<?php

/*
 * List of unreconciled deposits
 */

    if (!isset($c)) exit;

        $dl = explode('|', $_SESSION[$program]['lang']['discrepancy_variants']);
        
    ?>
    <script>
        function open_runs(elem) {
            window.open('?c=reconciliation_report&id=' 
                    + elem.id,
                    'rec' + elem.id);
        };
    </script>
    <?php
    
    $row = fetch_row_from_sql('
            SELECT MAX(SetDateTime) FROM ReportSets;');
    $set_last_datetime = $row[0]; // Date and time of the last report set created
    
    $sql = '
        SELECT
            DepositRunId
        FROM
            DepositRecs
        LEFT JOIN
            DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
        LEFT JOIN 
            Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
        WHERE
            `Machines`.`CashRoomId`="'.  addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            AND `DepositRuns`.`DepositRecId` is not null 
            AND `DepositRuns`.`DepositRecId` > 0
            AND DepositRecs.ReconcileStatus = 1
            AND DepositRecs.RecLastChangeDatetime > "'.$set_last_datetime.'"
        GROUP BY DepositRuns.DepositRecId
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
                `DepositRuns`.`DataSortCardNumber`,
                CashRooms.CashRoomName,
                SorterName,
                `DepositIndex`.`IndexValue`,
                CONCAT(
                    `UserConfiguration`.`UserFamilyName`," ",
                    `UserConfiguration`.`UserFirstName`," ",
                    `UserConfiguration`.`UserPatronymic`),
                DATE_FORMAT(`DepositRecs`.`RecLastChangeDatetime`,"%d/%m/%Y %H:%i:%s"),
                IF(`DepositRuns`.`DataSortCardNumber`="SERVICE",
                    "'.addslashes($dl[2]).'",
                    IF(`DepositRecs`.`IsBalanced`=1,"'.addslashes($dl[0]).'","'.addslashes($dl[1]).'")
                )
            FROM
                DepositRuns
            LEFT JOIN
                DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
            LEFT JOIN 
                Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
            LEFT JOIN
                SorterTypes ON SorterTypes.SorterTypeId=Machines.SorterTypeId
            LEFT JOIN
                UserConfiguration ON UserId=DepositRecs.RecOperatorId
            LEFT JOIN
                CashRooms ON `CashRooms`.`Id`=`Machines`.`CashRoomId`
            LEFT JOIN
                DepositIndex ON `DepositIndex`.`DepositIndexId`=`DepositRecs`.`DepositIndexId`
            WHERE 
                `Machines`.`CashRoomId`="'.  addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                AND `DepositRuns`.`DepositRecId` is not null 
                AND `DepositRuns`.`DepositRecId` > 0
                AND DepositRecs.ReconcileStatus = 1
                AND DATE_ADD(`DepositRecs`.`RecCreateDatetime`,INTERVAL -3 DAY) < NOW()
                AND DepositRecs.RecLastChangeDatetime > "'.$set_last_datetime.'"
            GROUP BY                 
            `DepositRecs`.`DepositRecId`
            ORDER BY DepositRecs.DepositRecId ASC        
        ;';
        //echo '<br/>',$sql;
        $table['data'] = get_array_from_sql($sql);
        
        foreach ($table['data'] as $key=>$value) {
            $row = get_array_from_sql('
                SELECT
                    Machines.SorterName
                FROM
                    DepositRuns
                LEFT JOIN
                    DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
                LEFT JOIN
                    Machines ON DepositRuns.MachineDBId = Machines.MachineDBId
                WHERE
                    DepositRuns.DepositRecId="'.$value[0].'"
                GROUP BY Machines.SorterName
            ;');
            $machines = array();
            foreach ($row as $k => $v) {
                $machines[] = $v[0];
            };
            $table['data'][$key][3] = implode(', ', $machines);
        };
        
        $table['header'] = explode('|', $_SESSION[$program]['lang']['reconciled_deposits_header']);
        $table['width'] = array( 100,100,100,100,250,150,100);
        $table['align'] = array( 'center','center','center','center','center','center','center');
        $table['th_onclick']=array(';',';',';',';',';',';');
        $table['tr_onclick']='open_runs(this.parentNode);';
        $table['title'] = $_SESSION[$program]['lang']['reconciled_deposits'];
        $table['fader'] = 1;
        $table['hide_id'] = 1;
        include_once 'app/view/draw_select_table.php';
        draw_select_table($table);


    };

?>
<script>
    $('table.info_table').find('tr').each(function(){
        var tds = $(this).find('td');
        if ('<?php echo htmlfix($dl[1]); ?>'==$(tds[6]).html()) {
            $(tds[6]).css('color','red');
        };
        if ('<?php echo htmlfix($dl[2]); ?>'==$(tds[6]).html()) {
            $(tds[6]).css('color','blue');
        };
    });
</script>
