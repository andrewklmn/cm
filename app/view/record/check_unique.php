<?php

/*
 * Check unique
 */

    if (!isset($c)) exit;
        
    $unique_fields = array();
    $unique_checks = array();
    // Поиск 
    foreach (get_array_from_sql('
                SHOW INDEXES FROM 
                    `'.$record['table'].'`
                WHERE 
                    `Key_name`<>"PRIMARY"
                    AND `Non_unique`=0
             ;') as $key => $value) {
        $unique_checks[$value[2]][]='`'.$value[4].'`="'.addslashes($_POST[$value[4]]).'"';
        
        // Находим подпись по данному полю
        foreach ($record['formula'] as $k => $v) {
            if($v==$value[4]){
                $unique_fields[$value[2]][]=$record['labels'][$k];
                break;
            };
        };
        
    };
    
    $not_unique_flag = false;
    $highlight_labels = array();
    foreach ($unique_checks as $key=>$value) {
        $row = fetch_row_from_sql('
            SELECT
                count(*)
            FROM
                `'.$record['table'].'`
            WHERE
                '.implode(' AND ', $value).'
        ;');
        if($row[0]>0) {
            $not_unique_flag = true;
            $not_unique[] = implode(' + ', $unique_fields[$key]);
            foreach ($unique_fields[$key] as $kkk=>$vvv) {
                $highlight_labels[] = $vvv;
            };
        };
    };
    
    $highlight = array();
    foreach ($record['labels'] as $key=>$value) {
        $highlight[$key] = 0;
        if (count($highlight_labels)>0) {
            foreach ($highlight_labels as $val) {
                if($value==$val) {
                    $highlight[$key] = 1;
                };
            };            
        };
    };
    
?>
