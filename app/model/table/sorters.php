<?php

/*
 * Таблица списка машин
 */

    if (!isset($c)) exit;
    
    if (!isset($_POST['order_by'])) {
        $_POST['order_by'] = 'SorterName';
        $_POST['order_type'] = 'ASC';
    };
?>
    <form id='driver' method="POST" action="?c=index" style='display:none;'>
        <input type="hidden" id="order_by" name="order_by" value="<?php echo htmlfix($_POST['order_by']) ?>">
        <input type="hidden" id="order_type" name="order_type" value="<?php echo htmlfix($_POST['order_type']) ?>">
    </form>
    <script>
        var a;
        function open_sorter(elem){
            window.location.replace('?c=sorter_edit&id=' + elem.id);
        };
        function order_by(name,order) {
            set_wait();
            $('input#order_by').attr('value',name);
            $('input#order_type').attr('value',order);
            a = setTimeout('$("form#driver").submit();', 100);
        };
    </script>
<?php

    $st = explode('|',$_SESSION[$program]['lang']['sorters_table_states']);
    
    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
            `Machines`.`MachineDBId`,
            `Machines`.`SorterName`,
            `Machines`.`SerialNumber`,
            SorterTypes.SorterType,
            CashRooms.CashRoomName,
            IF(`Machines`.`MachineConnectionOn`>0,"'.addslashes($st[0]).'","'.addslashes($st[1]).'"),
            IF(`Machines`.`MachineLogicallyDeleted`>0,"X","")
        FROM 
            `cashmaster`.`Machines`
        LEFT JOIN
            SorterTypes ON SorterTypes.SorterTypeId = `Machines`.`SorterTypeId`
        LEFT JOIN
            CashRooms ON CashRooms.Id = `Machines`.`CashRoomId`
        ORDER BY
            '.  addslashes($_POST['order_by']).' '.  addslashes($_POST['order_type']).'
    ;');      
    
    $table['header'] = explode('|',$_SESSION[$program]['lang']['sorters_table_header']);
    $table['width'] = array( 120,100,120,80,80,80);
    $table['align'] = array( 'left','left','left','center','center','center','center');
    $table['th_onclick']=array(
        'order_by('.(($_POST['order_by']=='SorterName' AND $_POST['order_type']=='ASC') ? '\'SorterName\',\'DESC\'':'\'SorterName\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='SerialNumber' AND $_POST['order_type']=='ASC') ? '\'SerialNumber\',\'DESC\'':'\'SerialNumber\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='SorterTypes.SorterType' AND $_POST['order_type']=='ASC') ? '\'SorterTypes.SorterType\',\'DESC\'':'\'SorterTypes.SorterType\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='CashRooms.CashRoomName' AND $_POST['order_type']=='ASC') ? '\'CashRooms.CashRoomName\',\'DESC\'':'\'CashRooms.CashRoomName\',\'ASC\'').');',
        'order_by('.(($_POST['order_by']=='MachineConnectionOn' AND $_POST['order_type']=='ASC') ? '\'MachineConnectionOn\',\'DESC\'':'\'MachineConnectionOn\',\'ASC\'').');',
        ';',';',';',';',';',';');
    $table['tr_onclick']='open_sorter(this.parentNode);';
    $table['title'] = '';
    $table['hide_id'] = 1;
    include_once './app/view/draw_select_table.php';
    draw_select_table($table);

?>
<script>
    $('table.info_table').find('tr').each(function(){
        var tds = $(this).find('td');
        if ('<?php echo htmlfix($st[1]); ?>'==$(tds[3]).html()) {
            $(tds[3]).css('color','red');
        };
        if ('<?php echo htmlfix($st[0]); ?>'==$(tds[3]).html()) {
            $(tds[3]).css('color','green');
        };
    });
</script>