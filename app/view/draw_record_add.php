<?php

/*
 * Record Edit view
 */

    function draw_record_add($record){

        global $db;
        global $c;
        global $program;

        
        $record['type'] = $record['type_for_new'];

        include 'app/view/record/start.php';
        
        if ( $record['add'] != true ) {
            ?>
                <div class="container">
                    <div class="alert alert-error">  
                      <a class="close" data-dismiss="alert">×</a>  
                      <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                      <br/>
                          <?php echo htmlfix($w[6]); ?>
                    </div> 
                    <br/>
                    <a 
                       onclick="if($('input#save').is(':visible')==true){ return confirm('<?php echo $w[8],'.\n',$w[9]; ?>?'); };"
                       class="btn btn-primary btn-large" 
                       href="<?php echo $record['back_page']; ?>">
                        <?php echo htmlfix($b[7]); ?>
                    </a>
                </div>
            <?php    
            exit;
        };

        $data = $def;
        $confirm_button = false;


        if (isset($_POST['action'])) {
            // Блокируем таблицу
            do_sql('LOCK TABLES '.addslashes($record['table']).' WRITE;');
            include 'app/view/record/check_unique.php';
            // Проверяем данные на уникальность пережд добавлением
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
                 do_sql('UNLOCK TABLES;');
            } else {
                if ($_POST['action']==$b[9]) {
                    if(isset($_POST['confirmation']) AND $_POST['confirmation']==$b[10]) {
                        // Добавляем новую запись
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
                        do_sql('UNLOCK TABLES;');
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
                        exit;
                    } else {
                        $confirm_button = true;  
                        do_sql('UNLOCK TABLES;');
                    };
                };
                if ($_POST['action']==$b[0]) {
                    // Возврат к редактированию новой записи 
                    foreach ($record['formula'] as $key => $value) {
                        foreach ($_POST as $k=>$v) {
                            if($value==$k) {
                                $data[$key]=$v;
                            };
                        };
                    };
                };
            };
        };
        do_sql('UNLOCK TABLES;');

    ?>
    <div class="container">
        <?php 
            //print_r($h);
            if ($confirm_button==true) {
                ?>
                    <h3><?php echo htmlfix($h[3]); ?></h3>
                <?php
            };
        ?>
        <form method="POST" onsubmit="return check_required();">
        <input type="hidden" name="olddata" value="<?php echo htmlfix(implode('|', $data ));  ?>">
        <table class="record" id="<?php echo htmlfix($table); ?>">
            <?php 

                if ($confirm_button==true) {
                    $data = array();
                    $label = array();
                    foreach ($record['formula'] as $key=>$value) {
                       foreach ($_POST as $k=>$v) {
                           if($k==$value) {
                               $label[$key] = $record['labels'][$key];
                               $data[$key] = $v;
                           };
                       };
                    };
                    foreach ($_POST as $key=>$value) {
                        echo '<input type="hidden" name="'.htmlfix($key).'" value="'.htmlfix($value).'"/>';
                    };
                    foreach ($label as $key=>$value) {

                        if ( isset($highlight[$key]) AND $highlight[$key] == 1 ) {
                            $color = 'red';
                            $d = $_POST[$record['formula'][$key]];
                        } else {
                            $color = 'black';
                            $d = $data[$key];
                        };

                       echo '<tr>';
                       echo '<th align="right">',$value,':</th>';
                       switch ($record['type'][$key]) {
                           case 'select':
                               include 'app/view/record/readonly_fields/select.php';
                               break;
                           case 'checker':
                               include 'app/view/record/readonly_fields/checker.php';
                               break;
                           case 'logical':
                               include 'app/view/record/readonly_fields/logical.php';
                           default:
                               echo '<td style="padding-left:10px;">',
                                        htmlfix(isset($data[$key])?$data[$key]:''),'
                                     </td>';
                               break;
                       };
                       echo '</tr>';
                    };                
                } else {
                    // Выводим подтверждение
                    foreach ($record['labels'] as $key=>$value) {
                        if ( isset($highlight[$key]) 
                                AND $highlight[$key]==1 ) {
                            $color = 'red';
                            $d = $_POST[$record['formula'][$key]];
                        } else {
                            $color = 'black';
                            $d = $data[$key];
                        };

                        include 'app/view/record/field_router.php';
                    };                
                };

            ?>
        </table>
        <?php 
            include 'app/view/record/required_field_message.php';
        ?>
        <br/>
        <?php 
            if ($confirm_button==true ) {
                ?>
                <input 
                       style="<?php echo $display_button[0]; ?>" 
                       class="btn btn-primary btn-large" 
                       type="submit" name="action" value="<?php echo htmlfix($b[0]); ?>">
                <input 
                       style="<?php echo $display_button[0]; ?>" 
                       class="btn btn-danger btn-large" 
                       type="submit" name="confirmation" value="<?php echo htmlfix($b[10]); ?>">
                <?php 
            } else {
                ?>
                <a 
                    onclick="if(check_fields()){ return confirm('<?php echo $w[8],'.\n',$w[9]; ?>?'); };"
                   class="btn btn-primary btn-large" 
                   href="<?php echo $record['back_page']; ?>">
                    <?php echo htmlfix($b[7]); ?>
                </a>
                <input 
                       style="<?php echo $display_button[0]; ?>" 
                       class="btn btn-danger btn-large" 
                       type="submit" name="action" value="<?php echo htmlfix($b[9]); ?>">
                <?php 
            };
        ?>
        </form>
    </div>
    <?php
    };
?>