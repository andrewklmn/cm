<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
        
        if (!isset($c)) exit;
        
        $xmls = array();
        if (count($scen_ids)>0) {
            $xmls = get_assoc_array_from_sql('
                SELECT
                    *
                FROM 
                    `cashmaster`.`ScenReportTypes`
                LEFT JOIN
                    ReportTypes ON  ReportTypes.ReportTypeId=ScenReportTypes.ReportTypeId
                WHERE
                    `ScenReportTypes`.`ScenarioId` IN ( '.  implode(', ', $scen_ids).' )
                    AND IsUsed = 1
                    AND GenerateXmlFile = 1
                GROUP BY `ScenReportTypes`.`ReportTypeId`
            ;');
        };
        
        $reports = array();
        if (count($scen_ids)>0) {
            $reports = get_assoc_array_from_sql('
                SELECT
                    *
                FROM 
                    `cashmaster`.`ScenReportTypes`
                LEFT JOIN
                    ReportTypes ON  ReportTypes.ReportTypeId=ScenReportTypes.ReportTypeId
                WHERE
                    `ScenReportTypes`.`ScenarioId` IN ( '.  implode(', ', $scen_ids).' )
                    AND IsUsed = 1
                    AND GenerateXmlFile = 0
                GROUP BY `ScenReportTypes`.`ReportTypeId`
            ;');
        };
?>
