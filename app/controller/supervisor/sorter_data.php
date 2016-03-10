<?php

/*
 * Deposit Manager
 */

        if (!isset($c)) exit;

        $data['title'] = 'Данные пересчетов';
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
?>
<div class="container">
    <h3><?php 
        echo 'Данные пересчета по карте №'.htmlfix($_GET['card']);
    ?></h3>
<?php 
    if (isset($data['error']) AND $data['error']!='') {
        include 'app/view/error_message.php';
    }; 
    include 'app/model/table/sorter_accounting_data_by_id.php';
?>
    <br/>
    <a class="btn btn-primary btn-large" href="?c=deposit_manager">
        <?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?>
    </a>
</div>

