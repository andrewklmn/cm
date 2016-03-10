<?php

/*
 * Блокирует пользователя с заданным id
 */

    function block_user_id($user_id) {
        global $db;
        do_sql('
            UPDATE 
                `cashmaster`.`UserConfiguration`
            SET
                `UserIsBlocked` = 1
            WHERE 
                `UserId` = "'.$user_id.'";
        ;');
    };

?>