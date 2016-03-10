<?php

    /*
     * Функция вовращает дату смены пароля
     */
     
     function get_password_exired_date($user_id) {
         $row = fetch_row_from_sql('
                SELECT
                    `UserConfiguration`.`LastChangePasswordDate`,
                    `UserConfiguration`.`ValidDays`,
                    ChangePassword
                FROM 
                    `cashmaster`.`UserConfiguration`
                WHERE
                    `UserConfiguration`.`UserId`="'.  addslashes($user_id).'"
         ;');
         
         if($row[2]==1) return date("-1 day", time());
         
         $last_change = $row[0];
         $valid_days = $row[1];
         
         $date = strtotime("+".$valid_days." day", strtotime($last_change));
         return date("Y-m-d", $date);
     };

?>
