<?php

/*
 * List of unreconciled deposits
 */

    if (!isset($c)) exit;

        
    ?>
    <script>
        function order_by(name,order) {
            $('input#order_by').attr('value',name);
            $('input#order_type').attr('value',order);
            $('form#order').submit();
        }
        function open_client(elem) {
            window.location.replace('?c=client_edit&id=' + elem.id);
        };
    </script>
    <?php
    
    if(isset($_POST['code']) AND isset($_POST['name'])) {
        $clients = get_array_from_sql('
            SELECT
                `Customers`.`CustomerId`,
                `Customers`.`CustomerName`,
                `Customers`.`CustomerCode`,
                `Customers`.`CustomerKPCode`,
                `Customers`.`CustomerContactPost`,
                `Customers`.`CustomerContactName`,
                `Customers`.`CustomerPhone1`,
                `Customers`.`CustomerPhone2`,
                IF(`Customers`.`LogicallyDeleted`=1,"X","")
            FROM 
                `cashmaster`.`Customers`
            WHERE
                #`Customers`.`LogicallyDeleted`="0"
                `Customers`.`CustomerName` like "%'.  addslashes($_POST['name']).'%"
                AND `Customers`.`CustomerCode` like "%'.  addslashes($_POST['code']).'%"
            '.$order.'
        ;');            
    } else {
        $clients = get_array_from_sql('
            SELECT
                `Customers`.`CustomerId`,
                `Customers`.`CustomerName`,
                `Customers`.`CustomerCode`,
                `Customers`.`CustomerKPCode`,
                `Customers`.`CustomerContactPost`,
                `Customers`.`CustomerContactName`,
                `Customers`.`CustomerPhone1`,
                `Customers`.`CustomerPhone2`,
                IF(`Customers`.`LogicallyDeleted`=1,"X","")
            FROM 
                `cashmaster`.`Customers`
            #WHERE
                #`Customers`.`LogicallyDeleted`="0"
            '.$order.'
        ;');            
    };

    
    ($_POST['order_by']=='CustomerName' AND $_POST['order_type']=='ASC') ? '"CustomerName","DESC"':'"CustomerName","ASC"';
    unset($table);
    $table['data'] = $clients;
    $table['header'] = explode('|',$_SESSION[$program]['lang']['customers_table_header']);
    $table['width'] = array( 350,80,60,120,120,120,120,50);
    $table['align'] = array( 'left','left','center','center','center','center','center','center','center');
    $table['th_onclick']=array(
        'order_by('.(($_POST['order_by']=='CustomerName' AND $_POST['order_type']=='ASC') ? '\'CustomerName\',\'DESC\'':'\'CustomerName\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='CustomerCode' AND $_POST['order_type']=='ASC') ? '\'CustomerCode\',\'DESC\'':'\'CustomerCode\',\'ASC\'').');',
        ';',';',';',';',';',';');
    $table['tr_onclick']='open_client(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

?>
