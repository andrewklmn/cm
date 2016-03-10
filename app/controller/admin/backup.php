<?php

/*
 * Список пользователей
 */

        if (!isset($c)) exit;
        
        // Блок контроля изменения даты бекапа на вновь созданный
        if (isset($_POST['check_time']) AND $_POST['check_time']=='check_time') {
            include 'app/view/html_header.php';
            $row = fetch_row_from_sql('
                    SELECT
                        `SystemGlobals`.`LastArchiveDate`
                    FROM 
                        `cashmaster`.`SystemGlobals`
            ;');
            if ($_SESSION[$program]['new_backup_date']==$row[0]) {
                echo '1';
                unset($_SESSION[$program]['new_backup_date']);
            } else {
                echo '0';
            };
            exit;
        };

        $b = explode('|',$_SESSION[$program]['lang']['users_buttons']);
        $l = explode('|',$_SESSION[$program]['lang']['users_labels']);
        
        $data['title'] = $_SESSION[$program]['lang']['backup_tool'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        
        
        if (isset($_POST['confirm']) AND $_POST['confirm']!=$_SESSION[$program]['lang']['start_backup']) {
            unset($_POST);
        };
        
        
        if (isset($_POST['action']) AND $_POST['action']==$_SESSION[$program]['lang']['start_backup']) {
            if (isset($_POST['confirm'])) {
                if ($_POST['confirm']==$_SESSION[$program]['lang']['start_backup']) {
                    // Страница отображения создания бэкапа
                    
                    // Устанавливаем в сессию новое значение даты бекапа
                    $_SESSION[$program]['new_backup_date'] = date('Y-m-d H:i:s');
                                        
                    ?>
                        <div class='container'>
                            <hr/>
                            <h1><?php echo htmlfix($_SESSION[$program]['lang']['backup_in_progress'].'...'); ?></h1>
                            <hr/>
                        </div>
                        <div id="progress" 
                             class='container' 
                             style="font-size: 60px;font-weight: bold;color:darkblue;word-break: break-all;">
                            
                        </div>
                        <script>
                            function check_backup_date() {
                                $('div#progress').html( $('div#progress').html() + '*' );
                                if (compare_system_backup_date()==true){
                                    // Бекап на стороне сервера завершился
                                    $('div#progress').html('');
                                    alert('<?php echo htmlfix($_SESSION[$program]['lang']['backup_was_done']); ?>');
                                    window.location = '?c=backup';
                                } else {
                                    setTimeout(check_backup_date,3000);
                                };
                            };
                            
                            $(document).ready( function(){
                                set_wait();
                                setTimeout(check_backup_date,3000);
                            });
                            
                            function compare_system_backup_date() {
                                var t = false ;
                                $.ajax({
                                    type: "POST",
                                    url: "?c=backup",
                                    async: false,
                                    data: {
                                        check_time: 'check_time'
                                    },
                                    error: function() {
                                        alert("Connection error, Can't update.");
                                        for(var i=0; i<inputs.length;i++) {
                                            inputs[i].value = oldvalues[i];
                                            $(inputs[i]).css('color','red');
                                        };
                                        remove_wait();
                                    },
                                    success: function(answer){
                                        if ( answer=="1" ) {
                                            t = true;
                                        };
                                    }
                                });
                                return t;
                            };
                        </script>
                    </body>
                    </html>
                    <?php
                    // Запускаем фоновый процесс снятия бэкапа
                    
                    /*
                    echo 'cd '.$service_directory.'/; php -f backup.php "'
                            .$_SESSION[$program]['new_backup_date'].'" "'
                            .$_SESSION[$program]['user_fio'].' login: '
                            .$_SESSION[$program]['UserConfiguration']['UserLogin'].'" > /dev/null &';
                     * 
                     */
                    exec('cd '.$service_directory.'/; php -f backup.php "'
                            .$_SESSION[$program]['new_backup_date'].'" "'
                            .$_SESSION[$program]['user_fio'].' login: '
                            .$_SESSION[$program]['UserConfiguration']['UserLogin'].'" > /dev/null &');
                    
                    //echo $_SESSION[$program]['new_backup_date'];
                    exit;
                };
            } else {
                // Запрос подтверждения создания архива
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
                                value='<?php echo htmlfix($_SESSION[$program]['lang']['start_backup']); ?>'/> 
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
      
?>
<div class='container'>
    <?php
        include 'app/model/table/backups.php';
    ?>
</div>
<div class="container no-print navbar navbar-fixed-bottom" 
             style="background-color: white; padding: 20px;">
    <form method="POST">
        <a class='btn btn-primary btn-large' href="?c=index">
            <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
        </a>
        <input 
            type='submit' 
            name="action"
            class='btn btn-danger btn-large' 
            value='<?php echo htmlfix($_SESSION[$program]['lang']['start_backup']); ?>'/>        
    </form>
</div>
