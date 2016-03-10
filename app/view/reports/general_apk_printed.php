<?php

    /*
     * Отчётная информация аппаратно-программного комплекса в разрезе по машинам, по индексам, сводная и по расхождениям
     * 
     */
    if (!isset($c)) exit;
    include_once 'app/model/get_short_iof_by_user_id.php';
    $r_report_label = 'Отчётная информация аппаратно-программного комплекса'; // Report label is used for selecting scenarios
    switch ($action_name) {
        case 'data_prepare':
            // Эта часть выполняется во время запроса параметров для генерации отчетов


            break;
        case 'data_report':
            // Эта часть выполняется при генерации отчета на экран
            // The following part prevents the report from being called with a wrong scenario
			// Define the scenarios for which this report is ON at the time
			$rows = get_array_from_sql('
				SELECT DISTINCT ScenReportTypes.ScenarioId
				FROM ScenReportTypes
					INNER JOIN ReportTypes ON ScenReportTypes.ReportTypeId = ReportTypes.ReportTypeId
					INNER JOIN Scenario ON Scenario.ScenarioId = ScenReportTypes.ScenarioId
				WHERE ReportLabel="'.addslashes($r_report_label).'"
				AND IsUsed=1
				AND Scenario.ReconcileAgainstValue = 0
				;');
			$r_scenarios = array();
			foreach ($rows as $row) {
				$r_scenarios[] = $row[0];
			};
			// Проверяем, есть ли в запрашиваемом наборе данные
            if (count($recs) > 0 AND count($r_scenarios) > 0) {
				// Following is the global set of deposits for the reported period
					$recsid = array();
					foreach ($recs as $rec) {
						$recsid[] = $rec[0];				
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
				if (count($recsid) > 0) {	
					// Определение стартового номера для отчётов (по каждому индексу отдельный отчёт)
					$date_year = substr($report_datetime, 0,4);
					$d_month = substr($report_datetime, 5,2);
					$date_day = substr($report_datetime, 8,2);
					$rows = get_array_from_sql('
						SELECT * FROM ReportSets
					;');
					if(count($rows) == 0) {
						// Случай, если ещё нет записей в таблице ReportSets и наша запись будет первая в истории системы
						$r_report_number = 0;
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
						$r_report_number = 0;
						foreach ($r_reportsets as $r_reportset) {
							 // Находим время начала обрабатываемого ReportSet
							$row = fetch_row_from_sql('
								SELECT IFNULL(MAX(SetDateTime), "1972-11-29 00:00:00")
								FROM ReportSets
								WHERE SetDateTime < "'.addslashes($r_reportset["SetDateTime"]).'"
							;');
							$r_setstart = $row[0];
							$row = fetch_row_from_sql('
								SELECT COUNT(DISTINCT DepositIndexId) 
								FROM DepositRecs
								WHERE RecLastChangeDatetime > "'.addslashes($r_setstart).'"
									AND RecLastChangeDatetime < "'.addslashes($r_reportset["SetDateTime"]).'"
							;');
							$r_report_number = $r_report_number + $row[0];
						}
					}
					// Конец блока определения стартового номера для отчёта
					
					// Getting general Cashcenter information
					$row = fetch_assoc_row_from_sql('
						SELECT 
							CashCenterName, 
							ComplexName
						FROM SystemGlobals 
							WHERE SystemGlobalsId = "1"
						;');            
				$site_name = $row['CashCenterName'];
				$complex_name = $row['ComplexName'];
				$row = fetch_assoc_row_from_sql('
					SELECT CashRoomName FROM CashRooms 
						WHERE Id = "'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
					;');            
				$cash_room = $row['CashRoomName'];
				?>
					<style>
						table.count {
								width:250mm;
						}
						table.count th {
								text-align: center; 
								padding: 3px;
								font-family: "Times New Roman", serif; 
								font-weight: normal; font-size: 12pt;
						}
						table.count td {
								text-align: right;
								padding: 3px; 
								font-family: "Times New Roman", serif; 
								font-size: 12pt; 
								font-weight: normal; 
						}
						table#signatures td {
								text-align: right; 
								font-family: "Times New Roman", serif; 
								font-size: 12pt; 
								font-weight: normal; 
						}
					</style>
					<script>


						function addClass(el, cls) {
								var c = el.className ? el.className.split(' ') : [];
								for (var i=0; i<c.length; i++) {
								if (c[i] == cls) return;
								}
								c.push(cls);
								el.className = c.join(' ');
						}
						function removeClass(el, cls) {
								var c = el.className.split(' ');
								for (var i=0; i<c.length; i++) {
								if (c[i] == cls) c.splice(i--, 1);
								}
								el.className = c.join(' ');
						}
						function hasClass(el, cls) {
								for (var c = el.className.split(' '),i=c.length-1; i>=0; i--) {
								if (c[i] == cls) return true;
								}
								return false;
						}
					</script>
				<?php
				// Define the list of indexes for the period
				$rows = get_array_from_sql('
					SELECT DISTINCT DepositIndexId
					FROM DepositRecs
					WHERE DepositRecId IN ('.implode(',',$recsid).');');
				$r_indexes = array();
				foreach ($rows as $row) {
					$r_indexes[] = $row[0];
				};
				$i = 0; // Счётчик отчётов (по индексам)
						foreach ($r_indexes as $r_index) {
					$i++;
					$r_report_number++;
					$row = fetch_row_from_sql('
						SELECT IndexValue
						FROM DepositIndex
						WHERE DepositIndexId = "'.$r_index.'";');	
					$r_index_value = $row[0];
				// Define machines that were engaged in operation for the period
				$rows = get_array_from_sql('
					SELECT 
						DISTINCT MachineDBId
					FROM 
						DepositRecs 
							INNER JOIN DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
						AND DepositRecs.DepositIndexId = "'.$r_index.'";');
				$r_machines = array();
				foreach ($rows as $row) {
					$r_machines[] = $row[0];				
				};
					?>
			<!---- Блок формирования данных по машинам //-->	
		
							<div class="rotated">
								<table class="rotated">
									<tr>
										<td style="vertical-align: top;">
											<span style="text-align:center; font-size:12pt; font-weight:bold; font-family: 'Times New Roman', serif;">
													<?php echo htmlfix($site_name); ?>
											</span>
											<table  style='width:250mm; font-size: 11pt; text-align: center;' border=0 cellspacing=0 cellpadding=1>
											  <tbody>
													<tr>
															<td style="text-align:center; font-size:12pt; font-weight:bold; font-family: 'Times New Roman', serif;">
															Отчётная информация аппаратно-программного комплекса <?php echo htmlfix($complex_name); ?>&nbsp;
															№&nbsp;<?php echo htmlfix($r_report_number); ?>
															</td>
													</tr>
													<tr>
															<td style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt; font-weight: bold;">
																	от <?php echo htmlfix(substr($report_datetime, 0, 16)); ?>
															</td>
											   </tr>
													<tr>
															<td <span style="text-align:center; font-size:12pt; font-weight:bold; font-family: 'Times New Roman', serif;">
																	Касса пересчёта <?php echo htmlfix($cash_room); ?>
															</td>
													</tr>
											  </tbody>
											</table>				
											<span style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt; font-weight: bold;">
													Индекс валют № <?php echo htmlfix($r_index_value); ?></span><br/>
											<?php
							// Rollup accounting data per indexes, machines, denoms
											// For each machine engaged define DepositRecs, that will be reported for the machine
											foreach ($r_machines as $r_machine) {
													$rows = get_array_from_sql('
															SELECT DISTINCT DepositRecId
															FROM DepositRuns
															WHERE 
																	DepositRecId IN ('.implode(',',$recsid).')
																	AND MachineDBId = '.$r_machine.'
																	AND DepositRecId IS NOT NULL	
															;');
													$r_machine_recs = array();
													foreach ($rows as $row) {
															$r_machine_recs[] = $row[0];
													};
													if(count($r_machine_recs) == 0) {
															continue;
													};
													$row = fetch_row_from_sql('
															SELECT SorterName FROM Machines WHERE MachineDBId = "'.$r_machine.'";');
													$sorter_name = $row[0];
													?>
													<span style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt; font-weight: bold;">
															Счётно-сортировальная машина № <?php echo htmlfix($sorter_name);?></span><br/>
													<?php
															$rows = get_array_from_sql('
																	SELECT DISTINCT DenomId
																	FROM SorterAccountingData 
																	INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
																	INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
																	INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
																	INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
																	WHERE DepositRuns.DepositRecId IN ('.implode(',',$r_machine_recs).')
																			AND DepositRecs.DepositIndexId = "'.$r_index.'"
																			AND ValuableTypeName = "banknotes"
																	ORDER BY DenomId;
																	');
																	$r_machine_denoms = array();
																	foreach ($rows as $row) {
																			$r_machine_denoms[] = $row[0];
																	};
																	?>
																	 <table class="count" border="1" cellspacing="0" cellpadding="1">
																			<tr>
																					<th width=18% rowspan=2 align="middle">Номинал</th>
																					<th width=52% colspan=4 align="middle">Количество листов</th>
																					<th width=26% colspan=2 align="middle">Всего обработано и уничтожено</th>
																	   </tr>
																	   <tr>
																					<th width=13% align="middle">Годные</th>
																					<th width=13% align="middle">Ветхие</th>
																					<th width=13% align="middle">Уничтоженные</th>
																					<th width=13% align="middle">Не прошедшие обработку</th>
																					<th width=13% align="middle">Всего листов</th>
																					<th width=13% align="middle">Всего рублей</th>
																			</tr>
																	<?php
																	// Initialize counters for totals per an index per a machine
																	$r_fit_total = 0;
																	$r_unfit_total = 0;
																	$r_shred_total = 0;
																	$r_fit_total_sum = 0;
																	$r_unfit_total_sum = 0;
																	$r_shred_total_sum = 0;
																	$r_grandtotal = 0;
																	$r_grandtotal_sum = 0;
																	foreach ($r_machine_denoms as $r_machine_denom)	{
																			// For each denom (within index within machine) rollup statistics
																			// Get the denom label, denom value
																			$row = fetch_row_from_sql('
																					SELECT DenomLabel, Value
																					FROM  Denoms
																					WHERE DenomId = "'.$r_machine_denom.'"
																					;');
																			$r_denom_label = $row[0];
																			$r_denom_value = $row[1];
																			// Get unfit number of notes
																			$row = fetch_row_from_sql('
																					SELECT
																							SUM(ActualCount)
																					FROM SorterAccountingData
																							INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
																							INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
																							INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
																							INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
																							INNER JOIN ValuablesGrades ON Valuables.ValuableId = ValuablesGrades.ValuableId
																									AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
																							INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
																					WHERE DepositRuns.DepositRecId IN ('.implode(',',$r_machine_recs).')
																							AND Valuables.DenomId="'.addslashes($r_machine_denom).'"
																							AND ValuableTypeName = "banknotes"
																							AND DepositRecs.DepositIndexId = "'.$r_index.'"
																							AND GradeName = "UNFIT"
																					;');
																			$r_denom_unfit = $row[0];
																			$r_unfit_total = $r_unfit_total + $r_denom_unfit;
																			$r_unfit_total_sum = $r_unfit_total_sum + $r_denom_unfit * $r_denom_value;
																			// Get fit number of notes
																			$row = fetch_row_from_sql('
																					SELECT
																							SUM(ActualCount)
																					FROM SorterAccountingData
																							INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
																							INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
																							INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
																							INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
																							INNER JOIN ValuablesGrades ON Valuables.ValuableId = ValuablesGrades.ValuableId
																									AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
																							INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
																					WHERE DepositRuns.DepositRecId IN ('.implode(',',$r_machine_recs).')
																							AND Valuables.DenomId="'.addslashes($r_machine_denom).'"
																							AND ValuableTypeName = "banknotes"
																							AND DepositRecs.DepositIndexId = '.$r_index.'
																							AND GradeName = "FIT"
																					;');
																			$r_denom_fit = $row[0];
																			$r_fit_total = $r_fit_total + $r_denom_fit;
																			$r_fit_total_sum = $r_fit_total_sum + $r_denom_fit * $r_denom_value;
																			// Get shred number of notes
																			$row = fetch_row_from_sql('
																					SELECT
																							SUM(ActualCount)
																					FROM SorterAccountingData
																							INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
																							INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
																							INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
																							INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
																							INNER JOIN ValuablesGrades ON Valuables.ValuableId = ValuablesGrades.ValuableId
																									AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
																							INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
																					WHERE DepositRuns.DepositRecId IN ('.implode(',',$r_machine_recs).')
																							AND Valuables.DenomId="'.addslashes($r_machine_denom).'"
																							AND ValuableTypeName = "banknotes"
																							AND DepositRecs.DepositIndexId = "'.$r_index.'"
																							AND GradeName = "SHRED"
																					;');
																			$r_denom_shred = $row[0];
																			$r_shred_total = $r_shred_total + $r_denom_shred;
																			$r_shred_total_sum = $r_shred_total_sum + $r_denom_shred * $r_denom_value;
																			$r_grandtotal = $r_grandtotal + $r_denom_fit + $r_denom_unfit + $r_denom_shred;
																			$r_grandtotal_sum = $r_grandtotal_sum + ($r_denom_fit + $r_denom_unfit + $r_denom_shred) * $r_denom_value;
																			?>
																			<tr>
																			<td style="text-align:center; border-bottom:1px solid black;"><?php echo $r_denom_label; ?></td>
																			<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_fit !=0) ? $r_denom_fit : ''; ?></td>
																			<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_unfit !=0) ? $r_denom_unfit : ''; ?></td>
																			<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_shred !=0) ? $r_denom_shred : ''; ?></td>
																			<td style="text-align:right; border-bottom:1px solid black;">&nbsp;</td>
																			<td style="text-align:right; border-bottom:1px solid black;"><?php echo $r_denom_fit + $r_denom_unfit + $r_denom_shred; ?></td>
																			<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_fit + $r_denom_unfit + $r_denom_shred) * $r_denom_value; ?></td>
																			</tr>
																			<?php
																	};
																	?>
																	<tr>
																		<td style="text-align:center; border-bottom:1px solid black;">Итого листов</td>
																		<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_fit_total !=0) ? $r_fit_total : ''; ?></td>
																		<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_unfit_total !=0) ? $r_unfit_total : ''; ?></td>
																		<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_shred_total !=0) ? $r_shred_total : ''; ?></td>
																		<td style="text-align:right; border-bottom:1px solid black;">&nbsp;</td>
																		<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_grandtotal !=0) ? $r_grandtotal : ''; ?></td>
																		<td style="text-align:center; border-bottom:1px solid black;">—</td>
																	</tr>
																	<tr>
																			<td align="middle">Итого рублей</td>
																			<td><?php echo ($r_fit_total_sum !=0) ? $r_fit_total_sum : ''; ?></td>
																			<td><?php echo ($r_unfit_total_sum !=0) ? $r_unfit_total_sum : ''; ?></td>
																			<td><?php echo ($r_shred_total_sum !=0) ? $r_shred_total_sum : ''; ?></td>
																			<td>&nbsp;</td>
																			<td style='text-align:center'>—</td>
																			<td><?php echo ($r_grandtotal_sum !=0) ? $r_grandtotal_sum : ''; ?></td>
																	</tr>
															</table><br/>
																	<?php

											}; // End of machines rollup
													?>
										</td>
									</tr>
								</table>
							</div>

							<?php
								include 'app/view/reports/page_divider.php';
							?>
									
						<div class="rotated">
				<table class="rotated">
								<tr>
									<td>
				<!--- //Rollup accounting data per indexes, denoms  //-->
						<span style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt;">
							Суммарные данные по аппаратно-программному комплексу --------------------------------------</span><br/>
						<?php
						// Define the list of denoms that will be reported
						// It will include sorter data denoms and recon data denoms (distinct)
						$rows = get_array_from_sql('
							SELECT DISTINCT DenomId
							FROM SorterAccountingData
								INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
								INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
								INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
								INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
							WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
								AND DepositRecs.DepositIndexId = "'.$r_index.'"
								AND ValuableTypeName = "banknotes"
							UNION
							SELECT DISTINCT DenomId
							FROM ReconAccountingData
								INNER JOIN DepositRecs ON ReconAccountingData.DepositRecId = DepositRecs.DepositRecId
								INNER JOIN ValuableTypes ON ReconAccountingData.ValuableTypeId = ValuableTypes.ValuableTypeId
							WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
								AND DepositRecs.DepositIndexId = "'.$r_index.'"
								AND ValuableTypeName = "banknotes"
							ORDER BY DenomId
							;
							');
						$r_denoms = array();
						foreach ($rows as $row) {
							$r_denoms[] = $row[0];
						};
						?>
						<table class="count" border="1" cellspacing=0 cellpadding=1>
							<tr>
								<th width=15.2% rowspan=2 align="middle">Номинал</th>
								<th width=17.28% colspan=2 align="middle">Годные</th>
								<th width=17.28% colspan=2 align="middle">Ветхие</th>
								<th width=17.28% colspan=2 align="middle">Уничтоженные</th>
								<th width=17.28% colspan=2 align="middle">Не прошедшие обработку</th>
								<th width=17.28% colspan=2 align="middle">Всего обработано и уничтожено</th>
						   </tr>
						   <tr>
								<th width=7.28% align="middle">Количество листов</th>
								<th width=10.0% align="middle">Сумма рублей</th>
								<th width=7.28% align="middle">Количество листов</th>
								<th width=10.0% align="middle">Сумма рублей</th>
								<th width=7.28% align="middle">Количество листов</th>
								<th width=10.0% align="middle">Сумма рублей</th>
								<th width=7.28% align="middle">Количество листов</th>
								<th width=10.0% align="middle">Сумма рублей</th>
								<th width=7.28% align="middle">Количество листов</th>
								<th width=10.0% align="middle">Сумма рублей</th>
							</tr>
						<?php
						// Initialize counters for totals per an index 
						$r_fit_total = 0;
						$r_unfit_total = 0;
						$r_shred_total = 0;
						$r_reject_total = 0;
						$r_fit_total_sum = 0;
						$r_unfit_total_sum = 0;
						$r_shred_total_sum = 0;
						$r_reject_total_sum = 0;
						$r_grandtotal =0;
						$r_grandtotal_sum =0;
						foreach ($r_denoms as $r_denom)	{
							// For each denom (within index) rollup statistics
							// Get the denom label, denom value and expected number of notes
							$row = fetch_row_from_sql('
								SELECT DenomLabel, Value
								FROM Denoms
								WHERE DenomId = '.$r_denom.';
							');
							$r_denom_label = $row[0];
							$r_denom_value = $row[1];
							// Get fit number of notes
							$row = fetch_row_from_sql('
								SELECT
									SUM(ActualCount)
								FROM SorterAccountingData
									INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
									INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
									INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
									INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
									INNER JOIN ValuablesGrades ON Valuables.ValuableId = ValuablesGrades.ValuableId
										AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
									INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
								WHERE DepositRuns.DepositRecId IN ('.implode(',',$recsid).')
									AND Valuables.DenomId="'.addslashes($r_denom).'"
									AND ValuableTypeName = "banknotes"
									AND DepositRecs.DepositIndexId = "'.$r_index.'"
									AND GradeName = "FIT"
								;');
							$r_denom_fit = $row[0];
							$r_denom_fit_sum = $r_denom_fit * $r_denom_value;
							$r_fit_total = $r_fit_total + $r_denom_fit;
							$r_fit_total_sum = $r_fit_total_sum + $r_denom_fit * $r_denom_value;
							// Get unfit number of notes
							$row = fetch_row_from_sql('
								SELECT
									SUM(ActualCount)
								FROM SorterAccountingData
									INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
									INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
									INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
									INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
									INNER JOIN ValuablesGrades ON Valuables.ValuableId = ValuablesGrades.ValuableId
										AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
									INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
								WHERE DepositRuns.DepositRecId IN ('.implode(',',$recsid).')
									AND Valuables.DenomId="'.addslashes($r_denom).'"
									AND ValuableTypeName = "banknotes"
									AND DepositRecs.DepositIndexId = "'.$r_index.'"
									AND GradeName = "UNFIT"
								;');
							$r_denom_unfit = $row[0];
							$r_denom_unfit_sum = $r_denom_unfit * $r_denom_value;
							$r_unfit_total = $r_unfit_total + $r_denom_unfit;
							$r_unfit_total_sum = $r_unfit_total_sum + $r_denom_unfit * $r_denom_value;
							// Get shred number of notes
							$row = fetch_row_from_sql('
								SELECT
									SUM(ActualCount)
								FROM SorterAccountingData
									INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
									INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
									INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
									INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
									INNER JOIN ValuablesGrades ON Valuables.ValuableId = ValuablesGrades.ValuableId
										AND DepositRecs.ScenarioId = ValuablesGrades.ScenarioId
									INNER JOIN Grades ON ValuablesGrades.GradeId = Grades.GradeId
								WHERE DepositRuns.DepositRecId IN ('.implode(',',$recsid).')
									AND Valuables.DenomId="'.addslashes($r_denom).'"
									AND ValuableTypeName = "banknotes"
									AND DepositRecs.DepositIndexId = "'.$r_index.'"
									AND GradeName = "SHRED"
								;');
							$r_denom_shred = $row[0];
							$r_denom_shred_sum = $r_denom_shred * $r_denom_value;
							$r_shred_total = $r_shred_total + $r_denom_shred;
							$r_shred_total_sum = $r_shred_total_sum + $r_denom_shred * $r_denom_value;
							// Get reject number of notes
							$row = fetch_row_from_sql('
								SELECT
									SUM(CullCount)
								FROM ReconAccountingData
									INNER JOIN ValuableTypes ON ReconAccountingData.ValuableTypeId = ValuableTypes.ValuableTypeId
									INNER JOIN DepositRecs ON ReconAccountingData.DepositRecId = DepositRecs.DepositRecId
								WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
									AND ReconAccountingData.DenomId="'.addslashes($r_denom).'"
									AND ValuableTypeName = "banknotes"
									AND DepositRecs.DepositIndexId = "'.$r_index.'"
								;');
							$r_denom_reject = $row[0];
							$r_denom_reject_sum = $r_denom_reject * $r_denom_value;
							$r_reject_total = $r_reject_total + $r_denom_shred;
							$r_reject_total_sum = $r_reject_total_sum + $r_denom_reject * $r_denom_value;
							$r_subtotal = $r_denom_fit + $r_denom_unfit + $r_denom_shred + $r_denom_reject;
							$r_subtotal_sum = $r_subtotal * $r_denom_value;
							$r_grandtotal = $r_grandtotal + $r_subtotal;
							$r_grandtotal_sum = $r_grandtotal_sum + $r_subtotal_sum;
							?>
							<tr>
								<td style="text-align:center; border-bottom:1px solid black;"><?php echo $r_denom_label; ?></td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_fit !=0) ? $r_denom_fit: ''; ?></td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_fit_sum !=0) ? $r_denom_fit_sum: ''; ?></td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_unfit !=0) ? $r_denom_unfit: ''; ?></td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_unfit_sum !=0) ? $r_denom_unfit_sum: ''; ?></td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_shred !=0) ? $r_denom_shred: ''; ?></td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_shred_sum !=0) ? $r_denom_shred_sum: ''; ?></td>
									
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_reject !=0) ? $r_denom_reject: ''; ?></td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_denom_reject_sum !=0) ? $r_denom_reject_sum: ''; ?></td>
									
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_subtotal !=0) ? $r_subtotal: ''; ?></td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_subtotal_sum !=0) ? $r_subtotal_sum : ''; ?></td>
							</tr>
							<?php
						};
								?>
								<tr>
									<td style="text-align:center; border-bottom:1px solid black;">Итого листов</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_fit_total !=0) ? $r_fit_total: ''; ?></td>
									<td style="text-align:center; border-bottom:1px solid black;">—</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_unfit_total !=0) ? $r_unfit_total: ''; ?></td>
									<td style="text-align:right; border-bottom:1px solid black;">—</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_shred_total !=0) ? $r_shred_total: ''; ?></td>
									<td style="text-align:center; border-bottom:1px solid black;">—</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_reject_total !=0) ? $r_reject_total: ''; ?></td>
									<td style="text-align:center; border-bottom:1px solid black;">—</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_grandtotal !=0) ? $r_grandtotal: ''; ?></td>
									<td style="text-align:center; border-bottom:1px solid black;">—</td>
								</tr>
								<tr>
									<td align="middle">Итого рублей</td>
									<td style='text-align:center'>—</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_fit_total_sum !=0) ? $r_fit_total_sum: ''; ?></td>
									<td style='text-align:center'>—</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_unfit_total_sum !=0) ? $r_unfit_total_sum: ''; ?></td>
									<td style='text-align:center'>—</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_shred_total_sum !=0) ? $r_shred_total_sum: ''; ?></td>
									<td style='text-align:center'>—</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_reject_total_sum !=0) ? $r_reject_total_sum: ''; ?></td>
									<td style='text-align:center'>—</td>
									<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($r_grandtotal_sum !=0) ? $r_grandtotal_sum: ''; ?></td>
								</tr>
							</table>
						</td>
										</tr>
								</table>
							</div>
							<?php
								include 'app/view/reports/page_divider.php';
							?>
			<!---- Блок формирования данных о расхождениях //-->		
			
					<div class="rotated">
						<table class="rotated">
							<tr>
								<td>
					<span style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt;">
					Данные о кассовых просчётах ---------------------------------------------------------</span><br/><br/>
					<table class="count" border="1" cellspacing=0 cellpadding=1>
						<tr>
							<th width=16% rowspan=2 align="middle">Номинал</th>
							<th width=28% colspan=2 align="middle">Излишки</th>
							<th width=28% colspan=2 align="middle">Недостачи</th>
							<th width=28% colspan=2 align="middle">Сомнительные</th>
					   </tr>
					   <tr>
							<th width=12% align="middle">Количество листов</th>
							<th width=16% align="middle">Сумма рублей</th>
							<th width=12% align="middle">Количество листов</th>
							<th width=16% align="middle">Сумма рублей</th>
							<th width=12% align="middle">Количество листов</th>
							<th width=16% align="middle">Сумма рублей</th>
						</tr>
					<?php
					//Define the denoms
					$rows = get_array_from_sql('
						SELECT DISTINCT expected.DenomId
							FROM 
							(SELECT DepositReclId, DenomId, ExpectedCount
							FROM DepositDenomTotal
							INNER JOIN DepositRecs 
								ON DepositRecs.DepositRecId=DepositDenomTotal.DepositReclId
							WHERE DepositIndexId = "'.$r_index.'"
							AND DepositReclId IN ('.implode(',',$recsid).') 
							) as expected
						LEFT JOIN
							(SELECT DepositRecs.DepositRecId as dri,
							Valuables.DenomId as di, SUM(SorterAccountingData.ActualCount) as sorter_count
							FROM SorterAccountingData
							INNER JOIN Valuables
							ON Valuables.ValuableId=SorterAccountingData.ValuableId
							INNER JOIN DepositRuns 
							ON SorterAccountingData.DepositRunId=DepositRuns.DepositRunId
							INNER JOIN DepositRecs
							ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
							WHERE DepositIndexId = "'.$r_index.'"
							AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
							GROUP BY dri, di) as sorter
						ON expected.DepositReclId=sorter.dri
						AND expected.DenomId=sorter.di
						LEFT JOIN
							(SELECT DepositRecId, DenomId, SUM(CullCount) as CullCount
							FROM ReconAccountingData
							WHERE DepositRecId IN ('.implode(',',$recsid).')
							GROUP BY DepositRecId, DenomId) as recon
						ON expected.DepositReclId=recon.DepositRecId
						AND expected.DenomId=recon.DenomId
						LEFT JOIN
							(SELECT DepositRecId, DenomId, CullCount as SuspectCount
							FROM ReconAccountingData
							INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
							WHERE DepositRecId IN ('.implode(',',$recsid).')
							AND Grades.GradeName = "SUSPECT") as suspect
						ON expected.DepositReclId=suspect.DepositRecId
						AND expected.DenomId=suspect.DenomId
						WHERE expected.ExpectedCount-COALESCE(sorter_count, 0)-COALESCE(CullCount, 0)<>0
						OR COALESCE(SuspectCount,0) > 0
						ORDER BY expected.DenomId ASC;
						');
					$r_denoms = array();
					foreach ($rows as $row) {
						$r_denoms[] = $row[0];
					};
					$over_total = 0;
					$short_total = 0;
					$suspect_total = 0;
					$over_total_sum = 0;
					$short_total_sum = 0;
					$suspect_total_sum =0;
					foreach ($r_denoms as $r_denom) {
						//For each denom get label and calculate overs, shorts and suspects
						$row = fetch_row_from_sql('
							SELECT Value, DenomLabel FROM Denoms WHERE DenomId = "'.$r_denom.'";');
						$denom_value = $row[0];
						$denom_label = $row[1];
						$row = fetch_row_from_sql('
							SELECT
								COALESCE(SUM(COALESCE(sorter_count, 0)+COALESCE(CullCount, 0)-expected.ExpectedCount), 0)
							FROM (
								SELECT DepositReclId, DenomId, ExpectedCount
								FROM DepositDenomTotal
								INNER JOIN DepositRecs ON DepositRecs.DepositRecId=DepositDenomTotal.DepositReclId
								WHERE DepositReclId IN ('.implode(',',$recsid).')
								AND DepositIndexId = '.$r_index.'
								AND IsBalanced <> 1
								AND DenomId = "'.$r_denom.'") as expected
							LEFT JOIN (
								SELECT DepositRecs.DepositRecId as dri,
								Valuables.DenomId as di, SUM(SorterAccountingData.ActualCount) as sorter_count
								FROM SorterAccountingData
								INNER JOIN Valuables
								ON Valuables.ValuableId=SorterAccountingData.ValuableId
								INNER JOIN DepositRuns 
								ON SorterAccountingData.DepositRunId=DepositRuns.DepositRunId
								INNER JOIN DepositRecs
								ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
								WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
								AND DepositRecs.DepositIndexId = "'.$r_index.'"
								AND Valuables.DenomId = "'.$r_denom.'"
								AND IsBalanced <> 1
								GROUP BY dri, di) as sorter
								ON expected.DepositReclId=sorter.dri
								AND expected.DenomId=sorter.di
							LEFT JOIN (
								SELECT DepositRecId, DenomId, SUM(CullCount) as CullCount
								FROM ReconAccountingData
								WHERE DepositRecId IN ('.implode(',',$recsid).')
								AND DenomId = "'.$r_denom.'"
								GROUP BY DepositRecId, DenomId) as recon
							ON expected.DepositReclId=recon.DepositRecId
							AND expected.DenomId=recon.DenomId
							WHERE expected.ExpectedCount-COALESCE(sorter_count, 0)-COALESCE(CullCount, 0)<0;');
						$over_count = $row[0];
						$over_sum = $over_count * $denom_value;
						$over_total = $over_total + $over_count;
						$over_total_sum = $over_total_sum + $over_sum;
						$row = fetch_row_from_sql('
							SELECT
								COALESCE(SUM(expected.ExpectedCount-COALESCE(sorter_count, 0)-COALESCE(CullCount, 0)), 0)
							FROM (
								SELECT DepositReclId, DenomId, ExpectedCount
								FROM DepositDenomTotal
								INNER JOIN DepositRecs ON DepositRecs.DepositRecId=DepositDenomTotal.DepositReclId
								WHERE DepositReclId IN ('.implode(',',$recsid).')
								AND DepositIndexId = "'.$r_index.'"
								AND IsBalanced <> 1
								AND DenomId = "'.$r_denom.'") as expected
							LEFT JOIN (
								SELECT DepositRecs.DepositRecId as dri,
								Valuables.DenomId as di, SUM(SorterAccountingData.ActualCount) as sorter_count
								FROM SorterAccountingData
								INNER JOIN Valuables
								ON Valuables.ValuableId=SorterAccountingData.ValuableId
								INNER JOIN DepositRuns 
								ON SorterAccountingData.DepositRunId=DepositRuns.DepositRunId
								INNER JOIN DepositRecs
								ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
								WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
								AND DepositIndexId = "'.$r_index.'"
								AND Valuables.DenomId = "'.$r_denom.'"
								AND IsBalanced <> 1
								GROUP BY dri, di) as sorter
								ON expected.DepositReclId=sorter.dri
								AND expected.DenomId=sorter.di
							LEFT JOIN (
								SELECT DepositRecId, DenomId, SUM(CullCount) as CullCount
								FROM ReconAccountingData
								WHERE DepositRecId IN ('.implode(',',$recsid).')
					
						AND DenomId = "'.$r_denom.'"
								GROUP BY DepositRecId, DenomId) as recon
							ON expected.DepositReclId=recon.DepositRecId
							AND expected.DenomId=recon.DenomId
							WHERE expected.ExpectedCount-COALESCE(sorter_count, 0)-COALESCE(CullCount, 0)>0;');

						$short_count = $row[0];
						$short_sum = $short_count * $denom_value;
						$short_total = $short_total + $short_count;
						$short_total_sum = $short_total_sum + $short_sum;
						
						$row = fetch_row_from_sql('
							SELECT SUM(CullCount)
							FROM ReconAccountingData
							INNER JOIN DepositRecs ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
							INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
							WHERE ReconAccountingData.DepositRecId IN ('.implode(',',$recsid).')
							AND IsBalanced <> 1
							AND DepositIndexId = "'.$r_index.'"
							AND DenomId = "'.$r_denom.'"
							AND GradeName = "SUSPECT"
						;');
						$suspect_count = $row[0];
						$suspect_sum = $suspect_count * $denom_value;
						$suspect_total = $suspect_total + $suspect_count;
						$suspect_total_sum = $suspect_total_sum + $suspect_sum;
						?>
						   <tr>
								<td style="text-align:right; border-bottom:1px solid black;"><?php echo $denom_label;?></th>
								<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($over_count !=0) ? $over_count: ''; ?></th>
								<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($over_sum !=0) ? $over_sum: ''; ?></th>
								<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($short_count !=0) ? $short_count: ''; ?></th>
								<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($short_sum !=0) ? $short_sum: ''; ?></th>
								<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($suspect_count !=0) ? $suspect_count: ''; ?></th>
								<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($suspect_sum !=0) ? $suspect_sum: ''; ?></th>
							</tr>
						<?php
					};	
					?>
					   <tr>
							<td style="text-align:right; border-bottom:1px solid black;">Итого листов</td>
							<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($over_total !=0) ? $over_total: ''; ?></td>
							<td style="text-align:center; border-bottom:1px solid black;">—</td>
							<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($short_total !=0) ? $short_total: ''; ?></td>
							<td style="text-align:center; border-bottom:1px solid black;">—</td>
							<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($suspect_total !=0) ? $suspect_total: ''; ?></td>
							<td style="text-align:center; border-bottom:1px solid black;"'>—</td>
						</tr>
					   <tr>
							<td style="text-align:right; border-bottom:1px solid black;">Итого рублей</td>
							<td style="text-align:center; border-bottom:1px solid black;">—</td>
							<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($over_total_sum !=0) ? $over_total_sum: ''; ?></td>
							<td style="text-align:center; border-bottom:1px solid black;">—</td>
							<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($short_total_sum !=0) ? $short_total_sum: ''; ?></td>
							<td style="text-align:center; border-bottom:1px solid black;">—</td>
							<td style="text-align:right; border-bottom:1px solid black;"><?php echo ($suspect_total_sum !=0) ? $suspect_total_sum: ''; ?></td>
						</tr>
					</table>
					<br/>
				<!-- Пример вставки имени котролера сгенерившего отчет -->
				<table  id="signatures" style='width:250mm;' border=0 cellspacing=0 cellpadding=1>
						<tr style="line-height: 1.3em;">
							<td style='border-bottom-width: 1px; border-bottom-style: solid; text-align: left;'>
								<?php echo $_SESSION[$program]['UserConfiguration']['UserPost']; ?></td>
							<td>&nbsp;</td>
							<td style='border-bottom-width: 1px; border-bottom-style: solid;'>
								&nbsp;</td>
							<td>&nbsp;</td>
							<td style='border-bottom-width: 1px; border-bottom-style: solid; text-align: left;'>
							<?php echo htmlfix(get_short_iof_by_user_id($_SESSION[$program]['UserConfiguration']['UserId'])); ?>
							</td>
						</tr>
						<tr  style="line-height: 1em;">
							<td style='font-size: 8pt; text-align: center; vertical-align: top; line-height: 1em;'>(должность)</td>
							<td>&nbsp;</td>
							<td style='font-size: 8pt; text-align: center; vertical-align: top; line-height: 1em;'>(подпись)</td>
							<td>&nbsp;</td>
							<td style='font-size: 8pt; text-align: center; vertical-align: top; line-height: 1em;'>(инициалы, фамилия)</td>
						</tr>
			
			   <?php
			   $signers = get_report_signers();
					foreach ($signers as $signer) {
						?>
						<tr style="line-height: 1.3em;">
							<td style='width:32%; border-bottom: 1px solid black; text-align:left;'>
								<?php echo htmlfix($signer[0]); ?></td>
							<td style='width:3%;'>&nbsp;</td>
							<td style='width:30%; border-bottom: 1px solid black;'>&nbsp;</td>
							<td style='width:3%;'>&nbsp;</td>
							<td style='width:32%; border-bottom: 1px solid black; text-align:left;'>
							<?php echo htmlfix($signer[1]); ?>
							</td>
						</tr>
						</tr>
						<tr  style="line-height: 1em;">
							<td style='font-size: 8pt; text-align: center; vertical-align: top; line-height: 1em;'>(должность)</td>
							<td>&nbsp;</td>
							<td style='font-size: 8pt; text-align: center; vertical-align: top; line-height: 1em;'>(подпись)</td>
							<td>&nbsp;</td>
							<td style='font-size: 8pt; text-align: center; vertical-align: top; line-height: 1em;'>(инициалы, фамилия)</td>
						</tr>
				<?php
				 };
				?>
					</table>
								</td>
							</tr>
						</table>
					</div>
					<?php
						if($i != count($r_indexes)) {
								// Пропуск между двумя отчётами по индексам
							include 'app/view/reports/page_divider.php';
						}
					?>
			<?php
				}; // End of index rollup 
			};
		}		
		break;
    };
?>

