<?php

/*
 * Контроллер мультиязычности
 */

    if (!isset($c)) exit;
    
    $data['title'] = "Cashmaster Filemanager";
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
   
    // Получаем путь кешмастера на сервере
    $cashmaster_root = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
    // Перечитываем содержимое папки
    
    if (!isset($_POST['directory'])) {
        $curdir = '';
    } else {
        $curdir = $_POST['directory'];
    };
    
    if (isset($_POST['newdir'])) {
        if ($_POST['newdir']=='..') {
            $t = explode('/',$curdir);
            unset($t[count($t)-1]);
            $curdir = implode('/', $t);
        } elseif ($_POST['newdir']=='/') {
            $curdir = '';
        } else {
            $curdir = $curdir.'/'.$_POST['newdir'];
        };
    };

    // Отрабатываем заданные действия с файлами
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'Upload file':
                if ($curdir=='') {
                    $uploaddir = $cashmaster_root.'/';
                } else {
                    $uploaddir = $cashmaster_root.'/'.$curdir.'/';
                };
                $uploadfile = $uploaddir.basename($_FILES['userfile']['name']);
                //echo '<pre>';
                if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                    $data['success'] = 'File '.$uploadfile.' was successful uploaded.';                        
                    include 'app/view/success_message.php';                    
                    system('chmod 777 '.$uploadfile);
                } else {
                    $data['error'] = 'Something went wrong.';                        
                    include 'app/view/error_message.php';
                    //echo '<pre>';
                    //print_r($_FILES);
                    //echo '</pre>';
                };
                break;
            case 'Make Cashmaster zip':
                /*
                $name = 'backup_cashmaster_'.date("Ymd_His").'.zip';
                exec("zip -r ".$name." ".$cashmaster_root );
                exec("chmod 777 ".$name);
                 * 
                 */
                break;
            case 'Delete':
                // Удаление выбранных файлов
                $files = explode('|', $_POST['files']);
                if (count($files) > 0 AND $files[0]!='') {
                    foreach ($files as $value) {
                        if ($curdir!='') {
                            unlink($cashmaster_root.'/'.$curdir.'/'.$value);
                        } else {
                            unlink($cashmaster_root.$value);
                        };
                    };
                    if (count($files) > 1) {
                        $data['success'] = 'Files: '.implode(', ',$files).' were deleted.';
                    } else {
                        $data['success'] = 'File: '.implode(', ',$files).' was deleted.';                        
                    }; 
                    include 'app/view/success_message.php';
                };
                break;
            default:
                break;
        };
    };
    
    
    
    // Получаем список файлов и папок в текущей папке
    $list = scandir($cashmaster_root.'/'.$curdir);
            
?>
<script>
    function selector_click() {
        var sel = $('input:checked');
        var files = [];
        $(sel).each(function(){
            files[files.length]=$($(this.parentNode.parentNode).find('a')[0]).html();
        });
        $('input#files').val(files.join('|'));
    };
</script>
<div class="container">
    <form method="POST" enctype="multipart/form-data">
        <div class="alert alert-info">
            <input type="hidden" name="directory" value="<?php echo htmlfix($curdir); ?>"/>
            <table>
                <tr>
                    <th></th>
                    <th>Filename</th>
                    <th>Size</th>
                    <th>Date</th>
                </tr>
                <?php
                    echo '<tr>';
                    ?>
                        <td></td>
                    <?php
                    echo '<td style="padding:3px;" colspan="3">
                          <a title="Back to root directory" 
                            href="?c=files"
                            class="btn btn-small btn-info">/</a> ';
                        echo '<font style="font-size:14px;color:darkblue;">';
                        echo htmlfix(substr($curdir,1));
                        echo '</font></td>';
                    echo '</tr>';
                    if ($curdir!='') {
                        echo '<tr>
                             <td></td>';
                        echo '<td colspan="3">';                        
                        echo ' <input 
                                    title="Back to parent directory"
                                    class="btn btn-small"
                                    type="submit" 
                                    name="newdir" 
                                    value=".."/>'; 
                        echo'</td>';
                        echo '</tr>';
                    };
                    foreach ($list as $value) {
                        if ( strtolower($value) != '.' 
                                AND strtolower($value) != '..'
                                AND is_dir($cashmaster_root.'/'.$curdir.'/'.$value)) {
                            echo '<tr>';
                            ?>
                                <td></td>
                            <?php
                            echo '<td style="padding:3px;">
                                    <input 
                                        class="btn btn-small"
                                        type="submit" 
                                        name="newdir" 
                                        value="',htmlfix($value),'"/></td>';
                            echo '<td style="padding:3px;" align="right">',filesize ( $cashmaster_root.'/'.$curdir.'/'.$value ),'</td>';
                            echo '<td style="padding:3px;" align="center">',date('Y-m-d H:i:s',filemtime ( $cashmaster_root.'/'.$curdir.'/'.$value )),'</td>';                                                                
                            echo '</tr>';
                        };
                    };
                    
                    foreach ($list as $value) {
                        if ( strtolower($value) != '.' 
                                AND strtolower($value) != '..'
                                AND !is_dir($cashmaster_root.'/'.$curdir.'/'.$value)) {
                            echo '<tr>';
                            ?>
                                <td>
                                    <input class="selector" type="checkbox" onchange="selector_click();">
                                </td>
                            <?php
                            echo '<td style="padding:3px;">';
                            echo '<a target="_blank" href="?c=get_file&n=',urlencode($curdir.'/'.$value),'">';
                            echo htmlfix($value);
                            echo '</a>';
                            echo '</td>';
                            echo '<td style="padding:3px;" align="right">',filesize ( $cashmaster_root.'/'.$curdir.'/'.$value ),'</td>';
                            echo '<td style="padding:3px;" align="center">',date('Y-m-d H:i:s',filemtime ( $cashmaster_root.'/'.$curdir.'/'.$value )),'</td>';
                            echo '</tr>';
                        };
                    };
                ?>
            </table>
        </div>
        <span class="btn btn-medium" onclick="$('input.selector').click();">Select all files</span>
        <input type="hidden" id="files" name="files" value="">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
        <input class="btn" name="userfile" type="file" />
        <input class="btn btn-warning btn-medium" type="submit" name="action" value="Upload file" />
        <?php 
            if ($curdir=='') {
                ?>
                    <a class="btn btn-info btn-medium" href="?c=get_archive" target="_blank">
                        Download Cashmaster as ZIP
                    </a>
                <?php
            };
        ?>
        <!--<input type="submit" name="action" value="Download file" class="btn btn-info btn-large">-->
        <input type="submit" name="action" value="Add to update envelope" class="btn btn-primary btn-medium">
        <input type="submit" name="action" value="Delete" class="btn btn-danger btn-medium">
    </form>
</div>
<?php

    include 'app/controller/common/envelope.php';

?>