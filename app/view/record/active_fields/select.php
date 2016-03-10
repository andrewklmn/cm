<?php

/*
 * Select field
 */

        echo '<td>';
        echo '<select 
                    style="color:'.$color.';width:',isset($record['width'][$key])?$record['width'][$key]:'120','px;"
                    onchange="_u(this);" 
                    class="field" 
                    name="',$record['formula'][$key],'" 
                    oldvalue="',htmlfix($data[$key]),'">';
        // получаем список значений для селекта
                $list = get_array_from_sql($record['select'][$key]);
        foreach ($list as $val) {
            if ($d==$val[0]) {
                echo '<option selected value="',htmlfix($val[0]),'">',htmlfix($val[1]),'</option>';
            } else {
                echo '<option value="',htmlfix($val[0]),'">',htmlfix($val[1]),'</option>';
            };
        };
        echo '</select>';
        echo '</td>';
?>
