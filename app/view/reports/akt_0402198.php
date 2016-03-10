<?php

    /*
     * Скрипт формирования XML-файла AKT0402198
     */
    if (!isset($c)) exit;
    
    $r_report_label = 'AKT0402198'; // Report label is used for selecting scenarios
    switch ($action_name) {
        case 'data_prepare':
            // Parameter section

            break;
        case 'data_report':
            // Report generate section
            $row = fetch_assoc_row_from_sql('
                SELECT  
                    CashRoomName 
                FROM 
                    CashRooms 
                WHERE 
                    Id = "'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                    ;');            
            $cash_room = $row['CashRoomName'];
            $today = getdate();
            if ($today['yday'] < 9) {
                    $yday = '00'.($today['yday'] + 1);
            } elseif ($today['yday'] < 99) {
                    $yday = '0'.($today['yday'] + 1);
            } else {
                    $yday = $today['yday'] + 1;
            };
            $report_date = str_replace(' ', 'T', $report_datetime);
            $r_protocol_date = substr($report_datetime, 0, 10);
            // Define the array of DepositRecs, that must be reported
            $recsid = array();
            foreach ($recs as $rec) {
                $recsid[] = $rec[0];				
            };
                // Define the scenarios for which this report is ON at the time
            $rows = get_array_from_sql('
                SELECT DISTINCT 
                    ScenarioId
                FROM ScenReportTypes
                    INNER JOIN ReportTypes ON ScenReportTypes.ReportTypeId = ReportTypes.ReportTypeId
                WHERE ReportLabel="'.addslashes($r_report_label).'"
                AND IsUsed=1;');
            $r_scenarios = array();
            foreach ($rows as $row) {
                $r_scenarios[] = $row[0];
            };
            $rows = get_array_from_sql('
                SELECT 
                    DepositRecId
                FROM DepositRecs
                WHERE DepositRecId IN ('.implode(',',$recsid).')
                    AND ScenarioId IN ('.implode(',',$r_scenarios).')
                ;');
            // Redefine the global set of deposits limiting to only those that has been processed with scenarios switched ON for the report
            unset($recsid);				
            $recsid = array();
            foreach ($rows as $row) {
                $recsid[] = $row[0];				
            };
            // Define the list of indexes for the report
            $rows = get_array_from_sql('
                SELECT DISTINCT DepositIndexId
                FROM DepositRecs
                    INNER JOIN DepositRuns ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
                    INNER JOIN SorterAccountingData ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
                    INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
                        AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
                    INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
                WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
                    AND Grades.GradeName = "SHRED"
                ;');
            $r_indexes = array();
            foreach ($rows as $row) {
                $r_indexes[] = $row[0];
            };
            $r_act_no = 0;
            foreach ($r_indexes as $r_index) {
                $r_act_no = $r_act_no + 1;
                if ($r_act_no <10) {
                    $r_act_num = '000'.$r_act_no;
                } else if ($r_act_no <100) {
                    $r_act_num = '00'.$r_act_no;
                } else if ($r_act_no <1000) {
                    $r_act_num = '0'.$r_act_no;
                } else {
                    $r_act_num = $r_act_no;
                }
                // Define the index value
                $row = fetch_row_from_sql('
                    SELECT IndexValue
                    FROM DepositIndex
                    WHERE DepositIndexId = '.$r_index.'
                    ;');
                $r_index_value = $row[0];
                $r_xml_body = '<?xml version="1.0" encoding="UTF-8"?>
                ';
                $r_xml_body = $r_xml_body.'<AKT0402198 xmlns="urn:cbr-ru:csm:v1.0" CreateTimeFile="'.$report_date.'" CurrencyIndex="'.$r_index_value.'">
                ';
                    // Define machines that were engaged in operation for the period
                $rows = get_array_from_sql('
                SELECT DISTINCT 
                    MachineDBId
                FROM DepositRecs
                    INNER JOIN DepositRuns ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
                    INNER JOIN SorterAccountingData ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
                    INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
                        AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
                    INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
                WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
                    AND Grades.GradeName = "SHRED"
                    AND DepositIndexId = '.$r_index.';');
                $r_machines = array();
                foreach ($rows as $row) {
                    $r_machines[] = $row[0];				
                };
                 foreach ($r_machines as $r_machine) {
                    $row = fetch_row_from_sql('
                        SELECT SorterName FROM Machines WHERE MachineDBId = '.$r_machine.';');
                    $sorter_name = $row[0];
                    $r_xml_body = $r_xml_body.'<CSMProtocol CSMNumber="'.$sorter_name.'" ProtocolNumber="'.$sorter_name.$yday.'" ProtocolDate="'.$r_protocol_date.'">
                    ';
                    $rows = get_assoc_array_from_sql('
                        SELECT 
                            Denoms.Value as value,
                            SUM(ActualCount) as shredcount
                        FROM SorterAccountingData
                            INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
                            INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
                            INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
                            INNER JOIN Denoms ON Denoms.DenomId = Valuables.DenomId
                            INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
                                AND ValuablesGrades.ScenarioId = DepositRecs.ScenarioId
                            INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
                        WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
                            AND DepositIndexId = '.$r_index.'
                            AND MachineDBId = '.$r_machine.'
                            AND Grades.GradeName = "SHRED"
                        GROUP BY Denoms.DenomId
                        HAVING SUM(ActualCount) > 0
                        ORDER BY Denoms.Value ASC 
                        ;');
                    foreach ($rows as $row) {
                        $r_denom = $row['value'];
                        $r_count = $row['shredcount'];
                        $r_xml_body = $r_xml_body.'<DestroyBanknotes Nominal="'.$r_denom.'" Count="'.$r_count.'" />
                        ';
                    };
                    $r_xml_body = $r_xml_body.'</CSMProtocol>';
                }; // End of machines rollup
                $r_xml_body = $r_xml_body.'</AKT0402198>';
                $r_filename = 'AKT0402198_'.$report_date.'_'.$r_act_num.'.xml';
                add_xml_file_to_reportset($r_xml_body, $r_filename); // Creating each separate file
            }; // End of indexes rollup
     break;
    };
?>

