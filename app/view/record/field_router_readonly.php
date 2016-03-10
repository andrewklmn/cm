<?php

/*
 * Field router 
 */
    if (!isset($c)) exit;
        
    echo '<tr>';
    echo '<th align="right">',$value,':</th>';
    switch (isset($type[$key])?$type[$key]:'readonly') {
        case 'text':
            include 'readonly_fields/text.php';
            break;
        case 'pointer':
            include 'readonly_fields/pointer.php';
            break;
        case 'select':
            include 'readonly_fields/select.php';
            break;
        case 'checker':
            include 'readonly_fields/checker.php';
            break;
        case 'logical':
            include 'readonly_fields/logical.php';
            break;
        default:
            include 'readonly_fields/readonly.php';
            break;
    };
    echo '</tr>';
    
?>
