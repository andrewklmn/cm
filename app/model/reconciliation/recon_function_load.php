<?php

/*
 * Include all recon functions
 */

        if (!isset($c)) exit;
        
        $list = scandir('./app/model/reconciliation');
        foreach ($list as $name) {
            if($name!='recon_function_load.php'
                    AND $name!='..'
                    AND $name!='.'
                    AND $name!='index.php') {
                include_once './app/model/reconciliation'.'/'.$name;
            };
        };
        
?>
