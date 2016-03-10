<?php

/*
 * Table record adder
 */

    if (!isset($c)) exit;
    
    if (isset($_POST['last_index'])
            AND isset($_POST['key'])
            AND isset($_POST['table'])) {

        $fields = array();
        $values = array();
        foreach ($_POST as $key => $value) {
            if (substr($key,0,5)=="data-") {
                if (substr($key,5)!=$_POST['key']) {
                    $f=  explode('|', substr($key,5));
                    if ($f[1]!='tinyint(4)') {
                        switch ($f[1]) {
                            case 'timestamp':
                                break;
                            case 'bigint(20)': case 'int(11)':
                                $values[]='"'.addslashes($value).'"';
                                $fields[]=$f[0];
                                $format[]=$f[1];
                                break;
                            default:
                                $values[]='"'.addslashes($value).'"';
                                $fields[]=$f[0];
                                $format[]=$f[1];
                                break;
                        }
                    } else {
                        if($value=='on') {
                            $values[]=1;
                            $fields[]=$f[0];
                            $format[]=$f[1];
                        } else {
                            $values[]=0;
                            $fields[]=$f[0];
                            $format[]=$f[1];
                        }
                    }
                }
            }
        };
        
        // Lock table for check and update =========================================
        $sql = 'LOCK TABLES '.addslashes($_POST['table']).' WRITE;';
        do_sql($sql);
        
        // Check last index in table ===========================================
        $sql = '
            SELECT
                MAX('.addslashes($_POST['key']).')
            FROM
                '.addslashes($_POST['table']).'
        ;';
        $r = fetch_row_from_sql($sql);
        if ($r[0]==$_POST['last_index']) {
            // Insert new record ===============================================
            //do_sql('SET foreign_key_checks = 0;');
            $sql = '
                INSERT INTO '.$_POST['table'].' 
                    ('.implode(',',$fields).') 
                        VALUES ('.implode(',',$values).');
            ';
            do_sql($sql);
            //echo $sql;
            $data['success'] = "New record was added.";
            include './app/view/success_message.php';
        } else {
            $data['error'] = "New records in the base was added by another user. Check new records and try again.";
            include './app/view/error_message.php';
        }
        $sql = 'UNLOCK TABLES;';
        do_sql($sql);
            
    };
    
     
?>