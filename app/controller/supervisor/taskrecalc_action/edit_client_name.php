<?php

    if (!isset($c)) exit;

    $t = unserialize(base64_decode($_POST['wrong_clients']));
    
    $l = explode('|', $_SESSION[$program]['lang']['customer_edit_labels']);
    $b = explode('|', $_SESSION[$program]['lang']['customer_edit_buttons']);
    
    if (isset($_POST['action']) AND $_POST['action']==$_SESSION[$program]['lang']['edit_client_name']
        AND isset($_POST['confirm']) AND $_POST['confirm']==$b[3]) {
           
            $client_bic_found = true;
            $client_name_found = true;
            
            foreach ($t as $value) {
                // Проверяем уникальность нового имени клиента!
                $no_name = true;
                $sql = '
                    SELECT 
                        count(*)
                    FROM 
                        `Customers`
                    WHERE
                        `Customers`.`CustomerName`="'.addslashes($value[1]).'"
                ;';
                $row = fetch_row_from_sql($sql);
                if ($row[0]>0) $no_name = false;
                
                if ( $no_name == true ) {
                    // Если нормально, то обновляем
                    $sql = '
                        UPDATE `cashmaster`.`Customers`
                        SET
                            `CustomerName` = "'.addslashes($value[1]).'"
                        WHERE `CustomerCode` = "'.addslashes($value[0]).'"
                    ;';
                    //echo $sql;
                    do_sql($sql);
                    $success[count($success)] = $_SESSION[$program]['lang']['client_name_was_updated'].': '.$value[0].', '.$value[1];
                    $warning = array();
                    $client_bic_found = $client_bic_found AND true;
                    $client_name_found = $client_name_found AND true;
                } else {
                    // если ненормально то сообщаем что обновление невозможно из-за неуникальности имени
                    $error[count($error)] = $_SESSION[$program]['lang']['name_exist_cannot_update'].': '.$value[1];
                    $client_bic_found = $client_bic_found AND false;
                    $client_name_found = $client_name_found AND false;
                };
            };
        
    } else {
        if ( count($t) > 0 ) {


            $table['header'] = array(
                $l[2],
                $l[11],
                $l[12]
            );
            $table['align'] = array( 'center','left','left');
            $table['data'] = array();
            foreach ($t as $value) {

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
                         `Customers`.`CustomerCode`="'.addslashes($value[0]).'"
                ;');
                $table['data'][count($table['data'])] = array( 
                                                            $client['CustomerCode'],
                                                            $client['CustomerName'],
                                                            $value[1]
                                                        );
            };
            $table['title'] = $l[13];
            $table['width'] = array( 150,300,300);
            ?>
            <form role="form" method="POST">
                <?php
                    include_once 'app/view/draw_select_table.php';
                    draw_select_table($table);
                ?>
                    <br/>
                    <br/>
                    <a class="btn btn-primary btn-large" href="?c=taskrecalc_file_view&id=<?php echo urlencode($_GET['id']); ?>">
                        <?php echo  $_SESSION[$program]['lang']['cancel']; ?>
                    </a>
                    <input 
                           type="submit"
                           class="btn btn-large btn-warning"
                           name="confirm" 
                           value="<?php echo htmlfix($b[3]); ?>"/>
                    <input type="hidden" 
                           name="action"
                           value="<?php echo $_POST['action'] ?>"/>
                    <input type="hidden" 
                           name="wrong_clients"
                           value="<?php echo $_POST['wrong_clients'] ?>"/>
                </form>
            <?php
            exit;
        };
    };