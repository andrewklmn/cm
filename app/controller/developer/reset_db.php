<?php

/*
 * Сброс базы данных к исходным значениям
 */

    if (!isset($c)) exit;

    include './app/view/html_header.php';
    
?>
    <script src="js/jquery-2.0.2.min.js"></script>
<?php

    echo 'Reset in progress... please, wait.<br/> ';
    
    $file = 'DB_RESET.sql';
    $command = 'mysql --default-character-set=utf8 -u '.DB_USER.' -p'.DB_PASS.' '.DB_BASE.' < lib/'.$file;

    echo '<pre>';
    echo "Restoring file: $file\n"; 
    echo $command,"\n";
    system($command,$status);
    echo '</pre>';
    
    if ($status==0) {
        ?>
            <h3>Base was reset.</h3>
            <br/>
            <br/>
            <a style='width:150px;' 
               onclick="$('body').html('Update 1 in progress... please, wait.<br/> ');"
               class="btn btn-primary btn-large" href="?c=insert_data1">Next update step</a>            
        <?php
    } else {
        ?>
            <h3>There is error in <?php echo $file; ?></h3>
            <p>Please use MySQL Workbench to reset the base from reset scripts.</p>
            <br/>
            <br/>

            <a style='width:150px;' class="btn btn-primary btn-large" href="?c=index">Back to Index Page</a>
        <?php
    };
?>
