<?php

/*
 * Valuable types
 */
    if (!isset($c)) exit;
    
    
    // Определяем типы ценностей сценария
    $valuable_types = array();
    $rows = get_assoc_array_from_sql('
        SELECT
            `ScenValuableTypes`.`ValuableTypeId`
        FROM 
            `cashmaster`.`ScenValuableTypes`
        WHERE
            `ScenValuableTypes`.`ScenarioId`="'.addslashes($_GET['id']).'"
            AND `ScenValuableTypes`.`IsUsed`=1
        ORDER BY ValuableTypeId ASC
    ;');
    foreach ($rows as $key => $value) {
        $valuable_types[] = $value['ValuableTypeId'];
    };
    
    
    $sorter_grades = get_assoc_array_from_sql('
        SELECT
            `ScenSorterGrades`.`Id`,
            `ScenSorterGrades`.`ScenarioId`,
            `ScenSorterGrades`.`GradeId`,
            `ScenSorterGrades`.`ValuableTypeId`,
            `ScenSorterGrades`.`IsUsed`,
            Grades.GradeLabel
        FROM 
            `cashmaster`.`ScenSorterGrades`
        LEFT JOIN
            Grades ON Grades.GradeId = `ScenSorterGrades`.`GradeId`
        WHERE
            `ScenSorterGrades`.`ScenarioId`="'.addslashes($_GET['id']).'"
            AND `ScenSorterGrades`.`IsUsed`=1
    ;');
 
    $valuable_grades = array();
    // для типа проверяем наличие деномов 
    foreach ($valuable_types as $valuable_type_id) {
        
        $denoms = array();
        $rows = get_assoc_array_from_sql('
            SELECT
                `ScenDenoms`.`DenomId`
            FROM `cashmaster`.`ScenDenoms`
            WHERE
                `ScenDenoms`.`ScenarioId`="'.addslashes($_GET['id']).'"
                AND `ScenDenoms`.`IsUsed`=1
                AND `ScenDenoms`.`ValuableTypeId`="'.$valuable_type_id.'"
        ;');
        foreach ($rows as $key => $value) {
            $denoms[] = $value['DenomId'];
        };
    

        
        $valuables = array();
        if (count($denoms)>0) {
            // Получаем все валуаблы по сценарию c типом  и деномом
            $sql = '
                SELECT
                    `Valuables`.`ValuableId`,
                    `Valuables`.`CategoryName`,
                    `Valuables`.`SorterTypeId`,
                    `Valuables`.`DenomId`,
                    `Valuables`.`ValuableTypeId`,
                    Denoms.DenomLabel,
                    ValuableTypes.ValuableTypeLabel,
                    SorterTypes.SorterType,
                    Currency.CurrSymbol
                FROM 
                    `cashmaster`.`Valuables`
                LEFT JOIN
                    Denoms ON Denoms.DenomId = `Valuables`.`DenomId`
                LEFT JOIN
                    Currency ON Currency.CurrencyId = Denoms.CurrencyId
                LEFT JOIN
                    ValuableTypes ON ValuableTypes.ValuableTypeId = `Valuables`.`ValuableTypeId`
                LEFT JOIN
                    SorterTypes ON SorterTypes.SorterTypeId=`Valuables`.`SorterTypeId`
                WHERE
                    `Valuables`.`DenomId` IN ('.  implode(', ', $denoms).')
                    AND `Valuables`.`ValuableTypeId`="'.$valuable_type_id.'"
                ORDER BY 
                    `Valuables`.`CategoryName` ASC
            ;';
            $valuables = get_assoc_array_from_sql($sql); 
        }
    
        foreach ($valuables as $key => $value) {
            $row = fetch_assoc_row_from_sql('
                SELECT
                    `ValuablesGrades`.`SequenceId`,
                    `ValuablesGrades`.`ScenarioId`,
                    `ValuablesGrades`.`ValuableId`,
                    `ValuablesGrades`.`GradeId`,
                    IFNULL(Grades.GradeLabel,"-") as Label
                FROM 
                    `ValuablesGrades`
                LEFT JOIN
                    Grades ON Grades.GradeId = `ValuablesGrades`.`GradeId`
                WHERE
                    `ValuablesGrades`.`ScenarioId`="'.addslashes($_GET['id']).'"
                    AND `ValuablesGrades`.`ValuableId`="'.$value['ValuableId'].'"
            ;');
            if (count($row)==0) {
                // Сиквенции нет в базе, необходимо сформировать
                do_sql('
                    INSERT INTO `cashmaster`.`ValuablesGrades`
                    (
                        `ScenarioId`,
                        `ValuableId`,
                        `GradeId`
                    )
                    VALUES
                    (
                        "'.addslashes($_GET['id']).'",
                        "'.$value['ValuableId'].'",
                        0
                    );
                ;');
                $selected_grade="0";
            } else {
                $selected_grade=$row['GradeId'];
            };
            $valuable_grades[]=array(
                'CategoryName'=>$value['CategoryName'],
                'Sorter'=>$value['SorterType'],
                'Currency'=>$value['CurrSymbol'],
                'Denom'=>$value['DenomLabel'],
                'Grade'=>$row['Label'],
                'Type'=>$value['ValuableTypeLabel'],
                'TypeId'=>$value['ValuableTypeId'],
                $_GET['id'],
                'ValuableId'=>$value['ValuableId'],
                'GradeId'=>$selected_grade
            );
        };
    };
    
    
    
    $h1 = explode('|', $_SESSION[$program]['lang']['wizard_valuable_grades_header']);
    
?>
        <script>
            function update(elem){
                var scenario="<?php echo htmlfix($_GET['id']); ?>";
                var valuable=$(elem.parentNode).find('input')[0].value;
                var grade=$(elem.parentNode).find('select')[0].value;
                var oldgrade=$($(elem.parentNode).find('select')[0]).attr('oldvalue');
                // Обновляем сиквенцию валуабле_грейда с помощью AJAX
                $.ajax({
                     type: "POST",
                     url: "?c=scen_wizard&id=" + scenario,
                     async: false,
                     data: {
                         action: 'update_sequense',
                         valuable: valuable,
                         grade: grade,
                         oldgrade: oldgrade,
                         step: 4
                     },
                     error: function() {
                         alert("Connection error, Can't update.");
                         remove_wait();
                     },
                     success: function(answer){
                         switch(answer[0]){
                             case "0":
                                 // обновление успешное
                                 // Ничего не отображаем
                                 $($(elem.parentNode).find('select')[0]).attr('oldvalue',$(elem).val());
                                 break;
                             default:
                                 alert(answer);
                                 $(elem).val($($(elem.parentNode).find('select')[0]).attr('oldvalue'));
                         };
                         remove_wait();
                     }
                 });        
            };
        </script>
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
                width: 800px;
            }
            table.used {
                width: 800px;
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
            <h3 style="margin: 0px;color:lightgray;">4. <?php echo htmlfix($h[7]); ?></h3>
            <h3 style="margin: 0px;">5. <?php echo htmlfix($h[8]); ?>:</h3>
            <table class="available both">
                <tr>
                    <th><?php echo htmlfix($h1[0]); ?></th>
                    <th><?php echo htmlfix($h1[1]); ?></th>
                    <th><?php echo htmlfix($h1[2]); ?></th>
                    <th><?php echo htmlfix($h1[3]); ?></th>
                    <th><?php echo htmlfix($h1[4]); ?></th>
                    <th><?php echo htmlfix($h1[5]); ?></th>
                </tr>
                <?php
                    foreach ($valuable_grades as $value) {
                        ?>
                            <tr>
                                <td>
                                    <?php echo htmlfix($value['CategoryName']); ?>
                                </td>
                                <td align="center">
                                    <?php echo htmlfix($value['Sorter']); ?>
                                </td>
                                <td align="center">
                                    <?php echo htmlfix($value['Currency']); ?>
                                </td>
                                <td align="right">
                                    <?php echo htmlfix($value['Denom']); ?>
                                </td>
                                <td align="center">
                                    <?php echo htmlfix($value['Type']); ?>
                                </td>
                                <td align="center" style="width:200px;">
                                    <input type="hidden" name="ValuableId" value="<?php echo $value['ValuableId']; ?>"/>
                                    <?php 
                                        $select_grade = '0';
                                        $select_gradelabel = '-';
                                        foreach ($sorter_grades as $val) {
                                            if ($val['GradeId']==$value['GradeId']
                                                    AND $val['ValuableTypeId']==$value['TypeId']) {
                                                $select_grade = $val['GradeId'];
                                                $select_gradelabel = $val['GradeLabel'];
                                            };
                                        };
                                    ?>
                                    <select
                                        class="update"
                                        name="GradeId"
                                        onchange="update(this);"
                                        style="width:200px;margin: 0px;"
                                        oldvalue="<?php echo $select_grade; ?>">
                                    <?php
                                        echo '<option selected value="',$select_grade,'">',$select_gradelabel,'</option>';
                                        foreach ($sorter_grades as $val) {
                                            if ($val['GradeId']==$select_grade 
                                                    AND $val['ValuableTypeId']==$value['TypeId']) {

                                            } else {
                                                if($val['ValuableTypeId']==$value['TypeId']) {
                                                    echo '<option value="',$val['GradeId'],'">',$val['GradeLabel'],'</option>';
                                                };                                                
                                            };
                                        };
                                        if ( $select_grade=='0') {
                                        } else {
                                            echo '<option value="0">-</option>';
                                        };
                                    ?>
                                    </select>
                                </td>
                            </tr>
                        <?php
                    };
                ?>
            </table>
            <hr/>
            <form method="POST">
                <input id="step" type="hidden" name="step" value="5"/>
                <input 
                    type="submit" 
                    class="btn btn-primary btn-large" 
                    onclick="set_wait();$('input#step').val('3');"
                    value="<?php echo htmlfix($_SESSION[$program]['lang']['previous_step']); ?>"/>
                <input 
                    type="submit" 
                    class="btn btn-warning btn-large" 
                    onclick="set_wait();"
                    value="<?php echo htmlfix($_SESSION[$program]['lang']['finish']); ?>"
                />
            </form>
        </div>
    <?php
    exit;
    
?>
