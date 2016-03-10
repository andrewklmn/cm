<?php

/*
 * Список работников
 */

        if (!isset($c)) exit;
        
        $b = explode('|',$_SESSION[$program]['lang']['users_buttons']);
        $l = explode('|',$_SESSION[$program]['lang']['users_labels']);
        
        $data['title'] = $l[0];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        include './app/view/print_array_as_html_table.php';

        if (isset($_POST['clear'])) unset($_POST);
        
        if (!isset($_POST['order_by']) OR !isset($_POST['order_type'])) { 
                $_POST['order_by']= 'UserFamilyName';
                $_POST['order_type']= 'ASC';
        };
        $order = ' ORDER BY '.  addslashes($_POST['order_by']).' '.addslashes($_POST['order_type']);
        
?>
<div class='container'>
    <h4><?php echo htmlfix($l[1]); ?></h4>
            <form id='order' method="POST" action="?c=users" style='margin-bottom: 10px;'>
                <input id='order_by' type='hidden' name='order_by' value='<?php echo $_POST['order_by']; ?>'>
                <input id='order_type' type='hidden' name='order_type' value='<?php echo $_POST['order_type']; ?>'>
                <?php echo htmlfix($l[2]); ?> <input
                                    class='search-query'
                                    style='<?php if (isset($_POST['name']) AND $_POST['name']!='') echo 'background-color:lightgreen;color:darkgreen;'; ?>width:120px;margin: 0px;'
                                    type='text' 
                                    autocomplete="off"
                                    name='name' 
                                    value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>"/>
                <?php echo htmlfix($l[3]); ?> <input 
                                     class='search-query'
                                     type='text' 
                                     style='<?php if (isset($_POST['code']) AND $_POST['code']!='') echo 'background-color:lightgreen;color:darkgreen;'; ?>width:80px;margin: 0px;'
                                     autocomplete="off"
                                     name='code' 
                                     value="<?php if (isset($_POST['code'])) echo $_POST['code']; ?>"/>
                <?php echo htmlfix($l[4]); ?> <input 
                                     class='search-query'
                                     type='text' 
                                     style='<?php if (isset($_POST['kassa']) AND $_POST['kassa']!='') echo 'background-color:lightgreen;color:darkgreen;'; ?>width:40px;margin: 0px;'
                                     autocomplete="off"
                                     name='kassa' 
                                     value="<?php if (isset($_POST['kassa'])) echo $_POST['kassa']; ?>"/>
                <input class='btn btn-medium' type='submit' value='<?php echo htmlfix($b[2]); ?>' />
                <input class='btn btn-medium' type='submit' name='clear' value='<?php echo htmlfix($b[3]); ?>' />
            </form>
            <?php
        include 'app/model/table/users.php';
    ?>
    <br/>
    <input 
        id='0' 
        onclick='window.location.replace("?c=index");' 
        type='button' 
        class='btn btn-primary btn-large' 
        value='<?php echo htmlfix($b[0]); ?>'/>
    <?php 
        if ($no_add==0) {
            ?>
                <a class='btn btn-warning btn-large' href='?c=user_add'><?php echo htmlfix($b[1]); ?></a>
            <?php
        };
    ?>
</div>
<?php 
    if (isset($_POST['name']) AND $_POST['name']!='') {
        ?>
            <script>
                $(document).ready(function () {
                    var trs = $('table.info_table').find('TR');
                    for (var i=1; i<trs.length; i++) {
                        var tds = $(trs[i]).find('td');
                        $(tds[0]).css('background-color','lightgreen');
                    }
                });
            </script>
        <?php
    };
    if (isset($_POST['code']) AND $_POST['code']!='') {
        ?>
            <script>
                $(document).ready(function () {
                    var trs = $('table.info_table').find('tr');
                    for (var i=1; i<trs.length; i++) {
                        var tds = $(trs[i]).find('td');
                        $(tds[1]).css('background-color','lightgreen');
                    }
                });
            </script>
        <?php        
    };
    if (isset($_POST['kassa']) AND $_POST['kassa']!='') {
        ?>
            <script>
                $(document).ready(function () {
                    var trs = $('table.info_table').find('tr');
                    for (var i=1; i<trs.length; i++) {
                        var tds = $(trs[i]).find('td');
                        $(tds[3]).css('background-color','lightgreen');
                    }
                });
            </script>
        <?php        
    };
    
    include './app/view/set_rs_to_stat.php';
?>