<?php

/*
 * Reconciliation Controller
 */

        if (!isset($c)) exit;
        
        include_once './app/model/reconciliation/get_scenario_by_id.php';
        include_once './app/model/reconciliation/get_depositruns_by_rec_id.php';
        include_once './app/model/reconciliation/get_recon_accounting_data_by_rec_id.php';
        include_once './app/model/reconciliation/get_reconciliation_by_sort_card_number.php';
        include_once './app/model/reconciliation/get_sorter_accounting_data_by_run_id.php';
        include_once './app/model/reconciliation/get_sorter_accounting_data_by_rec_id.php';
        include_once './app/model/reconciliation/get_sorter_currency_by_rec_id.php';
        

        if (!isset($_GET['id'])) {
            //Переход на стартовую страницу
            header("Location: index.php");
            exit;
        } else {
            //Проверка существует такая открытая сверка или нет
            $sql = '
                SELECT
                    DepositRuns.DepositRecId
                FROM
                    DepositRuns
                LEFT JOIN
                    DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
                LEFT JOIN 
                    Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
                WHERE
                    `Machines`.`CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                    AND DepositRuns.DepositRecId="'.addslashes($_GET['id']).'"
                    AND DepositRecs.ReconcileStatus = 1 
                GROUP BY DepositRecId
            ';
            $rows = get_array_from_sql($sql);
            if (count($rows)>0){
                // Сверка закрыта
                include 'app/view/forms/finished_reconciliation.php';
                exit;
            } else {
                // Нет депозитов с такой картой
                $data['error']=  $_SESSION[$program]['lang']['wrong_separator_number'];
                switch ($_SESSION[$program]['user_role_id']) {
                    case 2;
                        include './app/controller/supervisor/index.php';
                        break;
                    case 3;
                        include './app/controller/operator/index.php';
                        break;
                    default:
                        break;
                };
                exit;                    
            };
        };
             
?>