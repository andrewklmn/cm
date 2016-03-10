<?php

/*
 * Контроллер для работы со списком клиентов
 */
        if (!isset($c)) exit;
        
        if (isset($_POST['action']) AND $_POST['action']==$_SESSION[$program]['lang']['restore_client']) {
            // Снимаем блокировку
            do_sql('
                UPDATE `cashmaster`.`Customers`
                SET
                    `Customers`.`LogicallyDeleted`="0"
                WHERE 
                    `Customers`.`CustomerId`="'.addslashes($_REQUEST['id']).'"
            ;');            
        };
        
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
        
        $l = explode('|', $_SESSION[$program]['lang']['customer_edit_labels']);
        $b = explode('|', $_SESSION[$program]['lang']['customer_edit_buttons']);
        $w = explode('|', $_SESSION[$program]['lang']['client_delete_labels']);
        
        
        $data['title'] = $l[0];
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
                CustomerCodeLength,
                `Customers`.`LogicallyDeleted`
            FROM 
                `cashmaster`.`Customers`
            WHERE
                 `Customers`.`CustomerId`="'.addslashes($_GET['id']).'"
        ;');
        
        if($client['LogicallyDeleted']==1) {
            if ($_SESSION[$program]['UserConfiguration']['UserRoleId']!=1) {
                // Если это супервизор, то просто неразрешаем
                ?>
                    <div class="container">
                        <div class="alert alert-error">  
                          <a class="close" data-dismiss="alert">×</a>  
                          <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                          <br/>
                              <?php echo htmlfix($w[2]); ?>
                        </div> 
                        <br/>
                        <button onclick="window.location.replace('?c=customers');" 
                                class='btn btn-large btn-info'><?php 
                                    echo htmlfix($_SESSION[$program]['lang']['back_to_list']); 
                                ?></button>
                    </div>
                <?php
            } else {
                // Если это админ, то разрешаем
                ?>
                    <div class="container">
                        <h3><?php echo htmlfix($_SESSION[$program]['lang']['confirm_client_restoration']); ?>:</h3>
                        <table class='edit_client'>
                            <tr>
                                <th><?php echo htmlfix($l[1]); ?>:</th>
                                <td>
                                    <input 
                                        class='span7'
                                        placeholder='<?php echo htmlfix($l[1]); ?>' 
                                        type='text' 
                                        readonly
                                        name='CustomerName'
                                        onblur="update(this);"
                                        onkeyup="stat_onkeyup(event);"
                                        oldvalue='<?php echo htmlfix($client['CustomerName']); ?>'
                                        value='<?php echo htmlfix($client['CustomerName']); ?>' 
                                        maxlength='80'/>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo htmlfix($l[2]); ?>:</th>
                                <td>
                                    <input style='margin: 0px;' 
                                           class='stat'
                                           placeholder='<?php echo htmlfix($l[2]); ?>' type='text' name='CustomerCode'
                                           readonly
                                           oldvalue='<?php echo htmlfix($client['CustomerCode']); ?>'
                                           value='<?php echo htmlfix($client['CustomerCode']); ?>' 
                                           maxlength='<?php echo htmlfix($client['CustomerCodeLength']); ?>'/>
                                    <br/>
                                </td>
                            </tr>
                        </table>
                        <br/>
                        <form method="POST">                            
                            <a class='btn btn-large btn-info' href="?c=customers"><?php 
                                        echo htmlfix($_SESSION[$program]['lang']['cancel']); 
                                    ?></a>
                            <input 
                                type="submit" 
                                class='btn btn-large btn-danger' 
                                name="action" 
                                value="<?php 
                                    echo htmlfix($_SESSION[$program]['lang']['restore_client']); 
                                ?>"/>
                        </form>
                    </div>
                <?php
            };
            exit;
        };
        
        if (isset($_REQUEST['action']) AND $_REQUEST['action']=='delete') {
            if(isset($_POST['confirmation']) 
                    AND $_REQUEST['confirmation']==$_SESSION[$program]['lang']['yes']){
                    do_sql('
                        UPDATE 
                            `cashmaster`.`Customers`
                        SET
                            `LogicallyDeleted` = 1
                        WHERE `CustomerId` = "'.  addslashes($_GET['id']).'"
                    ;');
               ?>
                    <div class="container">
                        <div class="alert alert-success">  
                          <a class="close" data-dismiss="alert">×</a>  
                          <strong><?php echo $_SESSION[$program]['lang']['success']; ?>!</strong>
                          <br/>
                          <?php echo htmlfix($client['CustomerName'].' - '.$w[0]); ?>
                        </div> 
                        <br/>
                        <button onclick="window.location.replace('?c=customers');" 
                                class='btn btn-large btn-info'><?php 
                                    echo htmlfix($_SESSION[$program]['lang']['back_to_list']); 
                                ?></button>
                    </div>
               <?php
               exit;
            } else {
                if(isset($_POST['confirmation']) AND $_REQUEST['confirmation']==$_SESSION[$program]['lang']['no']) {
                } else {
                   ?>
                        <div class="container">
                            <h3>
                                <?php echo htmlfix($w[1]); ?>: 
                                <?php echo htmlfix($client['CustomerName']); ?>?
                            </h3>
                            <br/>
                            <form method="POST">
                                <input 
                                    class="btn btn-primary btn-large"
                                    type="submit" name="confirmation" value="<?php 
                                    echo htmlfix($_SESSION[$program]['lang']['no']);
                                ?>"/>
                                <input 
                                    class="btn btn-danger btn-large"
                                    type="submit" name="confirmation" value="<?php 
                                    echo htmlfix($_SESSION[$program]['lang']['yes']);
                                ?>"/>
                            </form>
                        </div>
                   <?php
                   exit;
                };
            };
        };
        
?>
<script>
    var before_update = "<?php echo htmlfix(implode('|',$client)); ?>";
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
            <th><?php echo htmlfix($l[1]); ?>:</th>
            <td>
                <input 
                    class='span7 stat'
                    placeholder='<?php echo htmlfix($l[1]); ?>' 
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
            <th><?php echo htmlfix($l[2]); ?>:</th>
            <td>
                <input style='margin: 0px;' 
                       class='stat'
                       placeholder='<?php echo htmlfix($l[2]); ?>' type='text' name='CustomerCode'
                       readonly=""
                       oldvalue='<?php echo htmlfix($client['CustomerCode']); ?>'
                       value='<?php echo htmlfix($client['CustomerCode']); ?>' 
                       maxlength='<?php echo htmlfix($client['CustomerCodeLength']); ?>'/>
                <br/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[3]); ?>:</th>
            <td>
                <input
                       class="stat"
                       placeholder='<?php echo htmlfix($l[3]); ?>' type='text' name='CustomerKPCode' 
                       onblur="update(this);"
                       onkeyup="stat_onkeyup(event);"
                       oldvalue='<?php echo htmlfix($client['CustomerKPCode']); ?>'
                       value='<?php echo htmlfix($client['CustomerKPCode']); ?>' maxlength='15'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[4]); ?>:</th>
            <td>
                <input 
                       class='stat'
                       placeholder='<?php echo htmlfix($l[4]); ?>' type='text' name='CustomerOKATOCode'
                       onblur="update(this);"
                       onkeyup="stat_onkeyup(event);"
                       oldvalue='<?php echo htmlfix($client['CustomerOKATOCode']); ?>'
                       value='<?php echo htmlfix($client['CustomerOKATOCode']); ?>' maxlength='15'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[5]); ?>:</th>
            <td>
                <input 
                        class='span7 stat'
                        placeholder='<?php echo htmlfix($l[5]); ?>' type='text' name='CustomerAddress' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['CustomerAddress']); ?>'
                        value='<?php echo htmlfix($client['CustomerAddress']); ?>' maxlength='120'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[6]); ?>:</th>
            <td>
                <input 
                        class='span5 stat'
                        placeholder='<?php echo htmlfix($l[6]); ?> 1' type='text' name='Customer_Email1' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['Customer_Email1']); ?>'
                        value='<?php echo htmlfix($client['Customer_Email1']); ?>' 
                        maxlength='80'/>
                <br/>
                <input 
                        class='span5 stat'
                        placeholder='<?php echo htmlfix($l[6]); ?> 2' type='text' name='Customer_Email2' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['Customer_Email2']); ?>' 
                        value='<?php echo htmlfix($client['Customer_Email2']); ?>' 
                        maxlength='80'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[7]); ?>:</th>
            <td>
                <input 
                       class='stat'
                       placeholder='<?php echo htmlfix($l[7]); ?>' type='text' name='CustomerPhone1' 
                       onblur="update(this);"
                       onkeyup="stat_onkeyup(event);"
                       oldvalue='<?php echo htmlfix($client['CustomerPhone1']); ?>'
                       value='<?php echo htmlfix($client['CustomerPhone1']); ?>' maxlength='20'/>
                <br/>
                <input 
                       class='stat'
                       placeholder='<?php echo htmlfix($l[7]); ?>/<?php echo htmlfix($l[8]); ?>' 
                       type='text' name='CustomerPhone2' 
                       onblur="update(this);"
                       onkeyup="stat_onkeyup(event);"
                       oldvalue='<?php echo htmlfix($client['CustomerPhone2']); ?>'
                       value='<?php echo htmlfix($client['CustomerPhone2']); ?>' maxlength='20'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[9]); ?>:</th>
            <td>
                <input class='span7 stat'
                        placeholder='<?php echo htmlfix($l[9]); ?>' type='text' 
                        name='CustomerContactName' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['CustomerContactName']); ?>'
                        value='<?php echo htmlfix($client['CustomerContactName']); ?>' maxlength='45'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[10]); ?>:</th>
            <td>
                <input class='span7 stat'
                        placeholder='<?php echo htmlfix($l[10]); ?>' type='text' 
                        name='CustomerContactPost' 
                        onblur="update(this);"
                        onkeyup="stat_onkeyup(event);"
                        oldvalue='<?php echo htmlfix($client['CustomerContactPost']); ?>'
                        value='<?php echo htmlfix($client['CustomerContactPost']); ?>' maxlength='80'/>
            </td>
        </tr>
    </table>
        <button onclick="set_before_data();window.location.replace('?c=customers');" 
            class='btn btn-large btn-info'><?php echo htmlfix($b[1]); ?></button>
        <button onclick="window.location.replace('?c=customers');" 
                class='btn btn-large btn-warning'><?php echo htmlfix($b[0]); ?></button>
        <?php 
            if ($_SESSION[$program]['UserConfiguration']['RoleId']=='1') {
                ?>
                    <a href="?c=client_edit&id=<?php echo htmlfix($_GET['id']); ?>&action=delete" class='btn btn-large btn-danger'>
                        <?php echo htmlfix($b[2]); ?>
                    </a>
                <?php
            };
        ?>
</div>
<?php 
    include './app/view/set_rs_to_stat.php';
?>
