<?php

    /*
     * Отчёт в разрезе машин, кассиров сверки, свободный от некоторых недостатков
     * Отчёт пригоден для любого сценария и их комбинации
     * Раскладывает по типам ценностей (банкнотам и монетам)
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
			// Define the array of DepositRecs, that must be reflected in the report
			$recsid = array();
			foreach ($recs as $rec) {
				$recsid[] = $rec[0];				
			};
			
			// Define machine indexes that were engaged in operation for the period
			$r_machines = get_assoc_array_from_sql('
				SELECT 
					DISTINCT DepositRuns.MachineDBId as MachineDBId, SorterName
				FROM 
					DepositRecs 
					INNER JOIN DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
					INNER JOIN Machines ON Machines.MachineDBId = DepositRuns.MachineDBId
				WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
				ORDER BY SorterName;');
			$r_i = 0;
			foreach ($r_machines as $r_machine) {
				$r_i ++; //Count of machine blocks
				// Define the sorter grades that were used during the reporting period by the selected machine
				$rows = get_array_from_sql('
					SELECT DISTINCT GradeId
					FROM DepositRecs
						INNER JOIN DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
						INNER JOIN SorterAccountingData ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
						INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
						AND ValuablesGrades.ScenarioId = DepositRecs.ScenarioId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
						AND MachineDBId = '.$r_machine["MachineDBId"].'
					ORDER BY GradeId
					;');
				$r_grades = array();
				foreach ($rows as $row) {
					$r_grades[] = $row[0];				
				};
				// Define the list of operators that run each machine (they might be more than one per a machine)
				$rows = get_array_from_sql('
					SELECT DISTINCT OperatorName
					FROM DepositRuns
						INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
					WHERE MachineDBId = '.$r_machine['MachineDBId'].'
						AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).');');
				$r_operators = array();
				foreach ($rows as $row) {
					$r_operators[] = $row[0];				
				};
				?>
				Машина № <?php echo $r_machine['SorterName']; ?><br/>
				Операторы: <?php echo implode(',', $r_operators).'<br/>'; ?>
				<table border = 1 style="width:171mm; border: 1px solid black;">
					<tr>
				<?php
				// Define currencies processed on the selected machine
				$rows = get_array_from_sql('
					SELECT DISTINCT CurrencyId
					FROM SorterAccountingData
						INNER JOIN DepositRuns
						ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
						INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
						INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
						INNER JOIN Denoms ON Valuables.DenomId = Denoms.DenomId
					WHERE MachineDBId = '.$r_machine['MachineDBId'].'
					AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
					ORDER BY Denoms.DenomId
					;');
				$r_currencies = array();
				foreach ($rows as $row) {
				$r_currencies[] = $row[0];				
				};
				$i = 0;
				foreach ($r_currencies as $r_currency) {
					$i ++;
					$row = fetch_row_from_sql('
						SELECT CurrName, CurrSymbol, CurrYear
						FROM Currency
						WHERE CurrencyId = '.$r_currency.'
						;');
						$r_curr_name = $row[0];
						$r_curr_symbol = $row[1];
						$r_curr_year = ($row[2]) ? '<br/>образца '.$row[2].' года' : '';
						$r_curr_processed_count = 0;
						$r_curr_processed_sum = 0;
					?>
						<tr>
							<td align="center"><?php echo $r_curr_name.$r_curr_year; ?></td>
					<?php
					foreach ($r_grades as $r_grade) {
						$row = fetch_row_from_sql('
							SELECT GradeLabel FROM Grades
							WHERE GradeId = '.$r_grade.';');
						$r_grade_label = $row[0];
						$r_grade_total_count[$r_grade] = 0;
						echo '<td rowspan=2 align="center">'.$r_grade_label.'<br/>количество</td>';
					};
					?>
							<td colspan=2 align="center">ВСЕГО</td>
						</tr>
						<tr>
							<td align="center">номинал</td>
							<td align="center">кол-во</td>
							<td align="center">сумма</td>
						</tr>
					<?php
					// Define the valuable types that were processed during the period by the selected machine for the selected currency
					$rows = get_array_from_sql('
						SELECT DISTINCT ValuableTypeId
						FROM DepositRuns
						INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
						INNER JOIN SorterAccountingData ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
						INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
						INNER JOIN Denoms ON Denoms.DenomId = Valuables.DenomId
						WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
						AND MachineDBId = '.$r_machine["MachineDBId"].'
						AND CurrencyId = '.$r_currency.'
						ORDER BY ValuableTypeId
						;');
					$r_valuable_types = array();
					foreach ($rows as $row) {
						$r_valuable_types[] = $row[0];				
					};
					foreach ($r_valuable_types as $r_valuable_type) {
						$row = fetch_row_from_sql('
							SELECT ValuableTypeLabel
							FROM ValuableTypes
							WHERE ValuableTypeId = '.$r_valuable_type.'
							;');
						$r_valuable_type_label = $row[0];
						?>
						<tr>
							<td colspan="<?php echo count($r_grades) + 3 ?>;" style="text-align: center"><?php echo $r_valuable_type_label; ?></td>
						</tr>
						<?php
						// Define the denoms of the selected currency and type processed on the selected machine
						$rows = get_array_from_sql('
							SELECT DISTINCT Denoms.DenomId
							FROM SorterAccountingData
								INNER JOIN DepositRuns
								ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
								INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
								INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
								INNER JOIN Denoms ON Valuables.DenomId = Denoms.DenomId
							WHERE MachineDBId = '.$r_machine['MachineDBId'].'
							AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
							AND Denoms.CurrencyId = '.$r_currency.'
							AND Valuables.ValuableTypeId='.$r_valuable_type.'
							ORDER BY Denoms.Value
							;');
						$r_denoms = array();
						foreach ($rows as $row) {
						$r_denoms[] = $row[0];				
						};
						foreach ($r_denoms as $r_denom) {
							$row = fetch_row_from_sql('
								SELECT DenomLabel, Value, CurrSymbol, CurrYear
								FROM Denoms
									INNER JOIN Currency ON Currency.CurrencyId = Denoms.CurrencyId
								WHERE DenomId = '.$r_denom.'
								;');
							$r_denom_label = $row[0];
							$r_denom_value = $row[1];
							$r_curr_symbol = $row[2];
							$r_curr_year = $row[3];
							$r_denom_count = 0;
							$r_denom_sum = 0;
							?>
							<tr>
								<td align="right"><?php echo $r_denom_label; ?></td>
							<?php
							foreach ($r_grades as $r_grade) {
								$row = fetch_row_from_sql('
									SELECT SUM(ActualCount)
									FROM SorterAccountingData
										INNER JOIN DepositRuns
										ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
										INNER JOIN DepositRecs ON DepositRuns.DepositRecId = DepositRecs.DepositRecId
										INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
										INNER JOIN ValuablesGrades ON ValuablesGrades.ScenarioId = DepositRecs.ScenarioId
											AND ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
									WHERE MachineDBId = '.$r_machine['MachineDBId'].'
									AND DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
									AND Valuables.DenomId = '.$r_denom.'
									AND GradeId = "'.$r_grade.'"
									;');
								$r_denom_count = $r_denom_count + $row[0];
								$r_grade_total_count[$r_grade] = $r_grade_total_count[$r_grade] + $row[0];
								echo '<td align="right">'.$row[0].'</td>';
							}; // End of grades rollup
							$r_denom_sum = $r_denom_sum + $r_denom_count * $r_denom_value;
							$r_curr_processed_count = $r_curr_processed_count + $r_denom_count;
							$r_curr_processed_sum = $r_curr_processed_sum + $r_denom_sum;
							?>
							<td align="right"><?php echo $r_denom_count; ?></td>
							<td align="right"><?php echo $r_denom_sum; ?></td>
							</tr>
							<?php
						}; // End of denoms rollup
					}; // End of valuable type rollup
					?>
					<tr>
						<td align="center">ИТОГО:</td>
						<?php
						foreach ($r_grades as $r_grade) {
							?>
							<td align="right"><?php echo ($r_grade_total_count[$r_grade] !=0) ? $r_grade_total_count[$r_grade]: ''; ?></td>
							<?php
						};
						?>
						<td align="right"><?php echo $r_curr_processed_count; ?></td>
						<td align="right"><?php echo $r_curr_processed_sum; ?></td>
					</tr>
					<?php
					}
				// End of currency rollup
				?>
				</table>
				<?php
				if ($r_i < count($r_machines)) {echo '<br/>';}
			};	
			// End of machines rollup
			
			// Rolling up reconciliation data per operators -----------------------------------------------------------
			// Define the reconciliation grades that were used during the reporting period
			$rows = get_array_from_sql('
				SELECT DISTINCT GradeId
				FROM DepositRecs
					INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
				WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
				ORDER BY GradeId
				;');
			$r_grades = array(); // We use the same variable (array) name as for sorter data
			foreach ($rows as $row) {
				$r_grades[] = $row[0];				
			};
			$r_g = count($r_grades);
			// Define the reconciliation operators
			$rows = get_array_from_sql('
				SELECT DISTINCT RecOperatorId
				FROM DepositRecs
					INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
				WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
				;');
			$r_operators = array(); // Again we use the same variable (array) name as for sorter data
			foreach ($rows as $row) {
				$r_operators[] = $row[0];				
			};
			foreach ($r_operators as $r_operator) {
				$r_operator_name = get_short_fio_by_user_id($r_operator);
				?>
				Оператор сверки <?php echo $r_operator_name; ?><br/>
				<table border = 1 style="width:171mm; border: 1px solid black;">
				<?php
				// Define currencies processed on the selected machine
				$rows = get_array_from_sql('
					SELECT DISTINCT CurrencyId
					FROM DepositRecs
						INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
						INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
					AND RecOperatorId = '.$r_operator.'
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
					$r_curr_processed_count = 0;
					$r_curr_processed_sum = 0;
				?>
					<tr>
						<td align="center"><?php echo $r_curr_name.$r_curr_year; ?></td>
				<?php
				foreach ($r_grades as $r_grade) {
					$row = fetch_row_from_sql('
						SELECT GradeLabel FROM Grades
						WHERE GradeId = '.$r_grade.';');
					$r_grade_label = $row[0];
					echo '<td rowspan=2 align="center">'.$r_grade_label.'<br/>количество</td>';
				};
				?>
						<td colspan=2 align="center">ВСЕГО</td>
					</tr>
					<tr>
						<td align="center">номинал</td>
						<td align="center">кол-во</td>
						<td align="center">сумма</td>
					</tr>
				<?php
				// Define the valuable types that were processed by the selected operator for the selected currency
				$rows = get_array_from_sql('
					SELECT DISTINCT ValuableTypeId
					FROM DepositRecs
					INNER JOIN ReconAccountingData ON ReconAccountingData.DepositRecId = DepositRecs.DepositRecId
					INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
					AND DepositRecs.RecOperatorId = '.$r_operator.'
					AND CurrencyId = '.$r_currency.'
					ORDER BY ValuableTypeId
					;');
				$r_valuable_types = array();
				foreach ($rows as $row) {
					$r_valuable_types[] = $row[0];				
				};
				foreach ($r_valuable_types as $r_valuable_type) {
					$row = fetch_row_from_sql('
						SELECT ValuableTypeLabel
						FROM ValuableTypes
						WHERE ValuableTypeId = '.$r_valuable_type.'
						;');
					$r_valuable_type_label = $row[0];
					?>
					<tr>
						<td colspan="<?php echo count($r_grades) + 3 ?>;" style="text-align: center"><?php echo $r_valuable_type_label; ?></td>
					</tr>
					<?php
					// Define the denoms that were processed for the currency
					$rows = get_array_from_sql('
					SELECT DISTINCT ReconAccountingData.DenomId
					FROM DepositRecs
						INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
						INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
					AND RecOperatorId = '.$r_operator.'
					AND CurrencyId = '.$r_currency.'
					AND ReconAccountingData.ValuableTypeId = '.$r_valuable_type.'
					ORDER BY Value
					;');
					$r_denoms = array();
					foreach ($rows as $row) {
					$r_denoms[] = $row[0];				
					};
						foreach ($r_denoms as $r_denom) {
							$row = fetch_row_from_sql('
								SELECT DenomLabel, Value, CurrSymbol, CurrYear
								FROM Denoms
									INNER JOIN Currency ON Currency.CurrencyId = Denoms.CurrencyId
								WHERE DenomId = '.$r_denom.'
								;');
							$r_denom_label = $row[0];
							$r_denom_value = $row[1];
							$r_curr_symbol = $row[2];
							$r_curr_year = $row[3];
							$r_denom_count = 0;
							$r_denom_sum = 0;
							?>
							<tr>
								<td align="right"><?php echo $r_denom_label; ?></td>
							<?php
							foreach ($r_grades as $r_grade) {
								$row = fetch_row_from_sql('
									SELECT SUM(CullCount)
									FROM DepositRecs
										INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
									WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
									AND RecOperatorId = '.$r_operator.'
									AND DenomId = '.$r_denom.'
									AND ReconAccountingData.ValuableTypeId = '.$r_valuable_type.'
									AND GradeId = '.$r_grade.'
									;');
								$r_denom_count = $r_denom_count + $row[0];
								echo '<td align="right">'.$row[0].'</td>';
							}; // End of grades rollup
							$r_denom_sum = $r_denom_sum + $r_denom_count * $r_denom_value;
							$r_curr_processed_count = $r_curr_processed_count + $r_denom_count;
							$r_curr_processed_sum = $r_curr_processed_sum + $r_denom_sum;
							?>
							<td align="right"><?php echo $r_denom_count; ?></td>
							<td align="right"><?php echo $r_denom_sum; ?></td>
							</tr>
							<?php
						}; // End of denom rollup
					}; // End of valuable type rollup
					?>
					<tr>
						<td align="center">ИТОГО:</td>
						<td colspan="<?php echo $r_g; ?>">&nbsp;</td>
						<td align="right"><?php echo $r_curr_processed_count; ?></td>
						<td align="right"><?php echo $r_curr_processed_sum; ?></td>
					</tr>
					<?php
				}; 
				// End of currency rollup
				?>
				</table>
				<?php
			}; 
			// End of reconciliation operator rollup
			
			
			// Summery part ----------------------------------------------------------------------------------------------
			// Define all grades that were used during the reporting period
			$rows = get_array_from_sql('
				SELECT DISTINCT GradeId
				FROM DepositRecs
					INNER JOIN DepositRuns ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
					INNER JOIN SorterAccountingData ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
					INNER JOIN ValuablesGrades ON ValuablesGrades.ValuableId = SorterAccountingData.ValuableId
					AND ValuablesGrades.ScenarioId = DepositRecs.ScenarioId
				WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
				UNION
				SELECT DISTINCT GradeId
				FROM DepositRecs
					INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
				WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
				ORDER BY GradeId
				;');
			$r_grades = array();
			foreach ($rows as $row) {
				$r_grades[] = $row[0];				
			};
			$r_g = count($r_grades);
			?>
			Сводная таблица<br/>
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
				WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
				UNION
				SELECT DISTINCT CurrencyId
				FROM DepositRecs
					INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
					INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
				WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
				UNION
				SELECT DISTINCT CurrencyId
				FROM DepositDenomTotal
					INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
					INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
					WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
					AND ExpectedCount > 0
				;');
			$r_currencies = array();
			foreach ($rows as $row) {
			$r_currencies[] = $row[0];				
			};
			$i = 0; // Counter of currency blocks
			foreach ($r_currencies as $r_currency) {
				$i ++;
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
						<td align="center"><?php echo $r_curr_name.$r_curr_year; ?></td>
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
						<td align="center">номинал</td>
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
					// Define the valuable types that were processed during the period for the selected currency
					$rows = get_array_from_sql('
						SELECT DISTINCT ValuableTypeId
						FROM DepositRuns
							INNER JOIN DepositRecs ON DepositRecs.DepositRecId = DepositRuns.DepositRecId
							INNER JOIN SorterAccountingData ON SorterAccountingData.DepositRunId = DepositRuns.DepositRunId
							INNER JOIN Valuables ON SorterAccountingData.ValuableId = Valuables.ValuableId
							INNER JOIN Denoms ON Denoms.DenomId = Valuables.DenomId
						WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
						AND CurrencyId = '.$r_currency.'
						UNION
						SELECT DISTINCT ValuableTypeId
						FROM DepositRecs
							INNER JOIN ReconAccountingData ON ReconAccountingData.DepositRecId = DepositRecs.DepositRecId
							INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
						WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
							AND CurrencyId = '.$r_currency.'
						UNION
						SELECT ValuableTypeId
						FROM DepositDenomTotal
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
						WHERE DepositReclId IN ('.implode(',',$recsid).')
							AND CurrencyId = '.$r_currency.'
						;');
					$r_valuable_types = array();
					foreach ($rows as $row) {
						$r_valuable_types[] = $row[0];				
					};
					foreach ($r_valuable_types as $r_valuable_type) {
						$row = fetch_row_from_sql('
							SELECT ValuableTypeLabel
							FROM ValuableTypes
							WHERE ValuableTypeId = '.$r_valuable_type.'
							;');
						$r_valuable_type_label = $row[0];
						?>
						<tr>
							<td colspan="<?php echo count($r_grades)*2 + 9 ?>;" style="text-align: center"><?php echo $r_valuable_type_label; ?></td>
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
						WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
							AND CurrencyId = '.$r_currency.'
							AND ValuableTypeId = '.$r_valuable_type.'
						UNION
						SELECT DISTINCT Denoms.DenomId, Value
						FROM DepositRecs
							INNER JOIN ReconAccountingData ON DepositRecs.DepositRecId = ReconAccountingData.DepositRecId
							INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
						WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
							AND CurrencyId = '.$r_currency.'
							AND ValuableTypeId = '.$r_valuable_type.'
						UNION
						SELECT DISTINCT Denoms.DenomId, Value
						FROM DepositDenomTotal
							INNER JOIN DepositRecs ON DepositDenomTotal.DepositReclId = DepositRecs.DepositRecId
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
							WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
							AND ExpectedCount > 0
							AND CurrencyId = '.$r_currency.'
							AND ValuableTypeId = '.$r_valuable_type.'
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
								<td align="right"><?php echo $r_denom_label; ?></td>
							<?php
							// Define the number and sum of expected notes
							$row = fetch_row_from_sql('
								SELECT SUM(ExpectedCount)
								FROM DepositDenomTotal
								WHERE DepositReclId IN ('.implode(',',$recsid).')
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
									WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
										AND Valuables.DenomId = '.$r_denom.'
										AND ValuablesGrades.GradeId = '.$r_grade.'
										AND ValuableTypeId = '.$r_valuable_type.'
									;');
								$r_grade_count = $row[0];
								$row = fetch_row_from_sql('
									SELECT COALESCE(SUM(CullCount),0)
									FROM ReconAccountingData
										INNER JOIN DepositRecs ON ReconAccountingData.DepositRecId = DepositRecs.DepositRecId
									WHERE DepositRecs.DepositRecId IN ('.implode(',',$recsid).')
										AND DenomId = '.$r_denom.'
										AND GradeId = '.$r_grade.'
										AND ValuableTypeId = '.$r_valuable_type.'
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
								WHERE DepositReclId IN ('.implode(',',$recsid).')
									AND ValuableTypeId = '.$r_valuable_type.'
									AND DenomId = '.$r_denom.') as expected
							LEFT JOIN (
								SELECT DepositRecId, SUM(ActualCount) as s_count
								FROM SorterAccountingData
								INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
								INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
								WHERE DepositRecId IN ('.implode(',',$recsid).')
								AND ValuableTypeId = '.$r_valuable_type.'
								GROUP BY DepositRecId, DenomId
								HAVING DenomId = '.$r_denom.') as sorter
							ON expected.DepositReclId = sorter.DepositRecId
							LEFT JOIN (
								SELECT DepositRecId, SUM(CullCount) as r_count
								FROM ReconAccountingData
								WHERE DepositRecId IN ('.implode(',',$recsid).')
								AND ValuableTypeId = '.$r_valuable_type.'
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
								WHERE DepositReclId IN ('.implode(',',$recsid).')
									AND ValuableTypeId = '.$r_valuable_type.'
									AND DenomId = '.$r_denom.') as expected
							LEFT JOIN (
								SELECT DepositRecId, SUM(ActualCount) as s_count
								FROM SorterAccountingData
								INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
								INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
								WHERE DepositRecId IN ('.implode(',',$recsid).')
								AND ValuableTypeId = '.$r_valuable_type.'
								GROUP BY DepositRecId, DenomId
								HAVING DenomId = '.$r_denom.') as sorter
							ON expected.DepositReclId = sorter.DepositRecId
							LEFT JOIN (
								SELECT DepositRecId, SUM(CullCount) as r_count
								FROM ReconAccountingData
								WHERE DepositRecId IN ('.implode(',',$recsid).')
								AND ValuableTypeId = '.$r_valuable_type.'
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
				}; // End of valuable type rollup
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
				if ($i < count($r_currencies)) {
					?>
					<tr>
						<td colspan="<?php echo ($r_g * 2 + 9); ?>">&nbsp;</td>
					</tr>
					<?php
				};
			}; // End of currency rollup
			?>
			</table>
			<br/><br/>
			<?php
			
            break;
    };
    
?>

