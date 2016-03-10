<?php

/*
 * Меняет рабочий сценарий на сценарий текущей сверки
 */

        if (!isset($c)) exit;

        if (isset($DepositRec['ScenarioId'])) {
            if ($_SESSION[$program]['scenario'][0] != $DepositRec['ScenarioId']) {
                $data['success'] = $_SESSION[$program]['lang']['scenario_was_changed_to_recon'];
                include './app/view/success_message.php';
                $sql='
                    SELECT
                        `Scenario`.`ScenarioId`,
                        `Scenario`.`ScenarioName`,
                        `Scenario`.`DefaultScenario`,
                        `Scenario`.`LogicallyDeleted`
                    FROM 
                        `cashmaster`.`Scenario`
                    WHERE 
                        `Scenario`.`ScenarioId`="'.$DepositRec['ScenarioId'].'"
                ;';
                $rows = get_array_from_sql($sql);
                $_SESSION[$program]['scenario'] = $rows[0];
            };
        };

?>
