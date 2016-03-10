<?php

/*
 * List of unreconciled deposits
 */

    if (!isset($c)) exit;

        
    ?>
    <script>
        function open_runs(elem) {
            var card = $(elem).find('td')[0].innerHTML;
            var input = $('input#focus')[0];
            $(input).prop('value',card);
            $(input).focus();
        }
    </script>
    <?php
    $sql = '
        SELECT
            DepositRunId
        FROM
            DepositRuns
        LEFT JOIN 
            Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
        WHERE
            `Machines`.`CashRoomId`="'.  addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            AND (`DepositRuns`.`DepositRecId` is null 
                    OR `DepositRuns`.`DepositRecId` = 0)
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
                `DepositRuns`.`DataSortCardNumber`,
                CashRooms.CashRoomName,
                SorterName,
                IndexName,
                DATE_FORMAT(`DepositRuns`.`DepositStartTimeStamp`,"%d/%m/%Y %H:%i:%s"),
                DATE_FORMAT(`DepositRuns`.`DepositEndTimeStamp`,"%d/%m/%Y %H:%i:%s")
            FROM
                DepositRuns
            LEFT JOIN
                DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
            LEFT JOIN 
                Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
            LEFT JOIN
                SorterTypes ON SorterTypes.SorterTypeId=Machines.SorterTypeId
            LEFT JOIN
                CashRooms ON CashRooms.Id=Machines.CashRoomId
            WHERE 
                `Machines`.`CashRoomId`="'.  addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                AND DataSortCardNumber="'.  addslashes($_REQUEST['separator_id']).'"
                AND IFNULL(DepositRecs.ReconcileStatus,0)="0"
            ORDER BY `DepositRuns`.`DataSortCardNumber` ASC
        ;';
        //echo '<br/>',$sql;
        $table['data'] = get_array_from_sql($sql);
        $table['header'] = explode('|', $_SESSION[$program]['lang']['unreconciled_deposits_header']);
        $table['width'] = array( 100,100,150,150,150);
        $table['align'] = array( 'center','center','center','center','center','center');
        $table['th_onclick']=array(';',';',';',';',';');
        //$table['tr_onclick']='$(\'input#focus\')[0].focus();';
        $table['tr_onclick']='open_runs(this.parentNode);';
        $table['title'] = $_SESSION[$program]['lang']['sorter_accounting_data_by_card'].  htmlfix($_REQUEST['separator_id']);
        include_once 'app/view/draw_select_table.php';
        draw_select_table($table);


    };


?>
