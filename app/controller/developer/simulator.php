<?php

/*
 * Simulator
 */

        if (!isset($c)) exit;
        
        //error_reporting(0);

        $data['title'] = 'Sorter Simulator';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/draw_table.php';
        
    // получаем список машины из БД
    //include_once './app/model/db/connection.php';
    
            
        
    include 'app/controller/select_sorter_type.php';
        
    
    if (isset($_POST['action']) AND $_POST['action']=='collect') {
        $sql='
            SELECT 
                * 
            FROM 
                Machines
            WHERE
                SorterName="Simulator"
                AND SorterVariant="Simulator"
        ;';
        $machines = get_assoc_array_from_sql($sql);
        foreach ($machines as $key=>$value) {
            include 'app/controller/collector/simulator_collector.php';
        };
        $data['success'] = 'Collection report: '.$files_were_skiped.' files skiped, '
                           .$files_were_processed.' files collected, '
                           .$new_deposits_were_added.' new deposits, '
                           .$new_sorter_accounting_data_were_added.' new accounting records, '
                           .$new_category_names_were_added.' new category names for '.$_SESSION[$program]['simulated_sorter_type'];
        include 'app/view/success_message.php';
    };

    
    $sql='
        SELECT
               *
        FROM
               Machines
        WHERE
            SorterName="Simulator"
            AND SorterVariant="Simulator"
    ;';
    $sorter = fetch_assoc_row_from_sql($sql);
    //print_r($sorter);

    if (isset($_POST['action']) AND $_POST['action']=='delete') {
        $files = explode("|",$_POST['filename']);
        foreach ($files as $key=>$value) {
                // Добавление окончено - удаляем файл с пересчетом
                if (file_exists($sorter['MachineConnectionDirectory'].'/'.$value)) 
                        unlink($sorter['MachineConnectionDirectory'].'/'.$value) ;
        };
        $data['success']= implode(",", $files).' was/were deleted';
        include 'app/view/success_message.php';
    };
    
    
    
    if (isset($_POST['action']) AND $_POST['action']=='add') {
        if($_POST['filename']=='') {
            $_POST['filename'] = 'E'.date('YmdHis');
        };
        if($_POST['xml']!='|0') {
            // добавляем данные в XML файл и рапортуем
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
                        <root>
                            <sorter>
                                <SorterName>'.$sorter['SorterName'].'</SorterName>
                                <SorterSerialNumber>'.$sorter['SerialNumber'].'</SorterSerialNumber>
                                <SorterVariant>'.$sorter['SorterVariant'].'</SorterVariant>
                            </sorter>
                            <deposit>
                                <ShiftId>1</ShiftId>
                                <BatchId>1</BatchId>
                                <DepositInBatchId>1</DepositInBatchId>
                                <DepositStartTimeStamp>'.date('Y-m-d H:i:s').'</DepositStartTimeStamp>
                                <DepositEndTimeStamp>'.date('Y-m-d H:i:s').'</DepositEndTimeStamp>
                                <DataSortCardNumber>'.$_POST['filename'].'</DataSortCardNumber>
                                <OperatorName>'.$_SESSION[$program]['user_fio'].'</OperatorName>
                                <SupervisorName></SupervisorName>
                                <IndexName>'.$_POST['index_name'].'</IndexName>
                                <SortModeName>Тестовая сортировка</SortModeName>';
            $records = explode('||', $_POST['xml']);
            foreach ($records as $value) {
                $t = explode('|', $value);
                $xml.='<SorterAccountingData>';
                $xml.='<CategoryName>'.$t[0].'</CategoryName><ActualCount>'.$t[1].'</ActualCount>';
                $xml.='</SorterAccountingData>';
            };
            $xml.='</deposit></root>';
            
            $target_name=$sorter['MachineConnectionDirectory'].'/'.$_POST['filename'].'.xml';
            /*
            $f = 0;
            while (file_exists($target_name)) {
                        $d = explode('.', $target_name);
                        $target_name = $d[0].'1.'.$d[1];
                        $f++;
            };
            if ($f>0) {
                $data['info_header']='Warning';
                $data['info_text']='File '.$_POST['filename'].'.xml exist. Filename was changed to '.$target_name;
                include 'app/view/info_message.php';
            };
            
             * 
             */
            
            //if (file_exists($sorter['MachineConnectionDirectory'].'/'.$target_name)) 
            //    unlink($sorter['MachineConnectionDirectory'].'/'.$target_name) ;
            
            $fp = fopen($target_name,"wb");
            fwrite($fp,$xml);
            fclose($fp);
            
            if (file_exists($target_name)) {
                $data['success']='File '.$target_name.' was saved';
                include 'app/view/success_message.php';
            } else {
                $data['error']="Can't create XML-file. Please check permission.";
                include 'app/view/error_message.php';
            }
            
        } else {
            $data['error']='XML-file was not added. Please, fill in sorter data.';
            include 'app/view/error_message.php';
        };
    };
    
    
    
    

    $line = array();
    
    if (!file_exists($sorter['MachineConnectionDirectory'])) {
        ?>
                <div class="container">
                    <h3>ERROR!</h3>
                    <p>
                        Simulator sorter directory does not exist.<br/>
                        Check the Machines table and MachineConnectionDirectory field.
                    </p>
                    <br/>
                    <br/>
                    <hr/>
                    <a href="?c=index" class="btn btn-primary btn-large">Back to main menu</a>
                </div>
            </body>
        </html>   
    <?php
        exit;
    };
    
    $list = scandir($sorter['MachineConnectionDirectory']);
    foreach ($list as $value) {
         if (!is_dir($sorter['MachineConnectionDirectory'].'/'.$value)) {
             $root = simplexml_load_file($sorter['MachineConnectionDirectory'].'/'.$value);
             if ($root) {
                $line[] = array($value,'right sorter data'); 
             } else {
                $line[] = array($value,'-'); 
             };
         };
    };
        ?>
            <script>
                function open_file(elem){
                    var indexes = $("input.index:checked");
                    if (indexes.length==0 ){
                        alert('Select one XML-file for open.');
                        return true;
                    };
                    if (indexes.length>1 ){
                        alert('Select only one XML-file for open.');
                        return true;
                    };
                    var a=$(indexes[0].parentNode.parentNode).find('a');
                    var value = $(a[0]).html();
                    var input = $('form#open').find('input');
                    input[0].value = value;
                    $('form#open').submit();
                };
                function delete_selected(elem){
                    var names =[];
                    var indexes = $("input.index:checked");
                    $(indexes).each(function(){
                        var a=$(this.parentNode.parentNode).find('a');
                        names[names.length]=$(a[0]).html();
                    });
                    var input = $('form#delete').find('input');
                    input[0].value = names.join("|");
                    $('form#delete').submit();
                };
            </script>
            <div class="container" style="margin-bottom: 0px;">
                <h4>Incoming XML sorter data files:</h4>
                <table style="margin: 0px;" class="table">
                  <tbody>
                        <?php 
                            if (count($line)>0) {
                                foreach ($line as $value) {
                                    ?>
                                        <tr>
                                            <td style="width:30px;vertical-align: middle;text-align: center;">
                                                <input style="margin:0px;" type="checkbox" class="index"/>
                                            <td>
                                                <a target='_blank' href='simulator/<?php echo htmlfix($value[0]); ?>'><?php echo htmlfix($value[0]); ?></a>
                                            </td>
                                            <td><?php echo htmlfix($value[1]); ?></td>
                                        </tr>
                                    <?php
                                };
                                ?>
                                      </tbody>
                                    </table>
                                    <button onclick="open_file(this);" class="btn btn-medium">Edit selected file</button> 
                                    <button onclick="delete_selected(this);" class="btn btn-medium">Delete Selected</button>
                                    ---
                                    <button onclick="$('form#collect').submit();" class="btn btn-warning btn-medium">
                                        Collect all incoming sorter data files to Cashmaster
                                    </button>
                                <?php
                            } else {
                                ?>
                                        <tr>
                                            <td colspan="3">There is no incoming XML-files</td>
                                        </tr>   
                                      </tbody>
                                    </table>
                                <?php
                            };
                        ?>

                <form style="margin: 0px;" method="POST" action="?c=simulator" id="collect">
                    <input type="hidden" name="action" value="collect"/>
                </form>
                <form style="margin: 0px;" method="POST" action="?c=simulator" id="delete">
                    <input type="hidden" name="filename" value=""/>
                    <input type="hidden" name="action" value="delete"/>
                </form>
                <form style="margin: 0px;" method="POST" action="?c=simulator" id="open">
                    <input type="hidden" name="filename" value=""/>
                    <input type="hidden" name="action" value="open"/>
                </form>
              </div> 
            <br/>
        <?php
?>
<style>
    table#xml th {
        padding:3px; 
        font-size:11px; 
        border: 2px solid black;
    }
    table#xml td {
        padding:3px; 
        font-size:11px; 
        border: 0px solid gray;
    }
    table#xml th {
        background-color: lightgray;
    }
</style>
<script>
    function add_record(elem) {
        $(elem.parentNode.parentNode).clone().appendTo(elem.parentNode.parentNode.parentNode);
    }
    function remove_record(elem) {
        var trs = $(elem.parentNode.parentNode.parentNode).find('tr.data');
        if (trs.length > 1) {
            $(elem.parentNode.parentNode).remove();
        }
        var inputs = $(elem.parentNode.parentNode).find('input.data');
        inputs[0].value="";
        inputs[1].value="0";
    }
    function add_xml() {
        var trs = $('body').find('tr.data');
        var xml = [];
        $(trs).each(function(){
            var inputs = $(this).find('input.data');
            xml[xml.length] = inputs[0].value + '|' + inputs[1].value;
        });
        $('input#xml').attr('value',xml.join('||'));
        $('form#submit').submit();
    ;}
</script>
<?php 

    //print_r($sorter);

    //echo $sorter['MachineConnectionDirectory'].'/'.$_POST['filename'].'.xml';

    $pos=array();
    if(isset($_POST['action']) AND $_POST['action']=='open') {
        $root = simplexml_load_file($sorter['MachineConnectionDirectory'].'/'.$_POST['filename']);
        $filename = str_replace('.xml', '', $_POST['filename']);
        foreach ($root->deposit->SorterAccountingData as $key => $value) {
            $pos[] = array($value->CategoryName,$value->ActualCount);
        };
        $action_name = 'Save XML-file';
        $header = 'Filename (DataSortCardNumber):';
    } else {
        $filename = 'S'.date('YmdHis');
        $pos[] = array('',0);
        $action_name = 'Create new XML-file';
        $header = 'New filename (DataSortCardNumber):';
    };
?>
<div class="container">
    <div style="padding: 20px;margin-bottom: 0px;" class="alert alert-error">
        <form style="padding:0px;margin:0px;" id="submit" method="POST" action="?c=simulator">
            <input type="hidden" name="action" value="add"/>
            <input id="xml" type="hidden" name="xml" value=""/>
            <h4>
                <?php echo $header; ?> 
                <input 
                    autocomplete="off"
                    type="text" 
                    onblur="this.value=this.value.replace(/[^0-9A-Za-z]/g,'');"
                    style="color:darkblue;font-size:20px;text-align:center;" 
                    name="filename" 
                    value="<?php 
                        echo $filename;
                ?>"/>.xml
                &nbsp;&nbsp;&nbsp;
                Deposit index: 
                <input placeholder="Index"
                    id="IndexName"
                    autocomplete="off"
                    class='data'
                    style='margin: 0px;width:100px;padding: 0xp;margin: 0px;' 
                    type='text' name='index_name' 
                    value='<?php echo htmlfix($v[0]);?>'/>
                <select
                    onchange="$('input#IndexName').val(this.value);"
                    style="padding: 0xp;margin: 0px;width:30px;">
                        <option value=''></option>
                    <?php 
                        $row = get_assoc_array_from_sql('
                            SELECT
                                `SorterIndexes`.`Id`,
                                `SorterIndexes`.`IndexName`,
                                `SorterIndexes`.`DepositIndexId`
                            FROM 
                                SorterIndexes
                            ORDER BY IndexName ASC
                        ;');
                        foreach ($row as $value) {
                            echo '<option value="',$value['IndexName'],'">',$value['IndexName'],'</option>';
                        };
                    ?>
                </select>
            </h4>
        </form>
        <h4>Add or remove sorter data for simulation:</h4>
        <table id='xml'>
            <tr>
                <th><font style="color:red;"><?php echo $_SESSION[$program]['simulated_sorter_type']; ?></font> Category Name</th>
                <th>Actual Count</th>
                <th> ... </th>
            </tr>
            <?php 
                
                foreach ($pos as $k=>$v) {
                    ?>
                        <tr class='data'>
                            <td style='vertical-align: middle;'><input placeholder="Category Name"
                                autocomplete="off"
                                class='data'
                                style='margin: 0px;width:500px;' 
                                type='text' name='CategoryName' 
                                value='<?php echo htmlfix($v[0]);?>'/><select 
                                onchange="this.parentNode.firstChild.value=this.value;"
                                style="margin: 0px;width:30px;">
                                    <option value="" selected></option>
                                    <?php 
                                        $sql = '
                                            SELECT
                                                    CategoryName,
                                                    DenomId,
                                                    ValuableTypeLabel,
                                                    SorterType
                                             FROM
                                                    Valuables
                                             LEFT JOIN
                                                    ValuableTypes ON ValuableTypes.ValuableTypeId = Valuables.ValuableTypeId
                                             LEFT JOIN
                                                    SorterTypes ON SorterTypes.SorterTypeId=Valuables.SorterTypeId
                                             WHERE
                                                    Valuables.SorterTypeId="'.$_SESSION[$program]['simulated_sorter'].'"
                                             ORDER BY CategoryName ASC
                                        ;';
                                        $row = get_array_from_sql($sql);
                                        foreach ($row as $key => $value) {
                                            echo '<option value="',htmlfix($value[0]),'">',htmlfix($value[0]),'</option>';
                                        };
                                    ?>
                                </select>
                            </td>
                            <td style='vertical-align: middle;'>
                                <input
                                    class='data'
                                    autocomplete="off"
                                    onblur="this.value=this.value.replace(/[^0-9]/g,'');
                                        if (this.value=='') this.value=0;
                                        this.value=parseInt(this.value);"
                                    style='text-align: center; margin: 0px;' type='text' name='ActualCount' value='<?php echo $v[1];?>'/>
                            </td>
                            <td style='vertical-align: middle;'>
                                <button onclick="add_record(this);" style='margin: 0px;' class='btn btn-mini'>+</button>
                                <button onclick="remove_record(this);" style='margin: 0px;' class='btn btn-mini'>-</button>
                            </td>                
                        </tr>
                    <?php
                }
            ?>
        </table> 
        <br/>
        <button onclick='add_xml();' class='btn btn-warning btn-medium'><?php echo $action_name; ?></button>
    </div>
</div>
<hr/>
<div class="container">
    <a href="?c=simulator" class="btn btn-primary btn-large">Reload Simulator page</a>
    <a href="?c=index" class="btn btn-primary btn-large">Back to main menu</a>
</div>
</body>
</html>