<?php

/*
 * Таблица списка типов машин
 */

    if (!isset($c)) exit;
    
?>
    <script>
        function open_valuable_type(elem){
            window.location.replace('?c=user_ip_edit&id=' + elem.id);            
        };
    </script>
<?php
    
    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
            `UsersIP`.`Id`,
            `UserConfiguration`.`UserLogin`,
            `UsersIP`.`IP`
        FROM 
            `cashmaster`.`UsersIP`
        LEFT JOIN
            UserConfiguration ON UserConfiguration.UserId = `UsersIP`.`UserId`
        WHERE
            `UsersIP`.`UserId`="'.addslashes($_GET['id']).'"
        ORDER BY 
            `UsersIP`.`IP` ASC
    ;');      
    $table['header'] = explode('|', $_SESSION[$program]['lang']['user_ips_header']);
    $table['width'] = array( 300,300);
    $table['align'] = explode('|','center|center|center');  // Первое значение всегда игнорируется если спрятан ID
    $table['tr_onclick']='open_valuable_type(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once 'app/view/draw_select_table.php';
    draw_select_table($table);

    
?>
<script>

</script>