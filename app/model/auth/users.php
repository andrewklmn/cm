<?php

    /*
     * Model of User tables
     */

     $sql = '
         SELECT
               *
         FROM
                UserConfiguration
         LEFT JOIN 
                Roles ON Roles.RoleId= UserConfiguration.UserRoleId
     ;';
     $users = get_assoc_array_from_sql($sql);
     //print_r($users);
?>