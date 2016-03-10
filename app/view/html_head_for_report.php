<?php

/*
 * HTML5 
 */

    if (!isset($data['title'])) {
        $data['title'] = '';
    }
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title><?php echo $data['title'];?></title>
    <style>
        body {
            /*font-family: serif;*/
            width: 800px;
        }
        table.report {
            border-collapse: collapse;
        }
        table.width100 {
            width: 100%;
        }
        table.report th {
            border: black solid 1px;
            font-size: 9pt;
            font-family: monospace;
            padding: 2px;
        }
        table.report td {
            border: black solid 1px;
            font-size: 9pt;
            font-family: monospace;
            padding: 2px;
        }
        th.right {
            text-align: right;
        }
        table.noborder th {
            border: none;
            text-align: right;
        }
        table.noborder td {
            border: none;
            text-align: left;
        }
    </style>
    <script src="js/jquery-1.10.1.min.js"></script>
  </head>
  <body 
      onkeyup="
        switch (event.keyCode) {
            case 27:
                 window.close();
            break;
            case 13:
                 window.print();
                 window.close();
            break;
        };">