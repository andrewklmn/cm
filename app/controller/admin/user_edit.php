<?php

/*
 * Редактирование кассы пересчета для работника
 */

        if (!isset($c)) exit;

        $roles = array(1,2,3,4); // массив редактируемых ролей
        
        $l = explode('|', $_SESSION[$program]['lang']['user_edit_labels']);
        $b = explode('|', $_SESSION[$program]['lang']['user_edit_buttons']); 
        $s = explode('|', $_SESSION[$program]['lang']['user_edit_states']); 
        $w = explode('|', $_SESSION[$program]['lang']['user_edit_warnings']); 
        $r = explode('|', $_SESSION[$program]['lang']['user_delete_results']); 

        $data['title'] = $l[0];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';
        //include './app/view/update_record.php';
        include './app/model/check_access_to_roles.php';
        
        include 'app/controller/common/set_systemconfiguration.php';
?>
<style>
    table.user td {
        text-align: left;
        padding-left: 5px;
        padding-top: 3px;
    }
    table.user th {
        text-align: right;
        padding-left: 5px;
        padding-top: 3px;
    }
</style>
<?php

        
        if(isset($_POST['delete']) AND $_POST['delete']=='delete') {
            $user = get_user_config($_REQUEST['id']);
            if (!isset($_POST['confirm'])) {
                // Требуем подтверждения удаления
                ?>
                    <div class="container">
                        <h3><?php echo htmlfix($w[2]); ?>:</h3>
                        <table class='user'>
                            <tr>
                                <th><?php echo htmlfix($l[5]); ?>:</th>
                                <td><?php 
                                        echo htmlfix($user['UserLogin']); 
                                ?></td>
                            </tr>
                            <tr>
                                <th><?php echo htmlfix($l[13]); ?>:</th>
                                <td><?php 
                                        echo htmlfix($user['UserFamilyName']); 
                                ?></td>
                            </tr>
                            <tr>
                                <th><?php echo htmlfix($l[14]); ?>:</th>
                                <td><?php 
                                        echo htmlfix($user['UserFirstName']); 
                                ?></td>
                            </tr>
                            <tr>
                                <th><?php echo htmlfix($l[15]); ?>:</th>
                                <td><?php 
                                        echo htmlfix($user['UserPatronymic']); 
                                ?></td>
                            </tr>
                            <?php 
                                if ($_SESSION[$program]['SystemConfiguration']['UseGenitiveName']==1) {
                                    ?>
                                        <tr>
                                            <th><?php echo htmlfix($l[19]); ?>:</th>
                                            <td><?php 
                                                    echo htmlfix($user['GenetiveName']); 
                                            ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo htmlfix($l[20]); ?>:</th>
                                            <td><?php 
                                                    echo htmlfix($user['InstrName']); 
                                            ?></td>
                                        </tr>
                                    <?php 
                                };
                            ?>
                            <tr>
                                <th><?php echo htmlfix($l[1]); ?>:</th>
                                <td><?php 
                                        echo htmlfix($user['Phone']); 
                                ?></td>
                            </tr>
                            <tr>
                                <th><?php echo htmlfix($l[2]); ?>:</th>
                                <td><?php 
                                        echo htmlfix($user['UserPost']); 
                                ?></td>
                            </tr>
                            <tr>
                                <th><?php echo htmlfix($l[3]); ?>:</th>
                                <td><?php 
                                        echo htmlfix($user['RoleLabel']); 
                                ?></td>
                            </tr>
                        </table>
                        <br/><br/>
                            <form 
                                    style="padding: 0px;margin: 0px;" 
                                    method="POST" 
                                    action="?c=user_edit&id=<?php echo htmlfix($_GET['id']); ?>">
                                <input type="hidden" name="delete" value="delete"/>
                                <input type="hidden" name="id" value="<?php echo htmlfix($_GET['id']); ?>"/>
                                <input 
                                    type="button"
                                    onclick='window.location.replace("?c=users");'
                                    class="btn btn-primary btn-large" value="<?php echo htmlfix($b[2]); ?>"/>
                                <input
                                    type="submit"
                                    class="btn btn-danger btn-large" name="confirm" value="<?php echo htmlfix($b[3]); ?>"/>
                            </form>
                    </div>
                <?php
                exit;
                
            } else {
                
                do_sql('LOCK TABLES UserConfiguration WRITE, SystemLog WRITE;');
                $row = fetch_row_from_sql('
                    SELECT
                        `UserConfiguration`.`UserIsBlocked`
                    FROM 
                        `cashmaster`.`UserConfiguration`
                    WHERE
                        `UserConfiguration`.`UserId`="'.  addslashes($_REQUEST['id']).'"
                ;');

                if ($row[0]=="1") {
                    do_sql('
                        UPDATE 
                            `cashmaster`.`UserConfiguration`
                        SET
                            `UserLogicallyDeleted` = "1"
                        WHERE 
                            `UserId` = "'.addslashes($_REQUEST['id']).'"                    
                    ;');
                    // выводим сообщение что пользователь удален.
                    system_log('User login: '.$user['UserLogin']
                            .' user: '.$user['UserFamilyName'].' '.$user['UserFirstName']
                            .'  '.$user['UserPatronymic'].' post: '.$user['UserPost']
                            .' was deleted by: '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' IP: '.$_SERVER['REMOTE_ADDR']);
                    
                    
                    $data['success'] = $r[0];
                    include 'app/view/success_message.php';
                    ?>
                        <div class="container">
                            <br/>
                            <br/>
                                <button 
                                    onclick='window.location.replace("?c=users");'
                                    class="btn btn-primary btn-large"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>    
                        </div>
                    <?php                    
                    exit;
                } else {
                    // выводим сообщение что пользователь удален.
                    $data['error'] = $r[1];
                    include 'app/view/error_message.php';
                    ?>
                        <div class="container">
                            <br/>
                            <br/>
                                <button 
                                    onclick='window.location.replace("?c=users");'
                                    class="btn btn-primary btn-large"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
                        </div>
                    <?php       
                    exit;
                };            
                do_sql('UNLOCK TABLES;');  
                exit;
            };
        };

        
        if (isset($_REQUEST['id']) AND $_REQUEST['id']>0) {
            $user = get_user_config($_REQUEST['id']);
            // ID задан и больше 0
            if (check_access_to_roles($_REQUEST['id'], $roles)==false
                    OR $_REQUEST['id']==$_SESSION[$program]['UserConfiguration']['UserId']
                    OR $user['UserLogicallyDeleted']=="1") {
                $data['error'] = 'У вас нет прав редактировать этого пользователя';
            };
        } else {
            $data['error'] = 'Неправильный запрос на редактирование';
        };
        if(isset($data['error'])) {
            echo '<div class="container">';
            include 'app/view/error_message.php';
            ?>
                <button 
                    onclick='window.location.replace("?c=users");'
                    class="btn btn-primary btn-large"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
            <?php
            echo '</div>';
            exit;
        };
        
        if(isset($_POST['action']) AND $_POST['action']=='update') {
            $newvalue = explode('|',$_POST['new_data']);
            $oldvalue = explode('|',$_POST['old_data']);
            //Обновление данных
            //1. Проверяем совпадают ли старые данные с теми что в базе. 
            //   Если нет то сообщаем что запись изменил другой пользователь
            if ($_SESSION[$program]['SystemConfiguration']['UseGenitiveName']==1) {
                $row = fetch_row_from_sql('
                    SELECT
                        `UserConfiguration`.`UserFamilyName`,
                        `UserConfiguration`.`UserFirstName`,
                        `UserConfiguration`.`UserPatronymic`,
                        `UserConfiguration`.`GenetiveName`,
                        `UserConfiguration`.`InstrName`,
                        `UserConfiguration`.`UserPost`,
                        `UserConfiguration`.`Phone`
                    FROM 
                        `cashmaster`.`UserConfiguration`
                    WHERE
                        `UserConfiguration`.`UserId`="'.addslashes($_GET['id']).'"
                ;');
            } else {
                $row = fetch_row_from_sql('
                    SELECT
                        `UserConfiguration`.`UserFamilyName`,
                        `UserConfiguration`.`UserFirstName`,
                        `UserConfiguration`.`UserPatronymic`,
                        `UserConfiguration`.`UserPost`,
                        `UserConfiguration`.`Phone`
                    FROM 
                        `cashmaster`.`UserConfiguration`
                    WHERE
                        `UserConfiguration`.`UserId`="'.addslashes($_GET['id']).'"
                ;');                
            };
            if ($oldvalue != $row) {
                $w = explode('|',$_SESSION[$program]['lang']['record_edit_warning']);
                $data['error'] = $w[0];
                include 'app/view/error_message.php';
            } else {
                //2. Обновляем данные и продолжаем работу
                if ($_SESSION[$program]['SystemConfiguration']['UseGenitiveName']==1) {
                    do_sql('
                        UPDATE `cashmaster`.`UserConfiguration`
                        SET
                            `UserFamilyName` = "'.addslashes($newvalue[0]).'",
                            `UserFirstName` = "'.addslashes($newvalue[1]).'",
                            `UserPatronymic` = "'.addslashes($newvalue[2]).'",
                            `GenetiveName` = "'.addslashes($newvalue[3]).'",
                            `InstrName` = "'.addslashes($newvalue[4]).'",
                            `UserPost` = "'.addslashes($newvalue[5]).'",
                            `Phone` = "'.addslashes($newvalue[6]).'",
                            `UserIsBlocked` = "1"
                        WHERE
                            `UserConfiguration`.`UserId`="'.addslashes($_GET['id']).'"
                    ;');                    
                } else {
                    do_sql('
                        UPDATE `cashmaster`.`UserConfiguration`
                        SET
                            `UserFamilyName` = "'.addslashes($newvalue[0]).'",
                            `UserFirstName` = "'.addslashes($newvalue[1]).'",
                            `UserPatronymic` = "'.addslashes($newvalue[2]).'",
                            `UserPost` = "'.addslashes($newvalue[3]).'",
                            `Phone` = "'.addslashes($newvalue[4]).'",
                            `UserIsBlocked` = "1"
                        WHERE
                            `UserConfiguration`.`UserId`="'.addslashes($_GET['id']).'"
                    ;');
                };
                $user = get_user_config($_GET['id']);
                system_log('User login: '.$user['UserLogin']
                        .' name: '.$user['UserFamilyName'].' '.$user['UserFirstName']
                        .'  '.$user['UserPatronymic'].' post: '.$user['UserPost']
                        .' was editet by: '.$_SESSION[$program]['UserConfiguration']['UserLogin'].' IP: '.$_SERVER['REMOTE_ADDR']);

            };
        };
        
        $user = get_user_config($_REQUEST['id']);        
        
?>
<script>
var _0x11d7=['\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5A\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7A\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x2B\x2F\x3D','','\x63\x68\x61\x72\x41\x74','\x69\x6E\x64\x65\x78\x4F\x66','\x66\x72\x6F\x6D\x43\x68\x61\x72\x43\x6F\x64\x65','\x6C\x65\x6E\x67\x74\x68'];function base64_decode(data){var b64=_0x11d7[0];var o1,o2,o3,h1,h2,h3,h4,bits,i=0,enc=_0x11d7[1];do{h1=b64[_0x11d7[3]](data[_0x11d7[2]](i++));h2=b64[_0x11d7[3]](data[_0x11d7[2]](i++));h3=b64[_0x11d7[3]](data[_0x11d7[2]](i++));h4=b64[_0x11d7[3]](data[_0x11d7[2]](i++));bits=h1<<18|h2<<12|h3<<6|h4;o1=bits>>16&0xff;o2=bits>>8&0xff;o3=bits&0xff;if(h3==64){enc+=String[_0x11d7[4]](o1);} else {if(h4==64){enc+=String[_0x11d7[4]](o1,o2);} else {enc+=String[_0x11d7[4]](o1,o2,o3);} ;} ;} while(i<data[_0x11d7[5]]);;return enc;} ;x44591890y89008606='JTI1dTAwMGElMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDA2NiUyNXUwMDc1JTI1dTAwNmUlMjV1MDA2MyUyNXUwMDc0JTI1dTAwNjklMjV1MDA2ZiUyNXUwMDZlJTI1dTAwMjAlMjV1MDA3OCUyNXUwMDMxJTI1dTAwMzglMjV1MDAzMCUyNXUwMDM5JTI1dTAwMzclMjV1MDAzNyUyNXUwMDMxJTI1dTAwMzglMjV1MDA3OSUyNXUwMDMzJTI1dTAwMzklMjV1MDAzMCUyNXUwMDM3JTI1dTAwMzQlMjV1MDAzNiUyNXUwMDM0JTI1dTAwMzIlMjV1MDAyOCUyNXUwMDc0JTI1dTAwMjklMjV1MDAyMCUyNXUwMDdiJTI1dTAwMGElMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDcyJTI1dTAwNjUlMjV1MDA3NCUyNXUwMDc1JTI1dTAwNzIlMjV1MDA2ZSUyNXUwMDIwJTI1dTAwNzUlMjV1MDA2ZSUyNXUwMDY1JTI1dTAwNzMlMjV1MDA2MyUyNXUwMDYxJTI1dTAwNzAlMjV1MDA2NSUyNXUwMDI4JTI1dTAwNjQlMjV1MDA2NSUyNXUwMDYzJTI1dTAwNmYlMjV1MDA2NCUyNXUwMDY1JTI1dTAwNTUlMjV1MDA1MiUyNXUwMDQ5JTI1dTAwMjglMjV1MDA2MiUyNXUwMDYxJTI1dTAwNzMlMjV1MDA2NSUyNXUwMDM2JTI1dTAwMzQlMjV1MDA1ZiUyNXUwMDY0JTI1dTAwNjUlMjV1MDA2MyUyNXUwMDZmJTI1dTAwNjQlMjV1MDA2NSUyNXUwMDI4JTI1dTAwNjIlMjV1MDA2MSUyNXUwMDczJTI1dTAwNjUlMjV1MDAzNiUyNXUwMDM0JTI1dTAwNWYlMjV1MDA2NCUyNXUwMDY1JTI1dTAwNjMlMjV1MDA2ZiUyNXUwMDY0JTI1dTAwNjUlMjV1MDAyOCUyNXUwMDc0JTI1dTAwMjklMjV1MDAyOSUyNXUwMDI5JTI1dTAwMjklMjV1MDAzYiUyNXUwMDBhJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwN2QlMjV1MDAzYiUyNXUwMDBhJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMCUyNXUwMDIwJTI1dTAwMjAlMjV1MDAyMA==';eval(unescape(decodeURI(base64_decode(x44591890y89008606)))); eval(x18097718y39074642("SlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBMk5pVXlOWFV3TURjMUpUSTFkVEF3Tm1VbE1qVjFNREEyTXlVeU5YVXdNRGMwSlRJMWRUQXdOamtsTWpWMU1EQTJaaVV5TlhVd01EWmxKVEkxZFRBd01qQWxNalYxTURBM05TVXlOWFV3TURjd0pUSTFkVEF3TmpRbE1qVjFNREEyTVNVeU5YVXdNRGMwSlRJMWRUQXdOalVsTWpWMU1EQXlPQ1V5TlhVd01EWTFKVEkxZFRBd05tTWxNalYxTURBMk5TVXlOWFV3TURaa0pUSTFkVEF3TWprbE1qVjFNREF5TUNVeU5YVXdNRGRpSlRJMWRUQXdNR1FsTWpWMU1EQXdZU1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNakFsTWpWMU1EQTJPU1V5TlhVd01EWTJKVEkxZFRBd01qQWxNalYxTURBeU9DVXlOWFV3TURJMEpUSTFkVEF3TWpnbE1qVjFNREEyTlNVeU5YVXdNRFpqSlRJMWRUQXdOalVsTWpWMU1EQTJaQ1V5TlhVd01ESTVKVEkxZFRBd01tVWxNalYxTURBM05pVXlOWFV3TURZeEpUSTFkVEF3Tm1NbE1qVjFNREF5T0NVeU5YVXdNREk1SlRJMWRUQXdNakFsTWpWMU1EQXpaQ1V5TlhVd01ETmtKVEkxZFRBd00yUWxNalYxTURBeU1DVXlOWFV3TURJMEpUSTFkVEF3TWpnbE1qVjFNREEyTlNVeU5YVXdNRFpqSlRJMWRUQXdOalVsTWpWMU1EQTJaQ1V5TlhVd01ESTVKVEkxZFRBd01tVWxNalYxTURBMk1TVXlOWFV3TURjMEpUSTFkVEF3TnpRbE1qVjFNREEzTWlVeU5YVXdNREk0SlRJMWRUQXdNamNsTWpWMU1EQTJaaVV5TlhVd01EWmpKVEkxZFRBd05qUWxNalYxTURBM05pVXlOWFV3TURZeEpUSTFkVEF3Tm1NbE1qVjFNREEzTlNVeU5YVXdNRFkxSlRJMWRUQXdNamNsTWpWMU1EQXlPU1V5TlhVd01ESTVKVEkxZFRBd01qQWxNalYxTURBM01pVXlOWFV3TURZMUpUSTFkVEF3TnpRbE1qVjFNREEzTlNVeU5YVXdNRGN5SlRJMWRUQXdObVVsTWpWMU1EQXlNQ1V5TlhVd01EYzBKVEkxZFRBd056SWxNalYxTURBM05TVXlOWFV3TURZMUpUSTFkVEF3TTJJbE1qVjFNREF3WkNVeU5YVXdNREJoSlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNRGN6SlRJMWRUQXdOalVsTWpWMU1EQTNOQ1V5TlhVd01EVm1KVEkxZFRBd056Y2xNalYxTURBMk1TVXlOWFV3TURZNUpUSTFkVEF3TnpRbE1qVjFNREF5T0NVeU5YVXdNREk1SlRJMWRUQXdNMklsTWpWMU1EQXdaQ1V5TlhVd01EQmhKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01EYzJKVEkxZFRBd05qRWxNalYxTURBM01pVXlOWFV3TURJd0pUSTFkVEF3TmprbE1qVjFNREEyWlNVeU5YVXdNRGN3SlRJMWRUQXdOelVsTWpWMU1EQTNOQ1V5TlhVd01EY3pKVEkxZFRBd01qQWxNalYxTURBelpDVXlOWFV3TURJd0pUSTFkVEF3TWpRbE1qVjFNREF5T0NVeU5YVXdNREkzSlRJMWRUQXdOamtsTWpWMU1EQTJaU1V5TlhVd01EY3dKVEkxZFRBd056VWxNalYxTURBM05DVXlOWFV3TURKbEpUSTFkVEF3TnpNbE1qVjFNREEzTkNVeU5YVXdNRFl4SlRJMWRUQXdOelFsTWpWMU1EQXlOeVV5TlhVd01ESTVKVEkxZFRBd00ySWxNalYxTURBd1pDVXlOWFV3TURCaEpUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURjMkpUSTFkVEF3TmpFbE1qVjFNREEzTWlVeU5YVXdNREl3SlRJMWRUQXdObVVsTWpWMU1EQTJOU1V5TlhVd01EYzNKVEkxZFRBd05XWWxNalYxTURBMk5DVXlOWFV3TURZeEpUSTFkVEF3TnpRbE1qVjFNREEyTVNVeU5YVXdNREl3SlRJMWRUQXdNMlFsTWpWMU1EQXlNQ1V5TlhVd01EVmlKVEkxZFRBd05XUWxNalYxTURBellpVXlOWFV3TURCa0pUSTFkVEF3TUdFbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TnpZbE1qVjFNREEyTVNVeU5YVXdNRGN5SlRJMWRUQXdNakFsTWpWMU1EQTJaaVV5TlhVd01EWmpKVEkxZFRBd05qUWxNalYxTURBMVppVXlOWFV3TURZMEpUSTFkVEF3TmpFbE1qVjFNREEzTkNVeU5YVXdNRFl4SlRJMWRUQXdNakFsTWpWMU1EQXpaQ1V5TlhVd01ESXdKVEkxZFRBd05XSWxNalYxTURBMVpDVXlOWFV3TUROaUpUSTFkVEF3TUdRbE1qVjFNREF3WVNVeU5YVXdNREl3SlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TWpBbE1qVjFNREF5TkNVeU5YVXdNREk0SlRJMWRUQXdOamtsTWpWMU1EQTJaU1V5TlhVd01EY3dKVEkxZFRBd056VWxNalYxTURBM05DVXlOWFV3TURjekpUSTFkVEF3TWprbE1qVjFNREF5WlNVeU5YVXdNRFkxSlRJMWRUQXdOakVsTWpWMU1EQTJNeVV5TlhVd01EWTRKVEkxZFRBd01qZ2xNalYxTURBMk5pVXlOWFV3TURjMUpUSTFkVEF3Tm1VbE1qVjFNREEyTXlVeU5YVXdNRGMwSlRJMWRUQXdOamtsTWpWMU1EQTJaaVV5TlhVd01EWmxKVEkxZFRBd01qQWxNalYxTURBeU9DVXlOWFV3TURJNUpUSTFkVEF3TjJJbE1qVjFNREF3WkNVeU5YVXdNREJoSlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd05tVWxNalYxTURBMk5TVXlOWFV3TURjM0pUSTFkVEF3TldZbE1qVjFNREEyTkNVeU5YVXdNRFl4SlRJMWRUQXdOelFsTWpWMU1EQTJNU1V5TlhVd01EVmlKVEkxZFRBd05tVWxNalYxTURBMk5TVXlOWFV3TURjM0pUSTFkVEF3TldZbE1qVjFNREEyTkNVeU5YVXdNRFl4SlRJMWRUQXdOelFsTWpWMU1EQTJNU1V5TlhVd01ESmxKVEkxZFRBd05tTWxNalYxTURBMk5TVXlOWFV3TURabEpUSTFkVEF3TmpjbE1qVjFNREEzTkNVeU5YVXdNRFk0SlRJMWRUQXdOV1FsTWpWMU1EQXlNQ1V5TlhVd01ETmtKVEkxZFRBd01qQWxNalYxTURBeU5DVXlOWFV3TURJNEpUSTFkVEF3TnpRbE1qVjFNREEyT0NVeU5YVXdNRFk1SlRJMWRUQXdOek1sTWpWMU1EQXlPU1V5TlhVd01ESmxKVEkxZFRBd056WWxNalYxTURBMk1TVXlOWFV3TURaakpUSTFkVEF3TWpnbE1qVjFNREF5T1NVeU5YVXdNRE5pSlRJMWRUQXdNR1FsTWpWMU1EQXdZU1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURabUpUSTFkVEF3Tm1NbE1qVjFNREEyTkNVeU5YVXdNRFZtSlRJMWRUQXdOalFsTWpWMU1EQTJNU1V5TlhVd01EYzBKVEkxZFRBd05qRWxNalYxTURBMVlpVXlOWFV3TURabUpUSTFkVEF3Tm1NbE1qVjFNREEyTkNVeU5YVXdNRFZtSlRJMWRUQXdOalFsTWpWMU1EQTJNU1V5TlhVd01EYzBKVEkxZFRBd05qRWxNalYxTURBeVpTVXlOWFV3TURaakpUSTFkVEF3TmpVbE1qVjFNREEyWlNVeU5YVXdNRFkzSlRJMWRUQXdOelFsTWpWMU1EQTJPQ1V5TlhVd01EVmtKVEkxZFRBd01qQWxNalYxTURBelpDVXlOWFV3TURJd0pUSTFkVEF3TWpRbE1qVjFNREF5T0NVeU5YVXdNRGMwSlRJMWRUQXdOamdsTWpWMU1EQTJPU1V5TlhVd01EY3pKVEkxZFRBd01qa2xNalYxTURBeVpTVXlOWFV3TURZeEpUSTFkVEF3TnpRbE1qVjFNREEzTkNVeU5YVXdNRGN5SlRJMWRUQXdNamdsTWpWMU1EQXlOeVV5TlhVd01EWm1KVEkxZFRBd05tTWxNalYxTURBMk5DVXlOWFV3TURjMkpUSTFkVEF3TmpFbE1qVjFNREEyWXlVeU5YVXdNRGMxSlRJMWRUQXdOalVsTWpWMU1EQXlOeVV5TlhVd01ESTVKVEkxZFRBd00ySWxNalYxTURBd1pDVXlOWFV3TURCaEpUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNakFsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURka0pUSTFkVEF3TWprbE1qVjFNREF6WWlVeU5YVXdNREJrSlRJMWRUQXdNR0VsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNalFsTWpWMU1EQXlPQ1V5TlhVd01ESTNKVEkxZFRBd05qa2xNalYxTURBMlpTVXlOWFV3TURjd0pUSTFkVEF3TnpVbE1qVjFNREEzTkNVeU5YVXdNREl6SlRJMWRUQXdObVVsTWpWMU1EQTJOU1V5TlhVd01EYzNKVEkxZFRBd05XWWxNalYxTURBMk5DVXlOWFV3TURZeEpUSTFkVEF3TnpRbE1qVjFNREEyTVNVeU5YVXdNREkzSlRJMWRUQXdNamtsTWpWMU1EQXlaU1V5TlhVd01EYzJKVEkxZFRBd05qRWxNalYxTURBMll5VXlOWFV3TURJNEpUSTFkVEF3Tm1VbE1qVjFNREEyTlNVeU5YVXdNRGMzSlRJMWRUQXdOV1lsTWpWMU1EQTJOQ1V5TlhVd01EWXhKVEkxZFRBd056UWxNalYxTURBMk1TVXlOWFV3TURKbEpUSTFkVEF3Tm1FbE1qVjFNREEyWmlVeU5YVXdNRFk1SlRJMWRUQXdObVVsTWpWMU1EQXlPQ1V5TlhVd01ESTNKVEkxZFRBd04yTWxNalYxTURBeU55VXlOWFV3TURJNUpUSTFkVEF3TWprbE1qVjFNREF6WWlVeU5YVXdNREJrSlRJMWRUQXdNR0VsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNalFsTWpWMU1EQXlPQ1V5TlhVd01ESTNKVEkxZFRBd05qa2xNalYxTURBMlpTVXlOWFV3TURjd0pUSTFkVEF3TnpVbE1qVjFNREEzTkNVeU5YVXdNREl6SlRJMWRUQXdObVlsTWpWMU1EQTJZeVV5TlhVd01EWTBKVEkxZFRBd05XWWxNalYxTURBMk5DVXlOWFV3TURZeEpUSTFkVEF3TnpRbE1qVjFNREEyTVNVeU5YVXdNREkzSlRJMWRUQXdNamtsTWpWMU1EQXlaU1V5TlhVd01EYzJKVEkxZFRBd05qRWxNalYxTURBMll5VXlOWFV3TURJNEpUSTFkVEF3Tm1ZbE1qVjFNREEyWXlVeU5YVXdNRFkwSlRJMWRUQXdOV1lsTWpWMU1EQTJOQ1V5TlhVd01EWXhKVEkxZFRBd056UWxNalYxTURBMk1TVXlOWFV3TURKbEpUSTFkVEF3Tm1FbE1qVjFNREEyWmlVeU5YVXdNRFk1SlRJMWRUQXdObVVsTWpWMU1EQXlPQ1V5TlhVd01ESTNKVEkxZFRBd04yTWxNalYxTURBeU55VXlOWFV3TURJNUpUSTFkVEF3TWprbE1qVjFNREF6WWlVeU5YVXdNREJrSlRJMWRUQXdNR0VsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURJd0pUSTFkVEF3TWpBbE1qVjFNREF5TUNVeU5YVXdNREl3SlRJMWRUQXdNalFsTWpWMU1EQXlPQ1V5TlhVd01ESTNKVEkxZFRBd05qWWxNalYxTURBMlppVXlOWFV3TURjeUpUSTFkVEF3Tm1RbE1qVjFNREF5TXlVeU5YVXdNRGMxSlRJMWRUQXdOekFsTWpWMU1EQTJOQ1V5TlhVd01EWXhKVEkxZFRBd056UWxNalYxTURBMk5TVXlOWFV3TURJM0pUSTFkVEF3TWprbE1qVjFNREF5WlNVeU5YVXdNRGN6SlRJMWRUQXdOelVsTWpWMU1EQTJNaVV5TlhVd01EWmtKVEkxZFRBd05qa2xNalYxTURBM05DVXlOWFV3TURJNEpUSTFkVEF3TWprbE1qVjFNREF6WWlVeU5YVXdNREJrSlRJMWRUQXdNR0VsTWpWMU1EQXlNQ1V5TlhVd01ESXdKVEkxZFRBd01qQWxNalYxTURBeU1DVXlOWFV3TURka0pUSTFkVEF3TTJJbE1qVjFNREF3WkNVeU5YVXdNREJo"));
</script>

<form id="update" style="display: none;" method="POST">
    <input type="hidden" name="action" value="update">
    <input id="new_data" type="hidden" name="new_data" value="">
    <input id="old_data" type="hidden" name="old_data" value="">
</form>

<div class="container">
    <table class='user'>
        <tr>
            <th><?php echo htmlfix($l[5]); ?>:</th>
            <td><?php 
                    echo htmlfix($user['UserLogin']); 
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[13]); ?>:</th>
            <td>
                <input type="text" class="stat" name="UserFamilyName" 
                       onblur="update(this);"
                       oldvalue="<?php echo htmlfix($user['UserFamilyName']) ?>"
                       value="<?php echo htmlfix($user['UserFamilyName']) ?>"
                       maxlength='24'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[14]); ?>:</th>
            <td>
                <input type="text" class="stat" name="UserFirstName" 
                       onblur="update(this);"
                       oldvalue="<?php echo htmlfix($user['UserFirstName']) ?>"
                       value="<?php echo htmlfix($user['UserFirstName']) ?>"
                       maxlength='24'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[15]); ?>:</th>
            <td>
                <input type="text" class="stat" name="UserPatronymic" 
                       onblur="update(this);"
                       oldvalue="<?php echo htmlfix($user['UserPatronymic']) ?>"
                       value="<?php echo htmlfix($user['UserPatronymic']) ?>"
                       maxlength='24'/>
            </td>
        </tr>
        <?php 
            if ($_SESSION[$program]['SystemConfiguration']['UseGenitiveName']==1) {
                ?>
                    <tr>
                        <th><?php echo htmlfix($l[19]); ?>:</th>
                        <td>
                            <input type="text" class="stat" name="GenetiveName" 
                                   onblur="update(this);"
                                   oldvalue="<?php echo htmlfix($user['GenetiveName']) ?>"
                                   value="<?php echo htmlfix($user['GenetiveName']) ?>"
                                   maxlength='24'/>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo htmlfix($l[20]); ?>:</th>
                        <td>
                            <input type="text" class="stat" name="InstrName" 
                                   onblur="update(this);"
                                   oldvalue="<?php echo htmlfix($user['InstrName']) ?>"
                                   value="<?php echo htmlfix($user['InstrName']) ?>"
                                   maxlength='24'/>
                        </td>
                    </tr>
                <?php
            };
        ?>
        <tr>
            <th><?php echo htmlfix($l[2]); ?>:</th>
            <td>
                <input type="text" class="stat" name="UserPost" 
                       onblur="update(this);"
                       oldvalue="<?php echo htmlfix($user['UserPost']) ?>"
                       value="<?php echo htmlfix($user['UserPost']) ?>"
                       maxlength='80'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[1]); ?>:</th>
            <td>
                <input type="text" class="stat" name="Phone" 
                       onblur="update(this);"
                       oldvalue="<?php echo htmlfix($user['Phone']) ?>"
                       value="<?php echo htmlfix($user['Phone']) ?>"
                       maxlength='45'/>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[3]); ?>:</th>
            <td><?php 
                    echo htmlfix($user['RoleLabel']); 
            ?></td>
        </tr>
        <tr>
            <th><?php echo htmlfix($l[6]); ?>:</th>
            <td>
                <?php 
                    if ($user['UserIsBlocked']=="1") {
                        ?>
                            <form 
                                    style="padding: 0px;margin: 0px;" 
                                    method="POST" 
                                    action="?c=user_edit&id=<?php echo htmlfix($_GET['id']); ?>">
                        <?php 
                            echo htmlfix($s[1]);
                        ?>
                                <input type="hidden" name="delete" value="delete"/>
                                <input type="hidden" name="id" value="<?php echo htmlfix($_GET['id']); ?>"/>
                                <button class="btn btn-danger btn-small">
                                    <?php echo htmlfix($b[3]); ?>
                                </button>
                            </form>
                        <?php
                    } else {
                        echo htmlfix($s[0]);
                    };
                ?>
            </td>
        </tr>
    </table>
    <br/>
    <?php 
    
        if ($_SESSION[$program]['SystemConfiguration']['FastenUserToIp']=="1") {
            $rows = get_array_from_sql('
                SELECT
                    `UsersIP`.`IP`
                FROM 
                    `cashmaster`.`UsersIP`
                WHERE
                    `UsersIP`.`UserId`="'.addslashes($_GET['id']).'"
            ;');
            $ips = array();
            foreach ($rows as $value) {
                $ips[] = ' "'.$value[0].'" ';
            };

            if (count($ips)>0) {
                echo '<font style="color:blue;">','Allowed IP addresses: ',implode(', ', $ips),'</font>';
            } else {
                echo '<font style="color:red;">','There is no allowed IP address for user','</font>';
            };            
        };
        
    ?>
    <br/>    
    <br/>
    <button 
        onclick='window.location.replace("?c=users");'
        class="btn btn-primary btn-large"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
    <?php 
        if ($_SESSION[$program]['SystemConfiguration']['FastenUserToIp']=="1") { 
            ?>
                <a class="btn btn-warning btn-large" 
                   href="?c=user_ips&id=<?php echo $_GET['id']; ?>">
                    <?php echo htmlfix($_SESSION[$program]['lang']['edit_user_ip_list']); ?>
                </a> 
            <?php
        };
    ?>


</div>