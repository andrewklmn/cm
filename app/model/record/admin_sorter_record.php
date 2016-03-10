<?php

    /*
     * Sorter record
     */

    $record['table'] = 'Machines';
    $record['labels'] = explode('|',$_SESSION[$program]['lang']['sorter_edit_labels']);
    $record['formula'] = explode('|', 'SorterName|SerialNumber|SorterTypeId|SorterVariant|Softwarerelease|CashRoomId|MachineLogicallyDeleted|NetworkAddress|NetworkMask|NetworkPort|MachineLogin|MachinePass|MachineDatabaseName|MachineConnectionDirectory|MachineConnectionOn');
    //$record['default'] = explode('|', 'Name|Number|1|New|1.0.1|2|0|192.168.0.1|255.255.255.0||||||');
    $record['type'] = explode('|','text|text|select|text|text|pointer|checker|text|text|text|text|text|text|text|logical');
    $record['type_for_new'] = explode('|','text|text|select|text|text|select|checker|text|text|text|text|text|text|text|logical');
    $record['select'] = explode('|','||
        SELECT
            `SorterTypes`.`SorterTypeId`, `SorterTypes`.`SorterType`
        FROM `cashmaster`.`SorterTypes`
        ORDER BY `SorterTypes`.`SorterType` ASC;
    |||
        SELECT
            `CashRooms`.`Id`,
            `CashRooms`.`CashRoomName`
        FROM 
            `cashmaster`.`CashRooms`
        ORDER BY
            `CashRooms`.`CashRoomName` ASC;
    ');
    $record['width'] = explode('|','220|220|220|220|220|220|220|220|220|220|220|220|220|220');
    $record['back_page'] = '?c=index';
    $record['confirm_update'] = true;
    // ================ Possible action ==========
    $record['clone'] = true;
    $record['add'] = true;
    $record['delete'] = false;
    $record['edit'] = true;
        
?>
