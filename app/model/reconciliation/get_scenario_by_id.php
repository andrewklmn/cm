<?php

/*
 * Scenario Model
 */

    function get_scenario_by_id($scenario) {
        
        global $db;
        $sql = '
            SELECT
                *
            FROM 
                `cashmaster`.`Scenario`
            WHERE
                `Scenario`.`ScenarioId`="'.addslashes($scenario[0]).'"
        ;';
        return fetch_assoc_row_from_sql($sql);
    }
?>
