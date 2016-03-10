<?php

/*
 * Форма ввода серийных номеров сомнительных банкнот
 */
        if (!isset($c)) exit;
        
        foreach ($pos as $val) {
            ?>
                <tr id="<?php echo htmlfix($val[0]); ?>">
                    <td align="center"><?php echo htmlfix($val[1]); ?></td>
                    <td align="right"><?php echo htmlfix($val[2]); ?></td>
                    <td>
                        <input 
                            class='stat'
                            onfocus='$(this).css("background-color","white");'
                            type="text" 
                            style="width:80px;" 
                            onkeyup="s(this);"
                            onblur="update_record(this);"
                            value="<?php echo htmlfix($val[3]); ?>"
                            oldvalue="<?php echo htmlfix($val[3]); ?>"
                            maxlength="<?php echo htmlfix($val[7]); ?>"/>
                    </td>
                    <td>
                        <input 
                            class='stat'
                            onfocus='$(this).css("background-color","white");'
                            type="text" 
                            style="width:200px;" 
                            onkeyup="n(this);"
                            onblur="update_record(this);"
                            value="<?php echo htmlfix($val[4]); ?>"
                            oldvalue="<?php echo htmlfix($val[4]); ?>"
                            maxlength="<?php echo htmlfix($val[8]); ?>"/>
                    </td>
                    <td>
                        <input 
                            class='stat'
                            onfocus='$(this).css("background-color","white");'
                            type="text" 
                            style="width:80px;" 
                            onkeyup="s1(this);"                            
                            onblur="update_record(this);"
                            value="<?php echo htmlfix($val[5]); ?>"
                            oldvalue="<?php echo htmlfix($val[5]); ?>"
                            maxlength="<?php echo htmlfix($val[7]); ?>"/>
                    </td>
                    <td>
                        <input 
                            onfocus='$(this).css("background-color","white");'
                            class='stat'
                            type="text" 
                            style="width:200px;" 
                            onkeyup="n1(this);"
                            onblur="update_record(this);"
                            value="<?php echo htmlfix($val[6]); ?>"
                            oldvalue="<?php echo htmlfix($val[6]); ?>"
                            maxlength="<?php echo htmlfix($val[8]); ?>"/>
                    </td>
                </tr>
            <?php
        };
        
?>