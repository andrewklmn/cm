<?php

    /*
     * Script that creates RECRESULT XML (a separate file for each deposit)
     * Good and simple deposit by deposit rollup
     */
    if (!isset($c)) exit;
	$r_report_label = 'RECRESULT (expanded mode)'; // Report label is used for selecting scenarios
    
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
			// Define the global set of deposits limiting to only those that has been processed with scenarios switched ON for the report
			$r_deposits = array();
			foreach ($rows as $row) {
				$r_deposits[] = $row[0];				
			};
			$file_count = 0; // Initializing file counter
			foreach ($r_deposits as $r_deposit) {
				// Get machine name (if a deposit was processed by more than one machine, the latest that finished is selected)
				$row = fetch_row_from_sql('
					SELECT DISTINCT MachineDBId
					FROM DepositRuns as b1
					WHERE DepositRecId = "'.addslashes($r_deposit).'"
						AND NOT EXISTS (
							SELECT * FROM DepositRuns as b2
							WHERE b1.DepositRunId <> b2.DepositRunId
							AND b1.DepositRecId = b2.DepositRecId
							AND b1.MachineDBId <> b2.MachineDBId
							AND b1.DepositEndTimeStamp < b2.DepositEndTimeStamp)
					;');
				$r_machine_id = $row[0];
				$row = fetch_row_from_sql('
					SELECT SorterName
					FROM Machines
					WHERE MachineDBId = '.$r_machine_id.'
					;');
				$csmnumber = $row[0];
                                        // Get deposit index
                                $row = fetch_row_from_sql('
                                    SELECT IndexValue 
                                    FROM DepositIndex 
                                        INNER JOIN DepositRecs ON DepositRecs.DepositIndexId = DepositIndex.DepositIndexId
                                    WHERE DepositRecId = "'.addslashes($r_deposit).'";');
                                $currindex = $row[0];
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
					// Get the expected count and denom
				$row = fetch_row_from_sql('
					SELECT DepositDenomTotal.DenomId, Value, CurrYear, SUM(ExpectedCount)
					FROM DepositDenomTotal
						INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = DepositDenomTotal.ValuableTypeId
						INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
						INNER JOIN Currency ON Currency.CurrencyId = Denoms.CurrencyId
					WHERE DepositReclId = '.$r_deposit.'
						AND ValuableTypeName = "banknotes"
						AND ExpectedCount > 0
					GROUP BY DepositDenomTotal.DenomId
					;');
				$r_denom = $row[0];
                                $currnominal = $row[1];
				$curryear = $row[2];
				$plannedbanknotescount = $row[3];
					// Get the current date and time in two formats
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
					// Get number of UNFIT notes
				$row = fetch_row_from_sql('
					SELECT SUM(ActualCount)
					FROM SorterAccountingData
						INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
						INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
						INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
						INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
						INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
							AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
						INNER JOIN Grades ON Grades.GradeId = ValuablesGrades.GradeId
					WHERE DepositRuns.DepositRecId = "'.addslashes($r_deposit).'"
						AND Valuables.DenomId = "'.addslashes($r_denom).'"
						AND Grades.GradeName = "UNFIT"
						AND ValuableTypeName = "banknotes"
					;');
				$oldbanknotescount = $row[0];
				$row = fetch_row_from_sql('
					SELECT SUM(CullCount)
					FROM ReconAccountingData
						INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
						INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = ReconAccountingData.ValuableTypeId
					WHERE DepositRecId = "'.addslashes($r_deposit).'"
						AND DenomId = "'.addslashes($r_denom).'"
						AND GradeName = "UNFIT"
						AND ValuableTypeName = "banknotes"
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
						INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
						INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
							AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
						INNER JOIN Grades ON Grades.GradeId = ValuablesGrades.GradeId
					WHERE DepositRuns.DepositRecId = "'.addslashes($r_deposit).'"
						AND Valuables.DenomId = "'.addslashes($r_denom).'"
						AND Grades.GradeName = "SHRED"
						AND ValuableTypeName = "banknotes"
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
						INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
						INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
							AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
						INNER JOIN Grades ON Grades.GradeId = ValuablesGrades.GradeId
					WHERE DepositRuns.DepositRecId = "'.addslashes($r_deposit).'"
						AND Valuables.DenomId = "'.addslashes($r_denom).'"
						AND Grades.GradeName = "FIT"
						AND ValuableTypeName = "banknotes"
					;');
				$fitcount = $row[0];
				$row = fetch_row_from_sql('
					SELECT SUM(CullCount)
					FROM ReconAccountingData
						INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
						INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = ReconAccountingData.ValuableTypeId
					WHERE DepositRecId = "'.addslashes($r_deposit).'"
						AND DenomId = "'.addslashes($r_denom).'"
						AND GradeName = "FIT"
						AND ValuableTypeName = "banknotes"
					;');
				$fitcount = $fitcount + $row[0];	
					// Get number of SUSPECT notes
				$row = fetch_row_from_sql('
					SELECT COALESCE(SUM(CullCount), 0)
					FROM ReconAccountingData
						INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
						INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = ReconAccountingData.ValuableTypeId
					WHERE DepositRecId = "'.addslashes($r_deposit).'"
						AND DenomId = "'.addslashes($r_denom).'"
						AND GradeName = "SUSPECT"
						AND ValuableTypeName = "banknotes"
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
				$xml_body = $xml_body .'
					</RECRESULT>';
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
				add_xml_file_to_reportset($xml_body, 'RECRESULT_'.$r_xml_datetime.'_'.$r_file_no.'.xml');
			}; // End of deposits rollup
            break;
    };
    
?>

