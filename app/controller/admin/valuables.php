<?php

/*
 * Главное рабочее окно администратора
 */
    if (!isset($c)) exit;

    include './app/model/menu.php';
    
    $data['title'] = $_SESSION[$program]['lang']['valuables_title'];
    include './app/view/page_header_with_logout.php';
    include './app/view/set_remove_wait.php';
    include './app/view/forms/details_css.php';
    //include 'app/view/reload_after_1_min.php';
    
    if (isset($_POST['clear'])) unset($_POST);

    $l = explode('|',$_SESSION[$program]['lang']['valuables_find_labels']);
?>
<div class="container">
    <form id='order' method="POST" action="?c=valuables" style='margin-bottom: 0px;'>
    <?php echo htmlfix($l[0]); ?> <input
                        class='search-query'
                        style='<?php if (isset($_POST['SorterType']) AND $_POST['SorterType']!='') 
                            echo 'background-color:lightgreen;color:darkgreen;'; ?>width:50px;margin: 0px;'
                        type='text' 
                        autocomplete="off"
                        name='SorterType' 
                        value="<?php if (isset($_POST['SorterType'])) echo $_POST['SorterType']; ?>"/>
    <?php echo htmlfix($l[1]); ?> <input
                    class='search-query'
                    style='<?php if (isset($_POST['CategoryName']) AND $_POST['CategoryName']!='') 
                        echo 'background-color:lightgreen;color:darkgreen;'; ?>width:100px;margin: 0px;'
                    type='text' 
                    autocomplete="off"
                    name='CategoryName' 
                    value="<?php if (isset($_POST['CategoryName'])) echo $_POST['CategoryName']; ?>"/>
    <?php echo htmlfix($l[2]); ?> <input
                        class='search-query'
                        style='<?php if (isset($_POST['DenomLabel']) AND $_POST['DenomLabel']!='') 
                            echo 'background-color:lightgreen;color:darkgreen;'; ?>width:50px;margin: 0px;'
                        type='text' 
                        autocomplete="off"
                        name='DenomLabel' 
                        value="<?php if (isset($_POST['DenomLabel'])) echo $_POST['DenomLabel']; ?>"/>
    <?php echo htmlfix($l[3]); ?> <input
                        class='search-query'
                        style='<?php if (isset($_POST['CurrSymbol']) AND $_POST['CurrSymbol']!='') 
                            echo 'background-color:lightgreen;color:darkgreen;'; ?>width:30px;margin: 0px;'
                        type='text' 
                        autocomplete="off"
                        name='CurrSymbol' 
                        value="<?php if (isset($_POST['CurrSymbol'])) echo $_POST['CurrSymbol']; ?>"/>
    <?php echo htmlfix($l[4]); ?> <input
                        class='search-query'
                        style='<?php if (isset($_POST['ValuableTypeLabel']) AND $_POST['ValuableTypeLabel']!='') 
                            echo 'background-color:lightgreen;color:darkgreen;'; ?>width:30px;margin: 0px;'
                        type='text' 
                        autocomplete="off"
                        name='ValuableTypeLabel' 
                        value="<?php if (isset($_POST['ValuableTypeLabel'])) echo $_POST['ValuableTypeLabel']; ?>"/>
    <input class='btn btn-medium' type='submit' value='<?php echo htmlfix($l[5]); ?>' />
    <input class='btn btn-medium' type='submit' name='clear' value='<?php echo htmlfix($l[6]); ?>' />
    </form>
    <table>
        <tr>
            <td style="vertical-align: top;padding-right: 30px;">
                <h3><?php echo htmlfix($_SESSION[$program]['lang']['valuables_table_valuables_title']); ?></h3>
                <?php 
                    include 'app/model/table/valuables.php';
                ?>
            </td>
            <td style="vertical-align: top;">
                <?php
                    $row = fetch_row_from_sql('
                        SELECT
                            count(*)
                        FROM 
                            `cashmaster`.`Valuables`
                        WHERE
                            `Valuables`.`DenomId`="0"
                            AND `Valuables`.`ValuableTypeId`="0"');
                    if($row[0]>0) {  
                        ?>
                        <h3><?php echo htmlfix($_SESSION[$program]['lang']['valuables_table_new_valuables_title']); ?></h3>
                        <?php 
                            include 'app/model/table/new_valuables.php';
                    };                    
                ?>
            </td>
        </tr>
    </table>
    <hr/>
    <a href="?c=index" class="btn btn-primary btn-large" onclick="set_wait();">
        <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
    </a>
    <a href="?c=valuables" class="btn btn-primary btn-large" onclick="set_wait();">
        <?php echo htmlfix($_SESSION[$program]['lang']['refresh_button']); ?>
    </a>
    <a class="btn btn-warning btn-large" href="?c=valuable_add">
        <?php echo htmlfix($_SESSION[$program]['lang']['valuables_add_new_valuable']); ?>
    </a>
</div>
<?php
        foreach(explode('|','SorterType|CategoryName|DenomLabel|CurrSymbol|ValuableTypeLabel') as $key=>$value) {
            if (isset($_POST[$value]) AND $_POST[$value]!='') {
                ?>
                    <script>
                        $(document).ready(function () {
                            var trs = $($('table.info_table')[0]).find('TR');
                            for (var i=1; i<trs.length; i++) {
                                var tds = $(trs[i]).find('td');
                                $(tds[<?php echo $key; ?>]).css('background-color','lightgreen');
                            }
                        });
                    </script>
                <?php
            };
        };
?>