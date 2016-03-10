<?php

/*
 * Кнопки формы сверки
 */
        if (!isset($c)) exit;

        if($sorter_grades_is_ok==true 
                AND $sorter_data_is_ok==true
                AND count($extra_denoms)==0) { 
            ?>
                <input type="button" onclick="back_to_workflow();" class="btn btn-primary btn-large" value="<?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?>"/>
            <?php 
        } else {
            ?>
                <input type="button" onclick="back_to_workflow();" class="btn btn-primary btn-large" value="<?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?>"/>
            <?php 
        };

        if($no_sverka_button==false) { 
            ?>
                <input type="button"
                    id="finish"
                    onclick="recon(this);"
                    style=""
                    class="btn btn-primary btn-warning btn-large" value="<?php echo htmlfix($_SESSION[$program]['lang']['reconcile']); ?>"/>
                <?php 
                    if ($_SESSION[$program]['user_role_id']!=2) { 
                        ?>
                            <input type="button"
                                id="control"
                                onclick="control(this);"
                                style="display:none;"
                                class="btn btn-primary btn-danger btn-large" value="<?php echo htmlfix($_SESSION[$program]['lang']['send_to_control']); ?>"/>
                        <?php
                    } else {
                        ?>
                            <input type="button"
                                id="control"
                                onclick="recon_with_discrep(this);"
                                style="display:none;"
                                class="btn btn-primary btn-danger btn-large" value="<?php echo htmlfix($_SESSION[$program]['lang']['reconcile_with_discrep']); ?>"/>
                        <?php
                    };
                ?>
            <?php
        };

        echo '<br/><br/>CM-'.$_SESSION[$program]['SystemConfiguration']['CashCenterCode']
                        .'-'.str_pad(htmlfix($DepositRecId),6,'0', STR_PAD_LEFT);
        
    ?>