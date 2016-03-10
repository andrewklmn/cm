<?php

/*
 * ������ ������� htmlspecialchars()
 */
        function htmlfix($text){
            return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        }
?>
