<?php

/*
 * Перепривязка несверенных депозитов с номером разделительной карты к отложенной сверке
 */

    function rebind_deposits_to_recon($separator_id,$rec_id) {
        global $db, $program;
        // Получаем список депозитов с таким номером карты
        // TODO проверить чтобы не брало из другой кассы
        
        do_sql('LOCK TABLES DepositRuns WRITE, Machines WRITE, DepositRecs WRITE;');
        
        //echo 'Список несверенных депозитов с таким номером карты:';
        $deposits = get_array_from_sql('
             SELECT
                    DepositRunId
             FROM
                    DepositRuns
             LEFT JOIN
                    DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
             LEFT JOIN
                    Machines ON Machines.MachineDBId = DepositRuns.MachineDBId
             WHERE
                    DataSortCardNumber="'.addslashes($separator_id).'"
                    AND (`DepositRuns`.`DepositRecId` = "'.$rec_id.'" 
                            OR `DepositRuns`.`DepositRecId`="0" 
                            OR `DepositRuns`.`DepositRecId` is NULL)
                    AND (DepositRecs.ReconcileStatus="0" OR DepositRecs.ReconcileStatus is NULL)
                    AND Machines.CashRoomId = "'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'"
             ORDER BY DepositRunId ASC;
        ');
        $d = array();
        foreach ($deposits as $deposit) {
            $d[] = $deposit[0];
        };
        // если есть такие депозиты, то переподвязываем их на эту сверку
        if (count($d)>0) {
        // Привязываем депозиты на добавленную сверку
            $sql = '
                UPDATE `cashmaster`.`DepositRuns`
                SET
                    `DepositRecId` = "'.$rec_id.'"
                WHERE DepositRunId IN ('.  implode(",", $d).')
            ;';
            do_sql($sql);
        };
        do_sql('UNLOCK TABLES');
    };

?>
