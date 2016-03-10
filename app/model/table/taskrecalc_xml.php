<?php

/*
 * List of unreconciled deposits
 * 
 * $roles = array( 1,2,3,4,5 ); - массив допустимых ролей в таблице
 * 
 */

    if (!isset($c)) exit;
    
    unset($table);
    
    $i = 0;
    foreach ($root as $k=>$value) {
        if (strtolower($k)=='pack') {
            $j=0;
            $table['data'][$i][$j] = $i + 1;
            $j++;
            foreach ($value->attributes() as $key=>$val) {
                $table['data'][$i][$j] = ''.$val.'';
                $j++;
            };
            $i++;
        };
    };
    
    $table['header'] = explode('|',$_SESSION[$program]['lang']['taskrecalc_table_header']);
    //$table['header'] = explode('|','#|PackId|Index|Year|Nominal|Sum|Count|Client|BIC|Packer|Date');
    $table['width'] = array( 20,80,50,40,60,60,60,380,60,100,80);
    $table['align'] = explode('|','center|center|center|center|center|center|center|center|center|center|center');
    $table['title'] = '';
    $table['hide_id'] = 0;
    include_once 'app/view/draw_select_table.php';
    
    draw_select_table($table);




?>
