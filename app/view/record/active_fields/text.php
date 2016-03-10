<?php

/*
 * text field
 */

        if (!isset($c)) exit;

        echo '<td>';
        echo '<input 
                    style="color:'.$color.';width:',isset($record['width'][$key])?$record['width'][$key]:'120','px;"
                    autocomplete="off"
                    onkeyup="_u(this);" 
                    onfocus="$(this).css(\'background-color\',\'white\');"
                    class="field',($not_null[$key])?' required':'','" 
                    type="text" 
                    maxlength="',isset($maxwidth[$key])?$maxwidth[$key]:'80','"
                    name="',$record['formula'][$key],'" 
                    value="',htmlfix($d),'" 
                    oldvalue="',htmlfix($data[$key]),'"/>';
        if ($not_null[$key]) {
            echo ' <font style="font-family: serif;font-size: 20px;color:red;font-weight: bold;">*</font>';
        };
        echo '</td>';
?>
