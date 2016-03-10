<?php

/*
 * Проверяет соответствие конфигурации сценария с текущим Valuable Grades
 */
        if (!isset($c)) exit;
        
        
        $sqls = array(
            array(  // проверяем все ли типы прогонялись через волшебника
                'SELECT
                    *
                 FROM 
                    `cashmaster`.`ValuableTypes`;',
                'SELECT
                    *
                 FROM 
                    `cashmaster`.`ScenValuableTypes`
                 WHERE
                    `ScenValuableTypes`.`ScenarioId`="'.addslashes($scen_id).'"
                ;'),
            array(  // проверяем все ли деномы прогонялись через волшебника
                '   SELECT
                        *
                    FROM `cashmaster`.`Denoms`;',
                '   SELECT
                        *
                    FROM 
                        `cashmaster`.`ScenDenoms`
                    WHERE
                        `ScenDenoms`.`ScenarioId`="'.addslashes($scen_id).'"
                    GROUP BY 
                        ScenDenoms.DenomId;'
            ),
            array(  // проверяем все ли сортер грейды прогонялись через волшебника
                '   SELECT
                        *
                    FROM `cashmaster`.`Grades`;',
                '   SELECT
                        *
                    FROM 
                        `cashmaster`.`ScenSorterGrades`
                    WHERE
                        `ScenarioId`="'.addslashes($scen_id).'"
                    GROUP BY 
                        ScenSorterGrades.GradeId;'
            ),
            array(  // проверяем все ли рекон грейды прогонялись через волшебника
                '   SELECT
                        *
                    FROM `cashmaster`.`Grades`;',
                '   SELECT
                        *
                    FROM 
                        `cashmaster`.`ScenReconGrades`
                    WHERE
                        ScenReconGrades.ScenarioId="'.addslashes($scen_id).'"
                    GROUP BY 
                        ScenReconGrades.GradeId;'
            ),
            array(  // проверяем все ли валуаблы прогонялись через волшебника
                '   
                    SELECT
                        *
                    FROM 
                        `cashmaster`.`Valuables`
                    LEFT JOIN
                        ScenDenoms ON `ScenDenoms`.`DenomId`=`Valuables`.`DenomId` 
                                      AND `ScenDenoms`.`ValuableTypeId`=`Valuables`.`ValuableTypeId` 
                    WHERE
                        ScenDenoms.ScenarioId="'.addslashes($scen_id).'"
                        AND ScenDenoms.IsUsed=1
                ;',
                '   SELECT
                        *
                    FROM 
                        `cashmaster`.`ValuablesGrades`
                    WHERE
                        ValuablesGrades.ScenarioId="'.addslashes($scen_id).'"
                    GROUP BY 
                        `ValuablesGrades`.`ValuableId`
                ;'
            )
        );        
        
        foreach ($sqls as $key => $value) {
            // Проверяем количество типа ценностей в системе
            $system = count_rows_from_sql($value[0]);
            //Проверяем количество типа ценностей в сценарии
            $scen = count_rows_from_sql($value[1]);        
            if ( $system != $scen) { 
                $flag=false;
                $flag2=false;// FLAG 
                break;
            };
        };
        

?>
