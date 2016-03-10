<?php

/*
 * Scenario Selector
 */

        if (!isset($c)) exit;
        
        if(isset($_POST['action']) AND $_POST['action']=='change_scenario') {
            if (isset($_POST['new_default']) AND $_POST['new_default']!='') {
                $sql='
                    SELECT
                        `Scenario`.`ScenarioId`,
                        `Scenario`.`ScenarioName`,
                        `Scenario`.`DefaultScenario`,
                        `Scenario`.`LogicallyDeleted`
                    FROM 
                        `cashmaster`.`Scenario`
                    WHERE 
                        `Scenario`.`ScenarioId`='.  addslashes($_POST['new_default']).'
                        AND `Scenario`.`LogicallyDeleted`<>1
                ;';
                $rows = get_array_from_sql($sql);
                if(count($rows)>0) {
                    $_SESSION[$program]['scenario'] = $rows[0];
                } else {
                    $sql='
                        SELECT
                            `Scenario`.`ScenarioId`,
                            `Scenario`.`ScenarioName`,
                            `Scenario`.`DefaultScenario`,
                            `Scenario`.`LogicallyDeleted`
                        FROM 
                            `cashmaster`.`Scenario`
                        WHERE 
                            `Scenario`.`LogicallyDeleted`<>1
                        ORDER BY `Scenario`.`ScenarioId` ASC
                    ;';
                    $rows = get_array_from_sql($sql);
                    $_SESSION[$program]['scenario'] = $rows[0];
                };
            } else {
                    //Форма выбора сценария
                    $sql = '                        
                        SELECT
                            `Scenario`.`ScenarioId`,
                            `Scenario`.`ScenarioName`,
                            `Scenario`.`DefaultScenario`,
                            `Scenario`.`LogicallyDeleted`
                        FROM 
                            `cashmaster`.`Scenario`
                        WHERE 
                            `Scenario`.`LogicallyDeleted`<>1
                        ORDER BY `Scenario`.`ScenarioId` ASC';
                    $rows = get_array_from_sql($sql);
                ?>
                    <div class="container">
                        <div class="alert alert-info">  
                          <form method="POST" style="padding:0px;margin:0px;">
                          <strong><?php echo $_SESSION[$program]['lang']['select_scenario']; ?>:</strong>
                              &nbsp;&nbsp;&nbsp;
                              <select style="width:500px;margin: 0px;" name="new_default">
                                  <?php 
                                      foreach ($rows as $key => $value) {
                                          if ($value[0]==$_SESSION[$program]['scenario'][0]) {
                                              echo '<option selected value="',$value[0],'">',htmlfix($value[1]),'</option>';
                                          } else {
                                              echo '<option value="',$value[0],'">',htmlfix($value[1]),'</option>';
                                          }
                                      }
                                  ?>
                              </select>
                              &nbsp;&nbsp;&nbsp;
                              <input type="hidden" name="action" value="change_scenario"/>
                              <button type="submit" class="btn btn-small btn-primary">
                                  <?php echo $_SESSION[$program]['lang']['change_scenario']; ?>
                              </button>
                          </form>
                        </div> 
                    </div>
                </body>
                </html>
                <?php
                exit;
            }
        }
        
        //Если сенарий не задан то берем дефолтный из табицы
        if (!isset($_SESSION[$program]['scenario'])) {
            $sql='
                SELECT
                    `Scenario`.`ScenarioId`,
                    `Scenario`.`ScenarioName`,
                    `Scenario`.`DefaultScenario`,
                    `Scenario`.`LogicallyDeleted`
                FROM 
                    `cashmaster`.`Scenario`
                WHERE 
                    `Scenario`.`DefaultScenario`=1
                    AND `Scenario`.`LogicallyDeleted`<>1
            ;';
            $rows = get_array_from_sql($sql);
            if(count($rows)>0) {
                $_SESSION[$program]['scenario'] = $rows[0];
            } else {
                $sql='
                    SELECT
                        `Scenario`.`ScenarioId`,
                        `Scenario`.`ScenarioName`,
                        `Scenario`.`DefaultScenario`,
                        `Scenario`.`LogicallyDeleted`
                    FROM 
                        `cashmaster`.`Scenario`
                    WHERE 
                        `Scenario`.`LogicallyDeleted`<>1
                    ORDER BY `Scenario`.`ScenarioId` ASC
                ;';
                $rows = get_array_from_sql($sql);
                $_SESSION[$program]['scenario'] = $rows[0];
            };
        };
        
?>
    <div class="container">
        <div class='pull-left span12' style='padding: 0px;margin: 0px;'>
            <div class="alert alert-info" style="margin:0px;">  
              <form method="POST" style="padding:0px;margin:0px;">
              <strong><?php echo $_SESSION[$program]['lang']['work_scenario']; ?>:</strong>
              &nbsp;&nbsp;&nbsp;<?php echo $_SESSION[$program]['scenario'][1]; ?>&nbsp;&nbsp;&nbsp;
                  <input type="hidden" name="action" value="change_scenario"/>
                  <button type="submit" class="btn btn-small btn-info pull">
                      <?php echo $_SESSION[$program]['lang']['change_scenario']; ?>
                  </button>
              </form>
            </div> 
        </div>
    </div>
           