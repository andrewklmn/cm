<?php

/*
 * Список пользователей
 */

        if (!isset($c)) exit;
        
        
        $data['title'] = 'Restore tool';
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        
        // получаем список бекапов в виде массива для таблицы
        $list = scandir($backup_directory);
        rsort($list);

        $backups = array();
        foreach ($list as $value) {
            if ($value!='.' 
                    AND $value!='..'
                    AND is_dir($backup_directory.'/'.$value)) {
                $creator = '';
                include $backup_directory.'/'.$value.'/ticket.php';
                $backups[]=array( 
                    $value,
                    $creator
                );
            };
        };
        
        $flag = false;
        // Проверяем есть ли такое время в списке бекапов
        if (isset($_GET['time'])) {
            foreach ($backups as $key => $value) {
                if ($value[0]==$_GET['time']) {
                    $flag = true;
                    break;
                };
            }
            if ($flag == false ) {
                // Неправильное дата-время бекапа
                $data['error'] = 'There is no backup with that date and time';
                include 'app/view/error_message.php';
            } else {
                if (isset($_POST['action']) AND $_POST['action']=='Start restoring process') {
                    
                    // собственно восстанавливаем систему
                    // Запускаем фоновый процесс восстановления                    
                    exec('cd '.$service_directory.'/; php -f restore.php "'
                            .$_GET['time'].'" "'
                            .$_SESSION[$program]['user_fio'].' login: '
                            .$_SESSION[$program]['UserConfiguration']['UserLogin'].'"');
                    
                    //echo $_SESSION[$program]['new_backup_date'];
                    // Пишем состемный лог про это
                    do_sql('
                        INSERT INTO `cashmaster`.`SystemLog`
                            (`Comment`)
                        VALUES
                            ("'.addslashes('Database was restored to state up to '.$_GET['time'].' by '.$_SESSION[$program]['UserConfiguration']['UserLogin']).'")
                    ;');
                    ?>
                        <div class='container'>
                            <h3>Restoring complete</h3>
                            <p>
                                Database was restored up to date:
                                <br/>
                                <br/>
                                <font style="font-weight: bold;color:darkred;" size="5">
                                    <?php echo htmlfix($_GET['time']); ?>
                                </font>
                            </p>
                            <br/>
                            <br/>
                            <a class="btn btn-primary btn-large" href="?c=restore">Back to list</a>
                        </div>
                    <?php
                    // Запрашиваем подтверждение
                    exit;
                } else {
                    ?>
                        <div class='container'>
                            <h3>Attention!</h3>
                            <p>
                                Database will be restored up to date:
                                <br/>
                                <br/>
                                <font style="font-weight: bold;color:darkred;" size="5">
                                    <?php echo htmlfix($_GET['time']); ?>
                                </font>
                                <br/>
                                <br/>
                                All current data will be lost!
                            </p>
                            <br/>
                            <br/>
                            <form method="POST">
                                <a class="btn btn-primary btn-large" href="?c=restore">Cancel</a>
                                <input class="btn btn-danger btn-large" type="submit" name="action" value="Start restoring process"/>
                            </form>
                        </div>
                    <?php
                    // Запрашиваем подтверждение
                    exit;
                };
            };
        };
      
?>
<div class='container'>
    <?php
        include 'app/model/table/backups_for_restore.php';
    ?>
</div>
