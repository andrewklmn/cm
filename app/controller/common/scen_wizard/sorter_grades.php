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
                    `ScenSorterGrades`
                SET
                    `IsUsed` = 1
                WHERE 
                    `ScenarioId` = "'.addslashes($_GET['id']).'"
                    AND `ValuableTypeId` = "'.addslashes($_POST['ValuableTypeId']).'"
                    AND GradeId = "'.addslashes($_POST['GradeId']).'"
            ;');
        };
        if ( $_POST['action']== 'unset' ) {

            $row = get_array_from_sql('
                SELECT
                    *
                FROM
                    ValuablesGrades
                LEFT JOIN
                    Valuables ON Valuables.ValuableId = ValuablesGrades.ValuableId
                WHERE
                    `ValuablesGrades`.`ScenarioId`="'.addslashes($_GET['id']).'"
                    AND GradeId = "'.addslashes($_POST['GradeId']).'"
            ;');
            
            if(count($row)>0) {
                // грейд используется в валуабле ID, убирать нельзя!
                $data['info_header'] = $_SESSION[$program]['lang']['attention'];
                $data['info_text'] = htmlfix($_SESSION[$program]['lang']['cannot_remove_grade']).'!';
                include 'app/view/info_message.php';
            } else {
                do_sql('
                    UPDATE 
                        `ScenSorterGrades`
                    SET
                        `IsUsed` = 0
                    WHERE 
                        `ScenarioId` = "'.addslashes($_GET['id']).'"
                        AND `ValuableTypeId` = "'.addslashes($_POST['ValuableTypeId']).'"
                        AND GradeId = "'.addslashes($_POST['GradeId']).'"
                ;');  
            };
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
                `ScenSorterGrades`.`Id`,
                `ScenSorterGrades`.`ScenarioId`,
                `ScenSorterGrades`.`GradeId`,
                `ScenSorterGrades`.`ValuableTypeId`,
                `ScenSorterGrades`.`IsUsed`
            FROM 
                `ScenSorterGrades`
            WHERE
                `ScenSorterGrades`.`ScenarioId`="'.addslashes($_GET['id']).'"
                AND `ScenSorterGrades`.`ValuableTypeId`="'.$value['ValuableTypeId'].'"
                AND GradeId="'.addslashes($value['GradeId']).'"
        ;');
        if (count($row)==0) {
            do_sql('
                INSERT INTO `cashmaster`.`ScenSorterGrades`
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
            <h3 style="margin: 0px;">3. <?php echo htmlfix($h[4]); ?>:</h3>
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
                                                    <input type="hidden" name="step" value="2"/>
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
                                                    <input type="hidden" name="step" value="2"/>
                                                    <input type="hidden" name="GradeId" 
                                                           value="<?php echo htmlfix($value[0]); ?>"/>
                                                    <input type="hidden" name="ValuableTypeId" 
                                                           value="<?php echo htmlfix($value[3]); ?>"/>
                                                    <input type="hidden" name="action" value="unset"/>
                                                    <input class="btn-small btn btn-danger" type="submit" value="<<"/>
                                                </form>
                                                <td><?php echo htmlfix($value[2]); ?></td>
                                                <td><?php echo htmlfix($value[1]); ?></td>
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
                <input id="step" type="hidden" name="step" value="3"/>
                <input 
                    type="submit" 
                    class="btn btn-primary btn-large" 
                    onclick="set_wait();$('input#step').val('1');"
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
