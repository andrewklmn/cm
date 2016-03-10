<?php

/*
 * Устанавливает одну сверку в СЕРВИС
 */

        if (!isset($c)) exit;
        
        $w = explode('|', $_SESSION[$program]['lang']['deposit_manager_release_messages']);
        
        $card = explode('|', $_POST['card']);
        $recs = explode('|', $_POST['recs']);
        $runs = explode('|', $_POST['runs']);
        if (isset($_POST['confirmation'])){
            if ($_POST['confirmation']==$a[4]) {
                do_sql('LOCK TABLES DepositRecs WRITE, DepositRuns WRITE;');
                // переводим флаг сверки в состояние сервис
                
                $row = fetch_row_from_sql('
                    SELECT
                        IFNULL(PrepOperatorId,0)
                    FROM
                        DepositRecs
                    WHERE
                        DepositRecs.DepositRecId="'.addslashes($recs[0]).'"
                ;');
                if ($row[0]==0) {
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
                    $data['success'] = $w[0].'. '.$w[1].'.';
                } else {
                    $data['success'] = $w[1].'.';
                };
                
                do_sql('
                    UPDATE `cashmaster`.`DepositRuns`
                    SET
                        `DepositRecId` = "0"
                    WHERE 
                        `DepositRecId` IN  ('.addslashes(implode(', ', $recs)).')
                ;');
                do_sql('UNLOCK TABLES;');
                
                $data['title'] = $a[4];
                include './app/model/menu.php';
                include './app/view/page_header_with_logout.php';
                include './app/view/set_remove_wait.php';
                
                include 'app/view/success_message.php';
                // выводим отчет по сервисной сверке с кнопкой возврата к списку
                ?>
                <div class="container">
                <br/>
                    <a class="btn btn-primary btn-large" href="?c=deposit_manager">
                        <?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?>
                    </a>
                </div>
                <?php
                exit;
            };
        } else {
            // Предлагаем подтвердить сверку
            $data['title'] = $a[4];
            include './app/model/menu.php';
            include './app/view/page_header_with_logout.php';
            include './app/view/set_remove_wait.php';
            // Отображаем сверку для подтверждения
            // Пересчеты по депозиту №
            $data['info_header'] = $_SESSION[$program]['lang']['attention'];
            $data['info_text'] = '<br/>'.$w[2].implode(', ', $card).' '.$w[3].'<br/>'.$w[4].'!';
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
                   value="<?php echo htmlfix($a[4]); ?>">
            <?php
            echo '</form>';
            echo '</div>';
            exit;
        };
?>
