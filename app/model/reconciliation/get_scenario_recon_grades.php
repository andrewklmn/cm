<?php

/*
 * Get Scenario Currency as array
 */

    function get_scenario_recon_grades($scenario_id) {
        global $db;
        return get_array_from_sql('
            SELECT
                Grades.GradeId,
                Grades.GradeName,
                Grades.GradeLabel
            FROM 
                `cashmaster`.`ScenReconGrades`
            LEFT JOIN
                Grades ON Grades.GradeId = `ScenReconGrades`.`GradeId`
            WHERE
                `ScenReconGrades`.`ScenarioId`="'.  addslashes($scenario_id).'"
                AND ScenReconGrades.IsUsed=1
            ORDER BY ScenReconGrades.Id ASC

        ;');
    };
?>
