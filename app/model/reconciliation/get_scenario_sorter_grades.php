<?php

/*
 * Get Scenario Currency as array
 */

    function get_scenario_sorter_grades($scenario_id) {
        global $db;
        return get_array_from_sql('
            SELECT
                ScenSorterGrades.GradeId,
                Grades.GradeName,
                Grades.GradeLabel
            FROM 
                ScenSorterGrades
            LEFT JOIN
                Grades ON Grades.GradeId = `ScenSorterGrades`.`GradeId`
            WHERE
                `ScenSorterGrades`.`ScenarioId`="'.  addslashes($scenario_id).'"
                AND ScenSorterGrades.IsUsed=1

        ;');
    };
?>
