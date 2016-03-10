<?php

/*
 * Устанавливает создает сервисную сверку и привязывает к ней несколько ранов
 */

        if (!isset($c)) exit;
        
        $w = explode('|', $_SESSION[$program]['lang']['deposit_manager_edit_number_messages']);
        
        $card = explode('|', $_POST['card']);
        $recs = explode('|', $_POST['recs']);
        $runs = explode('|', $_POST['runs']);
        if (isset($_POST['confirmation'])){
            if ($_POST['confirmation']==$a[1]) {
                do_sql('LOCK TABLES DepositRecs WRITE, DepositRuns WRITE;');
                // переводим флаг сверки в состояние сервис
                /*
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
                 * 
                 */
                $sql = '
                    UPDATE `cashmaster`.`DepositRuns`
                    SET
                        `DataSortCardNumber` = "'.addslashes($card[0]).'"
                    WHERE 
                        `DepositRunId` IN  ('.addslashes(implode(', ', $runs)).')   
                ;';
                //echo $sql;
                do_sql($sql);
                
                do_sql('UNLOCK TABLES;');
                
                $data['title'] = $a[1];
                include './app/model/menu.php';
                include './app/view/page_header_with_logout.php';
                include './app/view/set_remove_wait.php';
                
                $data['success'] = $w[7].' '.htmlfix($card[0]);
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
            $data['title'] = $a[1];
            include './app/model/menu.php';
            include './app/view/page_header_with_logout.php';
            include './app/view/set_remove_wait.php';
            // Отображаем сверку для подтверждения
            // Номера пересчетов  будут изменены. Отменить будет невозможно.
            $data['info_header'] = $_SESSION[$program]['lang']['attention'];
            $data['info_text'] = '<br/>'.htmlfix($w[5])
                    .implode(', ', $card).' '.htmlfix($w[6]).'<br/>'.htmlfix($w[3]).'!';
            include './app/view/info_message.php';
            echo '<div class="container">';
            echo '<form method="POST">';
            include 'app/view/repost_post.php';
            echo htmlfix($w[4]),':';
            ?>
            <input 
                    class="search-query" 
                    autocomplete="off"
                    name="card" type="text" value=""/>
            <br/>
            <br/>
            <input class="btn btn-primary btn-large" 
                   type="submit" 
                   name="confirmation" 
                   value="<?php echo htmlfix($a[0]); ?>">
            <input 
                   class="btn btn-danger btn-large" 
                   type="submit" 
                   name="confirmation" 
                   value="<?php echo htmlfix($a[1]); ?>">
            <?php
            echo '</form>';
            echo '</div>';
            exit;
        };
?>
