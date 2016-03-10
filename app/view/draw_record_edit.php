<?php

/*
 * Record Edit view
 */

    function draw_record_edit($record){

        global $db;
        global $c;
        global $program;


        include 'record/start.php';
        
        // Блокируем таблицу
        do_sql('LOCK TABLES '.addslashes($record['table']).' WRITE;');
        // Чтение актуальных данных из таблицы БД
        $query = '
            SELECT
                '.implode(',',$record['formula']).'
            FROM 
                '.addslashes($record['table']).'
            WHERE
                `'.addslashes($record['table']).'`.`'.addslashes($table_key).'`="'.addslashes($_GET['id']).'"
        ;';
        $data = fetch_row_from_sql($query);
        $display_button = array(
            'display:none;',
            ''
        );
       
        
        if (!isset($data)) {
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
                   class="btn btn-primary btn-large" 
                   href="<?php echo $record['back_page']; ?>">
                    <?php echo htmlfix($b[7]); ?>
                </a>
            </div>
            <?php
            exit;
        };
        
        // Проверяем есть ли действия в запросе и сравниваем старое значение с текущим
        if (isset($_REQUEST['action'])) {
            // Проверяем совпадают ли текущие данные в таблице со старым значением
            if (implode('|', $data)!=$_POST['olddata']) {
                   ?>
                    <div class="container">
                        <div class="alert alert-error">  
                          <a class="close" data-dismiss="alert">×</a>  
                          <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                          <br/>
                              <?php echo htmlfix($w[0]); ?>
                        </div> 
                    </div>
                    <?php                
            } else {
                
                // Обрабортка заданного действия
                switch ($_REQUEST['action']) {
                    // DELETE action
                    case $b[3]:
                        include 'app/view/record/delete.php';
                        break;
                    // CLONE action
                    case $b[2]:
                        include 'app/view/record/clone.php';
                        break;                    
                    // UPDATE action
                    case $b[1]:
                        include 'app/view/record/update.php';
                        break;
                    default:
                        break;
                };               
            };
        };
        
        do_sql('UNLOCK TABLES;');
        ?>
            <div class="container">
                <form method="POST" onsubmit="return check_required();">
                <input type="hidden" name="olddata" value="<?php echo htmlfix(implode('|', $data ));  ?>">
                <table class="record" id="<?php echo htmlfix($record['table']); ?>">
                    <?php 

                        foreach ($record['labels'] as $key=>$value) {
                            if ( isset($highlight[$key]) 
                                    AND $highlight[$key]==1 ) {
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

                            if ($record['edit']==true) {
                                include 'record/field_router.php';
                            } else {
                                include 'record/field_router_readonly.php';
                            }
                        };
                    ?>
                </table>
                <?php 
                    include 'record/required_field_message.php';
                ?>
                <br/>
                <a 
                   onclick="if($('input#save').is(':visible')==true){ return confirm('<?php echo $w[8],'.\n',$w[9]; ?>?'); };"
                   class="btn btn-primary btn-large" 
                   href="<?php echo $record['back_page']; ?>">
                    <?php echo htmlfix($b[7]); ?>
                </a>
                <input id="save" 
                       style="<?php echo $display_button[0]; ?>" 
                       class="btn btn-warning btn-large" 
                       type="submit" name="action" value="<?php echo htmlfix($b[1]); ?>">
                <?php 
                    if($record['clone']==true) {
                        ?>
                            <input id="clone" 
                                   style="<?php echo $display_button[1]; ?>"
                                   class="btn btn-info btn-large" 
                                   type="submit" 
                                   name="action" value="<?php echo htmlfix($b[2]); ?>">
                        <?php
                    };
                    if($record['delete']==true) {
                        ?>
                            <input class="btn btn-danger btn-large" 
                                   type="submit" 
                                   name="action" value="<?php echo htmlfix($b[3]); ?>">
                        <?php
                    };
                ?>
                </form>
            </div>
        <?php
    };

?>
