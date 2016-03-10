<?php

/*
 * Success message view
 */
        /*
           $data['danger_header'];
           $data['danger_text'];
         */

        ?>
        <div class="container">
            <div class="alert alert-danger">  
              <a class="close" data-dismiss="alert">×</a>  
              <strong><?php echo $data['danger_header']; ?>:</strong>
                  <?php echo $data['danger_text']; ?>
            </div> 
        </div>
        <?php

?>
