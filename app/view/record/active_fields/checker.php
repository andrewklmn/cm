<?php

/*
 * text field
 */

        if (!isset($c)) exit;

        echo '<td>';
        echo '<input class="field" type="hidden" name="',$record['formula'][$key],'" 
                value="',htmlfix($d),'" 
                oldvalue="',htmlfix($data[$key]),'"/>';
        
        if(preg_match('/(?i)msie [8-9]/',$_SERVER['HTTP_USER_AGENT'])) {
            // if MSIE<=9
            echo '<span class="checker" onclick="_c(this);" 
                      style="font-size:1.5em;',$color,'">',
                   ($d!=1)?'[ ]':'[√]','</span>';
        } else {
            // if MSIE>10
            echo '<span class="checker" onclick="_c(this);" 
                      style="font-size:1.5em;',$color,'">',
                   ($d!=1)?'☐':'☑','</span>';
        };
        
        echo '</td>';
        
?>
