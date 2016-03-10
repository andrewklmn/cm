<?php

/*
 * Success message view
 */

        if (isset($data['success']) AND $data['success']!='') {
            ?>
            <div class="container">
                <div class="alert alert-success">  
                  <a class="close" data-dismiss="alert">×</a>  
                  <strong><?php echo $_SESSION[$program]['lang']['success']; ?>!</strong>
                  <br/>
                      <?php echo htmlfix($data['success']); ?>
                </div> 
            </div>
            <?php
        };

?>
