<?php

/*
 * Index page of operator workflow
 */

        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['supervisor_workflow'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
        include 'app/controller/common/check_new_taskrecalc_files.php';
        
        include 'app/controller/set_default_scenario.php';
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
                        window.location.replace('?c=reconciliation&separator_id=' 
                                + elem.value )
                    };
                break;
                default:
            }
        }
        function open_rec(elem) {
            alert('Открываем окно сверки для Recs с кодом: ' + $($(elem).find('td:first')).html());
        }
    </script>
    <div class="container">
        <?php 
                
                include './app/model/table/for_check_reconciliations.php';
                include './app/model/table/deffered_reconciliations.php';
        ?>
        <br/>
        <?php echo $_SESSION[$program]['lang']['scan_barcode_or_enter_deposit_card_number']; ?>: 
            <input
                type="text"
                id="focus"
                class="navbar-form"
                onchange="this.value=rs(this.value);"
                placeholder="<?php echo $_SESSION[$program]['lang']['deposit_card_number']; ?>" 
                onkeyup="focus_keyup(event);"
                onfocus="$(this).select();"
                style="height:35px;width:170px;background-color: cyan;color:darkblue;font-size: 22px;"
                name="code" 
                value=""
                autocomplete="off"/>
        <?php echo $_SESSION[$program]['lang']['and_press_esc_or_enter']; ?>
        <?php
            include 'app/view/error_message.php';
            include './app/model/table/prepared_reconciliations.php';
            include './app/model/table/unreconciled_deposits.php';
            //echo '<br/>';
            //include './app/model/table/reconciled_deposits.php';
        ?>
    </div>
</body>
</html>