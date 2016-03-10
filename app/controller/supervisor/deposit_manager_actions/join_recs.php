<?php

/*
 * Устанавливает одну сверку в СЕРВИС
 */

        if (!isset($c)) exit;
        
        $w = explode('|', $_SESSION[$program]['lang']['deposit_manager_join_messages']);
        
        $card = explode('|', $_POST['card']);
        $recs = explode('|', $_POST['recs']);
        $runs = explode('|', $_POST['runs']);
        if (isset($_POST['confirmation'])){
            if ($_POST['confirmation']==$a[2]) {
                do_sql('LOCK TABLES DepositRecs WRITE, DepositRuns WRITE;');
                // переводим флаг сверки в состояние сервис
                $result_card=$card[0];
                $service_ids=array();
                foreach ($recs as $key=>$value) {
                    if($_POST['result_id']==$value) {
                        $result_card=$card[$key];
                    } else {
                        $service_ids[]=$key;
                    };
                };
                
                foreach ($service_ids as $key => $value) {
                    $row = fetch_row_from_sql('
                        SELECT
                            IFNULL(PrepOperatorId,0)
                        FROM
                            DepositRecs
                        WHERE
                            DepositRecs.DepositRecId="'.addslashes($value).'"
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
                                `DepositRecId` IN  ("'.addslashes($value).'")
                        ;');
                    };
                };
                
                do_sql('
                    UPDATE 
                        `DepositRuns`
                    SET
                        `DataSortCardNumber` = "'.addslashes($result_card).'",
                        `DepositRecId` = "'.addslashes($_POST['result_id']).'"
                    WHERE 
                        `DepositRecId` IN  ('.addslashes(implode(', ', $recs)).')
                ;');
                

                do_sql('UNLOCK TABLES;');
                
                $data['title'] = $a[2];
                include './app/model/menu.php';
                include './app/view/page_header_with_logout.php';
                include './app/view/set_remove_wait.php';
                
                $data['success'] = $w[0];
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
            $data['title'] = $a[2];
            include './app/model/menu.php';
            include './app/view/page_header_with_logout.php';
            include './app/view/set_remove_wait.php';
            // Отображаем сверку для подтверждения
            // Депозиты № 1111 будет объеденены
            $data['info_header'] = $_SESSION[$program]['lang']['attention'];
            $data['info_text'] = '<br/>'.$w[1].implode(', ', $card).' '.$w[2].'<br/>'.$w[3].'!';
            include './app/view/info_message.php';
            echo '<div class="container">';
            echo '<form method="POST">';
            //unset ($_POST['card']);
            include 'app/view/repost_post.php';
            echo htmlfix($w[4]);
            ?>:
            <select 
                    class="search-query" 
                    name="result_id">
                <?php
                    foreach($card as $key=>$value){
                        echo '<option value="',htmlfix($recs[$key]),'">',htmlfix($value),'</option>';
                    };
                ?>
            </select>
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
                   value="<?php echo htmlfix($a[2]); ?>">
            <?php
            echo '</form>';
            echo '</div>';
            exit;
        };
?>
