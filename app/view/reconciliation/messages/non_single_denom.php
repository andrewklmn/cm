<?php

/*
 * Сообщает о неправильном классе в данных пересчета
 */

        if (!isset($c)) exit;
  
        $sql = '
            SELECT
                    DepositRuns.SortModeName
             FROM
                    DepositRuns
             WHERE
                    DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                    AND `DepositRuns`.`DepositRecId`="'.$DepositRecId.'"
             GROUP BY DepositRuns.SortModeName;             
        ;';
        $rows = get_array_from_sql($sql);
        $mode_names=array();
        foreach ($rows as $row) {
            $mode_names[] = htmlfix($row[0]);
        };

        $t = implode(',',$mode_names);
        if ($t=='') $t=' нет данных';

        $data['error'] = "Более одного номинала в данных пересчета. 
                        <br/>Использованый режим работы машины: "
                        .$t;
        include './app/view/error_message.php';
        
?>
