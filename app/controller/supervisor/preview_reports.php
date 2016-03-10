<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;
        
        $data['title'] = $_SESSION[$program]['lang']['supervisor_preview_reports'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
        
        include_once './app/model/reconciliation/get_reconciled_deposit_by_rec_id.php';
        include 'app/controller/common/report/check_operation_day_status.php';
        
        $b = explode('|',$_SESSION[$program]['lang']['create_reports_confirm_buttons']);
        $t = explode('|',$_SESSION[$program]['lang']['create_preview_reports_confirmation_header']);
        $h = explode('|',$_SESSION[$program]['lang']['reports_table_headers']);
        
        
        $row= fetch_row_from_sql('
            SELECT
                MAX(`ReportSets`.`SetDateTime`)
            FROM 
                `cashmaster`.`ReportSets`
            WHERE
                ReportSets.CashRoomId="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
        ;');
        if (!isset($row[0]) OR $row[0]=='' OR $row[0]=='0000-00-00 00:00:00') {
            $_POST['start_datetime'] = '1972-11-29 00:00:00';
        } else {
            $_POST['start_datetime'] = $row[0];
        };        
        $_POST['stop_datetime'] = date('Y-m-d H:i:s', time());
        
        
        // Устанавливаем текущий рабочий рекорд_сет_айди
        include 'app/controller/common/report/set_active_record_set_id.php';
        
        // Получаем сценарии использованные за этот период
        include 'app/controller/common/report/get_scens_by_reportset_period.php';
        
        // Получаем отчеты по использованным сценариям
        include 'app/controller/common/report/get_reports_by_used_scen_ids.php';
        
        
        $need_signers = false;
        $signed_reports = array();
        /*
        foreach ($xmls as $report) {
            // Проверяем есть ли необходимость открывать страницу для ввода подписантов
            if ("1"==$report['NeedSigner']) {
                $need_signers = true;
                $signed_reports[] = $report['ReportTypeId']; // дополняем массив отчетов для подписей
            };
        };
         * 
         */
        foreach ($reports as $report) {
            // Проверяем есть ли необходимость открывать страницу для ввода подписантов
            if ("1"==$report['NeedSigner']) {
                $need_signers = true;
                $signed_reports[] = $report['ReportTypeId']; // дополняем массив отчетов для подписей
            };
        };
        
        // Если есть необходимость в подписантах - открываем окно выбора подписантов
        $signers_seleсted = false;
        
        if ($need_signers == true AND !isset($_POST['selected_signers'])) {
            
            if (!isset($_POST['signers'])) {
                if (isset($_POST['action']) 
                        AND $_POST['action']==$_SESSION[$program]['lang']['continue']) {
                    $data['error'] = $_SESSION[$program]['lang']['one_or_more_signers_needed_in_report'];
                    include 'app/view/error_message.php';
                };
                include 'app/controller/common/report/select_signers.php';
                exit;
            } else {
                if (isset($_POST['action']) 
                        AND $_POST['action']==$_SESSION[$program]['lang']['continue']) {
                    // Добавляем подписантов и переходим к второй части марлезонского балета
                    $signers_seleсted = true;
                } else {
                    include 'app/controller/common/report/select_signers.php';
                    exit;
                }
            };
        };
        
        
        // Если нажата кнопка генерации отчета, то переходим к генерации
        if (isset($_POST['action']) AND $_POST['action']==$b[1]) {
            include 'app/controller/common/report/preview_report.php';
        };
        
?>
    <script>
        $(document).ready(function() {
            
        });
    </script>
    <style>
            table.info_table th {
                padding:1px; 
                font-size:11px; 
                border-bottom: 2px solid black;
            }
            table.info_table td {
                padding:1px; 
                font-size:11px; 
                border-bottom: 1px solid gray;
            }
            table.info_table th {
                background-color: lightgray;
            }
            table.info_table th.total {
                background-color: white;
            }
            
    </style>
    <div class="container">
        <h4 style="padding: 0px; margin-bottom:0px;">
            <?php echo htmlfix($t[0]); ?>: &nbsp;
            <font style="color:darkgreen;"> 
                <?php echo $_POST['start_datetime']; ?>
            </font> &nbsp;
            <?php echo htmlfix($t[1]); ?>: &nbsp;
            <font style="color:darkred;">
                <?php echo $_POST['stop_datetime']; ?>
            </font>
        </h4>
        <?php 
            if (count($xmls)==0 AND count($reports)==0) {
                // Сообщаем что нет данных для отчетов и что дата будет переведена
                        echo '<br/>';
                        $data['info_header'] = $_SESSION[$program]['lang']['attention'];
                        $data['info_text'] = '<br/>'.$_SESSION[$program]['lang']['no_data_for_reports'];
                        include 'app/view/info_message.php';
                        echo '<br/>';
                
            } else {
                ?>
                    <h5 style="padding: 0px; margin-bottom:5px;margin-top:25px;">
                        <?php echo htmlfix($_SESSION[$program]['lang']['create_preview_reports_confirm_report_list']); ?>:
                    </h5>
                <?php
            };
        ?>
        <form method="POST">
        <?php 
            if ($signers_seleсted == true) {
                ?>
                    <input 
                        type="hidden" 
                        name="selected_signers" 
                        value="<?php echo htmlfix(implode(',', $_POST['signers'])); ?>">
                <?php 
            };
        ?>
        <input type="hidden" name="report_set_id" value="<?php echo $report_set_id; ?>">
        <!--<ul>-->
        <?php
            /*
            foreach ($xmls as $value) {
                ?>
                    <div class="alert alert-info"><?php echo htmlfix($value['ReportLabel']); ?> - XML <br/>
                <?php
                 $action_name = 'data_prepare';
                 include 'app/view/reports/'.$value['FileName'];
                ?>
                    </div>
                <?php
            };
             * 
             */
            foreach ($reports as $value) {
                ?>
                    <div class="alert alert-success">
                        <h5 style="padding: 0px; margin-bottom:5px;">
                            <?php echo htmlfix($value['ReportLabel']); ?>
                        </h5>
                        <?php
                         $action_name = 'data_prepare';
                         include 'app/view/reports/'.$value['FileName'];
                        ?>
                    </div>
                <?php 
            };
        ?>
        <!--</ul>-->
        <hr/>
            <a class="btn btn-primary btn-large" href="?c=reports"><?php echo htmlfix($b[0]); ?></a>
            <input type="submit" class="btn btn-warning btn-large" name="action" value="<?php echo htmlfix($b[1]); ?>"/>
        </form>
    </div>
</body>
</html>