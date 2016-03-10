<?php

/*
 * Report generator
 */
    
    if (!isset($c)) exit;
    include_once 'app/model/nmr2str_by_currency.php';
    include_once 'app/model/int2str.php';
    
    $bb = explode('|', $_SESSION[$program]['lang']['reports_generate_buttons']);
    
    // Проверяем повторную отсылку данных по рефреш при генерации отчета
    // Если репорт_сут_айди уже закрыт (т.е. дата не равна нулю), то ругаемся и возвращаем на экран отчетов
    
    if (isset($_POST['report_set_id'])) {
        $rows= fetch_row_from_sql('
            SELECT
                `ReportSets`.`SetDateTime`
            FROM 
                `cashmaster`.`ReportSets`
            WHERE
                ReportSets.SetId="'.addslashes($_POST['report_set_id']).'"
        ;');
        if (isset($rows[0]) AND $rows[0]!='' AND $rows[0]!='0000-00-00 00:00:00') {
            $data['error'] = $_SESSION[$program]['lang']['reports_were_created'];
            include 'app/view/error_message.php';
            ?>
                <div class="container">
                    <hr/>
                    <a class="btn btn-primary btn-large" href="?c=reports"><?php echo htmlfix($bb[0]); ?></a>
                </div>
            <?php
        exit;
        };
    };
    
    // 1. Проверяем статус операционного дня (можно или нельзя генерить отчет сейчас)
    include 'app/controller/common/report/check_operation_day_status.php';
    
    // 2. Провеяем дату последнего отчетного периода, если она не совпадает 
    //    выводим сообщение об ошибке
    $rows= fetch_row_from_sql('
        SELECT
            MAX(`ReportSets`.`SetDateTime`)
        FROM 
            `cashmaster`.`ReportSets`
        WHERE
            ReportSets.CashRoomId="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
    ;');
        
    if (isset($row[0]) AND $_POST['start_datetime']!=$row[0]) {
        $data['error'] = $_SESSION[$program]['lang']['new_report_was_created_by_another_user'];
        include 'app/view/error_message.php';
        ?>
            <div class="container">
                <hr/>
                <a class="btn btn-primary btn-large" href="?c=reports"><?php echo htmlfix($bb[0]); ?></a>
            </div>
        <?php
        exit;
    };
    
    // 3. Находим айди всех реков, которые не сервисные и имеют флаг сверки 1 с окончанием сверки старше 
    //    первой даты отчетного периода
    $recs = get_array_from_sql('
        SELECT DISTINCT
            DepositRecs.DepositRecId
        FROM 
            `cashmaster`.`DepositRecs`
        LEFT JOIN
            DepositRuns ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
        LEFT JOIN
            Machines ON Machines.MachineDBId=DepositRuns.MachineDBId
        LEFT JOIN
            CashRooms ON `CashRooms`.`Id`=`Machines`.`CashRoomId`
        WHERE
            `DepositRecs`.`ServiceRec`=0
            AND `DepositRecs`.`ReconcileStatus`=1
            AND `DepositRecs`.`RecLastChangeDatetime` > "'.addslashes($_POST['start_datetime']).'"
            AND `Machines`.`CashRoomId`="'.  addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
    ;');
    
    // Запускаем генератор отчетов
    $report_datetime = date('Y-m-d H:i:s',time());
    
    
    // Проверяем файлы моделей отчетов 
    include 'app/controller/common/report/check_needed_reports_files.php';
    
    //echo '<pre>';
    //print_r($_POST);
    //echo '</pre>';

    // Сохраняет всех подписантов по всем отчетам
    include 'app/controller/common/report/fill_report_signers.php';
    
    // Сохраняет все дополнительные параметры по отчетам
    include 'app/controller/common/report/fill_report_saves.php';
    
    // Модель функции необходимой для вытаскивания значениея по ключу из РепортСавес
    include_once 'app/model/report/get_value_from_report_saves.php';
    
    // Модель функции необходимой для вытаскивания подписантов по отчету
    include_once 'app/model/report/get_report_signers.php';
    
    
    // Модель функции получения сокращенного имени работчника
    include_once 'app/model/reconciliation/get_short_fio_by_user_id.php';
    // Модель функции генерации XML файлов
    include_once 'app/model/report/add_xml_file_to_reportset.php';

    // Номер комнаты для пути для генерации отчетов
    $room = $_SESSION[$program]['UserConfiguration']['CashRoomId'];
        
    //echo '<pre>';
    //print_r($xmls);
    //echo '</pre>';
    
    // формируем файлы XML 
    $ticket_xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $ticket_xml_files = array();
    
    foreach ($xmls as $report) {
         $action_name = 'data_report';
         include 'app/view/reports/'.$report['FileName'];
    };
    
    
    include 'app/view/reports/rotated_page_css.php';
    
    // Выводим отчеты для печати
    echo '<div class="container">';
    $iii = 0;
    foreach ($reports as $report) {
         $action_name = 'data_report';
         include 'app/view/reports/'.$report['FileName'];
         $iii++;
         if ($iii < count($reports)) {
             include 'app/view/reports/page_divider.php';
         };
    };
    
    
    $print_button_show = true;
    if (count($recs)==0) {    
        $print_button_show = false;
        echo '<br/>';
        $data['success'] = $_SESSION[$program]['lang']['start_date_was_adjusted'];
        include 'app/view/success_message.php';
        echo '<br/>';
    };
    
    /*
    // Фиксируем дату создания отчета в РепортСете
    do_sql('
        UPDATE `cashmaster`.`ReportSets`
        SET
            `SetDateTime` = "'.addslashes($_POST['stop_datetime']).'",
            `CreatedBy` = "'.addslashes($_SESSION[$program]['UserConfiguration']['UserId']).'"
        WHERE 
            `SetId` = "'.addslashes($_POST['report_set_id']).'"
    ;');
*/
   /* 
    // Копируем квитанцию и файлы по данному репорсету в аутпут
    exec ('find "'.$xml_archive_directory.'/'.$room.'/'
            .$report_datetime.'" -name *.xml -exec cp {} "'
                .$xml_output_directory.'/'.$room.'" \;');
    exec ('chmod -R 777 "'.$xml_output_directory.'/'.$room.'"');
    
    system_log( $events[103].' by '.$_SESSION[$program]['UserConfiguration']['UserLogin']);
    */
    echo '</div>';
    ?>
        <div class="container no-print navbar navbar-fixed-bottom" 
             style="background-color: white; padding: 20px;">
            <a class="btn btn-primary btn-large" href="?c=reports"><?php echo htmlfix($bb[0]); ?></a>
            <?php 
                if ($print_button_show) {
                    ?>
                        <button onclick="window.print();" class="btn btn-warning btn-large">
                            <?php echo htmlfix($bb[1]); ?>
                        </button>
                    <?php 
                };
            ?>
        </div>
    <?php
    // Запечатываем отчеты устанавливая дату в таблицу РепорСетс
    exit;
?>
