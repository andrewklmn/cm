<?php

    /*
     * Отчётная информация аппаратно-программного комплекса в разрезе по машинам, по индексам, сводная и по расхождениям
     * При группировке по машинам депозиты, обрабатывавшиеся на более чем одной машине
     * назначаются (в полном объёме) той машине, которая завершила обработку депозита последняя.
     * Вследствии этого данные настоящего отчёта могут расходиться с данными, полученными непосредственно с машин.
     */
    if (!isset($c)) exit;
    include_once 'app/model/get_short_iof_by_user_id.php';
    switch ($action_name) {
        case 'data_prepare':
            // Эта часть выполняется во время запроса параметров для генерации отчетов


            break;
        case 'data_report':
            // Эта часть выполняется при генерации отчета на экран
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
            $report_date = date("d.m.Y H:i");
				// Определениа массива с индексами DepositRecs, участвующих в формировании отчёта
				$recsid = array();
				foreach ($recs as $rec) {
					$recsid[] = $rec[0];				
				};

				// Define machine indexes that were engaged in operation for the period
				$rows = get_array_from_sql('
					SELECT 
						DISTINCT MachineDBId
					FROM 
						DepositRecs INNER JOIN DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).');');
				$r_machines = array();
				foreach ($rows as $row) {
					$r_machines[] = $row[0];				
				};
				?>
				<style>
					table#count {
						width:181mm;
					}
					table#count th {
						text-align: center; font-family: "Times New Roman", serif; font-weight: normal; font-size: 10pt;
					}
					table#count,#signatures td {
						text-align: right; font-family: "Times New Roman", serif; font-size: 10pt; font-weight: normal; 
					}
					table#signatures td {
						text-align: right; font-family: "Times New Roman", serif; font-size: 12pt; font-weight: normal; 
					}
				</style>

				<?php echo $site_name; ?>

				<table  style='width:181mm;' border=0 cellspacing=0 cellpadding=1>
					<tr>
						<td align=middle><b>Отчётная информация аппаратно-программного комплекса <?php echo $complex_name; ?></b></td>
					</tr>
					<tr>
						<td align=middle><b>Касса пересчёта <?php echo $cash_room; ?></b></td>
					</tr>
					<tr>
						<td align=middle>от <?php echo $report_date; ?></td>
				   </tr>
				</table>				
				<?php
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
					$sorter_name = $row[0];
					?>
					<span style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt;">Счётно-сортировальная машина № <?php echo $sorter_name;?></span><br/>
					<?php
					// Define the list of indexes for the machine
					$rows = get_array_from_sql('
						SELECT DISTINCT DepositIndexId
						FROM DepositRecs
						WHERE DepositRecId IN ('.implode(',',$r_machine_recs).');');
					$r_machine_indexes = array();
					foreach ($rows as $row) {
						$r_machine_indexes[] = $row[0];
					};
					
					// For each machine index define the list of denoms that will be reported
						foreach ($r_machine_indexes as $r_machine_index) {
							$rows = get_array_from_sql('
								SELECT DISTINCT DenomId
								FROM DepositDenomTotal 
								INNER JOIN DepositRuns ON DepositRuns.DepositRecId = DepositDenomTotal.DepositReclId
								INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositDenomTotal.DepositReclId
								WHERE DepositRuns.DepositRecId IN ('.implode(',',$r_machine_recs).')
									AND DepositRecs.DepositIndexId = '.$r_machine_index.'
									AND ExpectedCount > 0;
								');
							$r_machine_denoms = array();
							foreach ($rows as $row) {
								$r_machine_denoms[] = $row[0];
							};
							$row = fetch_row_from_sql('
								SELECT IndexValue FROM DepositIndex WHERE DepositIndexId = '.$r_machine_index.';');
							$index_value = $row[0];
							?>
							<span style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt;">Индекс валют № </span><?php echo $index_value; ?>
							 <table id="count" border="1" cellspacing=0 cellpadding=1>
								<tr>
									<th width=15% rowspan=2 align="middle">Номинал</th>
									<th width=51% colspan=3 align="middle">Количество листов</th>
									<th width=34% colspan=2 align="middle">Всего обработано и уничтожено</th>
							   </tr>
							   <tr>
									<th width=17% align="middle">Годные</th>
									<th width=17% align="middle">Ветхие</th>
									<th width=17% align="middle">Уничтоженные</th>
									<th width=17% align="middle">Всего листов</th>
									<th width=17% align="middle">Всего рублей</th>
								</tr>
							<?php
							// Initialize counters for totals per an index per a machine
							$r_fit_total = 0;
							$r_unfit_total = 0;
							$r_shred_total = 0;
							$r_expected_total = 0;
							$r_fit_total_sum = 0;
							$r_unfit_total_sum = 0;
							$r_shred_total_sum = 0;
							$r_expected_total_sum = 0;
							foreach ($r_machine_denoms as $r_machine_denom)	{
								// For each denom (within index within machine) rollup statistics
								// Get the denom label, denom value and expected number of notes
								$row = fetch_row_from_sql('
									SELECT DenomLabel, Value, SUM(ExpectedCount)
									FROM DepositDenomTotal 
									INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
									INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
									WHERE DepositDenomTotal.DepositReclId IN ('.implode(',',$r_machine_recs).')
									AND DepositIndexId = '.$r_machine_index.'
									GROUP BY DepositDenomTotal.DenomId
									HAVING DepositDenomTotal.DenomId = '.$r_machine_denom.';
								');
								$r_denom_label = $row[0];
								$r_denom_value = $row[1];
								$r_denom_expected = $row[2];
								$r_expected_total = $r_expected_total + $r_denom_expected;
								$r_expected_total_sum = $r_expected_total_sum + $r_expected_total * $r_denom_value;
								// Get unfit number of notes
								$row = fetch_row_from_sql('
									SELECT 
										SUM(sorter.sorter_count),
										SUM(recon.CullCount)
									FROM (
										SELECT DepositReclId, DenomId, ExpectedCount
										FROM DepositDenomTotal
										INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
										WHERE ExpectedCount <>0 AND DepositRecs.DepositIndexId = '.$r_machine_index.'
											AND DepositReclId IN ('.implode(',',$r_machine_recs).')) as expected
									LEFT JOIN (
										SELECT 
											DepositRecs.DepositRecId as dri,
											Valuables.DenomId as di, 
											SUM(SorterAccountingData.ActualCount
											) as sorter_count
										FROM SorterAccountingData
										INNER JOIN Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
										INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId=DepositRuns.DepositRunId
										INNER JOIN DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
										INNER JOIN ValuablesGrades ON DepositRecs.ScenarioId=ValuablesGrades.ScenarioId
											AND SorterAccountingData.ValuableId=ValuablesGrades.ValuableId
										INNER JOIN Grades ON Grades.GradeId=ValuablesGrades.GradeId
											WHERE GradeName="UNFIT"
										GROUP BY dri, di
										) as sorter
									ON expected.DepositReclId=sorter.dri AND expected.DenomId=sorter.di
									LEFT JOIN (
										SELECT 
											DepositRecId, 
											DenomId, 
											CullCount
										FROM ReconAccountingData 
										INNER JOIN Grades ON Grades.GradeId=ReconAccountingData.GradeId
											WHERE GradeName="UNFIT"
											) as recon
									ON expected.DepositReclId=recon.DepositRecId 
									AND expected.DenomId=recon.DenomId AND expected.DenomId=recon.DenomId
									GROUP BY expected.DenomId
									HAVING expected.DenomId="'.addslashes($r_machine_denom).'";');
								$r_denom_unfit = $row[0] + $row[1];
								$r_unfit_total = $r_unfit_total + $r_denom_unfit;
								$r_unfit_total_sum = $r_unfit_total_sum + $r_unfit_total * $r_denom_value;
								// Get shred number of notes
								$row = fetch_row_from_sql('
									SELECT 
										SUM(sorter.sorter_count),
										SUM(recon.CullCount)
									FROM (
										SELECT DepositReclId, DenomId, ExpectedCount
										FROM DepositDenomTotal
										INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
										WHERE ExpectedCount <>0 AND DepositRecs.DepositIndexId = '.$r_machine_index.'
											AND DepositReclId IN ('.implode(',',$r_machine_recs).')) as expected
									LEFT JOIN (
										SELECT 
											DepositRecs.DepositRecId as dri,
											Valuables.DenomId as di, 
											SUM(SorterAccountingData.ActualCount
											) as sorter_count
										FROM SorterAccountingData
										INNER JOIN Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
										INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId=DepositRuns.DepositRunId
										INNER JOIN DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
										INNER JOIN ValuablesGrades ON DepositRecs.ScenarioId=ValuablesGrades.ScenarioId
											AND SorterAccountingData.ValuableId=ValuablesGrades.ValuableId
										INNER JOIN Grades ON Grades.GradeId=ValuablesGrades.GradeId
											WHERE GradeName="SHRED"
										GROUP BY dri, di
										) as sorter
									ON expected.DepositReclId=sorter.dri AND expected.DenomId=sorter.di
									LEFT JOIN (
										SELECT 
											DepositRecId, 
											DenomId, 
											CullCount
										FROM ReconAccountingData 
										INNER JOIN Grades ON Grades.GradeId=ReconAccountingData.GradeId
											WHERE GradeName="SHRED"
											) as recon
									ON expected.DepositReclId=recon.DepositRecId 
									AND expected.DenomId=recon.DenomId AND expected.DenomId=recon.DenomId
									GROUP BY expected.DenomId
									HAVING expected.DenomId="'.addslashes($r_machine_denom).'";');
								$r_denom_shred = $row[0] + $row[1];
								$r_denom_fit = $r_denom_expected - $r_denom_shred - $r_denom_unfit;
								$r_shred_total = $r_shred_total + $r_denom_shred;
								$r_fit_total = $r_fit_total + $r_denom_fit;
								$r_shred_total_sum = $r_shred_total_sum + $r_shred_total * $r_denom_value;
								$r_fit_total_sum = $r_fit_total_sum + $r_fit_total * $r_denom_value;
								?>
								<tr>
									<td align="middle"><?php echo $r_denom_label; ?></td>
										<td><?php echo ($r_denom_fit !=0) ? $r_denom_fit : ''; ?></td>
										<td><?php echo ($r_denom_unfit !=0) ? $r_denom_unfit : ''; ?></td>
										<td><?php echo ($r_denom_shred !=0) ? $r_denom_shred : ''; ?></td>
										<td><?php echo ($r_denom_expected !=0) ? $r_denom_expected : ''; ?></td>
										<td><?php echo ($r_denom_expected * $r_denom_value !=0) ? $r_denom_expected * $r_denom_value : ''; ?></td>
								</tr>
								<?php
							};
							?>
							<tr>
								<td align="middle">Итого листов</td>
								<td><?php echo ($r_fit_total !=0) ? $r_fit_total : ''; ?></td>
								<td><?php echo ($r_unfit_total !=0) ? $r_unfit_total : ''; ?></td>
								<td><?php echo ($r_shred_total !=0) ? $r_shred_total : ''; ?></td>
								<td><?php echo ($r_expected_total !=0) ? $r_expected_total : ''; ?></td>
								<td style='text-align:center'>—</td>
							</tr>
							<tr>
								<td align="middle">Итого рублей</td>
								<td><?php echo ($r_fit_total_sum !=0) ? $r_fit_total_sum : ''; ?></td>
								<td><?php echo ($r_unfit_total_sum !=0) ? $r_unfit_total_sum : ''; ?></td>
								<td><?php echo ($r_shred_total_sum !=0) ? $r_shred_total_sum : ''; ?></td>
								<td style='text-align:center'>—</td>
								<td><?php echo ($r_expected_total_sum !=0) ? $r_expected_total_sum : ''; ?></td>
							</tr>
						</table><br/>
							<?php
						};
				};
                                include 'app/view/reports/page_divider.php';
                        ?>
                                    				
		<!--- //Rollup accounting data per indexes, denoms  //-->
				<span style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt;">Суммарные данные по аппаратно-программному комплексу --------------------------------------</span><br/>
				<?php
				// Define the list of indexes
				$rows = get_array_from_sql('
					SELECT DISTINCT DepositIndexId
					FROM DepositRecs
					WHERE DepositRecId IN ('.implode(',',$recsid).');');
				$r_indexes = array();
				foreach ($rows as $row) {
					$r_indexes[] = $row[0];
				};
				// For each index define the list of denoms that will be reported
				$i=0;
				foreach ($r_indexes as $r_index) {
					$i++;
					$rows = get_array_from_sql('
						SELECT DISTINCT DenomId
						FROM DepositDenomTotal 
						INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositDenomTotal.DepositReclId
						WHERE DepositDenomTotal.DepositReclId IN ('.implode(',',$recsid).')
							AND DepositRecs.DepositIndexId = '.$r_index.'
							AND ExpectedCount > 0;
						');
					$r_denoms = array();
					foreach ($rows as $row) {
						$r_denoms[] = $row[0];
					};
					$row = fetch_row_from_sql('
						SELECT IndexValue FROM DepositIndex WHERE DepositIndexId = '.$r_index.';');
					$index_value = $row[0];
					?>
					<span style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt;">Индекс валют № <?php echo $index_value; ?></span>
					<table id="count" border="1" cellspacing=0 cellpadding=1>
						<tr>
							<th width=15.2% rowspan=2 align="middle">Номинал</th>
							<th width=21.6% colspan=2 align="middle">Годные</th>
							<th width=21.6% colspan=2 align="middle">Ветхие</th>
							<th width=21.6% colspan=2 align="middle">Уничтоженные</th>
							<th width=21.6% colspan=2 align="middle">Всего обработано и уничтожено</th>
					   </tr>
					   <tr>
							<th width=9% align="middle">Количество листов</th>
							<th width=12.0% align="middle">Сумма рублей</th>
							<th width=9% align="middle">Количество листов</th>
							<th width=12.0% align="middle">Сумма рублей</th>
							<th width=9% align="middle">Количество листов</th>
							<th width=12.0% align="middle">Сумма рублей</th>
							<th width=9% align="middle">Количество листов</th>
							<th width=12.0% align="middle">Сумма рублей</th>
						</tr>
					<?php
					// Initialize counters for totals per an index per a machine
					$r_fit_total = 0;
					$r_unfit_total = 0;
					$r_shred_total = 0;
					$r_expected_total = 0;
					$r_fit_total_sum = 0;
					$r_unfit_total_sum = 0;
					$r_shred_total_sum = 0;
					$r_expected_total_sum = 0;
					foreach ($r_denoms as $r_denom)	{
						// For each denom (within index within machine) rollup statistics
						// Get the denom label, denom value and expected number of notes
						$row = fetch_row_from_sql('
							SELECT DenomLabel, Value, SUM(ExpectedCount)
							FROM DepositDenomTotal 
							INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
							WHERE DepositDenomTotal.DepositReclId IN ('.implode(',',$recsid).')
							AND DepositIndexId = '.$r_index.'
							GROUP BY DepositDenomTotal.DenomId
							HAVING DepositDenomTotal.DenomId = '.$r_denom.';
						');
						$r_denom_label = $row[0];
						$r_denom_value = $row[1];
						$r_denom_expected = $row[2];
						$r_expected_total = $r_expected_total + $r_denom_expected;
						$r_expected_total_sum = $r_expected_total_sum + $r_expected_total * $r_denom_value;
						// Get unfit number of notes
						$row = fetch_row_from_sql('
							SELECT 
								SUM(sorter.sorter_count),
								SUM(recon.CullCount)
							FROM (
								SELECT DepositReclId, DenomId, ExpectedCount
								FROM DepositDenomTotal
								INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
								WHERE ExpectedCount <>0 AND DepositRecs.DepositIndexId = '.$r_index.'
									AND DepositReclId IN ('.implode(',',$recsid).')) as expected
							LEFT JOIN (
								SELECT 
									DepositRecs.DepositRecId as dri,
									Valuables.DenomId as di, 
									SUM(SorterAccountingData.ActualCount
									) as sorter_count
								FROM SorterAccountingData
								INNER JOIN Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
								INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId=DepositRuns.DepositRunId
								INNER JOIN DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
								INNER JOIN ValuablesGrades ON DepositRecs.ScenarioId=ValuablesGrades.ScenarioId
									AND SorterAccountingData.ValuableId=ValuablesGrades.ValuableId
								INNER JOIN Grades ON Grades.GradeId=ValuablesGrades.GradeId
									WHERE GradeName="UNFIT"
								GROUP BY dri, di
								) as sorter
							ON expected.DepositReclId=sorter.dri AND expected.DenomId=sorter.di
							LEFT JOIN (
								SELECT 
									DepositRecId, 
									DenomId, 
									CullCount
								FROM ReconAccountingData 
								INNER JOIN Grades ON Grades.GradeId=ReconAccountingData.GradeId
									WHERE GradeName="UNFIT"
									) as recon
							ON expected.DepositReclId=recon.DepositRecId 
							AND expected.DenomId=recon.DenomId
							GROUP BY expected.DenomId
							HAVING expected.DenomId="'.addslashes($r_denom).'";');
						$r_denom_unfit = $row[0] + $row[1];
						$r_denom_unfit_sum = $r_denom_unfit * $r_denom_value;
						$r_unfit_total = $r_unfit_total + $r_denom_unfit;
						$r_unfit_total_sum = $r_unfit_total_sum + $r_unfit_total * $r_denom_value;
						// Get shred number of notes
						$row = fetch_row_from_sql('
							SELECT 
								SUM(sorter.sorter_count),
								SUM(recon.CullCount)
							FROM (
								SELECT DepositReclId, DenomId, ExpectedCount
								FROM DepositDenomTotal
								INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
								WHERE ExpectedCount <>0 AND DepositRecs.DepositIndexId = '.$r_index.'
									AND DepositReclId IN ('.implode(',',$recsid).')) as expected
							LEFT JOIN (
								SELECT 
									DepositRecs.DepositRecId as dri,
									Valuables.DenomId as di, 
									SUM(SorterAccountingData.ActualCount
									) as sorter_count
								FROM SorterAccountingData
								INNER JOIN Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
								INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId=DepositRuns.DepositRunId
								INNER JOIN DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
								INNER JOIN ValuablesGrades ON DepositRecs.ScenarioId=ValuablesGrades.ScenarioId
									AND SorterAccountingData.ValuableId=ValuablesGrades.ValuableId
								INNER JOIN Grades ON Grades.GradeId=ValuablesGrades.GradeId
									WHERE GradeName="SHRED"
								GROUP BY dri, di
								) as sorter
							ON expected.DepositReclId=sorter.dri AND expected.DenomId=sorter.di
							LEFT JOIN (
								SELECT 
									DepositRecId, 
									DenomId, 
									CullCount
								FROM ReconAccountingData 
								INNER JOIN Grades ON Grades.GradeId=ReconAccountingData.GradeId
									WHERE GradeName="SHRED"
									) as recon
							ON expected.DepositReclId=recon.DepositRecId 
							AND expected.DenomId=recon.DenomId AND expected.DenomId=recon.DenomId
							GROUP BY expected.DenomId
							HAVING expected.DenomId="'.addslashes($r_denom).'";');
						$r_denom_shred = $row[0] + $row[1];
						$r_denom_shred_sum = $r_denom_shred * $r_denom_value;
						$r_denom_fit = $r_denom_expected - $r_denom_shred - $r_denom_unfit;
						$r_denom_fit_sum = $r_denom_fit * $r_denom_value;
						$r_shred_total = $r_shred_total + $r_denom_shred;
						$r_fit_total = $r_fit_total + $r_denom_fit;
						$r_shred_total_sum = $r_shred_total_sum + $r_shred_total * $r_denom_value;
						$r_fit_total_sum = $r_fit_total_sum + $r_fit_total * $r_denom_value;
						?>
						<tr>
							<td align="middle"><?php echo $r_denom_label; ?></td>
								<td><?php echo ($r_denom_fit !=0) ? $r_denom_fit: ''; ?></td>
								<td><?php echo ($r_denom_fit_sum !=0) ? $r_denom_fit_sum: ''; ?></td>
								<td><?php echo ($r_denom_unfit !=0) ? $r_denom_unfit: ''; ?></td>
								<td><?php echo ($r_denom_unfit_sum !=0) ? $r_denom_unfit_sum: ''; ?></td>
								<td><?php echo ($r_denom_shred !=0) ? $r_denom_shred: ''; ?></td>
								<td><?php echo ($r_denom_shred_sum !=0) ? $r_denom_shred_sum: ''; ?></td>
								<td><?php echo ($r_denom_expected !=0) ? $r_denom_expected: ''; ?></td>
								<td><?php echo ($r_denom_expected * $r_denom_value !=0) ? $r_denom_expected * $r_denom_value : ''; ?></td>
						</tr>
						<?php
					};
							?>
							<tr>
								<td align="middle">Итого листов</td>
								<td><?php echo ($r_fit_total !=0) ? $r_fit_total: ''; ?></td>
								<td style='text-align:center'>—</td>
								<td><?php echo ($r_unfit_total !=0) ? $r_unfit_total: ''; ?></td>
								<td style='text-align:center'>—</td>
								<td><?php echo ($r_shred_total !=0) ? $r_shred_total: ''; ?></td>
								<td style='text-align:center'>—</td>
								<td><?php echo ($expected_total !=0) ? $expected_total: ''; ?></td>
								<td style='text-align:center'>—</td>
							</tr>
							<tr>
								<td align="middle">Итого рублей</td>
								<td style='text-align:center'>—</td>
								<td><?php echo ($r_fit_total_sum !=0) ? $r_fit_total_sum: ''; ?></td>
								<td style='text-align:center'>—</td>
								<td><?php echo ($r_unfit_total_sum !=0) ? $r_unfit_total_sum: ''; ?></td>
								<td style='text-align:center'>—</td>
								<td><?php echo ($r_shred_total_sum !=0) ? $r_shred_total_sum: ''; ?></td>
								<td style='text-align:center'>—</td>
								<td><?php echo ($r_expected_total_sum !=0) ? $r_expected_total_sum: ''; ?></td>
							</tr>
						</table>
					<?php
					if ($i < count($r_indexes)) {echo '<br/>';}
				};
                                include 'app/view/reports/page_divider.php';
                                    
                            ?>
		<!--------  Rollup discrepancies --------------------------------------------------------------------->
				<span style="font-family: 'Times New Roman', serif; font-weight: normal; font-size: 12pt;">Данные о кассовых просчётах ---------------------------------------------------------</span><br/><br/>
				<?php
                 // Define Indexes where a discrepancy or SUSPECT was found
				$rows = get_array_from_sql('
					SELECT DISTINCT DepositIndexId
					FROM DepositRecs
					WHERE DepositRecId IN ('.implode(',',$recsid).')
					AND IsBalanced = 0;');
				$r_indexes = array();
				foreach ($rows as $row) {
					$r_indexes[] = $row[0];
				};
				$i=0;
				foreach ($r_indexes as $r_index) {
					$i++;
					$row = fetch_row_from_sql('
						SELECT IndexValue FROM DepositIndex WHERE DepositIndexId = '.$r_index.';');
					$index_value = $row[0];
					?>
					 Индекс валют № <?php echo $index_value; ?>
					<table id="count" border="1" cellspacing=0 cellpadding=1>
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
					//For each index where a discrepancy was found define the denoms
					$rows = get_array_from_sql('
						SELECT DISTINCT expected.DenomId
							FROM 
							(SELECT DepositReclId, DenomId, ExpectedCount
							FROM DepositDenomTotal
							INNER JOIN DepositRecs 
								ON DepositRecs.DepositRecId=DepositDenomTotal.DepositReclId
							WHERE DepositIndexId = '.$r_index.'
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
							WHERE DepositIndexId = '.$r_index.'
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
							SELECT Value, DenomLabel FROM Denoms WHERE DenomId = '.$r_denom.';');
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
								AND IsBalanced = 0
								AND DenomId = '.$r_denom.') as expected
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
								AND DepositRecs.DepositIndexId = '.$r_index.'
								AND Valuables.DenomId = '.$r_denom.'
								AND IsBalanced = 0
								GROUP BY dri, di) as sorter
								ON expected.DepositReclId=sorter.dri
								AND expected.DenomId=sorter.di
							LEFT JOIN (
								SELECT DepositRecId, DenomId, SUM(CullCount) as CullCount
								FROM ReconAccountingData
								WHERE DepositRecId IN ('.implode(',',$recsid).')
								AND DenomId = '.$r_denom.'
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
								AND DepositIndexId = '.$r_index.'
								AND IsBalanced = 0
								AND DenomId = '.$r_denom.') as expected
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
								AND DepositIndexId = '.$r_index.'
								AND Valuables.DenomId = '.$r_denom.'
								AND IsBalanced = 0
								GROUP BY dri, di) as sorter
								ON expected.DepositReclId=sorter.dri
								AND expected.DenomId=sorter.di
							LEFT JOIN (
								SELECT DepositRecId, DenomId, SUM(CullCount) as CullCount
								FROM ReconAccountingData
								WHERE DepositRecId IN ('.implode(',',$recsid).')
								AND DenomId = '.$r_denom.'
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
							AND IsBalanced = 0
							AND DepositIndexId = '.$r_index.'
							AND DenomId = '.$r_denom.'
							AND GradeName = "SUSPECT"
						;');
						$suspect_count = $row[0];
						$suspect_sum = $suspect_count * $denom_value;
						$suspect_total = $suspect_total + $suspect_count;
						$suspect_total_sum = $suspect_total_sum + $suspect_sum;
						?>
						   <tr>
								<td><?php echo $denom_label;?></th>
								<td><?php echo ($over_count !=0) ? $over_count: ''; ?></th>
								<td><?php echo ($over_sum !=0) ? $over_sum: ''; ?></th>
								<td><?php echo ($short_count !=0) ? $short_count: ''; ?></th>
								<td><?php echo ($short_sum !=0) ? $short_sum: ''; ?></th>
								<td><?php echo ($suspect_count !=0) ? $suspect_count: ''; ?></th>
								<td><?php echo ($suspect_sum !=0) ? $suspect_sum: ''; ?></th>
							</tr>
						<?php
					};	
					?>
					   <tr>
							<td>Итого листов</td>
							<td><?php echo ($over_total !=0) ? $over_total: ''; ?></td>
							<td style='text-align:center'>—</td>
							<td><?php echo ($short_total !=0) ? $short_total: ''; ?></td>
							<td style='text-align:center'>—</td>
							<td><?php echo ($suspect_total !=0) ? $suspect_total: ''; ?></td>
							<td style='text-align:center'>—</td>
						</tr>
					   <tr>
							<td>Итого рублей</td>
							<td style='text-align:center'>—</td>
							<td><?php echo ($over_total_sum !=0) ? $over_total_sum: ''; ?></td>
							<td style='text-align:center'>—</td>
							<td><?php echo ($short_total_sum !=0) ? $short_total_sum: ''; ?></td>
							<td style='text-align:center'>—</td>
							<td><?php echo ($suspect_total_sum !=0) ? $suspect_total_sum: ''; ?></td>
						</tr>
					</table>
					<br/>
					<?php
					if ($i < count($r_indexes)) {echo '<br/>';}	
				};
            $signers = get_report_signers();
?>

            <!-- Пример вставки имени котролера сгенерившего отчет -->
            <table  id="signatures" style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
            <tr>
                <td style=' width: 60mm;'>Контролер</td>
                <td style=' width: 40mm; border-bottom-width: 1px; border-bottom-style: solid;'>&nbsp;</td>
                <td style=' width: 71mm; text-align: left;'>
                    <?php echo htmlfix(get_short_iof_by_user_id($_SESSION[$program]['UserConfiguration']['UserId'])); ?>
                </td>
            </tr>
            <?php
            
                foreach ($signers as $signer) {
                    ?>
                        <tr>
                            <td style=' width: 60mm;'><?php echo htmlfix($signer[0]); ?></td>
                            <td style=' width: 40mm; border-bottom-width: 1px; border-bottom-style: solid;'>&nbsp;</td>
                            <td style=' width: 71mm; text-align: left;'><?php echo htmlfix($signer[1]); ?></td>
                        </tr>
                    <?php
                };
            ?>
        </table>
        <div class="container no-print" style="height: 120px;">
        </div>
        <?php
            break;
    };
    
?>

