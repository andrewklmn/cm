<?php

/*
 * Кнопки формы сверки
 */
    if (!isset($c)) exit;

    ?>
    <a class="btn btn-primary btn-large" href="?c=index">
        <?php echo htmlfix($_SESSION[$program]['lang']['back_to_list']); ?>
    </a>
    <a class="btn btn-primary btn-warning btn-large" 
       target="_blank"
       href="?c=receipt&id=<?php echo urlencode($DepositRecId); ?>">
        <?php echo htmlfix($_SESSION[$program]['lang']['print_receipt']); ?>
    </a>
<?php

        echo '<br/><br/>CM-'.$_SESSION[$program]['SystemConfiguration']['CashCenterCode']
                        .'-'.str_pad(htmlfix($DepositRecId),6,'0', STR_PAD_LEFT);
?>