<?php

/*
 * Reconciliation total data updater
 */

        if (!isset($c)) exit;

        include './app/view/html_header.php';
        
        //проверяем входные параметры
        if(!(isset($_REQUEST['deposit_rec_id']) AND $_REQUEST['deposit_rec_id']>0)
                OR !(isset($_REQUEST['currency_id']) AND $_REQUEST['currency_id']>0)
                OR !isset($_REQUEST['value'])
                OR !isset($_REQUEST['oldvalue'])) {
            echo 'Wrong update data';
            exit;
        };
        
        $update_date = date("Y-m-d H:i:s", time());
        
        
        $_REQUEST['value']=  str_replace(',', '.', $_REQUEST['value']);
        $_REQUEST['oldvalue']=  str_replace(',', '.', $_REQUEST['oldvalue']);
        
        // Блокируем таблицу
        do_sql('LOCK TABLES DepositCurrencyTotal WRITE;');
        
        // Проверяем есть ли данные для такого Деном
        $row = fetch_row_from_sql('
            SELECT
                count(*)
            FROM 
                `cashmaster`.`DepositCurrencyTotal`
            WHERE
                `DepositCurrencyTotal`.`DepositRecId` = "'.addslashes($_REQUEST['deposit_rec_id']).'"
                AND `DepositCurrencyTotal`.`CurrencyId` = "'.addslashes($_REQUEST['currency_id']).'"
        ;');
        if ($row[0]>0) {
            //Получаем ключ существующей записи
            $row = fetch_row_from_sql('
                SELECT
                    `DepositCurrencyTotal`.`Id`,
                    `DepositCurrencyTotal`.`DepositRecId`,
                    `DepositCurrencyTotal`.`CurrencyId`,
                    `DepositCurrencyTotal`.`ExpectedDepositValue`
                FROM 
                    `cashmaster`.`DepositCurrencyTotal`
            WHERE
                `DepositCurrencyTotal`.`DepositRecId` = "'.addslashes($_REQUEST['deposit_rec_id']).'"
                AND `DepositCurrencyTotal`.`CurrencyId` = "'.addslashes($_REQUEST['currency_id']).'"
            ;');
            //print_r($row);
            // Проверяем старое значение
            if ((float)$row[3]==(float)$_REQUEST['oldvalue']) {
                // Можно обновлять 
                do_sql('
                    UPDATE `cashmaster`.`DepositCurrencyTotal`
                    SET
                        `ExpectedDepositValue` = "'.addslashes($_REQUEST['value']).'"
                    WHERE  `DepositCurrencyTotal`.`Id`= "'.addslashes($row[0]).'"
                ;');
                echo 0;   // is OK
                
            } else {
                // Нельзя обновлять, возвращаем новое значение
                echo '1';
                echo (float)$row[3];
            };
            
        } else {
            // записи нет, можно создать новую
            do_sql('
                INSERT INTO `cashmaster`.`DepositCurrencyTotal`
                    (
                        `DepositRecId`,
                        `CurrencyId`,
                        `ExpectedDepositValue`)
                VALUES
                    (
                        "'.addslashes($_REQUEST['deposit_rec_id']).'",
                        "'.addslashes($_REQUEST['currency_id']).'",
                        "'.addslashes($_REQUEST['value']).'"
                    )
            ;');
            echo 0;   // is OK
        };
        
        
        // Снимаем блокировку с таблиц 
        do_sql('UNLOCK TABLES;');

        
?>
