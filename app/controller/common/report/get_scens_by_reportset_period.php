<?php

/*
 * Получаем сценарии использованные за этот период
 */
    if (!isset($c)) exit;

        // Получаем сценарии использованные за этот период
        $scens = get_array_from_sql('
            SELECT
                DepositRecs.ScenarioId
            FROM 
                `cashmaster`.`DepositRecs`
            LEFT JOIN
                DepositRuns ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
            LEFT JOIN
                Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
            LEFT JOIN
                CashRooms ON `CashRooms`.`Id`=`Machines`.`CashRoomId`
            WHERE
                `DepositRecs`.`ServiceRec`=0
                AND `DepositRecs`.`ReconcileStatus`=1
                AND `DepositRecs`.`RecLastChangeDatetime` > "'.addslashes($_POST['start_datetime']).'"
                AND `DepositRecs`.`RecLastChangeDatetime` <= "'.addslashes($_POST['stop_datetime']).'"
                AND `Machines`.`CashRoomId`="'.  addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            GROUP BY
                DepositRecs.ScenarioId
        ;');
        $scen_ids = array();
        foreach ($scens as $value) {
            $scen_ids[]=$value[0];
        };

?>
