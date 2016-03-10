<?php
/*
 * Редактирование записи (пример работы)
 */
        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['scen_edit_title'];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';

        $b = explode('|', $_SESSION[$program]['lang']['record_edit_buttons']);
                
        
        // Проверяем меняется ли дефолтный сценарий
        if (isset($_POST['DefaultScenario']) 
                AND $_POST['DefaultScenario']=="1"
                AND isset($_POST['action'])
                AND $_POST['action']==$b[1]
                AND isset($_POST['confirmation'])
                AND $_POST['confirmation']==$b[4]) {
            do_sql('
                UPDATE 
                    `Scenario`
                SET
                    `DefaultScenario` = 0
                WHERE `ScenarioId` <> "'.addslashes($_GET['id']).'"
            ;');
        };
        
        // проверяем нужно ли переопределить Valuable Grades для сценария
        $flag=true;
        $scen_id = $_GET['id'];
        include 'app/controller/common/check_scenario_valuable_grades.php';
        if ($flag==false) {
            $data['info_header'] = $_SESSION[$program]['lang']['attention'];
            $data['info_text'] = htmlfix($_SESSION[$program]['lang']['scen_need_run_wizard']).'!
                &nbsp;&nbsp; 
                <a class="btn btn-danger btn-medium" href="?c=scen_wizard&id='
                    .htmlfix($_GET['id']).'">
                    '.htmlfix($_SESSION[$program]['lang']['scen_edit_wizard_button']).'
                </a>
            ';
            include 'app/view/info_message.php';
        };
        
        include 'app/model/record/admin_scenario_record.php';
        include_once 'app/view/draw_record_edit.php';                
        draw_record_edit( $record );

?>
    <script>
        $('<a style="margin-left: 5px;" class="btn btn-warning btn-large" href="?c=scen_reports&id=<?php echo htmlfix($_GET['id']); ?>">'
            + '<?php echo htmlfix($_SESSION[$program]['lang']['scen_edit_report_button']); ?>'
            + '</a>').insertAfter($('input#clone'));
    </script>
<?php
        
        if ($flag==true) {
            ?>
                <script>
                    $('<a style="margin-left: 5px;" class="btn btn-danger btn-large" href="?c=scen_wizard&id=<?php echo htmlfix($_GET['id']); ?>">'
                        + '<?php echo htmlfix($_SESSION[$program]['lang']['scen_edit_wizard_button']); ?>'
                        + '</a>').insertAfter($('input#clone'));
                </script>
            <?php
        };
?>
