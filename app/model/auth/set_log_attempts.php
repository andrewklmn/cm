<?php

/*
 * Sets log attempts by user id
 */

    function set_log_attempts( $user_id, $amount ) {
        global $db;
        do_sql('
            UPDATE 
                `cashmaster`.`UserConfiguration`
            SET
                `BadLogAttempts` = "'.addslashes($amount).'"
            WHERE 
                `UserId` = "'.  addslashes($user_id).'"
        ;');
    };

?>
