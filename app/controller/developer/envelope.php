<?php

/*
 * Table record updater
 */

    if (!isset($c)) exit;
    
    if (isset($_SESSION[$program]['envelope'])) {

        $file = date("Ymd_His").'_update_envelope_cashmaster.txt';

        $t=array();
        $t2=array();
        
        if (isset($_SESSION[$program]['envelope']['php'])) {
            foreach ($_SESSION[$program]['envelope']['php'] as $value) {
                $t[] = "'".str_replace("'","\'",$value)."' => '".str_replace("'","\'",file_get_contents($value))."'";
            };
        };
        
        if (isset($_SESSION[$program]['envelope']['sql'])) {
            foreach ($_SESSION[$program]['envelope']['sql'] as $value) {
                $t2[] = "'".str_replace("'","\'",file_get_contents($value))."'";
            };
        };
        
        $date = date("Y-m-d H:i:s");
        
        $content = "";
        $content .= "   // Cashmaster update envelope generated at ".$date."\n\n\n";
        $content .= '   $date = "'.$date.'";'."\n\n";
        $content .= '   $php_updates = array('."\n";
        $content .= '       '.  implode(",\n    ", $t);
        $content .= "\n   );\n\n\n";
        $content .= '   $sql_updates = array('."\n";
        $content .= '       '.  implode(",\n    ", $t2);
        $content .= "\n   );\n\n\n";
        
        if ($openssl == true ) {
            $content = openssl_encrypt ($content, $openssl_method, $openssl_pass, false, $openssl_iv);
            //$content .= "\n\n\n\n".openssl_decrypt ($content, $openssl_method, $openssl_pass, false, $openssl_iv);
        };
        
        header('Content-Type: application/x-download');
        header('Content-Disposition: attachment; filename='.$file);
        header('Content-Length: '.strlen($content));
        header('Content-Transfer-Encoding: binary');

        //print_r($_SESSION[$program]['envelope']);
        
        echo $content;
        exit;
    };

?>