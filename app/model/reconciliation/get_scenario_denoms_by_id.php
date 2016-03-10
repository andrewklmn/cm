<?php

/*
 * Get scenario denoms by Id
 */

    function get_scenario_denoms_by_id($scenario_id) {
        global $db;
        global $program;
        return get_assoc_array_from_sql('
                    SELECT
                           *
                    FROM
                           ScenDenoms
                    LEFT JOIN
                           Denoms ON Denoms.DenomId = ScenDenoms.DenomId
                    LEFT JOIN
                           Currency ON Currency.CurrencyId=Denoms.CurrencyId
                    WHERE
                           ScenarioId = "'.$scenario_id.'"
                           AND ScenDenoms.IsUsed="1"
                    GROUP BY Denoms.DenomId
        ;');
    };

?>
