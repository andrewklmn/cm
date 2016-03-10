<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;
        include_once 'app/model/nmr2str_by_currency.php';
        include_once 'app/model/int2str.php';

        $data['title'] = $_SESSION[$program]['lang']['reportset_view'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';

        $b = explode('|', $_SESSION[$program]['lang']['reportset_view_buttons']);
        
        // прямой вызов без номера отчета
        if (!isset($_GET['id']) OR $_GET['id']=='') {
            $data['error'] = $_SESSION[$program]['lang']['no_data_for_view'];
            include 'app/view/error_message.php';
            ?>
                <div class="container">
                    <hr/>
                    <a href="?c=reports_archive" class='btn btn-primary btn-large'>
                        <?php echo htmlfix($b[0]); ?>
                    </a>
                </div>                
            <?php
            exit;
        };
        
        // проверяем кассу пересчета отчета и вызывающего
        $row = fetch_row_from_sql('
            SELECT
                count(*)
            FROM 
                `cashmaster`.`ReportSets`
            WHERE
                `ReportSets`.`CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                AND `ReportSets`.`SetId`="'.addslashes($_GET['id']).'"
                AND `ReportSets`.`SetDateTime`<>"0000-00-00 00:00:00"
        ;');
        if ($row[0]==0) {
            $data['error'] = $_SESSION[$program]['lang']['access_denied'];
            include 'app/view/error_message.php';
            ?>
                <div class="container">
                    <hr/>
                    <a href="?c=reports_archive" class='btn btn-primary btn-large'>
                        <?php echo htmlfix($b[0]); ?>
                    </a>
                </div>                
            <?php
            exit;            
        };
        
        // Находим время выбранного репортсета
        $row= fetch_row_from_sql('
            SELECT
                `ReportSets`.`SetDateTime`
            FROM 
                `cashmaster`.`ReportSets`
            WHERE
                ReportSets.SetId="'.addslashes($_GET['id']).'"
        ;');
        $report_datetime = $row[0];
        $_POST['stop_datetime'] = $report_datetime;
        
        
        // Находим время предыдущего репортсета
        $row= fetch_row_from_sql('
            SELECT
                `ReportSets`.`SetDateTime`
            FROM 
                `cashmaster`.`ReportSets`
            WHERE
                ReportSets.SetId="'.addslashes(($_GET['id']-1)).'"
        ;');
        if (isset($row[0])) {
            $_POST['start_datetime'] = $row[0];
            $previous_report_datetime = $row[0];
        } else {
            $_POST['start_datetime'] = '1972-11-29 00:00:00';
            $previous_report_datetime = '1972-11-29 00:00:00';
        };
        
        // Совместимость с модулями 
        $_POST['report_set_id'] = $_GET['id'];
        $report_set_id = $_GET['id'];
        
        // копируем набор файлов если задано повторное копирование
        if (isset($_POST['action']) AND $_POST['action']==$b[2]) {
            echo '<div class="no-print">';
            // копируем отчеты в папку xml_output
            $room = $_SESSION[$program]['UserConfiguration']['CashRoomId'];
            // создаем папку с кассой физически если её ещё нет
            if (!file_exists($xml_output_directory.'/'.$room)) {
                mkdir( $xml_output_directory.'/'.$room, 0777);
                chmod( $xml_output_directory.'/'.$room, 0777);
            };
            
            // Копируем квитанцию и файлы по данному репорсету в аутпут
            exec ('find "'.$xml_archive_directory.'/'.$room.'/'
                    .$report_datetime.'" -name *.xml -exec cp {} "'
                        .$xml_output_directory.'/'.$room.'" \;');
            exec ('chmod -R 777 "'.$xml_output_directory.'/'.$room.'"');

            system_log( $events[104].' by '.$_SESSION[$program]['UserConfiguration']['UserLogin']);
            
            // сообщаем об успешном копировании фалов
            $data['success'] = $_SESSION[$program]['lang']['files_were_copied_successfully'];
            include 'app/view/success_message.php';
            echo '</div>';
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
                AND `DepositRecs`.`RecLastChangeDatetime` > "'.addslashes($previous_report_datetime).'"
                AND `DepositRecs`.`RecLastChangeDatetime` <= "'.addslashes($report_datetime).'"
                AND `Machines`.`CashRoomId`="'.  addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
        ;');
        
        
        // Получаем использованные отчеты в репортсете
        $reports = array();
        $reports = get_assoc_array_from_sql('
            SELECT
                *
            FROM 
                `cashmaster`.`ScenReportTypes`
            LEFT JOIN
                ReportTypes ON  ReportTypes.ReportTypeId=ScenReportTypes.ReportTypeId
            LEFT JOIN
                Reports ON Reports.ReportTypeId = ReportTypes.ReportTypeId
            WHERE
                Reports.ReportSetId = "'.addslashes($_GET['id']).'"
                AND GenerateXmlFile = 0
            GROUP BY `ScenReportTypes`.`ReportTypeId`
        ;');
        
        
        // Модель функции необходимой для вытаскивания значениея по ключу из РепортСавес
        include_once 'app/model/report/get_value_from_report_saves.php';
        // Модель функции необходимой для вытаскивания подписантов по отчету
        include_once 'app/model/report/get_report_signers.php';
        // Модель функции получения сокращенного имени работчника
        include_once 'app/model/reconciliation/get_short_fio_by_user_id.php';
        
        // Стили CSS для поворота таблиц на печати
        include_once 'app/view/reports/rotated_page_css.php';
        
        
        // Выводим отчеты для печати
        echo '<div class="container">';
        $iii = 0;
        foreach ($reports as $report) {
            
             //echo '<pre>';
             //print_r($report);
             //echo '</pre>';
            
             $action_name = 'data_report';
             include 'app/view/reports/'.$report['FileName'];
             $iii++;
             if ($iii < count($reports)) {
                 include 'app/view/reports/page_divider.php';
             };
        };
        echo '</div>';
        
        if (count($reports)==0) {
            // Сообщение об отсутствии печатных отчетов
            $data['info_header'] = $_SESSION[$program]['lang']['attention'];
            $data['info_text'] = '<br/>'.$_SESSION[$program]['lang']['no_reports_for_print'];
            include 'app/view/info_message.php';
        };
        
        
    ?>
    <div class="container no-print navbar navbar-fixed-bottom" 
                 style="background-color: white; padding: 20px;">
        <form method="POST">
            <a href="?c=reports_archive" class='btn btn-primary btn-large'>
                <?php echo htmlfix($b[0]); ?>
            </a>
            <?php 
                if (count($reports)>0) {
                    ?>
                        <input 
                            type='button' 
                            onclick="window.print();"
                            class='btn btn-warning btn-large' 
                            value='<?php echo htmlfix($b[1]); ?>'/>
                    <?php 
                };
            ?>
            <input 
                type='submit' 
                class='btn btn-danger btn-large' 
                name="action"
                value='<?php echo htmlfix($b[2]); ?>'/>
        </form>
    </div>
</body>
</html>