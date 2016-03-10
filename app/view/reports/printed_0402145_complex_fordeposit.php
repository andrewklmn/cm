<?php

    /*
     * Скрипт формирования обобщённого акта 0402145 для случая единый акт для каждого типа расхождения и каждого номинала
     */
    if (!isset($c)) exit;
    
    switch ($action_name) {
		case 'data_prepare':
            // The following code is performed when gathering report parameters
            break;
		case 'data_report':
		// Определяем номер выводимого акта
			$deposit_id = $_GET['id'];
			$row = fetch_row_from_sql('
				SELECT RecLastChangeDatetime
				FROM DepositRecs
				WHERE DepositRecId = "'.$deposit_id.'"
			;');
			$report_datetime = $row[0];
         $r_midnight = substr($report_datetime,0,4).'-'.substr($report_datetime,5,2).'-'.substr($report_datetime,8,2).' 00:00:00';  
         
         //echo $r_midnight;          
			// Define the numbering of acts and assign the initial count to $act_count variable
			
                        $sql = '
                            SELECT 
                                COUNT(*)
                            FROM 
                                DepositRecs 
                            WHERE 
                                RecLastChangeDatetime BETWEEN "'.addslashes($r_midnight).'" 
                                AND "'.addslashes($report_datetime).'"
                                AND ReconcileStatus = 1 
                                AND ServiceRec = 0
                                AND IsBalanced = 0
                        ;';
                        $row = fetch_row_from_sql($sql);
                        
                        //echo $sql;
			$r_act_number = $row[0]; // Номер создаваемого акта


					include('single_0402145.php');
				
				
				
           break;
        };
    
?>
