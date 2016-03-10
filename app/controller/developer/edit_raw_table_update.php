<?php

/*
 * Table record updater
 */

    if (!isset($c)) exit;

    include './app/view/html_header.php';
        
    
    $t = explode('||', $_POST['fields']);
    $values = explode('|', $_POST['values']);
    $oldvalues = explode('|', $_POST['oldvalues']);
    
    $fields=array();
    $format = array();
    foreach ($t as $key=>$value) {
        $f = explode('|', $value);
        $fields[] = $f[0];
        $format[] = $f[1];
    };
    
    
    // Lock table for check and update =========================================
    $sql = 'LOCK TABLES '.addslashes($_POST['table']).' WRITE;';
    do_sql($sql);
    
    // Check old_values ========================================================
    $sql = '
        SELECT 
            '.implode(',', $fields).'
        FROM
            '.addslashes($_POST['table']).'
        WHERE
            '.$_POST['key_name'].'="'.addslashes($_POST['key_value']).'"
    ;';
    $oldvalue = fetch_row_from_sql($sql);
    if (implode('|',$oldvalues)!=  implode('|', $oldvalue)) {
        // records was changed 
        $sql = 'UNLOCK TABLES;';
        do_sql($sql);
        echo 1;
        echo implode('|',$oldvalue);
        exit;
    };
    
    // Generate update parameter string ========================================
    $params = array();
    foreach ($fields as $key=>$value) {
        switch ($format[$key]) {
            case 'timestamp':
                break;
            case 'bigint(20)': case 'tinyint(4)': case 'int(11)':
                if ($values[$key]=='') {
                    $params[]=$value.'="'.addslashes($values[$key]).'"';
                } else {
                    $params[]=$value.'="'.addslashes($values[$key]).'"';                    
                };
                break;
            default:
                $params[]=$value.'="'.addslashes($values[$key]).'"';
                break;
        }
    };
    
    // Update record ===========================================================
    $sql = ' 
        UPDATE 
            '.addslashes($_POST['table']).' 
        SET 
            '.implode(',', $params).'
        WHERE 
            '.$_POST['key_name'].'="'.addslashes($_POST['key_value']).'"
    ;';
    //echo $sql;
    //exit;
    do_sql($sql);
    $sql = '
        SELECT
            *
        FROM
            '.addslashes($_POST['table']).' 
        WHERE
            '.$_POST['key_name'].'="'.addslashes($_POST['key_value']).'"
    ;';
    $record = fetch_row_from_sql($sql);
    $sql = 'UNLOCK TABLES;';
    do_sql($sql);

    echo '0';
    echo implode('|',$record);  
?>