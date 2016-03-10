<?php
/*
 * Редактирование записи (пример работы)
 */
        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['signer_edit_title'];
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';

        
        
        /*
        // Проверяем использование ценности в сверенных сверках
        $row = fetch_row_from_sql('
            SELECT
                count(*)
            FROM 
                `Valuables`
            LEFT JOIN
                `SorterAccountingData` ON `SorterAccountingData`.`ValuableId`=`Valuables`.`ValuableId`
            LEFT JOIN
                `DepositRuns` ON `DepositRuns`.`DepositRunId`=`SorterAccountingData`.`DepositRunId`
            LEFT JOIN
                `DepositRecs` ON `DepositRecs`.`DepositRecId`=`DepositRuns`.`DepositRecId`
            WHERE
                `Valuables`.`ValuableId`="'.addslashes($_GET['id']).'"
                AND `DepositRecs`.`ReconcileStatus`=1
        ;');
        if ( $row[0] > 0){
            $b = explode('|', $_SESSION[$program]['lang']['record_edit_buttons']);
            ?>
            <div class="container">
                <div class="alert alert-error">  
                  <a class="close" data-dismiss="alert">×</a>  
                  <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                  <br/>
                      <?php echo htmlfix($_SESSION[$program]['lang']['signer_edit_cannot_edit']); ?>
                </div> 
                <br/>
                <a 
                   class="btn btn-primary btn-large" 
                   href="?c=valuables">
                    <?php echo htmlfix($b[7]); ?>
                </a>
            </div>
            <?php
            exit;
        };
        */
        
        
        include 'app/model/record/supervisor_signer_record.php';
        include_once 'app/view/draw_record_edit.php'; 
        draw_record_edit( $record );
        
?>