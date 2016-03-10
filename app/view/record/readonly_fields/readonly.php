<?php

/*
 * Readonly text field
 */
    echo '<td 
            style="padding-left:10px;">',htmlfix($d);
    echo '<input 
            class="field" 
            type="hidden" 
            name="',$record['formula'][$key],'" 
            value="',htmlfix($d),'" 
            oldvalue="',htmlfix($data[$key]),'"/>';
    echo '</td>';

?>
