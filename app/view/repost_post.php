<?php

/*
 * Вывод всех данных массива $_POST в виде скрытых инпутов для формы
 */
    foreach ($_POST as $key=>$value) {
        echo '<input type="hidden" name="'.htmlfix($key).'" value="'.htmlfix($value).'"/>';
    };
?>
