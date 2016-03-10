<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;
        
        if (!isset($_GET['id']) OR $_GET['id']=='') {
                header( 'Location: ?c=taskrecalc' );
                exit;
        };

        // проверяем существует ли файл
        if (!file_exists('input/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id'])) {
            header( 'Location: ?c=taskrecalc' );
            exit;
        };
        
        $data['title'] = $_SESSION[$program]['lang']['taskrecalc_view'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
        //include 'app/view/reload_after_1_min.php';
        include_once 'app/model/taskrecalc/get_taskrecalc_files_list.php';
        
        $list = get_taskrecalc_files_list();
        
?>
    <div class="container">
        <?php
        
            $root = simplexml_load_file('input/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id']);
            
            if (!$root) {
                // Файл не соответствует стандарту XML, перекладываем его в Эррор
                copy(
                    'input/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id'],
                    'error/'.$_GET['id']
                );
                chmod('error/'.$_GET['id'], 0777);
                unlink('input/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id']);
                
                echo htmlfix($_GET['id']).' - has XML syntax error<br/>';
                echo 'File was moved to error folder';
            } else {;

                // проверяем наличие тегов Pack в корне папки
                $packs = 0;
                foreach ($root as $key=>$value) {
                    if (strtolower($key)=='pack') $packs++;
                };

                if ($packs == 0) {
                    // Файл не содержит информации о подготовках
                    copy(
                        'input/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id'],
                        'error/'.$_GET['id']
                    );
                    chmod('error/'.$_GET['id'], 0777);
                    unlink('input/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id']);

                    echo htmlfix($_GET['id']).' - has wrong XML structure<br/>';
                    echo 'File was moved to error folder';                

                } else {;
                    
                    // проверяем действия с файлом
                    include 'app/controller/supervisor/taskrecalc_action/taskrecalc_actions.php';
                    
                    echo '<h4>',htmlfix($_GET['id']),'</h4>';
                    include 'app/model/table/taskrecalc_xml.php';
                };
            };
        ?>
    </div>
    <div class="container navbar navbar-fixed-bottom"
         style="background-color: white; padding: 20px;">
        <form method="POST">
            <a class="btn btn-primary btn-large" href="?c=taskrecalc">
                <?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?>
            </a>
            <?php 

                if ($packs > 0 ) {
                    if ( $year_denom_found==TRUE
                            AND $index_found==TRUE
                            AND $client_name_found==TRUE
                            AND $client_bic_found==TRUE) {
                        ?>
                            <input 
                                   type="submit"
                                   class="btn btn-large btn-success"
                                   name="action" 
                                   value="<?php echo htmlfix($_SESSION[$program]['lang']['create_prebook_recs']); ?>"/>
                        <?php
                    } elseif ( $year_denom_found==FALSE) {
                        ?>
                            <!--
                            <a class="btn btn-warning btn-large" href="?c=taskrecalc">
                                <?php echo htmlfix($_SESSION[$program]['lang']['add_new_index']); ?>
                            </a>
                            -->
                            <input 
                                   type="submit"
                                   class="btn btn-large btn-danger"
                                   name="action" 
                                   value="<?php echo htmlfix($_SESSION[$program]['lang']['delete_taskrecalc']); ?>"/>

                        <?php                    
                    } elseif ($client_bic_found==FALSE
                                AND $client_name_found==FALSE){
                        
                        ?>
                            <input 
                                   type="submit"
                                   class="btn btn-large btn-warning"
                                   name="action" 
                                   value="<?php echo htmlfix($_SESSION[$program]['lang']['add_new_client']); ?>"/>
                            <input type='hidden' name='new_clients' value="<?php echo base64_encode(serialize(array_unique($new_clients))); ?>"/>
                        <?php
                    } elseif ($client_bic_found==TRUE
                                AND $client_name_found==FALSE) {
                        ?>
                            <input 
                                   type="submit"
                                   class="btn btn-large btn-warning"
                                   name="action" 
                                   value="<?php echo htmlfix($_SESSION[$program]['lang']['edit_client_name']); ?>"/>
                            <input type='hidden' name='wrong_clients' value="<?php echo base64_encode(serialize(array_unique($wrong_clients))); ?>"/>
                        <?php                    
                    } elseif ($client_bic_found==FALSE
                                AND $client_name_found==TRUE) {
                        ?>
                            <input 
                                   type="submit"
                                   class="btn btn-large btn-danger"
                                   name="action" 
                                   value="<?php echo htmlfix($_SESSION[$program]['lang']['delete_taskrecalc']); ?>"/>
                        <?php                    
                    };
                };
            ?>
        </form>
    </div>
</body>
</html>