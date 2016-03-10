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
            <div class="alert alert-danger">  
              <a class="close" data-dismiss="alert">×</a>  
              <strong>
                  <?php echo (!isset($data['header']) 
                                    OR $data['header']=='')?'':$data['header'].':'; ?>
              </strong>
                  <?php echo $data['text']; ?>
            </div> 
        </div>
        <?php

?>
