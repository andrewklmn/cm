<?php

/*
 * Deposit Manager Actions
 */

    if (!isset($c)) exit;
    
    $a = explode('|', $_SESSION[$program]['lang']['deposit_manager_actions']);
    
    if(isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'rec_to_service':
                include 'app/controller/supervisor/deposit_manager_actions/rec_to_service.php';
                break;
            case 'runs_to_service':
                include 'app/controller/supervisor/deposit_manager_actions/runs_to_service.php';
                break;
            case 'change_card_number_in_runs':
                include 'app/controller/supervisor/deposit_manager_actions/change_card_number_in_runs.php';
                break;
            case 'change_card_number_in_rec':
                include 'app/controller/supervisor/deposit_manager_actions/change_card_number_in_rec.php';
                break;
            case 'release_runs':
                include 'app/controller/supervisor/deposit_manager_actions/release_runs.php';
                break;
            case 'join_recs':
                include 'app/controller/supervisor/deposit_manager_actions/join_recs.php';
                break;
            default:
                break;
        };
    };
    header("Location: ?c=deposit_manager");
?>

