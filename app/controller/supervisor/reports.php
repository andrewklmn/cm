<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;
        
        include_once './app/model/reconciliation/get_reconciled_deposit_by_rec_id.php';

        $data['title'] = $_SESSION[$program]['lang']['supervisor_reports'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include 'app/view/reload_after_1_min.php';

        $b = explode('|',$_SESSION[$program]['lang']['reports_buttons']);
        $t = explode('|',$_SESSION[$program]['lang']['reports_header']);
        $h = explode('|',$_SESSION[$program]['lang']['reports_table_headers']);
        
        $row= fetch_row_from_sql('
            SELECT
                MAX(`ReportSets`.`SetDateTime`)
            FROM 
                `cashmaster`.`ReportSets`
            WHERE
                ReportSets.CashRoomId="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
        ;');
        if (!isset($row[0]) OR $row[0]=='' OR $row[0]=='0000-00-00 00:00:00') {
            $_POST['start_datetime'] = '1972-11-29 00:00:00';
        } else {
            $_POST['start_datetime'] = $row[0];
        };        
        $_POST['stop_datetime'] = date('Y-m-d H:i:s', time());
        
        
        
?>
    <script>
        $(document).ready(function() {
            
        });
    </script>
    <style>
            table.info_table th {
                padding:1px; 
                font-size:11px; 
                border-bottom: 2px solid black;
            }
            table.info_table td {
                padding:1px; 
                font-size:11px; 
                border-bottom: 1px solid gray;
            }
            table.info_table th {
                background-color: lightgray;
            }
            table.info_table th.total {
                background-color: white;
            }
            
    </style>
    <div class="container">
        <h4>
            <?php echo htmlfix($t[0]); ?>: &nbsp;
            <font style="color:darkgreen;"> 
                <?php echo $_POST['start_datetime']; ?>
            </font> &nbsp;
            <?php echo htmlfix($t[1]); ?>: &nbsp;
            <font style="color:darkred;">
                <?php echo $_POST['stop_datetime']; ?>
            </font>
        </h4>
        <?php 
            // получаем массив сверок за этот период
            $sql = '
                SELECT DISTINCT
                    DepositRecs.DepositRecId
                FROM 
                    `cashmaster`.`DepositRecs`
                LEFT JOIN
                    DepositRuns ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
                LEFT JOIN
                    Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
                LEFT JOIN
                    CashRooms ON `CashRooms`.`Id`=`Machines`.`CashRoomId`
                WHERE
                    `DepositRecs`.`ServiceRec`=0
                    AND `DepositRecs`.`ReconcileStatus`=1
                    AND `DepositRecs`.`RecLastChangeDatetime` > "'.addslashes($_POST['start_datetime']).'"
                    AND `DepositRecs`.`RecLastChangeDatetime` <= "'.addslashes($_POST['stop_datetime']).'"
                    AND `Machines`.`CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                
            ;';
            $recs = get_array_from_sql($sql);
            
            $lines = array(); // $lines[CURRENCY]=array( $amount, $summ );
            foreach ($recs as $rec) {
                $data = get_reconciled_deposit_by_rec_id($rec[0]);
                
                // 1. Получаем все валюты по текущей из данных пересчета
                $from_sorter = get_array_from_sql('
                    SELECT
                        Currency.CurrName,
                        Denoms.Value,
                        IFNULL(SUM(ActualCount),0)
                    FROM 
                        `cashmaster`.`SorterAccountingData`
                    LEFT JOIN
                        DepositRuns ON DepositRuns.DepositRunId = `SorterAccountingData`.`DepositRunId`
                    LEFT JOIN
                        DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
                    LEFT JOIN
                        Valuables ON Valuables.ValuableId = `SorterAccountingData`.`ValuableId`
                    LEFT JOIN
                        Denoms ON Denoms.DenomId = Valuables.DenomId
                    LEFT JOIN
                        Currency ON Currency.CurrencyId = Denoms.CurrencyId
                    WHERE
                        DepositRecs.DepositRecId = "'.$rec[0].'"
                    GROUP BY 
                        Currency.CurrName, Denoms.Value
                ;');
                
                foreach ($from_sorter as $key => $value) {
                    if (isset($lines[$value[0]][$value[1]])) {
                        $lines[$value[0]][$value[1]] += $value[2];
                    } else {
                        $lines[$value[0]][$value[1]] = $value[2];
                    };
                };
                
                // 2. Получаем все валюты по текущей из данных ручного ввода
                $from_recon = get_array_from_sql('
                    SELECT
                        Currency.CurrName,
                        Denoms.Value,
                        IFNULL(SUM(CullCount),0)
                    FROM 
                        `cashmaster`.`ReconAccountingData`
                    LEFT JOIN
                        DepositRecs ON DepositRecs.DepositRecId = `ReconAccountingData`.`DepositRecId`
                    LEFT JOIN
                        Denoms ON Denoms.DenomId = `ReconAccountingData`.`DenomId`
                    LEFT JOIN
                        Currency ON Currency.CurrencyId = Denoms.CurrencyId
                    WHERE
                        DepositRecs.DepositRecId = "'.$rec[0].'"
                    GROUP BY 
                        Currency.CurrName, Denoms.Value
                    
                ;');
                
                foreach ($from_recon as $key => $value) {
                    if (isset($lines[$value[0]][$value[1]])) {
                        $lines[$value[0]][$value[1]] += $value[2];
                    } else {
                        $lines[$value[0]][$value[1]] = $value[2];
                    };
                };
                
                
            };
            
            
            ?>
            <table class="info_table" width="700px;">
            <tr>
                <th><?php echo htmlfix($h[0]); ?></th>
                <th><?php echo htmlfix($h[1]); ?></th>
                <th><?php echo htmlfix($h[2]); ?></th>
                <th><?php echo htmlfix($h[3]); ?></th>
            </tr>
            <?php
               
               foreach ($lines as $key=>$value) {
                   $amount = 0;
                   $summ = 0;
                    ?>
                    <?php  
                    ksort($value);
                    foreach ($value as $k=>$v) {
                        echo '<tr>';
                        echo '<td>',htmlfix($key),'</td>';
                        echo '<td align="center">',$k,'</td>';
                        echo '<td align="center">',$v,'</td>';
                        echo '<td align="right">',  number_format($v*$k,2),'</td>';
                        echo '</tr>';
                        $amount += $v;
                        $summ += $v*$k;
                    };
                    ?>
                        <tr>
                            <th class="total"><?php echo htmlfix($h[4]); ?>:</th>
                            <th align="right" class="total"></th>
                            <th class="total"><?php echo $amount; ?></th>
                            <th class="total" align="right"><?php echo number_format($summ,2); ?></th>
                        </tr>
                    <?php
               };
            ?>  
            </table>
            <br/>
            <a class="btn btn-primary btn-large" href="?c=reports_archive">
                <?php echo htmlfix($b[0]); ?>
            </a>
            <a class="btn btn-warning btn-large" href="?c=preview_reports">
                <?php echo htmlfix($b[2]); ?>
            </a>
            <a class="btn btn-danger btn-large" href="?c=create_reports">
                <?php echo htmlfix($b[1]); ?>
            </a>
    </div>
</body>
</html>