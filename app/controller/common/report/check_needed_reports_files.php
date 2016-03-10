<?php

/*
 * Проверяет наличие файлов моделей отчетов 
 */

    if (!isset($c)) exit;
    
    $missing = array();
    
    // Проверяем отчеты в XML
    foreach ($xmls as $key=>$report) {
        if ($report['FileName']!='') {
            if (!file_exists('app/view/reports/'.$report['FileName'])) {
                $missing[] = $report['FileName'];
            };
        };
        include 'app/controller/common/report/add_reports_seq.php';
    };
    foreach ($reports as $key=>$report) {
        if ($report['FileName']!='') {
            if (!file_exists('app/view/reports/'.$report['FileName'])) {
                $missing[] = $report['FileName'];
            };
        };
        include 'app/controller/common/report/add_reports_seq.php';
    };
    
    if (count($missing)>0) {
        $data['error'] = 'Отчеты не были сгенерированы. Нет исходных файлов: <br/>'.  implode(', ', $missing);
        include 'app/view/error_message.php';
        ?>
            <div class="container no-print">
                <hr/>
                <a class="btn btn-primary btn-large" href="?c=reports">Back to reports page</a>
            </div>
        <?php
        exit;
    };
    

?>
