<?php

/*
 * Valuable types
 */
    if (!isset($c)) exit;

    
    // Первый шаг волшебника
    if (isset($_POST['GradeId'])) {
        if ( $_POST['action']== 'set' ) {
            do_sql('
                UPDATE 
                    `ScenReconGrades`
                SET
                    `IsUsed` = 1
                WHERE 
                    `ScenarioId` = "'.addslashes($_GET['id']).'"
                    AND `ValuableTypeId` = "'.addslashes($_POST['ValuableTypeId']).'"
                    AND GradeId = "'.addslashes($_POST['GradeId']).'"
            ;');
        };
        if ( $_POST['action']== 'unset' ) {
            /*
            // Проверяем есть ли используемая категория ценности в таблице сцен деномов
            $row = get_array_from_sql('
                SELECT
                    `ScenDenoms`.`DenomId`,
                    `ScenDenoms`.`ValuableTypeId`,
                    `ScenDenoms`.`IsUsed`
                FROM 
                    `ScenDenoms`
                WHERE
                    `ScenDenoms`.`ScenarioId`="'.addslashes($_GET['id']).'"
                    AND `ScenDenoms`.`ValuableTypeId`="'.addslashes($_POST['ValuableTypeId']).'"
                    AND `ScenDenoms`.`IsUsed`="1"
            ;');
            if (count($row)>0) {
                $data['error'] = 'Данный тип ценности уже использован при конфигурации номиналов'.'.'.'Удаление невозможно'.'!';
                include 'app/view/error_message.php';
            } else {
             * 
             */
                do_sql('
                    UPDATE 
                        `ScenReconGrades`
                    SET
                        `IsUsed` = 0
                    WHERE 
                        `ScenarioId` = "'.addslashes($_GET['id']).'"
                        AND `ValuableTypeId` = "'.addslashes($_POST['ValuableTypeId']).'"
                        AND GradeId = "'.addslashes($_POST['GradeId']).'"
                ;');    
            //};
        };
    };


    $valuable_types = array();
    $scen_types = get_array_from_sql('

        SELECT
            `ScenValuableTypes`.`ValuableTypeId`,
            `ScenValuableTypes`.`IsUsed`
        FROM 
            `ScenValuableTypes`
        WHERE
            `ScenValuableTypes`.`ScenarioId`="'.addslashes($_GET['id']).'"
             AND `ScenValuableTypes`.`IsUsed`=1
        GROUP BY Id
    ;');
    foreach ($scen_types as $key => $value) {
        $valuable_types[] = $value[0];
    };
    
    // начало конфигурирования сценария
    // 1. Получаем все грейды системы.
    $all_grades = get_assoc_array_from_sql('

        SELECT
            `Grades`.`GradeId`,
            `Grades`.`GradeLabel`,
            ValuableTypes.ValuableTypeLabel,
            ValuableTypes.ValuableTypeId
        FROM `cashmaster`.`Grades`
        CROSS JOIN
            ValuableTypes 
        WHERE
            ValuableTypes.ValuableTypeId IN ('.addslashes(implode(', ',$valuable_types)).')
        ORDER BY ValuableTypes.ValuableTypeId,`Grades`.`GradeLabel` ASC
    ;');
    //$rows = count($valuable_types);
   
    
    
    $available = array();
    $used = array();
    foreach ($all_grades as $value) {
        $row = fetch_assoc_row_from_sql('
            SELECT
                `ScenReconGrades`.`Id`,
                `ScenReconGrades`.`ScenarioId`,
                `ScenReconGrades`.`GradeId`,
                `ScenReconGrades`.`ValuableTypeId`,
                `ScenReconGrades`.`IsUsed`
            FROM 
                `ScenReconGrades`
            WHERE
                `ScenReconGrades`.`ScenarioId`="'.addslashes($_GET['id']).'"
                AND `ScenReconGrades`.`ValuableTypeId`="'.$value['ValuableTypeId'].'"
                AND GradeId="'.addslashes($value['GradeId']).'"
        ;');
        if (count($row)==0) {
            do_sql('
                INSERT INTO `cashmaster`.`ScenReconGrades`
                (
                    `ScenarioId`,
                    `GradeId`,
                    `ValuableTypeId`,
                    `IsUsed`
                )
                VALUES
                (
                    "'.addslashes($_GET['id']).'",
                    "'.addslashes($value['GradeId']).'",
                    "'.$value['ValuableTypeId'].'",
                    0
                )
            ;');
        };
        if (isset($row['IsUsed']) AND $row['IsUsed']=="1") {
            $used[] = array(
                $value['GradeId'],
                $value['GradeLabel'],
                $value['ValuableTypeLabel'],
                $value['ValuableTypeId']
            );
        } else {
            $available[] = array(
                $value['GradeId'],
                $value['GradeLabel'],
                $value['ValuableTypeLabel'],
                $value['ValuableTypeId']
            );
        };

    };
    
    
    if (count($used)>count($available)) {
        $rows = count($used);
    } else {
        $rows = count($available);
    };
    
    $h1 = explode('|', $_SESSION[$program]['lang']['wizard_denoms_table_headers']);
    
    // 3. Выводим таблицу для корректировки
    ?>
        <style>
            table.both td{
                border-bottom: gray solid thin;
                height:24px;
                vertical-align: middle;
            }
            table.both th{
                border: gray solid thin;                    
            }
            table.both {
                margin-right: 40px;
            }
            table.available {
                width: 320px;
            }
            table.used {
                width: 320px;
            }
            form {
                padding: 0px;
                margin: 0px;
            }
        </style>
        <div class="container">
            <h3 style="margin: 0px;color:lightgray;">1. <?php echo htmlfix($h[1]); ?></h3>
            <h3 style="margin: 0px;color:lightgray;">2. <?php echo htmlfix($h[3]); ?></h3>
            <h3 style="margin: 0px;color:lightgray;">3. <?php echo htmlfix($h[5]); ?></h3>
            <h3 style="margin: 0px;">4. <?php echo htmlfix($h[6]); ?>:</h3>
            <table>
                <tr>
                    <td style="text-align: center;vertical-align: top;">
                        <h4>
                            <?php echo htmlfix($_SESSION[$program]['lang']['scen_wizard_available']); ?>
                        </h4>
                        <table class="available both">
                            <tr>
                                <th><?php echo htmlfix($h1[2]); ?></th>
                                <th colspan="2"><?php echo htmlfix($h1[3]); ?></th>
                            </tr>
                            <?php 
                                $i = 0;
                                foreach($available as $key=>$value){
                                    ?>
                                        <tr>
                                            <td><?php echo htmlfix($value[2]); ?></td>
                                            <td align="left"><?php echo htmlfix($value[1]); ?></td>
                                            <td align="right">
                                                <form method="POST">
                                                    <input type="hidden" name="step" value="3"/>
                                                    <input type="hidden" name="GradeId" 
                                                           value="<?php echo htmlfix($value[0]); ?>"/>
                                                    <input type="hidden" name="ValuableTypeId" 
                                                           value="<?php echo htmlfix($value[3]); ?>"/>
                                                    <input type="hidden" name="action" value="set"/>
                                                    <input class="btn-small btn btn-danger" type="submit" value=">>"/>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                };
                                while ($i<$rows) {
                                    echo '<tr><td colspan="2">&nbsp;</td><td>&nbsp;</td></tr>';
                                    $i++;
                                };
                            ?>
                        </table>
                    </td>
                    <td style="text-align: center;vertical-align: top;">
                        <h4>
                            <?php echo htmlfix($_SESSION[$program]['lang']['scen_wizard_in_use']); ?>
                        </h4>
                        <table class="used both">
                            <tr>
                                <th colspan="2"><?php echo htmlfix($h1[2]); ?></th>
                                <th><?php echo htmlfix($h1[3]); ?></th>
                            </tr>
                            <?php 
                                $i = 0;
                                foreach($used as $key=>$value){
                                    ?>
                                        <tr>
                                            <td align="left">
                                                <form method="POST">
                                                    <input type="hidden" name="step" value="3"/>
                                                    <input type="hidden" name="GradeId" 
                                                           value="<?php echo htmlfix($value[0]); ?>"/>
                                                    <input type="hidden" name="ValuableTypeId" 
                                                           value="<?php echo htmlfix($value[3]); ?>"/>
                                                    <input type="hidden" name="action" value="unset"/>
                                                    <input class="btn-small btn btn-danger" type="submit" value="<<"/>
                                                </form>
                                                <td><?php echo htmlfix($value[2]); ?></td>
                                                <td align="left"><?php echo htmlfix($value[1]); ?></td>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                };
                                while ($i<$rows) {
                                    echo '<tr><td colspan="2">&nbsp;</td><td>&nbsp;</td></tr>';
                                    $i++;
                                };
                            ?>
                        </table>
                    </td>
                </tr>
            </table>
            <br/>
            <hr/>
            <form method="POST">
                <input id="step" type="hidden" name="step" value="4"/>
                <input 
                    type="submit" 
                    class="btn btn-primary btn-large" 
                    onclick="set_wait();$('input#step').val('2');"
                    value="<?php echo htmlfix($_SESSION[$program]['lang']['previous_step']); ?>"/>    
                <input 
                    type="submit" 
                    class="btn btn-warning btn-large" 
                    onclick="set_wait();"
                    value="<?php echo htmlfix($_SESSION[$program]['lang']['next_step']); ?>"
                />
            </form>
        </div>
    <?php
    exit;
    
?>
