<?php

/*
 * Волшебник сценариев
 */
    if (!isset($c)) exit;
    
    include './app/model/menu.php';
    
    $data['title'] = $_SESSION[$program]['lang']['scen_edit_report_button'];
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/forms/details_css.php';
    //include 'app/view/reload_after_1_min.php';
    
    
    
?>
<div class="container">
    <?php    
        include 'app/model/table/scenario_reports.php';
    ?>
    <hr/>
    <form method="POST">
        <input type="hidden" name="step" value="1"/>
        <a href="?c=scen_edit&id=<?php echo htmlfix($_GET['id']); ?>" class="btn btn-primary btn-large" onclick="set_wait();">
            <?php echo htmlfix($_SESSION[$program]['lang']['back_to_scen_edit']); ?>
        </a>
    </form>
</div>