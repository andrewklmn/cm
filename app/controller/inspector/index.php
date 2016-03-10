<?php

/*
 * Список пользователей
 */

        if (!isset($c)) exit;

        $roles = array(1,2,3,4); // массив отображаемых ролей 
        
        $data['title'] = $_SESSION[$program]['lang']['system_events'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        include 'app/view/reload_after_1_min.php';
        include_once 'app/model/system_log.php';
        
        if (isset($_POST['confirm']) AND $_POST['confirm']!=$_SESSION[$program]['lang']['start_backup']) {
            unset($_POST);
        };
        
        if (isset($_POST['action'])
                AND $_POST['action']==$_SESSION[$program]['lang']['move_log_to_archive']) {
            if ($_POST['confirm']==$_SESSION[$program]['lang']['start_backup']) {
                // 1 =========== блокируем таблицы журнала на время архивирования =========================
                do_sql('LOCK TABLES SystemLog WRITE;');
                // 2 =========== добавляем архивирование как последнее событие
                $text = 'Events log was initiated by '.$_SESSION[$program]['user'];
                system_log($text);
                
                // 3 =========== переносим данные из таблицы в файл ========================================
                $name = date('Y-m-d_H-i-s',time()).'_'.$_SESSION[$program]['user'].'.csv';
                $ticket = '';
                $system_log = get_assoc_array_from_sql('
                    SELECT
                        `SystemLog`.`DateAndTime`,
                        `SystemLog`.`Comment`
                    FROM 
                        `cashmaster`.`SystemLog`
                    '.$where.'
                    ORDER BY `SystemLog`.`Id`DESC
                ;');

                foreach ($system_log as $value) {
                    $ticket .= '"'.$value['DateAndTime'].'","'.iconv('UTF-8','cp1251',$value['Comment']).'"
';
                };
                
                if (!file_exists('../'.$log_backup_directory)) {
                    mkdir( '../'.$log_backup_directory, 0777);
                    chmod( '../'.$log_backup_directory, 0777);
                };
                
                $fp = fopen( $log_backup_directory.'/'.$name, 'w');
                chmod( $log_backup_directory.'/'.$name, 0777);
                fwrite($fp, $ticket);
                fclose($fp);
                
                // 4 =========== сверяем данные таблицы и нового файла архива
                //               если не совпало - удаляем файл и останавливаем процесс =======================
                $check = file_get_contents ($log_backup_directory.'/'.$name);
                if ($ticket == $check) {
                    // 5 =========== удаляеми данные из таблицы если совпало с файлом
                    do_sql('
                        DELETE FROM SystemLog WHERE Id>0
                    ;');
                    // 6 =========== ставим первым событием в журнал это архивирование
                    $text = 'Events log was created by '.$_SESSION[$program]['user'];
                    system_log($text);                    
                    // 7 =========== сообщаем об успешном архивировании
                    $data['success'] = $_SESSION[$program]['lang']['backup_was_done'];
                    include 'app/view/success_message.php';
                } else {
                    unlink($log_backup_directory.'/'.$name);
                    $data['error'] = $_SESSION[$program]['lang']['system_events_backup_was_wrong'];
                    include 'app/view/error_message.php';
                };
                do_sql('UNLOCK TABLES;');
                
            } else {
                $data['info_header']=$_SESSION[$program]['lang']['attention'];
                $data['info_text']=$_SESSION[$program]['lang']['log_will_be_moved'];
                include 'app/view/info_message.php';

                ?>
                    <div class='container'>
                        <h3><?php echo htmlfix($_SESSION[$program]['lang']['confirm_backup']); ?></h3>
                        </br>
                        <form method="POST">
                            <input 
                                type='submit' 
                                name="confirm"
                                class='btn btn-primary btn-large' 
                                value='<?php echo htmlfix($_SESSION[$program]['lang']['cancel']); ?>'/> 
                            <input 
                                type='hidden' 
                                name="action"
                                class='btn btn-danger btn-large' 
                                value='<?php echo htmlfix($_SESSION[$program]['lang']['move_log_to_archive']); ?>'/> 
                            <input 
                                type='submit' 
                                name="confirm"
                                class='btn btn-danger btn-large' 
                                value='<?php echo htmlfix($_SESSION[$program]['lang']['start_backup']); ?>'/> 
                        </form>
                    </div>
                <?php
                exit;
            };
        };
        
        $where = '';
        
        if (isset($_POST['action'])) {
            if ($_POST['action']=='Find') {
                $where = 'WHERE 
                    Comment like "%'.addslashes($_POST['text']).'%"
                        OR DateAndTime like "%'.addslashes($_POST['text']).'%"';
            } else {
                $_POST['text']='';
            };
        };
        
        
        $system_log = get_assoc_array_from_sql('
            SELECT
                `SystemLog`.`DateAndTime`,
                `SystemLog`.`Comment`
            FROM 
                `cashmaster`.`SystemLog`
            '.$where.'
            ORDER BY `SystemLog`.`Id`DESC
            LIMIT 0,500
        ;');
        
?>
<div class='container'>
    <form method="POST">
        <input style="<?php 
            if (isset($_POST['text']) AND $_POST['text']!='') echo 'background-color:lightgreen;';
        ?>" type="text" class="search-query" name="text" value="<?php
            if (isset($_POST['text'])) echo htmlfix($_POST['text']);
        ?>"/>
        <input class="btn btn-medium" type="submit" name="action" value="Find">
        <input class="btn btn-medium" type="submit" name="action" value="Show all">
    </form>
    <table>
        <?php 
            foreach ($system_log as $value) {
              ?>
                    <tr>
                        <td style="font-family: monospace; color:darkblue;">
                            <?php echo htmlfix($value['DateAndTime']
                                            .' '.$value['Comment']); ?>
                        </td>
                    </tr>
              <?php
            };
        ?>
    </table>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
</div>
<div class="no-print navbar navbar-fixed-bottom" 
                 style="background-color: white; padding: 20px;">
         <div class="container">
            <form method="POST">
                <a target="_blank" href="?c=get_log" class='btn btn-warning btn-large'>
                    <?php echo htmlfix($_SESSION[$program]['lang']['create_log_copy']); ?>
                </a>
                <input 
                    type='submit' 
                    class='btn btn-danger btn-large' 
                    name="action"
                    value='<?php echo htmlfix($_SESSION[$program]['lang']['move_log_to_archive']); ?>'/>
                <a href="?c=log_archive" class='btn btn-primary btn-large'>
                    <?php echo htmlfix($_SESSION[$program]['lang']['log_archive']); ?>
                </a>
            </form>
        </div>
    </div>