<?php

/*
 * Valuable types
 */
    if (!isset($c)) exit;

    // Первый шаг волшебника
    if (isset($_POST['ValuableTypeId'])) {
        if ( $_POST['action']== 'set' ) {
            do_sql('
                UPDATE 
                    `ScenValuableTypes`
                SET
                    `IsUsed` = 1
                WHERE 
                    `ScenarioId` = "'.addslashes($_GET['id']).'"
                    AND `ValuableTypeId` = "'.addslashes($_POST['ValuableTypeId']).'"
            ;');
        };
        if ( $_POST['action']== 'unset' ) {
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
                $data['error'] = $_SESSION[$program]['lang']['cannot_remove_valuable_type'];
                include 'app/view/error_message.php';
            } else {
                do_sql('
                    UPDATE 
                        `ScenValuableTypes`
                    SET
                        `IsUsed` = 0
                    WHERE 
                        `ScenarioId` = "'.addslashes($_GET['id']).'"
                        AND `ValuableTypeId` = "'.addslashes($_POST['ValuableTypeId']).'"
                ;');    
            };
        };
    };

    // начало конфигурирования сценария
    // 1. Получаем все типы ценностей системы.
    $valuable_types = get_assoc_array_from_sql('
        SELECT
            `ValuableTypes`.`ValuableTypeId`,
            `ValuableTypes`.`ValuableTypeName`,
            `ValuableTypes`.`ValuableTypeLabel`,
            `ValuableTypes`.`SerialNumberIsUsed`
        FROM 
            `ValuableTypes`
        ORDER BY 
            `ValuableTypes`.`ValuableTypeLabel` ASC

    ;');
    //$rows = count($valuable_types);
    $available_types = array();
    $used_types = array();
    // 2. Заполняем таблицу типов ценностей сценария расставляя IsUsed = 0
    foreach ($valuable_types as $value) {
        $row = fetch_assoc_row_from_sql('
            SELECT
                `ScenValuableTypes`.`Id`,
                `ScenValuableTypes`.`ScenarioId`,
                `ScenValuableTypes`.`ValuableTypeId`,
                `ScenValuableTypes`.`IsUsed`
            FROM 
                `cashmaster`.`ScenValuableTypes`
            WHERE
                `ScenValuableTypes`.`ScenarioId`="'.addslashes($_GET['id']).'"
                AND `ScenValuableTypes`.`ValuableTypeId`="'.$value['ValuableTypeId'].'";
        ;');
        if (count($row)==0) {
            // Нужно добавить такой ВалуаблеТип в таблицу СценВалуаблеТипс со значением 0
            do_sql('
                INSERT INTO `cashmaster`.`ScenValuableTypes`
                    (
                        `ScenarioId`,
                        `ValuableTypeId`,
                        `IsUsed`
                    )
                VALUES
                    (
                        "'.addslashes($_GET['id']).'",
                        "'.$value['ValuableTypeId'].'",
                        0
                    );
            ;');
        };
        if (isset($row['IsUsed']) AND $row['IsUsed']=="1") {
            $used_types[] = array(
                $value['ValuableTypeId'],
                $value['ValuableTypeLabel']
            );
        } else {
            $available_types[] = array(
                $value['ValuableTypeId'],
                $value['ValuableTypeLabel']
            );
        };

    };
    
    
    if (count($used_types)>count($available_types)) {
        $rows = count($used_types);
    } else {
        $rows = count($available_types);
    };
    
    
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
            <h3>1. <?php echo htmlfix($h[0]); ?>:</h3>
            <br/>
            <table>
                <tr>
                    <td style="vertical-align: top;">
                        <table class="available both">
                            <tr>
                                <th colspan="2">
                                    <?php echo htmlfix($_SESSION[$program]['lang']['scen_wizard_available']); ?>
                                </th>
                            </tr>
                            <?php 
                                $i = 0;
                                foreach($available_types as $key=>$value){
                                    ?>
                                        <tr>
                                            <td><?php echo htmlfix($value[1]); ?></td>
                                            <td align="right">
                                                <form method="POST">
                                                    <input type="hidden" name="ValuableTypeId" 
                                                           value="<?php echo htmlfix($value[0]); ?>"/>
                                                    <input type="hidden" name="action" value="set"/>
                                                    <input class="btn-small btn btn-danger" type="submit" value=">>"/>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                };
                                while ($i<$rows) {
                                    echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
                                    $i++;
                                };
                            ?>
                        </table>
                    </td>
                    <td style="vertical-align: top;">
                        <table class="used both">
                            <tr>
                                <th colspan="2">
                                    <?php echo htmlfix($_SESSION[$program]['lang']['scen_wizard_in_use']); ?>
                                </th>
                            </tr>
                            <?php 
                                $i = 0;
                                foreach($used_types as $key=>$value){
                                    ?>
                                        <tr>
                                            <td>
                                                <form method="POST">
                                                    <input type="hidden" name="ValuableTypeId" 
                                                           value="<?php echo htmlfix($value[0]); ?>"/>
                                                    <input type="hidden" name="action" value="unset"/>
                                                    <input class="btn-small btn btn-danger" type="submit" value="<<"/>
                                                </form>
                                                <td><?php echo htmlfix($value[1]); ?></td>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                };
                                while ($i<$rows) {
                                    echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
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
                <input type="hidden" name="step" value="1"/>
                <a href="?c=scen_edit&id=<?php echo htmlfix($_GET['id']); ?>" class="btn btn-primary btn-large" onclick="set_wait();">
                    <?php echo htmlfix($_SESSION[$program]['lang']['back_to_scen_edit']); ?>
                </a>
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
