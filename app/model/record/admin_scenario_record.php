<?php

    /*
     * Sorter record
     */

    $flag = true;
    $scen_id = '';
    if (isset($_GET['id'])) $scen_id = $_GET['id'];
    include 'app/controller/common/check_finished_recon_by_scenario.php';
    
    $record['table'] = 'Scenario';
    $record['labels'] = explode('|',$_SESSION[$program]['lang']['scen_edit_labels']);
    $record['formula'] = array(
        0 => 'ScenarioName',
        1 => 'DefaultScenario',
        2 => 'SingleDenomDeposits',
        3 => 'ReconcileAgainstValue',
        4 => 'DefExpectedNumber',
        5 => 'UsePreparationStep',
        6 => 'ForceDepositDetails',
        7 => 'CheckIndexes',
        8 => 'UseSuspectSerialNumbers',
        9 => 'UsePackingOperatorName',
        10 => 'UseDepositPackingDate',
        11 => 'UsePackType',
        12 => 'UsePackId',
        13 => 'UsePackIntegrity',
        14 => 'UseSealType',
        15 => 'UseSealNumber',
        16 => 'UseSealIntegrity',
        17 => 'UseStrapType',
        18 => 'UseStrapsIntegrity',
        19 => 'LogicallyDeleted'
    );
    //$record['default'] = explode('|', '');
    if ($flag==true) {
        $record['type'] = array(
            0 => 'text',
            1 => 'checker',
            2 => 'checker',
            3 => 'checker',
            4 => 'text',
            5 => 'checker',
            6 => 'checker',
            7 => 'checker',
            8 => 'checker',
            9 => 'checker',
            10 => 'checker',
            11 => 'checker',
            12 => 'checker',
            13 => 'checker',
            14 => 'checker',
            15 => 'checker',
            16 => 'checker',
            17 => 'checker',
            18 => 'checker',
            19 => 'checker'
        );
    } else {
        $record['type'] = array(
            0 => 'text',
            1 => 'checker',
            2 => 'logical',
            3 => 'logical',
            4 => 'text',
            5 => 'checker',
            6 => 'checker',
            7 => 'logical',
            8 => 'checker',
            9 => 'checker',
            10 => 'checker',
            11 => 'checker',
            12 => 'checker',
            13 => 'checker',
            14 => 'checker',
            15 => 'checker',
            16 => 'checker',
            17 => 'checker',
            18 => 'checker',
            19 => 'checker'
        );        
    };

    $record['type_for_new'] = array(
        0 => 'text',
        1 => 'logical',
        2 => 'checker',
        3 => 'checker',
        4 => 'text',
        5 => 'checker',
        6 => 'checker',
        7 => 'checker',
        8 => 'checker',
        9 => 'checker',
        10 => 'checker',
        11 => 'checker',
        12 => 'checker',
        13 => 'checker',
        14 => 'checker',
        15 => 'checker',
        16 => 'checker',
        17 => 'checker',
        18 => 'checker',
        19 => 'checker'
    );
    $record['select'] = explode('|','|');
    $record['width'] = explode('|','600|300');
    $record['back_page'] = '?c=scens';
    // ================ Possible action ==========
    $record['confirm_update'] = false;
    $record['clone'] = true;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
        
?>
