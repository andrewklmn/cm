<?php

/*
 * Create new reconciliation
 */
    function create_new_recon_value($scenario_id,$deposits){
        
        //Пробуем создать новую запись в DepositRecs
        global $db;
        global $program;
        
        // временно закрываем запись в таблицы
        do_sql('LOCK TABLES 
                    DepositRuns WRITE, 
                    Scenario WRITE, 
                    Denoms WRITE, 
                    ScenDenoms WRITE, 
                    DepositDenomTotal WRITE,
                    DepositCurrencyTotal WRITE,
                    SorterAccountingData WRITE,
                    Valuables WRITE,
                    Currency WRITE,
                    DepositRecs WRITE;');
        
        // проверяем не привязал ли кто-то другой эти депозиты к сверке
        $flag=true;
        foreach ($deposits as $deposit) {
            $row = fetch_row_from_sql('
                SELECT
                    count(*)
                FROM
                    DepositRuns
                LEFT JOIN
                    DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
                WHERE
                    DepositRunId="'.addslashes($deposit['DepositRunId']).'"
                    AND (DepositRuns.DepositRecId is NULL OR DepositRuns.DepositRecId="0")
                    AND (DepositRecs.ReconcileStatus="0" OR DepositRecs.ReconcileStatus is NULL)
            ;');
            if ($row[0]==0) $flag=false;
        };
        
        if ($flag==false) {
            // депозиты уже обработаны
            $data['error'] = "Deposits were reconciliated by another operator.";
            include './app/view/error_message.php';
            do_sql('UNLOCK TABLES');
            return 0;
        };
        
        $create_date = date("Y-m-d H:i:s", time());
        
        // Создаем новую сверку в базе
        $sql = '
            INSERT INTO `cashmaster`.`DepositRecs`
                (
                    `ScenarioId`,
                    `DepositPackingDate`,
                    `PackIntegrity`,
                    `StrapsIntegrity`,
                    `SealNumber`,
                    `SealType`,
                    `PackId`,
                    `RecOperatorId`,
                    `RecCreateDatetime`,
                    `RecLastChangeDatetime`,
                    `IsBalanced`
                )
            VALUES
                (
                    "'.addslashes($scenario_id).'",
                    CURRENT_TIMESTAMP,
                    1,
                    1,
                    "",
                    1,
                    "",
                    "'.$_SESSION[$program]['user_id'].'",
                    "'.$create_date.'",
                    "'.$create_date.'",
                    0
                );
        ;';
        do_sql($sql);
        
        // Получаем добавленный номер сверки
        $row = fetch_row_from_sql('
            SELECT
                MAX(DepositRecId)
            FROM
                DepositRecs
        ;');
        $last_rec_id = $row[0];
        
        // Собираем ID депозитов, в которые впишем номер сверки
        $deposits_id=array();
        foreach ($deposits as $deposit) {
            $deposits_id[] = $deposit['DepositRunId'];
        };
        
        // Привязываем депозиты на добавленную сверку
        $sql = '
            UPDATE `cashmaster`.`DepositRuns`
            SET
                `DepositRecId` = "'.$last_rec_id.'"
            WHERE DepositRunId IN ('.  implode(",", $deposits_id).')
        ;';
        do_sql($sql);
        
        
            $scenario = get_scenario_by_id($scenario_id);
       
            $denoms = get_scenario_denoms_by_id($_SESSION[$program]['scenario'][0]);
            foreach ($denoms as $denom) {
                // делаем запись по умолчанию в DepositDenomTotal для этого денома
                do_sql('
                INSERT INTO `cashmaster`.`DepositDenomTotal`
                    (
                        `DepositReclId`,
                        `DenomId`,
                        `ExpectedCount`,
                        `ValuableTypeId`
                    )
                VALUES
                    (
                        "'.$last_rec_id.'",
                        "'.$denom['DenomId'].'",
                        "0",
                        "2"
                    )
                ;');
            };
            
            
            
            
            $denoms = get_sorter_accounting_data_denoms_by_rec_id($last_rec_id);
            foreach ($denoms as $denom) {
                // проверяем есть ли запись для такого денома
                $row = fetch_row_from_sql('
                    SELECT
                        `DepositDenomTotal`.`id`,
                        `DepositDenomTotal`.`DepositReclId`,
                        `DepositDenomTotal`.`DenomId`,
                        `DepositDenomTotal`.`ExpectedCount`
                    FROM 
                        `cashmaster`.`DepositDenomTotal`
                    WHERE
                        `DepositDenomTotal`.`DepositReclId` = "'.addslashes($last_rec_id).'"
                        AND `DepositDenomTotal`.`DenomId`= "'.addslashes($denom['DenomId']).'"
                ;');
                if($row[0]>0) {
                    // меняем существующую запись
                    do_sql('
                        UPDATE `cashmaster`.`DepositDenomTotal`
                        SET
                            `ExpectedCount` = 0
                        WHERE 
                            `DepositDenomTotal`.`DepositReclId` = "'.addslashes($last_rec_id).'"
                            AND `DepositDenomTotal`.`DenomId`= "'.  addslashes($denom['DenomId']).'"
                    ;');
                } else {
                    // делаем запись по умолчанию в DepositDenomTotal для этого денома
                    do_sql('
                    INSERT INTO `cashmaster`.`DepositDenomTotal`
                        (
                            `DepositReclId`,
                            `DenomId`,
                            `ExpectedCount`,
                            `ValuableTypeId`
                        )
                    VALUES
                        (
                            "'.$last_rec_id.'",
                            "'.$denom['DenomId'].'",
                            0,
                            2
                        )
                    ;');                    
                };
            };          
            
            
            
            
            foreach (get_scenario_currency($_SESSION[$program]['scenario'][0]) as $currency){
                //$summ = 0;
                // По каждой валюте данных пересчета для каждого номинала суммируем 
                // $scenario['DefExpectedNumber']*$denom['Value']
                //$denoms = get_sorter_accounting_data_denoms_by_rec_id_and_currency($last_rec_id, $currency[0]);
                //foreach ($denoms as $denom) {
                    //$summ += 0*$denom['Value'];
                //};
                // Добавляем сумму в DepositCurrencyTotal
                do_sql('
                INSERT INTO `cashmaster`.`DepositCurrencyTotal`
                    (
                        `DepositRecId`,
                        `CurrencyId`,
                        `ExpectedDepositValue`,
                        `DepositCurrencyTotal`.`ValuableTypeId`
                    )
                VALUES
                (
                    "'.addslashes($last_rec_id).'",
                    "'.addslashes($currency[0]).'",
                    0,
                    2
                )    
                ;');
            };
            
        //};
        
        // открываем запись в таблицы
        do_sql('UNLOCK TABLES');

        // Возвращаем код добавленной сверки
        return $last_rec_id;
    };
    
?>
