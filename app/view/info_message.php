<?php

/*
 * Success message view
 */
        /*
           $data['info_header'];
           $data['info_text'];
         */

        ?>
        <div class="container">
            <div class="alert alert-info">  
              <a class="close" data-dismiss="alert">×</a>  
              <strong>
                  <?php echo (!isset($data['info_header']) 
                                    OR $data['info_header']=='')?'':$data['info_header'].':'; ?>
              </strong>
                  <?php echo $data['info_text']; ?>
            </div> 
        </div>
        <?php

?>
