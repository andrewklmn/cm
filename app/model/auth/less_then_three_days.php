<?php

/*
 * Проверяет остаток дней до окончания срока действия пароля
 */

    function less_then_three_days($user_id) {
        global $db;
        $row = fetch_row_from_sql('
            SELECT
                `UserConfiguration`.`LastChangePasswordDate`,
                `UserConfiguration`.`ValidDays`,
                CURRENT_TIMESTAMP
            FROM 
                `cashmaster`.`UserConfiguration`
            WHERE
                `UserConfiguration`.`UserId` = "'.$user_id.'"
        ;');
        
        if ((((strtotime($row[0])+$row[1]*86400) - strtotime($row[2]))/86400) < 6) {
            return true;
        } else {
            return false;
        };
    };

?>
