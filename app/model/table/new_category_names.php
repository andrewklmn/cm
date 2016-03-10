<?php

/*
 * New category names from sorter data
 */
    unset($table);
    $sql = '
        SELECT 
            Valuables.CategoryName,
            ActualCount,
            IFNULL(Denoms.DenomId,"-") as Denom
        FROM 
            SorterAccountingData
        LEFT JOIN
               Valuables ON Valuables.ValuableId=SorterAccountingData.ValuableId
        LEFT JOIN
            DepositRuns ON DepositRuns.DepositRunId = SorterAccountingData.DepositRunId
        LEFT JOIN
            Denoms ON Denoms.DenomId = Valuables.DenomId
        WHERE
               DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
               AND Valuables.DenomId="0" AND Valuables.ValuableTypeId="0" 
               AND `DepositRuns`.`DepositRecId` is NULL
    ;';

    echo '<div class="container">';
    // Есть неформализованные Валуаблы, срочно звонить Супервайзеру в колокол
    //echo "<h1></h1>";
    //$data['error'] = "New Valuables in Deposit. Can't start. Call supervisor.";
    $data['error'] = $_SESSION[$program]['lang']['new_valuables_in_deposit'];
    include './app/view/error_message.php';
    $table['header']=explode('|',$_SESSION[$program]['lang']['new_category_header']);
    $table['align']=array('left', 'center', 'center');
    $table['width']=array(300, 60, 40);
    $table['data']=get_array_from_sql($sql);
    include_once 'app/view/draw_select_table.php';
    draw_simple_table($table);
    
    $sorter_data_is_ok = false;
    echo '</div>';
?>
<hr/>
<div class="container">
    <button
        onclick="back_to_workflow();"
        class="btn-primary btn-large" href="index.php"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></button>
</div>
<?php
    exit;
?>