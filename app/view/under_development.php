<?php

/*
 * под разработкой
 */

        if (!isset($c)) exit;

        $data['title'] = 'Under development';
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
        ?>
<div class="container">
    <?php                
        $data['error']='Controller '.htmlspecialchars($c).' is under development';
        include './app/view/error_message.php'

    ?>    
</div>

