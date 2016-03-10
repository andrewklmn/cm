<?php

        if (!isset($c)) exit;

        $roles = array(1,2,3,4); // массив отображаемых ролей 
        
        $data['title'] = $_SESSION[$program]['lang']['system_events_archive'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        //include 'app/view/reload_after_1_min.php';
        
        $list = scandir($log_backup_directory);
        rsort($list);

        $backups = array();
        foreach ($list as $value) {
            if ($value!='.' 
                    AND $value!='..') {
                $t = explode('_', $value);
                $d = explode('.', $t[2]);                
                $d = get_user_config_by_login($d[0]);
                
                $backups[]=array(
                                    $t[0].' '.  str_replace('-', ':', $t[1]),
                                    $d['UserLogin'].' - '
                                        .$d['UserPost'].': '
                                        .$d['UserFamilyName'].' '
                                        .$d['UserFirstName'].' '
                                        .$d['UserPatronymic'].' '
                );
            };
        };
        
?>
<script>
    function open_log_backup(date) {
        var tds = $(date).find('td');
        var t = tds[0].innerHTML.split(' '); 
        var l = tds[1].innerHTML.split(' ');
        var name = t[0] + '_' + t[1].replace(/\:/g,'-') + '_' + l[0];
        window.open('?c=get_log&name=' + encodeURI(name),'file');
    };
</script>
<div class="container">
    <?php
            unset($table);
            $table['data'] = $backups;
            $table['header'] = explode('|',$_SESSION[$program]['lang']['backup_admin_table_header']);
            $table['width'] = array( 200,400);
            $table['align'] = array( 'center','left');
            $table['th_onclick']=array( ';',';',';',';',';',';' );
            $table['tr_onclick']='open_log_backup(this.parentNode);';
            $table['title'] = $_SESSION[$program]['lang']['backup_list'];
            $table['hide_id'] = 0;
            include_once 'app/view/draw_select_table.php';
            draw_select_table($table);

            $b = explode('|',$_SESSION[$program]['lang']['users_buttons']);
    ?>
</div>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<div class="no-print navbar navbar-fixed-bottom" 
                 style="background-color: white; padding: 20px;">
     <div class="container">
        <form method="POST">
            <a class='btn btn-primary btn-large' href='?c=index'><?php echo htmlfix($b[0]); ?></a>
        </form>
    </div>
</div>