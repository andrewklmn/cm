<?php

/*
 * Select field
 */


        echo '<td style="padding-left:10px;">';
        echo '<input 
            class="field" 
            type="hidden" 
            readonly
            name="',$record['formula'][$key],'" 
            value="',htmlfix($d),'" 
            oldvalue="',htmlfix($data[$key]),'"/> ';
        $list = get_array_from_sql($record['select'][$key]);
        foreach ($list as $val) {
            if ($d==$val[0]) {
                echo htmlfix($val[1]);
                break;
            };
        };
        /*
        echo '<select 
                    style="color:'.$color.';width:',isset($width[$key])?$width[$key]:'120','px;"
                    onchange="_u(this);" 
                    class="field" 
                    readonly
                    name="',$formula[$key],'" 
                    oldvalue="',htmlfix($data[$key]),'">';
        // получаем список значений для селекта
        $list = get_array_from_sql($select[$key]);
        foreach ($list as $val) {
            if ($d==$val[0]) {
                echo '<option selected value="',htmlfix($val[0]),'">',htmlfix($val[1]),'</option>';
                break;
            };
        };
        echo '</select>';
         * 
         */
        echo '</td>';
?>
