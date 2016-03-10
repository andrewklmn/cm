<?php

/*
 * Главное рабочее окно администратора
 */
    if (!isset($c)) exit;

    include './app/model/menu.php';
    
    $b = explode('|',$_SESSION[$program]['lang']['sorter_buttons']);
    
    $data['title'] = $_SESSION[$program]['lang']['sorter_list_title'];
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/forms/details_css.php';
    //include 'app/view/reload_after_1_min.php';

    $flag=true;
    foreach (get_assoc_array_from_sql('SELECT * FROM Scenario;') as $scen) {
        $scen_id = $scen['ScenarioId'];
        include 'app/controller/common/check_scenario_valuable_grades.php';
    };
    if ($flag==false) {
        $data['info_header'] = $_SESSION[$program]['lang']['attention'];
        $data['info_text'] = htmlfix($_SESSION[$program]['lang']['scens_need_checked']).'!
            &nbsp;&nbsp; 
            <a class="btn btn-danger btn-medium" href="?c=scens">
                '.htmlfix($_SESSION[$program]['lang']['goto_scens_button']).'
            </a>
        ';
        include 'app/view/info_message.php';
    };     
    
?>
<div class="container">
    <table>
        <tr>
            <td style="vertical-align: top;padding-right: 60px;">
                <h4><?php echo htmlfix($_SESSION[$program]['lang']['sorter_list_title']); ?></h4>
                <?php 
                    include 'app/model/table/sorters.php';
                ?>
            </td>
            <td style="vertical-align: top;">
                <h4><?php echo htmlfix($_SESSION[$program]['lang']['sorter_type_list_title']); ?></h4>
                <?php 
                    include 'app/model/table/sorter_types.php';
                ?>
            </td>
        </tr>
    </table>
    <hr/>
    <a href="?c=index" class="btn btn-primary btn-large" onclick="set_wait();">
        <?php echo htmlfix($_SESSION[$program]['lang']['refresh_button']); ?>
    </a>
    <a class="btn btn-warning btn-large" href="?c=sorter_add">
        <?php 
            echo htmlfix($b[0]);
        ?>
    </a>
    <a class="btn btn-warning btn-large" href="?c=sorter_type_add">
        <?php 
            echo htmlfix($b[1]);
        ?>
    </a>
</div>
