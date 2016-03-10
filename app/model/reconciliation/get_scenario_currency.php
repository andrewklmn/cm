<?php

/*
 * Get Scenario Currency as array
 */

    function get_scenario_currency($scenario_id) {
        global $db;
        return get_array_from_sql('
            SELECT
                   Currency.CurrencyId,
                   Currency.CurrSymbol,
                   Currency.CurrCode,
                   Currency.CurrYear,
                   Currency.CurrName                   
            FROM
                   ScenDenoms
            LEFT JOIN
                   Denoms ON Denoms.DenomId = ScenDenoms.DenomId
            LEFT JOIN
                   Currency On Currency.CurrencyId = Denoms.CurrencyId
            WHERE
                   ScenarioId = "'.addslashes($scenario_id).'"
                   AND ScenDenoms.IsUsed="1"
            GROUP BY Currency.CurrencyId
            ORDER BY Currency.CurrSymbol ASC;

        ;');
    };
?>
