<?php

/*
 * Список работников
 */

        if (!isset($c)) exit;
        
        $b = explode('|',$_SESSION[$program]['lang']['signers_buttons']);
        $l = explode('|',$_SESSION[$program]['lang']['signers_labels']);
        
        $data['title'] = $l[0];
        include 'app/model/menu.php';
        include 'app/view/page_header_with_logout.php';
        include 'app/view/set_remove_wait.php';
        include 'app/view/print_array_as_html_table.php';

if (isset($_POST['clear'])) unset($_POST);
        
        if (!isset($_POST['order_by']) OR !isset($_POST['order_type'])) { 
                $_POST['order_by']= 'ExternalUserName';
                $_POST['order_type']= 'ASC';
        };
        $order = ' ORDER BY '.  addslashes($_POST['order_by']).' '.addslashes($_POST['order_type']);
        
?>
<div class='container'>
    <h4><?php echo htmlfix($l[1]); ?></h4>
            <form id='order' method="POST" action="?c=signers" style='margin-bottom: 10px;'>
                <input id='order_by' type='hidden' name='order_by' value='<?php echo $_POST['order_by']; ?>'>
                <input id='order_type' type='hidden' name='order_type' value='<?php echo $_POST['order_type']; ?>'>
                <?php echo htmlfix($l[2]); ?> <input
                                    class='search-query'
                                    style='<?php 
                                            if (isset($_POST['ExternalUserName']) 
                                                        AND $_POST['ExternalUserName']!='') 
                                                            echo 'background-color:lightgreen;color:darkgreen;'; 
                                    ?>width:120px;margin: 0px;'
                                    type='text' 
                                    autocomplete="off"
                                    name='ExternalUserName' 
                                    value="<?php if (isset($_POST['ExternalUserName'])) echo $_POST['ExternalUserName']; ?>"/>
                <?php echo htmlfix($l[3]); ?> <input 
                                     class='search-query'
                                     type='text' 
                                     style='<?php if (isset($_POST['ExternalUserPost']) AND $_POST['ExternalUserPost']!='') echo 'background-color:lightgreen;color:darkgreen;'; ?>width:80px;margin: 0px;'
                                     autocomplete="off"
                                     name='ExternalUserPost' 
                                     value="<?php if (isset($_POST['ExternalUserPost'])) echo $_POST['ExternalUserPost']; ?>"/>
                <input class='btn btn-medium' type='submit' value='<?php echo htmlfix($b[2]); ?>' />
                <input class='btn btn-medium' type='submit' name='clear' value='<?php echo htmlfix($b[3]); ?>' />
            </form>
            <?php
        include 'app/model/table/signers.php';
    ?>
    <br/>
    <input 
        id='0' 
        onclick='window.location.replace("?c=index");' 
        type='button' 
        class='btn btn-primary btn-large' 
        value='<?php echo htmlfix($b[0]); ?>'/>
    <a class='btn btn-warning btn-large' href='?c=signer_add'><?php echo htmlfix($b[1]); ?></a>
</div>
<?php 
    if (isset($_POST['ExternalUserName']) AND $_POST['ExternalUserName']!='') {
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
        if (isset($_POST['ExternalUserPost']) AND $_POST['ExternalUserPost']!='') {
        ?>
            <script>
                $(document).ready(function () {
                    var trs = $('table.info_table').find('TR');
                    for (var i=1; i<trs.length; i++) {
                        var tds = $(trs[i]).find('td');
                        $(tds[1]).css('background-color','lightgreen');
                    }
                });
            </script>
        <?php
    };
    include './app/view/set_rs_to_stat.php';
?>