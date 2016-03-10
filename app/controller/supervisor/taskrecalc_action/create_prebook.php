<?php

        if (!isset($c)) exit;
    
        $skiped = array();
        $added = array();

        foreach ($root as $key=>$value) {
            // проверяем - есть ли такой файл в таблице Prebook
            $sql = '
            SELECT 
                count(*)
            FROM `cashmaster`.`Prebook`
            WHERE
                `Prebook`.`Filename`="'.  addslashes($_GET['id']).'"
                AND `Prebook`.`PackId`="'.  addslashes($value['PackId']).'"
            ;';
            $row = fetch_row_from_sql($sql);
            
            if ($row[0]>0) {
                // если есть, то пропускаем и переходим к следующему
                $skiped[count($skiped)] = $value['PackId'];
                continue;
            } else {
                // если нету, то заполняем таблицу пребук *********************************
                $sql = '
                    INSERT INTO `cashmaster`.`Prebook`
                        (
                            `PackId`,
                            `Filename`,
                            `CashroomId`,
                            `UserId`,
                            `CustomerApproved`,
                            `Prepared`
                        )
                    VALUES
                        (
                            "'.addslashes($value['PackId']).'",
                            "'.addslashes($_GET['id']).'",
                            "'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'",
                            "'.$_SESSION[$program]['UserConfiguration']['UserId'].'",
                            "1",
                            "0"
                        )
                ;';
                do_sql($sql);
                //echo '<br/>';
                $added[count($added)] = $value['PackId'];
            };
        };
        
        
        
        // Создаём папку для архива таскрекалк, если нету
        if (!file_exists('taskrecalc_archive')) {
            mkdir( 'taskrecalc_archive', 0777);
            chmod( 'taskrecalc_archive', 0777);
        };
        
        
        // Создаём папку нужной кассы, если нету
        if (!file_exists('taskrecalc_archive/'.$_SESSION[$program]['UserConfiguration']['CashRoomId']) ) {
            mkdir( 'taskrecalc_archive/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'], 0777);
            chmod( 'taskrecalc_archive/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'], 0777);
        };
        
        // Перемещаем в неё обработанный файл 
        if (file_exists('input/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id'])) {
            rename(
                    'input/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id'],
                    'taskrecalc_archive/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id']
            );
            chmod( 'taskrecalc_archive/'.$_SESSION[$program]['UserConfiguration']['CashRoomId'].'/'.$_GET['id'], 0777);
        };
        
        
        echo '<h4>',htmlfix($_GET['id']),'</h4>';
        
        // Сообщаем о добавленных и пропущенных
        if (count($skiped)>0) {
            $data['info_text'] = 'Prebook skiped for packs: '. implode(', ', $skiped);
            include 'app/view/info_message.php';            
        };
        if (count($added)>0) {
            $data['success'] = 'Prebook created for packs: '. implode(', ', $added);
            include 'app/view/success_message.php';
        };
        
?>
<div class="container navbar navbar-fixed-bottom"
    style="background-color: white; padding: 20px;">
    <form method="POST">
        <a class="btn btn-primary btn-large" href="?c=taskrecalc">
            <?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?>
        </a>
    </form>
</div>
<?php
  
    //echo '<pre>';
    //print_r($_SESSION[$program]);
    //echo '</pre>';
    exit;
    
?>