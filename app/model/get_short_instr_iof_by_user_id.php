<?php

/*
 * Get short FIO by user id
 */

        function get_short_instr_iof_by_user_id($id) {
            
            global $db;
            
            if (isset($id) and $id>0) {

                $value = fetch_assoc_row_from_sql('
                        SELECT
                            UserFirstName,
                            UserPatronymic,
                            UserFamilyName,
                            CASE
                            	WHEN InstrName IS NOT NULL AND InstrName <> "" THEN InstrName
                            	ELSE UserFamilyName
                            END as InstrName
                        FROM 
                            `cashmaster`.`UserConfiguration`
                        WHERE
                            `UserConfiguration`.`UserId`="'.$id.'"          
                ;');
                /*
                $initials = ($value['UserPatronymic']=='')?'':substr($value['UserPatronymic'],0,2).'.';
                $initials = substr($value['UserFirstName'],0,2).'.'.$initials;
                 * 
                 */
                if (preg_match('/^[a-zA-Z]/', $value['UserPatronymic'])) {
                    $initials = ($value['UserPatronymic']=='')?'':substr($value['UserPatronymic'],0,1).'.';
                } else {
                    $initials = ($value['UserPatronymic']=='')?'':substr($value['UserPatronymic'],0,2).'.';
                };
                if (preg_match('/^[a-zA-Z]/', $value['UserFirstName'])) {
                    $initials = substr($value['UserFirstName'],0,1).'.'.$initials;
                } else {
                    $initials = substr($value['UserFirstName'],0,2).'.'.$initials;
                };
                
                $fio = $initials.' '.$value['InstrName'];
                return $fio;

            } else {
                return '';
            };
        };

?>
