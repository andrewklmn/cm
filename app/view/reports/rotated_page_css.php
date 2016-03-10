<?php

/*
 * CSS данные для поворота страниц
 * 
 */

    if (!isset($c)) exit;
    
    include_once 'app/model/get_browser_version.php';

?>

<style>
    @media screen {
        div.rotated {

            padding: 0px;
            margin: 0px;
            
            width:270mm;
        }
        table.rotated {
            padding: 0px;
            margin: 0px;
            width:270mm;
        }
    }
    
    
    @media print {
        div.rotated {
            
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
            filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=3)";
            
            width:270mm;
            height:185mm;
            <?php 
                if ($browser!='IE') { 
                    ?>
                        margin-top: 43mm;
                        margin-left: -43mm;
                        margin-right: -42mm;
                        margin-bottom: 42mm;            
                    <?php
                }; 
            ?>
        }
        table.rotated {
            padding: 0px;
            margin: 0px;
            width:270mm;
            height:185mm;
        }
    }
    
   @page {
     margin: 1cm 1cm 1cm 1cm;
     size: A4 portrait;
   }
   
    table {
        border-collapse: collapse;
    }
    
    table.rotated td {
        padding: 0px;
        margin: 0px; 
        vertical-align: top;
        border: none;
    }
</style>