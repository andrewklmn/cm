<?php

/*
 * Список пользователей
 */

        if (!isset($c)) exit;

        $roles = array(1,2,3,4); // массив отображаемых ролей 
        
        $data['title'] = $_SESSION[$program]['lang']['error'];
        
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        include 'app/view/reload_after_1_min.php';
        
        if (isset($_POST['action']) 
                AND $_POST['action']==$_SESSION[$program]['lang']['restore_application_files']) {
                include 'app/controller/common/restore_application.php';
            ?>
                <div class='container'>
                    <?php 
                        $data['success'] = $_SESSION[$program]['lang']['application_files_restored'];
                        include 'app/view/success_message.php';
                    ?>
                    <a href="?c=index" class="btn btn-primary btn-large" onclick="set_wait();">
                        <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
                    </a>
                </div>
            <?php
        } else {
            ?>
                <div class='container'>
                    <?php 
                        $data['error'] = $_SESSION[$program]['lang']['application_files_corrupted'];
                        include 'app/view/error_message.php';
                    ?>
                    <form action="?c=restore_scripts" method="POST">
                        <input class="btn btn-large btn-danger" type="submit" name="action" value="<?php 
                           echo htmlfix($_SESSION[$program]['lang']['restore_application_files']);
                        ?>"/>
                    </form>
                </div>
            <?php
        };
        
?>
