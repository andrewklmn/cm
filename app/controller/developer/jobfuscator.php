<?php

    if (!isset($c)) exit;
    
    $data['title'] = "JS Obfuscator / Deobfuscator";
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/draw_table.php';
    include './app/view/base_64_javascript.php';

    include "./app/model/dj.php";
    $js = (isset($_POST['source']))?$_POST['source']:'alert("Hello world!");';
?>
    <style>
        textarea.code {
            width: 1000px;
            height: 200px;
        }
    </style>
    <div class="container">
        <p>Enter javascript code:</p>
        <form method="POST" action="?c=jobfuscator">
            <textarea class="code" name="source"><?php  
                echo $js;
            ?></textarea>
            <br/>
            <input type="submit" name="obfuscate" value="Obfuscate">
            <input type="submit" name="decode" value="Decode">
        </form>

        <textarea class="code"><?php 
            if (isset($_POST['decode'])) {
                $s=d($js); 
            } else {
                $s=e($js); 
            };
            echo $s;
        ?></textarea>        
    </div>
