<?php

/*
 * Контроль наличия подозрительных и введенных серийных номеров
 */

    // Если запрошена страница напрямую, то выход.
    if (!isset($c)) exit;
    
    // Проверяем есть ли подозрительные в ручном вводе
    $row = get_array_from_sql('
        SELECT
            `ReconAccountingData`.`DenomId`,
            `ReconAccountingData`.`CullCount`
        FROM 
            `cashmaster`.`ReconAccountingData`
        WHERE
            `ReconAccountingData`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
            AND `ReconAccountingData`.`GradeId`=1
            AND `ReconAccountingData`.`CullCount`>0
    ;');
    
    if (count($row)>0) {
        do_sql('UNLOCK TABLES;');
        echo 6;
        exit;
    };
    
    
    /*
    if ($c!='operator/recon_to_control' AND $c!='supervisor/recon_to_control') { 
        include './app/view/html_header.php';
        echo $c,' ';
        echo 'Wrong request!';
        exit; 
    };
     * 
     */
    
    /*
    
    // если есть поля с пустыми строками в серийных номерах, значит надо заполнить
    $r = get_array_from_sql('
        SELECT
            DepositRecId
        FROM 
            `cashmaster`.`SuspectSerialNumbs`
        WHERE
            `SuspectSerialNumbs`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
            AND (IFNULL(`SuspectSerialNumbs`.`LeftSeria`,"")=""
                OR IFNULL(`SuspectSerialNumbs`.`LeftNumber`,"")=""
                OR IFNULL(`SuspectSerialNumbs`.`RightSeria`,"")=""
                OR IFNULL(`SuspectSerialNumbs`.`RightNumber`,"")="")
    ;');
    do_sql('UNLOCK TABLES;');
    if (count($r)>0) {
            do_sql('UNLOCK TABLES;');
            echo 6;
            exit;
    };

    
    // Получаем количество номиналов с подозрительными купюрами по этой сверке.
    $row = get_array_from_sql('
        SELECT
            `ReconAccountingData`.`DenomId`,
            `ReconAccountingData`.`CullCount`
        FROM 
            `cashmaster`.`ReconAccountingData`
        WHERE
            `ReconAccountingData`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
            AND `ReconAccountingData`.`GradeId`=1
    ;');
    
    // Для каждого номинала:
    foreach ($row as $key=>$value) {
        // 2. Проверяем количество серийных номеров, которые ввел кассир
        $r = fetch_row_from_sql('
            SELECT
                count(*)
            FROM 
                `cashmaster`.`SuspectSerialNumbs`
            WHERE
                `SuspectSerialNumbs`.`DepositRecId`="'.addslashes($_REQUEST['deposit_rec_id']).'"
                AND `SuspectSerialNumbs`.`DenomId`="'.$value[0].'"
        ;');
        if ($value[1]!=$r[0]) {
            do_sql('UNLOCK TABLES;');
            echo 6;
            exit;
        };
    };
     * 
     */

?>
