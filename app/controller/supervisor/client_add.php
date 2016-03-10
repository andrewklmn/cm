<?php

/*
 * Контроллер для работы со списком клиентов
 */
        if (!isset($c)) exit;
        
        $l = explode('|', $_SESSION[$program]['lang']['customer_add_labels']);
        $b = explode('|', $_SESSION[$program]['lang']['customer_add_buttons']);
        $m = explode('|', $_SESSION[$program]['lang']['customer_add_messages']);
        $a = explode('|', $_SESSION[$program]['lang']['customer_add_alerts']);

        $data['title'] = $l[0];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';

        $data = explode('|','||9|||||||||');

        if (isset($_POST['action']) AND $_POST['action']=='add_new') {
            if (isset($_POST['confirmation'])) {
                echo '<div class="container">';
                $names = explode('|','CustomerName|CustomerCode|CustomerCodeLength|CustomerKPCode|CustomerOKATOCode|CustomerAddress|Customer_Email1|Customer_Email2|CustomerPhone1|CustomerPhone2|CustomerContactName|CustomerContactPost ');
                $data = explode('|',$_POST['newdata']);
                
                //echo '<pre>';
                //print_r($data);
                //echo '</pre>';
                
                //foreach ($data as $key=>$value) {
                //    $data[$key] = '"'.addslashes($value).'"';
                //};
                
                do_sql('LOCK TABLES Customers WRITE;');
                // проверяем есть ли клиент с таким кодом БИКы
                
                //echo '<pre>';
                //print_r($data);
                //echo '</pre>';
                
                $row = get_array_from_sql('
                    SELECT
                        *
                    FROM
                        `cashmaster`.`Customers`
                    WHERE
                        CustomerCode="'.addslashes($data[1]).'"
                        OR CustomerName="'.addslashes($data[0]).'"
                ;');

                foreach ($data as $key=>$value) {
                    $data[$key] = '"'.addslashes($value).'"';
                };
                
                if (count($row)==0) {
                    do_sql('
                        INSERT INTO `cashmaster`.`Customers`
                            (
                                '.implode(',', $names).'
                            )
                        VALUES
                            (
                                '.implode(',', $data).'
                            )
                    ;');
                    $data['success'] = $m[0].' '.stripslashes($data[0]);
                    include 'app/view/success_message.php';
                } else {
                    //такой клиент уже есть
                    $data['error'] = stripslashes($data[1]).' '.$m[1];
                    include 'app/view/error_message.php';
                };
                    ?>
                        <a href="?c=customers" class="btn btn-large btn-primary">
                            <?php echo htmlfix($b[3]); ?>
                        </a>
                    <?php
                    echo '</div>';
                do_sql('UNLOCK TABLES;');                
                exit;
            } else {
                
                $data = explode('|',$_POST['newdata']);
                if(!isset($_POST['cancelation'])) {
                    // проверяем есть ли клиент с таким кодом БИКы
                    $row = get_array_from_sql('
                        SELECT
                            *
                        FROM
                            `cashmaster`.`Customers`
                        WHERE
                            CustomerCode="'.addslashes($data[1]).'"
                            OR CustomerName="'.addslashes($data[0]).'"
                    ;');

                    if (count($row)>0) {
                        echo '<div class="container">';
                        //такой клиент уже есть
                        $data['error'] = $data[1].' - '.$m[1];
                        include 'app/view/error_message.php';
                        ?>
                            <a href="?c=customers" class="btn btn-large btn-primary">
                                <?php echo htmlfix($b[3]); ?>
                            </a>
                        <?php
                        echo '</div>';
                        exit;
                    };
                    ?>
                        <style>
                            table.confirm th {
                                text-align: left;
                            }
                            table.confirm td {
                                color: blue;
                                font-size: 16px;
                            }
                        </style>
                        <div class="container">
                            <h3><?php echo htmlfix($l[1]); ?>:</h3>
                            <br/>
                            <table class="confirm">
                                <tr>
                                    <th><?php echo htmlfix($l[2]); ?>: </th>
                                    <td><?php echo htmlfix($data[0]); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo htmlfix($l[3]); ?>: </th>
                                    <td><?php echo htmlfix($data[1]); ?></td>
                                </tr>
                            </table>
                                <form action="?c=client_add" method="POST">
                                    <input type="hidden" name="action" value="add_new"/>
                                    <input 
                                           type="hidden"
                                           name="newdata"
                                           value="<?php echo htmlfix($_POST['newdata']); ?>"/>
                                    <input 
                                           type="hidden"
                                           name="names"
                                           value="<?php echo htmlfix($_POST['names']); ?>"/>
                                    <br/>                                    
                                    <br/>
                                    <input type="submit"
                                           class="btn btn-info btn-large"
                                           name="confirmation" value="<?php echo htmlfix($b[2]); ?>" />
                                    <input type="submit"
                                           class="btn btn-warning btn-large"
                                           name="cancelation" value="<?php echo htmlfix($b[1]); ?>" />
                                </form>
                        </div>
                    <?php
                    exit;
                };
            }
        };
        
?>
<script>
    function add_client(){
        var inputs = $('input.stat');
        var newdata = [];
        var names = [];
        $(inputs).each(function (){
            $(this).css('background-color','white');
            newdata[newdata.length] = this.value;
            names[names.length] = $(this).attr('name');           
        });
        if (check_fields(inputs)) {
            // Добавляем клиента
            $('input#newdata').val(newdata.join("|"));
            $('input#names').val(names.join("|"));
            $('form#add').submit();
        };
    };
    function check_fields(inputs) {
        if (inputs[0].value=='' || inputs[1].value=='' || inputs[2].value=='') {
            if (inputs[0].value=='') $(inputs[0]).css('background-color','yellow');
            if (inputs[1].value=='') $(inputs[1]).css('background-color','yellow');
            if (inputs[2].value=='') $(inputs[2]).css('background-color','yellow');
            alert('<?php echo htmlfix($a[2]); ?>');
            return false;
        } else {
            if (inputs[1].value.length==inputs[2].value) {
                return true;
            } else {
                $(inputs[1]).css('background-color','yellow');
                alert('<?php echo htmlfix($a[0]); ?>');
                return false;
            };
        };
    };
    function change_length(elem) {
        if (parseFloat(elem.value)>1 && parseFloat(elem.value)<99) { 
            $('input#bic').each(function(){
                this.value = this.value.substring(0,elem.value); 
                $(this).attr('maxlength',elem.value);
            });
        } else {
            alert('<?php echo htmlfix($a[1]); ?>');
            $(elem).css('background-color','yellow');
            elem.value=9;
        };
    };
</script>
<div class='container'>
    <style>
        table.edit_client th {
            text-align: right;
            vertical-align: top;
            padding: 5px;
        }
        table.edit_client input {
            margin: 4px;
        }
    </style>
    <table class='edit_client'>
        <tr>
            <th><?php echo htmlfix($l[2]); ?>:</th>
            <td>
                <input 
                    class='span7 stat'
                    placeholder='<?php echo htmlfix($l[2]); ?>' 
                    type='text' 
                    name='CustomerName'
                    value='<?php echo htmlfix($data[0]); ?>' 
                    maxlength='80'/>
                <font style='font-family: serif;font-size: 20px;color:red;font-weight: bold;'>*</font>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[3]); ?>:</th>
            <th style="text-align: left;">
                <input style='margin: 0px;' 
                       class='stat'
                       id="bic"
                       placeholder='<?php echo htmlfix($l[3]); ?>' type='text' name='CustomerCode'
                       value='<?php echo htmlfix($data[1]); ?>' 
                       maxlength='9'/>
                <font style='font-family: serif;font-size: 20px;color:red;'>*</font>
                &nbsp;&nbsp;&nbsp; <?php echo htmlfix($l[4]); ?>: <input style='text-align: center; margin: 0px;' 
                       class='stat span1'
                       placeholder='<?php echo htmlfix($l[4]); ?>' type='text' name='CustomerCodeLength'
                       onblur="change_length(this);"
                       value='<?php echo htmlfix($data[2]); ?>' 
                       maxlength='2'/>
                <font style='font-family: serif;font-size: 20px;color:red;'>*</font>
                <br/>
                <font style='font-family: serif;font-size: 15px;color:red;'>*</font>
                <font style='font-size: 10px;color:red;'>
                     - <?php echo htmlfix($l[5]); ?>
                </font>
            </th>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[6]); ?>:</th>
            <td>
                <input
                       class="stat"
                       placeholder='<?php echo htmlfix($l[6]); ?>' type='text' name='CustomerKPCode' 
                       value='<?php echo htmlfix($data[3]); ?>' 
                       maxlength='15'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[7]); ?>:</th>
            <td>
                <input 
                       class='stat'
                       placeholder='<?php echo htmlfix($l[7]); ?>' type='text' name='CustomerOKATOCode'
                       value='<?php echo htmlfix($data[4]); ?>' 
                       maxlength='15'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[8]); ?>:</th>
            <td>
                <input 
                        class='span7 stat'
                        placeholder='<?php echo htmlfix($l[8]); ?>' type='text' name='CustomerAddress' 
                       value='<?php echo htmlfix($data[5]); ?>' 
                        maxlength='120'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[9]); ?>:</th>
            <td>
                <input 
                        class='span5 stat'
                        placeholder='<?php echo htmlfix($l[9]); ?> 1' type='text' name='Customer_Email1' 
                        value='<?php echo htmlfix($data[6]); ?>'  
                        maxlength='80'/>
                <br/>
                <input 
                        class='span5 stat'
                        placeholder='<?php echo htmlfix($l[9]); ?> 2' type='text' name='Customer_Email2' 
                        value='<?php echo htmlfix($data[7]); ?>' 
                        maxlength='80'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[10]); ?>:</th>
            <td>
                <input 
                       class='stat'
                       placeholder='<?php echo htmlfix($l[10]); ?>' type='text' name='CustomerPhone1' 
                       value='<?php echo htmlfix($data[8]); ?>' 
                       maxlength='20'/>
                <br/>
                <input 
                       class='stat'
                       placeholder='<?php echo htmlfix($l[10]); ?>/<?php echo htmlfix($l[11]); ?>' type='text' name='CustomerPhone2' 
                       value='<?php echo htmlfix($data[9]); ?>' 
                       maxlength='20'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[12]); ?>:</th>
            <td>
                <input class='span7 stat'
                        placeholder='<?php echo htmlfix($l[13]); ?>' type='text' 
                        name='CustomerContactName' 
                       value='<?php echo htmlfix($data[10]); ?>' 
                        maxlength='45'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[14]); ?>:</th>
            <td>
                <input class='span7 stat'
                        placeholder='<?php echo htmlfix($l[14]); ?>' type='text' 
                        name='CustomerContactPost' 
                        value='<?php echo htmlfix($data[11]); ?>' 
                        maxlength='80'/>
            </td>
        </tr>
    </table>
        <button onclick="add_client();" 
            class='btn btn-large btn-info'><?php echo htmlfix($b[0]); ?></button>
        <button onclick="window.location.replace('?c=customers');" 
                class='btn btn-large btn-warning'><?php echo htmlfix($b[1]); ?></button>
    <form style="display:none;" id="add" action="?c=client_add" method="POST">
        <input type="hidden" name="action" value="add_new"/>
        <input id="newdata" type="hidden" name="newdata" value=""/>
        <input id="names" type="hidden" name="names" value=""/>
    </form>
</div>
<?php 
    include './app/view/set_rs_to_stat.php';
?>
