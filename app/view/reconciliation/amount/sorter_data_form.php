<?php

/*
 * Sorter Data Form
 */

        if (!isset($c)) exit;
        $width=75;
?>
<style>
        table.info_table {
            
        }
        table.info_table th {
            padding:3px; 
            font-size:10px; 
            border: 2px solid black;
            overflow: hidden;
        }
        table.info_table td {
            padding:0px; 
            font-size:12px; 
            border: 1px solid gray;
            text-align: center;
            overflow: hidden;
        }
        table.info_table th {
            background-color: lightgray;
        }
</style>
<table class="info_table" id="sorter" 
       style="width:<?php echo $width*2+count($grades)*$width; ?>px;">
    <colgroup>
        <col width="<?php echo $width; ?>px">
        <?php 
            foreach ($grades as $grade) {
                echo '<col width="'.$width.'px">';
            };
        ?>
        <col width="<?php echo $width; ?>px">
    </colgroup>
    <tr>
        <th colspan="<?php 
            echo count($grades)+2;
        ?>"><?php echo htmlfix($_SESSION[$program]['lang']['sorter_accounting_data']); ?></th>
    </tr>
    <tr>
        <th><?php echo htmlfix($_SESSION[$program]['lang']['denom']); ?></th>
            <?php 
                foreach ($grades as $grade) {
                    echo '<th style="width:'.$width.'px;">',$grade[2],'</th>';
                };
            ?>
        <th style="width:70px;"><?php echo htmlfix($_SESSION[$program]['lang']['total']); ?></th>
    </tr>
        <?php 
            $vsego_deneg = 0;
            $vsego_banknot = 0;
            
            foreach ($denoms as $dk=>$denom) {
                if($denom[4]=='1') {
                    echo '<tr style="background-color:LemonChiffon;" class="denom">';                    
                } else {
                    echo '<tr class="denom">';
                };
                echo '<td>',htmlfix($denom[1]),'</td>';
                $vsego = 0;
                foreach ($grades as $grade) {
                    if($grade[1]=='-') {
                        $background = "background-color:red;color:yellow;";
                    } else {
                        $background = '';
                    };
                    $sql = '
                        SELECT
                            IFNULL(SUM(SorterAccountingData.ActualCount),"-")
                        FROM
                            Denoms 
                        LEFT JOIN
                            Valuables ON Denoms.DenomId = Valuables.DenomId
                        LEFT JOIN
                            SorterAccountingData ON Valuables.ValuableId=SorterAccountingData.ValuableId
                        LEFT JOIN
                            Currency ON Currency.CurrencyId=Denoms.CurrencyId
                        LEFT JOIN
                            (SELECT 
                                * 
                             FROM 
                                ValuablesGrades
                             WHERE  
                                ScenarioId="'.$_SESSION[$program]['scenario'][0].'") as t1 ON t1.ValuableId = Valuables.ValuableId
                        LEFT JOIN
                            Grades ON Grades.GradeId=t1.GradeId
                        LEFT JOIN
                            DepositRuns ON DepositRuns.DepositRunId=SorterAccountingData.DepositRunId
                        WHERE
                                DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                                AND (`DepositRuns`.`DepositRecId`="'.$DepositRecId.'" 
                                        OR `DepositRuns`.`DepositRecId` is NULL 
                                        OR `DepositRuns`.`DepositRecId`="0" )
                                AND Denoms.CurrencyId="'.$currency[0].'"
                                AND Denoms.DenomId = "'.$denom[0].'"
                                AND IFNULL(Grades.GradeName,"-")="'.$grade[1].'"
                        GROUP BY Denoms.Value,Grades.GradeName
                    ;';
                    
                    $row=  fetch_row_from_sql($sql);
                    $vsego += $row[0]; 
                    if ((int)$row[0]==0) {
                        echo '<td style="',$background,'"></td>';
                    } else {
                        echo '<td style="',$background,'">',htmlfix($row[0]),'</td>';   
                    }
                };
                if ((int)$vsego==0) {
                    echo '<td class="data"></td>';
                } else {
                    echo '<td class="data">',htmlfix($vsego),'</td>';                                        
                }
                echo '</tr>';
                $denoms[$dk][6] = $vsego;
                $vsego_deneg += $vsego*$denom[1];
                $vsego_banknot += $vsego;
            };              
            
            //print_array_as_html_table($denoms);
        ?>
    </tr>
</table>