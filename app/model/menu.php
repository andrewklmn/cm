<?php

/*
 * Модель меню перехода
 */

    // меню администратора
    $t = explode('|',$_SESSION[$program]['lang']['menu_admin']);
    
    $menu[1] = array(
        array('?c=index',$t[0]),
        array('?c=denoms',$t[1]),
        array('?c=valuables',$t[2]),
        array('?c=indexes',$t[3]),
        array('?c=customers',$t[4]),
        array('?c=users',$t[5]),
        array('?c=scens',$t[6]),
        array('?c=system',$t[7]),
        array('?c=backup',$t[9]),
        array('?c=update',$t[10]),
        array('?c=profile',$t[8])
    ); 
    
    // меню контролера
    $t = explode('|',$_SESSION[$program]['lang']['menu_supervisor']);
    $menu[2] = array(
        array('?c=index', $t[0]),
        array('?c=reconciled', $t[1]),
        array('?c=deposit_manager', $t[2]),
        array('?c=reports', $t[3]),
        array('?c=customers', $t[4]),
        array('?c=users', $t[5]),
        array('?c=signers', $t[6]),
        array('?c=profile', $t[7])
    );
    
    // меню оператора
    $t = explode('|',$_SESSION[$program]['lang']['menu_operator']);
    $menu[3] = array(
        array('?c=index', $t[0]),
        array('?c=reconciled', $t[1]),
        array('?c=users', $t[2]),
        array('?c=profile', $t[3])
    );
    
    // меню хера по безопасности
    $t = explode('|',$_SESSION[$program]['lang']['menu_security']);
    $menu[4] = array(
        array('?c=index', $t[0]),
        array('?c=users', $t[1]),
        array('?c=profile', $t[2])        
    ); 
    
    // меню божественной силы
    //$menu[5] = array(); 

?>
