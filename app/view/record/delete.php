<?php

/*
 * Delete action
 */
    if ($record['delete']==true) {
        if (isset($_POST['confirmation'])) {
            if($_POST['confirmation']==$b[6]) {
                // Можно удалять
                do_sql('
                    DELETE FROM 
                                '.addslashes($record['table']).'
                    WHERE 
                        '.addslashes($table_key).'="'.addslashes($_GET['id']).'"
                ;');
                ?>
                    <div class="container">
                        <div class="alert alert-success">  
                          <a class="close" data-dismiss="alert">×</a>  
                          <strong><?php echo $_SESSION[$program]['lang']['success']; ?>!</strong>
                          <br/>
                              <?php echo htmlfix($w[3]); ?>
                        </div> 
                        <br/>
                        <a class="btn btn-primary btn-large"
                           href="<?php echo $record['back_page']; ?>"><?php echo htmlfix($b[7]); ?></a>
                    </div>
                <?php
                do_sql('UNLOCK TABLES;');
                exit;
            } else {
                if($_POST['confirmation']!=$b[0]) {
                    ?>
                     <div class="container">
                         <div class="alert alert-error">  
                           <a class="close" data-dismiss="alert">×</a>  
                           <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                           <br/>
                               <?php echo htmlfix($w[4]); ?>
                         </div> 
                     </div>
                     <?php
                };
            };
        } else {
            // Форма запроса подтверждения
            do_sql('UNLOCK TABLES;');
            ?>
                <div class="container">
                    <h3><?php echo htmlfix($h[1]); ?></h3>
                    <table class="record">
                        <?php 
                            foreach ($record['labels'] as $key=>$value) {
                                $color = 'black';
                                $d = $data[$key];
                                echo '<tr>';
                                echo '<th align="right">',$value,':</th>';
                                switch ($record['type'][$key]) {
                                   case 'select':
                                       include 'readonly_fields/select.php';
                                       break;
                                   case 'checker':
                                       include 'readonly_fields/checker.php';
                                       break;
                                   case 'logical':
                                       include 'readonly_fields/logical.php';
                                       break;
                                   default:
                                        echo '<td style="padding-left:10px;">',htmlfix($data[$key]),'</td>';
                                        break;
                                };
                                echo '</tr>';
                            };
                        ?>
                    </table>
                    <br/>
                    <form method="POST">
                        <input type="hidden" name="olddata" value="<?php echo htmlfix(implode('|', $data ));  ?>">
                        <input type="hidden" name="action" value="<?php echo htmlfix($b[3]);  ?>"/>
                        <input class="btn btn-primary btn-large" 
                               type="submit" 
                               name="confirmation" 
                               value="<?php echo htmlfix($b[0]); ?>">
                        <input 
                               class="btn btn-danger btn-large" 
                               type="submit" 
                               name="confirmation" 
                               value="<?php echo htmlfix($b[6]); ?>">
                    </form>
                </div>
            <?php
            exit;
        };
    } else {
        ?>
            <div class="container">
                <div class="alert alert-error">  
                  <a class="close" data-dismiss="alert">×</a>  
                  <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                  <br/>
                      <?php echo htmlfix($w[6]); ?>
                </div> 
            </div>
        <?php
    };
?>
