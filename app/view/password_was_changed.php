<?php

/*
 * Удачное изменение пароля
 */
        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['operator_workflow'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        
?>
<div class='container'>
        <?php 
            $data['success'] = $p[0];
            include './app/view/success_message.php';
        ?>
        <a class='btn btn-primary btn-large' href="?c=index"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?></a>
    </div>
</div>
