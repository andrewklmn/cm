<?php
/*
 * Ввод серийных номеров подозрительных купюр.
 */
        if (!isset($c)) exit;
        
        if (isset($_POST['action'])) {
            if ($_POST['action']=="update") {
                include './app/view/html_header.php';
                do_sql('LOCK TABLES SuspectSerialNumbs WRITE, DepositRecs WRITE;');

                // проверяем не закрылась ли сверка?
                $row = fetch_row_from_sql('
                    SELECT
                        `DepositRecs`.`ReconcileStatus`,
                        `DepositRecs`.`FwdToSupervisor`
                    FROM 
                        `cashmaster`.`DepositRecs`
                    WHERE
                        `DepositRecs`.`DepositRecId`="'.addslashes($_GET['deposit_rec_id']).'"
                ;');

                // если сверка закрыта или передалась контроллеру то вернуть 2
                if ($row[0]==1 OR $row[1]==1 ) {
//                  // Если смотрит не контроллер, то вернуть 2
                    if ($_SESSION[$program]['user_role_id']!=2) {
                        do_sql('UNLOCK TABLES;');
                        echo 2;
                        exit;
                    };
                };

                // проверяем не обновлялась ли запись с серийными номерами
                $row = fetch_row_from_sql('
                    SELECT
                        `SuspectSerialNumbs`.`LeftSeria`,
                        `SuspectSerialNumbs`.`LeftNumber`,
                        `SuspectSerialNumbs`.`RightSeria`,
                        `SuspectSerialNumbs`.`RightNumber`
                    FROM 
                        `cashmaster`.`SuspectSerialNumbs`
                    WHERE
                        `SuspectSerialNumbs`.`SequenceId`="'.addslashes($_POST['id']).'"
                ;');
                $olddata = explode('|', $_POST['olddata']);

                // если обновлялась, то возвращаем 1 и обновленное значение
                if($olddata!=$row) {
                    do_sql('UNLOCK TABLES;');
                    echo 1,  implode('|', $row);
                    exit;
                };

                $newdata = explode('|', $_POST['newdata']);
                // обновляем запись и возвращаем 0 
                do_sql('
                    UPDATE 
                        `cashmaster`.`SuspectSerialNumbs`
                    SET
                        `LeftSeria` = "'.addslashes($newdata[0]).'",
                        `LeftNumber` = "'.addslashes($newdata[1]).'",
                        `RightSeria` = "'.addslashes($newdata[2]).'",
                        `RightNumber` = "'.addslashes($newdata[3]).'"
                    WHERE 
                        `SuspectSerialNumbs`.`SequenceId`="'.addslashes($_POST['id']).'"
                ;');
                do_sql('UNLOCK TABLES;');
                echo 0;
                exit;
            }; 
            if ($_POST['action']=="to_control") {
                // передаем сверку контролеру
                do_sql('
                    UPDATE `cashmaster`.`DepositRecs`
                    SET
                        `DepositRecs`.`IsBalanced`="0",
                        `DepositRecs`.`FwdToSupervisor`="1",
                        `RecLastChangeDatetime` = CURRENT_TIMESTAMP
                    WHERE 
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');
                echo 0;
                exit;
            };
            if ($_POST['action']=="recon_with_discrep") {
                
                include './app/controller/common/reconciliation/check_recon_indexes.php';
                // передаем сверку контролеру
                
                do_sql('
                    UPDATE `cashmaster`.`DepositRecs`
                    SET
                        `DepositRecs`.`IsBalanced`="0",
                        `DepositRecs`.`FwdToSupervisor`="0",
                        `DepositRecs`.`ReconcileStatus`="1",
                        `RecLastChangeDatetime` = CURRENT_TIMESTAMP,
                        `RecSupervisorId` = "'.$_SESSION[$program]['user_id'].'",
                        `DepositIndexId` = "'.addslashes($index_id).'"
                    WHERE 
                        `DepositRecId` = "'.$_REQUEST['deposit_rec_id'].'"
                ;');
                echo 0;
                exit;
            };
        };
                
        
        

        $data['title'] = $_SESSION[$program]['lang']['suspect_title'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
        ?>
            <style>
                table.info_table th {
                    padding:3px; 
                    font-size:11px; 
                    border-bottom: 2px solid black;
                    background-color: lightgray;
                }
                table.info_table td {
                    padding:3px; 
                    font-size:11px; 
                    border-bottom: 1px solid gray;
                }
                table.info_table input {
                    padding:0px; 
                    margin: 0px;
                    text-align: center;
                }
            </style>
            <script>
                function back_to_workflow() {
                    window.location.replace('index.php');
                };
                function recon_with_discrep(elem) {
                   var stat=$('.stat:visible');
                   var flag = true;
                   $(stat).each(function(){
                       if (this.value=='' || this.value.length != $(this).attr('maxlength')) {
                           $(this).css('background-color','yellow');
                           flag = false;
                       };
                   });
                   if(flag==false) {
                       alert('<?php echo htmlfix($_SESSION[$program]['lang']['fill_required_fields']) ?>');
                       return true;
                   };
                    $.ajax({
                        type: "POST",
                        url: "?c=suspect_enter&deposit_rec_id=<?php echo htmlfix($_REQUEST['deposit_rec_id']); ?>",
                        async: false,
                        data: {
                            deposit_rec_id: "<?php echo htmlfix($_REQUEST['deposit_rec_id']); ?>",
                            last_update_time: $('input#RecLastChangeDatetime')[0].value,
                            action: 'recon_with_discrep'
                        },
                        error: function() {
                            alert("Connection error, Can't reconcile.");
                            //inputs[3].value = $(inputs[3]).attr('oldvalue');
                            remove_wait();
                        },
                        success: function(answer){
                            switch(answer[0]) {
                                case '0':
                                    back_to_workflow();
                                break;
                                case '2':
                                    alert('Deposit was reconciled by another user');
                                    back_to_workflow();
                                break;
                                case '3':
                                    alert('Deposit was changed by another user');
                                    window.location.reload();
                                break;
                                case '4':
                                    alert('Deposit has ambiguos indexes');
                                    window.location.reload();
                                break;
                                case '5':
                                    alert('SorterData was updated');
                                    window.location.reload();
                                break;
                                case '6':
                                    window.location.href='?c=suspect_enter&deposit_rec_id=' + '<?php echo htmlfix($_REQUEST['deposit_rec_id']); ?>';
                                break;
                                case '1':
                                    alert('Record was changed by another user');
                                    window.location.reload()
                                break;
                                default:
                                    alert(answer);
                            };
                        }
                    });
                };
                function control(elem) {
                   var stat=$('.stat:visible');
                   var flag = true;
                   $(stat).each(function(){
                       if (this.value=='' || this.value.length != $(this).attr('maxlength')) {
                           $(this).css('background-color','yellow');
                           flag = false;
                       };
                   });
                   if(flag==false) {
                       alert('<?php echo htmlfix($_SESSION[$program]['lang']['fill_required_fields']) ?>');
                   } else {
                        $.ajax({
                            type: "POST",
                            url: "?c=suspect_enter&deposit_rec_id=<?php echo htmlfix($_REQUEST['deposit_rec_id']); ?>",
                            async: false,
                            data: {
                                deposit_rec_id: "<?php echo htmlfix($_REQUEST['deposit_rec_id']); ?>",
                                last_update_time: $('input#RecLastChangeDatetime')[0].value,
                                action: 'to_control'
                            },
                            error: function() {
                                alert("Connection error, Can't reconcile.");
                                inputs[3].value = $(inputs[3]).attr('oldvalue');
                                remove_wait();
                            },
                            success: function(answer){
                                switch(answer[0]) {
                                    case '0':
                                        back_to_workflow();
                                    break;
                                    case '2':
                                        alert('Deposit was reconciled by another user');
                                        back_to_workflow();
                                    break;
                                    case '3':
                                        alert('Deposit was changed by another user');
                                        window.location.reload();
                                    break;
                                    case '4':
                                        alert('Deposit has ambiguos indexes');
                                        window.location.reload();
                                    break;
                                    case '5':
                                        alert('SorterData was updated');
                                        window.location.reload();
                                    break;
                                    case '6':
                                        window.location.href='?c=suspect_enter&deposit_rec_id=' + '<?php echo htmlfix($_REQUEST['deposit_rec_id']); ?>';
                                    break;
                                    default:
                                        alert(answer);
                                };
                            }
                        });
                   };
                };
                function s(elem) {
                    var inputs = $(elem.parentNode.parentNode).find('input');
                    if ($(inputs[2]).attr("oldvalue")=="") {
                        inputs[2].value=elem.value;
                    };
                };
                function n(elem) {
                    var inputs = $(elem.parentNode.parentNode).find('input');
                    if ($(inputs[3]).attr("oldvalue")=="") {
                        inputs[3].value=elem.value;
                    };                    
                };
                function s1(elem) {
                    var inputs = $(elem.parentNode.parentNode).find('input');
                    if ($(inputs[0]).attr("oldvalue")=="") {
                        inputs[0].value=elem.value;
                    };                    
                };
                function n1(elem) {
                    var inputs = $(elem.parentNode.parentNode).find('input');
                    if ($(inputs[1]).attr("oldvalue")=="") {
                        inputs[1].value=elem.value;
                    };                      
                };
                function update_record(elem) {
                    var olddata = [];
                    var newdata = [];
                    var inputs = $(elem.parentNode.parentNode).find('input');
                    for (var i=0; i<inputs.length; i++) {
                        olddata[olddata.length] = $(inputs[i]).attr("oldvalue");
                        newdata[newdata.length] = inputs[i].value;
                    };
                    set_wait();
                    $.ajax({
                        type: "POST",
                        url: "?c=suspect_enter&deposit_rec_id=<?php echo htmlfix($_REQUEST['deposit_rec_id']); ?>",
                        async: false,
                        data: {
                            action: 'update',
                            id: elem.parentNode.parentNode.id,
                            olddata: olddata.join('|'),
                            newdata: newdata.join('|')
                        },
                        error: function() {
                            alert("Connection error, Can't update.");
                            for(var i=0; i<inputs.length;i++) {
                                inputs[i].value = olddata[i];
                                $(inputs[i]).css('color','red');
                            };
                            remove_wait();
                        },
                        success: function(answer){
                            switch(answer[0]){
                                case "0":
                                    for(var i=0; i<inputs.length;i++) {
                                        $(inputs[i]).attr("oldvalue",newdata[i]);
                                        //$(inputs[i]).css('color','red');
                                    };
                                    break;
                                case "1":
                                    alert('Record was changed by another user.');
                                    answer = answer.substring(1);
                                    newdata = answer.split('|');
                                    for(var i=0; i<inputs.length;i++) {
                                        $(inputs[i]).attr("oldvalue",newdata[i]);
                                        $(inputs[i]).val(newdata[i]);
                                        $(inputs[i]).css('color','red');
                                    };
                                    break;
                                case "2":
                                    alert('Reconciliation was closed by another user.');
                                    window.location.href = "?c=index";
                                    break;
                                default:
                                    for(var i=0; i<inputs.length;i++) {
                                        $(inputs[i]).attr("oldvalue",olddata[i]);
                                        $(inputs[i]).css('color','red');
                                    };
                                    alert(answer);
                            };
                        }
                    });
                    remove_wait();
                };
            </script>
            <div class="container">
                <h3><?php echo $_SESSION[$program]['lang']['suspect_header']; ?></h3>
                <table class="info_table">
                    <?php $d=explode('|',$_SESSION[$program]['lang']['suspect_table_headers']); ?>
                    <tr>
                        <th><?php echo htmlfix($d[0]); ?></th>
                        <th><?php echo htmlfix($d[1]); ?></th>
                        <th><?php echo htmlfix($d[2]); ?></th>
                        <th><?php echo htmlfix($d[3]); ?></th>
                        <th><?php echo htmlfix($d[4]); ?></th>
                        <th><?php echo htmlfix($d[5]); ?></th>
                    </tr>
        <?php
        
        // получаем номиналы и количество фальшивых банкнот из данных ручного ввода
        $row = get_array_from_sql('
            SELECT
                `ReconAccountingData`.`DenomId`,
                `ReconAccountingData`.`CullCount`
            FROM 
                `cashmaster`.`ReconAccountingData`
            WHERE
                `ReconAccountingData`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                AND `ReconAccountingData`.`GradeId`=1
            ORDER BY DenomId ASC
        ;');

        // Для каждого номинала:
        foreach ($row as $key=>$value) {
            // 2. Проверяем количество серийных номеров, которые есть в таблице
            $r = fetch_row_from_sql('
                SELECT
                    count(*)
                FROM 
                    `cashmaster`.`SuspectSerialNumbs`
                WHERE
                    `SuspectSerialNumbs`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                    AND `SuspectSerialNumbs`.`DenomId`="'.addslashes($value['0']).'"
            ;');
            // Для каждого номинала делаем проверку:
            // если в таблице серийных номеров нет полного соответствия с данными ручного ввода
            if ($value[1]!=$r[0]) {
                // то удаляем все серийные номера по этому номиналу 
                do_sql('
                    DELETE FROM 
                        `cashmaster`.`SuspectSerialNumbs`
                    WHERE 
                        `SuspectSerialNumbs`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                        AND `SuspectSerialNumbs`.`DenomId`="'.addslashes($value[0]).'"
                ;');
                // и заполняем таблицу новым количеством пустыми полями
                for ($i = 0; $i< $value[1]; $i++) {
                    do_sql('
                        INSERT INTO `cashmaster`.`SuspectSerialNumbs`
                            (
                                `DepositRecId`,
                                `DenomId`,
                                `LeftSeria`,
                                `LeftNumber`,
                                `RightSeria`,
                                `RightNumber`
                            )
                        VALUES
                            (
                                "'.addslashes($_REQUEST['deposit_rec_id']).'",
                                "'.addslashes($value[0]).'",
                                "",
                                "",
                                "",
                                ""
                            )
                    ;');                    
                };
            };
            // выводим позиции для ввода серийных номеров
            $pos = get_array_from_sql('
                SELECT
                    `SuspectSerialNumbs`.`SequenceId`,
                    `Currency`.`CurrSymbol`,
                    `Denoms`.`Value`,
                    `SuspectSerialNumbs`.`LeftSeria`,
                    `SuspectSerialNumbs`.`LeftNumber`,
                    `SuspectSerialNumbs`.`RightSeria`,
                    `SuspectSerialNumbs`.`RightNumber`,
                    `Currency`.`SeriaLength`,
                    `Currency`.`NumberLength`
                FROM 
                    `cashmaster`.`SuspectSerialNumbs`
                LEFT JOIN
                    Denoms ON Denoms.DenomId = SuspectSerialNumbs.DenomId
                LEFt JOIN
                    Currency ON Currency.CurrencyId=Denoms.CurrencyId
                WHERE
                    `SuspectSerialNumbs`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                    AND `SuspectSerialNumbs`.`DenomId`="'.addslashes($value[0]).'"
                ORDER BY SequenceId ASC
            ;');
            // в виде формы с полями и индексами
            include './app/view/reconciliation/suspect_enter.php';
            
        };
        
        include_once './app/model/reconciliation/get_recon_details_by_id.php';
        $row = get_recon_details_by_id($_REQUEST['deposit_rec_id']);
        
        $b = explode('|', $_SESSION[$program]['lang']['suspect_buttons']);
        
        //echo '<pre>';
        //print_r($row);
        //echo '</pre>';
?>
                </table>
                <br/>
                <button onclick="window.location.href='?c=reconciliation&separator_id=<?php echo $row['DataSortCardNumber']; ?>'" class="btn btn-primary btn-large">
                    <?php echo htmlfix($b[0]); ?>
                </button>
                <?php 
                    if($_SESSION[$program]['user_role_id']!=2){
                        ?>
                            <button 
                                class="btn btn-danger btn-large" 
                                onclick="control(this);">
                                <?php echo htmlfix($b[1]); ?>
                            </button>
                        <?php
                    } else {
                        ?>
                            <button 
                                class="btn btn-danger btn-large" 
                                onclick="recon_with_discrep(this);">
                                <?php echo htmlfix($b[2]); ?>
                            </button>                
                        <?php
                    };
                ?>
                <input type="hidden" id="RecLastChangeDatetime" value="<?php echo $row['RecLastChangeDatetime']; ?>"/>
            </div>
