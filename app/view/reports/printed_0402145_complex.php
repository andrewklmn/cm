<?php

    /*
     * Скрипт формирования пакета обобщённых актов 0402145 для случая единый акт для каждого типа расхождения и каждого номинала
     */
    if (!isset($c)) exit;
    switch ($action_name) {
		case 'data_prepare':
            // The following code is performed when gathering report parameters
            break;
		case 'data_report':
		
		
			if (count($recs) > 0) { // Если в наборе отчётов есть депозиты, продолжаем
				// Define the Deposits that require act compiling
				$recsid = array();
				foreach ($recs as $rec) {
					$recsid[] = $rec[0];				
				};
				
				// Определение стартового номера для актов о расхождениях
				$date_year = substr($report_datetime, 0, 4);
				$d_month = substr($report_datetime, 5, 2);
				$date_day = substr($report_datetime, 8, 2);
				
				$rows = get_array_from_sql('
					SELECT * FROM ReportSets
				;');
				if(count($rows) == 0) {
					// Случай, если ещё нет записей в таблице ReportSets и наша запись будет первая в истории системы
					$r_act_number = 0;
				} else {
					// Находим ReportSetы, созданные от начала суток запрашиваемой даты до запрашиваемой даты-времени
					$r_reportsets = get_assoc_array_from_sql('
						SELECT SetDateTime 
						FROM ReportSets
						WHERE 
							SetDateTime >= "'.addslashes($date_year).'-'.addslashes($d_month).'-'.addslashes($date_day).' 00:00:00" 
							AND 
							SetDateTime < "'.addslashes($report_datetime).'"
					');
					$r_act_number = 0;
					foreach ($r_reportsets as $r_reportset) {
						 // Находим время начала каждого обрабатываемого в цикле ReportSet
						$row = fetch_row_from_sql('
							SELECT IFNULL(MAX(SetDateTime), "1972-11-29 00:00:00")
							FROM ReportSets
							WHERE SetDateTime < "'.addslashes($r_reportset["SetDateTime"]).'"
						;');
						$r_setstart = $row[0];
						$rows = get_array_from_sql('
							SELECT * 
							FROM DepositRecs
							WHERE RecLastChangeDatetime > "'.addslashes($r_setstart).'"
								AND RecLastChangeDatetime < "'.addslashes($r_reportset["SetDateTime"]).'"
								AND IsBalanced <> 1
								AND ReconcileStatus = 1
								AND ServiceRec = 0
						;');
						$r_act_number = $r_act_number + count($rows); // Это номер последнего акта в предыдущем наборе отчётов
					}
				}
				// Конец блока определения стартового номера для отчёта
				
				// Get act events for the reporting period
				$r_events = get_assoc_array_from_sql('
					SELECT DISTINCT DepositId
					FROM Acts
					INNER JOIN DepositRecs ON DepositRecs.DepositRecId = Acts.DepositId
					WHERE DepositId IN ('.implode(',',$recsid).')
					AND IFNULL(DepositRecs.isBalanced, 0) = 0
					ORDER BY DepositId;');
					$i = 0;
				foreach ($r_events as $r_event) {
					$i++; //Counter of act cycles (to skip page-break in the end of the last act)
					$r_act_number++;
					$deposit_id = $r_event['DepositId'];


					include('single_0402145.php');



						if (count($r_events) != $i) {
							include 'app/view/reports/page_divider.php';
							
						};
				};
			};
           break;
		};
    
?>
