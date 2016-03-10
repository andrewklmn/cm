<?php

/*
 * Prepared reconciliation form
 */

        if (!isset($c)) exit;
        
        $data['title'] = $_SESSION[$program]['lang']['reconciliation'];
        include_once './app/view/page_header_with_logout.php';
        include_once './app/view/set_remove_wait.php'; 
        include_once './app/model/reconciliation/recon_function_load.php';
        include_once './app/view/draw_simple_table.php';
        
        include_once './app/view/print_array_as_html_table.php';
        include_once './app/model/reconciliation/get_scenario_by_id.php';
        
        
        $_GET['separator_id'] = preg_replace("/[^a-zA-Z\_\-0-9]/",'', $_GET['separator_id']);
        
        // Так как это подготовка, то находим её id по номеру разделительной карты 
        $row = fetch_row_from_sql('
            SELECT
                `DepositRecs`.`DepositRecId`
            FROM 
                DepositRecs 
            LEFT JOIN
                UserConfiguration ON DepositRecs.PrepOperatorId = UserConfiguration.UserId          
            WHERE
                `DepositRecs`.`CardNumber`="'.addslashes($_GET['separator_id']).'"
                AND IFNULL(DepositRecs.ReconcileStatus,0)="0"
                AND IFNULL(DepositRecs.ServiceRec,0)="0"
                AND UserConfiguration.CashRoomId="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            GROUP BY DepositRecs.DepositRecId
        ;');
        
        if (!isset($row[0])) {
            // Проверяем если это подготовка
            $sql ='
                SELECT
                    `DepositRecs`.`DepositRecId`
                FROM 
                    DepositRecs
                WHERE
                `DepositRecs`.`CardNumber`="'.addslashes($_GET['separator_id']).'"
                AND IFNULL(DepositRecs.ReconcileStatus,0)="0"
                AND IFNULL(DepositRecs.ServiceRec,0)="0"
            GROUP BY DepositRecs.DepositRecId                    
            ;';
            
            $row = fetch_row_from_sql($sql);
            if (!isset($row[0])) {

                $data['error'] = $_SESSION[$program]['lang']['deposit_by_card'].htmlfix($_GET['separator_id']).$_SESSION[$program]['lang']['was_reconciled'];
                include './app/view/error_message.php';
                ?>
                    <hr/>
                    <div class="container">
                        <button
                            onclick="back_to_workflow();"
                            class="btn-primary btn-large" href="index.php"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
                    </div>
                <?php
                exit;                
            };
        };
        
        $DepositRecId = $row[0];
        $DepositRec = fetch_assoc_row_from_sql('
            SELECT
                *
            FROM 
                `DepositRecs`
            WHERE
                DepositRecId="'.addslashes($DepositRecId).'"
        ;');
        $DepositRec_last_operator_id=$DepositRec['RecOperatorId'];
        
        
        $display_button= true;
        $rashozhdenie = false;
        $no_sverka_button = false;
        
        ?>
            <script>
                
                function back_to_workflow() {
                    window.location.replace('index.php');
                };
                
                
                $('body').keyup(function(e){
                    //var key = e.keyCode;
                    //alert(key);
                });
                
            </script>
        <?php
        
        
        //echo '<pre>';
        //print_r($DepositRec);
        //echo '</pre>';
        
        /*
         *  Тут нужна проверка кассы пересчета пользователя подготовки
         * 
         * 
         * 
         * 
         * 
        if ($_SESSION[$program]['user_id']!=$DepositRec_last_operator_id
                AND $_SESSION[$program]['user_role_id']!=2) {
                $data['error'] = $_SESSION[$program]['lang']['access_denied'];
                include './app/view/error_message.php';
                ?>
                    <hr/>
                    <div class="container">
                        <button
                            onclick="back_to_workflow();"
                            class="btn-primary btn-large" href="index.php"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
                    </div>
                <?php
                
        } else {
        */
        
            
        
            if ($_SESSION[$program]['UserConfiguration']['UserRoleId']==3) {
                if ($DepositRec['ScenarioId'] != $_SESSION[$program]['UserConfiguration']['CurrentScenario']) {
                    $data['danger_header'] = $_SESSION[$program]['lang']['attention'];
                    $data['danger_text'] = $_SESSION[$program]['lang']['scenario_was_changed_to_current'];
                    include './app/view/danger_message.php';
                };
                include 'app/controller/echo_current_scenario.php';
                do_sql('
                    UPDATE 
                        `cashmaster`.`DepositRecs`
                    SET
                        `ScenarioId` = "'.addslashes($_SESSION[$program]['scenario'][0]).'"
                    WHERE 
                        `DepositRecId` = "'.addslashes($DepositRecId).'"
                ;');
            } else {
                // Проверка и автоподстановка сценария сверки для контролера
                include 'app/controller/common/reconciliation/auto_change_scenario.php';
                include 'app/controller/select_scenario.php';
            };
            
            $scenario = get_scenario_by_id($_SESSION[$program]['scenario']);
            
            if ($scenario['ReconcileAgainstValue']==1) {
                // пересчет суммы
                include './app/view/reconciliation/prep_against_value.php';
            } else {
                // пересчет количества
                include './app/view/reconciliation/prep_against_amount.php';
            };
        //};
?>