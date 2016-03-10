<?php
			?>
			<style>
			table#formcode {
				width: 166px;
				text-align: center;
			}
			table.act145 td {
				font-family: "Times New Roman", serif;
				font-size: 10pt;
			}
			table#signatures td {
                                vertical-align: bottom; 
                                text-align: left; 
                                font-weight: normal; 
                                font-size: 12pt;
			}
			</style>
			<?php
    		include_once 'app/model/get_short_iof_by_user_id.php';
    		include_once 'app/model/get_short_genetive_iof_by_user_id.php';
    		include_once 'app/model/get_short_instr_iof_by_user_id.php';
    		
			$date_year = substr($report_datetime,0,4); // Дата, когда нажали на кнопку формирования акта
			$d_month = substr($report_datetime,5,2);
			$date_day = substr($report_datetime,8,2);
			if ($d_month == 1) {
				$date_month = "января";
			} elseif ($d_month == 02) {
				$date_month = "февраля";
			} elseif ($d_month == 03) {
				$date_month = "марта";
			} elseif ($d_month == 04) {
				$date_month = "апреля";
			} elseif ($d_month == 05) {
				$date_month = "мая";
			} elseif ($d_month == 06) {
				$date_month = "июня";
			} elseif ($d_month == 07) {
				$date_month = "июля";
			} elseif ($d_month == 08) {
				$date_month = "августа";
			} elseif ($d_month == 09) {
				$date_month = "сентября";
			} elseif ($d_month == 10) {
				$date_month = "октября";
			} elseif ($d_month == 11) {
				$date_month = "ноября";
			} else{
				$date_month = "декабря";
			};

			// Определяем инициалы,фамилию и должность создателя акта
			$rows = get_array_from_sql('
				SELECT CreatedBy
				FROM ReportSets
				WHERE SetDateTime = "'.addslashes($report_datetime).'"
			;');
			if(count($rows) == 0) {  
				$r_creator_name  = get_short_iof_by_user_id($_SESSION[$program]['UserConfiguration']['UserId']);
				$r_creator_post = $_SESSION[$program]['UserConfiguration']['UserPost'];
			}  else {
				$r_creator = array();
				foreach ($rows as $row) {
					$r_creator[] = $row[0];
				}
				$row = fetch_row_from_sql('
					SELECT UserPost
					FROM UserConfiguration
					WHERE UserId = "'.$r_creator[0].'"
				;');
				$r_creator_name  = get_short_iof_by_user_id($r_creator[0]);
				$r_creator_post = $row[0];
			}              


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
			$row = fetch_assoc_row_from_sql('
				SELECT 
					COALESCE(CustomerName, "клиент не указан") as CustomerName,
					RecOperatorId as OperatorId,
					COALESCE(RecSupervisorId, RecOperatorId) as SupervisorId,
					COALESCE(DATE_FORMAT(DepositPackingDate, "%d.%m.%Y"), "не указана") as PackingDate,
					DATE_FORMAT(RecLastChangeDatetime, "%Y-%m-%d") as ReconcileDate,
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
				WHERE DepositRecId = "'.$deposit_id.'"
				;');
				// Формируем дату сверки этого депозита
				$r_rec_year = substr($row['ReconcileDate'],2,2);
				$r_rec_m = substr($row['ReconcileDate'],5,2);
				$r_rec_day = substr($row['ReconcileDate'],8,2);
				if ($r_rec_m == 01) {
					$r_rec_month = "января";
				} elseif ($r_rec_m == 02) {
					$r_rec_month = "февраля";
				} elseif ($r_rec_m == 03) {
					$r_rec_month = "марта";
				} elseif ($r_rec_m == 04) {
					$r_rec_month = "апреля";
				} elseif ($r_rec_m == 05) {
					$r_rec_month = "мая";
				} elseif ($r_rec_m == 06) {
					$r_rec_month = "июня";
				} elseif ($r_rec_m == 07) {
					$r_rec_month = "июля";
				} elseif ($r_rec_m == 08) {
					$r_rec_month= "августа";
				} elseif ($r_rec_m == 09) {
					$r_rec_month = "сентября";
				} elseif ($r_rec_m == 10) {
					$r_rec_month = "октября";
				} elseif ($r_rec_m == 11) {
					$r_rec_month = "ноября";
				} else{
					$r_rec_month = "декабря";
				};

				$customer_name = $row['CustomerName'];
				// Блок, который разбивает по словам на две части название клиента
				if(strlen ($customer_name) < 30) {
					// Если всё название умещается в первую часть
					$part_1 = htmlfix($customer_name);
					$part_2 = '&nbsp;';
				} else {
					$r_words = explode(' ',$customer_name);
					$words_counter = 0;
					$tester = '';
					while(strlen ($tester) < 30) {
						$tester = $tester.$r_words[$words_counter].' ';
						$words_counter++;
					}
					$part_1 = '';
					$part_2 = '';
					for ($r_i=0; $r_i < $words_counter; $r_i++) {
						$part_1 = $part_1.$r_words[$r_i].' ';
					}
					for ($r_i=$words_counter; $r_i < count($r_words); $r_i++) {
						$part_2 = $part_2.$r_words[$r_i].' ';
					}
				$part_1 = htmlfix($part_1);
				$part_2 = htmlfix($part_2);
				}
				// Конец блока разбивания

			$customer_name = $row['CustomerName'];
			$r_operator_name  = get_short_iof_by_user_id($row['OperatorId']);
			$r_operator_instr_name  = get_short_instr_iof_by_user_id($row['OperatorId']);
			$r_supervisor_name  = get_short_iof_by_user_id($row['SupervisorId']);
			$r_supervisor_gen_name = get_short_genetive_iof_by_user_id($row['SupervisorId']);
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
			if ($row['SealType'] == 0) { //Клише
				$cliche_striked = false;
				$lead_striked = true;
			} elseif ($row['SealType'] == 1) { //Пломба
				$cliche_striked = true;
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

			$row = fetch_row_from_sql('
				SELECT 
					o.UserPost, s.UserPost
				FROM DepositRecs
					INNER JOIN UserConfiguration as o ON DepositRecs.RecOperatorId = o.UserId
					INNER JOIN UserConfiguration as s ON DepositRecs.RecSupervisorId = s.UserId
				WHERE DepositRecId = "'.$deposit_id.'"
				;');
			$r_operator_post = $row[0];
			$r_supervisor_post = $row[1];
			// Определяем номинал и заявленную сумму депозита. Если депозит многономинальный, то сумму по всем номиналам
			$expecteds = get_assoc_array_from_sql('
				SELECT 
					Value,
					DenomLabel, 
					SUM(ExpectedCount) * Value as ex_sum
				FROM DepositDenomTotal
					INNER JOIN Denoms ON DepositDenomTotal.DenomId = Denoms.DenomId
				WHERE DepositReclId = "'.$deposit_id.'"
					AND ExpectedCount > 0
				GROUP BY DepositDenomTotal.DenomId;');
			if (count($expecteds) == 1) {
				$r_deposit_denomlabel = $expecteds[0]['DenomLabel'];
				$r_deposit_expected = $expecteds[0]['ex_sum'];
			} else {
				$r_deposit_denomlabel = 'Сборная';
				$r_deposit_expected = 0;
				foreach ($expecteds as $expected) {
					$r_deposit_expected += $expected['ex_sum'];
				}
			}
			// Define the kinds of discrepancy in the deposit
			$rows = get_array_from_sql('
				SELECT DiscrepancyKindName
				FROM Acts
				INNER JOIN DiscrepancyKind ON DiscrepancyKind.DiscrepancyKindId = Acts.DiscrepancyKindId
				WHERE DepositId = '.$deposit_id.'
				;');
			$r_discr_kinds = array();
			foreach ($rows as $row) {
				$r_discr_kinds[] = $row[0];				
			};
			if (in_array("излишек", $r_discr_kinds)) {
				$over_striked = false;
			} else {
				$over_striked = true;
			};
			if (in_array("недостача", $r_discr_kinds)) {
				$short_striked = false;
			} else {
				$short_striked = true;
			};
			if (in_array("сомнительная банкнота", $r_discr_kinds)) {
				$suspect_striked = false;
			} else {
				$suspect_striked = true;
			};

			?>
			<!-- Creating the body of the act (static part of the act)-------------------------------------------->
			
			
	<table style='width:171mm;'>
	  <tr>
		<td style='width:40mm; text-align:left; font-size:12pt; font-family: "Times New Roman", serif; border-bottom: 1px solid black;'>
			Банк России
		</td>
		<td rowspan=2 style='width:61mm;'>&nbsp;</td>
		<td rowspan=2 style='width:70mm; vertical-align: top;text-align: right;'>
			<table class='act145' style="text-align: center;width:70mm;" border=1 cellspacing="0" cellpadding="1">
				<tr>
					<td>Код формы документа по ОКУД</td>
				</tr>
				<tr>
            <td>0402145</td>
				</tr>
			</table>
		</td>
	  </tr>
	  <tr>
		<td style='text-align:center; vertical-align: top; line-height: 1.1em; font-size: 8pt; font-family: "Times New Roman", serif;'>
			(наименование организации)
		</td>
	  </tr>
	</table>
			<table  class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
				<tr>
					<td colspan=11 style='line-height:1.1em; text-align: center; font-size: 12pt; font-weight: bold;'>АКТ<br/>
					о выявлении излишка, недостачи, сомнительного денежного знака</td>
				</tr>
				<tr>
					<td colspan="11" style="height:5px;"></td>
				</tr>
				<tr>
					<td style='width:7mm; text-align:center;'>
						от&nbsp;&nbsp;
					</td>
					<td style='width:2mm; text-align:right; font-weight: bold;'>
						«
					</td>
					<td style='width:5mm; text-align:center; border-bottom: 1px solid black; font-weight: bold;'>
						<?php echo htmlfix($r_rec_day);?>
					</td>
					<td style='width:4mm; text-align:left; font-weight: bold;'>
						»
					</td>
					<td style='width:30mm; text-align:center; border-bottom: 1px solid black; font-weight: bold;'>
						<?php echo htmlfix($r_rec_month);?>
					</td>
					<td style='width:7mm; text-align:center; font-weight: bold;'>
						20
					</td>
					<td style='width:8mm; text-align:center; border-bottom: 1px solid black; font-weight: bold;'>
						<?php echo htmlfix($r_rec_year);?>
					</td>
               <td style='width:10mm; text-align:center; font-weight: bold'>
						года
					</td>
					<td style='width:79mm;'>&nbsp;</td>
					<td style='width:7mm; text-align:center; font-weight: bold'>
						№
					</td>
					<td style='width:12mm; text-align:center; border-bottom: 1px solid black; font-weight: bold'>
						<?php echo htmlfix($r_act_number);?>
					</td>
				</tr>
				

			<table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
				<tr>
				<tr>
					<td colspan="2" style="height:5px;"></td>
				</tr>
					<td style='width:43mm; text-align:left;'>Настоящий акт составлен в</td>
					<td style='width:128mm; text-align:center; border-bottom: 1px solid black; font-weight: bold;'>
						<?php echo htmlfix($site_name);?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style='font-size: 8pt; text-align: center; line-height:1.1em; vertical-align: top;'>
				(наименование учреждения Банка России или кредитной огранизации)
					</td>
				</tr>
			</table>

			<table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
			<tr>
					<td style='width:19mm; text-align:left;'>
						в том, что&nbsp;&nbsp;
					</td>
					<td style='width:2mm; text-align:right; font-weight: bold;'>
						«
					</td>
					<td style='width:5mm; text-align:center; border-bottom: 1px solid black; font-weight: bold;'>
						<?php echo htmlfix($r_rec_day);?>
					</td>
					<td style='width:4mm; text-align:left; font-weight: bold;'>
						»
					</td>
					<td style='width:30mm; text-align:center; border-bottom: 1px solid black; font-weight: bold;'>
						<?php echo htmlfix($r_rec_month);?>
					</td>
					<td style='width:7mm; text-align:center; font-weight: bold;'>
						20
					</td>
					<td style='width:8mm; text-align:center; border-bottom: 1px solid black; font-weight: bold;'>
						<?php echo htmlfix($r_rec_year);?>
					</td>
					<td style='width:48mm; text-align:left;'>
						года при вскрытии упаковки
					</td>
					<td style='width:48mm; text-align:right; border-bottom: 1px solid black; font-weight: bold;'>
					<?php echo $part_1; ?>
					</td>
				</tr>
				<tr>
					<td colspan=9 style='text-align:left; border-bottom: 1px solid black;  height:28px; font-weight: bold;'>
				<?php echo $part_2; ?>
					</td>
				</tr>
				<tr>
					<td colspan=9 style='font-size: 8pt; text-align: center; line-height: 1.1em; vertical-align: top;'>
						(наименование предприятия - изготовителя банкнот и монеты Банка России, учреждения Банка России
						или кредитной огранизации)
					</td>
				</tr>
				<tr>
					<td colspan=9 style='text-align: left; border-bottom: 1px solid black; word-spacing: 8px;'>
					и пересчете в кассе, <span style='text-decoration: line-through'>
					в предкладовой, в помещении для </span>пересчёта 
					<span style='text-decoration: line-through'>наличных денег клиентом</span>
					</td>
				</tr>
				<tr>
					<td colspan=9 style='font-size: 8pt; text-align: center; line-height: 1.1em; vertical-align: top;'>
						(ненужное зачеркнуть)
					</td>
				</tr>
			</table>
			
			<table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
				<tr>
					<td style='width:15%; text-align: left; border-bottom: 1px solid black;'>
						банкнот
					</td>
					<td rowspan=2 style="width:3%">&nbsp;</td>
					<td style='width:35%; text-align:center; border-bottom: 1px solid black;'>
						кассовым работником
					</td>
					<td rowspan=2 style="width:3%">&nbsp;</td>
					<td style='width: 50%; text-align: center; border-bottom: 1px solid black; font-weight: bold;'>
						<?php echo htmlfix($r_operator_instr_name);?>
					</td>
				</tr>
				<tr>
					<td style='text-align:left;'>
						<span style='text-decoration: line-through;'>монеты</span>
					</td>
					<td style='text-align: center;'>
						<span style='text-decoration: line-through;'>клиентом</span>
					</td>
					<td style='text-align: center;'>
						<span style='font-size: 8pt; vertical-align: top; line-height: 1em;'>(фамилия и инициалы)</span>
					</td>
				</tr>
			</table>
			<table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
				<tr>
					<td style='width: 18%; text-align: left;'>
						в присутствии
					</td>
					<td width=57% style='text-align: center; border-bottom: 1px solid black; font-weight: bold;'>
						<?php echo htmlfix($r_supervisor_gen_name);?>
					</td>
					<td width=25% style='text-align: center;'>
						был(а) обнаружен(а)
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style='font-size: 8pt; line-height: 1em; text-align: center; line-height:1.1em; vertical-align:top;'>
						(фамилия и инициалы работника учреждения Банка России<br>
						или кредитной организации)
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
				<tr>
					<td width=18% style="text-align: left; border-bottom-width: 1px; border-bottom-style: solid; vertical-align: bottom;">
						<span style="<?php echo ($pack_striked)?'text-decoration:line-through;':''; ?>;">в пачке</span>&nbsp;
						<span style="<?php echo ($cassette_striked)?'text-decoration:line-through;':''; ?>;">в кассете</span>
					</td>
					<td style="width: 15%; text-align: center; vertical-align: bottom;">
						номиналом&nbsp;
					</td>
					<td style="width: 15%; font-size: 12pt; text-align: center; border-bottom: 1px solid black; font-weight: bold; vertical-align: bottom;">
						<?php echo htmlfix($r_deposit_denomlabel); ?>&nbsp;
						<span style="font-size:xx-small; vertical-align:top;">1</span>
					</td>
					<td style="width: 11%; text-align: center; vertical-align: bottom;">на сумму</td>
					<td style="width: 41%; font-size: 12pt; text-align: center; border-bottom: 1px solid black; font-weight: bold; vertical-align: bottom;">
						<?php echo htmlfix($r_deposit_expected); ?>
					</td>
				</tr>
				<tr>
					<td style="vertical-align: top; text-align: left;">
						<span style="<?php echo ($sack_striked)?'text-decoration:line-through;':''; ?>;">
							в мешке
						</span>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td rowspan=2>&nbsp;</td>
					<td style='font-size: 8pt; text-align: center; line-height: 1em;'>
						(сумма, указанная на верхней накладке пачки,<br>ярлыке мешка, в руб., коп.)
					</td>
				</tr>
			</table>
			<table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
				<tr>
					<td colspan=3 style='width:25%; text-align: left; border-bottom-width: 1px; border-bottom-style: solid;'>
				<span style="<?php echo ($pack_striked AND $cassette_striked)?'text-decoration:line-through;':''; ?>;">
				сформированной</span>
				</td>
					<td rowspan=2 width=3%>&nbsp;</td>
					<td style='width: 37%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
						<strong><?php echo htmlfix($packing_date); ?></strong>
					</td>
				   <td rowspan=2 width=2%>&nbsp;</td>
				   <td width=3% rowspan=2 align=middle>с</td>
				   <td width=2% rowspan=2>&nbsp;</td>
					<td style='width: 13%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
						<span style="<?php echo ($pack_ok_striked)?'text-decoration:line-through;':''; ?>;">целой</span></td>
				   <td width=14% rowspan=2 align=middle>упаковкой</td>
				</tr>
				<tr>
					<td colspan=3 style='text-align: left;'>
						<span style="<?php echo ($sack_striked)?'text-decoration:line-through;':''; ?>;">
							сформированном</span>
						</td>
					<td style='font-size: 8pt; text-align:center; line-height: 1.1em; vertical-align:top;'>
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
						<span style="<?php echo ($lead_striked)?'text-decoration:line-through;':''; ?>;">
							пломбой №
						</span> 
						<span style="<?php echo ($lead_striked)?'display:none;':''; ?>;">
							<?php echo htmlfix($seal_number); ?>
						</span>
					</td>
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
						<span style="<?php echo ($cliche_striked)?'text-decoration:line-through;':''; ?>;">
							упаковкой № 
						</span>
						<span style="<?php echo ($cliche_striked)?'display:none;':''; ?>;">
							<?php echo htmlfix($seal_number); ?>
						</span>
					</td>
					<td>
						<span style="<?php echo ($strap_broken_striked)?'text-decoration:line-through;':''; ?>;">нарушенными</span></td>
				</tr>
			</table>
			<table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
				<tr>
					<td style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
						<strong><?php echo htmlfix($packing_operator_name); ?></strong>
					</td>
				</tr>
				<tr>
					<td style='font-size: 8pt; text-align: center; vertical-align: top; line-height: 1em; padding: 0; letter-spacing:-0.3px;'>
				(инициалы, фамилия кассового работника, табельный номер контролёра-упаковщика или номер бригады (для банкнот), номер автомата<br>
				или шифр контролёра–счётчика, шифр контролёра–упаковщика, номер упаковочной линии (для монеты), в упаковке которых установлен(а)
					</td>
				</tr>
			</table>
			<?php
			$r_serials = array();
			$r_entrycounter = 0;
			$r_kindcounter = 0;
			$r_kindnumber = count($r_discr_kinds); //Количество типов расхождений в акте
			foreach ($r_discr_kinds as $r_discr_kind) {
				$r_kindcounter++; //Счётчик типов расхождений в акте
				if ($r_discr_kind == "излишек") {
					$act_rows = get_assoc_array_from_sql('
						SELECT 
							expected.CurrSymbol,
							expected.DenomId as DenomId,
							expected.DenomLabel as DenomLabel, 
							expected.Value as Value,
							ABS(expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)) as discr_cnt
						FROM (
							SELECT 
								CurrSymbol,
								Denoms.DenomLabel as DenomLabel, 
								DepositDenomTotal.DenomId as DenomId, Value, ExpectedCount
							FROM DepositDenomTotal
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
							INNER JOIN Currency ON Denoms.CurrencyId = Currency.CurrencyId
							WHERE DepositReclId = "'.$deposit_id.'") as expected
						LEFT JOIN (
							SELECT DenomId, SUM(ActualCount) as s_count
							FROM SorterAccountingData
							INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
							INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
							WHERE DepositRecId = "'.$deposit_id.'"
							GROUP BY DenomId) as sorter
						ON expected.DenomId = sorter.DenomId
						LEFT JOIN (
							SELECT DenomId, SUM(CullCount) as r_count
							FROM ReconAccountingData
							WHERE DepositRecId = "'.$deposit_id.'"
							GROUP BY DenomId) as recon
						ON expected.DenomId = recon.DenomId
						WHERE expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)<0
						;');
					$r_over_striked = false;
					$r_short_striked = true;
					$r_suspect_striked = true;
				};
				if ($r_discr_kind == "недостача") {
					$act_rows = get_assoc_array_from_sql('
						SELECT 
							expected.CurrSymbol,
							expected.DenomId as DenomId,
							expected.DenomLabel as DenomLabel, 
							expected.Value as Value,
							ABS(expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)) as discr_cnt
						FROM (
							SELECT 
								CurrSymbol,
								Denoms.DenomLabel as DenomLabel, 
								DepositDenomTotal.DenomId as DenomId, 
								Value, 
								ExpectedCount
							FROM DepositDenomTotal
							INNER JOIN Denoms ON Denoms.DenomId = DepositDenomTotal.DenomId
							INNER JOIN Currency ON Denoms.CurrencyId = Currency.CurrencyId
							WHERE DepositReclId = "'.$deposit_id.'") as expected
						LEFT JOIN (
							SELECT DenomId, SUM(ActualCount) as s_count
							FROM SorterAccountingData
							INNER JOIN DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
							INNER JOIN Valuables ON Valuables.ValuableId = SorterAccountingData.ValuableId
							WHERE DepositRecId = "'.$deposit_id.'"
							GROUP BY DenomId) as sorter
						ON expected.DenomId = sorter.DenomId
						LEFT JOIN (
							SELECT DenomId, SUM(CullCount) as r_count
							FROM ReconAccountingData
							WHERE DepositRecId = "'.$deposit_id.'"
							GROUP BY DenomId) as recon
						ON expected.DenomId = recon.DenomId
						WHERE expected.ExpectedCount-COALESCE(sorter.s_count,0)-COALESCE(recon.r_count,0)>0
						;');
					$r_over_striked = true;
					$r_short_striked = false;
					$r_suspect_striked = true;
				};
				if ($r_discr_kind == "сомнительная банкнота") {
					$act_rows = get_assoc_array_from_sql('
						SELECT 
							CurrSymbol,
							ReconAccountingData.DenomId as DenomId,
							DenomLabel, 
							Value, 
							CullCount as discr_cnt
						FROM ReconAccountingData
						INNER JOIN Denoms ON Denoms.DenomId = ReconAccountingData.DenomId
						INNER JOIN Currency ON Denoms.CurrencyId = Currency.CurrencyId
						INNER JOIN Grades ON Grades.GradeId = ReconAccountingData.GradeId
						INNER JOIN ValuableTypes ON ValuableTypes.ValuableTypeId = ReconAccountingData.ValuableTypeId
						WHERE DepositRecId = "'.$deposit_id.'"
							AND GradeName = "SUSPECT"
							AND ValuableTypeName = "banknotes"
							AND CullCount > 0
						GROUP BY ReconAccountingData.DenomId;');
					$r_over_striked = true;
					$r_short_striked = true;
					$r_suspect_striked = false;
				};
				$r_rowcounter = 0; // Счётчик расхождений по каждому типу
				$r_rownumber = count($act_rows); // Количество расхождений по этому типу
			foreach ($act_rows as $act_row) {
				$r_entrycounter++;
				$r_rowcounter++;
				$act_denom_id = $act_row['DenomId'];
				$act_denom_label = $act_row['DenomLabel'];
				$act_denom = $act_row['Value'];
				$act_qty = $act_row['discr_cnt'];
				$act_curr_symbol = $act_row['CurrSymbol'];
				// Compile each separate discrepancy type denom by denom
				if ($r_discr_kind == "сомнительная банкнота") {
					// Get serial numbers for the suspect banknotes
					$r_suspects = get_assoc_array_from_sql('
						SELECT LeftSeria, LeftNumber, RightSeria, RightNumber
						FROM SuspectSerialNumbs
						WHERE DepositRecId = '.$deposit_id.'
						AND DenomId = '.$act_denom_id.';');
					if ($act_qty ==1) {
						if (($r_suspects[0]['LeftSeria'] != $r_suspects[0]['RightSeria']) OR ($r_suspects[0]['LeftNumber'] != $r_suspects[0]['RightNumber'])) {
							$r_sn = $r_suspects[0]['LeftSeria'].' '.$r_suspects[0]['LeftNumber'].'&nbsp;&nbsp;'.$r_suspects[0]['RightSeria'].' '.$r_suspects[0]['RightNumber'];
						} else {
							$r_sn = $r_suspects[0]['LeftSeria'].' '.$r_suspects[0]['LeftNumber'];
						};
					} else {
						$r_sn = 'См.приложение';
							foreach ($r_suspects as $r_suspect) {
								if (($r_suspect['LeftSeria'] != $r_suspect['RightSeria']) OR ($r_suspect['LeftNumber'] != $r_suspect['RightNumber'])) {
									$r_serials[] = $act_denom_label.' '.$r_suspect['LeftSeria'].' '.$r_suspect['LeftNumber'].' '.$r_suspect['RightSeria'].' '.$r_suspects[0]['RightNumber'];
								} else {
									$r_serials[] = $act_denom_label.' '.$r_suspect['LeftSeria'].' '.$r_suspect['LeftNumber'];
								}
							}
						}
				} else {
					$r_sn = '<s>(серии и номера)</s>';
				}
				if ($r_entrycounter == 1) {
				// Если это первая запись о расхождении, то делаем её без квадратных скобочек	
				?>
                                    <!-- Начало первого блока с расхождением//-->	
                                    <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                        <tr>
                                                <td width=28% style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                        <span style="<?php echo ($r_over_striked)?'text-decoration:line-through;':''; ?>;">излишек</span>, 
                                        <span style="<?php echo ($r_short_striked)?'text-decoration:line-through;':''; ?>;">недостача</span>
                                                </td>
                                                <td width=2% rowspan=2>&nbsp;</td>
                                                <td width=28% style='text-align: center; font-weight: bold; border-bottom: 1px solid black;'>
                                                        <?php echo $act_qty; ?>&nbsp;(<?php echo  int2str($act_qty); ?>)
                                                </td>
                                                <td width=2% rowspan=2>&nbsp;</td>
                                                <td width=40% style='text-align: left; border-bottom-width: 1px; border-bottom-style: solid;'>
                                                        банкнот(а)
                                                        <span><?php echo $r_sn; ?></span>&nbsp;<span style="font-size:xx-small; vertical-align:top;">3</span>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td align=middle>
                                                        <span style="<?php echo ($r_suspect_striked)?'text-decoration:line-through;':''; ?>;">
                                                        сомнительная</span>&nbsp;
                                                        <span style="font-size:xx-small; vertical-align:top;">2</span>
                                                </td>
                                                <td style='font-size: 8pt; text-align: center; line-height: 1em; line-height: 1.1em; vertical-align: top;'>
                                                        (количество цифрами и прописью)
                                                </td>
                                                <td style="text-align: left;">
                                                        <span style="text-decoration: line-through;">монеты (а)</span>
                                                </td>
                                        </tr>
                                    </table>
                                    <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                                        <tr>
                                                <td style="text-align: left;">
                                                        номиналом
                                                </td>
                                                <td style='text-align: center; border-bottom: 1px solid black; font-weight: bold;'>
                                                        <?php echo $act_denom_label; ?> 
                                                </td>
                                                <td style="text-align: center;">
                                                        на сумму
                                                </td>
                                                <td style='text-align: center; border-bottom-width: 1px; border-bottom-style: solid; font-weight: bold;'>
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
                                                <td style='font-size: 8pt; text-align: center;  line-height: 1.1em; vertical-align: top;'>
                                        (сумма цифрами и прописью в руб. коп.)
                                                </td>
                                        </tr>
                                    </table>
                                    <!-- Конец первого блока с расхождением//-->
				<?php
				};
				// Если имеет место один вид расхождения и один номинал, то выводим пустую табличку в квадратных скобках
				if ($r_rownumber == 1 AND $r_kindnumber == 1) {
				?>	
                                    <!-- Пустая табличка в квадратных скобках//-->					
                                    <table class="act145" border=0 style="width:174mm; margin-left:-3mm;">
                                      <tr>
                                            <td rowspan=4 style="width:3mm; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">
                                              &nbsp;
                                            </td>
                                            <td colspan=2 style="width:36mm; border-bottom: 1px solid black; text-align: center;">
                                              излишек, недостача
                                            </td>
                                            <td rowspan=2 style="width:3mm;">
                                              &nbsp;
                                            </td>
                                            <td colspan=4 style="width:64mm; border-bottom: 1px solid black; text-align: center; font-weight:bold;">
                                              &nbsp;
                                            </td>
                                            <td rowspan=2 style="width:3mm;">&nbsp;</td>
                                            <td style="width:59mm; border-bottom: 1px solid black; text-align: center;">
                                              банкнот(а) (серии и номера)
                                            </td>
                                            <td rowspan=4 style="width:3mm; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">
                                              &nbsp;
                                            </td>
                                            <td rowspan=4 style="width:3mm; text-align:right; vertical-align: top; font-size: 8pt; line-height: 1.1em;">
                                              4
                                            </td>
                                      </tr>
                                      <tr>
                                            <td colspan=2 style="text-align:center;">
                                                            сомнительная
                                            </td>
                                            <td colspan=4 style="text-align:center; font-size:8pt; vertical-align: top; line-height: 1.1em;">
                                              (количество цифрами и прописью)
                                            </td>
                                            <td style="text-align:center;">
                                                    монеты (а)
                                            </td>
                                      </tr>
                                      <tr>
                                            <td style="width:17mm; text-align:left;">
                                              номиналом
                                            </td>
                                            <td colspan=3 style="width:28mm; border-bottom:1px solid black; font-weight:bold;">
                                                    &nbsp; 
                                            </td>
                                            <td rowspan=2 style="width:2mm;">&nbsp;</td>
                                            <td style="width:18mm; text-align:left;">
                                              на сумму
                                            </td>
                                            <td colspan=3 style="width: 115mm;border-bottom:1px solid black; font-weight:bold;">
                                                    &nbsp;
                                            </td>
                                      </tr>
                                      <tr>
                                            <td style="width:17mm;">&nbsp;</td>
                                            <td style="width:19mm;">&nbsp;</td>
                                            <td style="width:3mm;">&nbsp;</td>
                                            <td style="width:6mm;">&nbsp;</td>
                                            <td style="width:18mm;">&nbsp;</td>
                                            <td colspan=3 style="width:115mm; text-align:center; font-size:8pt;  line-height: 1.1em; vertical-align: top;">
                                              (сумма цифрами и прописью в руб. и коп.)
                                            </td>
                                       </tr>
                                </table>
                                    <!--Конец пустой таблицы в квадратных скобочках//-->
                            <?php
                            } 
                            if ($r_entrycounter != 1) {
                                    // Если более одного типа расхождения или более одного номинала, то начинаем выводить блок в квадратных скобках
                                    $r_lastblock = ($r_rowcounter == $r_rownumber AND $r_kindcounter == $r_kindnumber) ?1:0;
                                    ?>
                                    <!-- Заполненная табличка в квадратных скобках//-->					
                                    <table class="act145" border=0 style="width:174mm; margin-left:-3mm;">
                                      <tr>
                                            <td rowspan=4 style="width:3mm; border-left: 1px solid black; 
                                            <?php echo ($r_entrycounter == 2)?'border-top: 1px solid black; ':''; ?>;
                                            <?php echo ($r_lastblock == 1)?'border-bottom: 1px solid black; ':''; ?>;">
                                              &nbsp;
                                            </td>
                                            <td colspan=2 style="width:36mm; border-bottom: 1px solid black; text-align: center;">
                                                    <span style="<?php echo ($r_over_striked)?'text-decoration:line-through;':''; ?>;">излишек</span>, 
                                                    <span style="<?php echo ($r_short_striked)?'text-decoration:line-through;':''; ?>;">недостача</span>
                                            </td>
                                            <td rowspan=2 style="width:3mm;">
                                              &nbsp;
                                            </td>
                                            <td colspan=4 style="width:64mm; border-bottom: 1px solid black; text-align: center; font-weight:bold;">
                                                    <?php echo $act_qty; ?>&nbsp;(<?php echo  int2str($act_qty); ?>)
                                            </td>
                                            <td rowspan=2 style="width:3mm;">&nbsp;</td>
                                            <td style="width:59mm; border-bottom: 1px solid black; text-align: center;">
                                                            банкнот(а)
                                                            <span><?php echo $r_sn; ?></span>
                                            </td>
                                            <td rowspan=4 style="width:3mm; border-right: 1px solid black; 
                                            <?php echo ($r_entrycounter == 2)?'border-top: 1px solid black; ':''; ?>;
                                            <?php echo ($r_lastblock == 1)?'border-bottom: 1px solid black; ':''; ?>;">
                                                    &nbsp;
                                            </td>
                                            <td rowspan=4 style="width:3mm; text-align:right; vertical-align: top; font-size: 8pt; line-height: 1.1em;">
                                              <?php echo ($r_entrycounter == 2)?'4':'&nbsp'; ?>
                                            </td>
                                      </tr>
                                      <tr>
                                            <td colspan=2 style="text-align:center;">
                                                    <span style="<?php echo ($r_suspect_striked)?'text-decoration:line-through;':''; ?>;">
                                                            сомнительная
                                                    </span>
                                            </td>
                                            <td colspan=4 style="text-align:center; font-size:8pt; vertical-align: top; line-height: 1.1em;">
                                              (количество цифрами и прописью)
                                            </td>
                                            <td style="text-align:center;">
                                                    <span style="text-decoration: line-through;">монеты (а)</span>
                                            </td>
                                      </tr>
                                      <tr>
                                            <td style="width:17mm; text-align:left;">
                                              номиналом
                                            </td>
                                            <td colspan=3 style="width:28mm; border-bottom:1px solid black; font-weight:bold; padding-left:3mm;">
                                                    <?php echo htmlfix($act_denom_label); ?> 
                                            </td>
                                            <td rowspan=2 style="width:2mm;">&nbsp;</td>
                                            <td style="width:18mm; text-align:left;">
                                              на сумму
                                            </td>
                                            <td colspan=3 style="width:115mm; border-bottom:1px solid black; font-weight:bold;">
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
                                            <td style="width:17mm;">&nbsp;</td>
                                            <td style="width:19mm;">&nbsp;</td>
                                            <td style="width:3mm;">&nbsp;</td>
                                            <td style="width:6mm;">&nbsp;</td>
                                            <td style="width:18mm;">&nbsp;</td>
                                            <td colspan=3 style="width:115mm; text-align:center; font-size:8pt; line-height: 1.1em; vertical-align: top;">
                                              (сумма цифрами и прописью в руб. и коп.)
                                            </td>
                                      </tr>
                                    </table>
                                    <!--Конец заполненной таблицы в квадратных скобочках//-->
				<?php
                            };
			};
                    };
                    ?>
                    <table  class='act145' id="signatures" style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                            <tr>
                                <td rowspan=2 style='width:35%; text-align: left; font-size: 10pt; vertical-align: top; height:25px;'>Подпись лица, производившего пересчёт</td>
                                <td style='width:26%; border-bottom: 1px solid black; text-align:center; font-size:10pt; vertical-align:bottom; font-weight:bold;'>
                                        <?php echo htmlfix($r_operator_post); ?></td>
                                <td style='width:2%;'>&nbsp;</td>
                                <td style='width:15%; border-bottom: 1px solid black; height:25px;'>
                                        &nbsp;</td>
                                <td style='width:2%;'>&nbsp;</td>
                                <td style='width:20%; border-bottom: 1px solid black; text-align: left; font-size: 10pt; height:25px; vertical-align: bottom; font-weight:bold;'>
                                        <?php echo htmlfix($r_operator_name); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(должность)</td>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(подпись)</td>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(инициалы, фамилия)</td>
                            </tr>
                            <tr>
                                <td rowspan=2 style='text-align: left; font-size: 10pt; vertical-align: top; height:25px;'>Подписи лиц, присутствовавших при пересчете</td>
                                <td style='border-bottom: 1px solid black; text-align: center; font-size: 10pt; height:25px; vertical-align: bottom; font-weight: bold;'>
                                        <?php echo htmlfix($r_supervisor_post); ?></td>
                                <td>&nbsp;</td>
                                <td style='border-bottom: 1px solid black; height:25px;'>
                                        &nbsp;</td>
                                <td>&nbsp;</td>
                                <td style='border-bottom: 1px solid black; text-align: left; font-size: 10pt; height:25px; vertical-align: bottom; font-weight: bold;'>
                                <?php echo htmlfix($r_supervisor_name); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(должность)</td>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(подпись)</td>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(инициалы, фамилия)</td>
                            </tr>
                            <tr>
                                <td style='text-align: left;'>&nbsp;</td>
                                <td style='border-bottom: 1px solid black; text-align: center; height:25px;'>
                                        &nbsp;</td>
                                <td>&nbsp;</td>
                                <td style='border-bottom: 1px solid black; height:25px;'>
                                        &nbsp;</td>
                                <td>&nbsp;</td>
                                <td style='border-bottom: 1px solid black; text-align: left; height:25px;'>
                                &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(должность)</td>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(подпись)</td>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(инициалы, фамилия)</td>
                            </tr>
                            <tr>
                                <td style='text-align: left; font-size: 10pt; vertical-align: bottom; height:25px;'>Составитель</td>
                                <td style='border-bottom: 1px solid black; text-align: center; font-size: 10pt; height:25px; vertical-align: bottom; font-weight: bold;'>
                                    <?php echo htmlfix($r_creator_post); ?></td>
                                <td>&nbsp;</td>
                                <td style='border-bottom: 1px solid black; height:25px;'>
                                        &nbsp;</td>
                                <td>&nbsp;</td>
                                <td style='border-bottom: 1px solid black; text-align: left; font-size: 10pt; height:25px; vertical-align: bottom;  font-weight: bold;'>
                                	<?php echo htmlfix($r_creator_name); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(должность)</td>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(подпись)</td>
                                <td>&nbsp;</td>
                                <td style='font-size: 8pt; text-align: center; vertical-align: top; height:18px;'>(инициалы, фамилия)</td>
                            </tr>
                    </table>
                    <table style="width: 171mm;">
                        <tr>
                            <td style="width: 100%; font-size: 7pt;">
                                <hr style="height: 1px; width: 50mm; text-align: left; color: #000000; background: #000000; font-size: 0;border: 0; margin-top: 0; margin-bottom: 1px;">
                                <p style="text-indent: -1em; text-align:justify; line-height: 1.1em; margin-top:0; margin-bottom:0;">
                                <span style="font-size:xx-small; vertical-align:top; line-height: 1.1em;">1</span>
                                При пересчёте сборной пачки банкнот указывается слово «сборная».</p>
                                <p style="text-indent: -1em; text-align:justify; line-height: 1.1em; margin-top:0; margin-bottom:0;">
                                <span style="font-size:xx-small; vertical-align:top; line-height: 1.1em;">2</span>
                                В случае выявления в кредитной организации неплатежеспособного, не имеющего признаков подделки денежного
                                знака Банка России, имеющего признаки подделки денежного знака Банка России слова «сомнительных»
                                зачёркиваются и проставляются слова «неплатежеспособные, не имеющие признаков подделки» или
                                «имеющие признаки подделки» в соответствующих числах и падежах.</p>
                                <p style="text-indent: -1em; text-align:justify; line-height: 1.1em; margin-top:0; margin-bottom:0;">
                                <span style="font-size:xx-small; vertical-align:top; line-height: 1.1em;">3</span>
                                В случае значительного количества сомнительных банкнот Банка России (для кредитных организаций
                                также неплатежеспособных, не имеющих признаков подделки банкнот Банка России, имеющих признаки
                                подделки банкнот Банка России) их серии и номера могут указываться в приложении к акту.</p>
                                <p style="text-indent: -1em; text-align:justify; line-height: 1.1em; margin-top:0; margin-bottom:0;">
                                <span style="font-size:xx-small; vertical-align:top; line-height: 1.1em;">4</span>
                                Заполняются в случае выявления в пачке, кассете с корешками банкнот (мешке с монетой) денежного
                                знака Банка России, номинал которого не соответствует номиналу, указанному на верхней 
                                накладке пачки банкнот, ярлыке к кассете с корешками банкнот (ярлыке к мешку с монетой), 
                                излишнего сомнительного денежного знака Банка России, одновременно недостачи банкноты Банка
                                России (монеты Банка России) и сомнительного денежного знака Банка России.</p>

                            </td>
                        </tr>
                    </table>
                <?php
                    include 'app/view/reports/page_divider.php';
                ?>
                    <table class='act145' style="width: 171mm;">
                        <tr>
                            <td colspan=3 style="text-align: left;">К акту прилагаются:<br/>
                                верхняя и нижняя накладки от пачки банкнот;<br/>

                                        бандероли от всех корешков 
                                <span style="<?php echo ($full_length_striked)?'text-decoration:line-through;':''; ?>;">
                                (полной величины)</span> пачки банкнот,
                                <span style="text-decoration:line-through;">
                                поперечная бандероль от<br/>пачки банкнот;</span><br>
                                <span style="<?php echo ($pack_striked)?'text-decoration:line-through;':''; ?>;">
                                <span style="text-decoration:line-through;">обвязка с пломбой</span>
                                 (полиэтиленовая упаковка с оттиском(ами) клише) от
                                пачки банкнот 
                                <span style="<?php echo ($sack_striked)?'text-decoration:line-through;':''; ?>;">или<br>
                                ярлык от кассеты с корешками банкнот, или обвязка с пломбой и ярлык от мешка с монетой<br/>
                                (кольцо-пломба)</span>, в которой(ом) был(а) обнаружен(а)
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <table class='act145' style='width:171mm;' border=0 cellspacing=0 cellpadding=1>
                            <tr>
                                <td style='width: 25%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    <span style="<?php echo ($over_striked)?'text-decoration:line-through;':''; ?>;">излишек</span>, 
                                    <span style="<?php echo ($short_striked)?'text-decoration:line-through;':''; ?>;">недостача</span>
                                </td>
                                <td rowspan=2 width=8%>&nbsp;</td>
                                <td style='width: 25%; text-align: center; border-bottom-width: 1px; border-bottom-style: solid;'>
                                    банкнот(а)
                                </td>
                                <td rowspan=2 width=42%>&nbsp;</td>
                            </tr>
                            <tr>
                                <td align=middle>
                                    <span style="<?php echo ($suspect_striked)?'text-decoration:line-through;':''; ?>;">сомнительная</span>
                                </td>
                                <td align=middle>
                                    <span style='text-decoration: line-through;'>монеты(а)</span>
                                </td>
                            </tr>
                    </table>
			<?php
			// Блок формирования приложения с серийными номерами сомнительных банкнот
                    if (count($r_serials) > 1) {
                        ?>
                        <table class="act145">
                                <tr>
                                        <td>Приложение к акту.<br/>Серийные номера сомнительных банкнот:
                                        </td>
                                </tr>
                        <?php
                        foreach ($r_serials as $r_serial) {
                                ?>
                                <tr>
                                        <td><?php echo htmlfix($r_serial); ?>	</td>
                                </tr>

                        <?php
                        };
                        echo '</table>';
                        // Конец блока формирования приложения со списком серийных номеров сомнительных банкнот
					};
                        
 ?>
