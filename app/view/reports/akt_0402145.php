<?php

if (!isset($c)) exit;
include_once 'app/model/get_short_iof_by_user_id.php';
$r_report_label = 'AKT0402145'; // Report label is used for selecting scenarios

/*
 *  Параметры определенные перед вызовом отчета XML
 *      $recs - массив содержащий записи из таблицы сверок за искомый период 
 *      $confirm - массив файлов отчетов XML для генерации строк в файле квитанции
 * Скрипт формирования набора XML-файлов актов 0402145 для случая отдельный акт для каждого типа расхождения и каждого номинала (Банк России)
 */

    // 1. Обработка входных данных и получение итогов по ним
    
    // 2. Генерация файла отчета 
        // Начало цикла (по количеству файлов)
            // 2. Генерация текста будущего файла XML  в текстовую переменную
            // 3. Генерация файла отчета средствами PHP
            // 4. Добавляем в массив квитации запись о сгенерированном файле
        // Конец цикла

    switch ($action_name) {
        case 'data_prepare':
                // Эта часть выполняется во время запроса параметров для генерации отчетов
                // Пример задания одиночного параметра
                ?>
                   Номер кладовой: 
                   <input 
                       class='stat' 
                       type='text' 
                       name="<?php echo $value['ReportTypeId']; ?>|kladovaya" 
                       value=""/>
                    
                <?php
                    // Обрати внимание на метод задания имени переменной в инпуте!
                    // Фактически она формируется на лету в виде
                    // 'Id|Name' - где Id - айди типа отчета, Name - имя переменной
                     // Это сделано для того чтобы не пересекались одинаковые названия параметров
                    // в отчетах разного типа в одном РепортСете
            break;
        case 'data_report':
            // The following code is performed when printing the report on the screen
			$date_year = date("Y");
			$date_month = date("m");
			$date_day = date("d");
			$date_hour = date("H");
			$date_min = date("i");
			$date_sec = date("s");
			$createtimefile = $date_year.'-'.$date_month.'-'.$date_day.'T'.$date_hour.':'.$date_min.':'.$date_sec;
			$r_xml_datetime = $date_year.'-'.$date_month.'-'.$date_day.'T'.$date_hour.'-'.$date_min.'-'.$date_sec;
			// Define the scenarios for which this report is ON now
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
			$row = fetch_assoc_row_from_sql('
				SELECT 
					CashCenterName, 
					CashCenterCity,
					CashCenterCode
				FROM SystemGlobals 
					WHERE SystemGlobalsId = "1"
				;');            
            $site_name = $row['CashCenterName'];
            $site_city = $row['CashCenterCity'];
            $site_code = $row['CashCenterCode'];
			$row = fetch_assoc_row_from_sql('
					SELECT CashRoomName FROM CashRooms 
						WHERE Id = "'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
				;');            
            $cash_room = $row['CashRoomName'];
			// Define the Deposits that require act compiling
			$recsid = array();
			foreach ($recs as $rec) {
				$recsid[] = $rec[0];				
			};
			// Get act events for the reporting period
			$events = get_assoc_array_from_sql('
				SELECT ActId, DepositId, DiscrepancyKindName
				FROM Acts
				INNER JOIN DiscrepancyKind ON DiscrepancyKind.DiscrepancyKindId = Acts.DiscrepancyKindId
				INNER JOIN DepositRecs ON DepositRecs.DepositRecId = Acts.DepositId
				WHERE DepositId IN ('.implode(',',$recsid).')
				AND ScenarioId IN ('.implode(',',$r_scenarios).')
				AND IFNULL(DepositRecs.isBalanced, 0) = 0
				ORDER BY DepositId;');
			$act_count = 0;
			foreach ($events as $event) {
				$deposit_id = $event['DepositId'];
				$discrepancy_name = $event['DiscrepancyKindName'];
				$row = fetch_assoc_row_from_sql('
					SELECT 
						COALESCE(CustomerName, "клиент не указан") as CustomerName,
						COALESCE(CustomerCode, "клиент не указан") as CustomerCode,
						RecOperatorId as OperatorId,
						COALESCE(RecSupervisorId, RecOperatorId) as SupervisorId,
						COALESCE(DATE_FORMAT(DepositPackingDate, "%Y-%m-%d"), "не указана") as PackingDate,
						PackType,
						PackIntegrity,
						COALESCE(PackId, "Б/Н") as PackId,
						SealType,
						SealIntegrity,
						COALESCE(SealNumber, "Б/Н") as SealNumber,
						StrapsIntegrity,
						StrapType,
						COALESCE(PackingOperatorName, "не указан") as PackingOperatorName
					FROM DepositRecs
					LEFT JOIN Customers ON DepositRecs.CustomerId = Customers.CustomerId
					WHERE DepositRecId = "'.addslashes($deposit_id).'"
					;');
				$clientname = $row['CustomerName'];
				$bic = $row['CustomerCode'];
				$cashiername  = get_short_fio_by_user_id($row['OperatorId']);
				$presencesignature = get_short_fio_by_user_id($row['SupervisorId']);
                                $presencename = $presencesignature;
				$packing_date = $row['PackingDate'];
				$packername = $row['PackingOperatorName'];
				$packnumber = $row['PackId'];
				$seal_number = $row['SealNumber'];
				$packintegrity = $row['PackIntegrity'];
				if ($row['SealType'] == 0) {
					$packtype = 2;
				} elseif ($row['SealType'] == 1) {
					$packtype = 1;
				} elseif ($row['SealType'] == 2) {
					$packtype = 3;
				};
				$packkind = 0;
				$banderolintegrity = $row['StrapsIntegrity'];
				$banderoltype = 1 - $row['StrapType'];
				// The following gets the arrays of expected denoms and denom sums of a deposit
				// It needs much attention for it produces arrays rather than values
				// By the way the SQL script will sum expecteds for banknotes and coins in one value per a denom
				$expected_denom = Array();
				$expected_sum = Array();
				$rows = get_array_from_sql('
					SELECT
						Value, 
						SUM(ExpectedCount) * Value
					FROM DepositDenomTotal
					INNER JOIN Denoms ON DepositDenomTotal.DenomId = Denoms.DenomId
					WHERE DepositReclId = '.$deposit_id.'
					AND ExpectedCount > 0
					GROUP BY DepositDenomTotal.DenomId
					;');
				foreach ($rows as $row) {
				$expected_denom[] = $row[0];
				$expected_sum[] = $row[1];
				};
				$packnominal = $expected_denom[0];
				$packsum = $expected_sum[0];
				if ($discrepancy_name == "излишек") { // defining overs
					$act_rows = get_assoc_array_from_sql('
						SELECT 
							expected.DenomId as DenomId, 
							expected.Value as Value,
							ABS(expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)) as discr_cnt
						FROM (
							SELECT DepositDenomTotal.DenomId as DenomId, Value, ExpectedCount
							FROM DepositDenomTotal
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
							WHERE DepositReclId = "'.addslashes($deposit_id).'") as expected
						LEFT JOIN (
							SELECT DenomId, SUM(ActualCount) as s_count
							FROM SorterAccountingData
							INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
							INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
							WHERE DepositRecId = "'.addslashes($deposit_id).'"
							GROUP BY DenomId) as sorter
						ON expected.DenomId = sorter.DenomId
						LEFT JOIN (
							SELECT DenomId, SUM(CullCount) as r_count
							FROM ReconAccountingData
							WHERE DepositRecId="'.addslashes($deposit_id).'"
							GROUP BY DenomId) as recon
						ON expected.DenomId = recon.DenomId
						WHERE expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)<0
						;');
					$akttype = 2;
				};
				if ($discrepancy_name == "недостача") { // defining shortages
					$act_rows = get_assoc_array_from_sql('
						SELECT 
							expected.DenomId as DenomId, 
							expected.Value as Value,
							ABS(expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)) as discr_cnt
						FROM (
							SELECT DepositDenomTotal.DenomId as DenomId, Value, ExpectedCount
							FROM DepositDenomTotal
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
							WHERE DepositReclId = "'.addslashes($deposit_id).'") as expected
						LEFT JOIN (
							SELECT DenomId, SUM(ActualCount) as s_count
							FROM SorterAccountingData
							INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
							INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
							WHERE DepositRecId = "'.addslashes($deposit_id).'"
							GROUP BY DenomId) as sorter
						ON expected.DenomId = sorter.DenomId
						LEFT JOIN (
							SELECT DenomId, SUM(CullCount) as r_count
							FROM ReconAccountingData
							WHERE DepositRecId='.$deposit_id.'
							GROUP BY DenomId) as recon
						ON expected.DenomId = recon.DenomId
						WHERE expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)>0
						;');
					$akttype = 1;
				};
				if ($discrepancy_name == "сомнительная банкнота") { // defining suspects
					$act_rows = get_assoc_array_from_sql('
						SELECT 
							ReconAccountingData.DenomId as DenomId, 
							Value, 
							SUM(CullCount) as discr_cnt
						FROM ReconAccountingData
						INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
						INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
						WHERE DepositRecId='.$deposit_id.' 
						AND GradeName = "SUSPECT"
						GROUP BY ReconAccountingData.DenomId;');
					$akttype = 3;
				};
				foreach ($act_rows as $act_row) {
					$act_count = $act_count + 1;
					if ($act_count < 10) {
						$r_act_no = '000'.$act_count;
					} elseif ($act_count < 100) {
						$r_act_no = '00'.$act_count;
					} elseif ($act_count < 1000) {
						$r_act_no = '0'.$act_count;
					} else {
						$r_act_no = $act_count;
					};
					$r_xml_filename = 'AKT0402145_'.$r_xml_datetime.'_'.$r_act_no; // This is the FILENAME
					$denom_id = $act_row['DenomId'];
					$nominal = $act_row['Value'];
					$banknotescount = $act_row['discr_cnt'];
					// Compile the body of an act
					$xml_body = '<?xml version="1.0" encoding="UTF-8"?>
					<AKT0402145 xmlns="urn:cbr-ru:csm:v1.0" CreateTimeFile="'.$createtimefile.'" 
					AktType="'.$akttype.'" 
					CashierName="'.$cashiername.'" 
					PresenceName="'.$presencename.'" 
					PresenceSignature="'.$presencesignature.'" 
					ClientName="'.$clientname.'" 
					BIC="'.$bic.'" PackerName="'.$packername.'" 
					PackDate="'.$packing_date.'" PackIntegrity="'.$packintegrity.'" 
					PackType="'.$packtype.'" PackKind="'.$packkind.'" 
					PackNumber="'.$packnumber.'" 
					RootNumber="1" BanknotesCount="'.$banknotescount.'" 
					Nominal="'.$nominal.'" 
					PackNominal="'.$packnominal.'" PackSum="'.$packsum.'" 
					BanderolIntegrity="'.$banderolintegrity.'" 
					BanderolType="'.$banderoltype.'" 
					PackId="'.$packnumber.'">';
					// Get serial numbers for the suspect banknotes
					if ($akttype == 3) {
						$r_suspects = get_assoc_array_from_sql('
							SELECT LeftSeria, LeftNumber
							FROM SuspectSerialNumbs
							WHERE DepositRecId = "'.addslashes($deposit_id).'"
							AND DenomId = '.$denom_id.'
							;');
						foreach ($r_suspects as $r_suspect) {
							$xml_body = $xml_body.'<BanknoteNumber Seria="'.$r_suspect['LeftSeria'].'" Number="'.$r_suspect['LeftNumber'].'"/>';
						}
					}
							$xml_body = $xml_body.'</AKT0402145>';
						add_xml_file_to_reportset($xml_body, 'AKT0402145_'.$r_xml_datetime.'_'.$r_act_no.'.xml');
					// The end of an individual act creating
				};
			};
           break;
		};
?>

