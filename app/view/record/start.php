<?php

/*
 * Start record editing
 */

        if (!isset($c)) exit;
        
        include_once 'app/view/remove_special_symbols.php';
        
        if (!isset($record['table'])) {
            echo '<h1>Record model is not included!<h1>';
            exit;
        };
        
?>
    <style>
        table.record th {
            color: #555555;
            padding-right: 10px;
        }
        table.record td {
            font-size: 14px;
            color: black;
        }
        table.record input, table.record select {
            margin: 0px;
            font-size: 14px;
        }       
        span.checker {
            cursor: pointer;
        }
    </style>
    <script>
        function check_required() {
            var flag = true;
            $('.required').each(function(){
                if($(this).val()== '') {
                    $(this).css('background-color','yellow');
                    flag = false;
                };
            });
            if (flag == false) {
                alert('<?php echo htmlfix($_SESSION[$program]['lang']['fill_required_fields']); ?>');            
            };
            return flag;
        };
        function check_fields(){
            var flag = false;
            $('.field').each(function(){
                if($(this).val()!= $(this).attr('oldvalue')) {
                    $('input#save').show();
                    $('input#clone').hide();
                    flag = true;
                };
            });
            return flag;
        };
        function _u(elem){
            // TODO отброс ненужных символов
            //$(elem).val(rs($(elem).val()));
            if ($(elem).val()==$(elem).attr('oldvalue')) {
                $(elem).css('color','black');
                $('input#save').hide();
                $('input#clone').show();
                check_fields();
            } else {
                $(elem).css('color','darkred');
                $('input#save').show();
                $('input#clone').hide();
            };  
        };
        function _c(elem){
            var input = elem.parentNode.getElementsByTagName('INPUT')[0];
            <?php 
                if(preg_match('/(?i)msie [8-9]/',$_SERVER['HTTP_USER_AGENT'])) {
                    // if MSIE<=9
                    ?>
                        if($(elem).html() == '[ ]') {
                            input.value = "1";
                            $(elem).html('[√]');
                        } else {
                            input.value = "0";
                            $(elem).html('[ ]');
                        };
                    <?php
                } else {
                    // if MSIE>10
                    ?>
                        if($(elem).html() == '☐') {
                            input.value = "1";
                            $(elem).html('☑');
                        } else {
                            input.value = "0";
                            $(elem).html('☐');
                        };
                    <?php
                };
            ?>
            if ($(input).val()!=$(input).attr('oldvalue')) {
                $(elem).css('color','darkred');
                $('input#save').show();
                $('input#clone').hide();               
            } else {
                $(elem).css('color','black');
                $('input#save').hide();
                $('input#clone').show();
                check_fields();                
            };
        };
        /*
        function _c(elem){
            var input = $(elem.parentNode).find('input')[0];
            var prop = $(elem).prop("checked");
            var oldvalue = $(elem).attr('oldvalue');
            if (prop==true) {
                $(input).val('1');
            } else {
                $(input).val('0');
            };
            if((prop==true && oldvalue=="0")
                || (prop==false && oldvalue=="1")) {
                $(elem.parentNode).css('background-color','#FFEEAA');
                $('input#save').show();
                $('input#clone').hide();               
            } else {
                $(elem.parentNode).css('background-color','white');
                $('input#save').hide();
                $('input#clone').show();
                check_fields();                
            };
        };
        */
    </script>
<?php

        // Проверка задан ли массив локализации (для использования вне кешмастера)
        $w = ($_SESSION[$program]['lang']['record_edit_warning'])?
                explode('|', $_SESSION[$program]['lang']['record_edit_warning'])
                :explode('|', 'Record was changed by another user|Record was successfuly added|Can not clone. Wrong operation data|Record was successfuly deleted|Can not delete. Wrong operation data|Record was successfuly updated|Action is not permitted|Record has not unique fields|Data was changed|Exit without saving');
        $h = ($_SESSION[$program]['lang']['record_edit_warning'])?
                explode('|', $_SESSION[$program]['lang']['record_edit_headers'])
                :explode('|', 'Cloning confirmation|Confirm deleting|Updating confirmation|Cloning confirmation|Adding confirmation');
        $b = ($_SESSION[$program]['lang']['record_edit_buttons'])?
                explode('|', $_SESSION[$program]['lang']['record_edit_buttons'])
                :explode('|', 'Cancel|Save|Clone|Delete|Confirm saving|Confirm cloning|Confirm deleting|Back to list|Edit added record|Add new record|Confirm adding');

        // Получаем свойства полей таблицы
        $prop = get_array_from_sql('
            DESCRIBE
                '.addslashes($record['table']).'
        ;');
        
        $unique = array();
        $maxwidth = array();
        $def = array();
        $not_null = array();
        $not_null_label = '';
        
        foreach ($prop as $k=>$v) {
            if ($v[3]=='PRI') {
                $table_key = $v[0];
            };
            foreach ($record['formula'] as $key=>$value) {
                if($value == $v[0]){
                    
                    $unique[$key] = ($v[3]=='UNI')?'1':'0';
                    if ( substr($v[1], 0, 8) == 'varchar(' ) {
                        $maxwidth[$key] = str_replace(')','',str_replace('varchar(', '', $v[1]));
                    } else {
                        $maxwidth[$key] = 80;
                    };
                    
                    if (isset($record['default'][$key]) AND $record['default'][$key]!='') {
                        $def[$key] = $record['default'][$key];
                    } else {
                        $def[$key] = $v[4];
                    };
                    
                    if ($v[3]=='PRI') {
                        $type[$key] = 'readonly';
                    };
                    if ($v[2]=='NO') {
                        $not_null[$key] = true;
                        $not_null_label = $_SESSION[$program]['lang']['fields_are_required'];
                    } else {
                        $not_null[$key] = false;
                    };
                };
            };
        };
        
?>
