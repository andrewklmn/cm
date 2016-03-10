<?php 

/*
 * Application - Cashmaster 0.0.1
 */

    session_start();
    date_default_timezone_set('Europe/Moscow');
    //error_reporting( E_ALL );
    error_reporting( 0 );
    
    $msie9 = preg_match('/(?i)msie [8-9]/',$_SERVER['HTTP_USER_AGENT']);
    
    include 'app/view/htmlfix.php';
    
    // Global data =============================================================
    $program    = 'CashMaster';
    $data       = array();

    include 'app/model/directories.php';
    
    // DB connection ===========================================================
    include 'app/model/db/connection.php';

    // OpenSSL setup ===========================================================
    $openssl_pass = DB_PASS;
    $openssl_method = 'aes128';
    $openssl_iv = "CPI CashMaster 1";
    $openssl = true;   // FALSE - Отключено шифрование апдейтов
    
    // Loads system configuration into session if it was not loaded
    if (!isset($_SESSION[$program]['SystemConfiguration'])) {
        include 'app/controller/common/set_systemconfiguration.php';
    };
    
    
    // System localization =====================================================
    if (!isset($_SESSION[$program]['lang'])) {
        include 'app/model/lang/'.strtolower($_SESSION[$program]['SystemConfiguration']['DefaultLanguage']).'.php';
    };
    

    // Autorization check ======================================================
    include 'app/controller/auth.php';
    
    
    // Load user localization if it was not loaded==============================
    if (!isset($_SESSION[$program]['lang_loaded'])) {
        include 'app/model/lang/'.strtolower($_SESSION[$program]['UserConfiguration']['InterfaceLanguage']).'.php';
        $_SESSION[$program]['lang_loaded'] = true;
    };
    
    // Controller start ========================================================
    $c = (isset($_GET['c'])) ? preg_replace("/[^a-z\_\/0-9]/",'', $_GET['c']) : 'index';
    if ($c=='logout') {
        include 'app/controller/logout.php'; 
        exit;
    };
    
    // Если сервисный режим, то переходим на контроллер восстановления файлов
    if ($_SESSION[$program]['SystemConfiguration']['FilesCorrupted']=="1"
            AND $_SESSION[$program]['user_role_id']!='5') {
        $c = 'restore_scripts';
    };
    
    
    switch ($_SESSION[$program]['user_role_id']) {
        case '1':
            $c = 'admin/'.$c;
            break;
        case '2':
            $c = 'supervisor/'.$c;
            break;
        case '3':
            $c = 'operator/'.$c;
            break;
        case '4':
            $c = 'inspector/'.$c;
        break;
        case '5':
            $c = 'developer/'.$c;
        break;
    }
    
    if(file_exists('app/controller/'.$c.'.php')) {
        include 'app/controller/'.$c.'.php'; 
    } else {
        include './app/view/under_development.php';
        //echo 'ERROR 404 <br/> controller "'.htmlspecialchars($c).'" was not found';
        //exit;
    };
  
?>