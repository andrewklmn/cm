<?php

/*
 * Update action
 */
    if (!isset($c)) exit;

    $unique_fields = array();
    $unique_checks = array();
    // Поиск 
    foreach (get_array_from_sql('
                SHOW INDEXES FROM 
                    `'.$record['table'].'`
                WHERE 
                    `Key_name`<>"PRIMARY"
                    AND `Non_unique`=0
             ;') as $key => $value) {
        $unique_checks[$value[2]][]='`'.$value[4].'`="'.addslashes($_POST[$value[4]]).'"';
        
        // Находим подпись по данному полю
        foreach ($record['formula'] as $k => $v) {
            if($v==$value[4]){
                $unique_fields[$value[2]][]=$record['labels'][$k];
                break;
            };
        };
        
    };
    
    $not_unique_flag = false;
    $highlight_labels = array();
    foreach ($unique_checks as $key=>$value) {
        $row = fetch_row_from_sql('
            SELECT
                count(*)
            FROM
                `'.$record['table'].'`
            WHERE
                '.implode(' AND ', $value).'
                AND `'.$table_key.'`<>"'.addslashes($_GET['id']).'"
        ;');
        if($row[0]>0) {
            $not_unique_flag = true;
            $not_unique[] = implode(' + ', $unique_fields[$key]);
            foreach ($unique_fields[$key] as $kkk=>$vvv) {
                $highlight_labels[] = $vvv;
            };
        };
    };
    
    $highlight = array();
    foreach ($record['labels'] as $key=>$value) {
        $highlight[$key] = 0;
        if (count($highlight_labels)>0) {
            foreach ($highlight_labels as $val) {
                if($value==$val) {
                    $highlight[$key] = 1;
                };
            };            
        };
    };
    
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
     } else {
        if (isset($_POST['confirmation']) OR $record['confirm_update']==false) {
            if ($record['confirm_update']==false OR $_POST['confirmation']==$b[4]) {
                // Подготовка полей
               $set = array();
               foreach ($record['formula'] as $key=>$value) {
                   foreach ($_POST as $k=>$v) {
                       if($k==$value) {
                           $set[] = $record['formula'][$key].'="'.addslashes($_POST[$record['formula'][$key]]).'"';
                       };
                   };
               };
               // Запись не менялась - обновляем
               $sql = '
                   UPDATE '.addslashes($record['table']).'
                   SET
                       '.implode(',',$set).'
                   WHERE
                       '.addslashes($table_key).'="'.addslashes($_GET['id']).'"
               ;';
               //echo $sql;
               do_sql($sql);
               ?>
               <div class="container">
                   <div class="alert alert-success">  
                     <a class="close" data-dismiss="alert">×</a>  
                     <strong><?php echo $_SESSION[$program]['lang']['success']; ?>!</strong>
                     <br/>
                         <?php echo htmlfix($w[5]); ?>
                   </div> 
               </div>
               <?php
               // Перезагрузка новых данных для вывода формы редактирования
               $data = fetch_row_from_sql($query);
            };
        } else {

           // Форма подтверждения обновления
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
           
           do_sql('UNLOCK TABLES;');
            // Форма подтверждения обновления
           ?>
               <div class="container">
                   <h3><?php echo htmlfix($h[2]); ?></h3>
               <table class="record">
                       <?php 
                           
                           foreach ($label as $key=>$value) {
                                if ( isset($highlight[$key]) AND $highlight[$key] == 1 ) {
                                    $color = 'red';
                                    $d = $_POST[$formula[$key]];
                                    $display_button = array(
                                        '',
                                        'display:none;'
                                    );
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
                                       include 'app/view/record/readonly_fields/readonly.php';
                                   default:
                                       echo '<td style="padding-left:10px;">',
                                                htmlfix(isset($data[$key])?$data[$key]:''),'
                                             </td>';
                                       break;
                               };
                               echo '</tr>';
                           };
                       ?>
                   </table>
                   <br/>
                   <form method="POST">
                       <input type="hidden" name="olddata" value="<?php echo htmlfix(implode('|', $data ));  ?>">
                       <?php 
                           // repost POST data
                           foreach ($_POST as $key=>$value) {
                               echo '<input type="hidden" name="'.htmlfix($key).'" value="'.htmlfix($value).'"/>';
                           };
                       ?>
                       <input class="btn btn-primary btn-large" 
                              type="submit" 
                              name="confirmation" 
                              value="<?php echo htmlfix($b[0]); ?>">
                       <input 
                              class="btn btn-danger btn-large" 
                              type="submit" 
                              name="confirmation" 
                              value="<?php echo htmlfix($b[4]); ?>">
                   </form>
               </div>
           <?php
            exit;
        }; 
     }; 
?>
