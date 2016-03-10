<?php

/*
 * Field router 
 */
    if (!isset($c)) exit;
        
    echo '<tr>';
    echo '<th align="right">',$value,':</th>';
    switch (isset($record['type'][$key])?$record['type'][$key]:'readonly') {
        case 'text':
            include 'active_fields/text.php';
            break;
        case 'pointer':
            include 'readonly_fields/pointer.php';
            break;
        case 'select':
            include 'active_fields/select.php';
            break;
        case 'checker':
            include 'active_fields/checker.php';
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
