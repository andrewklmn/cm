<?php

/*
 * Scenarion configuration page
 */

        if (!isset($c)) exit;
        

        $data['title'] = 'Scenario Configurator';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';

        include 'app/controller/select_scenario.php';

?>
<div class="container">
    <?php 
    
        $sql = '
            SELECT
                `Scenario`.`ScenarioId`,
                `Scenario`.`ScenarioName`
            FROM 
                `cashmaster`.`Scenario`
            WHERE
            `Scenario`.`ScenarioId`="'.  addslashes($_SESSION[$program]['scenario'][0]).'"
        ;';
        $table['fields'] = fetch_fields_info_from_sql($sql);
        $table['headers'] = $table['fields'];
        $table['widths'] = array( 40,600 );
        $table['data'] = get_assoc_array_from_sql($sql);
        $table['form_action'] = 'scenario_props_update';
        //$table['hide_key'] = true;
        $table['title'] = 'Scenario Properties';
        include './app/view/draw_editable_table.php';
        echo '<br/>';
        
        $sql = '
            SELECT
                `Scenario`.`ScenarioId`,
                `Scenario`.`BlindReconciliation`,
                `Scenario`.`UsePackIntegrity`,
                `Scenario`.`UseSealNumber`,
                `Scenario`.`UseSealType`,
                `Scenario`.`SingleDenomDeposits`,
                `Scenario`.`ReconcileAgainstValue`,
                `Scenario`.`DefaultScenario`,
                `Scenario`.`LogicallyDeleted`
            FROM 
                `cashmaster`.`Scenario`
            WHERE
            `Scenario`.`ScenarioId`="'.  addslashes($_SESSION[$program]['scenario'][0]).'"
        ;';
        $table['fields'] = fetch_fields_info_from_sql($sql);
        $table['headers'] = $table['fields'];
        //$table['widths'] = array( 40,600 );
        $table['data'] = get_assoc_array_from_sql($sql);
        $table['form_action'] = 'scenario_options_update';
        $table['title'] = 'Scenario options';
        $table['hide_key'] = true;
        include './app/view/draw_editable_table.php';
        echo '<br/>';
        
    ?>
    <table>
        <tr>
                        <td style="text-align: center;vertical-align: top;padding-right: 20px;"><?php 
                $sql = '
                    SELECT
                        `ValuablesGrades`.`SequenceId`,
                        CategoryName,
                        Grades.GradeName,
                        Denoms.Value,
                        Currency.CurrName,
                        Currency.CurrYear
                    FROM 
                        `cashmaster`.`ValuablesGrades`
                    LEFT JOIN
                        Valuables ON Valuables.ValuableId = `ValuablesGrades`.`ValuableId`
                    LEFT JOIN
                        Denoms ON Denoms.DenomId = Valuables.DenomId
                    LEFT JOIN
                        Grades ON Grades.GradeId = `ValuablesGrades`.`GradeId`
                    LEFT JOIN
                        Currency ON Denoms.CurrencyId = Currency.CurrencyId
                    WHERE
                        `ValuablesGrades`.`ScenarioId`="'.  addslashes($_SESSION[$program]['scenario'][0]).'"
                ;';
                $table['data'] = get_array_from_sql($sql);
                $table['header'] = explode('|', 'Id|Valuable|Grade|Denom|Name|Year');
                $table['width'] = array( 40,250,60,60,150,60);
                $table['align'] = array( 'center','left','center','right','center','center');
                $table['tr_onclick']=';';
                $table['title'] = 'Valuable Grades';
                include './app/view/draw_select_table.php';
            ?></td>
            <td style="text-align: center;vertical-align: top;padding-right: 20px;"><?php 
                $sql = '
                    SELECT
                        `ScenDenoms`.`Id`,
                        Denoms.Value,
                        Currency.CurrName,
                        Currency.CurrYear
                    FROM 
                        `cashmaster`.`ScenDenoms`
                    LEFT JOIN
                        Denoms ON Denoms.DenomId = ScenDenoms.DenomId
                    LEFT JOIN
                        Currency ON Denoms.CurrencyId = Currency.CurrencyId
                    WHERE
                        `ScenDenoms`.`ScenarioId`="'.  addslashes($_SESSION[$program]['scenario'][0]).'"
                ;';
                $table['data'] = get_array_from_sql($sql);
                $table['header'] = explode('|', 'Id|Denom|Name|Year');
                $table['width'] = array( 40,50,150,50);
                $table['align'] = array( 'center','right','center','center');
                $table['tr_onclick']=';';
                $table['title'] = 'Denoms';
                include './app/view/draw_select_table.php';
                
            ?></td>
            <td style="text-align: center;vertical-align: top;padding-right: 20px;"><?php 
                $sql = '
                    SELECT
                        `ScenSorterGrades`.`Id`,
                        Grades.GradeName,
                        Grades.GradeLabel
                    FROM 
                        `cashmaster`.`ScenSorterGrades`
                    LEFT JOIN
                        Grades ON Grades.GradeId = `ScenSorterGrades`.`GradeId`
                    WHERE
                        `ScenSorterGrades`.`ScenarioId`="'.  addslashes($_SESSION[$program]['scenario'][0]).'"
                ;';
                $table['data'] = get_array_from_sql($sql);
                $table['header'] = explode('|', 'Id|Name|Label');
                $table['width'] = array( 40,60,60);
                $table['align'] = array( 'center','center','center');
                $table['tr_onclick']=';';
                $table['title'] = 'Sorter Grades';
                include './app/view/draw_select_table.php';
            ?>
            </td>
            <td style="text-align: center;vertical-align: top;padding-right: 20px;"><?php 
                $sql = '
                    SELECT
                        `ScenReconGrades`.`Id`,
                        Grades.GradeName,
                        Grades.GradeLabel
                    FROM 
                        `cashmaster`.`ScenReconGrades`
                    LEFT JOIN
                        Grades ON Grades.GradeId = `ScenReconGrades`.`GradeId`
                    WHERE
                        `ScenReconGrades`.`ScenarioId`="'.  addslashes($_SESSION[$program]['scenario'][0]).'"
                ;';
                $table['data'] = get_array_from_sql($sql);
                $table['header'] = explode('|', 'Id|Name|Label');
                $table['width'] = array( 40,60,60);
                $table['align'] = array( 'center','center','center');
                $table['tr_onclick']=';';
                $table['title'] = 'Recon Grades';
                include './app/view/draw_select_table.php';
            ?></td>
        </tr>
    </table>    
</div>
