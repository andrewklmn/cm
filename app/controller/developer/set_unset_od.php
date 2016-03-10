<?php

/*
 * Главное меню Администратора
 */

        if (!isset($c)) exit;
        
        $input_directory = 'input';
        
        if (isset($_POST['action']) AND $_POST['action']=='Back to main page') {
            header('location: ?c=index');
            exit;
        };        
        
        $data['title'] = "Operation Day";
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
        //echo '<pre>';
        //print_r($_POST);
        //echo '</pre>';
        
                
        if (isset($_POST['action']) AND $_POST['action']=='Send XML to input') {
            // Сохраняем XML с нужной датой и статусом
            $name = $input_directory.'/'.time().'.xml';
            $fh = fopen( $name, 'w');
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
<TASKOD xmlns:csm="urn:cbr-ru:csm:v1.0">
    <CurentOD DateOD="'.$_POST['date'].'" StateOD="'.$_POST['status'].'"/>
</TASKOD>
';
            fwrite($fh, $xml);
            fclose($fh);
            $state = ($_POST['status']=='1')?'OPEN':'CLOSED';
            if (file_exists($name)) {
                $data['success'] = 'XML file with status '.$state.' was created';
                include 'app/view/success_message.php';                
            } else {
                $data['error'] = 'XML file with status '.$state.' was NOT created';
                include 'app/view/error_message.php';                
            };
        };
        
?>
        <div class="container">
            <h3>Select operation day date and status:</h3>
            <form method="POST">
                <br/>
                <br/>
                Date: 
                <input type="text" class="search-query" name="date" value="<?php echo date("Y-m-d",time()); ?>"/>
                <br/>
                <br/>
                Status:
                <select name="status">
                    <?php 
                        if (isset($_POST['status']) AND $_POST['status']=="0") {
                            ?>
                                <option selected value="0">Closed</option>
                                <option value="1">Open</option>
                            <?php 
                        } else {
                            ?>
                                <option value="0">Closed</option>
                                <option selected value="1">Open</option>
                            <?php
                        };
                    ?>
                </select>
                <hr/>
                <input class="btn btn-primary btn-large" type="submit" name="action" value="Back to main page"/>
                <input class="btn btn-danger btn-large" type="submit" name="action" value="Send XML to input"/>
            </form>
        </div>    
    </body>
</html>
