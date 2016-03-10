<?php

/*
 * Контроллер мультиязычности
 */
    
    $data['title'] = "User interface translator";
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    
    if (isset($_POST['saver']) AND $_POST['saver']=="ok") {
        // Сохраняем данные в файл с языком 
        $t = array();
        
        // Сортируем массив по индексам
        ksort($_POST);

        foreach ($_POST as $key=>$value) {
            if ($key!="saver"
                    AND $key!="target_lang"
                    AND $key!="source_lang") {
                $t[]='\''.$key.'\'=>\''.str_replace("'", "\'", $value).'\'';
            };
        };
        
        $text = '<?php 
    /*
     * '.ucfirst(strtolower($_POST['target_lang'])).' localization
     */
     
    $_SESSION[$program][\'lang\'] = array(
        '.implode(',
        ', $t).'
    ); 
?>';
        file_put_contents('app/model/lang/'.strtolower($_POST['target_lang']).'.php', $text);
        $data['success'] = 'Language file was saved.';
        include './app/view/success_message.php';
        
    } else {
    
        // Если не выбраны языки для перевода, то выбираем
        if(!isset($_POST['source_lang'])) {
            include './app/view/select_language.php';
            exit;
        };

        // Если выбраны одинаковые языки для перевода, то выбираем
        if ($_POST['source_lang']==$_POST['target_lang']) {
            $data['error'] = 'Source and target languages are not different!';
            include './app/view/error_message.php';
            include './app/view/select_language.php';
            exit;
        };

        // Если не стали создавать новый файл с языком
        if(isset($_POST['confirmation']) AND $_POST['confirmation']=='Back to select languages') {
            $_POST['target_lang'] = '';
            include './app/view/select_language.php';
            exit;
        };

        // Ищем конечный язык в списке файлов локализации
        $list = scandir('app/model/lang/');
        $flag = false;
        foreach ($list as $value) {
            if (strtolower($value)==strtolower($_POST['target_lang']).'.php') $flag = true;
        };
        // Если файла для конечного языка не существует, то запрос на создание
        if ($flag == false 
                AND isset($_POST['confirmation'])
                AND $_POST['confirmation']!='Create new language file') {
            // Приводим название языка к правильному виду с большой буквы и маленькими
            $_POST['target_lang'] = ucfirst(strtolower($_POST['target_lang']));
            echo '<div class="container">';
            echo '<br/>';
            $data['info_header'] = 'Attention';
            $data['info_text'] = 'File "'.ucfirst(strtolower($_POST['target_lang'])).'" for tagret language does not exist.';

            //echo '<pre>';
            //print_r($_POST);
            //echo '</pre>';

            include './app/view/info_message.php';
            echo '<br/><form method="POST">';
            include './app/view/repost_post.php';
            echo '<input class="btn btn-primary btn-large" type="submit" name="confirmation" value="Back to select languages"/>';
            echo ' ';
            echo '<input  class="btn btn-warning btn-large" type="submit" name="confirmation" value="Create new language file"/>';
            echo '</form>';
            echo '</div>';
            exit;
        };
        // Если был запрос на создание нового файла с языком, то
        if (isset($_POST['confirmation']) AND $_POST['confirmation']=='Create new language file') {
            // Создаем новый файл для нового языка из файла исходного языка
            $file = 'app/model/lang/'.strtolower($_POST['source_lang']).'.php';
            $newfile = 'app/model/lang/'.strtolower($_POST['target_lang']).'.php';
            if (!copy($file, $newfile)) {
                echo '<div class="container">';
                $data['error'] = 'Can not create new language file!';
                include './app/view/error_message.php';
                echo '<br/><br/><a class="btn btn-primary btn-large" href="?c=index">Back to index page</a>';
                echo '</div>';
                exit;
            };
        };
    
    };
    
    // Открываем редактор интерфейса
    echo '<div class="container">';
    // Считываем исходный язык
    eval("include 'app/model/lang/".strtolower($_POST['source_lang']).".php';");
    $source = $_SESSION[$program]['lang'];
    // Считываем исходный язык
    if (file_exists("app/model/lang/".strtolower($_POST['target_lang']).".php")) {
        eval("include 'app/model/lang/".strtolower($_POST['target_lang']).".php';");
    };
    $target = $_SESSION[$program]['lang'];
    /*
    echo '<pre>';
    print_r($source);
    echo '</pre>';
    echo '<br/>';
    echo '<br/>';
    echo '<pre>';
    print_r($target);
    echo '</pre>';
     * 
     */
    ?>
    <style>
        table.translation {
            table-layout:fixed;
            border-collapse: collapse;
        }
        table.translation td {
            word-wrap: break-word;
            overflow-wrap:break-word;
            border: black thin solid;
            padding: 3px;
        }
        table.translation th {
            word-wrap: break-word;
            overflow-wrap:break-word;
            border: black thin solid;
        }
        input.stat {
            width: 800px;
            color: darkred;
        }
        th.up {
            background-color: gray;
            padding: 5px;
            color: white;
        }
    </style>
    <script>
        function button_driver(elem) {
            if (elem.value!=$(elem).attr('oldvalue')) {
                $(elem).css('background-color','#FFEECC');
                $('button#save').show();
            } else {
                $(elem).css('background-color','white');
            };
        }
        function save_file() {
            set_wait();
            $('form#text').submit();
        };
    </script>
    <form method="POST" id="text">
        <input type="hidden" name="saver" value="ok"/>
        <input type="hidden" name="target_lang" value="<?php echo $_POST['target_lang']; ?>"/>
        <input type="hidden" name="source_lang" value="<?php echo $_POST['source_lang']; ?>"/>
        <table class="translation">
            <tr>
                <th class="up">Index</th>
                <th class="up">Text</th>
            </tr>
            <?php
                foreach ($source as $key=>$value) {
                    echo '<tr>';
                    echo '<th>',htmlfix($key),'</th>';
                    echo '<td><font style="color:blue;">',htmlfix($value),'</font>';
                    if (isset($target[$key])) {
                        echo '<br/><input autocomplete="off" onblur="button_driver(this);" class="stat" type="text" name="',htmlfix($key),'" value="',htmlfix($target[$key]),'" oldvalue="',htmlfix($target[$key]),'"/></td>';
                    } else {
                        echo '<br/><input autocomplete="off" onblur="button_driver(this);" class="stat" type="text" name="',htmlfix($key),'" value="',htmlfix($value),'" oldvalue="',htmlfix($value),'"/></td>';
                    };
                    echo '</tr>';
                };
            ?>
        </table>
    </form>
    <br/>
    <button onclick="window.location.href='?c=index';"class="btn btn-primary btn-large">Back to index page</button>
    <button id="save" onclick="save_file();" class="btn btn-warning btn-large">Save changes</button>
</div>