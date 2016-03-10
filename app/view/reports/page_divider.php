<?php

/*
 * Выводи правильный разделитель страницы в зависимости от браузера клиента
 */

    if (!isset($c)) exit;
    
?>
    <div class="no-print" style="border-bottom: red dotted medium;height: 100px;width: 800px;"></div>
    <div class="no-print" style="height: 100px;width: 800px;"></div>
<?php

    include_once 'app/model/get_browser_version.php';

    switch ($browser) {
        case 'FF':
            echo '<div style=" page-break-before: always;height:1px;"></div>';
            //echo '<br/>';
            break;
        case 'GC':
            echo '<div style=" page-break-after: always;"></div>';
            break;
        case 'IE':
            echo '<br style=" page-break-after: always;"/>';
            break;
        case 'IE9':
        case 'IE10':
        case 'IE11':
            echo '<div style=" page-break-before: always;height:1px;"></div>';
            break;

    };
    
?>
