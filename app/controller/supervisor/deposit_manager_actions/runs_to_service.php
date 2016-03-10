<?php

/*
 * Устанавливает создает сервисную сверку и привязывает к ней несколько ранов
 */

        if (!isset($c)) exit;
        
        $w = explode('|', $_SESSION[$program]['lang']['deposit_manager_service_messages']);
        
        $card = explode('|', $_POST['card']);
        $recs = explode('|', $_POST['recs']);
        $runs = explode('|', $_POST['runs']);
        if (isset($_POST['confirmation'])){
            if ($_POST['confirmation']==$a[3]) {
                do_sql('LOCK TABLES DepositRecs WRITE, DepositRuns WRITE;');
                // переводим флаг сверки в состояние сервис
                
                do_sql('
                    INSERT INTO `cashmaster`.`DepositRecs`
                    (
                        `ScenarioId`,
                        `RecOperatorId`,
                        `RecCreateDatetime`,
                        `RecLastChangeDatetime`,
                        `IsBalanced`,
                        `RecSupervisorId`,
                        `ReconcileStatus`,
                        `ServiceRec`
                    )
                    VALUES
                    (
                        0,
                        "'.addslashes($_SESSION[$program]['UserConfiguration']['UserId']).'",
                        CURRENT_TIMESTAMP,
                        CURRENT_TIMESTAMP,
                        0,
                        "'.addslashes($_SESSION[$program]['UserConfiguration']['UserId']).'",
                        1,
                        1
                    )
                ;');
                
                $row = fetch_row_from_sql('
                    SELECT
                        MAX(DepositRecId)
                    FROM
                        DepositRecs
                ;');
                $new_rec_id = $row[0];
                $sql = '
                    UPDATE `cashmaster`.`DepositRuns`
                    SET
                        `DataSortCardNumber` = "SERVICE",
                        `DepositRecId` = "'.$new_rec_id.'"
                    WHERE 
                        `DepositRunId` IN  ('.addslashes(implode(', ', $runs)).')   
                ;';
                //echo $sql;
                do_sql($sql);
                
                do_sql('UNLOCK TABLES;');

                $data['success'] = $w[0];
                include 'app/view/success_message.php';
                // выводим отчет по сервисной сверке с кнопкой возврата к списку
                $_REQUEST['id'] = $new_rec_id;
                include 'app/view/forms/reconciliation_view.php';
                exit;
            };
        } else {
            // Предлагаем подтвердить сверку
            $data['title'] = $a[3];
            include './app/model/menu.php';
            include './app/view/page_header_with_logout.php';
            include './app/view/set_remove_wait.php';
            // Отображаем сверку для подтверждения
            // Пересчеты № будут собраны в сервисный депозит Отменить будет невозможно
            $data['info_header'] = $_SESSION[$program]['lang']['attention'];
            $data['info_text'] = '<br/>'.$w[4].implode(', ', $card).' '.$w[5].'<br/>'.$w[6].'!';
            include './app/view/info_message.php';
            echo '<div class="container">';
            echo '<form method="POST">';
            include 'app/view/repost_post.php';
            ?>
            <input class="btn btn-primary btn-large" 
                   type="submit" 
                   name="confirmation" 
                   value="<?php echo htmlfix($a[0]); ?>">
            <input 
                   class="btn btn-danger btn-large" 
                   type="submit" 
                   name="confirmation" 
                   value="<?php echo htmlfix($a[3]); ?>">
            <?php
            echo '</form>';
            echo '</div>';
            exit;
        };
?>
