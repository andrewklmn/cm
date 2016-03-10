<?php

    /*
     * Справка об обработанных банкнотах для сценариев с подготовкой
     */
    if (!isset($c)) exit;
    include_once 'app/model/get_short_iof_by_user_id.php';
    
    switch ($action_name) {
        case 'data_prepare':
            // Эта часть выполняется во время запроса параметров для генерации отчетов
                // Пример задания одиночного параметра
                ?>
            <table>
            	<tr>
            		<td>
                   Номер кладовой: 
                   <input 
                       class='stat' 
                       type='text' 
                       name="<?php echo $value['ReportTypeId']; ?>|kladovaya" 
                       value=""/>
                       &nbsp;&nbsp;
                 	</td>
                 	<td>
                   Номер акта об уничтожении: 
                   <input 
                       class='stat' 
                       type='text' 
                       name="<?php echo $value['ReportTypeId']; ?>|shred_act_number" 
                       value=""/>
                   &nbsp;&nbsp;
                 	</td>
                 	<td>
                   Номер акта об расхождениях информации: 
                   <input 
                       class='stat' 
                       type='text' 
                       name="<?php echo $value['ReportTypeId']; ?>|discrepancy_act_number" 
                       value=""/>
                   &nbsp;&nbsp;
                  </td>
                 </tr>
                </table>
                <?php
                    // Обрати внимание на метод задания имени переменной в инпуте!
                    // Фактически она формируется на лету в виде
                    // 'Id|Name' - где Id - айди типа отчета, Name - имя переменной
                     // Это сделано для того чтобы не пересекались одинаковые названия параметров
                    // в отчетах разного типа в одном РепортСете
            break;
        case 'data_report':
            // Эта часть выполняется при генерации отчета на экран
         if(count($recs) > 0) {
			$r_report_label = 'Справка об обработанных банкнотах, форма 0402013'; // Report label is used for selecting scenarios
			$row = fetch_assoc_row_from_sql('
					SELECT 
						CashCenterName, 
						CashCenterCode
					FROM SystemGlobals 
						WHERE SystemGlobalsId = "1"
					;');            
            $site_name = $row['CashCenterName'];
            $site_code = $row['CashCenterCode'];
            
				// Формируем дату этого отчёта
				$r_rep_year = substr($report_datetime,2,2);
				$r_rep_m = substr($report_datetime,5,2);
				$r_rep_day = substr($report_datetime,8,2);
				if ($r_rep_m == 01) {
					$r_rep_month = "января";
				} elseif ($r_rep_m == 02) {
					$r_rep_month = "февраля";
				} elseif ($r_rep_m == 03) {
					$r_rep_month = "марта";
				} elseif ($r_rep_m == 04) {
					$r_rep_month = "апреля";
				} elseif ($r_rep_m == 05) {
					$r_rep_month = "мая";
				} elseif ($r_rep_m == 06) {
					$r_rep_month = "июня";
				} elseif ($r_rep_m == 07) {
					$r_rep_month = "июля";
				} elseif ($r_rep_m == 08) {
					$r_rep_month= "августа";
				} elseif ($r_rep_m == 09) {
					$r_rep_month = "сентября";
				} elseif ($r_rep_m == 10) {
					$r_rep_month = "октября";
				} elseif ($r_rep_m == 11) {
					$r_rep_month = "ноября";
				} else{
					$r_rep_month = "декабря";
				};
            
				$row = fetch_assoc_row_from_sql('
					SELECT CashRoomName FROM CashRooms 
						WHERE Id = "'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
				;');            
            $cash_room = $row['CashRoomName'];
            $safe = get_value_from_report_saves('kladovaya');        // Вот эта переменная уже использована в отчете
            $report_date = date("d.m.Y");
            $shred_act_date = get_value_from_report_saves('shred_act_date');
            $shred_act_number = get_value_from_report_saves('shred_act_number');
            $discrepancy_act_date = get_value_from_report_saves('discrepancy_act_date');
            $discrepancy_act_number = get_value_from_report_saves('discrepancy_act_number');
            $curr_year = "1997";
			// Define the scenarios for which this report is ON at the time
			$rows = get_array_from_sql('
				SELECT DISTINCT ScenarioId
				FROM ScenReportTypes
					INNER JOIN ReportTypes ON ScenReportTypes.ReportTypeId = ReportTypes.ReportTypeId
				WHERE ReportLabel="'.addslashes($r_report_label).'"
					AND IsUsed=1
				;');
			$r_scenarios = array();
			foreach ($rows as $row) {
				$r_scenarios[] = $row[0];
			};
			// Ниже формируется общий массив СВЕРЕННЫХ депозитов за отчётный период
			// Following is the global set of RECONCILED deposits for the reported period
				$recsid = array();
				foreach ($recs as $rec) {
					$recsid[] = $rec[0];				
				};
			if (count($recsid) > 0) {
			$rows = get_array_from_sql('
				SELECT DepositRecId
				FROM DepositRecs
				WHERE DepositRecId IN ('.implode(',',$recsid).')
				AND ScenarioId IN ('.implode(',',$r_scenarios).')
				;');
				// Переопределяем общий массив СВЕРЕННЫХ депозитов, ограничиваясь теми, сценарии которых включены для данного отчёта
				// Redefine the global set of deposits limiting to only those that has been processed with scenarios switched ON for the report
				unset($recsid);				
				$recsid = array();
 				foreach ($rows as $row) {
					$recsid[] = $row[0];				
				};
				// Находим дату и время формирования последнего ReportSet'а
				$row = fetch_row_from_sql('
					SELECT MAX(SetDatetime) FROM ReportSets
				;');
				$r_last_set_datetime = $row[0];
				
				// Находим массив всех не обработанных депозитов, и называем его $r_unfinished
				$rows = get_array_from_sql('
					SELECT DepositRecId
					FROM DepositRecs
					WHERE ReconcileStatus = 0
				;');
				$r_unfinished = array();
 				foreach ($rows as $row) {
					$r_unfinished[] = $row[0];				
				};

				// Находим, кто создал этот ReportSet
				if(isset($_GET['id'])) {
					$row = fetch_row_from_sql('
						SELECT CreatedBy
						FROM ReportSets
						WHERE SetId = "'.$_GET['id'].'"
					;');
					$r_creator_id = $row[0];
					$r_creator_name = get_short_iof_by_user_id($row[0]);
					$row = fetch_row_from_sql('
						SELECT UserPost
						FROM UserConfiguration
						WHERE UserId = "'.$r_creator_id.'"
					;');
					$r_creator_post = $row[0];
				} else {
					$r_creator_post = $_SESSION[$program]['UserConfiguration']['UserPost'];
					$r_creator_name = get_short_iof_by_user_id($_SESSION[$program]['UserConfiguration']['UserId']);
				}
				// Определим, были ли расхождения (нужно для формирования строки с актом о расхождениях)
				// Как оказалось, это не нужно, но запрос оставим на всякий случай
				$row = fetch_row_from_sql('
					SELECT * 
					FROM DepositRecs
					WHERE DepositRecId IN ('.implode(',',$recsid).')
						AND IsBalanced <> 1
						AND ReconcileStatus = 1
						AND ServiceRec = 0
				;');
				$r_discrepancy = count($row[0]);
				
				// Определяем индексы депозитов, которые были обработаны или планировались к обработке
				// Далее мы НЕ РАЗВОРАЧИВАЕМ по индексам, ним нужно будет только показать список индексов
				$r_indexes = array();
				$cnt = fetch_row_from_sql('
					SELECT 
						COUNT(*) 
					FROM 
						DepositRecs 
					INNER JOIN 
						DepositIndex
							ON DepositRecs.DepositIndexId = DepositIndex.DepositIndexId
					WHERE 
							DepositIndex.IndexValue = "3"
							AND DepositRecId IN ('.implode(',', array_merge($recsid, $r_unfinished)).')
					;');
 
				if($cnt[0] > 0) {
					$r_indexes[] = '3';
				};           
				$cnt = fetch_row_from_sql('
					SELECT 
						COUNT(*) 
					FROM 
						DepositRecs 
					INNER JOIN 
						DepositIndex
							ON DepositRecs.DepositIndexId = DepositIndex.DepositIndexId
					WHERE 
							DepositIndex.IndexValue = "4"
							AND DepositRecId IN ('.implode(',', array_merge($recsid, $r_unfinished)).')
					;'); 
				if($cnt[0] > 0) {
					$r_indexes[] = '4';
				};           
				$cnt = fetch_row_from_sql('
					SELECT 
						COUNT(*) 
					FROM 
						DepositRecs 
					INNER JOIN 
						DepositIndex
							ON DepositRecs.DepositIndexId = DepositIndex.DepositIndexId
					WHERE 
							DepositIndex.IndexValue = "5"
							AND DepositRecId IN ('.implode(',', array_merge($recsid, $r_unfinished)).')
					;'); 
				if($cnt[0] > 0) {
					$r_indexes[] = '5';
				};           
				
				// Определяем номиналы (DenomId) депозитов, которые были обработаны или планировались к обработке
				// (для которых есть заявленное) и храним их в массиве $r_denoms_id
				$r_denomrows = get_array_from_sql('
					SELECT 
						DISTINCT DenomId
					FROM DepositDenomTotal
					WHERE ExpectedCount <>0
					AND DepositReclId IN ('.implode(',', array_merge($recsid, $r_unfinished)).');');
				$r_denoms_id = array();

				foreach ($r_denomrows as $r_denomrow) {
					$r_denoms_id[] = $r_denomrow[0];				
				};

			// Для полученных DenomId определяем собственно номинал, ярлык и заявленную сумму
            $denoms = array();
				foreach ($r_denoms_id as $r_value) {
					
					$row = fetch_row_from_sql('
						SELECT 
							Denoms.Value, DenomLabel, SUM(ExpectedCount)
						FROM 
							DepositDenomTotal
						INNER JOIN 
							Denoms ON DepositDenomTotal.DenomId = Denoms.DenomId
						WHERE 
							DepositReclId IN ('.implode(',', array_merge($recsid, $r_unfinished)).')
						GROUP BY Denoms.DenomId
						HAVING DenomId = "'.addslashes($r_value).'";');
					$r_denvalue = $row[0];					
					$r_label = $row[1];
					$r_expected = $row[2] * $r_denvalue;
					//Запрос, вычисляющий unfit
					$row = fetch_row_from_sql('
						SELECT 
							SUM(sorter.sorter_count),
							SUM(recon.CullCount)
						FROM (
							SELECT DepositReclId, DenomId, ExpectedCount
							FROM DepositDenomTotal
							WHERE ExpectedCount <>0
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
						ON expected.DepositReclId=recon.DepositRecId AND expected.DenomId=recon.DenomId AND expected.DenomId=recon.DenomId
						GROUP BY expected.DenomId
						HAVING expected.DenomId="'.addslashes($r_value).'";');
					$r_unfit = ($row[0] + $row[1]) * $r_denvalue;
					
					
					//Вставить запрос, вычисляющий shred
					$row = fetch_row_from_sql('
						SELECT 
							SUM(sorter.sorter_count),
							SUM(recon.CullCount)
						FROM (
							SELECT DepositReclId, DenomId, ExpectedCount
							FROM DepositDenomTotal
							WHERE ExpectedCount <>0
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
						ON expected.DepositReclId=recon.DepositRecId AND expected.DenomId=recon.DenomId AND expected.DenomId=recon.DenomId
						GROUP BY expected.DenomId
						HAVING expected.DenomId="'.addslashes($r_value).'";');
					$r_shred = ($row[0] + $row[1]) * $r_denvalue;
					
					// Запрос, вычисляющий заявленное, но не обработанное (с проверкой, есть ли таковое)
					if (count($r_unfinished) > 0) {
						$row = fetch_row_from_sql('
							SELECT IFNULL(ExpectedCount, 0)
							FROM DepositDenomTotal
							WHERE DepositReclId IN ('.implode(',',$r_unfinished).')
								AND DenomId="'.addslashes($r_value).'"
						;');
						$r_unprocessed = $row[0] * $r_denvalue;
					} else {
						$r_unprocessed = 0;
					}
					$denoms[] = array(
                    'label' => $r_label,
                    'expected' => $r_expected,
                    'unfit' =>  $r_unfit,
                    'shred' => $r_shred,
                    'unprocessed' => $r_unprocessed
                );
  /*              
 echo '<pre>';
 echo 'Что оно тут выводит?';
print_r($denoms);
echo '</pre>';
*/                
                
				};
				
                      
            $r_signers = get_report_signers();

            // Инициализация итоговых полей
            $unfit_total= 0;
            $shred_total= 0;
            $expected_total= 0;
            $unprocessed_total = 0;

        ?><style>
            table#name {
                width:250mm;
            }
            table#name caption {
                text-align: right; 
                font-size: 10pt; 
                font-weight: normal;
                font-family: "Times New Roman", serif;
            }
            table#name th {
                text-align: center; 
                font-weight: normal; 
                font-size: 12pt;
                font-family: "Times New Roman", serif;
                font-weight: normal;
            }
            table#name td {
                text-align: right; 
                font-weight: normal; 
                font-size: 12pt;
                font-family: "Times New Roman", serif;
                font-weight: normal;
                padding-left: 3px;
                padding-right: 3px;
                vertical-align: middle;
            }
        table.formcode {
                font-family: "Times New Roman", serif;
                font-weight: normal;
                width:80mm;
                text-align: center;
        }
        table.name {
                font-family: "Times New Roman", serif;
                font-size: 12pt;
        }
        table#signatures td {
                font-family: "Times New Roman", serif; 
                font-size: 12pt; 
                font-weight: normal; 
                height: 30px;
        }
				
        </style>
        <div class="rotated">
            <table class="rotated">
                <tr>
                    <td align="center" style="">
                        <table class="formcode" style='width:250mm;'>
                                <tr>
                                        <td style="width:85mm; text-align:left; font-size: 12pt; line-height: 1.7em; border-bottom: 1px solid black;">
                                                Банк России
                                        </td>
                                        <td style='width:85mm;'>&nbsp;</td>
                                        <td rowspan="3" style='width:80mm; text-align: right; vertical-align:top;'>
                                                <table class="formcode"  border=1 cellspacing="0" cellpadding="1">
                                                <tr>
                                                <td style="line-height: 1.2em;">
                                                        Код формы документа по ОКУД
                                                </td>
                                        </tr>
                                        <tr>
                                                <td style="line-height: 1.2em;">
                                                        0402013
                                                </td>
                                        </tr>
                                                </table>
                                        </td>
                                </tr>
                                <tr>
                                        <td style="text-align: center; font-size: 10pt; vertical-align:top; line-height:1.1em; border-bottom: 1px solid black;">
                                                (наименование организации)
                                        </td>
                                        <td>&nbsp;</td>
                                </tr>
                                <tr>
                                        <td style="text-align: left; font-size: 12pt; vertical-align:top; border-bottom: 1px solid black;">
                                                <?php echo htmlfix($site_name); ?>
                                        </td>
                                        <td>&nbsp;</td>
                                </tr>
                                <tr>
                                        <td style="text-align: left; font-size: 10pt; vertical-align:top; line-height:1.1em;">
                                                (наименование подразделения Банка России)
                                        </td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                </tr>
                        </table>
                        <table class="name" style='width:250mm;' border='0' cellspacing='0' cellpadding='1'>
                            <tr>
                                <td colspan="8" style='letter-spacing:0.4em; text-align: center; font-size:12pt; font-weight:bold;'>
                                СПРАВКА
                               </td>
                            </tr>
                            <tr>
                                <td colspan="8" style='text-align: center; font-size:12pt; font-weight:bold;'>
                                                        об обработанных банкнотах резервных фондов банкноты и монеты
                                                </td>
                            </tr>
                            <tr style='line-height: 1.1em;'>
                                <td style='width:37%'>&nbsp;</td>
                                <td style='width:4%; text-align:right; vertical-align: bottom; font-size:12pt;'>за «</td>
                                <td style='width:3%; text-align: center; vertical-align: bottom; border-bottom: 1px solid black; font-size:12pt;'>
                                        <?php echo ' '.htmlfix($r_rep_day).' '; ?>
                                </td>
                                <td style='width:1%; text-align: center; vertical-align: bottom; font-size:12pt;'>»</td>
                                <td style='width:12%; text-align: center; vertical-align: bottom; font-size:12pt; border-bottom: 1px solid black;'>
                                                <?php echo htmlfix($r_rep_month); ?>
                                </td>
                                <td style='width:2%; text-align: center; vertical-align: bottom; font-size:12pt;'>20</td>
                                <td style='width:4%; text-align: center; vertical-align: bottom; font-size:12pt; border-bottom: 1px solid black;'>
                                                <?php echo htmlfix($r_rep_year); ?>
                                </td>
                                                         <td style='width:37%; text-align: left; vertical-align: bottom; font-size:12pt;'>г.</td>
                           </tr>
                        </table>
                        <table border=0 style="width: 150mm; font-family: 'Times New Roman', serif;">
                                <tr>
                                        <td style='width:35mm; text-align:left; vertical-align: bottom; font-size:12pt;'>
                                                Касса пересчёта
                                        </td>
                                        <td colspan=2 style='width:115mm; text-align: left; border-bottom: 1px solid black; font-size:12pt;'>
                                                <?php echo htmlfix($cash_room); ?>
                                        </td>
                                </tr>
                                <tr>
                                        <td colspan=2 style='width:42mm; text-align: left; vertical-align: bottom; font-size:12pt;'>
                                                Получено из кладовой
                                        </td>
                                        <td style='width:108mm; text-align: left; border-bottom: 1px solid black; font-size:12pt;'>
                                                <?php echo htmlfix($safe); ?>
                                        </td>
                                </tr>
                                <tr style="line-height: 0.9em;">
                                        <td style='width:35mm;'>&nbsp;</td>
                                        <td style='width:7mm;'>&nbsp;</td>
                                        <td style='width:108mm;text-align: center; font-size:10pt; line-height: 0.9em; vertical-align:top;'>
                                                (наименование и (или) номер кладовой)
                                        </td>
                                </tr>
                        </table>
                        <table style="width: 250mm;" id="name" border="1" cellspacing=0 cellpadding=1>
                        <caption style="line-height:1.1em;">Сумма в рублях</caption>
                            <tr>
                                <th rowspan=3 style="width:40mm; text-align: center; line-height: 1.2em;">
                                        Номинал<br/>банкнот<br>образца
                                        <span style="text-decoration:underline;">
                                                <?php echo htmlfix($curr_year); ?>
                                        </span>
                                                года
                                </th>
                                <th rowspan=3 style="width:50mm; text-align: center; line-height: 1.2em;">
                                Получено на<br/>обработку,<br/>индекс валюты «<span style="text-decoration:underline;">
                                        <?php echo htmlfix(implode(',',$r_indexes)); ?>
                                </span>»
                                </th>
                                <th colspan=3 style="width:120mm; text-align: center; line-height: 1.2em;">По результатам обработки</th>
                                <th rowspan=3 style="width:40mm; text-align: center; line-height: 1.2em;">Не обработано</th>
                           </tr>
                           <tr>
                                <th colspan=2 style="width:80mm; text-align: center; line-height: 1.2em;">отсортировано</th>
                                <th rowspan=2 style="width:40mm; text-align: center; line-height: 1.2em;">уничтожено</th>
                            </tr>
                            <tr>
                                <th style="width:40mm; text-align: center; line-height: 1.2em;">в разряд годных</th>
                                <th style="width:40mm; text-align: center; line-height: 1.2em;">в разряд ветхих</th>
                            </tr>
                            <tr style="line-height: 1.2em;">
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                               <th>6</th>
                            </tr>
                            <?php 
                                foreach ($denoms as $value) {
                                    ?>
                                      <tr style="height: 25px;">
                                        <td style="text-align:center; border-bottom: 1px solid black; border-right: 1px solid black;">
											<?php echo htmlfix($value['label']); ?></td>
                                        <td style="text-align:right; border-bottom: 1px solid black; border-right: 1px solid black;">
                                                <?php echo number_format($value['expected'],0,',',' '); ?>
                                        </td>
                                        <td style="text-align:right; border-bottom: 1px solid black; border-right: 1px solid black;">
                                                <?php echo ($value['expected'] - $value['shred'] - $value['unfit']-$value['unprocessed']>0)?number_format($value['expected'] - $value['shred'] - $value['unfit']-$value['unprocessed'],0,',',' '):'&nbsp'; ?>
                                        </td>
                                        <td style="text-align:right; border-bottom: 1px solid black; border-right: 1px solid black;">
                                                <?php echo ($value['unfit']>0)?number_format($value['unfit'],0,',',' '):'&nbsp;'; ?>
                                        </td>
                                        <td style="text-align:right; border-bottom: 1px solid black; border-right: 1px solid black;">
                                                <?php echo ($value['shred']>0)?number_format($value['shred'],0,',',' '):'&nbsp'; ?>
                                        </td>
                                        <td style="text-align:right; border-bottom: 1px solid black;">
											<?php echo ($value['unprocessed']>0)?number_format($value['unprocessed'],0,',',' '):'&nbsp'; ?>
                                        </td>
                                        </tr>
                                    <?php 

                                    $unfit_total += $value['unfit'];
                                    $shred_total += $value['shred'];
                                    $expected_total += $value['expected'];
                                    $unprocessed_total += $value['unprocessed'];
                                    $fit_total = $expected_total - $shred_total - $unfit_total;
                                };    
                            ?>
                            <tr style="height: 25px;">
                                <td style="text-align:right; border-right: 1px solid black;">Всего:</td>
                                <td style="text-align:right; border-right: 1px solid black;"><?php echo ($unprocessed_total + $expected_total>0)?number_format($unprocessed_total + $expected_total,0,',',' '):'&nbsp;'; ?></td>
                                <td style="text-align:right; border-right: 1px solid black;"><?php echo ($fit_total>0)?number_format($fit_total,0,',',' '):'&nbsp;'; ?></td>
                                <td style="text-align:right; border-right: 1px solid black;"><?php echo ($unfit_total>0)?number_format($unfit_total,0,',',' '):'&nbsp;'; ?></td>
                                <td style="text-align:right; border-right: 1px solid black;"><?php echo ($shred_total>0)?number_format($shred_total,0,',',' '):'&nbsp;'; ?></td>
                                <td style="text-align:right;">&nbsp;</td>
                            </tr>
                        </table>
        
                        <table border=0 class="name" style="width: 250mm;">
                            <tr style='line-height: 1.1em;'>
                                <td style='width:30%; text-align:left; padding-left: 5px; vertical-align: bottom; font-size:12pt;'>
                                        Акт об уничтожении ветхих банкнот от
                                </td>
                                 <td style='width:2%; text-align:right; padding-left: 5px; vertical-align: bottom; font-size:12pt;'>
                                        «
                                </td>
                               <td style='width:3%; text-align: center; vertical-align: bottom; border-bottom: 1px solid black; font-size:12pt;'>
                                        <?php echo (isset($shred_total) AND $shred_total > 0)?' '.htmlfix($r_rep_day).' ':'&nbsp;'; ?>
                                </td>
                                <td style='width:1%; text-align: center; vertical-align: bottom; font-size:12pt;'>»</td>
                                <td style='width:10%; text-align: center; vertical-align: bottom; font-size:12pt; border-bottom: 1px solid black;'>
                                                <?php echo (isset($shred_total) AND $shred_total > 0)?htmlfix($r_rep_month):'&nbsp;'; ?>
                                </td>
                                <td style='width:2%; text-align: center; vertical-align: bottom; font-size:12pt;'>20</td>
                                <td style='width:4%; text-align: center; vertical-align: bottom; font-size:12pt; border-bottom: 1px solid black;'>
                                                <?php echo (isset($shred_total) AND $shred_total > 0)?htmlfix($r_rep_year):'&nbsp;'; ?>
                                </td>
                                                         <td style='width:5%; text-align: left; vertical-align: bottom; font-size:12pt;'>г. №</td>
                                                         <td style='width:10%; text-align: left; vertical-align: bottom; font-size:12pt; border-bottom: 1px solid black;'>
                                                                <?php echo (isset($shred_total) AND $shred_total > 0)?htmlfix($shred_act_number):'&nbsp;'; ?>
                                                         </td>
                                                         <td style='width:30%; text-align: left; vertical-align: top; font-size: 8pt; line-height: 1.1em;'>
                                                                1
                                                         </td>
                           </tr>
                           <tr style='line-height: 1.1em;'>
                                <td style='width:30%; text-align:left; padding-left: 5px; vertical-align: bottom; font-size:12pt;'>
                                        Акт о расхождении информации от
                                </td>
                                <td style='width:2%; text-align:right; padding-left: 5px; vertical-align: bottom; font-size:12pt;'>
                                        «
                                </td>
                                <td style='width:3%; text-align: center; vertical-align: bottom; border-bottom: 1px solid black; font-size:12pt;'>
                                        <?php echo (isset($discrepancy_act_number) AND $discrepancy_act_number != '')?' '.htmlfix($r_rep_day).' ':'&nbsp;'; ?>
                                </td>
                                <td style='width:1%; text-align: center; vertical-align: bottom; font-size:12pt;'>»</td>
                                <td style='width:10%; text-align: center; vertical-align: bottom;  font-size:12pt; border-bottom: 1px solid black;'>
                                                <?php echo (isset($discrepancy_act_number) AND $discrepancy_act_number != '')?htmlfix($r_rep_month):'&nbsp;'; ?>
                                </td>
                                <td style='width:2%; text-align: center; vertical-align:  bottom; font-size:12pt;'>20</td>
                                <td style='width:4%; text-align: center; vertical-align:  bottom; font-size:12pt; border-bottom: 1px solid black;'>
                                                <?php echo (isset($discrepancy_act_number) AND $discrepancy_act_number != '')?htmlfix($r_rep_year):'&nbsp;'; ?>
                                </td>
                                <td style='width:5%; text-align: left;  vertical-align: bottom; font-size:12pt;'>г. №</td>
                                <td style='width:10%; text-align: left;  vertical-align: bottom; font-size:12pt; border-bottom: 1px solid black;'>
                                	<?php echo (isset($discrepancy_act_number) AND $discrepancy_act_number != '')?htmlfix($discrepancy_act_number):'&nbsp;'; ?>
                                </td>
                                <td style='width:30%; text-align: left;  vertical-align: top; font-size: 8pt; line-height: 1.1em;'>
                                    2
                                </td>
                           </tr>
                        </table>
                            <!-- Пример вставки имени котролера сгенерившего отчет -->
                      <table style="margin: 0; width: 250mm;">
                      	<tr>
                      		<td style="width:180mm">
                            <table  id="signatures" style='width:250mm;' border='0' cellspacing='0' cellpadding='1'>
                                   <tr>
                                            <td colspan=2 style='border-bottom: 1px solid black; text-align: left; height:25px;;'>
                                                    <?php echo $_SESSION[$program]['UserConfiguration']['UserPost']; ?></td>
                                            <td>&nbsp;</td>
                                            <td colspan=2 style='border-bottom: 1px solid black; height:25px;'>
                                                    &nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td style='border-bottom: 1px solid black; text-align: left; height:25px;'>
                                            <?php echo htmlfix(get_short_iof_by_user_id($_SESSION[$program]['UserConfiguration']['UserId'])); ?>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td colspan=2 style='font-size: 10pt; text-align: center; vertical-align: top; height:15px;'>(должность)</td>
                                            <td>&nbsp;</td>
                                            <td colspan=2 style='font-size: 10pt; text-align: center; vertical-align: top; height:15px;'>(подпись)</td>
                                            <td>&nbsp;</td>
                                            <td style='font-size: 10pt; text-align: center; vertical-align: top; height:15px;'>(инициалы, фамилия)</td>
                                    </tr>

                           <?php
                           $signers = get_report_signers();
                                foreach ($signers as $signer) {
                                    ?>
                                        <tr>
                                            <td colspan=2 style='width:37%; border-bottom: 1px solid black; text-align:left; height:25px;'>
                                                    <?php echo htmlfix($signer[0]); ?></td>
                                            <td style='width:3%;'>&nbsp;</td>
                                            <td colspan=2 style='width:25%; border-bottom: 1px solid black; height:25px;'>&nbsp;</td>
                                            <td style='width:3%;'>&nbsp;</td>
                                            <td style='width:32%; border-bottom: 1px solid black; text-align:left; height:25px;'>
                                                <?php echo htmlfix($signer[1]); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan=2 style='font-size: 10pt; text-align: center; vertical-align: top; height:18px;'>(должность)</td>
                                            <td>&nbsp;</td>
                                            <td colspan=2 style='font-size: 10pt; text-align: center; vertical-align: top; height:18px;'>(подпись)</td>
                                            <td>&nbsp;</td>
                                            <td style='font-size: 10pt; text-align: center; vertical-align: top; height:18px;'>(инициалы, фамилия)</td>
                                        </tr>
                                    <?php
                                };
                            ?>
                            <tr>
                                <td style='width:15%; text-align:left; height:25px;'>Составитель</td>
                                <td style='width:22%; border-bottom: 1px solid black; text-align:left; height:25px;'>
                                        <?php echo htmlfix($r_creator_post); ?></td>
                                <td style='width:3%;'>&nbsp;</td>
                                <td colspan=2 style='width:25%; border-bottom: 1px solid black; height:25px;'>&nbsp;</td>
                                <td style='width:3%;'>&nbsp;</td>
                                <td style='width:32%; border-bottom: 1px solid black; text-align:left; height:25px;'>
                                <?php echo htmlfix($r_creator_name); ?>
                                </td>
                            </tr>
                            <tr>
                            	  <td style='border-bottom: 1px solid black;'>&nbsp;</td>	
                                <td style='font-size: 10pt; text-align: center; vertical-align: top; height:18px;'>(должность)</td>
                                <td>&nbsp;</td>
                                <td colspan=2 style='font-size: 10pt; text-align: center; vertical-align: top; height:18px;'>(подпись)</td>
                                <td>&nbsp;</td>
                                <td style='font-size: 10pt; text-align: center; vertical-align: top; height:18px;'>(инициалы, фамилия)</td>
                            </tr>
                            <tr>
                            	<td colspan="4" style='width: 40%; border-top: 1px solid black; text-align:left;'>
                                        <p style="text-align:justify; line-height: 1.1em; margin-top:0; margin-bottom:0; font-family: 'Times New Roman', serif; font-size:10pt;">
                                        <span style="font-size:xx-small; vertical-align:top; line-height: 1.1em;">1</span>
                                        Заполняется в случае осуществления операции по уничтожению банкнот.</p>
                                        <p style="text-align:justify; line-height: 1.1em; margin-top:0; margin-bottom:0; font-family: 'Times New Roman', serif; font-size:10pt;">
                                        <span style="font-size:xx-small; vertical-align:top; line-height: 1.1em;">2</span>
                                        Заполняется в случае возникновения расхождения информации.</p>
                            	
                            	</td>
                            	<td colspan="3" style='width: 60%;'>&nbsp;<td>
                        	</tr>                
								</table>
							</td>
							<td style="width:70mm">&nbsp;</td>
						</tr>
					</table>

                    </td>
                </tr>
            </table>
        </div>
        <?php
            //include 'app/view/reports/page_divider.php';
            break;
          };
       };
    };
    
?>
