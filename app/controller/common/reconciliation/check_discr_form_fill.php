<?php

/*
 * Проверка заполненности формы расхождений 
 */

    if (!isset($c)) exit;
    
    $acts = get_array_from_sql('
        SELECT
            `Acts`.`ActId`,
            `Acts`.`DepositId`,
            `Acts`.`DiscrepancyKindId`,
            `Acts`.`DiscrepancyDescr`
        FROM 
            `cashmaster`.`Acts`
        WHERE
            `Acts`.`DepositId`="'.addslashes($DepositRecId).'"
    ;');
    
    if (count($acts)>0) {
        ?>
            <script>
                $(document).ready(function(){
                    $('input#finish').click();
                });
            </script>
        <?php
    };
    
    // определяем данные в комментариях формы
    $row = fetch_row_from_sql('
        SELECT
            `Acts`.`DiscrepancyDescr`
        FROM 
            `cashmaster`.`Acts`
        WHERE
            `Acts`.`DiscrepancyKindId` = "3"
            AND `Acts`.`DepositId`="'.addslashes($DepositRecId).'"
    ;');
    $comment_over = (count($row)>0) ? $row[0]:'';
    
    
    $row = fetch_row_from_sql('
        SELECT
            `Acts`.`DiscrepancyDescr`
        FROM 
            `cashmaster`.`Acts`
        WHERE
            `Acts`.`DiscrepancyKindId` = "2"
            AND `Acts`.`DepositId`="'.addslashes($DepositRecId).'"
    ;');
    $comment_deficit = (count($row)>0) ? $row[0]:'';
    
    
    $row = fetch_row_from_sql('
        SELECT
            `Acts`.`DiscrepancyDescr`
        FROM 
            `cashmaster`.`Acts`
        WHERE
            `Acts`.`DiscrepancyKindId` = "1"
            AND `Acts`.`DepositId`="'.addslashes($DepositRecId).'"
    ;');
    $comment_suspect = (count($row)>0) ? $row[0]:'';
    
?>
