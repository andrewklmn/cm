<?php

/*
 * Устанавливает фильтр допустимых символов на поля класса stat
 */

?>
<script>
    $('.stat').bind('change',function(){
        this.value = rs(this.value);
    });
    $('.search-query').bind('change',function(){
        this.value = rs(this.value);
    });
</script>