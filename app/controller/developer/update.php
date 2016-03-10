<?php

/*
 * Список пользователей
 */

        if (!isset($c)) exit;
        
        $b = explode('|',$_SESSION[$program]['lang']['users_buttons']);
        $l = explode('|',$_SESSION[$program]['lang']['users_labels']);
        
        $data['title'] = $_SESSION[$program]['lang']['update_tool'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        
        $output = array();
        
        if (isset($_POST['action']) AND $_POST['action']=='Upload APP archive') {

            if ($_FILES['userfile']['error'] == UPLOAD_ERR_OK               
                  AND is_uploaded_file($_FILES['userfile']['tmp_name'])) { 
                
                // Имя загруженного архива
                $uploadfile = 'input/'.basename($_FILES['userfile']['name']);
                
                
                if (substr($_FILES['userfile']['name'], 0, 28)=='app_files_archive_cashmaster') {                        
                    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                        $data['success'] = 'Archive '.$uploadfile.' was successful unziped.';                        
                        include 'app/view/success_message.php';                    
                        exec('chmod 777 '.$uploadfile , $output);
                        exec('rm -Rf app' , $output);
                        exec('unzip '.$uploadfile , $output);
                        exec('chmod -R 777 app' , $output);
                        exec ('rm -f '.$uploadfile,$output);
                        
                        include 'app/controller/common/refill_integrity_check.php';
                        
                    } else {
                        $data['error'] = 'Something went wrong.';                        
                        include 'app/view/error_message.php';
                        //echo '<pre>';
                        //print_r($_FILES);
                        //echo '</pre>';
                    };
                } else {
                    $data['error'] = 'File is wrong.';                        
                    include 'app/view/error_message.php';                    
                };
            } else {
                //$data['error'] = 'Upload system update went wrong';                        
                $data['error'] = $_SESSION[$program]['lang']['update_upload_wrong'];
                include 'app/view/error_message.php';
            };
        };
      
?>
<div class='container'>
    <h3>This tool updates whole CashMaster scripts from uploaded ZIP-archive.</h3>
</div>
<div class="container no-print navbar" 
             style="background-color: white; padding: 20px;">
    <form method="POST" enctype="multipart/form-data">
        <a class='btn btn-primary btn-large' href="?c=index">
            <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
        </a>
        <a class='btn btn-warning btn-large' target="_blank" href="?c=get_app_archive">
            Download APP archive 
        </a>
        <input type="hidden" id="files" name="files" value="">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
        <input class="btn" name="userfile" type="file" />
        <input class="btn btn-danger btn-large" 
               type="submit" 
               name="action" 
               value="Upload APP archive" />
    </form>
</div>
<?php 
    if(count($output)>0) {
        echo '<div class="container">
                    <p style="font-size:10px;">';
        foreach ($output as $value) {
            echo htmlfix($value),'<br/>';
        };
        echo '      </p>
              </div>';
    };
?>