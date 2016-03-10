<?php

    /**
     * Вывод в виде HTML таблицы заданной высоты со скроллингом (или без для мобилок) 
     * 
     * @param array $table header, data, footer, align, fontsize, width, id, 
     * count, height
     * 
     * @return ничего не возвращает, кроме вывода  таблицы
     */    

/*  ====================================== Использование ======================
 *  
    $sql = "SELECT * FROM clients WHERE true is true ORDER BY id DESC"
    $table['data'] = get_array_from_sql($sql);
    
    $table['name'] = 'clients';   
    $table['header'] = 'Код|Имя|Координаты|е-мейл|Кат.';
    $table['width'] = '50|200|350|150|50';
    $table['align'] = 'center|left|left|left|center';
    $table['class'] = 'blue||||money';
    $table['onclick'] = '||||';
    $table['onmouseover'] = '||||';
    $table['onmouseout'] = '||||';
    $table['style']='.money { color:darkred;font-weight: bold;} .blue{color:darkblue;}  .blue:hover{color:darkblue;font-weight:bold;cursor:pointer;}';
    $table['css']='table_clients.css';
    $table['script']='';
    $table['js']='table_clients.js';    
    $table['footer'] = '|Найдено '.$itogo.' зап.|||';
    $table['after_load'] = 'alert("hello world");';

    include_once '../common/app/view/draw_table.php';
    draw_table($table);
 */
    
     include_once 'app/model/is_mobile.php';
     
     
     function make_param_array( &$table, $param, $default) {
         if (!isset($table[$param])) $table[$param] = array();
         if(count($table['data']) > 0) {
            if( count($table['data'][0]) > 0) {
                foreach ($table['data'][0] as $value) {
                    if ( $default=='' ) {
                        $table[$param][] = count($table[$param]);
                    } else {
                        $table[$param][] = $default;
                    }
                }
            } else {
                //echo '<br/>There is no table data...<br/>';
                return true;                    
            }
        } else {
            //echo '<br/>There is no table data...<br/>';
            return true;
        }
     }
     
     
     function draw_table($table) {
         
        if (!isset($table['data'])) {
            echo 'There is no table data...';
            exit;
        }
         
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')>0){
            $ie = true;
        } else {
            $ie = false;
        };
        
        $header = (isset($table['header'])) ? true : false;
        $data = (isset($table['data'])) ? true : false; 
        $footer = (isset($table['footer'])) ? true : false; 
        
       // преобразование параметров на случай задания в старом виде
        
       if (isset($table['header'])) {
            $table['header'] = normalize_string_param($table['header']);
       } else {
           make_param_array($table,'header','');
       };
       
       if (isset($table['width'])) {
            $table['width'] = normalize_string_param($table['width']);
       } else {
           make_param_array($table,'width','100');
       };
       
       if (isset($table['align'])) {
            $table['align'] = normalize_string_param($table['align']);
       } else {
           make_param_array($table,'align','center');
       };
       
       if (isset($table['class'])) {
            $table['class'] = normalize_string_param($table['class']);
       } else {
           make_param_array($table,'class','data');
       };

       if (isset($table['onclick'])) {
            $table['onclick'] = normalize_string_param($table['onclick']);
       } else {
           make_param_array($table,'onclick',';');
       };
       if (isset($table['onmouseover'])) {
            $table['onmouseover'] = normalize_string_param($table['onmouseover']);
       } else {
           make_param_array($table,'onmouseover',';');
       };
       if (isset($table['onmouseout'])) {
            $table['onmouseout'] = normalize_string_param($table['onmouseout']);
       } else {
           make_param_array($table,'onmouseout',';');
       };
       if (isset($table['footer'])) {
            $table['footer'] = normalize_string_param($table['footer']);
       } else {
           make_param_array($table,'footer','');
       };
        
        $table_width = 0;
        foreach ($table['width'] as $value) {
            $table_width+=$value;
        }
        
        $table['row_height'] = (isset($table['row_height'])) ? $table['row_height'] : 16;
        $header_footer_width = $table['row_height'] + $table_width;
        $table['height'] = (isset($table['height'])) ? $table['height'] : 200;
        $table['name'] = (isset($table['name'])) ? $table['name'] : 'table';
        $table['id'] = (isset($table['id'])) ? $table['id'] : $table['name'];
        $table['fontsize'] = (isset($table['fontsize'])) ? $table['fontsize'] : 10;
        $table['font'] = (isset($table['font'])) ? $table['font'] : 'sans-serif';
        
        
        if ( file_exists('css/table_'.$table['id'].'.css')) {
            ?>
                <link type="text/css" rel="stylesheet" href="css/table_<?php echo $table['id']; ?>.css"/>
            <?php
        }
        ?>
            <style>
                @media screen {
                    <?php if (isset($table['style']) 
                            AND $table['style']!='') { echo $table['style']; } ?>
                    table.header {
                        font-size: <?php echo $table['fontsize']; ?>px;
                        font-family: <?php echo $table['font']; ?>;
                        margin: 0px;
                        padding: 0px;
                        height: <?php echo $table['row_height']; ?>px;
                        background-color: #DEDEDE;
                        border-collapse: collapse;
                        cellspacing: 0px;
                        cellpading: 0px;                        
                    }
                    table.footer th, table.header th {
                        border-style: solid;
                        border-width: 1px;
                        border-color: gray;
                        color:gray;
                        background-color: #DEDEDE;
                        border-collapse: collapse;
                    }
                    table.data {
                        font-size: <?php echo $table['fontsize']; ?>px;
                        font-family: <?php echo $table['font']; ?>;                        
                        margin: 0px;
                        padding: 0px;
                        border-collapse: collapse;
                        cellspacing: 0px;
                        cellpading: 0px;        
                        word-wrap:break-word;
                        word-break:break-all;
                    }
                    table.data td, table.data th {
                        padding: 0px;
                        padding-left: 1px;
                        padding-right: 1px;
                        border-width: 1px;
                        border-style: solid;
                        border-color: gray;
                        word-break: break-all;
                        border-collapse: collapse;
                    }
                    table.footer {
                        font-size: <?php echo $table['fontsize']; ?>px;
                        font-family: <?php echo $table['font']; ?>;                        
                        margin: 0px;
                        padding: 0px;
                        height: <?php echo $table['row_height']; ?>px;                    
                        background-color: #DEDEDE;
                        border-collapse: collapse;
                        cellspacing: 0px;
                        cellpading: 0px;                        
                    }
                    input.s {
                        margin: 0px;
                        cursor: pointer;
                    }
                    tr.data:hover {
                        background-color: yellow;
                    }
                    tr.selected {
                        background-color: lightblue;
                    }
                    tr.selected:hover {
                        background-color: lightseagreen;
                    }
                    
                    th.control {
                        color: gray;
                        background-color: #DEDEDE;
                        cursor:pointer;
                        border-collapse: collapse;
                    }
                    th.control:hover {
                        color:white;
                        background-color: gray;
                    }
                    div.scroll_<?php echo $table['id'];?> {
                        overflow-y: scroll;
                        overflow-x: hidden;
                        height:<?php echo $table['height'];?>px;
                    }
                    th.s{
                        height: <?php echo $table['row_height']; ?>px;
                        cursor:pointer;
                        background-color: #DEDEDE;
                    }
                    th.s_selected{
                        height: <?php echo $table['row_height']; ?>px;
                        cursor:pointer;
                        font-weight: bold;
                        color: black;
                        background-color: none;
                    }                    
                    th.s:hover{
                        background-color: #f6a828;
                    }  
                    div.v {
                        position:absolute;
                        background-color: #EEEEFF;
                        width:100px;
                        border-color: #4444AA;
                        border-style:solid;
                        border-width: 1px;
                        -moz-box-shadow: 0 0 10px rgba(0,0,0,0.5); /* Для Firefox */
                        -webkit-box-shadow: 0 0 10px rgba(0,0,0,0.5); /* Для Safari и Chrome */
                        box-shadow: 0 0 10px rgba(0,0,0,0.5); /* Параметры тени */
                    }
                    div.v1, div.v2 {
                        font-size:10px;
                        color:#4444AA;
                        padding: 2px;
                        cursor: pointer;
                    }
                    
                    div.v1:hover, div.v2:hover {
                        background-color: gray;
                        color: white;
                    }
                    
                    div.v1 {
                        background-color: #DDDDEE;
                    }                  
                    div.back {
                        position:absolute;
                        top:0;
                        left:0;
                        height:100%;
                        width:100%;
                    }
                }
                @media print {
                    input.s, th.s, col.s, th.control, th.s_selected {
                        display:none;
                    }
                    table {
                        border-collapse: collapse;
                    }
                    td,th {
                        border-style:solid;
                        border-width: 1px;
                        border-color:black;
                        font-size:10px;
                        word-wrap:break-word;
                        word-break:break-all;
                    }
                    div.scroll_<?php echo $table['id'];?> {
                        
                    }
                }
            </style>
            <script>
                function v_<?php echo $table['id'];?>(obj) {
                        var elem = obj;
                        var curleft = curtop = 5;
                        if (obj.offsetParent) {
                                curleft += obj.offsetLeft
                                curtop += obj.offsetTop
                                while (obj = obj.offsetParent) {
                                        curleft += obj.offsetLeft
                                        curtop += obj.offsetTop
                                }
                        }
                        var back = document.createElement('div');
                        back.className = "back";
                        back.id = "back";
                        back.onclick = function () {
                            close_v_<?php echo $table['id'];?>();
                        }
                        document.body.appendChild(back);
                        
                        var div = document.createElement('div');
                        div.id = "v";
                        div.className = "v";
                        div.innerHTML = '<div class="v1" ' + 
                            'onclick="select_all_<?php echo $table['id'];?>();close_v_<?php echo $table['id'];?>();">' + 
                            '<?php echo $_SESSION[$program]['lang']['select_all'];?></div>' + 
                            '<div class="v2" ' + 
                            'onclick="invert_<?php echo $table['id'];?>();close_v_<?php echo $table['id'];?>();"' +
                            '><?php echo $_SESSION[$program]['lang']['invert_selection'];?></div>' + 
                            '<div class="v1" ' + 
                            'onclick="select_all_<?php echo $table['id'];?>();invert_<?php echo $table['id'];?>();close_v_<?php echo $table['id'];?>();"' +
                            '><?php echo $_SESSION[$program]['lang']['clear_selection'];?></div>';
                        document.body.appendChild(div);
                        var v = document.getElementById('v');
                        v.style.top = curtop + "px";
                        v.style.left = curleft + "px";
                        
                }
                function close_v_<?php echo $table['id'];?>(){
                    var back = document.getElementById('back');
                    var v = document.getElementById('v');
                    document.body.removeChild(back);
                    document.body.removeChild(v);
                }
                function s_<?php echo $table['id'];?>(elem){
                    if (elem.innerHTML == '') {
                        elem.parentNode.className += " selected";
                        elem.innerHTML = 'x';
                        elem.className = 's_selected';
                    } else {
                        elem.parentNode.className = 
                            elem.parentNode.className.replace(" selected","");
                        elem.innerHTML = '';
                        elem.className = 's';
                    }
                }
                function select_all_<?php echo $table['id'];?>(){
                    var div = document.getElementById('<?php echo $table['id'];?>');
                    var trs = div.getElementsByTagName('TR');
                    for( i=0; i<trs.length; i++ ) {
                        if (trs[i].firstChild.innerHTML=='') {
                            trs[i].firstChild.innerHTML='x';
                            trs[i].firstChild.className = 's_selected';                            
                            trs[i].className += " selected";
                        }
                    }
                    
                }
                function invert_<?php echo $table['id'];?>(){
                    var div = document.getElementById('<?php echo $table['id'];?>');
                    var trs = div.getElementsByTagName('TR');
                    for( i=0; i<trs.length; i++ ) {
                        s_<?php echo $table['id'];?>(trs[i].firstChild);
                    }
                }
                function after_load_<?php echo $table['id'];?>(){
                    var div = document.getElementById('<?php echo $table['id'];?>');
                    var trs = div.getElementsByTagName('TR');
                    <?php if (isset($table['after_load'])) echo $table['after_load'];?>
                }                
            </script>
        <?php
        
            if (is_mobile() OR (isset($table['noscroll']) AND $table['noscroll']==true)) {
                if ($header) {
                    ?>
                        <table class="header" style="table-layout:fixed;width:<?php 
                                echo $table['row_height'] + $table_width;
                            ?>px;">
                            <colgroup>
                                <col class="s" width="<?php echo $table['row_height']; ?>px"/>
                                <?php
                                    foreach ($table['width'] as $value) {
                                        echo '<col width="',$value,'px"/>';
                                    }
                                ?>
                            </colgroup>
                            <tr>
                                <th 
                                    class="control"
                                    title="Действия с выборкой"
                                    onclick="v_<?php echo $table['id'];?>(this);">*</th>
                                    <?php
                                        // выводим заголовок
                                        foreach ($table['header'] as $value) {
                                            echo '<th>',htmlfix($value),'</th>';
                                        }
                                    ?>
                            </tr>
                        </table>
                    <?php
                }
            ?>
                <div id="<?php echo $table['id'];?>">
                    <table class="data" style="table-layout:fixed;width:<?php 
                        echo $table['row_height'] + $table_width;
                    ?>px;">
                        <colgroup>
                            <col class="s" width="<?php echo $table['row_height']; ?>px"/>
                            <?php
                                foreach ($table['width'] as $value) {
                                    echo '<col width="',$value,'px"/>';
                                }
                            ?>
                        </colgroup>      
                        <?php
                            foreach ($table['data'] as $value) {
                                echo '<tr class="data">';
                                echo '<th class="s" onclick="s_',
                                        $table['id'],'(this);"></th>';
                                $i=0;
                                foreach ($value as $k=>$v) {
                                    echo '<td';
                                    if ( $table['class'][$i]!='') echo ' class="',$table['class'][$i],'"'; 
                                    if ( $table['onclick'][$i]!='') echo ' onclick=\'',$table['onclick'][$i],'\'';
                                    if ( $table['onmouseover'][$i]!='') 
                                        echo ' onmouseover="',$table['onmouseover'][$i],'"';
                                    if ( $table['onmouseout'][$i]!='') 
                                        echo ' onmouseout="',$table['onmouseout'][$i],'"';
                                    if ( $table['align'][$i]!='') echo ' align="',$table['align'][$i],'"';  
                                    echo '>',htmlfix($v),'</td>';
                                    $i++;
                                }
                                echo '</tr>';
                            }
                        ?>
                    </table>                
                </div>
             <?php
                if ($footer) {
                    ?>
                        <table class="footer" style="table-layout:fixed;width:<?php 
                        echo $table['row_height'] + $table_width;
                    ?>px;">
                            <colgroup>
                                <col class="s" width="<?php echo $table['row_height']; ?>px"/>
                                <?php
                                    foreach ($table['width'] as $value) {
                                        echo '<col width="',$value,'px"/>';
                                    }
                                ?>
                            </colgroup>
                            <tr>
                                <th class="s"></th>
                                    <?php
                                        // выводим заголовок
                                        foreach ($table['footer'] as $value) {
                                            echo '<th>',htmlfix($value),'</th>';
                                        }
                                    ?>
                            </tr>
                        </table>
                    <?php        
                }
            } else {
                if ($header) {
                    ?>
                    <table class="header" width="<?php 
                            echo $table['row_height'] + $table_width + $table['row_height'];
                        ?>" style="table-layout:fixed;">
                        <colgroup>
                            <col class="s" width="<?php echo $table['row_height']; ?>"/>
                            <?php
                                foreach ($table['width'] as $value) {
                                    echo '<col width="',$value,'px"/>';
                                }
                            ?>
                            <col  class="s" width="<?php echo $table['row_height']; ?>"/>
                        </colgroup>
                        <tr>
                            <th 
                                class="control noprint"
                                title="Действия с выборкой"
                                onclick="v_<?php echo $table['id'];?>(this);">*</th>
                                <?php
                                    // выводим заголовок
                                    foreach ($table['header'] as $value) {
                                        echo '<th>',htmlfix($value),'</th>';
                                    }
                                ?>
                            <th class="s"></th>
                        </tr>
                    </table>
                    <?php
                }
            ?>
                <div
                    class="scroll_<?php echo $table['id'];?>"
                    id="<?php echo $table['id'];?>"
                    style="width:<?php 
                            if ($ie) {
                                echo $header_footer_width + $table['row_height'] + 10; 
                            } else {
                                echo $header_footer_width + $table['row_height'];
                            };
                        ?>px;">
                    <table class="data" width="<?php
                        echo $table['row_height'] + $table_width + $table['row_height'];
                    ?>" style="table-layout:fixed;" >
                        <colgroup>
                            <col  class="s" width="<?php echo $table['row_height']; ?>"/>
                            <?php
                                foreach ($table['width'] as $value) {
                                    echo '<col width="',$value,'px"/>';
                                }
                            ?>
                            <col  class="s" width="<?php echo $table['row_height']; ?>"/>
                        </colgroup>      
                        <?php
                            foreach ($table['data'] as $value) {
                                echo '<tr class="data">';
                                echo '<th class="s" onclick="s_',
                                        $table['id'],'(this);"></th>';
                                $i=0;
                                foreach ($value as $k=>$v) {
                                    echo '<td';
                                    if ( $table['class'][$i]!='') echo ' class="',$table['class'][$i],'"'; 
                                    if ( $table['onclick'][$i]!='') echo ' onclick=\'',$table['onclick'][$i],'\'';
                                    if ( $table['onmouseover'][$i]!='') 
                                        echo ' onmouseover=\'',$table['onmouseover'][$i],'\'';
                                    if ( $table['onmouseout'][$i]!='') 
                                        echo ' onmouseout=\'',$table['onmouseout'][$i],'\'';
                                    if ( $table['align'][$i]!='') echo ' align="',$table['align'][$i],'"';  
                                    echo '>',htmlfix($v),'</td>';
                                    $i++;
                                }
                                echo '<th class="s"></th>';
                                echo '</tr>';
                            }
                        ?>
                    </table> 
                </div>
            <?php
            if ($footer) {
                ?>
                    <table class="footer" width="<?php 
                        echo $table['row_height'] + $table_width + $table['row_height'];
                    ?>" style="table-layout:fixed;">
                        <colgroup>
                            <col class="s" width="<?php echo $table['row_height']; ?>"/>
                            <?php
                                if (count($table['width'])>0) {
                                    foreach ($table['width'] as $value) {
                                        echo '<col width="',$value,'px"/>';
                                    }
                                } else {
                                    foreach ($table['header'] as $value) {
                                        echo '<col width="120px"/>';
                                    }
                                }
                            ?>
                            <col  class="s" width="<?php echo $table['row_height']; ?>"/>
                        </colgroup>
                        <tr>
                            <th class="s"></th>
                                <?php
                                    // выводим заголовок
                                    foreach ($table['footer'] as $value) {
                                        echo '<th>',htmlfix($value),'</th>';
                                    }
                                ?>
                            <th class="s"></th>
                        </tr>
                    </table>
                <?php
            }            
        }
        if ( isset($table['script']) AND $table['script']!='') {
            ?>
                <script><?php echo $table['script']; ?></script>
            <?php
        }        
        if ( isset($table['js']) AND $table['js']!='' AND file_exists('js/'.$table['js'])) {
            ?>
                <script src="js/<?php echo $table['js']; ?>"></script>
            <?php
        }
        if (file_exists('js/table_'.$table['id'].'.js')) {
            ?>
                <script src="js/table_<?php echo $table['id']; ?>.js"></script>
            <?php
        }
        ?>
            <script language="javascript" type="text/javascript">after_load_<?php echo $table['id'];?>();</script>
        <?php
     }
     
     function normalize_string_param($string) {
         // функция преобразует параметры в массивы, если они заданы в виде строк
         if (is_array($string)) {
            return $string; 
         } else {
            return explode('|',$string);    
         }
         
     }
?>
