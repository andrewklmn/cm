<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['reports_archive'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';

        $l = explode('|', $_SESSION[$program]['lang']['reports_archive_search_label']);
        
        if (isset($_POST['clear'])) unset($_POST);
?>
    <div class="container">
        <h3><?php echo htmlfix($_SESSION[$program]['lang']['reports_archive']); ?></h3>
        <form id='order' method="POST" style='margin-bottom: 10px;'>
            <?php echo htmlfix($l[0]); ?>:
            <input
                class='search-query'
                style='<?php if (isset($_POST['date']) AND $_POST['date']!='') echo 'background-color:lightgreen;color:darkgreen;'; ?>width:120px;margin: 0px;'
                type='text' 
                autocomplete="off"
                name='date' 
                value="<?php if (isset($_POST['date'])) echo $_POST['date']; ?>"/>            
            <input class='btn btn-medium' type='submit' value='<?php echo htmlfix($l[1]); ?>' />
            <input class='btn btn-medium' type='submit' name='clear' value='<?php echo htmlfix($l[2]); ?>' />
        </form>
        <?php 
            include './app/model/table/reports.php';
        ?>
        <br/>
        <input 
            id='0' 
            onclick='window.location.replace("?c=reports");' 
            type='button' 
            class='btn btn-primary btn-large' 
            value='<?php echo htmlfix($_SESSION[$program]['lang']['back_to_reports']); ?>'/>
    </div>
</body>
</html>