<?php

    if (!isset($c)) exit;
    
    $t = unserialize(base64_decode($_POST['new_clients']));
    
    if (count($t) > 0 ) {
        
        $client_bic_found = true;
        $client_name_found = true;
        
        foreach ($t as $value) {
            // Проверяем уникальность нового имени клиента!
            $no_name = true;
            $sql = '
                SELECT 
                    count(*)
                FROM 
                    `Customers`
                WHERE
                    `Customers`.`CustomerName`="'.addslashes($value[1]).'"
            ;';
            $row = fetch_row_from_sql($sql);
            
            if ($row[0]>0) $no_name = false;
            if ( $no_name == true ) {
                // Если уникальное, то обновляем
                $sql = '
                    INSERT INTO `cashmaster`.`Customers`
                        (
                            `CustomerCode`,
                            `CustomerName`,
                            `CustomerCodeLength`
                        )
                    VALUES
                        (
                            "'.  addslashes($value[0]).'",
                            "'.  addslashes($value[1]).'",
                            "'. strlen($value[0]).'"
                        )
                ;';
                do_sql($sql);
                $success[count($success)] = $_SESSION[$program]['lang']['client_was_added'].': '.$value[0].', '.$value[1];
                $warning = array();
                $client_bic_found = $client_bic_found AND true;
                $client_name_found = $client_name_found AND true;
            } else {
                // если неуникальное то сообщаем что обновление невозможно из-за неуникальности имени
                $error[count($error)] = $_SESSION[$program]['lang']['name_exist_cannot_update'].': '.$value[1];
                $client_bic_found = $client_bic_found AND false;
                $client_name_found = $client_name_found AND false;
            };
        };
    };