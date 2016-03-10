<?php

/*
 * Выбор исходного и конечного языка для перевода
 */

        if (!isset($c)) exit;
    
        echo '<div class="container">';
        // Получаем список файлов с локализацией
        $list = scandir('app/model/lang/');
        // Предлагаем пользователю выбор языка
        echo '<form method="POST">';
        echo 'Source language: ';
        echo '<select name="source_lang">';
        foreach ($list as $value) {
            if ($value!='.' 
                    AND $value!='..' 
                    AND $value!='index.php' ) {
                $t = explode('.', $value);
                echo '<option value="',ucfirst($t[0]),'">',  ucfirst($t[0]),'</option>';
            };
        };
        echo '</select>';
        echo '<br/><br/>Target language: ';
        echo '<input type="text" name="target_lang" id="target_lang" value="',(isset($_POST['target_lang']))?$_POST['target_lang']:'','"/>';
        echo '<select style="width:30px;" onchange="$(\'input#target_lang\').val(this.value)">';
        echo '<option value="" selected></option>';
        foreach ($list as $value) {
            if ($value!='.' 
                    AND $value!='..' 
                    AND $value!='index.php' ) {
                $t = explode('.', $value);
                echo '<option value="',ucfirst($t[0]),'">',ucfirst($t[0]),'</option>';
            };
        };
        echo '</select> ';        
        echo '<br/><br/><input class="btn btn-warning btn-large" type="submit" name="action" value="Start translation"/>';
        echo '</form>';
        echo '</div>';
?>
