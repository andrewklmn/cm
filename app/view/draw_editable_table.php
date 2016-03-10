<?php

/*
 * Draws Editable Table
 * 
 * Example:
        $sql = '
            SELECT
                `Scenario`.`ScenarioId`,
                `Scenario`.`BlindReconciliation`,
                `Scenario`.`UsePackIntegrity`,
                `Scenario`.`UseSealNumber`,
                `Scenario`.`UseBagNumber`,
                `Scenario`.`SingleDenomDeposits`,
                `Scenario`.`ReconcileAgainstValue`,
                `Scenario`.`DefaultScenario`,
                `Scenario`.`LogicallyDeleted`
            FROM 
                `cashmaster`.`Scenario`
            WHERE
            `Scenario`.`ScenarioId`="'.  addslashes($_SESSION[$program]['scenario'][0]).'"
        ;';
        $table['fields'] = fetch_fields_info_from_sql($sql);
        $table['headers'] = $table['fields'];
        //$table['widths'] = array( 40,600 );
        $table['data'] = get_assoc_array_from_sql($sql);
        $table['form_action'] = 'scenario_update';
        $table['title'] = 'Scenario options';
        $table['hide_key'] = true;
        include './app/view/draw_editable_table.php';
 */
        if (!isset($c)) exit;
?><style>
        table.editable_table th {
            padding:3px; 
            font-size:11px; 
            border: 2px solid black;
        }
        table.editable_table td {
            padding:3px; 
            font-size:11px; 
            border: 0px solid gray;
        }
        table.editable_table th {
            background-color: lightgray;
        }
    </style>
    <script>
            function <?php echo $table['form_action']; ?>_editor_key(event){
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
                        while (true) {
                            var next_input = $('table#<?php echo $table['form_action']; ?>').find('input#' + next_id);
                            if (next_input.length>0 ) {
                                /*
                                if ($(next_input.parentNode).is(':hidden')) {
                                    next_id++;
                                    continue;
                                };
                                */
                                $(next_input).focus();
                                $(next_input).select();
                                return true;
                            };
                            
                            var next_input = $('table#<?php echo $table['form_action']; ?>').find('select#' + next_id);
                            if (next_input.length>0 && $(next_input.parentNode).css('display')!='none') {
                                /*
                                if ($(next_input.parentNode).is(':hidden')) {
                                    next_id++;
                                    continue;
                                };
                                */
                                $(next_input).focus()
                                return true;
                            };
                            next_id=0;
                        };
                    break;
                    default:
                }
            }
            function <?php echo $table['form_action']; ?>(elem) {
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
                var inputs = $(elem.parentNode.parentNode).find('.editor');
                var fields = new Array();
                var values = new Array();
                var oldvalues = new Array();
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
                };
                $.ajax({
                    type: "POST",
                    url: "?c=<?php echo $table['form_action']; ?>",
                    async: false,
                    data: {
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
                                            $(inputs[i]).prop('checked', false);
                                        };
                                        $(inputs[i].parentNode).css('background-color','yellow');
                                    } else {
                                        inputs[i].value = newvalues[i];  
                                        $(inputs[i]).css('background-color','yellow');
                                    };
                                    $(inputs[i]).attr('oldvalue',newvalues[i]);
                                    $(inputs[i]).css('color','red');
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
                                        $(inputs[i].parentNode).css('background-color','yellow');
                                    } else {
                                        inputs[i].value = newvalues[i];  
                                        $(inputs[i]).css('background-color','yellow');
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
                                            $(inputs[i]).prop('checked', true );
                                        } else {
                                            $(inputs[i]).prop('checked', false);
                                        };
                                        $(inputs[i].parentNode).css('background-color','yellow');
                                    } else {
                                        inputs[i].value = newvalues[i];  
                                        $(inputs[i]).css('background-color','yellow');
                                    };
                                    $(inputs[i]).attr('oldvalue',newvalues[i]);
                                    $(inputs[i]).css('color','red');
                                };
                        };
                        remove_wait();
                    },
                    dataType: "html"
                });
            }
            </script>
        <?php
        if(isset($table['title'])) echo '<h5 style="padding:0px;margin:0px;">',htmlfix($table['title']),'</h5>';
        echo '<table class="editable_table" id="',$table['form_action'],'">';
        echo '<tr>';
        $i=0;
        foreach ($table['headers'] as $key => $value) {
            if ($table['fields'][$i]->flags==49667 
                    AND isset($table['hide_key']) 
                    AND $table['hide_key']==true) {
                echo '<th style="display:none;">',htmlfix($value->name),'</th>';
            } else {
                echo '<th>',htmlfix($value->name),'</th>';
            } 
            $i++;
        };
        echo '</tr>';
        $id=0;
        foreach ($table['data'] as $key => $value) {
            echo '<tr>';
            $i=0;
            foreach ($value as $k => $v) {
                $readonly = ($table['fields'][$i]->flags==49667)?' readonly ':''; 
                $display = ($table['fields'][$i]->flags==49667
                                AND isset($table['hide_key']) 
                                AND $table['hide_key']==true)?'display:none;':''; 
                switch ($table['fields'][$i]->type) {
                    case 1:
                        echo '<td align="center" style="',$display,'vertical-align:top;"><input 
                                        onclick="',$table['form_action'],'(this);"
                                        onkeyup="',$table['form_action'],'_editor_key(event);"
                                        class="editor"
                                        name="',$k,'"
                                        id="',$id,'"
                                        oldvalue = "',($v==1)?'checked':'','"
                                        type="checkbox" 
                                        ',$readonly,'
                                        maxlength="',$table['fields'][$i]->max_length,'" 
                                        ',($v==1)?'checked="checked"':'','/></td>';
                        break;
                    case 8:
                        // BIGINT
                        echo '<td align="center" style="',$display,'"><input 
                                    onblur="',$table['form_action'],'(this);"
                                    onkeyup="',$table['form_action'],'_editor_key(event);"
                                        class="editor"
                                    name="',$k,'"
                                    id="',$id,'"
                                    oldvalue = "',$v,'"
                                    type="text"
                                    ',$readonly,'
                                    style="width:70px;text-align:center;"
                                    maxlength="',$table['fields'][$i]->max_length,'" 
                                    value="',htmlfix($v),'"/>
                            </td>';
                        break;
                    case 253:
                        // STRING
                        if(!isset($table['widths'][$i])) {
                            $width = $table['fields'][$i]->max_length*7;
                            if ($width<100) $width=100;
                            if ($width>450) $width=450;
                        } else {
                            $width = $table['widths'][$i];
                        };
                        echo '<td align="center" style="',$display,'border-width:0px;"><input 
                                    onblur="',$table['form_action'],'(this);"
                                    onkeyup="',$table['form_action'],'_editor_key(event);"
                                    class="editor"
                                    name="',$k,'"
                                    id="',$id,'"
                                    oldvalue = "',$v,'"
                                    type="text"
                                    ',$readonly,'
                                    style="width:',$width,'px;"
                                    maxlength="',$table['fields'][$i]->max_length,'" 
                                    value="',htmlfix($v),'"/>
                            </td>';
                        break;
                    default:
                        echo '<td style="',$display,'border-width:0px;"><input 
                                        onblur="',$table['form_action'],'(this);"
                                        onkeyup="',$table['form_action'],'_editor_key(event);"
                                        class="editor"
                                        name="',$k,'"
                                        id="',$id,'"
                                        oldvalue = "',$v,'"
                                        type="text" 
                                        ',$readonly,'
                                        maxlength="',$table['fields'][$i]->max_length,'" 
                                        value="',htmlfix($v),'"/></td>';
                }
                $i++;
                $id++;
            };
            echo '</tr>';
        }
        echo '</table>';
        unset($table);
?>
