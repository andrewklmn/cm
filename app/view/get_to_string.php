<?php

/*
 * $_GET[] to string encode
 */
    function get_to_string(){
        $string='?';
        $i=0;
        foreach ($_GET as $key=>$value) {
            if ($i==(count($_GET)-1)) {
                $string .= $key.'='.$value;
            } else {
                $string .= $key.'='.$value.'&';
            }
            $i++;
        }
        return $string;
    }
?>
