<?php

    /**
     * Вывод в виде HTML таблицы 
     * 
     * @param array $table header, data, footer, align, fontsize, width, id, count
     * 
     * @return ничего не возвращает, кроме вывода  таблицы
     */    

    function draw_simple_table($table) {
        
        $noscript=false;
        if (!isset ($table['data'])) {
            if (!isset($table)) {
                $table['data']=array(
                    array(11,12,13,14),
                    array(21,22,23,24)
                );                
            }
        } else {
            if (count($table['data'])==0 AND isset($table['header'])) {
                $table['data'][0]=array();
                foreach ($table['header'] as $key=>$value) {
                    $table['data'][0][$key]='-';
                }
                $noscript=true;
            }
        }

        
        $fontsize = (isset($table['fontsize'])) ? $table['fontsize'] : '10px';
        $width = (isset($table['width'])) ? $table['width'] : array();
        $table['id'] = (isset($table['id'])) ? $table['id'] : 'table';
        $fontfamily = (isset($table['fontfamily'])) ? $table['fontfamily']:'sans-serif';
        
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')>0){
            $ie = true;
        } else {
            $ie = false;
        };
            
        ?>
            <style>
                table<?php echo '#'.$table['id']; ?> th {
                    background-color: lightgray;
                    font-family: <?php echo $fontfamily; ?>;
                    border-style: solid;
                    border-width:2px;
                    padding: 2px;
                }
                table<?php echo '#'.$table['id']; ?> td {
                    font-family: <?php echo $fontfamily; ?>;
                    padding: 2px;
                }
                tr.selected {
                    background-color: lightblue;
                }
                tr.selected:hover,tr.selectedhover {
                    background-color: dodgerblue;
                }               
                tr.table_data {
                    background-color: white;
                }
                tr.table_data:hover,tr.table_datahover {
                    background-color: yellow;
                }
          </style>
          <?php
                if ($noscript!=true) {
                    ?>
                        <script>
                            function tr_click_<?php echo $table['id']; ?>(tr) {
                                <?php
                                    if ($ie) {
                                        ?>
                                            if (tr.className=="table_datahover") {
                                                tr.className="selectedhover";
                                            } else {
                                                tr.className="table_datahover";
                                            }
                                        <?php
                                    } else {
                                        ?>
                                            if (tr.className=="table_data") {
                                                tr.className="selected";
                                            } else {
                                                tr.className="table_data";
                                            }
                                        <?php
                                    }
                                ?>
                            }
                            function get_selected_tr_<?php echo $table['id']; ?>() {
                                var table=document.getElementById('<?php echo $table['id']; ?>');
                                var trs=table.getElementsByTagName('TR');
                                var selected=new Array();
                                var k=0;
                                for (i = 0; i < trs.length; i++) {
                                    if(trs[i].className=='selected'){
                                        selected[k]=trs[i];
                                        k++;
                                    };
                                }
                                return selected;
                            }
                            function deselect_tr_<?php echo $table['id']; ?>() {
                                var trs=get_selected_tr_<?php echo $table['id']; ?>();
                                for (i = 0; i < trs.length; i++) {
                                    if(trs[i].className=='selected'){
                                        trs[i].className='table_data';
                                    };
                                }
                            }
                            function get_all_tr_<?php echo $table['id']; ?>() {
                                var table=document.getElementById('<?php echo $table['id']; ?>');
                                var trs=table.getElementsByTagName('TR');
                                var tr_data=new Array();
                                var k=0;
                                for (i = 1; i < (trs.length-1); i++) {
                                    if(trs[i].className=='table_data' || trs[i].className=='selected'){
                                        tr_data[k]=trs[i];
                                        k++;
                                    };
                                }
                                return tr_data;
                            }
                            function get_footer_<?php echo $table['id']; ?>() {
                                var table=document.getElementById('<?php echo $table['id']; ?>');
                                var trs=table.getElementsByTagName('TR');
                                for (i = 1; i < (trs.length-1); i++) {
                                    if(trs[i].id=="footer"){
                                        footer=trs[i];
                                        break;
                                    };
                                }       
                                return footer;
                            }
                            function hide_tr_table(elem){
                                elem.setAttribute('hidden',true);
                                elem.style.visibility='hidden';
                                elem.style.display='none';
                            }
                            function show_tr_table(elem){
                                elem.setAttribute('hidden',false);
                                elem.style.visibility='visible';
                                if (navigator.appName == 'Microsoft Internet Explorer')
                                {
                                    elem.style.display='block';
                                } else {
                                    elem.style.display='table-row';
                                };
                            }
                        </script>                        
                    <?php
                }
        
        echo '<table id="'.$table['id'].'" border="1" style="border-collapse:collapse;">';
        /*
        echo '<colgroup>';
        if (isset($table['count']) AND $table['count']==true) {
            echo '<col width="30px"/>';
        }        
        foreach ($width as $key=>$value) {
            echo '<col width="',$value,'px"/>';
        }
        echo '</colgroup>';
         * 
         */
        if (isset($table['header'])) {
            echo '<tr id="header">';
            if (isset($table['count']) AND $table['count']==true) {
                echo '<th style="font-size:'.$fontsize.';width:30px;">п/п</th>';
            }
            foreach ($table['header'] as $key=>$value) {
                if (isset($table['width'][$key])) {
                    echo '<th style="width:'.$table['width'][$key].'px;font-size:'.$fontsize.';">';
                } else {
                    echo '<th style="font-size:'.$fontsize.';">';
                }
                echo htmlfix($value);
                echo '</th>';
            }
            echo '</tr>';
        }
        $i=1;
        if (isset($table['data'])) {
            foreach ($table['data'] as $key=>$value) {
                echo '<tr 
                    class="table_data" 
                    onclick="tr_click_'.$table['id'].'(this);" ';
                if ($ie) {
                    echo '
                        onmouseover=" this.className = this.className + \'hover\'; "
                        onmouseout=" this.className = this.className.replace(\'hover\',\'\'); "
                    ';
                };    
                echo '>';
                if (isset($table['count']) AND $table['count']==true) {
                    echo '<td align="center" style="width:30px;cursor:pointer;font-size:'.$fontsize.';">'.$i.'</td>';
                    $i++;
                }
                $ii=0;
                foreach ($value as $k=>$v) {
                    $align = (isset($table['align'][$ii])) ? $table['align'][$ii]:'center';
                    if (isset($table['width'][$ii])) {
                        echo '<td style="text-align:'.$align.';width:'.$table['width'][$ii]
                                .'px;font-size:'.$fontsize.';">';
                    } else {
                        echo '<td style="text-align:'.$align.';font-size:'.$fontsize.';">';
                    }
                    echo htmlfix($v, ENT_HTML5, 'cp1251');
                    echo '</td>';
                    $ii++;
                }        
                echo '</tr>';
            }
        }
        if (isset($table['footer'])) {
            echo '<tr id="footer">';  
            if (isset($table['count']) AND $table['count']==true) {
                echo '<th align="center" style="width:30px;"></th>';
            }        
            foreach ($table['footer'] as $key=>$value) {
                $align = (isset($table['align'][$key])) ? $table['align'][$key]:'center';
                if (isset($table['width'][$key])) {
                    echo '<th style="text-align:'.$align.';width:'.$table['width'][$key].'px;font-size:'.$fontsize.';">';
                } else {
                    echo '<th style="text-align:'.$align.';font-size:'.$fontsize.';">';
                }
                echo htmlfix($value);
                echo '</th>';
            }
            echo '</tr>';        
        }
        echo '</table>';
    }
?>