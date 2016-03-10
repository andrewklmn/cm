<?php

    /*
     * Script that creates RECRESULT XML 
     */
    if (!isset($c)) exit;
	$r_report_label = 'RECRESULT'; // Report label is used for selecting scenarios
    
    switch ($action_name) {
        case 'data_prepare':
        // Parameter entry sectoion if needed
            break;
        case 'data_report':
         // File generating section
				$recsid = array();
				foreach ($recs as $rec) {
					$recsid[] = $rec[0];				
				};
			// Define the scenarios for which this report is ON at the time
			$rows = get_array_from_sql('
				SELECT DISTINCT ScenarioId
				FROM ScenReportTypes
					INNER JOIN ReportTypes ON ScenReportTypes.ReportTypeId = ReportTypes.ReportTypeId
				WHERE ReportLabel="'.addslashes($r_report_label).'"
				AND IsUsed=1;');
			$r_scenarios = array();
			foreach ($rows as $row) {
				$r_scenarios[] = $row[0];
			};
			$rows = get_array_from_sql('
				SELECT DepositRecId
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
			// Define machine indexes that were engaged in operation for the period
			$rows = get_array_from_sql('
				SELECT 
					DISTINCT MachineDBId
				FROM 
					DepositRecs INNER JOIN DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
				WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
				ORDER BY MachineDBId;');
			$r_machines = array();
			foreach ($rows as $row) {
				$r_machines[] = $row[0];				
			};
			$file_count = 0; // Initializing file counter
		// Rollup accounting data per machines, indexes, denoms
			// For each machine engaged define DepositRecs, that will be reported for the machine
			foreach ($r_machines as $r_machine) {
				$rows = get_array_from_sql('
					SELECT DISTINCT a1.DepositRecId
					FROM DepositRuns as a1
					WHERE 
						a1.DepositRecId IN ('.implode(',',$recsid).')
						AND a1.MachineDBId = '.$r_machine.'
						AND a1.DepositRecId IS NOT NULL	
						AND NOT EXISTS
							(SELECT * FROM DepositRuns as a2 
							WHERE 
								a2.DepositRecId IN ('.implode(',',$recsid).')
								AND a2.DepositRecId = a1.DepositRecId
								AND a2.MachineDBId <> '.$r_machine.'
								AND a2.DepositEndTimeStamp > a1.DepositEndTimeStamp);');
				$r_machine_recs = array();
				foreach ($rows as $row) {
					$r_machine_recs[] = $row[0];
				};
				if(count($r_machine_recs) == 0) {
					continue;
				};
				$row = fetch_row_from_sql('
					SELECT SorterName FROM Machines WHERE MachineDBId = '.$r_machine.';');
				$csmnumber = $row[0];

				// Define the indexes that were processed within the perion
				$rows = get_array_from_sql('
					SELECT DISTINCT DepositIndexId
					FROM DepositRecs
					WHERE DepositRecId IN ('.implode(',',$r_machine_recs).');');
				$r_indexes = array();
				foreach ($rows as $row) {
					$r_indexes[] = $row[0];
				};
				foreach ($r_indexes as $r_index) {
						// Get the selected index value
					$row = fetch_row_from_sql('
						SELECT IndexValue FROM DepositIndex WHERE DepositIndexId = '.$r_index.';');
					$currindex = $row[0];
						// Define denoms that were processed within each index
					$rows = get_array_from_sql('
						SELECT DISTINCT DenomId
						FROM DepositDenomTotal 
						INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositDenomTotal.DepositReclId
						WHERE DepositDenomTotal.DepositReclId IN ('.implode(',',$r_machine_recs).')
							AND DepositRecs.DepositIndexId = '.$r_index.'
							AND ExpectedCount > 0
						ORDER BY DenomId;
						;');
					$r_denoms = array();
					foreach ($rows as $row) {
						$r_denoms[] = $row[0];
					};
					foreach ($r_denoms as $r_denom) {
						$row = fetch_assoc_row_from_sql('
							SELECT Value, CurrYear
							FROM Denoms
								INNER JOIN Currency ON Denoms.CurrencyId = Currency.CurrencyId
							WHERE DenomId = '.$r_denom.'
							;');
						$curryear = $row['CurrYear'];
						$currnominal = $row['Value'];
							// Define deposits that have selected index, denom
						$rows = get_array_from_sql('
							SELECT DISTINCT DepositReclId
							FROM DepositDenomTotal
								INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
							WHERE DepositReclId IN ('.implode(',',$r_machine_recs).')
								AND DepositRecs.DepositIndexId = '.$r_index.'
								AND DepositDenomTotal.DenomId = '.$r_denom.'
								AND ExpectedCount > 0
							ORDER BY DepositReclId
							;');
						$r_deposits = array();
						foreach ($rows as $row) {
							$r_deposits[] = $row[0];
						};

						$file_count = $file_count + 1;
						if ($file_count < 10) {
							$r_file_no = '000'.$file_count;
						} elseif ($file_count < 100) {
							$r_file_no = '00'.$file_count;
						} elseif ($file_count < 1000) {
							$r_file_no = '0'.$file_count;
						} else {
							$r_file_no = $file_count;
						};
						$date_year = date("Y");
						$date_month = date("m");
						$date_day = date("d");
						$date_hour = date("H");
						$date_min = date("i");
						$date_sec = date("s");
						$createtimefile = $date_year.'-'.$date_month.'-'.$date_day.'T'.$date_hour.':'.$date_min.':'.$date_sec;
						$r_xml_datetime = $date_year.'-'.$date_month.'-'.$date_day.'T'.$date_hour.'-'.$date_min.'-'.$date_sec;
						$xml_body = '<?xml version="1.0" encoding="UTF-8"?>
						<RECRESULT xmlns="urn:cbr-ru:csm:v1.0" CreateTimeFile="'.$createtimefile.'" CSMNumber="'.$csmnumber.'" 
						CurrIndex="'.$currindex.'" CurrYear="'.$curryear.'" CurrNominal="'.$currnominal.'">';
						
						foreach ($r_deposits as $r_deposit) {
								// Get sorter operator(s) that processed the selected deposit
							$rows = get_array_from_sql('
								SELECT DISTINCT OperatorName
								FROM DepositRuns
								WHERE DepositRecId = "'.addslashes($r_deposit).'"
								;');
							$r_operators = array();
							foreach ($rows as $row) {
								$r_operators[] = $row[0];
							};
							$cashiername = implode(', ',$r_operators);
							$xml_body = $xml_body .'<Cashier CashierName="'.$cashiername.'"/>';
								// Get the expected count
							$row = fetch_row_from_sql('
								SELECT SUM(ExpectedCount)
								FROM DepositDenomTotal
								WHERE DepositReclId = '.$r_deposit.'
								GROUP BY DenomId
								HAVING DenomId = "'.$r_denom.'"
								;');
							$plannedbanknotescount = $row[0];
								// Get number of UNFIT notes
							$row = fetch_row_from_sql('
								SELECT SUM(ActualCount)
								FROM SorterAccountingData
									INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
									INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
									INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
									INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
										AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
									INNER JOIN Grades ON Grades.GradeId = ValuablesGrades.GradeId
								WHERE DepositRuns.DepositRecId = "'.addslashes($r_deposit).'"
									AND Valuables.DenomId = "'.addslashes($r_denom).'"
									AND Grades.GradeName = "UNFIT"
								;');
							$oldbanknotescount = $row[0];
							$row = fetch_row_from_sql('
								SELECT SUM(CullCount)
								FROM ReconAccountingData
									INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
								WHERE DepositRecId = "'.addslashes($r_deposit).'"
									AND DenomId = '.$r_denom.'
									AND GradeName = "UNFIT"
								;');
							$oldbanknotescount = $oldbanknotescount + $row[0];	
							$oldbanknotessum = $oldbanknotescount * $currnominal;
								// Get number of SHRED notes
							$row = fetch_row_from_sql('
								SELECT COALESCE(SUM(ActualCount), 0)
								FROM SorterAccountingData
									INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
									INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
									INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
									INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
										AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
									INNER JOIN Grades ON Grades.GradeId = ValuablesGrades.GradeId
								WHERE DepositRuns.DepositRecId = "'.addslashes($r_deposit).'"
									AND Valuables.DenomId = "'.addslashes($r_denom).'"
									AND Grades.GradeName = "SHRED"
								;');
							$destroybanknotescount = $row[0];
							$destroybanknotessum = $destroybanknotescount * $currnominal;
								// Get number of FIT notes
							$goodbanknotescount = $plannedbanknotescount - $oldbanknotescount - $destroybanknotescount;
							$goodbanknotessum = $goodbanknotescount * $currnominal;
								// Get number of REALLY FIT notes :-)
							$row = fetch_row_from_sql('
								SELECT SUM(ActualCount)
								FROM SorterAccountingData
									INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
									INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
									INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
									INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
										AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
									INNER JOIN Grades ON Grades.GradeId = ValuablesGrades.GradeId
								WHERE DepositRuns.DepositRecId = "'.addslashes($r_deposit).'"
									AND Valuables.DenomId = "'.addslashes($r_denom).'"
									AND Grades.GradeName = "FIT"
								;');
							$fitcount = $row[0];
							$row = fetch_row_from_sql('
								SELECT SUM(CullCount)
								FROM ReconAccountingData
									INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
								WHERE DepositRecId = "'.addslashes($r_deposit).'"
									AND DenomId = "'.addslashes($r_denom).'"
									AND GradeName = "FIT"
								;');
							$fitcount = $fitcount + $row[0];	
								// Get number of SUSPECT notes
							$row = fetch_row_from_sql('
								SELECT COALESCE(SUM(CullCount), 0)
								FROM ReconAccountingData
									INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
								WHERE DepositRecId = "'.addslashes($r_deposit).'"
									AND DenomId = "'.addslashes($r_denom).'"
									AND GradeName = "SUSPECT"
								;');
							$examinationcount = $row[0];	
							$discrepancy_count = $fitcount + $oldbanknotescount + $destroybanknotescount + $examinationcount - $plannedbanknotescount;
							if ($discrepancy_count < 0) {
								$overflowcount = 0;
								$incompletecount = -$discrepancy_count;
							} else if ($discrepancy_count > 0) {
								$overflowcount = $discrepancy_count;
								$incompletecount = 0;
							} else {
								$overflowcount = 0;
								$incompletecount = 0;
							};
							$row = fetch_row_from_sql('
								SELECT 
									RecOperatorId, 
									DATE(RecLastChangeDatetime), 
									PackId
								FROM DepositRecs
								WHERE DepositRecId = "'.addslashes($r_deposit).'"
								;');
							$deposit_operator = get_short_fio_by_user_id($row[0]);
							$resultdate = $row[1];
							$packid = ($row[2]) ? $row[2] : 'Б/Н';
							$xml_body = $xml_body.'
							<BanknotesPack ResultDate="'.$resultdate.'" 
							PlannedBanknotesCount="'.$plannedbanknotescount.'" 
							PlannedBanknotesSum="'.$plannedbanknotescount * $currnominal.'" 
							GoodBanknotesCount="'.$goodbanknotescount.'" 
							GoodBanknotesSum="'.$goodbanknotessum.'" 
							OldBanknotesCount="'.$oldbanknotescount.'" 
							OldBanknotesSum="'.$oldbanknotessum.'" 
							DestroyBanknotesCount="'.$destroybanknotescount.'" 
							DestroyBanknotesSum="'.$destroybanknotessum.'" 
							DoubtfulBanknotesCount="0" 
							DoubtfulBanknotesSum="0" 
							OverflowCount="'.$overflowcount.'" 
							IncomplitCount="'.$incompletecount.'" 
							ExaminationCount="'.$examinationcount.'" 
							PackId="'.$packid.'"/>';
						}; // End of deposits rollup
						$xml_body = $xml_body .'
							</RECRESULT>';
						add_xml_file_to_reportset($xml_body, 'RECRESULT_'.$r_xml_datetime.'_'.$r_file_no.'.xml');
					};	// End of denoms rollup
				}; // End of indexes rollup
			}; // End of machines rollup	
            break;
    };
    
?>

