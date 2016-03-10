<?php

/*
 * Scenarion configuration page
 */

        if (!isset($c)) exit;
        
        $uploaddir = 'app/view/reports/';

        $data['title'] = 'Upload Report';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';

?>
<div class="container">
<?php
        if (isset($_POST['action'])) {
            
            $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
            //echo '<pre>';
            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                echo "File was successful uploaded.<br/>";
                //echo 'chmod 777 '.$uploadfile,'<br/>';
                system('chmod 777 '.$uploadfile);
            } else {
                echo "Something went wrong<br/>";
                echo '<pre>';
                print_r($_FILES);
                echo '</pre>';
            };
            //echo 'Некоторая отладочная информация:';
            //print_r($_FILES);
            //print "</pre>";
        };

?>

   <h4>Report files on server side:</h4>
   <table border="1">
       <tr>
           <th>Filename</th>
           <th>Filesize</th>
           <th>Created</th>
       </tr>
   <?php 

        $list = scandir($uploaddir);
        foreach ($list as $value) {
            echo '<tr>';
            if ( strtolower($value) != '.' AND strtolower($value) != '..') {
                echo '<td style="padding:3px;">',htmlfix($value),'</td>';
                echo '<td style="padding:3px;" align="right">',filesize ( $uploaddir.$value ),'</td>';
                echo '<td style="padding:3px;" align="center">',date('Y-m-d H:i:s',filemtime ( $uploaddir.$value )),'</td>';
            };
            echo '</tr>';
        };
   ?>
   </table>
   <hr/>
   <h4>Select report file and press Upload button:</h4>
   <form method="POST" enctype="multipart/form-data">
       <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
       <input name="userfile" type="file" />
       <input type="submit" name="action" value="Upload" />
   </form>
</div>
