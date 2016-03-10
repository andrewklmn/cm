<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['operator_workflow'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
        include 'app/controller/echo_current_scenario.php';
        include 'app/view/reload_after_1_min.php';
        
?>
    <script>
        $(document).ready(function() {
            $('input#focus')[0].focus();
        });
        
        function focus_keyup(event) {
            var key = event.keyCode;
            var elem = ( event.target ) ? event.target : event.srcElement;
            switch(key){
                case 27: case 13:
                    elem.value = elem.value.replace(/[^a-zA-Z0-9\-\_]/g,'');
                    if(elem.value!='') {
                        set_wait();
                        window.location.replace('?c=reconciliation&separator_id=' + elem.value);
                    }
                break;
                default:
            }            
        }
    </script>
    <div class="container">
        <?php 
            include './app/model/table/deffered_reconciliations_by_user.php';
        ?>
        <br/>
        <?php echo $_SESSION[$program]['lang']['scan_barcode_or_enter_deposit_card_number']; ?>: 
            <input
                type="text"
                id="focus"
                class="navbar-form"
                placeholder="<?php echo $_SESSION[$program]['lang']['deposit_card_number']; ?>" 
                onkeyup="focus_keyup(event);"
                onfocus="$(this).select();"
                style="height:35px;width:170px;background-color: cyan;color:darkblue;font-size: 22px;"
                name="code" value=""
                autocomplete="off"/>
        <?php echo $_SESSION[$program]['lang']['and_press_esc_or_enter']; ?>
        <?php
            include 'app/view/error_message.php';
            include 'app/model/table/prebook.php';
            include 'app/model/table/prepared_reconciliations.php';
            include 'app/model/table/unreconciled_deposits_passive.php';
        ?>
    </div>
</body>
</html>