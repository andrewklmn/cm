<?php

/*
 * Controller sql
 */

        if (!isset($c)) exit;

        $data['title'] = 'Raw SQL wizard';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/draw_table.php';
        
        $sql = 'SHOW TABLES';
        $tables = get_array_from_sql($sql);

        //do_sql('SET foreign_key_checks = 0;');
        
        if (isset($_POST['sql'])) {
            $sql = $_POST['sql'];
        };
        
?>
<script>
    function describe_table_query() {
        $('textarea#sql').html('DESCRIBE ' + $('select#current_table')[0].value + ';');
        $('button#do_sql').click();
    }
    function select_all_query() {
        $('textarea#sql').html('SELECT \n * \n FROM \n' + $('select#current_table')[0].value + ';');
    }
    function edit_table_data() {
        var name=$('select#current_table')[0].value;
        window.open("?c=edit_table&name=" + name, name);
    }
    function edit_raw_table_data() {
        var name=$('select#current_table')[0].value;
        window.open("?c=edit_raw_table&name=" + name, 'raw' + name);
    }
    function put_to_editor() {
        var sql= $('div#sample')[0].innerHTML;
        //alert(sql);
        sql =  sql.replace(/\<BR\>/g,"\r\n");
        sql =  sql.replace(/\<br\>/g,"\r\n");
        sql =  sql.replace(/\&nbsp\;/g,' ');
        $('textarea#sql').html(sql);
        //&nbsp;
    }
</script>
<div class="container">
        <table>
            <tr>
                <td style="padding-right:10px;vertical-align: top;">
                    <b>SQL text editor:</b><br/>
                    <form action="?c=sql" method="POST">
                        <textarea id='sql' name='sql' style="width: 600px; height: 300px;" id="sql"><?php
                            echo $sql;
                        ?></textarea>
                        <br/>
                        <button 
                            onclick="set_wait();this.innerHTML='Query was sent...'"
                            id="do_sql" type='submit' class='btn btn-primary btn-medium'>Do SQL>></button>
                    </form>
                </td>
                <td style="vertical-align: top;" colspan="2">
                    <b>SQL help:</b><br/>
                    <font style='font-size: 11px;color:gray;'>
                        SELECT column_name(s) FROM table_name
                        <br/>
                        UPDATE table_name SET column1=value, column2=value2,... WHERE some_column=some_value
                        <br/>
                        INSERT INTO table_name VALUES (value1, value2, value3,...)
                        <br/>
                        INSERT INTO table_name (column1, column2, column3,...) VALUES (value1, value2, value3,...)
                        <br/>
                        SHOW tables
                        <br/>
                        DESCRIBE table_name
                        <br/>
                        SELECT table1.* FROM table1 LEFT JOIN table2 ON table1.id=table2.id WHERE table2.id IS NULL
                        <br/>
                        SELECT field1,SUM(field2) FROM table GROUP BY field1
                    </font>
                    <br/>
                    <br/>
                    <b>Table selector:</b><br>
                    <select id='current_table' name='current_table'>
                    <?php 
                        foreach ($tables as $key => $value) {
                            if ($key==0) {
                                echo '<option selected="selected" value="',htmlfix($value[0]),'">',htmlfix($value[0]),'</option>';
                            } else {
                                echo '<option value="',htmlfix($value[0]),'">',htmlfix($value[0]),'</option>';
                            }
                        }
                    ?>                        
                    </select>
                    <br/>
                    <button onclick='describe_table_query();' class='btn-mini'>DESCRIBE TABLE</button>
                    <button onclick='select_all_query();'class='btn-mini'>SELECT ALL</button>
                    <button onclick='edit_table_data();'class='btn-mini'>EDIT TABLE DATA</button>
                    <button onclick='edit_raw_table_data();'class='btn-mini'>EDIT RAW TABLE DATA</button>
                </td>
            </tr>
            </table>
        <?php
            //include 'app/controller/common/envelope.php';
        ?>
            <table>
                <tr>
                    <td style='padding-right: 30px;vertical-align: top;'>
                        <b>SQL result:</b><br/>
                        <?php
                            echo '<table border="1" style="font-size:11px;">';
                            //==================== Печать заголовка
                            if (substr(ltrim($sql),0,5)=="SELEC" OR 
                                    substr(ltrim(strtolower($sql)),0,5)=="selec" OR
                                    substr(ltrim(strtolower($sql)),0,5)=="show " OR
                                    substr(ltrim(strtolower($sql)),0,5)=="descr") {
                                            $header=  fetch_fields_info_from_sql($sql);
                                            $num_col=count($header);
                                            echo '<tr>';
                                            foreach ($header as $key=>$value) { //  вынимаем из базы характеристики полей в результате запроса 
                                                    echo '<th>';
                                                    echo $value->name;
                                                    echo '</th>';
                                            };
                                            echo '</tr>';
                                    //=================== Печать таблицы
                                    $rows= get_array_from_sql($sql);
                                    foreach ($rows as $key=>$value) {
                                            echo '<tr>';
                                            for ( $i=0; $i<count($value) ; $i++ ) {
                                                    echo '<td class="t_res">'.htmlfix($value[$i]).'</td>';
                                            };
                                            echo '</tr>';
                                    }; 
                            } else {
                                    $rows = do_sql($sql);
                                    echo 'Query was done.<br/>';
                                    echo "Affected rows: ",$rows,'*<br/>';
                                    echo '<font style=font-size:10px;>
                                            * if query was multiple then number of affected rows belongs to last query.
                                          </font>';
                            };
                            echo '</table>';
                        ?>
                    </td>
                    <td style='vertical-align: top;'>
                        <?php 
                        
                            if (substr(ltrim(strtolower($sql)),0,5)=="descr") {
                                //echo '<b>All fields SQL query text:</b><br/>';
                                echo '<div id="sample" style="color:blue;">SELECT<br/>';
                                foreach ($rows as $key=>$value) {
                                    $d[] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$value[0];
                                }; 
                                echo implode(',<br/>', $d);
                                echo '<br/>FROM<br/>';
                                $d = explode(' ', $sql);
                                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',  str_replace(';', '', $d[1]);
                                echo '<br/>ORDER BY ',$rows[0][0],' ASC;</div>';
                                ?>
                                    <button onclick='put_to_editor();' class='btn-mini'>Put this example to SQL text editor</button>
                                <?php
                            }
                    ?></td>
                                        <td style='vertical-align: top;'>
                        <?php 
                        
                            if (substr(ltrim(strtolower($sql)),0,5)=="descr") {
                                $d = array();
                                foreach ($rows as $key=>$value) {
                                    $d[] = $value[0];
                                }; 
                                echo 'Fields string:<br/>';
                                echo implode('|', $d);
                                echo '<br/>';
                                echo '<br/>';
                                foreach ($d as $key=>$value) {
                                    $d[$key] = "    ".$key." => '".$value."'";
                                }; 
                                echo 'Fields array:<br/>';
                                echo '<pre>';
                                echo "array(\n".implode(",\n", $d)."\n);";
                                echo '</pre>';
                            };
                            
                    ?></td>
                </tr>
            </table>
</div>
