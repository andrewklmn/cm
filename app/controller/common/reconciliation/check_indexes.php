<?php

/*
 * Проверяет индексы на этапе перед созданием сверки
 * мультиязычно
 */

    if (!isset($c)) exit;
    
    $sorter_indexes = get_array_from_sql('
            SELECT
                IndexName
            FROM
                DepositRuns
            LEFT JOIN
                DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
            WHERE
                    DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                    AND IFNULL(DepositRecs.ReconcileStatus,0)="0"
            GROUP BY IndexName
    ;');
    
    
    $indexes = get_assoc_array_from_sql('
        SELECT
            `SorterIndexes`.`Id`,
            `SorterIndexes`.`IndexName`,
            `SorterIndexes`.`DepositIndexId`
        FROM 
            `cashmaster`.`SorterIndexes`
    ;');
    
    
    $flag = false;
    // Проверяем есть ли индекс в нашей таблице соответствия индексов.
    foreach ($sorter_indexes as $value) {
        foreach ($indexes as $val) {
            //echo $value[0],'==',$val['IndexName'],'<br/>';
            if ($value[0]==$val['IndexName']) {
                $flag = true;
            };
        };
    };
    
    
    if ( $flag==false ) {
        
        //print_r($_SESSION[$program]['UserConfiguration']);
        
        if ($_SESSION[$program]['UserConfiguration']['RoleId']==3){
            $data['error'] = $_SESSION[$program]['lang']['cant_open_recon_by_card'].$_REQUEST['separator_id'].'.<br/>'
                .$_SESSION[$program]['lang']['wrong_index_in_accounting_data'].'. '
                .$_SESSION[$program]['lang']['call_supervisor'];
            include './app/view/error_message.php';
            ?>
                <hr/>
                <div class="container">
                    <button
                        onclick="back_to_workflow();"
                        class="btn-primary btn-large" href="?c=index"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
                </div>
            <?php
            exit;            
        } else {
            $data['error'] = $_SESSION[$program]['lang']['cant_open_recon_by_card'].$_REQUEST['separator_id'].'.<br/>'
                    .$_SESSION[$program]['lang']['wrong_index_in_accounting_data'];
            include './app/view/error_message.php';
            ?>
                <hr/>
                <div class="container">
                    <button
                        onclick="back_to_workflow();"
                        class="btn-primary btn-large" href="?c=index"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
                    <a class="btn btn-warning btn-large" 
                       href="?c=index_conflict&separator_id=<?php echo htmlfix($_REQUEST['separator_id']); ?>">
                        <?php echo $_SESSION[$program]['lang']['index_correction']; ?>
                    </a>
                </div>
            <?php
            exit;
        };
    };
    
    if (count($sorter_indexes)>1) {
        if ($_SESSION[$program]['UserConfiguration']['RoleId']==2){
            $data['error'] = $_SESSION[$program]['lang']['cant_open_recon_by_card'].$_REQUEST['separator_id'].'.<br/>'.$_SESSION[$program]['lang']['more_than_one_index'];
            include './app/view/error_message.php';

            ?>
                <hr/>
                <div class="container">
                    <button
                        onclick="back_to_workflow();"
                        class="btn-primary btn-large" href="?c=index"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
                    <a class="btn btn-warning btn-large" 
                       href="?c=index_conflict&separator_id=<?php echo htmlfix($_REQUEST['separator_id']); ?>">
                           <?php echo htmlfix($_SESSION[$program]['lang']['edit_indexes']); ?>
                    </a>
                </div>
            <?php
            exit;
        } else {
            $data['error'] = $_SESSION[$program]['lang']['cant_open_recon_by_card'].$_REQUEST['separator_id'].'.<br/>'.$_SESSION[$program]['lang']['more_than_one_index'].$_SESSION[$program]['lang']['call_supervisor'];
            include './app/view/error_message.php';

            ?>
                <hr/>
                <div class="container">
                    <button
                        onclick="back_to_workflow();"
                        class="btn-primary btn-large" href="?c=index"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
                </div>
            <?php
            exit;
        }; 
    };
    
    
?>
