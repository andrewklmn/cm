<?php

/*
 * List of unreconciled deposits
 * 
 * $roles = array( 1,2,3,4,5 ); - массив допустимых ролей в таблице
 * 
 */

    if (!isset($c)) exit;
    
    ?>
    <script>
        function order_by(name,order) {
            $('input#order_by').attr('value',name);
            $('input#order_type').attr('value',order);
            $('form#order').submit();
        }
        function open_user(elem) {
            window.location.replace('?c=signer_edit&id=' + elem.id);
        };
    </script>
    <?php
    
    if(isset($_POST['ExternalUserName']) AND isset($_POST['ExternalUserPost'])) {
        $users = get_array_from_sql('
            SELECT
                `ExternalUsers`.`ExternalUserId`,
                `ExternalUsers`.`ExternalUserName`,
                `ExternalUsers`.`ExternalUserPost`,
                Phone
            FROM 
                `ExternalUsers`
            WHERE
                ExternalUserName like "%'.addslashes($_POST['ExternalUserName']).'%"
                AND ExternalUserPost like "%'.addslashes($_POST['ExternalUserPost']).'%"
            '.$order.'
        ;');            
    } else {
        $users = get_array_from_sql('
            SELECT
                `ExternalUsers`.`ExternalUserId`,
                `ExternalUsers`.`ExternalUserName`,
                `ExternalUsers`.`ExternalUserPost`,
                Phone
            FROM 
                `ExternalUsers`
            '.$order.'
        ;');            
    };

    
    ($_POST['order_by']=='CustomerName' AND $_POST['order_type']=='ASC') ? '"CustomerName","DESC"':'"CustomerName","ASC"';
    
    unset($table);
    $table['data'] = $users;
    $table['header'] = explode('|',$_SESSION[$program]['lang']['signers_table_header']);
    $table['width'] = array( 200,400,200,100);
    $table['align'] = array( 'left','left','left','left','center');
    $table['th_onclick']=array(
        'order_by('.(($_POST['order_by']=='ExternalUserName' AND $_POST['order_type']=='ASC') ? '\'ExternalUserName\',\'DESC\'':'\'ExternalUserName\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='ExternalUserPost' AND $_POST['order_type']=='ASC') ? '\'ExternalUserPost\',\'DESC\'':'\'ExternalUserPost\',\'ASC\'').');',
        ';',';',';',';',';',';');

    $table['tr_onclick']='open_user(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);




?>
