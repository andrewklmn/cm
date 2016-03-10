<?php

/*
 * Главное рабочее окно администратора
 */
    if (!isset($c)) exit;

    include './app/model/menu.php';
    
    $data['title'] = $_SESSION[$program]['lang']['denoms_title'];
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/forms/details_css.php';
    //include 'app/view/reload_after_1_min.php';

    $b = explode('|', $_SESSION[$program]['lang']['denoms_buttons']);
    
?>
<div class="container">
    <table>
        <tr>
            <td style="vertical-align: top;padding-right: 40px;">
                <h4><?php echo htmlfix($_SESSION[$program]['lang']['denoms_denoms_title']); ?></h4>
                <?php 
                    include 'app/model/table/denoms.php';
                ?>
            </td>
            <td style="vertical-align: top;padding-right: 40px;">
                <h4><?php echo htmlfix($_SESSION[$program]['lang']['denoms_currencies_title']); ?></h4>
                <?php 
                    include 'app/model/table/currencies.php';
                ?>
                <br/>
                <h4><?php echo htmlfix($_SESSION[$program]['lang']['denoms_valuable_types_title']); ?></h4>
                <?php 
                    include 'app/model/table/valuable_types.php';
                ?>
            </td>
            <td style="vertical-align: top;padding-right: 40px;">
                <h4><?php echo htmlfix($_SESSION[$program]['lang']['denoms_grades_title']); ?></h4>
                <?php 
                    include 'app/model/table/grades.php';
                ?>
            </td>
        </tr>
    </table>
    <hr/>
    <a href="?c=index" class="btn btn-primary btn-large" onclick="set_wait();">
        <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
    </a>
    <a href="?c=indexes" class="btn btn-primary btn-large" onclick="set_wait();">
        <?php echo htmlfix($_SESSION[$program]['lang']['refresh_button']); ?>
    </a>
    <?php 
        echo '<font style="color:brown;font-weight:bold;">
                    &nbsp;&nbsp;&nbsp;',htmlfix($b[0]),':</font>';
    ?>
    <a class="btn btn-warning btn-large" href="?c=denom_add">
        <?php echo htmlfix($b[1]); ?>
    </a>
    <a class="btn btn-warning btn-large" href="?c=currency_add">
        <?php echo htmlfix($b[2]); ?>
    </a>
    <a class="btn btn-warning btn-large" href="?c=grade_add">
        <?php echo htmlfix($b[4]); ?>
    </a>
    <a class="btn btn-warning btn-large" href="?c=valuable_type_add">
        <?php echo htmlfix($b[3]); ?>
    </a>
</div>
