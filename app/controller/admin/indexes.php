<?php

/*
 * Главное рабочее окно администратора
 */
    if (!isset($c)) exit;

    include './app/model/menu.php';
    
    $data['title'] = $_SESSION[$program]['lang']['indexes_title'];
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/forms/details_css.php';
    //include 'app/view/reload_after_1_min.php';

?>
<div class="container">
    <table>
        <tr>
            <td style="vertical-align: top;padding-right: 60px;">
                <h4><?php echo htmlfix($_SESSION[$program]['lang']['indexes_sorter_index']); ?></h4>
                <?php 
                    include 'app/model/table/sorter_indexes.php';
                ?>
            </td>
            <td style="vertical-align: top;">
                <h4><?php echo htmlfix($_SESSION[$program]['lang']['indexes_deposit_index']); ?></h4>
                <?php 
                    include 'app/model/table/deposit_indexes.php';
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
    <a class="btn btn-warning btn-large" href="?c=sorter_index_add">
        <?php echo htmlfix($_SESSION[$program]['lang']['indexes_add_sorter_index']); ?>
    </a>
    <a class="btn btn-warning btn-large" href="?c=deposit_index_add">
        <?php echo htmlfix($_SESSION[$program]['lang']['indexes_add_deposit_index']); ?>
    </a>

</div>
