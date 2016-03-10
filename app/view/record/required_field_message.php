<?php

    if ( $record['edit']==true ) {
        if (!isset($confirm_button) OR $confirm_button!=true ) {
            if ($not_null_label!='') {
                echo '<font style="font-family: serif;font-size: 20px;color:red;font-weight: bold;">*</font>
                     <font style="color:red;"> - ',htmlfix($not_null_label),'</font>';
            };
        };
    };
?>
    <br/>