<?php

    if (!isset($c)) exit;
    
    $data['title'] = "PHP Obfuscator / Deobfuscator";
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/draw_table.php';

    include "./app/model/d.php";
    $php = (isset($_POST['source']))?$_POST['source']:'<?php 
        include \'./app/view/html_header.php\'; 
        echo "Hello world!"; 
    ?> ';
?>
    <style>
        textarea.code {
            width: 1000px;
            height: 200px;
        }
    </style>
    <div class="container">
        <p>Enter php code:</p>
        <form method="POST" action="?c=obfuscator">
            <textarea class="code" name="source"><?php  
                echo $php;
            ?></textarea>
            <br/>
            <input type="submit" name="obfuscate" value="Obfuscate">
            <input type="submit" name="decode" value="Decode">
        </form>
        <?php 
            if (isset($_POST['decode'])) {
                $s=d($php); 
            } else {
                $s=e($php); 
            };
        ?>
        <textarea class="code"><?php echo $s; ?></textarea>        
    </div>
