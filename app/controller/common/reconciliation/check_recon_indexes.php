<?php

/*
 * Проверяет не добавились ли индексы в данных пересчета
 */
        if (!isset($c)) exit;
        
        // Получаем номер карты из депозитов по этой сверке
        $row = fetch_row_from_sql('
                SELECT
                    `DepositRuns`.`DataSortCardNumber`
                FROM
                    `cashmaster`.`DepositRuns`
                WHERE
                    `DepositRuns`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                GROUP BY
                    `DepositRuns`.`IndexName`
        ;');
        $data_sort_cardnumber = $row[0];
        
        $row = fetch_row_from_sql('
            SELECT
                `DepositRuns`.`IndexName`
            FROM
                `cashmaster`.`DepositRuns`
            WHERE
                `DepositRuns`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
            GROUP BY 
                `DepositRuns`.`IndexName`
        ;');
        
        if(count($row)>1) {
            do_sql('UNLOCK TABLES;');
            echo 4;
            exit;
        } else {
            $index_name = $row[0];
            $row = fetch_row_from_sql('
                    SELECT
                        `SorterIndexes`.`DepositIndexId`
                    FROM 
                        `cashmaster`.`SorterIndexes`
                    WHERE
                        `SorterIndexes`.`IndexName`="'.addslashes($index_name).'"
            ;');
            $index_id = $row[0];
        };
?>
