<?php

/*
 * Denoms
 */
    if (!isset($c)) exit;
    
    
        // Второй шаг волшебника
        if (isset($_POST['DenomId'])) {
            if ( $_POST['action']== 'set' ) {
                do_sql('
                    UPDATE 
                        `ScenDenoms`
                    SET
                        `IsUsed` = 1
                    WHERE 
                        `ScenarioId` = "'.addslashes($_GET['id']).'"
                        AND `ValuableTypeId` = "'.addslashes($_POST['ValuableTypeId']).'"
                        AND DenomId = "'.addslashes($_POST['DenomId']).'"
                ;');
            };
            if ( $_POST['action']== 'unset' ) {
                // Проверяем можно ли удалять деномы из сценария, для этого
                // смотрим есть ли такой деном с ненулевым грейдом в валуабле_грейдс
                
                $sorter_grades = array();
                $row = get_array_from_sql('
                    SELECT
                        `ScenSorterGrades`.`GradeId`
                    FROM 
                        `ScenSorterGrades`
                    WHERE
                        `ScenSorterGrades`.`ScenarioId`="'.addslashes($_GET['id']).'"
                        AND `ScenSorterGrades`.`IsUsed`=1
                    GROUP BY
                        `ScenSorterGrades`.`GradeId`
                ;');
                foreach ($row as $value) {
                    $sorter_grades[] = $value[0];
                };
                
                $count = 0;
                if (count($sorter_grades) > 0) {
                    $count = count_rows_from_sql('
                        SELECT
                            *
                        FROM
                            ValuablesGrades
                        LEFT JOIN
                            Valuables ON Valuables.ValuableId = ValuablesGrades.ValuableId
                        WHERE
                            ValuablesGrades.ScenarioId="'.addslashes($_GET['id']).'"
                            AND Valuables.DenomId="'.addslashes($_POST['DenomId']).'"
                            AND ValuablesGrades.GradeId IN ('.implode(', ', $sorter_grades).')
                    ;');    
                };
                if ($count>0){
                    
                    // Деном используется в валуабле ID, убирать нельзя!
                    $data['info_header'] = $_SESSION[$program]['lang']['attention'];
                    $data['info_text'] = htmlfix($_SESSION[$program]['lang']['cannot_remove_denom']).'!';
                    include 'app/view/info_message.php';
                    
                } else {
                    do_sql('
                        UPDATE 
                            `ScenDenoms`
                        SET
                            `IsUsed` = 0
                        WHERE 
                            `ScenarioId` = "'.addslashes($_GET['id']).'"
                            AND `ValuableTypeId` = "'.addslashes($_POST['ValuableTypeId']).'"
                            AND DenomId = "'.addslashes($_POST['DenomId']).'"
                    ;');  
                };                
            };
        };
    
    
        // 1. Получаем все типы по этому сценарию
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
        
        if (count($valuable_types)==0) {
            // Возврат на начало с ошибкой
                $data['error'] = $_SESSION[$program]['lang']['select_one_valuable_type'];
                include 'app/view/error_message.php';
            ?>
            <div class="container">
                <br/>
                <form method="POST">
                    <input id="step" type="hidden" name="step" value="2"/>
                    <input 
                        type="submit" 
                        class="btn btn-primary btn-large" 
                        onclick="set_wait();$('input#step').remove();"
                        value="<?php echo htmlfix($_SESSION[$program]['lang']['previous_step']); ?>"/>       
                </form>
            </div>
            <?php
            exit;
        };
        
        
        // 2. Получаем все номиналы для всех типов ценностей из сценария
        $sql ='
            SELECT
                `Denoms`.`DenomId`,
                `Denoms`.`Value`,
                `Denoms`.`CurrencyId`,
                `Denoms`.`DenomLabel`,
                Currency.CurrSymbol,
                `ValuableTypes`.`ValuableTypeLabel`,
                Denoms.DenomLabel,
                `ValuableTypes`.`ValuableTypeId`
            FROM 
                `cashmaster`.`Denoms`
            LEFT JOIN
                Currency ON Currency.CurrencyId = `Denoms`.`CurrencyId`
            CROSS JOIN
                ValuableTypes 
            WHERE
                ValuableTypeId IN ('.addslashes(implode(', ',$valuable_types)).')
            ORDER BY Currency.CurrSymbol,`ValuableTypes`.`ValuableTypeId`, `Denoms`.`Value`
        ;';
        $all_denoms = get_assoc_array_from_sql($sql);
        
        $available_denoms = array();
        $used_denoms = array();
        
        // 3. Заполняем таблицу деномов сценария расставляя IsUsed = 0 по умолчанию
        foreach ($all_denoms as $key => $value) {
            $row = fetch_assoc_row_from_sql('
                SELECT
                    `ScenDenoms`.`DenomId`,
                    `ScenDenoms`.`ValuableTypeId`,
                    `ScenDenoms`.`IsUsed`
                FROM 
                    `ScenDenoms`
                WHERE
                    `ScenDenoms`.`ScenarioId`="'.addslashes($_GET['id']).'"
                    AND `ScenDenoms`.`DenomId`="'.addslashes($value['DenomId']).'"
                    AND `ScenDenoms`.`ValuableTypeId`="'.addslashes($value['ValuableTypeId']).'"
            ;');
            
            if (count($row)==0) {
                // Нужно добавить такой Деном в таблицу СценДеномс со значением ИсЮзед=0
                do_sql('
                    INSERT INTO 
                        `ScenDenoms`
                    (
                        `ScenarioId`,
                        `DenomId`,
                        `ValuableTypeId`,
                        `IsUsed`
                    )
                    VALUES
                    (
                        "'.addslashes($_GET['id']).'",
                        "'.addslashes($value['DenomId']).'",
                        "'.addslashes($value['ValuableTypeId']).'",
                        0
                    );
                ;');
            };
            if (isset($row['IsUsed']) AND $row['IsUsed']=="1") {
                $used_denoms[] = array(
                    $value['DenomId'],
                    $value['CurrSymbol'],
                    $value['DenomLabel'],
                    $value['ValuableTypeLabel'],
                    $value['ValuableTypeId']
                );
            } else {
                $available_denoms[] = array(
                    $value['DenomId'],
                    $value['CurrSymbol'],
                    $value['DenomLabel'],
                    $value['ValuableTypeLabel'],
                    $value['ValuableTypeId']
                );
            };
        };
        
        if (count($used_denoms)>count($available_denoms)) {
            $rows = count($used_denoms);
        } else {
            $rows = count($available_denoms);
        };
        
        $h = explode('|', $_SESSION[$program]['lang']['wizard_denoms_table_headers']);
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
            <h3 style="margin-top: 0px;">2. <?php echo htmlfix($h[2]); ?>:</h3>
            <table>
                <tr>
                    <td style="text-align: center;vertical-align: top;">
                        <h4>
                            <?php echo htmlfix($_SESSION[$program]['lang']['scen_wizard_available']); ?>
                        </h4>
                        <table class="available both">
                            <tr>
                                <th><?php echo htmlfix($h[0]); ?></th>
                                <th><?php echo htmlfix($h[1]); ?></th>
                                <th colspan="2"><?php echo htmlfix($h[2]); ?></th>
                            </tr>
                            <?php 
                                $i = 0;
                                foreach($available_denoms as $key=>$value){
                                    ?>
                                        <tr>
                                            <td style="text-align: center;width:50px;"><?php echo htmlfix($value[1]); ?></td>
                                            <td style="text-align: right;"><?php echo htmlfix($value[2]); ?></td>
                                            <td style="text-align: right;"><?php echo htmlfix($value[3]); ?></td>
                                            <td align="right">
                                                <form method="POST">
                                                    <input type="hidden" name="step" value="1"/>
                                                    <input type="hidden" name="DenomId" 
                                                           value="<?php echo htmlfix($value[0]); ?>"/>
                                                    <input type="hidden" name="ValuableTypeId" 
                                                           value="<?php echo htmlfix($value[4]); ?>"/>
                                                    <input type="hidden" name="action" value="set"/>
                                                    <input class="btn-small btn btn-danger" type="submit" value=">>"/>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                };
                                while ($i<$rows) {
                                    echo '<tr><td colspan="3">&nbsp;</td><td>&nbsp;</td></tr>';
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
                                <th colspan="2"><?php echo htmlfix($h[0]); ?></th>
                                <th><?php echo htmlfix($h[1]); ?></th>
                                <th><?php echo htmlfix($h[2]); ?></th>
                            </tr>
                            <?php 
                                $i = 0;
                                foreach($used_denoms as $key=>$value){
                                    ?>
                                        <tr>
                                            <td>
                                                <form method="POST">
                                                    <input type="hidden" name="step" value="1"/>
                                                    <input type="hidden" name="DenomId" 
                                                           value="<?php echo htmlfix($value[0]); ?>"/>
                                                    <input type="hidden" name="ValuableTypeId" 
                                                           value="<?php echo htmlfix($value[4]); ?>"/>
                                                    <input type="hidden" name="action" value="unset"/>
                                                    <input class="btn-small btn btn-danger" type="submit" value="<<"/>
                                                </form>
                                                <td style="text-align: center;width:80px;"><?php echo htmlfix($value[1]); ?></td>
                                                <td style="text-align: right;"><?php echo htmlfix($value[2]); ?></td>
                                                <td style="text-align: right;"><?php echo htmlfix($value[3]); ?></td>
                                            </td>
                                        </tr>
                                    <?php
                                    $i++;
                                };
                                while ($i<$rows) {
                                    echo '<tr><td colspan="3">&nbsp;</td><td>&nbsp;</td></tr>';
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
                <input id="step" type="hidden" name="step" value="2"/>
                <input 
                    type="submit" 
                    class="btn btn-primary btn-large" 
                    onclick="set_wait();$('input#step').remove();"
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
