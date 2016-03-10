<?php

/*
 * Проверяем новые данные с сортеров с такой же картой
 */

        if (!isset($c)) exit;
        $row = get_array_from_sql('
            SELECT
                DepositRunId
            FROM
                DepositRuns
            WHERE
                DataSortCardNumber="'.addslashes($data_sort_cardnumber).'"
                AND IFNULL(DepositRecId,0)=0
        ;');
        if (count($row)>0) {
            do_sql('UNLOCK TABLES;');
            echo 5;
            exit;  
        };

?>
