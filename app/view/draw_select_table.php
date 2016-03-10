<?php

/*
 * Draw Selectable table
 * 
 * Example:
 * 
                $table['id'] = 'example'; 
                $sql = '
                    SELECT
                        `ScenDenoms`.`Id`,
                        Denoms.Value,
                        Currency.CurrName,
                        Currency.CurrYear
                    FROM 
                        `cashmaster`.`ScenDenoms`
                    LEFT JOIN
                        Denoms ON Denoms.DenomId = ScenDenoms.DenomId
                    LEFT JOIN
                        Currency ON Denoms.CurrencyId = Currency.CurrencyId
                    WHERE
                        `ScenDenoms`.`ScenarioId`="'.  addslashes($_SESSION[$program]['scenario'][0]).'"
                ;';
                $table['data'] = get_array_from_sql($sql);
                $table['header'] = explode('|', 'Id|Denom|Name|Year');
                $table['width'] = array( 40,50,150,50);
                $table['align'] = array( 'center','right','center','center');
                $table['th_onclick']=array( ';',';',';',';');
                $table['tr_onclick']=';';       // It will bind to each data TD 
                $table['title'] = 'Denoms';
                $table['selector'] = true;
                include './app/view/draw_select_table.php';
                draw_select_table($table);
 */
        
    function draw_select_table($table) {
        $msie9 = preg_match('/(?i)msie [8-9]/',$_SERVER['HTTP_USER_AGENT']);
        ?>
            <style>
                table.info_table th {
                    padding:1px; 
                    font-size:11px; 
                    border-bottom: 2px solid black;
                }
                table.info_table td {
                    padding:3px; 
                    font-size:11px; 
                    border-bottom: 1px solid gray;
                }
                table.info_table th {
                    background-color: lightgray;
                }
                table.info_table tr.not_selected {
                    background-color: white;
                    cursor: pointer;
                }
                table.info_table tr.selected {
                    background-color: #FFFFBB;
                    cursor: pointer;
                }
                table.info_table tr.not_selected:hover {
                    background-color: lightblue;
                    cursor: pointer;
                }
                table.info_table tr.selected:hover {
                    background-color: lightgreen;
                    cursor: pointer;
                }
                input.index {
                    vertical-align: middle;
                    margin-left: 2px;
                    margin-right: 2px;
                    margin-top: 0px;
                    margin-bottom: 0px;
                    padding: 0px;
                }
                table.info_table th.index {
                    border-bottom: 1px solid gray;
                    width: 26px;
                }
            </style> 
            <script>
                function index_click(elem) {
                    if($(elem).prop('checked')==true){
                        elem.parentNode.parentNode.className = 'selected';
                    } else {
                        elem.parentNode.parentNode.className = 'not_selected';
                    };
                };
            </script>
        <?php
        
        if(isset($table['fader']) AND $table['fader']==1) {
            if(isset($table['title']) AND $table['title']!='') echo '<h4>',$table['title'],'</h4>';
            echo '<table class="info_table" id="',(isset($table['id']))?$table['id']:'table','">';
            echo '<tr>';
            
            if (isset($table['selector']) AND $table['selector']==true) {
                if($msie9) {
                    echo '<th></th>';
                } else {
                    echo '<th></th>';
                };
            };
            
            foreach ($table['header'] as $key=>$value) {
                //if((isset($table['hide_id']) 
                //        AND $table['hide_id']==1
                //        AND $key==0)) {
                //} else {
                    if (isset($table['th_onclick'][$key])) {
                        echo '<th onclick="'.$table['th_onclick'][$key].'" style="width:',$table['width'][$key],'px;">',$value,'</th>';
                    } else {
                        echo '<th style="width:',$table['width'][$key],'px;">',$value,'</th>';
                    };
                //};
            };
            echo '</tr>';
            foreach ($table['data'] as $key => $value) {
                if((isset($table['hide_id']) AND $table['hide_id']==1)) {
                    echo '<tr class="not_selected" id="'.$value[0].'">';
                    //if (isset($table['selector']) AND $table['selector']==true) echo '<th>☐</th>';
                    if (isset($table['selector']) AND $table['selector']==true) {
                        echo '<th class="index"><input onclick="index_click(this);" type="checkbox" class="index"/></th>';
                    };
                    foreach ($value as $k=>$v) {
                        if ($k!=0) {
                            echo '<td onclick="'.$table['tr_onclick'].'" style="border-color:lightgray;color:gray;" align="',$table['align'][$k],'">',$v,'</td>';
                        };
                    };
                    echo '</tr>';                    
                } else {
                    echo '<tr class="not_selected">';
                    if (isset($table['selector']) AND $table['selector']==true) {
                        /*
                        if($msie9) {
                            echo '<th>[ ]</th>';
                        } else {
                            echo '<th>☐</th>';
                        };
                         */
                        echo '<th class="index"><input onclick="index_click(this);" type="checkbox" class="index"/></th>';
                    };
                    foreach ($value as $k=>$v) {
                        echo '<td onclick="'.$table['tr_onclick'].'" style="border-color:lightgray;color:gray;" align="',$table['align'][$k],'">',$v,'</td>';
                    };
                    echo '</tr>';
                }
            };
            echo '</table>';            
        } else {
            if(isset($table['title']) AND $table['title']!='') echo '<h4>',$table['title'],'</h4>';
            echo '<table class="info_table" id="',(isset($table['id']))?$table['id']:'table','">';
            echo '<tr>';
            if (isset($table['selector']) AND $table['selector']==true) echo '<th></th>';
            foreach ($table['header'] as $key=>$value) {
                //if((isset($table['hide_id']) 
                //        AND $table['hide_id']==1
                //        AND $key==0)) {
                //} else {
                    if (isset($table['th_onclick'][$key])) {
                        echo '<th onclick="'.$table['th_onclick'][$key].'" style="width:',$table['width'][$key],'px;">',$value,'</th>';
                    } else {
                        echo '<th style="width:',$table['width'][$key],'px;">',$value,'</th>';
                    };
                //};
            };
            echo '</tr>';
            foreach ($table['data'] as $key => $value) {
                if((isset($table['hide_id']) AND $table['hide_id']==1)) {
                    echo '<tr class="not_selected" id="'.$value[0].'">';
                    if (isset($table['selector']) AND $table['selector']==true) {
                        /*
                        if($msie9) {
                            echo '<th>[ ]</th>';
                        } else {
                            echo '<th>☐</th>';
                        };
                         */
                        echo '<th class="index"><input onclick="index_click(this);" type="checkbox" class="index"/></th>';
                    };
                    foreach ($value as $k=>$v) {
                        if ($k!=0) {
                            echo '<td onclick="'.$table['tr_onclick'].'" style="border-color:gray;" align="',$table['align'][$k],'">',$v,'</td>';
                        };
                    };
                    echo '</tr>';                    
                } else {
                    echo '<tr class="not_selected">';
                    if (isset($table['selector']) AND $table['selector']==true) {
                        /*
                        if($msie9) {
                            echo '<th>[ ]</th>';
                        } else {
                            echo '<th>☐</th>';
                        };
                         */
                        echo '<th class="index"><input onclick="index_click(this);" type="checkbox" class="index"/></th>';
                    };
                    foreach ($value as $k=>$v) {
                        echo '<td onclick="'.$table['tr_onclick'].'" style="border-color:gray;" align="',$table['align'][$k],'">',$v,'</td>';
                    };
                    echo '</tr>';
                };
            };
            echo '</table>';
        };      
    };
?>
