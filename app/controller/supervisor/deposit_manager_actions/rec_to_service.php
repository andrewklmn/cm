<?php

/*
 * Устанавливает одну сверку в СЕРВИС
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
                    UPDATE 
                        `DepositRecs`
                    SET
                        `RecSupervisorId` = "'.$_SESSION[$program]['UserConfiguration']['UserId'].'",
                        `ReconcileStatus` = 1,
                        `ServiceRec` = 1
                    WHERE 
                        `DepositRecId` IN  ("'.addslashes($recs[0]).'")
                ;');
                do_sql('
                    UPDATE `cashmaster`.`DepositRuns`
                    SET
                        `DataSortCardNumber` = "SERVICE"
                    WHERE 
                        `DepositRecId` IN  ("'.addslashes($recs[0]).'")
                ;');
                do_sql('UNLOCK TABLES;');

                $_REQUEST['id'] = $rec[0];
                        
                $data['success'] = $w[0];
                include 'app/view/success_message.php';
                // выводим отчет по сервисной сверке с кнопкой возврата к списку
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
            // Депозит №  будет переведен в сервисную категорию Отменить будет невозможно
            $data['info_header'] = $_SESSION[$program]['lang']['attention'];
            $data['info_text'] = '<br/>'.$w[1].implode(', ', $card).' '.$w[2].'<br/>'.$w[3].'!';
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
