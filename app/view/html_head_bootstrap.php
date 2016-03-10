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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="Bootstrap_files/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="Bootstrap_files/bootstrap-responsive.css" rel="stylesheet">
    <script src="js/jquery-1.10.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="Bootstrap_files/bootstrap-tab.js"></script> 
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->
  </head>
  <body>