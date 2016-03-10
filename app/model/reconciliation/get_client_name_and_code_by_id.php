<?php

/*
 * Возвращает имя и код БИК клиента по его коду
 */
    function get_client_name_and_code_by_id($id) {
        global $db;
        global $program;
        return fetch_assoc_row_from_sql('
            SELECT
                *
            FROM 
                `cashmaster`.`Customers`
            WHERE
                 `Customers`.`CustomerId`="'.addslashes($id).'"

        ;');
    };
?>
