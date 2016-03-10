<?php

/*
 * text field
 */

        if (!isset($c)) exit;

        echo '<td>';
        echo '<input 
                    style="color:'.$color.';width:',isset($width[$key])?$width[$key]:'120','px;"
                    autocomplete="off"
                    class="field" 
                    type="text" 
                    readonly
                    maxwidth="',isset($maxwidth[$key])?$maxwidth[$key]:'80','"
                    name="',$record['formula'][$key],'" 
                    value="',htmlfix($d),'" 
                    oldvalue="',htmlfix($data[$key]),'"/>';
        echo '</td>';
?>
