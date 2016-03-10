<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['taskrecalc_list'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
        include 'app/view/reload_after_1_min.php';
        include_once 'app/model/taskrecalc/get_taskrecalc_files_list.php';
        
        $list = get_taskrecalc_files_list();
        
?>
    <div class="container">
        <?php
            //echo '<pre>';
            //print_r($list);
            //echo '</pre>';
            echo '<h3>',htmlfix($_SESSION[$program]['lang']['taskrecalc_list']),'</h3>';
        
            include 'app/model/table/taskrecalc_list.php';
        ?>
    </div>
    <div class="container navbar navbar-fixed-bottom"
         style="background-color: white; padding: 20px;">
        <form method="POST">
            <a class="btn btn-primary btn-large" href="?c=index">
                <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
            </a>
        </form>
    </div>
</body>
</html>