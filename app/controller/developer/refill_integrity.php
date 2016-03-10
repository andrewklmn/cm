<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    $data['title'] = "Refill integrity check";
    include './app/view/page_header_with_logout.php';

    include './app/controller/common/refill_integrity_check.php';
    
    $data['success'] = 'Integrity check was refilled';
    include './app/view/success_message.php';
    
?>
<div class="container">
    <a href="?c=index" class="btn btn-primary btn-large">Back to main menu</a>
</div>
