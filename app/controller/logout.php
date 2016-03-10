<?php

/*
 * Logout controller
 */

    if (!isset($c)) exit;
    
    system_log($events[2].' '.$_SESSION[$program]['user_fio'].' IP: '.$_SERVER['REMOTE_ADDR']);
    unset($_SESSION[$program]['auth']);
    unset($_SESSION[$program]['UserConfiguration']);
    unset($_SESSION[$program]['SystemConfiguration']);
    unset($_SESSION[$program]['lang_loaded']);
    
    include 'app/view/login.php';
    exit;
    
?>
