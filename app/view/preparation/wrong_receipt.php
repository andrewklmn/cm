<?php

/* 
 * Неправильная квитанция
 */

    if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['reconciliation'];
        include_once './app/view/page_header_with_logout.php';
        include_once './app/view/set_remove_wait.php'; 
        include_once './app/model/reconciliation/recon_function_load.php';
        include_once './app/view/draw_simple_table.php';
        
        
?>
<div class="container">
    <?php 
        $data['error'] = $_SESSION[$program]['lang']['wrong_receipt_number'];
        include 'app/view/error_message.php';
    ?>
    <a class="btn btn-warning btn-large" href="?c=index">
        <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
    </a>
</div>