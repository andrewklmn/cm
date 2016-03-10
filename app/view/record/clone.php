<?php

/*
 * Clone action
 */
    if (!isset($c)) exit;
    
    /*
    echo '<pre>';
    print_r($_POST);
    print_r($_GET);
    echo '</pre>';
    */

    if (isset($_POST['confirmation']) AND $_POST['confirmation']==$b[0]) {
        //$type = $type_for_new;        
    } else {
        $record['type'] = $record['type_for_new'];
    };
    
    include 'check_unique.php';

    // Создаем новую запись с такими же значениями
    if (isset($_POST['confirmation'])) {
        if($_POST['confirmation']==$b[5]) {
            //Получено подтверждение
            if ($not_unique_flag) {
            // Запись не уникальна, добавление невозможно
            ?>
                <div class="container">
                    <div class="alert alert-error">  
                      <a class="close" data-dismiss="alert">×</a>  
                      <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                      <br/>
                          <?php echo htmlfix($w[7]),': ',implode(',',$not_unique); ?>
                    </div> 
                </div>
             <?php
            // Снова запрашиваем подтверждение клонирования
            // Форма запроса подтверждения
            do_sql('UNLOCK TABLES;');
            ?>
                <div class="container">
                <h3><?php echo htmlfix($h[0]); ?></h3>
                <form method="POST" onsubmit="return check_required();">
                    <input type="hidden" name="olddata" value="<?php echo htmlfix(implode('|', $data ));  ?>">
                    <table class="record">
                        <?php 
                            foreach ($record['labels'] as $key=>$value) {
                                if ( $highlight[$key] == 1 ) {
                                    $color = 'red';
                                    $display_button = array(
                                        '',
                                        'display:none;'
                                    );
                                } else {
                                    $color = 'black';
                                    $d = $data[$key];
                                };
                                
                                if (isset($_POST[$record['formula'][$key]])) {
                                    $d = $_POST[$record['formula'][$key]];
                                } else {
                                    $d = $data[$key];
                                };
                                
                                include 'app/view/record/field_router.php';
                            };
                        ?>
                    </table>
                    <?php 
                        include 'app/view/record/required_field_message.php';
                    ?>                    
                    <br/>
                    <input type="hidden" name="action" value="<?php echo htmlfix($b[2]); ?>">
                    <input class="btn btn-primary btn-large" 
                           type="submit" 
                           name="confirmation" 
                           value="<?php echo htmlfix($b[0]); ?>">
                    <input 
                           class="btn btn-danger btn-large" 
                           type="submit" 
                           name="confirmation" 
                           value="<?php echo htmlfix($b[5]); ?>">
                </form>
                </div>
            <?php
            exit;
            } else {
                $fields = array();
                $values = array();
               foreach ($record['formula'] as $key=>$value) {
                   foreach ($_POST as $k=>$v) {
                       if($k==$value) {
                        $fields[$key] = $record['formula'][$key];
                        $values[$key] ='"'.addslashes($_POST[$record['formula'][$key]]).'"';
                       };
                   };
               };
                // Добавляем данные в таблицу
                do_sql('
                    INSERT INTO '.addslashes($record['table']).'
                        ('.implode(',', $fields).')
                    VALUES
                        ('.implode(',', $values).')
                ;');
                // Получаем номер добаленной записи
                $row = fetch_row_from_sql('
                    SELECT
                        MAX('.addslashes($table_key).')
                    FROM
                        '.addslashes($record['table']).'
                ;');

                // Выводим сообщение что добавлено
                // С предложением либо вернуться к списку, либо редактировать новую запись
                ?>
                    <div class="container">
                        <div class="alert alert-success">  
                          <a class="close" data-dismiss="alert">×</a>  
                          <strong><?php echo $_SESSION[$program]['lang']['success']; ?>!</strong>
                          <br/>
                              <?php echo htmlfix($w[1]); ?>
                        </div> 
                        <br/>
                        <a class="btn btn-primary btn-large"
                           href="<?php echo $record['back_page']; ?>"><?php echo htmlfix($b[7]); ?></a>
                        <?php 
                            if ($record['edit']==true) {
                                $_GET['id']=$row[0];
                                $param = array();

                                $temp = explode('_',$_GET['c']);
                                unset($temp[count($temp)-1]);
                                $_GET['c']=  implode('_', $temp).'_edit';

                                foreach ($_GET as $key=>$value) {
                                    $param[]=$key.'='.$value; 
                                };
                            ?>
                            <a class="btn btn-primary btn-large"
                               href="<?php echo '?'.implode('&',$param); ?>"><?php echo htmlfix($b[8]); ?></a>
                            <?php 
                            };
                        ?>
                    </div>
                <?php

                do_sql('UNLOCK TABLES;');
                exit;
            };
        } else {
            // Не получено подтверждение - Выводим форму снова
            if ($_POST['confirmation']!=$b[0]) {
                // Не отмена подтверждения
                ?>
                 <div class="container">
                     <div class="alert alert-error">  
                       <a class="close" data-dismiss="alert">×</a>  
                       <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                       <br/>
                           <?php echo htmlfix($w[2]); ?>
                     </div> 
                 </div>
                 <?php   
            } else {
                // Отмена подтвержения
                unset($highlight);
            };
        };
    } else {
        if ($not_unique_flag) {
            // Запись не уникальна, добавление невозможно
            ?>
                <div class="container">
                    <div class="alert alert-info">  
                      <a class="close" data-dismiss="alert">×</a>  
                      <strong><?php echo $_SESSION[$program]['lang']['attention']; ?>!</strong>
                      <br/>
                          <?php echo htmlfix($w[7]),': ',implode(',',$not_unique); ?>
                    </div> 
                </div>
             <?php
        };
        //Запрашиваем подтверждение клонирования
        // Форма запроса подтверждения
        do_sql('UNLOCK TABLES;');
        ?>
            <div class="container">
            <h3><?php echo htmlfix($h[0]); ?></h3>
            <form method="POST" onsubmit="return check_required();">
                <input type="hidden" name="olddata" value="<?php echo htmlfix(implode('|', $data ));  ?>">
                <table class="record">
                    <?php 
                    
                        //echo '<pre>';
                        //print_r($highlight);
                        //echo '</pre>';
                    
                        foreach ($record['labels'] as $key=>$value) {
                            if ( isset($highlight[$key]) AND $highlight[$key]==1 ) {
                                $color = 'red';
                                $d = $_POST[$record['formula'][$key]];
                                $display_button = array(
                                    '',
                                    'display:none;'
                                );
                            } else {
                                $color = 'black';
                                $d = $data[$key];
                            };
                            include 'app/view/record/field_router.php';
                        };
                    ?>
                </table>
                <?php 
                    include 'app/view/record/required_field_message.php';
                ?>     
                <br/>
                <input type="hidden" name="action" value="<?php echo htmlfix($b[2]); ?>">
                <input class="btn btn-primary btn-large" 
                       type="submit" 
                       name="confirmation" 
                       value="<?php echo htmlfix($b[0]); ?>">
                <input 
                       class="btn btn-danger btn-large" 
                       type="submit" 
                       name="confirmation" 
                       value="<?php echo htmlfix($b[5]); ?>">
            </form>
            </div>
        <?php
        exit;                        
    };
?>
