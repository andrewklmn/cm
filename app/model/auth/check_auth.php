<?php  
    
    function check_auth($user,$pass,$users){
        
        global $program, $events;        
        foreach ($users as $key=>$value) {
            
            if ($user==$value['UserLogin']) {
                
                include 'app/controller/common/set_systemconfiguration.php';
                
                // Проверяем можно ли заходить с этого IP пользователю
                $row = fetch_row_from_sql('
                    SELECT
                        count(*)
                    FROM 
                        `cashmaster`.`UsersIP`
                    WHERE
                        `UsersIP`.`UserId`="'.addslashes($value['UserId']).'"
                        AND ( `UsersIP`.`IP`="'.$_SERVER['REMOTE_ADDR'].'"
                               OR `UsersIP`.`IP`="%")
                ;');

                if ($row[0]==0 
                        AND $_SESSION[$program]['SystemConfiguration']['FastenUserToIp']=="1"
                        AND $user<>'developer') {
                    // пользователю нельзя 
                    set_log_attempts($value['UserId'], 0);
                    $data['error'] = $_SESSION[$program]['lang']['auth_user_ip_is_not_allowed'];
                    system_log('IP is not allowed for '.$_POST['user'].' from IP: '.$_SERVER['REMOTE_ADDR']);
                    include 'app/view/login.php';
                    unset($_SESSION[$program]['auth']);
                    exit;
                };
                // увеличиваем количество неудачных попыток на 1
                set_log_attempts($value['UserId'], ( (int)$value['BadLogAttempts'] + 1 ));
                if (((int)$value['BadLogAttempts'] + 1) > (int)$value['LoggingAttemptsLimit']) {
                    set_log_attempts($value['UserId'], 0);
                    include_once 'app/model/auth/block_user_id.php';
                    block_user_id($value['UserId']);
                    $users[$key]['UserIsBlocked']=1;
                    system_log($events[13].' '.$_POST['user'].' IP: '.$_SERVER['REMOTE_ADDR']);
                };
                if ($value['UserLogicallyDeleted']==1) {
                    // пользователь удалён
                    set_log_attempts($value['UserId'], 0);
                    $data['error'] = $_SESSION[$program]['lang']['auth_user_is_deleted'];
                    system_log($events[9].' '.$_POST['user'].' IP: '.$_SERVER['REMOTE_ADDR']);
                    include 'app/view/login.php';
                    unset($_SESSION[$program]['auth']);
                    exit;
                };
                if ($value['UserIsBlocked']==1) {
                    set_log_attempts($value['UserId'], 0);
                    // пользователь блокирован
                    $data['error'] = $_SESSION[$program]['lang']['auth_user_is_blocked'];
                    system_log($events[8].' '.$_POST['user'].' IP: '.$_SERVER['REMOTE_ADDR']);
                    include 'app/view/login.php';
                    unset($_SESSION[$program]['auth']);
                    exit;
                };
            };
            
            if (isset($_SESSION[$program]['code']) 
                    AND $user==$value['UserLogin'] 
                    AND $pass==MD5($value['UserPassword'].$_SESSION[$program]['code'])){
                
                $_SESSION[$program]['auth']=true;
                $_SESSION[$program]['user']=$user;
                $_SESSION[$program]['user_id']=$value['UserId'];
                $_SESSION[$program]['user_role_id']=$value['UserRoleId'];
                $_SESSION[$program]['user_post']=$value['UserPost'];

                
                if (preg_match('/^[a-zA-Z]/', $value['UserPatronymic'])) {
                    $initials = ($value['UserPatronymic']=='')?'':substr($value['UserPatronymic'],0,1).'.';
                } else {
                    $initials = ($value['UserPatronymic']=='')?'':substr($value['UserPatronymic'],0,2).'.';
                };
                if (preg_match('/^[a-zA-Z]/', $value['UserFirstName'])) {
                    $initials = substr($value['UserFirstName'],0,1).'.'.$initials;
                } else {
                    $initials = substr($value['UserFirstName'],0,2).'.'.$initials;
                };
                
                $_SESSION[$program]['user_fio']=$value['UserFamilyName'].' '.$initials;
                set_log_attempts($value['UserId'], 0);
                system_log($events[1].' '.$_SESSION[$program]['user_fio'].' IP: '.$_SERVER['REMOTE_ADDR']);
                return true;
            };
        };
        return false;
    
    };
?>
