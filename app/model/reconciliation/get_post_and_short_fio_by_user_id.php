<?php

/*
 * Get short FIO by user id
 */

        function get_post_and_short_fio_by_user_id($id) {
            
            global $db;
            
            if (isset($id) and $id>0) {
            
                $value = fetch_assoc_row_from_sql('
                        SELECT
                            *
                        FROM 
                            `cashmaster`.`UserConfiguration`
                        WHERE
                            `UserConfiguration`.`UserId`="'.$id.'"          
                ;');
                $initials = ($value['UserPatronymic']=='')?'':substr($value['UserPatronymic'],0,2).'.';
                $initials = substr($value['UserFirstName'],0,2).'.'.$initials;
                $fio = $value['UserPost'].': '.$value['UserFamilyName'].' '.$initials;
                return $fio; 

            } else {
                return '';
            };
        };


?>
