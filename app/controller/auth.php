<?php

/*
 * Autorizathion controller
 */

    include 'app/model/auth/check_auth.php';
    include 'app/model/auth/users.php';
    include 'app/model/auth/get_password_expired_date.php';
    include 'app/model/auth/get_user_config.php';
    include 'app/model/auth/set_log_attempts.php';
    include 'app/model/system_log.php';
    
    
    // Check AUTH flag in $_SESSION
    if (!isset($_SESSION[$program]['auth'])) {
        if (isset($_POST['user']) AND isset($_POST['pass'])) {
            // Check authorization
            if (!check_auth($_POST['user'], $_POST['pass'],$users)) {
                $data['error'] = $_SESSION[$program]['lang']['wrong_login_pass'];
                //system_log('Just for test');
                system_log($events[10].' '.$_POST['user'].' IP: '.$_SERVER['REMOTE_ADDR']);
                include 'app/view/login.php';
                //print_r($_SESSION);
                exit;
            };
            
        } else {
            
            // Go to login page
            include 'app/view/login.php';
            exit;
        };

    };
    
    $_SESSION[$program]['UserConfiguration'] = get_user_config($_SESSION[$program]['user_id']);
    
    if ($_SESSION[$program]['user_role_id']!=5) {
		
        
        if ($_SESSION[$program]['UserConfiguration']['UserIsBlocked']=="1") {
			// user is blocked
            include 'app/view/html_header.php';
            echo htmlfix($_SESSION[$program]['lang']['auth_user_is_blocked']);
            system_log($events[12].' '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' IP: '.$_SERVER['REMOTE_ADDR']);
            unset($_SESSION[$program]['auth']);
            exit;
        };

        if ($_SESSION[$program]['UserConfiguration']['UserLogicallyDeleted']=="1") {
            // user is deleted
            $c='change_password';
            $data['error'] = $_SESSION[$program]['lang']['auth_user_is_deleted'];
            system_log($events[11].' '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' IP: '.$_SERVER['REMOTE_ADDR']);
            include 'app/view/login.php';
            unset($_SESSION[$program]['auth']);
            exit;
        };

        if ($_SESSION[$program]['UserConfiguration']['ChangePassword']=="1") {
            $c='change_password';
            $data['error'] = $_SESSION[$program]['lang']['auth_change_password'];
            include 'app/controller/common/change_password.php';
            exit;
        };

        // Check EXPIRED PASSWORD
        if ( strtotime(get_password_exired_date($_SESSION[$program]['UserConfiguration']['UserId'])) < time()
                AND $_SESSION[$program]['UserConfiguration']['ChangePassword']!=1 ) {
            // блокируем пользователя так как пароль просрочен
            include_once 'app/model/auth/block_user_id.php';
            block_user_id($_SESSION[$program]['UserConfiguration']['UserId']);
            system_log($events[7].' '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' IP: '.$_SERVER['REMOTE_ADDR']);
            $data['error'] = $_SESSION[$program]['lang']['auth_user_is_blocked'];
            include 'app/view/login.php';
            unset($_SESSION[$program]);
            exit;
        };    
    };
?>