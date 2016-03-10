<?php

/*
 * Таблица списка машин
 */

    if (!isset($c)) exit;
    
    if (isset($_POST['action']) AND $_POST['action']=='set') {
        if( (int)$_POST['id'] > 0 ) {
            do_sql('
                UPDATE 
                    `cashmaster`.`ScenReportTypes`
                SET
                    `IsUsed` = "'.addslashes($_POST['isused']).'"
                WHERE 
                    `Id` = "'.addslashes($_POST['id']).'"
                    AND `ScenarioId` = "'.addslashes($_GET['id']).'"
            ;');
        };
    };
    
    // Получаем все типы отчетов из таблицу ReportTypes
    $reports = get_assoc_array_from_sql('
        SELECT
            `ReportTypes`.`ReportTypeId`,
            `ReportTypes`.`ReportLabel`,
            `ReportTypes`.`DefaultReport`,
            `ReportTypes`.`NeedSigner`,
            `ReportTypes`.`GenerateXmlFile`,
            `ReportTypes`.`Description`,
            `ReportTypes`.`FileName`
        FROM 
            `cashmaster`.`ReportTypes`
    ;');
    foreach ($reports as $value) {
        $sql = '
            SELECT
                `ScenReportTypes`.`Id`,
                `ScenReportTypes`.`ScenarioId`,
                `ScenReportTypes`.`ReportTypeId`,
                `ScenReportTypes`.`IsUsed`
            FROM 
                `cashmaster`.`ScenReportTypes`
            WHERE
                `ScenReportTypes`.`ReportTypeId`="'.$value['ReportTypeId'].'"
                AND `ScenReportTypes`.`ScenarioId`="'.addslashes($_GET['id']).'"
        ;';
        if (count_rows_from_sql($sql)==0) {
            // Добавляем такой тип отчета по сценарию
            do_sql('
                INSERT INTO `cashmaster`.`ScenReportTypes`
                (
                    `ScenarioId`,
                    `ReportTypeId`,
                    `IsUsed`
                )
                VALUES
                (
                    "'.addslashes($_GET['id']).'",
                    "'.$value['ReportTypeId'].'",
                    0
                )
            ;');
        };
        
    };
    
?>
    <script>
        var a;
        function toogle(elem){
            elem.className="selected"
            set_wait();
            var value = $(elem).find('td')[2].innerHTML;
            var isused = 1;
            if (value=="<?php echo $_SESSION[$program]['lang']['yes']; ?>") {
                isused = 0;
            };
            $('input#isused').val(isused);
            $('input#id').val(elem.id);
            $('form#update').submit();
        };
    </script>
    <form style="display:none;" method="POST" id="update">
        <input type="hidden" id="id" name="id" value="0"/>
        <input type="hidden" id="isused" name="isused" value="0"/>
        <input type="hidden" name="action" value="set"/>
    </form>
<?php

    unset($table);
    $table['data'] = get_array_from_sql('
        SELECT
            `ScenReportTypes`.`Id`,
            `ReportTypes`.`ReportLabel`,
            `ReportTypes`.`Description`,
            IF(`ScenReportTypes`.`IsUsed`=1,"'
                .$_SESSION[$program]['lang']['yes'].'","'
                .$_SESSION[$program]['lang']['no'].'")
        FROM 
            `cashmaster`.`ScenReportTypes`
        LEFT JOIN
            ReportTypes ON ScenReportTypes.ReportTypeId=ReportTypes.ReportTypeId
        WHERE
            `ScenReportTypes`.`ScenarioId`="'.addslashes($_GET['id']).'"
        ORDER BY ReportLabel ASC
    ;');
    
    $table['header'] = explode('|',$_SESSION[$program]['lang']['scen_reports_table_headers']);
    $table['width'] = array( 320,350,100 );
    $table['align'] = array( 'left','left','left','center');
    $table['th_onclick']=array(';',';',';',';',';',';');
    $table['tr_onclick']='toogle(this.parentNode);';
    $table['title'] = $_SESSION[$program]['lang']['scen_reports_header'];
    $table['hide_id'] = 1;
    include_once './app/view/draw_select_table.php';
    draw_select_table($table);

?>
<script>
    $('table.info_table').find('tr').each(function(){
        var tds = $(this).find('td');
        if ('<?php echo htmlfix($_SESSION[$program]['lang']['no']); ?>'==$(tds[2]).html()) {
            $(tds[2]).css('color','red');
        } else {
            $(tds[2]).css('color','green');
        };
        //$(tds[2]).css('font-weight','bold');
    });
</script>