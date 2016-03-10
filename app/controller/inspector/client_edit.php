<?php

/*
 * Контроллер для работы со списком клиентов
 */
        if (!isset($c)) exit;

        if (isset($_POST['action']) AND $_POST['action']=='update') {
            include 'app/view/html_header.php';
            $oldvalue = explode('|', $_POST['olddata']);
            $newvalue = explode('|', $_POST['newdata']);
            
            do_sql('LOCK TABLES Customers WRITE;');
            $row = fetch_row_from_sql('
                SELECT
                    `Customers`.`CustomerName`,
                    `Customers`.`CustomerCode`,
                    `Customers`.`CustomerKPCode`,
                    `Customers`.`CustomerOKATOCode`,
                    `Customers`.`CustomerAddress`,
                    `Customers`.`Customer_Email1`,
                    `Customers`.`Customer_Email2`,
                    `Customers`.`CustomerPhone1`,
                    `Customers`.`CustomerPhone2`,
                    `Customers`.`CustomerContactName`,
                    `Customers`.`CustomerContactPost`
                FROM 
                    `cashmaster`.`Customers`
                WHERE
                     `Customers`.`LogicallyDeleted`="0"
                     AND `Customers`.`CustomerId`="'.addslashes($_REQUEST['id']).'"
            ;');
            
            if ( $oldvalue==$row ) {
                do_sql('
                    UPDATE `cashmaster`.`Customers`
                    SET
                        `CustomerName` = "'.addslashes($newvalue[0]).'",
                        `CustomerKPCode` = "'.addslashes($newvalue[2]).'",
                        `CustomerOKATOCode` = "'.addslashes($newvalue[3]).'",
                        `CustomerAddress` = "'.addslashes($newvalue[4]).'",
                        `Customer_Email1` = "'.addslashes($newvalue[5]).'",
                        `Customer_Email2` = "'.addslashes($newvalue[6]).'",
                        `CustomerPhone1` = "'.addslashes($newvalue[7]).'",
                        `CustomerPhone2` = "'.addslashes($newvalue[8]).'",
                        `CustomerContactName` = "'.addslashes($newvalue[9]).'",
                        `CustomerContactPost` = "'.addslashes($newvalue[10]).'"
                    WHERE 
                        `Customers`.`LogicallyDeleted`="0"
                         AND `Customers`.`CustomerId`="'.addslashes($_REQUEST['id']).'"
    
                ;');
                echo 0;
                do_sql('UNLOCK TABLES;');     
                exit;            
            } else {
                echo 1;
                echo implode('|', $row);
                do_sql('UNLOCK TABLES;');     
                exit;
            };
        };
        
        
        $data['title'] = "Редактирование клиента";
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        include './app/view/update_record.php';
        
        
        $client = fetch_assoc_row_from_sql('
            SELECT
                `Customers`.`CustomerName`,
                `Customers`.`CustomerCode`,
                `Customers`.`CustomerKPCode`,
                `Customers`.`CustomerOKATOCode`,
                `Customers`.`CustomerAddress`,
                `Customers`.`Customer_Email1`,
                `Customers`.`Customer_Email2`,
                `Customers`.`CustomerPhone1`,
                `Customers`.`CustomerPhone2`,
                `Customers`.`CustomerContactName`,
                `Customers`.`CustomerContactPost`,
                CustomerCodeLength
            FROM 
                `cashmaster`.`Customers`
            WHERE
                 `Customers`.`CustomerId`="'.$_GET['id'].'"
        ;');
        
        
?>
<script>
    var before_update = "<?php echo implode('|',$client); ?>";
    function update(elem){
        if (check_fields()) {
            update_record(elem,'?c=client_edit&id=' + <?php echo $_GET['id']; ?>);
        };
    };
    function check_fields() {
        
        return true;
    };
    function set_before_data() {
        var inputs = $('input.stat');
        var before = before_update.split("|");
        var i=0;
        $(inputs).each(function(){
            this.value = before[i];
            i++;
        });
        update(inputs[0]);
    };
    function stat_onkeyup(event) {
        var key = event.keyCode;
        var elem = ( event.target ) ? event.target : event.srcElement;
        switch(key){
            case 27:
                elem.value = $(elem).attr('oldvalue');
            break;
            default:
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
            <th>Название организации:</th>
            <td>
                <input 
                    class='span7 stat'
                    placeholder='Название организации' 
                    type='text' 
                    name='CustomerName'
                    onblur="update(this);"
                    onkeyup="stat_onkeyup(event);"
                    oldvalue='<?php echo htmlfix($client['CustomerName']); ?>'
                    value='<?php echo htmlfix($client['CustomerName']); ?>' 
                    maxlength='80'/>
            </td>
        </tr>
        <tr>
            <th>Код (БИК):</th>
            <td>
                <input style='margin: 0px;' 
                       class='stat'
                       placeholder='Код (БИК)' type='text' name='CustomerCode'
                       readonly=""
                       oldvalue='<?php echo htmlfix($client['CustomerCode']); ?>'
                       value='<?php echo htmlfix($client['CustomerCode']); ?>' 
                       maxlength='<?php echo htmlfix($client['CustomerCodeLength']); ?>'/>
                <br/>
            </td>
        </tr>
        <tr>
            <th>Код КП:</th>
            <td>
                <input
                       class="stat"
                       placeholder='Код КП' type='text' name='CustomerKPCode' 
                       onblur="update(this);"
                       onkeyup="stat_onkeyup(event);"
                       oldvalue='<?php echo htmlfix($client['CustomerKPCode']); ?>'
                       value='<?php echo htmlfix($client['CustomerKPCode']); ?>' maxlength='15'/>
            </td>
        </tr>
        <tr>
            <th>Код ОКАТО:</th>
            <td>
                <input 
                       class='stat'
                       placeholder='Код ОКАТО' type='text' name='CustomerOKATOCode'
                       onblur="update(this);"
                       onkeyup="stat_onkeyup(event);"
                       oldvalue='<?php echo htmlfix($client['CustomerOKATOCode']); ?>'
                       value='<?php echo htmlfix($client['CustomerOKATOCode']); ?>' maxlength='15'/>
            </td>
        </tr>
        <tr>
            <th>Адрес:</th>
            <td>
                <input 
                        class='span7 stat'
                        placeholder='Адрес' type='text' name='CustomerAddress' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['CustomerAddress']); ?>'
                        value='<?php echo htmlfix($client['CustomerAddress']); ?>' maxlength='120'/>
            </td>
        </tr>
        <tr>
            <th>Адреса эл.почты:</th>
            <td>
                <input 
                        class='span5 stat'
                        placeholder='e-mail 1' type='text' name='Customer_Email1' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['Customer_Email1']); ?>'
                        value='<?php echo htmlfix($client['Customer_Email1']); ?>' 
                        maxlength='80'/>
                <br/>
                <input 
                        class='span5 stat'
                        placeholder='e-mail 2' type='text' name='Customer_Email2' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['Customer_Email2']); ?>' 
                        value='<?php echo htmlfix($client['Customer_Email2']); ?>' 
                        maxlength='80'/>
            </td>
        </tr>
        <tr>
            <th>Телефоны:</th>
            <td>
                <input 
                       class='stat'
                       placeholder='Телефон' type='text' name='CustomerPhone1' 
                       onblur="update(this);"
                       onkeyup="stat_onkeyup(event);"
                       oldvalue='<?php echo htmlfix($client['CustomerPhone1']); ?>'
                       value='<?php echo htmlfix($client['CustomerPhone1']); ?>' maxlength='20'/>
                <br/>
                <input 
                       class='stat'
                       placeholder='Телефон/факс' type='text' name='CustomerPhone2' 
                       onblur="update(this);"
                       onkeyup="stat_onkeyup(event);"
                       oldvalue='<?php echo htmlfix($client['CustomerPhone2']); ?>'
                       value='<?php echo htmlfix($client['CustomerPhone2']); ?>' maxlength='20'/>
            </td>
        </tr>
        <tr>
            <th>Контактное лицо:</th>
            <td>
                <input class='span7 stat'
                        placeholder='Фамилия Имя Отчество' type='text' 
                        name='CustomerContactName' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['CustomerContactName']); ?>'
                        value='<?php echo htmlfix($client['CustomerContactName']); ?>' maxlength='45'/>
            </td>
        </tr>
        <tr>
            <th>Должность:</th>
            <td>
                <input class='span7 stat'
                        placeholder='Должность' type='text' 
                        name='CustomerContactPost' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['CustomerContactPost']); ?>'
                        value='<?php echo htmlfix($client['CustomerContactPost']); ?>' maxlength='80'/>
            </td>
        </tr>
    </table>
        <button onclick="window.location.replace('?c=customers');" 
            class='btn btn-large btn-info'>Сохранить и выйти</button>
        <button onclick="set_before_data(); window.location.replace('?c=customers');" 
                class='btn btn-large btn-warning'>Отменить изменения и выйти</button>
        <?php 
            if ($_SESSION[$program]['UserConfiguration']['RoleId']=='1') {
                ?>
                    <button onclick="window.location.replace('?c=customers');" 
                            class='btn btn-large btn-danger'>Удалить клиента</button>
                <?php
            };
        ?>
</div>
