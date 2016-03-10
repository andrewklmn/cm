<?php

/*
 * Шаблон добавления записи БД
 */

    if (!isset($c)) exit;
    
    /*
    echo '<pre>';
    print_r($_POST);
    print_r($_GET);
    echo '</pre>';
    */
    
    $type = $type_for_new;
    
    include 'start.php';

    if ( $add != true ) {
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
                   href="<?php echo $back_page; ?>">
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
        do_sql('LOCK TABLES '.addslashes($table).' WRITE;');
        include 'check_unique.php';
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
                    foreach ($formula as $key=>$value) {
                       foreach ($_POST as $k=>$v) {
                           if($k==$value) {
                            $fields[$key] = $formula[$key];
                            $values[$key] ='"'.addslashes($_POST[$formula[$key]]).'"';
                           };
                       };
                    };
                    
                    // Добавляем данные в таблицу
                    do_sql('
                        INSERT INTO '.addslashes($table).'
                            ('.implode(',', $fields).')
                        VALUES
                            ('.implode(',', $values).')
                    ;');
                    // Получаем номер добаленной записи
                    $row = fetch_row_from_sql('
                        SELECT
                            MAX('.addslashes($table_key).')
                        FROM
                            '.addslashes($table).'
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
                               href="<?php echo $back_page; ?>"><?php echo htmlfix($b[7]); ?></a>
                            <?php 
                                if ($edit==true) {
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
                foreach ($formula as $key => $value) {
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
        if ($confirm_button==true) {
            ?>
                <h3><?php echo htmlfix($h[4]); ?></h3>
            <?php
        };
    ?>
    <form method="POST" onsubmit="return check_required();">
    <input type="hidden" name="olddata" value="<?php echo htmlfix(implode('|', $data ));  ?>">
    <table class="record" id="<?php echo htmlfix($table); ?>">
        <?php 
            
                /*
                $highlight = array();
                if (isset($_POST['olddata'])) {
                    $data = explode('|',$_POST['olddata']);
                    foreach ($formula as $k=>$v) {
                        if (isset($_POST[$v])) {
                            if($_POST[$v]!=$data[$k]) {
                                $highlight[$k] = 1;
                            } else {
                                $highlight[$k] = 0;
                            };
                        } else {
                            $highlight[$k] = 0;
                        };
                    };
                };
                 * 
                 */
            
            if ($confirm_button==true) {
                $data = array();
                $label = array();
                foreach ($formula as $key=>$value) {
                   foreach ($_POST as $k=>$v) {
                       if($k==$value) {
                           $label[$key] = $labels[$key];
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
                        $d = $_POST[$formula[$key]];
                    } else {
                        $color = 'black';
                        $d = $data[$key];
                    };

                   echo '<tr>';
                   echo '<th align="right">',$value,':</th>';
                   switch ($type[$key]) {
                       case 'select':
                           include 'readonly_fields/select.php';
                           break;
                       case 'checker':
                           include 'readonly_fields/checker.php';
                           break;
                       case 'logical':
                           include 'readonly_fields/logical.php';
                       default:
                           echo '<td 
                                   style="padding-left:10px;" 
                                   align="',isset($align[$key])?$align[$key]:'left','">',
                                    htmlfix(isset($data[$key])?$data[$key]:''),'
                                 </td>';
                           break;
                   };
                   echo '</tr>';
                };                
            } else {
                // Выводим подтверждение
                foreach ($labels as $key=>$value) {
                    if ( isset($highlight[$key]) 
                            AND $highlight[$key]==1 ) {
                        $color = 'red';
                        $d = $_POST[$formula[$key]];
                    } else {
                        $color = 'black';
                        $d = $data[$key];
                    };

                    include 'field_router.php';
                };                
            };
            
        ?>
    </table>
    <?php 
        include 'required_field_message.php';
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
               href="<?php echo $back_page; ?>">
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