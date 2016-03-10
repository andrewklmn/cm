<?php

    /*
     * Отчёт в разрезе клиентов
     * Отчёт пригоден для любого сценария и их комбинации
    */
    if (!isset($c)) exit;
    
    switch ($action_name) {
        case 'data_prepare':
            // Эта часть выполняется во время запроса параметров для генерации отчетов


            break;
        case 'data_report':
            // Эта часть выполняется при генерации отчета на экран
				$r_site = fetch_assoc_row_from_sql('
					SELECT 
						CashCenterName, 
						CashCenterCode
					FROM SystemGlobals 
						WHERE SystemGlobalsId = "1"
					;');            
			$row = fetch_assoc_row_from_sql('
					SELECT CashRoomName FROM CashRooms 
						WHERE Id = "'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
				;');            
            $r_cash_room = $row['CashRoomName'];
			$report_date = date("Y.m.d H:i:s");
			$r_grade_total_count = array();
			$r_grade_total_sum = array();
			?>
			<table border = 0 style="width:171mm;">
				<tr>
					<td align="center" valign="middle">
						<?php echo $r_site['CashCenterName']; ?><br/>
						Касса пересчёта <?php echo $r_cash_room; ?><br/>
						Итоговый отчёт за <?php echo $report_date; ?><br/>
					</td>
				</tr>
			</table>
			<?php
			// Определениа массива с индексами DepositRecs, участвующих в формировании отчёта
			$recsid = array();
			foreach ($recs as $rec) {
				$recsid[] = $rec[0];
				};			
			// Define the customers whose money was processed during the period
			$rows = get_array_from_sql('
				SELECT DISTINCT CustomerId
				FROM DepositRecs
				WHERE DepositRecId IN ('.implode(',',$recsid).')
				;');
			$r_customers = array();
			foreach ($rows as $row) {
				$r_customers[] = $row[0];
				};
			foreach ($r_customers as $r_customer) {	
				if ($r_customer == '') {
					$rows = get_array_from_sql('
						SELECT DepositRecId
						FROM DepositRecs
						WHERE CustomerId IS NULL
						;');
					echo 'Клиент не выбран';
				} else {
					$rows = get_array_from_sql('
						SELECT DepositRecId
						FROM DepositRecs
						WHERE CustomerId = '.$r_customer.'
						;');
					$r_customer_details = fetch_assoc_row_from_sql('
						SELECT CustomerName, CustomerCode
						FROM Customers
						WHERE CustomerId = '.$r_customer.'
						;');
					echo $r_customer_details['CustomerName'].'.&nbsp;Код клиента '.$r_customer_details['CustomerCode'].'<br/>';
				};
				$r_c_recs = array();
				foreach ($rows as $row) {
				$r_c_recs[] = $row[0];
				};
				?>
				<table>
				<?php
				// Define all grades that were used during the reporting period
				$rows = get_array_from_sql('
					SELECT DISTINCT GradeId
					FROM DepositRecs
						INNER JOIN DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
						INNER JOIN SorterAccountingData ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
						INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
						AND ValuablesGrades.ScenarioId = DepositRecs.ScenarioId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
					UNION
					SELECT DISTINCT GradeId
					FROM DepositRecs
						INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
					ORDER BY GradeId
					;');
				$r_grades = array();
				foreach ($rows as $row) {
					$r_grades[] = $row[0];				
				};
				$r_g = count($r_grades);
				?>
				<table id="summery" style="font-family: 'Times New Roman', serif; font-size: 10pt; font-weight: normal;" border=1>
				<?php
				// Define currencies processed 
				$rows = get_array_from_sql('
					SELECT DISTINCT CurrencyId
					FROM SorterAccountingData
						INNER JOIN DepositRuns
						ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
						INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
						INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
						INNER JOIN Denoms ON Valuables.DenomId = Denoms.DenomId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
					UNION
					SELECT DISTINCT CurrencyId
					FROM DepositRecs
						INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
						INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
					UNION
					SELECT DISTINCT CurrencyId
					FROM DepositDenomTotal
						INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
						INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
						WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
						AND ExpectedCount > 0
					;');
				$r_currencies = array();
				foreach ($rows as $row) {
				$r_currencies[] = $row[0];				
				};
				foreach ($r_currencies as $r_currency) {
					$row = fetch_row_from_sql('
						SELECT CurrName, CurrSymbol, CurrYear
						FROM Currency
						WHERE CurrencyId = '.$r_currency.'
						;');
						$r_curr_name = $row[0];
						$r_curr_symbol = $row[1];
						$r_curr_year = ($row[2]) ? '<br/>образца '.$row[2].' года' : '';
					?>
						<tr>
							<td rowspan=2 align="center"><?php echo $r_curr_name.$r_curr_year; ?></td>
							<td colspan=2 align="center">ЗАЯВЛЕНО</td>
					<?php
					foreach ($r_grades as $r_grade) {
						$row = fetch_row_from_sql('
							SELECT GradeLabel FROM Grades
							WHERE GradeId = '.$r_grade.';');
						$r_grade_label = $row[0];
					$r_grade_total_count[$r_grade] = 0;
					$r_grade_total_sum[$r_grade] = 0;
						echo '<td colspan=2 align="center">'.$r_grade_label.'</td>';
					};
					?>		
							<td colspan=2 align="center">ВСЕГО</td>
							<td colspan=2 align="center">ИЗЛИШКИ</td>
							<td colspan=2 align="center">НЕДОСТАЧИ</td>
						</tr>
							<td align="center">кол-во</td>
							<td align="center">сумма</td>
					<?php
					foreach ($r_grades as $r_grade) {
						?>
							<td align="center">кол-во</td>
							<td align="center">сумма</td>
						<?php
					};
					?>
							<td align="center">кол-во</td>
							<td align="center">сумма</td>
							<td align="center">кол-во</td>
							<td align="center">сумма</td>
							<td align="center">кол-во</td>
							<td align="center">сумма</td>
						</tr>
					<?php
					// Define denoms processed 
					$rows = get_array_from_sql('
						SELECT DISTINCT Denoms.DenomId, Value
						FROM SorterAccountingData
							INNER JOIN DepositRuns
							ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
							INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
							INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
							INNER JOIN Denoms ON Valuables.DenomId = Denoms.DenomId
						WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
							AND CurrencyId = '.$r_currency.'
						UNION
						SELECT DISTINCT Denoms.DenomId, Value
						FROM DepositRecs
							INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
							INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
						WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
							AND CurrencyId = '.$r_currency.'
						UNION
						SELECT DISTINCT Denoms.DenomId, Value
						FROM DepositDenomTotal
							INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
							WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
							AND ExpectedCount > 0
							AND CurrencyId = '.$r_currency.'
						ORDER BY Value
						;');
					$r_denoms = array();
					foreach ($rows as $row) {
						$r_denoms[] = $row[0];				
					};
					$r_curr_expected_count = 0;
					$r_curr_expected_sum = 0;
					$r_curr_processed_count = 0;
					$r_curr_processed_sum = 0;
					$r_curr_over_count = 0;
					$r_curr_over_sum = 0;
					$r_curr_short_count = 0;
					$r_curr_short_sum = 0;
						foreach ($r_denoms as $r_denom) {
							$row = fetch_row_from_sql('
								SELECT DenomLabel, Value
								FROM Denoms
								WHERE DenomId = '.$r_denom.'
								;');
							$r_denom_label = $row[0];
							$r_denom_value = $row[1];
							$r_denom_count = 0;
							$r_denom_sum = 0;
							?>
							<tr>
								<td align="right"><?php echo $r_denom_value; ?></td>
							<?php
							// Define the number and sum of expected notes
							$row = fetch_row_from_sql('
								SELECT SUM(ExpectedCount)
								FROM DepositDenomTotal
								WHERE DepositReclId IN ('.implode(',',$r_c_recs).')
								AND DenomId = '.$r_denom.'
								;');
							$r_denom_expected_count = $row[0];
							$r_denom_expected_sum = $row[0] * $r_denom_value;
							$r_curr_expected_count = $r_curr_expected_count + $r_denom_expected_count;
							$r_curr_expected_sum = $r_curr_expected_sum + $r_denom_expected_sum;
							?>
								<td align="right"><?php echo ($r_denom_expected_count!=0) ? $r_denom_expected_count:''; ?></td>
								<td align="right"><?php echo ($r_denom_expected_count!=0) ? $r_denom_expected_sum:''; ?></td>
							<?php
							$r_total = 0;
							foreach ($r_grades as $r_grade) {
								$row = fetch_row_from_sql('
									SELECT coalesce(SUM(ActualCount),0)
									FROM SorterAccountingData
										INNER JOIN DepositRuns ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
										INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
										INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
										INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = Valuables.ValuableId
											AND ValuablesGrades.ScenarioId = DepositRecs.ScenarioId
									WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
										AND Valuables.DenomId = '.$r_denom.'
										AND ValuablesGrades.GradeId = '.$r_grade.'
									;');
								$r_grade_count = $row[0];
								$row = fetch_row_from_sql('
									SELECT COALESCE(SUM(CullCount),0)
									FROM ReconAccountingData
										INNER JOIN DepositRecs ON ReconAccountingData.DepositRecId = DepositRecs.DepositRecId
									WHERE DepositRecs.DepositRecId IN ('.implode(',',$r_c_recs).')
										AND DenomId = '.$r_denom.'
										AND GradeId = '.$r_grade.'
									;');
								$r_grade_count = $r_grade_count + $row[0];
								$r_total = $r_total + $r_grade_count;
								$r_grade_total_count[$r_grade] = $r_grade_total_count[$r_grade] + $r_grade_count;
								$r_grade_total_sum[$r_grade] = $r_grade_total_sum[$r_grade] + $r_grade_count * $r_denom_value;
								?>
								<td align="right"><?php echo ($r_grade_count != 0)? $r_grade_count:''; ?></td>
								<td align="right"><?php echo ($r_grade_count != 0)? $r_grade_count * $r_denom_value:''; ?></td>
								<?php
								
						}; // End of grades rollup	
						$r_curr_processed_count = $r_curr_processed_count + $r_total;
						$r_curr_processed_sum = $r_curr_processed_sum + $r_total * $r_denom_value;
						?>
						<td align="right"><?php echo ($r_total != 0) ? $r_total : ''; ?></td>
						<td align="right"><?php echo ($r_total != 0) ? $r_total * $r_denom_value : ''; ?></td>
						<?php
						$row = fetch_row_from_sql('
							SELECT 
								SUM(-expected.ExpectedCount+COALESCE(sorter.s_count,0)+COALESCE(recon.r_count,0))
							FROM (
								SELECT DepositReclId, ExpectedCount
								FROM DepositDenomTotal
								WHERE DepositReclId IN ('.implode(',',$r_c_recs).')
									AND DenomId = '.$r_denom.') as expected
							LEFT JOIN (
								SELECT DepositRecId, SUM(ActualCount) as s_count
								FROM SorterAccountingData
								INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
								INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
								WHERE DepositRecId IN ('.implode(',',$r_c_recs).')
								GROUP BY DepositRecId, DenomId
								HAVING DenomId = '.$r_denom.') as sorter
							ON expected.DepositReclId = sorter.DepositRecId
							LEFT JOIN (
								SELECT DepositRecId, SUM(CullCount) as r_count
								FROM ReconAccountingData
								WHERE DepositRecId IN ('.implode(',',$r_c_recs).')
								GROUP BY DepositRecId, DenomId
								HAVING DenomId = '.$r_denom.') as recon
							ON expected.DepositReclId = recon.DepositRecId
							WHERE expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)<0
							;');
						$r_curr_over_count = $r_curr_over_count + $row[0];
						$r_curr_over_sum = $r_curr_over_sum + $row[0] * $r_denom_value;
						?>
						<td align="right"><?php echo ($row[0]!=0)?$row[0]:''; ?></td>
						<td align="right"><?php echo ($row[0]!=0)?$row[0] * $r_denom_value:''; ?></td>
						<?php
						$row = fetch_row_from_sql('
							SELECT 
								COALESCE(SUM(expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)), 0)
							FROM (
								SELECT DepositReclId, ExpectedCount
								FROM DepositDenomTotal
								WHERE DepositReclId IN ('.implode(',',$r_c_recs).')
									AND DenomId = '.$r_denom.') as expected
							LEFT JOIN (
								SELECT DepositRecId, SUM(ActualCount) as s_count
								FROM SorterAccountingData
								INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
								INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
								WHERE DepositRecId IN ('.implode(',',$r_c_recs).')
								GROUP BY DepositRecId, DenomId
								HAVING DenomId = '.$r_denom.') as sorter
							ON expected.DepositReclId = sorter.DepositRecId
							LEFT JOIN (
								SELECT DepositRecId, SUM(CullCount) as r_count
								FROM ReconAccountingData
								WHERE DepositRecId IN ('.implode(',',$r_c_recs).')
								GROUP BY DepositRecId, DenomId
								HAVING DenomId = '.$r_denom.') as recon
							ON expected.DepositReclId = recon.DepositRecId
							WHERE expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)>0
							;');
						$r_curr_short_count = $r_curr_short_count + $row[0];
						$r_curr_short_sum = $r_curr_short_sum + $row[0] * $r_denom_value;
						?>
						<td align="right"><?php echo ($row[0]!=0) ? $row[0]:''; ?></td>
						<td align="right"><?php echo ($row[0]!=0) ? $row[0] * $r_denom_value:''; ?></td>
						</tr>
						<?php

					}; // End of denom rollup
					?>
					<tr>
						<td align="right">ИТОГО:</td>
						<td align="right"><?php echo ($r_curr_expected_count!=0) ? $r_curr_expected_count:''; ?></td>
						<td align="right"><?php echo ($r_curr_expected_sum!=0) ? $r_curr_expected_sum:''; ?></td>
					<?php
					foreach ($r_grades as $r_grade) {
						?>
					<td align="right"><?php echo ($r_grade_total_count[$r_grade]!=0) ? $r_grade_total_count[$r_grade]:''; ?></td>
					<td align="right"><?php echo ($r_grade_total_sum[$r_grade]!=0) ? $r_grade_total_sum[$r_grade]:''; ?></td>
						<?php
					};
					?>	
						<td align="right"><?php echo ($r_curr_processed_count!=0) ? $r_curr_processed_count:''; ?></td>
						<td align="right"><?php echo ($r_curr_processed_sum!=0)? $r_curr_processed_sum:''; ?></td>
						<td align="right"><?php echo ($r_curr_over_count!=0) ? $r_curr_over_count:''; ?></td>
						<td align="right"><?php echo ($r_curr_over_sum!=0) ? $r_curr_over_sum:''; ?></td>
						<td align="right"><?php echo ($r_curr_short_count!=0) ? $r_curr_short_count:''; ?></td>
						<td align="right"><?php echo ($r_curr_short_sum!=0) ? $r_curr_short_sum:''; ?></td>
					</tr>
					<?php
				}; // End of currency rollup
				?>
				</table>
				<?php
			}; // End of customers rollup
			?>
			<br/><br/>
			<?php
            break;
    };
    
?>

