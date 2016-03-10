<?php

/*
 * Редактирование кассы пересчета для работника
 */

        if (!isset($c)) exit;

        $roles = array(1,2,3,4); // массив редактируемых ролей
        
        
        $data['title'] = $_SESSION[$program]['lang']['user_ips_title'];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        
        $user = get_user_config($_REQUEST['id']);
        
        if (isset($_POST['action']) AND $_POST['action']==$_SESSION[$program]['lang']['add_new_ip']) {
            do_sql('
                INSERT INTO `cashmaster`.`UsersIP`
                    (
                        `UserId`,
                        `IP`
                    )
                VALUES
                    (
                        "'.addslashes($_GET['id']).'",
                        "%"
                    )
            ;');
            
            system_log('IP-address % for user: '.$user['UserLogin']
                    .' was added to allowed list '
                    .' by: '.$_SESSION[$program]['UserConfiguration']['UserLogin']
                    .' IP: '.$_SERVER['REMOTE_ADDR']);

            $data['success'] = $_SESSION[$program]['lang']['ip_was_added'];
            include 'app/view/success_message.php';
        };
        
?>
<style>
    table.user td {
        text-align: left;
        padding-left: 5px;
        padding-top: 3px;
    }
    table.user th {
        text-align: right;
        padding-left: 5px;
        padding-top: 3px;
    }
</style>
<div class="container">
    <h3>
        <?php echo htmlfix($_SESSION[$program]['lang']['user_ips_title']); ?>
    </h3>
    <?php
        include 'app/model/table/user_ips.php';
    ?>
    <br/>
    <br/>
    <form method="POST">
        <a class="btn btn-primary btn-large" 
           href="?c=user_edit&id=<?php echo $_GET['id']; ?>"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_user_edit']); ?></a>
        <input class="btn btn-danger btn-large" type="submit" name="action" 
               value="<?php echo htmlfix($_SESSION[$program]['lang']['add_new_ip']); ?>" />
    </form>
</div>