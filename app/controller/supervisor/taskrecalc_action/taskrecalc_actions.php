<?php

        if (!isset($c)) exit;
        
        do_sql('LOCK TABLES Prebook WRITE, Denoms WRITE, DepositIndex WRITE, Customers WRITE, Currency WRITE;');
        
        include 'app/controller/supervisor/taskrecalc_action/check.php';
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case $_SESSION[$program]['lang']['create_prebook_recs']:
                    include 'app/controller/supervisor/taskrecalc_action/create_prebook.php';
                    break;
                case $_SESSION[$program]['lang']['edit_client_name']:
                    include 'app/controller/supervisor/taskrecalc_action/edit_client_name.php';
                    break;
                case $_SESSION[$program]['lang']['add_new_client']:
                    include 'app/controller/supervisor/taskrecalc_action/add_new_client.php';
                    break;
                default:
                    //print_r($_POST);
                    //echo 'default';
                    break;
            };
        };

        do_sql('UNLOCK TABLES;');
        
        if (count($error)>0) {
            $error = array_unique($error); 
            $data['error'] = implode('<br/> ', $error);
            include 'app/view/error_message.php';
        };
        

        if (count($warning)>0) {
            $warning = array_unique($warning); 
            //$data['info_header'] = 'Warning';
            $data['text'] = implode('<br/> ', $warning);
            include 'app/view/warning_message.php';
        };
        
        if (count($success)>0) {
            $warning = array_unique($warning); 
            //$data['info_header'] = 'Warning';
            $data['success'] = implode('<br/> ', $success);
            include 'app/view/success_message.php';
        };
        
        //echo '<pre>';
        //print_r($wrong_clients);
        //echo '</pre>';