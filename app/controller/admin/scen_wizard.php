<?php

/*
 * Волшебник сценариев
 */
    if (!isset($c)) exit;
    
    $flag = true;
    $scen_id = $_GET['id'];
    include 'app/controller/common/check_finished_recon_by_scenario.php';
    if ( $flag==false ) {
        
        $data['title'] = $_SESSION[$program]['lang']['scen_wizard_title'];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/forms/details_css.php';
        
        $data['error'] = $_SESSION[$program]['lang']['access_denied'];
        include 'app/view/error_message.php';
            ?>
                <div class="container">
                    <form method="POST">
                        <input type="hidden" name="step" value="1"/>
                        <a href="?c=scen_edit&id=<?php echo htmlfix($_GET['id']); ?>" class="btn btn-primary btn-large" onclick="set_wait();">
                            <?php echo htmlfix($_SESSION[$program]['lang']['back_to_scen_edit']); ?>
                        </a>
                    </form>
                </div>
            <?php
        exit;
    };
    
    // Обновлялка сиквенций
    if (isset($_POST['action']) AND $_POST['action']=='update_sequense') {
        include 'app/view/html_header.php';
        // Проверяем есть ли такая сиквенция или нужно создать новую
        do_sql('
            UPDATE 
                `ValuablesGrades`
            SET
                `GradeId` = "'.addslashes($_POST['grade']).'"
            WHERE 
                `ScenarioId` = "'.addslashes($_GET['id']).'"
                AND `ValuableId` = "'.addslashes($_POST['valuable']).'"
        ;');
        echo '0';
        exit;
    };
    
    include './app/model/menu.php';
    
    $data['title'] = $_SESSION[$program]['lang']['scen_wizard_title'];
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/forms/details_css.php';
    //include 'app/view/reload_after_1_min.php';
    
    $h = explode('|',$_SESSION[$program]['lang']['scen_wizard_headers']);
    
    if(!isset($_POST['step'])) {
        include 'app/controller/common/scen_wizard/valuable_types.php';
    } else {
        switch($_POST['step']){
            case 1:
                include 'app/controller/common/scen_wizard/denoms.php';
                break;
            case 2:
                include 'app/controller/common/scen_wizard/sorter_grades.php';
                break;
            case 3:
                include 'app/controller/common/scen_wizard/recon_grades.php';
                break;
            case 4:
                include 'app/controller/common/scen_wizard/valuable_grades.php';
                break;
            case 5:
                include 'app/controller/common/scen_wizard/finish.php';
                break;
        };
    };
       

    switch ($_POST['step']) {
        case 1:
            // Переходим к выбору валют
            break;
        default:
            break;
    };
   
    
?>
