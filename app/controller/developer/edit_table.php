<?php

/*
 * Table data editor
 */

        if (!isset($c)) exit;
        
        
        if (!isset($_GET['name'])) {
            $data['title'] = 'Edit table data';
            include './app/view/page_header_with_logout.php';
            $data['error'] = 'Table name is not set';
            include './app/view/error_message.php';
            exit;
        };

        include './app/model/check_table_exist.php';
        if (!check_table_exist($_GET['name'])) {
            $data['title'] = 'Edit "'.htmlfix($_GET['name']).'" table data';
            include './app/view/page_header_with_logout.php';
            $data['error'] = 'Table "'.$_GET['name'].'" is not exist in database';
            include './app/view/error_message.php';
            exit;            
        }
        
        $data['title'] = 'Edit "'.htmlfix($_GET['name']).'" table data';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/get_to_string.php';
        
        ?>
<style>
    th.index {
        cursor: pointer;
    }
</style>
<script>
    function editor_key(event){
        var key = event.keyCode;
        var elem = ( event.target ) ? event.target : event.srcElement;
        switch(key){
            case 27:
                if (elem.type=='checkbox') {
                    if ($(elem).attr('oldvalue')=='checked') {
                        $(elem).attr('checked', true);
                    } else {
                        $(elem).attr('checked', false);
                    }
                } else {
                    elem.value = $(elem).attr('oldvalue');                    
                }
            break;
            case 13:
                $(elem).blur();
                var next_id=parseInt(elem.id) + 1;
                if ($('input#' + next_id).length>0) {
                    $('input#' + next_id).focus();
                    $('input#' + next_id).select();
                } else {
                    $('select#' + next_id).focus();
                }
            break;
            default:
        }
    }
    function update_records(elem){
        if (!(elem.type=='checkbox')) {
            if ( elem.value==$(elem).attr('oldvalue') ) { 
                return true;
            };
        } else {
            var status = ($(elem).prop('checked'))?'checked':'';
            if (status==$(elem).attr('oldvalue')) {
                return true;
            };
        }
        set_wait();
        var key_name = $(elem.parentNode.parentNode).attr('name');
        var key_value = elem.parentNode.parentNode.id;
        var fields = new Array();
        var values = new Array();
        var oldvalues = new Array();
        var table = $($('table#target')[0]).attr('name');
        var inputs = $(elem.parentNode.parentNode).find('.editor');
        for(var i=0; i<inputs.length;i++) {
            fields[i]=$(inputs[i]).attr('name');
            oldvalues[i]=$(inputs[i]).attr('oldvalue');
            if (inputs[i].type=='checkbox') {
                if ($(inputs[i]).prop("checked")){
                    values[i]='checked';
                } else {
                    values[i]='';
                }
            } else {
                values[i]=inputs[i].value;
            }
        }
        $.ajax({
            type: "POST",
            url: "?c=edit_table_update",
            async: false,
            data: {
                table: table,
                key_name: key_name,
                key_value: key_value,
                fields: fields.join('||'),
                values: values.join('|'),
                oldvalues: oldvalues.join('|')
            },
            error: function() {
                alert("Connection error, Can't update.");
                for(var i=0; i<inputs.length;i++) {
                    inputs[i].value = oldvalues[i];
                    $(inputs[i]).css('color','red');
                };
                remove_wait();
            },
            success: function(answer){                
                switch(answer[0]) {
                    case '0':
                        answer = answer.substring(1);
                        var newvalues = answer.split('|');
                        for(var i=0; i<inputs.length;i++) {
                            if(inputs[i].type=='checkbox') {
                                if(newvalues[i]=='checked') {
                                    $(inputs[i]).prop('checked', true);
                                } else {
                                    $(inputs[i]).prop('checked',false);
                                }
                            } else {
                                inputs[i].value = newvalues[i];                                
                            };
                            $(inputs[i]).attr('oldvalue',newvalues[i]);
                        };
                    break;
                    case '1':
                        alert('Record was changed by another user');
                        answer = answer.substring(1);
                        var newvalues = answer.split('|');
                        for(var i=0; i<inputs.length;i++) {
                            if(inputs[i].type=='checkbox') {
                                if(newvalues[i]=='checked') {
                                    $(inputs[i]).prop('checked', true);
                                } else {
                                    $(inputs[i]).prop('checked', false);
                                };
                            } else {
                                inputs[i].value = newvalues[i];                                
                            };
                            $(inputs[i]).attr('oldvalue',newvalues[i]);
                            $(inputs[i]).css('color','red');
                        };
                    break;
                    default:
                        alert(answer);
                        var newvalues = oldvalues;
                        for(var i=0; i<inputs.length;i++) {
                            if(inputs[i].type=='checkbox') {
                                if(newvalues[i]=='checked') {
                                    $(inputs[i]).prop('checked', true);
                                } else {
                                    $(inputs[i]).prop('checked', false);
                                };
                            } else {
                                inputs[i].value = newvalues[i];                                
                            };
                            $(inputs[i]).attr('oldvalue',newvalues[i]);
                            $(inputs[i]).css('color','red');
                        };
                }   
                remove_wait();
            },
            dataType: "html"
        });
    }
</script>
<div class='container'>
    <?php 
    
    
        if (isset($_POST['action']) AND $_POST['action']=='add_new') {
            include 'app/controller/developer/edit_table_add.php';
        };
    
    
        $sql = 'DESCRIBE '.addslashes($_GET['name']).';';
        $fields = get_array_from_sql($sql);
        
        $fields_name=array();
        $default = array();
        $format = array();
        $length = array();
        $width = array();
        $align = array();
        $readonly = array();
        $key_index = 0;
        $key_name='';
                
    
        //CHECK IF REFERENCED FIELDS EXIST =====================================
        $sql = 'SELECT
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
        FROM 
                information_schema.KEY_COLUMN_USAGE
        WHERE 
                CONSTRAINT_SCHEMA="cashmaster"
                AND TABLE_NAME = "'.addslashes($_GET['name']).'"
                AND REFERENCED_COLUMN_NAME is not null;
         ';
        $table_keys = get_array_from_sql($sql);
        //print_r($table_keys);
        
        if (count($table_keys)>0) {
            //prepare selects for the table
            foreach ($table_keys as $key => $value) {
                $sql = '
                    SELECT
                        *
                    FROM
                        '.$value[2].';
                ';
                $table_keys[$key][] = get_assoc_array_from_sql($sql);
            }
        }
        
        // Draw Table Editor Form ==============================================
        echo '<h3 style="margin-bottom:0px;">Table "'.$_GET['name'].'"</h3>'; 
        
    ?>
    <table id="target" name="<?php echo $_GET['name']; ?>" style="margin-bottom: 5px;">
        <tr>
            <!---
            <th style="padding:10px; font-size:11px; border: 2px solid black;">*</th>
            -->
            <?php 
            
                // рисуем заголовок таблицы
                foreach ($fields as $key => $value) {
                    $format[$key] = $value[1];
                    $fields_name[$key] = $value[0];
                    $default[$key] = $value[4];
                    if ($value[1]=='datetime' OR $value[1]=='timestamp') {
                        $length[$key] = 19;
                        $width[$key] = 110;
                        $align[$key]='center';
                    } else {
                        $length[$key] = preg_replace("/[^0-9]/", '', $value[1]); ;                        
                        $width[$key] = 7*$length[$key];
                        if ($width[$key]<70) $width[$key]=70;
                        if ($width[$key]>300) $width[$key]=300;
                        
                        if (substr($format[$key],0,3)=='int') {
                            $align[$key] = 'center';
                        };
                        if (substr($format[$key],0,6)=='bigint') {
                            $align[$key] = 'center';
                            $width[$key] = '80';
                        };
                        if (substr($format[$key],0,7)=='tinyint') {
                            $align[$key] = 'center';
                        };
                        if (substr($format[$key],0,8)=='smallint') {
                            $align[$key] = 'center';
                        };
                        if (substr($format[$key],0,7)=='varchar') {
                            $align[$key] = 'left';
                        };
                        if (substr($format[$key],0,7)=='decimal') {
                            $align[$key] = 'right';
                            $width[$key] = '100';
                        };
                        if ($format[$key]=='date') {
                            $align[$key] = 'center';
                            $width[$key] = '70';
                        };
                    };
                    if ($value[3]=='PRI') {
                        $readonly[$key]='readonly';
                        $key_index = $key;
                        $key_name = $value[0];
                    } else {
                        $readonly[$key]='';
                    };
                    
                    
                    if (isset($_GET['order']) AND $value[0]==$_GET['order']) {
                        if (isset($_GET['sort']) AND $_GET['sort']=='ASC') {
                            echo '<th style="padding:3px; font-size:11px; border: 2px solid black;">
                                    <a onclick="set_wait();" href="?c=edit_table&name=',htmlfix($_GET['name']),'&order=',$value[0],'&sort=DESC">
                                        <font style="color:red;">',$value[0],'</font>
                                    </a><br/>
                                    <font style="color:gray;font-size:11px;">
                                            ',$value[1],'
                                    </font>
                                 </th>';
                        } else {
                            echo '<th style="padding:3px; font-size:11px; border: 2px solid black;">
                                    <a onclick="set_wait();" href="?c=edit_table&name=',htmlfix($_GET['name']),'&order=',$value[0],'&sort=ASC">
                                        <font style="color:blue;">',$value[0],'</font>
                                    </a><br/>
                                    <font style="color:gray;font-size:11px;">
                                            ',$value[1],'
                                    </font>
                                 </th>';                            
                        }
                    } else {
                        if ($value[3]=='PRI' AND !isset($_GET['order'])) {
                            echo '<th style="padding:3px; font-size:11px; border: 2px solid black;">
                                <a onclick="set_wait();" href="?c=edit_table&name=',htmlfix($_GET['name']),'&order=',$value[0],'&sort=DESC">
                                    <font style="color:red;">',$value[0],'</font>
                                </a><br/>
                                <font style="color:gray;font-size:11px;">
                                        ',$value[1],'
                                </font>
                             </th>';
                        } else {
                            echo '<th style="padding:3px; font-size:11px; border: 2px solid black;">
                                    <a onclick="set_wait();" href="?c=edit_table&name=',htmlfix($_GET['name']),'&order=',$value[0],'&sort=ASC">
                                        <font style="color:black;">',$value[0],'</font>
                                    </a><br/>
                                    <font style="color:gray;font-size:11px;">
                                            ',$value[1],'
                                    </font>
                                 </th>';                            
                        }
                    };
                }
            ?>
        </tr>
        <?php 
        
            $rows_on_page = 15;
            
            // Get last index ==================================================
            $sql = '
                SELECT
                    MAX('.$key_name.')
                FROM
                    '.addslashes($_GET['name']).'
            ;';
            //echo $sql;
            $r = fetch_row_from_sql($sql);            
            $last_index = $r[0];
            
            // Get table data ==================================================
            $sql = 'SELECT count(*) FROM '.addslashes($_GET['name']).';';
            $count = fetch_row_from_sql($sql);
            $pages = ceil($count[0]/$rows_on_page);

            if (!isset($_GET['limit'])) {
                $limit = '0';
            } else {
                $limit = $_GET['limit'];
            };
            
            if (!isset($_GET['order'])) {
                $order_name=$key_name;
                $sort_mode='ASC';
            } else {
                $order_name=$_GET['order'];
                $sort_mode=$_GET['sort'];
            }
            
            $sql = '
                    SELECT 
                        * 
                    FROM '.addslashes($_GET['name']).' 
                    ORDER BY '.$order_name.' '.$sort_mode.' 
                    LIMIT '.$limit.','.$rows_on_page.';';
            
            $records = get_array_from_sql($sql);
            $id=0;
            foreach ($records as $key=>$value) {
                echo '<tr id="',$value[$key_index],'" name="',$key_name,'">';
                /*
                echo '<!---<th   
                            onclick="index_click(this);"
                            class="index" style="padding:0px; font-size:11px; border: 2px solid black;"></th>-->';
                 * 
                 */
                foreach ($value as $k=>$v) {
                    ?><td align="center"><?php
                    
                        // default is no referenced field
                        $ref_table='';
                        $ref_field='';
                        
                        // check if field has refereced link ===================
                        foreach ($table_keys as $kk=>$vv) {
                            if ($vv[1]==$fields_name[$k]) {
                                $ref_table = $vv[2];
                                $ref_field = $vv[3];
                            };
                        }
                        
                        if ($ref_table!='' AND $ref_field!='') {
                            //prepare list
                            $sql = '
                                SELECT 
                                    * 
                                FROM
                                    '.$ref_table.'
                                ORDER BY '.$ref_field.' ASC;';
                            $row = get_array_from_sql($sql);
                            $options='';
                            foreach ($row as $kk=>$vv) {
                                if ($v==$vv[0]) {
                                    $options .= '<option selected value="'.$vv[0].'">';
                                } else {
                                    $options .= '<option value="'.$vv[0].'">';
                                }
                                $options .= $vv[1];
                                $options .= '</option>';
                                
                            };
                            ?><select 
                                    onkeyup="editor_key(event);"
                                    onblur="update_records(this);"
                                    id="<?php echo $id; ?>"
                                    name="<?php echo $fields_name[$k],'|',$format[$k]; ?>"
                                    style="
                                        height: 20px;
                                        margin: 0px;
                                        padding: 0px;
                                        font-size:10px;
                                        text-align:left;"
                                    class="editor" 
                                    oldvalue="<?php echo htmlfix($v); ?>"
                                    ><?php echo $options;?></select>
                            <?php
                        } else {
                            if ($format[$k]=='timestamp') {
                              ?><input
                                   id="<?php echo $id; ?>"
                                   onkeyup="editor_key(event);"
                                   onblur="update_records(this);"
                                   name="<?php echo $fields_name[$k],'|',$format[$k]; ?>"
                                   style="
                                        margin: 0px;
                                        padding: 0px;
                                        font-size:10px;
                                        width:<?php echo $width[$k]; ?>px;
                                        text-align:<?php echo $align[$k]; ?>;" 
                                   maxlength="<?php echo $length[$k]; ?>" 
                                   class="editor" 
                                   autocomplete="off"
                                   readonly
                                   type="text" 
                                   <?php echo $readonly[$k]; ?>
                                   value="<?php echo htmlfix($v); ?>"
                                   oldvalue="<?php echo htmlfix($v); ?>"    
                               /><?php 
                            } else {
                                if($format[$k]=='tinyint(4)') {
                                    ?><input 
                                       id="<?php echo $id; ?>"
                                       onkeyup="editor_key(event);"
                                       onchange="update_records(this);"
                                       name="<?php echo $fields_name[$k],'|',$format[$k]; ?>"
                                       style="margin: 0px;padding: 0px;" 
                                       class="editor" 
                                       type="checkbox" 
                                       <?php echo $readonly[$k]; ?>
                                       <?php 
                                            if ($v=='1') {
                                                echo 'checked';
                                            } else {
                                                echo '';
                                            }; 
                                       ?>
                                       oldvalue="<?php 
                                            if ($v=='1') {
                                                echo 'checked';
                                            } else {
                                                echo '';
                                            }; 
                                       ?>"    
                                   /><?php   
                                } else {
                                    ?><input 
                                           id="<?php echo $id; ?>"
                                           onkeyup="editor_key(event);"
                                           onblur="update_records(this);"
                                           name="<?php echo $fields_name[$k],'|',$format[$k]; ?>"
                                           style="
                                                margin: 0px;
                                                padding: 0px;
                                                font-size:10px;
                                                width:<?php echo $width[$k]; ?>px;
                                                text-align:<?php echo $align[$k]; ?>;" 
                                           maxlength="<?php echo $length[$k]; ?>" 
                                           class="editor" 
                                           autocomplete="off"
                                           type="text" 
                                           <?php echo $readonly[$k]; ?>
                                           value="<?php echo htmlfix($v); ?>"
                                           oldvalue="<?php echo htmlfix($v); ?>"    
                                       /><?php                                   
                                }                                 
                            }
                        };
                       ?></td><?php
                   $id++;
                };
                echo '</tr>';
            }
        ?>
    </table>
    <a onclick="set_wait();" class='btn btn-primary btn-small' href="<?php $_GET['limit']=0;echo get_to_string(); ?>">|<</a>
    <a onclick="set_wait();" class='btn btn-primary btn-small' href="<?php
        if (($limit-$rows_on_page)>0) {
            $_GET['limit']=$limit-$rows_on_page; 
        } else {
            $_GET['limit']=0; 
        }
        echo get_to_string(); 
    ?>"><<</a>
    <a onclick="set_wait();" class='btn btn-primary btn-small' href="<?php $_GET['limit']=$limit;echo get_to_string(); ?>"><?php 
            $t = (($limit+$rows_on_page)<=$count[0])?$limit+$rows_on_page:$count[0];
            echo ($limit+1),'-',$t,'/',$count[0];
        ?></a>
    <a onclick="set_wait();" class='btn btn-primary btn-small' href="<?php 
        if (( $limit + $rows_on_page ) < $count[0]) {
            $_GET['limit']= $limit + $rows_on_page;
        } else {
            $_GET['limit']= 0;
        }
        echo get_to_string(); 
    ?>">>></a>
    <a onclick="set_wait();" class='btn btn-primary btn-small' href="<?php 
        if (($count[0]-$rows_on_page)>0) {
            $_GET['limit']= $count[0]-$rows_on_page;
        } else {
            $_GET['limit']= $limit;
        }
        echo get_to_string(); 
    ?>">>|</a>
    <!---
        <a class='btn btn-small btn-primary'>Delete selected</a>
    -->
    <br/>
    <br/>
    <form 
          id="newline" 
          style="padding:0px;margin:0px;"
          action="<?php 
            $_GET['order']=$key_name;
            $_GET['sort']='DESC';
            unset($_GET['limit']);
            echo get_to_string(); 
            ?>" method="POST">
        <input type="hidden" name="action" value="add_new"/>
        <input type="hidden" name="last_index" value="<?php echo $last_index; ?>">
        <input type="hidden" name="key" value="<?php echo $key_name; ?>">
        <input type="hidden" name="table" value="<?php echo addslashes($_GET['name']); ?>">
        <b>New record template:</b>
        <table id="newline" name="<?php echo $_GET['name']; ?>">
            <tr>
                <!---<th style="padding:5px; font-size:11px; border: 2px solid black;">*</th>-->
                <?php 

                    // рисуем заголовок таблицы
                    foreach ($fields as $key => $value) {
                        echo '<th style="padding:3px; font-size:11px; border: 2px solid black;">
                                    <font style="color:black;">',$value[0],'</font>
                             </th>';
                    }
                ?>
            </tr>
            <tr>
                <!--<th style="padding:0px; font-size:11px; border: 2px solid black;">*</th>-->
                <?php 

                    foreach ($default as $k=>$v) {
                        ?><td align="center"><?php

                            // default is no referenced field
                            $ref_table='';
                            $ref_field='';

                            // check if field has refereced link ===================
                            foreach ($table_keys as $kk=>$vv) {
                                if ($vv[1]==$fields_name[$k]) {
                                    $ref_table = $vv[2];
                                    $ref_field = $vv[3];
                                };
                            }

                            if ($ref_table!='' AND $ref_field!='') {
                                //prepare list
                                $sql = '
                                    SELECT 
                                        * 
                                    FROM
                                        '.$ref_table.'
                                    ORDER BY '.$ref_field.' ASC;';
                                $row = get_array_from_sql($sql);
                                $options='';
                                foreach ($row as $kk=>$vv) {
                                    if ($v==$vv[0]) {
                                        $options .= '<option selected value="'.$vv[0].'">';
                                    } else {
                                        $options .= '<option value="'.$vv[0].'">';
                                    }
                                    $options .= $vv[1];
                                    $options .= '</option>';

                                };
                                ?><select 
                                        id="<?php echo $id; ?>"
                                        name="data-<?php echo $fields_name[$k],'|',$format[$k]; ?>"
                                        style="
                                            height: 20px;
                                            margin: 0px;
                                            padding: 0px;
                                            font-size:10px;
                                            text-align:left;"
                                        class="newline" 
                                        ><?php echo $options;?></select>
                                <?php
                            } else {
                                if($format[$k]=='timestamp') {
                                      ?><input 
                                               id="<?php echo $id; ?>"
                                               name="data-<?php echo $fields_name[$k],'|',$format[$k]; ?>"
                                               style="
                                                    margin: 0px;
                                                    padding: 0px;
                                                    font-size:10px;
                                                    width:<?php echo $width[$k]; ?>px;
                                                    text-align:<?php echo $align[$k]; ?>;" 
                                               maxlength="<?php echo $length[$k]; ?>" 
                                               class="newline" 
                                               readonly
                                               type="text" 
                                               autocomplete="off"
                                               <?php echo $readonly[$k]; ?>
                                               value="<?php echo htmlfix($v); ?>"
                                           /><?php                                        
                                } else {
                                    if($format[$k]=='tinyint(4)') {
                                        ?><input 
                                           id="<?php echo $id; ?>"
                                           name="data-<?php echo $fields_name[$k],'|',$format[$k]; ?>"
                                           style="margin: 0px;padding: 0px;" 
                                           class="editor" 
                                           type="checkbox" 
                                           <?php echo $readonly[$k]; ?>
                                           <?php 
                                                if ($v=='1') {
                                                    echo 'checked';
                                                } else {
                                                    echo '';
                                                }; 
                                           ?>
                                       /><?php   
                                    } else {
                                        ?><input 
                                               id="<?php echo $id; ?>"
                                               name="data-<?php echo $fields_name[$k],'|',$format[$k]; ?>"
                                               style="
                                                    margin: 0px;
                                                    padding: 0px;
                                                    font-size:10px;
                                                    width:<?php echo $width[$k]; ?>px;
                                                    text-align:<?php echo $align[$k]; ?>;" 
                                               maxlength="<?php echo $length[$k]; ?>" 
                                               class="newline" 
                                               type="text" 
                                               autocomplete="off"
                                               <?php echo $readonly[$k]; ?>
                                               value="<?php echo htmlfix($v); ?>"
                                           /><?php    
                                    };  
                                };
                            };
                           ?></td><?php
                    };
                ?>
            </tr>
        </table>
    </form>
    <input 
        onclick="
            set_wait();
            this.value='Record is adding...';
            $('form#newline').submit();"
        style="margin-top: 5px;" 
        class="btn btn-primary btn-small"
        type="button"
        value="Add this record"
    />
    <?php 
        if(isset($_POST['action']) AND $_POST['action']=='add_new') {
            if (isset($data['error'])) {
                ?>
                    <script>
                         var newline=$('tr#' + '<?php echo $last_index; ?>').find('.editor');
                         $(newline).css('color','red');
                         $(newline).css('background-color','#FFDDDD');
                    </script>
                <?php
            } else {
                ?>
                    <script>
                         var newline=$('tr#' + '<?php echo $last_index; ?>').find('.editor');
                         $(newline).css('color','green');
                         $(newline).css('background-color','#DDFFDD');
                    </script>
                <?php                
            }
        };
        
    ?>
    <!---
    <form action="<?php 
            $_GET['order']=$key_name;
            $_GET['sort']='DESC';
            unset($_GET['limit']);
            echo get_to_string(); 
        ?>" method="POST">
        
    </form>
    -->
</div>