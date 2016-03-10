<?php

/*
 * Set default scenario
 */

    if (!isset($c)) exit;
/*
    $sql='
        SELECT
            `Scenario`.`ScenarioId`,
            `Scenario`.`ScenarioName`,
            `Scenario`.`DefaultScenario`,
            `Scenario`.`LogicallyDeleted`
        FROM 
            `cashmaster`.`Scenario`
        WHERE 
            `Scenario`.`DefaultScenario`=1
            AND `Scenario`.`LogicallyDeleted`<>1
    ;';
    $rows = get_array_from_sql($sql);
    if(count($rows)>0) {
        $_SESSION[$program]['scenario'] = $rows[0];
    } else {
        $sql='
            SELECT
                `Scenario`.`ScenarioId`,
                `Scenario`.`ScenarioName`,
                `Scenario`.`DefaultScenario`,
                `Scenario`.`LogicallyDeleted`
            FROM 
                `cashmaster`.`Scenario`
            WHERE 
                `Scenario`.`LogicallyDeleted`<>1
            ORDER BY `Scenario`.`ScenarioId` ASC
        ;';
        $rows = get_array_from_sql($sql);
        $_SESSION[$program]['scenario'] = $rows[0];
    };
*/
?>
