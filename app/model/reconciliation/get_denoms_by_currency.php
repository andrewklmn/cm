<?php

/*
 * Get scenario denoms by Id
 */

    function get_denoms_by_currency($currency) {
        global $db;
        global $program;
        return get_assoc_array_from_sql('
            SELECT
                   *
            FROM
                   Denoms 
            LEFT JOIN
                   Currency ON Currency.CurrencyId=Denoms.CurrencyId
            WHERE
                   Denoms.CurrencyId = "'.addslashes($currency).'"
            GROUP BY Denoms.DenomId
        ;');
    };

?>
