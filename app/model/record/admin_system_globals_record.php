<?php

    /*
     * Sorter record
     */

    // Получаем список файлов с локализацией
    $list = scandir('app/model/lang/');
    // Формируем запрос для генерации списка
    $record['select'] = array();
    foreach ($list as $value) {
        if ($value!='.' AND $value!='..' AND $value!='index.php' ) {
            $t = explode('.', $value);
            $select[]='(SELECT "'.ucfirst($t[0]).'","'.ucfirst($t[0]).'")';
            //(select 'Russian', 'Russian')
        };
    };

    $record['table'] = 'SystemGlobals';
    $record['labels'] = explode('|', $_SESSION[$program]['lang']['system_edit_headers']);
    $record['formula'] = explode('|', 'CashCenterName|CashCenterCity|CashCenterCode|KPCode|OKATOCode|ComplexName|DefaultLanguage|AllowRecBySupervisor|FastenUserToIp|AutoArchivePeriod|LeaveDataFor');
    //$record['default'] = explode('|', '|');
    $record['type'] = explode('|','text|text|readonly|text|text|text|select|checker|checker|text|text');
    $record['type_for_new'] = explode('|','text');
    $record['select'] = explode('|','||||||
        '.implode(' UNION ', $select).'
    ||');
    $record['width'] = explode('|','680|450|400|350|350|680|100|100');
    $record['back_page'] = '?c=indexes';
    $record['confirm_update'] = false;
    // ================ Possible action ==========
    $record['clone'] = false;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
        
?>
