<?php

/*
 * Проверяет входит ли пользователь с $user_id в список разрешенных ролей $roles
 */
    
    function check_access_to_roles( $user_id, $roles ){
        global $db;
        $row = fetch_row_from_sql('
            SELECT
                `UserConfiguration`.`UserRoleId`
            FROM 
                `cashmaster`.`UserConfiguration`
            WHERE
                `UserConfiguration`.`UserId`="'.addslashes($user_id).'"
        ;');
        foreach ($roles as $value) {
            if ($row[0]==$value) return true;
        };
        return false;
    };

?>
