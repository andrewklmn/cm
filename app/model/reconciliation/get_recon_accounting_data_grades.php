<?php

/*
 * Recon Accounting Data
 */

    function get_recon_accounting_data_grades($id){
        global $db;
        $sql='
            SELECT
                `ReconAccountingData`.`GradeId`,
                Grades.GradeName,
                Grades.GradeLabel,
                SUM(`ReconAccountingData`.`CullCount`)
            FROM 
                `cashmaster`.`ReconAccountingData`
            LEFT JOIN
                Grades ON Grades.GradeId = `ReconAccountingData`.`GradeId`
            WHERE 
                `ReconAccountingData`.`DepositRecId` = "'.addslashes($id).'"
                AND `ReconAccountingData`.`CullCount`>0
            GROUP BY `ReconAccountingData`.`GradeId`
        ;';
        return get_array_from_sql($sql);
    }
?>
