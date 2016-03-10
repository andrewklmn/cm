<?php

/*
 * Table record updater
 */

    if (!isset($c)) exit;
    
    
   
    $cashmaster_root = 'app';
    $path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']).'/';
    $path = '';
    $file = 'app_files_archive_cashmaster_'.date("Ymd_His").'.zip';
    
    // создаём архив
    exec("zip -r ".$path.$file." ".$cashmaster_root );
    $size = filesize($path.$file);
    $content = file_get_contents($path.$file);
    // удаляем архив
    unlink($path.$file);
    
    header('Content-Type: application/x-download');
    header('Content-Disposition: attachment; filename='.$file);
    header('Content-Length: '.$size);
    header('Content-Transfer-Encoding: binary');
    
    echo $content;
    exit;

     
?>