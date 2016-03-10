<?php

/*
 * Получение должности и имени подписанта по отчету
 */


    
    function  get_report_signers() {
        
        global $db, $program, $report_set_id, $report;
        
        return get_array_from_sql('
            SELECT
                `ExternalUsers`.`ExternalUserPost`,
                `ExternalUsers`.`ExternalUserName`,
                `ExternalUsers`.`Phone`
            FROM 
                ReportSignes
            LEFT JOIN
                Reports ON Reports.SeqId=`ReportSignes`.`RepSeqId`
            LEFT JOIN
                ExternalUsers ON `ExternalUsers`.`ExternalUserId`=`ReportSignes`.`SignerId`
            WHERE
                Reports.ReportSetId = "'.addslashes($report_set_id).'"
                AND Reports.ReportTypeId = "'.addslashes($report['ReportTypeId']).'"
        ;');
    };

?>
