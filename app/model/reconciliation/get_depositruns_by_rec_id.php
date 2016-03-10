<?php

/*
 * DepositRuns Model
 */
    function get_depositruns_by_rec_id($id){
        global $db;
        $sql='
            SELECT
                `DepositRuns`.`DepositRunId`,
                `DepositRuns`.`MachineDBId`,
                `DepositRuns`.`ShiftId`,
                `DepositRuns`.`BatchId`,
                `DepositRuns`.`DepositInBatchId`,
                `DepositRuns`.`DepositStartTimeStamp`,
                `DepositRuns`.`DepositEndTimeStamp`,
                `DepositRuns`.`DataSortCardNumber`,
                `DepositRuns`.`OperatorName`,
                `DepositRuns`.`SupervisorName`,
                `DepositRuns`.`IndexName`,
                `DepositRuns`.`SortModeName`,
                `DepositRuns`.`DepositRecId`
            FROM 
                `cashmaster`.`DepositRuns`
            WHERE 
                `DepositRuns`.`DepositRecId` = "'.addslashes($id).'"
        ;';
        return get_array_from_sql($sql);
    }
?>
