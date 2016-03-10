<?php

    /*
     * Отчёт, формируемый в виде XML-файла
    */
    if (!isset($c)) exit;

    $r_report_label = 'C'; // Report label is used for selecting scenarios
    switch ($action_name) {
        case 'data_prepare':
            // Эта часть выполняется во время запроса параметров для генерации отчетов

            break;
        case 'data_report':

                // Определениа массива с индексами DepositRecs, участвующих в формировании отчёта
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
            // Эта часть выполняется при генерации отчета на экран
         $r_site = fetch_assoc_row_from_sql('
            SELECT 
                OKATOCode, 
                CashCenterCode,
                CashCenterName, 
                ComplexName,
                KPCode
            FROM SystemGlobals 
            WHERE SystemGlobalsId = "1"
            ;');            
        $row = fetch_assoc_row_from_sql('
            SELECT CashRoomName FROM CashRooms 
            WHERE Id = "'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
            ;');            
            $r_cash_room = $row['CashRoomName'];
                // Define the start date and time for the reported period
                $row = fetch_row_from_sql('
                        SELECT MAX(SetDateTime) FROM ReportSets;');
                if (count($row) == 0) {
                        $r_start_datetime = '1972-11-29T00:00:00';
                } else {
                        $r_start_datetime = str_replace(' ', 'T', $row[0]);
                };
           //$report_date = date("Y.m.d\TH:i:s");
           $report_date = str_replace(' ', 'T', $report_datetime);
			// Define machine indexes that were engaged in operation for the period
			$rows = get_array_from_sql('
				SELECT 
                                    DISTINCT DepositRuns.MachineDBId
				FROM 
                                    DepositRecs 
				INNER JOIN DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
				INNER JOIN Machines ON Machines.MachineDBId = DepositRuns.MachineDBId
				WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
				ORDER BY SorterName;');
			$r_m_ids = array();
			foreach ($rows as $row) {
				$r_m_ids[] = $row[0];				
			};
                        $xml_body = '<?xml version="1.0" encoding="utf-8" ?>
                            <AggregateComplexReport xmlns:ps="urn:ps-au-hc:v1.0" xmlns="urn:cbr-ru:deko:v1.0">
                            <Version>1.0</Version>
                            <ReportID>1</ReportID>
                            <RequestID>0</RequestID>
                            <ReportCode>10</ReportCode>
                            <ReportName>0</ReportName>
                            <BeginDate>'.$r_start_datetime.'</BeginDate>
                            <EndDate>'.$report_date.'</EndDate>
                            <OKATOCode>'.$r_site["OKATOCode"].'</OKATOCode>
                            <Department>
                            <Name>'.$r_site["CashCenterName"].'</Name>
                            <BIC>'.$r_site["CashCenterCode"].'</BIC>
                            <KPCode>'.$r_site["KPCode"].'</KPCode>
                            </Department>
                            <ReportDate>'.$report_date.'</ReportDate>';
                $r_signers = get_report_signers();
                if (count($r_signers)>0) {
                    foreach ($r_signers as $signer) {
                        $xml_body = $xml_body.'
                        <Signatures>
                            <EmployeeName>'.$signer[1].'</EmployeeName>
                            <EmployeePost>'.$signer[0].'</EmployeePost>
                            <EmployeePhone>'.$signer[2].'</EmployeePhone>
                        </Signatures>';
                    };
                };
                        $xml_body = $xml_body.'
                            <Executor>
                            <EmployeeName>'.get_short_fio_by_user_id($_SESSION[$program]['UserConfiguration']['UserId']).'</EmployeeName>
                            <EmployeePost>'.$_SESSION[$program]['UserConfiguration']['UserPost'].'</EmployeePost>
                            <EmployeePhone>'.$_SESSION[$program]['UserConfiguration']['Phone'].'</EmployeePhone>
                            </Executor>
                            <ReportBody>
                            <ps:aggregateComplexInfo>
                            <ps:complexName>'.$r_site["ComplexName"].'</ps:complexName>';
			// For each machine get its specifications
			foreach ($r_m_ids as $r_m_id) {
                            $r_machine = fetch_assoc_row_from_sql('
                                SELECT 
                                    SorterName, 
                                    COALESCE(SerialNumber, "Б/Н") as SerialNumber,
                                    SorterType,
                                    SorterVariant,
                                    Softwarerelease
                                FROM Machines
                                    INNER JOIN SorterTypes ON SorterTypes.SorterTypeId = Machines.SorterTypeId
                                WHERE MachineDBId = '.$r_m_id.'
                                ;');
                            $xml_body = $xml_body.'
                                <ps:machine>
                                <ps:serialnumber>'.$r_machine['SerialNumber'].'</ps:serialnumber>
                                <ps:softwarerelease>'.$r_machine['Softwarerelease'].'</ps:softwarerelease>
                                <ps:type>'.$r_machine['SorterType'].'</ps:type>
                                <ps:variant>'.$r_machine['SorterVariant'].'</ps:variant>
                                <ps:name>'.$r_machine['SorterName'].'</ps:name>
                            ';        
				// Define the list of shifts run by the machine
				$rows = get_array_from_sql('
					SELECT DISTINCT ShiftId
					FROM DepositRuns
						INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
					WHERE MachineDBId = '.$r_m_id.'
						AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).');');
				$r_shifts = array();
				foreach ($rows as $row) {
					$r_shifts[] = $row[0];				
				};
				foreach ($r_shifts as $r_shift) {
                                    // Find the start and the end of each shift for a machine
                                    $row = fetch_row_from_sql('
                                        SELECT MIN(DepositStartTimeStamp), MAX(DepositEndTimeStamp)
                                        FROM DepositRuns
                                            INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
                                        WHERE MachineDBId = '.$r_m_id.'
                                        AND ShiftId = '.$r_shift.'
                                        AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
                                        ;');
                                    $r_shift_start = str_replace(' ', 'T', $row[0]);
                                    $r_shift_end = str_replace(' ', 'T', $row[1]);
                                    $xml_body = $xml_body.'
                                        <ps:shift>
                                        <ps:number>'.$r_shift.'</ps:number>
                                        <ps:name>'.$r_shift.'</ps:name>
                                        <ps:startDate>'.$r_shift_start.'</ps:startDate>
                                        <ps:endDate>'.$r_shift_end.'</ps:endDate>
                                        ';
                                    $rows = get_array_from_sql('
                                            SELECT DISTINCT SortModeName
                                            FROM DepositRuns
                                                    INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
                                            WHERE MachineDBId = '.$r_m_id.'
                                            AND ShiftId = '.$r_shift.'
                                            AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
                                            ;');
					$r_processes = array();
					foreach ($rows as $row) {
					$r_processes[] = $row[0];				
					};
					foreach ($r_processes as $r_process) {
                                            $xml_body = $xml_body.'<ps:processingInfo>
                                                <ps:sortingmode>'.$r_process.'</ps:sortingmode>';
						$rows = get_array_from_sql('
							SELECT DISTINCT DepositIndexId
							FROM DepositRuns
								INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
							WHERE MachineDBId = '.$r_m_id.'
							AND ShiftId = '.$r_shift.'
							AND SortModeName = "'.$r_process.'"
							AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
							;');
						$r_indexes = array();
						foreach ($rows as $row) {
						$r_indexes[] = $row[0];				
						};
						foreach ($r_indexes as $r_index) {
							$row = fetch_row_from_sql('
								SELECT IndexValue, IndexLabel
								FROM DepositIndex
								WHERE DepositIndexId = '.$r_index.'
								;');
							$r_index_value = $row[0]; // Номер индекса для XML-файла
							$r_index_label = $row[1];  // Качество исходных банкнот для XML-файла
							$rows = get_array_from_sql('
								SELECT DISTINCT DenomId
								FROM SorterAccountingData
									INNER JOIN DepositRuns
									ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
									INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
									INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
								WHERE MachineDBId = '.$r_m_id.'
								AND ShiftId = '.$r_shift.'
								AND SortModeName = "'.$r_process.'"
								AND DepositIndexId = '.$r_index.'
								AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
								ORDER BY DenomId
								;');
							$r_denoms = array();
							foreach ($rows as $row) {
							$r_denoms[] = $row[0];				
							};
							foreach ($r_denoms as $r_denom) {
								$row = fetch_row_from_sql('
									SELECT Value, IFNULL(SUM(ActualCount),0)
									FROM SorterAccountingData
										INNER JOIN DepositRuns
										ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
										INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
										INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
										INNER JOIN Denoms ON Valuables.DenomId = Denoms.DenomId
										INNER JOIN ValuablesGrades ON ValuablesGrades.ScenarioId = DepositRecs.ScenarioId
											AND ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
										INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
									WHERE MachineDBId = '.$r_m_id.'
									AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
									AND ShiftId = '.$r_shift.'
									AND SortModeName = "'.$r_process.'"
									AND DepositIndexId = '.$r_index.'
									AND Valuables.DenomId = '.$r_denom.'
									AND GradeName = "FIT"
									GROUP BY Denoms.DenomId
									;');
								$r_denom_value = $row[0];
								$r_fit_count = $row[1];
								$row = fetch_row_from_sql('
									SELECT IFNULL(SUM(ActualCount),0)
									FROM SorterAccountingData
										INNER JOIN DepositRuns
										ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
										INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
										INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
										INNER JOIN ValuablesGrades ON ValuablesGrades.ScenarioId = DepositRecs.ScenarioId
											AND ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
										INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
									WHERE MachineDBId = '.$r_m_id.'
									AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
									AND ShiftId = '.$r_shift.'
									AND SortModeName = "'.$r_process.'"
									AND DepositIndexId = '.$r_index.'
									AND Valuables.DenomId = '.$r_denom.'
									AND GradeName = "UNFIT"
									;');
								$r_unfit_count = $row[0];
								$row = fetch_row_from_sql('
									SELECT IFNULL(SUM(ActualCount),0)
									FROM SorterAccountingData
										INNER JOIN DepositRuns
										ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
										INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
										INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
										INNER JOIN ValuablesGrades ON ValuablesGrades.ScenarioId = DepositRecs.ScenarioId
											AND ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
										INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
									WHERE MachineDBId = '.$r_m_id.'
									AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
									AND ShiftId = '.$r_shift.'
									AND SortModeName = "'.$r_process.'"
									AND DepositIndexId = '.$r_index.'
									AND Valuables.DenomId = '.$r_denom.'
									AND GradeName = "SHRED"
									;');
								$r_shred_count = $row[0];
								$xml_body = $xml_body.'
                                                                <ps:counter>    
								<ps:index>'.$r_index_value.'</ps:index>
								<ps:Sortlim/>
								<ps:quality>'.$r_index_label.'</ps:quality>
								<ps:denomid>'.$r_denom_value.'</ps:denomid>
								<ps:good>'.$r_fit_count.'</ps:good>
								<ps:shabby>'.$r_unfit_count.'</ps:shabby>
								<ps:destroyed>'.$r_shred_count.'</ps:destroyed>
								<ps:doubtful>0</ps:doubtful>
                                                                </ps:counter>
								';
 							}; // End of denom rollup
							$r_rejects = get_assoc_array_from_sql('
								SELECT RejectMappingId, SUM(CullCount) as RejectCount
								FROM Rejects
									INNER JOIN SorterRejectData ON Rejects.RejectId = SorterRejectData.RejectId
									INNER JOIN DepositRuns On SorterRejectData.DepositRunId = DepositRuns.DepositRunId
									INNER JOIN DepositRecs On DepositRuns.DepositRecId = DepositRecs.DepositRecId
									WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
									AND MachineDBId = '.$r_m_id.'
									AND ShiftId = '.$r_shift.'
									AND SortModeName = "'.$r_process.'"
									AND DepositIndexId = '.$r_index.'
								GROUP BY RejectMappingId
								;');
                                                if (count($r_rejects) > 0) {
                                                    foreach ($r_rejects as $r_reject) {
                                                        $xml_body = $xml_body.'
                                                        <ps:reject>
                                                            <ps:pid>'.$r_reject['RejectMappingId'].'</ps:pid>
                                                            <ps:count>'.$r_reject['RejectCount'].'</ps:count>
                                                        </ps:reject>';
                                                    };
                                                }; 
                                        };	// End of indexes rollap
                                $xml_body = $xml_body.'
                                   </ps:processingInfo>';
                            }; // End of processes rollup
                            $xml_body = $xml_body.'
                                </ps:shift>';
                        };  // End of shifts rollup
                        $xml_body = $xml_body.'
                            </ps:machine>';
                    };	// End of machines rollup
                    $xml_body = $xml_body.'
                        </ps:aggregateComplexInfo>
                        </ReportBody>
                        </AggregateComplexReport>';
                $date_year = date("y");
                $date_month = date("m");
                $date_day = date("d");
                $file_name = 'C'.$date_day.$date_month.$date_year.substr($r_site["CashCenterCode"], 2, 4).'.xml';
                add_xml_file_to_reportset($xml_body, $file_name);
                
            break;
    };
?>