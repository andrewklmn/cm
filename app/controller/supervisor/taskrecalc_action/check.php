<?php

        if (!isset($c)) exit;
        
        //1. проверяем Год и Номинал, если нет в системе, то только удаление
        $year_denom_found = true;
        //2. проверяем Индекс, если новый, то предложить Админу добавить или удаление
        $index_found = true;
        //3. проверяем Биг и Имя клиента, если есть БИГ но несовпадает с нашей таблицей, то предлагаем отредактировать
        //   если нет Бига, то предлагаем добавить клиента
        $client_bic_found = true;
        $client_name_found = true;        
        
        $error = array();
        $warning = array();
        $success = array();
        
        $new_clients = array();
        $wrong_clients = array();
        $wrong_bic_clients = array();
        
        $button_scenario = 0;  // - назад, создавать предподготовку
        //                 1   // - назад
        //                 2   // - назад, удалить файл
        //                 3   // - назад, добавить клиента
        //                 4   // - назад, исправить клиента
        
        
        foreach ($root as $k=>$value) {
            if (strtolower($k)=='pack') { 
                // проверяем год валюты
                $sql ='
                    SELECT 
                        count(*)
                    FROM 
                        Currency
                    WHERE
                        Currency.CurrYear="'.addslashes($value->attributes()->CurrYear).'"
                        AND Currency.CurrCode = "643"
                ;';
                $row = fetch_row_from_sql($sql);
                if ($row[0]==0) {
                    $year_denom_found = false;
                    $error[count($error)] = 'Wrong Currency Year: '.$value->attributes()->CurrYear;
                    $button_scenario = 2;
                    break;
                } else {
                    // проверяем деном
                    $sql ='
                        SELECT 
                            count(*)
                        FROM 
                            `cashmaster`.`Denoms`
                        LEFT JOIN
                            Currency ON Currency.CurrencyId = Denoms.CurrencyId
                        WHERE
                            `Denoms`.`Value`="'.addslashes($value->attributes()->CurrNominal).'"
                            AND Currency.CurrCode = "643"
                    ;';
                    $row = fetch_row_from_sql($sql);
                    if ($row[0]==0) {
                        $year_denom_found = false;
                        $error[count($error)] = 'Wrong Denom: '.$value->attributes()->CurrNominal;
                        $button_scenario = 2;
                        break;
                    } else {
                        // проверяем индекс
                        $sql = '
                            SELECT
                                count(*)
                            FROM
                                DepositIndex
                            WHERE
                                IndexValue = "'.addslashes($value->attributes()->CurrIndex).'"
                        ;';
                        $row = fetch_row_from_sql($sql);
                        if ($row[0]==0) {
                            $warning[count($warning)] = $_SESSION[$program]['lang']['index_not_found']
                                                        .': '.$value->attributes()->CurrIndex;
                            $index_found = false;
                            $button_scenario = 1;
                            break;
                        } else {
                            // Проверяем на предмет нету ни БИКА ни ИМЕНИ
                            $sql = '
                                SELECT
                                    count(*)
                                FROM
                                    Customers
                                WHERE
                                    CustomerName="'.addslashes($value->attributes()->Client).'"
                                    OR CustomerCode="'.addslashes($value->attributes()->BIC).'"
                            ;';
                            $row = fetch_row_from_sql($sql);
                            if ($row[0]==0) {
                                // Новый клиент чисто
                                $warning[count($warning)] = 
                                        $_SESSION[$program]['lang']['new_client_bic_found']
                                                        .': '.$value->attributes()->BIC.', '
                                                        .htmlfix($value->attributes()->Client);
                                $client_bic_found = false;
                                $client_name_found = false;
                                $new_clients[count($new_clients)] = array(
                                    (string)$value->attributes()->BIC,
                                    (string)$value->attributes()->Client
                                );
                                $button_scenario = 3;
                            } else {
                                // Если что-то есть, то выясняем что именно есть
                                $sql = '
                                    SELECT
                                        count(*)
                                    FROM
                                        Customers
                                    WHERE
                                        CustomerName<>"'.addslashes($value->attributes()->Client).'"
                                        AND CustomerCode="'.addslashes($value->attributes()->BIC).'"
                                ;';
                                $bic = fetch_row_from_sql($sql);
                                if ($bic[0]>0) {
                                    // Биг есть, но имя не совпадает, предлагаем исправить имя клиента
                                    $warning[count($warning)] = $_SESSION[$program]['lang']['wrong_client_name']
                                                        .': '.$value->attributes()->BIC.', '
                                                        .htmlfix($value->attributes()->Client);
                                    $client_name_found = false;
                                    $wrong_clients[count($wrong_clients)] = array(
                                         (string)$value->attributes()->BIC,
                                         (string)$value->attributes()->Client
                                    );
                                    $button_scenario = 4;
                                    break;
                                } else {
                                   // 2) Проверяем есть ли такое NAME в списке
                                    $sql = '
                                        SELECT
                                            count(*)
                                        FROM
                                            Customers
                                        WHERE
                                            CustomerName="'.addslashes($value->attributes()->Client).'"
                                            AND CustomerCode<>"'.addslashes($value->attributes()->BIC).'"
                                    ;';
                                    $name = fetch_row_from_sql($sql);
                                    if ($name[0]>0) {
                                        // Код неизвестный, но имя есть в списке
                                        $error[count($error)] = $_SESSION[$program]['lang']['wrong_client_bic']
                                                            .': '.$value->attributes()->BIC.', '
                                                            .htmlfix($value->attributes()->Client);
                                        $client_bic_found = false;
                                        $wrong_bic_clients[count($wrong_bic_clients)] = array(
                                             (string)$value->attributes()->BIC,
                                             (string)$value->attributes()->Client
                                        );
                                        $button_scenario = 2;
                                        break;
                                    };
                                };
                            };
                        };
                    };
                };
            };
        };

//echo $button_scenario;
//echo ($client_name_found)?'TRUE':'FALSE';
//echo ($client_bic_found)?'TRUE':'FALSE';