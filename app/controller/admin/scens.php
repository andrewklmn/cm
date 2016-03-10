<?php

/*
 * Главное рабочее окно администратора
 */
    if (!isset($c)) exit;

    include './app/model/menu.php';
    
    $data['title'] = $_SESSION[$program]['lang']['scens_title'];
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/forms/details_css.php';
    //include 'app/view/reload_after_1_min.php';
    
    $flag=true;
    $scen_ids = array();
    foreach (get_assoc_array_from_sql('SELECT * FROM Scenario;') as $scen) {
        $flag2 = true;
        $scen_id = $scen['ScenarioId'];
        include 'app/controller/common/check_scenario_valuable_grades.php';
        if ($flag2==false) $scen_ids[]=$scen_id;
    };
    
    //print_r($scen_ids);
    
    if ($flag==false) {
        $data['info_header'] = $_SESSION[$program]['lang']['attention'];
        $data['info_text'] = htmlfix($_SESSION[$program]['lang']['scens_need_checked']).'!
        ';
        include 'app/view/info_message.php';
    }; 
    

?>
<div class="container">
    <table>
        <tr>
            <td style="vertical-align: top;padding-right: 60px;">
                <h4><?php echo htmlfix($_SESSION[$program]['lang']['scens_table_title']); ?></h4>
                <?php 
                    include 'app/model/table/scenarios.php';
                ?>
            </td>
        </tr>
    </table>
    <hr/>
    <a href="?c=index" class="btn btn-primary btn-large" onclick="set_wait();">
        <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
    </a>
    <a href="?c=scens" class="btn btn-primary btn-large" onclick="set_wait();">
        <?php echo htmlfix($_SESSION[$program]['lang']['refresh_button']); ?>
    </a>
    <a class="btn btn-warning btn-large" href="?c=scen_add">
        <?php echo htmlfix($_SESSION[$program]['lang']['scens_add_new_button']); ?>
    </a>

</div>
<script>
    var scen_id = [ <?php echo implode(', ', $scen_ids); ?> ];
    $('tr').each(function(){
        for (var i=0; i< scen_id.length; i++) {
            if (parseInt(this.id)==scen_id[i]) {
                //$(this).css('background-color','#FFFFBB');
                this.className="selected"
            };
        };
    });    
</script>