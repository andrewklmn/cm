<?php

/*
 * Table record updater
 */

    if (!isset($c)) exit;
    
    $t = explode('/', substr($_GET['n'],1));
    $file = $t[count($t)-1];
    unset($t[count($t)-1]);
    $path = implode('/', $t);
    $cashmaster_root = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
    
    
    $size = filesize($cashmaster_root.substr($_GET['n'],1));
    header('Content-Type: application/x-download');
    header('Content-Disposition: attachment; filename='.$file);
    header('Content-Length: '.$size);
    header('Content-Transfer-Encoding: binary');

    $homepage = file_get_contents($cashmaster_root.substr($_GET['n'],1));
    echo $homepage;
    exit;

     
?>