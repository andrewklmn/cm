<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['reconciled_deposits'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
        include 'app/controller/set_default_scenario.php';
        //include 'app/controller/select_scenario.php';
        include 'app/view/reload_after_1_min.php';
?>
    <script>

    </script>
    <div class="container">
        <?php 
            include './app/model/table/reconciled_deposits.php';
        ?>
    </div>
</body>
</html>