<?php

/* 
 * Контроллер спрашивающий про подготовку
 */

    if (!isset($c)) exit;

    // Проверяем включена ли подготовка
    $sql='
        SELECT
            UsePreparationStep
        FROM 
            Scenario
        WHERE 
            `Scenario`.`ScenarioId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CurrentScenario']).'"
            AND `Scenario`.`LogicallyDeleted`<>1
    ;';
    $row = fetch_row_from_sql($sql);

    $d = preg_replace("/[^a-zA-Z\_\-0-9]/",'', $_GET['separator_id']);
    
    if ($row[0]==1 AND $d!='') {
        if (!isset($_POST['create'])) {
            
            // Предлагаем создать подготовку
            $data['title'] = $_SESSION[$program]['lang']['operator_workflow'];
            include './app/model/menu.php';
            include './app/view/page_header_with_logout.php';
            include './app/view/set_remove_wait.php';
            
        ?>
                <div class="container alert-success alert">
                    <h3><?php echo htmlfix($d.' - '.$_SESSION[$program]['lang']['card_is_free_to_use']); ?>!</h3>
                    <h4><?php echo htmlfix($_SESSION[$program]['lang']['create_preparation']); ?>?</h4>
                    <br/>
                    <form method="POST">
                        <input 
                            id="yes" 
                            class="btn btn-large btn-danger" 
                            type="submit" 
                            name="create" 
                            value="<?php echo htmlfix($_SESSION[$program]['lang']['yes']); ?>"/>
                        <input 
                            id="no" 
                            class="btn btn-large btn-primary" 
                            type="submit" 
                            name="create" 
                            value="<?php echo htmlfix($_SESSION[$program]['lang']['no']); ?>"/>
                    </form>
                    <font style="font-size: 10px;">
                        <?php echo htmlfix($_SESSION[$program]['lang']['prep_key_instruction']); ?>.
                    </font>
                </div>
                <script>
                    $('input#yes').focus();
                    $('body').keyup(function(event){
                        switch(event.keyCode) {
                            case 27:
                                $('input#no').click();
                                break;
                            case 13:
                                //$('input#yes').click();
                                break;
                        };
                    });
                </script>
            <?php
            exit;            
        } else {
            if ($_POST['create']==$_SESSION[$program]['lang']['yes']) {
                
                // Создаем подготовку и открываем для редактирования как prepared
                $sql = '
                    INSERT INTO `cashmaster`.`DepositRecs`
                        (
                            `ScenarioId`,
                            `RecCreateDatetime`,
                            `RecLastChangeDatetime`,
                            `IsBalanced`,
                            `ReconcileStatus`,
                            `FwdToSupervisor`,
                            `DepositIndexId`,
                            `Reported`,
                            `ServiceRec`,
                            `CardNumber`,
                            `PrepOperatorId`
                        )
                    VALUES
                        (
                            "'.$_SESSION[$program]['scenario'][0].'",
                            CURRENT_TIMESTAMP,
                            CURRENT_TIMESTAMP,
                            0,
                            0,
                            0,
                            0,
                            0,
                            0,
                            "'.addslashes($d).'",
                            "'.addslashes($_SESSION[$program]['user_id']).'"
                        )
                ;';
                do_sql($sql);
                
                // Есть открытая сверка по такой карте
                include 'app/view/forms/prepared_reconciliation.php';
                
                exit;
            } else {
                $was_canceled = 1;
            };
        };
    };



?>