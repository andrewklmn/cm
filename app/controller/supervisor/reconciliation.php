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
        
        
        if (!isset($_GET['separator_id'])) {
            //Переход на стартовую страницу
            header("Location: index.php");
            exit;
        } else {
        
            $t = substr($_REQUEST['separator_id'],0,22);
            $t = preg_replace("/[^a-zA-Z\_\-0-9]/",'', $t);
            $_REQUEST['separator_id'] = $t;
            $_GET['separator_id'] = $t;
            $_POST['separator_id'] = $t;
            
            // Проверяем - не квитанция ли это
            include 'app/controller/common/preparaton/check_receipt.php';
            
            // Проверяем - не предподготовка ли это
            include 'app/controller/common/preparaton/check_prebook.php';
            
            // Получаем свойства текущего сценария
            $scenario = get_scenario_by_id($_SESSION[$program]['scenario'][0]);
            
            //1. проверяем есть ли отложенные сверки в работе с таким номером
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
                //  Если есть, то открываем для редактирования как отложенную
                // echo 'Deffered opened';
                include 'app/view/forms/deferred_reconciliation.php';
                exit;
            } else {
                //2. проверяем есть ли прогоны с таким номером
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
                    if ($scenario['UsePreparationStep']==1) {
                        // echo 'Work scenario with prep<br/>';
                        //  Если сценарий с подготовкой 
                            // Проверяем есть ли подготовки с таким номером
                            // Проверяем есть ли такая подготовка
                            $sql = '
                                SELECT
                                    DepositRecs.DepositRecId
                                FROM
                                    DepositRecs
                                LEFT JOIN
                                    UserConfiguration ON DepositRecs.PrepOperatorId = UserConfiguration.UserId
                                WHERE
                                    IFNULL(DepositRecs.ServiceRec,0) = 0 
                                    AND IFNULL(DepositRecs.ReconcileStatus,0) = 0 
                                    AND `DepositRecs`.`RecOperatorId` = 0
                                    AND `DepositRecs`.`CardNumber`="'.addslashes($_GET['separator_id']).'"
                                    AND UserConfiguration.CashRoomId="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                                GROUP BY DepositRecId
                            ';
                            $rows = get_array_from_sql($sql);
                            if (count($rows)>0) { 
                                // если есть, то сводим их и делаем отложенной
                                echo 'Depositruns exists<br/>';
                                echo 'Prep exist<br/>';
                            } else {                                
                                // если нет, то предлагаем создать подготовку 
                                    // Если да, то создаем, связываем и открываем как отложенную
                                    // Если нет, то переход на рабочий экран 
                                include 'app/controller/common/preparaton/ask_about_preparation_for_existing_depositruns.php';
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
                    } else {
                        // echo 'Work scenario without prep<br/>';
                        //  Если сценарий без подготовки, то 
                        //      создаем сверку и открываем её как отложенную
                        include 'app/view/forms/new_reconciliation.php';
                        exit;
                    };
                } else {
                    //echo 'No depositruns<br/>';
                    //  Если нету
                    //  Если сценарий с подготовкой то проверяем есть ли подготовки с таким номером
                    if ($scenario['UsePreparationStep']==1) {
                        // echo 'Work scenario with prep<br/>';
                        //  Если есть, то проверяем есть подготовки с таким номером
                        $sql = '
                            SELECT
                                DepositRecs.DepositRecId
                            FROM
                                DepositRecs
                            LEFT JOIN
                                UserConfiguration ON DepositRecs.PrepOperatorId = UserConfiguration.UserId
                            WHERE
                                IFNULL(DepositRecs.ServiceRec,0) = 0 
                                AND IFNULL(DepositRecs.ReconcileStatus,0) = 0 
                                AND `DepositRecs`.`RecOperatorId` = 0
                                AND `DepositRecs`.`CardNumber`="'.addslashes($_GET['separator_id']).'"
                                AND UserConfiguration.CashRoomId="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                            GROUP BY DepositRecId
                        ';
                        $rows = get_array_from_sql($sql);
                        if (count($rows)>0) {
                            // echo 'Prep exist<br/>';
                            // Если есть то открываем её
                            include 'app/view/forms/prepared_reconciliation.php';
                            exit;
                        } else {
                            //echo 'Prep not exist<br/>';
                            // Сверки нет, поєтому спрашиваем о создании

                            // Проверяем подготовку
                            include 'app/controller/common/preparaton/ask_about_preparation.php';
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
                    } else {
                        //echo 'Work scenario without prep<br/>';
                        //  Если сценарий без подготовки то сообщаем что номер неправильный 
                        $data['error']=  htmlfix($_GET['separator_id']).' - '.$_SESSION[$program]['lang']['wrong_separator_number'];
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

             
?>