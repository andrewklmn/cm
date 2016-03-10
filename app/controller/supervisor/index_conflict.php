<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['index_correction'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';

        
        // Проверяем действительно ли индексы разные или новые
        $sorter_indexes = get_array_from_sql('
            SELECT
                IndexName
            FROM
                DepositRuns 
            LEFT JOIN
                DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
            WHERE
                    DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                    AND IFNULL(DepositRecs.ReconcileStatus,0)="0"
            GROUP BY IndexName
        ;');

        $indexes = get_assoc_array_from_sql('
            SELECT
                `SorterIndexes`.`Id`,
                `SorterIndexes`.`IndexName`,
                `SorterIndexes`.`DepositIndexId`
            FROM 
                `cashmaster`.`SorterIndexes`
        ;');
        
        $flag = false;
        // Проверяем есть ли индекс в нашей таблице соответствия индексов.
        foreach ($sorter_indexes as $value) {
            foreach ($indexes as $val) {
                if ($value[0]==$val['IndexName']) {
                    $flag = true;
                };
            };
        };
        
        if ( $flag==true AND count($sorter_indexes)==1 ) {
            echo '<div class="container">';
            $data['error'] = $_SESSION[$program]['lang']['cant_edit_correct_indexes_by_card'].$_REQUEST['separator_id'];
            include './app/view/error_message.php';
            ?>
                <hr/>
                <a class="btn btn-primary btn-large" href="?c=index"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></a>
                </div>
            <?php
            exit;
        };
        
        
        
        if(isset($_POST['action']) AND $_POST['action']=='set_index') {
            // Если пришло обновление, то обновляем данные
            
            // Получаем список Депозитрансов для обновления по этой карте
            $rows = get_array_from_sql('
                SELECT
                    DepositRunId
                FROM
                    DepositRuns 
                LEFT JOIN
                    DepositRecs ON DepositRecs.DepositRecId=DepositRuns.DepositRecId
                WHERE
                        DataSortCardNumber="'.addslashes($_REQUEST['separator_id']).'"
                        AND IFNULL(DepositRecs.ReconcileStatus,0)=0;
            ;');
            
            $runs = array();
            foreach ($rows as $value) {
                $runs[] = $value[0];
            };
            
            do_sql('
                UPDATE 
                    `cashmaster`.`DepositRuns`
                SET
                    `IndexName` = "'.  addslashes($_POST['index']).'"
                WHERE 
                    DepositRunId IN ('.implode(',', $runs).')
                        
            ;');
            
            echo '<div class="container">';
            $data['success'] = $_SESSION[$program]['lang']['index_was_changed'];
            include './app/view/success_message.php';
            ?>
                <hr/>
                <a class="btn btn-primary btn-large" href="?c=index"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></a>
                </div>
            <?php
            exit;
        };
        
?>
    <script>
        function index_shower(elem) {
            if (elem.value=='') {
                $('button#set_index').hide();
            } else {
                $('button#set_index').show();
            };
        };
        function set_index(){
            var sel = $('select#index');
            if (sel[0].value=='') {
                alert('<?php echo htmlfix($_SESSION[$program]['lang']['select_index_from_list']); ?>');
            } else {
                $('form#index').submit();
            };
        };
    </script>
    <div class="container">
        <?php
                    
            $data['info_header'] = $_SESSION[$program]['lang']['attention'];
            $data['info_text'] = $_SESSION[$program]['lang']['call_to_admin_for_new_index'];
            include './app/view/info_message.php';
            include './app/model/table/unreconciled_deposits_by_separator_id.php';
            
            $row = get_array_from_sql('
                SELECT
                    `SorterIndexes`.`IndexName`
                FROM 
                    `cashmaster`.`SorterIndexes`
                ORDER BY `SorterIndexes`.`IndexName` ASC
            ;');
                    
        ?>
        <br/>
        <form id="index" method="POST" action="?c=index_conflict&separator_id=<?php echo $_REQUEST['separator_id']; ?>">
        <?php echo htmlfix($_SESSION[$program]['lang']['select_index_from_list']); ?>:
        <select 
            id="index" 
            name="index"
            onchange="index_shower(this);"
            class="span2">
            <option value=""></option>
            <?php 
                foreach ($row as $key => $value) {
                    echo '<option value="'.htmlfix($value[0]).'">'.htmlfix($value[0]).'</option>';
                };
            ?>
        </select>
        <input type="hidden" name="action" value="set_index">
        </form>
        <a class="btn btn-primary btn-large" href="?c=index"><?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?></a>
        <button 
            onclick="set_index();"
            id="set_index" style="display: none;" class="btn btn-warning btn-large">
            <?php echo htmlfix($_SESSION[$program]['lang']['set_index']) ?>
        </button>
    </div>
</body>
</html>