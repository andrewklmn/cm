<?php

/*
 * Reconciliation Controller
 */

        if (!isset($c)) exit;
        
        /*
        include_once './app/model/reconciliation/get_scenario_by_id.php';
        include_once './app/model/reconciliation/get_depositruns_by_rec_id.php';
        include_once './app/model/reconciliation/get_recon_accounting_data_by_rec_id.php';
        include_once './app/model/reconciliation/get_reconciliation_by_sort_card_number.php';
        include_once './app/model/reconciliation/get_sorter_accounting_data_by_run_id.php';
        include_once './app/model/reconciliation/get_sorter_accounting_data_by_rec_id.php';
        include_once './app/model/reconciliation/get_sorter_currency_by_rec_id.php';
        
        if (!isset($_GET['separator_id'])) {
            //Переход на стартовую страницу
            header("Location: index.php");
            exit;
        } else {
            //Проверка существует такая отложенная сверка или нет
            $sql = '
                SELECT
                    DepositRecs.DepositRecId
                FROM
                    DepositRecs
                LEFT JOIN
                    DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
                LEFT JOIN 
                    Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
                WHERE
                    `Machines`.`CashRoomId`="'.  addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                    AND `DepositRuns`.`DataSortCardNumber`="'.addslashes($_GET['separator_id']).'"
                    AND DepositRuns.DepositRecId > 0
                    AND IFNULL(DepositRecs.ServiceRec,0) = 0 
                    AND IFNULL(DepositRecs.ReconcileStatus,0) = 0 
                GROUP BY DepositRecId
            ';
            $rows = get_array_from_sql($sql);
            
            if (count($rows)>0){
                // Есть открытая сверка по такой карте
                include 'app/view/forms/deferred_reconciliation.php';
                exit;
            } else {
                // Нет открытой сверки по такой карте
                //Проверка есть ли вообще депозиты с таким номером карты в несверенных
                $sql = '
                    SELECT
                        *
                    FROM
                        DepositRuns
                    LEFT JOIN
                        DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
                    LEFT JOIN 
                        Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
                    WHERE
                        `Machines`.`CashRoomId`="'.  addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                        AND `DepositRuns`.`DataSortCardNumber`="'.addslashes($_GET['separator_id']).'"
                        AND (`DepositRecs`.`ReconcileStatus`=0 OR `DepositRecs`.`ReconcileStatus` is NULL)
                ;';
                $rows= get_array_from_sql($sql);
                
                
                if(count($rows)>0) {
                    // Создаем новую сверку
                    // 
                    //          
                    //          СПРОСИТЬ НУЖНО ЛИ СОЗДАВАТЬ СВЕРКУ
                    // 
                    // 
                    //include './app/view/html_header.php';
                    //echo 'Новая сверка';
                    include 'app/view/forms/new_reconciliation.php';
                    exit;                    
                } else {
                    // Проверяем есть ли такая подготовка
                    $sql = '
                        SELECT
                            DepositRecs.DepositRecId
                        FROM
                            DepositRecs
                        WHERE
                            IFNULL(DepositRecs.ServiceRec,0) = 0 
                            AND IFNULL(DepositRecs.ReconcileStatus,0) = 0 
                            AND `DepositRecs`.`RecOperatorId` = 0
                            AND `DepositRecs`.`CardNumber`="'.addslashes($_GET['separator_id']).'"
                        GROUP BY DepositRecId
                    ';
                    $rows = get_array_from_sql($sql);
                    if (count($rows)>0) { 
                        // открываем отложенную сверку
                        include 'app/view/forms/prepared_reconciliation.php';
                        exit;
                    } else {
                        $was_canceled = 0; 
                        // Проверяем подготовку
                        include 'app/controller/common/preparaton/ask_about_preparation.php';

                        if ($was_canceled == 0) {
                            // Нет депозитов с такой картой
                            $data['error']=  htmlfix($_GET['separator_id']).' - '.$_SESSION[$program]['lang']['wrong_separator_number'];
                        };

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
            };
        };
         * 
         */

             
?>