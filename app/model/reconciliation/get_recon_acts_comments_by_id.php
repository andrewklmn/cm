<?php

/*
 * Комментарии актов сверки с расхождениями
 */

    function get_recon_acts_comments_by_id($DepositRecId) {
        global $db;
        global $program;
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
        
        return array($comment_over,$comment_suspect,$comment_deficit);
    };

?>
