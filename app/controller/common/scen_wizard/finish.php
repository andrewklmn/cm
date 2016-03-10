<?php

/*
 * Denoms
 */
    if (!isset($c)) exit;
    
    ?>
        <style>
            table.both td{
                border-bottom: gray solid thin;
                height:24px;
                vertical-align: middle;
            }
            table.both th{
                border: gray solid thin;                    
            }
            table.both {
                margin-right: 40px;
            }
            table.available {
                width: 320px;
            }
            table.used {
                width: 320px;
            }
            form {
                padding: 0px;
                margin: 0px;
            }
        </style>
        <?php 
            $data['success'] = $_SESSION[$program]['lang']['scenario_was_configured'];
            include 'app/view/success_message.php';
        ?>
        <div class="container">
            <form method="POST">
                <input type="hidden" name="step" value="1"/>
                <a href="?c=scen_edit&id=<?php echo htmlfix($_GET['id']); ?>" class="btn btn-primary btn-large" onclick="set_wait();">
                    <?php echo htmlfix($_SESSION[$program]['lang']['back_to_scen_edit']); ?>
                </a>
            </form>
        </div>
    <?php
    exit;
    
?>
