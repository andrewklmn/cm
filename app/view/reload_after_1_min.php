<?php

/*
 * Reload Documnet after 1 min
 */
    
?>
<script>
    delay = 60000;
    timeout = setTimeout('reload_page();',delay);
    var t;
    
    $(document).ready(function (){
        $(document.body).bind("mousemove keypress",function (){
            clearTimeout(timeout);
            timeout = setTimeout('reload_page();',delay);
        });    
    });
    function reload_page(){
        // Пытаемся обновить страницу через драйвер, если он задан
        $('form#driver').submit();

        // Проверяем не заполнено ли частично поле ввода номера карты
        var v = '';
        var focus = $('input#focus');
        if (focus.length==1) {
            v = focus[0].value;
        };
        if (v=='') {
            // Если поле отсутствует или незаполнено, то устанавливаем ообновление по таймауту.
            set_wait();
            timeout = setTimeout(action_reload,300);
            //timeout = setTimeout('reload_page();',delay);
        };
    };
    function action_reload() {
        <?php 
            if (isset($_GET['c'])) {
                ?>
                    window.location.href="<?php echo '?c=',urlencode($_GET['c']); ?>";
                <?php
            } else {
                ?>
                    window.location.href="?c=index";
                <?php
            }
        ?>
    };
</script>
