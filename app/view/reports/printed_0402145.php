<?php

    /*
     * Скрипт формирования набора актов 0402145 для случая отдельный акт для каждого типа расхождения и каждого номинала
     */
    if (!isset($c)) exit;
    include_once 'app/model/get_short_iof_by_user_id.php';
    $r_report_label = 'Акт формы 0402145'; // Report label is used for selecting scenarios

    switch ($action_name) {
		case 'data_prepare':
            // The following code is performed when gathering report parameters
            break;
		case 'data_report':
            // The following code is performed when printing the report on the screen
			?>
			<style>
			table#formcode {
				position:relative; 
                                top: 0mm; 
                                left: 126mm;
				width: 166px;
				text-align: center;
			}
			table#signatures td {
				height: 13mm; 
                                vertical-align: bottom; 
                                text-align: left; 
                                font-weight: normal; 
                                font-size: 12pt;
			}
			table.act145 {font-family: "Times New Roman", serif;
			}
			</style>
			<?php
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
			$date_year = date("Y");
			$d_month = date("m");
			$date_day = date("d");
			if ($d_month == 1) {
				$date_month = "января";
			} elseif ($d_month == 2) {
				$date_month = "февраля";
			} elseif ($d_month == 3) {
				$date_month = "марта";
			} elseif ($d_month == 4) {
				$date_month = "апреля";
			} elseif ($d_month == 5) {
				$date_month = "мая";
			} elseif ($d_month == 6) {
				$date_month = "июня";
			} elseif ($d_month == 7) {
				$date_month = "июля";
			} elseif ($d_month == 8) {
				$date_month = "августа";
			} elseif ($d_month == 9) {
				$date_month = "сентября";
			} elseif ($d_month == 10) {
				$date_month = "октября";
			} elseif ($d_month == 11) {
				$date_month = "ноября";
			} else{
				$date_month = "декабря";
			};
			$str_date = $date_day.' '.$date_month.' '.$date_year.' года';
			// Define the numbering of acts and assign the initial count to $act_count variable
			$row = fetch_row_from_sql('
				SELECT MAX(SetDateTime) FROM ReportSets;');
			$set_last_datetime = $row[0]; // Date and time of the last report set created
			$row = fetch_row_from_sql('
				SELECT MAX(SetDateTime)
				FROM ReportSets
				WHERE SetDateTime < "'.addslashes($date_year).'-'.addslashes($d_month).'-'.addslashes($date_day).' 00:00:00";');
			$set_before_datetime = $row[0]; // Date and time of the last report set for the previous date created
			if ($set_last_datetime != $set_before_datetime) {
				$rows = get_assoc_array_from_sql('
					SELECT ActId, DepositId, DiscrepancyKindName
					FROM Acts
					INNER JOIN DepositRecs ON DepositRecs.DepositRecId = Acts.DepositId
					INNER JOIN DiscrepancyKind ON DiscrepancyKind.DiscrepancyKindId = Acts.DiscrepancyKindId
					WHERE RecLastChangeDatetime BETWEEN "'.addslashes($set_before_datetime).'" AND "'.addslashes($set_last_datetime).'"
					AND ReconcileStatus = 1 AND ServiceRec = 0 AND IFNULL(DepositRecs.isBalanced, 0) = 0
					AND ScenarioId IN ('.implode(',',$r_scenarios).')
					;');
				$act_count = 0;
				foreach ($rows as $row) {
                                    $deposit_id = $row['DepositId'];
                                    if (($row['DiscrepancyKindName'] == "излишек") OR ($row['DiscrepancyKindName']) == "недостача") {
                                        $cnt = fetch_row_from_sql('
                                                    SELECT 
                                                            COUNT(expected.DenomId)
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
                                                    WHERE expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)<>0
                                                    ;');
                                            $act_count = $act_count + $cnt[0];
                                    };
                                    if ($row['DiscrepancyKindName'] == "сомнительная банкнота") {
                                            $cnt = fetch_row_from_sql('
                                                    SELECT 
                                                            COUNT(DenomLabel) 
                                                    FROM ReconAccountingData
                                                    INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
                                                    INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
                                                    WHERE DepositRecId='.$deposit_id.' 
                                                    AND GradeName = "SUSPECT"
                                                    GROUP BY ReconAccountingData.DenomId;');
                                            $act_count = $act_count + $cnt[0];
					};
				};
			} else {
				$act_count = 0; // Случай, если сегодня ещё не отчитывались
			};
			// end of block that defines the start point of acts numbering
			
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
			$r_events = get_assoc_array_from_sql('
				SELECT ActId, DepositId, DiscrepancyKindName
				FROM Acts
				INNER JOIN DiscrepancyKind ON DiscrepancyKind.DiscrepancyKindId = Acts.DiscrepancyKindId
				INNER JOIN DepositRecs ON DepositRecs.DepositRecId = Acts.DepositId
				WHERE DepositId IN ('.implode(',',$recsid).')
				AND ScenarioId IN ('.implode(',',$r_scenarios).')
				ORDER BY DepositId;');
				$i=0; // Counter for external cycles
			foreach ($r_events as $r_event) {
				$i++;
				$deposit_id = $r_event['DepositId'];
				$discrepancy_name = $r_event['DiscrepancyKindName'];
				$row = fetch_assoc_row_from_sql('
					SELECT 
						COALESCE(CustomerName, "клиент не указан") as CustomerName,
						RecOperatorId as OperatorId,
						COALESCE(RecSupervisorId, RecOperatorId) as SupervisorId,
						COALESCE(DATE_FORMAT(DepositPackingDate, "%d.%m.%Y"), "не указана") as PackingDate,
						PackType,
						PackIntegrity,
						COALESCE(PackId, " ") as PackId,
						SealType,
						SealIntegrity,
						COALESCE(SealNumber, " ") as SealNumber,
						StrapsIntegrity,
						StrapType,
						COALESCE(PackingOperatorName, "не указан") as PackingOperatorName
					FROM DepositRecs
					LEFT JOIN Customers ON DepositRecs.CustomerId = Customers.CustomerId
					WHERE DepositRecId = "'.addslashes($deposit_id).'"
					;');
				$customer_name = $row['CustomerName'];
				$operator_name  = get_short_iof_by_user_id($row['OperatorId']);
				$supervisor_name = get_short_iof_by_user_id($row['SupervisorId']);
				$r_operator_name  = get_short_fio_by_user_id($row['OperatorId']);
				$r_supervisor_name = get_short_fio_by_user_id($row['SupervisorId']);
				$packing_date = $row['PackingDate'];
				$packing_operator_name = $row['PackingOperatorName'];
				$seal_number = $row['SealNumber'];
				$pack_id = $row['PackId'];
				if ($row['PackType'] == 0) {
					$sack_striked = false;
					$pack_striked = true;
					$cassette_striked = true;
				} elseif ($row['PackType'] == 1) {
					$sack_striked = true;
					$pack_striked = false;
					$cassette_striked = true;
				} elseif ($row['PackType'] == 2){
					$sack_striked = true;
					$pack_striked = true;
					$cassette_striked = false;
				};
				if ($row['SealType'] == 0) {
					$seal_striked = false;
					$lead_striked = true;
				} elseif ($row['SealType'] == 1) {
					$seal_striked = true;
					$lead_striked = false;
				};
				if ($row['SealIntegrity'] == 0) {
					$seal_ok_striked = true;
					$seal_broken_striked = false;
				} elseif ($row['SealIntegrity'] == 1) {
					$seal_ok_striked = false;
					$seal_broken_striked = true;
				};
				if ($row['StrapType'] == 0) {
					$full_length_striked = false;
					$cross_striked = true;
				} elseif ($row['StrapType'] == 1) {
					$full_length_striked = true;
					$cross_striked = false;
				};
				if ($row['StrapsIntegrity'] == 0) {
					$strap_ok_striked = true;
					$strap_broken_striked = false;
				} elseif ($row['StrapsIntegrity'] == 1) {
					$strap_ok_striked = false;
					$strap_broken_striked = true;
				};
				if ($row['PackIntegrity'] == 0) {
					$pack_ok_striked = true;
					$pack_broken_striked = false;
				} elseif ($row['PackIntegrity'] == 1) {
					$pack_ok_striked = false;
					$pack_broken_striked = true;
				};
				$expecteds = get_assoc_array_from_sql('
					SELECT 
						DenomLabel, 
						SUM(ExpectedCount) * Value as ex_sum
					FROM DepositDenomTotal
					INNER JOIN Denoms ON DepositDenomTotal.DenomId = Denoms.DenomId
					WHERE DepositReclId = '.$deposit_id.'
					AND ExpectedCount > 0
					GROUP BY DepositDenomTotal.DenomId;');

				if ($discrepancy_name == "излишек") {
					$act_rows = get_assoc_array_from_sql('
						SELECT 
							expected.CurrSymbol as CurrSymbol,
							expected.ValuableTypeLabel as ValuableTypeLabel,
							expected.DenomLabel as DenomLabel, 
							expected.Value as Value,
							ABS(expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)) as discr_cnt
						FROM (
							SELECT 
								CurrSymbol,
								ValuableTypeLabel,
								Denoms.DenomLabel as DenomLabel, 
								DepositDenomTotal.DenomId, 
								Value, 
								ExpectedCount
							FROM DepositDenomTotal
							INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = DepositDenomTotal.ValuableTypeId
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
							INNER JOIN Currency ON Denoms.CurrencyId = Currency.CurrencyId
							WHERE DepositReclId = '.$deposit_id.'
							GROUP BY DepositDenomTotal.ValuableTypeId, DepositDenomTotal.DenomId) as expected
						LEFT JOIN (
							SELECT DenomId, ValuableTypeLabel, SUM(ActualCount) as s_count
							FROM SorterAccountingData
							INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
							INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
							INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
							WHERE DepositRecId = "'.addslashes($deposit_id).'"
							GROUP BY Valuables.ValuableTypeId, DenomId) as sorter
						ON expected.DenomId = sorter.DenomId
							AND expected.ValuableTypeLabel = sorter.ValuableTypeLabel
						LEFT JOIN (
							SELECT ValuableTypeLabel, DenomId, SUM(CullCount) as r_count
							FROM ReconAccountingData
							INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = ReconAccountingData.ValuableTypeId
							WHERE DepositRecId='.$deposit_id.'
							GROUP BY ReconAccountingData.ValuableTypeId, DenomId) as recon
						ON expected.DenomId = recon.DenomId
							AND expected.ValuableTypeLabel = recon.ValuableTypeLabel
						WHERE expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)<0
						;');
					$over_striked = false;
					$short_striked = true;
					$suspect_striked = true;
				};
				if ($discrepancy_name == "недостача") {
					$act_rows = get_assoc_array_from_sql('
						SELECT 
							expected.CurrSymbol as CurrSymbol,
							expected.ValuableTypeLabel as ValuableTypeLabel,
							expected.DenomLabel as DenomLabel, 
							expected.Value as Value,
							ABS(expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)) as discr_cnt
						FROM (
							SELECT 
								CurrSymbol,
								ValuableTypeLabel,
								Denoms.DenomLabel as DenomLabel, 
								DepositDenomTotal.DenomId, 
								Value, 
								ExpectedCount
							FROM DepositDenomTotal
							INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = DepositDenomTotal.ValuableTypeId
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
							INNER JOIN Currency ON Denoms.CurrencyId = Currency.CurrencyId
							WHERE DepositReclId = '.$deposit_id.'
							GROUP BY DepositDenomTotal.ValuableTypeId, DepositDenomTotal.DenomId) as expected
						LEFT JOIN (
							SELECT ValuableTypeLabel, DenomId, SUM(ActualCount) as s_count
							FROM SorterAccountingData
							INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
							INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
							INNER JOIN ValuableTypes ON Valuables.ValuableTypeId = ValuableTypes.ValuableTypeId
							WHERE DepositRecId = "'.addslashes($deposit_id).'"
							GROUP BY Valuables.ValuableTypeId, DenomId) as sorter
						ON expected.DenomId = sorter.DenomId
							AND expected.ValuableTypeLabel = sorter.ValuableTypeLabel
						LEFT JOIN (
							SELECT ValuableTypeLabel, DenomId, SUM(CullCount) as r_count
							FROM ReconAccountingData
							INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = ReconAccountingData.ValuableTypeId
							WHERE DepositRecId='.$deposit_id.'
							GROUP BY ReconAccountingData.ValuableTypeId, DenomId) as recon
						ON expected.DenomId = recon.DenomId
							AND expected.ValuableTypeLabel = recon.ValuableTypeLabel
						WHERE expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)>0
						;');
					$over_striked = true;
					$short_striked = false;
					$suspect_striked = true;
				};
				if ($discrepancy_name == "сомнительная банкнота") {
					$act_rows = get_assoc_array_from_sql('
						SELECT 
							CurrSymbol,
							ValuableTypeLabel,
							DenomLabel, 
							Value, 
							SUM(CullCount) as discr_cnt
						FROM ReconAccountingData
						INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
						INNER JOIN Currency ON Denoms.CurrencyId = Currency.CurrencyId
						INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
						INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = ReconAccountingData.ValuableTypeId
						WHERE DepositRecId='.$deposit_id.' 
						AND GradeName = "SUSPECT"
						GROUP BY ReconAccountingData.ValuableTypeId, ReconAccountingData.DenomId;');
					$over_striked = true;
					$short_striked = true;
					$suspect_striked = false;
				};
				$ii = 0; // Counter for 
				foreach ($act_rows as $act_row) {
					$ii++; 
					$act_count = $act_count + 1;
					$act_denom_label = $act_row['DenomLabel'];
					$act_denom = $act_row['Value'];
					$act_qty = $act_row['discr_cnt'];
					$act_curr_symbol = $act_row['CurrSymbol'];
					if ($act_row['ValuableTypeLabel'] == 'банкноты') {
						$banknote_striked = false;
						$coin_striked = true;
					} elseif ($act_row['ValuableTypeLabel'] == 'монеты') {
						$banknote_striked = true;
						$coin_striked = false;
					};
					// Compile the body of an act
					?>
                            <table class='act145' id="formcode" border=1 cellspacing="0" cellpadding="1">
                                    <tr>
                                            <td>Код формы<br>документа по ОКУД</td>
                                    </tr>
                                    <tr>
                                            <td>0402145</td>
                                    </tr>
                            </table>
                            <table  class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td colspan=3 align=middle style='letter-spacing:0.4em;'><b>АКТ</b></td>
                                    </tr>
                                    <tr>
                                            <td width=30% align=left><?php echo $str_date;?></td>
                                            <td width=45%>&nbsp;</td>
                                            <td width=25% align=right>№ <?php echo $act_count;?></td>
                                    </tr>
                                    <tr><td colspan=3>&nbsp;</td></tr>
                                    <tr>
                                            <td style="text-align: left; border-bottom-width: 1px; border-bottom-style: solid;">
                                    об 
                                    <span style="<?php echo ($over_striked)?'text-decoration:line-through;':''; ?>;">излишках</span>, 
                                    <span style="<?php echo ($short_striked)?'text-decoration:line-through;':''; ?>;">недостачах</span></td>
                                            <td>&nbsp;</td>
                                            <td style="text-align: right; border-bottom-width: 1px; border-bottom-style: solid;">
                                                    <span style="<?php echo ($banknote_striked)?'text-decoration:line-through;':''; ?>;">
                                    банкнот(ах) </span>
                                                    <span style="<?php echo ($pack_striked)?'text-decoration:line-through;':''; ?>;">
                                    в пачках</span>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td align=left>
                                                    <span style="<?php echo ($suspect_striked)?'text-decoration:line-through;':''; ?>;">сомнительных</span></td>
                                            <td>&nbsp;</td>
                                            <td align=right>
                                                    <span style="<?php echo ($coin_striked)?'text-decoration:line-through;':''; ?>;">монеты(ах)</span>
                                                    <span style="<?php echo ($sack_striked)?'text-decoration:line-through;':''; ?>;"> в мешках</span></td>
                               </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td width=18% align=left>в упаковке</td>
                                            <td width=82% style='text-align:center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <?php echo $customer_name;?>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td>&nbsp;</td>
                                            <td style='font-size: 8pt; text-align: center'>
                                    (наименование предприятия - изготовителя банкнот и монеты Банка России, учреждения Банка России
                                    <br>или кредитной огранизации)
                                            </td>
                                    </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td width=30% align=left>Настоящий акт составлен в</td>
                                            <td width=70% style='text-align:center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <?php echo $site_name;?>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td>&nbsp;</td>
                                            <td style='font-size: 8pt; text-align: center'>
                                    (наименование учреждения Банка России или кредитной огранизации)
                                            </td>
                                    </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td align=left>в 
                                    <span style='font-size: 12pt; text-decoration: underline'>г. <?php echo $site_city;?></span> 
                                    в том, что сего числа при вскрытиии и пересчёте</td>
                                    </tr>
                                    <tr>
                                            <td style='font-size: 8pt; text-align: left'>
                                    (наименование населённого пункта)
                                            </td>
                                    </tr>
                                    <tr>
                                            <td style='text-align: left; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    в кассе, 
                                    <span style='font-size: 12pt; text-decoration: line-through'>
                                    в предкладовой, в помещении для пересчёта наличных денег клиентом
                                    </span>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td style='font-size: 8pt; text-align: center'>
                                    (ненужное зачеркнуть)
                                            </td>
                                    </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td width=15% style='text_align: left; font-size: 12pt; text-decoration: underline;'>
                                            <span style="<?php echo ($banknote_striked)?'text-decoration:line-through;':''; ?>;">банкнот</span>
                                            </td>
                                            <td width=35% align=left>
                                    <span style='font-size: 12pt; text-decoration: underline;'>кассовым работником</span>
                                            </td>
                                            <td width=50% style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <?php echo $r_operator_name;?>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td style='text-align: left;'>
                                    <span style="<?php echo ($coin_striked)?'text-decoration:line-through;':''; ?>;">монеты</span>
                                            </td>
                                            <td style='text-align: left;'>
                                    <span style='font-size: 12pt; text-decoration: line-through;'>клиентом</span>
                                            </td>
                                            <td style='text-align: center;'>
                                    <span style='font-size: 8pt;'>(фамилия и инициалы)</span>
                                            </td>
                                    </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td width=18% align=middle>
                                    в присутствии
                                            </td>
                                            <td width=57% style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <?php echo $r_supervisor_name;?>
                                            </td>
                                            <td width=25% style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    был(а) обнаружен(а)
                                            </td>
                                    </tr>
                                    <tr>
                                            <td>&nbsp;</td>
                                            <td align=middle><span style='font-size: 8pt;'>
                                    (фамилия и инициалы работника учреждения Банка России<br>
                                    или кредитной организации)</span>
                                            </td>
                                            <td>&nbsp;</td>
                                    </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                            <?php
                            foreach ($expecteds as $expected) {
                            ?>
                                    <tr>
                                            <td width=19% style="text-align: center; border-bottom-width: 1px; border-bottom-style: solid;">
                                    <span style="<?php echo ($pack_striked)?'text-decoration:line-through;':''; ?>;">в пачке</span>&nbsp;
                                    <span style="<?php echo ($cassette_striked)?'text-decoration:line-through;':''; ?>;">в кассете</span>
                                            </td>
                                            <td rowspan=2 width=14% align=middle>
                                    номиналом
                                            </td>
                                            <td rowspan=2 width=17% align=middle>
                                    <span style="font-size: 12pt; text-decoration: underline;"><?php echo $expected['DenomLabel']; ?></span>
                                            </td>
                                            <td rowspan=2 width=10%>
                                    на сумму
                                            </td>
                                            <td rowspan=2 width=40% align=middle>
                                    <span style="font-size: 12pt;"><?php echo $expected['ex_sum']; ?></span><br>
                                    <span style='font-size: 8pt; text-align: center'>
                                    (сумма, указанная на верхней накладке пачки,<br>ярлыке мешка, в руб., коп.)
                                                    </span>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td align=middle>
                                                    <span style="<?php echo ($sack_striked)?'text-decoration:line-through;':''; ?>;">в мешке</span>
                                            </td>
                                    </tr>
                            <?php
                            };
                            ?>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td colspan=3 style='width: 25%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <span style="<?php echo ($pack_striked AND $cassette_striked)?'text-decoration:line-through;':''; ?>;">
                                    сформированной</span>
                                    </td>
                                            <td rowspan=2 width=3%>&nbsp;</td>
                                            <td style='width: 37%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <?php echo $packing_date; ?></td>
                                       <td rowspan=2 width=2%>&nbsp;</td>
                                       <td width=3% rowspan=2 align=middle>с</td>
                                       <td width=2% rowspan=2>&nbsp;</td>
                                            <td style='width: 13%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <span style="<?php echo ($pack_ok_striked)?'text-decoration:line-through;':''; ?>;">целой</span></td>
                                       <td width=14% rowspan=2 align=middle>упаковкой</td>
                                    </tr>
                                    <tr>
                                            <td colspan=3 align=middle>
                                                    <span style="<?php echo ($sack_striked)?'text-decoration:line-through;':''; ?>;">
                                                    сформированном</span>
                                                    </td>
                                            <td style='font-size: 8pt; text-align: center';>
                                    (дата формирования пачки, мешка)
                                            </td>
                                            <td>
                                                    <span style="<?php echo ($pack_broken_striked)?'text-decoration:line-through;':''; ?>;">нарушенной</span></td>
                                    </tr>
                                    <tr>
                                            <td width=3% rowspan=2>с</td>
                                            <td width=2% rowspan=2>&nbsp;</td>
                                            <td style='width: 20%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <span style="<?php echo ($seal_ok_striked)?'text-decoration:line-through;':''; ?>;">целой</span></td>
                                            <td rowspan=2 width=3%>&nbsp;</td>
                                            <td style='width: 37%; text-align: left; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    пломбой № <?php echo $seal_number; ?></td>
                                       <td rowspan=2 width=2%>&nbsp;</td>
                                       <td width=3% rowspan=2 align=middle>с</td>
                                       <td width=2% rowspan=2>&nbsp;</td>
                                            <td style="width: 13%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;">
                                    <span style="<?php echo ($strap_ok_striked)?'text-decoration:line-through;':''; ?>;">целыми</span></td>
                                       <td width=14% rowspan=2 align=middle>бандеролями</td>
                                    </tr>
                                    <tr>
                                            <td align=middle>
                                                    <span style="<?php echo ($seal_broken_striked)?'text-decoration:line-through;':''; ?>;">нарушенной</span></td>
                                            <td align=left>
                                    упаковкой № <?php echo $pack_id; ?>
                                            </td>
                                            <td>
                                                    <span style="<?php echo ($strap_broken_striked)?'text-decoration:line-through;':''; ?>;">нарушенными</span></td>
                                    </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <?php echo $packing_operator_name; ?>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td style='font-size: 8pt; text-align: center'>
                                    (фамилия и инициалы кассового работника, табельный номер контролёра-упаковщика
                                    или номер бригады (для банкнот), номер<br>автомата или шифр контролёра-счётчика,
                                    шифр контролёра-упаковщика (для монеты), в упаковке которых установлен(а))
                                            </td>
                                    </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td width=28% style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <span style="font-size: 12pt; <?php echo ($over_striked)?'text-decoration:line-through;':''; ?>;">излишек</span>, 
                                    <span style="font-size: 12pt; <?php echo ($short_striked)?'text-decoration:line-through;':''; ?>;">недостача</span>
                                            </td>
                                            <td width=2% rowspan=2>&nbsp;</td>
                                            <td width=28% style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <?php echo $act_qty; ?> (<?php echo  int2str($act_qty); ?>)
                                            </td>
                                            <td width=2% rowspan=2>&nbsp;</td>
                                            <td width=40% style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <span style="<?php echo ($banknote_striked)?'text-decoration:line-through;':''; ?>;">банкнот(а)</span>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td align=middle>
                                    <span style="font-size: 12pt; <?php echo ($suspect_striked)?'text-decoration:line-through;':''; ?>;">сомнительная</span>
                                            </td>
                                            <td style='font-size: 8pt; text-align: center;'>
                                    (количество цифрами и прописью)
                                            </td>
                                            <td align=middle>
                                    <span style="<?php echo ($coin_striked)?'text-decoration:line-through;':''; ?>;">монеты (а)</span>
                                            </td>
                                    </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td align=left>
                                    номиналом
                                            </td>
                                            <td style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <?php echo $act_denom_label; ?> 
                                            </td>
                                            <td align=left>
                                    на сумму
                                            </td>
                                            <td style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <?php 
                                            if ($act_curr_symbol == 'RUB') {
                                                    echo number_format($act_qty * $act_denom, 2, ',', ' ').' руб.';
                                            } elseif ($act_curr_symbol == 'USD') {
                                                    echo 'USD '.number_format($act_qty * $act_denom, 2, ',', ' ');
                                            } else {
                                                    echo number_format($act_qty * $act_denom, 2, ',', ' ');
                                            };
                                     ?> (<?php echo  num2str_by_currency($act_qty * $act_denom, $act_curr_symbol); ?>)
                                            </td>
                                    </tr>
                                    <tr>
                                            <td colspan=3>&nbsp;</td>
                                            <td style='font-size: 8pt; text-align: center;'>
                                    (сумма цифрами и прописью)
                                            </td>
                                    </tr>
                            </table>
                            <table  class='act145' id="signatures" style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td style='width: 50%;'>Подпись лица, производившего пересчёт</td>
                                            <td style='width: 25%; border-bottom-width: 1px; border-bottom-style: solid;'>&nbsp;</td>
                                            <td style='width: 25%;'><?php echo $operator_name; ?></td>
                                    </tr>
                                    <tr>
                                            <td style='width: 50%;'>Подпись лиц, присутствовавших,<br>при пересчёте</td>
                                            <td style='width: 25%; border-bottom-width: 1px; border-bottom-style: solid;'>&nbsp;</td>
                                            <td style='width: 25%;'><?php echo $supervisor_name; ?></td>
                                    </tr>
                                    <tr>
                                            <td colspan=3 style="text-align: left;">К акту прилагаются:<br/>
                                    - верхняя и нижняя накладки от пачки банкнот;<br/>
                                    - <span style="font-size: 12pt; <?php echo ($full_length_striked)?'text-decoration:line-through;':''; ?>;">
                                            бандероли от всех корешков (полной величины) пачки банкнот</span>,
                                    <span style="font-size: 12pt; <?php echo ($cross_striked)?'text-decoration:line-through;':''; ?>;">
                                    поперечная бандероль от<br/>пачки банкнот;</span><br>
                                    - <span style="<?php echo ($pack_striked)?'text-decoration:line-through;':''; ?>;">
                                    обвязка с пломбой (полиэтиленовая упаковка с оттиском(ами) клише) от
                                    пачки банкнот</span> или<br>
                                    <span style="<?php echo ($sack_striked)?'text-decoration:line-through;':''; ?>;">
                                    обвязка с пломбой и ярлык от мешка с монетой
                                    (кольцо-пломба), в которой(ом) был(а) обнаружен(а)</span>
                                            </td>
                                    </tr>
                            </table>
                            <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                    <tr>
                                            <td style='width: 25%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <span style="font-size: 12pt; <?php echo ($over_striked)?'text-decoration:line-through;':''; ?>;">излишек</span>, 
                                    <span style="font-size: 12pt; <?php echo ($short_striked)?'text-decoration:line-through;':''; ?>;">недостача</span>
                                            </td>
                                            <td rowspan=2 width=8%>&nbsp;</td>
                                            <td style='font-size: 12pt; width: 25%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    банкнот(а)
                                            </td>
                                            <td rowspan=2 width=42%>&nbsp;</td>
                                    </tr>
                                    <tr>
                                            <td align=middle>
                                    <span style="font-size: 12pt; <?php echo ($suspect_striked)?'text-decoration:line-through;':''; ?>;">сомнительная</span>
                                            </td>
                                            <td align=middle>
                                    <span style='font-size: 12pt; text-decoration: line-through;'>монеты(а)</span>
                                            </td>
                                    </tr>
                            </table>
                                <?php
                                    if ((count($act_rows) != $ii OR count($r_events) != $i)) {
                                        include 'app/view/reports/page_divider.php';
                                    };
                                // The end of an individual act creating
				};
			};
           break;
		};
    
?>

