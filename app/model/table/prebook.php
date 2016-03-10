<?php

/*
 * List of prebook
 */

    if (!isset($c)) exit;

        
    $sql = '
        SELECT 
            `Prebook`.`Id`,
            `Prebook`.`PackId`,
            `Prebook`.`Filename`
        FROM `cashmaster`.`Prebook`
        WHERE
            `CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
             AND `Prepared` = 0
             AND `CustomerApproved` = 1
    ;';
    $row = get_array_from_sql($sql);

    if(count($row)>0) {
        $where = array();
        foreach ($row as $key => $value) {
            $where[] = 'DepositRecs.DepositRecId='.$value[0];
        };
        unset($table);
        $sql = '
            SELECT 
                `Prebook`.`Id`,
                `Prebook`.`PackId`,
                `Prebook`.`Filename`
            FROM `cashmaster`.`Prebook`
            WHERE
                `CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                AND `Prepared` = 0
                AND `CustomerApproved` = 1
        ;';

        
        $table['data'] = get_array_from_sql($sql);
        $table['header'] = explode('|', $_SESSION[$program]['lang']['prebook_header']);
        $table['width'] = array( 200,580);
        $table['align'] = array( 'center','center','left');
        $table['th_onclick']=array(';',';',';',';');
        $table['tr_onclick']='open_recs(this.parentNode);';
        $table['title'] = $_SESSION[$program]['lang']['prebook'];
        $table['hide_id'] = 1;
        //$table['selector'] = true;
        include_once 'app/view/draw_select_table.php';
        draw_select_table($table);
        unset($table);
    };


?>
