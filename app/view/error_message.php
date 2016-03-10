<?php

/*
 * Error message view
 */

        if (isset($data['error']) AND $data['error']!='') {
            ?>
            <div class="container">
                <div class="alert alert-error">  
                  <a class="close" data-dismiss="alert">×</a>  
                  <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                  <br/>
                      <?php echo $data['error']; ?>
                </div> 
            </div>
            <?php
        };

?>
