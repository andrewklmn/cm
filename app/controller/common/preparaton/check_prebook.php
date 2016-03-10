<?php

/* 
 * Проверяем не является ли номер PackId Предподготовки
 */


    if (!isset($c)) exit;
    
    if (isset($_GET['separator_id'])) {
        $sql = '
            SELECT 
                `Prebook`.`Id`,
                `Prebook`.`PackId`,
                `Prebook`.`Filename`
            FROM `cashmaster`.`Prebook`
            WHERE
                `CashRoomId`="'.addslashes($_SESSION[$program]['UserConfiguration']['CashRoomId']).'"
                 AND `Prepared` = 0
                 AND `CustomerApproved` = 1
                 AND `PackId` = "'.  addslashes($_GET['separator_id']).'"
        ;';
        $rows = get_array_from_sql($sql);
        if (count($rows)>0) {
            if (!isset($_POST['create'])) {
                
                // Есть пребуки с таким номером упаковки
                // Предлагаем создать подготовку
                $data['title'] = $_SESSION[$program]['lang']['operator_workflow'];
                include './app/model/menu.php';
                include './app/view/page_header_with_logout.php';
                include './app/view/set_remove_wait.php';
                ?>
                    <div class="container alert-success alert">
                        <h3><?php echo htmlfix($_GET['separator_id'].' - '.$_SESSION[$program]['lang']['packid_for_prebook']); ?>!</h3>
                        <h4><?php echo htmlfix($_SESSION[$program]['lang']['create_preparation_for_prebook']); ?>?</h4>
                        <br/>
                        <form method="POST">
                            <input 
                                id="yes" 
                                class="btn btn-large btn-danger" 
                                type="submit" 
                                name="create" 
                                value="<?php echo htmlfix($_SESSION[$program]['lang']['yes']); ?>"/>
                            <input 
                                id="no" 
                                class="btn btn-large btn-primary" 
                                type="submit" 
                                name="create" 
                                value="<?php echo htmlfix($_SESSION[$program]['lang']['no']); ?>"/>
                        </form>
                        <font style="font-size: 10px;">
                            <?php echo htmlfix($_SESSION[$program]['lang']['prep_key_instruction']); ?>.
                        </font>
                    </div>
                    <script>
                        $('input#yes').focus();
                        $('body').keyup(function(event){
                            switch(event.keyCode) {
                                case 27:
                                    $('input#no').click();
                                    break;
                                case 13:
                                    //$('input#yes').click();
                                    break;
                            };
                        });
                    </script>
                <?php
                exit;
            } else {
                if (isset($_POST['create']) AND $_POST['create']==$_SESSION[$program]['lang']['no']) {
                    
                    // Ничего не делаем, так как отказались создавать
                    
                } else {
                    
                    // Для первого найденного PackID создаём рек. Для этого проводим карточкой разделителем
                    
                    echo '<pre>';
                    print_r($_POST);
                    echo '</pre>';
                    
                    if (!$_POST['link_to_separator'] OR $_POST['link_to_separator']=='') {
                        ?>
                            <form method="POST">
                                <input type="hidden" name="separator_id" value="<?php echo htmlfix($_POST['separator_id']); ?>"/>
                                <br/>
                                <input id="field" name="link_to_separator" value=""/>
                                <br/>
                                <input type="hidden" name="create" value="<?php echo htmlfix($_POST['create']); ?>"/>
                                <input type="submit" value="Submit"/>
                            </form>
                        <?php
                    } else {
                        echo 'Создаём подготовку по пребуку';    
                        echo '<br/><br/>Если в текущем ТАСКРЕКАЛК все Пекайди Препаред, то файл удаляем физически.';
                    };
                    
                    
                    
                    ?>
                        <br/>
                        <a class="btn btn-large btn-primary" href="?c=index">
                            <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
                        </a>
                    <?php
                    exit;
                };
            };
        };
    };
    
    //print_r($_POST);
    
?>