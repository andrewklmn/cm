<?php

/*
 * Finished reconciliation form
 */

        if (!isset($c)) exit;
        
        $data['title'] = $_SESSION[$program]['lang']['reconciliation'];
        include_once './app/view/page_header_with_logout.php';
        include_once './app/view/set_remove_wait.php'; 
        include_once './app/model/reconciliation/recon_function_load.php';
        include_once './app/view/draw_simple_table.php';
        
        $display_button= true;
        $rashozhdenie = false;
        $no_sverka_button = false;
        
        ?>
            <script>
                function back_to_workflow() {
                    window.location.replace('index.php');
                };
            </script>
        <?php
        
        
            if ($_SESSION[$program]['UserConfiguration']['UserRoleId']==3) {
                include 'app/controller/echo_current_scenario.php';
                if (isset($DepositRecId)) {
                    do_sql('
                        UPDATE 
                            `cashmaster`.`DepositRecs`
                        SET
                            `ScenarioId` = "'.addslashes($_SESSION[$program]['scenario'][0]).'"
                        WHERE 
                            `DepositRecId` = "'.addslashes($DepositRecId).'"
                    ;');
                };
            } else {
                // Проверка и автоподстановка сценария сверки
                include 'app/controller/common/reconciliation/auto_change_scenario.php';
                include 'app/controller/select_scenario.php';
            };
            $scenario = get_scenario_by_id($_SESSION[$program]['scenario']);

            echo '<br/>';
            
            //echo '<pre>OK</pre>';
            
            if ($scenario['ReconcileAgainstValue']==1) {
                // пересчет суммы
                include './app/view/reconciliation/against_value_new.php';
            } else {
                // пересчет количества
                include './app/view/reconciliation/against_amount_new.php';
            }
?>