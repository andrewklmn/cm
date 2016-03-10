<?php

/*
 * Scenario Selector
 */

        if (!isset($c)) exit;
        

        // Получаем текущий сценарий пользователя
        $sql='
            SELECT
                `Scenario`.`ScenarioId`,
                `Scenario`.`ScenarioName`,
                `Scenario`.`DefaultScenario`,
                `Scenario`.`LogicallyDeleted`
            FROM 
                `cashmaster`.`Scenario`
            WHERE 
                `Scenario`.`ScenarioId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CurrentScenario']).'"
                AND `Scenario`.`LogicallyDeleted`<>1
        ;';
        $rows = get_array_from_sql($sql);
        
        if (count($rows)==1) {
            $_SESSION[$program]['scenario'] = $rows[0];
        } else {
            // Не установлен рабочий сценарий, обратитесь к контроллеру
            $data['error'] = $_SESSION[$program]['lang']['no_current_scenario'];
            include 'app/view/error_message.php';
            exit;            
        };

?>
    <div class="container">
        <div class='pull-left span12' style='padding: 0px;margin: 0px;'>
            <div class="alert alert-info" style="margin:0px;">  
              <strong><?php echo htmlfix($_SESSION[$program]['lang']['work_scenario']); ?>:</strong>
              &nbsp;&nbsp;&nbsp;<?php echo $_SESSION[$program]['scenario'][1]; ?>&nbsp;&nbsp;&nbsp;
            </div> 
        </div>
    </div>
           