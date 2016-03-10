<?php

/*
 *      Sample of record_edit/record_add usage
 * 

    $table = 'Machines';
    $labels = explode('|','SorterName|SerialNumber|SorterTypeId|SorterVariant|Softwarerelease|CashRoomId|MachineLogicallyDeleted|NetworkAddress|NetworkMask|NetworkPort|MachineLogin|MachinePass|MachineDatabaseName|MachineConnectionDirectory|MachineConnectionOn');
    $formula = explode('|', 'SorterName|SerialNumber|SorterTypeId|SorterVariant|Softwarerelease|CashRoomId|MachineLogicallyDeleted|NetworkAddress|NetworkMask|NetworkPort|MachineLogin|MachinePass|MachineDatabaseName|MachineConnectionDirectory|MachineConnectionOn');
    $default = explode('|', 'Name|Number|1|New|1.0.1|2|0|192.168.0.1|255.255.255.0||||||');
    $type = explode('|','text|text|select|text|text|select|checker|text|text|text|text|text|text|text|logical');
    $type_for_new = explode('|','text|text|select|text|text|select|logical|text|text|text|text|text|text|text|logical');
    $select = explode('|','||
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
    $align = explode('|','left|left|left');
    $width = explode('|','220|220|220|220|220|220|220|220|220|220|220|220|220|220');
    $back_page = '?c=index';
    $confirm_update = true;
    // ================ Possible action ==========
    $clone = true;
    $add = true;
    $delete = true;
    
     
 *  Variant for add
            include 'app/view/record_edit/record_add.php';

 *  Variant for edit
             include 'app/view/record_edit/record_edit.php';

 * 
 */

?>
