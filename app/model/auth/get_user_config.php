<?php

    /*
     * Функция вовращает дату смены пароля
     */
     
     function get_user_config($user_id) {
         $row = fetch_assoc_row_from_sql('
                SELECT
                    *   
                FROM 
                    `cashmaster`.`UserConfiguration`
                LEFT JOIN 
                    Roles ON Roles.RoleId= UserConfiguration.UserRoleId
                WHERE
                    `UserConfiguration`.`UserId`="'.  addslashes($user_id).'"
         ;');
         return $row;
     };
     
    function get_user_config_by_login($user_login) {
         $row = fetch_assoc_row_from_sql('
                SELECT
                    *   
                FROM 
                    `cashmaster`.`UserConfiguration`
                LEFT JOIN 
                    Roles ON Roles.RoleId= UserConfiguration.UserRoleId
                WHERE
                    `UserConfiguration`.`UserLogin`="'.  addslashes($user_login).'"
         ;');
         return $row;
     };

?>