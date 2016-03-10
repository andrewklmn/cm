<?php

/*
 * Сохраняем в таблицу ReportSignes сиквенцию отчета и код подписанта
 */

        if (!isset($c)) exit;
        
        
        if (isset($_POST['report_set_id']) 
                AND (int)$_POST['report_set_id']>0 
                AND isset($_POST['selected_signers'])
                AND count($signed_reports)>0) {
            
            // Получаем всех подписантов
            $selected_signers = explode(',', $_POST['selected_signers']);
            do_sql('LOCK TABLES Reports WRITE, ReportSignes WRITE, ReportTypes WRITE;');    
            
            
            // Получаем все номера сиквенций для отчетов с подписантами если они были
            $seq_id = array();
            $row = get_array_from_sql('
                SELECT DISTINCT
                    `Reports`.`SeqId`
                FROM 
                    `Reports`
                LEFT JOIN
                    ReportTypes ON ReportTypes.ReportTypeId=Reports.ReportTypeId
                WHERE
                    `Reports`.`ReportSetId`="'.addslashes($_POST['report_set_id']).'"
                    AND ReportTypes.ReportTypeId IN ('.  implode(',', $signed_reports).')
            ;');
            
            foreach ($row as $value) {
                $seq_id[]=$value[0];
            };
            
            // Удаляем подписантов если какие-то уже есть
            if (count($seq_id)>0) {
                do_sql('
                    DELETE FROM 
                        `cashmaster`.`ReportSignes`
                    WHERE 
                        `ReportSignes`.`RepSeqId` IN ( '.implode(',', $seq_id).' )
                ;');
            };
                        
            // Заполняем новыми подписантами нужные сиквенции
            foreach ($seq_id as $value) {
                foreach ($selected_signers as $val) {
                    do_sql('
                        INSERT INTO `cashmaster`.`ReportSignes`
                            (
                                `RepSeqId`,
                                `SignerId`
                            )
                        VALUES
                            (
                                "'.addslashes($value).'",
                                "'.addslashes($val).'"
                            )
                    ;');
                };
            };
            
            do_sql('UNLOCK TABLES;');
        };
?>
