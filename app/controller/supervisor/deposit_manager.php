<?php

/*
 * Deposit Manager
 */

        if (!isset($c)) exit;

        $data['title'] = $_SESSION[$program]['lang']['deposit_manager'];
        include './app/model/menu.php';
        include './app/view/page_header_with_logout.php';
        include './app/view/set_remove_wait.php';
        
?>
<script>
    function join_runs() {
        // Связывает выбранные >=1 рансов, в 1 выбранный рек
        alert('Join runs');
    };
    function to_service() {
        var recs = $('table#recs').find('input:checked');
        var runs = $('table#runs').find('input:checked');
        // 1. Связывает выбранные >0 рансов, в новый сервисный рек если реков выбрано 0
        if (recs.length==0 && runs.length>0) {
            var id = [];
            var card = [];
            $(runs).each(function(){
                id[id.length] = this.parentNode.parentNode.id;
                card[card.length] = $(this.parentNode.parentNode).find('td')[0].innerHTML;
            });
            $('input#runs').val(id.join('|'));
            $('input#card').val(card.join('|'));
            $('input#action').val('runs_to_service'); 
            $('form#action').submit();
        };
        // 2. Перводит 1 выбранный рек в сервисный (должно быть выбрано 0 рансов)
        if (recs.length==1 && runs.length==0) {
            $('input#recs').val(recs[0].parentNode.parentNode.id);
            $('input#card').val($(recs[0].parentNode.parentNode).find('td')[0].innerHTML);
            $('input#action').val('rec_to_service');  
            $('form#action').submit();
        };
    };
    
    function join_recs() {
        var recs = $('table#recs').find('input:checked');
        var runs = $('table#runs').find('input:checked');
        // Связывает выбранные >1 рексы, с выбором номера карты, (должно быть выбрано 0 ранов )
        if (recs.length>1 && runs.length==0) {
            var id = [];
            var card = [];
            $(recs).each(function(){
                id[id.length] = this.parentNode.parentNode.id;
                card[card.length] = $(this.parentNode.parentNode).find('td')[0].innerHTML;
            });
            $('input#recs').val(id.join('|'));
            $('input#card').val(card.join('|'));
            $('input#action').val('join_recs');
            $('form#action').submit();
        };
    };
    
    function change_card_number() {
        var recs = $('table#recs').find('input:checked');
        var runs = $('table#runs').find('input:checked');
        // 1. Меняем номер в рансах
        if (recs.length==0 && runs.length>0) {
            var id = [];
            var card = [];
            $(runs).each(function(){
                id[id.length] = this.parentNode.parentNode.id;
                card[card.length] = $(this.parentNode.parentNode).find('td')[0].innerHTML;
            });
            $('input#runs').val(id.join('|'));
            $('input#card').val(card.join('|'));
            $('input#action').val('change_card_number_in_runs'); 
            $('form#action').submit();
        };
        // 2. Меняем номер в рансах по одному реку
        if (recs.length==1 && runs.length==0) {
            $('input#recs').val(recs[0].parentNode.parentNode.id);
            $('input#card').val($(recs[0].parentNode.parentNode).find('td')[0].innerHTML);
            $('input#action').val('change_card_number_in_rec');  
            $('form#action').submit();    
        };
    }; 
    function release_runs() {
            var recs = $('table#recs').find('input:checked');
            var runs = $('table#runs').find('input:checked');
        // Из 1 сверки выбрасываются все рансы... (должно быть выбрано 0 ранов )
            if (recs.length==1 && runs.length==0) {
                $('input#recs').val(recs[0].parentNode.parentNode.id);
                $('input#card').val($(recs[0].parentNode.parentNode).find('td')[0].innerHTML);
                $('input#action').val('release_runs');  
                $('form#action').submit(); 
            };
    };
</script>
<div class="container">
<?php 
    if (isset($data['error']) AND $data['error']!='') {
        include 'app/view/error_message.php';
    }; 
    include 'app/model/table/deffered_reconciliations_selectable.php';
    
    $b = explode('|', $_SESSION[$program]['lang']['deposit_manager_buttons']);
    include 'app/model/table/unreconciled_deposits_selectable.php';
?>
    <div class="container navbar navbar-fixed-bottom"
         style="background-color: white; padding: 20px;">
        <a class="btn btn-primary btn-large" href="?c=index">
            <?php echo htmlfix($_SESSION[$program]['lang']['back_to_workflow']); ?>
        </a>
        <button 
            id="to_service"
            style="display: none;"
            onclick="to_service();"
            class="btn btn-danger btn-large button"
            ><?php echo htmlfix($b[0]); ?></button>
        <button 
            id="join_recs"
            style="display: none;"
            onclick="join_recs();"        
            class="btn btn-warning btn-large button"><?php echo htmlfix($b[1]); ?></button>
        <button 
            id="change_card_number"
            style="display: none;"
            onclick="change_card_number();"
            class="btn btn-info btn-large button"><?php echo htmlfix($b[2]); ?></button>
        <button 
            id="release_runs"
            style="display: none;"
            onclick="release_runs();"
            class="btn btn-warning btn-large button"><?php echo htmlfix($b[3]); ?></button>
    </div>
<form id="action" method="POST" action="?c=deposit_manager_actions" style="display: none;">
    <input type="hidden" id="recs" name="recs" value=""/>
    <input type="hidden" id="runs" name="runs" value=""/>
    <input type="hidden" id="card" name="card" value=""/>
    <input type="hidden" id="action" name="action" value=""/>
</form>
</div>
<script>
    // Биндим обработчик нажатия на галочку индекс для подсвечивания нужных кнопок
    $(document).ready(function(){
        $('th.index').find('input').click(function(){
            $('button.button').hide();
            var recs = $('table#recs').find('input:checked');
            var runs = $('table#runs').find('input:checked');
            if (recs.length==1 && runs.length>0) {
                $('button#join').show();
            };
            if ((recs.length==0 && runs.length>0)
                    ||(recs.length==1 && runs.length==0)) {
                $('button#to_service').show();
            };
            if (recs.length>1 && runs.length==0) {
                $('button#join_recs').show();
            };
            if ((recs.length==1 && runs.length==0)
                || (recs.length==0 && runs.length>0)) {
                $('button#change_card_number').show();
            };
            if (recs.length==1 && runs.length==0) {
                $('button#release_runs').show();
            };
        });
    });
</script>

